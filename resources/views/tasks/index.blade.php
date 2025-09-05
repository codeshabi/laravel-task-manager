@extends('layouts.app')

@section('title', 'Tasks')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Tasks</h1>
    <a href="{{ route('tasks.create') }}" class="btn btn-primary">Create New Task</a>
</div>

<div class="row">
    @forelse($tasks as $task)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <span class="badge bg-{{ $task->priority === 'high' ? 'danger' : ($task->priority === 'medium' ? 'warning' : 'success') }}">
                        {{ ucfirst($task->priority) }}
                    </span>
                    <span class="badge bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'info' : 'secondary') }}">
                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                    </span>
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ $task->title }}</h5>
                    <p class="card-text">{{ Str::limit($task->description, 100) }}</p>
                    @if($task->documents->count() > 0)
                        <small class="text-muted">{{ $task->documents->count() }} document(s) attached</small>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-outline-primary">View</a>
                    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="text-center py-5">
                <h3>No tasks found</h3>
                <p>Create your first task to get started!</p>
                <a href="{{ route('tasks.create') }}" class="btn btn-primary">Create Task</a>
            </div>
        </div>
    @endforelse
</div>
@endsection