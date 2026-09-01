<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\BhwMonthlyReport;
use App\Models\Checkup;
use App\Models\MaternalDeath;
use App\Models\MaternalMorbidity;
use App\Models\Pregnancy;
use App\Models\SupplyRequest;
use App\Models\User;
use App\Services\AIInsightService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ChoController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::whereIn('role', ['rhu', 'midwife', 'bhw_president', 'bhw'])->count();
        $totalPatients = User::where('role', 'user')->where('status', 'approved')->count();
        $activePregnancies = Pregnancy::active()->count();
        $totalSupplyRequests = SupplyRequest::count();
        $pendingSupplyRequests = SupplyRequest::where('status', 'submitted')->count();
        $totalDeaths = MaternalDeath::count();
        $totalNearMiss = MaternalMorbidity::count();
        $ancCoverageRate = $this->ancCoverageRate();
        $recentRequests = SupplyRequest::with('requestedBy')->latest()->limit(5)->get();
        $recentDeaths = MaternalDeath::latest('death_date')->limit(5)->get();

        return view('cho.dashboard', compact(
            'totalUsers',
            'totalPatients',
            'activePregnancies',
            'totalSupplyRequests',
            'pendingSupplyRequests',
            'totalDeaths',
            'totalNearMiss',
            'ancCoverageRate',
            'recentRequests',
            'recentDeaths'
        ));
    }

    public function settings()
    {
        return view('cho.settings');
    }

    public function users(Request $request)
    {
        $search = $request->input('search');
        $role = $request->input('role', 'all');

        $users = User::whereIn('role', ['rhu', 'midwife', 'bhw_president', 'bhw'])
            ->when($role !== 'all', fn ($query) => $query->where('role', $role))
            ->when($search, function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('first_name', 'like', "%{$search}%")
                        ->orWhere('middle_initial', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('contact_number', 'like', "%{$search}%")
                        ->orWhere('barangay', 'like', "%{$search}%")
                        ->orWhere('rhu_assignment', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('cho.users.index', compact('users', 'search', 'role'));
    }

    public function createUser()
    {
        return view('cho.users.create');
    }

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'role' => 'required|in:rhu,midwife,bhw_president,bhw',
            'first_name' => 'required|string|max:255',
            'middle_initial' => 'nullable|string|max:2',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female',
            'contact_number' => 'required|string|max:20',
            'rhu_assignment' => 'nullable|string|max:255',
            'cho_office' => 'nullable|string|max:255',
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['status'] = 'approved';
        $data['registered_by_cho_id'] = auth()->id();

        $user = User::create($data);
        ActivityLog::log('create', "CHO registered {$user->name} as {$user->role}", $user);

        return redirect()->route('cho.users.index')->with('success', 'Staff account registered successfully.');
    }

    public function userDetails($id)
    {
        $user = User::whereIn('role', ['rhu', 'midwife', 'bhw_president', 'bhw'])->findOrFail($id);

        return view('cho.users.show', compact('user'));
    }

    public function approveUser($id)
    {
        $user = User::whereIn('role', ['rhu', 'midwife', 'bhw_president', 'bhw'])->findOrFail($id);
        $user->update(['status' => 'approved', 'rejection_reason' => null]);
        ActivityLog::log('approve', "CHO approved staff account for {$user->name}", $user);

        return back()->with('success', 'Staff account approved.');
    }

    public function rejectUser(Request $request, $id)
    {
        $request->validate(['reason' => 'nullable|string|max:500']);

        $user = User::whereIn('role', ['rhu', 'midwife', 'bhw_president', 'bhw'])->findOrFail($id);
        $name = $user->name;
        $user->update(['status' => 'archived', 'rejection_reason' => $request->input('reason')]);
        $user->delete();
        ActivityLog::log('archive', "CHO rejected and archived staff account for {$name}");

        return redirect()->route('cho.users.index')->with('success', 'Staff account rejected and archived.');
    }

    public function deactivateUser($id)
    {
        $user = User::whereIn('role', ['rhu', 'midwife', 'bhw_president', 'bhw'])->findOrFail($id);
        $user->update(['status' => 'suspended']);
        ActivityLog::log('update', "CHO suspended staff account for {$user->name}", $user);

        return back()->with('success', 'Staff account suspended.');
    }

    public function activateUser($id)
    {
        $user = User::whereIn('role', ['rhu', 'midwife', 'bhw_president', 'bhw'])->findOrFail($id);
        $user->update(['status' => 'approved']);
        ActivityLog::log('update', "CHO reactivated staff account for {$user->name}", $user);

        return back()->with('success', 'Staff account activated.');
    }

    public function supplyRequests(Request $request)
    {
        $requests = SupplyRequest::with(['requestedBy', 'approvedBy'])
            ->when($request->filled('status') && $request->status !== 'all', fn ($query) => $query->where('status', $request->status))
            ->when($request->filled('urgency') && $request->urgency !== 'all', fn ($query) => $query->where('urgency', $request->urgency))
            ->latest('submitted_at')
            ->paginate(15)
            ->withQueryString();

        return view('cho.supply-requests.index', compact('requests'));
    }

    public function showSupplyRequest($id)
    {
        $supplyRequest = SupplyRequest::with(['requestedBy', 'approvedBy'])->findOrFail($id);

        return view('cho.supply-requests.show', compact('supplyRequest'));
    }

    public function approveSupplyRequest(Request $request, $id)
    {
        $data = $request->validate([
            'expected_delivery_date' => 'nullable|date|after_or_equal:today',
            'cho_notes' => 'nullable|string|max:1000',
        ]);

        $supplyRequest = SupplyRequest::findOrFail($id);
        $supplyRequest->update([
            'status' => 'approved',
            'approved_by_id' => auth()->id(),
            'cho_notes' => $data['cho_notes'] ?? null,
            'expected_delivery_date' => $data['expected_delivery_date'] ?? null,
            'reviewed_at' => now(),
            'approved_at' => now(),
        ]);
        ActivityLog::log('approve', "CHO approved supply request for {$supplyRequest->supply_name}", $supplyRequest);

        return redirect()->route('cho.supply-requests.index')->with('success', 'Supply request approved.');
    }

    public function rejectSupplyRequest(Request $request, $id)
    {
        $data = $request->validate(['cho_notes' => 'required|string|max:1000']);

        $supplyRequest = SupplyRequest::findOrFail($id);
        $supplyRequest->update([
            'status' => 'declined',
            'approved_by_id' => auth()->id(),
            'cho_notes' => $data['cho_notes'],
            'reviewed_at' => now(),
        ]);
        ActivityLog::log('reject', "CHO declined supply request for {$supplyRequest->supply_name}", $supplyRequest);

        return redirect()->route('cho.supply-requests.index')->with('success', 'Supply request declined.');
    }

    public function patients(Request $request)
    {
        $search = $request->input('search');
        $barangay = $request->input('barangay');
        $status = $request->input('status', 'approved');

        $patients = User::where('role', 'user')
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->when($search, function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('contact_number', 'like', "%{$search}%");
                });
            })
            ->when($barangay, fn ($query) => $query->where('barangay', $barangay))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('cho.patients.index', compact('patients', 'search', 'barangay', 'status'));
    }

    public function patientDetails($id)
    {
        $patient = User::where('role', 'user')->findOrFail($id);
        $pregnancies = Pregnancy::where('user_id', $id)->latest()->get();
        $healthRecords = \App\Models\HealthRecord::where('user_id', $id)->latest()->paginate(10);

        return view('cho.patients.show', compact('patient', 'pregnancies', 'healthRecords'));
    }

    public function pregnancies(Request $request)
    {
        $status = $request->input('status');
        $riskLevel = $request->input('risk_level');
        $search = $request->input('search');

        $pregnancies = Pregnancy::with('user')
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($riskLevel, fn ($query) => $query->where('risk_level', $riskLevel))
            ->when($search, function ($query) use ($search) {
                $query->whereHas('user', function ($inner) use ($search) {
                    $inner->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('cho.pregnancies.index', compact('pregnancies', 'status', 'riskLevel', 'search'));
    }

    public function pregnancyDetails($id)
    {
        $pregnancy = Pregnancy::with('user', 'healthRecords')->findOrFail($id);

        return view('cho.pregnancies.show', compact('pregnancy'));
    }

    public function immunizationRecords(Request $request)
    {
        $search = $request->input('search');
        $barangay = $request->input('barangay');

        $records = \App\Models\HealthRecord::where('record_type', 'immunization')
            ->with('user')
            ->when($search, function ($query) use ($search) {
                $query->whereHas('user', function ($inner) use ($search) {
                    $inner->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                });
            })
            ->when($barangay, fn ($query) => $query->whereHas('user', fn ($inner) => $inner->where('barangay', $barangay)))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('cho.immunization.index', compact('records', 'search', 'barangay'));
    }

    public function reports(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $status = $request->input('status');

        $reports = BhwMonthlyReport::with('submittedBy')
            ->whereYear('month', '=', date('Y', strtotime($month)))
            ->whereMonth('month', '=', date('m', strtotime($month)))
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('cho.reports.index', compact('reports', 'month', 'status'));
    }

    public function exportReportsCsv(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $reports = BhwMonthlyReport::whereYear('month', '=', date('Y', strtotime($month)))
            ->whereMonth('month', '=', date('m', strtotime($month)))
            ->get();

        $csv = "BHW,Month,Pregnant Women,Checkups,Deliveries\n";
        foreach ($reports as $report) {
            $csv .= "{$report->submittedBy->name},{$report->month},{$report->pregnant_count},{$report->checkup_count},{$report->delivery_count}\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=reports-{$month}.csv");
    }

    public function exportReportsPdf(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $reports = BhwMonthlyReport::with('submittedBy')->whereYear('month', '=', date('Y', strtotime($month)))
            ->whereMonth('month', '=', date('m', strtotime($month)))
            ->get();

        return view('cho.reports.pdf', compact('reports', 'month'));
    }

    public function staffIndex(Request $request)
    {
        $search = $request->input('search');
        $role = $request->input('role', 'all');

        $staff = User::whereIn('role', ['rhu', 'midwife', 'bhw_president', 'bhw'])
            ->when($role !== 'all', fn ($query) => $query->where('role', $role))
            ->when($search, function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('cho.staff.index', compact('staff', 'search', 'role'));
    }

    public function midwives(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status', 'approved');

        $midwives = User::where('role', 'midwife')
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->when($search, function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('cho.staff.midwives', compact('midwives', 'search', 'status'));
    }

    public function bhws(Request $request)
    {
        $search = $request->input('search');
        $barangay = $request->input('barangay');
        $status = $request->input('status', 'approved');

        $bhws = User::where('role', 'bhw')
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->when($search, function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($barangay, fn ($query) => $query->where('barangay', $barangay))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('cho.staff.bhws', compact('bhws', 'search', 'barangay', 'status'));
    }

    public function aiDashboard()
    {
        $insights = app(AIInsightService::class)->generateInsights([
            'totalPregnant' => Pregnancy::active()->count(),
            'highRiskCount' => Pregnancy::active()->highRisk()->count(),
            'ancCoverageRate' => $this->ancCoverageRate(),
            'facilityBirthRate' => $this->facilityBirthRate(),
            'totalDeaths' => MaternalDeath::count(),
            'totalNearMiss' => MaternalMorbidity::count(),
        ]);

        return view('cho.ai-dashboard.index', compact('insights'));
    }

    public function aiInsights(Request $request)
    {
        $data = $request->validate(['question' => 'required|string|max:500']);
        $answer = app(AIInsightService::class)->chat($data['question'], [
            'active_pregnancies' => Pregnancy::active()->count(),
            'high_risk_pregnancies' => Pregnancy::active()->highRisk()->count(),
            'pending_supply_requests' => SupplyRequest::where('status', 'submitted')->count(),
            'maternal_deaths' => MaternalDeath::count(),
            'near_miss_events' => MaternalMorbidity::count(),
        ]);

        return response()->json(['answer' => $answer]);
    }

    public function maternalDeaths(Request $request)
    {
        $deaths = MaternalDeath::with(['user', 'walkInPatient', 'recordedBy', 'purok'])
            ->when($request->filled('audit_status') && $request->audit_status !== 'all', fn ($query) => $query->where('audit_status', $request->audit_status))
            ->latest('death_date')
            ->paginate(15)
            ->withQueryString();

        return view('cho.maternal-deaths.index', compact('deaths'));
    }

    public function showMaternalDeath($id)
    {
        $death = MaternalDeath::with(['user', 'walkInPatient', 'pregnancy', 'recordedBy', 'reviewedBy', 'purok'])->findOrFail($id);

        return view('cho.maternal-deaths.show', compact('death'));
    }

    public function auditMaternalDeath(Request $request, $id)
    {
        $data = $request->validate([
            'audit_status' => 'required|in:reviewed,closed',
            'audit_notes' => 'required|string|max:2000',
        ]);

        $death = MaternalDeath::findOrFail($id);
        $death->update([
            'audit_status' => $data['audit_status'],
            'audit_notes' => $data['audit_notes'],
            'reviewed_by_id' => auth()->id(),
            'reviewed_at' => now(),
        ]);
        ActivityLog::log('update', "CHO audited maternal death case for {$death->patient_name}", $death);

        return back()->with('success', 'Maternal death audit updated.');
    }

    public function logs()
    {
        $logs = ActivityLog::with('user')->latest()->paginate(25);

        return view('cho.logs.index', compact('logs'));
    }

    public function analytics()
    {
        $totalPregnant = Pregnancy::active()->count();
        $highRiskCount = Pregnancy::active()->highRisk()->count();
        $totalCompleted = Pregnancy::completed()->count();
        $ancCoverageRate = $this->ancCoverageRate();
        $facilityBirthRate = $this->facilityBirthRate();
        $smsEnabledCount = User::where('role', 'user')->whereNotNull('contact_number')->where('sms_opt_out', false)->count();
        $monthlyPregnancies = Pregnancy::selectRaw("DATE_FORMAT(created_at, '%b %Y') as period, COUNT(*) as total")
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('period')
            ->orderByRaw('MIN(created_at)')
            ->pluck('total', 'period')
            ->toArray();
        $causes = MaternalDeath::selectRaw('cause_category, COUNT(*) as total')
            ->groupBy('cause_category')
            ->pluck('total', 'cause_category')
            ->toArray();

        // Rule-based insights (AI fallback only)
        $aiInsights = app(AIInsightService::class)->generateInsights([
            'totalPregnant' => $totalPregnant,
            'highRiskCount' => $highRiskCount,
            'ancCoverageRate' => $ancCoverageRate,
            'facilityBirthRate' => $facilityBirthRate,
            'totalDeaths' => array_sum($causes),
            'totalNearMiss' => MaternalMorbidity::count(),
        ]);

        // Prioritized patients list (top 10)
        try {
            $patientIds = User::where('role', 'user')->where('status', 'approved')->pluck('id')->toArray();
            $prioritized = app(\App\Services\RiskAnalysisService::class)->prioritize($patientIds)->take(10);
        } catch (\Throwable $e) {
            \Log::error('CHO analytics prioritization failed: ' . $e->getMessage());
            $prioritized = collect();
        }

        return view('cho.analytics', compact(
            'totalPregnant',
            'highRiskCount',
            'totalCompleted',
            'ancCoverageRate',
            'facilityBirthRate',
            'smsEnabledCount',
            'monthlyPregnancies',
            'causes',
            'aiInsights',
            'prioritized'
        ));
    }

    public function analyticsChat(Request $request)
    {
        $data = $request->validate(['question' => 'required|string|max:500']);
        $answer = app(AIInsightService::class)->chat($data['question'], [
            'active_pregnancies' => Pregnancy::active()->count(),
            'high_risk_pregnancies' => Pregnancy::active()->highRisk()->count(),
            'pending_supply_requests' => SupplyRequest::where('status', 'submitted')->count(),
            'maternal_deaths' => MaternalDeath::count(),
            'near_miss_events' => MaternalMorbidity::count(),
        ]);

        return response()->json(['answer' => $answer]);
    }

    private function ancCoverageRate(): float
    {
        $completedPregnancies = Pregnancy::completed()->withCount('healthRecords')->get();
        $totalCompleted = $completedPregnancies->count();

        if ($totalCompleted === 0) {
            return 0;
        }

        $compliant = $completedPregnancies->filter(fn ($pregnancy) => $pregnancy->health_records_count >= 4)->count();

        return round(($compliant / $totalCompleted) * 100, 1);
    }

    private function facilityBirthRate(): float
    {
        $completed = Pregnancy::completed()->count();

        if ($completed === 0) {
            return 0;
        }

        $facilityDeliveries = Pregnancy::completed()
            ->whereNotNull('facility_delivery_place')
            ->where('facility_delivery_place', '!=', 'home')
            ->count();

        return round(($facilityDeliveries / $completed) * 100, 1);
    }
}
