@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<form method="GET" class="flex gap-3 mb-6">
    <select name="project_id" class="border rounded-lg p-2">
        <option value="">All Projects</option>
        @foreach ($projects as $p)
            <option value="{{ $p->id }}" @selected(request('project_id') == $p->id)>{{ $p->name }}</option>
        @endforeach
    </select>
    <button class="bg-blue-600 text-white rounded-lg px-4">Apply Filter</button>
</form>

<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-6 rounded-xl shadow-sm">
        <p class="text-gray-500">Pending Tasks</p>
        <p class="text-3xl font-semibold">{{ $stats['pending'] }}</p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm">
        <p class="text-gray-500">In Progress</p>
        <p class="text-3xl font-semibold">{{ $stats['in_progress'] }}</p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm">
        <p class="text-gray-500">Completed</p>
        <p class="text-3xl font-semibold">{{ $stats['completed'] }}</p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm">
        <p class="text-gray-500">Completion Rate</p>
        <p class="text-3xl font-semibold">{{ $stats['rate'] }}%</p>
    </div>
</div>

<div class="grid grid-cols-3 gap-6 mb-6">
    <div class="bg-white p-6 rounded-xl shadow-sm">
        <div class="flex justify-between mb-4">
            <h3 class="font-semibold">Active Projects</h3>
            <a href="/projects" class="text-blue-600 text-sm">View All</a>
        </div>
        @forelse ($activeProjects as $p)
            @php $pct = $p->tasks_count ? round($p->completed_tasks_count / $p->tasks_count * 100) : 0; @endphp
            <div class="mb-4">
                <div class="flex justify-between text-sm">
                    <a href="/projects/{{ $p->id }}">{{ $p->name }}</a>
                    <span>{{ $pct }}%</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2 mt-1">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                </div>
                <p class="text-xs text-gray-400 mt-1">Tasks: {{ $p->completed_tasks_count }}/{{ $p->tasks_count }}</p>
            </div>
        @empty
            <p class="text-sm text-gray-400">No active projects</p>
        @endforelse
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm col-span-2">
        <div class="flex justify-between mb-4">
            <h3 class="font-semibold">Upcoming & Priority Tasks</h3>
            <a href="/tasks" class="text-blue-600 text-sm">Board</a>
        </div>
        <table class="w-full text-sm text-left">
            <thead class="text-gray-400">
                <tr><th class="pb-2">Task</th><th>Project</th><th>Priority</th><th>Progress</th><th>Due</th></tr>
            </thead>
            <tbody>
            @forelse ($upcomingTasks as $t)
                <tr class="border-t">
                    <td class="py-2">{{ $t->title }}</td>
                    <td>{{ $t->project->name }}</td>
                    <td>{{ ucfirst($t->priority) }}</td>
                    <td>{{ $t->progress }}%</td>
                    <td>{{ $t->due_date?->format('M d') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-4 text-gray-400">No tasks</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="grid grid-cols-3 gap-6">
    <div class="bg-white p-6 rounded-xl shadow-sm">
        <h3 class="font-semibold mb-4">Teams Status</h3>
        @foreach ($teamStatus as $u)
            <div class="flex justify-between text-sm py-2 border-b last:border-0">
                <div>
                    <p>{{ $u->name }}</p>
                    <p class="text-xs text-gray-400">{{ $u->job_title }} · {{ $u->availability }}</p>
                </div>
                <span>{{ $u->done_tasks_count }}/{{ $u->tasks_count }} Tasks</span>
            </div>
        @endforeach
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm">
        <h3 class="font-semibold mb-4">Recent Files</h3>
        @forelse ($recentFiles as $f)
            <div class="flex justify-between text-sm py-2">
                <div>
                    <p>{{ $f->name }}</p>
                    <p class="text-xs text-gray-400">By {{ $f->uploader->name }}</p>
                </div>
                <a href="/files/{{ $f->id }}/download" class="text-blue-600">Download</a>
            </div>
        @empty
            <p class="text-sm text-gray-400">No files yet</p>
        @endforelse
    </div>

   <div class="bg-white p-6 rounded-xl shadow-sm">
    <h3 class="font-semibold mb-2">Upcoming Meetings</h3>
    <p class="text-sm text-gray-400">No upcoming meetings</p>
</div>
</div>
@endsection
