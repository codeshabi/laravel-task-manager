@extends('layouts.app')

@section('title', $task->title)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>{{ $task->title }}</h4>
                <div>
                    <span class="badge bg-{{ $task->priority === 'high' ? 'danger' : ($task->priority === 'medium' ? 'warning' : 'success') }} me-2">
                        {{ ucfirst($task->priority) }} Priority
                    </span>
                    <span class="badge bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'info' : 'secondary') }}">
                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                @if($task->description)
                    <h6>Description:</h6>
                    <p>{{ $task->description }}</p>
                @endif

                @if($task->due_date)
                    <h6>Due Date:</h6>
                    <p>{{ $task->due_date->format('M d, Y H:i') }}</p>
                @endif



                @if($task->ai_analysis)
                    <h6>AI Analysis:</h6>
                    <div class="alert alert-light">
                        {{ $task->ai_analysis }}
                    </div>
                @endif
            </div>
            <div class="card-footer">
                <a href="{{ route('tasks.edit', $task) }}" class="btn btn-primary">Edit</a>
                <a href="{{ route('tasks.index') }}" class="btn btn-secondary">Back to Tasks</a>
                <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" 
                            onclick="return confirm('Are you sure you want to delete this task?')">Delete</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        @if($task->documents->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h5>Attached Documents</h5>
                </div>
                <div class="card-body">
                    @foreach($task->documents as $document)
                        <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                            <div>
                                <strong>{{ $document->original_name }}</strong><br>
                                <small class="text-muted">{{ number_format($document->size / 1024, 2) }} KB</small>
                            </div>
                            <a href="{{ $document->google_drive_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                View
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection