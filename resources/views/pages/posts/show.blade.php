@extends('layouts.layout')

@section('content')
<article class="bg-white dark:bg-gray-800 rounded shadow p-6 border border-gray-100 dark:border-gray-700">
    <h1 class="text-3xl font-bold mb-2 text-indigo-700 dark:text-indigo-300">{{ $post->title }}</h1>
    <div class="text-gray-500 dark:text-gray-400 text-sm mb-4">
        By {{ $post->user->name ?? 'Unknown' }} in {{ $post->category->name ?? 'Uncategorized' }} &middot; {{ $post->created_at->format('Y-m-d') }}
    </div>
    <div class="prose max-w-none mb-6 text-gray-900 dark:text-gray-100">
        {!! nl2br(e($post->body)) !!}
    </div>
    <a href="{{ route('posts.index') }}" class="text-indigo-600 dark:text-indigo-300 hover:underline">&larr; Back to Posts</a>
</article>
@endsection 