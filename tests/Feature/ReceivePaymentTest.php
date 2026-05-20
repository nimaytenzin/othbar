<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Invoice;
use App\Services\PaymentAllocationService;
use Database\Seeders\TaxClassificationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReceivePaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(TaxClassificationSeeder::class);
    }

    public function test_auto_allocate_applies_oldest_invoice_first(): void
    {
        $customer = Customer::query()->create(['display_name' => 'FIFO Customer', 'is_active' => true]);

        $older = Invoice::query()->create([
            'number' => 'INV-OLD',
            'customer_id' => $customer->id,
            'status' => 'sent',
            'issue_date' => '2026-01-01',
            'due_date' => '2026-01-15',
            'subtotal_minor' => 5000,
            'discount_minor' => 0,
            'tax_minor' => 0,
            'total_minor' => 5000,
            'amount_paid_minor' => 0,
            'currency_code' => 'BTN',
        ]);

        $newer = Invoice::query()->create([
            'number' => 'INV-NEW',
            'customer_id' => $customer->id,
            'status' => 'sent',
            'issue_date' => '2026-02-01',
            'due_date' => '2026-02-15',
            'subtotal_minor' => 8000,
            'discount_minor' => 0,
            'tax_minor' => 0,
            'total_minor' => 8000,
            'amount_paid_minor' => 0,
            'currency_code' => 'BTN',
        ]);

        $payment = app(PaymentAllocationService::class)->receivePaymentAutoAllocate(
            $customer->id,
            7000,
            'cash',
        );

        $older->refresh();
        $newer->refresh();

        $this->assertSame('paid', $older->status->value);
        $this->assertSame(5000, (int) $older->amount_paid_minor);
        $this->assertSame('partial', $newer->status->value);
        $this->assertSame(2000, (int) $newer->amount_paid_minor);
        $this->assertCount(2, $payment->allocations);
    }

    public function test_fifo_builder_returns_empty_when_no_outstanding(): void
    {
        $customer = Customer::query()->create(['display_name' => 'Paid up', 'is_active' => true]);

        $allocations = app(PaymentAllocationService::class)->buildFifoAllocations($customer->id, 10000);

        $this->assertSame([], $allocations);
    }
}
