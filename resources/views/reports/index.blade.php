@extends('layouts.app')
@section('title', 'Reports')

@section('content')
@if (session('success'))
    <div class="bg-green-50 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
@endif
@if ($errors->any())
    <div class="bg-red-50 text-red-600 p-3 rounded mb-4">{{ $errors->first() }}</div>
@endif

<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white p-6 rounded-xl shadow-sm">
        <p class="text-gray-500">Task Completion Rate</p>
        <p class="text-4xl font-semibold">{{ $taskRate }} %</p>
        <p class="text-xs {{ $change >= 0 ? 'text-green-600' : 'text-red-500' }}">{{ $change >= 0 ? '+' : '' }}{{ $change }}% From last month</p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm">
        <p class="text-gray-500">Projects Completion Rate</p>
        <p class="text-4xl font-semibold">{{ $projectRate }} %</p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm">
        <p class="text-gray-500">Teams Activity Rate</p>
        <p class="text-4xl font-semibold">{{ $teamRate }} %</p>
    </div>
</div>

<div class="grid grid-cols-2 gap-6 mb-6">
    <div class="bg-white p-6 rounded-xl shadow-sm">
        <h3 class="font-semibold mb-4">Task Completion Time vs Deadline</h3>
        @php $sum = max($onTime + $late, 1); @endphp
        <div class="flex items-end gap-8 h-32">
            <div class="flex-1 text-center">
                <div class="bg-blue-600 rounded-t" style="height: {{ $onTime / $sum * 100 }}%"></div>
                <p class="text-xs mt-1">On time ({{ $onTime }})</p>
            </div>
            <div class="flex-1 text-center">
                <div class="bg-red-400 rounded-t" style="height: {{ $late / $sum * 100 }}%"></div>
                <p class="text-xs mt-1">Late ({{ $late }})</p>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm">
        <h3 class="font-semibold mb-4">Projects Progress</h3>
        @foreach ($projects as $p)
            @php $pct = $p->tasks_count ? round($p->done_count / $p->tasks_count * 100) : 0; @endphp
            <div class="mb-3">
                <div class="flex justify-between text-sm"><span>{{ $p->name }}</span><span>{{ $pct }}%</span></div>
                <div class="w-full bg-gray-100 rounded-full h-2"><div class="bg-green-400 h-2 rounded-full" style="width: {{ $pct }}%"></div></div>
            </div>
        @endforeach
    </div>
</div>

<div class="grid grid-cols-2 gap-6">
    <div class="bg-white p-6 rounded-xl shadow-sm">
        <h3 class="font-semibold mb-4">Worker Activity Rate</h3>
        @php $max = max($workers->max('done_count'), 1); @endphp
        @foreach ($workers as $w)
            <div class="mb-3">
                <div class="flex justify-between text-sm"><span>{{ $w->name }}</span><span>{{ $w->done_count }} done</span></div>
                <div class="w-full bg-gray-100 rounded-full h-2"><div class="bg-blue-600 h-2 rounded-full" style="width: {{ $w->done_count / $max * 100 }}%"></div></div>
            </div>
        @endforeach
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm">
        <h3 class="font-semibold mb-4">Submit a detailed report</h3>
        <form method="POST" action="/reports" class="space-y-3">
            @csrf
            <select name="type" class="border rounded-lg p-2 w-full" required>
                <option value="">Select Report Type</option>
                <option>Tasks</option><option>Projects</option><option>Teams</option><option>Activity</option>
            </select>
            <input name="note" placeholder="Add Note" class="border rounded-lg p-2 w-full">
            <div class="grid grid-cols-2 gap-3">
                <input type="date" name="start_date" class="border rounded-lg p-2">
                <input type="date" name="end_date" class="border rounded-lg p-2">
            </div>
            <button class="bg-blue-600 text-white rounded-lg px-4 py-2 float-right">Submit</button>
        </form>
        <div class="clear-both pt-4 text-sm">
            @foreach ($reports as $r)
                <p class="text-gray-500 py-1">{{ $r->type }} · {{ $r->user->name }} · {{ $r->created_at->diffForHumans() }}</p>
            @endforeach
        </div>
    </div>
</div>
@endsection
