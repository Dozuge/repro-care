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
        $search       = $request->input('search');
        $barangay     = $request->input('barangay');
        $status       = $request->input('status', 'approved');
        $riskLevel    = $request->input('risk_level');
        $ageGroup     = $request->input('age_group');
        $trimester    = $request->input('trimester');
        $showArchived = $request->boolean('show_archived');

        $query = User::where('role', 'user')
            ->with(['purok', 'pregnancies' => fn($q) => $q->active(), 'healthRecords' => fn($q) => $q->latest()]);

        if ($showArchived) {
            $query->onlyTrashed();
        } else {
            $query->when($status !== 'all', fn ($q) => $q->where('status', $status));
        }

        // Text Search
        if ($search) {
            $cleanSearch = ltrim($search, '#');
            $query->where(function ($inner) use ($search, $cleanSearch) {
                $inner->where('id', $cleanSearch)
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_initial', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('contact_number', 'like', "%{$search}%")
                    ->orWhere('barangay', 'like', "%{$search}%")
                    ->orWhere('medical_history', 'like', "%{$search}%");
            });
        }

        // Barangay filter
        if ($barangay) {
            $query->where('barangay', 'like', "%{$barangay}%");
        }

        // Age Group filter (Teenage <19, Adult 19-34, Advanced 35+)
        if ($ageGroup === 'teen') {
            $query->whereNotNull('date_of_birth')
                  ->where('date_of_birth', '>', now()->subYears(19)->toDateString());
        } elseif ($ageGroup === 'adult') {
            $query->whereNotNull('date_of_birth')
                  ->where('date_of_birth', '<=', now()->subYears(19)->toDateString())
                  ->where('date_of_birth', '>', now()->subYears(35)->toDateString());
        } elseif ($ageGroup === 'advanced') {
            $query->whereNotNull('date_of_birth')
                  ->where('date_of_birth', '<=', now()->subYears(35)->toDateString());
        }

        // Risk Level filter
        if ($riskLevel) {
            if ($riskLevel === 'high_risk_only') {
                $query->where(function ($q) {
                    $q->whereHas('pregnancies', fn($p) => $p->active()->whereIn('risk_level', ['High', 'Critical']))
                      ->orWhereHas('healthRecords', fn($h) => $h->whereIn('risk_level', ['High', 'Critical']));
                });
            } else {
                $query->where(function ($q) use ($riskLevel) {
                    $q->whereHas('pregnancies', fn($p) => $p->active()->where('risk_level', ucfirst($riskLevel)))
                      ->orWhereHas('healthRecords', fn($h) => $h->where('risk_level', ucfirst($riskLevel)));
                });
            }
        }

        // Trimester filter (1st: 1-13, 2nd: 14-26, 3rd: 27+)
        if ($trimester) {
            $query->whereHas('pregnancies', function ($p) use ($trimester) {
                $p->active();
                if ($trimester == '1') {
                    $p->where('aog_weeks', '<=', 13);
                } elseif ($trimester == '2') {
                    $p->whereBetween('aog_weeks', [14, 26]);
                } elseif ($trimester == '3') {
                    $p->where('aog_weeks', '>=', 27);
                }
            });
        }

        $patients = $query->latest()->paginate(15)->withQueryString();

        $barangays = User::where('role', 'user')
            ->whereNotNull('barangay')
            ->where('barangay', '!=', '')
            ->distinct()
            ->pluck('barangay')
            ->toArray();

        return view('cho.patients.index', compact(
            'patients',
            'search',
            'barangay',
            'status',
            'riskLevel',
            'ageGroup',
            'trimester',
            'barangays',
            'showArchived'
        ));
    }

    public function patientDetails($id)
    {
        $patient = User::withTrashed()->where('role', 'user')->findOrFail($id);
        $pregnancies = Pregnancy::where('user_id', $id)->latest()->get();
        $healthRecords = \App\Models\HealthRecord::where('user_id', $id)->latest()->paginate(10);
        $availableVideos = \App\Models\LearningMaterial::videos()->latest()->take(6)->get();

        return view('cho.patients.show', compact('patient', 'pregnancies', 'healthRecords', 'availableVideos'));
    }

    public function archivePatient($id)
    {
        $patient = User::where('role', 'user')->findOrFail($id);
        $name = $patient->name;
        $patient->delete(); // Soft delete only

        ActivityLog::log('archive', "Archived patient record for {$name}", $patient);

        return back()->with('success', "Patient record for '{$name}' has been moved to archives.");
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

        // ── 1. Monthly Pregnancy Registrations Trend (Last 12 Months) ─────────
        $monthlyPregnancies = Pregnancy::selectRaw("DATE_FORMAT(created_at, '%b %Y') as period, COUNT(*) as total")
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('period')
            ->orderByRaw('MIN(created_at)')
            ->pluck('total', 'period')
            ->toArray();

        // ── 2. Maternal Mortality & Morbidity Causes ───────────────────────────
        $causes = MaternalDeath::selectRaw('cause_category, COUNT(*) as total')
            ->groupBy('cause_category')
            ->pluck('total', 'cause_category')
            ->toArray();

        $totalDeaths = array_sum($causes);
        $totalNearMiss = MaternalMorbidity::count();

        // ── 3. Adolescent / Teenage Pregnancy Analysis (<19 years) ────────────
        $teenPatientsQuery = User::where('role', 'user')
            ->whereNotNull('date_of_birth')
            ->where('date_of_birth', '>', now()->subYears(19)->toDateString())
            ->whereHas('pregnancies', fn($q) => $q->active());

        $teenPregnancies = $teenPatientsQuery->count();

        $teenByBarangay = User::where('role', 'user')
            ->whereNotNull('date_of_birth')
            ->where('date_of_birth', '>', now()->subYears(19)->toDateString())
            ->whereHas('pregnancies', fn($q) => $q->active())
            ->whereNotNull('barangay')
            ->selectRaw('barangay, COUNT(*) as total')
            ->groupBy('barangay')
            ->orderByDesc('total')
            ->pluck('total', 'barangay')
            ->toArray();

        $teenHotspots = array_keys(array_slice($teenByBarangay, 0, 3, true));

        // ── 4. High-Risk Distribution by Barangay ─────────────────────────────
        $highRiskByBarangay = Pregnancy::active()->highRisk()
            ->join('users', 'pregnancies.user_id', '=', 'users.id')
            ->whereNotNull('users.barangay')
            ->selectRaw('users.barangay, COUNT(*) as total')
            ->groupBy('users.barangay')
            ->orderByDesc('total')
            ->pluck('total', 'barangay')
            ->toArray();

        $highRiskHotspots = array_keys(array_slice($highRiskByBarangay, 0, 3, true));

        // ── 5. Dynamic Context-Aware Strategic Interventions Engine ───────────
        $insightService = app(AIInsightService::class);
        $analyticsPayload = [
            'totalPregnant'     => $totalPregnant,
            'highRiskCount'     => $highRiskCount,
            'ancCoverageRate'   => $ancCoverageRate,
            'facilityBirthRate' => $facilityBirthRate,
            'totalDeaths'       => $totalDeaths,
            'totalNearMiss'     => $totalNearMiss,
            'teenPregnancies'   => $teenPregnancies,
            'teenHotspots'      => $teenHotspots,
            'highRiskHotspots'  => $highRiskHotspots,
        ];

        $strategicInterventions = $insightService->generateStrategicInterventions($analyticsPayload);
        $aiInsights = $insightService->generateInsights($analyticsPayload);

        // ── 6. Prioritized Patients List (Top 10 High-Risk) ───────────────────
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
            'totalDeaths',
            'totalNearMiss',
            'teenPregnancies',
            'teenByBarangay',
            'highRiskByBarangay',
            'strategicInterventions',
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
