@extends('midwife.layout')

@section('title', 'Notification Details - ReproCare')

@section('midwife-content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="mb-1"><i class="bi bi-bell me-2"></i>Notification Details</h1>
            <p class="text-muted mb-0">Review the full notification content and destination.</p>
        </div>
        <a href="{{ route('midwife.notifications.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Notifications
        </a>
    </div>

    <div class="card">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
                <span class="badge bg-{{ $notification->type === 'info' ? 'info' : ($notification->type === 'warning' ? 'warning' : ($notification->type === 'success' ? 'success' : 'danger')) }}">
                    {{ ucfirst($notification->type) }}
                </span>
                <span class="badge bg-{{ $notification->is_read ? 'secondary' : 'primary' }}">
                    {{ $notification->is_read ? 'Read' : 'Unread' }}
                </span>
                @if($notification->target_role)
                    <span class="badge bg-dark">{{ ucfirst($notification->target_role) }}</span>
                @endif
            </div>

            <h3 class="mb-2">{{ $notification->title }}</h3>
            <div class="text-muted mb-4">{{ $notification->created_at->format('F j, Y g:i A') }}</div>

            <div class="border rounded p-3 mb-4" style="white-space: pre-line;">
                {{ $notification->message }}
            </div>

            <div class="d-flex gap-2 flex-wrap">
                @if($notification->action_url)
                    <a href="{{ $notification->action_url }}" class="btn btn-primary">
                        <i class="bi bi-box-arrow-up-right me-1"></i>Open Linked Page
                    </a>
                @endif
                <a href="{{ route('midwife.notifications.index') }}" class="btn btn-outline-secondary">Back</a>
            </div>
        </div>
    </div>
</div>
@endsection
