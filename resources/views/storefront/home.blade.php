@extends('storefront.layout')

@section('title', 'Othbar Horticulture')

@section('content')

{{-- Hero --}}
<section class="hero-gradient sf-hero sf-hero--home">
    <div class="sf-container sf-hero__inner">
        <div style="max-width: 40rem;">
            <div class="animate-fade-up" style="margin-bottom: 1.75rem;">
                <span class="badge-organic sf-hero-badge">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 100 20A10 10 0 0012 2zm0 4a6 6 0 110 12A6 6 0 0112 6z" opacity="0.5"/><path d="M12 8a4 4 0 100 8 4 4 0 000-8z"/></svg>
                    {{ $site->hero_badge }}
                </span>
            </div>

            <h1 class="sf-hero-title animate-fade-up animate-fade-up-delay-1">
                {{ $site->hero_line1 }}<br>
                <em>{{ $site->hero_emphasis }}</em><br>
                {{ $site->hero_line2 }}
            </h1>

            <p class="sf-hero-lead animate-fade-up animate-fade-up-delay-2">
                {{ $site->hero_description }}
            </p>

            <div class="sf-hero-actions animate-fade-up animate-fade-up-delay-3">
                <a href="{{ route('shop') }}" class="btn-primary sf-btn-hero-primary">
                    {{ $site->hero_cta_primary }}
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
                </a>
                <a href="{{ route('story') }}" class="btn-outline sf-btn-hero-outline">
                    {{ $site->hero_cta_secondary }}
                </a>
            </div>
        </div>
    </div>

</section>

@if(filled($site->provenance_items))
<section class="sf-provenance" aria-label="Provenance highlights">
    <div class="sf-container">
        <ul class="sf-provenance-grid">
            @foreach($site->provenance_items as $item)
            <li class="sf-provenance-item">
                <span class="sf-provenance-item__icon" aria-hidden="true">{{ $item['icon'] ?? '' }}</span>
                <span class="sf-provenance-item__text">{{ $item['text'] ?? '' }}</span>
            </li>
            @endforeach
        </ul>
    </div>
</section>
@endif

{{-- Categories --}}
<section class="sf-container sf-container--section">
    <header class="sf-section-header">
        <p class="section-label animate-fade-up">{{ $site->home_categories_label }}</p>
        <h2 class="sf-heading-lg animate-fade-up animate-fade-up-delay-1">{{ $site->home_categories_title }}</h2>
    </header>

    @if($categories->isEmpty())
    <div class="sf-empty-state">
        <p class="sf-empty-state__title">No categories yet</p>
        <p style="font-size: 0.9rem; color: rgba(30,58,42,0.6); margin-bottom: 1.5rem;">Browse our full shop while categories are being set up.</p>
        <a href="{{ route('shop') }}" class="btn-primary" style="display: inline-flex; text-decoration: none;">Browse shop</a>
    </div>
    @else
    <div class="sf-grid-auto">
        @foreach($categories as $category)
        <a href="{{ route('shop') }}?category={{ $category->slug }}" class="sf-category-card">
            <h3 class="sf-category-card__title">{{ $category->name }}</h3>
            <p class="sf-category-card__meta">{{ $category->products_count }} {{ $category->products_count === 1 ? 'product' : 'products' }}</p>
            <span class="sf-category-card__link">
                Browse
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
            </span>
        </a>
        @endforeach
    </div>
    @endif
</section>

{{-- Featured products --}}
<section class="sf-band" style="background: var(--bg-secondary);">
    <div class="sf-container">
        <div class="sf-flex-between sf-section-header" style="margin-bottom: 2.5rem;">
            <header>
                <p class="section-label">{{ $site->home_featured_label }}</p>
                <h2 class="sf-heading-lg">{{ $site->home_featured_title }}</h2>
            </header>
            <a href="{{ route('shop') }}" class="sf-link-arrow">
                View all
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
            </a>
        </div>

        @if($featuredProducts->isEmpty())
        <div class="sf-empty-state">
            <p class="sf-empty-state__title">No featured products yet</p>
            <p style="font-size: 0.9rem; color: rgba(30,58,42,0.6); margin-bottom: 1.5rem;">Check back soon or browse everything in the shop.</p>
            <a href="{{ route('shop') }}" class="btn-primary" style="display: inline-flex; text-decoration: none;">Browse shop</a>
        </div>
        @else
        <div class="sf-grid-products">
            @foreach($featuredProducts as $product)
            @php
                $price = $product->prices->first();
                $category = $product->categories->first();
            @endphp
            <article class="product-card animate-fade-up animate-fade-up-delay-{{ min($loop->index + 1, 6) }}">
                <a href="{{ route('product', $product->slug) }}" style="text-decoration: none; display: block;">
                    <div class="product-image-frame product-image-frame--4x5" style="margin-bottom: 1.25rem;">
                        @if($category)
                        <span class="sf-product-tag">{{ $category->name }}</span>
                        @endif
                        <x-product-image :product="$product" />
                    </div>
                    @if($product->brand)
                    <p class="sf-product-card__origin">{{ $product->brand->name }}</p>
                    @endif
                    <h3 class="sf-product-card__title">{{ $product->name }}</h3>
                    @if($price)
                    <div class="sf-product-card__footer">
                        <span class="price-tag" style="font-size: 1.25rem;">Nu. {{ number_format($price->amount / 100) }}</span>
                    </div>
                    @endif
                </a>
            </article>
            @endforeach
        </div>
        @endif
    </div>
</section>

{{-- Story --}}
<section id="farms" class="sf-container sf-container--section">
    <div class="sf-grid-2col">
        <div>
            <p class="section-label animate-fade-up">{{ $site->home_story_label }}</p>
            <h2 class="sf-heading-lg animate-fade-up animate-fade-up-delay-1" style="margin-bottom: 1.5rem;">{{ $site->home_story_title }}</h2>
            <p class="sf-story-copy animate-fade-up animate-fade-up-delay-2">{{ $site->home_story_paragraph_1 }}</p>
            <p class="sf-story-copy animate-fade-up animate-fade-up-delay-3" style="margin-bottom: 2rem;">{{ $site->home_story_paragraph_2 }}</p>
            <a href="{{ route('story') }}" class="btn-primary animate-fade-up animate-fade-up-delay-4" style="text-decoration: none;">
                Read our full story
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
            </a>
        </div>

        <div class="sf-offset-accent">
            <div class="sf-story-media">
                <div class="sf-story-media__inner">
                    <svg width="100" height="130" viewBox="0 0 120 160" fill="none" opacity="0.55" aria-hidden="true">
                        <path d="M60 150 C60 150 10 110 10 70 C10 30 60 10 60 10 C60 10 110 30 110 70 C110 110 60 150 60 150Z" stroke="#F7F2E8" stroke-width="1" fill="rgba(247,242,232,0.05)"/>
                        <path d="M60 150 L60 10" stroke="#F7F2E8" stroke-width="0.8"/>
                        <path d="M60 50 Q30 65 25 90" stroke="#F7F2E8" stroke-width="0.6"/>
                        <path d="M60 80 Q90 90 95 115" stroke="#F7F2E8" stroke-width="0.6"/>
                    </svg>
                    <div>
                        <p class="sf-story-media__title">{{ $site->home_story_media_title }}</p>
                        <p class="sf-story-media__subtitle">{{ $site->home_story_media_subtitle }}</p>
                    </div>
                </div>
            </div>
            <div class="sf-offset-accent__box" style="background: #C4843C; padding: 1.5rem 2rem; z-index: 10;">
                <p style="font-family: 'Cormorant Garamond', serif; font-size: 2.5rem; font-weight: 700; color: white; line-height: 1;">{{ $site->home_story_stat_value }}</p>
                <p style="font-size: 0.72rem; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; color: rgba(255,255,255,0.85); margin-top: 0.25rem;">{!! nl2br(e($site->home_story_stat_label)) !!}</p>
            </div>
        </div>
    </div>
</section>

{{-- Stats --}}
@if(filled($site->stats))
<section class="sf-band" style="background: #1E3A2A;">
    <div class="sf-container">
        <div class="sf-stats-grid">
            @foreach($site->stats as $stat)
            <div class="sf-stat-item">
                <p class="sf-stat-num">{{ $stat['value'] ?? '' }}</p>
                <p class="sf-stat-label">{{ $stat['unit'] ?? '' }}</p>
                <p class="sf-stat-desc">{{ $stat['description'] ?? '' }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Testimonials --}}
@if(filled($site->testimonials))
<section class="sf-container sf-container--section">
    <header class="sf-section-header sf-section-header--center">
        <p class="section-label">{{ $site->home_testimonials_label }}</p>
        <h2 class="sf-heading-lg">{{ $site->home_testimonials_title }}</h2>
    </header>

    <div class="sf-grid-3">
        @foreach($site->testimonials as $testimonial)
        <blockquote class="sf-testimonial-card">
            <div style="margin-bottom: 1rem;" aria-label="{{ $testimonial['rating'] ?? 5 }} out of 5 stars">
                @for ($i = 0; $i < ($testimonial['rating'] ?? 5); $i++)
                <span style="color: #D4A843; font-size: 0.85rem;" aria-hidden="true">&#9733;</span>
                @endfor
            </div>
            <p class="sf-testimonial-card__quote">&ldquo;{{ $testimonial['quote'] ?? '' }}&rdquo;</p>
            <footer>
                <p class="sf-testimonial-card__name">{{ $testimonial['name'] }}</p>
                <p class="sf-testimonial-card__location">{{ $testimonial['location'] }}</p>
            </footer>
        </blockquote>
        @endforeach
    </div>
</section>
@endif

@endsection
