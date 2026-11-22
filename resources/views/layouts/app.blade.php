<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Collabspace')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">
    <nav class="bg-white border-b px-8 py-4 flex items-center justify-between">
        <div class="flex items-center gap-8">
            <span class="text-xl font-semibold">Collabspace</span>
            <div class="flex gap-4 text-sm">
                <a href="/dashboard" class="{{ request()->is('dashboard') ? 'text-blue-600 font-semibold' : '' }}">Dashboard</a>
                <a href="/projects">Projects</a>
                <a href="/tasks">Tasks</a>
                <a href="/chats">Chats</a>
                <a href="/meetings">Meetings</a>
                <a href="/reports">Reports</a>
            </div>
        </div>
        <div class="flex items-center gap-4 text-sm">
            <a href="/profile">{{ auth()->user()->name }}</a>
            <form method="POST" action="/logout">
                @csrf
                <button class="text-red-500">Logout</button>
            </form>
        </div>
    </nav>

    <main class="p-8 max-w-7xl mx-auto">
        @yield('content')
    </main>
</body>
</html>
