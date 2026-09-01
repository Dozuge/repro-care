@extends('midwife.layout')

@section('title', 'Walk-in Woman Details - Midwife Portal | ReproCare')

@section('midwife-content')
<div class="py-4">
    <div class="page-hero fade-in-card mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3" style="position:relative;z-index:1;">
            <div>
                <h1 class="page-hero-title">
                    <i class="bi bi-person-walking me-2"></i>{{ $patient->full_name }}
                </h1>
                <p class="page-hero-subtitle">Walk-in woman details and related referrals.</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                @if(!$patient->converted_to_user_id)
                    <a href="{{ route('midwife.walk-in-patients.edit', $patient->id) }}" class="btn-hero-primary">
                        <i class="bi bi-pencil-square"></i> Edit Walk-in
                    </a>
                @endif
                <a href="{{ route('midwife.patients', ['filter' => 'unregistered']) }}" class="btn-hero-primary">
                    <i class="bi bi-arrow-left"></i> Back to Women
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card fade-in-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-person-vcard-fill me-2" style="color:var(--primary-light);"></i>Walk-in Information</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <small class="text-uppercase" style="letter-spacing:0.08em;color:var(--text-muted);font-size:0.72rem;">Date of Birth</small>
                            <div class="mt-1">{{ $patient->date_of_birth?->format('M d, Y') ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <small class="text-uppercase" style="letter-spacing:0.08em;color:var(--text-muted);font-size:0.72rem;">Age</small>
                            <div class="mt-1">{{ $patient->age ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <small class="text-uppercase" style="letter-spacing:0.08em;color:var(--text-muted);font-size:0.72rem;">Purok</small>
                            <div class="mt-1">{{ $patient->purok?->name ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <small class="text-uppercase" style="letter-spacing:0.08em;color:var(--text-muted);font-size:0.72rem;">Barangay</small>
                            <div class="mt-1">{{ $patient->barangay ?? 'Burgos' }}</div>
                        </div>
                        <div class="col-md-6">
                            <small class="text-uppercase" style="letter-spacing:0.08em;color:var(--text-muted);font-size:0.72rem;">Contact Number</small>
                            <div class="mt-1">{{ $patient->contact_number ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <small class="text-uppercase" style="letter-spacing:0.08em;color:var(--text-muted);font-size:0.72rem;">Status</small>
                            <div class="mt-1">
                                @if($patient->converted_to_user_id)
                                    <span class="summary-chip chip-success">Converted to Registered</span>
                                @else
                                    <span class="summary-chip chip-warning">Walk-in</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-12">
                            <small class="text-uppercase" style="letter-spacing:0.08em;color:var(--text-muted);font-size:0.72rem;">Reason for Visit</small>
                            <div class="mt-1">{{ $patient->reason_for_visit ?: 'Not specified' }}</div>
                        </div>
                        <div class="col-12">
                            <small class="text-uppercase" style="letter-spacing:0.08em;color:var(--text-muted);font-size:0.72rem;">Notes</small>
                            <div class="mt-1">{{ $patient->notes ?: 'No notes provided' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            @if($patient->checkupReferrals->count() > 0)
                <div class="card fade-in-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-clipboard-heart-fill me-2" style="color:var(--primary-light);"></i>Related Referrals</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Referred By</th>
                                        <th>Reason</th>
                                        <th>Urgency</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($patient->checkupReferrals as $referral)
                                        <tr>
                                            <td>{{ $referral->referredByBhw?->name ?? '—' }}</td>
                                            <td>{{ $referral->reason }}</td>
                                            <td>{{ ucfirst($referral->urgency) }}</td>
                                            <td>{{ ucfirst($referral->status) }}</td>
                                            <td>{{ $referral->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card fade-in-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2" style="color:var(--primary-light);"></i>Record Info</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-uppercase" style="letter-spacing:0.08em;color:var(--text-muted);font-size:0.72rem;">Recorded On</small>
                        <div class="mt-1">{{ $patient->created_at->format('M d, Y g:i A') }}</div>
                    </div>
                    <div>
                        <small class="text-uppercase" style="letter-spacing:0.08em;color:var(--text-muted);font-size:0.72rem;">Recorded By</small>
                        <div class="mt-1">{{ $patient->recordedBy?->name ?? '—' }}</div>
                    </div>
                </div>
            </div>

            @if(!$patient->converted_to_user_id)
                <div class="card fade-in-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-trash-fill me-2" style="color:var(--danger);"></i>Delete Record</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">Delete this walk-in record if it was created by mistake.</p>
                        <form method="POST" action="{{ route('midwife.walk-in-patients.destroy', $patient->id) }}" onsubmit="return confirm('Are you sure you want to delete this walk-in patient? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-trash me-1"></i> Delete Walk-in
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
