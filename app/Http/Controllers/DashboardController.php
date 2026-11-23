<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Meeting;
class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::orderBy('name')->get();

        $tasks = Task::query()
            ->when($request->project_id, fn ($q, $id) => $q->where('project_id', $id));

        $total = (clone $tasks)->count();
        $completed = (clone $tasks)->where('status', 'completed')->count();

        $stats = [
            'pending' => (clone $tasks)->where('status', 'todo')->count(),
            'in_progress' => (clone $tasks)->whereIn('status', ['in_progress', 'in_review'])->count(),
            'completed' => $completed,
            'rate' => $total ? round($completed / $total * 100) : 0,
        ];

        $activeProjects = Project::withCount([
            'tasks',
            'tasks as completed_tasks_count' => fn ($q) => $q->where('status', 'completed'),
        ])->where('status', 'active')->get();

        $upcomingTasks = (clone $tasks)->with('project')
            ->where('status', '!=', 'completed')
            ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
            ->orderBy('due_date')
            ->take(5)
            ->get();

        $teamStatus = User::withCount([
            'tasks',
            'tasks as done_tasks_count' => fn ($q) => $q->where('status', 'completed'),
        ])->orderBy('name')->take(5)->get();

        $recentFiles = ProjectFile::with('uploader')->latest()->take(3)->get();
        $upcomingMeetings = Meeting::with('project')
    ->where('start_at', '>=', now())
    ->orderBy('start_at')
    ->take(3)
    ->get();
        return view('dashboard', compact(
            'projects', 'stats', 'activeProjects', 'upcomingTasks', 'teamStatus', 'recentFiles','upcomingMeetings'
        ));
    }
}
