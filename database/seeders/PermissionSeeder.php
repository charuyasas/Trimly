<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'view sidebar',
            'manage users',
            'manage roles',
            'manage sidebar',
            // Sidebar link permissions
            'services',
            'customers',
            'employee',
            'bookings',
            'supplier',
            'item-master',
            'categories',
            'sub-categories',
            'items',
            'sales-invoice',
            'invoice',
            'invoiceList',
            'accounts',
            'postingAccount',
            'userCreation',
            'roles',
            'cash-transfer',
            'userCreate'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
