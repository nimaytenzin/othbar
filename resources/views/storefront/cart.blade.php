@extends('storefront.layout')

@section('title', 'Your Basket — Othbar')

@section('content')

<div class="sf-page-header sf-page-header--parchment">
    <div class="sf-container">
        <p class="section-label">Your selections</p>
        <h1 class="sf-heading-lg">Your basket</h1>
    </div>
</div>

<div class="sf-container sf-page-body">

    @if(session('success'))
    <div class="sf-alert sf-alert--success" role="status">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="sf-alert sf-alert--error" role="alert">{{ session('error') }}</div>
    @endif

    @if($cartLines->isEmpty())
    <div class="sf-empty-state">
        <p class="sf-empty-state__title">Your basket is empty</p>
        <a href="{{ route('shop') }}" class="btn-primary" style="display: inline-flex; text-decoration: none; justify-content: center;">
            Continue shopping
        </a>
    </div>
    @else
    <div class="sf-grid-split">

        <div>
            @foreach($cartLines as $line)
            <div class="sf-cart-line" style="padding: 1.75rem 0; border-bottom: 1px solid var(--border);">
                <div class="product-image-frame product-image-frame--square">
                    @if($line->purchasable)
                    <x-product-image :product="$line->purchasable" />
                    @endif
                </div>
                <div>
                    <h3 class="sf-cart-line-title">{{ $line->purchasable->name ?? 'Product' }}</h3>
                    <span class="sf-cart-line-meta">Nu. {{ number_format($line->unit_price_amount / 100) }} each</span>

                    <form method="POST" action="{{ route('cart.line.update', $line->line_index) }}">
                        @csrf
                        @method('PATCH')
                        <div class="sf-qty-control">
                            <button type="button" class="sf-qty-btn"
                                onclick="const i=this.nextElementSibling;if(parseInt(i.value)>1){i.value=parseInt(i.value)-1;this.closest('form').submit();}">−</button>
                            <input type="number" name="quantity" value="{{ $line->quantity }}" min="1" class="sf-qty-input">
                            <button type="button" class="sf-qty-btn"
                                onclick="const i=this.previousElementSibling;i.value=parseInt(i.value)+1;this.closest('form').submit();">+</button>
                        </div>
                    </form>
                </div>
                <div class="sf-cart-line__price">
                    <span class="sf-cart-line-price">Nu. {{ number_format(($line->unit_price_amount * $line->quantity) / 100) }}</span>
                    <form method="POST" action="{{ route('cart.line.remove', $line->line_index) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="sf-remove-btn" onclick="return confirm('Remove this item?')">Remove</button>
                    </form>
                </div>
            </div>
            @endforeach

            <div style="margin-top: 2rem;">
                <a href="{{ route('shop') }}" class="sf-link-back">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 12H5m7-7-7 7 7 7"/></svg>
                    Continue shopping
                </a>
            </div>
        </div>

        <aside class="sf-sticky-summary sf-summary-panel">
            <h2 class="sf-summary-panel__title">Order summary</h2>

            <div class="sf-summary-lines">
                @foreach($cartLines as $line)
                <div class="sf-summary-line">
                    <span>{{ $line->purchasable->name ?? 'Product' }} &times; {{ $line->quantity }}</span>
                    <span>Nu. {{ number_format(($line->unit_price_amount * $line->quantity) / 100) }}</span>
                </div>
                @endforeach
            </div>

            <div class="gold-line" style="margin-bottom: 1.25rem;"></div>

            @if($cart && $cart->coupon_code)
            <div class="sf-summary-row sf-summary-row--coupon">
                <span>Coupon: <strong>{{ $cart->coupon_code }}</strong></span>
                <form method="POST" action="{{ route('cart.coupon.remove') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="sf-remove-btn" style="margin-top: 0;">Remove</button>
                </form>
            </div>
            @endif

            <div class="sf-summary-row" style="border-top: 1px solid var(--border); padding-top: 0.75rem;">
                <span style="font-weight: 600;">Subtotal</span>
                <span style="font-family: 'Cormorant Garamond', Georgia, serif; font-weight: 600;">Nu. {{ number_format(($subtotalMinor ?? $cartLines->sum(fn($l) => $l->unit_price_amount * $l->quantity)) / 100) }}</span>
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

            @unless($cart && $cart->coupon_code)
            <form method="POST" action="{{ route('cart.coupon.apply') }}">
                @csrf
                @if(session('coupon_error'))
                <p class="sf-coupon-error">{{ session('coupon_error') }}</p>
                @endif
                <div class="sf-coupon-form">
                    <input type="text" name="coupon_code" placeholder="Promo code" value="{{ old('coupon_code') }}" class="sf-coupon-input" />
                    <button type="submit" class="sf-coupon-btn">Apply</button>
                </div>
            </form>
            @endunless

            <a href="{{ route('checkout') }}" class="btn-primary sf-btn-block" style="font-size: 0.85rem;">
                Proceed to checkout
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
            </a>
        </aside>

    </div>
    @endif
</div>

@endsection
