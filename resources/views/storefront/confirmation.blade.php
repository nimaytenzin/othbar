@extends('storefront.layout')

@section('title', 'Order Confirmed — Othbar')

@section('content')

<div style="background: #EDE5D0; border-bottom: 1px solid #D8CCAD; padding: 4rem 0 3rem;">
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 2rem;">
        <p class="section-label">Thank you</p>
        <h1 style="font-family: 'Cormorant Garamond', serif; font-size: clamp(2.5rem, 5vw, 4rem); color: #1E3A2A; margin-top: 0.5rem;">Order confirmed</h1>
    </div>
</div>

<div style="max-width: 700px; margin: 0 auto; padding: 5rem 2rem; text-align: center;">

    <div style="width: 72px; height: 72px; background: #1E3A2A; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem;">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#D4A843" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
    </div>

    <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 1.75rem; color: #1E3A2A; margin-bottom: 0.75rem;">
        Order received — pending payment verification
    </h2>
    <p style="font-size: 0.92rem; color: rgba(30,58,42,0.65); line-height: 1.8; margin-bottom: 2.5rem;">
        We've received your order. Once we verify your bank transfer (usually within 1–2 hours), we'll prepare your order and dispatch it from the Bhutanese highlands.
    </p>

    <div style="background: #EDE5D0; border: 1px solid #D8CCAD; padding: 2rem; text-align: left; margin-bottom: 3rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #D8CCAD;">
            <span style="font-size: 0.72rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: rgba(30,58,42,0.5);">Order number</span>
            <span style="font-family: 'Cormorant Garamond', serif; font-size: 1.2rem; font-weight: 700; color: #1E3A2A;">{{ $order->number }}</span>
        </div>

        @foreach($order->items as $item)
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.6rem 0; border-bottom: 1px solid rgba(216,204,173,0.5);">
            <div>
                <p style="font-size: 0.88rem; color: #1E3A2A; font-weight: 500;">{{ $item->name }}</p>
                <p style="font-size: 0.75rem; color: rgba(30,58,42,0.5);">Qty: {{ $item->quantity }}</p>
            </div>
            <span style="font-family: 'Cormorant Garamond', serif; font-size: 1rem; font-weight: 600; color: #1E3A2A;">Nu. {{ number_format($item->total) }}</span>
        </div>
        @endforeach

        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; margin-top: 0.5rem; border-top: 2px solid #D8CCAD;">
            <span style="font-size: 0.9rem; font-weight: 600; color: #1E3A2A;">Total paid</span>
            <span style="font-family: 'Cormorant Garamond', serif; font-size: 1.5rem; font-weight: 700; color: #1E3A2A;">Nu. {{ number_format($order->price_amount / 100) }}</span>
        </div>

        {{-- Payment proof / reference --}}
        <div style="margin-top: 1rem; padding: 1rem 1.25rem; background: rgba(30,58,42,0.04); border-left: 3px solid #C4843C;">
            <p style="font-size: 0.72rem; font-weight: 600; letter-spacing: 0.12em; text-transform: uppercase; color: rgba(30,58,42,0.5); margin-bottom: 0.5rem;">Payment confirmation</p>
            @if($order->payment_proof_path)
            <p style="font-size: 0.85rem; color: #1E3A2A; display: flex; align-items: center; gap: 0.5rem;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#1E3A2A" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Screenshot uploaded — we'll review and confirm shortly.
            </p>
            @elseif($order->payment_reference)
            <p style="font-size: 0.85rem; color: #1E3A2A;">
                Reference: <strong style="font-family: monospace; letter-spacing: 0.05em;">{{ $order->payment_reference }}</strong>
            </p>
            @else
            <p style="font-size: 0.85rem; color: #b91c1c;">No payment proof provided — please contact us if you've already transferred.</p>
            @endif
        </div>

        {{-- Status note --}}
        <div style="margin-top: 0.75rem; padding: 0.875rem 1.25rem; background: #F7F2E8; border: 1px solid #D8CCAD;">
            <p style="font-size: 0.8rem; color: rgba(30,58,42,0.7); line-height: 1.6;">
                <strong>Payment status:</strong> Pending verification &nbsp;&bull;&nbsp;
                Questions? Call <a href="tel:+97502123456" style="color: #C4843C; text-decoration: none;">+975 02 123 456</a> or email <a href="mailto:hello@othbar.bt" style="color: #C4843C; text-decoration: none;">hello@othbar.bt</a>
            </p>
        </div>
    </div>

    <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
        <a href="{{ route('shop') }}" class="btn-primary" style="text-decoration: none;">
            Continue shopping
        </a>
        <a href="{{ route('home') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.78rem; font-weight: 600; letter-spacing: 0.12em; text-transform: uppercase; color: #1E3A2A; text-decoration: none; padding: 0.875rem 1.75rem; border: 1px solid #1E3A2A;">
            Back to home
        </a>
    </div>

</div>

@endsection
