@extends('midwife.layout')

@section('title', 'Dashboard - Midwife Portal | ReproCare')

@section('midwife-content')

@php
    $hour = now()->hour;
    $timeOfDay = $hour < 12 ? 'Morning' : ($hour < 17 ? 'Afternoon' : 'Evening');
@endphp

{{-- ═══════════════════════════════
     PAGE HERO
═══════════════════════════════ --}}
<div class="page-hero fade-in-card">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3" style="position:relative;z-index:1;">
        <div>
            <div class="page-hero-title">
                Good {{ $timeOfDay }}, {{ auth()->user()->name }}! 👋
            </div>
            <p class="page-hero-subtitle">
                <i class="bi bi-calendar3 me-1"></i>{{ now()->format('l, F j, Y') }}
                &nbsp;·&nbsp; Midwife Portal
            </p>
        </div>
        <a href="{{ route('midwife.checkups.create') }}" class="btn-hero-primary">
            <i class="bi bi-plus-circle-fill"></i> Schedule Checkup
        </a>
    </div>

    {{-- Today's Summary Bar --}}
    <div class="d-flex flex-wrap gap-2 mt-3" style="position:relative;z-index:1;">
        <span class="summary-chip chip-primary">
            <i class="bi bi-clipboard-check"></i>
            {{ $scheduledCheckups }} Checkups Scheduled
        </span>
        @if($highRiskPatients > 0)
        <span class="summary-chip chip-danger">
            <i class="bi bi-exclamation-triangle-fill"></i>
            {{ $highRiskPatients }} High Risk
        </span>
        @endif
        @if($missedCheckups > 0)
        <span class="summary-chip chip-warning">
            <i class="bi bi-calendar-x"></i>
            {{ $missedCheckups }} Missed
        </span>
        @endif
        <span class="summary-chip chip-success">
            <i class="bi bi-people-fill"></i>
            {{ $totalPatients }} Total Patients
        </span>
    </div>
</div>

{{-- ═══════════════════════════════
     URGENT ALERTS (conditional)
═══════════════════════════════ --}}
@if($highRiskPatients > 0 || $missedCheckups > 0)
<div class="card mb-4 fade-in-card" style="border-left: 4px solid var(--warning); background: var(--bg-card);">
    <div class="card-body d-flex align-items-center gap-3 py-3">
        <div style="width:42px;height:42px;border-radius:12px;background:rgba(245,158,11,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="bi bi-exclamation-triangle-fill" style="color:var(--warning);font-size:1.2rem;"></i>
        </div>
        <div>
            <div style="font-weight:700;font-size:0.9rem;color:var(--text);">Attention Required</div>
            <div style="font-size:0.82rem;color:var(--text-muted);">
                @if($highRiskPatients > 0)
                    <span style="color:var(--danger);">{{ $highRiskPatients }} high-risk patient(s)</span>
                    @if($missedCheckups > 0) &nbsp;·&nbsp; @endif
                @endif
                @if($missedCheckups > 0)
                    <span style="color:var(--warning);">{{ $missedCheckups }} missed checkup(s)</span>
                @endif
                &nbsp;need your attention.
            </div>
        </div>
        <a href="{{ route('midwife.pregnancies.index') }}" class="btn btn-sm ms-auto" style="background:rgba(245,158,11,0.15);border:1px solid rgba(245,158,11,0.4);color:var(--warning);border-radius:10px;white-space:nowrap;">
            View All
        </a>
    </div>
</div>
@endif

{{-- ═══════════════════════════════
     STAT CARDS
═══════════════════════════════ --}}
<div class="row g-3 mb-4">

    <div class="col-xl-3 col-md-6">
        <div class="stat-card stat-purple fade-in-card">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <div class="stat-label">Total Patients</div>
            <div class="stat-number" data-count="{{ $totalPatients }}">0</div>
            <div class="stat-trend up">
                <i class="bi bi-arrow-up-short"></i> Registered patients
            </div>
            <canvas id="sparkline1" width="80" height="36"
                    style="position:absolute;bottom:1rem;right:1rem;opacity:0.5;"></canvas>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stat-card stat-rose fade-in-card">
            <div class="stat-icon"><i class="bi bi-heart-pulse-fill"></i></div>
            <div class="stat-label">Active Pregnancies</div>
            <div class="stat-number" data-count="{{ $activePregnancies }}">0</div>
            <div class="stat-trend up">
                <i class="bi bi-arrow-up-short"></i> Currently monitoring
            </div>
            <canvas id="sparkline2" width="80" height="36"
                    style="position:absolute;bottom:1rem;right:1rem;opacity:0.5;"></canvas>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stat-card stat-cyan fade-in-card">
            <div class="stat-icon"><i class="bi bi-calendar-check-fill"></i></div>
            <div class="stat-label">Scheduled Checkups</div>
            <div class="stat-number" data-count="{{ $scheduledCheckups }}">0</div>
            <div class="stat-trend">
                <i class="bi bi-calendar3"></i> Upcoming sessions
            </div>
            <canvas id="sparkline3" width="80" height="36"
                    style="position:absolute;bottom:1rem;right:1rem;opacity:0.5;"></canvas>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stat-card stat-amber fade-in-card">
            <div class="stat-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <div class="stat-label">High Risk Patients</div>
            <div class="stat-number" data-count="{{ $highRiskPatients }}">0</div>
            <div class="stat-trend danger">
                <i class="bi bi-arrow-up-short"></i> Requires attention
            </div>
            <canvas id="sparkline4" width="80" height="36"
                    style="position:absolute;bottom:1rem;right:1rem;opacity:0.5;"></canvas>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════
     MAIN CONTENT GRID
═══════════════════════════════ --}}
<div class="row g-4">

    {{-- LEFT: Recent Checkups --}}
    <div class="col-lg-8">
        <div class="card fade-in-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-700" style="font-family:'Plus Jakarta Sans',sans-serif;">
                    <i class="bi bi-clipboard-heart me-2" style="color:var(--primary-light);"></i>
                    Recent Checkups
                </h5>
                <a href="{{ route('midwife.checkups.index') }}" class="btn btn-sm btn-outline-primary">
                    View All <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                @if($recentCheckups->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Date</th>
                                    <th>Purpose</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentCheckups as $checkup)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--accent-violet));display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;color:#fff;flex-shrink:0;">
                                                    {{ strtoupper(substr(optional($checkup->user)->name ?? 'U', 0, 1)) }}
                                                </div>
                                                <span style="font-weight:500;">{{ optional($checkup->user)->name ?? 'Unknown User' }}</span>
                                            </div>
                                        </td>
                                        <td style="color:var(--text-muted);font-size:0.875rem;">
                                            {{ $checkup->scheduled_date->format('M j, Y') }}
                                        </td>
                                        <td style="font-size:0.875rem;">{{ $checkup->purpose }}</td>
                                        <td>
                                            <span class="status-{{ strtolower($checkup->status) }}">
                                                {{ ucfirst($checkup->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="bi bi-calendar-x empty-state-icon"></i>
                        <h6>No Recent Checkups</h6>
                        <p>Schedule your first checkup to get started.</p>
                        <a href="{{ route('midwife.checkups.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle me-1"></i> Schedule Checkup
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- RIGHT COLUMN --}}
    <div class="col-lg-4">

        {{-- Missed Checkups --}}
        <div class="card fade-in-card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-700" style="font-family:'Plus Jakarta Sans',sans-serif;">
                    <i class="bi bi-calendar-x me-2" style="color:var(--danger);"></i>
                    Missed Checkups
                </h5>
                @if($missedCheckups > 0)
                <span class="badge" style="background:var(--danger);color:#fff;font-size:0.75rem;padding:0.35em 0.65em;border-radius:8px;">
                    {{ $missedCheckups }}
                </span>
                @endif
            </div>
            <div class="card-body">
                @if($missedCheckups > 0)
                    <div class="alert alert-danger d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
                        <span><strong>{{ $missedCheckups }}</strong> patient(s) missed their scheduled checkups.</span>
                    </div>
                    <a href="{{ route('midwife.checkups.index') }}" class="btn btn-danger w-100">
                        <i class="bi bi-eye me-1"></i> View Missed Checkups
                    </a>
                @else
                    <div class="empty-state" style="padding:1.5rem;">
                        <i class="bi bi-check-circle-fill" style="font-size:2.5rem;color:var(--success);display:block;margin-bottom:0.75rem;"></i>
                        <h6 style="color:var(--success);">All Good!</h6>
                        <p class="mb-0">No missed checkups. Keep it up!</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="card fade-in-card">
            <div class="card-header">
                <h5 class="mb-0 fw-700" style="font-family:'Plus Jakarta Sans',sans-serif;">
                    <i class="bi bi-lightning-charge-fill me-2" style="color:var(--warning);"></i>
                    Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-6">
                        <a href="{{ route('midwife.health-records.create') }}" class="quick-action-tile">
                            <i class="bi bi-clipboard-plus-fill"></i>
                            <span>Add Health Record</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('midwife.pregnancies.create') }}" class="quick-action-tile">
                            <i class="bi bi-heart-fill" style="color:#f43f8e;"></i>
                            <span>Record Pregnancy</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('midwife.learning.create') }}" class="quick-action-tile">
                            <i class="bi bi-book-fill" style="color:var(--info);"></i>
                            <span>Add Learning</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('midwife.notifications.create') }}" class="quick-action-tile">
                            <i class="bi bi-bell-fill" style="color:var(--warning);"></i>
                            <span>Send Notification</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('midwife.patients') }}" class="quick-action-tile">
                            <i class="bi bi-people-fill" style="color:var(--success);"></i>
                            <span>All Patients</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('midwife.reports.index') }}" class="quick-action-tile">
                            <i class="bi bi-file-earmark-bar-graph-fill" style="color:var(--accent-violet);"></i>
                            <span>Reports</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
// Sparkline helper
function drawSparkline(canvasId, data, color) {
    const canvas = document.getElementById(canvasId);
    if (!canvas || !window.Chart) return;
    new Chart(canvas, {
        type: 'line',
        data: {
            labels: data.map((_, i) => i),
            datasets: [{
                data: data,
                borderColor: color || 'rgba(255,255,255,0.9)',
                borderWidth: 2,
                pointRadius: 0,
                tension: 0.4,
                fill: false
            }]
        },
        options: {
            responsive: false,
            plugins: { legend: { display: false }, tooltip: { enabled: false } },
            scales: { x: { display: false }, y: { display: false } },
            animation: { duration: 1000 }
        }
    });
}

// Animate stat numbers
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
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    drawSparkline('sparkline1', [8, 12, 10, 15, 13, 18, {{ $totalPatients }}]);
    drawSparkline('sparkline2', [3, 5, 4, 7, 6, 8, {{ $activePregnancies }}]);
    drawSparkline('sparkline3', [5, 8, 6, 10, 9, 12, {{ $scheduledCheckups }}]);
    drawSparkline('sparkline4', [2, 3, 2, 4, 3, 5, {{ $highRiskPatients }}]);
    animateStatNumbers();
});
</script>
@endpush

@endsection
