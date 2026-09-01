@extends('bhw-president.layout')

@section('title', 'Edit Task - BHW President Portal | ReproCare')

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title">Edit Task</h1>
            <p class="page-subtitle">{{ $task->title }}</p>
        </div>
        <a href="{{ route('bhw-president.tasks.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('bhw-president.tasks.update', $task->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Title *</label>
                        <input type="text" name="title" class="form-control" required value="{{ old('title', $task->title) }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Assign To BHW *</label>
                        <select name="assigned_to_id" class="form-select" required>
                            <option value="">Select BHW...</option>
                            @foreach($bhws as $bhw)
                            <option value="{{ $bhw->id }}" {{ old('assigned_to_id', $task->assigned_to_id) == $bhw->id ? 'selected' : '' }}>{{ $bhw->name }} ({{ $bhw->barangay }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Task Type *</label>
                        <select name="task_type" class="form-select" required>
                            <option value="">Select Type...</option>
                            <option value="patient_visit" {{ old('task_type', $task->task_type) == 'patient_visit' ? 'selected' : '' }}>Patient Visit</option>
                            <option value="data_collection" {{ old('task_type', $task->task_type) == 'data_collection' ? 'selected' : '' }}>Data Collection</option>
                            <option value="follow_up" {{ old('task_type', $task->task_type) == 'follow_up' ? 'selected' : '' }}>Follow Up</option>
                            <option value="report_submission" {{ old('task_type', $task->task_type) == 'report_submission' ? 'selected' : '' }}>Report Submission</option>
                            <option value="other" {{ old('task_type', $task->task_type) == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Status *</label>
                        <select name="status" class="form-select" required>
                            <option value="pending" {{ old('status', $task->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('status', $task->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Due Date</label>
                        <input type="date" name="due_date" class="form-control" value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '') }}">
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="4">{{ old('description', $task->description) }}</textarea>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Update Task
                </button>
                <a href="{{ route('bhw-president.tasks.show', $task->id) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
