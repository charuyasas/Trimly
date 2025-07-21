<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\UseCases\Role\GetSidebarPermissionsForRoleInteractor;
use App\UseCases\Role\ListRoleInteractor;
use App\UseCases\Role\LoadRolesDropdownInteractor;
use App\UseCases\Role\StoreRoleInteractor;
use App\UseCases\Role\ShowRoleInteractor;
use App\UseCases\Role\UpdateRoleInteractor;
use App\UseCases\Role\Requests\RoleRequest;
use App\UseCases\Role\UpdateRolePermissionsInteractor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(ListRoleInteractor $listRoleInteractor): Collection
    {
        return $listRoleInteractor->execute();
    }

    public function store(StoreRoleInteractor $storeRoleInteractor): JsonResponse
    {
        $data = RoleRequest::from(request()->all());
        $role = $storeRoleInteractor->execute($data->toArray());
        return response()->json($role, 201);
    }

    public function show(Role $role, ShowRoleInteractor $showRoleInteractor): Role
    {
        return $showRoleInteractor->execute($role);
    }

    public function update(Role $role, UpdateRoleInteractor $updateRoleInteractor): JsonResponse
    {
        $data = RoleRequest::from(array_merge(request()->all(), ['id' => $role->id]));
        $updatedRole = $updateRoleInteractor->execute($role, $data->toArray());
        return response()->json($updatedRole);
    }

    public function destroy(Role $role, DeleteRoleInteractor $deleteRoleInteractor): JsonResponse
    {
        $deleteRoleInteractor->execute($role);
        return response()->json(null, 204);
    }

    public function loadRolesDropdown(LoadRolesDropdownInteractor $loadRolesDropdownInteractor): JsonResponse
    {
        return response()->json($loadRolesDropdownInteractor->execute());
    }

    public function loadRolePermissions(int $role_id, GetSidebarPermissionsForRoleInteractor $interactor): JsonResponse
    {
        return response()->json($interactor->execute($role_id));
    }

    public function updateRolePermissions(Request $request, int $roleId, UpdateRolePermissionsInteractor $interactor)
    {
        $validated = $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'integer',
        ]);

        $interactor->execute($roleId, $validated['permissions'] ?? []);

        return response()->json(['message' => 'Permissions updated successfully.']);
    }
}
