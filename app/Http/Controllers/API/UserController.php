<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\UseCases\Users\ListUserInteractor;
use App\UseCases\Users\Requests\UpdateUserPasswordRequest;
use App\UseCases\Users\Requests\UserRequest;
use App\UseCases\Users\ShowUserInteractor;
use App\UseCases\Users\StoreUserInteractor;
use App\UseCases\Users\UpdateUserInteractor;
use App\UseCases\Users\UpdateUserPasswordInteractor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class UserController
{
    public function index(ListUserInteractor $listUserInteractor): Collection
    {
        return $listUserInteractor->execute();
    }

    public function store(StoreUserInteractor $storeUserInteractor): JsonResponse
    {
        $newUser = $storeUserInteractor->execute(UserRequest::validateAndCreate(request()));
        return response()->json($newUser , 201);
    }

    public function show(User $user, ShowUserInteractor $showUserInteractor): User
    {
        return $showUserInteractor->execute($user);
    }

    public function update(User $user, UpdateUserInteractor $updateUserInteractor): JsonResponse
    {
        $updateUser = $updateUserInteractor->execute($user, UserRequest::validateAndCreate(request()));
        return response()->json($updateUser);
    }

    public function updateUserPassword(UpdateUserPasswordRequest $updateUserPasswordRequest, UpdateUserPasswordInteractor $updateUserPasswordInteractor): JsonResponse
    {
        $updateUserPasswordInteractor->execute($updateUserPasswordRequest);

        return response()->json(['message' => 'Password updated successfully.']);
    }



}
