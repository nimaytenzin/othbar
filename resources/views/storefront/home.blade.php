@extends('storefront.layout')

@section('title', 'Othbar Horticulture')

@section('content')

{{-- Hero Section --}}
<section class="hero-gradient druk-pattern sf-hero">

    {{-- Decorative botanical SVG --}}
    <div class="sf-hide-mobile" style="position: absolute; right: -4rem; top: -4rem; opacity: 0.08; pointer-events: none;">
        <svg width="600" height="600" viewBox="0 0 600 600" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="300" cy="300" r="280" stroke="#F7F2E8" stroke-width="0.5"/>
            <circle cx="300" cy="300" r="240" stroke="#F7F2E8" stroke-width="0.5"/>
            <circle cx="300" cy="300" r="180" stroke="#F7F2E8" stroke-width="0.5"/>
            <line x1="20" y1="300" x2="580" y2="300" stroke="#F7F2E8" stroke-width="0.5"/>
            <line x1="300" y1="20" x2="300" y2="580" stroke="#F7F2E8" stroke-width="0.5"/>
            <line x1="97" y1="97" x2="503" y2="503" stroke="#F7F2E8" stroke-width="0.5"/>
            <line x1="503" y1="97" x2="97" y2="503" stroke="#F7F2E8" stroke-width="0.5"/>
            <path d="M300 60 L340 120 L300 100 L260 120 Z" fill="#F7F2E8" opacity="0.4"/>
            <path d="M300 540 L340 480 L300 500 L260 480 Z" fill="#F7F2E8" opacity="0.4"/>
            <path d="M60 300 L120 260 L100 300 L120 340 Z" fill="#F7F2E8" opacity="0.4"/>
            <path d="M540 300 L480 260 L500 300 L480 340 Z" fill="#F7F2E8" opacity="0.4"/>
        </svg>
    </div>

    {{-- Decorative leaf motif left --}}
    <div class="sf-hide-mobile" style="position: absolute; left: 1rem; bottom: 2rem; opacity: 0.12; pointer-events: none;">
        <svg width="200" height="300" viewBox="0 0 200 300" fill="none">
            <path d="M100 280 C100 280 20 200 20 120 C20 50 100 20 100 20 C100 20 180 50 180 120 C180 200 100 280 100 280Z" stroke="#F7F2E8" stroke-width="1" fill="rgba(247,242,232,0.1)"/>
            <path d="M100 280 L100 20" stroke="#F7F2E8" stroke-width="0.8"/>
            <path d="M100 100 Q60 120 40 160" stroke="#F7F2E8" stroke-width="0.6"/>
            <path d="M100 140 Q150 155 160 190" stroke="#F7F2E8" stroke-width="0.6"/>
            <path d="M100 180 Q65 190 55 220" stroke="#F7F2E8" stroke-width="0.6"/>
        </svg>
    </div>

    <div class="sf-container sf-hero__inner">
        <div style="max-width: 640px;">

            <div class="animate-fade-up" style="margin-bottom: 2rem;">
                <span class="badge-organic" style="background: rgba(212,168,67,0.15); border-color: rgba(212,168,67,0.3); color: #D4A843;">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 100 20A10 10 0 0012 2zm0 4a6 6 0 110 12A6 6 0 0112 6z" opacity="0.5"/><path d="M12 8a4 4 0 100 8 4 4 0 000-8z"/></svg>
                    {{ $site->hero_badge }}
                </span>
            </div>

            <h1 class="animate-fade-up animate-fade-up-delay-1" style="font-family: 'Cormorant Garamond', serif; font-size: clamp(3rem, 6vw, 5.5rem); font-weight: 600; color: #F7F2E8; line-height: 1.0; margin-bottom: 1.5rem;">
                {{ $site->hero_line1 }}<br>
                <em style="color: #D4A843; font-style: italic;">{{ $site->hero_emphasis }}</em><br>
                {{ $site->hero_line2 }}
            </h1>

            <p class="animate-fade-up animate-fade-up-delay-2" style="font-size: 1.05rem; color: rgba(247,242,232,0.75); line-height: 1.9; margin-bottom: 2.5rem; max-width: 480px; font-weight: 300;">
                {{ $site->hero_description }}
            </p>

            <div class="animate-fade-up animate-fade-up-delay-3" style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <a href="{{ route('shop') }}" class="btn-primary" style="background: #C4843C; border-color: #C4843C; color: white;">
                    {{ $site->hero_cta_primary }}
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
                </a>
                <a href="{{ route('story') }}" class="btn-outline" style="border-color: rgba(247,242,232,0.4); color: #F7F2E8;">
                    {{ $site->hero_cta_secondary }}
                </a>
            </div>

        </div>
    </div>

    {{-- Scroll indicator --}}
    <div style="position: absolute; bottom: 2rem; left: 50%; transform: translateX(-50%); display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
        <span style="font-size: 0.65rem; letter-spacing: 0.2em; color: rgba(247,242,232,0.4); text-transform: uppercase;">Scroll</span>
        <div style="width: 1px; height: 40px; background: linear-gradient(to bottom, rgba(247,242,232,0.4), transparent); animation: pulse 2s infinite;"></div>
    </div>
</section>

{{-- Provenance strip --}}
<section style="background: #EDE5D0; border-top: 1px solid #D8CCAD; border-bottom: 1px solid #D8CCAD; padding: 2rem 0; overflow: hidden;">
    <div class="sf-container">
        <div class="sf-provenance-strip">
            @foreach($site->provenance_items ?? [] as $item)
            <div style="display: flex; align-items: center; gap: 0.75rem; flex-shrink: 0;">
                <span style="font-size: 1.25rem;">{{ $item['icon'] }}</span>
                <span style="font-size: 0.78rem; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; color: #1E3A2A;">{{ $item['text'] }}</span>
            </div>
            @if (!$loop->last)
            <div style="width: 1px; height: 24px; background: #D8CCAD; flex-shrink: 0;"></div>
            @endif
            @endforeach
        </div>
    </div>
</section>

{{-- Categories Grid --}}
<section class="sf-container sf-container--section">
    <div style="margin-bottom: 3rem;">
        <p class="section-label animate-fade-up">{{ $site->home_categories_label }}</p>
        <h2 class="animate-fade-up animate-fade-up-delay-1" style="font-family: 'Cormorant Garamond', serif; font-size: clamp(2rem, 4vw, 3.2rem); color: #1E3A2A; margin-top: 0.5rem;">
            {{ $site->home_categories_title }}
        </h2>
    </div>

    @if($categories->isEmpty())
    {{-- Static category display if no DB data yet --}}
    <div class="sf-grid-3">
        @foreach([
            ['name' => 'Heritage Grains', 'desc' => 'Red rice, buckwheat, millet', 'color' => '#8B6914', 'bg' => '#F0E4C8'],
            ['name' => 'Fresh Vegetables', 'desc' => 'Seasonal highland produce', 'color' => '#1E3A2A', 'bg' => '#D4E6D8'],
            ['name' => 'Wild Honey', 'desc' => 'Forest-gathered cliff honey', 'color' => '#7A3F0E', 'bg' => '#F5E0C0'],
            ['name' => 'Himalayan Herbs', 'desc' => 'Teas, seasonings & remedies', 'color' => '#2C4D6E', 'bg' => '#C8D8E8'],
            ['name' => 'Preserved Foods', 'desc' => 'Fermented & sun-dried goods', 'color' => '#5C3D1E', 'bg' => '#E8D8C0'],
            ['name' => 'Chili & Spices', 'desc' => 'Ema datshi ingredients', 'color' => '#8B1A1A', 'bg' => '#F0C8C8'],
        ] as $i => $cat)
        <a href="{{ route('shop') }}?category={{ strtolower(str_replace(' ', '-', $cat['name'])) }}"
           class="animate-fade-up animate-fade-up-delay-{{ $loop->index + 1 }}"
           style="text-decoration: none; display: block; padding: 2rem; background: {{ $cat['bg'] }}; border: 1px solid rgba(0,0,0,0.06); transition: all 0.3s ease; cursor: pointer;"
           onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 40px rgba(30,58,42,0.12)';"
           onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
            <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 1.5rem; color: {{ $cat['color'] }}; margin-bottom: 0.5rem;">{{ $cat['name'] }}</h3>
            <p style="font-size: 0.82rem; color: rgba(0,0,0,0.5); font-weight: 300;">{{ $cat['desc'] }}</p>
            <div style="margin-top: 1.5rem; display: flex; align-items: center; gap: 0.5rem; font-size: 0.72rem; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; color: {{ $cat['color'] }};">
                Browse
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
            </div>
        </a>
        @endforeach
    </div>
    @else
    <div class="sf-grid-auto">
        @foreach($categories as $category)
        <a href="{{ route('shop') }}?category={{ $category->slug }}"
           style="text-decoration: none; display: block; padding: 2rem; background: #EDE5D0; border: 1px solid #D8CCAD; transition: all 0.3s ease;"
           onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 40px rgba(30,58,42,0.12)';"
           onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
            <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 1.5rem; color: #1E3A2A; margin-bottom: 0.5rem;">{{ $category->name }}</h3>
            <p style="font-size: 0.82rem; color: rgba(30,58,42,0.5);">{{ $category->products_count }} products</p>
        </a>
        @endforeach
    </div>
    @endif
</section>

{{-- Featured Products --}}
<section class="sf-band" style="background: #EDE5D0;">
    <div class="sf-container">

        <div class="sf-flex-between" style="margin-bottom: 3rem;">
            <div>
                <p class="section-label">{{ $site->home_featured_label }}</p>
                <h2 style="font-family: 'Cormorant Garamond', serif; font-size: clamp(2rem, 4vw, 3.2rem); color: #1E3A2A; margin-top: 0.5rem;">
                    {{ $site->home_featured_title }}
                </h2>
            </div>
            <a href="{{ route('shop') }}" style="font-size: 0.78rem; font-weight: 600; letter-spacing: 0.12em; text-transform: uppercase; color: #C4843C; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; border-bottom: 1px solid #C4843C; padding-bottom: 2px;">
                View all
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
            </a>
        </div>

        @if($featuredProducts->isEmpty())
        {{-- Static product grid for demo --}}
        <div class="sf-grid-products">
            @foreach([
                ['name' => 'Bhutanese Red Rice', 'origin' => 'Paro Valley', 'price' => 'Nu. 280', 'weight' => '1 kg', 'tag' => 'Heritage Grain'],
                ['name' => 'Wild Forest Honey', 'origin' => 'Trongsa District', 'price' => 'Nu. 650', 'weight' => '500 ml', 'tag' => 'Wild-Harvested'],
                ['name' => 'Highland Buckwheat', 'origin' => 'Bumthang Valley', 'price' => 'Nu. 180', 'weight' => '1 kg', 'tag' => 'Ancient Grain'],
                ['name' => 'Dried Ema Datshi Chili', 'origin' => 'Othbar Farm', 'price' => 'Nu. 220', 'weight' => '200 g', 'tag' => 'Sun-Dried'],
                ['name' => 'Himalayan Nettle Tea', 'origin' => 'Haa Valley', 'price' => 'Nu. 340', 'weight' => '100 g', 'tag' => 'Medicinal'],
                ['name' => 'Organic Buckwheat Noodles', 'origin' => 'Bumthang', 'price' => 'Nu. 195', 'weight' => '400 g', 'tag' => 'Handmade'],
            ] as $product)
            <div class="product-card animate-fade-up animate-fade-up-delay-{{ $loop->index + 1 }}">
                {{-- Product image placeholder --}}
                <div style="position: relative; overflow: hidden; aspect-ratio: 4/5; background: #DAE0BF; margin-bottom: 1.25rem;">
                    <div style="position: absolute; top: 1rem; left: 1rem;">
                        <span style="background: #1E3A2A; color: #F7F2E8; font-size: 0.62rem; font-weight: 600; letter-spacing: 0.12em; text-transform: uppercase; padding: 0.3rem 0.7rem;">{{ $product['tag'] }}</span>
                    </div>
                    <button class="product-add-btn" style="position: absolute; bottom: 1rem; left: 1rem; right: 1rem; padding: 0.75rem; background: #1E3A2A; color: #F7F2E8; border: none; font-size: 0.72rem; font-weight: 600; letter-spacing: 0.12em; text-transform: uppercase; cursor: pointer; width: calc(100% - 2rem);">
                        Add to basket
                    </button>
                </div>
                <div>
                    <p style="font-size: 0.68rem; color: #C4843C; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; margin-bottom: 0.35rem;">{{ $product['origin'] }}</p>
                    <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 1.2rem; color: #1E3A2A; margin-bottom: 0.5rem; font-weight: 600;">{{ $product['name'] }}</h3>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span class="price-tag">{{ $product['price'] }}</span>
                        <span style="font-size: 0.75rem; color: rgba(30,58,42,0.5);">{{ $product['weight'] }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="sf-grid-products">
            @foreach($featuredProducts as $product)
            <div class="product-card animate-fade-up animate-fade-up-delay-{{ min($loop->index + 1, 6) }}">
                <a href="{{ route('product', $product->slug) }}" style="text-decoration: none; display: block;">
                    <div class="product-image-frame product-image-frame--4x5" style="margin-bottom: 1.25rem;">
                        <x-product-image :product="$product" />
                    </div>
                    <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 1.2rem; color: #1E3A2A; margin-bottom: 0.5rem;">{{ $product->name }}</h3>
                    @if($product->prices->first())
                    <span class="price-tag">Nu. {{ number_format($product->prices->first()->amount / 100) }}</span>
                    @endif
                </a>
            </div>
            @endforeach
        </div>
        @endif

    </div>
</section>

{{-- Story/Mission Section --}}
<section id="farms" class="sf-container sf-container--section">
    <div class="sf-grid-2col">

        <div>
            <p class="section-label animate-fade-up">{{ $site->home_story_label }}</p>
            <h2 class="animate-fade-up animate-fade-up-delay-1" style="font-family: 'Cormorant Garamond', serif; font-size: clamp(2rem, 3.5vw, 3rem); color: #1E3A2A; margin-top: 0.5rem; margin-bottom: 1.5rem;">
                {{ $site->home_story_title }}
            </h2>
            <p class="animate-fade-up animate-fade-up-delay-2" style="font-size: 0.95rem; line-height: 2; color: rgba(30,58,42,0.7); margin-bottom: 1.5rem;">
                {{ $site->home_story_paragraph_1 }}
            </p>
            <p class="animate-fade-up animate-fade-up-delay-3" style="font-size: 0.95rem; line-height: 2; color: rgba(30,58,42,0.7); margin-bottom: 2rem;">
                {{ $site->home_story_paragraph_2 }}
            </p>
            <a href="{{ route('story') }}" class="btn-primary animate-fade-up animate-fade-up-delay-4" style="text-decoration: none;">
                Read our full story
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
            </a>
        </div>

        <div class="sf-offset-accent">
            {{-- Large image placeholder with artistic treatment --}}
            <div style="aspect-ratio: 4/5; background: linear-gradient(135deg, #2D5440 0%, #1E3A2A 100%); position: relative; overflow: hidden;">
                <div class="druk-pattern" style="position: absolute; inset: 0; opacity: 0.3;"></div>
                <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 1rem; padding: 3rem;">
                    <svg width="120" height="160" viewBox="0 0 120 160" fill="none" opacity="0.6">
                        <path d="M60 150 C60 150 10 110 10 70 C10 30 60 10 60 10 C60 10 110 30 110 70 C110 110 60 150 60 150Z" stroke="#F7F2E8" stroke-width="1" fill="rgba(247,242,232,0.05)"/>
                        <path d="M60 150 L60 10" stroke="#F7F2E8" stroke-width="0.8"/>
                        <path d="M60 50 Q30 65 25 90" stroke="#F7F2E8" stroke-width="0.6"/>
                        <path d="M60 80 Q90 90 95 115" stroke="#F7F2E8" stroke-width="0.6"/>
                        <path d="M60 110 Q35 118 28 135" stroke="#F7F2E8" stroke-width="0.6"/>
                    </svg>
                    <div style="text-align: center;">
                        <p style="font-family: 'Cormorant Garamond', serif; font-size: 1.8rem; color: rgba(247,242,232,0.9); font-style: italic;">{{ $site->home_story_media_title }}</p>
                        <p style="font-size: 0.7rem; letter-spacing: 0.2em; color: rgba(247,242,232,0.4); text-transform: uppercase; margin-top: 0.5rem;">{{ $site->home_story_media_subtitle }}</p>
                    </div>
                </div>
            </div>
            {{-- Offset accent box --}}
            <div class="sf-offset-accent__box" style="background: #C4843C; padding: 1.5rem 2rem; z-index: 10;">
                <p style="font-family: 'Cormorant Garamond', serif; font-size: 2.5rem; font-weight: 700; color: white; line-height: 1;">{{ $site->home_story_stat_value }}</p>
                <p style="font-size: 0.72rem; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; color: rgba(255,255,255,0.8); margin-top: 0.25rem;">{!! nl2br(e($site->home_story_stat_label)) !!}</p>
            </div>
        </div>

    </div>
</section>

{{-- Stats row --}}
<section class="sf-band" style="background: #1E3A2A;">
    <div class="sf-container">
        <div class="sf-stats-grid">
            @foreach($site->stats ?? [] as $stat)
            <div>
                <p class="sf-stat-num">{{ $stat['value'] ?? '' }}</p>
                <p style="font-size: 0.85rem; font-weight: 600; color: #F7F2E8; margin-top: 0.5rem; letter-spacing: 0.05em;">{{ $stat['unit'] ?? '' }}</p>
                <p style="font-size: 0.72rem; color: rgba(247,242,232,0.4); margin-top: 0.25rem; text-transform: uppercase; letter-spacing: 0.1em;">{{ $stat['description'] ?? '' }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Testimonials --}}
<section class="sf-container sf-container--section">
    <div style="margin-bottom: 3rem; text-align: center;">
        <p class="section-label">{{ $site->home_testimonials_label }}</p>
        <h2 style="font-family: 'Cormorant Garamond', serif; font-size: clamp(2rem, 4vw, 3.2rem); color: #1E3A2A; margin-top: 0.5rem;">
            {{ $site->home_testimonials_title }}
        </h2>
    </div>

    <div class="sf-grid-3">
        @foreach($site->testimonials ?? [] as $testimonial)
        <div class="testimonial-card">
            <div style="margin-bottom: 1rem;">
                @for ($i = 0; $i < ($testimonial['rating'] ?? 5); $i++)
                <span style="color: #D4A843; font-size: 0.85rem;">&#9733;</span>
                @endfor
            </div>
            <p style="font-family: 'Cormorant Garamond', serif; font-size: 1.1rem; line-height: 1.8; color: #1E3A2A; font-style: italic; margin-bottom: 1.25rem;">"{{ $testimonial['quote'] ?? '' }}"</p>
            <div>
                <p style="font-size: 0.85rem; font-weight: 600; color: #1E3A2A;">{{ $testimonial['name'] }}</p>
                <p style="font-size: 0.75rem; color: #C4843C; letter-spacing: 0.08em;">{{ $testimonial['location'] }}</p>
            </div>
        </div>
        @endforeach
    </div>
</section>

{{-- Newsletter --}}
<section class="sf-band" style="background: linear-gradient(135deg, #2D5440, #1E3A2A); position: relative; overflow: hidden;">
    <div class="druk-pattern" style="position: absolute; inset: 0; opacity: 0.4;"></div>
    <div class="sf-container" style="max-width: 580px; text-align: center; position: relative;">
        <p class="section-label" style="color: #D4A843;">{{ $site->newsletter_label }}</p>
        <h2 style="font-family: 'Cormorant Garamond', serif; font-size: clamp(1.75rem, 5vw, 2.5rem); color: #F7F2E8; margin-top: 0.5rem; margin-bottom: 1rem;">
            {{ $site->newsletter_title }}
        </h2>
        <p style="font-size: 0.9rem; color: rgba(247,242,232,0.65); line-height: 1.8; margin-bottom: 2.5rem;">
            {{ $site->newsletter_description }}
        </p>
        <form class="sf-newsletter-form">
            <input type="email" placeholder="your@email.com" style="flex: 1; padding: 0.875rem 1.25rem; background: rgba(247,242,232,0.1); border: 1px solid rgba(247,242,232,0.2); border-right: none; color: #F7F2E8; font-family: 'Jost', sans-serif; font-size: 0.9rem; outline: none;" />
            <button type="submit" style="padding: 0.875rem 1.5rem; background: #C4843C; border: 1px solid #C4843C; color: white; font-family: 'Jost', sans-serif; font-size: 0.78rem; font-weight: 600; letter-spacing: 0.12em; text-transform: uppercase; cursor: pointer; white-space: nowrap;">
                Subscribe
            </button>
        </form>
    </div>
</section>

@endsection
