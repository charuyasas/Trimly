<?php

namespace App\UseCases\Users;

use App\Models\User;
use App\UseCases\Users\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;

class StoreUserInteractor
{
    public function execute(UserRequest $request): User
    {

        $admin = User::create(
            [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verified_at' => now(),
            ]
        );
        return $admin->assignRole($request->role);
    }
}

