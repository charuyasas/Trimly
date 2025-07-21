<?php

namespace App\UseCases\Users;

use App\Models\User;

class ListUserInteractor
{
    public function execute(){
        return User::with('roles')->get();
    }
}
