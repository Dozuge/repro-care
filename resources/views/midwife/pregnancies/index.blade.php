@extends('midwife.layout')

@section('title', 'Pregnancies - ReproCare')

@section('midwife-content')

{{-- ═══════════════════════════════
     PAGE HERO
═══════════════════════════════ --}}
<div class="page-hero fade-in-card mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3" style="position:relative;z-index:1;">
        <div>
            <div class="page-hero-title">
                <i class="bi bi-heart-fill me-2"></i>Pregnancy Tracking
            </div>
            <p class="page-hero-subtitle">
                <i class="bi bi-calendar3 me-1"></i>{{ now()->format('l, F j, Y') }}
                &nbsp;·&nbsp; Manage all pregnancy records
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('midwife.pregnant-patients') }}" class="btn" 
               style="background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.3); color:#fff; border-radius:12px; padding:0.6rem 1.25rem; font-weight:500; backdrop-filter:blur(10px); display:flex; align-items:center; gap:0.5rem;">
                <i class="bi bi-heart-pulse-fill"></i> Pregnant Patients
            </a>
            <a href="{{ route('midwife.pregnancies.create') }}" class="btn" 
               style="background:#fff; color:var(--primary); border-radius:12px; padding:0.6rem 1.25rem; font-weight:600; display:flex; align-items:center; gap:0.5rem; box-shadow:0 4px 15px rgba(0,0,0,0.1);">
                <i class="bi bi-plus-circle-fill"></i> Add Pregnancy
            </a>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════
     STAT CARDS
═══════════════════════════════ --}}
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card stat-purple fade-in-card">
            <div class="stat-icon"><i class="bi bi-heart-fill"></i></div>
            <div class="stat-label">Total Pregnancies</div>
            <div class="stat-number" data-count="{{ $pregnancies->total() }}">0</div>
            <div class="stat-trend">
                <i class="bi bi-heart"></i> All records
            </div>
            <canvas id="sparkline1" width="80" height="36" style="position:absolute;bottom:1rem;right:1rem;opacity:0.5;"></canvas>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stat-card stat-cyan fade-in-card">
            <div class="stat-icon"><i class="bi bi-heart-pulse-fill"></i></div>
            <div class="stat-label">Active</div>
            <div class="stat-number" data-count="{{ $pregnancies->where('status', 'active')->count() }}">0</div>
            <div class="stat-trend">
                <i class="bi bi-heart"></i> Currently monitoring
            </div>
            <canvas id="sparkline2" width="80" height="36" style="position:absolute;bottom:1rem;right:1rem;opacity:0.5;"></canvas>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stat-card stat-rose fade-in-card">
            <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
            <div class="stat-label">Completed</div>
            <div class="stat-number" data-count="{{ $pregnancies->where('status', 'completed')->count() }}">0</div>
            <div class="stat-trend">
                <i class="bi bi-check2-all"></i> Delivered
            </div>
            <canvas id="sparkline3" width="80" height="36" style="position:absolute;bottom:1rem;right:1rem;opacity:0.5;"></canvas>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stat-card stat-amber fade-in-card">
            <div class="stat-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <div class="stat-label">High Risk</div>
            <div class="stat-number" data-count="{{ $pregnancies->where('risk_level', 'High')->count() }}">0</div>
            <div class="stat-trend danger">
                <i class="bi bi-arrow-up-short"></i> Requires attention
            </div>
            <canvas id="sparkline4" width="80" height="36" style="position:absolute;bottom:1rem;right:1rem;opacity:0.5;"></canvas>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════
     SEARCH BAR
═══════════════════════════════ --}}
<div class="card fade-in-card mb-4" style="border:none; background:var(--bg-card);">
    <div class="card-body p-3">
        <form action="{{ route('midwife.pregnancies.index') }}" method="GET" class="d-flex gap-2 align-items-center flex-wrap">
            <div class="position-relative" style="width:320px;">
                <i class="bi bi-search position-absolute" 
                   style="left:1rem; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:1rem; z-index:10;"></i>
                <input type="text" name="search" class="form-control" 
                       style="background:var(--bg-input); border:1px solid var(--border-color); color:var(--text); border-radius:12px; padding:0.65rem 1rem 0.65rem 2.5rem; font-size:0.9rem;"
                       placeholder="Search by patient name..." value="{{ request('search') }}">
            </div>
            <select name="trimester" class="form-select" style="width:180px; border-radius:12px;">
                <option value="all" {{ $trimester === 'all' ? 'selected' : '' }}>All Trimesters</option>
                <option value="1" {{ $trimester === '1' ? 'selected' : '' }}>1st Trimester</option>
                <option value="2" {{ $trimester === '2' ? 'selected' : '' }}>2nd Trimester</option>
                <option value="3" {{ $trimester === '3' ? 'selected' : '' }}>3rd Trimester</option>
            </select>
            <select name="risk_level" class="form-select" style="width:160px; border-radius:12px;">
                <option value="all" {{ $riskLevel === 'all' ? 'selected' : '' }}>All Risk Levels</option>
                <option value="Low" {{ $riskLevel === 'Low' ? 'selected' : '' }}>Low Risk</option>
                <option value="Medium" {{ $riskLevel === 'Medium' ? 'selected' : '' }}>Medium Risk</option>
                <option value="High" {{ $riskLevel === 'High' ? 'selected' : '' }}>High Risk</option>
            </select>
            <button type="submit" class="btn btn-primary" style="border-radius:10px; padding:0.6rem 1rem; white-space:nowrap;">
                <i class="bi bi-funnel"></i> Apply
            </button>
            @if(request('search') || request('trimester', 'all') !== 'all' || request('risk_level', 'all') !== 'all')
                <a href="{{ route('midwife.pregnancies.index') }}" class="btn btn-outline-secondary" style="border-radius:10px; padding:0.6rem 1rem; white-space:nowrap;">
                    <i class="bi bi-x-lg"></i> Clear
                </a>
            @endif
        </form>
    </div>
</div>

{{-- ═══════════════════════════════
     PREGNANCY RECORDS TABLE
═══════════════════════════════ --}}
<div class="card fade-in-card" style="border:none; background:var(--bg-card);">
    <div class="card-header d-flex justify-content-between align-items-center" 
         style="background:transparent; border-bottom:1px solid var(--border-color); padding:1rem 1.5rem;">
        <h5 class="mb-0 fw-700" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--text);">
            <i class="bi bi-heart-fill me-2" style="color:var(--primary-light);"></i>
            Pregnancy Records
        </h5>
    </div>
    
    <div class="card-body p-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" 
                 style="background:rgba(25,135,84,0.1); border:1px solid rgba(25,135,84,0.3); color:var(--success); border-radius:10px;">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" style="filter:invert(1);"></button>
            </div>
        @endif

        @if($pregnancies->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle" style="font-size:0.9rem;">
                    <thead>
                        <tr style="color:var(--text-muted); font-weight:600; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.5px;">
                            <th style="padding:1rem; border:none;">Patient</th>
                            <th style="padding:1rem; border:none;">Gestational Age</th>
                            <th style="padding:1rem; border:none;">Trimester</th>
                            <th style="padding:1rem; border:none;">LMP</th>
                            <th style="padding:1rem; border:none;">EDD</th>
                            <th style="padding:1rem; border:none;">Status</th>
                            <th style="padding:1rem; border:none;">Risk</th>
                            <th style="padding:1rem; border:none; text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pregnancies as $pregnancy)
                            <tr style="border-bottom:1px solid var(--border-color);">
                                <td style="padding:1rem;">
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="width:42px;height:42px;border-radius:12px;background:linear-gradient(135deg,var(--primary),var(--accent-violet));display:flex;align-items:center;justify-content:center;font-weight:700;color:#fff;flex-shrink:0;">
                                            {{ strtoupper(substr($pregnancy->patient_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight:600; color:var(--text);">{{ $pregnancy->patient_name }}</div>
                                            <small style="color:var(--text-muted);">
                                                @if($pregnancy->user_id && $pregnancy->woman)
                                                    {{ $pregnancy->woman->email }}
                                                @elseif($pregnancy->walk_in_patient_id)
                                                    Walk-in Patient
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding:1rem;">
                                    <div style="font-weight:600; color:var(--text);">{{ $pregnancy->formatted_aog }}</div>
                                </td>
                                <td style="padding:1rem;">
                                    @if($pregnancy->trimester)
                                        <div style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.35rem 0.75rem; background:rgba(13,110,253,0.1); color:var(--info); border-radius:20px; font-weight:500; font-size:0.85rem;">
                                            <i class="bi bi-calendar-week"></i>
                                            {{ $pregnancy->trimester_name }}
                                        </div>
                                    @else
                                        <span style="color:var(--text-muted);">N/A</span>
                                    @endif
                                </td>
                                <td style="padding:1rem;">
                                    <div style="font-weight:500; color:var(--text);">
                                        {{ \Carbon\Carbon::parse($pregnancy->lmp)->format('M j, Y') }}
                                    </div>
                                </td>
                                <td style="padding:1rem;">
                                    <div style="font-weight:500; color:var(--text);">
                                        {{ \Carbon\Carbon::parse($pregnancy->lmp)->addDays(280)->format('M j, Y') }}
                                    </div>
                                    @if($pregnancy->is_active)
                                        <small style="color:var(--text-muted);">{{ $pregnancy->days_until_due }} days left</small>
                                    @endif
                                </td>
                                <td style="padding:1rem;">
                                    <div style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.35rem 0.75rem; background:{{ $pregnancy->status === 'completed' ? 'rgba(108,117,125,0.1)' : 'rgba(25,135,84,0.1)' }}; color:{{ $pregnancy->status === 'completed' ? 'var(--text-muted)' : 'var(--success)' }}; border-radius:20px; font-weight:500; font-size:0.85rem;">
                                        {{ ucfirst($pregnancy->status ?? 'Unknown') }}
                                    </div>
                                </td>
                                <td style="padding:1rem;">
                                    <div style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.35rem 0.75rem; background:{{ ($pregnancy->risk_level ?? 'Low') === 'High' ? 'rgba(220,53,69,0.1)' : (($pregnancy->risk_level ?? 'Low') === 'Medium' ? 'rgba(255,193,7,0.18)' : 'rgba(25,135,84,0.1)') }}; color:{{ ($pregnancy->risk_level ?? 'Low') === 'High' ? 'var(--danger)' : (($pregnancy->risk_level ?? 'Low') === 'Medium' ? '#8a6d00' : 'var(--success)') }}; border-radius:20px; font-weight:500; font-size:0.85rem;">
                                        {{ $pregnancy->risk_level ?? 'Low' }}
                                    </div>
                                </td>
                                <td style="padding:1rem; text-align:right;">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('midwife.pregnancies.show', $pregnancy->id) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           style="border-radius:8px; padding:0.4rem 0.6rem;"
                                           title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('midwife.pregnancies.edit', $pregnancy->id) }}" 
                                           class="btn btn-sm btn-outline-warning" 
                                           style="border-radius:8px; padding:0.4rem 0.6rem;"
                                           title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $pregnancies->links('pagination::bootstrap-5') }}
            </div>
        @else
            {{-- Empty State --}}
            <div class="text-center py-5">
                <div style="width:80px;height:80px;border-radius:20px;background:linear-gradient(135deg,var(--primary),var(--accent-violet));display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;">
                    <i class="bi bi-heart" style="font-size:2.5rem;color:#fff;"></i>
                </div>
                <h4 style="font-weight:700; color:var(--text); margin-bottom:0.5rem;">No Pregnancies Found</h4>
                <p style="color:var(--text-muted); margin-bottom:1.5rem;">No pregnancy records have been added yet.</p>
                <a href="{{ route('midwife.pregnancies.create') }}" class="btn btn-primary" style="border-radius:10px; padding:0.75rem 1.5rem;">
                    <i class="bi bi-plus-circle me-2"></i>Add First Pregnancy
                </a>
            </div>
        @endif
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

document.addEventListener('DOMContentLoaded', function () {
    drawSparkline('sparkline1', [5, 8, 6, 10, 9, 12, {{ $pregnancies->total() }}], 'rgba(255,255,255,0.9)');
    drawSparkline('sparkline2', [3, 5, 4, 7, 6, 8, {{ $pregnancies->where('status', 'active')->count() }}], 'rgba(255,255,255,0.9)');
    drawSparkline('sparkline3', [2, 4, 3, 5, 4, 6, {{ $pregnancies->where('status', 'completed')->count() }}], 'rgba(255,255,255,0.9)');
    drawSparkline('sparkline4', [1, 2, 1, 3, 2, 4, {{ $pregnancies->where('risk_level', 'High')->count() }}], 'rgba(255,255,255,0.9)');
});
</script>
@endpush

@endsection
