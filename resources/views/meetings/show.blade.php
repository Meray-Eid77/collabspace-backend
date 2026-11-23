@extends('layouts.app')
@section('title', $meeting->title)

@section('content')
<p class="text-sm text-gray-400"><a href="/meetings">Meetings</a> &gt; {{ $meeting->title }}</p>

<div class="flex justify-between items-start mb-6">
    <div>
        <h1 class="text-2xl font-semibold">{{ $meeting->title }}</h1>
        <p class="text-sm text-gray-500">
            {{ $meeting->project?->name ?? 'No project' }} ·
            {{ $meeting->start_at->format('M d, Y') }} ·
            {{ $meeting->start_at->format('H:i') }} - {{ $meeting->end_at->format('H:i') }}
        </p>
        <p class="text-sm text-gray-500 mt-1">
            Participants: {{ $meeting->participants->pluck('name')->implode(', ') }}
        </p>
    </div>
    <div class="flex gap-2">
        @if ($meeting->link)
            <a href="{{ $meeting->link }}" target="_blank" class="bg-blue-600 text-white rounded-lg px-4 py-2 text-sm">Join</a>
        @endif
        <form method="POST" action="/meetings/{{ $meeting->id }}">
            @csrf @method('DELETE')
            <button class="border border-red-300 text-red-500 rounded-lg px-4 py-2 text-sm">Delete</button>
        </form>
    </div>
</div>

@if (session('success'))
    <div class="bg-green-50 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
@endif

@if ($meeting->description)
    <p class="bg-white p-4 rounded-xl shadow-sm text-sm text-gray-600 mb-6">{{ $meeting->description }}</p>
@endif

<div class="grid grid-cols-2 gap-6">
    <div class="bg-white p-6 rounded-xl shadow-sm">
        <div class="flex justify-between items-center mb-3">
            <h3 class="font-semibold">AI Generate Summary</h3>
            <form method="POST" action="/meetings/{{ $meeting->id }}/summary">
                @csrf
                <button class="bg-gray-800 text-white rounded-lg px-3 py-1 text-sm">Generate</button>
            </form>
        </div>
        @if ($meeting->summary)
            <p class="text-sm text-gray-600 whitespace-pre-line">{{ $meeting->summary }}</p>
        @else
            <p class="text-sm text-gray-400">No summary yet</p>
        @endif
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm">
        <h3 class="font-semibold mb-3">Meeting Transcripts</h3>
        <form method="GET" class="mb-3">
            <input name="search" value="{{ request('search') }}" placeholder="Search Transcript" class="border rounded-lg p-2 w-full text-sm">
        </form>
        @forelse ($transcripts as $t)
            <div class="text-sm py-2 border-b last:border-0">
                <p class="text-xs text-gray-400">{{ $t->timestamp }} · {{ $t->speaker?->name ?? 'Unknown' }}</p>
                <p>{{ $t->text }}</p>
            </div>
        @empty
            <p class="text-sm text-gray-400">No transcript</p>
        @endforelse
    </div>
</div>
@endsection
