@extends('layouts.layout')

@section('content')
<h1 class="text-2xl font-bold mb-6">All Posts</h1>
<form method="GET" action="" class="flex flex-wrap gap-2 mb-6" autocomplete="off" id="search-form">
    <div class="relative">
        <x-input name="search" id="search-input" placeholder="Search posts..." class="w-48" value="{{ request('search') }}" autocomplete="off" />
        <div id="suggestions" class="absolute bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 w-full z-50 rounded shadow mt-1 hidden"></div>
    </div>
    <select name="category" class="border-gray-300 rounded-md">
        <option value="">All Categories</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
        @endforeach
    </select>
    <select name="author" class="border-gray-300 rounded-md">
        <option value="">All Authors</option>
        @foreach($authors as $author)
            <option value="{{ $author->id }}" {{ request('author') == $author->id ? 'selected' : '' }}>{{ $author->name }}</option>
        @endforeach
    </select>
    <select name="created_at" class="border-gray-300 rounded-md">
        <option value="">Any Time</option>
        <option value="today" {{ request('created_at') == 'today' ? 'selected' : '' }}>Today</option>
        <option value="week" {{ request('created_at') == 'week' ? 'selected' : '' }}>This Week</option>
    </select>
    <x-button type="submit">Filter</x-button>
</form>

@if(request('search'))
    <h5 class="mb-4 text-gray-700 dark:text-gray-300">Search results for "{{ request('search') }}"</h5>
@endif

<div class="space-y-4">
    @forelse($posts as $post)
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow px-6 py-4 border border-gray-100 dark:border-gray-700 hover:border-indigo-400 dark:hover:border-indigo-500">
        <div class="flex justify-between items-center">
            <div>
                <a href="{{ route('posts.show', $post) }}">
                    <h2 class="font-semibold text-lg text-indigo-700 dark:text-indigo-300">{{ $post->title }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        By {{ $post->user->name ?? 'Unknown' }} in {{ $post->category->name ?? 'Uncategorized' }} &middot; {{ $post->created_at->format('Y-m-d') }}
                    </p>
                </a>
            </div>
            <div class="flex items-center gap-2">
                @auth
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('posts.edit', $post) }}" class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-xs transition">Edit</a>
                        <form method="POST" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('Delete this post?');">
                            @csrf
                            <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs transition">Delete</button>
                        </form>
                    @endif
                @endauth
                <span class="text-indigo-600 dark:text-indigo-300 font-semibold">Read &rarr;</span>
            </div>
        </div>
    </div>
    @empty
    <div class="text-gray-500 dark:text-gray-400">No posts found.</div>
    @endforelse
</div>
<div class="mt-6 flex justify-center">
    {{ $posts->links() }}
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('search-input');
    const suggestions = document.getElementById('suggestions');
    let timeout = null;

    input.addEventListener('input', function() {
        clearTimeout(timeout);
        const query = this.value;
        if (query.length < 2) {
            suggestions.innerHTML = '';
            suggestions.classList.add('hidden');
            return;
        }
        timeout = setTimeout(() => {
            fetch(`/posts/suggestions?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    suggestions.innerHTML = '';
                    if (data.length === 0) {
                        suggestions.classList.add('hidden');
                        return;
                    }
                    data.forEach(item => {
                        const div = document.createElement('div');
                        div.className = 'px-4 py-2 cursor-pointer hover:bg-indigo-100';
                        div.textContent = item.title;
                        div.onclick = () => {
                            input.value = item.title;
                            suggestions.innerHTML = '';
                            suggestions.classList.add('hidden');
                            document.getElementById('search-form').submit();
                        };
                        suggestions.appendChild(div);
                    });
                    suggestions.classList.remove('hidden');
                });
        }, 200);
    });

    document.addEventListener('click', function(e) {
        if (!input.contains(e.target) && !suggestions.contains(e.target)) {
            suggestions.innerHTML = '';
            suggestions.classList.add('hidden');
        }
    });
});
</script>
@endsection