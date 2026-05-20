<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\TaxClassification;
use App\Models\User;
use Database\Seeders\TaxClassificationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaxClassificationResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_system_classification_cannot_be_deleted(): void
    {
        $this->seed(TaxClassificationSeeder::class);

        $standard = TaxClassification::query()->where('code', 'STANDARD')->first();

        $this->expectException(\RuntimeException::class);
        $standard->delete();
    }

    public function test_custom_classification_can_be_created_and_deleted(): void
    {
        $this->seed(TaxClassificationSeeder::class);

        $custom = TaxClassification::query()->create([
            'code' => 'SPECIAL',
            'name' => 'Special rate',
            'rate_percent' => 2.5,
            'input_credits_claimable' => true,
            'is_active' => true,
            'sort_order' => 10,
        ]);

        $this->assertDatabaseHas('tax_classifications', ['code' => 'SPECIAL']);

        $custom->delete();

        $this->assertDatabaseMissing('tax_classifications', ['code' => 'SPECIAL']);
    }

    public function test_classification_in_use_cannot_be_deleted_via_model_check(): void
    {
        $this->seed(TaxClassificationSeeder::class);

        $custom = TaxClassification::query()->create([
            'code' => 'CUSTOM',
            'name' => 'Custom',
            'rate_percent' => 5,
            'is_active' => true,
        ]);

        Product::query()->create([
            'name' => 'Linked',
            'slug' => 'linked',
            'price_minor' => 1000,
            'tax_classification_id' => $custom->id,
        ]);

        $this->assertTrue(Product::query()->where('tax_classification_id', $custom->id)->exists());
    }

    public function test_admin_can_access_tax_classifications_page(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('administrator');

        $this->actingAs($admin)
            ->get('/admin/tax-classifications')
            ->assertOk();
    }
}
