@extends('layouts.app')

@section('title', 'Create Task')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4>Create New Task</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">
                            Description 
                            <button type="button" id="generateDesc" class="btn btn-sm btn-outline-primary ms-2">
                                <i class="fas fa-magic"></i> Auto-Generate
                            </button>
                        </label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4" 
                                  placeholder="Enter description or click Auto-Generate to create one based on the title">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="priority" class="form-label">Priority</label>
                                <select class="form-select @error('priority') is-invalid @enderror" 
                                        id="priority" name="priority" required>
                                    <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="due_date" class="form-label">Due Date</label>
                                <input type="datetime-local" class="form-control @error('due_date') is-invalid @enderror" 
                                       id="due_date" name="due_date" value="{{ old('due_date') }}">
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="documents" class="form-label">Upload Documents</label>
                        <input type="file" class="form-control @error('documents.*') is-invalid @enderror" 
                               id="documents" name="documents[]" multiple 
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif">
                        <div class="form-text">Supported formats: PDF, DOC, DOCX, JPG, JPEG, PNG, GIF (Max: 10MB each)</div>
                        @error('documents.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('tasks.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const generateBtn = document.getElementById('generateDesc');
    const titleInput = document.getElementById('title');
    const descriptionTextarea = document.getElementById('description');
    
    generateBtn.addEventListener('click', async function() {
        const title = titleInput.value.trim();
        
        if (!title) {
            alert('Please enter a title first');
            titleInput.focus();
            return;
        }
        
        // Show loading state
        const originalText = generateBtn.innerHTML;
        generateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
        generateBtn.disabled = true;
        
        try {
            const response = await fetch('{{ route("tasks.generate-description") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ title: title })
            });
            
            const data = await response.json();
            
            if (data.success) {
                descriptionTextarea.value = data.description;
            } else {
                alert('Failed to generate description: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            alert('Error generating description. Please try again.');
            console.error('Error:', error);
        } finally {
            // Restore button state
            generateBtn.innerHTML = originalText;
            generateBtn.disabled = false;
        }
    });
});
</script>
@endsection