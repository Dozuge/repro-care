@extends('bhw-president.layout')

@section('title', 'Tasks - BHW President Portal | ReproCare')

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title">Tasks</h1>
            <p class="page-subtitle">Manage BHW tasks and assignments</p>
        </div>
        <a href="{{ route('bhw-president.tasks.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> New Task
        </a>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">Pending</h5>
                <h2>{{ $tasks->where('status', 'pending')->count() }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <h5 class="card-title">In Progress</h5>
                <h2>{{ $tasks->where('status', 'in_progress')->count() }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Completed</h5>
                <h2>{{ $tasks->where('status', 'completed')->count() }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h5 class="card-title">Overdue</h5>
                <h2>{{ $tasks->where('due_date', '<', now())->where('status', '!=', 'completed')->count() }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($tasks->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Assigned To</th>
                            <th>Type</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tasks as $task)
                        <tr>
                            <td>{{ $task->title }}</td>
                            <td>{{ $task->assignedTo->name }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $task->task_type)) }}</td>
                            <td>{{ $task->due_date ? $task->due_date->format('M d, Y') : '-' }}</td>
                            <td>
                                @switch($task->status)
                                    @case('pending')
                                        <span class="badge bg-primary">Pending</span>
                                        @break
                                    @case('in_progress')
                                        <span class="badge bg-warning">In Progress</span>
                                        @break
                                    @case('completed')
                                        <span class="badge bg-success">Completed</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge bg-danger">Cancelled</span>
                                        @break
                                @endswitch
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('bhw-president.tasks.show', $task->id) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('bhw-president.tasks.edit', $task->id) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($task->status != 'completed')
                                    <form action="{{ route('bhw-president.tasks.complete', $task->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Mark Complete">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                    @endif
                                    <form action="{{ route('bhw-president.tasks.destroy', $task->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
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
        @else
            <div class="text-center py-5">
                <i class="bi bi-clipboard-task fs-1 text-muted"></i>
                <p class="text-muted mt-3">No tasks found</p>
                <a href="{{ route('bhw-president.tasks.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Create First Task
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
