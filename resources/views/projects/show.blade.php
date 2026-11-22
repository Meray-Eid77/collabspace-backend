@extends('layouts.app')
@section('title', $project->name)

@section('content')
<h1 class="text-2xl font-semibold">{{ $project->name }}</h1>
<p class="text-gray-500 mt-2">{{ $project->description }}</p>
@endsection
