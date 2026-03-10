@extends('storefront.layout')

@section('title', isset($product) ? $product->name . ' — Othbar' : 'Product — Othbar')

@section('content')

{{-- Breadcrumb --}}
<div style="background: #EDE5D0; border-bottom: 1px solid #D8CCAD; padding: 0.875rem 0;">
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 2rem;">
        <nav style="display: flex; gap: 0.5rem; align-items: center; font-size: 0.75rem; color: rgba(30,58,42,0.5);">
            <a href="{{ route('home') }}" style="color: rgba(30,58,42,0.5); text-decoration: none;">Home</a>
            <span>/</span>
            <a href="{{ route('shop') }}" style="color: rgba(30,58,42,0.5); text-decoration: none;">Shop</a>
            <span>/</span>
            <span style="color: #1E3A2A;">{{ isset($product) ? $product->name : 'Bhutanese Red Rice' }}</span>
        </nav>
    </div>
</div>

{{-- Product section --}}
<div style="max-width: 1280px; margin: 0 auto; padding: 4rem 2rem;">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 6rem; align-items: start;">

        {{-- Product images --}}
        <div>
            <div style="aspect-ratio: 1; background: #D8CCAD; position: relative; overflow: hidden; margin-bottom: 1rem;">
                @isset($product)
                    @if($product->getFirstMediaUrl('thumbnail'))
                    <img src="{{ $product->getFirstMediaUrl('thumbnail') }}" alt="{{ $product->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @elseif($product->getFirstMediaUrl('uploads'))
                    <img src="{{ $product->getFirstMediaUrl('uploads') }}" alt="{{ $product->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                    <div class="img-placeholder" style="width: 100%; height: 100%;">
                        <svg width="80" height="110" viewBox="0 0 80 110" fill="none" opacity="0.2">
                            <path d="M40 105 C40 105 5 80 5 50 C5 20 40 5 40 5 C40 5 75 20 75 50 C75 80 40 105 40 105Z" stroke="#1E3A2A" stroke-width="1.5" fill="none"/>
                            <path d="M40 105 L40 5" stroke="#1E3A2A" stroke-width="1"/>
                            <path d="M40 30 Q20 42 16 62" stroke="#1E3A2A" stroke-width="0.8"/>
                            <path d="M40 55 Q62 63 66 82" stroke="#1E3A2A" stroke-width="0.8"/>
                        </svg>
                    </div>
                    @endif
                @else
                <div class="img-placeholder" style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 1rem;">
                    <svg width="80" height="110" viewBox="0 0 80 110" fill="none" opacity="0.3">
                        <path d="M40 105 C40 105 5 80 5 50 C5 20 40 5 40 5 C40 5 75 20 75 50 C75 80 40 105 40 105Z" stroke="#1E3A2A" stroke-width="1.5" fill="none"/>
                        <path d="M40 105 L40 5" stroke="#1E3A2A" stroke-width="1"/>
                        <path d="M40 30 Q20 42 16 62" stroke="#1E3A2A" stroke-width="0.8"/>
                        <path d="M40 55 Q62 63 66 82" stroke="#1E3A2A" stroke-width="0.8"/>
                    </svg>
                    <p style="font-family: 'Cormorant Garamond', serif; font-size: 1.2rem; color: rgba(30,58,42,0.4); font-style: italic;">Bhutanese Red Rice</p>
                </div>
                @endisset
                {{-- Organic badge overlay --}}
                <div style="position: absolute; top: 1.25rem; right: 1.25rem; background: #1E3A2A; padding: 0.75rem; display: flex; flex-direction: column; align-items: center; gap: 0.25rem;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#D4A843" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    <span style="font-size: 0.55rem; letter-spacing: 0.15em; text-transform: uppercase; color: #D4A843; font-weight: 600;">Organic</span>
                </div>
            </div>

            {{-- Thumbnail row --}}
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem;">
                @for($i = 0; $i < 4; $i++)
                <div style="aspect-ratio: 1; background: #D8CCAD; cursor: pointer; border: 2px solid {{ $i === 0 ? '#1E3A2A' : 'transparent' }}; transition: border-color 0.2s;" onmouseover="this.style.borderColor='#C4843C'" onmouseout="this.style.borderColor='{{ $i === 0 ? '#1E3A2A' : 'transparent' }}'">
                    <div class="img-placeholder" style="width: 100%; height: 100%;"></div>
                </div>
                @endfor
            </div>
        </div>

        {{-- Product info --}}
        <div style="padding-top: 1rem;">

            @isset($product)
            @foreach($product->categories as $cat)
            <p class="section-label" style="margin-bottom: 0.5rem;">{{ $cat->name }}</p>
            @endforeach
            @else
            <p class="section-label" style="margin-bottom: 0.5rem;">Heritage Grain</p>
            @endisset

            <h1 style="font-family: 'Cormorant Garamond', serif; font-size: clamp(2rem, 4vw, 3rem); color: #1E3A2A; margin-bottom: 0.5rem; line-height: 1.1;">
                {{ isset($product) ? $product->name : 'Bhutanese Red Rice' }}
            </h1>

            <p style="font-size: 0.78rem; color: rgba(30,58,42,0.5); margin-bottom: 1.75rem; letter-spacing: 0.05em;">
                Origin: <span style="color: #C4843C; font-weight: 500;">{{ isset($product) ? ($product->brand->name ?? 'Paro Valley, Bhutan') : 'Paro Valley, Bhutan' }}</span>
            </p>

            <div class="gold-line" style="margin-bottom: 1.75rem;"></div>

            {{-- Price --}}
            <div style="margin-bottom: 2rem;">
                @isset($product)
                    @if($product->prices->first())
                    <span style="font-family: 'Cormorant Garamond', serif; font-size: 2.25rem; font-weight: 600; color: #1E3A2A;">Nu. {{ number_format($product->prices->first()->amount / 100) }}</span>
                    @endif
                @else
                <span style="font-family: 'Cormorant Garamond', serif; font-size: 2.25rem; font-weight: 600; color: #1E3A2A;">Nu. 280</span>
                @endisset
                <span style="font-size: 0.78rem; color: rgba(30,58,42,0.45); margin-left: 0.5rem;">/ 1 kg</span>
            </div>

            {{-- Short description --}}
            <p style="font-size: 0.92rem; line-height: 2; color: rgba(30,58,42,0.7); margin-bottom: 2rem;">
                {{ isset($product) ? ($product->excerpt ?? 'A nutritious, nutty-flavoured rice with a beautiful deep red colour, cultivated in the traditional terraced fields of Paro Valley for over a thousand years.') : 'A nutritious, nutty-flavoured rice with a beautiful deep red colour, cultivated in the traditional terraced fields of Paro Valley for over a thousand years. Rich in antioxidants, fibre, and minerals unique to Bhutan\'s high-altitude soil.' }}
            </p>

            {{-- Flash messages --}}
            @if(session('error'))
            <div style="background: #F8D7DA; border: 1px solid #F5C6CB; color: #721C24; padding: 0.875rem 1.25rem; margin-bottom: 1.5rem; font-size: 0.875rem;">
                {{ session('error') }}
            </div>
            @endif
            @if(session('success'))
            <div style="background: #D4EDDA; border: 1px solid #C3E6CB; color: #155724; padding: 0.875rem 1.25rem; margin-bottom: 1.5rem; font-size: 0.875rem;">
                {{ session('success') }}
            </div>
            @endif

            {{-- Quantity + Add to cart --}}
            <div style="margin-bottom: 2rem;">
                @isset($product)
                @php
                    $inStock = $product->allow_backorder || $product->stock > 0;
                    $lowStock = !$product->allow_backorder && $product->stock > 0 && $product->stock <= 5;
                @endphp

                {{-- Stock badge --}}
                @if(!$inStock)
                <div style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.4rem 0.875rem; background: #F8D7DA; border: 1px solid #F5C6CB; margin-bottom: 1rem;">
                    <span style="width: 7px; height: 7px; border-radius: 50%; background: #b91c1c; display: inline-block;"></span>
                    <span style="font-size: 0.75rem; font-weight: 600; color: #b91c1c; letter-spacing: 0.05em;">Out of stock</span>
                </div>
                @elseif($lowStock)
                <div style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.4rem 0.875rem; background: #FFF3CD; border: 1px solid #FFEEBA; margin-bottom: 1rem;">
                    <span style="width: 7px; height: 7px; border-radius: 50%; background: #C4843C; display: inline-block;"></span>
                    <span style="font-size: 0.75rem; font-weight: 600; color: #856404; letter-spacing: 0.05em;">Only {{ $product->stock }} left</span>
                </div>
                @endif

                <form action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <p style="font-size: 0.72rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: #1E3A2A; margin-bottom: 0.875rem;">Quantity</p>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <div style="display: flex; align-items: center; border: 1px solid #D8CCAD; {{ !$inStock ? 'opacity:0.5;' : '' }}">
                            <button type="button" {{ !$inStock ? 'disabled' : '' }} style="padding: 0.75rem 1rem; background: none; border: none; cursor: pointer; font-size: 1.1rem; color: #1E3A2A;" onclick="const q=document.getElementById('qty');q.value=Math.max(1,parseInt(q.value)-1)">−</button>
                            <input id="qty" name="quantity" type="number" value="1" min="1" {{ !$product->allow_backorder && $product->stock > 0 ? 'max="'.$product->stock.'"' : '' }} style="width: 50px; text-align: center; border: none; background: none; font-family: 'Cormorant Garamond', serif; font-size: 1.1rem; color: #1E3A2A; outline: none;" {{ !$inStock ? 'disabled' : '' }}>
                            <button type="button" {{ !$inStock ? 'disabled' : '' }} style="padding: 0.75rem 1rem; background: none; border: none; cursor: pointer; font-size: 1.1rem; color: #1E3A2A;" onclick="const q=document.getElementById('qty');q.value=parseInt(q.value)+1">+</button>
                        </div>
                        @if($inStock)
                        <button type="submit" class="btn-primary" style="flex: 1; text-decoration: none; justify-content: center; border: none; cursor: pointer;">
                            Add to basket
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
                        </button>
                        @else
                        <button type="button" disabled style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.875rem 2rem; background: rgba(30,58,42,0.15); border: none; font-family: 'Jost', sans-serif; font-size: 0.78rem; font-weight: 600; letter-spacing: 0.12em; text-transform: uppercase; color: rgba(30,58,42,0.4); cursor: not-allowed;">
                            Out of stock
                        </button>
                        @endif
                    </div>
                </form>
                @else
                <p style="font-size: 0.72rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: #1E3A2A; margin-bottom: 0.875rem;">Quantity</p>
                <div style="display: flex; gap: 1rem; align-items: center;">
                    <div style="display: flex; align-items: center; border: 1px solid #D8CCAD;">
                        <button style="padding: 0.75rem 1rem; background: none; border: none; cursor: pointer; font-size: 1.1rem; color: #1E3A2A;" onclick="const q=document.getElementById('qty');q.value=Math.max(1,parseInt(q.value)-1)">−</button>
                        <input id="qty" type="number" value="1" min="1" style="width: 50px; text-align: center; border: none; background: none; font-family: 'Cormorant Garamond', serif; font-size: 1.1rem; color: #1E3A2A; outline: none;">
                        <button style="padding: 0.75rem 1rem; background: none; border: none; cursor: pointer; font-size: 1.1rem; color: #1E3A2A;" onclick="const q=document.getElementById('qty');q.value=parseInt(q.value)+1">+</button>
                    </div>
                    <button class="btn-primary" style="flex: 1; text-decoration: none; justify-content: center;">
                        Add to basket
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
                    </button>
                </div>
                @endisset
            </div>

            <div class="gold-line" style="margin-bottom: 2rem;"></div>

            {{-- Product attributes --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 2rem;">
                @foreach([
                    ['label' => 'Certification', 'value' => 'Bhutan Organic #BT-2019'],
                    ['label' => 'Farm', 'value' => 'Othbar Community Farm'],
                    ['label' => 'Altitude', 'value' => '2,400 – 2,800m'],
                    ['label' => 'Harvest', 'value' => 'October – November'],
                    ['label' => 'Processing', 'value' => 'Sun-dried, stone-milled'],
                    ['label' => 'Storage', 'value' => 'Cool, dry place — 12 months'],
                ] as $attr)
                <div style="padding: 1rem; background: #EDE5D0; border-left: 2px solid #C4843C;">
                    <p style="font-size: 0.65rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: rgba(30,58,42,0.5); margin-bottom: 0.25rem;">{{ $attr['label'] }}</p>
                    <p style="font-size: 0.85rem; color: #1E3A2A; font-weight: 400;">{{ $attr['value'] }}</p>
                </div>
                @endforeach
            </div>

            {{-- Trust signals --}}
            <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
                @foreach([
                    ['icon' => '🌿', 'text' => 'Zero pesticides'],
                    ['icon' => '🏔', 'text' => 'High-altitude grown'],
                    ['icon' => '📦', 'text' => 'Eco packaging'],
                ] as $signal)
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="font-size: 1rem;">{{ $signal['icon'] }}</span>
                    <span style="font-size: 0.78rem; color: rgba(30,58,42,0.6); font-weight: 300;">{{ $signal['text'] }}</span>
                </div>
                @endforeach
            </div>

        </div>
    </div>
</div>

{{-- Full description --}}
<div style="background: #EDE5D0; padding: 5rem 0;">
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 2rem;">
        <div style="max-width: 700px; margin: 0 auto;">
            <p class="section-label" style="text-align: center; margin-bottom: 0.5rem;">The full story</p>
            <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 2.2rem; color: #1E3A2A; text-align: center; margin-bottom: 2.5rem;">
                About this product
            </h2>
            <div style="font-size: 0.95rem; line-height: 2.2; color: rgba(30,58,42,0.75);">
                @isset($product)
                {!! $product->description !!}
                @else
                <p>Bhutanese Red Rice (<em>Oryza sativa</em>) is a medium-grain rice with a distinctive reddish-brown colour and a rich, nutty flavour. It has been cultivated in the Paro valley for over a millennium and is a staple of the traditional Bhutanese diet.</p>
                <p style="margin-top: 1.25rem;">Unlike polished white rice, red rice retains its bran layer, giving it a substantial nutritional profile rich in manganese, magnesium, phosphorus, and B vitamins. The high-altitude clay soils and pure glacial water of Paro Valley impart a subtle minerality that cannot be replicated elsewhere.</p>
                <p style="margin-top: 1.25rem;">Our farming community practices traditional transplant cultivation, planting each seedling by hand in April and harvesting in October. The grain is then threshed, sun-dried on bamboo mats, and stone-milled to order — preserving freshness and nutritional integrity.</p>
                @endisset
            </div>
        </div>
    </div>
</div>

{{-- Related products --}}
@if(isset($related) && $related->isNotEmpty() || !isset($related))
<div style="max-width: 1280px; margin: 0 auto; padding: 5rem 2rem;">
    <div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: center;">
        <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 2rem; color: #1E3A2A;">You may also like</h2>
        <a href="{{ route('shop') }}" style="font-size: 0.78rem; font-weight: 600; letter-spacing: 0.12em; text-transform: uppercase; color: #C4843C; text-decoration: none; border-bottom: 1px solid #C4843C; padding-bottom: 2px;">View all</a>
    </div>
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 2rem;">
        @isset($related)
            @foreach($related->take(4) as $rel)
            <div class="product-card">
                <a href="{{ route('product', $rel->slug) }}" style="text-decoration: none; display: block;">
                    <div style="aspect-ratio: 1; background: #D8CCAD; margin-bottom: 1rem; overflow: hidden;">
                        @if($rel->getFirstMedia())
                        <img src="{{ $rel->getFirstMedia()->getUrl() }}" alt="{{ $rel->name }}" class="product-img" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                        <div class="img-placeholder" style="width: 100%; height: 100%;"></div>
                        @endif
                    </div>
                    <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 1.05rem; color: #1E3A2A; margin-bottom: 0.35rem;">{{ $rel->name }}</h3>
                    @if($rel->prices->first())
                    <span style="font-family: 'Cormorant Garamond', serif; font-size: 1.15rem; font-weight: 600; color: #1E3A2A;">Nu. {{ number_format($rel->prices->first()->amount / 100) }}</span>
                    @endif
                </a>
            </div>
            @endforeach
        @else
            @foreach([
                ['name' => 'Wild Forest Honey', 'price' => 'Nu. 650', 'slug' => 'wild-forest-honey'],
                ['name' => 'Highland Buckwheat', 'price' => 'Nu. 180', 'slug' => 'highland-buckwheat'],
                ['name' => 'Himalayan Nettle Tea', 'price' => 'Nu. 340', 'slug' => 'nettle-tea'],
                ['name' => 'Dried Chili Peppers', 'price' => 'Nu. 220', 'slug' => 'dried-chili'],
            ] as $rel)
            <div class="product-card">
                <a href="{{ route('product', $rel['slug']) }}" style="text-decoration: none; display: block;">
                    <div style="aspect-ratio: 1; background: #D8CCAD; margin-bottom: 1rem; overflow: hidden;">
                        <div class="img-placeholder" style="width: 100%; height: 100%;"></div>
                    </div>
                    <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 1.05rem; color: #1E3A2A; margin-bottom: 0.35rem;">{{ $rel['name'] }}</h3>
                    <span style="font-family: 'Cormorant Garamond', serif; font-size: 1.15rem; font-weight: 600; color: #1E3A2A;">{{ $rel['price'] }}</span>
                </a>
            </div>
            @endforeach
        @endisset
    </div>
</div>
@endif

@endsection
