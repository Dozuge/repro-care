@extends('bhw-president.layout')

@section('title', 'Pending Registrations - BHW President Portal | ReproCare')

@push('styles')
<style>
    .pending-search {
        display: flex;
        gap: 0.5rem;
        align-items: center;
        background: var(--bg-card2);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 0.4rem 0.75rem;
        min-width: 260px;
    }
    .pending-search input {
        border: none;
        background: transparent;
        color: var(--text);
        outline: none;
        width: 100%;
    }
</style>
@endpush

@section('bhw-president-content')
<div class="page-hero fade-in-card mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3" style="position:relative;z-index:1;">
        <div>
            <div class="page-hero-title"><i class="bi bi-person-check-fill me-2"></i>Pending Registrations</div>
            <p class="page-hero-subtitle">Review and verify patient accounts before they can access ReproCare.</p>
        </div>
        <form method="GET" action="{{ route('bhw-president.pending-patients') }}" class="d-flex">
            <div class="pending-search">
                <i class="bi bi-search" style="color:var(--text-muted);"></i>
                <input type="search" name="search" placeholder="Search pending patients..." value="{{ request('search') }}">
            </div>
        </form>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card fade-in-card">
    @if($pending->count() > 0)
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-people-fill me-2"></i>Pending Patients ({{ $pending->total() }})</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="px-4 py-3">Patient</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Contact</th>
                            <th class="px-4 py-3">Barangay</th>
                            <th class="px-4 py-3">Registered</th>
                            <th class="px-4 py-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pending as $woman)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="width:42px;height:42px;border-radius:10px;background:linear-gradient(135deg,var(--warning),var(--accent-pink));display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;">
                                            {{ strtoupper(substr($woman->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $woman->name }}</div>
                                            <div class="small text-warning">Pending verification</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">{{ $woman->email }}</td>
                                <td class="px-4 py-3">{{ $woman->contact_number ?? $woman->phone ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $woman->barangay ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $woman->created_at->format('M j, Y g:i A') }}</td>
                                <td class="px-4 py-3 text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <form action="{{ route('bhw-president.approve-patient', $woman->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve {{ $woman->name }}?')">
                                                <i class="bi bi-check-lg me-1"></i>Approve
                                            </button>
                                        </form>
                                        <button class="btn btn-sm btn-outline-danger" type="button" data-bs-toggle="collapse" data-bs-target="#reject-{{ $woman->id }}">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                    <div class="collapse mt-2" id="reject-{{ $woman->id }}">
                                        <form action="{{ route('bhw-president.reject-patient', $woman->id) }}" method="POST">
                                            @csrf
                                            <textarea name="reason" class="form-control form-control-sm mb-2" rows="2" placeholder="Reason for rejection (optional)"></textarea>
                                            <button type="submit" class="btn btn-sm btn-danger w-100">Confirm Reject</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-transparent">
            <div class="d-flex justify-content-center">
                {{ $pending->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @else
        <div class="card-body py-5 text-center">
            <i class="bi bi-person-check" style="font-size:2.5rem;color:var(--text-muted);"></i>
            <h5 class="mt-3">No Pending Registrations</h5>
            <p class="text-muted mb-0">
                @if(request('search'))
                    No pending patients matched your search.
                @else
                    All pending patient accounts have already been reviewed.
                @endif
            </p>
        </div>
    @endif
</div>
@endsection
