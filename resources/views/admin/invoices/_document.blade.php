@php
    /** @var \App\Models\Invoice $invoice */
    /** @var \App\Models\SiteSetting $site */
    $currency = $invoice->currency_code ?: 'BTN';
    $balanceDue = $invoice->balanceDueMinor();
    $showGst = (bool) $site->is_gst_registered;
    $invoiceTitle = $showGst ? 'TAX INVOICE' : 'INVOICE';
    $lineColspan = $showGst ? 6 : 5;
    $logoSrc = $logoUrl ?? null;
    if (! $logoSrc && ! empty($logoPath) && is_file($logoPath)) {
        $logoSrc = $logoPath;
    }
@endphp

<style>
    .inv-doc { font-family: DejaVu Sans, ui-sans-serif, system-ui, sans-serif; color: #111; font-size: 12px; line-height: 1.45; }
    .inv-doc * { box-sizing: border-box; }
    .inv-doc__title { margin: 0; font-size: 20px; font-weight: 700; letter-spacing: 0.02em; }
    .inv-doc__number { margin: 4px 0 0; font-size: 13px; color: #444; }
    .inv-doc__muted { color: #555; font-size: 11px; margin: 2px 0 0; }
    .inv-doc__header { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
    .inv-doc__header td { vertical-align: top; padding: 0; }
    .inv-doc__logo { max-height: 56px; max-width: 140px; }
    .inv-doc__parties { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
    .inv-doc__parties td { width: 50%; vertical-align: top; padding: 12px 14px; border: 1px solid #ddd; background: #fafafa; }
    .inv-doc__party-label { margin: 0 0 6px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #666; }
    .inv-doc__party-name { margin: 0 0 4px; font-size: 13px; font-weight: 700; }
    .inv-doc__lines { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    .inv-doc__lines th,
    .inv-doc__lines td { border-bottom: 1px solid #ddd; padding: 8px 6px; vertical-align: top; }
    .inv-doc__lines th { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #666; background: #f5f5f5; text-align: left; }
    .inv-doc__lines .num { text-align: right; white-space: nowrap; }
    .inv-doc__sku { font-size: 10px; color: #666; margin-top: 2px; }
    .inv-doc__totals-wrap { width: 100%; margin-bottom: 14px; }
    .inv-doc__totals { width: 280px; margin-left: auto; border-collapse: collapse; }
    .inv-doc__totals th,
    .inv-doc__totals td { padding: 5px 0; border-bottom: 1px solid #eee; }
    .inv-doc__totals th { font-weight: 500; text-align: left; color: #555; }
    .inv-doc__totals td { text-align: right; font-variant-numeric: tabular-nums; }
    .inv-doc__totals tr.inv-doc__grand th,
    .inv-doc__totals tr.inv-doc__grand td { font-weight: 700; font-size: 13px; border-bottom: none; padding-top: 8px; }
    .inv-doc__totals tr.inv-doc__balance th,
    .inv-doc__totals tr.inv-doc__balance td { font-weight: 700; border-bottom: none; }
    .inv-doc__discount { color: #b91c1c; }
    .inv-doc__balance-due { color: #b91c1c; }
    .inv-doc__balance-clear { color: #15803d; }
    .inv-doc__footer { margin-top: 12px; font-size: 11px; color: #555; }
</style>

<div class="inv-doc">
    <table class="inv-doc__header">
        <tr>
            <td>
                <h1 class="inv-doc__title">{{ $invoiceTitle }}</h1>
                <p class="inv-doc__number">{{ $invoice->number }}</p>
                <p class="inv-doc__muted">
                    Issued {{ $invoice->issue_date->format('d M Y') }}
                    @if($invoice->due_date)
                        · Due {{ $invoice->due_date->format('d M Y') }}
                    @endif
                    @if($invoice->order)
                        · Order {{ $invoice->order->number }}
                    @endif
                </p>
            </td>
            @if($logoSrc)
                <td style="text-align: right; width: 150px;">
                    <img src="{{ $logoSrc }}" alt="" class="inv-doc__logo">
                </td>
            @endif
        </tr>
    </table>

    <table class="inv-doc__parties">
        <tr>
            <td>
                <p class="inv-doc__party-label">From</p>
                @if(filled($site->business_name))
                    <p class="inv-doc__party-name">{{ $site->business_name }}</p>
                @endif
                @if(filled($site->businessAddressBlock()))
                    <p class="inv-doc__muted" style="white-space: pre-line;">{{ $site->businessAddressBlock() }}</p>
                @endif
                @if(filled($site->businessContactLine()))
                    <p class="inv-doc__muted">{{ $site->businessContactLine() }}</p>
                @endif
                @if($showGst && filled($site->gst_tpn))
                    <p class="inv-doc__muted">GST TPN: {{ $site->gst_tpn }}</p>
                @endif
                @if(filled($site->drc_registration_number))
                    <p class="inv-doc__muted">DRC reg.: {{ $site->drc_registration_number }}</p>
                @endif
                @if(filled($site->business_licence_number))
                    <p class="inv-doc__muted">Licence: {{ $site->business_licence_number }}</p>
                @endif
            </td>
            <td>
                <p class="inv-doc__party-label">Bill to</p>
                <p class="inv-doc__party-name">{{ $invoice->customer->display_name }}</p>
                @if(filled($invoice->customer->gst_tpn))
                    <p class="inv-doc__muted">TPN: {{ $invoice->customer->gst_tpn }}</p>
                @endif
                @if(filled($invoice->customer->email))
                    <p class="inv-doc__muted">{{ $invoice->customer->email }}</p>
                @endif
                @if(filled($invoice->customer->phone))
                    <p class="inv-doc__muted">{{ $invoice->customer->phone }}</p>
                @endif
                @if(filled($invoice->customer->billingAddressLines()))
                    <p class="inv-doc__muted" style="white-space: pre-line;">{{ $invoice->customer->billingAddressLines() }}</p>
                @endif
            </td>
        </tr>
    </table>

    <table class="inv-doc__lines">
        <thead>
            <tr>
                <th>Description</th>
                <th class="num">Qty</th>
                <th class="num">Unit price</th>
                <th class="num">Discount</th>
                @if($showGst)
                    <th class="num">GST</th>
                @endif
                <th class="num">Line total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoice->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->description }}</strong>
                        @if($item->sku)
                            <div class="inv-doc__sku">{{ $item->sku }}</div>
                        @endif
                    </td>
                    <td class="num">{{ $item->quantity }}</td>
                    <td class="num">{{ $currency }} {{ number_format($item->unit_price_minor / 100, 2) }}</td>
                    <td class="num">
                        @if($item->discount_minor > 0)
                            − {{ $currency }} {{ number_format($item->discount_minor / 100, 2) }}
                        @else
                            —
                        @endif
                    </td>
                    @if($showGst)
                        <td class="num">
                            {{ number_format((float) $item->tax_rate_percent, 1) }}%
                            <div class="inv-doc__sku">{{ $currency }} {{ number_format($item->tax_minor / 100, 2) }}</div>
                        </td>
                    @endif
                    <td class="num"><strong>{{ $currency }} {{ number_format($item->line_total_minor / 100, 2) }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $lineColspan }}" style="text-align: center; color: #666; padding: 16px;">No line items</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="inv-doc__totals-wrap">
        <table class="inv-doc__totals">
            <tbody>
                <tr>
                    <th scope="row">Subtotal</th>
                    <td>{{ $currency }} {{ number_format($invoice->subtotal_minor / 100, 2) }}</td>
                </tr>
                @if($invoice->discount_minor > 0)
                    <tr>
                        <th scope="row">Discount</th>
                        <td class="inv-doc__discount">− {{ $currency }} {{ number_format($invoice->discount_minor / 100, 2) }}</td>
                    </tr>
                @endif
                @if($showGst)
                    <tr>
                        <th scope="row">GST</th>
                        <td>{{ $currency }} {{ number_format($invoice->tax_minor / 100, 2) }}</td>
                    </tr>
                @endif
                <tr class="inv-doc__grand">
                    <th scope="row">Invoice total</th>
                    <td>{{ $currency }} {{ number_format($invoice->total_minor / 100, 2) }}</td>
                </tr>
                <tr>
                    <th scope="row">Paid</th>
                    <td>{{ $currency }} {{ number_format($invoice->amount_paid_minor / 100, 2) }}</td>
                </tr>
                <tr class="inv-doc__balance">
                    <th scope="row">Balance due</th>
                    <td class="{{ $balanceDue > 0 ? 'inv-doc__balance-due' : 'inv-doc__balance-clear' }}">
                        {{ $currency }} {{ number_format($balanceDue / 100, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    @if($bankAccount ?? null)
        <p class="inv-doc__footer"><strong>Pay to:</strong> {{ $bankAccount->bank_name }} — {{ $bankAccount->account_name }} — {{ $bankAccount->account_number }}</p>
    @endif

    @if(filled($invoice->terms_snapshot))
        <p class="inv-doc__footer">{{ $invoice->terms_snapshot }}</p>
    @endif

    @if(filled($site->invoice_footer_text))
        <p class="inv-doc__footer">{{ $site->invoice_footer_text }}</p>
    @endif

    @if(filled($invoice->notes))
        <p class="inv-doc__footer"><strong>Notes:</strong> {{ $invoice->notes }}</p>
    @endif
</div>
