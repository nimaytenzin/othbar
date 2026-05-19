<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Filament\Pages\CreateCounterOrder;
use App\Models\Product;
use App\Models\User;
use App\Services\CounterOrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

class CounterOrderTest extends TestCase
{
    use RefreshDatabase;

    private function createStaffUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('staff');

        return $user;
    }

    private function createProduct(int $stock = 10, int $priceMinor = 5000): Product
    {
        return Product::query()->create([
            'name' => 'Test Product',
            'slug' => 'test-product-'.uniqid(),
            'stock_quantity' => $stock,
            'allow_backorder' => false,
            'is_visible' => true,
            'price_minor' => $priceMinor,
            'currency_code' => 'BTN',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function orderPayload(Product $product): array
    {
        return [
            'first_name' => 'Karma',
            'last_name' => 'Dorji',
            'phone' => '17123456',
            'email' => 'karma@example.com',
            'notes' => 'Counter test',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2],
            ],
        ];
    }

    public function test_staff_can_create_pending_counter_order(): void
    {
        $staff = $this->createStaffUser();
        $product = $this->createProduct();

        $this->actingAs($staff);

        $order = app(CounterOrderService::class)->createPending(
            $this->orderPayload($product),
            $staff,
        );

        $this->assertTrue($order->isCounter());
        $this->assertSame(PaymentStatus::Pending, $order->payment_status);
        $this->assertSame(OrderStatus::New, $order->status);
        $this->assertSame($staff->id, $order->created_by_user_id);
        $this->assertCount(1, $order->items);

        $expectedTotal = app(CounterOrderService::class)->calculateTotals([
            ['product_id' => $product->id, 'quantity' => 2],
        ])['total_minor'];
        $this->assertSame($expectedTotal, $order->total_minor);
    }

    public function test_staff_can_complete_counter_sale_and_decrement_stock(): void
    {
        $staff = $this->createStaffUser();
        $product = $this->createProduct(stock: 8);

        $this->actingAs($staff);

        $order = app(CounterOrderService::class)->createAndComplete(
            $this->orderPayload($product),
            $staff,
            'cash',
            null,
        );

        $this->assertSame(PaymentStatus::Paid, $order->payment_status);
        $this->assertSame(OrderStatus::Completed, $order->status);
        $this->assertNull($order->payment_reference);
        $this->assertSame('cash', $order->metadata['payment_method']);

        $product->refresh();
        $this->assertSame(6, $product->stock_quantity);
    }

    public function test_pending_counter_order_can_be_paid_and_fulfilled_later(): void
    {
        $staff = $this->createStaffUser();
        $product = $this->createProduct(stock: 5);

        $this->actingAs($staff);

        $service = app(CounterOrderService::class);
        $order = $service->createPending($this->orderPayload($product), $staff);

        $service->recordPaymentAndFulfill($order, 'bank-transfer', 'JRN-2026-002', 'mbob');
        $order->refresh();
        $product->refresh();

        $this->assertSame(PaymentStatus::Paid, $order->payment_status);
        $this->assertSame(OrderStatus::Completed, $order->status);
        $this->assertSame('mbob', $order->metadata['payment_bank']);
        $this->assertSame('JRN-2026-002', $order->payment_reference);
        $this->assertSame(3, $product->stock_quantity);
    }

    public function test_bank_transfer_requires_bank_and_journal_reference(): void
    {
        $staff = $this->createStaffUser();
        $product = $this->createProduct();

        $this->actingAs($staff);

        $this->expectException(ValidationException::class);

        app(CounterOrderService::class)->createAndComplete(
            $this->orderPayload($product),
            $staff,
            'bank-transfer',
            null,
            null,
        );
    }

    public function test_counter_order_product_search_finds_by_name_and_sku(): void
    {
        $staff = $this->createStaffUser();
        $product = $this->createProduct();
        $product->update([
            'name' => 'Bhutan Red Rice',
            'sku' => 'RICE-001',
        ]);

        $this->actingAs($staff);

        $component = Livewire::test(CreateCounterOrder::class)
            ->set('productSearch', 'RICE-001');

        $results = $component->instance()->getProductSearchResults();

        $this->assertCount(1, $results);
        $this->assertSame($product->id, $results[0]['id']);

        $component->set('productSearch', 'Bhutan');
        $results = $component->instance()->getProductSearchResults();

        $this->assertCount(1, $results);
        $this->assertSame('Bhutan Red Rice', $results[0]['name']);
    }

    public function test_counter_order_rejects_insufficient_stock(): void
    {
        $staff = $this->createStaffUser();
        $product = $this->createProduct(stock: 1);

        $this->actingAs($staff);

        $payload = $this->orderPayload($product);
        $payload['items'][0]['quantity'] = 3;

        $this->expectException(ValidationException::class);

        app(CounterOrderService::class)->createPending($payload, $staff);
    }
}
