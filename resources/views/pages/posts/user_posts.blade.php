@extends('layouts.layout')

@section('content')
<h1 class="text-2xl font-bold mb-6">My Posts</h1>
<div class="mb-4">
    <a href="{{ route('posts.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Create New Post</a>
</div>
<div class="space-y-4">
    @forelse($posts as $post)
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-6 py-4 border border-gray-100 dark:border-gray-700">
        <div class="flex justify-between items-center">
            <div>
                <a href="{{ route('posts.show', $post) }}">
                    <h2 class="font-semibold text-lg text-indigo-700 dark:text-indigo-300">{{ $post->title }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        In {{ $post->category->name ?? 'Uncategorized' }} &middot; {{ $post->created_at->format('Y-m-d') }}
                    </p>
                </a>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('posts.edit', $post) }}" class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-xs transition">Edit</a>
                <form method="POST" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('Delete this post?');">
                    @csrf
                    <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs transition">Delete</button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="text-gray-500 dark:text-gray-400">You have not created any posts yet.</div>
    @endforelse
</div>
<div class="mt-6 flex justify-center">
    {{ $posts->links() }}
</div>
@endsection