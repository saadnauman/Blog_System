<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Blog System' }}</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen text-gray-900 dark:text-gray-100">
    <nav class="bg-white dark:bg-gray-950 border-b border-gray-200 dark:border-gray-800 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-16">
            <div class="flex items-center space-x-4">
                <a href="/" class="text-xl font-bold text-indigo-700 dark:text-indigo-300 hover:text-indigo-900 dark:hover:text-indigo-200 transition-colors">BlogSystem</a>
                <a href="/posts" class="text-gray-700 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-300 transition-colors">Posts</a>
            </div>
            <div class="flex items-center space-x-2">
                @auth
                    {{-- Authenticated User Links --}}
                    @if(Auth::user()->role === 'admin')
                        <a href="/admin/dashboard" class="text-gray-700 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-300 transition-colors">Admin Dashboard</a>
                        <a href="/admin/categories" class="text-gray-700 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-300 transition-colors">Categories</a>
                    @else
                        <a href="/user/dashboard" class="text-gray-700 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-300 transition-colors">Dashboard</a>
                    @endif
                    <span class="text-gray-500 dark:text-gray-400">|</span>
                    <span class="text-gray-700 dark:text-gray-200">{{ Auth::user()->name }}</span>
                    <form method="POST" action="/logout" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-700 dark:text-gray-200 hover:text-red-600 dark:hover:text-red-400 transition-colors">Logout</button>
                    </form>
                @else
                    {{-- Guest Links --}}
                    <a href="/login" class="text-gray-700 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-300 transition-colors">Login</a>
                    <a href="/register" class="text-gray-700 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-300 transition-colors">Register</a>
                @endauth
            </div>
        </div>
    </nav>
    <main class="max-w-3xl mx-auto py-8 px-4 bg-white dark:bg-gray-900 rounded shadow-sm">
        @if(session('status'))
            <x-alert type="success">{{ session('status') }}</x-alert>
        @endif
        @if(session('error'))
            <x-alert type="error">{{ session('error') }}</x-alert>
        @endif
        @yield('content')
    </main>
</body>
</html> 