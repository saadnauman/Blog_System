@extends('layouts.layout')

@section('content')
<h1 class="text-2xl font-bold mb-6">Register</h1>
<form method="POST" action="/register" class="space-y-4">
    @csrf
    <div>
        <x-label for="name">Name</x-label>
        <x-input id="name" name="name" type="text" required autofocus value="{{ old('name') }}" />
        @error('name')<x-alert type="error">{{ $message }}</x-alert>@enderror
    </div>
    <div>
        <x-label for="email">Email</x-label>
        <x-input id="email" name="email" type="email" required value="{{ old('email') }}" />
        @error('email')<x-alert type="error">{{ $message }}</x-alert>@enderror
    </div>
    <div>
        <x-label for="password">Password</x-label>
        <x-input id="password" name="password" type="password" required />
        @error('password')<x-alert type="error">{{ $message }}</x-alert>@enderror
    </div>
    <div>
        <x-label for="password_confirmation">Confirm Password</x-label>
        <x-input id="password_confirmation" name="password_confirmation" type="password" required />
    </div>
    <div>
        <x-button type="submit" class="w-full">Register</x-button>
    </div>
</form>
<p class="mt-4 text-center text-sm">Already have an account? <a href="/login" class="text-indigo-600 hover:underline">Login</a></p>
@endsection 