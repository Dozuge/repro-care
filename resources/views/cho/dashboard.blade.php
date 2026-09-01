@extends('cho.layout')

@section('title', 'Dashboard - CHO Portal | ReproCare')

@section('cho-content')

@php
    $hour = now()->hour;
    $timeOfDay = $hour < 12 ? 'Morning' : ($hour < 17 ? 'Afternoon' : 'Evening');
@endphp

<div class="page-hero fade-in-card">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3" style="position:relative;z-index:1;">
        <div>
            <div class="page-hero-title">
                Good {{ $timeOfDay }}, {{ auth()->user()->name }}! 👋
            </div>
            <p class="page-hero-subtitle">
                <i class="bi bi-calendar3 me-1"></i>{{ now()->format('l, F j, Y') }}
                &nbsp;·&nbsp; City Health Office (CHO) Portal
            </p>
        </div>
    </div>

    <div class="d-flex flex-wrap gap-2 mt-3" style="position:relative;z-index:1;">
        <span class="summary-chip chip-primary" style="background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.3); color: #fff; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">
            <i class="bi bi-shield-check me-1"></i> ANC Coverage: {{ $ancCoverageRate }}%
        </span>
        <span class="summary-chip chip-success" style="background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.3); color: #fff; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">
            <i class="bi bi-people-fill me-1"></i> {{ $totalPatients }} Patients Registered
        </span>
        @if($pendingSupplyRequests > 0)
        <span class="summary-chip chip-warning" style="background: rgba(245,158,11,0.25); border: 1px solid rgba(245,158,11,0.4); color: #fef08a; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">
            <i class="bi bi-box-seam me-1"></i> {{ $pendingSupplyRequests }} Pending Supplies
        </span>
        @endif
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card stat-purple fade-in-card">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <div class="stat-label">Total Staff Accounts</div>
            <div class="stat-number" data-count="{{ $totalUsers }}">0</div>
            <div class="stat-trend">
                <i class="bi bi-shield-lock-fill"></i> Health workers & admins
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stat-card stat-rose fade-in-card">
            <div class="stat-icon"><i class="bi bi-heart-pulse-fill"></i></div>
            <div class="stat-label">Active Pregnancies</div>
            <div class="stat-number" data-count="{{ $activePregnancies }}">0</div>
            <div class="stat-trend">
                <i class="bi bi-activity"></i> Monitoring city-wide
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stat-card stat-cyan fade-in-card">
            <div class="stat-icon"><i class="bi bi-box-seam-fill"></i></div>
            <div class="stat-label">Supply Requests</div>
            <div class="stat-number" data-count="{{ $totalSupplyRequests }}">0</div>
            <div class="stat-trend up">
                <i class="bi bi-clock-history"></i> {{ $pendingSupplyRequests }} pending review
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stat-card stat-danger fade-in-card">
            <div class="stat-icon"><i class="bi bi-journal-x"></i></div>
            <div class="stat-label">Maternal Deaths</div>
            <div class="stat-number" data-count="{{ $totalDeaths }}">0</div>
            <div class="stat-trend danger">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ $totalNearMiss }} Near-miss events
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Left: Recent Supply Requests --}}
    <div class="col-lg-8">
        <div class="card fade-in-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-700" style="font-family:'Plus Jakarta Sans',sans-serif;">
                    <i class="bi bi-box-seam me-2" style="color:var(--primary-light);"></i>
                    Recent Supply Requests
                </h5>
                <a href="{{ route('cho.supply-requests.index') }}" class="btn btn-sm btn-outline-primary">
                    View All <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                @if($recentRequests->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Item Name</th>
                                    <th>Requested By</th>
                                    <th>Quantity</th>
                                    <th>Urgency</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentRequests as $request)
                                    <tr>
                                        <td>
                                            <span style="font-weight:600;">{{ $request->supply_name }}</span>
                                            <div style="font-size:0.75rem; color:var(--text-muted);">{{ ucfirst($request->supply_category) }}</div>
                                        </td>
                                        <td style="font-size:0.875rem;">
                                            {{ $request->requestedBy->name ?? 'Unknown' }}
                                        </td>
                                        <td style="font-size:0.875rem;">
                                            {{ $request->quantity_requested }} {{ $request->unit ?? 'pcs' }}
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $request->urgency === 'emergency' ? 'danger' : ($request->urgency === 'urgent' ? 'warning' : 'info') }} text-white text-xs">
                                                {{ ucfirst($request->urgency) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $request->status === 'approved' ? 'success' : ($request->status === 'declined' ? 'danger' : 'secondary') }} text-white text-xs">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('cho.supply-requests.show', $request->id) }}" class="btn btn-xs btn-primary py-1 px-2" style="font-size:0.75rem; border-radius:8px;">
                                                Review
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-box-seam" style="font-size: 2.5rem; color: var(--text-muted);"></i>
                        <h6 class="mt-3">No Supply Requests</h6>
                        <p class="text-muted text-xs">Supply requests from RHU centers will appear here.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Right: Quick Actions & Recent Deaths --}}
    <div class="col-lg-4">
        {{-- Quick Actions --}}
        <div class="card fade-in-card mb-4">
            <div class="card-header">
                <h5 class="mb-0 fw-700" style="font-family:'Plus Jakarta Sans',sans-serif;">
                    <i class="bi bi-lightning-charge-fill me-2" style="color:var(--warning);"></i>
                    Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-6">
                        <a href="{{ route('cho.users.create') }}" class="quick-action-tile" style="display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:1rem; border:1px solid var(--border); border-radius:12px; text-decoration:none; color:var(--text); background:var(--bg-card2); transition: all var(--transition-base);">
                            <i class="bi bi-person-plus-fill mb-2" style="font-size:1.5rem; color:var(--primary-light);"></i>
                            <span style="font-size:0.8rem; font-weight:600;">Add Health Worker</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('cho.users.index') }}" class="quick-action-tile" style="display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:1rem; border:1px solid var(--border); border-radius:12px; text-decoration:none; color:var(--text); background:var(--bg-card2); transition: all var(--transition-base);">
                            <i class="bi bi-people-fill mb-2" style="font-size:1.5rem; color:#10b981;"></i>
                            <span style="font-size:0.8rem; font-weight:600;">Manage Accounts</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('cho.analytics') }}" class="quick-action-tile" style="display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:1rem; border:1px solid var(--border); border-radius:12px; text-decoration:none; color:var(--text); background:var(--bg-card2); transition: all var(--transition-base);">
                            <i class="bi bi-bar-chart-fill mb-2" style="font-size:1.5rem; color:var(--info);"></i>
                            <span style="font-size:0.8rem; font-weight:600;">View Analytics</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('cho.maternal-deaths.index') }}" class="quick-action-tile" style="display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:1rem; border:1px solid var(--border); border-radius:12px; text-decoration:none; color:var(--text); background:var(--bg-card2); transition: all var(--transition-base);">
                            <i class="bi bi-journal-x mb-2" style="font-size:1.5rem; color:var(--danger);"></i>
                            <span style="font-size:0.8rem; font-weight:600;">Maternal Audit</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Maternal Deaths --}}
        <div class="card fade-in-card">
            <div class="card-header">
                <h5 class="mb-0 fw-700" style="font-family:'Plus Jakarta Sans',sans-serif;">
                    <i class="bi bi-journal-x me-2" style="color:var(--danger);"></i>
                    Recent Deaths overview
                </h5>
            </div>
            <div class="card-body p-0">
                @if($recentDeaths->count() > 0)
                    <ul class="list-group list-group-flush mb-0">
                        @foreach($recentDeaths as $death)
                            <li class="list-group-item bg-transparent" style="border-color: var(--border); padding: 0.85rem 1.25rem;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div style="font-weight:600; font-size:0.875rem;">
                                            {{ $death->patient_name }}
                                        </div>
                                        <div style="font-size:0.75rem; color:var(--text-muted);">
                                            {{ $death->death_date->format('M j, Y') }} &bull; {{ $death->age_at_death ?? 'N/A' }} yrs old
                                        </div>
                                    </div>
                                    <span class="badge bg-{{ $death->audit_status === 'closed' ? 'success' : 'warning' }} text-white text-xs">
                                        {{ ucfirst($death->audit_status) }}
                                    </span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-shield-check" style="font-size: 2rem; color: var(--success);"></i>
                        <p class="text-muted text-xs mt-2 mb-0">No maternal deaths recorded.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function animateStatNumbers() {
    const statNumbers = document.querySelectorAll('.stat-number');
    statNumbers.forEach(stat => {
        const target = parseInt(stat.dataset.count);
        if (target > 0) {
            let current = 0;
            const increment = target / 50;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    stat.textContent = target;
                    clearInterval(timer);
                } else {
                    stat.textContent = Math.floor(current);
                }
            }, 30);
        } else {
            stat.textContent = 0;
        }
    });
}
document.addEventListener('DOMContentLoaded', animateStatNumbers);
</script>
@endpush

@endsection
