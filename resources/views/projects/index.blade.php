@extends('layouts.app')
@section('title', 'Projects')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold">All Projects</h1>
</div>

@if (session('success'))
    <div class="bg-green-50 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
@endif
@if ($errors->any())
    <div class="bg-red-50 text-red-600 p-3 rounded mb-4">{{ $errors->first() }}</div>
@endif

<form method="GET" class="flex gap-3 mb-6">
    <input name="search" value="{{ request('search') }}" placeholder="Search" class="border rounded-lg p-2">
    <select name="status" class="border rounded-lg p-2">
        <option value="">All</option>
        <option value="active" @selected(request('status') == 'active')>Active</option>
        <option value="done" @selected(request('status') == 'done')>Done</option>
    </select>
    <button class="bg-gray-800 text-white rounded-lg px-4">Filter</button>
</form>

<div class="bg-white rounded-xl shadow-sm overflow-hidden mb-8">
    <table class="w-full text-sm text-left">
        <thead class="text-gray-500 border-b">
            <tr>
                <th class="p-4">Project Name</th>
                <th>Start Date</th>
                <th>Deadline</th>
                <th>Status</th>
                <th>Files</th>
                <th>Priority</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($projects as $p)
            <tr class="border-b">
                <td class="p-4"><a href="/projects/{{ $p->id }}" class="text-blue-600">{{ $p->name }}</a></td>
                <td>{{ $p->start_date?->format('Y-m-d') }}</td>
                <td>{{ $p->deadline?->format('Y-m-d') }}</td>
                <td>{{ $p->status }}</td>
                <td>{{ $p->files_count }} Files</td>
                <td>{{ ucfirst($p->priority) }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="p-6 text-center text-gray-400">No projects yet</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<h2 class="text-lg font-semibold mb-3">Add Project</h2>
<form method="POST" action="/projects" class="bg-white p-6 rounded-xl shadow-sm grid grid-cols-2 gap-4">
    @csrf
    <input name="name" placeholder="Project name" class="border rounded-lg p-2" required>
    <input name="type" placeholder="Type (e.g. SaaS)" class="border rounded-lg p-2">
    <input type="date" name="start_date" class="border rounded-lg p-2">
    <input type="date" name="deadline" class="border rounded-lg p-2">
    <select name="priority" class="border rounded-lg p-2">
        <option value="low">Low</option>
        <option value="medium" selected>Medium</option>
        <option value="high">High</option>
    </select>
    <textarea name="description" placeholder="Description" class="border rounded-lg p-2 col-span-2"></textarea>
    <button class="bg-blue-600 text-white rounded-lg p-2 col-span-2">Add Project</button>
</form>
@endsection
