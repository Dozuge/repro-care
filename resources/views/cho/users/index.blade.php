@extends('cho.layout')

@section('title', 'User Management - CHO Portal | ReproCare')

@section('cho-content')

<div class="page-hero fade-in-card mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <div class="page-hero-title">
                <i class="bi bi-people-fill me-2" style="color:var(--primary-light);"></i>User Management
            </div>
            <p class="page-hero-subtitle">
                Review, approve, and manage administrative and healthcare worker accounts.
            </p>
        </div>
        <a href="{{ route('cho.users.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus-fill me-1"></i> Register New Staff
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 12px;">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Filters Card --}}
<div class="card fade-in-card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('cho.users.index') }}" class="row g-3">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0" style="color: var(--text-muted);">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0" value="{{ $search }}" placeholder="Search by name, email...">
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0" style="color: var(--text-muted);">
                        <i class="bi bi-funnel"></i>
                    </span>
                    <select name="role" class="form-select border-start-0">
                        <option value="all" {{ $role === 'all' ? 'selected' : '' }}>All Roles</option>
                        <option value="rhu" {{ $role === 'rhu' ? 'selected' : '' }}>RHU Admin</option>
                        <option value="midwife" {{ $role === 'midwife' ? 'selected' : '' }}>Midwife</option>
                        <option value="bhw_president" {{ $role === 'bhw_president' ? 'selected' : '' }}>BHW President</option>
                        <option value="bhw" {{ $role === 'bhw' ? 'selected' : '' }}>BHW</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 d-grid">
                <button type="submit" class="btn btn-primary">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Users List Card --}}
<div class="card fade-in-card">
    <div class="card-body p-0">
        @if($users->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Full Name</th>
                            <th>Email Address</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Contact</th>
                            <th class="pe-4 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $u)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $u->profile_image_url }}"
                                             alt="{{ $u->name }}"
                                             class="rounded-circle"
                                             style="width: 38px; height: 38px; object-fit: cover;"
                                             onerror="this.onerror=null;this.src='{{ $u->gender === 'male' ? '/images/avatars/avatar-male.svg' : '/images/avatars/avatar-female.svg' }}';">
                                        <div>
                                            <span class="fw-700" style="color: var(--text);">{{ $u->name }}</span>
                                            <div style="font-size:0.75rem; color:var(--text-muted);">Registered {{ $u->created_at->format('M j, Y') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span style="font-size:0.875rem;">{{ $u->email }}</span>
                                </td>
                                <td>
                                    @php
                                        $badgeColor = match($u->role) {
                                            'rhu' => 'indigo',
                                            'midwife' => 'purple',
                                            'bhw_president' => 'violet',
                                            'bhw' => 'info',
                                            default => 'secondary'
                                        };
                                        $roleLabel = match($u->role) {
                                            'rhu' => 'RHU Admin',
                                            'midwife' => 'Midwife',
                                            'bhw_president' => 'BHW President',
                                            'bhw' => 'BHW',
                                            default => ucfirst($u->role)
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $badgeColor }} text-white text-xs">
                                        {{ $roleLabel }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $u->status === 'approved' ? 'success' : ($u->status === 'suspended' ? 'danger' : 'warning') }} text-white text-xs">
                                        {{ ucfirst($u->status) }}
                                    </span>
                                </td>
                                <td style="font-size:0.875rem;">
                                    {{ $u->contact_number ?? 'N/A' }}
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('cho.users.show', $u->id) }}" class="btn btn-xs btn-outline-primary" style="font-size:0.75rem;">
                                            <i class="bi bi-eye"></i> Details
                                        </a>

                                        @if($u->status === 'pending')
                                            <form method="POST" action="{{ route('cho.users.approve', $u->id) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-xs btn-success text-white" style="font-size:0.75rem;">
                                                    <i class="bi bi-check-lg"></i> Approve
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('cho.users.reject', $u->id) }}" class="d-inline" onsubmit="return confirm('Reject and archive this user\'s account? The record will be retained but hidden.');">
                                                @csrf
                                                <button type="submit" class="btn btn-xs btn-danger text-white" style="font-size:0.75rem;">
                                                    <i class="bi bi-archive"></i> Reject
                                                </button>
                                            </form>
                                        @elseif($u->status === 'approved')
                                            <form method="POST" action="{{ route('cho.users.deactivate', $u->id) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to suspend this account?');">
                                                @csrf
                                                <button type="submit" class="btn btn-xs btn-outline-danger" style="font-size:0.75rem;">
                                                    <i class="bi bi-person-x"></i> Suspend
                                                </button>
                                            </form>
                                        @elseif($u->status === 'suspended')
                                            <form method="POST" action="{{ route('cho.users.activate', $u->id) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-xs btn-outline-success" style="font-size:0.75rem;">
                                                    <i class="bi bi-person-check"></i> Activate
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
            
            @if($users->hasPages())
                <div class="card-footer bg-transparent border-top">
                    {{ $users->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="bi bi-people" style="font-size: 3rem; color: var(--text-muted);"></i>
                <h5 class="mt-3">No Staff Accounts Found</h5>
                <p class="text-muted text-xs">Try adjusting your filters or search query.</p>
            </div>
        @endif
    </div>
</div>

@endsection
