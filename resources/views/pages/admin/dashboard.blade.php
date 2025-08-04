@extends('layouts.layout')

@section('content')
<h1 class="text-2xl font-bold mb-6">Admin Dashboard</h1>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <a href="/posts" class="block bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow p-6 border border-gray-100 dark:border-gray-700 hover:border-indigo-400 dark:hover:border-indigo-500">
        <h2 class="font-semibold text-lg text-indigo-700 dark:text-indigo-300 mb-2">Manage Posts</h2>
        <p class="text-gray-500 dark:text-gray-400">View, edit, and delete all blog posts.</p>
    </a>
    <a href="/admin/categories" class="block bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow p-6 border border-gray-100 dark:border-gray-700 hover:border-indigo-400 dark:hover:border-indigo-500">
        <h2 class="font-semibold text-lg text-indigo-700 dark:text-indigo-300 mb-2">Manage Categories</h2>
        <p class="text-gray-500 dark:text-gray-400">Create, edit, and delete categories.</p>
    </a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-100 dark:border-gray-700 mb-6">
    <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100 mb-4">Stats</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="text-center">
            <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">42</div>
            <div class="text-gray-500 dark:text-gray-400 mt-1">Posts</div>
        </div>
        <div class="text-center">
            <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">5</div>
            <div class="text-gray-500 dark:text-gray-400 mt-1">Categories</div>
        </div>
        <div class="text-center">
            <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">8</div>
            <div class="text-gray-500 dark:text-gray-400 mt-1">Users</div>
        </div>
    </div>
</div>

<!-- Notifications Section -->
@if (auth()->user()->notifications->count())
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-yellow-300 dark:border-yellow-600">
        <h2 class="font-semibold text-lg text-yellow-700 dark:text-yellow-300 mb-4">🔔 Notifications</h2>

        <ul class="space-y-2">
            @foreach (auth()->user()->notifications as $notification)
                <li class="text-gray-800 dark:text-gray-200 bg-yellow-50 dark:bg-yellow-900 px-4 py-2 rounded">
                    @php
                        $data = $notification->data;
                    @endphp

                    @if (isset($data['post_id']))
                        {{ $data['tagger_name'] ?? 'Someone' }} tagged you in a post titled
                        <a href="{{ route('posts.show', $data['post_id']) }}"
                           class="text-blue-600 dark:text-blue-300 underline">
                            "{{ $data['post_title'] ?? 'Untitled' }}"
                        </a>.
                    @else
                        <span>{{ $data['message'] ?? 'You have a new notification.' }}</span>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
@endif
@endsection
