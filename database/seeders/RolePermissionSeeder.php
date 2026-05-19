<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * @var list<string>
     */
    private const PERMISSIONS = [
        'orders.view',
        'orders.create',
        'orders.fulfill',
        'products.view',
        'products.manage',
        'catalog.manage',
        'content.manage',
        'settings.manage',
        'reports.view',
        'users.manage',
    ];

    /**
     * @var list<string>
     */
    private const STAFF_PERMISSIONS = [
        'orders.view',
        'orders.create',
        'orders.fulfill',
        'products.view',
    ];

    public function run(): void
    {
        foreach (self::PERMISSIONS as $permission) {
            Permission::query()->firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web'],
            );
        }

        $administrator = Role::query()->firstOrCreate(
            ['name' => 'administrator', 'guard_name' => 'web'],
        );
        $administrator->syncPermissions(self::PERMISSIONS);

        $staff = Role::query()->firstOrCreate(
            ['name' => 'staff', 'guard_name' => 'web'],
        );
        $staff->syncPermissions(self::STAFF_PERMISSIONS);
    }
}
