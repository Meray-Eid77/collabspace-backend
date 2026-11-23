@extends('layouts.app')
@section('title', 'Profile')

@section('content')
@php $tabs = ['overview' => 'Overview', 'tasks' => 'Tasks', 'files' => 'Files']; @endphp

<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-4">
        <div class="w-16 h-16 rounded-full bg-gray-200 overflow-hidden">
            @if ($user->avatar)
                <img src="{{ asset('storage/' . $user->avatar) }}" class="w-full h-full object-cover">
            @endif
        </div>
        <div>
            <h1 class="text-2xl font-semibold">{{ $user->name }}</h1>
            <p class="text-sm text-gray-500">{{ $user->job_title }} · Joined {{ $user->created_at->format('M Y') }}</p>
        </div>
    </div>
    <div class="flex gap-4">
        <div class="bg-white p-4 rounded-xl shadow-sm text-center">
            <p class="text-xs text-gray-400">Completed Tasks</p>
            <p class="text-2xl font-semibold">{{ $stats['completed'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm text-center">
            <p class="text-xs text-gray-400">Total Tasks</p>
            <p class="text-2xl font-semibold">{{ $stats['total'] }}</p>
        </div>
    </div>
</div>

<div class="flex justify-between border-b mb-6">
    <div class="flex gap-6">
        @foreach ($tabs as $key => $label)
            <a href="?tab={{ $key }}" class="pb-2 {{ $tab == $key ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'text-gray-500' }}">{{ $label }}</a>
        @endforeach
    </div>
    <a href="/profile/settings" class="text-blue-600 text-sm">Settings</a>
</div>

@if ($tab == 'overview')
    <div class="bg-white p-6 rounded-xl shadow-sm">
        <h3 class="font-semibold mb-2">About</h3>
        <p class="text-sm text-gray-600">{{ $user->bio ?: 'No bio yet' }}</p>
    </div>
@endif

@if ($tab == 'tasks')
    <div class="bg-white p-6 rounded-xl shadow-sm">
        @forelse ($tasks as $t)
            <div class="flex justify-between text-sm py-2 border-b last:border-0">
                <span>{{ $t->title }} <span class="text-xs text-gray-400">({{ $t->project->name }})</span></span>
                <span class="text-gray-500">{{ str_replace('_', ' ', ucfirst($t->status)) }}</span>
            </div>
        @empty
            <p class="text-sm text-gray-400">No tasks assigned</p>
        @endforelse
    </div>
@endif

@if ($tab == 'files')
    <div class="grid grid-cols-3 gap-4">
        @forelse ($files as $f)
            <div class="bg-white rounded-xl shadow-sm p-4 flex justify-between text-sm">
                <span>{{ $f->name }}</span>
                <a href="/files/{{ $f->id }}/download" class="text-blue-600">Download</a>
            </div>
        @empty
            <p class="text-sm text-gray-400 col-span-3">No files uploaded</p>
        @endforelse
    </div>
@endif
@endsection
