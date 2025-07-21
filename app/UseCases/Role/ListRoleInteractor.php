<?php

namespace App\UseCases\Role;

use Spatie\Permission\Models\Role;

class ListRoleInteractor
{
    public function execute()
    {
        return Role::all();
    }
} 