<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            'clients',
            'suppliers',
            'contacts',
            'products',
            'quotations',
            'orders',
            'supplier-orders',
            'supplier-invoices',
            'users',
            'permission-groups',
        ];

        $actions = ['create', 'read', 'update', 'delete'];

        foreach ($menus as $menu) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(
                    [
                        'name' => "{$menu}.{$action}",
                        'guard_name' => 'web',
                    ],
                    [
                        'name' => "{$menu}.{$action}",
                        'guard_name' => 'web',
                    ]
                );
            }
        }
    }
}
