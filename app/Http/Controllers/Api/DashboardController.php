<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tasks = Task::query()->when($request->project_id, fn ($q, $id) => $q->where('project_id', $id));

        $total = (clone $tasks)->count();
        $completed = (clone $tasks)->where('status', 'completed')->count();

        return response()->json(['success' => true, 'data' => [
            'stats' => [
                'pending' => (clone $tasks)->where('status', 'todo')->count(),
                'in_progress' => (clone $tasks)->whereIn('status', ['in_progress', 'in_review'])->count(),
                'completed' => $completed,
                'completion_rate' => $total ? round($completed / $total * 100) : 0,
            ],
            'active_projects' => Project::withCount([
                'tasks',
                'tasks as completed_tasks_count' => fn ($q) => $q->where('status', 'completed'),
            ])->where('status', 'active')->get(),
            'upcoming_tasks' => (clone $tasks)->with('project:id,name')
                ->where('status', '!=', 'completed')
                ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
                ->orderBy('due_date')->take(5)->get(),
            'team_status' => User::withCount([
                'tasks',
                'tasks as done_tasks_count' => fn ($q) => $q->where('status', 'completed'),
            ])->orderBy('name')->take(5)->get(['id', 'name', 'job_title', 'availability']),
            'recent_files' => ProjectFile::with('uploader:id,name')->latest()->take(3)->get(),
        ]]);
    }
}
