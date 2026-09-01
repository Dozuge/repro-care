@extends('midwife.layout')

@section('title', 'Menstruation Record Details - ReproCare')

@section('midwife-content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-calendar-heart"></i> Menstruation Record Details</h1>
        <a href="{{ route('midwife.menstruation.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Records
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <!-- Patient Information -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person-fill"></i> Patient Information</h5>
                </div>
                <div class="card-body">
                    @if($record->user)
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Name:</strong><br>
                                {{ $record->user->name }}
                            </div>
                            <div class="col-md-6">
                                <strong>Email:</strong><br>
                                {{ $record->user->email }}
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <strong>Address:</strong><br>
                                {{ $record->user->address ?? 'Not provided' }}
                            </div>
                            <div class="col-md-6">
                                <strong>Barangay:</strong><br>
                                {{ $record->user->barangay ?? 'Not provided' }}
                            </div>
                        </div>
                    @else
                        <div class="text-center text-muted">
                            <i class="bi bi-person-x" style="font-size: 3rem;"></i>
                            <h5>Patient not found</h5>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Menstruation Details -->
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-calendar-heart-fill"></i> Menstruation Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Start Date:</strong><br>
                            {{ \Carbon\Carbon::parse($record->period_start_date)->format('F j, Y') }}
                        </div>
                        <div class="col-md-6">
                            <strong>End Date:</strong><br>
                            {{ $record->period_end_date ? \Carbon\Carbon::parse($record->period_end_date)->format('F j, Y') : 'Ongoing' }}
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <strong>Flow Type:</strong><br>
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
                        </div>
                        <div class="col-md-4">
                            <strong>Duration:</strong><br>
                            @if($record->period_end_date)
                                {{ \Carbon\Carbon::parse($record->period_start_date)->diffInDays(\Carbon\Carbon::parse($record->period_end_date)) }} days
                            @else
                                Ongoing
                            @endif
                        </div>
                        <div class="col-md-4">
                            <strong>Recorded:</strong><br>
                            {{ \Carbon\Carbon::parse($record->created_at)->format('F j, Y g:i A') }}
                        </div>
                    </div>
                    @if($record->notes)
                        <div class="row mt-3">
                            <div class="col-12">
                                <strong>Notes:</strong><br>
                                {{ $record->notes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="card shadow mb-4">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="bi bi-gear-fill"></i> Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('midwife.menstruation.edit', $record->id) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Edit Record
                        </a>
                        <form action="{{ route('midwife.menstruation.destroy', $record->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this menstruation record?')">
                            @csrf
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash"></i> Delete Record
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
