@extends('bhw.layout')

@section('title', 'Review Patient Registration - BHW Portal | ReproCare')

@push('styles')
<style>
    .patient-avatar { width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #f59e0b, #d97706); display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 800; color: #fff; }
    .info-card { background: var(--bg-input); border: 1px solid var(--border); border-radius: 12px; padding: 1.5rem; }
    .id-image-container { background: var(--bg-input); border: 1px solid var(--border); border-radius: 12px; padding: 1.5rem; }
    .id-image-preview { width: 100%; max-width: 400px; border-radius: 8px; border: 2px solid var(--border); }
</style>
@endpush

@section('bhw-content')

<div class="d-flex justify-content-between align-items-center mb-4 fade-in-card">
    <div>
        <h1 class="page-title">
            <i class="bi bi-person-lines-fill me-2" style="color:var(--warning);"></i>
            Review Patient Registration
        </h1>
        <p class="page-subtitle">Review patient information and ID images before approval</p>
    </div>
    <a href="{{ route('bhw.pending-patients') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Pending
    </a>
</div>

<div class="row fade-in-card">
    {{-- Patient Info --}}
    <div class="col-lg-6 mb-4">
        <div class="info-card">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="patient-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                <div>
                    <h5 style="font-weight: 700; margin: 0;">{{ $user->name }}</h5>
                    <span class="badge bg-warning text-dark">⏳ Pending</span>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-6">
                    <label style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Email</label>
                    <div style="font-size: 0.9rem;">{{ $user->email }}</div>
                </div>
                <div class="col-6">
                    <label style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Date of Birth</label>
                    <div style="font-size: 0.9rem;">{{ $user->date_of_birth ? $user->date_of_birth->format('F d, Y') : '—' }}</div>
                </div>
                <div class="col-12">
                    <label style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Barangay</label>
                    <div style="font-size: 0.9rem;">{{ $user->barangay ?? '—' }}</div>
                </div>
                <div class="col-12">
                    <label style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Purok</label>
                    <div style="font-size: 0.9rem;">{{ $user->purok->name ?? '—' }}</div>
                </div>
                <div class="col-12">
                    <label style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Registered</label>
                    <div style="font-size: 0.9rem;">{{ $user->created_at->format('F d, Y - g:i A') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ID Images --}}
    <div class="col-lg-6 mb-4">
        <div class="id-image-container">
            <h5 style="font-weight: 700; margin-bottom: 1rem;">
                <i class="bi bi-card-image me-2" style="color: var(--primary-light);"></i>
                Valid ID Photos
            </h5>
            
            <div class="row g-3">
                <div class="col-12">
                    <label style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Front of ID</label>
                    @if($user->id_image_front)
                        <img src="{{ asset('storage/' . $user->id_image_front) }}" alt="Front of ID" class="id-image-preview mt-2">
                    @else
                        <div class="text-muted mt-2">No front ID image uploaded</div>
                    @endif
                </div>
                <div class="col-12">
                    <label style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Back of ID</label>
                    @if($user->id_image_back)
                        <img src="{{ asset('storage/' . $user->id_image_back) }}" alt="Back of ID" class="id-image-preview mt-2">
                    @else
                        <div class="text-muted mt-2">No back ID image uploaded</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Action Buttons --}}
<div class="row fade-in-card">
    <div class="col-12">
        <div class="d-flex gap-3 justify-content-end">
            {{-- Reject --}}
            <button class="btn btn-outline-danger" type="button" data-bs-toggle="collapse" data-bs-target="#reject-form">
                <i class="bi bi-x-lg me-1"></i> Reject Registration
            </button>
            
            {{-- Approve --}}
            <form action="{{ route('bhw.approve-patient', $user->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success" onclick="return confirm('Approve {{ $user->name }}? This will allow them to log in to the system.')">
                    <i class="bi bi-check-lg me-1"></i> Approve Registration
                </button>
            </form>
        </div>

        {{-- Reject Form --}}
        <div class="collapse mt-3" id="reject-form">
            <div class="card border-danger">
                <div class="card-body">
                    <h6 class="card-title text-danger">Reject Registration</h6>
                    <form action="{{ route('bhw.reject-patient', $user->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason for Rejection</label>
                            <textarea name="reason" class="form-control" id="reason" rows="3" placeholder="Please provide a reason for rejecting this registration..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-x-circle me-1"></i> Confirm Rejection
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
