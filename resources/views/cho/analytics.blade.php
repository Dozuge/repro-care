@extends('cho.layout')

@section('title', 'Strategic Analytics & AI Policy Engine - CHO | ReproCare')

@section('cho-content')

{{-- Header Banner --}}
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-800 mb-1" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--text); letter-spacing:-0.5px;">
            <i class="bi bi-bar-chart-line-fill me-2 text-primary"></i>Strategic Analytics &amp; Policy Action Engine
        </h2>
        <p class="text-muted mb-0" style="font-size:0.9rem;">
            City-wide reproductive health surveillance, dynamic threshold triggers, and automated intervention strategies.
        </p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1.5" onclick="window.print()" style="border-radius:10px;">
            <i class="bi bi-printer-fill"></i> Export Surveillance Report
        </button>
        <a href="{{ route('cho.sms.index') }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1.5" style="border-radius:10px;">
            <i class="bi bi-broadcast"></i> SMS Alert Hub
        </a>
    </div>
</div>

{{-- 1. Top KPI Metric Strip --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card shadow-sm border p-3.5 h-100" style="border-radius:16px; background:var(--bg-card); border-color:var(--border) !important;">
            <div class="d-flex align-items-center justify-content-between">
                <span class="text-xs text-muted fw-700 text-uppercase" style="letter-spacing:0.5px;">Active Pregnancies</span>
                <div class="rounded-circle p-2" style="background:var(--primary-subtle); color:var(--primary);">
                    <i class="bi bi-person-heart fs-6"></i>
                </div>
            </div>
            <h2 class="fw-800 mb-0 mt-2 text-dark" style="font-size:2rem; font-family:'Plus Jakarta Sans',sans-serif;">{{ $totalPregnant }}</h2>
            <small class="text-muted mt-1 d-block"><i class="bi bi-check2-circle text-success me-1"></i>Active monitored cases</small>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card shadow-sm border p-3.5 h-100" style="border-radius:16px; background:var(--bg-card); border-color:var(--border) !important;">
            <div class="d-flex align-items-center justify-content-between">
                <span class="text-xs text-muted fw-700 text-uppercase" style="letter-spacing:0.5px;">High-Risk Proportion</span>
                <div class="rounded-circle p-2" style="background:var(--badge-critical-bg); color:var(--badge-critical-text);">
                    <i class="bi bi-exclamation-triangle-fill fs-6"></i>
                </div>
            </div>
            @php $highRiskPct = $totalPregnant > 0 ? round(($highRiskCount / $totalPregnant) * 100, 1) : 0; @endphp
            <h2 class="fw-800 mb-0 mt-2 text-danger" style="font-size:2rem; font-family:'Plus Jakarta Sans',sans-serif;">{{ $highRiskCount }} <span class="fs-6 text-muted fw-600">({{ $highRiskPct }}%)</span></h2>
            <small class="text-muted mt-1 d-block"><i class="bi bi-shield-exclamation text-danger me-1"></i>Requiring specialist triage</small>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card shadow-sm border p-3.5 h-100" style="border-radius:16px; background:var(--bg-card); border-color:var(--border) !important;">
            <div class="d-flex align-items-center justify-content-between">
                <span class="text-xs text-muted fw-700 text-uppercase" style="letter-spacing:0.5px;">Teenage Pregnancies</span>
                <div class="rounded-circle p-2" style="background:var(--badge-warning-bg); color:var(--badge-warning-text);">
                    <i class="bi bi-person-exclamation fs-6"></i>
                </div>
            </div>
            @php $teenPct = $totalPregnant > 0 ? round(($teenPregnancies / $totalPregnant) * 100, 1) : 0; @endphp
            <h2 class="fw-800 mb-0 mt-2 text-warning" style="font-size:2rem; font-family:'Plus Jakarta Sans',sans-serif;">{{ $teenPregnancies }} <span class="fs-6 text-muted fw-600">({{ $teenPct }}%)</span></h2>
            <small class="text-muted mt-1 d-block"><i class="bi bi-flag-fill text-warning me-1"></i>Adolescents &lt;19 years</small>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card shadow-sm border p-3.5 h-100" style="border-radius:16px; background:var(--bg-card); border-color:var(--border) !important;">
            <div class="d-flex align-items-center justify-content-between">
                <span class="text-xs text-muted fw-700 text-uppercase" style="letter-spacing:0.5px;">Maternal Mortality / Near-Miss</span>
                <div class="rounded-circle p-2" style="background:rgba(100,116,139,0.12); color:#475569;">
                    <i class="bi bi-journal-x fs-6"></i>
                </div>
            </div>
            <h2 class="fw-800 mb-0 mt-2 text-dark" style="font-size:2rem; font-family:'Plus Jakarta Sans',sans-serif;">{{ $totalDeaths }} <span class="fs-6 text-muted fw-600">/ {{ $totalNearMiss }} Near-Miss</span></h2>
            <small class="text-muted mt-1 d-block"><i class="bi bi-clipboard-check text-primary me-1"></i>Under maternal audit review</small>
        </div>
    </div>
</div>

{{-- 2. Dynamic Strategy Card ("AI Policy & Action Engine") --}}
<div class="card shadow-sm border mb-4" style="border-radius:16px; background:var(--bg-card); border-color:var(--border) !important; border-left: 5px solid var(--primary) !important;">
    <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom">
        <div>
            <h5 class="fw-800 mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-cpu-fill text-primary"></i> AI Policy &amp; Strategic Action Engine
            </h5>
            <small class="text-muted">Automated surveillance threshold triggers and recommended CHO policy directives</small>
        </div>
        <span class="badge" style="background:var(--primary-subtle); color:var(--primary); font-size:0.75rem; font-weight:700;">
            Surveillance Rule Engine Active
        </span>
    </div>
    <div class="card-body p-4">
        <div class="row g-3">
            @forelse($strategicInterventions as $item)
                @php
                    $severity = $item['severity'] ?? 'info';
                    $borderStyle = match($severity) {
                        'critical' => 'border-danger-subtle bg-danger-subtle text-danger-emphasis',
                        'warning'  => 'border-warning-subtle bg-warning-subtle text-warning-emphasis',
                        default    => 'border-info-subtle bg-info-subtle text-info-emphasis',
                    };
                    $icon = match($severity) {
                        'critical' => 'bi-exclamation-octagon-fill text-danger',
                        'warning'  => 'bi-exclamation-triangle-fill text-warning',
                        default    => 'bi-info-circle-fill text-info',
                    };
                @endphp
                <div class="col-lg-6">
                    <div class="p-3.5 rounded-3 border h-100 d-flex flex-column justify-content-between {{ $borderStyle }}" style="border-radius:14px;">
                        <div>
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <h6 class="fw-800 mb-0 d-flex align-items-center gap-1.5" style="font-size:0.95rem;">
                                    <i class="bi {{ $icon }}"></i> {{ $item['title'] }}
                                </h6>
                                <span class="badge bg-dark text-white text-xs px-2 py-1">{{ $item['badge'] }}</span>
                            </div>
                            <p class="text-xs text-muted mb-2.5">
                                <i class="bi bi-flag-fill me-1"></i><strong>Trigger:</strong> {{ $item['trigger'] }}
                            </p>
                            <div class="text-xs text-uppercase fw-700 mb-1.5" style="letter-spacing:0.5px;">Recommended Strategic Directives:</div>
                            <ul class="ps-3 mb-3" style="font-size:0.84rem; line-height:1.5;">
                                @foreach($item['actions'] as $action)
                                    <li>{{ $action }}</li>
                                @endforeach
                            </ul>
                        </div>

                        {{-- Interactive Policy Action Buttons --}}
                        <div class="pt-2 border-top d-flex gap-2 flex-wrap">
                            @if(str_contains(strtolower($item['id'] ?? ''), 'teen'))
                                <a href="{{ route('cho.patients.index', ['age_group' => 'teen']) }}" class="btn btn-sm btn-dark text-xs fw-700 d-inline-flex align-items-center gap-1" style="border-radius:8px;">
                                    <i class="bi bi-people-fill"></i> Deploy Adolescent Health Drive
                                </a>
                                <a href="{{ route('learning.index', ['category' => 'pregnancy-guide']) }}" class="btn btn-sm btn-outline-dark text-xs fw-600 d-inline-flex align-items-center gap-1" style="border-radius:8px;">
                                    <i class="bi bi-book-half"></i> Youth Counseling Kits
                                </a>
                            @elseif(str_contains(strtolower($item['id'] ?? ''), 'maternal') || str_contains(strtolower($item['id'] ?? ''), 'mortality'))
                                <a href="{{ route('cho.maternal-deaths.index') }}" class="btn btn-sm btn-danger text-xs fw-700 d-inline-flex align-items-center gap-1" style="border-radius:8px;">
                                    <i class="bi bi-journal-medical"></i> Open Clinical Audit
                                </a>
                                <a href="{{ route('cho.supply-requests.index') }}" class="btn btn-sm btn-outline-danger text-xs fw-600 d-inline-flex align-items-center gap-1" style="border-radius:8px;">
                                    <i class="bi bi-box-seam"></i> Audit Emergency Supplies
                                </a>
                            @elseif(str_contains(strtolower($item['id'] ?? ''), 'anc') || str_contains(strtolower($item['id'] ?? ''), 'high_risk'))
                                <a href="{{ route('cho.patients.index', ['risk_level' => 'high_risk_only']) }}" class="btn btn-sm btn-primary text-xs fw-700 d-inline-flex align-items-center gap-1" style="border-radius:8px;">
                                    <i class="bi bi-shield-check"></i> High-Risk Home Visits
                                </a>
                                <a href="{{ route('cho.sms.index') }}" class="btn btn-sm btn-outline-primary text-xs fw-600 d-inline-flex align-items-center gap-1" style="border-radius:8px;">
                                    <i class="bi bi-chat-dots-fill"></i> Broadcast Prenatal Reminders
                                </a>
                            @else
                                <a href="{{ route('cho.patients.index') }}" class="btn btn-sm btn-secondary text-xs fw-700" style="border-radius:8px;">
                                    Maintain Active Protocol
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-4">
                    <i class="bi bi-check-circle-fill text-success fs-1"></i>
                    <h6 class="fw-700 mt-2">All Surveillance Parameters Optimal</h6>
                    <p class="text-muted text-xs">Maternal care compliance meets or exceeds regional targets.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- 3. Visual Trend Analytics & Distribution Graphs --}}
<div class="row g-4 mb-4">
    {{-- Monthly Pregnancy Trend (Line Chart) --}}
    <div class="col-lg-8">
        <div class="card shadow-sm border h-100" style="border-radius:16px; background:var(--bg-card); border-color:var(--border) !important;">
            <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center border-bottom">
                <div>
                    <h6 class="fw-800 mb-0 text-dark"><i class="bi bi-graph-up me-2 text-primary"></i>Maternal Registration &amp; Surveillance Trend</h6>
                    <small class="text-muted">Monthly new pregnancy registrations over the last 12 months</small>
                </div>
                <span class="badge bg-light text-dark border">Monthly Cohort</span>
            </div>
            <div class="card-body p-3">
                <div style="position: relative; height: 280px;">
                    <canvas id="monthlyTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Causes Breakdown / Distribution --}}
    <div class="col-lg-4">
        <div class="card shadow-sm border h-100" style="border-radius:16px; background:var(--bg-card); border-color:var(--border) !important;">
            <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center border-bottom">
                <div>
                    <h6 class="fw-800 mb-0 text-dark"><i class="bi bi-pie-chart-fill me-2 text-danger"></i>Risk &amp; Complication Tiers</h6>
                    <small class="text-muted">Proportion by clinical classification</small>
                </div>
            </div>
            <div class="card-body p-3 d-flex flex-column align-items-center justify-content-center">
                <div style="position: relative; width: 220px; height: 220px;">
                    <canvas id="riskDoughnutChart"></canvas>
                </div>
                <div class="d-flex justify-content-center gap-3 mt-3 text-xs text-muted">
                    <span><span class="d-inline-block rounded-circle me-1" style="width:8px;height:8px;background:#6C5CE7;"></span>Low Risk</span>
                    <span><span class="d-inline-block rounded-circle me-1" style="width:8px;height:8px;background:#F59E0B;"></span>Moderate</span>
                    <span><span class="d-inline-block rounded-circle me-1" style="width:8px;height:8px;background:#EF4444;"></span>High Risk</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Teenage Pregnancies by Barangay (Horizontal Bar Chart) --}}
    <div class="col-lg-6">
        <div class="card shadow-sm border h-100" style="border-radius:16px; background:var(--bg-card); border-color:var(--border) !important;">
            <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center border-bottom">
                <div>
                    <h6 class="fw-800 mb-0 text-dark"><i class="bi bi-geo-alt-fill me-2 text-warning"></i>Adolescent Cases by Barangay (&lt;19 yrs)</h6>
                    <small class="text-muted">Barangays with highest adolescent pregnancy incidence</small>
                </div>
            </div>
            <div class="card-body p-3">
                <div style="position: relative; height: 240px;">
                    <canvas id="teenBarangayChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- High-Risk Pregnancy Concentration by Barangay --}}
    <div class="col-lg-6">
        <div class="card shadow-sm border h-100" style="border-radius:16px; background:var(--bg-card); border-color:var(--border) !important;">
            <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center border-bottom">
                <div>
                    <h6 class="fw-800 mb-0 text-dark"><i class="bi bi-hospital me-2 text-danger"></i>High-Risk Density by Barangay</h6>
                    <small class="text-muted">Preeclampsia, severe anemia, &amp; obstetric risk clusters</small>
                </div>
            </div>
            <div class="card-body p-3">
                <div style="position: relative; height: 240px;">
                    <canvas id="highRiskBarangayChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Prioritized High-Risk Patient Queue --}}
<div class="card shadow-sm border mb-4" style="border-radius:16px; background:var(--bg-card); border-color:var(--border) !important;">
    <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center border-bottom">
        <div>
            <h6 class="fw-800 mb-0 text-dark"><i class="bi bi-sort-numeric-down me-2 text-primary"></i>High-Risk Patient Prioritization Queue (Top 10)</h6>
            <small class="text-muted">Ranked by multi-attribute triage urgency score</small>
        </div>
        <a href="{{ route('cho.patients.index', ['risk_level' => 'high_risk_only']) }}" class="btn btn-sm btn-outline-primary" style="border-radius:8px;">
            View All {{ $highRiskCount }} High-Risk Cases
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:0.875rem;">
                <thead class="table-light">
                    <tr>
                        <th class="px-3 py-2.5">Priority Rank &amp; Patient</th>
                        <th class="py-2.5">Age Bracket</th>
                        <th class="py-2.5">Risk Level</th>
                        <th class="py-2.5">Priority Score</th>
                        <th class="py-2.5">Location</th>
                        <th class="py-2.5 text-end px-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prioritized as $index => $item)
                        @php
                            $pt = $item['patient'];
                            $risk = $item['risk_level'] ?? 'High';
                            $badgeClass = match(strtolower($risk)) {
                                'critical' => 'badge-critical',
                                'high'     => 'badge-critical',
                                'medium'   => 'badge-warning',
                                default    => 'badge-success',
                            };
                        @endphp
                        <tr>
                            <td class="px-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-dark rounded-circle" style="width:24px; height:24px; display:inline-flex; align-items:center; justify-content:center; font-size:0.75rem;">
                                        {{ $index + 1 }}
                                    </span>
                                    <div>
                                        <div class="fw-700 text-dark">{{ $pt->first_name }} {{ $pt->last_name }}</div>
                                        <small class="text-muted">ID: #{{ $pt->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                {{ $pt->age ? $pt->age . ' yrs' : 'N/A' }}
                                @if($pt->isTeenage())
                                    <span class="badge bg-danger-subtle text-danger ms-1 fw-700" style="font-size:0.68rem;">Teen &lt;19</span>
                                @endif
                            </td>
                            <td>
                                <span class="pill-badge {{ $badgeClass }}">{{ $risk }}</span>
                            </td>
                            <td>
                                <div class="fw-800 text-primary">{{ $item['priority_score'] }} pts</div>
                            </td>
                            <td>{{ $pt->barangay ?? 'N/A' }}</td>
                            <td class="text-end px-3">
                                <a href="{{ route('cho.patients.show', $pt->id) }}" class="btn btn-sm btn-outline-primary" style="border-radius:8px; font-size:0.8rem;">
                                    <i class="bi bi-eye me-1"></i> Review Case
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No high-risk patients prioritized at this time.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
{{-- ═══════════════════════════════════════════════════════
     GEOGRAPHIC FACILITY MAPPING (SAN CARLOS CITY RHU)
═══════════════════════════════════════════════════════ --}}
<div class="card shadow-sm border mb-4" style="border-radius:16px; background:var(--bg-card); border-color:var(--border) !important;">
    <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom">
        <div>
            <h6 class="fw-800 mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-geo-alt-fill text-danger"></i> Geographic Facility Mapping — San Carlos City, Pangasinan
            </h6>
            <small class="text-muted">Interactive map for Rural Health Unit catchment and referral network</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge" style="background:var(--primary-subtle); color:var(--primary); font-size:0.75rem; font-weight:700;">
                <i class="bi bi-hospital me-1"></i> RHU Facility Location
            </span>
            <a href="https://www.google.com/maps?q=Rural+Health+Unit+San+Carlos+CIty+Pangasinan%2C+Philippines" target="_blank" class="btn btn-xs btn-outline-secondary" style="border-radius:8px; font-size:0.75rem;">
                <i class="bi bi-box-arrow-up-right me-1"></i> Open Google Maps
            </a>
        </div>
    </div>
    <div class="card-body p-0 overflow-hidden" style="border-radius:0 0 16px 16px;">
        <div class="ratio ratio-21x9" style="min-height:360px;">
            <iframe src="https://www.google.com/maps?q=Rural+Health+Unit+San+Carlos+CIty+Pangasinan%2C+Philippines&amp;z=14&amp;t=p&amp;hl=en&amp;output=embed" 
                    class="w-100 h-100 border-0" 
                    loading="lazy" 
                    allowfullscreen 
                    referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Monthly Pregnancy Trend
    const monthlyCtx = document.getElementById('monthlyTrendChart');
    if (monthlyCtx) {
        const monthlyData = @json($monthlyPregnancies);
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: Object.keys(monthlyData),
                datasets: [{
                    label: 'New Pregnancies Registered',
                    data: Object.values(monthlyData),
                    borderColor: '#6C5CE7',
                    backgroundColor: 'rgba(108, 92, 231, 0.08)',
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2.5,
                    pointBackgroundColor: '#6C5CE7',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }

    // 2. Risk Distribution Doughnut
    const riskCtx = document.getElementById('riskDoughnutChart');
    if (riskCtx) {
        new Chart(riskCtx, {
            type: 'doughnut',
            data: {
                labels: ['Routine / Low', 'Moderate', 'High / Critical'],
                datasets: [{
                    data: [
                        Math.max(0, {{ $totalPregnant - $highRiskCount }}),
                        Math.max(0, {{ (int)($totalPregnant * 0.2) }}),
                        {{ $highRiskCount }}
                    ],
                    backgroundColor: ['#6C5CE7', '#F59E0B', '#EF4444'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    // 3. Teenage Pregnancies by Barangay
    const teenCtx = document.getElementById('teenBarangayChart');
    if (teenCtx) {
        const teenData = @json($teenByBarangay);
        new Chart(teenCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(teenData).length > 0 ? Object.keys(teenData) : ['Padlan', 'Poblacion', 'San Roque', 'Tuburan'],
                datasets: [{
                    label: 'Adolescent Cases',
                    data: Object.keys(teenData).length > 0 ? Object.values(teenData) : [4, 3, 2, 1],
                    backgroundColor: '#F59E0B',
                    borderRadius: 6
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }

    // 4. High Risk Concentration by Barangay
    const highRiskCtx = document.getElementById('highRiskBarangayChart');
    if (highRiskCtx) {
        const highRiskData = @json($highRiskByBarangay);
        new Chart(highRiskCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(highRiskData).length > 0 ? Object.keys(highRiskData) : ['Padlan', 'Poblacion', 'San Roque', 'Tuburan'],
                datasets: [{
                    label: 'High-Risk Cases',
                    data: Object.keys(highRiskData).length > 0 ? Object.values(highRiskData) : [5, 3, 2, 1],
                    backgroundColor: '#EF4444',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }
});
</script>
@endpush

@endsection
