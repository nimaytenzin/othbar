@php
    /** @var \App\Models\CustomerPayment $payment */
    /** @var \App\Models\SiteSetting $site */
    use App\Services\PaymentReceiptService;

    $currency = $payment->currency_code ?: 'BTN';
    $allocated = $payment->allocatedMinor();
    $unallocated = $payment->unallocatedMinor();
    $logoSrc = $logoUrl ?? null;
    if (! $logoSrc && ! empty($logoPath) && is_file($logoPath)) {
        $logoSrc = $logoPath;
    }
@endphp

<style>
    .rcp-doc { font-family: DejaVu Sans, ui-sans-serif, system-ui, sans-serif; color: #111; font-size: 12px; line-height: 1.45; }
    .rcp-doc * { box-sizing: border-box; }
    .rcp-doc__title { margin: 0; font-size: 20px; font-weight: 700; letter-spacing: 0.04em; }
    .rcp-doc__number { margin: 4px 0 0; font-size: 13px; color: #444; }
    .rcp-doc__muted { color: #555; font-size: 11px; margin: 2px 0 0; }
    .rcp-doc__header { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
    .rcp-doc__header td { vertical-align: top; padding: 0; }
    .rcp-doc__logo { max-height: 56px; max-width: 140px; }
    .rcp-doc__amount-box {
        margin: 0 0 18px;
        padding: 14px 16px;
        border: 2px solid #111;
        text-align: center;
        background: #fafafa;
    }
    .rcp-doc__amount-label { margin: 0; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #666; }
    .rcp-doc__amount-value { margin: 6px 0 0; font-size: 22px; font-weight: 700; }
    .rcp-doc__meta { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
    .rcp-doc__meta th,
    .rcp-doc__meta td { padding: 6px 8px; border-bottom: 1px solid #eee; text-align: left; vertical-align: top; }
    .rcp-doc__meta th { width: 32%; font-weight: 600; color: #555; font-size: 11px; }
    .rcp-doc__lines { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    .rcp-doc__lines th,
    .rcp-doc__lines td { border-bottom: 1px solid #ddd; padding: 8px 6px; vertical-align: top; }
    .rcp-doc__lines th { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #666; background: #f5f5f5; text-align: left; }
    .rcp-doc__lines .num { text-align: right; white-space: nowrap; }
    .rcp-doc__totals-wrap { width: 100%; margin-bottom: 14px; }
    .rcp-doc__totals { width: 300px; margin-left: auto; border-collapse: collapse; }
    .rcp-doc__totals th,
    .rcp-doc__totals td { padding: 5px 0; border-bottom: 1px solid #eee; }
    .rcp-doc__totals th { font-weight: 500; text-align: left; color: #555; }
    .rcp-doc__totals td { text-align: right; font-variant-numeric: tabular-nums; }
    .rcp-doc__totals tr.rcp-doc__grand th,
    .rcp-doc__totals tr.rcp-doc__grand td { font-weight: 700; border-bottom: none; padding-top: 6px; }
    .rcp-doc__unallocated { color: #b45309; }
    .rcp-doc__footer { margin-top: 14px; font-size: 11px; color: #555; text-align: center; }
</style>

<div class="rcp-doc">
    <table class="rcp-doc__header">
        <tr>
            <td>
                <h1 class="rcp-doc__title">PAYMENT RECEIPT</h1>
                <p class="rcp-doc__number">{{ $payment->number }}</p>
                @if(filled($site->business_name))
                    <p class="rcp-doc__muted"><strong>{{ $site->business_name }}</strong></p>
                @endif
                @if(filled($site->businessContactLine()))
                    <p class="rcp-doc__muted">{{ $site->businessContactLine() }}</p>
                @endif
                @if((bool) $site->is_gst_registered && filled($site->gst_tpn))
                    <p class="rcp-doc__muted">GST TPN: {{ $site->gst_tpn }}</p>
                @endif
                @if(filled($site->drc_registration_number))
                    <p class="rcp-doc__muted">DRC reg.: {{ $site->drc_registration_number }}</p>
                @endif
            </td>
            @if($logoSrc)
                <td style="text-align: right; width: 150px;">
                    <img src="{{ $logoSrc }}" alt="" class="rcp-doc__logo">
                </td>
            @endif
        </tr>
    </table>

    <div class="rcp-doc__amount-box">
        <p class="rcp-doc__amount-label">Amount received</p>
        <p class="rcp-doc__amount-value">{{ $currency }} {{ number_format($payment->amount_minor / 100, 2) }}</p>
    </div>

    <table class="rcp-doc__meta">
        <tbody>
            <tr>
                <th scope="row">Payment date</th>
                <td>{{ $payment->payment_date->format('d M Y') }}</td>
            </tr>
            <tr>
                <th scope="row">Received from</th>
                <td><strong>{{ $payment->customer->display_name }}</strong></td>
            </tr>
            @if(filled($payment->customer->gst_tpn))
                <tr>
                    <th scope="row">Customer TPN</th>
                    <td>{{ $payment->customer->gst_tpn }}</td>
                </tr>
            @endif
            <tr>
                <th scope="row">Payment method</th>
                <td>{{ PaymentReceiptService::paymentMethodLabel($payment->payment_method) }}</td>
            </tr>
            @if($payment->bankAccount)
                <tr>
                    <th scope="row">Bank account</th>
                    <td>{{ $payment->bankAccount->displayLabel() }}</td>
                </tr>
            @endif
            @if(filled($payment->reference))
                <tr>
                    <th scope="row">Reference</th>
                    <td>{{ $payment->reference }}</td>
                </tr>
            @endif
            @if($payment->createdBy)
                <tr>
                    <th scope="row">Recorded by</th>
                    <td>{{ $payment->createdBy->name }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    @if($payment->allocations->isNotEmpty())
        <table class="rcp-doc__lines">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Issue date</th>
                    <th class="num">Applied</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payment->allocations as $allocation)
                    <tr>
                        <td><strong>{{ $allocation->invoice->number }}</strong></td>
                        <td>{{ $allocation->invoice->issue_date?->format('d M Y') ?? '—' }}</td>
                        <td class="num">{{ $currency }} {{ number_format($allocation->amount_minor / 100, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="rcp-doc__totals-wrap">
            <table class="rcp-doc__totals">
                <tbody>
                    <tr class="rcp-doc__grand">
                        <th scope="row">Total applied to invoices</th>
                        <td>{{ $currency }} {{ number_format($allocated / 100, 2) }}</td>
                    </tr>
                    @if($unallocated > 0)
                        <tr>
                            <th scope="row" class="rcp-doc__unallocated">Unallocated</th>
                            <td class="rcp-doc__unallocated">{{ $currency }} {{ number_format($unallocated / 100, 2) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <th scope="row">Payment received</th>
                        <td>{{ $currency }} {{ number_format($payment->amount_minor / 100, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    @else
        <p class="rcp-doc__muted" style="margin-bottom: 12px;">This payment has not been applied to any invoice yet.</p>
    @endif

    <p class="rcp-doc__footer">
        @if(filled($site->invoice_footer_text))
            {{ $site->invoice_footer_text }}
        @else
            Thank you for your payment.
        @endif
    </p>
</div>
