<?php

namespace App\UseCases\Role;

use Spatie\Permission\Models\Role;

class StoreRoleInteractor
{
    public function execute(array $data)
    {
        return Role::create($data);
    }
} 