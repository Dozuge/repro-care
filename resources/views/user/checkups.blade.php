@extends('user.layout')

@section('title', 'My Checkups - ReproCare')

@push('styles')
<style>
    .checkup-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 18px;
        overflow: hidden;
        height: 100%;
        transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
    }
    .checkup-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-md);
        border-color: var(--border-glass);
    }
    .checkup-card-header {
        padding: 0.9rem 1.2rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--bg-card2);
        transition: background 0.4s ease;
    }
    .checkup-id {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--primary-light);
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    .checkup-card-body { padding: 1.2rem; }
    .checkup-purpose {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 1rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 1rem;
        line-height: 1.4;
    }
    .checkup-info-row {
        display: flex;
        align-items: flex-start;
        gap: 0.6rem;
        margin-bottom: 0.6rem;
        font-size: 0.875rem;
        color: var(--text-muted);
    }
    .checkup-info-row i {
        color: var(--primary-light);
        font-size: 0.9rem;
        flex-shrink: 0;
        margin-top: 2px;
    }
    .checkup-info-row strong { color: var(--text); font-weight: 600; }
    .checkup-card-footer {
        padding: 0.75rem 1.2rem;
        border-top: 1px solid var(--border);
        font-size: 0.78rem;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
</style>
@endpush

@section('user-content')
<div class="py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 fade-in-card">
        <div>
            <h1 class="page-title">
                <i class="bi bi-clipboard2-pulse-fill me-2" style="color:var(--primary-light);"></i>
                My Checkups
            </h1>
        </div>
    @if($checkups->count() > 0)
        <span style="background:var(--bg-card2);border:1px solid var(--border);color:var(--text-muted);font-size:0.8rem;font-weight:600;padding:0.35em 1em;border-radius:20px;">
            {{ $checkups->count() }} {{ Str::plural('Record', $checkups->count()) }}
        </span>
    @endif
</div>

{{-- Summary chips --}}
@if($checkups->count() > 0)
<div class="d-flex flex-wrap gap-2 mb-4 fade-in-card">
    <span class="summary-chip chip-info">
        <i class="bi bi-calendar-check"></i>
        Scheduled: {{ $checkups->where('status', 'Scheduled')->count() }}
    </span>
    <span class="summary-chip chip-success">
        <i class="bi bi-check2-circle"></i>
        Completed: {{ $checkups->where('status', 'Completed')->count() }}
    </span>
    <span class="summary-chip chip-danger">
        <i class="bi bi-x-circle"></i>
        Missed: {{ $checkups->where('status', 'Missed')->count() }}
    </span>
</div>
@endif

{{-- Cards --}}
@if($checkups->count() > 0)
    <div class="row g-3">
        @foreach($checkups as $checkup)
        <div class="col-lg-6 col-md-6 fade-in-card">
            <div class="checkup-card">
                {{-- Card Header --}}
                <div class="checkup-card-header">
                    <span class="
                        @if($checkup->status === 'Scheduled') status-scheduled
                        @elseif($checkup->status === 'Completed') status-completed
                        @elseif($checkup->status === 'Missed') status-missed
                        @else status-pending
                        @endif
                    ">
                        @if($checkup->status === 'Scheduled')
                            <i class="bi bi-calendar-event me-1"></i>
                        @elseif($checkup->status === 'Completed')
                            <i class="bi bi-check2-circle me-1"></i>
                        @elseif($checkup->status === 'Missed')
                            <i class="bi bi-x-circle me-1"></i>
                        @endif
                        {{ $checkup->status }}
                    </span>
                </div>

                {{-- Card Body --}}
                <div class="checkup-card-body">
                    <h6 class="checkup-purpose">{{ $checkup->purpose }}</h6>

                    <div class="checkup-info-row">
                        <i class="bi bi-calendar-date"></i>
                        <div>
                            <strong>Scheduled:</strong><br>
                            {{ $checkup->scheduled_date->format('F j, Y') }}
                        </div>
                    </div>

                    @if($checkup->actual_date)
                    <div class="checkup-info-row">
                        <i class="bi bi-calendar-check"></i>
                        <div>
                            <strong>Actual Visit:</strong><br>
                            {{ $checkup->actual_date->format('F j, Y') }}
                        </div>
                    </div>
                    @endif

                    @if($checkup->midwife)
                    <div class="checkup-info-row">
                        <i class="bi bi-person-badge-fill"></i>
                        <div>
                            <strong>Midwife:</strong><br>
                            {{ $checkup->midwife->name }}
                        </div>
                    </div>
                    @endif

                    @if($checkup->notes)
                    <div class="checkup-info-row">
                        <i class="bi bi-journal-text"></i>
                        <div>
                            <strong>Notes:</strong><br>
                            {{ $checkup->notes }}
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Card Footer --}}
                <div class="checkup-card-footer">
                    <i class="bi bi-clock-history"></i>
                    Recorded on {{ $checkup->created_at->format('M j, Y') }}
                </div>
            </div>
        </div>
        @endforeach
    </div>

@else
    {{-- Empty State --}}
    <div class="card fade-in-card">
        <div class="empty-state">
            <i class="bi bi-clipboard2-x empty-state-icon"></i>
            <h6>No Checkup Records Yet</h6>
            <p>Your prenatal checkup schedule will appear here once it's been set up by your assigned midwife.</p>
        </div>
    </div>
@endif

@endsection
