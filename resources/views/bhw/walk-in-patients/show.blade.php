@extends('bhw.layout')

@section('title', 'Walk-in Patient Details - ReproCare')

@section('bhw-content')
<div class="py-4">
    <div class="page-hero fade-in-card mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3" style="position:relative;z-index:1;">
            <div>
                <div class="page-hero-title">
                    <i class="bi bi-person-plus-fill me-2"></i>{{ $patient->full_name }}
                </div>
                <p class="page-hero-subtitle">Walk-in patient profile and referral history</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('bhw.walk-in-patients.edit', $patient->id) }}" class="btn btn-light">
                    <i class="bi bi-pencil-square me-1"></i> Edit
                </a>
                @if(!$patient->converted_to_user_id)
                    <a href="{{ route('bhw.walk-in-patients.convert', $patient->id) }}" class="btn btn-light">
                        <i class="bi bi-person-check-fill me-1"></i> Convert
                    </a>
                @endif
                <a href="{{ route('bhw.walk-in-patients.index') }}" class="btn btn-outline-light">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card stat-cyan fade-in-card">
                <i class="bi bi-journal-medical stat-icon"></i>
                <div class="stat-label">Referrals</div>
                <div class="stat-number">{{ $patient->checkupReferrals->count() }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card stat-green fade-in-card">
                <i class="bi bi-geo-alt-fill stat-icon"></i>
                <div class="stat-label">Purok</div>
                <div class="stat-number" style="font-size:1.1rem;">{{ $patient->purok?->name ?? 'Not set' }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card stat-amber fade-in-card">
                <i class="bi bi-person-badge-fill stat-icon"></i>
                <div class="stat-label">Status</div>
                <div class="stat-number" style="font-size:1rem;">{{ $patient->converted_to_user_id ? 'Converted' : 'Walk-in' }}</div>
            </div>
        </div>
    </div>

    <div class="card fade-in-card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>Patient Information</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4"><strong>Full Name</strong><div class="text-muted mt-1">{{ $patient->full_name }}</div></div>
                <div class="col-md-4"><strong>Age</strong><div class="text-muted mt-1">{{ $patient->age ?? 'Not available' }}</div></div>
                <div class="col-md-4"><strong>Date of Birth</strong><div class="text-muted mt-1">{{ $patient->date_of_birth?->format('M j, Y') ?? 'Not available' }}</div></div>
                <div class="col-md-4"><strong>Contact Number</strong><div class="text-muted mt-1">{{ $patient->contact_number ?? 'Not provided' }}</div></div>
                <div class="col-md-4"><strong>Barangay</strong><div class="text-muted mt-1">{{ $patient->barangay ?? 'Barangay Burgos Padlan, San Carlos City, Pangasinan' }}</div></div>
                <div class="col-md-4"><strong>Purok</strong><div class="text-muted mt-1">{{ $patient->purok?->name ?? 'Not assigned' }}</div></div>
                <div class="col-md-6"><strong>Address</strong><div class="text-muted mt-1">{{ $patient->address ?? 'Not provided' }}</div></div>
                <div class="col-md-6"><strong>Reason for Visit</strong><div class="text-muted mt-1">{{ $patient->reason_for_visit ?? 'No reason recorded' }}</div></div>
                <div class="col-md-12"><strong>Notes</strong><div class="text-muted mt-1">{{ $patient->notes ?? 'No notes recorded' }}</div></div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card fade-in-card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-person-check me-2"></i>Recorded By</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3"><strong>Name</strong><div class="text-muted mt-1">{{ $patient->recordedBy?->name ?? 'Unknown' }}</div></div>
                    <div class="mb-3"><strong>Created At</strong><div class="text-muted mt-1">{{ $patient->created_at->format('M j, Y g:i A') }}</div></div>
                    @if($patient->converted_at)
                        <div><strong>Converted At</strong><div class="text-muted mt-1">{{ $patient->converted_at->format('M j, Y g:i A') }}</div></div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card fade-in-card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-send-check-fill me-2"></i>Referral History</h5>
                </div>
                <div class="card-body p-0">
                    @if($patient->checkupReferrals->count())
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Reason</th>
                                        <th>Urgency</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($patient->checkupReferrals as $referral)
                                        <tr>
                                            <td>{{ $referral->reason }}</td>
                                            <td>{{ ucfirst($referral->urgency) }}</td>
                                            <td>{{ ucfirst($referral->status) }}</td>
                                            <td>{{ $referral->created_at->format('M j, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="bi bi-send-x empty-state-icon"></i>
                            <h6>No referrals yet</h6>
                            <p>This walk-in patient has not been referred yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
