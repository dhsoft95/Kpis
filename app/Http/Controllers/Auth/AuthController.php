<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('LaravelPassportAuth')->accessToken;

        return response()->json(['token' => $token], 201);
    }

    /**
     * Login user and create token
     */

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            if (Auth::attempt($request->only('email', 'password'))) {
                $user = Auth::user();
                Log::info('User authenticated successfully', ['user_id' => $user->id]);

                $token = $user->createToken('AuthToken')->plainTextToken;
//                $token = $user->createToken('AuthToken')->accessToken;

                return response()->json([
                    'user' => $user,
                    'token' => $token
                ], 200);
            }

            Log::warning('Failed login attempt', ['email' => $request->email]);
            return response()->json(['error' => 'The provided credentials are incorrect.'], 401);
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'An error occurred during login.'], 500);
        }
    }


    /**
     * Logout user (Revoke the token)
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['message' => 'Successfully logged out']);
    }
}
