<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pregnancy;
use App\Models\Checkup;
use App\Models\HealthRecord;
use App\Models\BhwMonthlyReport;
use App\Models\BhwAssignment;
use App\Models\Purok;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class BhwPresidentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isBhwPresident()) {
                abort(403, 'Access denied. BHW President only.');
            }
            return $next($request);
        });
    }

    // Dashboard
    public function dashboard()
    {
        // Cache dashboard statistics for 5 minutes
        $cacheKey = 'bhw_president_dashboard_stats';
        $stats = Cache::remember($cacheKey, 300, function () {
            return [
                'totalPatients' => User::where('role', 'user')->count(),
                'totalBhws' => User::where('role', 'bhw')->count(),
                'activePregnancies' => Pregnancy::active()->count(),
                'highRiskPregnancies' => Pregnancy::active()->highRisk()->count(),
                'scheduledCheckups' => Checkup::scheduled()->count(),
                'missedCheckups' => Checkup::missed()->count(),
                'completedCheckups' => Checkup::where('status', 'Completed')->count(),
                'totalHealthRecords' => HealthRecord::count(),
                'monthlyReports' => BhwMonthlyReport::count(),
            ];
        });

        // Recent activity
        $recentCheckups = Checkup::with(['woman', 'scheduledByBhw'])
            ->latest('scheduled_date')
            ->limit(10)
            ->get();

        $recentHealthRecords = HealthRecord::with(['woman', 'recordedBy'])
            ->latest()
            ->limit(10)
            ->get();

        // High-risk pregnancies needing attention
        $highRiskPregnancies = Pregnancy::active()
            ->highRisk()
            ->with(['woman', 'checkups' => function ($query) {
                $query->latest('scheduled_date');
            }])
            ->latest()
            ->limit(5)
            ->get();

        $highRiskPregnanciesCount = $highRiskPregnancies->count();

        return view('bhw-president.dashboard', array_merge($stats, compact(
            'recentCheckups',
            'recentHealthRecords',
            'highRiskPregnancies',
            'highRiskPregnanciesCount'
        )));
    }

    public function pendingPatients()
    {
        $search = request('search');

        $query = User::where('role', 'user')->where('status', 'pending');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('middle_initial', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('barangay', 'like', '%' . $search . '%');
            });
        }

        $pending = $query->latest()->paginate(10)->withQueryString();

        return view('bhw-president.pending-patients', compact('pending'));
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

        return redirect()->route('bhw-president.pending-patients')
            ->with('success', $woman->name . ' has been approved.');
    }

    public function rejectPatient(Request $request, $id)
    {
        $request->validate(['reason' => 'nullable|string|max:500']);

        $woman = User::where('role', 'user')->findOrFail($id);
        $womanName = $woman->name;
        $woman->delete();

        return redirect()->route('bhw-president.pending-patients')
            ->with('success', $womanName . ' has been rejected and deleted.');
    }

    // BHW Management
    public function bhws()
    {
        $search = request('search');
        $status = request('status');

        $query = User::where('role', 'bhw')->with(['purok', 'activeBhwAssignment.purok']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('middle_initial', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('barangay', 'like', '%' . $search . '%');
            });
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $bhws = $query->latest()->paginate(15);

        // Get performance stats for each BHW
        foreach ($bhws as $bhw) {
            $bhw->health_records_count = HealthRecord::where('recorded_by_id', $bhw->id)->count();
            $bhw->checkups_scheduled = Checkup::where('scheduled_by_id', $bhw->id)->count();
            $bhw->monthly_reports_count = BhwMonthlyReport::where('bhw_id', $bhw->id)->count();
        }

        return view('bhw-president.bhws', compact('bhws'));
    }

    // Create BHW Form
    public function createBhw()
    {
        $puroks = Purok::orderBy('barangay')->orderBy('name')->get();

        return view('bhw-president.create-bhw', compact('puroks'));
    }

    // Store BHW
    public function storeBhw(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_initial' => 'nullable|string|max:2',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'purok_id' => 'nullable|exists:puroks,id',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female',
            'phone' => 'required|string|max:20',
            'certification_number' => 'required|string|max:255',
            'certification_date' => 'required|date',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $validated;
        $data['password'] = Hash::make($validated['password']);
        $data['role'] = 'bhw';
        $data['status'] = 'approved';
        $data['contact_number'] = $validated['phone'];
        $data['phone'] = $validated['phone'];
        $data['address'] = null;

        $selectedPurok = $request->filled('purok_id') ? Purok::find($request->purok_id) : null;
        $data['barangay'] = $selectedPurok?->barangay ?? 'Burgos';

        if ($request->hasFile('profile_image')) {
            $imageName = time() . '.' . $request->profile_image->extension();
            $request->profile_image->move(public_path('images/uploads/profile'), $imageName);
            $data['profile_image'] = 'uploads/profile/' . $imageName;
        }

        User::create($data);

        Cache::forget('bhw_president_dashboard_stats');

        return redirect()->route('bhw-president.bhws.index')
            ->with('success', 'BHW created successfully.');
    }

    // BHW Details
    public function bhwDetails($id)
    {
        $bhw = User::where('role', 'bhw')->with(['purok', 'activeBhwAssignment.purok'])->findOrFail($id);
        $puroks = Purok::orderBy('barangay')->orderBy('name')->get();

        $healthRecords = HealthRecord::where('recorded_by_id', $bhw->id)
            ->with('woman')
            ->latest()
            ->paginate(20);

        $checkups = Checkup::where('scheduled_by_id', $bhw->id)
            ->with('woman')
            ->latest()
            ->paginate(20);

        $monthlyReports = BhwMonthlyReport::where('bhw_id', $bhw->id)
            ->latest()
            ->paginate(10);

        return view('bhw-president.bhw-details', compact(
            'bhw',
            'puroks',
            'healthRecords',
            'checkups',
            'monthlyReports'
        ));
    }

    public function healthRecords()
    {
        $president = auth()->user();

        $healthRecords = HealthRecord::with(['woman', 'walkInPatient', 'recordedBy'])
            ->whereNotNull('recorded_by_id')
            ->when($president->barangay, function ($query) use ($president) {
                $query->where(function ($inner) use ($president) {
                    $inner->whereHas('woman', fn ($q) => $q->where('barangay', $president->barangay))
                        ->orWhereHas('walkInPatient', fn ($q) => $q->where('barangay', $president->barangay));
                });
            })
            ->latest()
            ->paginate(15);

        return view('bhw-president.health-records.index', compact('healthRecords'));
    }

    public function editHealthRecord($id)
    {
        $healthRecord = HealthRecord::with(['woman', 'walkInPatient', 'recordedBy'])->findOrFail($id);

        return view('bhw-president.health-records.edit', compact('healthRecord'));
    }

    public function updateHealthRecord(Request $request, $id)
    {
        $healthRecord = HealthRecord::findOrFail($id);

        $validated = $request->validate([
            'bp' => 'required|string|max:20',
            'weight' => 'nullable|numeric|min:0|max:300',
            'heart_rate' => 'nullable|integer|min:0|max:250',
            'temperature' => 'nullable|numeric|min:30|max:45',
            'notes' => 'nullable|string|max:1000',
            'risk_level' => 'required|in:Low,Medium,High',
            'workflow_notes' => 'nullable|string|max:1000',
        ]);

        $healthRecord->update([
            'bp' => $validated['bp'],
            'weight' => $validated['weight'],
            'heart_rate' => $validated['heart_rate'],
            'temperature' => $validated['temperature'],
            'notes' => $validated['notes'],
            'risk_level' => $validated['risk_level'],
            'workflow_notes' => $validated['workflow_notes'],
            'bhw_president_id' => auth()->id(),
        ]);

        return redirect()->route('bhw-president.health-records.index')
            ->with('success', 'Health record updated successfully.');
    }

    public function passHealthRecordToMidwife(Request $request, $id)
    {
        $healthRecord = HealthRecord::with('recordedBy')->findOrFail($id);

        $healthRecord->update([
            'bhw_president_id' => auth()->id(),
            'bhw_president_approved_at' => now(),
            'workflow_status' => 'submitted_to_midwife',
            'submitted_to_midwife_at' => now(),
            'workflow_notes' => $request->input('workflow_notes'),
        ]);

        $midwife = User::where('role', 'midwife')->where('status', 'approved')->first();
        if ($midwife) {
            \App\Models\Notification::createNotification(
                $midwife->id,
                'A health record for ' . $healthRecord->patient_name . ' was reviewed by the BHW President and is ready for midwife review.',
                'Health Record Ready for Review',
                'info',
                route('midwife.health-records.show', $healthRecord->id)
            );
        }

        return redirect()->route('bhw-president.health-records.index')
            ->with('success', 'Health record passed to the midwife.');
    }

    public function assignPurok(Request $request, $id)
    {
        $bhw = User::where('role', 'bhw')->findOrFail($id);

        $validated = $request->validate([
            'purok_id' => 'required|exists:puroks,id',
        ]);

        $purok = Purok::findOrFail($validated['purok_id']);

        if (!empty($bhw->barangay) && !empty($purok->barangay) && strcasecmp($bhw->barangay, $purok->barangay) !== 0) {
            return back()->withErrors([
                'purok_id' => 'The selected purok does not belong to this BHW\'s barangay.',
            ])->withInput();
        }

        $bhw->update([
            'purok_id' => $purok->id,
        ]);

        BhwAssignment::where('bhw_id', $bhw->id)->update(['is_active' => false]);
        BhwAssignment::updateOrCreate(
            [
                'bhw_id' => $bhw->id,
                'purok_id' => $purok->id,
            ],
            [
                'assigned_by_id' => auth()->id(),
                'assigned_at' => now(),
                'is_active' => true,
                'notes' => 'Synced from BHW details assignment.',
            ]
        );

        \App\Models\Notification::createNotification(
            $bhw->id,
            'Your assigned purok is now ' . $purok->name . '.',
            'Purok Assignment Updated',
            'info',
            route('bhw.dashboard')
        );

        return redirect()->route('bhw-president.bhws.details', $bhw->id)
            ->with('success', $bhw->name . ' is now assigned to ' . $purok->name . '.');
    }

    // Archive BHW
    public function archiveBhw($id)
    {
        $bhw = User::where('role', 'bhw')->findOrFail($id);
        $bhw->update([
            'status' => 'archived',
            'archived_at' => now(),
        ]);

        Cache::forget('bhw_president_dashboard_stats');

        return redirect()->route('bhw-president.bhws.index')
            ->with('success', 'BHW ' . $bhw->name . ' has been archived.');
    }

    // Mark BHW as Inactive
    public function markInactive($id)
    {
        $bhw = User::where('role', 'bhw')->findOrFail($id);
        $bhw->update(['status' => 'inactive']);

        Cache::forget('bhw_president_dashboard_stats');

        return redirect()->route('bhw-president.bhws.index')
            ->with('success', 'BHW ' . $bhw->name . ' has been marked as inactive.');
    }

    // Activate BHW
    public function activateBhw($id)
    {
        $bhw = User::where('role', 'bhw')->findOrFail($id);
        $bhw->update(['status' => 'approved']);

        Cache::forget('bhw_president_dashboard_stats');

        return redirect()->route('bhw-president.bhws.index')
            ->with('success', 'BHW ' . $bhw->name . ' has been activated.');
    }

    // Delete BHW
    public function deleteBhw($id)
    {
        $bhw = User::where('role', 'bhw')->findOrFail($id);

        if (($bhw->status ?? 'approved') === 'approved') {
            return redirect()->route('bhw-president.bhws.details', $bhw->id)
                ->withErrors(['delete' => 'Active BHW accounts cannot be deleted. Mark the BHW inactive first.']);
        }

        // Delete profile image if exists
        if ($bhw->profile_image) {
            $publicDisk = \Illuminate\Support\Facades\Storage::disk('public');
            if ($publicDisk->exists($bhw->profile_image)) {
                $publicDisk->delete($bhw->profile_image);
            }
        }

        $bhw->delete();

        Cache::forget('bhw_president_dashboard_stats');

        return redirect()->route('bhw-president.bhws.index')
            ->with('success', 'BHW ' . $bhw->name . ' has been deleted.');
    }

    // Analytics Dashboard
    public function analytics()
    {
        // Maternal health indicators
        $maternalStats = [
            'totalPregnancies' => Pregnancy::count(),
            'activePregnancies' => Pregnancy::active()->count(),
            'completedPregnancies' => Pregnancy::completed()->count(),
            'highRiskCount' => Pregnancy::active()->highRisk()->count(),
            'highRiskPercentage' => Pregnancy::active()->count() > 0 
                ? round((Pregnancy::active()->highRisk()->count() / Pregnancy::active()->count()) * 100, 2)
                : 0,
        ];

        // Service utilization
        $serviceStats = [
            'checkupsCompleted' => Checkup::where('status', 'Completed')->count(),
            'checkupsMissed' => Checkup::missed()->count(),
            'healthRecordsTotal' => HealthRecord::count(),
            'averageCheckupsPerPatient' => User::where('role', 'user')->count() > 0
                ? round(Checkup::where('status', 'Completed')->count() / User::where('role', 'user')->count(), 2)
                : 0,
        ];

        // Monthly trends (last 6 months)
        $monthlyTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthlyTrends[] = [
                'month' => $date->format('M Y'),
                'pregnancies' => Pregnancy::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
                'checkups' => Checkup::whereMonth('scheduled_date', $date->month)
                    ->whereYear('scheduled_date', $date->year)
                    ->count(),
                'healthRecords' => HealthRecord::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
            ];
        }

        return view('bhw-president.analytics', compact(
            'maternalStats',
            'serviceStats',
            'monthlyTrends'
        ));
    }

    // Coverage Report
    public function coverage()
    {
        $purokId = request('purok');
        $month = request('month', Carbon::now()->month);
        $year = request('year', Carbon::now()->year);

        $query = User::where('role', 'user')->with(['purok', 'checkups', 'healthRecords', 'pregnancies']);

        if ($purokId) {
            $query->where('purok_id', $purokId);
        }

        $registeredPatients = $query->get()->map(function ($patient) {
            $patient->coverage_type = 'registered';
            return $patient;
        });

        $walkInQuery = \App\Models\WalkInPatient::with(['purok', 'checkupReferrals', 'pregnancies'])
            ->whereNull('converted_to_user_id');

        if ($purokId) {
            $walkInQuery->where('purok_id', $purokId);
        }

        $walkInPatients = $walkInQuery->get()->map(function ($patient) {
            $patient->coverage_type = 'walk_in';
            return $patient;
        });

        $combinedPatients = $registeredPatients->concat($walkInPatients)->sortBy(function ($patient) {
            return $patient->coverage_type === 'walk_in'
                ? $patient->full_name
                : $patient->name;
        })->values();

        $page = request('page', 1);
        $perPage = 20;
        $patients = new \Illuminate\Pagination\LengthAwarePaginator(
            $combinedPatients->forPage($page, $perPage),
            $combinedPatients->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Get unique puroks for filter
        $purokIds = User::where('role', 'user')->select('purok_id')->distinct()->pluck('purok_id')
            ->merge(\App\Models\WalkInPatient::select('purok_id')->distinct()->pluck('purok_id'))
            ->filter()
            ->unique();
        $puroks = \App\Models\Purok::whereIn('id', $purokIds)
            ->orderBy('name')
            ->get();

        // Coverage statistics
        $patientCollection = $combinedPatients;
        $coverageStats = [
            'totalPatients' => $combinedPatients->count(),
            'walkInPatients' => $walkInPatients->count(),
            'patientsWithCheckups' => $patientCollection->filter(function($patient) {
                if (($patient->coverage_type ?? 'registered') === 'walk_in') {
                    return $patient->checkupReferrals && $patient->checkupReferrals->count() > 0;
                }
                return $patient->checkups && $patient->checkups->count() > 0;
            })->count(),
            'patientsWithHealthRecords' => $patientCollection->filter(function($patient) {
                if (($patient->coverage_type ?? 'registered') === 'walk_in') {
                    return \App\Models\HealthRecord::where('walk_in_patient_id', $patient->id)->exists();
                }
                return $patient->healthRecords && $patient->healthRecords->count() > 0;
            })->count(),
            'patientsWithPregnancies' => $patientCollection->filter(function($patient) {
                return $patient->pregnancies && $patient->pregnancies->count() > 0;
            })->count(),
            'coveragePercentage' => $combinedPatients->count() > 0
                ? round(($patientCollection->filter(function($patient) {
                    if (($patient->coverage_type ?? 'registered') === 'walk_in') {
                        return \App\Models\HealthRecord::where('walk_in_patient_id', $patient->id)->exists();
                    }
                    return $patient->healthRecords && $patient->healthRecords->count() > 0;
                })->count() / $combinedPatients->count()) * 100, 2)
                : 0,
        ];

        return view('bhw-president.coverage', compact(
            'patients',
            'puroks',
            'coverageStats',
            'purokId',
            'month',
            'year'
        ));
    }

    // High-Risk Report
    public function highRisk()
    {
        $highRiskPregnancies = Pregnancy::active()
            ->highRisk()
            ->with(['woman', 'checkups' => function($q) {
                $q->latest()->limit(5);
            }])
            ->latest()
            ->paginate(20);

        return view('bhw-president.high-risk', compact('highRiskPregnancies'));
    }

    // Reports Index
    public function reports()
    {
        $filter = request('filter', 'all');
        
        $reports = BhwMonthlyReport::with('bhw')
            ->when($filter !== 'all', function ($query) use ($filter) {
                $query->where('report_type', $filter);
            })
            ->latest()
            ->paginate(20);

        return view('bhw-president.reports', compact('reports', 'filter'));
    }

    // Report Details
    public function reportShow($id)
    {
        $report = BhwMonthlyReport::with('bhw')->findOrFail($id);

        if ($report->report_type === 'health_records') {
            $healthRecords = HealthRecord::with(['woman', 'recordedBy'])
                ->where('recorded_by_id', $report->bhw_id)
                ->whereMonth('created_at', $report->report_month)
                ->whereYear('created_at', $report->report_year)
                ->when(($report->filters['patient_filter'] ?? 'all') === 'selected', function ($query) use ($report) {
                    $query->whereIn('user_id', $report->filters['user_ids'] ?? []);
                })
                ->latest()
                ->get();

            $uniquePatients = $healthRecords->pluck('user_id')->unique()->count();

            $riskDistribution = [
                'low' => $healthRecords->where('risk_level', 'Low')->count(),
                'medium' => $healthRecords->where('risk_level', 'Medium')->count(),
                'high' => $healthRecords->where('risk_level', 'High')->count(),
            ];

            return view('bhw-president.report-show', compact(
                'report',
                'healthRecords',
                'uniquePatients',
                'riskDistribution'
            ));
        } else {
            // Pregnancy reports
            $pregnancies = Pregnancy::whereNull('ended_at')
                ->whereMonth('created_at', $report->report_month)
                ->whereYear('created_at', $report->report_year)
                ->with('woman')
                ->when(($report->filters['patient_filter'] ?? 'all') === 'selected', function ($query) use ($report) {
                    $query->whereIn('user_id', $report->filters['user_ids'] ?? []);
                })
                ->latest()
                ->get();

            $uniquePatients = $pregnancies->pluck('user_id')->unique()->count();
            $riskDistribution = [
                'low' => $pregnancies->where('is_high_risk', false)->count(),
                'high' => $pregnancies->where('is_high_risk', true)->count(),
                'medium' => 0,
            ];

            return view('bhw-president.report-show-pregnancies', compact(
                'report',
                'pregnancies',
                'uniquePatients',
                'riskDistribution'
            ));
        }
    }

    // Delete Report
    public function reportDelete($id)
    {
        $report = BhwMonthlyReport::findOrFail($id);
        $report->delete();

        return redirect()->route('bhw-president.reports.index')
            ->with('success', 'Report archived successfully.');
    }

    // Approve Report and Submit to Midwife
    public function reportApprove(Request $request, $id)
    {
        $report = BhwMonthlyReport::findOrFail($id);
        
        if ($report->submission_status !== 'submitted_to_president') {
            return redirect()->route('bhw-president.reports.index')
                ->with('error', 'This report cannot be approved at this stage.');
        }

        $report->update([
            'submission_status' => 'submitted_to_midwife',
            'approved_by_president' => auth()->id(),
            'approved_by_president_at' => now(),
            'president_notes' => $request->input('notes'),
            'submitted_to_midwife_by' => auth()->id(),
            'submitted_to_midwife_at' => now(),
        ]);

        return redirect()->route('bhw-president.reports.index')
            ->with('success', 'Report approved and submitted to Midwife successfully.');
    }

    // Reject Report
    public function reportReject(Request $request, $id)
    {
        $request->validate([
            'notes' => 'required|string',
        ]);

        $report = BhwMonthlyReport::findOrFail($id);
        
        if ($report->submission_status !== 'submitted_to_president') {
            return redirect()->route('bhw-president.reports.index')
                ->with('error', 'This report cannot be rejected at this stage.');
        }

        $report->rejectByPresident(auth()->id(), $request->input('notes'));

        return redirect()->route('bhw-president.reports.index')
            ->with('success', 'Report rejected and returned to BHW.');
    }

    // Settings
    public function settings()
    {
        return view('bhw-president.settings');
    }
}
