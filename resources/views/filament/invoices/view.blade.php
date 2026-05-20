@php
    /** @var \App\Models\Invoice $invoice */
    /** @var \App\Models\SiteSetting $site */
    use App\Enums\InvoiceStatus;

    $currency = $invoice->currency_code ?: 'BTN';
    $balanceDue = $invoice->balanceDueMinor();
    $showGst = (bool) $site->is_gst_registered;
    $invoiceTitle = $showGst ? 'Tax invoice' : 'Invoice';

    $statusColor = match ($invoice->status) {
        InvoiceStatus::Paid => 'success',
        InvoiceStatus::Partial => 'warning',
        InvoiceStatus::Void => 'danger',
        InvoiceStatus::Sent => 'info',
        default => 'gray',
    };
@endphp

<div class="oth-invoice-view">
    <div class="oth-invoice-view__header">
        <div class="oth-invoice-view__header-main">
            <p class="oth-invoice-view__eyebrow">{{ $invoiceTitle }}</p>
            <h2 class="oth-invoice-view__number">{{ $invoice->number }}</h2>
            <div class="oth-invoice-view__meta">
                <x-filament::badge :color="$statusColor">
                    {{ str($invoice->status->value)->headline() }}
                </x-filament::badge>
                <span>Issued {{ $invoice->issue_date->format('d M Y') }}</span>
                @if($invoice->due_date)
                    <span>· Due {{ $invoice->due_date->format('d M Y') }}</span>
                @endif
                @if($invoice->order)
                    <span>· Order {{ $invoice->order->number }}</span>
                @endif
            </div>
        </div>
        @if(filled($site->business_logo_path))
            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($site->business_logo_path) }}" alt="" class="oth-invoice-view__logo">
        @endif
    </div>

    <div class="oth-invoice-view__parties">
        <div class="oth-invoice-view__party">
            <h3>From</h3>
            @if(filled($site->business_name))
                <p class="oth-invoice-view__party-name">{{ $site->business_name }}</p>
            @endif
            @if(filled($site->businessAddressBlock()))
                <p class="oth-invoice-view__party-detail" style="white-space:pre-line;">{{ $site->businessAddressBlock() }}</p>
            @endif
            @if(filled($site->businessContactLine()))
                <p class="oth-invoice-view__party-detail">{{ $site->businessContactLine() }}</p>
            @endif
            @if($showGst && filled($site->gst_tpn))
                <p class="oth-invoice-view__party-detail">GST TPN: {{ $site->gst_tpn }}</p>
            @endif
            @if(filled($site->drc_registration_number))
                <p class="oth-invoice-view__party-detail">DRC reg.: {{ $site->drc_registration_number }}</p>
            @endif
            @if(filled($site->business_licence_number))
                <p class="oth-invoice-view__party-detail">Licence: {{ $site->business_licence_number }}</p>
            @endif
        </div>
        <div class="oth-invoice-view__party">
            <h3>Bill to</h3>
            <p class="oth-invoice-view__party-name">{{ $invoice->customer->display_name }}</p>
            @if(filled($invoice->customer->gst_tpn))
                <p class="oth-invoice-view__party-detail">TPN: {{ $invoice->customer->gst_tpn }}</p>
            @endif
            @if(filled($invoice->customer->email))
                <p class="oth-invoice-view__party-detail">{{ $invoice->customer->email }}</p>
            @endif
            @if(filled($invoice->customer->phone))
                <p class="oth-invoice-view__party-detail">{{ $invoice->customer->phone }}</p>
            @endif
            @if(filled($invoice->customer->billingAddressLines()))
                <p class="oth-invoice-view__party-detail" style="white-space:pre-line;">{{ $invoice->customer->billingAddressLines() }}</p>
            @endif
        </div>
    </div>

    <div class="oth-table-wrap">
        <table class="oth-table oth-invoice-view__lines">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Unit price</th>
                    <th class="text-right">Discount</th>
                    @if($showGst)
                        <th class="text-right">GST</th>
                    @endif
                    <th class="text-right">Line total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoice->items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->description }}</strong>
                            @if($item->sku)
                                <div class="oth-invoice-view__sku">{{ $item->sku }}</div>
                            @endif
                        </td>
                        <td class="text-right">{{ $item->quantity }}</td>
                        <td class="text-right">{{ $currency }} {{ number_format($item->unit_price_minor / 100, 2) }}</td>
                        <td class="text-right">
                            @if($item->discount_minor > 0)
                                − {{ $currency }} {{ number_format($item->discount_minor / 100, 2) }}
                            @else
                                —
                            @endif
                        </td>
                        @if($showGst)
                            <td class="text-right">
                                {{ number_format((float) $item->tax_rate_percent, 1) }}%
                                <div class="oth-invoice-view__sku">{{ $currency }} {{ number_format($item->tax_minor / 100, 2) }}</div>
                            </td>
                        @endif
                        <td class="text-right"><strong>{{ $currency }} {{ number_format($item->line_total_minor / 100, 2) }}</strong></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $showGst ? 6 : 5 }}" class="oth-invoice-view__empty">No line items.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="oth-invoice-view__footer">
        <table class="oth-invoice-view__totals">
            <tbody>
                <tr>
                    <th scope="row">Subtotal</th>
                    <td>{{ $currency }} {{ number_format($invoice->subtotal_minor / 100, 2) }}</td>
                </tr>
                @if($invoice->discount_minor > 0)
                    <tr>
                        <th scope="row">Discount</th>
                        <td class="oth-invoice-view__discount">− {{ $currency }} {{ number_format($invoice->discount_minor / 100, 2) }}</td>
                    </tr>
                @endif
                @if($showGst)
                    <tr>
                        <th scope="row">GST</th>
                        <td>{{ $currency }} {{ number_format($invoice->tax_minor / 100, 2) }}</td>
                    </tr>
                @endif
                <tr class="oth-invoice-view__totals-row--emphasis">
                    <th scope="row">Invoice total</th>
                    <td>{{ $currency }} {{ number_format($invoice->total_minor / 100, 2) }}</td>
                </tr>
                <tr>
                    <th scope="row">Paid</th>
                    <td>{{ $currency }} {{ number_format($invoice->amount_paid_minor / 100, 2) }}</td>
                </tr>
                <tr class="oth-invoice-view__totals-row--balance">
                    <th scope="row">Balance due</th>
                    <td class="{{ $balanceDue > 0 ? 'oth-invoice-view__balance-due' : 'oth-invoice-view__balance-clear' }}">
                        {{ $currency }} {{ number_format($balanceDue / 100, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    @if($bankAccount ?? null)
        <p class="oth-invoice-view__note">
            <strong>Pay to:</strong> {{ $bankAccount->bank_name }} — {{ $bankAccount->account_name }} — {{ $bankAccount->account_number }}
        </p>
    @endif

    @if(filled($invoice->terms_snapshot))
        <p class="oth-invoice-view__note">{{ $invoice->terms_snapshot }}</p>
    @endif

    @if(filled($invoice->notes))
        <p class="oth-invoice-view__note"><strong>Notes:</strong> {{ $invoice->notes }}</p>
    @endif
</div>

<style>
    .oth-invoice-view {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        padding: 0.25rem 0;
    }
    .oth-invoice-view__header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1.5rem;
        flex-wrap: wrap;
    }
    .oth-invoice-view__eyebrow {
        margin: 0;
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: rgb(107 114 128);
    }
    .oth-invoice-view__number {
        margin: 0.15rem 0 0.5rem;
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1.2;
    }
    .oth-invoice-view__meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem 0.75rem;
        font-size: 0.875rem;
        color: rgb(107 114 128);
    }
    .oth-invoice-view__logo {
        max-height: 3.5rem;
        max-width: 9rem;
        object-fit: contain;
    }
    .oth-invoice-view__parties {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.25rem;
        padding: 1rem 1.25rem;
        border: 1px solid rgba(0,0,0,.08);
        border-radius: 0.5rem;
        background: rgba(0,0,0,.02);
    }
    .dark .oth-invoice-view__parties {
        border-color: rgba(255,255,255,.1);
        background: rgba(255,255,255,.03);
    }
    .oth-invoice-view__party h3 {
        margin: 0 0 0.5rem;
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: rgb(107 114 128);
    }
    .oth-invoice-view__party-name {
        margin: 0 0 0.25rem;
        font-weight: 600;
    }
    .oth-invoice-view__party-detail {
        margin: 0.15rem 0 0;
        font-size: 0.875rem;
        color: rgb(107 114 128);
    }
    .oth-table-wrap { overflow-x: auto; }
    .oth-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }
    .oth-table th,
    .oth-table td {
        padding: 0.65rem 0.75rem;
        border-bottom: 1px solid rgba(0,0,0,.08);
        vertical-align: top;
    }
    .dark .oth-table th,
    .dark .oth-table td {
        border-bottom-color: rgba(255,255,255,.1);
    }
    .oth-table th {
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: rgb(107 114 128);
        background: rgba(0,0,0,.02);
    }
    .dark .oth-table th {
        background: rgba(255,255,255,.04);
    }
    .oth-table .text-right { text-align: right; }
    .oth-invoice-view__sku {
        font-size: 0.75rem;
        color: rgb(107 114 128);
        margin-top: 0.15rem;
    }
    .oth-invoice-view__empty {
        text-align: center;
        color: rgb(107 114 128);
        padding: 1.5rem !important;
    }
    .oth-invoice-view__footer {
        display: flex;
        justify-content: flex-end;
    }
    .oth-invoice-view__totals {
        width: 100%;
        max-width: 320px;
        border-collapse: collapse;
        font-size: 0.875rem;
    }
    .oth-invoice-view__totals th,
    .oth-invoice-view__totals td {
        padding: 0.4rem 0;
        border-bottom: 1px solid rgba(0,0,0,.06);
    }
    .dark .oth-invoice-view__totals th,
    .dark .oth-invoice-view__totals td {
        border-bottom-color: rgba(255,255,255,.08);
    }
    .oth-invoice-view__totals th {
        font-weight: 500;
        text-align: left;
        color: rgb(107 114 128);
    }
    .oth-invoice-view__totals td {
        text-align: right;
        font-variant-numeric: tabular-nums;
    }
    .oth-invoice-view__totals-row--emphasis th,
    .oth-invoice-view__totals-row--emphasis td {
        font-weight: 700;
        font-size: 1rem;
        border-bottom: none;
        padding-top: 0.5rem;
    }
    .oth-invoice-view__totals-row--balance th,
    .oth-invoice-view__totals-row--balance td {
        font-weight: 700;
        border-bottom: none;
        padding-top: 0.35rem;
    }
    .oth-invoice-view__discount { color: rgb(185 28 28); }
    .dark .oth-invoice-view__discount { color: rgb(248 113 113); }
    .oth-invoice-view__balance-due { color: rgb(185 28 28); }
    .dark .oth-invoice-view__balance-due { color: rgb(248 113 113); }
    .oth-invoice-view__balance-clear { color: rgb(21 128 61); }
    .dark .oth-invoice-view__balance-clear { color: rgb(74 222 128); }
    .oth-invoice-view__note {
        margin: 0;
        font-size: 0.875rem;
        color: rgb(107 114 128);
    }
</style>
