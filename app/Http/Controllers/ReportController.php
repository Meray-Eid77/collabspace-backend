<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Report;
use App\Models\Task;
use App\Models\Team;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    private function rate(int $part, int $total): int
    {
        return $total ? (int) round($part / $total * 100) : 0;
    }

    public function index(Request $request)
    {
        // البطاقات الثلاث
        $taskRate = $this->rate(Task::where('status', 'completed')->count(), Task::count());
        $projectRate = $this->rate(Project::where('status', 'done')->count(), Project::count());
        $teamRate = $this->rate(
            Team::whereHas('members.tasks', fn ($q) => $q->where('tasks.status', '!=', 'todo'))->count(),
            Team::count()
        );

        // مقارنة الشهر الحالي بالسابق (مهام مكتملة)
        $thisMonth = Task::where('status', 'completed')->whereMonth('completed_at', now()->month)->count();
        $lastMonth = Task::where('status', 'completed')->whereMonth('completed_at', now()->subMonth()->month)->count();
        $change = $lastMonth ? round(($thisMonth - $lastMonth) / $lastMonth * 100) : 0;

        // المهام المكتملة: قبل الموعد أو بعده
        $completed = Task::where('status', 'completed')->whereNotNull('due_date')->get();
        $onTime = $completed->filter(fn ($t) => $t->completed_at && $t->completed_at->toDateString() <= $t->due_date->toDateString())->count();
        $late = $completed->count() - $onTime;

        // تقدم كل مشروع
        $projects = Project::withCount([
            'tasks',
            'tasks as done_count' => fn ($q) => $q->where('status', 'completed'),
        ])->get();

        // نشاط الأعضاء (مهام مكتملة لكل عضو)
        $workers = \App\Models\User::withCount([
            'tasks as done_count' => fn ($q) => $q->where('tasks.status', 'completed'),
        ])->orderByDesc('done_count')->take(6)->get();

        $reports = Report::with('user')->latest()->take(5)->get();

        return view('reports.index', compact(
            'taskRate', 'projectRate', 'teamRate', 'change', 'onTime', 'late', 'projects', 'workers', 'reports'
        ));
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
        Report::create($data);

        return redirect('/reports')->with('success', 'Report submitted');
    }
}
