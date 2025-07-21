<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SidebarLink;

class SidebarLinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Top-level links
        $services = SidebarLink::create([
            'permission_name' => 'services',
            'display_name' => 'Services',
            'url' => '/',
            'icon_path' => 'assets/img/menu-icon/11.svg',
            'parent_id' => null,
        ]);
        $customers = SidebarLink::create([
            'permission_name' => 'customers',
            'display_name' => 'Customers',
            'url' => '/customers',
            'icon_path' => 'assets/img/menu-icon/5.svg',
            'parent_id' => null,
        ]);
        $employees = SidebarLink::create([
            'permission_name' => 'employee',
            'display_name' => 'Employees',
            'url' => '/employee',
            'icon_path' => 'assets/img/menu-icon/4.svg',
            'parent_id' => null,
        ]);
        $bookings = SidebarLink::create([
            'permission_name' => 'bookings',
            'display_name' => 'Bookings',
            'url' => '/bookings',
            'icon_path' => 'assets/img/menu-icon/15.svg',
            'parent_id' => null,
        ]);
        $suppliers = SidebarLink::create([
            'permission_name' => 'supplier',
            'display_name' => 'Suppliers',
            'url' => '/supplier',
            'icon_path' => 'assets/img/menu-icon/3.svg',
            'parent_id' => null,
        ]);

        // Item Master (parent)
        $itemMaster = SidebarLink::create([
            'permission_name' => 'item-master',
            'display_name' => 'Item Master',
            'url' => '#',
            'icon_path' => 'assets/img/menu-icon/16.svg',
            'parent_id' => null,
        ]);
        SidebarLink::create([
            'permission_name' => 'categories',
            'display_name' => 'Categories',
            'url' => '/categories',
            'icon_path' => null,
            'parent_id' => $itemMaster->id,
        ]);
        SidebarLink::create([
            'permission_name' => 'sub-categories',
            'display_name' => 'Sub Categories',
            'url' => '/sub-categories',
            'icon_path' => null,
            'parent_id' => $itemMaster->id,
        ]);
        SidebarLink::create([
            'permission_name' => 'items',
            'display_name' => 'Items',
            'url' => '/items',
            'icon_path' => null,
            'parent_id' => $itemMaster->id,
        ]);

        // Sales Invoice (parent)
        $salesInvoice = SidebarLink::create([
            'permission_name' => 'sales-invoice',
            'display_name' => 'Sales Invoice',
            'url' => '#',
            'icon_path' => 'assets/img/menu-icon/20.svg',
            'parent_id' => null,
        ]);
        SidebarLink::create([
            'permission_name' => 'invoice',
            'display_name' => 'Add New',
            'url' => '/invoice',
            'icon_path' => null,
            'parent_id' => $salesInvoice->id,
        ]);
        SidebarLink::create([
            'permission_name' => 'invoiceList',
            'display_name' => 'List',
            'url' => '/invoiceList',
            'icon_path' => null,
            'parent_id' => $salesInvoice->id,
        ]);

        // Accounts (parent)
        $accounts = SidebarLink::create([
            'permission_name' => 'accounts',
            'display_name' => 'Accounts',
            'url' => '#',
            'icon_path' => 'assets/img/menu-icon/21.svg',
            'parent_id' => null,
        ]);
        SidebarLink::create([
            'permission_name' => 'postingAccount',
            'display_name' => 'Posting Accounts',
            'url' => '/postingAccount',
            'icon_path' => null,
            'parent_id' => $accounts->id,
        ]);

        // Users (parent)
        $users = SidebarLink::create([
            'permission_name' => 'userCreation',
            'display_name' => 'User Creation',
            'url' => '#',
            'icon_path' => 'assets/img/menu-icon/16.svg',
            'parent_id' => null,
        ]);
        SidebarLink::create([
            'permission_name' => 'roles',
            'display_name' => 'Roles',
            'url' => '/roles',
            'icon_path' => null,
            'parent_id' => $users->id,
        ]);
        SidebarLink::create([
            'permission_name' => 'userCreate',
            'display_name' => 'Users',
            'url' => '/users',
            'icon_path' => null,
            'parent_id' => $users->id,
        ]);
    }
}
