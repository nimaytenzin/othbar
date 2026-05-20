<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\TaxClassification;
use App\Services\InvoiceService;
use App\Services\PaymentAllocationService;
use App\Services\StockService;
use App\Services\TaxCalculationService;
use Database\Seeders\TaxClassificationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EasyGstPhase1Test extends TestCase
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

    public function test_tax_calculation_standard_rate(): void
    {
        $calc = app(TaxCalculationService::class)->calculateLine(10000, 2);

        $this->assertSame(20000, $calc['line_subtotal_minor']);
        $this->assertSame(1000, $calc['tax_minor']);
        $this->assertSame(21000, $calc['line_total_minor']);
    }

    public function test_invoice_created_from_completed_order(): void
    {
        $product = Product::query()->create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'price_minor' => 10000,
            'currency_code' => 'BTN',
            'stock_quantity' => 5,
            'tax_classification_id' => TaxClassification::query()->where('code', 'STANDARD')->value('id'),
        ]);

        $order = Order::query()->create([
            'number' => 'OTH-TEST-001',
            'total_minor' => 10500,
            'currency_code' => 'BTN',
            'status' => OrderStatus::Completed,
            'payment_status' => PaymentStatus::Paid,
            'metadata' => [
                'subtotal_minor' => 10000,
                'discount_minor' => 0,
                'gst_minor' => 500,
                'effective_tax_rate' => 5,
            ],
        ]);

        OrderItem::query()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'name' => $product->name,
            'quantity' => 1,
            'unit_price_minor' => 10000,
        ]);

        $invoice = app(InvoiceService::class)->createFromOrder($order->fresh(['items.product', 'shippingAddress']));

        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertStringStartsWith('INV-', $invoice->number);
        $this->assertSame($order->id, $invoice->order_id);
        $this->assertCount(1, $invoice->items);
    }

    public function test_payment_allocation_updates_invoice(): void
    {
        $customer = Customer::query()->create(['display_name' => 'Test Customer', 'is_active' => true]);

        $invoice = Invoice::query()->create([
            'number' => 'INV-2026-0001',
            'customer_id' => $customer->id,
            'status' => 'sent',
            'issue_date' => now()->toDateString(),
            'subtotal_minor' => 10000,
            'discount_minor' => 0,
            'tax_minor' => 500,
            'total_minor' => 10500,
            'amount_paid_minor' => 0,
            'currency_code' => 'BTN',
        ]);

        app(PaymentAllocationService::class)->receivePayment(
            $customer->id,
            10500,
            'cash',
            [['invoice_id' => $invoice->id, 'amount_minor' => 10500]],
        );

        $invoice->refresh();
        $this->assertSame(10500, (int) $invoice->amount_paid_minor);
        $this->assertSame('paid', $invoice->status->value);
    }

    public function test_inventory_movement_on_stock_decrement(): void
    {
        $product = Product::query()->create([
            'name' => 'Tracked Product',
            'slug' => 'tracked-product',
            'price_minor' => 5000,
            'currency_code' => 'BTN',
            'stock_quantity' => 10,
            'track_inventory' => true,
        ]);

        $order = Order::query()->create([
            'number' => 'OTH-TEST-002',
            'total_minor' => 10000,
            'status' => OrderStatus::Completed,
            'payment_status' => PaymentStatus::Paid,
        ]);

        OrderItem::query()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'name' => $product->name,
            'quantity' => 2,
            'unit_price_minor' => 5000,
        ]);

        app(StockService::class)->decrementForOrder($order);

        $product->refresh();
        $this->assertSame(8, $product->stock_quantity);
        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $product->id,
            'type' => 'sale',
            'quantity_delta' => -2,
        ]);
    }
}
