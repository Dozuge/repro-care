@extends('midwife.layout')

@section('title', 'Health Record Details - ReproCare')

@section('midwife-content')
@php
    $isWalkIn = (bool) $healthRecord->walk_in_patient_id;
    $patient = $isWalkIn ? $healthRecord->walkInPatient : $healthRecord->woman;
    $patientName = $healthRecord->patient_name;
    $patientSubtitle = $isWalkIn ? ($healthRecord->patient_contact ?? 'Walk-in woman') : (optional($healthRecord->woman)->email ?? 'Registered woman');
    $patientImage = $isWalkIn ? '/images/avatars/avatar-female.svg' : (optional($healthRecord->woman)->profile_image_url ?? '/images/avatars/avatar-female.svg');
    $bmi = ($healthRecord->weight && $healthRecord->height) ? $healthRecord->weight / pow($healthRecord->height / 100, 2) : null;
    $bmiLabel = $bmi === null ? 'N/A' : ($bmi < 18.5 ? 'Malnourished' : ($bmi >= 30 ? 'Obese' : 'Normal'));
    $allRecords = $healthRecord->user_id
        ? $healthRecord->woman->healthRecords()->with('recordedBy')->orderBy('created_at', 'desc')->get()
        : \App\Models\HealthRecord::with('recordedBy')->where('walk_in_patient_id', $healthRecord->walk_in_patient_id)->orderBy('created_at', 'desc')->get();
@endphp

<div class="py-4">
    <div class="page-hero fade-in-card mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3" style="position:relative;z-index:1;">
            <div>
                <h1 class="page-hero-title"><i class="bi bi-clipboard2-pulse-fill me-2"></i>Health Record Details</h1>
                <p class="page-hero-subtitle">Review the patient snapshot, measurements, and recommendations.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('midwife.health-records.edit', $healthRecord->id) }}" class="btn-hero-primary">
                    <i class="bi bi-pencil-square"></i> Edit
                </a>
                <a href="{{ route('midwife.health-records.index') }}" class="btn-hero-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100 fade-in-card" style="background:var(--bg-card);">
                <div class="card-body p-4 text-center">
                    <img src="{{ $patientImage }}" alt="{{ $patientName }}" style="width:92px;height:92px;border-radius:24px;object-fit:cover;border:2px solid var(--border);">
                    <h3 class="mt-3 mb-1" style="color:var(--text);">{{ $patientName }}</h3>
                    <p class="mb-2" style="color:var(--text-muted);">{{ $patientSubtitle }}</p>
                    <span class="badge {{ $isWalkIn ? 'bg-info' : 'bg-primary' }}">{{ $isWalkIn ? 'Walk-in woman' : 'Registered woman' }}</span>

                    <hr>

                    <div class="text-start d-grid gap-3">
                        <div><strong>Recorded On</strong><div>{{ $healthRecord->created_at->format('M j, Y h:i A') }}</div></div>
                        <div><strong>Recorded By</strong><div>{{ optional($healthRecord->recordedBy)->name ?? 'System' }}</div></div>
                        <div><strong>Contact</strong><div>{{ $healthRecord->patient_contact ?? 'N/A' }}</div></div>
                        <div><strong>Barangay</strong><div>{{ $healthRecord->patient_barangay ?? 'N/A' }}</div></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="row g-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm fade-in-card" style="background:var(--bg-card);">
                        <div class="card-body p-4">
                            <h5 class="mb-3" style="color:var(--text);">Vital Signs</h5>
                            <div class="row g-3">
                                <div class="col-md-3"><strong>Blood Pressure</strong><div>{{ $healthRecord->bp ?? 'N/A' }}</div></div>
                                <div class="col-md-3"><strong>Heart Rate</strong><div>{{ $healthRecord->heart_rate ? $healthRecord->heart_rate . ' bpm' : 'N/A' }}</div></div>
                                <div class="col-md-3"><strong>Temperature</strong><div>{{ $healthRecord->temperature ? $healthRecord->temperature . ' °C' : 'N/A' }}</div></div>
                                <div class="col-md-3"><strong>Oxygen Saturation</strong><div>{{ $healthRecord->oxygen_saturation ? $healthRecord->oxygen_saturation . '%' : 'N/A' }}</div></div>
                                <div class="col-md-3"><strong>Weight</strong><div>{{ $healthRecord->weight ? $healthRecord->weight . ' kg' : 'N/A' }}</div></div>
                                <div class="col-md-3"><strong>Height</strong><div>{{ $healthRecord->height ? $healthRecord->height . ' cm' : 'N/A' }}</div></div>
                                <div class="col-md-3"><strong>BMI</strong><div>{{ $bmi ? number_format($bmi, 2) : 'N/A' }}</div></div>
                                <div class="col-md-3"><strong>BMI Status</strong><div>{{ $bmiLabel }}</div></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card border-0 shadow-sm fade-in-card" style="background:var(--bg-card);">
                        <div class="card-body p-4">
                            <h5 class="mb-3" style="color:var(--text);">Assessment</h5>
                            <div class="row g-3">
                                <div class="col-md-4"><strong>Risk Level</strong><div>{{ $healthRecord->risk_level ?? 'N/A' }}</div></div>
                                <div class="col-md-4"><strong>Hemoglobin</strong><div>{{ $healthRecord->hemoglobin ? $healthRecord->hemoglobin . ' g/dL' : 'N/A' }}</div></div>
                                <div class="col-md-4"><strong>Immunization</strong><div>{{ $healthRecord->immunization_status ?? 'N/A' }}</div></div>
                                <div class="col-md-6"><strong>Contraceptive Use</strong><div>{{ $healthRecord->contraceptive_use ?? 'N/A' }}</div></div>
                                <div class="col-md-6"><strong>Lab Results</strong><div>{{ $healthRecord->lab_results ?? 'N/A' }}</div></div>
                                @if($healthRecord->gestational_age || $healthRecord->fetal_heart_rate)
                                    <div class="col-md-6"><strong>Gestational Age</strong><div>{{ $healthRecord->gestational_age ? $healthRecord->gestational_age . ' weeks' : 'N/A' }}</div></div>
                                    <div class="col-md-6"><strong>Fetal Heart Rate</strong><div>{{ $healthRecord->fetal_heart_rate ? $healthRecord->fetal_heart_rate . ' bpm' : 'N/A' }}</div></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if($healthRecord->notes || $healthRecord->recommendations)
                    <div class="col-12">
                        <div class="card border-0 shadow-sm fade-in-card" style="background:var(--bg-card);">
                            <div class="card-body p-4">
                                <h5 class="mb-3" style="color:var(--text);">Notes And Recommendations</h5>
                                @if($healthRecord->notes)
                                    <div class="mb-3"><strong>Clinical Notes</strong><div>{!! nl2br(e($healthRecord->notes)) !!}</div></div>
                                @endif
                                @if($healthRecord->recommendations)
                                    <div><strong>Recommendations</strong><div>{!! nl2br(e($healthRecord->recommendations)) !!}</div></div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <div class="col-12">
                    <div class="card border-0 shadow-sm fade-in-card" style="background:var(--bg-card);">
                        <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div class="d-flex gap-2 flex-wrap">
                                @if($healthRecord->user_id)
                                    <a href="{{ route('midwife.patient-details', $healthRecord->user_id) }}" class="btn btn-outline-primary">
                                        <i class="bi bi-person"></i> View Woman
                                    </a>
                                @endif
                                @if($healthRecord->user_id)
                                    <a href="{{ route('midwife.health-records.create', $healthRecord->user_id) }}" class="btn btn-outline-success">
                                        <i class="bi bi-plus-circle"></i> Add New Record
                                    </a>
                                @endif
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                <form action="{{ route('midwife.health-records.archive', $healthRecord->id) }}" method="POST" onsubmit="return confirm('Archive this health record?')">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-secondary">
                                        <i class="bi bi-archive"></i> Archive
                                    </button>
                                </form>
                                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete({{ $healthRecord->id }})">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card border-0 shadow-sm fade-in-card" style="background:var(--bg-card);">
                        <div class="card-body p-0">
                            <div class="p-4 border-bottom">
                                <h5 class="mb-0" style="color:var(--text);">Other Health Records</h5>
                            </div>
                            @if($allRecords->isEmpty())
                                <div class="p-4 text-muted">No other health records found for this patient.</div>
                            @else
                                <div class="table-responsive">
                                    <table class="table align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Vitals</th>
                                                <th>Risk</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($allRecords as $record)
                                                <tr class="{{ $record->id === $healthRecord->id ? 'table-primary' : '' }}">
                                                    <td>
                                                        <div>{{ $record->created_at->format('M j, Y') }}</div>
                                                        <small class="text-muted">{{ $record->created_at->format('h:i A') }}</small>
                                                    </td>
                                                    <td>
                                                        <div>BP: {{ $record->bp ?? 'N/A' }}</div>
                                                        <div>Weight: {{ $record->weight ? $record->weight . ' kg' : 'N/A' }}</div>
                                                    </td>
                                                    <td>{{ $record->risk_level ?? 'N/A' }}</td>
                                                    <td class="text-end pe-4">
                                                        @if($record->id !== $healthRecord->id)
                                                            <a href="{{ route('midwife.health-records.show', $record->id) }}" class="btn btn-sm btn-outline-primary">
                                                                <i class="bi bi-eye"></i> View
                                                            </a>
                                                        @else
                                                            <span class="badge bg-primary">Current</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Health Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(recordId) {
    document.getElementById('deleteForm').action = '{{ route("midwife.health-records.destroy", ":id") }}'.replace(':id', recordId);
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endsection
