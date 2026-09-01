<?php

namespace App\Http\Controllers;

use App\Models\Pregnancy;
use App\Models\Cycle;
use App\Models\MenstruationDaily;
use App\Models\Checkup;
use App\Models\HealthRecord;
use App\Models\Notification;
use App\Services\CyclePredictionService;
use App\Services\MaternalRiskService;
use App\Services\RiskAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Dashboard
    public function dashboard()
    {
        $user = Auth::user();
        
        // Auto-run risk analysis and alerts when user visits dashboard
        try {
            // Check for overdue checkups and send alerts
            Checkup::markOverdueCheckups();
            
            // Re-evaluate user's risk level
            $riskService = new RiskAnalysisService();
            $riskService->evaluate($user->id);
        } catch (\Exception $e) {
            // Silently fail - don't break dashboard if alerts fail
            \Log::error('Dashboard alert check failed: ' . $e->getMessage());
        }
        
        $activePregnancy = $user->pregnancies()->active()->first();
        $upcomingCheckups = $user->checkups()->scheduled()->upcoming()->count();
        $unreadNotifications = $user->notifications()->unread()->count();
        $nextPeriod = Cycle::predictNextPeriod($user->id);

        return view('user.dashboard', compact(
            'activePregnancy',
            'upcomingCheckups',
            'unreadNotifications',
            'nextPeriod'
        ));
    }

    // Profile Management - Redirect to unified profile system
    public function profile()
    {
        return redirect()->route('profile.show');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'barangay' => 'nullable|string|max:255',
        ]);

        $user->update($request->only(['name', 'address', 'barangay']));

        return redirect()->route('user.dashboard')
            ->with('success', 'Profile updated successfully');
    }

    // Pregnancy Tracking
    public function pregnancies()
    {
        $pregnancies = Auth::user()->pregnancies()->orderBy('created_at', 'desc')->paginate(10);
        return view('user.pregnancies.index', compact('pregnancies'));
    }

    public function createPregnancy()
    {
        if (Auth::user()->pregnancies()->active()->exists()) {
            return redirect()->route('user.pregnancies.index')
                ->withErrors(['pregnancy' => 'You already have an active pregnancy record.']);
        }

        return view('user.pregnancies.create');
    }

    public function storePregnancy(Request $request)
    {
        $this->mergePregnancyLifestyleInputs($request);

        $request->validate([
            'lmp' => 'required|date|before:today',
            'gravida' => 'nullable|integer|min:1',
            'para' => 'nullable|integer|min:0',
            'bp_systolic' => 'nullable|integer|min:50|max:300',
            'bp_diastolic' => 'nullable|integer|min:30|max:200',
            'weight' => 'nullable|numeric|min:20|max:300',
            'height' => 'nullable|numeric|min:100|max:250',
            'smoking_status' => 'nullable|in:none,former,current',
            'alcohol_status' => 'nullable|in:none,former,current',
            'drug_use_status' => 'nullable|in:none,former,current',
            'obstetric_history' => 'nullable|string|max:2000',
            'lifestyle_notes' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ]);

        if (Auth::user()->pregnancies()->active()->exists()) {
            return back()
                ->withErrors(['pregnancy' => 'You already have an active pregnancy record.'])
                ->withInput();
        }

        $assessment = app(MaternalRiskService::class)->assess([
            'age' => Auth::user()->age,
            'blood_pressure' => $this->combineBloodPressure($request),
            'weight' => $request->weight,
            'height' => $request->height,
            'smoking_status' => $request->smoking_status,
            'alcohol_status' => $request->alcohol_status,
            'drug_use_status' => $request->drug_use_status,
            'obstetric_history' => $request->obstetric_history,
        ]);

        $pregnancy = Pregnancy::create([
            'user_id' => Auth::id(),
            'lmp' => $request->lmp,
            'blood_pressure' => $this->combineBloodPressure($request),
            'weight' => $request->weight,
            'height' => $request->height,
            'bmi' => $assessment['bmi'],
            'smoking_status' => $request->smoking_status,
            'alcohol_status' => $request->alcohol_status,
            'drug_use_status' => $request->drug_use_status,
            'obstetric_history' => $request->obstetric_history,
            'lifestyle_notes' => $request->lifestyle_notes,
            'risk_assessment_mode' => 'automatic',
            'risk_level' => $assessment['risk_level'],
            'risk_notes' => $assessment['reasons'] ? implode(' ', $assessment['reasons']) : null,
            'is_high_risk' => $assessment['is_high_risk'],
            'notes' => $request->notes,
        ]);

        HealthRecord::create([
            'user_id' => Auth::id(),
            'pregnancy_id' => $pregnancy->id,
            'bp' => $this->combineBloodPressure($request),
            'weight' => $request->weight,
            'height' => $request->height,
            'bmi' => $assessment['bmi'],
            'gestational_age' => $pregnancy->aog,
            'smoking_status' => $request->smoking_status,
            'alcohol_use' => $request->alcohol_status,
            'drug_use' => $request->drug_use_status,
            'lifestyle_notes' => $request->lifestyle_notes,
            'obstetric_history' => $request->obstetric_history,
            'notes' => $request->notes,
            'risk_level' => $assessment['risk_level'],
            'risk_assessment_mode' => 'automatic',
            'risk_notes' => $assessment['reasons'] ? implode(' ', $assessment['reasons']) : null,
        ]);

        // Create maternal care target client with obstetric data
        if ($request->filled('gravida') || $request->filled('para')) {
            \App\Models\MaternalCareTargetClient::updateOrCreate(
                ['user_id' => Auth::id(), 'pregnancy_id' => $pregnancy->id],
                [
                    'gravida' => $request->gravida,
                    'parity' => $request->para,
                ]
            );
        }

        return redirect()->route('user.pregnancies.index')
            ->with('success', 'Pregnancy record added successfully');
    }

    // Menstruation Tracking
    public function menstruation()
    {
        $records = Auth::user()->cycles()
            ->orderBy('period_start_date', 'desc')
            ->paginate(10);

        // Use Cycle model for predictions
        $nextPeriod = Cycle::predictNextPeriod(Auth::id());
        $averageCycle = Cycle::getAverageCycleLength(Auth::id());
        $averagePeriod = Cycle::getAveragePeriodLength(Auth::id());

        return view('user.menstruation.index', compact('records', 'nextPeriod', 'averageCycle', 'averagePeriod'));
    }

    public function createMenstruationRecord()
    {
        return view('user.menstruation.create');
    }

    public function storeMenstruationRecord(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);

        $record = Cycle::create([
            'user_id' => Auth::id(),
            'period_start_date' => $validated['start_date'],
            'period_end_date' => $validated['end_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('user.menstruation.index')
            ->with('success', 'Period record added successfully');
    }

    public function destroyMenstruationRecord($id)
    {
        $record = Cycle::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $record->delete();

        return redirect()->route('user.menstruation.index')
            ->with('success', 'Period record deleted successfully');
    }

    // Menstruation Calendar with Cycle Prediction
    public function menstruationCalendar()
    {
        $userId = Auth::id();
        
        // Get current month/year from request or use current
        $currentDate = request('date') ? \Carbon\Carbon::parse(request('date')) : now();
        $month = $currentDate->month;
        $year = $currentDate->year;
        
        // Get calendar data using CyclePredictionService
        $cyclePredictionService = new CyclePredictionService();
        $calendarData = $cyclePredictionService->getCalendarData($userId, $year, $month);
        
        $calendarDays = $calendarData['calendar_days'];
        $cycles = $calendarData['cycles'];
        $predictions = $calendarData['predictions'];
        $currentCyclePrediction = $calendarData['current_cycle_prediction'] ?? null;
        $averageCycle = $calendarData['average_cycle_length'];
        $averagePeriod = $calendarData['average_period_length'];
        
        // Get last period for reference
        $lastPeriod = Cycle::where('user_id', $userId)
            ->orderBy('period_start_date', 'desc')
            ->first();
        
        return view('user.menstruation.calendar', compact(
            'calendarDays',
            'month',
            'year',
            'currentDate',
            'cycles',
            'predictions',
            'currentCyclePrediction',
            'averageCycle',
            'averagePeriod',
            'lastPeriod'
        ));
    }

    // View Checkups (Read-only)
    public function checkups()
    {
        $checkups = Auth::user()->checkups()
            ->with('midwife')
            ->orderBy('scheduled_date', 'desc')
            ->paginate(10);

        return view('user.checkups', compact('checkups'));
    }

    // View Health Records (Read-only)
    public function healthRecords()
    {
        $healthRecords = Auth::user()->healthRecords()
            ->with('recordedBy')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.health-records', compact('healthRecords'));
    }

    // Create Health Record (Self-record)
    public function createHealthRecord()
    {
        return view('user.health-records.create');
    }

    // Store Health Record (Self-record)
    public function storeHealthRecord(Request $request)
    {
        $request->validate([
            'bp' => 'required|string|max:20',
            'weight' => 'required|numeric|min:0|max:300',
            'heart_rate' => 'required|numeric|min:0|max:250',
            'temperature' => 'required|numeric|min:30|max:45',
            'notes' => 'nullable|string|max:1000',
        ]);

        HealthRecord::create([
            'user_id' => Auth::id(),
            'pregnancy_id' => HealthRecord::resolvePregnancyIdForWoman(Auth::id(), now()),
            'bp' => $request->bp,
            'weight' => $request->weight,
            'heart_rate' => $request->heart_rate,
            'temperature' => $request->temperature,
            'notes' => $request->notes,
            'recorded_by_user_id' => Auth::id(),
            'risk_level' => 'Low', // Default risk level, can be updated by health worker
        ]);

        return redirect()->route('user.health-records')
            ->with('success', 'Health record added successfully');
    }

    // Notifications
    public function notifications()
    {
        $notifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Mark all as read
        Auth::user()->notifications()->unread()->update(['is_read' => true]);

        return view('user.notifications', compact('notifications'));
    }

    // Settings
    public function settings()
    {
        return view('user.settings');
    }

    // Menstruation Statistics
    public function menstruationStatistics()
    {
        $userId = Auth::id();
        
        // Get all period records
        $records = Cycle::where('user_id', $userId)
            ->orderBy('period_start_date', 'desc')
            ->get();
        
        // Calculate statistics using Cycle model
        $averageCycle = Cycle::getAverageCycleLength($userId);
        $averagePeriod = Cycle::getAveragePeriodLength($userId);
        
        // Calculate cycle length for each period
        $cycleHistory = [];
        $cycleLengths = [];
        
        for ($i = 0; $i < $records->count() - 1; $i++) {
            $current = $records[$i];
            $next = $records[$i + 1];
            
            $cycleLength = $next->period_start_date->diffInDays($current->period_start_date, false);
            $cycleLengths[] = $cycleLength;
            
            $isNormal = $this->isCycleLengthNormal($cycleLength);
            
            $cycleHistory[] = [
                'id' => $current->id,
                'cycle_length' => $cycleLength,
                'period_length' => $current->duration,
                'period_start_date' => $current->period_start_date,
                'period_end_date' => $current->period_end_date,
                'is_normal' => $isNormal,
            ];
        }
        
        return view('user.menstruation.statistics', compact(
            'records',
            'averageCycle',
            'averagePeriod',
            'cycleHistory',
            'cycleLengths'
        ));
    }

    private function isCycleLengthNormal($days)
    {
        // Normal cycle length: 21-35 days
        return $days >= 21 && $days <= 35;
    }

    // Generate PDF Report for Doctor
    public function generateMenstruationReport()
    {
        $userId = Auth::id();
        $user = Auth::user();
        
        // Get period records
        $records = Cycle::where('user_id', $userId)
            ->orderBy('period_start_date', 'desc')
            ->take(6)
            ->get();
        
        // Get statistics using Cycle model
        $averageCycle = Cycle::getAverageCycleLength($userId);
        $averagePeriod = Cycle::getAveragePeriodLength($userId);
        
        // Get daily tracking data
        $dailyData = MenstruationDaily::where('user_id', $userId)
            ->where('date', '>=', now()->subMonths(3))
            ->orderBy('date', 'desc')
            ->get();
        
        // Generate PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('user.menstruation.report', compact(
            'user',
            'records',
            'averageCycle',
            'averagePeriod',
            'dailyData'
        ));
        
        return $pdf->download('menstrual-cycle-report-' . now()->format('Y-m-d') . '.pdf');
    }

    // ============================================
    // NEW CYCLE TRACKING API (Task 1/5)
    // ============================================

    // GET /cycles - List all cycles for the user
    public function getCycles()
    {
        $cycles = Auth::user()->cycles()
            ->orderBy('period_start_date', 'desc')
            ->get();

        return response()->json([
            'cycles' => $cycles,
            'average_cycle_length' => Cycle::getAverageCycleLength(Auth::id()),
            'average_period_length' => Cycle::getAveragePeriodLength(Auth::id()),
        ]);
    }

    // POST /cycles - Store a new cycle
    public function storeCycle(Request $request)
    {
        $validated = $request->validate([
            'period_start_date' => 'required|date|before_or_equal:today',
            'period_end_date' => 'nullable|date|after_or_equal:period_start_date|before_or_equal:today',
            'flow_intensity' => 'required|in:light,medium,heavy',
            'notes' => 'nullable|string',
        ]);

        $cycle = Auth::user()->cycles()->create($validated);

        // Recalculate cycle lengths
        $this->recalculateCycleLengths(Auth::id());

        return redirect()->route('user.cycles.index')
            ->with('success', 'Cycle recorded successfully');
    }

    // DELETE /cycles/{id} - Delete a cycle
    public function destroyCycle($id)
    {
        $cycle = Cycle::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $cycle->delete();

        // Recalculate cycle lengths
        $this->recalculateCycleLengths(Auth::id());

        return redirect()->route('user.cycles.index')
            ->with('success', 'Cycle deleted successfully');
    }

    // GET /cycles/prediction - Get next period prediction
    public function getCyclePrediction()
    {
        $userId = Auth::id();
        $nextPeriod = Cycle::predictNextPeriod($userId);
        $averageCycle = Cycle::getAverageCycleLength($userId);
        $averagePeriod = Cycle::getAveragePeriodLength($userId);

        $cycles = Cycle::where('user_id', $userId)
            ->orderBy('period_start_date', 'desc')
            ->take(6)
            ->get();

        return response()->json([
            'next_period_date' => $nextPeriod?->format('Y-m-d'),
            'next_period_formatted' => $nextPeriod?->format('F j, Y'),
            'days_until' => $nextPeriod ? abs(round(now()->diffInDays($nextPeriod, false))) : null,
            'average_cycle_length' => $averageCycle,
            'average_period_length' => $averagePeriod,
            'recent_cycles' => $cycles,
            'confidence' => $cycles->count() >= 3 ? 'high' : ($cycles->count() >= 1 ? 'medium' : 'low'),
        ]);
    }

    // GET /cycles/calendar - Get calendar data with periods and predictions
    public function getCycleCalendar(Request $request)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $calendarData = Cycle::getCalendarData(Auth::id(), $year, $month);

        return response()->json([
            'year' => $year,
            'month' => $month,
            'calendar' => $calendarData,
        ]);
    }

    // Helper method to recalculate cycle lengths using service
    private function recalculateCycleLengths($userId)
    {
        $service = new CyclePredictionService();
        $service->recalculateCycleLengths($userId);
    }

    // ============================================

    // ============================================
    // CONSOLIDATED STATUS ENDPOINT (Task 4/5)
    // ============================================

    // GET /cycle-status - Get consolidated cycle status
    public function getCycleStatus(Request $request)
    {
        $validated = $request->validate([
            'date' => 'nullable|date',
        ]);

        $date = isset($validated['date']) ? \Carbon\Carbon::parse($validated['date']) : now();
        $userId = Auth::id();

        // Get cycle prediction
        $cycleService = new CyclePredictionService();
        $nextPeriod = $cycleService->predictNextPeriod($userId);
        $averageCycleLength = $cycleService->getAverageCycleLength($userId) ?? 28;

        if (!$nextPeriod) {
            return response()->json([
                'message' => 'No cycle data available. Please log your period first.',
            ], 404);
        }

        return response()->json([
            'next_period' => $nextPeriod->format('Y-m-d'),
            'cycle_length_avg' => $averageCycleLength,
        ]);
    }

    private function combineBloodPressure(Request $request): ?string
    {
        $systolic = $request->input('bp_systolic');
        $diastolic = $request->input('bp_diastolic');

        if ($systolic !== null && $systolic !== '' && $diastolic !== null && $diastolic !== '') {
            return $systolic . '/' . $diastolic;
        }

        return null;
    }

    private function mergePregnancyLifestyleInputs(Request $request): void
    {
        $otherLifestyle = trim((string) $request->input('lifestyle_other', ''));
        $notes = trim((string) $request->input('lifestyle_notes', ''));

        if ($otherLifestyle !== '') {
            $notes = trim($notes !== '' ? $notes . "\nOther: " . $otherLifestyle : 'Other: ' . $otherLifestyle);
        }

        $request->merge([
            'smoking_status' => $request->boolean('lifestyle_smoking') ? 'current' : 'none',
            'alcohol_status' => $request->boolean('lifestyle_alcohol') ? 'current' : 'none',
            'drug_use_status' => $request->boolean('lifestyle_drugs') ? 'current' : 'none',
            'lifestyle_notes' => $notes !== '' ? $notes : null,
        ]);
    }
}
