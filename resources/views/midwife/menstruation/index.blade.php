@extends('midwife.layout')

@section('title', 'Menstruation Records - ReproCare')

@section('midwife-content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-calendar-heart"></i> Menstruation Records</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('midwife.menstruation.create') }}" class="btn btn-primary">
                <i class="bi bi-calendar-plus"></i> Add Record
            </a>
            <form class="d-flex" method="GET" action="{{ route('midwife.menstruation.index') }}">
                <input class="form-control me-2" type="search" name="search" placeholder="Search records..." value="{{ request('search') }}">
                <button class="btn btn-outline-primary" type="submit">
                    <i class="bi bi-search"></i> Search
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4>{{ $records->total() }}</h4>
                    <p class="mb-0">Total Records</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4>{{ $records->where('flow_type', 'normal')->count() }}</h4>
                    <p class="mb-0">Normal Flow</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h4>{{ $records->where('flow_type', 'heavy')->count() }}</h4>
                    <p class="mb-0">Heavy Flow</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h4>{{ $records->where('flow_type', 'irregular')->count() }}</h4>
                    <p class="mb-0">Irregular Flow</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-calendar-heart-fill"></i> All Menstruation Records</h5>
        </div>
        <div class="card-body">
            @if($records->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>Patient</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Flow Type</th>
                                <th>Duration</th>
                                <th>Notes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $record)
                                <tr>
                                    <td>
                                        @if($record->user)
                                            <div>
                                                <strong>{{ $record->user->name }}</strong>
                                            </div>
                                            <div><small class="text-muted">{{ $record->user->email }}</small></div>
                                        @else
                                            <span class="text-muted">No patient</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($record->period_start_date)->format('M j, Y') }}</td>
                                    <td>{{ $record->period_end_date ? \Carbon\Carbon::parse($record->period_end_date)->format('M j, Y') : 'Ongoing' }}</td>
                                    <td>
                                        @switch($record->flow_type)
                                            @case('normal')
                                                <span class="badge bg-success">Normal</span>
                                                @break
                                            @case('heavy')
                                                <span class="badge bg-warning">Heavy</span>
                                                @break
                                            @case('irregular')
                                                <span class="badge bg-danger">Irregular</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ $record->flow_type ?? 'Unknown' }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($record->period_end_date)
                                            {{ \Carbon\Carbon::parse($record->period_start_date)->diffInDays(\Carbon\Carbon::parse($record->period_end_date)) }} days
                                        @else
                                            Ongoing
                                        @endif
                                    </td>
                                    <td>
                                        {{ Str::limit($record->notes ?? '', 30) }}
                                        @if($record->notes && strlen($record->notes) > 30)
                                            <small class="text-muted">...</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('midwife.menstruation.show', $record->id) }}" class="btn btn-sm btn-primary" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('midwife.menstruation.edit', $record->id) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('midwife.menstruation.destroy', $record->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this record?')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
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

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $records->links() }}
                </div>
            @else
                <div class="card shadow">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-calendar-heart text-muted" style="font-size: 4rem;"></i>
                        <h3 class="text-muted mt-3">No Menstruation Records</h3>
                        <p class="text-muted">
                            No menstruation records have been added yet.
                        </p>
                        <a href="{{ route('midwife.menstruation.create') }}" class="btn btn-primary">
                            <i class="bi bi-calendar-plus"></i> Add First Record
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
