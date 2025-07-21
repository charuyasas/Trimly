<?php

namespace App\UseCases\Role;

use Spatie\Permission\Models\Role;

class UpdateRoleInteractor
{
    public function execute(Role $role, array $data)
    {
        $role->update($data);
        return $role;
    }
} 