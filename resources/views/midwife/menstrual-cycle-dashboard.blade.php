@extends('midwife.layout')

@section('title', 'Menstrual Cycle Dashboard - ReproCare')

@section('midwife-content')

{{-- ═══════════════════════════════
     PAGE HERO
═══════════════════════════════ --}}
<div class="page-hero fade-in-card mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3" style="position:relative;z-index:1;">
        <div>
            <div class="page-hero-title">
                <i class="bi bi-graph-up-arrow me-2"></i>Menstrual Cycle Predictions
            </div>
            <p class="page-hero-subtitle">
                <i class="bi bi-calendar3 me-1"></i>{{ now()->format('l, F j, Y') }}
                &nbsp;·&nbsp; Overview of patient menstrual cycle predictions and fertility windows
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('midwife.dashboard') }}" class="btn-hero-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>
</div>

    {{-- ═══════════════════════════════
         SUMMARY STATS
    ════════════════════════════════ --}}
    <div class="row g-3 mb-4">
        <div class="col">
            <div class="stat-card stat-purple fade-in-card">
                <i class="bi bi-people-fill stat-icon"></i>
                <div class="stat-label">Patients with Data</div>
                <div class="stat-number" data-count="{{ count($womanPredictions) }}">{{ count($womanPredictions) }}</div>
            </div>
        </div>
        <div class="col">
            <div class="stat-card stat-rose fade-in-card">
                <i class="bi bi-calendar-heart stat-icon"></i>
                <div class="stat-label">Period in 7 Days</div>
                <div class="stat-number" data-count="{{ collect($womanPredictions)->where('days_until_period', '<=', 7)->count() }}">
                    {{ collect($womanPredictions)->where('days_until_period', '<=', 7)->count() }}
                </div>
            </div>
        </div>
        <div class="col">
            <div class="stat-card stat-green fade-in-card">
                <i class="bi bi-flower1 stat-icon"></i>
                <div class="stat-label">In Fertile Window</div>
                <div class="stat-number" data-count="{{ collect($womanPredictions)->where('days_until_period', '>=', 14)->where('days_until_period', '<=', 21)->count() }}">
                    {{ collect($womanPredictions)->where('days_until_period', '>=', 14)->where('days_until_period', '<=', 21)->count() }}
                </div>
            </div>
        </div>
        <div class="col">
            <div class="stat-card stat-blue fade-in-card" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                <i class="bi bi-calendar3 stat-icon"></i>
                <div class="stat-label">Avg Cycle Length</div>
                <div class="stat-number">
                    {{ collect($womanPredictions)->avg('average_cycle') ? round(collect($womanPredictions)->avg('average_cycle')) : 'N/A' }}
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════
         PATIENT PREDICTIONS TABLE
    ════════════════════════════════ --}}
    <div class="card fade-in-card" style="border:none; background:var(--bg-card);">
        <div class="card-header d-flex justify-content-between align-items-center" 
             style="background:transparent; border-bottom:1px solid var(--border-color); padding:1rem 1.5rem;">
            <h5 class="mb-0 fw-700" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--text);">
                <i class="bi bi-calendar-check-fill me-2" style="color:var(--primary);"></i>
                Patient Predictions
            </h5>
            <span style="background:rgba(155,54,255,0.1); color:var(--primary); padding:0.3rem 0.8rem; border-radius:20px; font-size:0.8rem; font-weight:500;">
                {{ count($womanPredictions) }} Patients
            </span>
        </div>
        <div class="card-body p-0">
            @if(count($womanPredictions) > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size:0.9rem;">
                        <thead>
                            <tr style="color:var(--text-muted); font-weight:600; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.5px; background:transparent;">
                                <th style="padding:1rem 1.5rem; border:none;">Patient Name</th>
                                <th style="padding:1rem; border:none;">Avg Cycle</th>
                                <th style="padding:1rem; border:none;">Next Period</th>
                                <th style="padding:1rem; border:none;">Days Until</th>
                                <th style="padding:1rem; border:none;">Ovulation</th>
                                <th style="padding:1rem; border:none;">Fertile Window</th>
                                <th style="padding:1rem; border:none;">Status</th>
                                <th style="padding:1rem 1.5rem; border:none; text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($womanPredictions as $prediction)
                                <tr style="border-bottom:1px solid var(--border-color);">
                                    <td style="padding:1rem 1.5rem;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,var(--primary),var(--accent-violet));display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                                <span style="font-weight:700;font-size:0.75rem;color:#fff;">{{ strtoupper(substr($prediction['woman']->name, 0, 1)) }}</span>
                                            </div>
                                            <div style="font-weight:600; color:var(--text);">{{ $prediction['woman']->name }}</div>
                                        </div>
                                    </td>
                                    <td style="padding:1rem; color:var(--text);">{{ $prediction['average_cycle'] }} days</td>
                                    <td style="padding:1rem; color:var(--text);">{{ $prediction['next_period']->format('M d, Y') }}</td>
                                    <td style="padding:1rem;">
                                        @if($prediction['days_until_period'] <= 7)
                                            <div style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.25rem 0.75rem; background:rgba(220,53,69,0.1); color:var(--danger); border-radius:20px; font-weight:500; font-size:0.8rem;">
                                                <i class="bi bi-calendar-event"></i> {{ round($prediction['days_until_period']) }} days
                                            </div>
                                        @elseif($prediction['days_until_period'] <= 14)
                                            <div style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.25rem 0.75rem; background:rgba(245,158,11,0.1); color:var(--warning); border-radius:20px; font-weight:500; font-size:0.8rem;">
                                                <i class="bi bi-clock"></i> {{ round($prediction['days_until_period']) }} days
                                            </div>
                                        @else
                                            <div style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.25rem 0.75rem; background:rgba(25,135,84,0.1); color:var(--success); border-radius:20px; font-weight:500; font-size:0.8rem;">
                                                <i class="bi bi-check-circle"></i> {{ round($prediction['days_until_period']) }} days
                                            </div>
                                        @endif
                                    </td>
                                    <td style="padding:1rem; color:var(--text-muted);">{{ $prediction['ovulation']->format('M d') }}</td>
                                    <td style="padding:1rem; color:var(--text-muted);">
                                        {{ $prediction['fertile_window']['start']->format('M d') }} – {{ $prediction['fertile_window']['end']->format('M d') }}
                                    </td>
                                    <td style="padding:1rem;">
                                        @if($prediction['days_until_period'] <= 7)
                                            <div style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.25rem 0.75rem; background:rgba(220,53,69,0.1); color:var(--danger); border-radius:20px; font-weight:500; font-size:0.8rem;">
                                                <i class="bi bi-exclamation-circle"></i> Period Soon
                                            </div>
                                        @elseif($prediction['days_until_period'] >= 14 && $prediction['days_until_period'] <= 21)
                                            <div style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.25rem 0.75rem; background:rgba(25,135,84,0.1); color:var(--success); border-radius:20px; font-weight:500; font-size:0.8rem;">
                                                <i class="bi bi-flower1"></i> Fertile
                                            </div>
                                        @else
                                            <div style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.25rem 0.75rem; background:rgba(6,182,212,0.1); color:var(--info); border-radius:20px; font-weight:500; font-size:0.8rem;">
                                                <i class="bi bi-check-circle"></i> Normal
                                            </div>
                                        @endif
                                    </td>
                                    <td style="padding:1rem 1.5rem; text-align:right;">
                                        <a href="{{ route('midwife.patient-details', $prediction['woman']->id) }}" 
                                           class="btn btn-sm" 
                                           style="background:linear-gradient(135deg,var(--primary),var(--accent-violet)); color:#fff; border:none; border-radius:8px; padding:0.4rem 1rem;">
                                            <i class="bi bi-eye me-1"></i>View Details
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="card-body p-5">
                    <div class="text-center py-5">
                        <div style="width:80px;height:80px;border-radius:20px;background:linear-gradient(135deg,var(--info),var(--primary));display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;">
                            <i class="bi bi-calendar-x" style="font-size:2rem;color:#fff;"></i>
                        </div>
                        <h5 style="font-weight:600; color:var(--text); margin-bottom:0.75rem;">No Predictions Available</h5>
                        <p style="color:var(--text-muted); margin-bottom:1.5rem; max-width:400px; margin-left:auto; margin-right:auto;">
                            No patients have sufficient menstrual cycle data for predictions.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════
         STATUS LEGEND
    ════════════════════════════════ --}}
    <div class="card fade-in-card mt-4" style="border:none; background:var(--bg-card);">
        <div class="card-header" style="background:transparent; border-bottom:1px solid var(--border-color); padding:1rem 1.5rem;">
            <h6 class="mb-0 fw-600" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--text);">
                <i class="bi bi-info-circle-fill me-2" style="color:var(--info);"></i>Status Legend
            </h6>
        </div>
        <div class="card-body" style="padding:1.25rem 1.5rem;">
            <div class="d-flex flex-wrap gap-4">
                <div class="d-flex align-items-center gap-2">
                    <div style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.25rem 0.75rem; background:rgba(220,53,69,0.1); color:var(--danger); border-radius:20px; font-weight:500; font-size:0.8rem;">
                        <i class="bi bi-exclamation-circle"></i> Period Soon
                    </div>
                    <span style="color:var(--text-muted); font-size:0.85rem;">Period expected within 7 days</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.25rem 0.75rem; background:rgba(25,135,84,0.1); color:var(--success); border-radius:20px; font-weight:500; font-size:0.8rem;">
                        <i class="bi bi-flower1"></i> Fertile
                    </div>
                    <span style="color:var(--text-muted); font-size:0.85rem;">Currently in fertile window</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.25rem 0.75rem; background:rgba(6,182,212,0.1); color:var(--info); border-radius:20px; font-weight:500; font-size:0.8rem;">
                        <i class="bi bi-check-circle"></i> Normal
                    </div>
                    <span style="color:var(--text-muted); font-size:0.85rem;">Outside critical windows</span>
                </div>
            </div>
        </div>
    </div>
@endsection
