<?php

namespace Dragonite\Accessibility\Services\Auth;

use App\Models\User;
use Dragonite\Accessibility\Requests\RegisterRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Lorisleiva\Actions\Concerns\AsAction;

class RegisterService
{
    use AsAction;

    public function handle(RegisterRequest $registerRequest): JsonResponse
    {
        $user = User::create([
            'name' => $registerRequest->name,
            'email' => $registerRequest->email,
            'password' => Hash::make($registerRequest->password),
        ]);

        $token = $user->createToken('authToken')->accessToken;
        dd($token);

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }
}
