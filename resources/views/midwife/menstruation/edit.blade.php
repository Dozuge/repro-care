@extends('midwife.layout')

@section('title', 'Edit Menstruation Record - ReproCare')

@section('midwife-content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-calendar-heart"></i> Edit Menstruation Record</h1>
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

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-calendar-heart-fill"></i> Edit Menstruation Record</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('midwife.menstruation.update', $record->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Patient</label>
                        <select class="form-select" name="patient_id" required>
                            <option value="">Select Patient</option>
                            @foreach($womans as $woman)
                                <option value="{{ $woman->id }}" {{ $record->patient_id == $woman->id ? 'selected' : '' }}>
                                    {{ $woman->name }} ({{ $woman->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Flow Type</label>
                        <select class="form-select" name="flow_type" required>
                            <option value="normal" {{ $record->flow_type === 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="heavy" {{ $record->flow_type === 'heavy' ? 'selected' : '' }}>Heavy</option>
                            <option value="irregular" {{ $record->flow_type === 'irregular' ? 'selected' : '' }}>Irregular</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control" name="start_date" value="{{ $record->period_start_date }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">End Date</label>
                        <input type="date" class="form-control" name="end_date" value="{{ $record->period_end_date ?? '' }}">
                        <small class="form-text text-muted">Leave blank if still ongoing</small>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="4">{{ $record->notes ?? '' }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check"></i> Update Record
                    </button>
                    <a href="{{ route('midwife.menstruation.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
