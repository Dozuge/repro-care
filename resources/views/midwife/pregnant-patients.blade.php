@extends('midwife.layout')

@section('title', 'Pregnant Women - ReproCare')

@section('midwife-content')

{{-- ═══════════════════════════════
     PAGE HERO
═══════════════════════════════ --}}
<div class="page-hero fade-in-card mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3" style="position:relative;z-index:1;">
        <div>
            <div class="page-hero-title">
                <i class="bi bi-heart-pulse-fill me-2"></i>Pregnant Women
            </div>
            <p class="page-hero-subtitle">
                <i class="bi bi-calendar3 me-1"></i>{{ now()->format('l, F j, Y') }}
                &nbsp;·&nbsp; Manage and monitor all pregnancies
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('midwife.pregnancies.index') }}" class="btn" 
               style="background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.3); color:#fff; border-radius:12px; padding:0.6rem 1.25rem; font-weight:500; backdrop-filter:blur(10px); display:flex; align-items:center; gap:0.5rem;">
                <i class="bi bi-eye-fill"></i> View All
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
    <div class="col-xl-4 col-md-4">
        <div class="stat-card stat-cyan fade-in-card">
            <div class="stat-icon"><i class="bi bi-heart-pulse-fill"></i></div>
            <div class="stat-label">Active Pregnancies</div>
            <div class="stat-number" data-count="{{ $totalActive }}">0</div>
            <div class="stat-trend">
                <i class="bi bi-heart"></i> Currently monitoring
            </div>
            <canvas id="sparkline1" width="80" height="36" style="position:absolute;bottom:1rem;right:1rem;opacity:0.5;"></canvas>
        </div>
    </div>

    <div class="col-xl-4 col-md-4">
        <div class="stat-card stat-green fade-in-card">
            <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
            <div class="stat-label">Completed</div>
            <div class="stat-number" data-count="{{ $totalCompleted }}">0</div>
            <div class="stat-trend">
                <i class="bi bi-check2-all"></i> Delivered successfully
            </div>
            <canvas id="sparkline2" width="80" height="36" style="position:absolute;bottom:1rem;right:1rem;opacity:0.5;"></canvas>
        </div>
    </div>

    <div class="col-xl-4 col-md-4">
        <div class="stat-card stat-danger fade-in-card">
            <div class="stat-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <div class="stat-label">High Risk</div>
            <div class="stat-number" data-count="{{ $totalHighRisk }}">0</div>
            <div class="stat-trend danger">
                <i class="bi bi-arrow-up-short"></i> Requires attention
            </div>
            <canvas id="sparkline3" width="80" height="36" style="position:absolute;bottom:1rem;right:1rem;opacity:0.5;"></canvas>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════
     FILTER TABS & CONTENT
═══════════════════════════════ --}}
<div class="card fade-in-card" style="border:none; background:var(--bg-card);">
    {{-- Custom Tab Navigation --}}
    <div class="card-header" style="background:transparent; border-bottom:1px solid var(--border-color); padding:1rem 1.5rem;">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex gap-2">
                <a href="{{ route('midwife.pregnant-patients', ['status' => 'active']) }}" 
                   class="btn {{ $status === 'active' ? 'btn-primary' : 'btn-outline-primary' }}"
                   style="border-radius:10px; padding:0.5rem 1rem; font-weight:500;">
                    <i class="bi bi-heart-pulse me-1"></i>Active
                </a>
                <a href="{{ route('midwife.pregnant-patients', ['status' => 'completed']) }}" 
                   class="btn {{ $status === 'completed' ? 'btn-success' : 'btn-outline-success' }}"
                   style="border-radius:10px; padding:0.5rem 1rem; font-weight:500;">
                    <i class="bi bi-check-circle me-1"></i>Completed
                </a>
                <a href="{{ route('midwife.pregnant-patients', ['status' => 'high-risk']) }}" 
                   class="btn {{ $status === 'high-risk' ? 'btn-danger' : 'btn-outline-danger' }}"
                   style="border-radius:10px; padding:0.5rem 1rem; font-weight:500;">
                    <i class="bi bi-exclamation-triangle me-1"></i>High Risk
                </a>
            </div>
            
            {{-- Search --}}
            <form action="{{ route('midwife.pregnant-patients') }}" method="GET" class="d-flex gap-2 align-items-center">
                <input type="hidden" name="status" value="{{ $status }}">
                <div class="position-relative" style="width:280px;">
                    <i class="bi bi-search position-absolute" 
                       style="left:1rem; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:1rem; z-index:10;"></i>
                    <input type="text" name="search" class="form-control" 
                           style="background:var(--bg-input); border:1px solid var(--border-color); color:var(--text); border-radius:12px; padding:0.65rem 1rem 0.65rem 2.5rem; font-size:0.9rem;"
                           placeholder="Search by name or email..." value="{{ $search ?? '' }}">
                </div>
                @if($search)
                    <a href="{{ route('midwife.pregnant-patients', ['status' => $status]) }}" 
                       class="btn btn-outline-secondary" style="border-radius:10px; padding:0.6rem 1rem; white-space:nowrap;">
                        <i class="bi bi-x-lg"></i> Clear
                    </a>
                @endif
            </form>
        </div>
    </div>
    
    <div class="card-body p-4">
        {{-- Pregnancies Table --}}
        @if($pregnancies->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle" style="font-size:0.9rem;">
                    <thead>
                        <tr style="color:var(--text-muted); font-weight:600; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.5px;">
                            <th style="padding:1rem; border:none;">Woman</th>
                            <th style="padding:1rem; border:none;">Barangay</th>
                            <th style="padding:1rem; border:none;">Status</th>
                            <th style="padding:1rem; border:none;">Due Date</th>
                            <th style="padding:1rem; border:none; text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pregnancies as $pregnancy)
                            <tr style="border-bottom:1px solid var(--border-color);">
                                <td style="padding:1rem;">
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="width:42px;height:42px;border-radius:12px;background:linear-gradient(135deg,var(--primary),var(--accent-violet));display:flex;align-items:center;justify-content:center;font-weight:700;color:#fff;flex-shrink:0;">
                                            {{ strtoupper(substr($pregnancy->woman->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight:600; color:var(--text);">{{ $pregnancy->woman->name }}</div>
                                            <small style="color:var(--text-muted);">{{ $pregnancy->woman->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding:1rem;">
                                    @if($pregnancy->woman->barangay)
                                        <div style="color:var(--text);">
                                            <i class="bi bi-house-fill me-1"></i>{{ $pregnancy->woman->barangay }}
                                        </div>
                                    @else
                                        <span style="color:var(--text-muted);">N/A</span>
                                    @endif
                                </td>
                                <td style="padding:1rem;">
                                    @if($pregnancy->status === 'high_risk')
                                        <div style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.35rem 0.75rem; background:rgba(220,53,69,0.1); color:var(--danger); border-radius:20px; font-weight:500; font-size:0.85rem;">
                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                            High Risk
                                        </div>
                                        <small style="display:block; margin-top:0.25rem; color:var(--text-muted);">{{ $pregnancy->formatted_aog }}</small>
                                    @elseif($pregnancy->is_active)
                                        <div style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.35rem 0.75rem; background:rgba(25,135,84,0.1); color:var(--success); border-radius:20px; font-weight:500; font-size:0.85rem;">
                                            <i class="bi bi-heart-pulse-fill"></i>
                                            Active
                                        </div>
                                        <small style="display:block; margin-top:0.25rem; color:var(--text-muted);">{{ $pregnancy->formatted_aog }}</small>
                                    @else
                                        <div style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.35rem 0.75rem; background:rgba(108,117,125,0.1); color:var(--text-muted); border-radius:20px; font-weight:500; font-size:0.85rem;">
                                            <i class="bi bi-check-circle-fill"></i>
                                            Completed
                                        </div>
                                    @endif
                                </td>
                                <td style="padding:1rem;">
                                    @if($pregnancy->edd)
                                        <div style="font-weight:500; color:var(--text);">{{ $pregnancy->edd->format('M d, Y') }}</div>
                                        @if($pregnancy->is_active)
                                            <small style="color:var(--text-muted);">{{ $pregnancy->days_until_due }} days left</small>
                                        @endif
                                    @else
                                        <span style="color:var(--text-muted);">N/A</span>
                                    @endif
                                </td>
                                <td style="padding:1rem; text-align:right;">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('midwife.patient-details', $pregnancy->user->id) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           style="border-radius:8px; padding:0.4rem 0.6rem;"
                                           title="View Woman Profile">
                                            <i class="bi bi-person"></i>
                                        </a>
                                        <a href="{{ route('midwife.pregnancies.show', $pregnancy->id) }}" 
                                           class="btn btn-sm btn-outline-info" 
                                           style="border-radius:8px; padding:0.4rem 0.6rem;"
                                           title="View Pregnancy Details">
                                            <i class="bi bi-heart"></i>
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
                    <i class="bi bi-heart-pulse" style="font-size:2.5rem;color:#fff;"></i>
                </div>
                <h4 style="font-weight:700; color:var(--text); margin-bottom:0.5rem;">No pregnancies found</h4>
                <p style="color:var(--text-muted); margin-bottom:1.5rem;">There are no pregnancy records matching your criteria.</p>
                <a href="{{ route('midwife.pregnancies.create') }}" class="btn btn-primary" style="border-radius:10px; padding:0.75rem 1.5rem;">
                    <i class="bi bi-plus-circle me-2"></i>Add Pregnancy Record
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
    drawSparkline('sparkline1', [3, 5, 4, 7, 6, 8, {{ $totalActive }}], 'rgba(255,255,255,0.9)');
    drawSparkline('sparkline2', [2, 4, 3, 5, 4, 6, {{ $totalCompleted }}], 'rgba(255,255,255,0.9)');
    drawSparkline('sparkline3', [1, 2, 1, 3, 2, 4, {{ $totalHighRisk }}], 'rgba(255,255,255,0.9)');
});
</script>
@endpush

@endsection
