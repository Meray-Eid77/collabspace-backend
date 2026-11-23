@extends('layouts.app')
@section('title', 'Chats')

@section('content')
<div class="grid grid-cols-3 gap-6" style="height: 70vh">

    <div class="bg-white rounded-xl shadow-sm p-4 overflow-y-auto">
        <h1 class="text-2xl font-semibold mb-3">Chats</h1>

        <div class="flex gap-4 text-sm mb-4 border-b">
            <a href="/chats?type=direct" class="pb-2 {{ $type == 'direct' ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'text-gray-500' }}">Inbox</a>
            <a href="/chats?type=project" class="pb-2 {{ $type == 'project' ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'text-gray-500' }}">Project Chats</a>
        </div>

        @forelse ($conversations as $c)
            <a href="/chats?type={{ $type }}&conversation={{ $c->id }}"
               class="block p-3 rounded-lg mb-1 {{ $active?->id == $c->id ? 'bg-blue-50' : 'hover:bg-gray-50' }}">
                <div class="flex justify-between text-sm">
                    <span class="font-semibold">{{ $c->displayName(auth()->id()) }}</span>
                    <span class="text-xs text-gray-400">{{ $c->latestMessage?->created_at->format('H:i') }}</span>
                </div>
                <p class="text-xs text-gray-400 truncate">{{ $c->latestMessage?->body ?? ($c->latestMessage?->attachment_name ? 'Attachment' : 'No messages yet') }}</p>
            </a>
        @empty
            <p class="text-sm text-gray-400 mb-4">No conversations yet</p>
        @endforelse


        <div class="mt-4 pt-4 border-t">
            @if ($type == 'direct')
                <form method="POST" action="/chats/direct" class="flex gap-2">
                    @csrf
                    <select name="user_id" class="border rounded p-1 text-sm flex-1" required>
                        <option value="">New chat with...</option>
                        @foreach ($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                    <button class="bg-gray-800 text-white rounded px-3 text-sm">Start</button>
                </form>
            @else
                <form method="POST" action="/chats/project" class="space-y-2">
                    @csrf
                    <select name="project_id" class="border rounded p-1 text-sm w-full" required>
                        <option value="">New channel for project...</option>
                        @foreach ($projects as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                    <input name="name" placeholder="Channel name (optional)" class="border rounded p-1 text-sm w-full">
                    <button class="bg-gray-800 text-white rounded w-full p-1 text-sm">Create Channel</button>
                </form>
            @endif
        </div>
    </div>


    <div class="col-span-2 bg-white rounded-xl shadow-sm flex flex-col">
        @if ($active)
            <div class="p-4 border-b">
                <p class="font-semibold">{{ $active->displayName(auth()->id()) }}</p>
                <p class="text-xs text-gray-400">{{ $active->members->count() }} members</p>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-3">
                @forelse ($messages as $m)
                    @php $mine = $m->sender_id == auth()->id(); @endphp
                    <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-sm px-4 py-2 rounded-xl text-sm {{ $mine ? 'bg-blue-600 text-white' : 'bg-gray-100' }}">
                            @unless ($mine)
                                <p class="text-xs text-gray-400">{{ $m->sender->name }}</p>
                            @endunless
                            @if ($m->body)<p>{{ $m->body }}</p>@endif
                            @if ($m->attachment_name)
                                <a href="/messages/{{ $m->id }}/attachment" class="underline text-xs">{{ $m->attachment_name }}</a>
                            @endif
                            <p class="text-[10px] opacity-60 text-right">{{ $m->created_at->format('H:i') }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center">No messages yet, say hi!</p>
                @endforelse
            </div>

            <form method="POST" action="/chats/{{ $active->id }}/messages" enctype="multipart/form-data" class="p-4 border-t flex gap-2">
                @csrf
                <input name="body" placeholder="Write a message" class="border rounded-lg p-2 flex-1" autocomplete="off">
                <input type="file" name="attachment" class="text-xs w-40">
                <button class="bg-blue-600 text-white rounded-lg px-4">Send</button>
            </form>
        @else
            <div class="flex-1 flex items-center justify-center text-gray-400">Select Chat</div>
        @endif
    </div>
</div>
@endsection
