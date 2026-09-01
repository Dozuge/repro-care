@extends('user.layout')

@section('title', 'My Notifications - ReproCare')

@push('styles')
<style>
    .notif-wrap { max-width: 720px; margin: 0 auto; }

    .notif-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1rem 1.25rem;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
        text-decoration: none;
        color: inherit;
    }
    .notif-card:hover {
        border-color: var(--primary);
        transform: translateX(4px);
        box-shadow: var(--shadow-sm);
        color: inherit;
    }
    .notif-card.unread { border-left: 3px solid var(--primary); background: linear-gradient(90deg, var(--primary-subtle), var(--bg-card)); }
    .notif-card.type-danger  { border-left: 3px solid #ef4444; background: linear-gradient(90deg, rgba(239,68,68,0.05), var(--bg-card)); }
    .notif-card.type-warning { border-left: 3px solid #f59e0b; background: linear-gradient(90deg, rgba(245,158,11,0.05), var(--bg-card)); }
    .notif-card.type-success { border-left: 3px solid #10b981; background: linear-gradient(90deg, rgba(16,185,129,0.05), var(--bg-card)); }
    .notif-card.type-info    { border-left: 3px solid #0ea5e9; background: linear-gradient(90deg, rgba(14,165,233,0.05), var(--bg-card)); }

    .notif-icon { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }
    .notif-icon-danger  { background: rgba(239,68,68,0.12);  color: #f87171; }
    .notif-icon-warning { background: rgba(245,158,11,0.12); color: #fbbf24; }
    .notif-icon-success { background: rgba(16,185,129,0.12); color: #34d399; }
    .notif-icon-info    { background: rgba(14,165,233,0.12); color: #38bdf8; }
    .notif-icon-primary { background: var(--primary-subtle); color: var(--primary-light); }

    .notif-body { flex: 1; min-width: 0; }
    .notif-title { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.875rem; font-weight: 700; color: var(--text); margin-bottom: 0.2rem; }
    .notif-message { font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin: 0 0 0.3rem; }
    .notif-time { font-size: 0.73rem; color: var(--text-muted); display: flex; align-items: center; gap: 0.3rem; }
    .notif-badge-new { background: var(--primary); color: #fff; font-size: 0.62rem; font-weight: 700; padding: 0.12em 0.6em; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.5px; flex-shrink: 0; box-shadow: 0 2px 8px var(--primary-glow); margin-top: 2px; }
    .type-badge { font-size: 0.68rem; font-weight: 700; padding: 0.15em 0.6em; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.5px; }
    .type-badge-danger  { background: rgba(239,68,68,0.15);  color: #f87171; }
    .type-badge-warning { background: rgba(245,158,11,0.15); color: #fbbf24; }
    .type-badge-success { background: rgba(16,185,129,0.15); color: #34d399; }
    .type-badge-info    { background: rgba(14,165,233,0.15); color: #38bdf8; }
</style>
@endpush

@section('user-content')

<div class="notif-wrap fade-in-card">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">
                <i class="bi bi-bell-fill me-2" style="color:var(--primary-light);"></i>
                Notifications
            </h1>
            <p class="page-subtitle">Your alerts, reminders, and health updates</p>
        </div>
        @if($notifications->count() > 0)
            <span style="background:var(--primary-subtle);border:1px solid var(--border-glass);color:var(--primary-light);font-size:0.8rem;font-weight:700;padding:0.35em 1em;border-radius:20px;">
                {{ $notifications->count() }} total
            </span>
        @endif
    </div>

    {{-- List --}}
    @if($notifications->count() > 0)
    @foreach($notifications as $notification)
        @php
            $type      = $notification->type ?? 'info';
            $iconMap   = ['danger' => 'bi-exclamation-circle-fill', 'warning' => 'bi-alarm-fill', 'success' => 'bi-check-circle-fill', 'info' => 'bi-bell-fill'];
            $iconClass = 'notif-icon-' . $type;
            $iconName  = $iconMap[$type] ?? 'bi-bell-fill';
        @endphp
        @php $hasAction = !empty($notification->action_url); @endphp
        <div class="notif-card type-{{ $type }} {{ !$notification->is_read ? 'unread' : '' }} fade-in-card">
            <div class="notif-icon {{ $iconClass }}">
                <i class="bi {{ $iconName }}"></i>
            </div>
            <div class="notif-body">
                @if($notification->title)
                    <div class="notif-title">{{ $notification->title }}</div>
                @endif
                <p class="notif-message">{{ $notification->message }}</p>
                <div class="d-flex align-items-center gap-2 mt-1">
                    <span class="type-badge type-badge-{{ $type }}">{{ ucfirst($type) }}</span>
                    <span class="notif-time">
                        <i class="bi bi-clock"></i>
                        {{ $notification->created_at->diffForHumans() }}
                        @if($notification->read_at)
                            · Read {{ $notification->read_at->diffForHumans() }}
                        @endif
                    </span>
                </div>
            </div>
            <div class="d-flex flex-column align-items-end gap-1">
                @if(!$notification->is_read)
                    <span class="notif-badge-new">New</span>
                @endif
                @if($hasAction)
                    <a href="{{ $notification->action_url }}" class="btn btn-sm" style="font-size:0.72rem;padding:0.2rem 0.6rem;border:1px solid var(--border);border-radius:8px;color:var(--primary-light);background:var(--primary-subtle);">
                        View <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                @endif
            </div>
        </div>
    @endforeach

    @else
        {{-- Empty State --}}
        <div class="card">
            <div class="empty-state">
                <i class="bi bi-bell-slash empty-state-icon"></i>
                <h6>All Caught Up!</h6>
                <p>No notifications right now. New alerts and reminders will appear here.</p>
            </div>
        </div>
    @endif

</div>
@endsection
