@extends('layouts.layout')

@section('content')
<h1 class="text-2xl font-bold mb-6">User Dashboard</h1>

<!-- Profile Card -->
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

<!-- Notifications -->
@if (auth()->user()->notifications->count())
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-yellow-300 dark:border-yellow-600 mb-8">
        <h2 class="font-semibold text-lg text-yellow-700 dark:text-yellow-300 mb-4">🔔 Notifications</h2>
        <ul class="space-y-2">
            @foreach (auth()->user()->notifications as $notification)
                @php $data = $notification->data; @endphp
                <li class="text-gray-800 dark:text-gray-200 bg-yellow-50 dark:bg-yellow-900 px-4 py-2 rounded">
                    @if (isset($data['post_title'], $data['post_id'], $data['tagger_name']))
                        {{ $data['tagger_name'] }} tagged you in a post titled 
                        <a href="{{ route('posts.show', $data['post_id']) }}" 
                           class="text-blue-600 dark:text-blue-300 underline">
                            "{{ $data['post_title'] }}"
                        </a>.
                    @else
                        🔔 You have a new notification.
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
@else
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-100 dark:border-gray-700 mb-8">
        <h2 class="font-semibold text-lg text-gray-900 dark:text-gray-100 mb-4">🔔 Notifications</h2>
        <p class="text-gray-600 dark:text-gray-400">You have no new notifications.</p>
    </div>
@endif

<!-- Post Actions -->
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
