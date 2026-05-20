@php
    /** @var \App\Models\Order $order */
    /** @var \App\Models\SiteSetting $site */
    $meta = is_array($order->metadata) ? $order->metadata : (json_decode((string) $order->metadata, true) ?: []);
    $customerEmail = $meta['email'] ?? null;
    $couponCode = $meta['coupon_code'] ?? null;
    $isPickup = ($order->fulfillment_method ?? 'delivery') === 'pickup';
    $isCounter = $order->isCounter();
    $pricing = $order->pricingSummary();
    $currency = $order->currency_code ?: 'BTN';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt — {{ $order->number }}</title>
    <style>
        * { box-sizing: border-box; }

        body {
            font-family: ui-monospace, 'SFMono-Regular', Menlo, Consolas, monospace;
            color: #111;
            width: 100%;
            max-width: 320px;
            margin: 0 auto;
            padding: 1rem 0.75rem;
            font-size: 12px;
            line-height: 1.45;
            background: #fff;
        }

        .receipt {
            width: 100%;
        }

        .receipt__header,
        .receipt__footer {
            text-align: center;
        }

        .receipt__brand {
            font-family: ui-sans-serif, system-ui, sans-serif;
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            margin: 0;
        }

        .receipt__legal-name {
            font-family: ui-sans-serif, system-ui, sans-serif;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.35;
            margin: 0;
            text-transform: none;
            letter-spacing: normal;
        }

        .receipt__legal-meta {
            margin: 0.35rem 0 0;
            font-size: 10px;
            color: #222;
            line-height: 1.5;
        }

        .receipt__legal-meta p {
            margin: 0;
        }

        .receipt__legal-meta p + p {
            margin-top: 0.15rem;
        }

        .receipt__slogan {
            margin: 0.2rem 0 0;
            font-family: ui-sans-serif, system-ui, sans-serif;
            font-size: 11px;
            color: #444;
            letter-spacing: 0.04em;
        }

        .receipt__contact {
            margin: 0.65rem 0 0;
            font-size: 10px;
            color: #333;
            line-height: 1.5;
        }

        .receipt__contact p {
            margin: 0;
        }

        .rule,
        .rule-thick {
            border: 0;
            border-top: 1px dashed #999;
            margin: 0.75rem 0;
        }

        .rule-thick {
            border-top-style: solid;
            border-top-width: 2px;
            border-color: #111;
        }

        .receipt__title {
            text-align: center;
            font-family: ui-sans-serif, system-ui, sans-serif;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            margin: 0;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 0.2rem 1rem;
            margin-top: 0.65rem;
        }

        .meta-grid dt {
            margin: 0;
            color: #555;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .meta-grid dd {
            margin: 0;
            text-align: right;
            font-weight: 600;
        }

        .section-label {
            margin: 0 0 0.35rem;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #555;
        }

        .customer-block p {
            margin: 0;
        }

        .customer-block p + p {
            margin-top: 0.15rem;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0.35rem;
        }

        .items-table th {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #555;
            font-weight: 600;
            padding: 0 0 0.35rem;
            border-bottom: 1px solid #ccc;
            text-align: left;
        }

        .items-table th.num,
        .items-table td.num {
            text-align: right;
            white-space: nowrap;
        }

        .items-table td {
            padding: 0.4rem 0;
            vertical-align: top;
            border-bottom: 1px dotted #ddd;
        }

        .items-table tr:last-child td {
            border-bottom: none;
        }

        .item-name {
            display: block;
            font-weight: 600;
        }

        .item-sku {
            display: block;
            font-size: 9px;
            color: #666;
            margin-top: 0.1rem;
        }

        .totals {
            margin-top: 0.35rem;
        }

        .totals .row {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.15rem 0;
        }

        .totals .row.discount span:last-child {
            color: #0a6b3d;
        }

        .totals .grand {
            margin-top: 0.35rem;
            padding-top: 0.35rem;
            border-top: 2px solid #111;
            font-size: 14px;
            font-weight: 800;
        }

        .payment-block p {
            margin: 0;
        }

        .payment-block p + p {
            margin-top: 0.15rem;
        }

        .receipt__footer {
            margin-top: 0.75rem;
            font-size: 10px;
            color: #333;
            line-height: 1.55;
        }

        .receipt__footer p {
            margin: 0;
        }

        .receipt__footer p + p {
            margin-top: 0.45rem;
        }

        .receipt__thanks {
            font-family: ui-sans-serif, system-ui, sans-serif;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            font-size: 11px;
            margin-bottom: 0.45rem !important;
        }

        .no-print {
            margin-top: 1.25rem;
            display: flex;
            gap: 0.5rem;
        }

        .no-print button,
        .no-print a.secondary {
            padding: 0.6rem 1rem;
            border: none;
            cursor: pointer;
            font-size: 12px;
            font-family: ui-sans-serif, system-ui, sans-serif;
        }

        .no-print button.primary {
            flex: 1;
            background: #1a2e22;
            color: #f7f2e8;
        }

        .no-print button.secondary,
        .no-print a.secondary {
            background: #e5e8e4;
            color: #1a2e22;
            text-decoration: none;
            text-align: center;
        }

        @media print {
            @page {
                margin: 4mm;
                size: 80mm auto;
            }

            body {
                padding: 0;
                max-width: none;
                width: 72mm;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <header class="receipt__header">
            @if(filled($site->business_name))
                <h1 class="receipt__legal-name">{{ $site->business_name }}</h1>
                @if(filled($site->gst_tpn))
                    <div class="receipt__legal-meta">
                        <p><strong>GST TPN:</strong> {{ $site->gst_tpn }}</p>
                        @if(filled($site->business_licence_number))
                            <p><strong>Licence:</strong> {{ $site->business_licence_number }}</p>
                        @endif
                        @if(filled($site->businessAddressBlock()))
                            <p style="white-space: pre-line;">{{ $site->businessAddressBlock() }}</p>
                        @endif
                        @if(filled($site->business_phone))
                            <p>{{ $site->business_phone }}</p>
                        @endif
                    </div>
                @endif
            @else
                <h1 class="receipt__brand">{{ $site->company_name }}</h1>
            @endif
            @if(filled($site->company_subtitle))
                <p class="receipt__slogan">{{ $site->company_subtitle }}</p>
            @endif
            <div class="receipt__contact">
                @if(filled($site->contact_address))
                    <p>{!! nl2br(e($site->contact_address)) !!}</p>
                @endif
                @if(filled($site->contact_phone))
                    <p>Tel: {{ $site->contact_phone }}</p>
                @endif
                @if(filled($site->contact_email))
                    <p>{{ $site->contact_email }}</p>
                @endif
            </div>
        </header>

        <hr class="rule-thick">

        <p class="receipt__title">Sales Receipt</p>

        <dl class="meta-grid">
            <dt>Receipt no.</dt>
            <dd>{{ $order->number }}</dd>

            <dt>Date</dt>
            <dd>{{ $order->created_at->format('M j, Y') }}</dd>

            <dt>Time</dt>
            <dd>{{ $order->created_at->format('g:i A') }}</dd>

            <dt>Order status</dt>
            <dd>{{ $order->status->getLabel() }}</dd>

            <dt>Payment</dt>
            <dd>{{ $order->payment_status->getLabel() }}</dd>

            @if($order->payment_reference)
                <dt>Payment ref.</dt>
                <dd>{{ $order->payment_reference }}</dd>
            @endif

            <dt>Fulfillment</dt>
            <dd>{{ $isCounter ? 'Counter sale' : ($isPickup ? 'In-store pickup' : 'Delivery') }}</dd>

            <dt>Currency</dt>
            <dd>{{ $currency }}</dd>
        </dl>

        <hr class="rule">

        @if($order->shippingAddress)
            <p class="section-label">Customer</p>
            <div class="customer-block">
                <p>{{ $order->shippingAddress->full_name }}</p>
                @if($order->shippingAddress->phone)
                    <p>{{ $order->shippingAddress->phone }}</p>
                @endif
                @if($customerEmail)
                    <p>{{ $customerEmail }}</p>
                @endif
                @if(! $isPickup)
                    <p style="margin-top: 0.35rem;">
                        {{ $order->shippingAddress->street_address }}<br>
                        {{ $order->shippingAddress->city }} {{ $order->shippingAddress->postal_code }}
                    </p>
                @elseif(filled($site->pickup_address_label))
                    <p style="margin-top: 0.35rem;">Pickup: {{ $site->pickup_address_label }}</p>
                @endif
            </div>

            <hr class="rule">
        @endif

        <p class="section-label">Items</p>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="num">Qty</th>
                    <th class="num">Rate</th>
                    <th class="num">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>
                            <span class="item-name">{{ $item->name }}</span>
                            @if($item->sku)
                                <span class="item-sku">SKU: {{ $item->sku }}</span>
                            @endif
                        </td>
                        <td class="num">{{ $item->quantity }}</td>
                        <td class="num">{{ number_format($item->unit_price_minor / 100, 2) }}</td>
                        <td class="num">{{ number_format($item->line_total_minor / 100, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <hr class="rule">

        <div class="totals">
            <div class="row">
                <span>Subtotal</span>
                <span>Nu. {{ number_format($pricing['subtotal_minor'] / 100, 2) }}</span>
            </div>
            @if($pricing['discount_minor'] > 0)
                <div class="row discount">
                    <span>Discount{{ $couponCode ? ' ('.$couponCode.')' : '' }}</span>
                    <span>− Nu. {{ number_format($pricing['discount_minor'] / 100, 2) }}</span>
                </div>
            @endif
            @if($pricing['gst_minor'] > 0)
                <div class="row">
                    <span>
                        @if(($pricing['show_tax_rate'] ?? true) && ($pricing['effective_tax_rate'] ?? $pricing['gst_percentage'] ?? 0) > 0)
                            GST ({{ rtrim(rtrim(number_format($pricing['effective_tax_rate'] ?? $pricing['gst_percentage'], 2), '0'), '.') }}%)
                        @else
                            GST
                        @endif
                    </span>
                    <span>Nu. {{ number_format($pricing['gst_minor'] / 100, 2) }}</span>
                </div>
            @endif
            <div class="row grand">
                <span>Total</span>
                <span>Nu. {{ number_format($order->total_minor / 100, 2) }}</span>
            </div>
        </div>

        <hr class="rule">

        <p class="section-label">Payment details</p>
        <div class="payment-block">
            <p>{{ \App\Support\PaymentMethods::paymentSummary($order->metadata ?? [], $order->payment_reference) }}</p>
            <p>Status: {{ $order->payment_status->getLabel() }}</p>
        </div>

        @include('partials.order-bank-details', [
            'merchantAccount' => $merchantAccount ?? PaymentChannels::merchantAccount(),
            'paymentApps' => $paymentApps ?? PaymentChannels::paymentApps(),
            'compact' => true,
        ])

        <hr class="rule-thick">

        <footer class="receipt__footer">
            <p class="receipt__thanks">Thank you for your purchase</p>
            @if(filled($site->footer_about))
                <p>{{ $site->footer_about }}</p>
            @endif
            @if(filled($site->contact_phone) || filled($site->contact_email))
                <p>
                    @if(filled($site->contact_phone))
                        {{ $site->contact_phone }}
                    @endif
                    @if(filled($site->contact_phone) && filled($site->contact_email))
                        ·
                    @endif
                    @if(filled($site->contact_email))
                        {{ $site->contact_email }}
                    @endif
                </p>
            @endif
            <p style="margin-top: 0.65rem; color: #666;">
                {{ filled($site->business_name) ? $site->business_name : $site->company_name }}
                @if(filled($site->gst_tpn))
                    · GST TPN {{ $site->gst_tpn }}
                @endif
                · {{ now()->format('Y') }}
            </p>
        </footer>
    </div>

    <div class="no-print">
        <button type="button" class="primary" onclick="window.print()">Print receipt</button>
        <a href="{{ $closeUrl }}" class="secondary">Back to order</a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (new URLSearchParams(window.location.search).get('autoprint') === '1') {
                setTimeout(function () { window.print(); }, 300);
            }
        });
    </script>
</body>
</html>
