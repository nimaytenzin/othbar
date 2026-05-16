@extends('storefront.layout')

@section('title', 'Your Basket — Othbar')

@section('content')

<div style="background: #EDE5D0; border-bottom: 1px solid #D8CCAD; padding: 4rem 0 3rem;">
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 2rem;">
        <p class="section-label">Your selections</p>
        <h1 style="font-family: 'Cormorant Garamond', serif; font-size: clamp(2.5rem, 5vw, 4rem); color: #1E3A2A; margin-top: 0.5rem;">Your basket</h1>
    </div>
</div>

<div style="max-width: 1280px; margin: 0 auto; padding: 4rem 2rem;">

    {{-- Flash messages --}}
    @if(session('success'))
    <div style="background: #D4EDDA; border: 1px solid #C3E6CB; color: #155724; padding: 0.875rem 1.25rem; margin-bottom: 2rem; font-size: 0.875rem;">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div style="background: #F8D7DA; border: 1px solid #F5C6CB; color: #721C24; padding: 0.875rem 1.25rem; margin-bottom: 2rem; font-size: 0.875rem;">
        {{ session('error') }}
    </div>
    @endif

    @if($cartLines->isEmpty())
    <div style="text-align: center; padding: 4rem 0;">
        <p style="font-family: 'Cormorant Garamond', serif; font-size: 1.5rem; color: #1E3A2A; margin-bottom: 1rem;">Your basket is empty</p>
        <a href="{{ route('shop') }}" class="btn-primary" style="display: inline-flex; text-decoration: none; justify-content: center;">
            Continue shopping
        </a>
    </div>
    @else
    <div style="display: grid; grid-template-columns: 1fr 380px; gap: 4rem; align-items: start;">

        {{-- Cart items --}}
        <div>
            @foreach($cartLines as $line)
            <div style="display: grid; grid-template-columns: 100px 1fr auto; gap: 1.5rem; padding: 1.75rem 0; border-bottom: 1px solid #D8CCAD; align-items: center;">
                <div style="aspect-ratio: 1; background: #D8CCAD; overflow: hidden;">
                    @if($line->purchasable && $line->purchasable->getFirstMediaUrl('thumbnail'))
                    <img src="{{ $line->purchasable->getFirstMediaUrl('thumbnail') }}" alt="{{ $line->purchasable->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                    <div class="img-placeholder" style="width: 100%; height: 100%;"></div>
                    @endif
                </div>
                <div>
                    <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 1.25rem; color: #1E3A2A; font-weight: 600; margin-bottom: 0.25rem;">{{ $line->purchasable->name ?? 'Product' }}</h3>
                    <span style="font-size: 0.85rem; color: rgba(30,58,42,0.55);">Nu. {{ number_format($line->unit_price_amount / 100) }} each</span>

                    {{-- Quantity update form --}}
                    <form method="POST" action="{{ route('cart.line.update', $line->line_index) }}" style="display: flex; align-items: center; margin-top: 1rem; gap: 0.5rem;">
                        @csrf
                        @method('PATCH')
                        <div style="display: flex; align-items: center; border: 1px solid #D8CCAD;">
                            <button type="button" style="padding: 0.375rem 0.75rem; background: none; border: none; cursor: pointer; font-size: 1rem; color: #1E3A2A; line-height: 1;"
                                onclick="const i=this.nextElementSibling;if(parseInt(i.value)>1){i.value=parseInt(i.value)-1;this.closest('form').submit();}">−</button>
                            <input type="number" name="quantity" value="{{ $line->quantity }}" min="1" style="width: 44px; text-align: center; border: none; border-left: 1px solid #D8CCAD; border-right: 1px solid #D8CCAD; background: none; font-family: 'Cormorant Garamond', serif; font-size: 1rem; color: #1E3A2A; outline: none; padding: 0.375rem 0;">
                            <button type="button" style="padding: 0.375rem 0.75rem; background: none; border: none; cursor: pointer; font-size: 1rem; color: #1E3A2A; line-height: 1;"
                                onclick="const i=this.previousElementSibling;i.value=parseInt(i.value)+1;this.closest('form').submit();">+</button>
                        </div>
                    </form>
                </div>
                <div style="text-align: right;">
                    <span style="font-family: 'Cormorant Garamond', serif; font-size: 1.5rem; font-weight: 600; color: #1E3A2A; display: block;">Nu. {{ number_format(($line->unit_price_amount * $line->quantity) / 100) }}</span>
                    <form method="POST" action="{{ route('cart.line.remove', $line->line_index) }}" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="margin-top: 0.75rem; font-size: 0.72rem; color: rgba(30,58,42,0.4); background: none; border: none; cursor: pointer; text-decoration: underline; text-underline-offset: 2px;"
                            onclick="return confirm('Remove this item?')">Remove</button>
                    </form>
                </div>
            </div>
            @endforeach

            <div style="margin-top: 2rem;">
                <a href="{{ route('shop') }}" style="font-size: 0.78rem; font-weight: 600; letter-spacing: 0.12em; text-transform: uppercase; color: #1E3A2A; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5m7-7-7 7 7 7"/></svg>
                    Continue shopping
                </a>
            </div>
        </div>

        {{-- Order summary --}}
        <div style="background: #EDE5D0; padding: 2rem; border: 1px solid #D8CCAD; position: sticky; top: 100px;">
            <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 1.5rem; color: #1E3A2A; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #D8CCAD;">Order summary</h2>

            {{-- Line items summary --}}
            <div style="margin-bottom: 1.25rem;">
                @foreach($cartLines as $line)
                <div style="display: flex; justify-content: space-between; padding: 0.4rem 0; font-size: 0.85rem; color: rgba(30,58,42,0.75);">
                    <span>{{ $line->purchasable->name ?? 'Product' }} &times; {{ $line->quantity }}</span>
                    <span>Nu. {{ number_format(($line->unit_price_amount * $line->quantity) / 100) }}</span>
                </div>
                @endforeach
            </div>

            <div class="gold-line" style="margin-bottom: 1.25rem;"></div>

            {{-- Coupon --}}
            @if($cart && $cart->coupon_code)
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; margin-bottom: 0.75rem; border-bottom: 1px solid #D8CCAD;">
                <span style="font-size: 0.82rem; color: #1E3A2A;">Coupon: <strong>{{ $cart->coupon_code }}</strong></span>
                <form method="POST" action="{{ route('cart.coupon.remove') }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="font-size: 0.72rem; color: rgba(30,58,42,0.4); background: none; border: none; cursor: pointer; text-decoration: underline; text-underline-offset: 2px;">Remove</button>
                </form>
            </div>
            @endif

            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-top: 1px solid #D8CCAD;">
                <span style="font-size: 0.88rem; font-weight: 600; color: #1E3A2A;">Subtotal</span>
                <span style="font-family: 'Cormorant Garamond', serif; font-size: 1rem; font-weight: 600; color: #1E3A2A;">Nu. {{ number_format(($subtotalMinor ?? $cartLines->sum(fn($l) => $l->unit_price_amount * $l->quantity)) / 100) }}</span>
            </div>

            @if(($discountMinor ?? 0) > 0)
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0; font-size: 0.88rem; color: #1E3A2A;">
                <span>Discount</span>
                <span style="color: #C4843C;">− Nu. {{ number_format($discountMinor / 100) }}</span>
            </div>
            @endif

            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-top: 1px solid #D8CCAD; border-bottom: 1px solid #D8CCAD; margin-bottom: 1.5rem;">
                <span style="font-size: 1rem; font-weight: 600; color: #1E3A2A;">Total</span>
                <span style="font-family: 'Cormorant Garamond', serif; font-size: 1.75rem; font-weight: 700; color: #1E3A2A;">Nu. {{ number_format(($totalMinor ?? $cartLines->sum(fn($l) => $l->unit_price_amount * $l->quantity)) / 100) }}</span>
            </div>

            {{-- Coupon form --}}
            @unless($cart && $cart->coupon_code)
            <form method="POST" action="{{ route('cart.coupon.apply') }}" style="margin-bottom: 1.5rem;">
                @csrf
                @if(session('coupon_error'))
                <p style="font-size: 0.78rem; color: #b91c1c; margin-bottom: 0.5rem;">{{ session('coupon_error') }}</p>
                @endif
                <div style="display: flex; border: 1px solid #D8CCAD; overflow: hidden;">
                    <input type="text" name="coupon_code" placeholder="Promo code" value="{{ old('coupon_code') }}"
                        style="flex: 1; padding: 0.75rem 1rem; background: #F7F2E8; border: none; font-family: 'Jost', sans-serif; font-size: 0.85rem; color: #1E3A2A; outline: none; text-transform: uppercase;" />
                    <button type="submit" style="padding: 0.75rem 1rem; background: #1E3A2A; border: none; font-family: 'Jost', sans-serif; font-size: 0.72rem; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; color: #F7F2E8; cursor: pointer;">Apply</button>
                </div>
            </form>
            @endunless

            <a href="{{ route('checkout') }}" class="btn-primary" style="width: 100%; display: flex; justify-content: center; text-decoration: none; font-size: 0.85rem; box-sizing: border-box;">
                Proceed to checkout
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
            </a>

            <div style="margin-top: 1.25rem; display: flex; justify-content: center; gap: 1.5rem; flex-wrap: wrap;">
                @foreach(['Secure payment', 'Organic certified', 'Eco packaging'] as $trust)
                <span style="font-size: 0.68rem; color: rgba(30,58,42,0.5);">{{ $trust }}</span>
                @endforeach
            </div>
        </div>

    </div>
    @endif
</div>

@endsection
