<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Report;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    private function rate(int $part, int $total): int
    {
        return $total ? (int) round($part / $total * 100) : 0;
    }

    public function stats()
    {
        $completed = Task::where('status', 'completed')->whereNotNull('due_date')->get();
        $onTime = $completed->filter(fn ($t) => $t->completed_at && $t->completed_at->toDateString() <= $t->due_date->toDateString())->count();

        return response()->json(['success' => true, 'data' => [
            'task_completion_rate' => $this->rate(Task::where('status', 'completed')->count(), Task::count()),
            'projects_completion_rate' => $this->rate(Project::where('status', 'done')->count(), Project::count()),
            'teams_activity_rate' => $this->rate(
                Team::whereHas('members.tasks', fn ($q) => $q->where('tasks.status', '!=', 'todo'))->count(),
                Team::count()
            ),
            'completion_vs_deadline' => ['on_time' => $onTime, 'late' => $completed->count() - $onTime],
            'projects_progress' => Project::withCount([
                'tasks',
                'tasks as done_count' => fn ($q) => $q->where('status', 'completed'),
            ])->get(['id', 'name']),
            'worker_activity' => User::withCount([
                'tasks as done_count' => fn ($q) => $q->where('tasks.status', 'completed'),
            ])->orderByDesc('done_count')->take(6)->get(['id', 'name']),
        ]]);
    }

    public function index()
    {
        return response()->json(['success' => true, 'data' => Report::with('user:id,name')->latest()->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', 'in:Tasks,Projects,Teams,Activity'],
            'note' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $data['user_id'] = $request->user()->id;

        return response()->json(['success' => true, 'message' => 'Report submitted', 'data' => Report::create($data)], 201);
    }
}
