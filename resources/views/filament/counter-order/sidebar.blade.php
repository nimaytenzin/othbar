@php
    /** @var array<string, mixed> $pricing */
    /** @var array<int, array<string, mixed>> $cartLines */
    /** @var int $itemCount */
    /** @var array<string, mixed> $customer */
    /** @var array<string, mixed> $payment */
    $steps = [
        ['key' => 'products', 'label' => 'Products', 'number' => 1],
        ['key' => 'payment', 'label' => 'Payment', 'number' => 2],
        ['key' => 'customer', 'label' => 'Customer', 'number' => 3],
        ['key' => 'receipt', 'label' => 'Receipt', 'number' => 4],
    ];
@endphp

<aside class="oth-counter-sidebar">
    <div class="oth-counter-sidebar__card">
        <p class="oth-counter-sidebar__eyebrow">In-store sale</p>
        <h3 class="oth-counter-sidebar__title">Order summary</h3>

        <ol class="oth-counter-steps">
            @foreach($steps as $step)
                <li class="oth-counter-steps__item">
                    <span class="oth-counter-steps__badge">{{ $step['number'] }}</span>
                    <span>{{ $step['label'] }}</span>
                </li>
            @endforeach
        </ol>

        <div class="oth-counter-sidebar__stats">
            <div class="oth-counter-sidebar__stat">
                <span class="oth-counter-sidebar__stat-label">Items</span>
                <strong>{{ $itemCount }}</strong>
            </div>
            <div class="oth-counter-sidebar__stat">
                <span class="oth-counter-sidebar__stat-label">Lines</span>
                <strong>{{ count($cartLines) }}</strong>
            </div>
        </div>

        @if($cartLines !== [])
            <ul class="oth-counter-sidebar__lines">
                @foreach(array_slice($cartLines, 0, 4) as $line)
                    <li>
                        <span>{{ $line['name'] }} × {{ $line['quantity'] }}</span>
                        <span>Nu. {{ number_format($line['line_total_minor'] / 100, 2) }}</span>
                    </li>
                @endforeach
                @if(count($cartLines) > 4)
                    <li class="oth-counter-sidebar__more">+ {{ count($cartLines) - 4 }} more line(s)</li>
                @endif
            </ul>
        @endif

        @include('filament.counter-order.summary', [
            'pricing' => $pricing,
            'compact' => true,
        ])

        @if(filled(trim(($customer['first_name'] ?? '').' '.($customer['last_name'] ?? ''))))
            <div class="oth-counter-sidebar__section">
                <p class="oth-counter-sidebar__section-label">Customer</p>
                <p class="oth-counter-sidebar__section-value">{{ trim(($customer['first_name'] ?? '').' '.($customer['last_name'] ?? '')) }}</p>
                @if(filled($customer['phone'] ?? null))
                    <p class="oth-counter-sidebar__section-meta">{{ $customer['phone'] }}</p>
                @endif
            </div>
        @endif

@if(filled($payment['payment_method'] ?? null))
            <div class="oth-counter-sidebar__section">
                <p class="oth-counter-sidebar__section-label">Payment</p>
                <p class="oth-counter-sidebar__section-value">{{ \App\Support\PaymentMethods::paymentSummary([
                    'payment_method' => $payment['payment_method'] ?? null,
                    'payment_bank' => $payment['payment_bank'] ?? null,
                ], $payment['payment_reference'] ?? null) }}</p>
            </div>
        @endif
    </div>
</aside>
