<?php

namespace App\Http\Controllers;

use App\Models\BhwMonthlyReport;
use App\Models\User;
use App\Models\Checkup;
use App\Models\HealthRecord;
use App\Models\CheckupReferral;
use App\Models\WalkInPatient;
use App\Models\Pregnancy;
use App\Models\Purok;
use App\Services\MaternalRiskService;
use App\Services\RiskAnalysisService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class BhwController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Dashboard
    public function dashboard()
    {
        // Auto-run alert checks when BHW visits dashboard
        try {
            // Check for overdue checkups
            Checkup::markOverdueCheckups();

            // Re-evaluate risk for women with missed checkups
            $missedWomen = Checkup::missed()->distinct('user_id')->pluck('user_id');
            $riskService = new RiskAnalysisService();
            foreach ($missedWomen as $womanId) {
                $riskService->evaluate($womanId);
            }
        } catch (\Exception $e) {
            \Log::error('BHW dashboard alert check failed: ' . $e->getMessage());
        }

        // Cache dashboard statistics for 5 minutes (300 seconds) for faster loading
        $cacheKey = 'bhw_dashboard_stats_' . auth()->id();
        $stats = Cache::remember($cacheKey, 300, function () {
            return [
                'totalPatients' => User::where('role', 'user')->count(),
                'scheduledCheckups' => Checkup::scheduled()->count(),
                'todayCheckups' => Checkup::whereDate('scheduled_date', today())
                    ->scheduled()
                    ->count(),
                'myHealthRecords' => HealthRecord::byBhw()
                    ->where('recorded_by_id', auth()->id())
                    ->count(),
            ];
        });

        // Only select needed columns for better performance
        $upcomingCheckups = Checkup::with(['woman:id,first_name,middle_initial,last_name', 'midwife:id,first_name,middle_initial,last_name'])
            ->select('id', 'user_id', 'midwife_id', 'scheduled_date', 'status', 'purpose')
            ->scheduled()
            ->upcoming()
            ->orderBy('scheduled_date', 'asc')
            ->limit(10)
            ->get();

        return view('bhw.dashboard', array_merge($stats, compact('upcomingCheckups')));
    }

    // Create Woman (registered or unregistered)
    public function createWoman()
    {
        $puroks = \App\Models\Purok::all();
        return view('bhw.women.create', compact('puroks'));
    }

    // Store Woman
    public function storeWoman(Request $request)
    {
        // Merge checkbox value to boolean
        $request->merge([
            'is_pregnant' => $request->has('is_pregnant'),
        ]);

        $request->validate([
            'patient_type' => 'required|in:registered,unregistered',
            'first_name' => 'required|string|max:255',
            'middle_initial' => 'nullable|string|max:10',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'barangay' => 'nullable|string|max:255',
            'purok_id' => 'nullable|exists:puroks,id',
            'email' => 'nullable|email|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'reason_for_visit' => 'nullable|string|max:500',
            'is_pregnant' => 'boolean',
            'lmp' => 'nullable|date|required_if:is_pregnant,true',
            'edd' => 'nullable|date',
            'gravida' => 'nullable|integer|min:1',
            'para' => 'nullable|integer|min:0',
            'gtpal' => 'nullable|string|max:20',
            'obstetric_history' => 'nullable|string',
        ]);

        $userId = null;

        if ($request->patient_type === 'registered') {
            $request->validate([
                'email' => 'required|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user = \App\Models\User::create([
                'first_name' => $request->first_name,
                'middle_initial' => $request->middle_initial,
                'last_name' => $request->last_name,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender ?? 'female',
                'contact_number' => $request->contact_number,
                'address' => $request->address,
                'barangay' => $request->barangay,
                'purok_id' => $request->purok_id,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role' => 'user',
                'status' => 'pending', // BHW-created users need approval
            ]);

            $userId = $user->id;

            // Create pregnancy record if pregnant
            if ($request->is_pregnant) {
                $edd = $request->edd;
                if (!$edd && $request->lmp) {
                    // Calculate EDD from LMP (add 280 days)
                    $edd = \Carbon\Carbon::parse($request->lmp)->addDays(280);
                }

                $pregnancy = \App\Models\Pregnancy::create([
                    'user_id' => $user->id,
                    'lmp' => $request->lmp ? \Carbon\Carbon::parse($request->lmp) : null,
                    'edd' => $edd ? \Carbon\Carbon::parse($edd) : null,
                    'risk_level' => 'Low',
                    'workflow_status' => 'draft',
                ]);

                // Create maternal care target client with obstetric data
                if ($request->filled('gravida') || $request->filled('para')) {
                    \App\Models\MaternalCareTargetClient::updateOrCreate(
                        ['user_id' => $user->id, 'pregnancy_id' => $pregnancy->id],
                        [
                            'gravida' => $request->gravida ?? 1,
                            'parity' => $request->para ?? 0,
                        ]
                    );
                }
            }

            return redirect()->route('bhw.patients')
                ->with('success', 'Woman created successfully and is pending approval.' . ($request->is_pregnant ? ' Pregnancy record added.' : ''));
        } else {
            // Unregistered (walk-in patient)
            $walkInPatient = \App\Models\WalkInPatient::create([
                'recorded_by_id' => auth()->id(),
                'first_name' => $request->first_name,
                'middle_initial' => $request->middle_initial,
                'last_name' => $request->last_name,
                'date_of_birth' => $request->date_of_birth,
                'contact_number' => $request->contact_number,
                'address' => $request->address,
                'barangay' => $request->barangay ?: 'Barangay Burgos Padlan, San Carlos City, Pangasinan',
                'purok_id' => $request->purok_id,
                'reason_for_visit' => $request->reason_for_visit,
                'notes' => $request->gravida ? "Gravida: {$request->gravida}, Para: {$request->para}" : null,
            ]);

            // Create pregnancy record if pregnant
            if ($request->is_pregnant) {
                $edd = $request->edd;
                if (!$edd && $request->lmp) {
                    // Calculate EDD from LMP (add 280 days)
                    $edd = \Carbon\Carbon::parse($request->lmp)->addDays(280);
                }

                $pregnancy = \App\Models\Pregnancy::create([
                    'walk_in_patient_id' => $walkInPatient->id,
                    'lmp' => $request->lmp ? \Carbon\Carbon::parse($request->lmp) : null,
                    'edd' => $edd ? \Carbon\Carbon::parse($edd) : null,
                    'risk_level' => 'Low',
                    'workflow_status' => 'draft',
                ]);

                // Create maternal care target client with obstetric data
                if ($request->filled('gravida') || $request->filled('para')) {
                    \App\Models\MaternalCareTargetClient::updateOrCreate(
                        ['pregnancy_id' => $pregnancy->id],
                        [
                            'gravida' => $request->gravida ?? 1,
                            'parity' => $request->para ?? 0,
                        ]
                    );
                }
            }

            return redirect()->route('bhw.patients', ['filter' => 'unregistered'])
                ->with('success', 'Unregistered patient created successfully.' . ($request->is_pregnant ? ' Pregnancy record added.' : ''));
        }
    }

    // View Patients (all women combined like midwife)
    public function patients()
    {
        $search = request('search');
        $filter = request('filter', 'all');

        // Get registered patients
        $registeredQuery = User::where('role', 'user')
            ->with(['pregnancies' => function($query) {
                $query->active();
            }, 'purok']);

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

        $registeredPatients = $registeredQuery->get()->map(function($patient) {
            $patient->type = 'registered';
            return $patient;
        });

        // Get unregistered patients
        $unregisteredQuery = WalkInPatient::with(['recordedBy', 'purok', 'convertedToUser'])
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
        $registeredCount = User::where('role', 'user')->count();
        $unregisteredCount = WalkInPatient::whereNull('converted_to_user_id')->count();

        return view('bhw.patients', compact(
            'patients',
            'scheduledCheckups',
            'missedCheckups',
            'registeredCount',
            'unregisteredCount'
        ));
    }

    // ── Pending Patient Approvals ──────────────────────────────
    public function pendingPatients()
    {
        $pending = User::where('role', 'user')->where('status', 'pending')
            ->latest()
            ->paginate(15);

        return view('bhw.pending-patients', compact('pending'));
    }

    public function reviewPatient($id)
    {
        $user = User::where('role', 'user')->where('status', 'pending')->findOrFail($id);
        return view('bhw.review-patient', compact('user'));
    }


    public function approvePatient($id)
    {
        $woman = User::where('role', 'user')->findOrFail($id);
        $woman->update(['status' => 'approved', 'rejection_reason' => null]);

        // Notify the woman
        \App\Models\Notification::createNotification(
            $woman->id,
            'Your registration has been approved! You can now log in to ReproCare.',
            '✅ Registration Approved',
            'success',
            route('user.dashboard')
        );

        return redirect()->route('bhw.pending-patients')
            ->with('success', $woman->name . ' has been approved.');
    }


    public function rejectPatient(Request $request, $id)
    {
        $request->validate(['reason' => 'nullable|string|max:500']);

        $woman = User::where('role', 'user')->findOrFail($id);
        $womanName = $woman->name;

        // Delete the user account
        $woman->delete();

        return redirect()->route('bhw.pending-patients')
            ->with('success', $womanName . ' has been rejected and deleted.');
    }

    public function patientDetails($id)
    {
        $woman = User::where('role', 'user')->with([
            'purok',
            'emergencyContacts',
            'checkups' => function($q) {
                $q->with('midwife')->latest()->select('id', 'user_id', 'midwife_id', 'scheduled_date', 'status', 'purpose');
            },
            'healthRecords' => function($q) {
                $q->with('recordedBy')->latest()->select('id', 'user_id', 'recorded_by_id', 'bp', 'weight', 'heart_rate', 'temperature', 'risk_level', 'notes', 'created_at')->take(50);
            }
        ])->select('id', 'first_name', 'middle_initial', 'last_name', 'email', 'address', 'barangay', 'date_of_birth', 'contact_number', 'partner_name', 'partner_contact')->findOrFail($id);

        return view('bhw.patient-details', [
            'woman' => $woman,
            'checkups' => $woman->checkups,
            'healthRecords' => $woman->healthRecords,
        ]);
    }

    // View patient menstrual cycle records
    public function patientMenstruation($id)
    {
        $woman = User::where('role', 'user')->findOrFail($id);
        $records = $woman->cycles()
            ->orderBy('period_start_date', 'desc')
            ->get();

        // Get statistics using Cycle model
        $averageCycle = \App\Models\Cycle::getAverageCycleLength($id);
        $averagePeriod = \App\Models\Cycle::getAveragePeriodLength($id);

        return view('bhw.patient-menstruation', compact('woman', 'records', 'averageCycle', 'averagePeriod'));
    }

    public function patientMenstruationReport($id)
    {
        $woman = User::where('role', 'user')->findOrFail($id);
        $records = $woman->cycles()
            ->orderBy('period_start_date', 'desc')
            ->get();

        // Get statistics
        $averageCycle = \App\Models\Cycle::getAverageCycleLength($id);
        $averagePeriod = \App\Models\Cycle::getAveragePeriodLength($id);

        // Get predictions (simplified without fertility service)
        $predictions = [];
        foreach ($records as $record) {
            $prediction = [
                'start_date' => $record->period_start_date,
                'end_date' => $record->period_end_date,
                'ovulation' => $record->period_start_date ? $record->period_start_date->copy()->addDays($averageCycle - 14) : null,
                'fertile_window' => [
                    'start' => $record->period_start_date ? $record->period_start_date->copy()->addDays($averageCycle - 19) : null,
                    'end' => $record->period_start_date ? $record->period_start_date->copy()->addDays($averageCycle - 13) : null,
                ],
            ];
            $predictions[] = $prediction;
        }

        return view('bhw.patient-menstruation-report', compact('woman', 'records', 'averageCycle', 'averagePeriod', 'predictions'));
    }

    public function patientMenstruationExport($id)
    {
        $woman = User::where('role', 'user')->findOrFail($id);
        $records = $woman->cycles()
            ->orderBy('period_start_date', 'desc')
            ->get();

        $averageCycle = \App\Models\Cycle::getAverageCycleLength($id);
        $averagePeriod = \App\Models\Cycle::getAveragePeriodLength($id);

        $csv = fopen('php://temp', 'r+');
        
        fputcsv($csv, ['Woman Name', $woman->name]);
        fputcsv($csv, ['Average Cycle Length', $averageCycle ?? 'N/A']);
        fputcsv($csv, ['Average Period Duration', $averagePeriod ?? 'N/A']);
        fputcsv($csv, []);
        fputcsv($csv, ['Start Date', 'End Date', 'Duration (days)', 'Symptoms', 'Notes']);

        foreach ($records as $record) {
            fputcsv($csv, [
                $record->start_date->format('Y-m-d'),
                $record->end_date ? $record->end_date->format('Y-m-d') : 'Ongoing',
                $record->duration,
                is_array($record->symptoms) ? implode(', ', $record->symptoms) : ($record->symptoms ?? 'N/A'),
                $record->notes ?? 'N/A',
            ]);
        }

        rewind($csv);
        $csvContent = stream_get_contents($csv);
        fclose($csv);

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="menstrual_cycle_report_' . $patient->name . '.csv"');
    }

    // View Schedules
    public function schedules()
    {
        $checkups = Checkup::with(['woman', 'midwife', 'scheduledByMidwife', 'scheduledByBhw'])
            ->orderBy('scheduled_date', 'asc')
            ->paginate(10);

        return view('bhw.checkups.index', compact('checkups'));
    }

    // Health Records (Add Only - No Edit/Delete)
    public function healthRecords()
    {
        $healthRecords = HealthRecord::with(['woman', 'walkInPatient', 'recordedBy', 'bhwPresident'])
            ->where('recorded_by_id', auth()->id())
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($inner) use ($search) {
                    $inner->whereHas('woman', function ($womanQuery) use ($search) {
                        $womanQuery->where('first_name', 'like', '%' . $search . '%')
                            ->orWhere('middle_initial', 'like', '%' . $search . '%')
                            ->orWhere('last_name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    })->orWhereHas('walkInPatient', function ($walkInQuery) use ($search) {
                        $walkInQuery->where('first_name', 'like', '%' . $search . '%')
                            ->orWhere('middle_initial', 'like', '%' . $search . '%')
                            ->orWhere('last_name', 'like', '%' . $search . '%')
                            ->orWhere('contact_number', 'like', '%' . $search . '%');
                    });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('bhw.health-records.index', compact('healthRecords'));
    }

    public function createHealthRecord($userId = null, $walkInPatientId = null)
    {
        if ($userId) {
            $woman = User::where('role', 'user')->findOrFail($userId);
            return view('bhw.health-records.create', compact('woman'));
        } elseif ($walkInPatientId) {
            $walkInPatient = WalkInPatient::findOrFail($walkInPatientId);
            return view('bhw.health-records.create-walk-in', compact('walkInPatient'));
        }
    }

    public function storeHealthRecord(Request $request)
    {
        $this->mergeLifestyleInputs($request);

        $request->validate([
            'patient_type' => 'required|in:registered,walk_in',
            'user_id' => 'exclude_unless:patient_type,registered|required|exists:users,id',
            'walk_in_patient_id' => 'exclude_unless:patient_type,walk_in|required|exists:walk_in_patients,id',
            'bp_systolic'        => 'required|integer|min:50|max:300',
            'bp_diastolic'       => 'required|integer|min:30|max:200',
            'weight'             => 'required|numeric|min:0|max:300',
            'height'             => 'nullable|numeric|min:100|max:250',
            'heart_rate'         => 'required|integer|min:0|max:250',
            'temperature'        => 'required|numeric|min:30|max:45',
            'hemoglobin'         => 'nullable|numeric|min:1|max:25',
            'gestational_age'    => 'nullable|integer|min:1|max:45',
            'immunization_status'=> 'nullable|string|max:255',
            'contraceptive_use'  => 'nullable|string|max:255',
            'lab_results'        => 'nullable|string|max:500',
            'smoking_status'     => 'nullable|in:none,former,current',
            'alcohol_use'        => 'nullable|in:none,former,current',
            'drug_use'           => 'nullable|in:none,former,current',
            'lifestyle_notes'    => 'nullable|string|max:1000',
            'obstetric_history'  => 'nullable|string|max:2000',
            'risk_assessment_mode' => 'nullable|in:automatic,manual',
            'risk_level'         => 'nullable|in:Low,Medium,High',
            'risk_notes'         => 'nullable|string|max:1000',
            'notes'              => 'nullable|string|max:1000',
        ]);

        $patientType = $request->patient_type;
        $userId = $request->user_id;
        $walkInPatientId = $request->walk_in_patient_id;

        // Get patient age for risk assessment
        $age = null;
        $barangay = null;

        if ($patientType === 'registered') {
            $patient = User::find($userId);
            $age = $patient?->age;
            $barangay = $patient?->barangay;
        } else {
            $patient = WalkInPatient::find($walkInPatientId);
            $age = $patient?->age;
            $barangay = $patient?->barangay;
        }

        $riskMode = $request->input('risk_assessment_mode', 'automatic');
        if ($riskMode === 'manual') {
            $riskLevel = $request->input('risk_level', 'Low');
            $bmi = app(MaternalRiskService::class)->calculateBmi($request->weight, $request->height);
        } else {
            $assessment = app(MaternalRiskService::class)->assess([
                'age' => $age,
                'bp' => $this->combineBloodPressure($request),
                'weight' => $request->weight,
                'height' => $request->height,
                'smoking_status' => $request->smoking_status,
                'alcohol_use' => $request->alcohol_use,
                'drug_use' => $request->drug_use,
                'obstetric_history' => $request->obstetric_history,
            ]);
            $riskLevel = $assessment['risk_level'];
            $bmi = $assessment['bmi'];
        }

        // Resolve pregnancy ID (only for registered users)
        $pregnancyId = null;
        if ($patientType === 'registered' && $userId) {
            $pregnancyId = \App\Models\HealthRecord::resolvePregnancyIdForWoman((int) $userId, now());
        }

        // Find BHW President
        $bhwPresidentId = User::where('role', 'bhw_president')
            ->where('status', 'approved')
            ->where(function ($query) use ($barangay) {
                $query->whereNull('barangay')
                    ->orWhere('barangay', $barangay);
            })
            ->value('id');

        \App\Models\HealthRecord::create([
            'user_id'            => $patientType === 'registered' ? $userId : null,
            'walk_in_patient_id' => $patientType === 'walk_in' ? $walkInPatientId : null,
            'pregnancy_id'        => $pregnancyId,
            'bp'                  => $this->combineBloodPressure($request),
            'weight'              => $request->weight,
            'height'              => $request->height,
            'bmi'                 => $bmi,
            'heart_rate'          => $request->heart_rate,
            'temperature'         => $request->temperature,
            'hemoglobin'          => $request->hemoglobin,
            'gestational_age'     => $request->gestational_age,
            'immunization_status' => $request->immunization_status,
            'contraceptive_use'   => $request->contraceptive_use,
            'lab_results'         => $request->lab_results,
            'smoking_status'      => $request->smoking_status,
            'alcohol_use'         => $request->alcohol_use,
            'drug_use'            => $request->drug_use,
            'lifestyle_notes'     => $request->lifestyle_notes,
            'obstetric_history'   => $request->obstetric_history,
            'notes'               => $request->notes,
            'risk_level'          => $riskLevel,
            'risk_assessment_mode'=> $riskMode,
            'risk_notes'          => $request->risk_notes,
            'recorded_by_id'      => auth()->id(),
            'bhw_president_id'    => $bhwPresidentId,
            'workflow_status'     => 'recorded_by_bhw',
        ]);

        // 🔴 Trigger automated risk analysis after every health record
        $detectedRisk = $riskLevel;
        if ($riskMode === 'automatic' && $patientType === 'registered') {
            $riskService = new \App\Services\RiskAnalysisService();
            $detectedRisk = $riskService->evaluate($userId);
        }

        $successMsg = 'Health record added successfully.';
        if ($detectedRisk !== 'Low') {
            $successMsg .= " ⚠️ {$detectedRisk} risk level detected — notifications sent.";
        }

        // Redirect based on patient type
        if ($patientType === 'registered') {
            return redirect()->route('bhw.patient-details', $userId)
                ->with('success', $successMsg);
        } else {
            return redirect()->route('bhw.walk-in-patients.show', $walkInPatientId)
                ->with('success', $successMsg);
        }
    }

    private function combineBloodPressure(Request $request): string
    {
        return $request->input('bp_systolic') . '/' . $request->input('bp_diastolic');
    }

    public function submitHealthRecordToPresident($id)
    {
        $healthRecord = HealthRecord::where('recorded_by_id', auth()->id())->findOrFail($id);

        if ($healthRecord->workflow_status !== 'recorded_by_bhw') {
            return back()->with('error', 'This health record has already been submitted.');
        }

        $healthRecord->update([
            'workflow_status' => 'submitted_to_bhw_president',
            'submitted_to_bhw_president_at' => now(),
        ]);

        return back()->with('success', 'Health record submitted to BHW President for review.');
    }

    private function mergeLifestyleInputs(Request $request): void
    {
        $otherLifestyle = trim((string) $request->input('lifestyle_other', ''));
        $notes = trim((string) $request->input('lifestyle_notes', ''));

        if ($otherLifestyle !== '') {
            $notes = trim($notes !== '' ? $notes . "\nOther: " . $otherLifestyle : 'Other: ' . $otherLifestyle);
        }

        $request->merge([
            'smoking_status' => $request->boolean('lifestyle_smoking') ? 'current' : 'none',
            'alcohol_use' => $request->boolean('lifestyle_alcohol') ? 'current' : 'none',
            'drug_use' => $request->boolean('lifestyle_drugs') ? 'current' : 'none',
            'lifestyle_notes' => $notes !== '' ? $notes : null,
        ]);
    }

    // Archive Health Record
    public function archiveHealthRecord(Request $request, $id)
    {
        $healthRecord = \App\Models\HealthRecord::findOrFail($id);
        $reason = $request->input('archived_reason', 'Archived by BHW');

        $healthRecord->archive($reason);
        $healthRecord->delete();

        return redirect()->route('bhw.health-records.index')
            ->with('success', 'Health record archived successfully.');
    }

    // Checkup Referrals - shows referrals created by this BHW
    public function checkups()
    {
        $referrals = CheckupReferral::with(['woman', 'walkInPatient', 'assignedMidwife', 'convertedCheckup'])
            ->where('referred_by_bhw_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('bhw.checkups.index', compact('referrals'));
    }

    public function createCheckup($userId)
    {
        $woman = User::where('role', 'user')->findOrFail($userId);
        return view('bhw.checkups.create', compact('woman'));
    }

    public function storeCheckup(Request $request)
    {
        $request->validate([
            'user_id'       => 'required|exists:users,id',
            'scheduled_date'   => 'required|date|after_or_equal:today',
            'purpose'          => 'nullable|string|max:255',
            'notes'            => 'nullable|string|max:1000',
        ]);

        // Auto-assign to the single midwife
        $midwife = User::where('role', 'midwife')->where('status', 'approved')->first();

        Checkup::create([
            'user_id'         => $request->user_id,
            'midwife_id'      => $midwife ? $midwife->id : null,
            'scheduled_by_id' => auth()->id(),
            'scheduled_date'     => $request->scheduled_date,
            'purpose'            => $request->purpose,
            'notes'              => $request->notes,
            'status'             => 'Scheduled',
        ]);

        return redirect()->route('bhw.checkups.index')
            ->with('success', 'Checkup scheduled successfully');
    }

    public function destroyCheckup($id)
    {
        $checkup = Checkup::findOrFail($id);

        // Prevent deletion if checkup was scheduled by a midwife
        if ($checkup->scheduled_by_midwife_id) {
            return redirect()->route('bhw.checkups.index')
                ->with('error', 'Cannot delete checkups scheduled by midwives.');
        }

        $archiveNote = trim((string) request('archive_reason', 'Archived by BHW'));
        $notes = trim((string) $checkup->notes);
        $checkup->update([
            'status' => 'Archived',
            'notes' => $notes !== '' ? $notes . "\n\nArchived: " . $archiveNote : 'Archived: ' . $archiveNote,
        ]);

        return redirect()->route('bhw.checkups.index')
            ->with('success', 'Checkup archived successfully');
    }

    public function reportsIndex(Request $request)
    {
        $filter = $request->input('filter', 'all');
        
        $reports = BhwMonthlyReport::with('bhw')
            ->where('bhw_id', auth()->id())
            ->when($request->filled('month'), function ($query) use ($request) {
                $query->where('report_month', $request->integer('month'));
            })
            ->when($request->filled('year'), function ($query) use ($request) {
                $query->where('report_year', $request->integer('year'));
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($filter !== 'all', function ($query) use ($filter) {
                $query->where('report_type', $filter);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('bhw.reports.index', compact('reports', 'filter'));
    }

    public function reportsCreate()
    {
        $women = User::where('role', 'user')->where('status', 'approved')->whereHas('healthRecords', function ($query) {
                $query->where('recorded_by_id', auth()->id());
            })
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'middle_initial', 'last_name', 'email']);

        $pregnantWomen = User::where('role', 'user')->where('status', 'approved')->whereHas('pregnancies', function ($query) {
                $query->whereNull('ended_at');
            })
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'middle_initial', 'last_name', 'email']);

        return view('bhw.reports.create', [
            'women' => $women,
            'pregnantWomen' => $pregnantWomen,
            'currentMonth' => now()->month,
            'currentYear' => now()->year,
        ]);
    }

    public function reportsStore(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required|in:health_records,pregnancies',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'report_month' => 'required|integer|between:1,12',
            'report_year' => 'required|integer|min:2020|max:2100',
            'patient_filter' => 'required|in:all,selected',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'integer|exists:users,id',
        ]);

        $patientIds = $validated['patient_filter'] === 'selected'
            ? array_values(array_unique($validated['user_ids'] ?? []))
            : [];

        $filters = [
            'patient_filter' => $validated['patient_filter'],
            'user_ids' => $patientIds,
        ];

        if ($validated['report_type'] === 'health_records') {
            $baseQuery = HealthRecord::where('recorded_by_id', auth()->id())
                ->whereMonth('created_at', $validated['report_month'])
                ->whereYear('created_at', $validated['report_year'])
                ->when($validated['patient_filter'] === 'selected', function ($query) use ($patientIds) {
                    $query->whereIn('user_id', $patientIds);
                });
            
            $totalRecords = $baseQuery->count();
        } else {
            $baseQuery = Pregnancy::whereNull('ended_at')
                ->whereMonth('created_at', $validated['report_month'])
                ->whereYear('created_at', $validated['report_year'])
                ->when($validated['patient_filter'] === 'selected', function ($query) use ($patientIds) {
                    $query->whereIn('user_id', $patientIds);
                });
            
            $totalRecords = $baseQuery->count();
        }

        $report = BhwMonthlyReport::create([
            'bhw_id' => auth()->id(),
            'report_type' => $validated['report_type'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'report_month' => $validated['report_month'],
            'report_year' => $validated['report_year'],
            'filters' => $filters,
            'total_records' => $totalRecords,
            'status' => 'completed',
        ]);

        return redirect()
            ->route('bhw.reports.show', $report->id)
            ->with('success', 'Monthly report created successfully.');
    }

    public function reportsShow($id)
    {
        $report = BhwMonthlyReport::with('bhw')
            ->where('bhw_id', auth()->id())
            ->findOrFail($id);

        if ($report->report_type === 'health_records') {
            $baseQuery = $this->buildReportHealthRecordsQuery(
                $report->bhw_id,
                $report->report_month,
                $report->report_year,
                $report->filters ?? []
            );

            $healthRecords = (clone $baseQuery)->paginate(20);
            $uniquePatients = (clone $baseQuery)
                ->select('user_id')
                ->distinct()
                ->count();
            $riskDistribution = [
                'low' => (clone $baseQuery)->where('risk_level', 'Low')->count(),
                'medium' => (clone $baseQuery)->where('risk_level', 'Medium')->count(),
                'high' => (clone $baseQuery)->where('risk_level', 'High')->count(),
            ];

            return view('bhw.reports.show', compact('report', 'healthRecords', 'uniquePatients', 'riskDistribution'));
        } else {
            // Pregnancy reports
            $baseQuery = Pregnancy::whereNull('ended_at')
                ->whereMonth('created_at', $report->report_month)
                ->whereYear('created_at', $report->report_year)
                ->with('woman')
                ->when(($report->filters['patient_filter'] ?? 'all') === 'selected', function ($query) use ($report) {
                    $query->whereIn('user_id', $report->filters['user_ids'] ?? []);
                });

            $pregnancies = (clone $baseQuery)->paginate(20);
            $uniquePatients = (clone $baseQuery)
                ->select('user_id')
                ->distinct()
                ->count();
            $riskDistribution = [
                'low' => (clone $baseQuery)->where('is_high_risk', false)->count(),
                'high' => (clone $baseQuery)->where('is_high_risk', true)->count(),
                'medium' => 0,
            ];

            return view('bhw.reports.show-pregnancies', compact('report', 'pregnancies', 'uniquePatients', 'riskDistribution'));
        }
    }

    public function reportsPrint($id)
    {
        $report = BhwMonthlyReport::with('bhw')
            ->where('bhw_id', auth()->id())
            ->findOrFail($id);

        if ($report->report_type === 'health_records') {
            $baseQuery = $this->buildReportHealthRecordsQuery(
                $report->bhw_id,
                $report->report_month,
                $report->report_year,
                $report->filters ?? []
            );

            $healthRecords = (clone $baseQuery)->get();
            $uniquePatients = (clone $baseQuery)
                ->select('user_id')
                ->distinct()
                ->count();
            $riskDistribution = [
                'low' => (clone $baseQuery)->where('risk_level', 'Low')->count(),
                'medium' => (clone $baseQuery)->where('risk_level', 'Medium')->count(),
                'high' => (clone $baseQuery)->where('risk_level', 'High')->count(),
            ];

            if (!$report->printed_at) {
                $report->forceFill(['printed_at' => now()])->save();
                $report->refresh();
            }

            return view('bhw.reports.print', compact('report', 'healthRecords', 'uniquePatients', 'riskDistribution'));
        } else {
            // Pregnancy reports
            $baseQuery = Pregnancy::whereNull('ended_at')
                ->whereMonth('created_at', $report->report_month)
                ->whereYear('created_at', $report->report_year)
                ->with('woman')
                ->when(($report->filters['patient_filter'] ?? 'all') === 'selected', function ($query) use ($report) {
                    $query->whereIn('user_id', $report->filters['user_ids'] ?? []);
                });

            $pregnancies = (clone $baseQuery)->get();
            $uniquePatients = (clone $baseQuery)
                ->select('user_id')
                ->distinct()
                ->count();
            $riskDistribution = [
                'low' => (clone $baseQuery)->where('is_high_risk', false)->count(),
                'high' => (clone $baseQuery)->where('is_high_risk', true)->count(),
                'medium' => 0,
            ];

            if (!$report->printed_at) {
                $report->forceFill(['printed_at' => now()])->save();
                $report->refresh();
            }

            return view('bhw.reports.print-pregnancies', compact('report', 'pregnancies', 'uniquePatients', 'riskDistribution'));
        }
    }

    public function reportsDestroy($id)
    {
        $report = BhwMonthlyReport::where('bhw_id', auth()->id())->findOrFail($id);
        $report->delete();

        return redirect()
            ->route('bhw.reports.index')
            ->with('success', 'Report deleted successfully.');
    }

    public function reportsSubmitToPresident($id)
    {
        $report = BhwMonthlyReport::where('bhw_id', auth()->id())->findOrFail($id);
        
        if ($report->submission_status !== 'draft') {
            return redirect()
                ->route('bhw.reports.index')
                ->with('error', 'This report has already been submitted.');
        }

        $report->submitToPresident(auth()->id());

        return redirect()
            ->route('bhw.reports.index')
            ->with('success', 'Report submitted to BHW President successfully.');
    }

    private function buildReportHealthRecordsQuery(int $bhwId, int $month, int $year, array $filters = [])
    {
        return HealthRecord::with(['woman', 'recordedBy'])
            ->where('recorded_by_id', $bhwId)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->when(($filters['patient_filter'] ?? 'all') === 'selected', function ($query) use ($filters) {
                $query->whereIn('user_id', $filters['user_ids'] ?? []);
            })
            ->latest();
    }

    // Profile - Redirect to unified profile system
    public function profile()
    {
        return redirect()->route('profile.show');
    }

    // Pregnancies Index
    public function pregnancies()
    {
        $search = request('search');
        $filter = request('filter', 'all');
        $authId = auth()->id();

        $query = Pregnancy::with(['woman', 'walkInPatient', 'healthRecords', 'healthRecords.recordedBy'])
            ->whereNull('ended_at');

        // Filter by creator
        if ($filter === 'mine') {
            // Pregnancies created by current BHW (through woman creation with pregnancy info)
            $query->where(function ($inner) use ($authId) {
                $inner->whereHas('woman', function ($q) use ($authId) {
                    $q->where('created_by_bhw_id', $authId);
                })->orWhereHas('walkInPatient', function ($q) use ($authId) {
                    $q->where('recorded_by_id', $authId);
                });
            });
        } elseif ($filter === 'midwife') {
            // Pregnancies created by midwife
            $query->whereHas('woman', function ($q) {
                $q->where('created_by_midwife_id', '!=', null);
            });
        } elseif ($filter === 'others') {
            // Pregnancies created by other BHWs
            $query->where(function ($inner) use ($authId) {
                $inner->whereHas('woman', function ($q) use ($authId) {
                    $q->where('created_by_bhw_id', '!=', $authId)
                      ->whereNotNull('created_by_bhw_id');
                })->orWhereHas('walkInPatient', function ($q) use ($authId) {
                    $q->whereNotNull('recorded_by_id')
                        ->where('recorded_by_id', '!=', $authId);
                });
            });
        }

        // Search by patient name
        if ($search) {
            $query->where(function ($outer) use ($search) {
                $outer->whereHas('woman', function ($q) use ($search) {
                    $q->where(function ($query) use ($search) {
                        $query->where('first_name', 'like', "%{$search}%")
                            ->orWhere('middle_initial', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
                })->orWhereHas('walkInPatient', function ($q) use ($search) {
                    $q->where(function ($query) use ($search) {
                        $query->where('first_name', 'like', "%{$search}%")
                            ->orWhere('middle_initial', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
                });
            });
        }

        $pregnancies = $query->latest()->paginate(20);

        // Mark which pregnancies are editable by current BHW
        foreach ($pregnancies as $pregnancy) {
            $pregnancy->is_mine = ($pregnancy->woman && $pregnancy->woman->created_by_bhw_id == $authId)
                || ($pregnancy->walkInPatient && $pregnancy->walkInPatient->recorded_by_id == $authId);
            $pregnancy->is_midwife_created = $pregnancy->woman && $pregnancy->woman->created_by_midwife_id != null;
            $pregnancy->created_by_name = $pregnancy->is_midwife_created
                ? 'Midwife'
                : ($pregnancy->is_mine
                    ? 'You'
                    : ($pregnancy->walkInPatient?->recordedBy?->name
                        ?? $pregnancy->healthRecords->first()?->recordedBy?->name
                        ?? 'Other BHW'));
        }

        return view('bhw.pregnancies.index', compact('pregnancies', 'filter', 'search'));
    }

    public function notifications()
    {
        $notifications = auth()->user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        auth()->user()->notifications()->unread()->update(['is_read' => true]);

        return view('bhw.notifications', compact('notifications'));
    }

    // Settings
    public function settings()
    {
        return view('bhw.settings');
    }

    // Checkup Referrals
    public function referrals()
    {
        $referrals = CheckupReferral::with(['woman', 'walkInPatient', 'assignedMidwife', 'convertedCheckup'])
            ->where('referred_by_bhw_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('bhw.referrals.index', compact('referrals'));
    }

    public function createReferral()
    {
        $women = User::where('role', 'user')->where('status', 'approved')->with('purok')->get(['id', 'first_name', 'middle_initial', 'last_name', 'email', 'purok_id']);
        $walkInPatients = WalkInPatient::notConverted()->latest()->get();
        $puroks = Purok::orderBy('name')->get(['id', 'name']);

        return view('bhw.referrals.create', compact('women', 'walkInPatients', 'puroks'));
    }

    public function storeReferral(Request $request)
    {
        $request->validate([
            'patient_type' => 'required|in:registered,walk_in,new_walk_in',
            'user_id' => 'exclude_unless:patient_type,registered|required|exists:users,id',
            'walk_in_patient_id' => 'exclude_unless:patient_type,walk_in|required|exists:walk_in_patients,id',
            'reason' => 'required|string|max:255',
            'urgency' => 'required|in:routine,urgent,emergency',
            'bhw_notes' => 'nullable|string|max:1000',
            // For new walk-in patient
            'first_name' => 'exclude_unless:patient_type,new_walk_in|required|string|max:255',
            'last_name' => 'exclude_unless:patient_type,new_walk_in|required|string|max:255',
            'middle_initial' => 'exclude_unless:patient_type,new_walk_in|nullable|string|max:10',
            'date_of_birth' => 'exclude_unless:patient_type,new_walk_in|nullable|date',
            'purok_id' => 'exclude_unless:patient_type,new_walk_in|required|exists:puroks,id',
            'contact_number' => 'exclude_unless:patient_type,new_walk_in|nullable|string|max:20',
            'reason_for_visit' => 'exclude_unless:patient_type,new_walk_in|nullable|string|max:500',
        ]);

        $walkInPatientId = null;
        $womanId = null;

        if ($request->patient_type === 'new_walk_in') {
            $walkInPatient = WalkInPatient::create([
                'recorded_by_id' => auth()->id(),
                'first_name' => $request->first_name,
                'middle_initial' => $request->middle_initial,
                'last_name' => $request->last_name,
                'date_of_birth' => $request->date_of_birth,
                'address' => null,
                'barangay' => 'Barangay Burgos Padlan, San Carlos City, Pangasinan',
                'purok_id' => $request->purok_id,
                'contact_number' => $request->contact_number,
                'reason_for_visit' => $request->reason_for_visit,
                'notes' => $request->bhw_notes,
            ]);
            $walkInPatientId = $walkInPatient->id;
        } elseif ($request->patient_type === 'walk_in') {
            $walkInPatientId = $request->walk_in_patient_id;
        } else {
            $womanId = $request->user_id;
        }

        // Get the available midwife
        $midwife = \App\Models\User::where('role', 'midwife')->first();

        $referral = CheckupReferral::create([
            'referred_by_bhw_id' => auth()->id(),
            'user_id' => $womanId,
            'walk_in_patient_id' => $walkInPatientId,
            'assigned_midwife_id' => $midwife ? $midwife->id : null,
            'reason' => $request->reason,
            'urgency' => $request->urgency,
            'bhw_notes' => $request->bhw_notes,
            'status' => 'pending',
        ]);

        // Update existing referrals without assigned midwife
        \App\Models\CheckupReferral::whereNull('assigned_midwife_id')->update([
            'assigned_midwife_id' => $midwife ? $midwife->id : null
        ]);

        // Notify the midwife if available
        if ($midwife) {
            \App\Models\Notification::createNotification(
                $midwife->id,
                "New checkup referral from BHW: {$referral->patient_name} - {$request->reason}",
                '📋 New Referral',
                'info',
                route('midwife.referrals.show', $referral->id)
            );
        }

        return redirect()->route('bhw.referrals.index')
            ->with('success', 'Checkup referral created successfully and sent to midwife.');
    }

    public function showReferral($id)
    {
        $referral = CheckupReferral::with(['woman', 'walkInPatient', 'assignedMidwife', 'referredByBhw', 'convertedCheckup'])
            ->where('referred_by_bhw_id', auth()->id())
            ->findOrFail($id);

        return view('bhw.referrals.show', compact('referral'));
    }

    // Walk-in Patients Management
    public function walkInPatients()
    {
        $patients = WalkInPatient::with(['recordedBy', 'purok'])
            ->where('recorded_by_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('bhw.walk-in-patients.index', compact('patients'));
    }

    public function createWalkInPatient()
    {
        $puroks = \App\Models\Purok::all(['id', 'name']);
        return view('bhw.walk-in-patients.create', compact('puroks'));
    }

    public function storeWalkInPatient(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_initial' => 'nullable|string|max:10',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string|max:500',
            'barangay' => 'nullable|string|max:255',
            'purok_id' => 'nullable|exists:puroks,id',
            'contact_number' => 'nullable|string|max:20',
            'reason_for_visit' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        WalkInPatient::create([
            'recorded_by_id' => auth()->id(),
            'first_name' => $request->first_name,
            'middle_initial' => $request->middle_initial,
            'last_name' => $request->last_name,
            'date_of_birth' => $request->date_of_birth,
            'address' => $request->address,
            'barangay' => $request->barangay,
            'purok_id' => $request->purok_id,
            'contact_number' => $request->contact_number,
            'reason_for_visit' => $request->reason_for_visit,
            'notes' => $request->notes,
        ]);

        return redirect()->route('bhw.walk-in-patients.index')
            ->with('success', 'Walk-in patient recorded successfully.');
    }

    public function showWalkInPatient($id)
    {
        $patient = WalkInPatient::with(['recordedBy', 'purok', 'checkupReferrals'])
            ->findOrFail($id);

        return view('bhw.walk-in-patients.show', compact('patient'));
    }

    public function editWalkInPatient($id)
    {
        $patient = WalkInPatient::with(['recordedBy', 'purok'])
            ->where('recorded_by_id', auth()->id())
            ->findOrFail($id);

        if ($patient->converted_to_user_id) {
            return back()->with('error', 'This walk-in patient has already been converted to a registered user and cannot be edited.');
        }

        $puroks = \App\Models\Purok::all();
        return view('bhw.walk-in-patients.edit', compact('patient', 'puroks'));
    }

    public function updateWalkInPatient(Request $request, $id)
    {
        $patient = WalkInPatient::where('recorded_by_id', auth()->id())->findOrFail($id);

        if ($patient->converted_to_user_id) {
            return back()->with('error', 'This walk-in patient has already been converted to a registered user and cannot be edited.');
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_initial' => 'nullable|string|max:10',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'barangay' => 'nullable|string|max:255',
            'purok_id' => 'nullable|exists:puroks,id',
            'reason_for_visit' => 'nullable|string|max:500',
        ]);

        $patient->update([
            'first_name' => $request->first_name,
            'middle_initial' => $request->middle_initial,
            'last_name' => $request->last_name,
            'date_of_birth' => $request->date_of_birth,
            'contact_number' => $request->contact_number,
            'address' => $request->address,
            'barangay' => $request->barangay,
            'purok_id' => $request->purok_id,
            'reason_for_visit' => $request->reason_for_visit,
        ]);

        return redirect()->route('bhw.walk-in-patients.show', $id)
            ->with('success', 'Walk-in patient updated successfully.');
    }

    public function deleteWalkInPatient($id)
    {
        $patient = WalkInPatient::where('recorded_by_id', auth()->id())->findOrFail($id);

        if ($patient->converted_to_user_id) {
            return back()->with('error', 'This walk-in patient has already been converted to a registered user and cannot be deleted.');
        }

        $patient->delete();

        return redirect()->route('bhw.walk-in-patients.index')
            ->with('success', 'Walk-in patient deleted successfully.');
    }

    public function convertWalkInToUser($id)
    {
        $walkInPatient = WalkInPatient::findOrFail($id);

        if ($walkInPatient->converted_to_user_id) {
            return back()->with('error', 'This walk-in patient has already been converted to a registered user.');
        }

        return view('bhw.walk-in-patients.convert', compact('walkInPatient'));
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

        return redirect()->route('bhw.walk-in-patients.show', $id)
            ->with('success', 'Walk-in patient converted to registered user successfully.');
    }
}
