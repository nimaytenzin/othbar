@props([
    'product',
    'alt' => null,
])

@php
    $url = $product->featuredImageUrl();
    $altText = $alt ?? $product->name;
@endphp

@if ($url)
    <img
        src="{{ $url }}"
        alt="{{ $altText }}"
        class="product-img"
        loading="lazy"
        decoding="async"
        {{ $attributes }}
    >
@else
    <div class="img-placeholder" aria-hidden="true" {{ $attributes }}>
        <svg width="48" height="64" viewBox="0 0 80 110" fill="none" opacity="0.25">
            <path d="M40 105 C40 105 5 80 5 50 C5 20 40 5 40 5 C40 5 75 20 75 50 C75 80 40 105 40 105Z" stroke="#1E3A2A" stroke-width="1.5" fill="none"/>
            <path d="M40 105 L40 5" stroke="#1E3A2A" stroke-width="1"/>
        </svg>
    </div>
@endif
