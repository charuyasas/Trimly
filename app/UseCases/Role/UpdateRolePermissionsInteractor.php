<?php

namespace App\UseCases\Role;

use App\Models\SidebarLink;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UpdateRolePermissionsInteractor
{
    public function execute(int $roleId, array $sidebarLinkIds): void
    {
        // Get the role
        $role = Role::findOrFail($roleId);

        // Convert SidebarLink IDs to permission names
        $permissionNames = SidebarLink::whereIn('id', $sidebarLinkIds)
            ->pluck('permission_name')
            ->toArray();

        // Get actual Permission models
        $permissions = Permission::whereIn('name', $permissionNames)->get();

        // Sync permissions to the role
        $role->syncPermissions($permissions);
    }
}
