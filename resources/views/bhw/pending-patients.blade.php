@extends('bhw.layout')

@section('title', 'Pending Women Registrations - BHW Portal | ReproCare')

@push('styles')
<style>
    .pending-badge { background: rgba(245,158,11,0.15); border: 1px solid rgba(245,158,11,0.4); color: #f59e0b; padding: 0.25em 0.7em; border-radius: 20px; font-size: 0.75rem; font-weight: 700; }
    .patient-avatar { width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #f59e0b, #d97706); display: flex; align-items: center; justify-content: center; font-size: 0.9rem; font-weight: 800; color: #fff; flex-shrink: 0; }
</style>
@endpush

@section('bhw-content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 fade-in-card flex-wrap gap-3">
        <div>
            <h1 class="page-title">
                <i class="bi bi-person-check-fill me-2" style="color:var(--warning);"></i>
                Pending Registrations
            </h1>
            <p class="page-subtitle">Review account requests and keep approval actions in one clean workspace.</p>
        </div>
        <a href="{{ route('bhw.patients') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Women
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4 fade-in-card">
            <i class="bi bi-check-circle-fill flex-shrink-0"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="table-card fade-in-card">
        @if($pending->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Woman</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>Location</th>
                            <th>Registered</th>
                            <th style="text-align:center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pending as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="patient-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                        <div>
                                            <div style="font-weight:700;font-size:0.875rem;">{{ $user->name }}</div>
                                            <span class="pending-badge">Pending</span>
                                        </div>
                                    </div>
                                </td>
                                <td style="font-size:0.875rem;color:var(--text-muted);">{{ $user->email }}</td>
                                <td style="font-size:0.875rem;">{{ $user->contact_number ?? '-' }}</td>
                                <td style="font-size:0.875rem;">
                                    <div>{{ $user->barangay ?? '-' }}</div>
                                    <small class="text-muted">{{ $user->purok?->name ?? 'Purok not set' }}</small>
                                </td>
                                <td style="font-size:0.8rem;color:var(--text-muted);">{{ $user->created_at->diffForHumans() }}</td>
                                <td>
                                    <div class="d-flex flex-column gap-2 align-items-center">
                                        <a href="{{ route('bhw.review-patient', $user->id) }}" class="btn btn-sm btn-primary w-100">
                                            <i class="bi bi-eye me-1"></i> Review
                                        </a>

                                        <form action="{{ route('bhw.approve-patient', $user->id) }}" method="POST" class="w-100">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success w-100" onclick="return confirm('Approve {{ $user->name }}?')">
                                                <i class="bi bi-check-lg me-1"></i> Approve
                                            </button>
                                        </form>

                                        <div style="width:100%;">
                                            <button class="btn btn-sm btn-outline-danger w-100" type="button" data-bs-toggle="collapse" data-bs-target="#reject-{{ $user->id }}">
                                                <i class="bi bi-x-lg me-1"></i> Reject
                                            </button>
                                            <div class="collapse mt-2" id="reject-{{ $user->id }}">
                                                <form action="{{ route('bhw.reject-patient', $user->id) }}" method="POST">
                                                    @csrf
                                                    <textarea name="reason" class="form-control form-control-sm mb-2" placeholder="Reason for rejection (optional)" rows="2"></textarea>
                                                    <button type="submit" class="btn btn-sm btn-danger w-100">Confirm Reject</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center" style="padding:1.25rem;">
                {{ $pending->links('pagination::bootstrap-5') }}
            </div>
        @else
            <div class="empty-state">
                <i class="bi bi-person-check empty-state-icon"></i>
                <h6>No pending registrations</h6>
                <p>All patient registrations have already been reviewed.</p>
            </div>
        @endif
    </div>
</div>
@endsection
