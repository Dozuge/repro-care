@extends('bhw.layout')

@section('title', 'Checkup Referrals - ReproCare')

@section('bhw-content')

{{-- ═══════════════════════════════
     PAGE HERO
════════════════════════════════ --}}
<div class="page-hero fade-in-card mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3" style="position:relative;z-index:1;">
        <div>
            <div class="page-hero-title">
                <i class="bi bi-share-fill me-2"></i>Checkup Referrals
            </div>
            <p class="page-hero-subtitle">
                <i class="bi bi-calendar3 me-1"></i>{{ now()->format('l, F j, Y') }}
                &nbsp;·&nbsp; Manage checkup referrals to midwives
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('bhw.referrals.create') }}" class="btn"
                    style="background:#fff; color:var(--primary); border-radius:12px; padding:0.6rem 1.25rem; font-weight:600; display:flex; align-items:center; gap:0.5rem; box-shadow:0 4px 15px rgba(0,0,0,0.1);">
                <i class="bi bi-plus-circle-fill"></i> New Checkup Referral
            </a>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════
     STATS CARDS
════════════════════════════════ --}}
@php
    $pendingCount = $referrals->where('status', 'pending')->count();
    $reviewedCount = $referrals->where('status', 'reviewed')->count();
    $scheduledCount = $referrals->where('status', 'scheduled')->count();
@endphp
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card stat-cyan fade-in-card">
            <i class="bi bi-clock stat-icon"></i>
            <div class="stat-label">Pending</div>
            <div class="stat-number">{{ $pendingCount }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card stat-green fade-in-card">
            <i class="bi bi-eye-fill stat-icon"></i>
            <div class="stat-label">Reviewed</div>
            <div class="stat-number">{{ $reviewedCount }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card stat-success fade-in-card">
            <i class="bi bi-calendar-check stat-icon"></i>
            <div class="stat-label">Scheduled</div>
            <div class="stat-number">{{ $scheduledCount }}</div>
        </div>
    </div>
</div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ═══════════════════════════════
         REFERRALS TABLE
    ════════════════════════════════ --}}
    <div class="card fade-in-card" style="border:none; background:var(--bg-card);">
        <div class="card-header" style="background:transparent; border-bottom:1px solid var(--border-color); padding:1rem 1.5rem;">
            <h5 class="mb-0 fw-700" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--text);">
                <i class="bi bi-share-fill me-2" style="color:var(--primary-light);"></i>
                My Checkup Referrals
            </h5>
        </div>
        <div class="card-body p-4">
            @if($referrals->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Reason</th>
                                <th>Urgency</th>
                                <th>Assigned Midwife</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($referrals as $referral)
                                <tr class="{{ $referral->urgency === 'emergency' ? 'table-danger' : ($referral->urgency === 'urgent' ? 'table-warning' : '') }}">
                                    <td>{{ $referral->patient_name }}</td>
                                    <td>{{ $referral->reason }}</td>
                                    <td>
                                        @if($referral->urgency === 'emergency')
                                            <span class="badge bg-danger">Emergency</span>
                                        @elseif($referral->urgency === 'urgent')
                                            <span class="badge bg-warning text-dark">Urgent</span>
                                        @else
                                            <span class="badge bg-info">Routine</span>
                                        @endif
                                    </td>
                                    <td>{{ $referral->assignedMidwife?->name ?? '—' }}</td>
                                    <td>
                                        @if($referral->status === 'pending')
                                            <span class="badge bg-secondary">Pending</span>
                                        @elseif($referral->status === 'reviewed')
                                            <span class="badge bg-primary">Reviewed</span>
                                        @elseif($referral->status === 'scheduled')
                                            <span class="badge bg-success">Scheduled</span>
                                        @elseif($referral->status === 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($referral->status === 'declined')
                                            <span class="badge bg-danger">Declined</span>
                                        @endif
                                    </td>
                                    <td>{{ $referral->created_at->format('M j, Y') }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm table-actions">
                                            <a href="{{ route('bhw.referrals.show', $referral->id) }}" class="btn btn-outline-primary" title="View Referral">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $referrals->links() }}
                </div>
            @else
                <div class="empty-state">
                    <i class="bi bi-share empty-state-icon"></i>
                    <h6>No Referrals Created</h6>
                    <p>You haven't created any checkup referrals yet.</p>
                    <a href="{{ route('bhw.referrals.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Create Your First Referral
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
