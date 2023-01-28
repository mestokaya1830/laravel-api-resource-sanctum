<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string|min:3|max:100',
            'email' => 'required|string|unique:users,email|min:10|max:100',
            'password' => 'required|string|confirmed|min:4|max:100',
            'password_confirmation' => 'required| min:4|max:100',
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password']),
        ]);

        $token = $user->createToken($fields['email'].' Register')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string|min:10|max:100',
            'password' => 'required|string|min:4|max:100',

        ]);

        $user = User::where('email', $fields['email'])->first();
        if (! $user || ! Hash::check($fields['password'], $user->password)) {
            return response()->json(['message' => 'Bad creds'], 401);
        }

        $token = $user->createToken($fields['email'].' Login')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return response()->json(['message' => 'User logedout']);
    }
}
