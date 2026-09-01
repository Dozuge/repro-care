@extends('midwife.layout')

@section('title', 'Pending Women Registrations - Midwife Portal | ReproCare')

@push('styles')
<style>
    .pending-badge { background: rgba(245,158,11,0.15); border: 1px solid rgba(245,158,11,0.4); color: #f59e0b; padding: 0.25em 0.7em; border-radius: 20px; font-size: 0.75rem; font-weight: 700; }
    .patient-avatar { width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--accent-violet)); display: flex; align-items: center; justify-content: center; font-size: 0.9rem; font-weight: 800; color: #fff; flex-shrink: 0; }
    .search-bar {
        display: flex; gap: 0.5rem; align-items: center;
        background: var(--bg-card2); border: 1px solid var(--border);
        border-radius: 12px; padding: 0.4rem 0.6rem; min-width: 260px;
    }
    .search-bar input {
        border: none; background: transparent; color: var(--text);
        font-size: 0.875rem; outline: none; flex: 1; min-width: 0;
    }
    .search-bar button {
        border: none; background: transparent; color: var(--text-muted);
        cursor: pointer; padding: 0.2rem 0.35rem; border-radius: 8px;
    }
</style>
@endpush

@section('midwife-content')

{{-- ═══════════════════════════════
     PAGE HERO
═══════════════════════════════ --}}
<div class="page-hero fade-in-card mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3" style="position:relative;z-index:1;">
        <div>
            <div class="page-hero-title">
                <i class="bi bi-person-check-fill me-2"></i>Pending Registrations
            </div>
            <p class="page-hero-subtitle">
                <i class="bi bi-shield-check me-1"></i>Review and verify new women accounts before they can log in.
            </p>
        </div>
        <div class="d-flex gap-2">
            <form method="GET" action="{{ route('midwife.pending-patients') }}" class="d-flex">
                <div class="search-bar">
                    <i class="bi bi-search" style="color:var(--text-muted);"></i>
                    <input type="search" name="search" placeholder="Search pending women..." value="{{ request('search') }}">
                </div>
            </form>
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

{{-- ═══════════════════════════════
     PENDING REGISTRATIONS TABLE
═══════════════════════════════ --}}
<div class="card fade-in-card" style="border:none; background:var(--bg-card);">
    @if($pending->count() > 0)
        <div class="card-header d-flex justify-content-between align-items-center" 
             style="background:transparent; border-bottom:1px solid var(--border-color); padding:1rem 1.5rem;">
            <h5 class="mb-0 fw-700" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--text);">
                <i class="bi bi-people-fill me-2" style="color:var(--warning);"></i>
                Pending Women <span style="color:var(--warning);">({{ $pending->total() }})</span>
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:0.9rem;">
                    <thead>
                        <tr style="color:var(--text-muted); font-weight:600; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.5px;">
                            <th style="padding:1rem; border:none;">Woman</th>
                            <th style="padding:1rem; border:none;">Email</th>
                            <th style="padding:1rem; border:none;">Contact</th>
                            <th style="padding:1rem; border:none;">Barangay</th>
                            <th style="padding:1rem; border:none;">Registered</th>
                            <th style="padding:1rem; border:none; text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pending as $woman)
                        <tr style="border-bottom:1px solid var(--border-color);">
                            <td style="padding:1rem;">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,var(--warning),var(--accent-pink));display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <span style="font-weight:700;font-size:0.9rem;color:#fff;">{{ strtoupper(substr($woman->name, 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <div style="font-weight:600; color:var(--text);">{{ $woman->name }}</div>
                                        <div style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.2rem 0.5rem; background:rgba(245,158,11,0.1); color:var(--warning); border-radius:20px; font-weight:500; font-size:0.75rem;">
                                            <i class="bi bi-clock"></i> Pending
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding:1rem; color:var(--text-muted);">{{ $woman->email }}</td>
                            <td style="padding:1rem; color:var(--text);">{{ $woman->phone ?? '—' }}</td>
                            <td style="padding:1rem; color:var(--text);">{{ $woman->barangay ?? '—' }}</td>
                            <td style="padding:1rem; color:var(--text-muted);">{{ $woman->created_at->diffForHumans() }}</td>
                            <td style="padding:1rem; text-align:right;">
                                <div class="d-flex gap-2 justify-content-end">
                                    <form action="{{ route('midwife.approve-patient', $woman->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm"
                                            style="background:linear-gradient(135deg,var(--success),var(--primary)); color:#fff; border:none; border-radius:8px;"
                                            onclick="return confirm('Approve {{ $woman->name }}?')">
                                            <i class="bi bi-check-lg"></i> Approve
                                        </button>
                                    </form>
                                    <button class="btn btn-sm" type="button"
                                        style="background:transparent; border:1px solid var(--danger); color:var(--danger); border-radius:8px;"
                                        data-bs-toggle="collapse" data-bs-target="#reject-{{ $woman->id }}">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                                <div class="collapse mt-2" id="reject-{{ $woman->id }}">
                                    <form action="{{ route('midwife.reject-patient', $woman->id) }}" method="POST">
                                        @csrf
                                        <textarea name="reason" class="form-control form-control-sm mb-2"
                                            placeholder="Reason for rejection (optional)" rows="2"
                                            style="background:var(--bg-input); border:1px solid var(--input-border); color:var(--text); border-radius:8px;"></textarea>
                                        <button type="submit" class="btn btn-sm w-100"
                                            style="background:var(--danger); color:#fff; border:none; border-radius:8px;">
                                            Confirm Reject
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer" style="background:transparent; border-top:1px solid var(--border-color); padding:1rem 1.5rem;">
            <div class="d-flex justify-content-center">
                {{ $pending->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @else
        <div class="card-body p-5">
            <div class="text-center py-5">
                <div style="width:80px;height:80px;border-radius:20px;background:linear-gradient(135deg,var(--success),var(--primary));display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;">
                    <i class="bi bi-person-check" style="font-size:2rem;color:#fff;"></i>
                </div>
                <h5 style="font-weight:600; color:var(--text); margin-bottom:0.75rem;">No Pending Registrations</h5>
                <p style="color:var(--text-muted); margin-bottom:1.5rem; max-width:400px; margin-left:auto; margin-right:auto;">
                    @if(request('search'))
                        No pending women matched your search.
                    @else
                        All woman registrations have already been reviewed and approved.
                    @endif
                </p>
                @if(request('search'))
                    <a href="{{ route('midwife.pending-patients') }}" class="btn"
                       style="background:linear-gradient(135deg,var(--primary),var(--accent-violet)); color:#fff; border:none; border-radius:10px; padding:0.6rem 1.5rem;">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Clear Search
                    </a>
                @else
                    <a href="{{ route('midwife.patients') }}" class="btn"
                       style="background:linear-gradient(135deg,var(--success),var(--primary)); color:#fff; border:none; border-radius:10px; padding:0.6rem 1.5rem;">
                        <i class="bi bi-people me-1"></i>View All Women
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>

@endsection
