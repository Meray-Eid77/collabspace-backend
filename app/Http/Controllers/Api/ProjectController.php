<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::withCount('files')
            ->with('owner:id,name')
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate($request->get('per_page', 10));

        return response()->json(['success' => true, 'data' => $projects]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['nullable', 'string', 'max:100'],
            'priority' => ['required', 'in:low,medium,high'],
            'status' => ['nullable', 'in:active,done'],
            'start_date' => ['nullable', 'date'],
            'deadline' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $data['owner_id'] = $request->user()->id;
        $project = Project::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Project created',
            'data' => $project,
        ], 201);
    }

    public function show(Project $project)
    {
        $project->load(['owner:id,name', 'teams.members:id,name', 'files', 'tasks.assignees:id,name']);

        $total = $project->tasks->count();
        $done = $project->tasks->where('status', 'completed')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'project' => $project,
                'progress' => [
                    'total_tasks' => $total,
                    'completed_tasks' => $done,
                    'percent' => $total ? round($done / $total * 100) : 0,
                ],
            ],
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['nullable', 'string', 'max:100'],
            'priority' => ['sometimes', 'in:low,medium,high'],
            'status' => ['sometimes', 'in:active,done'],
            'start_date' => ['nullable', 'date'],
            'deadline' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $project->update($data);

        return response()->json(['success' => true, 'message' => 'Project updated', 'data' => $project]);
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return response()->json(['success' => true, 'message' => 'Project deleted']);
    }
}
