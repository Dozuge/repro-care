@extends('bhw-president.layout')

@section('title', 'Edit Health Record - BHW President Portal | ReproCare')

@section('bhw-president-content')
<div class="page-hero fade-in-card mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3" style="position:relative;z-index:1;">
        <div>
            <div class="page-hero-title"><i class="bi bi-pencil-square me-2"></i>Edit Health Record</div>
            <p class="page-hero-subtitle">{{ $healthRecord->patient_name }} • {{ optional($healthRecord->recordedBy)->name ?? 'Unknown' }}</p>
        </div>
        <a href="{{ route('bhw-president.health-records.index') }}" class="btn btn-sm btn-light">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="card fade-in-card">
    <div class="card-body">
        <form action="{{ route('bhw-president.health-records.update', $healthRecord->id) }}" method="POST" class="row g-3">
            @csrf
            @method('PUT')
            <div class="col-md-6">
                <label class="form-label">Blood Pressure</label>
                <input type="text" name="bp" class="form-control" value="{{ old('bp', $healthRecord->bp) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Risk Level</label>
                <select name="risk_level" class="form-select" required>
                    @foreach(['Low', 'Medium', 'High'] as $riskLevel)
                        <option value="{{ $riskLevel }}" {{ old('risk_level', $healthRecord->risk_level) === $riskLevel ? 'selected' : '' }}>{{ $riskLevel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Weight</label>
                <input type="number" step="0.1" name="weight" class="form-control" value="{{ old('weight', $healthRecord->weight) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Heart Rate</label>
                <input type="number" name="heart_rate" class="form-control" value="{{ old('heart_rate', $healthRecord->heart_rate) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Temperature</label>
                <input type="number" step="0.1" name="temperature" class="form-control" value="{{ old('temperature', $healthRecord->temperature) }}">
            </div>
            <div class="col-12">
                <label class="form-label">Clinical Notes</label>
                <textarea name="notes" class="form-control" rows="4">{{ old('notes', $healthRecord->notes) }}</textarea>
            </div>
            <div class="col-12">
                <label class="form-label">Workflow Notes</label>
                <textarea name="workflow_notes" class="form-control" rows="3">{{ old('workflow_notes', $healthRecord->workflow_notes) }}</textarea>
            </div>
            <div class="col-12 d-flex justify-content-end gap-2">
                @if($healthRecord->recordedBy)
                    <a href="{{ route('bhw-president.messages.create', ['to' => $healthRecord->recordedBy->id, 'role' => 'bhw']) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-chat-text me-1"></i>Message Recorder
                    </a>
                @endif
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
