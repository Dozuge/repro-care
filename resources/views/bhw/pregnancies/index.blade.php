@extends('bhw.layout')

@section('title', 'Pregnancies - BHW Portal')

@section('bhw-content')
<div class="py-4">
    <div class="page-hero fade-in-card mb-4">
        <div style="position:relative;z-index:1;">
            <div class="page-hero-title"><i class="bi bi-heart-pulse-fill me-2"></i>Pregnancies</div>
            <p class="page-hero-subtitle">View pregnancies you created, those created by other BHWs, or by the midwife.</p>
        </div>
    </div>

    <div class="card fade-in-card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('bhw.pregnancies.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Filter</label>
                    <select name="filter" class="form-select">
                        <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>All Pregnancies</option>
                        <option value="mine" {{ $filter === 'mine' ? 'selected' : '' }}>My Created Patients</option>
                        <option value="midwife" {{ $filter === 'midwife' ? 'selected' : '' }}>Midwife Created</option>
                        <option value="others" {{ $filter === 'others' ? 'selected' : '' }}>Other BHW Created</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Search Patient</label>
                    <input type="text" name="search" class="form-control" placeholder="Search registered or walk-in patient..." value="{{ $search }}">
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-fill"><i class="bi bi-search me-1"></i> Apply</button>
                    <a href="{{ route('bhw.pregnancies.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card fade-in-card">
        <div class="card-body p-0">
            @if($pregnancies->count())
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>LMP</th>
                                <th>EDD</th>
                                <th>AOG</th>
                                <th>Trimester</th>
                                <th>Risk</th>
                                <th>Created By</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pregnancies as $pregnancy)
                                @php
                                    $isWalkIn = !is_null($pregnancy->walk_in_patient_id);
                                    $patientRoute = $isWalkIn
                                        ? route('bhw.walk-in-patients.show', $pregnancy->walk_in_patient_id)
                                        : route('bhw.patient-details', $pregnancy->user_id);
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $pregnancy->patient_name }}</div>
                                        <small class="text-muted">{{ $isWalkIn ? 'Walk-in patient' : (optional($pregnancy->woman)->email ?? 'Registered patient') }}</small>
                                    </td>
                                    <td>{{ $pregnancy->lmp?->format('M d, Y') ?? 'N/A' }}</td>
                                    <td>{{ $pregnancy->edd?->format('M d, Y') ?? 'N/A' }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $pregnancy->formatted_aog ?? 'N/A' }}</span></td>
                                    <td><span class="badge bg-info text-dark">{{ $pregnancy->trimester_name ?? 'N/A' }}</span></td>
                                    <td>
                                        <span class="badge {{ $pregnancy->is_high_risk ? 'bg-danger' : 'bg-success' }}">
                                            {{ $pregnancy->is_high_risk ? 'High Risk' : 'Normal' }}
                                        </span>
                                    </td>
                                    <td>{{ $pregnancy->created_by_name ?? 'Unknown' }}</td>
                                    <td class="text-center">
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ $patientRoute }}" class="btn btn-sm btn-outline-primary" title="View patient">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if($pregnancy->is_mine && $isWalkIn)
                                                <a href="{{ route('bhw.walk-in-patients.edit', $pregnancy->walk_in_patient_id) }}" class="btn btn-sm btn-outline-success" title="Edit walk-in patient">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            @elseif($pregnancy->is_mine && !$isWalkIn)
                                                <a href="{{ route('bhw.patient-details', $pregnancy->user_id) }}" class="btn btn-sm btn-outline-success" title="Open registered patient">
                                                    <i class="bi bi-person"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">{{ $pregnancies->links() }}</div>
            @else
                <div class="empty-state">
                    <i class="bi bi-heart empty-state-icon"></i>
                    <h6>No pregnancies found</h6>
                    <p>Try a different filter or search term.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
