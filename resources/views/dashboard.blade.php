@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="grid grid-cols-4 gap-4">
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
@endsection
