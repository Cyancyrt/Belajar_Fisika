<?php
// filepath: app/Http/Controllers/API/AuthController.php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'total_xp' => 0,
                'level' => 1,
                'streak_days' => 0,  // Ubah dari streak_count ke streak_days
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Registration successful',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'level' => $user->level,
                        'total_xp' => $user->total_xp,
                        'streak_days' => $user->streak_days,  // Ubah dari streak_count
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                ]
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Registration failed: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (Auth::attempt($request->only('email', 'password'))) {
                $user = Auth::user();
                
                // Update streak
                try {
                    $user->updateStreak();
                } catch (Exception $e) {
                    Log::error('Failed to update streak: ' . $e->getMessage());
                }
                
                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'status' => 'success',
                    'message' => 'Login successful',
                    'data' => [
                        'user' => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'level' => $user->level ?? 1,
                            'total_xp' => $user->total_xp ?? 0,
                            'streak_days' => $user->streak_days ?? 0,  // Ubah dari streak_count
                        ],
                        'token' => $token,
                        'token_type' => 'Bearer',
                    ]
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials'
            ], 401);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Login failed: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function profile()
    {
        try {
            $user = Auth::user();
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'level' => $user->level ?? 1,
                        'total_xp' => $user->total_xp ?? 0,
                        'streak_days' => $user->streak_days ?? 0,  // Ubah dari streak_count
                        'last_activity_date' => $user->last_activity_date,
                    ],
                ]
            ]);
        } catch (Exception $e) {
            Log::error('Profile error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get profile: ' . $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Successfully logged out'
            ]);
        } catch (Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Logout failed: ' . $e->getMessage()
            ], 500);
        }
    }
}