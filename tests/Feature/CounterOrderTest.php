<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Filament\Pages\CreateCounterOrder;
use App\Filament\Resources\Invoices\Support\InvoiceLinePreview;
use App\Models\Product;
use App\Models\TaxClassification;
use App\Models\User;
use App\Services\CounterOrderService;
use Database\Seeders\TaxClassificationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Filament\Actions\Testing\TestAction;
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

        $expectedTotal = app(CounterOrderService::class)->calculateTotals(
            [['product_id' => $product->id, 'quantity' => 2]],
        )['total_minor'];
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

    public function test_counter_order_accepts_custom_line_without_products(): void
    {
        $this->seed(\Database\Seeders\TaxClassificationSeeder::class);

        $staff = $this->createStaffUser();
        $this->actingAs($staff);

        $standard = \App\Models\TaxClassification::query()->where('code', 'STANDARD')->first();

        $order = app(CounterOrderService::class)->createAndComplete(
            [
                'first_name' => 'Karma',
                'last_name' => 'Dorji',
                'phone' => '17123456',
                'items' => [],
                'custom_lines' => [
                    [
                        'description' => 'Packaging fee',
                        'quantity' => 1,
                        'unit_price_minor' => 2000,
                        'discount_minor' => 0,
                        'tax_classification_id' => $standard?->id,
                    ],
                ],
            ],
            $staff,
            'cash',
        );

        $this->assertCount(1, $order->items);
        $this->assertNull($order->items->first()->product_id);
        $this->assertSame('Packaging fee', $order->items->first()->name);
        $this->assertGreaterThan(2000, $order->total_minor);
    }

    public function test_livewire_add_and_remove_product_line(): void
    {
        $staff = $this->createStaffUser();
        $product = $this->createProduct(stock: 10, priceMinor: 5000);

        $this->actingAs($staff);

        Livewire::test(CreateCounterOrder::class)
            ->set('selectedProductId', $product->id)
            ->set('addQuantity', 2)
            ->call('addSelectedProduct')
            ->assertSet('data.items', [['product_id' => $product->id, 'quantity' => 2]])
            ->assertCount('data.items', 1)
            ->tap(function ($component) use ($product): void {
                $rows = $component->instance()->getOrderLinesTableRows();
                $this->assertCount(1, $rows);
                $this->assertSame('product', $rows[0]['line_type']);
                $this->assertSame($product->name, $rows[0]['description']);
                $this->assertSame(2, $rows[0]['quantity']);
            })
            ->call('removeLine', 0)
            ->assertSet('data.items', [])
            ->assertCount('data.items', 0);
    }

    /**
     * Filament's test fillForm() does not reliably hydrate mounted action modal fields;
     * set mountedActions.*.data directly (same end state as submitting the modal).
     *
     * @param  array<string, mixed>  $modalData
     */
    private function mountAndSubmitCustomLineAction($component, array $modalData): void
    {
        $component
            ->mountAction('addCustomLine')
            ->set('mountedActions.0.data', $modalData)
            ->callMountedAction()
            ->assertHasNoFormErrors();
    }

    public function test_livewire_add_custom_line_via_action(): void
    {
        $this->seed(TaxClassificationSeeder::class);
        $staff = $this->createStaffUser();
        $standard = TaxClassification::query()->where('code', 'STANDARD')->first();

        $this->actingAs($staff);

        $component = Livewire::test(CreateCounterOrder::class);
        $this->mountAndSubmitCustomLineAction($component, [
            'description' => 'Packaging fee',
            'quantity' => 1,
            'unit_price_minor' => 25,
            'discount_minor' => 0,
            'tax_classification_id' => (string) $standard?->id,
        ]);

        $component
            ->assertCount('data.custom_lines', 1)
            ->tap(function ($component): void {
                $rows = $component->instance()->getOrderLinesTableRows();
                $this->assertCount(1, $rows);
                $this->assertSame('custom', $rows[0]['line_type']);
                $this->assertSame('Packaging fee', $rows[0]['description']);
            });
    }

    public function test_livewire_edit_and_remove_custom_line(): void
    {
        $this->seed(TaxClassificationSeeder::class);
        $staff = $this->createStaffUser();
        $standard = TaxClassification::query()->where('code', 'STANDARD')->first();

        $this->actingAs($staff);

        $component = Livewire::test(CreateCounterOrder::class);
        $this->mountAndSubmitCustomLineAction($component, [
            'description' => 'Packaging',
            'quantity' => 1,
            'unit_price_minor' => 20,
            'discount_minor' => 0,
            'tax_classification_id' => (string) $standard?->id,
        ]);

        $component
            ->mountAction(TestAction::make('editCustomLine')->arguments(['index' => 0]))
            ->set('mountedActions.0.data', [
                'description' => 'Gift wrapping',
                'quantity' => 2,
                'unit_price_minor' => 15,
                'discount_minor' => 0,
                'tax_classification_id' => (string) $standard?->id,
            ])
            ->callMountedAction()
            ->assertHasNoFormErrors()
            ->tap(function ($component): void {
                $line = $component->get('data.custom_lines')[0];
                $this->assertSame('Gift wrapping', $line['description']);
                $this->assertSame(2, $line['quantity']);
                $this->assertSame(1500, $line['unit_price_minor']);
            })
            ->call('removeCustomLine', 0)
            ->assertSet('data.custom_lines', []);
    }

    public function test_livewire_edit_product_quantity_via_action(): void
    {
        $staff = $this->createStaffUser();
        $product = $this->createProduct(stock: 10);

        $this->actingAs($staff);

        Livewire::test(CreateCounterOrder::class)
            ->set('data.items', [['product_id' => $product->id, 'quantity' => 1]])
            ->mountAction(TestAction::make('editProductLine')->arguments(['index' => 0]))
            ->set('mountedActions.0.data.quantity', 3)
            ->callMountedAction()
            ->assertHasNoFormErrors()
            ->assertSet('data.items', [['product_id' => $product->id, 'quantity' => 3]]);
    }

    public function test_livewire_custom_line_mount_action_is_available(): void
    {
        $this->seed(TaxClassificationSeeder::class);
        $staff = $this->createStaffUser();
        $this->actingAs($staff);

        Livewire::test(CreateCounterOrder::class)
            ->call('mountAction', 'addCustomLine')
            ->assertActionMounted('addCustomLine');
    }

    public function test_livewire_custom_lines_sync_to_table(): void
    {
        $this->seed(TaxClassificationSeeder::class);
        $staff = $this->createStaffUser();
        $standard = TaxClassification::query()->where('code', 'STANDARD')->first();

        $this->actingAs($staff);

        $normalized = InvoiceLinePreview::normalizeFromForm([
            'description' => 'Packaging fee',
            'quantity' => 1,
            'unit_price_minor' => 2500,
            'discount_minor' => 0,
            'tax_classification_id' => $standard?->id,
        ]);

        Livewire::test(CreateCounterOrder::class)
            ->set('data.custom_lines', [$normalized])
            ->tap(function ($component): void {
                $rows = $component->instance()->getOrderLinesTableRows();
                $this->assertCount(1, $rows);
                $this->assertSame('custom', $rows[0]['line_type']);
            })
            ->call('removeCustomLine', 0)
            ->assertSet('data.custom_lines', []);
    }

    public function test_livewire_select_product_from_search(): void
    {
        $staff = $this->createStaffUser();
        $product = $this->createProduct();
        $product->update(['sku' => 'SKU-99']);

        $this->actingAs($staff);

        Livewire::test(CreateCounterOrder::class)
            ->set('productSearch', 'SKU-99')
            ->call('selectProduct', $product->id)
            ->assertSet('selectedProductId', $product->id)
            ->call('clearSelectedProduct')
            ->assertSet('selectedProductId', null);
    }

    public function test_livewire_payment_method_switching(): void
    {
        $staff = $this->createStaffUser();
        $this->actingAs($staff);

        Livewire::test(CreateCounterOrder::class)
            ->set('data.payment_method', 'cash')
            ->set('data.payment_method', 'bank-transfer')
            ->set('data.payment_bank', 'mbob')
            ->assertSet('data.payment_method', 'bank-transfer')
            ->assertSet('data.payment_bank', 'mbob');
    }

    public function test_livewire_mixed_lines_pricing_summary(): void
    {
        $this->seed(TaxClassificationSeeder::class);
        $staff = $this->createStaffUser();
        $product = $this->createProduct(priceMinor: 10000);
        $standard = TaxClassification::query()->where('code', 'STANDARD')->first();

        $this->actingAs($staff);

        Livewire::test(CreateCounterOrder::class)
            ->set('data.items', [['product_id' => $product->id, 'quantity' => 1]])
            ->set('data.custom_lines', [
                InvoiceLinePreview::normalizeFromForm([
                    'description' => 'Packaging',
                    'quantity' => 1,
                    'unit_price_minor' => 1000,
                    'discount_minor' => 0,
                    'tax_classification_id' => $standard?->id,
                ]),
            ])
            ->tap(function ($component): void {
                $summary = $component->instance()->resolvePricingSummary();
                $this->assertGreaterThan(0, $summary['total_minor']);
                $this->assertCount(2, $component->instance()->getOrderLinesTableRows());
            });
    }
}
