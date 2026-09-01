@extends('midwife.layout')

@section('title', 'Notifications - ReproCare')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">
                <i class="bi bi-bell"></i> Notifications
            </h1>
            <p class="page-subtitle">Manage system notifications and announcements</p>
        </div>
        <div>
            <a href="{{ route('midwife.notifications.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Create Notification
            </a>
        </div>
    </div>
</div>

<!-- Notifications List -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">All Notifications</h5>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-primary active">All</button>
                <button class="btn btn-outline-secondary">Unread</button>
                <button class="btn btn-outline-secondary">Read</button>
            </div>
        </div>
    </div>
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
                            <tr class="{{ $notification->is_read ? '' : 'table-primary' }}">
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
                                    <span class="text-truncate d-block" style="max-width: 300px;">
                                        {{ Str::limit($notification->message, 100) }}
                                    </span>
                                </td>
                                <td>
                                    @if($notification->target_role)
                                        <span class="badge bg-outline-primary">{{ ucfirst($notification->target_role) }}</span>
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
                                        @if(!$notification->is_read)
                                            <form method="POST" action="{{ route('midwife.notifications.mark-read', $notification->id) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Mark as Read">
                                                    <i class="bi bi-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('midwife.notifications.delete', $notification->id) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this notification?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
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
                <p class="text-muted small">You haven't created any notifications yet.</p>
                <a href="{{ route('midwife.notifications.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Create First Notification
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h6 class="mb-0">Quick Stats</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Total Notifications</span>
                    <strong>{{ $notifications->total() }}</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Unread</span>
                    <strong class="text-primary">{{ $notifications->where('is_read', false)->count() }}</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>Read</span>
                    <strong class="text-success">{{ $notifications->where('is_read', true)->count() }}</strong>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h6 class="mb-0">Notification Templates</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="p-3 bg-light rounded text-center">
                            <i class="bi bi-info-circle text-info fs-4 mb-2 d-block"></i>
                            <h6>Information</h6>
                            <p class="text-muted small mb-0">General announcements and updates</p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="p-3 bg-light rounded text-center">
                            <i class="bi bi-exclamation-triangle text-warning fs-4 mb-2 d-block"></i>
                            <h6>Warning</h6>
                            <p class="text-muted small mb-0">Important alerts and warnings</p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="p-3 bg-light rounded text-center">
                            <i class="bi bi-check-circle text-success fs-4 mb-2 d-block"></i>
                            <h6>Success</h6>
                            <p class="text-muted small mb-0">Positive updates and achievements</p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="p-3 bg-light rounded text-center">
                            <i class="bi bi-x-circle text-danger fs-4 mb-2 d-block"></i>
                            <h6>Error</h6>
                            <p class="text-muted small mb-0">Critical issues and errors</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
