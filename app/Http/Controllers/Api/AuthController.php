<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    use ApiResponses;

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::query()
            ->active()
            ->where('email', (string) $request->string('email'))
            ->first();

        if (! $user || ! Hash::check((string) $request->string('password'), $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken($request->input('device_name', 'api-token'))->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
        ], 'Login successful.');
    }

    public function logout(): JsonResponse
    {
        request()->user()->currentAccessToken()?->delete();

        return $this->messageResponse('Logout successful.');
    }

    public function me(): JsonResponse
    {
        return $this->successResponse(new UserResource(request()->user()), 'User profile retrieved successfully.');
    }
}
