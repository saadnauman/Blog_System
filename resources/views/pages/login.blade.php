@extends('layouts.layout')

@section('content')
<h1 class="text-2xl font-bold mb-6">Login</h1>
<form method="POST" action="/login" class="space-y-4">
    @csrf
    <div>
        <x-label for="email">Email</x-label>
        <x-input id="email" name="email" type="email" required autofocus value="{{ old('email') }}" />
        @error('email')<x-alert type="error">{{ $message }}</x-alert>@enderror
    </div>
    <div>
        <x-label for="password">Password</x-label>
        <x-input id="password" name="password" type="password" required />
        @error('password')<x-alert type="error">{{ $message }}</x-alert>@enderror
    </div>
    <div>
        <x-button type="submit" class="w-full">Login</x-button>
    </div>
</form>
<p class="mt-4 text-center text-sm">Don't have an account? <a href="/register" class="text-indigo-600 hover:underline">Register</a></p>
@endsection 