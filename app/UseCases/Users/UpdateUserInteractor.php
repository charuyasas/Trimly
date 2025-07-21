<?php

namespace App\UseCases\Users;

use App\Models\User;
use App\UseCases\Users\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;

class UpdateUserInteractor
{
    public function execute(User $user, UserRequest $request): User
    {
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        return $user;
    }
}
