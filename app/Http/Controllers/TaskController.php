<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{

    public function index(Request $request)
    {
        $projects = Project::orderBy('name')->get();

        $tasks = Task::with(['project', 'assignees'])
            ->when($request->project_id, fn ($q, $id) => $q->where('project_id', $id))
            ->when($request->mine, fn ($q) => $q->whereHas('assignees', fn ($a) => $a->where('users.id', auth()->id())))
            ->get()
            ->groupBy('status');

        return view('tasks.index', compact('projects', 'tasks'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:todo,in_progress,in_review,completed'],
            'priority' => ['required', 'in:low,medium,high'],
            'due_date' => ['nullable', 'date'],
        ]);

        $data['created_by'] = $request->user()->id;
        $task = Task::create($data);
        $task->assignees()->attach($request->user()->id);

        return back()->with('success', 'Task created');
    }

    public function updateStatus(Request $request, Task $task)
    {
        $request->validate(['status' => ['required', 'in:todo,in_progress,in_review,completed']]);

        $task->update([
            'status' => $request->status,
            'completed_at' => $request->status === 'completed' ? now() : null,
            'progress' => $request->status === 'completed' ? 100 : $task->progress,
        ]);

        return back();
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return back()->with('success', 'Task deleted');
    }
}
