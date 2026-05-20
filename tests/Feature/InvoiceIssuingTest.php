<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\SiteSetting;
use App\Models\TaxClassification;
use App\Models\User;
use App\Services\InvoiceService;
use App\Services\PaymentAllocationService;
use Database\Seeders\TaxClassificationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceIssuingTest extends TestCase
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

    private function adminUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('administrator');

        return $user;
    }

    public function test_manual_invoice_with_custom_line_and_discounts(): void
    {
        $customer = Customer::query()->create([
            'display_name' => 'Acme Corp',
            'is_active' => true,
        ]);

        $zeroRated = TaxClassification::query()->where('code', 'ZERO_RATED')->first();

        $invoice = app(InvoiceService::class)->createManual(
            $customer->id,
            [
                [
                    'description' => 'Consulting fee',
                    'quantity' => 1,
                    'unit_price_minor' => 100000,
                    'discount_minor' => 5000,
                    'tax_classification_id' => $zeroRated->id,
                ],
            ],
            invoiceDiscountMinor: 10000,
        );

        $this->assertStringStartsWith('INV-', $invoice->number);
        $this->assertSame('sent', $invoice->status->value);
        $this->assertSame(95000, (int) $invoice->subtotal_minor);
        $this->assertSame(15000, (int) $invoice->discount_minor);
        $this->assertSame(0, (int) $invoice->tax_minor);
        $this->assertSame(80000, (int) $invoice->total_minor);
        $this->assertCount(1, $invoice->items);
        $this->assertSame('Consulting fee', $invoice->items->first()->description);
    }

    public function test_invoice_pdf_download_requires_auth_and_permission(): void
    {
        $customer = Customer::query()->create(['display_name' => 'Buyer', 'is_active' => true]);

        $invoice = app(InvoiceService::class)->createManual(
            $customer->id,
            [
                [
                    'description' => 'Item',
                    'quantity' => 1,
                    'unit_price_minor' => 10000,
                ],
            ],
        );

        $this->get(route('filament.admin.invoices.pdf', $invoice))
            ->assertRedirect();

        $staff = User::factory()->create();
        $staff->assignRole('staff');

        $this->actingAs($staff)
            ->get(route('filament.admin.invoices.pdf', $invoice))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($this->adminUser())
            ->get(route('filament.admin.invoices.pdf', $invoice))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_void_invoice_pdf_returns_not_found(): void
    {
        $customer = Customer::query()->create(['display_name' => 'Buyer', 'is_active' => true]);

        $invoice = app(InvoiceService::class)->createManual(
            $customer->id,
            [['description' => 'Item', 'quantity' => 1, 'unit_price_minor' => 10000]],
        );

        app(InvoiceService::class)->void($invoice);

        $this->actingAs($this->adminUser())
            ->get(route('filament.admin.invoices.pdf', $invoice))
            ->assertNotFound();
    }

    public function test_partial_payment_updates_invoice_status(): void
    {
        $customer = Customer::query()->create(['display_name' => 'Buyer', 'is_active' => true]);

        $invoice = app(InvoiceService::class)->createManual(
            $customer->id,
            [
                [
                    'description' => 'Service',
                    'quantity' => 1,
                    'unit_price_minor' => 20000,
                ],
            ],
        );

        $total = (int) $invoice->total_minor;
        $half = (int) floor($total / 2);

        app(PaymentAllocationService::class)->receivePayment(
            $customer->id,
            $half,
            'cash',
            [['invoice_id' => $invoice->id, 'amount_minor' => $half]],
        );

        $invoice->refresh();
        $this->assertSame($half, (int) $invoice->amount_paid_minor);
        $this->assertSame('partial', $invoice->status->value);
        $this->assertGreaterThan(0, $invoice->balanceDueMinor());
    }

    public function test_void_blocked_when_invoice_has_payments(): void
    {
        $customer = Customer::query()->create(['display_name' => 'Buyer', 'is_active' => true]);

        $invoice = app(InvoiceService::class)->createManual(
            $customer->id,
            [
                [
                    'description' => 'Service',
                    'quantity' => 1,
                    'unit_price_minor' => 20000,
                ],
            ],
        );

        $half = (int) floor($invoice->total_minor / 2);

        app(PaymentAllocationService::class)->receivePayment(
            $customer->id,
            $half,
            'cash',
            [['invoice_id' => $invoice->id, 'amount_minor' => $half]],
        );

        $invoice->refresh();
        $this->assertFalse($invoice->canVoid());

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        app(InvoiceService::class)->void($invoice);
    }
}
