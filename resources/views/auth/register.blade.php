<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @include('partials.theme')
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow p-8 w-full max-w-md">
        <h1 class="text-2xl font-semibold text-center">Sign Up</h1>
        <p class="text-center text-gray-500 text-sm mb-6">Connect with every application!</p>

        @if ($errors->any())
            <div class="bg-red-50 text-red-600 text-sm p-3 rounded mb-4">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="/register" class="space-y-4">
            @csrf
            <input type="text" name="name" value="{{ old('name') }}" placeholder="Full Name" class="w-full border rounded-lg p-3">
            <input type="email" name="email" value="{{ old('email') }}" placeholder="Your Email" class="w-full border rounded-lg p-3">
            <input type="password" name="password" placeholder="Password" class="w-full border rounded-lg p-3">
            <input type="password" name="password_confirmation" placeholder="Confirm Password" class="w-full border rounded-lg p-3">
            <button class="w-full bg-blue-600 text-white rounded-full p-3">Sign up</button>
        </form>

        <p class="text-center text-sm mt-4">Already have an account? <a href="/login" class="text-blue-600 font-semibold">Sign In</a></p>
    </div>
</body>
</html>
