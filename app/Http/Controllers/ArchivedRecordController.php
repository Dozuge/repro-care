<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\HealthRecord;
use App\Models\LearningMaterial;
use App\Models\MaternalDeath;
use App\Models\MaternalMorbidity;
use App\Models\Pregnancy;
use App\Models\SupplyRequest;
use App\Models\User;
use Illuminate\Http\Request;

class ArchivedRecordController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:cho']);
    }

    /**
     * Display all archived records across entities for the CHO Admin.
     */
    public function index(Request $request)
    {
        $activeTab = $request->input('tab', 'patients');
        $search = $request->input('search');

        // Archived Patients
        $patients = User::onlyTrashed()
            ->where('role', 'user')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('contact_number', 'like', "%{$search}%")
                        ->orWhere('barangay', 'like', "%{$search}%");
                });
            })
            ->latest('deleted_at')
            ->paginate(15, ['*'], 'patients_page')
            ->withQueryString();

        // Archived Staff Accounts
        $staff = User::onlyTrashed()
            ->whereIn('role', ['rhu', 'midwife', 'bhw_president', 'bhw'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('role', 'like', "%{$search}%");
                });
            })
            ->latest('deleted_at')
            ->paginate(15, ['*'], 'staff_page')
            ->withQueryString();

        // Archived Health Records
        $healthRecords = HealthRecord::onlyTrashed()
            ->with(['user', 'recordedBy'])
            ->when($search, function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                });
            })
            ->latest('deleted_at')
            ->paginate(15, ['*'], 'records_page')
            ->withQueryString();

        // Archived Learning Materials / Videos
        $learningMaterials = LearningMaterial::onlyTrashed()
            ->when($search, function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            })
            ->latest('deleted_at')
            ->paginate(15, ['*'], 'materials_page')
            ->withQueryString();

        // Archived Pregnancies
        $pregnancies = Pregnancy::onlyTrashed()
            ->with('user')
            ->when($search, function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                });
            })
            ->latest('deleted_at')
            ->paginate(15, ['*'], 'pregnancies_page')
            ->withQueryString();

        // Archived Supply Requests
        $supplyRequests = SupplyRequest::onlyTrashed()
            ->with('requestedBy')
            ->when($search, function ($q) use ($search) {
                $q->where('supply_name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            })
            ->latest('deleted_at')
            ->paginate(15, ['*'], 'supplies_page')
            ->withQueryString();

        $stats = [
            'patients' => User::onlyTrashed()->where('role', 'user')->count(),
            'staff' => User::onlyTrashed()->whereIn('role', ['rhu', 'midwife', 'bhw_president', 'bhw'])->count(),
            'health_records' => HealthRecord::onlyTrashed()->count(),
            'learning_materials' => LearningMaterial::onlyTrashed()->count(),
            'pregnancies' => Pregnancy::onlyTrashed()->count(),
            'supply_requests' => SupplyRequest::onlyTrashed()->count(),
        ];

        return view('cho.archived.index', compact(
            'activeTab',
            'search',
            'patients',
            'staff',
            'healthRecords',
            'learningMaterials',
            'pregnancies',
            'supplyRequests',
            'stats'
        ));
    }

    /**
     * Restore an archived record with a single action.
     */
    public function restore(string $type, int $id)
    {
        $name = '';
        switch ($type) {
            case 'patient':
            case 'staff':
            case 'user':
                $record = User::onlyTrashed()->findOrFail($id);
                $name = $record->name;
                $record->restore();
                if ($record->status === 'archived') {
                    $record->update(['status' => 'approved', 'rejection_reason' => null]);
                }
                break;

            case 'health-record':
                $record = HealthRecord::onlyTrashed()->findOrFail($id);
                $name = "Health Record #" . $record->id;
                $record->restore();
                break;

            case 'learning-material':
            case 'video':
                $record = LearningMaterial::onlyTrashed()->findOrFail($id);
                $name = $record->title;
                $record->restore();
                break;

            case 'pregnancy':
                $record = Pregnancy::onlyTrashed()->findOrFail($id);
                $name = "Pregnancy Record #" . $record->id;
                $record->restore();
                break;

            case 'supply-request':
                $record = SupplyRequest::onlyTrashed()->findOrFail($id);
                $name = "Supply Request: " . $record->supply_name;
                $record->restore();
                break;

            default:
                return back()->with('error', 'Invalid record type specified for restoration.');
        }

        ActivityLog::log('restore', "CHO restored archived {$type}: {$name}");

        return back()->with('success', "Archived {$type} '{$name}' restored successfully.");
    }
}
