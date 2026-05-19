@extends('storefront.layout')

@section('title', (isset($collection) ? $collection->name : 'Collection') . ' — Othbar')

@section('content')

<div class="sf-page-header" style="background: #1E3A2A; padding: 3rem 0; position: relative; overflow: hidden;">
    <div class="druk-pattern" style="position: absolute; inset: 0; opacity: 0.3;"></div>
    <div class="sf-container" style="position: relative;">
        <p class="section-label" style="color: #D4A843; margin-bottom: 0.75rem;">Curated collection</p>
        <h1 style="font-family: 'Cormorant Garamond', serif; font-size: clamp(2.5rem, 5vw, 4.5rem); color: #F7F2E8; font-weight: 600;">
            {{ isset($collection) ? $collection->name : 'Collection' }}
        </h1>
        @isset($collection)
        @if($collection->description)
        <p style="font-size: 0.95rem; color: rgba(247,242,232,0.65); line-height: 1.9; max-width: 560px; margin-top: 1rem;">{{ $collection->description }}</p>
        @endif
        @endisset
    </div>
</div>

<div class="sf-container sf-page-body">
    @if($products->isEmpty())
    <div style="text-align: center; padding: 6rem 0;">
        <p style="font-family: 'Cormorant Garamond', serif; font-size: 2rem; color: rgba(30,58,42,0.3);">Products coming soon</p>
        <a href="{{ route('shop') }}" class="btn-primary" style="text-decoration: none; margin-top: 2rem; display: inline-flex;">Browse all products</a>
    </div>
    @else
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 2rem;">
        @foreach($products as $product)
        <div class="product-card animate-fade-up animate-fade-up-delay-{{ min($loop->index + 1, 6) }}">
            <a href="{{ route('product', $product->slug) }}" style="text-decoration: none; display: block;">
                <div class="product-image-frame" style="margin-bottom: 1rem;">
                    <x-product-image :product="$product" />
                    <button class="product-add-btn" style="position: absolute; bottom: 0.75rem; left: 0.75rem; right: 0.75rem; padding: 0.625rem; background: #1E3A2A; color: #F7F2E8; border: none; font-size: 0.68rem; font-weight: 600; letter-spacing: 0.12em; text-transform: uppercase; cursor: pointer;">
                        Add to basket
                    </button>
                </div>
                <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 1.1rem; color: #1E3A2A; font-weight: 600; margin-bottom: 0.4rem;">{{ $product->name }}</h3>
                @if($product->prices->first())
                <span style="font-family: 'Cormorant Garamond', serif; font-size: 1.25rem; font-weight: 600; color: #1E3A2A;">Nu. {{ number_format($product->prices->first()->amount / 100) }}</span>
                @endif
            </a>
        </div>
        @endforeach
    </div>
    <div style="margin-top: 3rem;">{{ $products->links() }}</div>
    @endif
</div>

@endsection
