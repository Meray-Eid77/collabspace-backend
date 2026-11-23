@extends('layouts.app')
@section('title', $project->name)

@section('content')
@php
    $tabs = ['overview' => 'Overview', 'tasks' => 'Tasks', 'teams' => 'Teams', 'files' => 'Files'];
    $statusLabels = ['todo' => 'To-do', 'in_progress' => 'In Progress', 'in_review' => 'In Review', 'completed' => 'Completed'];
@endphp

<p class="text-sm text-gray-400"><a href="/projects">Projects</a> &gt; {{ $project->name }}</p>
<div class="flex items-center gap-3 mb-1">
    <h1 class="text-3xl font-semibold">{{ $project->name }}</h1>
    <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded">{{ $stats['percent'] }}% Completed</span>
</div>
<p class="text-sm text-gray-500 mb-4">
    Start: {{ $project->start_date?->format('M d') ?? '-' }} · Deadline: {{ $project->deadline?->format('M d') ?? '-' }}
</p>

@if (session('success'))
    <div class="bg-green-50 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
@endif
@if ($errors->any())
    <div class="bg-red-50 text-red-600 p-3 rounded mb-4">{{ $errors->first() }}</div>
@endif

<div class="flex gap-6 border-b mb-6">
    @foreach ($tabs as $key => $label)
        <a href="?tab={{ $key }}"
           class="pb-2 {{ $tab == $key ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'text-gray-500' }}">
            {{ $label }}
        </a>
    @endforeach
</div>

{{-- ============ OVERVIEW ============ --}}
@if ($tab == 'overview')
<div class="grid grid-cols-3 gap-6">
    <div class="col-span-2 space-y-6">
        <div class="bg-white p-6 rounded-xl shadow-sm">
            <h3 class="font-semibold mb-2">Description</h3>
            <p class="text-gray-600 text-sm">{{ $project->description ?: 'No description' }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm">
            <h3 class="font-semibold mb-4">Product Details</h3>
            <div class="grid grid-cols-3 gap-4 text-sm">
                <div><p class="text-gray-400">Project Type</p><p>{{ $project->type ?? '-' }}</p></div>
                <div><p class="text-gray-400">Start Date</p><p>{{ $project->start_date?->format('M d') ?? '-' }}</p></div>
                <div><p class="text-gray-400">Deadline</p><p>{{ $project->deadline?->format('M d') ?? '-' }}</p></div>
                <div><p class="text-gray-400">Priority</p><p>{{ ucfirst($project->priority) }}</p></div>
                <div><p class="text-gray-400">Total Tasks</p><p>{{ $stats['total'] }}</p></div>
                <div><p class="text-gray-400">Owner</p><p>{{ $project->owner->name }}</p></div>
            </div>
        </div>
    </div>
    <div class="space-y-6">
        <div class="bg-white p-6 rounded-xl shadow-sm">
            <h3 class="font-semibold mb-3">Project Progress</h3>
            <p class="text-4xl font-semibold">{{ $stats['percent'] }}%</p>
            <div class="w-full bg-gray-100 rounded-full h-2 my-3">
                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $stats['percent'] }}%"></div>
            </div>
            <div class="flex justify-between text-sm text-gray-500">
                <span>{{ $stats['todo'] }} pending</span>
                <span>{{ $stats['in_progress'] }} in progress</span>
                <span>{{ $stats['completed'] }} done</span>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm">
            <h3 class="font-semibold mb-3">Team Members</h3>
            @php $members = $project->teams->flatMap->members->unique('id'); @endphp
            @forelse ($members as $m)
                <p class="text-sm py-1">{{ $m->name }}</p>
            @empty
                <p class="text-sm text-gray-400">No members yet</p>
            @endforelse
        </div>
    </div>
</div>
@endif

{{-- ============ TASKS ============ --}}
@if ($tab == 'tasks')
<div class="grid grid-cols-4 gap-4">
    @foreach ($statusLabels as $status => $label)
        <div class="bg-white rounded-xl shadow-sm p-3">
            <p class="font-semibold text-sm mb-3">{{ $label }}</p>
            <div class="space-y-2">
                @forelse ($project->tasks->where('status', $status) as $task)
                    <div class="border rounded-lg p-3 text-sm">
                        <p class="font-semibold">{{ $task->title }}</p>
                        <p class="text-xs text-gray-400">{{ $task->due_date?->format('M d') }} · {{ ucfirst($task->priority) }}</p>
                    </div>
                @empty
                    <p class="text-xs text-gray-300">Empty</p>
                @endforelse
            </div>
        </div>
    @endforeach
</div>
<p class="text-sm text-gray-500 mt-4">To add or move tasks, <a class="text-blue-600" href="/tasks?project_id={{ $project->id }}">open the task board</a>.</p>
@endif

{{-- ============ TEAMS ============ --}}
@if ($tab == 'teams')
<div class="grid grid-cols-3 gap-4 mb-8">
    @forelse ($project->teams as $team)
        <div class="bg-white rounded-xl shadow-sm p-4">
            <h3 class="font-semibold mb-3">{{ $team->name }}</h3>
            @foreach ($team->members as $m)
                <div class="flex justify-between items-center text-sm py-1">
                    <span>{{ $m->name }} <span class="text-xs text-gray-400">{{ $m->pivot->role }}</span></span>
                    <form method="POST" action="/teams/{{ $team->id }}/members/{{ $m->id }}">
                        @csrf @method('DELETE')
                        <button class="text-red-400 text-xs">x</button>
                    </form>
                </div>
            @endforeach

            <form method="POST" action="/teams/{{ $team->id }}/members" class="mt-3 space-y-2">
                @csrf
                <select name="user_id" class="border rounded w-full p-1 text-sm" required>
                    <option value="">Add member</option>
                    @foreach ($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
                <input name="role" placeholder="Role (e.g. Leader)" class="border rounded w-full p-1 text-sm">
                <button class="bg-gray-800 text-white rounded w-full p-1 text-sm">Add</button>
            </form>
        </div>
    @empty
        <p class="text-gray-400 col-span-3">No teams yet</p>
    @endforelse
</div>

<form method="POST" action="/projects/{{ $project->id }}/teams" class="bg-white p-4 rounded-xl shadow-sm flex gap-3">
    @csrf
    <input name="name" placeholder="New team name (e.g. Backend)" class="border rounded-lg p-2 flex-1" required>
    <button class="bg-blue-600 text-white rounded-lg px-4">Add Team</button>
</form>
@endif


@if ($tab == 'files')
<form method="POST" action="/projects/{{ $project->id }}/files" enctype="multipart/form-data"
      class="bg-white p-4 rounded-xl shadow-sm flex gap-3 mb-6">
    @csrf
    <input type="file" name="file" class="flex-1" required>
    <button class="bg-blue-600 text-white rounded-lg px-4">Upload File</button>
</form>

<div class="grid grid-cols-3 gap-4">
    @forelse ($project->files as $f)
        <div class="bg-white rounded-xl shadow-sm p-4 flex justify-between items-center">
            <div>
                <p class="font-semibold text-sm">{{ $f->name }}</p>
                <p class="text-xs text-gray-400">By {{ $f->uploader->name }} · {{ number_format($f->size / 1048576, 1) }} MB · {{ $f->created_at->diffForHumans() }}</p>
            </div>
            <a href="/files/{{ $f->id }}/download" class="text-blue-600 text-sm">Download</a>
        </div>
    @empty
        <p class="text-gray-400 col-span-3">No files yet</p>
    @endforelse
</div>
@endif
@endsection
