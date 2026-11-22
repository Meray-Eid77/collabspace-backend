@extends('layouts.app')
@section('title', 'Tasks')

@section('content')
@php
    $columns = [
        'todo' => ['To-do', 'bg-gray-100'],
        'in_progress' => ['In Progress', 'bg-blue-100'],
        'in_review' => ['In Review', 'bg-pink-100'],
        'completed' => ['Completed', 'bg-green-100'],
    ];
    $priorityColors = ['high' => 'text-red-500', 'medium' => 'text-yellow-500', 'low' => 'text-green-500'];
@endphp

@if (session('success'))
    <div class="bg-green-50 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
@endif
@if ($errors->any())
    <div class="bg-red-50 text-red-600 p-3 rounded mb-4">{{ $errors->first() }}</div>
@endif

<form method="GET" class="flex gap-3 mb-6">
    <select name="project_id" class="border rounded-lg p-2">
        <option value="">All Projects</option>
        @foreach ($projects as $p)
            <option value="{{ $p->id }}" @selected(request('project_id') == $p->id)>{{ $p->name }}</option>
        @endforeach
    </select>
    <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="mine" value="1" @checked(request('mine'))> My Tasks
    </label>
    <button class="bg-gray-800 text-white rounded-lg px-4">Filter</button>
</form>

<div class="grid grid-cols-4 gap-4 mb-8">
    @foreach ($columns as $status => [$label, $color])
        <div class="bg-white rounded-xl shadow-sm">
            <div class="{{ $color }} rounded-t-xl p-3 font-semibold text-sm">{{ $label }}</div>
            <div class="p-3 space-y-3">
                @foreach ($tasks[$status] ?? [] as $task)
                    <div class="border rounded-lg p-3 text-sm">
                        <div class="flex justify-between">
                            <span class="font-semibold">{{ $task->title }}</span>
                            <span class="{{ $priorityColors[$task->priority] }} text-xs">{{ ucfirst($task->priority) }}</span>
                        </div>
                        <p class="text-gray-500 text-xs mt-1">{{ $task->project->name }}</p>
                        <p class="text-gray-400 text-xs">{{ $task->due_date?->format('M d') }}</p>

                        <div class="flex gap-2 mt-2">
                            <form method="POST" action="/tasks/{{ $task->id }}/status">
                                @csrf @method('PATCH')
                                <select name="status" onchange="this.form.submit()" class="border rounded text-xs p-1">
                                    @foreach ($columns as $s => [$l])
                                        <option value="{{ $s }}" @selected($task->status == $s)>{{ $l }}</option>
                                    @endforeach
                                </select>
                            </form>
                            <form method="POST" action="/tasks/{{ $task->id }}">
                                @csrf @method('DELETE')
                                <button class="text-red-500 text-xs">Delete</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>

<h2 class="text-lg font-semibold mb-3">Create Task</h2>
<form method="POST" action="/tasks" class="bg-white p-6 rounded-xl shadow-sm grid grid-cols-2 gap-4">
    @csrf
    <input name="title" placeholder="Task title" class="border rounded-lg p-2" required>
    <select name="project_id" class="border rounded-lg p-2" required>
        <option value="">Select project</option>
        @foreach ($projects as $p)
            <option value="{{ $p->id }}">{{ $p->name }}</option>
        @endforeach
    </select>
    <select name="status" class="border rounded-lg p-2">
        @foreach ($columns as $s => [$l])
            <option value="{{ $s }}">{{ $l }}</option>
        @endforeach
    </select>
    <select name="priority" class="border rounded-lg p-2">
        <option value="low">Low</option>
        <option value="medium" selected>Medium</option>
        <option value="high">High</option>
    </select>
    <input type="date" name="due_date" class="border rounded-lg p-2">
    <textarea name="description" placeholder="Description" class="border rounded-lg p-2"></textarea>
    <button class="bg-blue-600 text-white rounded-lg p-2 col-span-2">Add Task</button>
</form>
@endsection
