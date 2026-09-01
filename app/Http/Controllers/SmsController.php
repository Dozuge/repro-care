<?php

namespace App\Http\Controllers;

use App\Models\SmsLog;
use App\Models\User;
use App\Models\Purok;
use App\Services\SmsService;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * GET /cho/sms — SMS log dashboard with send form.
     */
    public function index(Request $request)
    {
        $userRole = auth()->user()->role;
        $query = SmsLog::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('phone_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', fn($u) => $u->where('first_name', 'like', '%' . $request->search . '%')
                      ->orWhere('last_name', 'like', '%' . $request->search . '%'));
            });
        }

        $logs = $query->paginate(20)->withQueryString();

        $stats = [
            'total'   => SmsLog::count(),
            'sent'    => SmsLog::where('status', 'sent')->count(),
            'failed'  => SmsLog::where('status', 'failed')->count(),
            'enabled' => User::where('role', 'user')->where('status', 'approved')
                ->where('sms_opt_out', false)->whereNotNull('contact_number')
                ->where('contact_number', '!=', '')->count(),
        ];

        if ($userRole === 'cho') {
            return view('cho.sms.index', compact('logs', 'stats'));
        }

        $patientsQuery = User::where('role', 'user')
            ->where('status', 'approved')
            ->where('sms_opt_out', false)
            ->whereNotNull('contact_number');

        $puroksQuery = Purok::orderBy('name');

        if ($userRole === 'bhw') {
            // Scope patients to BHW's purok if they have one
            if (auth()->user()->purok_id) {
                $patientsQuery->where('purok_id', auth()->user()->purok_id);
                $puroksQuery->where('id', auth()->user()->purok_id);
            }
        }

        $patients = $patientsQuery->orderBy('first_name')->get(['id', 'first_name', 'middle_initial', 'last_name', 'contact_number']);
        $puroks = $puroksQuery->get();

        if ($userRole === 'bhw') {
            return view('bhw.sms.index', compact('logs', 'patients', 'puroks', 'stats'));
        }

        return view('midwife.sms.index', compact('logs', 'patients', 'puroks', 'stats'));
    }

    /**
     * POST /cho/sms/send — Send a custom SMS to a single patient.
     */
    public function send(Request $request)
    {
        if (auth()->user()->role === 'cho') {
            abort(403, 'Unauthorized action.');
        }
        $request->validate([
            'user_id'    => 'required|exists:users,id',
            'message_en' => 'required|string|max:320',
            'message_tl' => 'nullable|string|max:320',
        ]);

        $user = User::findOrFail($request->user_id);

        if (!$user->hasSmsEnabled()) {
            return back()->with('error', "Patient {$user->name} has SMS disabled or no contact number.");
        }

        $smsService = new SmsService();
        $sent = $smsService->sendCustom($user, $request->message_en, $request->message_tl ?? '');

        \App\Models\ActivityLog::log('create', "Sent custom SMS to {$user->name} ({$user->contact_number})");

        return back()->with(
            $sent ? 'success' : 'error',
            $sent ? "SMS sent to {$user->name} successfully." : "Failed to send SMS. Check Movider SMS configuration."
        );
    }

    /**
     * POST /cho/sms/broadcast — Send a broadcast SMS to a group of patients.
     */
    public function broadcast(Request $request)
    {
        if (auth()->user()->role === 'cho') {
            abort(403, 'Unauthorized action.');
        }
        $request->validate([
            'message_en' => 'required|string|max:320',
            'message_tl' => 'nullable|string|max:320',
            'purok_id'   => 'nullable|exists:puroks,id',
            'barangay'   => 'nullable|string|max:255',
        ]);

        $purokId = $request->purok_id ? (int)$request->purok_id : null;
        if (auth()->user()->role === 'bhw' && auth()->user()->purok_id) {
            $purokId = auth()->user()->purok_id;
        }

        $smsService = new SmsService();
        $sent = $smsService->broadcast(
            $request->message_en,
            $request->message_tl ?? '',
            $purokId,
            $request->barangay
        );

        $scope = $purokId ? "Purok #{$purokId}" : ($request->barangay ?: 'all patients');
        \App\Models\ActivityLog::log('create', "Sent broadcast SMS to {$scope}. Total sent: {$sent}");

        return back()->with('success', "Broadcast SMS sent to {$sent} patient(s) successfully.");
    }
}
