@extends('cho.layout')

@section('title', 'Patient Prioritization & AI Triage - CHO | ReproCare')

@section('cho-content')

{{-- Header Banner --}}
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="fw-800 mb-1" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--text); letter-spacing:-0.5px;">
            <i class="bi bi-shield-shaded me-2 text-primary"></i>Decision Support &amp; Patient Prioritization
        </h2>
        <p class="text-muted mb-0" style="font-size:0.9rem;">
            Surveillance, automated risk triage, and dynamic clinical suggestions across all reproductive-age patients.
        </p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge px-3 py-2" style="background:var(--primary-subtle); color:var(--primary); font-size:0.82rem; font-weight:700; border-radius:12px;">
            <i class="bi bi-person-lines-fill me-1"></i> {{ $patients->total() }} Active Cases
        </span>
        <a href="{{ route('cho.archived.index') }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" style="border-radius:10px;">
            <i class="bi bi-archive"></i> Archived Hub
        </a>
    </div>
</div>

{{-- 1. Multi-Filter Toolbar --}}
<div class="card shadow-sm border mb-4" style="border-radius:16px; background:var(--bg-card); border-color:var(--border) !important;">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('cho.patients.index') }}" class="row g-2 align-items-end">
            {{-- Search Bar --}}
            <div class="col-md-3">
                <label class="form-label text-xs fw-700 text-muted mb-1 text-uppercase" style="letter-spacing:0.5px;">Search Patient</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control form-control-sm border-start-0 bg-light" 
                           placeholder="Name, ID, Contact..." value="{{ $search }}">
                </div>
            </div>

            {{-- Risk Level Filter --}}
            <div class="col-6 col-md-2">
                <label class="form-label text-xs fw-700 text-muted mb-1 text-uppercase" style="letter-spacing:0.5px;">Risk Urgency</label>
                <select name="risk_level" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
                    <option value="">All Risk Tiers</option>
                    <option value="high_risk_only" {{ $riskLevel === 'high_risk_only' ? 'selected' : '' }}>🚨 High Risk Only</option>
                    <option value="critical" {{ $riskLevel === 'critical' ? 'selected' : '' }}>Critical Priority</option>
                    <option value="high" {{ $riskLevel === 'high' ? 'selected' : '' }}>High Risk</option>
                    <option value="medium" {{ $riskLevel === 'medium' ? 'selected' : '' }}>Moderate Risk</option>
                    <option value="low" {{ $riskLevel === 'low' ? 'selected' : '' }}>Low / Routine</option>
                </select>
            </div>

            {{-- Age Bracket Filter --}}
            <div class="col-6 col-md-2">
                <label class="form-label text-xs fw-700 text-muted mb-1 text-uppercase" style="letter-spacing:0.5px;">Age Bracket</label>
                <select name="age_group" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
                    <option value="">All Age Brackets</option>
                    <option value="teen" {{ $ageGroup === 'teen' ? 'selected' : '' }}>⚠️ &lt;19 Teen (Adolescent)</option>
                    <option value="adult" {{ $ageGroup === 'adult' ? 'selected' : '' }}>20 - 34 Adult</option>
                    <option value="advanced" {{ $ageGroup === 'advanced' ? 'selected' : '' }}>35+ Advanced Age</option>
                </select>
            </div>

            {{-- Trimester Filter --}}
            <div class="col-6 col-md-2">
                <label class="form-label text-xs fw-700 text-muted mb-1 text-uppercase" style="letter-spacing:0.5px;">Gestational Stage</label>
                <select name="trimester" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
                    <option value="">All Trimesters</option>
                    <option value="1" {{ $trimester == '1' ? 'selected' : '' }}>1st Trimester (1-13 wks)</option>
                    <option value="2" {{ $trimester == '2' ? 'selected' : '' }}>2nd Trimester (14-26 wks)</option>
                    <option value="3" {{ $trimester == '3' ? 'selected' : '' }}>3rd Trimester (27+ wks)</option>
                </select>
            </div>

            {{-- Barangay Filter --}}
            <div class="col-6 col-md-2">
                <label class="form-label text-xs fw-700 text-muted mb-1 text-uppercase" style="letter-spacing:0.5px;">Barangay</label>
                <select name="barangay" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
                    <option value="">All Locations</option>
                    @foreach($barangays as $b)
                        <option value="{{ $b }}" {{ $barangay === $b ? 'selected' : '' }}>{{ $b }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter & Reset Actions --}}
            <div class="col-12 col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary w-100" title="Apply Filter" style="border-radius:8px;">
                    <i class="bi bi-funnel-fill"></i>
                </button>
                <a href="{{ route('cho.patients.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filters" style="border-radius:8px;">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>

            {{-- Show Archived Data Toggle --}}
            <div class="col-12 mt-2 pt-2 border-top d-flex justify-content-between align-items-center">
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" name="show_archived" value="1" id="showArchivedSwitch" 
                           {{ !empty($showArchived) ? 'checked' : '' }} onchange="this.form.submit()">
                    <label class="form-check-label text-xs text-muted fw-600" for="showArchivedSwitch">
                        Show Soft-Archived Records
                    </label>
                </div>
                <div class="text-xs text-muted">
                    Showing {{ $patients->count() }} of {{ $patients->total() }} results
                </div>
            </div>
        </form>
    </div>
</div>

{{-- 2. Dynamic Decision Support Priority Queue --}}
<div class="row g-3">
    @forelse($patients as $patient)
        @php
            $activePreg = $patient->pregnancies->first();
            $latestRecord = $patient->healthRecords->first();
            $currentRisk = strtolower($activePreg?->risk_level ?? $latestRecord?->risk_level ?? 'low');
            $isTeen = $patient->isTeenage();

            // Priority Signal Badge
            $pillClass = match($currentRisk) {
                'critical' => 'badge-critical',
                'high'     => 'badge-critical',
                'medium'   => 'badge-warning',
                default    => 'badge-success',
            };
            $pillText = match($currentRisk) {
                'critical' => '🚨 Critical Risk',
                'high'     => '⚠️ High Risk',
                'medium'   => 'Moderate Risk',
                default    => 'Routine / Low Risk',
            };

            // AI Smart Suggestion Generation
            $bp = $latestRecord?->bp;
            $hb = $latestRecord?->hemoglobin;
            $aiSuggestion = null;
            $aiIcon = 'bi-robot';

            if ($currentRisk === 'critical' || $currentRisk === 'high') {
                if ($bp && (int)explode('/', $bp)[0] >= 140) {
                    $aiSuggestion = "⚡ Flagged: High BP Trend ({$bp}) — Schedule Immediate Ultrasound & Consult OB";
                    $aiIcon = 'bi-lightning-charge-fill text-danger';
                } elseif ($hb && (float)$hb < 10.0) {
                    $aiSuggestion = "🩸 Flagged: Anemia Detected ({$hb} g/dL) — Prescribe Oral Iron & Nutrition Counseling";
                    $aiIcon = 'bi-droplet-fill text-danger';
                } elseif ($isTeen) {
                    $aiSuggestion = "⚠️ Adolescent Care Protocol — Enforce Bi-Weekly Midwife Surveillance";
                    $aiIcon = 'bi-exclamation-triangle-fill text-warning';
                } else {
                    $aiSuggestion = "📋 High Risk Triage — Prioritize for Facility-Based Birth Plan & Tertiary Referral";
                    $aiIcon = 'bi-clipboard2-pulse-fill text-primary';
                }
            } elseif ($isTeen) {
                $aiSuggestion = "👩‍⚕️ Adolescent Pregnancy — Provide Youth-Friendly Antenatal Guidance";
                $aiIcon = 'bi-info-circle-fill text-warning';
            } else {
                $aiSuggestion = "✅ Stable Parameters — Maintain Standard 4+ ANC Visit Schedule";
                $aiIcon = 'bi-check-circle-fill text-success';
            }
        @endphp

        <div class="col-12">
            <div class="card shadow-sm border patient-priority-card position-relative" 
                 style="border-radius:16px; border-color:var(--border) !important; background:var(--bg-card); transition:transform 0.2s ease, box-shadow 0.2s ease;">
                <div class="card-body p-3 p-md-4">
                    <div class="row align-items-center g-3">
                        {{-- Patient Identity & Avatar --}}
                        <div class="col-md-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-800 text-white flex-shrink-0" 
                                     style="width:46px; height:46px; background:linear-gradient(135deg, #6C5CE7, #5E35B1); font-size:0.95rem;">
                                    {{ substr($patient->first_name, 0, 1) }}{{ substr($patient->last_name, 0, 1) }}
                                </div>
                                <div>
                                    <h6 class="fw-800 mb-0 text-dark" style="font-size:0.98rem;">
                                        {{ $patient->first_name }} {{ $patient->last_name }}
                                    </h6>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <span class="text-xs text-muted">ID: #{{ $patient->id }}</span>
                                        <span class="text-xs text-muted">•</span>
                                        <span class="text-xs text-muted"><i class="bi bi-geo-alt me-0.5"></i>{{ $patient->barangay ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Clinical Demographics & Gestational Age --}}
                        <div class="col-6 col-md-2">
                            <div class="text-xs text-muted text-uppercase fw-700" style="letter-spacing:0.5px;">Demographics</div>
                            <div class="fw-700 text-dark mt-1" style="font-size:0.9rem;">
                                {{ $patient->age ? $patient->age . ' yrs' : 'N/A' }}
                                @if($isTeen)
                                    <span class="badge" style="background:var(--badge-critical-bg); color:var(--badge-critical-text); font-size:0.68rem; font-weight:700;">
                                        Teen &lt;19
                                    </span>
                                @endif
                            </div>
                            <div class="text-xs text-muted mt-0.5">
                                @if($activePreg)
                                    <span class="text-primary fw-600">🤰 {{ $activePreg->aog_weeks ?? 0 }} wks AOG</span>
                                @else
                                    <span>Postpartum / Non-preg</span>
                                @endif
                            </div>
                        </div>

                        {{-- Key Physiological Metrics (BP, Hb, Weight) --}}
                        <div class="col-6 col-md-2">
                            <div class="text-xs text-muted text-uppercase fw-700" style="letter-spacing:0.5px;">Vitals &amp; Labs</div>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <span class="badge bg-light text-dark border px-2 py-1" style="font-size:0.78rem;">
                                    BP: <strong>{{ $latestRecord?->bp ?? '110/70' }}</strong>
                                </span>
                                <span class="badge bg-light text-dark border px-2 py-1" style="font-size:0.78rem;">
                                    Hb: <strong>{{ $latestRecord?->hemoglobin ? $latestRecord->hemoglobin . ' g/dL' : '12.1' }}</strong>
                                </span>
                            </div>
                            <div class="text-xs text-muted mt-1">
                                Risk: <span class="pill-badge {{ $pillClass }}">{{ $pillText }}</span>
                            </div>
                        </div>

                        {{-- AI Smart Suggestion Badge --}}
                        <div class="col-md-3">
                            <div class="p-2.5 rounded-3 d-flex align-items-start gap-2" 
                                 style="background:var(--bg-card2); border:1px solid var(--border); font-size:0.8rem; line-height:1.4;">
                                <i class="bi {{ $aiIcon }} fs-6 flex-shrink-0 mt-0.5"></i>
                                <div>
                                    <span class="fw-700 text-dark">AI Decision Support:</span><br>
                                    <span class="text-muted">{{ $aiSuggestion }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- 3. Action Bar (View Details, Stream Video, Archive Record - NO DELETE) --}}
                        <div class="col-md-2 text-md-end">
                            <div class="d-flex align-items-center justify-content-md-end gap-1.5 flex-wrap">
                                {{-- View Details --}}
                                <a href="{{ route('cho.patients.show', $patient->id) }}" 
                                   class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1"
                                   style="border-radius:10px; font-weight:600; font-size:0.82rem; padding:0.4rem 0.75rem;">
                                    <i class="bi bi-eye-fill"></i> View Details
                                </a>

                                {{-- Stream Consultation Video --}}
                                <a href="{{ route('learning.index', ['type' => 'video']) }}" 
                                   class="btn btn-sm btn-light border text-danger d-inline-flex align-items-center gap-1"
                                   title="Stream Educational / Counseling Video"
                                   style="border-radius:10px; font-weight:600; font-size:0.82rem; padding:0.4rem 0.6rem;">
                                    <i class="bi bi-play-circle-fill"></i> Stream
                                </a>

                                {{-- Archive Record (Soft Delete Only - NO HARD DELETE) --}}
                                @if(!$patient->trashed())
                                    <button type="button" 
                                            class="btn btn-sm btn-light border text-secondary d-inline-flex align-items-center"
                                            title="Archive Patient Record"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#archiveModal{{ $patient->id }}"
                                            style="border-radius:10px; padding:0.4rem 0.6rem;">
                                        <i class="bi bi-archive-fill"></i>
                                    </button>

                                    {{-- Archive Confirmation Modal --}}
                                    <div class="modal fade" id="archiveModal{{ $patient->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-sm">
                                            <div class="modal-content border-0 shadow" style="border-radius:18px;">
                                                <div class="modal-body text-center p-4">
                                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3 text-warning" 
                                                         style="width:54px; height:54px; background:rgba(245,158,11,0.12);">
                                                        <i class="bi bi-archive fs-3"></i>
                                                    </div>
                                                    <h5 class="fw-800 text-dark mb-1">Archive Record?</h5>
                                                    <p class="text-muted text-xs mb-4">
                                                        Patient <strong>{{ $patient->name }}</strong> will be safely moved to Archived Records. You can restore this case anytime with 1-click.
                                                    </p>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-light w-50" data-bs-dismiss="modal" style="border-radius:10px;">Cancel</button>
                                                        <form action="{{ route('cho.patients.archive', $patient->id) }}" method="POST" class="w-50">
                                                            @csrf
                                                            <button type="submit" class="btn btn-warning w-100 text-white fw-700" style="border-radius:10px;">
                                                                Archive
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="badge bg-secondary text-xs">Archived</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="card shadow-sm border p-5" style="border-radius:16px; background:var(--bg-card);">
                <i class="bi bi-search text-muted mb-3" style="font-size:2.5rem;"></i>
                <h5 class="fw-700 text-dark">No Patients Match Filter</h5>
                <p class="text-muted text-xs mb-3">Adjust your risk level, age group, or location criteria to view cases.</p>
                <div>
                    <a href="{{ route('cho.patients.index') }}" class="btn btn-sm btn-outline-primary" style="border-radius:10px;">
                        Reset All Filters
                    </a>
                </div>
            </div>
        </div>
    @endforelse
</div>

{{-- Pagination --}}
@if($patients->hasPages())
    <div class="mt-4 d-flex justify-content-center">
        {{ $patients->links() }}
    </div>
@endif

@push('styles')
<style>
    .patient-priority-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px -4px rgba(108, 92, 231, 0.12) !important;
        border-color: var(--primary) !important;
    }
    .pill-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.25rem 0.65rem;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }
    .badge-critical {
        background: var(--badge-critical-bg);
        color: var(--badge-critical-text);
    }
    .badge-warning {
        background: var(--badge-warning-bg);
        color: var(--badge-warning-text);
    }
    .badge-success {
        background: var(--badge-success-bg);
        color: var(--badge-success-text);
    }
</style>
@endpush

@endsection
