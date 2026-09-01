@extends('midwife.layout')

@section('title', 'Women - Midwife Portal | ReproCare')

@push('styles')
<style>
    .women-toolbar { display: flex; justify-content: space-between; gap: 1rem; flex-wrap: wrap; }
    .women-search {
        display: flex; align-items: center; gap: 0.55rem;
        min-width: min(100%, 320px); padding: 0.55rem 0.8rem;
        border-radius: 14px; border: 1px solid var(--border); background: var(--bg-card2);
    }
    .women-search:focus-within { border-color: var(--primary); box-shadow: 0 0 0 3px var(--focus-ring); }
    .women-search input { flex: 1; border: 0; outline: 0; background: transparent; color: var(--text); }
    .women-filter-grid {
        display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem;
    }
    .women-filter-card {
        display: block; text-decoration: none; padding: 1.1rem 1.15rem;
        border-radius: 18px; border: 1px solid var(--border); background: var(--bg-card);
        color: var(--text); transition: transform 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease;
    }
    .women-filter-card:hover { transform: translateY(-2px); border-color: var(--primary); box-shadow: var(--shadow-sm); }
    .women-filter-card.active { border-color: var(--primary); box-shadow: 0 0 0 3px var(--focus-ring); background: linear-gradient(135deg, var(--primary-subtle), rgba(255,255,255,0.02)); }
    .women-filter-top { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 0.7rem; }
    .women-filter-label { font-weight: 700; }
    .women-filter-count { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.8rem; font-weight: 800; line-height: 1; }
    .women-filter-note { color: var(--text-muted); font-size: 0.84rem; }
    .women-meta { display: flex; gap: 0.6rem; flex-wrap: wrap; }
    .women-table-name { display: flex; align-items: center; gap: 0.8rem; }
    .women-avatar {
        width: 42px; height: 42px; border-radius: 14px; flex-shrink: 0;
        display: inline-flex; align-items: center; justify-content: center;
        color: #fff; font-weight: 800; background: linear-gradient(135deg, var(--primary), var(--accent-violet));
    }
    .women-name { font-weight: 700; color: var(--text); }
    .women-sub { color: var(--text-muted); font-size: 0.8rem; }
    .women-type-badge {
        display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.3rem 0.7rem;
        border-radius: 999px; font-size: 0.74rem; font-weight: 700;
    }
    .women-type-badge.registered { background: rgba(16,185,129,0.12); color: var(--success); }
    .women-type-badge.walk-in { background: rgba(245,158,11,0.12); color: var(--warning); }
    .women-action-btn {
        display: inline-flex; align-items: center; justify-content: center;
        width: 34px; height: 34px; border-radius: 10px; border: 1px solid var(--border);
        background: var(--bg-card2); color: var(--primary-light); text-decoration: none;
    }
    .women-action-btn:hover { background: var(--primary-subtle); border-color: var(--primary); }
    @media (max-width: 992px) {
        .women-filter-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('midwife-content')
<div class="page-hero fade-in-card">
    <div class="women-toolbar" style="position:relative;z-index:1;">
        <div>
            <h1 class="page-hero-title">
                <i class="bi bi-people-fill me-2"></i>Women
            </h1>
            <p class="page-hero-subtitle">Review registered women and walk-in records in one organized list.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <form method="GET" action="{{ route('midwife.patients') }}">
                <input type="hidden" name="filter" value="{{ request('filter', 'all') }}">
                <input type="hidden" name="purok_id" value="{{ request('purok_id') }}">
                <input type="hidden" name="pregnancy_status" value="{{ request('pregnancy_status', 'all') }}">
                <input type="hidden" name="age_range" value="{{ request('age_range', 'all') }}">
                <div class="women-search">
                    <i class="bi bi-search" style="color:var(--text-muted);"></i>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Search women...">
                    <button type="submit" class="btn btn-sm btn-link p-0 text-decoration-none" style="color:var(--primary-light);">Search</button>
                </div>
            </form>
            <a href="{{ route('midwife.patients.create') }}" class="btn btn-light">
                <i class="bi bi-person-plus-fill me-1"></i> Add Woman
            </a>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success d-flex align-items-center gap-2 mt-4 fade-in-card">
        <i class="bi bi-check-circle-fill"></i>
        <span>{{ session('success') }}</span>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card fade-in-card my-4 shadow-sm" style="border-radius:16px;">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('midwife.patients') }}" class="row g-2 align-items-end">
            <input type="hidden" name="filter" value="{{ request('filter', 'all') }}">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <div class="col-6 col-md-2">
                <label class="form-label text-xs fw-600 text-muted mb-1">Purok</label>
                <select name="purok_id" class="form-select form-select-sm">
                    <option value="">All Puroks</option>
                    @foreach($puroks as $purok)
                        <option value="{{ $purok->id }}" {{ (string) $purokId === (string) $purok->id ? 'selected' : '' }}>{{ $purok->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label text-xs fw-600 text-muted mb-1">Risk Triage</label>
                <select name="risk_level" class="form-select form-select-sm">
                    <option value="all" {{ ($riskLevel ?? 'all') === 'all' ? 'selected' : '' }}>All Risk Tiers</option>
                    <option value="high_risk_only" {{ ($riskLevel ?? '') === 'high_risk_only' ? 'selected' : '' }}>🚨 High Risk Only</option>
                    <option value="critical" {{ ($riskLevel ?? '') === 'critical' ? 'selected' : '' }}>Critical Risk</option>
                    <option value="high" {{ ($riskLevel ?? '') === 'high' ? 'selected' : '' }}>High Risk</option>
                    <option value="medium" {{ ($riskLevel ?? '') === 'medium' ? 'selected' : '' }}>Medium Risk</option>
                    <option value="low" {{ ($riskLevel ?? '') === 'low' ? 'selected' : '' }}>Low Risk</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label text-xs fw-600 text-muted mb-1">Age Tier</label>
                <select name="age_range" class="form-select form-select-sm">
                    <option value="all" {{ $ageRange === 'all' ? 'selected' : '' }}>All Ages</option>
                    <option value="teen" {{ $ageRange === 'teen' || $ageRange === 'under_20' ? 'selected' : '' }}>⚠️ Adolescent (&lt;19 yrs)</option>
                    <option value="20_34" {{ $ageRange === '20_34' ? 'selected' : '' }}>20 to 34 yrs</option>
                    <option value="35_plus" {{ $ageRange === '35_plus' ? 'selected' : '' }}>35+ yrs (Advanced)</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label text-xs fw-600 text-muted mb-1">Trimester</label>
                <select name="trimester" class="form-select form-select-sm">
                    <option value="all" {{ ($trimester ?? 'all') === 'all' ? 'selected' : '' }}>All Stages</option>
                    <option value="1" {{ ($trimester ?? '') === '1' ? 'selected' : '' }}>1st Trimester (1-13 wks)</option>
                    <option value="2" {{ ($trimester ?? '') === '2' ? 'selected' : '' }}>2nd Trimester (14-26 wks)</option>
                    <option value="3" {{ ($trimester ?? '') === '3' ? 'selected' : '' }}>3rd Trimester (27+ wks)</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label text-xs fw-600 text-muted mb-1">Pregnancy Status</label>
                <select name="pregnancy_status" class="form-select form-select-sm">
                    <option value="all" {{ $pregnancyStatus === 'all' ? 'selected' : '' }}>All Status</option>
                    <option value="pregnant" {{ $pregnancyStatus === 'pregnant' ? 'selected' : '' }}>Pregnant</option>
                    <option value="not_pregnant" {{ $pregnancyStatus === 'not_pregnant' ? 'selected' : '' }}>Not Pregnant</option>
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary flex-fill"><i class="bi bi-funnel-fill me-1"></i>Filter</button>
                <a href="{{ route('midwife.patients') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="women-filter-grid my-4 fade-in-card">
    <a href="{{ route('midwife.patients', ['filter' => 'all']) }}" class="women-filter-card {{ request('filter', 'all') === 'all' ? 'active' : '' }}">
        <div class="women-filter-top">
            <span class="women-filter-label"><i class="bi bi-people-fill me-2"></i>All Women</span>
            <span class="summary-chip chip-primary">Combined</span>
        </div>
        <div class="women-filter-count">{{ $patients->total() }}</div>
        <div class="women-filter-note">Registered women and walk-in records together.</div>
    </a>

    <a href="{{ route('midwife.patients', ['filter' => 'registered']) }}" class="women-filter-card {{ request('filter') === 'registered' ? 'active' : '' }}">
        <div class="women-filter-top">
            <span class="women-filter-label"><i class="bi bi-person-check-fill me-2"></i>Registered</span>
            <span class="summary-chip chip-success">Approved</span>
        </div>
        <div class="women-filter-count">{{ $registeredCount }}</div>
        <div class="women-filter-note">Women with registered accounts.</div>
    </a>

    <a href="{{ route('midwife.patients', ['filter' => 'unregistered']) }}" class="women-filter-card {{ request('filter') === 'unregistered' ? 'active' : '' }}">
        <div class="women-filter-top">
            <span class="women-filter-label"><i class="bi bi-person-walking me-2"></i>Walk-in</span>
            <span class="summary-chip chip-warning">Pending Account</span>
        </div>
        <div class="women-filter-count">{{ $unregisteredCount }}</div>
        <div class="women-filter-note">Walk-in women recorded without registered accounts.</div>
    </a>
</div>

<div class="women-meta mb-4 fade-in-card">
    <span class="summary-chip chip-success"><i class="bi bi-heart-pulse"></i> Scheduled Checkups: {{ $scheduledCheckups }}</span>
    <span class="summary-chip chip-danger"><i class="bi bi-exclamation-circle"></i> Missed: {{ $missedCheckups }}</span>
    @if(request('search'))
        <span class="summary-chip chip-warning">
            <i class="bi bi-search"></i> Results for "{{ request('search') }}"
            <a href="{{ route('midwife.patients', ['filter' => request('filter', 'all')]) }}" style="color:inherit;"><i class="bi bi-x-circle"></i></a>
        </span>
    @endif
</div>

<div class="table-card fade-in-card">
    @if($patients->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover table-sticky-head mb-0">
                <thead>
                    <tr>
                        <th>Woman</th>
                        <th>Type</th>
                        <th>Contact Number</th>
                        <th>Purok / Barangay</th>
                        <th>Pregnancy &amp; Risk Level</th>
                        <th class="text-end px-3">Clinical Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($patients as $patient)
                        @php
                            $isWalkIn = $patient->type === 'unregistered';
                            $name = $isWalkIn ? $patient->full_name : $patient->name;
                            $contact = $isWalkIn ? ($patient->contact_number ?? null) : ($patient->contact_number ?? $patient->phone ?? null);
                            $activePreg = $patient->pregnancies?->first();
                            $latestRecord = $patient->healthRecords?->first();
                            $risk = strtolower($activePreg?->risk_level ?? $latestRecord?->risk_level ?? 'low');
                            $isTeen = method_exists($patient, 'isTeenage') ? $patient->isTeenage() : ($patient->age !== null && $patient->age < 19);
                            $badgeClass = match($risk) {
                                'critical', 'high' => 'badge-critical',
                                'medium'           => 'badge-warning',
                                default            => 'badge-success',
                            };
                        @endphp
                        <tr>
                            <td>
                                <div class="women-table-name">
                                    <div class="women-avatar">{{ strtoupper(substr($name, 0, 1)) }}</div>
                                    <div>
                                        <div class="women-name">
                                            {{ $name }}
                                            @if($isTeen)
                                                <span class="badge" style="background:var(--badge-critical-bg); color:var(--badge-critical-text); font-size:0.68rem; font-weight:700;">
                                                    Teen &lt;19
                                                </span>
                                            @endif
                                        </div>
                                        <div class="women-sub">Age: {{ $patient->age ? $patient->age . ' yrs' : 'N/A' }} · ID: #{{ $patient->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="women-type-badge {{ $isWalkIn ? 'walk-in' : 'registered' }}">
                                    <i class="bi {{ $isWalkIn ? 'bi-person-walking' : 'bi-person-check-fill' }}"></i>
                                    {{ $isWalkIn ? 'Walk-in' : 'Registered' }}
                                </span>
                            </td>
                            <td>{{ $contact ?: '—' }}</td>
                            <td>
                                <div class="fw-600">{{ $patient->purok?->name ?? '—' }}</div>
                                <div class="women-sub">{{ $patient->barangay ?? 'Central' }}</div>
                            </td>
                            <td>
                                @if($activePreg)
                                    <div>
                                        <span class="pill-badge {{ $badgeClass }}">{{ ucfirst($risk) }} Risk</span>
                                    </div>
                                    <div class="text-muted text-xs mt-1">
                                        🤰 {{ $activePreg->aog_weeks ?? 0 }} wks AOG (EDD: {{ $activePreg->expected_delivery_date ? \Carbon\Carbon::parse($activePreg->expected_delivery_date)->format('M d') : 'N/A' }})
                                    </div>
                                @else
                                    <span class="women-sub">Non-pregnant / Postpartum</span>
                                @endif
                            </td>
                            <td class="text-end px-3">
                                <div class="d-inline-flex align-items-center gap-1.5">
                                    {{-- Video Consultation Helper --}}
                                    <a href="{{ route('learning.index', ['type' => 'video']) }}" 
                                       class="btn btn-sm btn-light border text-danger" 
                                       title="Stream Patient Counseling Video"
                                       style="border-radius:8px; padding:0.35rem 0.6rem; font-size:0.8rem; font-weight:600;">
                                        <i class="bi bi-play-circle-fill"></i> Stream
                                    </a>

                                    {{-- View Profile / Case --}}
                                    <a href="{{ $isWalkIn ? route('midwife.walk-in-patients.show', $patient->id) : route('midwife.patient-details', $patient->id) }}"
                                       class="btn btn-sm btn-primary"
                                       style="border-radius:8px; padding:0.35rem 0.65rem; font-size:0.8rem; font-weight:600;"
                                       title="Review Patient Record">
                                        <i class="bi bi-eye-fill me-1"></i> Review
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center" style="padding:1.25rem;">
            {{ $patients->links('pagination::bootstrap-5') }}
        </div>
    @else
        <div class="empty-state">
            <i class="bi bi-people empty-state-icon"></i>
            <h6>No Women Found</h6>
            <p>
                @if(request('search'))
                    No women match "{{ request('search') }}".
                    <a href="{{ route('midwife.patients', ['filter' => request('filter', 'all')]) }}">Clear search</a>
                @else
                    No {{ request('filter') === 'registered' ? 'registered' : (request('filter') === 'unregistered' ? 'walk-in' : '') }} women found.
                @endif
            </p>
            <a href="{{ route('midwife.patients.create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus-fill me-1"></i> Add First Woman
            </a>
        </div>
    @endif
</div>
@endsection
