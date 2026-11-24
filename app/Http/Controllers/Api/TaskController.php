<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $tasks = Task::with(['project:id,name', 'assignees:id,name'])
            ->when($request->project_id, fn ($q, $id) => $q->where('project_id', $id))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->mine, fn ($q) => $q->whereHas('assignees', fn ($a) => $a->where('users.id', $request->user()->id)))
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $request->boolean('grouped') ? $tasks->groupBy('status') : $tasks,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'in:todo,in_progress,in_review,completed'],
            'priority' => ['nullable', 'in:low,medium,high'],
            'due_date' => ['nullable', 'date'],
            'reviewer_id' => ['nullable', 'exists:users,id'],
            'assignees' => ['nullable', 'array'],
            'assignees.*' => ['exists:users,id'],
        ]);

        $data['created_by'] = $request->user()->id;
        $task = Task::create(collect($data)->except('assignees')->all());
        $task->assignees()->attach($data['assignees'] ?? [$request->user()->id]);

        return response()->json([
            'success' => true,
            'message' => 'Task created',
            'data' => $task->load('assignees:id,name'),
        ], 201);
    }

    public function show(Task $task)
    {
        return response()->json([
            'success' => true,
            'data' => $task->load(['project:id,name', 'assignees:id,name', 'reviewer:id,name']),
        ]);
    }

    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['sometimes', 'in:low,medium,high'],
            'progress' => ['sometimes', 'integer', 'min:0', 'max:100'],
            'due_date' => ['nullable', 'date'],
            'reviewer_id' => ['nullable', 'exists:users,id'],
        ]);

        $task->update($data);

        return response()->json(['success' => true, 'message' => 'Task updated', 'data' => $task]);
    }

    public function updateStatus(Request $request, Task $task)
    {
        $request->validate(['status' => ['required', 'in:todo,in_progress,in_review,completed']]);

        $task->update([
            'status' => $request->status,
            'completed_at' => $request->status === 'completed' ? now() : null,
            'progress' => $request->status === 'completed' ? 100 : $task->progress,
        ]);

        return response()->json(['success' => true, 'message' => 'Status updated', 'data' => $task]);
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json(['success' => true, 'message' => 'Task deleted']);
    }
}
