<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $permissions = Permission::all();
        $adminRole->syncPermissions($permissions);

        $cashierRole = Role::firstOrCreate(['name' => 'cashier']);
        $cashierPermissions = ['expenses', 'invoice'];

        foreach ($cashierPermissions as $permName) {
            $permission = Permission::firstOrCreate(['name' => $permName]);
            $cashierRole->givePermissionTo($permission);
        }

    }
}
