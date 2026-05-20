<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\SiteSetting;
use Illuminate\Support\Collection;

class FinancialReportService
{
    /**
     * @return array{date_from: string, date_to: string}
     */
    public function fiscalYearDateRange(): array
    {
        $settings = SiteSetting::current();
        $startMonth = max(1, min(12, (int) ($settings->fiscal_year_start_month ?? 1)));
        $today = today();

        $fyStart = $today->month >= $startMonth
            ? $today->copy()->month($startMonth)->startOfMonth()
            : $today->copy()->subYear()->month($startMonth)->startOfMonth();

        return [
            'date_from' => $fyStart->toDateString(),
            'date_to' => $today->toDateString(),
        ];
    }

    /**
     * @return Collection<int, array{customer: Customer, outstanding_minor: int, invoice_count: int}>
     */
    public function accountsReceivableSummary(): Collection
    {
        return Invoice::query()
            ->with('customer')
            ->where('status', '!=', InvoiceStatus::Void)
            ->whereColumn('amount_paid_minor', '<', 'total_minor')
            ->get()
            ->groupBy('customer_id')
            ->map(function (Collection $invoices): array {
                $customer = $invoices->first()->customer;

                return [
                    'customer' => $customer,
                    'outstanding_minor' => (int) $invoices->sum(
                        fn (Invoice $invoice): int => $invoice->balanceDueMinor(),
                    ),
                    'invoice_count' => $invoices->count(),
                ];
            })
            ->sortByDesc('outstanding_minor')
            ->values();
    }

    public function totalOutstandingMinor(): int
    {
        return (int) $this->accountsReceivableSummary()->sum('outstanding_minor');
    }

    /**
     * @return Collection<int, Invoice>
     */
    public function overdueInvoices(): Collection
    {
        return Invoice::query()
            ->with('customer')
            ->where('status', '!=', InvoiceStatus::Void)
            ->whereColumn('amount_paid_minor', '<', 'total_minor')
            ->whereDate('due_date', '<', now()->toDateString())
            ->orderBy('due_date')
            ->get();
    }

    /**
     * @param  array{date_from: string, date_to: string}  $range
     * @return array{
     *     invoice_count: int,
     *     subtotal_minor: int,
     *     tax_minor: int,
     *     total_minor: int,
     *     by_rate: list<array{rate_percent: float, tax_minor: int}>
     * }
     */
    public function gstSummary(array $range): array
    {
        $invoices = Invoice::query()
            ->where('status', '!=', InvoiceStatus::Void)
            ->whereDate('issue_date', '>=', $range['date_from'])
            ->whereDate('issue_date', '<=', $range['date_to'])
            ->get(['id', 'subtotal_minor', 'tax_minor', 'total_minor']);

        $invoiceIds = $invoices->pluck('id');

        $byRate = InvoiceItem::query()
            ->whereIn('invoice_id', $invoiceIds)
            ->where('tax_minor', '>', 0)
            ->selectRaw('tax_rate_percent, SUM(tax_minor) as tax_minor')
            ->groupBy('tax_rate_percent')
            ->orderBy('tax_rate_percent')
            ->get()
            ->map(fn ($row): array => [
                'rate_percent' => (float) $row->tax_rate_percent,
                'tax_minor' => (int) $row->tax_minor,
            ])
            ->values()
            ->all();

        return [
            'invoice_count' => $invoices->count(),
            'subtotal_minor' => (int) $invoices->sum('subtotal_minor'),
            'tax_minor' => (int) $invoices->sum('tax_minor'),
            'total_minor' => (int) $invoices->sum('total_minor'),
            'by_rate' => $byRate,
        ];
    }

    /**
     * @param  array{date_from: string, date_to: string}  $range
     * @return array{payment_count: int, total_minor: int, payments: Collection<int, CustomerPayment>}
     */
    public function paymentsReceived(array $range): array
    {
        $payments = CustomerPayment::query()
            ->with(['customer', 'allocations.invoice'])
            ->whereDate('payment_date', '>=', $range['date_from'])
            ->whereDate('payment_date', '<=', $range['date_to'])
            ->orderByDesc('payment_date')
            ->get();

        return [
            'payment_count' => $payments->count(),
            'total_minor' => (int) $payments->sum('amount_minor'),
            'payments' => $payments,
        ];
    }
}
