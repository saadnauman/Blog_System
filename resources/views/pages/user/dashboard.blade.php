@extends('layouts.layout')

@section('content')
<h1 class="text-2xl font-bold mb-6">User Dashboard</h1>
<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-100 dark:border-gray-700 mb-8">
    <h2 class="font-semibold text-lg text-gray-900 dark:text-gray-100 mb-4">Profile</h2>
    <div class="space-y-2">
        <div class="flex">
            <span class="text-gray-600 dark:text-gray-400 w-20">Name:</span>
            <span class="text-gray-900 dark:text-gray-100">{{ Auth::user()->name }}</span>
        </div>
        <div class="flex">
            <span class="text-gray-600 dark:text-gray-400 w-20">Email:</span>
            <span class="text-gray-900 dark:text-gray-100">{{ Auth::user()->email }}</span>
        </div>
        <div class="flex">
            <span class="text-gray-600 dark:text-gray-400 w-20">Role:</span>
            <span class="text-gray-900 dark:text-gray-100 capitalize">{{ Auth::user()->role }}</span>
        </div>
    </div>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <a href="/posts/create" class="block bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow p-6 border border-gray-100 dark:border-gray-700 hover:border-indigo-400 dark:hover:border-indigo-500">
        <h2 class="font-semibold text-lg text-indigo-700 dark:text-indigo-300 mb-2">Create Post</h2>
        <p class="text-gray-500 dark:text-gray-400">Write a new blog post.</p>
    </a>
    <a href="/user/posts" class="block bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow p-6 border border-gray-100 dark:border-gray-700 hover:border-indigo-400 dark:hover:border-indigo-500">
        <h2 class="font-semibold text-lg text-indigo-700 dark:text-indigo-300 mb-2">My Posts</h2>
        <p class="text-gray-500 dark:text-gray-400">View and edit your posts.</p>
    </a>
</div>
@endsection 