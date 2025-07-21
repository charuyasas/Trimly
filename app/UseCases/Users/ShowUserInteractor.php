<?php

namespace App\UseCases\Users;

use App\Models\User;

class ShowUserInteractor
{
    public function execute(User $user): User
    {
//        return $user;
        return $user->load('roles');
    }
}
