<?php

namespace App\UseCases\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\UseCases\Users\Requests\UpdateUserPasswordRequest;

class UpdateUserPasswordInteractor
{
    public function execute(UpdateUserPasswordRequest $request): void
    {
        $user = User::findOrFail($request->id);

        if (! Hash::check($request->oldPassword, $user->password)) {
            throw ValidationException::withMessages([
                'oldPassword' => ['The old password is incorrect.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($request->newPassword),
        ]);
    }
}

