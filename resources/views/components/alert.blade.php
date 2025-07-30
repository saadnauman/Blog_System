@props(['type' => 'info'])

@php
    $base = 'px-4 py-3 rounded relative mb-4';
    $types = [
        'success' => 'bg-green-100 text-green-800 border border-green-200',
        'error' => 'bg-red-100 text-red-800 border border-red-200',
        'info' => 'bg-blue-100 text-blue-800 border border-blue-200',
    ];
@endphp

<div class="{{ $base . ' ' . ($types[$type] ?? $types['info']) }}" role="alert">
    {{ $slot }}
</div> 