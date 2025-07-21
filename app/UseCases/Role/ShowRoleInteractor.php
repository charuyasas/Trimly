<?php

namespace App\UseCases\Role;

use Spatie\Permission\Models\Role;

class ShowRoleInteractor
{
    public function execute(Role $role){
        return $role;
    }
}
