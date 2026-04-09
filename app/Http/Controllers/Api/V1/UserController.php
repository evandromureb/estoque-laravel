<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\V1;

use App\Application\Inventory\Users\{CreateUserAction, ListUsersForIndexQuery, UpdateUserAction};
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\{StoreUserRequest, UpdateUserRequest};
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\{JsonResponse, Request, Response};
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function __construct(
        private readonly ListUsersForIndexQuery $listUsersForIndexQuery,
        private readonly CreateUserAction $createUserAction,
        private readonly UpdateUserAction $updateUserAction,
    ) {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', User::class);

        $search = $request->filled('search') ? (string) $request->query('search') : null;
        $users  = $this->listUsersForIndexQuery->execute($search, 15);

        return UserResource::collection($users);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->createUserAction->execute($request->validated());

        return (new UserResource($user))
            ->response()
            ->setStatusCode(201);
    }

    public function show(User $user): UserResource
    {
        $this->authorize('view', $user);

        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        $this->updateUserAction->execute($user, $request->validated());

        return new UserResource($user->fresh());
    }

    public function destroy(User $user): Response
    {
        $this->authorize('delete', $user);
        $user->delete();

        return response()->noContent();
    }
}
