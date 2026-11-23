@extends('layouts.app')
@section('title', 'Meetings')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Meetings</h1>
    <div class="flex items-center gap-3 text-sm">
        <a href="/meetings?week={{ $weekStart->copy()->subWeek()->format('Y-m-d') }}" class="border rounded px-3 py-1">&lt;</a>
        <span>{{ $weekStart->format('M d') }} - {{ $weekStart->copy()->endOfWeek()->format('M d, Y') }}</span>
        <a href="/meetings?week={{ $weekStart->copy()->addWeek()->format('Y-m-d') }}" class="border rounded px-3 py-1">&gt;</a>
    </div>
</div>

@if (session('success'))
    <div class="bg-green-50 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
@endif
@if ($errors->any())
    <div class="bg-red-50 text-red-600 p-3 rounded mb-4">{{ $errors->first() }}</div>
@endif

<div class="grid grid-cols-7 gap-2 mb-8">
    @foreach ($days as $day)
        <div class="bg-white rounded-xl shadow-sm p-3 min-h-40 {{ $day->isToday() ? 'ring-2 ring-blue-300' : '' }}">
            <p class="text-xs text-gray-400">{{ $day->format('D') }}</p>
            <p class="font-semibold mb-2">{{ $day->format('d') }}</p>
            @foreach ($meetings[$day->format('Y-m-d')] ?? [] as $m)
                <a href="/meetings/{{ $m->id }}" class="block bg-blue-50 rounded p-2 mb-2 text-xs">
                    <p class="font-semibold">{{ $m->title }}</p>
                    <p class="text-gray-500">{{ $m->start_at->format('H:i') }} - {{ $m->end_at->format('H:i') }}</p>
                    <p class="text-gray-400">{{ $m->participants->count() }} people</p>
                </a>
            @endforeach
        </div>
    @endforeach
</div>

<h2 class="text-lg font-semibold mb-3">Add Meeting</h2>
<form method="POST" action="/meetings" class="bg-white p-6 rounded-xl shadow-sm grid grid-cols-2 gap-4">
    @csrf
    <input name="title" placeholder="Meeting title" class="border rounded-lg p-2" required>
    <select name="project_id" class="border rounded-lg p-2">
        <option value="">No project</option>
        @foreach ($projects as $p)
            <option value="{{ $p->id }}">{{ $p->name }}</option>
        @endforeach
    </select>
    <div>
        <label class="text-xs text-gray-400">Start</label>
        <input type="datetime-local" name="start_at" class="border rounded-lg p-2 w-full" required>
    </div>
    <div>
        <label class="text-xs text-gray-400">End</label>
        <input type="datetime-local" name="end_at" class="border rounded-lg p-2 w-full" required>
    </div>
    <input name="link" placeholder="Meeting link (optional)" class="border rounded-lg p-2 col-span-2">
    <textarea name="description" placeholder="Description" class="border rounded-lg p-2 col-span-2"></textarea>
    <div class="col-span-2">
        <p class="text-xs text-gray-400 mb-1">Participants</p>
        <div class="flex flex-wrap gap-3 text-sm">
            @foreach ($users as $u)
                <label class="flex items-center gap-1">
                    <input type="checkbox" name="participants[]" value="{{ $u->id }}"> {{ $u->name }}
                </label>
            @endforeach
        </div>
    </div>
    <button class="bg-blue-600 text-white rounded-lg p-2 col-span-2">Add Meeting</button>
</form>
@endsection
