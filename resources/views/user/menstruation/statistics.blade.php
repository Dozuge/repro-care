@extends('user.layout')

@section('title', 'Cycle Statistics - ReproCare')

@section('user-content')
<div class="py-2">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title"><i class="bi bi-bar-chart-line me-2" style="color: var(--primary-light);"></i>Cycle Statistics</h1>
            <p class="page-subtitle">Insights from your complete cycle history</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('user.menstruation.calendar') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-calendar3 me-1"></i> Calendar
            </a>
            <a href="{{ route('user.menstruation.report') }}" class="btn btn-success btn-sm">
                <i class="bi bi-file-earmark-pdf me-1"></i> PDF Report
            </a>
        </div>
    </div>

    <!-- Stats Cards Row -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card text-center h-100">
                <div class="card-body py-3">
                    <div style="font-size: 2.5rem; font-weight: 800; color: var(--primary-light); line-height: 1;">
                        {{ $averageCycle ?? 'N/A' }}
                    </div>
                    @if($averageCycle) <div style="font-size: 0.65rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">days</div> @endif
                    <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.35rem; font-weight: 500;">Avg Cycle Length</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center h-100">
                <div class="card-body py-3">
                    <div style="font-size: 2.5rem; font-weight: 800; color: #fca5a5; line-height: 1;">
                        {{ $averagePeriod ?? 'N/A' }}
                    </div>
                    @if($averagePeriod) <div style="font-size: 0.65rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">days</div> @endif
                    <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.35rem; font-weight: 500;">Avg Period Duration</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center h-100">
                <div class="card-body py-3">
                    <div style="font-size: 2.5rem; font-weight: 800; color: #6ee7b7; line-height: 1;">
                        {{ count($cycleHistory) }}
                    </div>
                    <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.35rem; font-weight: 500;">Cycles Tracked</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center h-100">
                <div class="card-body py-3">
                    @php
                        $normalCount = collect($cycleHistory)->where('is_normal', true)->count();
                        $totalWithNormal = collect($cycleHistory)->whereNotNull('is_normal')->count();
                        $regularPct = $totalWithNormal > 0 ? round($normalCount / $totalWithNormal * 100) : null;
                    @endphp
                    <div style="font-size: 2.5rem; font-weight: 800; color: #fcd34d; line-height: 1;">
                        {{ $regularPct !== null ? $regularPct . '%' : 'N/A' }}
                    </div>
                    <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.35rem; font-weight: 500;">Regular Cycles</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Banner -->
    <div class="card mb-4" style="background: linear-gradient(135deg, rgba(16,185,129,0.15), rgba(5,150,105,0.08)); border-color: rgba(16,185,129,0.3);">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(16,185,129,0.2); display:flex; align-items:center; justify-content:center;">
                    <i class="bi bi-file-earmark-medical" style="color: #6ee7b7; font-size: 1.2rem;"></i>
                </div>
                <div>
                    <div style="font-weight: 600; color: var(--text);">Medical Report</div>
                    <div style="font-size: 0.8rem; color: var(--text-muted);">Download a PDF summary of your menstrual history for your doctor</div>
                </div>
            </div>
            <a href="{{ route('user.menstruation.report') }}" class="btn btn-success">
                <i class="bi bi-download me-1"></i> Download PDF
            </a>
        </div>
    </div>

    <!-- Legend -->
    <div class="d-flex gap-2 flex-wrap mb-4">
        <div style="display:inline-flex; align-items:center; gap:0.4rem; font-size:0.78rem; color: var(--text-muted);">
            <div style="width: 12px; height: 12px; border-radius: 3px; background: #ef4444;"></div> Period Days
        </div>
        <div style="display:inline-flex; align-items:center; gap:0.4rem; font-size:0.78rem; color: var(--text-muted);">
            <div style="width: 12px; height: 12px; border-radius: 3px; background: #06b6d4;"></div> Fertile Window
        </div>
        <div style="display:inline-flex; align-items:center; gap:0.4rem; font-size:0.78rem; color: var(--text-muted);">
            <div style="width: 12px; height: 12px; border-radius: 3px; background: #ec4899;"></div> Ovulation
        </div>
        <div style="display:inline-flex; align-items:center; gap:0.4rem; font-size:0.78rem; color: var(--text-muted);">
            <div style="width: 12px; height: 12px; border-radius: 3px; background: rgba(255,255,255,0.15);"></div> Safe Days
        </div>
    </div>

    <!-- Cycle Length Chart -->
    <div class="row g-4 mb-4">
        <div class="col-lg-12">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0 fw-600"><i class="bi bi-graph-up-arrow me-2" style="color: var(--primary-light);"></i>Cycle Length Trend</h5>
                </div>
                <div class="card-body">
                    @if(count($cycleLengths) > 0)
                        <canvas id="cycleChart" style="max-height: 260px;"></canvas>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-graph-up" style="font-size: 2.5rem; color: var(--text-muted);"></i>
                            <p class="mt-3" style="color: var(--text-muted);">Log at least 2 periods to see cycle trends</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Cycle History Timeline -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 fw-600"><i class="bi bi-clock-history me-2" style="color: var(--primary-light);"></i>Cycle History</h5>
        </div>
        <div class="card-body">
            @if(count($cycleHistory) > 0)
                <div class="d-flex flex-column gap-3">
                    @foreach($cycleHistory as $cycle)
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span style="font-size: 0.875rem; font-weight: 600; color: var(--text);">
                                        {{ $cycle['period_start_date']->format('M d') }} – {{ $cycle['period_end_date'] ? $cycle['period_end_date']->format('M d, Y') : 'Present' }}
                                    </span>
                                    @if(isset($cycle['is_current']))
                                        <span class="badge" style="background: rgba(245,158,11,0.15); color: #fcd34d; font-size: 0.7rem;">Current</span>
                                    @endif
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span style="font-size: 0.875rem; color: var(--text-muted);">{{ $cycle['cycle_length'] }} days</span>
                                    @if(isset($cycle['is_normal']))
                                        @if($cycle['is_normal'])
                                            <span style="font-size: 0.72rem; background: rgba(16,185,129,0.15); color: #6ee7b7; padding: 0.2em 0.6em; border-radius: 20px;">Normal</span>
                                        @else
                                            <span style="font-size: 0.72rem; background: rgba(245,158,11,0.15); color: #fcd34d; padding: 0.2em 0.6em; border-radius: 20px;">Watch</span>
                                        @endif
                                    @endif
                                    @if(!isset($cycle['is_current']))
                                        <form action="{{ route('user.menstruation.destroy', $cycle['id']) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this record?')" style="padding: 0.2em 0.5em; font-size: 0.75rem;">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            <!-- Visual strip -->
                            <div style="display: flex; height: 20px; border-radius: 10px; overflow: hidden; gap: 1px; background: var(--bg-card2);">
                                @for($i = 1; $i <= $cycle['cycle_length']; $i++)
                                    @php
                                        $col = 'rgba(255,255,255,0.06)';
                                        if ($i <= $cycle['period_length']) {
                                            $col = '#ef4444';
                                        } elseif ($i >= ($cycle['cycle_length'] - 19) && $i <= ($cycle['cycle_length'] - 15)) {
                                            $col = '#06b6d4';
                                        } elseif ($i == ($cycle['cycle_length'] - 14)) {
                                            $col = '#ec4899';
                                        }
                                    @endphp
                                    <div style="flex: 1; background: {{ $col }}; min-width: 2px;" title="Day {{ $i }}"></div>
                                @endfor
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x" style="font-size: 3rem; color: var(--text-muted);"></i>
                    <p class="mt-3" style="color: var(--text-muted);">No cycle history available. Start tracking your periods.</p>
                </div>
            @endif
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
Chart.defaults.color = '#9b8abf';
Chart.defaults.borderColor = 'rgba(124,58,237,0.15)';

@if(count($cycleLengths) > 0)
const cycleCtx = document.getElementById('cycleChart').getContext('2d');
new Chart(cycleCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_map(function($i) { return 'Cycle ' . ($i + 1); }, array_keys(array_reverse($cycleLengths)))) !!},
        datasets: [{
            label: 'Cycle Length (days)',
            data: {!! json_encode(array_reverse($cycleLengths)) !!},
            borderColor: '#7c3aed',
            backgroundColor: 'rgba(124,58,237,0.15)',
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#7c3aed',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
        },
        scales: {
            y: {
                beginAtZero: false,
                min: 15,
                max: 45,
                grid: { color: 'rgba(124,58,237,0.1)' },
                ticks: { color: '#9b8abf' },
                title: { display: true, text: 'Days', color: '#9b8abf' }
            },
            x: {
                grid: { color: 'rgba(124,58,237,0.1)' },
                ticks: { color: '#9b8abf' }
            }
        }
    }
});
@endif

</script>
@endpush
@endsection
