@extends('midwife.layout')

@section('title', 'Walk-in Women - Midwife Portal | ReproCare')

@section('midwife-content')
<div class="container-fluid py-4">
    <!-- Hero Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="hero-gradient p-4 rounded-4" style="background: linear-gradient(135deg, #8B5CF6 0%, #6D28D9 50%, #4C1D95 100%); position: relative; overflow: hidden;">
                <div class="position-absolute" style="top: -50px; right: -50px; width: 200px; height: 200px; background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);"></div>
                <div class="position-absolute" style="bottom: -30px; left: 10%; width: 150px; height: 150px; background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);"></div>

                <div class="d-flex justify-content-between align-items-center position-relative" style="z-index: 1;">
                    <div>
                        <h1 class="fw-bold text-white mb-2">
                            <i class="bi bi-people me-2"></i>Walk-in Women
                        </h1>
                        <p class="text-white-50 mb-0">
                            View and manage walk-in women records
                        </p>
                    </div>
                    <div class="text-center">
                        <div class="bg-white bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-person-walking text-white" style="font-size: 2rem;"></i>
                            <div class="text-white-50 small mt-1">Total Walk-ins</div>
                            <div class="fw-bold text-white fs-4">{{ $patients->total() ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if($patients->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Age</th>
                                <th>Contact</th>
                                <th>Barangay</th>
                                <th>Recorded By</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($patients as $patient)
                                <tr>
                                    <td>{{ $patient->full_name }}</td>
                                    <td>{{ $patient->age ?? '—' }}</td>
                                    <td>{{ $patient->contact_number ?? '—' }}</td>
                                    <td>{{ $patient->barangay ?? '—' }}</td>
                                    <td>{{ $patient->recordedBy?->name ?? '—' }}</td>
                                    <td>
                                        @if($patient->converted_to_user_id)
                                            <span class="badge bg-success">Converted</span>
                                        @else
                                            <span class="badge bg-secondary">Walk-in</span>
                                        @endif
                                    </td>
                                    <td>{{ $patient->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('midwife.walk-in-patients.show', $patient->id) }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-eye me-1"></i>View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $patients->links() }}
            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 80px; height: 80px; background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);">
                            <i class="bi bi-person-walking text-primary" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                    <h4 class="text-dark mb-2">No walk-in women</h4>
                    <p class="text-muted mb-0">There are currently no walk-in women recorded.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
