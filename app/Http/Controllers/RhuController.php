<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pregnancy;
use App\Models\Checkup;
use App\Models\HealthRecord;
use App\Models\BhwMonthlyReport;
use App\Models\SupplyRequest;
use App\Models\MaternalDeath;
use App\Models\MaternalMorbidity;
use App\Models\ActivityLog;
use App\Models\Purok;
use App\Services\RiskAnalysisService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

class RhuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // RHU Dashboard
    public function dashboard()
    {
        $rhuUser = auth()->user();
        
        // Auto-run alert checks when RHU admin visits dashboard
        try {
            Checkup::markOverdueCheckups();
        } catch (\Exception $e) {
            \Log::error('RHU dashboard alert check failed: ' . $e->getMessage());
        }

        // Stats
        $stats = [
            'totalPatients' => User::where('role', 'user')->where('status', 'approved')->count(),
            'activePregnancies' => Pregnancy::active()->count(),
            'highRiskPatients' => Pregnancy::active()->highRisk()->count(),
            'pendingSupplyRequests' => SupplyRequest::where('status', 'submitted')->count(),
            'pendingBhwReports' => BhwMonthlyReport::where('submission_status', 'submitted_to_midwife')->count(),
            'maternalDeathsCount' => MaternalDeath::count(),
            'nearMissCount' => MaternalMorbidity::count(),
        ];

        // ANC Coverage rate computation (Feature E)
        // DOH standard: at least 4 prenatal visits (1 in 1st trimester, 1 in 2nd, 2 in 3rd)
        // For dashboard purposes, we calculate the percentage of completed pregnancies that had 4+ visits
        $completedPregnancies = Pregnancy::completed()->withCount('healthRecords')->get();
        $totalCompleted = $completedPregnancies->count();
        $ancCompliant = $completedPregnancies->filter(fn($p) => $p->health_records_count >= 4)->count();
        $stats['ancCoverageRate'] = $totalCompleted > 0 ? round(($ancCompliant / $totalCompleted) * 100, 1) : 0;

        // Recent Maternal Deaths
        $recentDeaths = MaternalDeath::with(['user', 'walkInPatient', 'purok'])
            ->latest('death_date')
            ->limit(5)
            ->get();

        // Recent Near-Miss Events
        $recentMorbidities = MaternalMorbidity::with(['user', 'walkInPatient', 'purok'])
            ->latest('event_date')
            ->limit(5)
            ->get();

        // Pending Supply Requests
        $recentSupplyRequests = SupplyRequest::with('requestedBy')
            ->latest()
            ->limit(5)
            ->get();

        return view('rhu.dashboard', array_merge($stats, compact(
            'recentDeaths',
            'recentMorbidities',
            'recentSupplyRequests'
        )));
    }

    // Settings
    public function settings()
    {
        return view('rhu.settings');
    }

    // ─── Midwife Management ──────────────────────────────────────────

    public function midwives()
    {
        $midwives = User::where('role', 'midwife')
            ->where('status', '!=', 'archived')
            ->latest()
            ->paginate(15);

        return view('rhu.midwives.index', compact('midwives'));
    }

    public function createMidwife()
    {
        return view('rhu.midwives.create');
    }

    public function storeMidwife(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_initial' => 'nullable|string|max:1',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female',
            'contact_number' => 'required|string|max:20',
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        $data['role'] = 'midwife';
        $data['status'] = 'approved';
        $data['registered_by_rhu_id'] = auth()->id();

        $midwife = User::create($data);

        ActivityLog::log('create', "Created midwife account for {$midwife->name}", $midwife);

        return redirect()->route('rhu.midwives.index')
            ->with('success', 'Midwife created successfully.');
    }

    public function midwifeDetails($id)
    {
        $midwife = User::where('role', 'midwife')->findOrFail($id);
        return view('rhu.midwives.show', compact('midwife'));
    }

    public function editMidwife($id)
    {
        $midwife = User::where('role', 'midwife')->findOrFail($id);
        return view('rhu.midwives.edit', compact('midwife'));
    }

    public function updateMidwife(Request $request, $id)
    {
        $midwife = User::where('role', 'midwife')->findOrFail($id);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_initial' => 'nullable|string|max:1',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female',
            'contact_number' => 'required|string|max:20',
        ]);

        $data = $request->except(['password', 'password_confirmation']);

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            $data['password'] = Hash::make($request->password);
        }

        $midwife->update($data);

        ActivityLog::log('update', "Updated midwife account for {$midwife->name}", $midwife);

        return redirect()->route('rhu.midwives.index')
            ->with('success', 'Midwife updated successfully.');
    }

    public function destroyMidwife($id)
    {
        $midwife = User::where('role', 'midwife')->findOrFail($id);
        $name = $midwife->name;
        $midwife->delete();

        ActivityLog::log('delete', "Deleted midwife account for {$name}");

        return redirect()->route('rhu.midwives.index')
            ->with('success', "Midwife {$name} deleted successfully.");
    }

    // ─── BHW President Management (Copied from MidwifeController) ────

    public function bhwPresidents()
    {
        $bhwPresidents = User::where('role', 'bhw_president')
            ->where('status', '!=', 'archived')
            ->latest()
            ->paginate(15);

        foreach ($bhwPresidents as $president) {
            $president->managed_bhw_count = User::where('role', 'bhw')->count();
            $president->supervised_patients_count = User::where('role', 'user')->count();
            $president->active_pregnancies_count = Pregnancy::active()->count();
        }

        // Provide a view-friendly variable name ($presidents) expected by the blade
        $presidents = $bhwPresidents;

        return view('rhu.bhw-presidents.index', compact('presidents'));
    }

    public function createBhwPresident()
    {
        $currentPresident = User::where('role', 'bhw_president')
            ->where('status', '!=', 'archived')
            ->latest()
            ->first();

        return view('rhu.bhw-presidents.create', compact('currentPresident'));
    }

    public function storeBhwPresident(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_initial' => 'nullable|string|max:1',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female',
            'contact_number' => 'required|string|max:20',
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        $data['role'] = 'bhw_president';
        $data['status'] = 'approved';
        $data['barangay'] = 'Barangay Burgos, San Carlos City, Pangasinan';

        $existingPresident = User::where('role', 'bhw_president')
            ->where('status', '!=', 'archived')
            ->latest()
            ->first();

        if ($existingPresident) {
            $existingPresident->update([
                'status' => 'archived',
                'archived_at' => now(),
            ]);
        }

        $president = User::create($data);

        ActivityLog::log('create', "Appointed BHW President {$president->name}", $president);

        return redirect()->route('rhu.bhw-presidents.index')
            ->with('success', 'BHW President appointed successfully.');
    }

    public function showBhwPresident($id)
    {
        $bhwPresident = User::where('role', 'bhw_president')->findOrFail($id);
        $stats = [
            'managed_bhw_count' => User::where('role', 'bhw')->count(),
            'supervised_patients_count' => User::where('role', 'user')->count(),
            'active_pregnancies_count' => Pregnancy::active()->count(),
        ];

        return view('rhu.bhw-presidents.show', compact('bhwPresident', 'stats'));
    }

    public function editBhwPresident($id)
    {
        $bhwPresident = User::where('role', 'bhw_president')->findOrFail($id);
        return view('rhu.bhw-presidents.edit', compact('bhwPresident'));
    }

    public function updateBhwPresident(Request $request, $id)
    {
        $bhwPresident = User::where('role', 'bhw_president')->findOrFail($id);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_initial' => 'nullable|string|max:1',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female',
            'contact_number' => 'required|string|max:20',
        ]);

        $data = $request->except(['password', 'password_confirmation']);

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            $data['password'] = Hash::make($request->password);
        }

        $bhwPresident->update($data);

        ActivityLog::log('update', "Updated BHW President {$bhwPresident->name}", $bhwPresident);

        return redirect()->route('rhu.bhw-presidents.index')
            ->with('success', 'BHW President updated successfully.');
    }

    public function destroyBhwPresident($id)
    {
        $bhwPresident = User::where('role', 'bhw_president')->findOrFail($id);
        $name = $bhwPresident->name;
        $bhwPresident->delete();

        ActivityLog::log('delete', "Deleted BHW President account for {$name}");

        return redirect()->route('rhu.bhw-presidents.index')
            ->with('success', 'BHW President deleted successfully.');
    }

    // ─── Supply Requests (RHU → CHO) ────────────────────────────────

    public function supplyRequests()
    {
        $requests = SupplyRequest::with(['requestedBy', 'approvedBy'])
            ->latest()
            ->paginate(15);

        return view('rhu.supply-requests.index', compact('requests'));
    }

    public function createSupplyRequest()
    {
        return view('rhu.supply-requests.create');
    }

    public function storeSupplyRequest(Request $request)
    {
        $request->validate([
            'supply_category' => 'required|in:vitamins,vaccines,birthing_kits,medicines,equipment,ppe,other',
            'supply_name' => 'required|string|max:255',
            'quantity_requested' => 'required|integer|min:1',
            'unit' => 'nullable|string|max:50',
            'urgency' => 'required|in:routine,urgent,emergency',
            'reason' => 'nullable|string|max:1000',
        ]);

        $supplyRequest = SupplyRequest::create([
            'requested_by_id' => auth()->id(),
            'supply_category' => $request->supply_category,
            'supply_name' => $request->supply_name,
            'quantity_requested' => $request->quantity_requested,
            'unit' => $request->unit,
            'urgency' => $request->urgency,
            'reason' => $request->reason,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        ActivityLog::log('request_supply', "Requested {$request->quantity_requested} {$request->unit} of {$request->supply_name} from CHO", $supplyRequest);

        return redirect()->route('rhu.supply-requests.index')
            ->with('success', 'Supply request submitted successfully to CHO.');
    }

    public function showSupplyRequest($id)
    {
        $supplyRequest = SupplyRequest::with(['requestedBy', 'approvedBy'])->findOrFail($id);
        return view('rhu.supply-requests.show', compact('supplyRequest'));
    }

    public function destroySupplyRequest($id)
    {
        $supplyRequest = SupplyRequest::findOrFail($id);

        if ($supplyRequest->status !== 'draft' && $supplyRequest->status !== 'submitted') {
            return back()->with('error', 'Cannot delete a supply request that has been reviewed or approved.');
        }

        $supplyRequest->delete();

        return redirect()->route('rhu.supply-requests.index')
            ->with('success', 'Supply request cancelled and removed.');
    }

    // ─── Maternal Deaths (Feature A) ──────────────────────────────────

    public function maternalDeaths()
    {
        $deaths = MaternalDeath::with(['user', 'walkInPatient', 'purok'])
            ->latest('death_date')
            ->paginate(15);

        return view('rhu.maternal-deaths.index', compact('deaths'));
    }

    public function createMaternalDeath()
    {
        $users = User::where('role', 'user')->where('status', 'approved')->orderBy('first_name')->get();
        $walkIns = WalkInPatient::whereNull('converted_to_user_id')->orderBy('first_name')->get();
        $pregnancies = Pregnancy::orderBy('lmp', 'desc')->get();
        $puroks = Purok::orderBy('name')->get();

        return view('rhu.maternal-deaths.create', compact('users', 'walkIns', 'pregnancies', 'puroks'));
    }

    public function storeMaternalDeath(Request $request)
    {
        $request->validate([
            'patient_type' => 'required|in:registered,walk_in',
            'user_id' => 'required_if:patient_type,registered|nullable|exists:users,id',
            'walk_in_patient_id' => 'required_if:patient_type,walk_in|nullable|exists:walk_in_patients,id',
            'pregnancy_id' => 'nullable|exists:pregnancies,id',
            'purok_id' => 'required|exists:puroks,id',
            'death_date' => 'required|date|before_or_equal:today',
            'death_time' => 'nullable',
            'age_at_death' => 'nullable|integer|min:10|max:60',
            'place_of_death' => 'required|in:home,barangay_health_station,rhu,city_hospital,provincial_hospital,private_hospital,in_transit,other',
            'cause_of_death' => 'required|string|max:500',
            'cause_category' => 'required|in:hemorrhage,hypertension_eclampsia,sepsis,obstructed_labor,unsafe_abortion,embolism,other_direct,indirect_cause,unknown',
            'death_timing' => 'required|in:during_pregnancy,during_delivery,within_24_hours_postpartum,within_7_days_postpartum,within_42_days_postpartum,unknown',
            'notes' => 'nullable|string|max:1000',
        ]);

        $selectedPurok = Purok::findOrFail($request->purok_id);

        $maternalDeath = MaternalDeath::create([
            'user_id' => $request->patient_type === 'registered' ? $request->user_id : null,
            'walk_in_patient_id' => $request->patient_type === 'walk_in' ? $request->walk_in_patient_id : null,
            'pregnancy_id' => $request->pregnancy_id,
            'recorded_by_id' => auth()->id(),
            'purok_id' => $selectedPurok->id,
            'barangay' => $selectedPurok->barangay,
            'death_date' => $request->death_date,
            'death_time' => $request->death_time,
            'age_at_death' => $request->age_at_death,
            'place_of_death' => $request->place_of_death,
            'cause_of_death' => $request->cause_of_death,
            'cause_category' => $request->cause_category,
            'death_timing' => $request->death_timing,
            'notes' => $request->notes,
            'audit_status' => 'pending',
        ]);

        // End active pregnancy if exists and linked
        if ($request->pregnancy_id) {
            $pregnancy = Pregnancy::find($request->pregnancy_id);
            if ($pregnancy) {
                $pregnancy->update([
                    'ended_at' => $request->death_date,
                    'workflow_status' => 'completed',
                    'workflow_notes' => 'Ended due to maternal mortality case ID: ' . $maternalDeath->id,
                ]);
            }
        }

        ActivityLog::log('create', "Recorded maternal death case for patient: {$maternalDeath->patient_name}", $maternalDeath);

        return redirect()->route('rhu.maternal-deaths.index')
            ->with('success', 'Maternal death case recorded successfully.');
    }

    public function showMaternalDeath($id)
    {
        $death = MaternalDeath::with(['user', 'walkInPatient', 'pregnancy', 'recordedBy', 'reviewedBy', 'purok'])->findOrFail($id);
        return view('rhu.maternal-deaths.show', compact('death'));
    }

    public function editMaternalDeath($id)
    {
        $death = MaternalDeath::findOrFail($id);
        $puroks = Purok::orderBy('name')->get();
        return view('rhu.maternal-deaths.edit', compact('death', 'puroks'));
    }

    public function updateMaternalDeath(Request $request, $id)
    {
        $death = MaternalDeath::findOrFail($id);

        $request->validate([
            'purok_id' => 'required|exists:puroks,id',
            'death_date' => 'required|date|before_or_equal:today',
            'death_time' => 'nullable',
            'age_at_death' => 'nullable|integer|min:10|max:60',
            'place_of_death' => 'required|in:home,barangay_health_station,rhu,city_hospital,provincial_hospital,private_hospital,in_transit,other',
            'cause_of_death' => 'required|string|max:500',
            'cause_category' => 'required|in:hemorrhage,hypertension_eclampsia,sepsis,obstructed_labor,unsafe_abortion,embolism,other_direct,indirect_cause,unknown',
            'death_timing' => 'required|in:during_pregnancy,during_delivery,within_24_hours_postpartum,within_7_days_postpartum,within_42_days_postpartum,unknown',
            'notes' => 'nullable|string|max:1000',
        ]);

        $selectedPurok = Purok::findOrFail($request->purok_id);

        $death->update([
            'purok_id' => $selectedPurok->id,
            'barangay' => $selectedPurok->barangay,
            'death_date' => $request->death_date,
            'death_time' => $request->death_time,
            'age_at_death' => $request->age_at_death,
            'place_of_death' => $request->place_of_death,
            'cause_of_death' => $request->cause_of_death,
            'cause_category' => $request->cause_category,
            'death_timing' => $request->death_timing,
            'notes' => $request->notes,
        ]);

        ActivityLog::log('update', "Updated maternal death case for patient: {$death->patient_name}", $death);

        return redirect()->route('rhu.maternal-deaths.show', $id)
            ->with('success', 'Maternal death details updated.');
    }

    public function destroyMaternalDeath($id)
    {
        $death = MaternalDeath::findOrFail($id);
        $name = $death->patient_name;
        $death->delete();

        ActivityLog::log('delete', "Deleted maternal death case for {$name}");

        return redirect()->route('rhu.maternal-deaths.index')
            ->with('success', 'Maternal death case deleted successfully.');
    }

    // ─── Maternal Morbidities / Near-Miss Events (Feature K) ──────────

    public function morbidities()
    {
        $morbidities = MaternalMorbidity::with(['user', 'walkInPatient', 'purok'])
            ->latest('event_date')
            ->paginate(15);

        return view('rhu.morbidities.index', compact('morbidities'));
    }

    public function createMorbidity()
    {
        $users = User::where('role', 'user')->where('status', 'approved')->orderBy('first_name')->get();
        $walkIns = WalkInPatient::whereNull('converted_to_user_id')->orderBy('first_name')->get();
        $pregnancies = Pregnancy::orderBy('lmp', 'desc')->get();
        $puroks = Purok::orderBy('name')->get();
        $deaths = MaternalDeath::orderBy('death_date', 'desc')->get();

        return view('rhu.morbidities.create', compact('users', 'walkIns', 'pregnancies', 'puroks', 'deaths'));
    }

    public function storeMorbidity(Request $request)
    {
        $request->validate([
            'patient_type' => 'required|in:registered,walk_in',
            'user_id' => 'required_if:patient_type,registered|nullable|exists:users,id',
            'walk_in_patient_id' => 'required_if:patient_type,walk_in|nullable|exists:walk_in_patients,id',
            'pregnancy_id' => 'nullable|exists:pregnancies,id',
            'purok_id' => 'required|exists:puroks,id',
            'event_date' => 'required|date|before_or_equal:today',
            'event_time' => 'nullable',
            'complication_type' => 'required|in:severe_hemorrhage,eclampsia,severe_preeclampsia,sepsis,ruptured_uterus,severe_anemia,obstructed_labor,placenta_previa,placental_abruption,other',
            'place_of_event' => 'required|in:home,barangay_health_station,rhu,city_hospital,provincial_hospital,private_hospital,in_transit,other',
            'outcome' => 'required|in:survived_no_intervention,survived_with_intervention,transferred_to_higher_facility,died',
            'maternal_death_id' => 'required_if:outcome,died|nullable|exists:maternal_deaths,id',
            'description' => 'nullable|string|max:1000',
            'interventions_done' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ]);

        $selectedPurok = Purok::findOrFail($request->purok_id);

        $morbidity = MaternalMorbidity::create([
            'user_id' => $request->patient_type === 'registered' ? $request->user_id : null,
            'walk_in_patient_id' => $request->patient_type === 'walk_in' ? $request->walk_in_patient_id : null,
            'pregnancy_id' => $request->pregnancy_id,
            'recorded_by_id' => auth()->id(),
            'purok_id' => $selectedPurok->id,
            'barangay' => $selectedPurok->barangay,
            'event_date' => $request->event_date,
            'event_time' => $request->event_time,
            'complication_type' => $request->complication_type,
            'place_of_event' => $request->place_of_event,
            'outcome' => $request->outcome,
            'maternal_death_id' => $request->outcome === 'died' ? $request->maternal_death_id : null,
            'description' => $request->description,
            'interventions_done' => $request->interventions_done,
            'notes' => $request->notes,
            'review_status' => 'pending',
        ]);

        ActivityLog::log('create', "Recorded maternal morbidity near-miss event ({$request->complication_type}) for {$morbidity->patient_name}", $morbidity);

        return redirect()->route('rhu.morbidities.index')
            ->with('success', 'Maternal morbidity event recorded successfully.');
    }

    public function showMorbidity($id)
    {
        $morbidity = MaternalMorbidity::with(['user', 'walkInPatient', 'pregnancy', 'recordedBy', 'reviewedBy', 'purok', 'maternalDeath'])->findOrFail($id);
        return view('rhu.morbidities.show', compact('morbidity'));
    }

    public function editMorbidity($id)
    {
        $morbidity = MaternalMorbidity::findOrFail($id);
        $puroks = Purok::orderBy('name')->get();
        $deaths = MaternalDeath::orderBy('death_date', 'desc')->get();
        return view('rhu.morbidities.edit', compact('morbidity', 'puroks', 'deaths'));
    }

    public function updateMorbidity(Request $request, $id)
    {
        $morbidity = MaternalMorbidity::findOrFail($id);

        $request->validate([
            'purok_id' => 'required|exists:puroks,id',
            'event_date' => 'required|date|before_or_equal:today',
            'event_time' => 'nullable',
            'complication_type' => 'required|in:severe_hemorrhage,eclampsia,severe_preeclampsia,sepsis,ruptured_uterus,severe_anemia,obstructed_labor,placenta_previa,placental_abruption,other',
            'place_of_event' => 'required|in:home,barangay_health_station,rhu,city_hospital,provincial_hospital,private_hospital,in_transit,other',
            'outcome' => 'required|in:survived_no_intervention,survived_with_intervention,transferred_to_higher_facility,died',
            'maternal_death_id' => 'required_if:outcome,died|nullable|exists:maternal_deaths,id',
            'description' => 'nullable|string|max:1000',
            'interventions_done' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ]);

        $selectedPurok = Purok::findOrFail($request->purok_id);

        $morbidity->update([
            'purok_id' => $selectedPurok->id,
            'barangay' => $selectedPurok->barangay,
            'event_date' => $request->event_date,
            'event_time' => $request->event_time,
            'complication_type' => $request->complication_type,
            'place_of_event' => $request->place_of_event,
            'outcome' => $request->outcome,
            'maternal_death_id' => $request->outcome === 'died' ? $request->maternal_death_id : null,
            'description' => $request->description,
            'interventions_done' => $request->interventions_done,
            'notes' => $request->notes,
        ]);

        ActivityLog::log('update', "Updated maternal morbidity event details for {$morbidity->patient_name}", $morbidity);

        return redirect()->route('rhu.morbidities.show', $id)
            ->with('success', 'Morbidity details updated successfully.');
    }

    public function destroyMorbidity($id)
    {
        $morbidity = MaternalMorbidity::findOrFail($id);
        $name = $morbidity->patient_name;
        $morbidity->delete();

        ActivityLog::log('delete', "Deleted maternal morbidity record for {$name}");

        return redirect()->route('rhu.morbidities.index')
            ->with('success', 'Morbidity record deleted successfully.');
    }

    // ─── Activity Logs (Feature 11) ───────────────────────────────────

    public function logs()
    {
        // RHU sees all activity logs except CHO actions
        $logs = ActivityLog::with('user')
            ->where('user_role', '!=', 'cho')
            ->latest()
            ->paginate(30);

        return view('rhu.logs.index', compact('logs'));
    }

    // ─── Reports (FHSIS/MNCHN) ───────────────────────────────────────

    public function reports()
    {
        // Aggregated MCH report statistics
        $totalPregnant = Pregnancy::active()->count();
        $totalPostpartum = Pregnancy::completed()->count();
        $totalBirths = Pregnancy::completed()->whereNotNull('ended_at')->count();
        $facilityDeliveries = Pregnancy::completed()
            ->whereIn('facility_delivery_place', ['barangay_health_station', 'rhu_birth_center', 'city_hospital', 'provincial_hospital', 'private_hospital'])
            ->count();
        
        $facilityBirthRate = $totalBirths > 0 ? round(($facilityDeliveries / $totalBirths) * 100, 1) : 0;

        // Get patients
        $search = request('search');
        $status = request('status');

        $query = User::where('role', 'user')
            ->where('status', 'approved')
            ->with(['pregnancies', 'healthRecords']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('barangay', 'like', '%' . $search . '%');
            });
        }

        $patients = $query->paginate(20);

        return view('rhu.reports.index', compact(
            'patients',
            'totalPregnant',
            'totalPostpartum',
            'totalBirths',
            'facilityBirthRate',
            'search',
            'status'
        ));
    }

    public function reportDetails($id)
    {
        $woman = User::where('role', 'user')->with(['pregnancies', 'checkups', 'healthRecords'])->findOrFail($id);
        return view('rhu.reports.details', compact('woman'));
    }

    public function exportCsv()
    {
        $patients = User::where('role', 'user')->where('status', 'approved')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="rhu_patients_report_' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($patients) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Patient Name', 'Age', 'Status', 'Email', 'Contact Number', 'Barangay']);

            foreach ($patients as $patient) {
                fputcsv($file, [
                    $patient->name,
                    $patient->age,
                    $patient->pregnancy_status,
                    $patient->email,
                    $patient->contact_number,
                    $patient->barangay ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf()
    {
        $patients = User::where('role', 'user')->where('status', 'approved')->get();
        return view('rhu.reports.print', compact('patients'));
    }

    // ─── BHW Monthly Reports Approval (Copied from MidwifeController) ──

    public function bhwReports()
    {
        $filter = request('filter', 'all');
        
        $query = BhwMonthlyReport::with(['bhw', 'submittedToPresidentBy', 'approvedByPresident', 'submittedToMidwifeBy']);
        
        if ($filter !== 'all') {
            $query->where('report_type', $filter);
        }
        
        $reports = $query->latest()->paginate(10);
        
        return view('rhu.bhw-reports.index', compact('reports', 'filter'));
    }

    public function bhwReportShow($id)
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

            return view('rhu.bhw-reports.show', compact('report', 'healthRecords', 'uniquePatients', 'riskDistribution'));
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

            return view('rhu.bhw-reports.show-pregnancies', compact('report', 'pregnancies', 'uniquePatients', 'riskDistribution'));
        }
    }

    public function bhwReportPrint($id)
    {
        $report = BhwMonthlyReport::findOrFail($id);
        return view('rhu.bhw-reports.print', compact('report'));
    }

    public function bhwReportApprove(Request $request, $id)
    {
        $report = BhwMonthlyReport::findOrFail($id);
        $report->approveByMidwife(auth()->id(), $request->input('notes'));

        ActivityLog::log('approve', "Approved BHW monthly report ID: {$report->id} submitted by BHW: {$report->bhw->name}", $report);

        return redirect()->route('rhu.bhw-reports.index')
            ->with('success', 'BHW Monthly Report approved successfully.');
    }

    public function bhwReportReject(Request $request, $id)
    {
        $report = BhwMonthlyReport::findOrFail($id);
        $report->rejectByMidwife(auth()->id(), $request->input('notes'));

        ActivityLog::log('reject', "Rejected BHW monthly report ID: {$report->id} submitted by BHW: {$report->bhw->name}", $report);

        return redirect()->route('rhu.bhw-reports.index')
            ->with('success', 'Report rejected and returned to BHW President.');
    }

    public function bhwReportDestroy($id)
    {
        $report = BhwMonthlyReport::findOrFail($id);
        $report->delete();

        return redirect()->route('rhu.bhw-reports.index')
            ->with('success', 'Report deleted successfully.');
    }
}
