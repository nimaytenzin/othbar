<?php

namespace Tests\Feature;

use App\Filament\Pages\CreateCounterOrder;
use App\Filament\Pages\ManagePaymentSettings;
use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function createStaffUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('staff');

        return $user;
    }

    private function createAdminUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('administrator');

        return $user;
    }

    public function test_staff_can_access_counter_order_page(): void
    {
        $staff = $this->createStaffUser();
        $this->actingAs($staff);

        $this->assertTrue($staff->can('orders.create'));
        $this->assertTrue(CreateCounterOrder::canAccess());
    }

    public function test_staff_cannot_access_payment_settings_or_user_management(): void
    {
        $staff = $this->createStaffUser();
        $this->actingAs($staff);

        $this->assertFalse(ManagePaymentSettings::canAccess());
        $this->assertFalse(UserResource::canAccess());
    }

    public function test_administrator_can_access_user_management(): void
    {
        $admin = $this->createAdminUser();
        $this->actingAs($admin);

        $this->assertTrue(UserResource::canAccess());
        $this->assertTrue(ManagePaymentSettings::canAccess());
    }

    public function test_staff_can_view_products_but_not_manage_them(): void
    {
        $staff = $this->createStaffUser();
        $this->actingAs($staff);

        $this->assertTrue(ProductResource::canAccess());
        $product = Product::query()->create([
            'name' => 'Policy Product',
            'slug' => 'policy-product',
            'stock_quantity' => 1,
            'is_visible' => true,
            'price_minor' => 1000,
            'currency_code' => 'BTN',
        ]);
        $this->assertFalse($staff->can('create', Product::class));
        $this->assertFalse($staff->can('update', $product));
    }
}
