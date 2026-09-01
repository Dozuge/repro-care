@extends('midwife.layout')

@section('title', 'Notifications - ReproCare')

@section('midwife-content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-bell"></i> Notifications</h1>
        <a href="{{ route('midwife.notifications.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Create Notification
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            @if($notifications->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Title</th>
                                <th>Message</th>
                                <th>Target</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($notifications as $notification)
                                <tr>
                                    <td>
                                        <span class="badge bg-{{ $notification->type === 'info' ? 'info' : ($notification->type === 'warning' ? 'warning' : ($notification->type === 'success' ? 'success' : 'danger')) }}">
                                            <i class="bi bi-{{ $notification->type === 'info' ? 'info-circle' : ($notification->type === 'warning' ? 'exclamation-triangle' : ($notification->type === 'success' ? 'check-circle' : 'x-circle')) }}"></i>
                                            {{ ucfirst($notification->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>{{ $notification->title }}</strong>
                                    </td>
                                    <td>
                                        {{ Str::limit($notification->message, 100) }}
                                    </td>
                                    <td>
                                        @if($notification->target_role)
                                            <span class="badge bg-primary">{{ ucfirst($notification->target_role) }}</span>
                                        @else
                                            <span class="text-muted">All</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($notification->is_read)
                                            <span class="badge bg-secondary">Read</span>
                                        @else
                                            <span class="badge bg-primary">Unread</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $notification->created_at->format('M j, Y g:i A') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('midwife.notifications.show', $notification->id) }}" class="btn btn-sm btn-primary" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if(!$notification->is_read)
                                                <form method="POST" action="{{ route('midwife.notifications.mark-read', $notification->id) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" title="Mark as Read">
                                                        <i class="bi bi-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form method="POST" action="{{ route('midwife.notifications.delete', $notification->id) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this notification?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="bg-light rounded-circle p-3 d-inline-block mb-3">
                        <i class="bi bi-bell text-muted fs-4"></i>
                    </div>
                    <h6 class="text-muted">No Notifications Found</h6>
                    <p class="text-muted">You haven't created any notifications yet.</p>
                    <a href="{{ route('midwife.notifications.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Create First Notification
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
