<?php

namespace App\Http\Controllers;

use App\Models\Task;

class DashboardController extends Controller
{
    public function index()
    {
        $total = Task::count();
        $stats = [
            'pending' => Task::where('status', 'todo')->count(),
            'in_progress' => Task::where('status', 'in_progress')->count(),
            'completed' => Task::where('status', 'completed')->count(),
            'rate' => $total ? round(Task::where('status', 'completed')->count() / $total * 100) : 0,
        ];

        return view('dashboard', compact('stats'));
    }
}
