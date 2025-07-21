<?php

namespace App\UseCases\Role;

use Spatie\Permission\Models\Role;

class LoadRolesDropdownInteractor
{
    public function execute()
    {
        return Role::orderBy('name', 'asc')
            ->get()
            ->map(fn($role) => [
                'label' => $role->name,
                'value' => $role->name
            ]);
    }
}
