<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\SiteSetting;
use App\Services\FinancialReportService;
use App\Services\InvoiceService;
use App\Services\PaymentAllocationService;
use Database\Seeders\TaxClassificationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(TaxClassificationSeeder::class);
        SiteSetting::query()->updateOrCreate(['id' => 1], SiteSetting::defaults());
        SiteSetting::clearCache();
    }

    public function test_accounts_receivable_and_gst_summary(): void
    {
        $customer = Customer::query()->create(['display_name' => 'Buyer', 'is_active' => true]);

        $invoice = app(InvoiceService::class)->createManual(
            $customer->id,
            [
                [
                    'description' => 'Goods',
                    'quantity' => 1,
                    'unit_price_minor' => 10000,
                ],
            ],
            issueDate: today()->toDateString(),
        );

        $invoice->refresh();
        $totalDue = (int) $invoice->total_minor;

        $service = app(FinancialReportService::class);

        $this->assertSame($totalDue, $service->totalOutstandingMinor());
        $this->assertCount(1, $service->accountsReceivableSummary());

        $gst = $service->gstSummary([
            'date_from' => today()->startOfMonth()->toDateString(),
            'date_to' => today()->toDateString(),
        ]);

        $this->assertSame(1, $gst['invoice_count']);
        $this->assertGreaterThan(0, $gst['tax_minor']);

        app(PaymentAllocationService::class)->receivePayment(
            $customer->id,
            (int) $invoice->fresh()->total_minor,
            'cash',
            [['invoice_id' => $invoice->id, 'amount_minor' => (int) $invoice->total_minor]],
        );

        $this->assertSame(0, $service->totalOutstandingMinor());
    }

    public function test_fiscal_year_range_uses_settings_month(): void
    {
        SiteSetting::query()->where('id', 1)->update(['fiscal_year_start_month' => 7]);
        SiteSetting::clearCache();

        $range = app(FinancialReportService::class)->fiscalYearDateRange();

        $this->assertArrayHasKey('date_from', $range);
        $this->assertArrayHasKey('date_to', $range);
        $this->assertSame(today()->toDateString(), $range['date_to']);
    }
}
