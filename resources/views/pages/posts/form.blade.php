@extends('layouts.layout')

@section('content')
<h1 class="text-2xl font-bold mb-6">{{ $edit ? 'Edit Post' : 'Create Post' }}</h1>
<form method="POST" action="{{ $edit ? route('posts.update', $post) : route('posts.store') }}" class="space-y-4">
    @csrf
    @if($edit)
        @method('POST')
    @endif

    <!-- Title -->
    <div>
        <x-label for="title">Title</x-label>
        <x-input id="title" name="title" type="text" required value="{{ old('title', $post->title ?? '') }}" />
        @error('title')<x-alert type="error">{{ $message }}</x-alert>@enderror
    </div>

    <!-- Category -->
    <div>
        <x-label for="category_id">Category</x-label>
        <select id="category_id" name="category_id" class="border-gray-300 rounded-md w-full">
            <option value="">Select Category</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ old('category_id', $post->category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
        @error('category_id')<x-alert type="error">{{ $message }}</x-alert>@enderror
    </div>

    <!-- Body (textarea with mentions) -->
    <div>
        <x-label for="body">Body</x-label>
        <textarea id="body" name="body" rows="6" class="border-gray-300 rounded-md w-full">{{ old('body', $post->body ?? '') }}</textarea>
        @error('body')<x-alert type="error">{{ $message }}</x-alert>@enderror
    </div>

    <!-- is_published checkbox -->
    @if($isAdmin)
    <div class="flex items-center gap-2">
        <input type="checkbox" id="is_published" name="is_published" class="rounded border-gray-300" {{ old('is_published', $post->is_published ?? false) ? 'checked' : '' }}>
        <x-label for="is_published">Published</x-label>
    </div>
    @endif

    <div>
        <x-button type="submit" class="w-full">{{ $edit ? 'Update' : 'Create' }} Post</x-button>
    </div>
</form>
@endsection

@section('scripts')
<!-- Include Tribute.js from CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tributejs/5.1.3/tribute.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/tributejs/5.1.3/tribute.min.js"></script>

<script>
    const tribute = new Tribute({
        trigger: "@",
        values: async (text, cb) => {
            try {
                const response = await fetch(`/api/user-mentions?search=${text}`);
                const users = await response.json();
                cb(users.map(user => ({ key: user.name, value: user.name })));
            } catch (error) {
                cb([]);
            }
        },
        selectTemplate: function (item) {
            return '@' + item.original.value;
        }
    });

    tribute.attach(document.getElementById('body'));
</script>
@endsection
