@extends('storefront.layout')

@section('title', 'Checkout — Othbar')

@section('content')

<div class="sf-page-header" style="background: #EDE5D0; border-bottom: 1px solid #D8CCAD;">
    <div class="sf-container">
        <p class="section-label">Almost there</p>
        <h1 style="font-family: 'Cormorant Garamond', serif; font-size: clamp(2.5rem, 5vw, 4rem); color: #1E3A2A; margin-top: 0.5rem;">Checkout</h1>
    </div>
</div>

<div class="sf-container sf-page-body">

    @if($errors->any())
    <div style="background: #F8D7DA; border: 1px solid #F5C6CB; color: #721C24; padding: 0.875rem 1.25rem; margin-bottom: 2rem; font-size: 0.875rem; border-radius: 0;">
        <ul style="margin: 0; padding-left: 1.25rem;">
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

            {{-- Left column --}}
            <div>

                {{-- ── Delivery details ── --}}
                <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 1.75rem; color: #1E3A2A; margin-bottom: 2rem;">1. Contact &amp; fulfillment</h2>

                <div class="sf-form-grid-2" style="margin-bottom: 1.25rem;">
                    <div>
                        <label style="display: block; font-size: 0.72rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: #1E3A2A; margin-bottom: 0.5rem;">First name *</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" required
                            style="width: 100%; padding: 0.875rem 1rem; border: 1px solid {{ $errors->has('first_name') ? '#b91c1c' : '#D8CCAD' }}; background: #F7F2E8; font-family: 'Jost', sans-serif; font-size: 0.9rem; color: #1E3A2A; outline: none; box-sizing: border-box;"
                            onfocus="this.style.borderColor='#1E3A2A'" onblur="this.style.borderColor='#D8CCAD'">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.72rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: #1E3A2A; margin-bottom: 0.5rem;">Last name *</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" required
                            style="width: 100%; padding: 0.875rem 1rem; border: 1px solid {{ $errors->has('last_name') ? '#b91c1c' : '#D8CCAD' }}; background: #F7F2E8; font-family: 'Jost', sans-serif; font-size: 0.9rem; color: #1E3A2A; outline: none; box-sizing: border-box;"
                            onfocus="this.style.borderColor='#1E3A2A'" onblur="this.style.borderColor='#D8CCAD'">
                    </div>
                </div>

                <div class="sf-form-grid-2" style="margin-bottom: 1.25rem;">
                    <div>
                        <label style="display: block; font-size: 0.72rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: #1E3A2A; margin-bottom: 0.5rem;">Phone *</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="+975 17 123 456"
                            style="width: 100%; padding: 0.875rem 1rem; border: 1px solid {{ $errors->has('phone') ? '#b91c1c' : '#D8CCAD' }}; background: #F7F2E8; font-family: 'Jost', sans-serif; font-size: 0.9rem; color: #1E3A2A; outline: none; box-sizing: border-box;"
                            onfocus="this.style.borderColor='#1E3A2A'" onblur="this.style.borderColor='#D8CCAD'">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.72rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: #1E3A2A; margin-bottom: 0.5rem;">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="For order updates (optional)"
                            style="width: 100%; padding: 0.875rem 1rem; border: 1px solid #D8CCAD; background: #F7F2E8; font-family: 'Jost', sans-serif; font-size: 0.9rem; color: #1E3A2A; outline: none; box-sizing: border-box;"
                            onfocus="this.style.borderColor='#1E3A2A'" onblur="this.style.borderColor='#D8CCAD'">
                    </div>
                </div>

                <div style="margin-bottom: 2rem;">
                    <p style="font-size: 0.72rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: #1E3A2A; margin-bottom: 1rem;">How would you like to receive this order?</p>
                    <div style="display: flex; flex-wrap: wrap; gap: 1.25rem;">
                        <label style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer; font-size: 0.9rem; color: #1E3A2A;">
                            <input type="radio" name="fulfillment_method" value="delivery" {{ $fulfillment === 'delivery' ? 'checked' : '' }} onchange="window.othbarSetFulfillment('delivery')">
                            Delivery to address
                        </label>
                        <label style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer; font-size: 0.9rem; color: #1E3A2A;">
                            <input type="radio" name="fulfillment_method" value="pickup" {{ $fulfillment === 'pickup' ? 'checked' : '' }} onchange="window.othbarSetFulfillment('pickup')">
                            In-store pickup
                        </label>
                    </div>
                </div>

                <div id="delivery-address-block" style="{{ $fulfillment === 'pickup' ? 'display:none' : '' }}">
                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-size: 0.72rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: #1E3A2A; margin-bottom: 0.5rem;">Street address *</label>
                    <input type="text" name="street_address" id="checkout_street" value="{{ old('street_address') }}" {{ $fulfillment === 'delivery' ? 'required' : '' }} {{ $fulfillment === 'pickup' ? 'disabled' : '' }} placeholder="House/block number, street name"
                        style="width: 100%; padding: 0.875rem 1rem; border: 1px solid {{ $errors->has('street_address') ? '#b91c1c' : '#D8CCAD' }}; background: #F7F2E8; font-family: 'Jost', sans-serif; font-size: 0.9rem; color: #1E3A2A; outline: none; box-sizing: border-box;"
                        onfocus="this.style.borderColor='#1E3A2A'" onblur="this.style.borderColor='#D8CCAD'">
                </div>

                <div class="sf-form-grid-2" style="margin-bottom: 1.25rem;">
                    <div>
                        <label style="display: block; font-size: 0.72rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: #1E3A2A; margin-bottom: 0.5rem;">City / Dzongkhag *</label>
                        <input type="text" name="city" id="checkout_city" value="{{ old('city', 'Thimphu') }}" {{ $fulfillment === 'delivery' ? 'required' : '' }} {{ $fulfillment === 'pickup' ? 'disabled' : '' }}
                            style="width: 100%; padding: 0.875rem 1rem; border: 1px solid {{ $errors->has('city') ? '#b91c1c' : '#D8CCAD' }}; background: #F7F2E8; font-family: 'Jost', sans-serif; font-size: 0.9rem; color: #1E3A2A; outline: none; box-sizing: border-box;"
                            onfocus="this.style.borderColor='#1E3A2A'" onblur="this.style.borderColor='#D8CCAD'">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.72rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: #1E3A2A; margin-bottom: 0.5rem;">Postal code *</label>
                        <input type="text" name="postal_code" id="checkout_postal" value="{{ old('postal_code') }}" placeholder="e.g. 11001" {{ $fulfillment === 'delivery' ? 'required' : '' }} {{ $fulfillment === 'pickup' ? 'disabled' : '' }}
                            style="width: 100%; padding: 0.875rem 1rem; border: 1px solid {{ $errors->has('postal_code') ? '#b91c1c' : '#D8CCAD' }}; background: #F7F2E8; font-family: 'Jost', sans-serif; font-size: 0.9rem; color: #1E3A2A; outline: none; box-sizing: border-box;"
                            onfocus="this.style.borderColor='#1E3A2A'" onblur="this.style.borderColor='#D8CCAD'">
                    </div>
                </div>
                </div>

                <div style="margin-bottom: 2.5rem;">
                    <label style="display: block; font-size: 0.72rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: #1E3A2A; margin-bottom: 0.5rem;">Order notes</label>
                    <textarea name="notes" rows="2" placeholder="Delivery instructions, pickup time preference, or other notes…"
                        style="width: 100%; padding: 0.875rem 1rem; border: 1px solid #D8CCAD; background: #F7F2E8; font-family: 'Jost', sans-serif; font-size: 0.9rem; color: #1E3A2A; outline: none; box-sizing: border-box; resize: vertical;"
                        onfocus="this.style.borderColor='#1E3A2A'" onblur="this.style.borderColor='#D8CCAD'">{{ old('notes') }}</textarea>
                </div>

                <div class="gold-line" style="margin-bottom: 2.5rem;"></div>

                <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 1.75rem; color: #1E3A2A; margin-bottom: 0.5rem;">2. Payment</h2>
                <p style="font-size: 0.85rem; color: rgba(30,58,42,0.65); margin-bottom: 0; line-height: 1.7;">
                    After you place your order, you’ll go to a secure page with our bank details and scan-to-pay QR codes. You’ll upload your payment screenshot there — we only finalize payment after our team verifies your proof.
                </p>

            </div>

            {{-- ── Right: Order summary ── --}}
            <div class="sf-sticky-summary" style="background: #EDE5D0; padding: 2rem; border: 1px solid #D8CCAD;">
                <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 1.5rem; color: #1E3A2A; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #D8CCAD;">Your order</h2>

                <div style="margin-bottom: 1.25rem;">
                    @foreach($cartLines as $line)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid rgba(216,204,173,0.5);">
                        <div>
                            <p style="font-size: 0.88rem; color: #1E3A2A; font-weight: 500; line-height: 1.4;">{{ $line->purchasable->name ?? 'Product' }}</p>
                            <p style="font-size: 0.72rem; color: rgba(30,58,42,0.5); margin-top: 0.15rem;">Qty {{ $line->quantity }} &times; Nu. {{ number_format($line->unit_price_amount / 100) }}</p>
                        </div>
                        <span style="font-family: 'Cormorant Garamond', serif; font-size: 1rem; font-weight: 600; color: #1E3A2A; white-space: nowrap; margin-left: 1rem;">Nu. {{ number_format(($line->unit_price_amount * $line->quantity) / 100) }}</span>
                    </div>
                    @endforeach
                </div>

                @if($cart && $cart->coupon_code)
                <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; font-size: 0.82rem; color: #1E3A2A; border-bottom: 1px solid rgba(216,204,173,0.4);">
                    <span>Coupon: <strong>{{ $cart->coupon_code }}</strong></span>
                    <span style="color: #C4843C;">Applied</span>
                </div>
                @endif

                <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; font-size: 0.82rem; color: #1E3A2A; border-bottom: 1px solid rgba(216,204,173,0.4);">
                    <span>Subtotal</span>
                    <span>Nu. {{ number_format(($subtotalMinor ?? $cartLines->sum(fn($l) => $l->unit_price_amount * $l->quantity)) / 100) }}</span>
                </div>

                @if(($discountMinor ?? 0) > 0)
                <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; font-size: 0.82rem; color: #1E3A2A; border-bottom: 1px solid rgba(216,204,173,0.4);">
                    <span>Discount</span>
                    <span style="color: #C4843C;">− Nu. {{ number_format($discountMinor / 100) }}</span>
                </div>
                @endif

                <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-top: 2px solid #1E3A2A; margin-top: 0.5rem; margin-bottom: 1.5rem;">
                    <span style="font-size: 1rem; font-weight: 600; color: #1E3A2A;">Total</span>
                    <span style="font-family: 'Cormorant Garamond', serif; font-size: 1.75rem; font-weight: 700; color: #1E3A2A;">Nu. {{ number_format(($totalMinor ?? $cartLines->sum(fn($l) => $l->unit_price_amount * $l->quantity)) / 100) }}</span>
                </div>

                {{-- Payment method badge --}}
                <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem 1rem; background: #F7F2E8; border: 1px solid #D8CCAD; margin-bottom: 1.5rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1E3A2A" stroke-width="1.5"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    <div>
                        <p style="font-size: 0.78rem; font-weight: 600; color: #1E3A2A;">Scan to Pay</p>
                        <p style="font-size: 0.7rem; color: rgba(30,58,42,0.5);">MBOB · EPAY · DKBANK</p>
                    </div>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%; display: flex; justify-content: center; border: none; cursor: pointer; font-size: 0.85rem; box-sizing: border-box;">
                    Continue to payment
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
                </button>

                <p style="font-size: 0.7rem; color: rgba(30,58,42,0.45); text-align: center; margin-top: 1rem; line-height: 1.6;">
                    Next step: pay via QR and upload your payment screenshot for verification.
                </p>

                <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #D8CCAD;">
                    <a href="{{ route('cart') }}" style="font-size: 0.75rem; color: rgba(30,58,42,0.5); text-decoration: none; display: flex; align-items: center; gap: 0.4rem; justify-content: center;"
                        onmouseover="this.style.color='#1E3A2A'" onmouseout="this.style.color='rgba(30,58,42,0.5)'">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5m7-7-7 7 7 7"/></svg>
                        Edit basket
                    </a>
                </div>
            </div>

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
