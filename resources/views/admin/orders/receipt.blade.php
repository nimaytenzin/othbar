<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt — {{ $order->number }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: ui-sans-serif, system-ui, sans-serif;
            color: #1a2e22;
            max-width: 420px;
            margin: 0 auto;
            padding: 1.5rem;
            font-size: 13px;
        }
        h1 {
            font-size: 1.25rem;
            margin: 0 0 0.25rem 0;
            letter-spacing: 0.02em;
        }
        .muted { color: #64786b; font-size: 11px; }
        .row { display: flex; justify-content: space-between; gap: 1rem; padding: 0.35rem 0; border-bottom: 1px solid #e5e8e4; }
        .row:last-child { border-bottom: none; }
        .total {
            margin-top: 0.75rem;
            padding-top: 0.75rem;
            border-top: 2px solid #1a2e22;
            font-size: 1.1rem;
            font-weight: 700;
            display: flex;
            justify-content: space-between;
        }
        .logo { font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 1rem; }
        @media print {
            body { padding: 0; max-width: none; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="logo">Othbar</div>
    <h1>Order {{ $order->number }}</h1>
    <p class="muted">{{ $order->created_at->format('M j, Y g:i A') }}</p>

    <div style="margin: 1rem 0;">
        <p class="muted" style="margin: 0 0 0.25rem;">Fulfillment</p>
        <p style="margin: 0; font-weight: 600;">{{ ($order->fulfillment_method ?? 'delivery') === 'pickup' ? 'In-store pickup' : 'Delivery' }}</p>
    </div>

    @if($order->shippingAddress)
    <div style="margin: 1rem 0;">
        <p class="muted" style="margin: 0 0 0.25rem;">Customer</p>
        <p style="margin: 0;">{{ $order->shippingAddress->full_name }}</p>
        <p style="margin: 0.25rem 0 0;">{{ $order->shippingAddress->phone }}</p>
        @if(($order->fulfillment_method ?? 'delivery') !== 'pickup')
            <p style="margin: 0.35rem 0 0; line-height: 1.4;">
                {{ $order->shippingAddress->street_address }}<br>
                {{ $order->shippingAddress->city }} {{ $order->shippingAddress->postal_code }}
            </p>
        @endif
    </div>
    @endif

    <p class="muted" style="margin: 1rem 0 0.25rem;">Items</p>
    @foreach($order->items as $item)
    <div class="row">
        <span>{{ $item->name }} × {{ $item->quantity }}</span>
        <span>Nu. {{ number_format($item->line_total_minor / 100) }}</span>
    </div>
    @endforeach

    <div class="total">
        <span>Total</span>
        <span>Nu. {{ number_format($order->total_minor / 100) }}</span>
    </div>

    <div style="margin-top: 1.25rem;">
        <p class="muted" style="margin: 0;">Payment</p>
        <p style="margin: 0.25rem 0 0;">{{ $order->payment_status->getLabel() }}</p>
    </div>

    <button type="button" class="no-print" onclick="window.print()"
        style="margin-top: 1.5rem; width: 100%; padding: 0.6rem 1rem; background: #1a2e22; color: #f7f2e8; border: none; cursor: pointer; font-size: 12px;">
        Print receipt
    </button>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.location.search.indexOf('autoprint=1') !== -1) {
                window.print();
            }
        });
    </script>
</body>
</html>
