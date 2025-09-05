<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Document;
use App\Services\GoogleDriveService;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    private $googleDrive;
    private $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
        
        // Initialize Google Drive if enabled
        if (config('services.google_drive.enabled', false)) {
            try {
                $this->googleDrive = new GoogleDriveService();
            } catch (\Exception $e) {
                $this->googleDrive = null;
                \Log::warning('Google Drive service unavailable: ' . $e->getMessage());
            }
        } else {
            $this->googleDrive = null;
        }
    }

    public function index()
    {
        $tasks = Task::with('documents')->latest()->get();
        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        return view('tasks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date',
            'documents.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png,gif|max:10240'
        ]);

        // Auto-generate description if not provided
        $description = $request->description;
        if (empty($description)) {
            try {
                $description = $this->gemini->generateDescription($request->title);
            } catch (\Exception $e) {
                \Log::warning('AI description generation failed: ' . $e->getMessage());
                $description = null;
            }
        }

        $task = Task::create([
            'title' => $request->title,
            'description' => $description,
            'priority' => $request->priority,
            'due_date' => $request->due_date
        ]);

        // Upload documents
        $documents = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                if ($this->googleDrive) {
                    try {
                        $driveFile = $this->googleDrive->uploadFile($file);
                        
                        $document = Document::create([
                            'task_id' => $task->id,
                            'name' => $file->getClientOriginalName(),
                            'original_name' => $file->getClientOriginalName(),
                            'mime_type' => $file->getMimeType(),
                            'google_drive_id' => $driveFile['id'],
                            'google_drive_url' => $driveFile['url'],
                            'size' => $file->getSize()
                        ]);
                        $documents[] = $document;
                        continue;
                    } catch (\Exception $e) {
                        \Log::error('Google Drive upload failed: ' . $e->getMessage());
                    }
                }
                
                // Store file locally
                $path = $file->store('documents', 'public');
                
                $document = Document::create([
                    'task_id' => $task->id,
                    'name' => $file->getClientOriginalName(),
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'google_drive_id' => null,
                    'google_drive_url' => asset('storage/' . $path),
                    'size' => $file->getSize()
                ]);
                $documents[] = $document;
            }
        }

        // Generate AI analysis
        
        try {
            $analysis = $this->gemini->analyzeTask($task->title, $task->description);
            $task->update(['ai_analysis' => $analysis['analysis']]);
        } catch (\Exception $e) {
            \Log::warning('AI analysis failed: ' . $e->getMessage());
        }

        return redirect()->route('tasks.show', $task)->with('success', 'Task created successfully!');
    }

    public function show(Task $task)
    {
        $task->load('documents');
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        return view('tasks.edit', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date'
        ]);

        // Auto-generate description if empty and title changed
        $updateData = $request->only(['title', 'description', 'status', 'priority', 'due_date']);
        if (empty($updateData['description']) && $task->title !== $updateData['title']) {
            try {
                $updateData['description'] = $this->gemini->generateDescription($updateData['title'], $task->documents->toArray());
            } catch (\Exception $e) {
                \Log::warning('AI description generation failed: ' . $e->getMessage());
            }
        }

        $task->update($updateData);

        return redirect()->route('tasks.show', $task)->with('success', 'Task updated successfully!');
    }

    public function generateDescription(Request $request)
    {
        $request->validate(['title' => 'required|string|max:255']);
        
        try {
            $description = $this->gemini->generateDescription($request->title);
            return response()->json([
                'success' => true,
                'description' => htmlspecialchars($description, ENT_QUOTES, 'UTF-8')
            ]);
        } catch (\Exception $e) {
            \Log::error('AI description generation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate description'
            ], 500);
        }
    }

    public function destroy(Task $task)
    {
        foreach ($task->documents as $document) {
            if ($this->googleDrive && $document->google_drive_id) {
                try {
                    $this->googleDrive->deleteFile($document->google_drive_id);
                } catch (\Exception $e) {
                    \Log::warning('Failed to delete from Google Drive: ' . $e->getMessage());
                }
            }
        }
        
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully!');
    }
}