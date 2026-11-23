@extends('layouts.app')
@section('title', 'Settings')

@section('content')
<h1 class="text-2xl font-semibold mb-6">Profile Settings</h1>

@if (session('success'))
    <div class="bg-green-50 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
@endif
@if ($errors->any())
    <div class="bg-red-50 text-red-600 p-3 rounded mb-4">{{ $errors->first() }}</div>
@endif

<form method="POST" action="/profile" enctype="multipart/form-data" class="bg-white p-6 rounded-xl shadow-sm grid grid-cols-2 gap-4">
    @csrf @method('PUT')

    <div>
        <label class="text-xs text-gray-400">Full Name</label>
        <input name="name" value="{{ old('name', $user->name) }}" class="border rounded-lg p-2 w-full" required>
    </div>
    <div>
        <label class="text-xs text-gray-400">Email Address</label>
        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="border rounded-lg p-2 w-full" required>
    </div>
    <div>
        <label class="text-xs text-gray-400">Phone Number</label>
        <input name="phone" value="{{ old('phone', $user->phone) }}" class="border rounded-lg p-2 w-full">
    </div>
    <div>
        <label class="text-xs text-gray-400">Availability</label>
        <input name="availability" value="{{ old('availability', $user->availability) }}" class="border rounded-lg p-2 w-full">
    </div>
    <div>
        <label class="text-xs text-gray-400">Job Title</label>
        <input name="job_title" value="{{ old('job_title', $user->job_title) }}" class="border rounded-lg p-2 w-full">
    </div>
    <div>
        <label class="text-xs text-gray-400">Years of Experience</label>
        <input type="number" name="years_of_experience" value="{{ old('years_of_experience', $user->years_of_experience) }}" class="border rounded-lg p-2 w-full">
    </div>
    <div class="col-span-2">
        <label class="text-xs text-gray-400">Current Team / Project</label>
        <input name="current_team" value="{{ old('current_team', $user->current_team) }}" class="border rounded-lg p-2 w-full">
    </div>
    <div class="col-span-2">
        <label class="text-xs text-gray-400">About</label>
        <textarea name="bio" rows="3" class="border rounded-lg p-2 w-full">{{ old('bio', $user->bio) }}</textarea>
    </div>
    <div class="col-span-2">
        <label class="text-xs text-gray-400">Avatar</label>
        <input type="file" name="avatar" class="w-full">
    </div>
    <button class="bg-blue-600 text-white rounded-lg p-2 col-span-2">Save</button>
</form>
@endsection
