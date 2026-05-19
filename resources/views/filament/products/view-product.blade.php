@php
    /** @var \App\Models\Product $product */
    $product->loadMissing(['media', 'brand', 'categories', 'collections']);
    $gallery = $product->galleryImageUrls();
    $featuredUrl = $product->featuredImageUrl();
    $editUrl = \App\Filament\Resources\Products\ProductResource::getUrl('edit', ['record' => $product]);
    $storefrontUrl = route('product', $product->slug);

    $stockQty = (int) $product->stock_quantity;
    $isLowStock = $stockQty > 0 && $stockQty <= 5;
    $isOutOfStock = $stockQty <= 0 && ! $product->allow_backorder;

    $stockLabel = match (true) {
        $isOutOfStock => 'Out of stock',
        $product->allow_backorder && $stockQty <= 0 => 'Backorder only',
        $isLowStock => 'Low stock',
        default => 'In stock',
    };

    $stockColor = match (true) {
        $isOutOfStock => 'danger',
        $isLowStock, $product->allow_backorder && $stockQty <= 0 => 'warning',
        default => 'success',
    };
@endphp

<div class="oth-product-page">
    <div class="oth-product-main">
        <div class="oth-card oth-card--flush oth-product-gallery-card">
            <div class="oth-product-hero" id="oth-product-hero-{{ $product->id }}">
                @if($featuredUrl)
                    <img
                        src="{{ $featuredUrl }}"
                        alt="{{ $product->name }}"
                        class="oth-product-hero__img"
                        id="oth-product-main-img-{{ $product->id }}"
                    >
                @else
                    <div class="oth-product-hero__placeholder">
                        <svg width="72" height="72" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true">
                            <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p>No product photo yet</p>
                    </div>
                @endif

                @if($product->is_visible)
                    <span class="oth-product-hero__badge oth-product-hero__badge--live">Live on storefront</span>
                @else
                    <span class="oth-product-hero__badge oth-product-hero__badge--hidden">Hidden</span>
                @endif
            </div>

            @if(count($gallery) > 1)
                <div class="oth-product-thumbs">
                    @foreach($gallery as $index => $imageUrl)
                        <button
                            type="button"
                            class="oth-product-thumb {{ $index === 0 ? 'is-active' : '' }}"
                            data-product-thumb
                            data-image-url="{{ $imageUrl }}"
                            aria-label="View image {{ $index + 1 }}"
                        >
                            <img src="{{ $imageUrl }}" alt="">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        @if(filled($product->summary))
            <div class="oth-card">
                <h3 class="oth-card__title">Summary</h3>
                <p class="oth-product-summary">{{ $product->summary }}</p>
            </div>
        @endif

        @if(filled($product->description))
            <div class="oth-card">
                <h3 class="oth-card__title">Description</h3>
                <div class="oth-product-description">
                    {!! $product->description !!}
                </div>
            </div>
        @endif
    </div>

    <div class="oth-product-side">
        <div class="oth-card oth-product-head-card">
            <p class="oth-card__subtitle" style="margin:0;text-transform:uppercase;letter-spacing:0.05em;font-weight:600;">Product</p>
            <h2 class="oth-product-name">{{ $product->name }}</h2>
            <p class="oth-product-meta">
                <code>{{ $product->slug }}</code>
                · Updated {{ $product->updated_at?->format('M j, Y') }}
            </p>

            <div class="oth-badges" style="margin-top:0.75rem;">
                <x-filament::badge :color="$product->is_visible ? 'success' : 'gray'" :icon="$product->is_visible ? 'heroicon-o-eye' : 'heroicon-o-eye-slash'">
                    {{ $product->is_visible ? 'Visible' : 'Hidden' }}
                </x-filament::badge>
                <x-filament::badge :color="$stockColor">
                    {{ $stockLabel }}
                </x-filament::badge>
                @if($product->allow_backorder)
                    <x-filament::badge color="info" icon="heroicon-o-arrow-path">
                        Backorders allowed
                    </x-filament::badge>
                @endif
            </div>

            <p class="oth-product-price">Nu. {{ number_format($product->price_minor / 100, $product->price_minor % 100 === 0 ? 0 : 2) }}</p>
        </div>

        <div class="oth-card">
            <h3 class="oth-card__title">Inventory &amp; pricing</h3>
            <dl class="oth-stat-grid" style="margin-top:1rem;">
                <div class="oth-stat">
                    <dt class="oth-stat__label">Stock</dt>
                    <dd class="oth-stat__value">{{ number_format($stockQty) }}</dd>
                </div>
                <div class="oth-stat">
                    <dt class="oth-stat__label">SKU</dt>
                    <dd class="oth-stat__value" style="font-family:monospace;font-size:0.875rem;">{{ $product->sku ?: '—' }}</dd>
                </div>
                <div class="oth-stat">
                    <dt class="oth-stat__label">Currency</dt>
                    <dd class="oth-stat__value">{{ $product->currency_code }}</dd>
                </div>
            </dl>
        </div>

        <div class="oth-card">
            <h3 class="oth-card__title">Catalog</h3>
            <dl class="oth-dl">
                <div>
                    <dt>Brand</dt>
                    <dd>{{ $product->brand?->name ?: '—' }}</dd>
                </div>
                <div class="oth-dl__full">
                    <dt>Categories</dt>
                    <dd>
                        @if($product->categories->isNotEmpty())
                            <div class="oth-tag-list">
                                @foreach($product->categories as $category)
                                    <span class="oth-tag">{{ $category->name }}</span>
                                @endforeach
                            </div>
                        @else
                            —
                        @endif
                    </dd>
                </div>
                <div class="oth-dl__full">
                    <dt>Collections</dt>
                    <dd>
                        @if($product->collections->isNotEmpty())
                            <div class="oth-tag-list">
                                @foreach($product->collections as $collection)
                                    <span class="oth-tag oth-tag--muted">{{ $collection->name }}</span>
                                @endforeach
                            </div>
                        @else
                            —
                        @endif
                    </dd>
                </div>
            </dl>
        </div>

        <div class="oth-card">
            <h3 class="oth-card__title">Quick actions</h3>
            <div class="oth-actions">
                <a href="{{ $editUrl }}" class="oth-btn oth-btn--primary">Edit product</a>
                <a href="{{ $storefrontUrl }}" target="_blank" rel="noopener" class="oth-btn oth-btn--secondary">
                    View on storefront
                </a>
            </div>
        </div>
    </div>
</div>

@if(count($gallery) > 1)
<script>
    (function () {
        var root = document.getElementById('oth-product-hero-{{ $product->id }}');
        if (!root) return;

        var mainImg = document.getElementById('oth-product-main-img-{{ $product->id }}');
        if (!mainImg) return;

        document.querySelectorAll('[data-product-thumb]').forEach(function (thumb) {
            thumb.addEventListener('click', function () {
                mainImg.src = thumb.dataset.imageUrl;
                document.querySelectorAll('[data-product-thumb]').forEach(function (t) {
                    t.classList.remove('is-active');
                });
                thumb.classList.add('is-active');
            });
        });
    })();
</script>
@endif
