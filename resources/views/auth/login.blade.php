<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign In</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow p-8 w-full max-w-md">
        <h1 class="text-2xl font-semibold text-center">Sign In</h1>
        <p class="text-center text-gray-500 text-sm mb-6">Welcome back, you've been missed!</p>

        @if ($errors->any())
            <div class="bg-red-50 text-red-600 text-sm p-3 rounded mb-4">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="/login" class="space-y-4">
            @csrf
            <input type="email" name="email" value="{{ old('email') }}" placeholder="Your Email" class="w-full border rounded-lg p-3">
            <input type="password" name="password" placeholder="Password" class="w-full border rounded-lg p-3">
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="remember"> Remember Me</label>
            <button class="w-full bg-blue-600 text-white rounded-full p-3">Log in</button>
        </form>

        <p class="text-center text-sm mt-4">Don't have an account? <a href="/register" class="text-blue-600 font-semibold">Sign Up</a></p>
    </div>
</body>
</html>
