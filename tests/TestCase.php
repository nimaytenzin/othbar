<?php

namespace Tests;

use App\Models\SiteSetting;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\PermissionRegistrar;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        SiteSetting::query()->updateOrCreate(
            ['id' => 1],
            array_merge(SiteSetting::defaults(), ['is_gst_registered' => false]),
        );
        SiteSetting::clearCache();
    }
}
