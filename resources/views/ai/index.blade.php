@extends('layouts.app')
@section('title', 'Ask AI')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-xl shadow-sm flex flex-col" style="height: 70vh">
    <div class="p-4 border-b flex justify-between items-center">
        <h1 class="font-semibold">Collabspace AI</h1>
        @if ($messages->isNotEmpty())
            <form method="POST" action="/ai">
                @csrf @method('DELETE')
                <button class="text-xs text-red-500">Clear chat</button>
            </form>
        @endif
    </div>

    <div class="flex-1 overflow-y-auto p-4 space-y-3">
        @forelse ($messages as $m)
            <div class="flex {{ $m->role == 'user' ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-md px-4 py-2 rounded-xl text-sm {{ $m->role == 'user' ? 'bg-blue-600 text-white' : 'bg-gray-100' }}">
                    {{ $m->content }}
                </div>
            </div>
        @empty
            <div class="h-full flex flex-col items-center justify-center text-center">
                <p class="text-xl font-semibold">Hi There 👋</p>
                <p class="text-sm text-gray-500">Start chatting with Collabspace</p>
            </div>
        @endforelse
    </div>

    <form method="POST" action="/ai" class="p-4 border-t flex gap-2">
        @csrf
        <input name="message" placeholder="Ask me anything about..." class="border rounded-lg p-2 flex-1" required autocomplete="off">
        <button class="bg-blue-600 text-white rounded-lg px-4">Submit</button>
    </form>
</div>
@endsection
