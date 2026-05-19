@php
    /** @var array<string, mixed> $pricing */
    /** @var array<int, array<string, mixed>> $cartLines */
    /** @var array<string, mixed> $customer */
    /** @var array<string, mixed> $payment */
    use App\Support\PaymentMethods;
@endphp

<div class="oth-counter-review">
    <div class="oth-counter-review__intro">
        <h3 class="oth-card__title" style="margin:0;">Review &amp; complete</h3>
        <p class="oth-card__subtitle" style="margin:0.35rem 0 0;">Confirm the details below, then complete the sale and print the receipt.</p>
    </div>

    <div class="oth-counter-review__grid">
        <section class="oth-card">
            <h4 class="oth-counter-review__heading">Products</h4>
            @if($cartLines === [])
                <p class="oth-counter-review__empty">No products added.</p>
            @else
                <ul class="oth-counter-review__lines">
                    @foreach($cartLines as $line)
                        <li>
                            <div>
                                <strong>{{ $line['name'] }}</strong>
                                <span class="oth-counter-review__meta">Qty {{ $line['quantity'] }} · Nu. {{ number_format($line['unit_price_minor'] / 100, 2) }} each</span>
                            </div>
                            <strong>Nu. {{ number_format($line['line_total_minor'] / 100, 2) }}</strong>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <section class="oth-card">
            <h4 class="oth-counter-review__heading">Customer</h4>
            <dl class="oth-counter-review__dl">
                <div><dt>Name</dt><dd>{{ trim(($customer['first_name'] ?? '').' '.($customer['last_name'] ?? '')) ?: '—' }}</dd></div>
                <div><dt>Phone</dt><dd>{{ $customer['phone'] ?? '—' }}</dd></div>
                @if(filled($customer['email'] ?? null))
                    <div><dt>Email</dt><dd>{{ $customer['email'] }}</dd></div>
                @endif
                @if(filled($customer['notes'] ?? null))
                    <div class="oth-counter-review__full"><dt>Notes</dt><dd>{{ $customer['notes'] }}</dd></div>
                @endif
            </dl>
        </section>

        <section class="oth-card">
            <h4 class="oth-counter-review__heading">Payment</h4>
            <dl class="oth-counter-review__dl">
                <div><dt>Details</dt><dd>{{ PaymentMethods::paymentSummary([
                    'payment_method' => $payment['payment_method'] ?? null,
                    'payment_bank' => $payment['payment_bank'] ?? null,
                ], $payment['payment_reference'] ?? null) }}</dd></div>
            </dl>
        </section>

        <section class="oth-card oth-counter-review__total-card">
            <h4 class="oth-counter-review__heading">Totals</h4>
            @include('filament.counter-order.summary', ['pricing' => $pricing])
        </section>
    </div>
</div>
