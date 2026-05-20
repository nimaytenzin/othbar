@extends('storefront.layout')

@section('title', 'Checkout — Othbar')

@section('content')

<div class="sf-page-header sf-page-header--parchment">
    <div class="sf-container">
        <p class="section-label">Almost there</p>
        <h1 class="sf-heading-lg">Checkout</h1>
    </div>
</div>

<div class="sf-container sf-page-body">

    @if($errors->any())
    <div class="sf-alert sf-alert--error" role="alert">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('checkout.place') }}">
        @csrf
        @php($fulfillment = old('fulfillment_method', 'delivery'))
        <div class="sf-grid-split--wide">

            <div>
                <h2 class="sf-checkout-step">1. Contact &amp; fulfillment</h2>

                <div class="sf-form-grid-2">
                    <div class="sf-field">
                        <label class="sf-label" for="first_name">First name *</label>
                        <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required
                            class="sf-input {{ $errors->has('first_name') ? 'is-invalid' : '' }}">
                    </div>
                    <div class="sf-field">
                        <label class="sf-label" for="last_name">Last name *</label>
                        <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required
                            class="sf-input {{ $errors->has('last_name') ? 'is-invalid' : '' }}">
                    </div>
                </div>

                <div class="sf-form-grid-2">
                    <div class="sf-field">
                        <label class="sf-label" for="phone">Phone *</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required placeholder="+975 17 123 456"
                            class="sf-input {{ $errors->has('phone') ? 'is-invalid' : '' }}">
                    </div>
                    <div class="sf-field">
                        <label class="sf-label" for="email">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="For order updates (optional)"
                            class="sf-input {{ $errors->has('email') ? 'is-invalid' : '' }}">
                    </div>
                </div>

                <p class="sf-label" style="margin-bottom: 1rem;">How would you like to receive this order?</p>
                <div class="sf-fulfillment-options">
                    <label class="sf-fulfillment-option">
                        <input type="radio" name="fulfillment_method" value="delivery" {{ $fulfillment === 'delivery' ? 'checked' : '' }} onchange="window.othbarSetFulfillment('delivery')">
                        Delivery to address
                    </label>
                    <label class="sf-fulfillment-option">
                        <input type="radio" name="fulfillment_method" value="pickup" {{ $fulfillment === 'pickup' ? 'checked' : '' }} onchange="window.othbarSetFulfillment('pickup')">
                        In-store pickup
                    </label>
                </div>

                <div id="delivery-address-block" style="{{ $fulfillment === 'pickup' ? 'display:none' : '' }}">
                    <div class="sf-field">
                        <label class="sf-label" for="checkout_street">Street address *</label>
                        <input type="text" name="street_address" id="checkout_street" value="{{ old('street_address') }}"
                            {{ $fulfillment === 'delivery' ? 'required' : '' }} {{ $fulfillment === 'pickup' ? 'disabled' : '' }}
                            placeholder="House/block number, street name"
                            class="sf-input {{ $errors->has('street_address') ? 'is-invalid' : '' }}">
                    </div>

                    <div class="sf-form-grid-2">
                        <div class="sf-field">
                            <label class="sf-label" for="checkout_city">City / Dzongkhag *</label>
                            <input type="text" name="city" id="checkout_city" value="{{ old('city', 'Thimphu') }}"
                                {{ $fulfillment === 'delivery' ? 'required' : '' }} {{ $fulfillment === 'pickup' ? 'disabled' : '' }}
                                class="sf-input {{ $errors->has('city') ? 'is-invalid' : '' }}">
                        </div>
                        <div class="sf-field">
                            <label class="sf-label" for="checkout_postal">Postal code *</label>
                            <input type="text" name="postal_code" id="checkout_postal" value="{{ old('postal_code') }}"
                                placeholder="e.g. 11001" {{ $fulfillment === 'delivery' ? 'required' : '' }} {{ $fulfillment === 'pickup' ? 'disabled' : '' }}
                                class="sf-input {{ $errors->has('postal_code') ? 'is-invalid' : '' }}">
                        </div>
                    </div>
                </div>

                <div class="sf-field" style="margin-bottom: 2.5rem;">
                    <label class="sf-label" for="notes">Order notes</label>
                    <textarea id="notes" name="notes" rows="2" placeholder="Delivery instructions, pickup time preference, or other notes…"
                        class="sf-textarea">{{ old('notes') }}</textarea>
                </div>

                <div class="gold-line" style="margin-bottom: 2.5rem;"></div>

                <h2 class="sf-checkout-step" style="margin-bottom: 0.5rem;">2. Payment</h2>
                <p class="sf-checkout-lead">
                    After you place your order, you’ll go to a secure page with our bank details and scan-to-pay QR codes. You’ll upload your payment screenshot there — we only finalize payment after our team verifies your proof.
                </p>
            </div>

            <aside class="sf-sticky-summary sf-summary-panel">
                <h2 class="sf-summary-panel__title">Your order</h2>

                <div class="sf-summary-lines">
                    @foreach($cartLines as $line)
                    <div class="sf-summary-line">
                        <div>
                            <p class="sf-summary-line__name">{{ $line->purchasable->name ?? 'Product' }}</p>
                            <p class="sf-summary-line__meta">Qty {{ $line->quantity }} &times; Nu. {{ number_format($line->unit_price_amount / 100) }}</p>
                        </div>
                        <span class="sf-summary-line__amount">Nu. {{ number_format(($line->unit_price_amount * $line->quantity) / 100) }}</span>
                    </div>
                    @endforeach
                </div>

                @if($cart && $cart->coupon_code)
                <div class="sf-summary-row sf-summary-row--coupon">
                    <span>Coupon: <strong>{{ $cart->coupon_code }}</strong></span>
                    <span style="color: var(--accent);">Applied</span>
                </div>
                @endif

                <div class="sf-summary-row">
                    <span>Subtotal</span>
                    <span>Nu. {{ number_format(($subtotalMinor ?? $cartLines->sum(fn($l) => $l->unit_price_amount * $l->quantity)) / 100) }}</span>
                </div>

                @if(($discountMinor ?? 0) > 0)
                <div class="sf-summary-row">
                    <span>Discount</span>
                    <span style="color: var(--accent);">− Nu. {{ number_format($discountMinor / 100) }}</span>
                </div>
                @endif

                @if(($gstMinor ?? 0) > 0)
                <div class="sf-summary-row">
                    <span>
                        @if(($effectiveTaxRate ?? 0) > 0)
                            GST ({{ rtrim(rtrim(number_format($effectiveTaxRate, 2), '0'), '.') }}%)
                        @else
                            GST
                        @endif
                    </span>
                    <span>Nu. {{ number_format($gstMinor / 100) }}</span>
                </div>
                @endif

                <div class="sf-summary-total">
                    <span class="sf-summary-total__label">Total</span>
                    <span class="sf-summary-total__amount">Nu. {{ number_format(($totalMinor ?? $cartLines->sum(fn($l) => $l->unit_price_amount * $l->quantity)) / 100) }}</span>
                </div>

                <div class="sf-payment-badge">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1E3A2A" stroke-width="1.5" aria-hidden="true"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    <div>
                        <p class="sf-payment-badge__title">Scan to Pay</p>
                        <p class="sf-payment-badge__meta">{{ \App\Support\PaymentMethods::paymentAppNames() }}</p>
                    </div>
                </div>

                <button type="submit" class="btn-primary sf-btn-block" style="border: none; cursor: pointer; font-size: 0.85rem;">
                    Continue to payment
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
                </button>

                <p class="sf-checkout-note">
                    Next step: pay via QR and upload your payment screenshot for verification.
                </p>

                <div class="sf-summary-footer">
                    <a href="{{ route('cart') }}" class="sf-link-muted">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 12H5m7-7-7 7 7 7"/></svg>
                        Edit basket
                    </a>
                </div>
            </aside>

        </div>
    </form>
</div>

<script>
window.othbarSetFulfillment = function (mode) {
    const block = document.getElementById('delivery-address-block');
    const street = document.getElementById('checkout_street');
    const city = document.getElementById('checkout_city');
    const postal = document.getElementById('checkout_postal');
    if (!block || !street || !city || !postal) return;
    if (mode === 'pickup') {
        block.style.display = 'none';
        street.disabled = true;
        city.disabled = true;
        postal.disabled = true;
        street.removeAttribute('required');
        city.removeAttribute('required');
        postal.removeAttribute('required');
    } else {
        block.style.display = '';
        street.disabled = false;
        city.disabled = false;
        postal.disabled = false;
        street.setAttribute('required', 'required');
        city.setAttribute('required', 'required');
        postal.setAttribute('required', 'required');
    }
};
</script>

@endsection
