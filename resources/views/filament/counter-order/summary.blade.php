@php
    /** @var array<string, mixed> $pricing */
    /** @var bool $compact */
    $compact = $compact ?? false;
@endphp

<div @class(['oth-counter-summary', 'oth-counter-summary--compact' => $compact])>
    <dl class="oth-counter-summary__grid">
        <div>
            <dt>Subtotal</dt>
            <dd>Nu. {{ number_format(($pricing['subtotal_minor'] ?? 0) / 100, 2) }}</dd>
        </div>
        @if(($pricing['discount_minor'] ?? 0) > 0)
            <div>
                <dt>Discount</dt>
                <dd class="oth-counter-summary__discount">− Nu. {{ number_format($pricing['discount_minor'] / 100, 2) }}</dd>
            </div>
        @endif
        @if(($pricing['gst_minor'] ?? 0) > 0)
            <div>
                <dt>GST ({{ rtrim(rtrim(number_format($pricing['gst_percentage'] ?? 0, 2), '0'), '.') }}%)</dt>
                <dd>Nu. {{ number_format($pricing['gst_minor'] / 100, 2) }}</dd>
            </div>
        @endif
        <div class="oth-counter-summary__total">
            <dt>Total due</dt>
            <dd>Nu. {{ number_format(($pricing['total_minor'] ?? 0) / 100, 2) }}</dd>
        </div>
    </dl>

    @if(! empty($pricing['coupon_error']))
        <p class="oth-counter-summary__alert">Coupon code is invalid or expired.</p>
    @elseif(! empty($pricing['coupon_code']))
        <p class="oth-counter-summary__meta">Coupon applied: <code>{{ $pricing['coupon_code'] }}</code></p>
    @endif

    @if(! empty($customer) && ! $compact)
        <div class="oth-counter-summary__section">
            <h4>Customer</h4>
            <p>{{ trim(($customer['first_name'] ?? '').' '.($customer['last_name'] ?? '')) ?: '—' }}</p>
            @if(! empty($customer['phone']))
                <p>{{ $customer['phone'] }}</p>
            @endif
        </div>
    @endif

    @if(! empty($payment) && ! $compact)
        <div class="oth-counter-summary__section">
            <h4>Payment</h4>
            <p>{{ \App\Support\PaymentMethods::paymentSummary([
                'payment_method' => $payment['payment_method'] ?? null,
                'payment_bank' => $payment['payment_bank'] ?? null,
            ], $payment['payment_reference'] ?? null) }}</p>
        </div>
    @endif
</div>
