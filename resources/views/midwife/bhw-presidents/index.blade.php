@extends('midwife.layout')

@section('title', 'BHW Presidents - Midwife Portal | ReproCare')

@push('styles')
<style>
    .president-avatar {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        object-fit: cover;
        border: 2px solid rgba(255, 255, 255, 0.45);
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.12);
        flex-shrink: 0;
    }
    .president-avatar-fallback {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--primary), var(--accent-violet));
        color: #fff;
        font-weight: 800;
        font-size: 1rem;
        box-shadow: 0 10px 24px rgba(218, 54, 255, 0.28);
        flex-shrink: 0;
    }
    .president-row:hover {
        background: var(--row-hover);
    }
    .metric-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.36rem 0.72rem;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 700;
        border: 1px solid transparent;
    }
    .metric-pill-bhw {
        background: rgba(59, 130, 246, 0.12);
        color: #60a5fa;
        border-color: rgba(59, 130, 246, 0.22);
    }
    .metric-pill-patient {
        background: rgba(16, 185, 129, 0.12);
        color: #34d399;
        border-color: rgba(16, 185, 129, 0.22);
    }
    .president-barangay {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.4rem 0.8rem;
        border-radius: 999px;
        background: var(--bg-card2);
        border: 1px solid var(--border);
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--text);
    }
    .president-actions {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        flex-wrap: wrap;
    }
    .president-action-btn {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--border);
        background: var(--bg-card2);
        color: var(--text-muted);
        text-decoration: none;
        transition: all 0.18s ease;
    }
    .president-action-btn:hover {
        transform: translateY(-1px) scale(1.04);
    }
    .president-action-btn.view:hover {
        color: #22d3ee;
        border-color: rgba(34, 211, 238, 0.35);
        background: rgba(34, 211, 238, 0.1);
    }
    .president-action-btn.edit:hover {
        color: #fbbf24;
        border-color: rgba(251, 191, 36, 0.35);
        background: rgba(251, 191, 36, 0.1);
    }
    .president-summary-note {
        font-size: 0.82rem;
        color: rgba(255, 255, 255, 0.78);
        margin-top: 0.35rem;
    }
    @media (max-width: 768px) {
        .page-hero {
            padding: 1.35rem;
        }
        .president-actions {
            justify-content: flex-start;
        }
    }
</style>
@endpush

@section('midwife-content')
@php
    $totalPresidents = $bhwPresidents->total();
    $managedBhws = $bhwPresidents->sum('managed_bhw_count');
    $supervisedPatients = $bhwPresidents->sum('supervised_patients_count');
    $barangayCount = $bhwPresidents->pluck('barangay')->filter()->unique()->count();
    $activePresident = $bhwPresidents->first();
@endphp

<div class="page-hero fade-in-card">
    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
        <div>
            <h1 class="page-hero-title">
                <i class="bi bi-person-badge-fill me-2"></i>
                BHW Presidents
            </h1>
            <p class="page-hero-subtitle">Manage barangay leads, track their assigned BHWs, and review supervised patients in one place.</p>
            <div class="president-summary-note">
                {{ $barangayCount }} barangay{{ $barangayCount === 1 ? '' : 's' }} covered across the current list.
            </div>
        </div>
        <a href="{{ route('midwife.bhw-presidents.create') }}" class="btn-hero-primary">
            <i class="bi bi-plus-circle-fill"></i>
            {{ $totalPresidents > 0 ? 'Change BHW President' : 'Add BHW President' }}
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success d-flex align-items-center gap-2 mb-4 fade-in-card">
        <i class="bi bi-check-circle-fill flex-shrink-0"></i>
        <span>{{ session('success') }}</span>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="stat-card stat-purple fade-in-card">
            <i class="bi bi-people-fill stat-icon"></i>
            <div class="stat-label">Total Presidents</div>
            <div class="stat-number" data-count="{{ $totalPresidents }}">{{ $totalPresidents }}</div>
            <div class="stat-trend">Registered barangay leaders</div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card stat-cyan fade-in-card">
            <i class="bi bi-diagram-3-fill stat-icon"></i>
            <div class="stat-label">Managed BHWs</div>
            <div class="stat-number" data-count="{{ $managedBhws }}">{{ $managedBhws }}</div>
            <div class="stat-trend">Assignments visible on this page</div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card stat-green fade-in-card">
            <i class="bi bi-heart-pulse-fill stat-icon"></i>
            <div class="stat-label">Supervised Patients</div>
            <div class="stat-number" data-count="{{ $supervisedPatients }}">{{ $supervisedPatients }}</div>
            <div class="stat-trend">Patients under active oversight</div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card stat-amber fade-in-card">
            <i class="bi bi-geo-alt-fill stat-icon"></i>
            <div class="stat-label">Barangays</div>
            <div class="stat-number" data-count="{{ $barangayCount }}">{{ $barangayCount }}</div>
            <div class="stat-trend">Distinct areas represented</div>
        </div>
    </div>
</div>

<div class="table-card fade-in-card">
    <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap px-4 pt-4 pb-3 border-bottom">
        <div>
            <h2 class="mb-1" style="font-size:1.05rem;font-weight:800;color:var(--text);">President Directory</h2>
            <p class="mb-0" style="font-size:0.84rem;color:var(--text-muted);">A styled overview of all BHW presidents currently registered in the system.</p>
        </div>
        <span class="summary-chip chip-primary">
            <i class="bi bi-person-lines-fill"></i>
            {{ $totalPresidents }} total
        </span>
    </div>

    @if($bhwPresidents->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover table-sticky-head mb-0 align-middle">
                <thead>
                    <tr>
                        <th>President</th>
                        <th>Barangay</th>
                        <th>BHWs Managed</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bhwPresidents as $president)
                        <tr class="president-row">
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    @if(!empty($president->profile_image_url))
                                        <img
                                            class="president-avatar"
                                            src="{{ $president->profile_image_url }}"
                                            alt="{{ $president->name }}"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                        >
                                        <div class="president-avatar-fallback" style="display:none;">
                                            {{ strtoupper(substr($president->name, 0, 1)) }}
                                        </div>
                                    @else
                                        <div class="president-avatar-fallback">
                                            {{ strtoupper(substr($president->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div style="font-weight:800;color:var(--text);font-size:0.94rem;">
                                            {{ $president->name }}
                                        </div>
                                        <div style="font-size:0.8rem;color:var(--text-muted);">
                                            <i class="bi bi-envelope me-1"></i>{{ $president->email }}
                                        </div>
                                        <div style="font-size:0.76rem;color:var(--text-dim);">
                                            <i class="bi bi-calendar3 me-1"></i>{{ $president->age ? $president->age . ' years old' : 'Age not set' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="president-barangay">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    {{ $president->barangay ?: 'Not assigned' }}
                                </span>
                            </td>
                            <td>
                                <span class="metric-pill metric-pill-bhw">
                                    <i class="bi bi-diagram-3"></i>
                                    {{ $president->managed_bhw_count }}
                                </span>
                            </td>
                            <td>
                                <div class="president-actions">
                                    <a
                                        href="{{ route('midwife.bhw-presidents.show', $president->id) }}"
                                        class="president-action-btn view"
                                        title="View details"
                                        aria-label="View {{ $president->name }}"
                                    >
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a
                                        href="{{ route('midwife.bhw-presidents.edit', $president->id) }}"
                                        class="president-action-btn edit"
                                        title="Edit record"
                                        aria-label="Edit {{ $president->name }}"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center" style="padding:1.25rem;">
            {{ $bhwPresidents->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="bi bi-person-badge empty-state-icon"></i>
            <h6>No BHW Presidents Found</h6>
            <p>No barangay leaders have been added yet. Create the first record to start organizing BHW assignments.</p>
            <a href="{{ route('midwife.bhw-presidents.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle-fill me-1"></i> Add First BHW President
            </a>
        </div>
    @endif
</div>
@endsection
