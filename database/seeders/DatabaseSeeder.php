<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);
        $this->call(AdminUserSeeder::class);
        $this->call(StaffUserSeeder::class);
        $this->call(OthbarProductSeeder::class);
        $this->call(SiteSettingSeeder::class);
    }
}
