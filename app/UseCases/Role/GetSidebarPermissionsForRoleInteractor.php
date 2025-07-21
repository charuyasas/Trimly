<?php

namespace App\UseCases\Role;

use App\Models\SidebarLink;
use Spatie\Permission\Models\Role;

class GetSidebarPermissionsForRoleInteractor
{
    public function execute(int $roleId): array
    {
        $role = Role::findOrFail($roleId);
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        // Fetch all sidebar links in one query
        $allLinks = SidebarLink::all()->groupBy('parent_id');

        // Start from top-level (parent_id = null)
        return $this->buildTree(null, $allLinks, $rolePermissions);
    }

    private function buildTree(?int $parentId, $allLinks, array $rolePermissions): array
    {
        $tree = [];

        foreach ($allLinks[$parentId] ?? [] as $link) {
            $tree[] = [
                'id' => $link->id,
                'display_name' => $link->display_name,
                'permission_status' => in_array($link->permission_name, $rolePermissions),
                'children' => $this->buildTree($link->id, $allLinks, $rolePermissions),
            ];
        }

        return $tree;
    }
}
