<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SidebarLink;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SidebarLinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks temporarily to avoid constraint errors
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate permission-related tables
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('permissions')->truncate();

        // Truncate sidebar links
        SidebarLink::truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $sidebarLinks = [
            [
                'permission_name' => 'services',
                'display_name' => 'Services',
                'url' => '/services',
                'icon_path' => 'assets/img/menu-icon/11.svg',
                'parent_id' => null,
            ],
            [
                'permission_name' => 'customers',
                'display_name' => 'Customers',
                'url' => '/customers',
                'icon_path' => 'assets/img/menu-icon/5.svg',
                'parent_id' => null,
            ],
            [
                'permission_name' => 'employee',
                'display_name' => 'Employees',
                'url' => '/employee',
                'icon_path' => 'assets/img/menu-icon/4.svg',
                'parent_id' => null,
            ],
            [
                'permission_name' => 'supplier',
                'display_name' => 'Suppliers',
                'url' => '/supplier',
                'icon_path' => 'assets/img/menu-icon/3.svg',
                'parent_id' => null,
            ],
            [
                'permission_name' => 'bookings',
                'display_name' => 'Bookings',
                'url' => '/bookings',
                'icon_path' => 'assets/img/menu-icon/15.svg',
                'parent_id' => null,
            ],
            [
                'permission_name' => 'expenses',
                'display_name' => 'Expenses',
                'url' => '/expenses',
                'icon_path' => 'assets/img/menu-icon/12.svg',
                'parent_id' => null,
            ],
            [
                'permission_name' => 'item-master',
                'display_name' => 'Item Master',
                'url' => '#',
                'icon_path' => 'assets/img/menu-icon/16.svg',
                'children' => [
                    [
                        'permission_name' => 'categories',
                        'display_name' => 'Categories',
                        'url' => '/categories',
                    ],
                    [
                        'permission_name' => 'sub-categories',
                        'display_name' => 'Sub Categories',
                        'url' => '/sub-categories',
                    ],
                    [
                        'permission_name' => 'items',
                        'display_name' => 'Items',
                        'url' => '/items',
                    ],
                ],
            ],
            [
                'permission_name' => 'grn',
                'display_name' => 'GRN',
                'url' => '/grn',
                'icon_path' => 'assets/img/menu-icon/14.svg',
                'parent_id' => null,
            ],
            [
                'permission_name' => 'stockIssue',
                'display_name' => 'Stock Issuing',
                'url' => '/stockIssue',
                'icon_path' => 'assets/img/menu-icon/13.svg',
                'parent_id' => null,
            ],
            [
                'permission_name' => 'sales-invoice',
                'display_name' => 'Sales Invoice',
                'url' => '#',
                'icon_path' => 'assets/img/menu-icon/20.svg',
                'children' => [
                    [
                        'permission_name' => 'invoice',
                        'display_name' => 'Add New',
                        'url' => '/invoice',
                    ],
                    [
                        'permission_name' => 'invoiceList',
                        'display_name' => 'List',
                        'url' => '/invoiceList',
                    ],
                ],
            ],
            [
                'permission_name' => 'accounts',
                'display_name' => 'Accounts',
                'url' => '#',
                'icon_path' => 'assets/img/menu-icon/21.svg',
                'children' => [
                    [
                        'permission_name' => 'postingAccount',
                        'display_name' => 'Posting Accounts',
                        'url' => '/postingAccount',
                    ],
                ],
            ],
            [
                'permission_name' => 'userCreation',
                'display_name' => 'User Creation',
                'url' => '#',
                'icon_path' => 'assets/img/menu-icon/16.svg',
                'children' => [
                    [
                        'permission_name' => 'roles',
                        'display_name' => 'Roles',
                        'url' => '/roles',
                    ],
                    [
                        'permission_name' => 'userCreate',
                        'display_name' => 'Users',
                        'url' => '/users',
                    ],
                ],
            ],
            [
                'permission_name' => 'reports',
                'display_name' => 'Reports',
                'url' => '#',
                'icon_path' => 'assets/img/menu-icon/11.svg',
                'children' => [
                    [
                        'permission_name' => 'item-list-report',
                        'display_name' => 'Item List Report',
                        'url' => '/item-list-report',
                    ],
                    [
                        'permission_name' => 'stock-value-report',
                        'display_name' => 'Stock Value Report',
                        'url' => '/stock-value-report',
                    ],
                    [
                        'permission_name' => 'stock-summary-report',
                        'display_name' => 'Stock Summary Report',
                        'url' => '/stock-summary-report',
                    ],
                    [
                        'permission_name' => 'stock-detail-report',
                        'display_name' => 'Stock Detail Report',
                        'url' => '/stock-detail-report',
                    ],
                ],
            ],

        ];

        $this->createSidebarLinks($sidebarLinks);

        $additionalPermissions = [
            'view sidebar',
            'manage users',
            'manage roles',
            'manage sidebar',
        ];

        foreach ($additionalPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $permissions = Permission::all();
        $adminRole->syncPermissions($permissions);
    }

    private function createSidebarLinks(array $links, ?int $parentId = null): void
    {
        foreach ($links as $link) {
            $children = $link['children'] ?? null;

            $sidebarLink = SidebarLink::create([
                'permission_name' => $link['permission_name'],
                'display_name' => $link['display_name'],
                'url' => $link['url'],
                'icon_path' => $link['icon_path'] ?? null,
                'parent_id' => $parentId,
            ]);
            Permission::firstOrCreate(['name' => $link['permission_name']]);

            if ($children && is_array($children)) {
                $this->createSidebarLinks($children, $sidebarLink->id);
            }
        }

    }
}
