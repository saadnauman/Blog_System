<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //  REGISTER
    public function register(Request $request)
    {
    
        //Validations 
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required|in:admin,user',
        ]);
        // create user
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Assign role
        $user->assignRole($validated['role']);

        // Create API token
        // This is used for authenticating API requests
        // The token is used to authenticate the user in subsequent API requests
        // It is stored in the database and can be used to authenticate the user
        
        $token = $user->createToken('API Token')->plainTextToken;

        // Return user data and token
  
       return response()->json([
        'user' => $user,
        'role' => $user->getRoleNames(), // returns a collection
        'token' => $token,
    ], 201);
        // 201 Created status code indicates that the request has succeeded and a new resource has been created as a result.
    }

    // ✅ LOGIN
    public function login(Request $request)
    {
        // Validate the request data
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);
        // Check if the user exists and the password is correct
        $user = User::where('email', $request->email)->first();
        //if user enters wrong password or email
        // we throw a validation exception
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
        // Create API token
        // This is used for authenticating API requests
        $token = $user->createToken('API Token')->plainTextToken;
        // Return user data and token
        return response()->json([
            'user'  => $user,
            'token' => $token,
            'role'  => $user->getRoleNames(),
        ]);
    }

    // ✅ LOGOUT
    public function logout(Request $request)
    { 
        // Revoke the user's token
        // This will invalidate the token and the user will have to log in again
        $request->user()->currentAccessToken()->delete();
       
        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
