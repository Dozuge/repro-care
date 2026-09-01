@extends('midwife.layout')

@section('title', 'Referral Details - ReproCare')

@section('midwife-content')
@php
    $isWalkIn = (bool) $referral->walk_in_patient_id;
    $patient = $isWalkIn ? $referral->walkInPatient : $referral->woman;
    $patientName = $isWalkIn ? ($patient->full_name ?? $referral->patient_name) : ($patient->name ?? $referral->patient_name);
    $patientSubtitle = $isWalkIn ? ($patient->contact_number ?? 'Walk-in woman') : ($patient->email ?? 'Registered woman');
    $patientImage = $isWalkIn ? '/images/avatars/avatar-female.svg' : ($patient->profile_image_url ?? '/images/avatars/avatar-female.svg');
@endphp

<div class="py-4">
    <div class="page-hero fade-in-card mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3" style="position:relative;z-index:1;">
            <div>
                <h1 class="page-hero-title"><i class="bi bi-clipboard2-heart-fill me-2"></i>Referral Details</h1>
                <p class="page-hero-subtitle">Accepting this referral creates a checkup and removes it from the referral list.</p>
            </div>
            <a href="{{ route('midwife.referrals.index') }}" class="btn-hero-secondary">
                <i class="bi bi-arrow-left"></i> Back to Referrals
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success fade-in-card">{{ session('success') }}</div>
    @endif

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
                        <div><strong>Urgency</strong><div>{{ ucfirst($referral->urgency) }}</div></div>
                        <div><strong>Status</strong><div>{{ ucfirst($referral->status) }}</div></div>
                        <div><strong>Referred By</strong><div>{{ $referral->referredByBhw?->name ?? 'N/A' }}</div></div>
                        <div><strong>Created</strong><div>{{ $referral->created_at->format('M j, Y h:i A') }}</div></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4 fade-in-card" style="background:var(--bg-card);">
                <div class="card-body p-4">
                    <h5 class="mb-3" style="color:var(--text);">Referral Information</h5>
                    <div class="row g-3">
                        <div class="col-md-6"><strong>Reason</strong><div>{{ $referral->reason }}</div></div>
                        <div class="col-md-6"><strong>BHW Notes</strong><div>{{ $referral->bhw_notes ?: 'No notes provided' }}</div></div>
                        @if($isWalkIn)
                            <div class="col-md-6"><strong>Address</strong><div>{{ $patient->address ?? 'N/A' }}</div></div>
                            <div class="col-md-6"><strong>Barangay</strong><div>{{ $patient->barangay ?? 'N/A' }}</div></div>
                        @else
                            <div class="col-md-6"><strong>Address</strong><div>{{ $patient->address ?? 'N/A' }}</div></div>
                            <div class="col-md-6"><strong>Barangay</strong><div>{{ $patient->barangay ?? 'N/A' }}</div></div>
                        @endif
                        @if($referral->midwife_notes)
                            <div class="col-12"><strong>Midwife Notes</strong><div>{{ $referral->midwife_notes }}</div></div>
                        @endif
                    </div>
                </div>
            </div>

            @if($referral->convertedCheckup)
                <div class="card border-0 shadow-sm mb-4 fade-in-card" style="background:var(--bg-card);">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h5 class="mb-1" style="color:var(--text);">Referral already converted</h5>
                            <p class="mb-0 text-muted">This referral now has a linked checkup schedule.</p>
                        </div>
                        <a href="{{ route('midwife.checkups.show', $referral->convertedCheckup->id) }}" class="btn btn-outline-primary">
                            <i class="bi bi-calendar-check"></i> View Checkup
                        </a>
                    </div>
                </div>
            @endif

            @if(in_array($referral->status, ['pending', 'reviewed'], true) && !$referral->convertedCheckup)
                <div class="card border-0 shadow-sm fade-in-card" style="background:var(--bg-card);">
                    <div class="card-body p-4">
                        <h5 class="mb-3" style="color:var(--text);">Actions</h5>
                        <div class="d-flex gap-2 flex-wrap">
                            @if($referral->status === 'pending')
                                <form method="POST" action="{{ route('midwife.referrals.review', $referral->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="bi bi-check2-circle"></i> Mark Reviewed
                                    </button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('midwife.referrals.convert', $referral->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-calendar-plus"></i> Accept and Create Checkup
                                </button>
                            </form>
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#declineModal">
                                <i class="bi bi-x-circle"></i> Decline
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="declineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Decline Referral</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('midwife.referrals.decline', $referral->id) }}">
                @csrf
                <div class="modal-body">
                    <label for="midwife_notes" class="form-label">Reason for declining</label>
                    <textarea name="midwife_notes" id="midwife_notes" class="form-control" rows="4" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Decline Referral</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
