@php
    use App\Filament\Resources\Customers\CustomerResource;
    use App\Filament\Resources\Invoices\InvoiceResource;
    use Illuminate\Support\Carbon;

    /** @var string $dateFrom */
    /** @var string $dateTo */
    /** @var \Illuminate\Support\Collection $arSummary */
    /** @var int $totalOutstandingMinor */
    /** @var \Illuminate\Support\Collection $overdueInvoices */
    /** @var array $gstSummary */
    /** @var array $paymentsReceived */
    /** @var bool $isGstRegistered */
@endphp

<div style="margin-top: 1.5rem; display: flex; flex-direction: column; gap: 1.5rem;">
    <div class="oth-card">
        <h3 class="oth-card__title">Accounts receivable</h3>
        <p class="oth-card__subtitle">Open invoice balances as of today</p>

        <dl class="oth-stat-grid" style="margin-top: 1rem;">
            <div class="oth-stat">
                <dt class="oth-stat__label">Total outstanding</dt>
                <dd class="oth-stat__value">Nu. {{ number_format($totalOutstandingMinor / 100, 2) }}</dd>
            </div>
            <div class="oth-stat">
                <dt class="oth-stat__label">Customers with balance</dt>
                <dd class="oth-stat__value">{{ number_format($arSummary->count()) }}</dd>
            </div>
        </dl>

        @if($arSummary->isEmpty())
            <p class="oth-order-meta" style="margin-top: 1rem;">No outstanding invoices.</p>
        @else
            <div class="oth-table-wrap" style="margin-top: 1rem;">
                <table class="oth-table">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th class="text-right">Open invoices</th>
                            <th class="text-right">Outstanding</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($arSummary as $row)
                            <tr>
                                <td>
                                    <a href="{{ CustomerResource::getUrl('edit', ['record' => $row['customer']]) }}"
                                       style="font-weight: 600; text-decoration: underline;">
                                        {{ $row['customer']->display_name }}
                                    </a>
                                </td>
                                <td class="text-right">{{ $row['invoice_count'] }}</td>
                                <td class="text-right"><strong>Nu. {{ number_format($row['outstanding_minor'] / 100, 2) }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="oth-card">
        <h3 class="oth-card__title">Overdue invoices</h3>
        <p class="oth-card__subtitle">Past due date with a remaining balance</p>

        @if($overdueInvoices->isEmpty())
            <p class="oth-order-meta" style="margin-top: 1rem;">No overdue invoices.</p>
        @else
            <div class="oth-table-wrap" style="margin-top: 1rem;">
                <table class="oth-table">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Customer</th>
                            <th>Due date</th>
                            <th class="text-right">Balance due</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($overdueInvoices as $invoice)
                            <tr>
                                <td>
                                    <a href="{{ InvoiceResource::getUrl('view', ['record' => $invoice]) }}"
                                       style="font-weight: 600; text-decoration: underline;">
                                        {{ $invoice->number }}
                                    </a>
                                </td>
                                <td>{{ $invoice->customer->display_name }}</td>
                                <td>{{ $invoice->due_date?->format('d M Y') ?? '—' }}</td>
                                <td class="text-right"><strong>Nu. {{ number_format($invoice->balanceDueMinor() / 100, 2) }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="oth-card">
        <h3 class="oth-card__title">Period summary</h3>
        <p class="oth-card__subtitle">
            {{ Carbon::parse($dateFrom)->format('M j, Y') }} to {{ Carbon::parse($dateTo)->format('M j, Y') }}
        </p>

        @if($isGstRegistered)
            <h4 style="margin: 1.25rem 0 0.5rem; font-size: 0.875rem; font-weight: 600;">GST on invoices issued</h4>
            <dl class="oth-stat-grid">
                <div class="oth-stat">
                    <dt class="oth-stat__label">Invoices</dt>
                    <dd class="oth-stat__value">{{ number_format($gstSummary['invoice_count']) }}</dd>
                </div>
                <div class="oth-stat">
                    <dt class="oth-stat__label">Taxable subtotal</dt>
                    <dd class="oth-stat__value">Nu. {{ number_format($gstSummary['subtotal_minor'] / 100, 2) }}</dd>
                </div>
                <div class="oth-stat">
                    <dt class="oth-stat__label">GST collected</dt>
                    <dd class="oth-stat__value">Nu. {{ number_format($gstSummary['tax_minor'] / 100, 2) }}</dd>
                </div>
                <div class="oth-stat">
                    <dt class="oth-stat__label">Invoice total</dt>
                    <dd class="oth-stat__value">Nu. {{ number_format($gstSummary['total_minor'] / 100, 2) }}</dd>
                </div>
            </dl>

            @if(! empty($gstSummary['by_rate']))
                <div class="oth-table-wrap" style="margin-top: 1rem;">
                    <table class="oth-table">
                        <thead>
                            <tr>
                                <th>GST rate</th>
                                <th class="text-right">GST amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($gstSummary['by_rate'] as $row)
                                <tr>
                                    <td>{{ number_format($row['rate_percent'], 1) }}%</td>
                                    <td class="text-right">Nu. {{ number_format($row['tax_minor'] / 100, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @else
            <p class="oth-order-meta" style="margin-top: 1rem;">GST is disabled in tax settings. Invoice totals exclude GST.</p>
        @endif

        <h4 style="margin: 1.25rem 0 0.5rem; font-size: 0.875rem; font-weight: 600;">Payments received</h4>
        <dl class="oth-stat-grid">
            <div class="oth-stat">
                <dt class="oth-stat__label">Payments</dt>
                <dd class="oth-stat__value">{{ number_format($paymentsReceived['payment_count']) }}</dd>
            </div>
            <div class="oth-stat">
                <dt class="oth-stat__label">Total received</dt>
                <dd class="oth-stat__value">Nu. {{ number_format($paymentsReceived['total_minor'] / 100, 2) }}</dd>
            </div>
        </dl>

        @if($paymentsReceived['payments']->isEmpty())
            <p class="oth-order-meta" style="margin-top: 1rem;">No payments recorded in this period.</p>
        @else
            <div class="oth-table-wrap" style="margin-top: 1rem;">
                <table class="oth-table">
                    <thead>
                        <tr>
                            <th>Receipt</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($paymentsReceived['payments'] as $payment)
                            <tr>
                                <td>{{ $payment->number }}</td>
                                <td>{{ $payment->payment_date->format('d M Y') }}</td>
                                <td>{{ $payment->customer->display_name }}</td>
                                <td class="text-right"><strong>Nu. {{ number_format($payment->amount_minor / 100, 2) }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
