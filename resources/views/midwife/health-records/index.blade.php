@extends('midwife.layout')

@section('title', 'Health Records - ReproCare')

@section('midwife-content')

{{-- ═══════════════════════════════
     PAGE HERO
═══════════════════════════════ --}}
<div class="page-hero fade-in-card mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3" style="position:relative;z-index:1;">
        <div>
            <div class="page-hero-title">
                <i class="bi bi-clipboard-pulse-fill me-2"></i>Health Records
            </div>
            <p class="page-hero-subtitle">
                <i class="bi bi-calendar3 me-1"></i>{{ now()->format('l, F j, Y') }}
                &nbsp;·&nbsp; Manage and monitor patient health data
            </p>
        </div>
        <div class="d-flex gap-2">
            <form method="GET" action="{{ route('midwife.health-records.index') }}" class="d-flex gap-2 align-items-center flex-wrap">
                <div class="search-bar" style="display:flex; gap:0.5rem; align-items:center; background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.3); border-radius:12px; padding:0.5rem 0.8rem; min-width:260px; backdrop-filter:blur(10px);">
                    <i class="bi bi-search" style="color:rgba(255,255,255,0.8);"></i>
                    <input type="search" name="search" placeholder="Search records..." value="{{ request('search') }}" 
                           style="border:none; background:transparent; color:#fff; font-size:0.875rem; outline:none; flex:1; min-width:0;">
                </div>
                <select name="risk_level" class="form-select" style="width:160px; border-radius:12px;">
                    <option value="all" {{ $riskLevel === 'all' ? 'selected' : '' }}>All Risk Levels</option>
                    <option value="Low" {{ $riskLevel === 'Low' ? 'selected' : '' }}>Low Risk</option>
                    <option value="Medium" {{ $riskLevel === 'Medium' ? 'selected' : '' }}>Medium Risk</option>
                    <option value="High" {{ $riskLevel === 'High' ? 'selected' : '' }}>High Risk</option>
                </select>
                <button type="submit" class="btn-hero-secondary"><i class="bi bi-funnel me-1"></i>Apply</button>
                @if(request('search') || request('risk_level', 'all') !== 'all')
                    <a href="{{ route('midwife.health-records.index') }}" class="btn-hero-secondary"><i class="bi bi-x-lg me-1"></i>Clear</a>
                @endif
            </form>
            <a href="{{ route('midwife.health-records.archived') }}" class="btn-hero-secondary">
                <i class="bi bi-archive-fill me-1"></i> Archived Records
            </a>
            <a href="{{ route('midwife.health-records.create') }}" class="btn-hero-primary">
                <i class="bi bi-plus-circle-fill me-1"></i> Add Health Record
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

    {{-- ═══════════════════════════════
         STAT CARDS
    ════════════════════════════════ --}}
    <div class="row g-3 mb-4">
        <div class="col">
            <div class="stat-card stat-purple fade-in-card">
                <i class="bi bi-clipboard-data stat-icon"></i>
                <div class="stat-label">Total Records</div>
                <div class="stat-number" data-count="{{ $healthRecords->total() }}">{{ $healthRecords->total() }}</div>
            </div>
        </div>
        <div class="col">
            <div class="stat-card stat-green fade-in-card">
                <i class="bi bi-shield-check stat-icon"></i>
                <div class="stat-label">Low Risk</div>
                <div class="stat-number" data-count="{{ $healthRecords->where('risk_level', 'Low')->count() }}">{{ $healthRecords->where('risk_level', 'Low')->count() }}</div>
            </div>
        </div>
        <div class="col">
            <div class="stat-card stat-amber fade-in-card">
                <i class="bi bi-exclamation-circle stat-icon"></i>
                <div class="stat-label">Medium Risk</div>
                <div class="stat-number" data-count="{{ $healthRecords->where('risk_level', 'Medium')->count() }}">{{ $healthRecords->where('risk_level', 'Medium')->count() }}</div>
            </div>
        </div>
        <div class="col">
            <div class="stat-card stat-danger fade-in-card">
                <i class="bi bi-exclamation-triangle stat-icon"></i>
                <div class="stat-label">High Risk</div>
                <div class="stat-number" data-count="{{ $healthRecords->where('risk_level', 'High')->count() }}">{{ $healthRecords->where('risk_level', 'High')->count() }}</div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════
         HEALTH RECORDS TABLE
    ════════════════════════════════ --}}
    <div class="card fade-in-card" style="border:none; background:var(--bg-card);">
        <div class="card-header d-flex justify-content-between align-items-center" 
             style="background:transparent; border-bottom:1px solid var(--border-color); padding:1rem 1.5rem;">
            <h5 class="mb-0 fw-700" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--text);">
                <i class="bi bi-clipboard-pulse-fill me-2" style="color:var(--primary);"></i>
                All Health Records
            </h5>
            <span style="background:rgba(155,54,255,0.1); color:var(--primary); padding:0.3rem 0.8rem; border-radius:20px; font-size:0.8rem; font-weight:500;">
                {{ $healthRecords->total() }} Records
            </span>
        </div>
        <div class="card-body p-0">
            @if($healthRecords->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size:0.9rem;">
                        <thead>
                            <tr style="color:var(--text-muted); font-weight:600; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.5px; background:transparent;">
                                <th style="padding:1rem 1.5rem; border:none;">Date</th>
                                <th style="padding:1rem; border:none;">Patient</th>
                                <th style="padding:1rem; border:none;">Vital Signs</th>
                                <th style="padding:1rem; border:none;">Risk Level</th>
                                <th style="padding:1rem; border:none;">Recorded By</th>
                                <th style="padding:1rem 1.5rem; border:none; text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($healthRecords as $record)
                                @php
                                    $patient = $record->woman ?? $record->walkInPatient;
                                    $isWalkIn = $record->walkInPatient !== null;
                                    $patientName = $isWalkIn ? ($patient->full_name ?? 'Unknown') : ($patient->name ?? 'Unknown');
                                    $patientEmail = $isWalkIn ? ($patient->contact_number ?? 'N/A') : ($patient->email ?? 'N/A');
                                    $patientImage = !$isWalkIn && !empty($patient?->profile_image_url)
                                        ? $patient->profile_image_url
                                        : null;
                                    $recordedByName = $record->recordedBy?->name ?? 'Unknown';
                                @endphp
                                <tr style="border-bottom:1px solid var(--border-color);">
                                    <td style="padding:1rem 1.5rem;">
                                        <div style="font-weight:600; color:var(--text);">{{ $record->created_at->format('M j, Y') }}</div>
                                        <small style="color:var(--text-muted);">{{ $record->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td style="padding:1rem;">
                                        <div class="d-flex align-items-center gap-2">
                                            @if($patientImage)
                                                <img src="{{ $patientImage }}" alt="{{ $patientName }}" style="width:40px;height:40px;border-radius:10px;object-fit:cover;border:1px solid var(--border);flex-shrink:0;">
                                            @else
                                                <div style="width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,var(--primary),var(--accent-violet));display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                                    <span style="font-weight:700;font-size:0.8rem;color:#fff;">{{ strtoupper(substr($patientName, 0, 1)) }}</span>
                                                </div>
                                            @endif
                                            <div>
                                                <div style="font-weight:600; color:var(--text);">
                                                    {{ $patientName }}
                                                    @if($isWalkIn)
                                                        <span class="badge bg-info ms-1" style="font-size:0.7rem;">Walk-in</span>
                                                    @endif
                                                </div>
                                                <small style="color:var(--text-muted);">{{ $patientEmail }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="padding:1rem; color:var(--text-muted);">
                                        <div style="font-size:0.85rem; line-height:1.6;">
                                            <div><strong style="color:var(--text);">BP:</strong> {{ $record->bp ?? 'N/A' }}</div>
                                            <div><strong style="color:var(--text);">HR:</strong> {{ $record->heart_rate ? $record->heart_rate . ' bpm' : 'N/A' }}</div>
                                            <div><strong style="color:var(--text);">Temp:</strong> {{ $record->temperature ? $record->temperature . '°C' : 'N/A' }}</div>
                                            <div><strong style="color:var(--text);">Weight:</strong> {{ $record->weight ? $record->weight . ' kg' : 'N/A' }}</div>
                                            @if($record->fetal_heart_rate)
                                                <div><strong style="color:var(--text);">FHR:</strong> {{ $record->fetal_heart_rate }} bpm</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td style="padding:1rem;">
                                        @switch($record->risk_level)
                                            @case('Low')
                                                <div style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.25rem 0.75rem; background:rgba(25,135,84,0.1); color:var(--success); border-radius:20px; font-weight:500; font-size:0.8rem;">
                                                    <i class="bi bi-shield-check"></i> Low Risk
                                                </div>
                                                @break
                                            @case('Medium')
                                                <div style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.25rem 0.75rem; background:rgba(245,158,11,0.1); color:var(--warning); border-radius:20px; font-weight:500; font-size:0.8rem;">
                                                    <i class="bi bi-exclamation-circle"></i> Medium Risk
                                                </div>
                                                @break
                                            @case('High')
                                                <div style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.25rem 0.75rem; background:rgba(220,53,69,0.1); color:var(--danger); border-radius:20px; font-weight:500; font-size:0.8rem;">
                                                    <i class="bi bi-exclamation-triangle"></i> High Risk
                                                </div>
                                                @break
                                            @default
                                                <span style="padding:0.25rem 0.75rem; background:var(--bg-card2); color:var(--text-muted); border-radius:20px; font-size:0.8rem;">{{ $record->risk_level }}</span>
                                        @endswitch
                                    </td>
                                    <td style="padding:1rem;">
                                        <div style="font-weight:500; color:var(--text);">{{ $recordedByName }}</div>
                                    </td>
                                    <td style="padding:1rem 1.5rem; text-align:right;">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <a href="{{ route('midwife.health-records.show', $record->id) }}" 
                                               class="btn btn-sm" 
                                               style="width:36px;height:36px;border-radius:8px;background:var(--bg-card2);border:1px solid var(--border);color:var(--primary);display:flex;align-items:center;justify-content:center;"
                                               title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('midwife.health-records.create', $record->user_id) }}" 
                                               class="btn btn-sm" 
                                               style="width:36px;height:36px;border-radius:8px;background:var(--bg-card2);border:1px solid var(--border);color:var(--success);display:flex;align-items:center;justify-content:center;"
                                               title="Add New Record">
                                                <i class="bi bi-plus"></i>
                                            </a>
                                            @if($record->workflow_status === 'submitted_to_midwife')
                                                <form action="{{ route('midwife.health-records.accept', $record->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="btn btn-sm" 
                                                            style="width:36px;height:36px;border-radius:8px;background:var(--bg-card2);border:1px solid var(--border);color:var(--success);display:flex;align-items:center;justify-content:center;"
                                                            title="Accept Record">
                                                        <i class="bi bi-check2-circle"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('midwife.health-records.archive', $record->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Archive this health record? It will be removed from active records.')">
                                                @csrf
                                                <button type="submit" 
                                                        class="btn btn-sm" 
                                                        style="width:36px;height:36px;border-radius:8px;background:var(--bg-card2);border:1px solid var(--border);color:var(--text-muted);display:flex;align-items:center;justify-content:center;"
                                                        title="Archive Record">
                                                    <i class="bi bi-archive"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="card-footer" style="background:transparent; border-top:1px solid var(--border-color); padding:1rem 1.5rem;">
                    <div class="d-flex justify-content-center">
                        {{ $healthRecords->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @else
                <div class="card-body p-5">
                    <div class="text-center py-5">
                        <div style="width:80px;height:80px;border-radius:20px;background:linear-gradient(135deg,var(--primary),var(--accent-violet));display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;">
                            <i class="bi bi-clipboard-pulse" style="font-size:2rem;color:#fff;"></i>
                        </div>
                        <h5 style="font-weight:600; color:var(--text); margin-bottom:0.75rem;">No Health Records Found</h5>
                        <p style="color:var(--text-muted); margin-bottom:1.5rem;">
                            @if(request('search'))
                                No health records found matching your search criteria.
                            @else
                                No health records have been created yet. Start by adding your first health record.
                            @endif
                        </p>
                        @if(request('search'))
                            <a href="{{ route('midwife.health-records.index') }}" class="btn"
                               style="background:linear-gradient(135deg,var(--primary),var(--accent-violet)); color:#fff; border:none; border-radius:10px; padding:0.6rem 1.5rem;">
                                <i class="bi bi-arrow-counterclockwise me-1"></i>Clear Search
                            </a>
                        @else
                            <a href="{{ route('midwife.health-records.create') }}" class="btn"
                               style="background:linear-gradient(135deg,var(--primary),var(--accent-violet)); color:#fff; border:none; border-radius:10px; padding:0.6rem 1.5rem;">
                                <i class="bi bi-clipboard-plus me-1"></i>Add First Health Record
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
