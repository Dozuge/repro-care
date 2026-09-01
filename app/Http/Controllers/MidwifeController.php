<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pregnancy;
use App\Models\Checkup;
use App\Models\HealthRecord;
use App\Models\Bhw;
use App\Models\BhwProfile;
use App\Models\BhwMonthlyReport;
use App\Models\CheckupReferral;
use App\Models\WalkInPatient;
use App\Models\Purok;
use App\Services\RiskAnalysisService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class MidwifeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Dashboard
    public function dashboard()
    {
        // Auto-run alert checks when midwife visits dashboard
        try {
            // Check for overdue checkups across all women
            Checkup::markOverdueCheckups();
            
            // Re-evaluate risk for women with missed checkups
            $missedWomen = Checkup::missed()->distinct('user_id')->pluck('user_id');
            $riskService = new RiskAnalysisService();
            foreach ($missedWomen as $womanId) {
                $riskService->evaluate($womanId);
            }
        } catch (\Exception $e) {
            \Log::error('Midwife dashboard alert check failed: ' . $e->getMessage());
        }
        
        // Cache dashboard statistics for 5 minutes (300 seconds) for faster loading
        $cacheKey = 'midwife_dashboard_stats_' . auth()->id();
        $stats = Cache::remember($cacheKey, 300, function () {
            return [
                'totalPatients' => User::where('role', 'user')->where('status', 'approved')->count(),
                'activePregnancies' => Pregnancy::active()->count(),
                'scheduledCheckups' => Checkup::scheduled()->count(),
                'missedCheckups' => Checkup::missed()->count(),
                'highRiskPatients' => Pregnancy::active()->highRisk()->count(),
            ];
        });

        // Only select needed columns for better performance
        $recentCheckups = Checkup::with(['woman:id,first_name,middle_initial,last_name', 'midwife:id,first_name,middle_initial,last_name'])
            ->select('id', 'user_id', 'midwife_id', 'scheduled_date', 'status', 'purpose')
            ->scheduled()
            ->upcoming()
            ->latest('scheduled_date')
            ->limit(5)
            ->get();

        return view('midwife.dashboard', array_merge($stats, compact('recentCheckups')));
    }

    // Patients Management
    public function patients()
    {
        $search = request('search');
        $filter = request('filter', 'all');
        $purokId = request('purok_id');
        $pregnancyStatus = request('pregnancy_status', 'all');
        $ageRange = request('age_range', 'all');

        // Get registered patients
        $registeredQuery = User::where('role', 'user')
            ->with([
                'purok',
                'pregnancies' => function($query) {
                    $query->active();
                },
            ])
            ->where('status', 'approved');

        if ($search) {
            $registeredQuery->where(function($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                  ->orWhere('middle_initial', 'like', '%' . $search . '%')
                  ->orWhere('last_name', 'like', '%' . $search . '%')
                  ->orWhere('contact_number', 'like', '%' . $search . '%')
                  ->orWhere('address', 'like', '%' . $search . '%')
                  ->orWhere('barangay', 'like', '%' . $search . '%');
            });
        }

        if ($purokId) {
            $registeredQuery->where('purok_id', $purokId);
        }

        $registeredPatients = $registeredQuery->get()->map(function($patient) {
            $patient->type = 'registered';
            return $patient;
        });

        // Get unregistered patients
        $unregisteredQuery = WalkInPatient::with(['recordedBy', 'purok', 'convertedToUser', 'pregnancies' => function($query) {
            $query->active();
        }])
            ->whereNull('converted_to_user_id');

        if ($search) {
            $unregisteredQuery->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('middle_initial', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('address', 'like', '%' . $search . '%')
                    ->orWhere('barangay', 'like', '%' . $search . '%')
                    ->orWhere('contact_number', 'like', '%' . $search . '%');
            });
        }

        if ($purokId) {
            $unregisteredQuery->where('purok_id', $purokId);
        }

        $unregisteredPatients = $unregisteredQuery->latest()->get()->map(function($patient) {
            $patient->type = 'unregistered';
            return $patient;
        });

        // Combine both collections
        $allPatients = $registeredPatients->concat($unregisteredPatients);

        // Apply filter
        if ($filter === 'registered') {
            $allPatients = $allPatients->where('type', 'registered');
        } elseif ($filter === 'unregistered') {
            $allPatients = $allPatients->where('type', 'unregistered');
        }

        if ($pregnancyStatus === 'pregnant') {
            $allPatients = $allPatients->filter(fn ($patient) => $patient->pregnancies && $patient->pregnancies->count() > 0);
        } elseif ($pregnancyStatus === 'not_pregnant') {
            $allPatients = $allPatients->filter(fn ($patient) => !$patient->pregnancies || $patient->pregnancies->count() === 0);
        }

        if ($ageRange !== 'all') {
            $allPatients = $allPatients->filter(function ($patient) use ($ageRange) {
                $age = $patient->age;

                return match ($ageRange) {
                    'under_20' => $age !== null && $age < 20,
                    '20_34' => $age !== null && $age >= 20 && $age <= 34,
                    '35_plus' => $age !== null && $age >= 35,
                    default => true,
                };
            });
        }

        // Paginate manually
        $page = request('page', 1);
        $perPage = 10;
        $patients = new \Illuminate\Pagination\LengthAwarePaginator(
            $allPatients->forPage($page, $perPage),
            $allPatients->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Get statistics
        $scheduledCheckups = Checkup::where('status', 'scheduled')->count();
        $missedCheckups = Checkup::where('status', 'missed')->count();
        $registeredCount = User::where('role', 'user')->where('status', 'approved')->count();
        $unregisteredCount = WalkInPatient::whereNull('converted_to_user_id')->count();
        $puroks = Purok::orderBy('name')->get();

        return view('midwife.patients', compact(
            'patients',
            'scheduledCheckups',
            'missedCheckups',
            'registeredCount',
            'unregisteredCount',
            'puroks',
            'purokId',
            'pregnancyStatus',
            'ageRange'
        ));
    }

    public function pendingPatients()
    {
        $search = request('search');

        $query = User::where('role', 'user')->where('status', 'pending');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('address', 'like', '%' . $search . '%')
                    ->orWhere('barangay', 'like', '%' . $search . '%');
            });
        }

        $pending = $query->latest()->paginate(10)->withQueryString();

        return view('midwife.pending-patients', compact('pending'));
    }

    public function approvePatient($id)
    {
        $woman = User::where('role', 'user')->findOrFail($id);
        $woman->update([
            'status' => 'approved',
            'rejection_reason' => null,
        ]);

        \App\Models\Notification::createNotification(
            $woman->id,
            'Your registration has been approved. You can now log in to ReproCare.',
            'Registration Approved',
            'success',
            route('user.dashboard')
        );

        return redirect()->route('midwife.pending-patients')
            ->with('success', $woman->name . ' has been approved.');
    }

    public function rejectPatient(Request $request, $id)
    {
        $request->validate(['reason' => 'nullable|string|max:500']);

        $woman = User::where('role', 'user')->findOrFail($id);
        $womanName = $woman->name;

        // Delete the user account
        $woman->delete();

        return redirect()->route('midwife.pending-patients')
            ->with('success', $womanName . ' has been rejected and deleted.');
    }

    public function patientDetails($id)
    {
        // Eager load all relationships in single query for better performance
        $woman = User::where('role', 'user')->with([
            'emergencyContacts',
            'pregnancies' => function($q) { $q->latest()->select('id', 'user_id', 'lmp', 'edd', 'is_high_risk'); },
            'checkups' => function($q) { $q->with('midwife:id,first_name,middle_initial,last_name')->latest()->select('id', 'user_id', 'midwife_id', 'scheduled_date', 'status', 'purpose'); },
            'healthRecords' => function($q) { $q->with('recordedBy:id,first_name,middle_initial,last_name')->latest()->select('id', 'user_id', 'recorded_by_id', 'bp', 'weight', 'created_at')->take(50); }
        ])->select('id', 'first_name', 'middle_initial', 'last_name', 'email', 'address', 'barangay', 'date_of_birth', 'contact_number', 'partner_name', 'partner_contact')->findOrFail($id);

        return view('midwife.patient-details', [
            'woman' => $woman,
            'pregnancies' => $woman->pregnancies,
            'checkups' => $woman->checkups,
            'healthRecords' => $woman->healthRecords,
        ]);
    }

    // Create New Patient
    public function createPatient()
    {
        $puroks = Purok::orderBy('name')->get();

        return view('midwife.create-patient', compact('puroks'));
    }

    // Store New Patient
    public function storePatient(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_initial' => 'nullable|string|max:2',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'nullable|string|max:255',
            'purok_id' => 'required|exists:puroks,id',
            'contact_number' => 'nullable|string|max:20',
            'date_of_birth' => 'required|date|before:today',
        ]);

        $selectedPurok = Purok::findOrFail($request->purok_id);

        $woman = User::create([
            'first_name'     => $request->first_name,
            'middle_initial' => $request->middle_initial,
            'last_name'      => $request->last_name,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'role'           => 'user',
            'address'        => $request->address,
            'barangay'       => $selectedPurok->barangay,
            'purok_id'       => $selectedPurok->id,
            'contact_number' => $request->contact_number,
            'date_of_birth'  => $request->date_of_birth,
            'status'         => 'approved',
        ]);

        // Clear dashboard cache to refresh stats
        Cache::forget('midwife_dashboard_stats_' . auth()->id());

        return redirect()->route('midwife.patients')
            ->with('success', 'Patient ' . $woman->name . ' has been successfully added.');
    }

    // Profile - Redirect to unified profile system
    public function profile()
    {
        return redirect()->route('profile.show');
    }

    // Settings
    public function settings()
    {
        return view('midwife.settings');
    }

    // Pregnant Patients Management
    public function pregnantPatients()
    {
        $search = request('search');
        $status = request('status', 'active');
        
        // Cache pregnancy stats for 5 minutes
        $statsCacheKey = 'pregnancy_stats_' . $status . '_' . ($search ? md5($search) : 'no_search');
        $stats = Cache::remember($statsCacheKey, 300, function () use ($status) {
            return [
                'totalActive' => Pregnancy::active()->count(),
                'totalCompleted' => Pregnancy::completed()->count(),
                'totalHighRisk' => Pregnancy::highRisk()->count(),
            ];
        });
        
        $query = Pregnancy::with('woman')
            ->select('id', 'user_id', 'lmp', 'edd', 'aog', 'is_high_risk', 'created_at')
            ->when($status === 'active', function($q) {
                return $q->active();
            })
            ->when($status === 'completed', function($q) {
                return $q->completed();
            })
            ->when($status === 'high-risk', function($q) {
                return $q->highRisk();
            });

        // Apply search if provided
        if ($search) {
            $query->whereHas('woman', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }
        
        $pregnancies = $query->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('midwife.pregnant-patients', array_merge($stats, compact(
            'pregnancies',
            'search',
            'status'
        )));
    }

    public function pregnancyHistory($id)
    {
        $pregnancy = Pregnancy::with(['checkups' => function($query) {
            $query->with('midwife')->orderBy('scheduled_date', 'desc');
        }])->findOrFail($id);

        $woman = User::where('role', 'user')->find($pregnancy->user_id);
        $pregnancyHistory = Pregnancy::where('user_id', $woman->id)
            ->with('checkups')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('midwife.pregnancy-history', compact(
            'pregnancy',
            'woman',
            'pregnancyHistory'
        ));
    }

    // Notifications
    public function notifications()
    {
        $notifications = auth()->user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('midwife.notifications', compact('notifications'));
    }

    public function showNotification($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);

        if (!$notification->is_read) {
            $notification->markAsRead();
        }

        return view('midwife.notifications.show', compact('notification'));
    }

    public function createNotification()
    {
        return view('midwife.notifications.create');
    }

    public function storeNotification(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'type' => 'required|in:info,warning,success,error',
            'target_role' => 'nullable|in:user,bhw,midwife',
        ]);

        auth()->user()->notifications()->create([
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'target_role' => $request->target_role,
            'is_read' => false,
        ]);

        return redirect()->route('midwife.notifications.index')
            ->with('success', 'Notification created successfully');
    }

    public function markNotificationAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->update(['is_read' => true]);

        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    public function deleteNotification($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->delete();

        return redirect()->route('midwife.notifications.index')
            ->with('success', 'Notification deleted successfully');
    }



    // Checkup Referrals Management
    public function referrals()
    {
        $referrals = CheckupReferral::with(['woman', 'walkInPatient', 'referredByBhw', 'assignedMidwife', 'convertedCheckup'])
            ->where('assigned_midwife_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('midwife.referrals.index', compact('referrals'));
    }

    public function showReferral($id)
    {
        $referral = CheckupReferral::with(['woman', 'walkInPatient', 'referredByBhw', 'assignedMidwife', 'convertedCheckup'])
            ->where('assigned_midwife_id', auth()->id())
            ->findOrFail($id);

        return view('midwife.referrals.show', compact('referral'));
    }

    public function reviewReferral($id)
    {
        $referral = CheckupReferral::where('assigned_midwife_id', auth()->id())->findOrFail($id);
        $referral->update([
            'status' => 'reviewed',
            'reviewed_at' => now(),
        ]);

        return redirect()->route('midwife.referrals.show', $id)
            ->with('success', 'Referral marked as reviewed.');
    }

    public function convertToCheckup($id)
    {
        $referral = CheckupReferral::with(['woman', 'walkInPatient'])->where('assigned_midwife_id', auth()->id())->findOrFail($id);

        // Create the checkup
        $checkup = Checkup::create([
            'user_id' => $referral->user_id,
            'walk_in_patient_id' => $referral->walk_in_patient_id,
            'midwife_id' => auth()->id(),
            'scheduled_by_id' => $referral->referred_by_bhw_id,
            'scheduled_date' => now()->addDay(), // Default to tomorrow
            'scheduled_time' => '09:00:00',
            'purpose' => $referral->reason,
            'notes' => $referral->bhw_notes,
            'status' => 'Scheduled',
        ]);

        // Notify the BHW
        \App\Models\Notification::createNotification(
            $referral->referred_by_bhw_id,
            "Your referral for {$referral->patient_name} has been converted to a scheduled checkup.",
            '✅ Referral Scheduled',
            'success',
            route('bhw.checkups.index')
        );

        $referral->delete();

        return redirect()->route('midwife.checkups.edit', $checkup->id)
            ->with('success', 'Referral accepted and moved to checkups. Please set the final scheduled date and time.');
    }

    public function declineReferral(Request $request, $id)
    {
        $request->validate([
            'midwife_notes' => 'required|string|max:1000',
        ]);

        $referral = CheckupReferral::where('assigned_midwife_id', auth()->id())->findOrFail($id);
        $referral->update([
            'status' => 'declined',
            'midwife_notes' => $request->midwife_notes,
        ]);

        // Notify the BHW
        \App\Models\Notification::createNotification(
            $referral->referred_by_bhw_id,
            "Your referral for {$referral->patient_name} has been declined. Reason: {$request->midwife_notes}",
            '❌ Referral Declined',
            'error',
            route('bhw.referrals.show', $referral->id)
        );

        return redirect()->route('midwife.referrals.index')
            ->with('success', 'Referral declined and BHW notified.');
    }

    // Walk-in Patients Management
    public function walkInPatients()
    {
        $patients = WalkInPatient::with(['recordedBy', 'purok', 'convertedToUser'])
            ->latest()
            ->paginate(10);

        return view('midwife.walk-in-patients.index', compact('patients'));
    }

    public function showWalkInPatient($id)
    {
        $patient = WalkInPatient::with(['recordedBy', 'purok', 'convertedToUser', 'checkupReferrals'])
            ->findOrFail($id);

        return view('midwife.walk-in-patients.show', compact('patient'));
    }

    public function editWalkInPatient($id)
    {
        $patient = WalkInPatient::with(['recordedBy', 'purok'])
            ->findOrFail($id);
        $puroks = Purok::orderBy('name')->get();

        if ($patient->converted_to_user_id) {
            return back()->with('error', 'This walk-in patient has already been converted to a registered user and cannot be edited.');
        }

        return view('midwife.walk-in-patients.edit', compact('patient', 'puroks'));
    }

    public function updateWalkInPatient(Request $request, $id)
    {
        $patient = WalkInPatient::findOrFail($id);

        if ($patient->converted_to_user_id) {
            return back()->with('error', 'This walk-in patient has already been converted to a registered user and cannot be edited.');
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_initial' => 'nullable|string|max:10',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'contact_number' => 'nullable|string|max:20',
            'purok_id' => 'nullable|exists:puroks,id',
            'reason_for_visit' => 'nullable|string|max:500',
        ]);

        $selectedPurok = $request->filled('purok_id')
            ? Purok::find($request->purok_id)
            : null;

        $patient->update([
            'first_name' => $request->first_name,
            'middle_initial' => $request->middle_initial,
            'last_name' => $request->last_name,
            'date_of_birth' => $request->date_of_birth,
            'contact_number' => $request->contact_number,
            'address' => null,
            'barangay' => $selectedPurok?->barangay,
            'purok_id' => $request->purok_id,
            'reason_for_visit' => $request->reason_for_visit,
        ]);

        return redirect()->route('midwife.walk-in-patients.show', $id)
            ->with('success', 'Walk-in patient updated successfully.');
    }

    public function deleteWalkInPatient($id)
    {
        $patient = WalkInPatient::findOrFail($id);

        if ($patient->converted_to_user_id) {
            return back()->with('error', 'This walk-in patient has already been converted to a registered user and cannot be deleted.');
        }

        $patient->delete();

        return redirect()->route('midwife.walk-in-patients.index')
            ->with('success', 'Walk-in patient deleted successfully.');
    }

    public function convertWalkInToUser($id)
    {
        $walkInPatient = WalkInPatient::findOrFail($id);

        if ($walkInPatient->converted_to_user_id) {
            return back()->with('error', 'This walk-in patient has already been converted to a registered user.');
        }

        return view('midwife.walk-in-patients.convert', compact('walkInPatient'));
    }

    public function storeConvertedUser(Request $request, $id)
    {
        $walkInPatient = WalkInPatient::findOrFail($id);

        if ($walkInPatient->converted_to_user_id) {
            return back()->with('error', 'This walk-in patient has already been converted to a registered user.');
        }

        $request->validate([
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'required|string|max:500',
            'barangay' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
        ]);

        $user = User::create([
            'first_name' => $walkInPatient->first_name,
            'middle_initial' => $walkInPatient->middle_initial,
            'last_name' => $walkInPatient->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'status' => 'approved',
            'address' => $request->address,
            'barangay' => $request->barangay,
            'date_of_birth' => $walkInPatient->date_of_birth,
            'contact_number' => $request->contact_number,
        ]);

        $walkInPatient->update([
            'converted_to_user_id' => $user->id,
            'converted_at' => now(),
        ]);

        return redirect()->route('midwife.walk-in-patients.show', $id)
            ->with('success', 'Walk-in patient converted to registered user successfully.');
    }

    // --- Reports ---
    public function reportsIndex(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status', 'all');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = User::where('role', 'user')->where('status', 'approved');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Calculate metrics based on the current scope
        $allPatientsQuery = clone $query;
        $allPatients = $allPatientsQuery->get();

        $totalPatients = $allPatients->count();
        $pregnantPatients = $allPatients->filter(function($user) {
            return $user->pregnancy_status === 'Pregnant';
        })->count();

        // Count completed checkups within the date range
        $completedCheckupsQuery = Checkup::where('status', 'Completed');
        if ($startDate) $completedCheckupsQuery->whereDate('scheduled_date', '>=', $startDate);
        if ($endDate) $completedCheckupsQuery->whereDate('scheduled_date', '<=', $endDate);
        $completedCheckups = $completedCheckupsQuery->count();

        // Count upcoming appointments
        $upcomingAppointments = Checkup::where('status', 'Scheduled')
            ->whereDate('scheduled_date', '>=', Carbon::now())
            ->count();

        // Filter by status if needed
        if ($status === 'pregnant') {
            $query->whereHas('pregnancies', function($q) {
                $q->active();
            });
        } elseif ($status === 'postpartum') {
             $query->whereHas('pregnancies', function($q) {
                $q->completed();
            });
        } elseif ($status === 'not_pregnant') {
             $query->whereDoesntHave('pregnancies', function($q) {
                $q->active();
            })->whereDoesntHave('pregnancies', function($q) {
                $q->completed();
            });
        }

        $patients = $query->paginate(10)->withQueryString();

        return view('midwife.reports.index', compact(
            'patients', 'totalPatients', 'pregnantPatients', 
            'completedCheckups', 'upcomingAppointments', 
            'search', 'status', 'startDate', 'endDate'
        ));
    }

    public function reportsDetails($id)
    {
        $woman = User::with(['healthRecords', 'checkups', 'pregnancies'])->findOrFail($id);
        return view('midwife.reports.details', compact('woman'));
    }

    public function reportsExportCsv(Request $request)
    {
        return back()->with('error', 'CSV export not implemented yet.');
    }

    public function reportsExportPdf(Request $request)
    {
        return back()->with('error', 'PDF export not implemented yet.');
    }

    // --- BHW Presidents ---

    public function bhwPresidentsIndex()
    {
        $bhwPresidents = User::where('role', 'bhw_president')
            ->where('status', '!=', 'archived')
            ->latest()
            ->paginate(15);

        foreach ($bhwPresidents as $president) {
            $president->managed_bhw_count = User::where('role', 'bhw')->count();
            $president->supervised_patients_count = User::where('role', 'user')->count();
        }

        return view('midwife.bhw-presidents.index', compact('bhwPresidents'));
    }

    public function bhwPresidentsCreate()
    {
        $currentPresident = User::where('role', 'bhw_president')
            ->where('status', '!=', 'archived')
            ->latest()
            ->first();

        return view('midwife.bhw-presidents.create', compact('currentPresident'));
    }

    public function bhwPresidentsStore(Request $request)
    {
        $request->validate([
            'first_name'     => 'required|string|max:255',
            'middle_initial' => 'nullable|string|max:1',
            'last_name'      => 'required|string|max:255',
            'email'          => 'required|string|email|max:255|unique:users',
            'password'       => 'required|string|min:8|confirmed',
            'date_of_birth'  => 'required|date|before:today',
            'gender'         => 'required|in:male,female',
            'contact_number' => 'required|string|max:20',
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        $data['role'] = 'bhw_president';
        $data['status'] = 'approved';
        $data['barangay'] = 'Barangay Burgos, San Carlos City, Pangasinan';

        // Archive existing president
        $existingPresident = User::where('role', 'bhw_president')
            ->where('status', '!=', 'archived')
            ->latest()
            ->first();

        if ($existingPresident) {
            $existingPresident->update(['status' => 'archived']);
        }

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $data['profile_image'] = $request->file('profile_image')
                ->store('uploads/profile', 'public');
        }

        User::create($data);

        return redirect()->route('midwife.bhw-presidents.index')
            ->with('success', 'BHW President appointed successfully.');
    }

    public function bhwPresidentsShow($id)
    {
        $bhwPresident = User::where('role', 'bhw_president')->findOrFail($id);
        $stats = [
            'managed_bhw_count'        => User::where('role', 'bhw')->count(),
            'supervised_patients_count' => User::where('role', 'user')->count(),
            'active_pregnancies_count'  => Pregnancy::active()->count(),
        ];

        return view('midwife.bhw-presidents.show', compact('bhwPresident', 'stats'));
    }

    public function bhwPresidentsEdit($id)
    {
        $bhwPresident = User::where('role', 'bhw_president')->findOrFail($id);
        return view('midwife.bhw-presidents.edit', compact('bhwPresident'));
    }

    public function bhwPresidentsUpdate(Request $request, $id)
    {
        $bhwPresident = User::where('role', 'bhw_president')->findOrFail($id);

        $request->validate([
            'first_name'     => 'required|string|max:255',
            'middle_initial' => 'nullable|string|max:1',
            'last_name'      => 'required|string|max:255',
            'email'          => 'required|string|email|max:255|unique:users,email,' . $id,
            'date_of_birth'  => 'required|date|before:today',
            'gender'         => 'required|in:male,female',
            'contact_number' => 'required|string|max:20',
        ]);

        $data = $request->except(['password', 'password_confirmation']);

        if ($request->filled('password')) {
            $request->validate(['password' => 'required|string|min:8|confirmed']);
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('profile_image')) {
            $data['profile_image'] = $request->file('profile_image')
                ->store('uploads/profile', 'public');
        }

        $bhwPresident->update($data);

        return redirect()->route('midwife.bhw-presidents.show', $id)
            ->with('success', 'BHW President updated successfully.');
    }

    public function bhwPresidentsDestroy($id)
    {
        $bhwPresident = User::where('role', 'bhw_president')->findOrFail($id);
        $bhwPresident->delete();

        return redirect()->route('midwife.bhw-presidents.index')
            ->with('success', 'BHW President deleted successfully.');
    }

    // --- BHW Monthly Reports ---

    public function bhwReportsIndex()
    {
        $filter = request('filter', 'all');
        $month = request('month');
        $year = request('year');
        $submissionStatus = request('submission_status', 'all');
        
        $query = BhwMonthlyReport::with(['bhw', 'submittedToPresidentBy', 'approvedByPresident', 'submittedToMidwifeBy']);
        
        if ($filter !== 'all') {
            $query->where('report_type', $filter);
        }

        if ($month) {
            $query->where('report_month', (int) $month);
        }

        if ($year) {
            $query->where('report_year', (int) $year);
        }

        if ($submissionStatus !== 'all') {
            $query->where('submission_status', $submissionStatus);
        }
        
        $reports = $query->latest()->paginate(10)->withQueryString();
        
        return view('midwife.bhw-reports.index', compact('reports', 'filter', 'month', 'year', 'submissionStatus'));
    }

    public function bhwReportsShow($id)
    {
        $report = BhwMonthlyReport::with(['bhw', 'submittedToPresidentBy', 'approvedByPresident', 'submittedToMidwifeBy', 'approvedByMidwife'])
            ->findOrFail($id);

        if ($report->report_type === 'health_records') {
            $baseQuery = HealthRecord::where('recorded_by_id', $report->bhw_id)
                ->whereMonth('created_at', $report->report_month)
                ->whereYear('created_at', $report->report_year)
                ->with('recordedBy', 'patient');

            $healthRecords = $baseQuery->paginate(20);
            $uniquePatients = $baseQuery->select('user_id')->distinct()->count();
            $riskDistribution = [
                'low' => HealthRecord::where('recorded_by_id', $report->bhw_id)->whereMonth('created_at', $report->report_month)->whereYear('created_at', $report->report_year)->where('risk_level', 'Low')->count(),
                'medium' => HealthRecord::where('recorded_by_id', $report->bhw_id)->whereMonth('created_at', $report->report_month)->whereYear('created_at', $report->report_year)->where('risk_level', 'Medium')->count(),
                'high' => HealthRecord::where('recorded_by_id', $report->bhw_id)->whereMonth('created_at', $report->report_month)->whereYear('created_at', $report->report_year)->where('risk_level', 'High')->count(),
            ];

            return view('midwife.bhw-reports.show', compact('report', 'healthRecords', 'uniquePatients', 'riskDistribution'));
        } else {
            $baseQuery = Pregnancy::whereMonth('created_at', $report->report_month)
                ->whereYear('created_at', $report->report_year)
                ->with('woman');

            $pregnancies = $baseQuery->paginate(20);
            $uniquePatients = $baseQuery->select('user_id')->distinct()->count();
            $riskDistribution = [
                'low' => Pregnancy::whereMonth('created_at', $report->report_month)->whereYear('created_at', $report->report_year)->where('is_high_risk', false)->count(),
                'high' => Pregnancy::whereMonth('created_at', $report->report_month)->whereYear('created_at', $report->report_year)->where('is_high_risk', true)->count(),
                'medium' => 0,
            ];

            return view('midwife.bhw-reports.show-pregnancies', compact('report', 'pregnancies', 'uniquePatients', 'riskDistribution'));
        }
    }

    public function bhwReportsPrint($id)
    {
        $report = BhwMonthlyReport::findOrFail($id);
        return view('midwife.bhw-reports.print', compact('report'));
    }

    public function bhwReportsApprove(Request $request, $id)
    {
        $report = BhwMonthlyReport::findOrFail($id);
        $report->approveByMidwife(auth()->id(), $request->input('notes'));

        return redirect()->route('midwife.bhw-reports.index')
            ->with('success', 'BHW Monthly Report approved successfully.');
    }

    public function bhwReportsReject(Request $request, $id)
    {
        $report = BhwMonthlyReport::findOrFail($id);
        $report->rejectByMidwife(auth()->id(), $request->input('notes'));

        return redirect()->route('midwife.bhw-reports.index')
            ->with('success', 'Report rejected and returned to BHW President.');
    }

    public function bhwReportsDestroy($id)
    {
        $report = BhwMonthlyReport::findOrFail($id);
        $report->delete();

        return redirect()->route('midwife.bhw-reports.index')
            ->with('success', 'Report archived successfully.');
    }

}
