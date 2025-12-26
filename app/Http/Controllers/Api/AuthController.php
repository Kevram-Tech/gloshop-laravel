<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Inscription réussie',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ], 201);
    }

    /**
     * Login user.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants sont incorrects.'],
            ]);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ]);
    }

    /**
     * Logout user.
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if ($user) {
            // Delete all tokens for this user (simpler and more reliable)
            $user->tokens()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie',
        ]);
    }

    /**
     * Get authenticated user.
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $request->user(),
        ]);
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profil mis à jour avec succès',
            'data' => $user->fresh(),
        ]);
    }

    /**
     * Change user password.
     */
    public function changePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Le mot de passe actuel est incorrect.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mot de passe modifié avec succès',
        ]);
    }

    /**
     * Authenticate with Google.
     * Accepts user data from Flutter app after Google Sign-In
     */
    public function googleAuth(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'provider_id' => 'required|string',
            'avatar' => 'nullable|string|url',
        ]);

        try {
            // Find user by email or provider_id
            $user = User::where('email', $validated['email'])
                ->orWhere(function ($query) use ($validated) {
                    $query->where('provider', 'google')
                          ->where('provider_id', $validated['provider_id']);
                })
                ->first();

            if (!$user) {
                // Create new user
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'provider' => 'google',
                    'provider_id' => $validated['provider_id'],
                    'avatar' => $validated['avatar'] ?? null,
                    'password' => Hash::make(uniqid()), // Random password for social auth users
                ]);
            } else {
                // Update existing user if needed
                if (!$user->provider) {
                    $user->update([
                        'provider' => 'google',
                        'provider_id' => $validated['provider_id'],
                        'avatar' => $validated['avatar'] ?? $user->avatar,
                    ]);
                } else {
                    // Update name and avatar if changed
                    $user->update([
                        'name' => $validated['name'],
                        'avatar' => $validated['avatar'] ?? $user->avatar,
                    ]);
                }
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Connexion Google réussie',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'authentification Google: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Authenticate with Facebook.
     * Accepts user data from Flutter app after Facebook Login
     */
    public function facebookAuth(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'nullable|email',
            'name' => 'required|string|max:255',
            'provider_id' => 'required|string',
            'avatar' => 'nullable|string|url',
        ]);

        try {
            // Generate email if not provided
            $email = $validated['email'] ?? $validated['provider_id'] . '@facebook.com';
            
            // Find user by email or provider_id
            $user = User::where('email', $email)
                ->orWhere(function ($query) use ($validated) {
                    $query->where('provider', 'facebook')
                          ->where('provider_id', $validated['provider_id']);
                })
                ->first();

            if (!$user) {
                // Create new user
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $email,
                    'provider' => 'facebook',
                    'provider_id' => $validated['provider_id'],
                    'avatar' => $validated['avatar'] ?? null,
                    'password' => Hash::make(uniqid()), // Random password for social auth users
                ]);
            } else {
                // Update existing user if needed
                if (!$user->provider) {
                    $user->update([
                        'provider' => 'facebook',
                        'provider_id' => $validated['provider_id'],
                        'avatar' => $validated['avatar'] ?? $user->avatar,
                    ]);
                } else {
                    // Update name and avatar if changed
                    $user->update([
                        'name' => $validated['name'],
                        'avatar' => $validated['avatar'] ?? $user->avatar,
                    ]);
                }
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Connexion Facebook réussie',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'authentification Facebook: ' . $e->getMessage(),
            ], 400);
        }
    }
}

