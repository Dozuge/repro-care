@extends('user.layout')

@section('title', 'Menstrual Cycle - ReproCare')

@push('styles')
<style>
/* === FLO-STYLE OVERVIEW PAGE === */
.cycle-hero {
    background: linear-gradient(135deg, #2d1159 0%, #1a0538 60%, #0f0a1e 100%);
    border-radius: 20px;
    padding: 2rem;
    position: relative;
    overflow: hidden;
    border: 1px solid var(--border);
    margin-bottom: 1.5rem;
}

 [data-theme="light"] .cycle-hero {
     background: linear-gradient(135deg, #ffffff 0%, #f0e6ff 60%, #eadcff 100%);
 }

.cycle-hero::before {
    content: '';
    position: absolute;
    width: 350px; height: 350px;
    background: radial-gradient(circle, rgba(124,58,237,0.3) 0%, transparent 70%);
    top: -80px; right: -80px; pointer-events: none;
}

.cycle-phase-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.78rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    margin-bottom: 0.75rem;
}

.cycle-ring {
    width: 160px; height: 160px;
    border-radius: 50%;
    background: conic-gradient(#7c3aed var(--progress, 30%), rgba(255,255,255,0.08) 0%);
    display: flex; align-items: center; justify-content: center;
    position: relative;
    box-shadow: 0 0 40px rgba(124,58,237,0.4);
    flex-shrink: 0;
}

.cycle-ring::before {
    content: '';
    position: absolute;
    width: 130px; height: 130px;
    background: var(--bg-main);
    border-radius: 50%;
}

.cycle-ring-inner {
    position: relative; z-index: 1; text-align: center;
}

.cycle-ring-inner .day-num {
    font-size: 2.2rem;
    font-weight: 800;
    line-height: 1;
    color: var(--text);
}

.cycle-ring-inner .day-label {
    font-size: 0.72rem;
    color: var(--text-muted);
    font-weight: 500;
}

.period-record-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 1rem 1.25rem;
    transition: all 0.2s;
}

.period-record-card:hover {
    border-color: var(--primary);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(124,58,237,0.2);
}

.period-dot {
    width: 10px; height: 10px;
    border-radius: 50%;
    background: var(--primary);
    flex-shrink: 0;
}
</style>
@endpush

@section('user-content')
<div class="py-2">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title"><i class="bi bi-calendar-heart me-2" style="color: var(--primary-light);"></i>Menstrual Cycle</h1>
            <p class="page-subtitle">Track, predict, and understand your cycle</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Hero Cycle Card -->
    <div class="cycle-hero mb-4">
        <div class="d-flex align-items-center gap-4 flex-wrap">
            <!-- Cycle Ring -->
            @php
                $cycleDays = 28; // Default
                $currentDay = 1;
                $isOverdue = false;
                if ($records->count() > 0) {
                    $lastPeriod = $records->first();
                    if ($averageCycle) $cycleDays = round($averageCycle);
                    
                    // Calculate current day of cycle
                    $lastPeriodEnd = $lastPeriod->period_end_date ?? $lastPeriod->period_start_date;
                    $daysSinceEnd = abs(round($lastPeriodEnd->diffInDays(now(), false)));
                    $currentDay = $daysSinceEnd + 1;
                    
                    // Check if cycle is overdue
                    if ($currentDay > $cycleDays) {
                        $currentDay = $cycleDays;
                        $isOverdue = true;
                    }
                    
                    $currentDay = max(1, min($currentDay, $cycleDays));
                }
                $progress = min($currentDay / $cycleDays, 1) * 100;

                // Determine phase
                $phase = 'Follicular';
                $phaseColor = '#8b5cf6';
                $phaseBg = 'rgba(139,92,246,0.15)';
                $phaseIcon = 'bi-stars';

                if ($records->count() > 0) {
                    if ($currentDay <= ($records->first()->duration ?? 5)) {
                        $phase = 'Menstrual'; $phaseColor = '#ef4444'; $phaseBg = 'rgba(239,68,68,0.15)'; $phaseIcon = 'bi-droplet-fill';
                    } elseif ($currentDay <= 13) {
                        $phase = 'Follicular'; $phaseColor = '#8b5cf6'; $phaseBg = 'rgba(139,92,246,0.15)'; $phaseIcon = 'bi-stars';
                    } elseif ($currentDay == 14) {
                        $phase = 'Ovulation'; $phaseColor = '#ec4899'; $phaseBg = 'rgba(236,72,153,0.15)'; $phaseIcon = 'bi-egg-fill';
                    } else {
                        $phase = 'Luteal'; $phaseColor = '#f59e0b'; $phaseBg = 'rgba(245,158,11,0.15)'; $phaseIcon = 'bi-moon-stars';
                    }
                }
            @endphp

            @if($records->count() > 0)
                <div class="cycle-ring" style="--progress: {{ $progress }}%;">
                    <div class="cycle-ring-inner">
                        <div class="day-num">{{ $currentDay }}</div>
                        <div class="day-label">of {{ $cycleDays }} days</div>
                        @if($isOverdue)
                        <div class="day-label" style="color: #ef4444; font-size: 0.65rem; margin-top: 0.25rem;">
                            <i class="bi bi-exclamation-circle"></i> Overdue
                        </div>
                        @endif
                    </div>
                </div>
            @endif

            <div class="flex-grow-1">
                @if($records->count() > 0)
                    <div class="cycle-phase-badge" style="background: {{ $phaseBg }}; color: {{ $phaseColor }}; border: 1px solid {{ $phaseColor }}44;">
                        <i class="bi {{ $phaseIcon }}"></i> {{ $phase }} Phase
                    </div>
                @endif

                @if($nextPeriod)
                    <h2 class="fw-800 mb-1" style="font-size: 1.75rem; font-weight: 800; color: var(--text);">
                        {{ round(abs(now()->diffInDays($nextPeriod, false))) }} days
                    </h2>
                    <p style="color: var(--text-muted); font-size: 0.875rem; margin-bottom: 1rem;">
                        until next period — estimated <strong style="color: var(--text);">{{ $nextPeriod->format('F j') }}</strong>
                    </p>
                    @if($averageCycle)
                        <small style="color: var(--text-muted);">
                            <i class="bi bi-arrow-repeat me-1"></i> Average cycle: {{ $averageCycle }} days
                        </small>
                    @endif
                @else
                    <h3 class="fw-700 mb-1" style="color: var(--text);">Start Tracking</h3>
                    <p style="color: var(--text-muted); font-size: 0.875rem;">
                        Log your first period to get predictions and insights
                    </p>
                    <a href="{{ route('user.menstruation.create') }}" class="btn btn-primary btn-sm mt-2">
                        <i class="bi bi-plus-lg me-1"></i> Log Period
                    </a>
                @endif
            </div>

            <!-- Quick nav links on the right - only show when there are records -->
            @if($records->count() > 0)
                <div class="d-flex flex-column gap-2 ms-auto" style="min-width: 140px;">
                    <a href="{{ route('user.menstruation.calendar') }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-calendar3 me-1"></i> Calendar
                    </a>
                    <a href="{{ route('user.menstruation.statistics') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-bar-chart-line me-1"></i> Statistics
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Period History -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-600"><i class="bi bi-clock-history me-2" style="color: var(--primary-light);"></i>Period History</h5>
            <span class="badge" style="background: var(--primary-subtle); color: var(--primary-light); border: 1px solid var(--border);">
                {{ $records->count() }} records
            </span>
        </div>
        <div class="card-body p-0">
            @if($records->count() > 0)
                <div class="p-3 d-flex flex-column gap-3">
                    @foreach($records as $record)
                        <div class="period-record-card d-flex align-items-center gap-3">
                            <div class="text-center" style="min-width: 48px;">
                                <div style="font-size: 1.5rem; font-weight: 800; color: var(--primary-light); line-height: 1;">
                                    {{ $record->period_start_date->format('d') }}
                                </div>
                                <div style="font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px;">
                                    {{ $record->period_start_date->format('M') }}
                                </div>
                            </div>

                            <div class="d-flex flex-column" style="height: 48px; align-items:center; justify-content:center;">
                                <div style="width: 2px; flex: 1; background: linear-gradient(to bottom, var(--primary), var(--secondary));"></div>
                                <div class="period-dot"></div>
                                <div style="width: 2px; flex: 1; background: linear-gradient(to bottom, var(--secondary), transparent);"></div>
                            </div>

                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong style="font-size: 0.9rem; color: var(--text);">
                                            {{ $record->period_start_date->format('M d') }}
                                            @if($record->period_end_date)
                                                – {{ $record->period_end_date->format('M d, Y') }}
                                            @else
                                                – <span class="text-warning">Ongoing</span>
                                            @endif
                                        </strong>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge" style="background: rgba(239,68,68,0.15); color: #fca5a5; border-radius: 20px; padding: 0.3em 0.8em;">
                                            {{ $record->duration }} days
                                        </span>
                                        <form action="{{ route('user.menstruation.destroy', $record->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this record?')" style="padding: 0.2em 0.5em; font-size: 0.75rem;">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center">
                    <div style="width: 80px; height: 80px; border-radius: 50%; background: var(--primary-subtle); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                        <i class="bi bi-calendar-heart" style="font-size: 2rem; color: var(--primary-light);"></i>
                    </div>
                    <h5 style="color: var(--text);">No periods logged yet</h5>
                    <p style="color: var(--text-muted); font-size: 0.875rem;">Start tracking to get personalized insights & predictions</p>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
