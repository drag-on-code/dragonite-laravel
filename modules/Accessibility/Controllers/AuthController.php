<?php

namespace Dragonite\Accessibility\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Dragonite\Accessibility\Requests\RegisterRequest;
use Dragonite\Accessibility\Services\Auth\RegisterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Token;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(RegisterRequest $registerRequest): JsonResponse
    {
        dd($registerRequest);

        return RegisterService::run($registerRequest);
    }

    /**
     * Login user and return a token.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('authToken')->accessToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    /**
     * Logout the user (revoke token).
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Get authenticated user.
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
