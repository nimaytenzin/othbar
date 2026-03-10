@extends('storefront.layout')

@section('title', 'Our Story — Othbar Horticulture')

@section('content')

{{-- Hero --}}
<section class="hero-gradient druk-pattern" style="padding: 8rem 0; position: relative; overflow: hidden;">
    <div style="position: absolute; right: 0; top: 0; bottom: 0; width: 40%; background: rgba(212,168,67,0.05);"></div>
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 2rem; position: relative;">
        <p class="section-label" style="color: #D4A843; margin-bottom: 1rem;">Who we are</p>
        <h1 style="font-family: 'Cormorant Garamond', serif; font-size: clamp(3rem, 6vw, 5rem); color: #F7F2E8; font-weight: 600; max-width: 720px; line-height: 1.05; margin-bottom: 2rem;">
            Rooted in the earth of the <em style="color: #D4A843;">Last Shangri-La</em>
        </h1>
        <p style="font-size: 1rem; color: rgba(247,242,232,0.7); line-height: 2; max-width: 520px; font-weight: 300;">
            Founded in 2018 by a collective of 47 farming families in Punakha, Othbar exists to share Bhutan's extraordinary organic heritage with the world — without compromising the land that makes it possible.
        </p>
    </div>
</section>

{{-- Origin story --}}
<section style="max-width: 1280px; margin: 0 auto; padding: 7rem 2rem;">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 7rem; align-items: center;">
        <div>
            <p class="section-label">The beginning</p>
            <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 2.5rem; color: #1E3A2A; margin-top: 0.5rem; margin-bottom: 2rem;">How Othbar came to be</h2>
            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                <p style="font-size: 0.95rem; line-height: 2; color: rgba(30,58,42,0.7);">
                    The name Othbar comes from an ancient Dzongkha word for the high-altitude terraced fields where our founders' grandparents first cultivated red rice. When the youngest generation began returning to these valleys after studying modern agriculture, they brought with them a question: <em>How do we honour what our ancestors knew while building something that can sustain our community's future?</em>
                </p>
                <p style="font-size: 0.95rem; line-height: 2; color: rgba(30,58,42,0.7);">
                    The answer was a cooperative. Forty-seven families pooling their land, knowledge, and labour — certified organic from day one, committed to zero synthetic inputs, and guided by Bhutan's own framework of Gross National Happiness.
                </p>
                <p style="font-size: 0.95rem; line-height: 2; color: rgba(30,58,42,0.7);">
                    Today we cultivate 120 acres across Punakha and Paro, growing 28 varieties of heritage crops. We sell directly to homes across Bhutan and to a small number of international partners who share our values.
                </p>
            </div>
        </div>
        <div style="position: relative;">
            <div style="aspect-ratio: 3/4; background: linear-gradient(135deg, #C4843C 0%, #8B6914 100%); position: relative; overflow: hidden;">
                <div class="druk-pattern" style="position: absolute; inset: 0; opacity: 0.3;"></div>
                <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; padding: 3rem;">
                    <div style="text-align: center;">
                        <svg width="80" height="80" viewBox="0 0 80 80" fill="none" opacity="0.5">
                            <path d="M40 75 C40 75 5 55 5 35 C5 15 40 5 40 5 C40 5 75 15 75 35 C75 55 40 75 40 75Z" stroke="#F7F2E8" stroke-width="1.5" fill="rgba(247,242,232,0.1)"/>
                            <path d="M40 75 L40 5" stroke="#F7F2E8" stroke-width="1"/>
                            <path d="M40 20 Q22 30 18 48" stroke="#F7F2E8" stroke-width="0.8"/>
                            <path d="M40 40 Q58 48 62 62" stroke="#F7F2E8" stroke-width="0.8"/>
                        </svg>
                        <p style="font-family: 'Cormorant Garamond', serif; font-size: 2rem; color: rgba(247,242,232,0.9); font-style: italic; margin-top: 1.5rem;">Punakha Valley</p>
                        <p style="font-size: 0.68rem; letter-spacing: 0.2em; color: rgba(247,242,232,0.5); text-transform: uppercase; margin-top: 0.5rem;">Est. 2018</p>
                    </div>
                </div>
            </div>
            <div style="position: absolute; top: -1.5rem; left: -1.5rem; width: 80px; height: 80px; border: 3px solid #D4A843;"></div>
        </div>
    </div>
</section>

{{-- Values --}}
<section style="background: #1E3A2A; padding: 6rem 0;">
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 2rem;">
        <div style="text-align: center; margin-bottom: 4rem;">
            <p class="section-label" style="color: #D4A843;">What drives us</p>
            <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 2.5rem; color: #F7F2E8; margin-top: 0.5rem;">Our principles</h2>
        </div>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 2.5rem;">
            @foreach([
                [
                    'num' => '01',
                    'title' => 'Earth before profit',
                    'text' => 'Every farming decision is evaluated first by its impact on the soil, water, and biodiversity of the Punakha and Paro valleys. Profitability follows ecological health, never leads it.',
                ],
                [
                    'num' => '02',
                    'title' => 'Ancient knowledge, modern rigour',
                    'text' => 'We combine the intergenerational farming wisdom of our cooperative members with contemporary organic certification standards and sustainable agriculture research.',
                ],
                [
                    'num' => '03',
                    'title' => 'Community ownership',
                    'text' => 'Othbar is collectively owned by all 47 member families. Decisions are made by consensus. Profits are distributed equally. No investor holds a stake in our land.',
                ],
            ] as $value)
            <div>
                <span style="font-family: 'Cormorant Garamond', serif; font-size: 3.5rem; font-weight: 700; color: #C4843C; opacity: 0.4; line-height: 1; display: block; margin-bottom: 1rem;">{{ $value['num'] }}</span>
                <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 1.5rem; color: #F7F2E8; margin-bottom: 1rem;">{{ $value['title'] }}</h3>
                <p style="font-size: 0.88rem; line-height: 2; color: rgba(247,242,232,0.6);">{{ $value['text'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Team / Farmers --}}
<section style="max-width: 1280px; margin: 0 auto; padding: 7rem 2rem;">
    <div style="margin-bottom: 3rem;">
        <p class="section-label">The people</p>
        <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 2.5rem; color: #1E3A2A; margin-top: 0.5rem;">Our farming families</h2>
    </div>
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 2rem;">
        @foreach([
            ['name' => 'Tshering Lhamo', 'role' => 'Lead farmer, red rice', 'valley' => 'Paro Valley'],
            ['name' => 'Karma Wangdi', 'role' => 'Honey cooperative head', 'valley' => 'Trongsa'],
            ['name' => 'Sonam Choki', 'role' => 'Herb cultivation', 'valley' => 'Haa Valley'],
            ['name' => 'Jigme Dorji', 'role' => 'Cooperative director', 'valley' => 'Punakha'],
        ] as $farmer)
        <div>
            <div style="aspect-ratio: 3/4; background: linear-gradient(135deg, #2D5440, #1E3A2A); margin-bottom: 1.25rem; display: flex; align-items: flex-end; padding: 1.25rem;">
                <div>
                    <p style="font-family: 'Cormorant Garamond', serif; font-size: 1.25rem; color: rgba(247,242,232,0.9); font-weight: 600; margin-bottom: 0.25rem;">{{ $farmer['name'] }}</p>
                    <p style="font-size: 0.7rem; color: #C4843C; letter-spacing: 0.1em; text-transform: uppercase;">{{ $farmer['valley'] }}</p>
                </div>
            </div>
            <p style="font-size: 0.82rem; color: rgba(30,58,42,0.6);">{{ $farmer['role'] }}</p>
        </div>
        @endforeach
    </div>
</section>

{{-- CTA --}}
<section style="background: #EDE5D0; padding: 5rem 0; border-top: 1px solid #D8CCAD;">
    <div style="max-width: 600px; margin: 0 auto; padding: 0 2rem; text-align: center;">
        <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 2.5rem; color: #1E3A2A; margin-bottom: 1rem;">Taste the difference</h2>
        <p style="font-size: 0.95rem; color: rgba(30,58,42,0.65); line-height: 1.9; margin-bottom: 2.5rem;">
            Every purchase supports our farming families directly and funds the regeneration of traditional Bhutanese agriculture.
        </p>
        <a href="{{ route('shop') }}" class="btn-primary" style="text-decoration: none;">
            Explore our harvest
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
        </a>
    </div>
</section>

@endsection
