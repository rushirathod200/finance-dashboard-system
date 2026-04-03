<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    use ApiResponses;

    public function index(): JsonResponse
    {
        $users = User::query()
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return $this->successResponse([
            'items' => UserResource::collection($users->getCollection()),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ], 'Users retrieved successfully.');
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = User::create($request->validated());

        return $this->successResponse(new UserResource($user), 'User created successfully.', Response::HTTP_CREATED);
    }

    public function show(User $user): JsonResponse
    {
        return $this->successResponse(new UserResource($user), 'User retrieved successfully.');
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $validated = $request->validated();

        if (
            $request->user()->is($user)
            && (
                (array_key_exists('is_active', $validated) && ! $validated['is_active'])
                || (array_key_exists('role', $validated) && $validated['role'] !== User::ROLE_ADMIN)
            )
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => [
                    'user' => ['You cannot change your own admin role or deactivate your own account.'],
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->update($validated);

        return $this->successResponse(new UserResource($user->fresh()), 'User updated successfully.');
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        if ($request->user()->is($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => [
                    'user' => ['You cannot deactivate your own account.'],
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->update(['is_active' => false]);
        $user->tokens()->delete();

        return $this->messageResponse('User deactivated successfully.');
    }
}
