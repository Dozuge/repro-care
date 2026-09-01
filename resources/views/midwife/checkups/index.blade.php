@extends('midwife.layout')

@section('title', 'Checkups - Midwife Portal | ReproCare')

@push('styles')
<style>
    .patient-avatar {
        width: 34px; height: 34px; border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), var(--accent-violet));
        display: flex; align-items: center; justify-content: center;
        font-size: 0.78rem; font-weight: 800; color: #fff; flex-shrink: 0;
    }
    .search-bar {
        display: flex; gap: 0.5rem; align-items: center;
        background: var(--bg-card2); border: 1px solid var(--border);
        border-radius: 12px; padding: 0.4rem 0.6rem; max-width: 300px;
        transition: border-color 0.2s ease;
    }
    .search-bar:focus-within { border-color: var(--primary); box-shadow: 0 0 0 3px var(--focus-ring); }
    .search-bar input {
        border: none; background: transparent; color: var(--text);
        font-size: 0.875rem; outline: none; flex: 1; min-width: 0;
    }
    .search-bar input::placeholder { color: var(--placeholder); }
    .search-bar button {
        border: none; background: transparent; color: var(--text-muted);
        cursor: pointer; padding: 0.2rem 0.35rem; border-radius: 8px;
        transition: background 0.15s ease;
    }
    .search-bar button:hover { background: var(--primary-subtle); color: var(--primary-light); }
    .btn-action {
        width: 30px; height: 30px; border-radius: 8px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 0.78rem; border: 1px solid var(--border); background: var(--bg-card2);
        color: var(--text-muted); transition: all 0.15s ease; cursor: pointer;
    }
    .btn-action:hover { transform: scale(1.12); }
    .ba-view:hover    { background:var(--primary-subtle);border-color:var(--primary);color:var(--primary-light); }
    .ba-edit:hover    { background:rgba(245,158,11,0.12);border-color:var(--warning);color:var(--warning); }
    .ba-done:hover    { background:rgba(16,185,129,0.12);border-color:var(--success);color:var(--success); }
    .ba-miss:hover    { background:rgba(239,68,68,0.12);border-color:var(--danger);color:var(--danger); }
    .ba-cancel:hover  { background:var(--bg-card2);border-color:var(--text-muted);color:var(--text-muted); }
    .ba-resched:hover { background:rgba(245,158,11,0.12);border-color:var(--warning);color:var(--warning); }
    .purpose-pill {
        background: rgba(6,182,212,0.14); color: #22d3ee;
        border-radius: 10px; padding: 0.2em 0.65em;
        font-size: 0.78rem; font-weight: 600; max-width: 160px;
        overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: inline-block;
    }
</style>
@endpush

@section('midwife-content')

{{-- ── Header ── --}}
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3 fade-in-card">
    <div>
        <h1 class="page-title">
            <i class="bi bi-calendar2-check-fill me-2" style="color:var(--primary-light);"></i>
            Checkups
        </h1>
        <p class="page-subtitle">Manage all scheduled and completed prenatal checkups</p>
    </div>
    <div class="d-flex gap-2 align-items-center flex-wrap">
        <form method="GET" action="{{ route('midwife.checkups.index') }}" class="d-flex gap-2 align-items-center flex-wrap">
            <div class="search-bar">
                <input type="search" name="search" placeholder="Search checkups…" value="{{ request('search') }}">
                <button type="submit"><i class="bi bi-search"></i></button>
            </div>
            <select name="status" class="form-select form-select-sm" style="width:150px;">
                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Statuses</option>
                <option value="scheduled" {{ $status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="missed" {{ $status === 'missed' ? 'selected' : '' }}>Missed</option>
                <option value="rescheduled" {{ $status === 'rescheduled' ? 'selected' : '' }}>Rescheduled</option>
            </select>
            <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}" style="width:145px;">
            <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}" style="width:145px;">
            <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-funnel me-1"></i>Apply</button>
            @if(request()->hasAny(['search', 'status', 'start_date', 'end_date']))
                <a href="{{ route('midwife.checkups.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
            @endif
        </form>
        <a href="{{ route('midwife.checkups.create') }}" class="btn btn-primary">
            <i class="bi bi-calendar-plus-fill me-1"></i> Schedule
        </a>
    </div>
</div>

{{-- ── Flash ── --}}
@if(session('success'))
    <div class="alert alert-success d-flex align-items-center gap-2 mb-4 fade-in-card">
        <i class="bi bi-check-circle-fill flex-shrink-0"></i>
        {{ session('success') }}
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- ── Summary stat cards ── --}}
<div class="row g-3 mb-4">
    <div class="col">
        <div class="stat-card stat-purple fade-in-card">
            <i class="bi bi-clipboard-check stat-icon"></i>
            <div class="stat-label">Total</div>
            <div class="stat-number" data-count="{{ $stats['total'] }}">{{ $stats['total'] }}</div>
        </div>
    </div>
    <div class="col">
        <div class="stat-card stat-amber fade-in-card">
            <i class="bi bi-clock stat-icon"></i>
            <div class="stat-label">Scheduled</div>
            <div class="stat-number" data-count="{{ $stats['scheduled'] }}">{{ $stats['scheduled'] }}</div>
        </div>
    </div>
    <div class="col">
        <div class="stat-card stat-green fade-in-card">
            <i class="bi bi-check2-circle stat-icon"></i>
            <div class="stat-label">Completed</div>
            <div class="stat-number" data-count="{{ $stats['completed'] }}">{{ $stats['completed'] }}</div>
        </div>
    </div>
    <div class="col">
        <div class="stat-card stat-danger fade-in-card">
            <i class="bi bi-x-circle stat-icon"></i>
            <div class="stat-label">Missed</div>
            <div class="stat-number" data-count="{{ $stats['missed'] }}">{{ $stats['missed'] }}</div>
        </div>
    </div>
    <div class="col">
        <div class="stat-card stat-blue fade-in-card" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
            <i class="bi bi-arrow-repeat stat-icon"></i>
            <div class="stat-label">Rescheduled</div>
            <div class="stat-number" data-count="{{ $stats['rescheduled'] }}">{{ $stats['rescheduled'] }}</div>
        </div>
    </div>
</div>

{{-- ── Rescheduled Checkups Alert Section ── --}}
@php
    $rescheduledCheckups = \App\Models\Checkup::with('user')
        ->where('status', 'Rescheduled')
        ->orderBy('scheduled_date', 'desc')
        ->take(5)
        ->get();
@endphp
@if($rescheduledCheckups->count() > 0)
<div class="alert alert-warning alert-dismissible fade show fade-in-card mb-4" style="border-left: 4px solid var(--warning);">
    <div class="d-flex align-items-start gap-3">
        <div class="flex-shrink-0">
            <i class="bi bi-arrow-repeat fs-4"></i>
        </div>
        <div class="flex-grow-1">
            <h6 class="alert-heading mb-2">
                <i class="bi bi-calendar-event me-1"></i>
                {{ $rescheduledCheckups->count() }} Rescheduled Checkup{{ $rescheduledCheckups->count() > 1 ? 's' : '' }} Pending
            </h6>
            <div class="table-responsive">
                <table class="table table-sm table-borderless mb-0">
                    <thead>
                        <tr>
                            <th style="width: 30%;">Patient</th>
                            <th style="width: 25%;">New Scheduled Date</th>
                            <th style="width: 25%;">Purpose</th>
                            <th style="width: 20%;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rescheduledCheckups as $rescheduled)
                        @php
                            $dateDisplay = '';
                            if($rescheduled->scheduled_date) {
                                if(is_string($rescheduled->scheduled_date)) {
                                    $dateDisplay = \Carbon\Carbon::parse($rescheduled->scheduled_date)->format('M j, Y');
                                } else {
                                    $dateDisplay = $rescheduled->scheduled_date->format('M j, Y');
                                }
                            }
                        @endphp
                        <tr>
                            <td>
                                <div style="font-weight:600;">{{ $rescheduled->user->name ?? 'Unknown' }}</div>
                                <div style="font-size:0.75rem;color:var(--text-muted);">{{ $rescheduled->user->email ?? '' }}</div>
                            </td>
                            <td>
                                <span style="color:var(--warning);font-weight:600;">{{ $dateDisplay }}</span>
                            </td>
                            <td>
                                <span style="font-size:0.8rem;">{{ Str::limit($rescheduled->purpose ?? 'No purpose', 20) }}</span>
                            </td>
                            <td>
                                <a href="{{ route('midwife.checkups.show', $rescheduled->id) }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($stats['rescheduled'] > 5)
            <div class="mt-2">
                <a href="{{ route('midwife.checkups.index') }}?status=rescheduled" class="text-decoration-none">
                    <small>View all {{ $stats['rescheduled'] }} rescheduled checkups →</small>
                </a>
            </div>
            @endif
        </div>
        <button type="button" class="btn-close flex-shrink-0" data-bs-dismiss="alert"></button>
    </div>
</div>
@endif

{{-- ── Missed Checkups Alert Section ── --}}
@php
    $missedCheckups = \App\Models\Checkup::with('user')
        ->where('status', 'Missed')
        ->orderBy('scheduled_date', 'desc')
        ->take(5)
        ->get();
@endphp
@if($missedCheckups->count() > 0)
<div class="alert alert-danger alert-dismissible fade show fade-in-card mb-4" style="border-left: 4px solid var(--danger);">
    <div class="d-flex align-items-start gap-3">
        <div class="flex-shrink-0">
            <i class="bi bi-exclamation-triangle-fill fs-4"></i>
        </div>
        <div class="flex-grow-1">
            <h6 class="alert-heading mb-2">
                <i class="bi bi-x-circle-fill me-1"></i>
                {{ $missedCheckups->count() }} Missed Checkup{{ $missedCheckups->count() > 1 ? 's' : '' }} Require Attention
            </h6>
            <div class="table-responsive">
                <table class="table table-sm table-borderless mb-0">
                    <thead>
                        <tr>
                            <th style="width: 30%;">Patient</th>
                            <th style="width: 25%;">Scheduled Date</th>
                            <th style="width: 25%;">Purpose</th>
                            <th style="width: 20%;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($missedCheckups as $missed)
                        @php
                            $dateDisplay = '';
                            if($missed->scheduled_date) {
                                if(is_string($missed->scheduled_date)) {
                                    $dateDisplay = \Carbon\Carbon::parse($missed->scheduled_date)->format('M j, Y');
                                } else {
                                    $dateDisplay = $missed->scheduled_date->format('M j, Y');
                                }
                            }
                        @endphp
                        <tr>
                            <td>
                                <div style="font-weight:600;">{{ $missed->user->name ?? 'Unknown' }}</div>
                                <div style="font-size:0.75rem;color:var(--text-muted);">{{ $missed->user->email ?? '' }}</div>
                            </td>
                            <td>
                                <span style="color:var(--danger);font-weight:600;">{{ $dateDisplay }}</span>
                            </td>
                            <td>
                                <span style="font-size:0.8rem;">{{ Str::limit($missed->purpose ?? 'No purpose', 20) }}</span>
                            </td>
                            <td>
                                <form action="{{ route('midwife.checkups.schedule', $missed->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-warning" title="Reschedule">
                                        <i class="bi bi-calendar-event me-1"></i> Reschedule
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($stats['missed'] > 5)
            <div class="mt-2">
                <a href="{{ route('midwife.checkups.index') }}?status=missed" class="text-decoration-none">
                    <small>View all {{ $stats['missed'] }} missed checkups →</small>
                </a>
            </div>
            @endif
        </div>
        <button type="button" class="btn-close flex-shrink-0" data-bs-dismiss="alert"></button>
    </div>
</div>
@endif

{{-- ── Checkups Table ── --}}
<div class="table-card fade-in-card">

    @if($checkups->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover table-sticky-head mb-0">
            <thead>
                <tr>
                    <th>Date / Time</th>
                    <th>Patient</th>
                    <th>Purpose</th>
                    <th>Midwife</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th style="text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($checkups as $checkup)
                @php
                    $dateDisplay = $timeDisplay = '';
                    if($checkup->scheduled_date) {
                        $dateDisplay = is_string($checkup->scheduled_date)
                            ? \Carbon\Carbon::parse($checkup->scheduled_date)->format('M j, Y')
                            : $checkup->scheduled_date->format('M j, Y');
                    }
                    if($checkup->scheduled_time) {
                        $timeDisplay = \Carbon\Carbon::parse($checkup->scheduled_time)->format('g:i A');
                    }
                @endphp
                <tr>
                    <td>
                        <div style="font-weight:600;font-size:0.875rem;color:var(--text);">{{ $dateDisplay }}</div>
                        @if($timeDisplay)
                        <div style="font-size:0.75rem;color:var(--text-muted);">
                            <i class="bi bi-clock me-1"></i>{{ $timeDisplay }}
                        </div>
                        @endif
                    </td>
                    <td>
                        @if($checkup->patient_record)
                        <div style="display:flex;align-items:center;gap:0.6rem;">
                            <div class="patient-avatar">{{ strtoupper(substr($checkup->patient_name, 0, 1)) }}</div>
                            <div>
                                <div style="font-weight:700;font-size:0.875rem;color:var(--text);">{{ $checkup->patient_name }}</div>
                                <div style="font-size:0.75rem;color:var(--text-muted);">{{ $checkup->woman?->email ?? $checkup->walkInPatient?->contact_number ?? 'Walk-in woman' }}</div>
                            </div>
                        </div>
                        @else
                            <span style="color:var(--text-muted);font-size:0.875rem;">—</span>
                        @endif
                    </td>
                    <td><span class="purpose-pill">{{ Str::limit($checkup->purpose ?? 'No purpose', 22) }}</span></td>
                    <td>
                        @if($checkup->midwife)
                        <div style="font-weight:600;font-size:0.875rem;color:var(--text);">{{ $checkup->midwife->name }}</div>
                        <div style="font-size:0.75rem;color:var(--text-muted);">{{ $checkup->midwife->email }}</div>
                        @else
                            <span style="color:var(--text-muted);font-size:0.875rem;">Not assigned</span>
                        @endif
                    </td>
                    <td>
                        @if($timeDisplay)
                        <div style="font-weight:600;font-size:0.875rem;color:var(--text);">
                            <i class="bi bi-clock me-1" style="color:var(--primary-light);"></i>{{ $timeDisplay }}
                        </div>
                        @else
                        <span style="color:var(--text-muted);font-size:0.875rem;">—</span>
                        @endif
                    </td>
                    <td>
                        @switch($checkup->status)
                            @case('scheduled')
                            @case('Scheduled')   <span class="status-scheduled"><i class="bi bi-clock"></i> Scheduled</span>  @break
                            @case('completed')
                            @case('Completed')   <span class="status-completed"><i class="bi bi-check2-circle"></i> Completed</span> @break
                            @case('missed')
                            @case('Missed')      <span class="status-missed"><i class="bi bi-x-circle"></i> Missed</span>      @break
                            @case('cancelled')   <span class="status-cancelled"><i class="bi bi-dash-circle"></i> Cancelled</span> @break
                            @case('Rescheduled') <span class="ba-resched"><i class="bi bi-arrow-repeat"></i> Rescheduled</span> @break
                            @default             <span class="status-pending">{{ ucfirst($checkup->status ?? 'Unknown') }}</span>
                        @endswitch
                    </td>
                    <td>
                        <div class="d-flex gap-1 justify-content-center">
                            {{-- View --}}
                            <a href="{{ route('midwife.checkups.show', $checkup->id) }}" class="btn-action ba-view" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if(in_array($checkup->status, ['scheduled', 'Rescheduled']))
                                <a href="{{ route('midwife.checkups.edit', $checkup->id) }}" class="btn-action ba-edit" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('midwife.checkups.complete', $checkup->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn-action ba-done" title="Complete" onclick="return confirm('Mark as completed?')">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                                <form action="{{ route('midwife.checkups.miss', $checkup->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn-action ba-miss" title="Missed" onclick="return confirm('Mark as missed?')">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </form>
                                <form action="{{ route('midwife.checkups.cancel', $checkup->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn-action ba-cancel" title="Cancel" onclick="return confirm('Cancel this checkup?')">
                                        <i class="bi bi-dash-circle"></i>
                                    </button>
                                </form>
                                <form action="{{ route('midwife.checkups.destroy', $checkup->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-action ba-cancel" title="Archive" onclick="return confirm('Archive this checkup? It will be hidden but not permanently deleted.')">
                                        <i class="bi bi-archive"></i>
                                    </button>
                                </form>
                            @elseif(in_array($checkup->status, ['completed', 'missed', 'cancelled']))
                                <form action="{{ route('midwife.checkups.schedule', $checkup->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn-action ba-resched" title="Reschedule" onclick="return confirm('Reschedule this checkup?')">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                </form>
                                <form action="{{ route('midwife.checkups.destroy', $checkup->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-action ba-cancel" title="Archive" onclick="return confirm('Archive this checkup? It will be hidden but not permanently deleted.')">
                                        <i class="bi bi-archive"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center" style="padding:1.25rem;">
        {{ $checkups->links() }}
    </div>

    @else
    <div class="empty-state">
        <i class="bi bi-calendar-x empty-state-icon"></i>
        <h6>No Checkups Found</h6>
        <p>
            @if(request('search'))
                No checkups match "{{ request('search') }}".
                <a href="{{ route('midwife.checkups.index') }}">Clear search</a>
            @else
                No checkups have been scheduled yet.
            @endif
        </p>
        <a href="{{ route('midwife.checkups.create') }}" class="btn btn-primary">
            <i class="bi bi-calendar-plus-fill me-1"></i> Schedule First Checkup
        </a>
    </div>
    @endif

</div>

@endsection
