<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\TaxClassification;
use App\Services\CartSessionService;
use App\Services\TaxCalculationService;
use Database\Seeders\TaxClassificationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LineLevelTaxPricingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(TaxClassificationSeeder::class);

        $standard = TaxClassification::query()->where('code', 'STANDARD')->first();
        SiteSetting::query()->updateOrCreate(['id' => 1], [
            ...SiteSetting::defaults(),
            'is_gst_registered' => true,
            'default_tax_classification_id' => $standard?->id,
        ]);
        SiteSetting::clearCache();
    }

    public function test_mixed_standard_and_exempt_cart_totals(): void
    {
        $standard = TaxClassification::query()->where('code', 'STANDARD')->first();
        $exempt = TaxClassification::query()->where('code', 'EXEMPT')->first();

        $taxable = Product::query()->create([
            'name' => 'Honey',
            'slug' => 'honey',
            'price_minor' => 10000,
            'currency_code' => 'BTN',
            'tax_classification_id' => $standard->id,
        ]);

        $exemptProduct = Product::query()->create([
            'name' => 'Exempt item',
            'slug' => 'exempt-item',
            'price_minor' => 5000,
            'currency_code' => 'BTN',
            'tax_classification_id' => $exempt->id,
        ]);

        $totals = app(TaxCalculationService::class)->calculateCartTotals([
            ['product_id' => $taxable->id, 'quantity' => 1],
            ['product_id' => $exemptProduct->id, 'quantity' => 1],
        ]);

        $this->assertSame(15000, $totals['subtotal_minor']);
        $this->assertSame(500, $totals['gst_minor']);
        $this->assertSame(15500, $totals['total_minor']);
    }

    public function test_cart_session_uses_line_level_tax(): void
    {
        $standard = TaxClassification::query()->where('code', 'STANDARD')->first();

        $product = Product::query()->create([
            'name' => 'Rice',
            'slug' => 'rice',
            'price_minor' => 20000,
            'currency_code' => 'BTN',
            'tax_classification_id' => $standard->id,
        ]);

        $cart = app(CartSessionService::class);
        $cart->addProduct($product, 1);

        $this->assertSame(1000, $cart->gstMinor());
        $this->assertSame(21000, $cart->totalMinor());
        $this->assertSame(5.0, $cart->effectiveTaxRate());
    }
}
