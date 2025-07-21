<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\UseCases\Role\ListRoleInteractor;
use App\UseCases\Role\StoreRoleInteractor;
use App\UseCases\Role\ShowRoleInteractor;
use App\UseCases\Role\UpdateRoleInteractor;
use App\UseCases\Role\DeleteRoleInteractor;
use App\UseCases\Role\Requests\RoleRequest;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(ListRoleInteractor $listRoleInteractor)
    {
        return $listRoleInteractor->execute();
    }

    public function store(StoreRoleInteractor $storeRoleInteractor)
    {
        $data = RoleRequest::from(request()->all());
        $role = $storeRoleInteractor->execute($data->toArray());
        return response()->json($role, 201);
    }

    public function show(Role $role, ShowRoleInteractor $showRoleInteractor)
    {
        return $showRoleInteractor->execute($role);
    }

    public function update(Role $role, UpdateRoleInteractor $updateRoleInteractor)
    {
        $data = RoleRequest::from(array_merge(request()->all(), ['id' => $role->id]));
        $updatedRole = $updateRoleInteractor->execute($role, $data->toArray());
        return response()->json($updatedRole);
    }

    public function destroy(Role $role, DeleteRoleInteractor $deleteRoleInteractor)
    {
        $deleteRoleInteractor->execute($role);
        return response()->json(null, 204);
    }
} 