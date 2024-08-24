<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponse;
    
    /**
     * Register a new user.
     */
    public function register(RegisterUserRequest $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('Personal Access Token')->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token], 201);
    }

    /**
     * Login a user.
     */
    public function login(LoginUserRequest $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('Personal Access Token')->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token]);
    }

    /**
     * Logout the user (revoke the token).
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Get the authenticated user's information.
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Refresh the user's token.
     */
    public function refresh(Request $request)
    {
        $user = Auth::user();
        $token = $user->createToken('Personal Access Token')->plainTextToken;

        return response()->json(['token' => $token]);
    }
}
