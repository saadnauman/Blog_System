<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('pages.login');
    }

    public function showRegister()
    {
        return view('pages.register');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        //i could have added gates here by having the token assigned token be for admin or user
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Redirect based on user role
            if (Auth::user()->role === 'admin') {
                return redirect()->intended('/admin/dashboard');
            } else {
                return redirect()->intended('/user/dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            //'role' => 'user', // Default role for new registrations
        ]);

        Auth::login($user);

        return redirect('/user/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
} 
//auth sanctum tokens could have been used here for api authentication if this was an api first application
//but since this is a web application, session based authentication is used
//if this was an api first application, i would have used sanctum or passport for api
//authentication and would have returned tokens instead of redirecting to a dashboard
//this is a simple authentication controller for a web application

