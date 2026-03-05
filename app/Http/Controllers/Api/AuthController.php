<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Handles registration, login, and logout. Register/Login return a Sanctum token for API auth.
 */
class AuthController extends Controller
{
    /**
     * Create a new user (role = user). Validation via RegisterRequest (name, email, password confirmed, strength).
     */
    public function register(RegisterRequest $request)
    {
        $v = $request->validated();
        $user = User::create([
            'name'     => $v['name'],
            'email'    => $v['email'],
            'password' => Hash::make($v['password']),
            'role'     => User::ROLE_USER,
        ]);
        $token = $user->createToken('api_token')->plainTextToken;
        return response()->json([
            'message' => 'User registered successfully',
            'user'    => new UserResource($user),
            'token'   => $token,
        ], 201);
    }

    /**
     * Validate credentials; return user + token. Use UserResource so password is never in response.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|string|email|max:255',
            'password' => 'required|string|max:255',
        ]);

        $user = User::where('email', strtolower(trim($request->input('email'))))->first();
        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            throw ValidationException::withMessages(['email' => ['The provided credentials are incorrect.']]);
        }

        $token = $user->createToken('api_token')->plainTextToken;
        return response()->json([
            'message' => 'Login successful',
            'user'    => new UserResource($user),
            'token'   => $token,
        ], 200);
    }

    /**
     * Revoke all tokens for the authenticated user (logout). Requires auth:sanctum.
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
