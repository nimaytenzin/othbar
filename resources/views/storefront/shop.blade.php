@extends('storefront.layout')

@section('title', (optional($activeCategory)->name ?? 'Shop').' — Othbar Organic')

@section('content')

@php
    $shopQuery = array_filter([
        'search' => request('search'),
        'sort' => request('sort', 'newest') !== 'newest' ? request('sort') : null,
    ]);
@endphp

{{-- Page header --}}
<div class="sf-page-header" style="background: #EDE5D0; border-bottom: 1px solid #D8CCAD;">
    <div class="sf-container">
        <p class="section-label">The full harvest</p>
        <h1 style="font-family: 'Cormorant Garamond', serif; font-size: clamp(2.5rem, 5vw, 4rem); color: #1E3A2A; margin-top: 0.5rem;">{{ optional($activeCategory)->name ?? 'All products' }}</h1>
    </div>
</div>

<div class="sf-container sf-page-body" style="padding-top: 3rem; padding-bottom: 3rem;">
    <div class="sf-shop-layout">

        {{-- Sidebar filters --}}
        <aside>
            <div class="sf-shop-sidebar">

                {{-- Search --}}
                <div style="margin-bottom: 2.5rem;">
                    <p style="font-size: 0.7rem; font-weight: 600; letter-spacing: 0.2em; text-transform: uppercase; color: #1E3A2A; margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid #D8CCAD;">Search</p>
                    <form method="GET" action="{{ route('shop') }}">
                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                        @if($shopQuery['sort'] ?? null)
                            <input type="hidden" name="sort" value="{{ $shopQuery['sort'] }}">
                        @endif
                        <div style="display: flex; border: 1px solid #D8CCAD; overflow: hidden;">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Find a product..." style="flex: 1; padding: 0.625rem 0.875rem; background: #F7F2E8; border: none; font-family: 'Jost', sans-serif; font-size: 0.85rem; color: #1E3A2A; outline: none;" />
                            <button type="submit" style="padding: 0.625rem 0.875rem; background: #1E3A2A; border: none; cursor: pointer; color: #F7F2E8;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Categories --}}
                <div>
                    <p style="font-size: 0.7rem; font-weight: 600; letter-spacing: 0.2em; text-transform: uppercase; color: #1E3A2A; margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid #D8CCAD;">Categories</p>
                    <ul style="list-style: none; padding: 0; display: flex; flex-direction: column; gap: 0.125rem;">
                        <li>
                            <a href="{{ route('shop', $shopQuery) }}" style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem 0; font-size: 0.88rem; color: {{ !request('category') ? '#C4843C' : '#1E3A2A' }}; text-decoration: none; font-weight: {{ !request('category') ? '600' : '300' }}; border-bottom: 1px solid rgba(216,204,173,0.5);">
                                <span>All Products</span>
                            </a>
                        </li>
                        @forelse($categories as $category)
                        <li>
                            <a href="{{ route('shop', array_merge($shopQuery, ['category' => $category->slug])) }}" style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem 0; font-size: 0.88rem; color: {{ request('category') === $category->slug ? '#C4843C' : '#1E3A2A' }}; text-decoration: none; font-weight: {{ request('category') === $category->slug ? '600' : '300' }}; border-bottom: 1px solid rgba(216,204,173,0.5);">
                                <span>{{ $category->name }}</span>
                            </a>
                        </li>
                        @empty
                        <li style="padding: 0.5rem 0; font-size: 0.85rem; color: rgba(30,58,42,0.5);">No categories yet</li>
                        @endforelse
                    </ul>
                </div>

                {{-- Organic badge --}}
                <div style="margin-top: 2.5rem; padding: 1.5rem; background: #1E3A2A;">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#D4A843" stroke-width="1.5" style="margin-bottom: 0.75rem;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    <p style="font-family: 'Cormorant Garamond', serif; font-size: 1.1rem; color: #F7F2E8; font-weight: 600; margin-bottom: 0.5rem;">100% Certified Organic</p>
                    <p style="font-size: 0.78rem; color: rgba(247,242,232,0.6); line-height: 1.6;">All products grown without synthetic pesticides or fertilizers. Bhutan organic certification #BT-ORG-2019</p>
                </div>

            </div>
        </aside>

        {{-- Products grid --}}
        <div>

            {{-- Sort/count bar --}}
            <div class="sf-flex-between--center" style="margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 1px solid #D8CCAD;">
                <p style="font-size: 0.82rem; color: rgba(30,58,42,0.5);">
                    @if($products->total() > 0)
                        Showing {{ $products->firstItem() }}–{{ $products->lastItem() }} of {{ $products->total() }} products
                    @else
                        Showing our full catalogue
                    @endif
                </p>
                <form method="GET" action="{{ route('shop') }}">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    <select name="sort" onchange="this.form.submit()" style="font-family: 'Jost', sans-serif; font-size: 0.82rem; color: #1E3A2A; border: 1px solid #D8CCAD; background: #F7F2E8; padding: 0.375rem 0.75rem; outline: none;">
                        <option value="newest" @selected(request('sort', 'newest') === 'newest')>Sort: Newest</option>
                        <option value="price_asc" @selected(request('sort') === 'price_asc')>Price: Low to High</option>
                        <option value="price_desc" @selected(request('sort') === 'price_desc')>Price: High to Low</option>
                        <option value="name" @selected(request('sort') === 'name')>Name: A–Z</option>
                    </select>
                </form>
            </div>

            @if($products->isEmpty())
            @if(request()->filled('category') || request()->filled('search'))
            <div style="padding: 3rem 1rem; text-align: center; border: 1px dashed #D8CCAD; background: rgba(247,242,232,0.5);">
                <p style="font-family: 'Cormorant Garamond', serif; font-size: 1.5rem; color: #1E3A2A; margin-bottom: 0.5rem;">No products found</p>
                <p style="font-size: 0.88rem; color: rgba(30,58,42,0.55); margin-bottom: 1.25rem;">
                    @if(request()->filled('search'))
                        Nothing matched “{{ request('search') }}”.
                    @else
                        This category has no products yet.
                    @endif
                </p>
                <a href="{{ route('shop', $shopQuery) }}" style="font-size: 0.78rem; font-weight: 600; letter-spacing: 0.12em; text-transform: uppercase; color: #C4843C; text-decoration: none; border-bottom: 1px solid #C4843C; padding-bottom: 2px;">View all products</a>
            </div>
            @else
            <div class="sf-empty-state" style="padding: 3rem 1rem; border: 1px dashed var(--border); background: rgba(247,242,232,0.5);">
                <p class="sf-empty-state__title">No products available yet</p>
                <p style="font-size: 0.88rem; color: rgba(30,58,42,0.55); margin-bottom: 1.25rem;">Products will appear here once they are published in the admin.</p>
                <a href="{{ route('home') }}" class="sf-link-arrow">Return home</a>
            </div>
            @endif
            @else
            <div class="sf-grid-products">
                @foreach($products as $product)
                @php
                    $inStock = $product->allow_backorder || $product->stock > 0;
                    $lowStock = !$product->allow_backorder && $product->stock > 0 && $product->stock <= 5;
                @endphp
                <div class="product-card">
                    <div class="product-image-frame" style="margin-bottom: 1rem;">
                        <a href="{{ route('product', $product->slug) }}" style="display: block; height: 100%; text-decoration: none;">
                            <x-product-image :product="$product" />
                        </a>
                        @if(!$inStock)
                            <div style="position: absolute; inset: 0; background: rgba(247,242,232,0.7); display: flex; align-items: center; justify-content: center; pointer-events: none;">
                                <span style="background: #1E3A2A; color: #F7F2E8; font-size: 0.65rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; padding: 0.4rem 0.875rem;">Out of stock</span>
                            </div>
                        @elseif($lowStock)
                            <div style="position: absolute; top: 0.75rem; right: 0.75rem; pointer-events: none;">
                                <span style="background: #C4843C; color: #F7F2E8; font-size: 0.58rem; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; padding: 0.25rem 0.6rem;">Only {{ $product->stock }} left</span>
                            </div>
                        @endif
                        @if($inStock)
                            <a href="{{ route('product', $product->slug) }}" class="product-add-btn" style="position: absolute; bottom: 0.75rem; left: 0.75rem; right: 0.75rem; padding: 0.625rem; background: #1E3A2A; color: #F7F2E8; border: none; font-size: 0.68rem; font-weight: 600; letter-spacing: 0.12em; text-transform: uppercase; cursor: pointer; text-decoration: none; display: block; text-align: center;">
                                Add to basket
                            </a>
                        @endif
                    </div>
                    <a href="{{ route('product', $product->slug) }}" style="text-decoration: none; display: block; color: inherit;">
                        <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 1.1rem; color: #1E3A2A; font-weight: 600; margin-bottom: 0.4rem;">{{ $product->name }}</h3>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            @if($product->prices->first())
                            <span style="font-family: 'Cormorant Garamond', serif; font-size: 1.25rem; font-weight: 600; color: #1E3A2A;">Nu. {{ number_format($product->prices->first()->amount / 100) }}</span>
                            @endif
                            @if(!$inStock)
                            <span style="font-size: 0.7rem; color: #b91c1c; font-weight: 600;">Sold out</span>
                            @elseif($lowStock)
                            <span style="font-size: 0.7rem; color: #856404; font-weight: 600;">Low stock</span>
                            @endif
                        </div>
                    </a>
                </div>
                @endforeach
            </div>

            <div style="margin-top: 3rem;">{{ $products->links() }}</div>
            @endif

        </div>
    </div>
</div>

@endsection
