@extends('midwife.layout')

@section('title', 'BHW President Details - ReproCare')

@section('midwife-content')

{{-- ═══════════════════════════════
     PAGE HERO
════════════════════════════════ --}}
<div class="page-hero fade-in-card mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3" style="position:relative;z-index:1;">
        <div>
            <div class="page-hero-title">
                <i class="bi bi-person-badge-fill me-2"></i>BHW President Details
            </div>
            <p class="page-hero-subtitle">
                <i class="bi bi-calendar3 me-1"></i>{{ now()->format('l, F j, Y') }}
                &nbsp;·&nbsp; View detailed information and statistics
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('midwife.bhw-presidents.edit', $bhwPresident->id) }}" class="btn-hero-secondary">
                <i class="bi bi-pencil-fill me-1"></i> Edit President
            </a>
            <a href="{{ route('midwife.bhw-presidents.index') }}" class="btn-hero-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" 
         style="background:rgba(25,135,84,0.1); border:1px solid rgba(25,135,84,0.3); color:var(--success); border-radius:10px;">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" style="filter:invert(1);"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert"
         style="background:rgba(220,53,69,0.1); border:1px solid rgba(220,53,69,0.3); color:var(--danger); border-radius:10px;">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $errors->first() }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" style="filter:invert(1);"></button>
    </div>
@endif

{{-- ═══════════════════════════════
     PROFILE & STATISTICS
════════════════════════════════ --}}
<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-4">
        <div class="card fade-in-card" style="border:none; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.08);">
            <div class="card-body p-4 text-center">
                @if(!empty($bhwPresident->profile_image_url))
                    <img src="{{ $bhwPresident->profile_image_url }}" alt="{{ $bhwPresident->name }}"
                         class="rounded-circle mb-3" style="width:100px; height:100px; object-fit:cover; border:3px solid var(--primary);">
                @else
                    <div class="rounded-circle mb-3 d-flex align-items-center justify-content-center mx-auto"
                         style="width:100px; height:100px; background:linear-gradient(135deg, var(--primary), var(--accent-violet)); color:#fff; font-size:2.5rem; font-weight:800;">
                        {{ strtoupper(substr($bhwPresident->name, 0, 1)) }}
                    </div>
                @endif
                <h5 class="mb-1" style="font-weight:800;">{{ $bhwPresident->name }}</h5>
                <p class="text-muted mb-2">{{ $bhwPresident->email }}</p>
                <p class="text-muted mb-3">{{ $bhwPresident->contact_number }}</p>
                <hr class="my-3" style="border-color:var(--border);">
                <div class="text-start">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Age:</span>
                        <span class="fw-bold">{{ $bhwPresident->age ?? 'N/A' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Gender:</span>
                        <span class="fw-bold">{{ ucfirst($bhwPresident->gender) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Barangay:</span>
                        <span class="fw-bold">{{ $bhwPresident->barangay ?? 'Not assigned' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Status:</span>
                        <span class="badge bg-success">{{ ucfirst($bhwPresident->status) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-8">
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <div class="stat-card stat-blue fade-in-card">
                    <i class="bi bi-diagram-3-fill stat-icon"></i>
                    <div class="stat-label">BHWs Managed</div>
                    <div class="stat-number" data-count="{{ $stats['managed_bhw_count'] }}">{{ $stats['managed_bhw_count'] }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card stat-purple fade-in-card">
                    <i class="bi bi-people-fill stat-icon"></i>
                    <div class="stat-label">Active Pregnancies</div>
                    <div class="stat-number" data-count="{{ $stats['active_pregnancies_count'] }}">{{ $stats['active_pregnancies_count'] }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card stat-amber fade-in-card">
                    <i class="bi bi-calendar-check-fill stat-icon"></i>
                    <div class="stat-label">Scheduled Checkups</div>
                    <div class="stat-number" data-count="{{ $stats['scheduled_checkups'] }}">{{ $stats['scheduled_checkups'] }}</div>
                </div>
            </div>
        </div>

        <div class="card fade-in-card mt-3" style="border:none; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.08);">
            <div class="card-body p-4">
                <h6 class="mb-3" style="font-weight:800;"><i class="bi bi-geo-alt-fill me-2"></i>Barangay</h6>
                <p class="mb-0">{{ $bhwPresident->barangay ?? 'Burgos' }}</p>
                <p class="mb-0 text-muted">Automatically assigned barangay</p>
            </div>
        </div>

        <div class="card fade-in-card mt-3" style="border:none; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.08);">
            <div class="card-body p-4">
                <h6 class="mb-3" style="font-weight:800;"><i class="bi bi-shield-exclamation me-2"></i>Account Actions</h6>
                @if(($bhwPresident->status ?? 'approved') === 'approved')
                    <div class="alert alert-warning mb-0">
                        Active BHW Presidents cannot be deleted.
                    </div>
                @else
                    <form method="POST" action="{{ route('midwife.bhw-presidents.destroy', $bhwPresident->id) }}" onsubmit="return confirm('Delete this BHW President permanently? This cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="bi bi-trash me-1"></i> Delete BHW President
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════
     RECENT CHECKUPS
════════════════════════════════ --}}
@if($recentCheckups->count() > 0)
<div class="card fade-in-card mb-4" style="border:none; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.08);">
    <div class="card-body p-4">
        <h6 class="mb-3" style="font-weight:800;"><i class="bi bi-calendar-check me-2"></i>Recent Checkups</h6>
        <div class="table-responsive">
            <table class="table table-hover" style="margin:0;">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Date</th>
                        <th>Purpose</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentCheckups as $checkup)
                        <tr>
                            <td>{{ $checkup->woman->name ?? 'N/A' }}</td>
                            <td>{{ $checkup->scheduled_date->format('M d, Y') }}</td>
                            <td>{{ $checkup->purpose }}</td>
                            <td>
                                <span class="badge 
                                    @if($checkup->status == 'Completed') bg-success
                                    @elseif($checkup->status == 'Scheduled') bg-primary
                                    @elseif($checkup->status == 'Missed') bg-danger
                                    @else bg-secondary @endif">
                                    {{ $checkup->status }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

@endsection
