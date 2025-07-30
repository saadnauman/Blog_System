@extends('layouts.layout')

@section('content')
<h1 class="text-2xl font-bold mb-6">Manage Categories</h1>
<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-100 dark:border-gray-700 mb-6">
    <form method="POST" action="/admin/categories/create" class="flex gap-4 items-end">
        @csrf
        <div class="flex-1">
            <x-label for="name">New Category</x-label>
            <x-input id="name" name="name" type="text" required placeholder="Category name" />
        </div>
        <x-button type="submit">Add Category</x-button>
    </form>
</div>
<div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-100 dark:border-gray-700 overflow-hidden">
    <table class="min-w-full">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($categories as $cat)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ $cat->name }}</td>
                <td class="px-6 py-4">
                    <div class="flex gap-2">
                        <form method="POST" action="/admin/categories/{{ $cat->id }}/edit" class="inline">
                            @csrf
                            <x-input name="name" value="{{ $cat->name }}" class="w-32 text-sm" />
                            <x-button type="submit" class="text-xs">Edit</x-button>
                        </form>
                        <form method="POST" action="/admin/categories/{{ $cat->id }}/delete" class="inline">
                            @csrf
                            <x-button type="submit" class="bg-red-600 hover:bg-red-500 text-xs">Delete</x-button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection 