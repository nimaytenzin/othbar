@extends('storefront.layout')

@section('title', 'Our Story — ' . $site->company_name)

@section('content')

{{-- Hero --}}
<section class="hero-gradient druk-pattern sf-hero" style="padding: 0;">
    <div style="position: absolute; right: 0; top: 0; bottom: 0; width: 40%; background: rgba(212,168,67,0.05);"></div>
    <div class="sf-container sf-hero__inner" style="position: relative;">
        <p class="section-label" style="color: #D4A843; margin-bottom: 1rem;">{{ $site->story_hero_label }}</p>
        <h1 style="font-family: 'Cormorant Garamond', serif; font-size: clamp(3rem, 6vw, 5rem); color: #F7F2E8; font-weight: 600; max-width: 720px; line-height: 1.05; margin-bottom: 2rem;">
            {{ $site->story_hero_title }}
        </h1>
        <p style="font-size: 1rem; color: rgba(247,242,232,0.7); line-height: 2; max-width: 520px; font-weight: 300;">
            {{ $site->story_hero_intro }}
        </p>
    </div>
</section>

{{-- Origin story --}}
<section class="sf-container sf-container--section">
    <div class="sf-grid-2col">
        <div>
            <p class="section-label">{{ $site->story_origin_label }}</p>
            <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 2.5rem; color: #1E3A2A; margin-top: 0.5rem; margin-bottom: 2rem;">{{ $site->story_origin_title }}</h2>
            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                @foreach($site->story_origin_paragraphs ?? [] as $paragraph)
                <p style="font-size: 0.95rem; line-height: 2; color: rgba(30,58,42,0.7);">
                    {{ is_array($paragraph) ? ($paragraph['body'] ?? '') : $paragraph }}
                </p>
                @endforeach
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
                        <p style="font-family: 'Cormorant Garamond', serif; font-size: 2rem; color: rgba(247,242,232,0.9); font-style: italic; margin-top: 1.5rem;">{{ $site->story_origin_media_title }}</p>
                        <p style="font-size: 0.68rem; letter-spacing: 0.2em; color: rgba(247,242,232,0.5); text-transform: uppercase; margin-top: 0.5rem;">{{ $site->story_origin_media_subtitle }}</p>
                    </div>
                </div>
            </div>
            <div style="position: absolute; top: -1.5rem; left: -1.5rem; width: 80px; height: 80px; border: 3px solid #D4A843;"></div>
        </div>
    </div>
</section>

{{-- Values --}}
<section class="sf-band" style="background: #1E3A2A;">
    <div class="sf-container">
        <div style="text-align: center; margin-bottom: 4rem;">
            <p class="section-label" style="color: #D4A843;">{{ $site->story_principles_label }}</p>
            <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 2.5rem; color: #F7F2E8; margin-top: 0.5rem;">{{ $site->story_principles_title }}</h2>
        </div>
        <div class="sf-grid-3">
            @foreach($site->principles ?? [] as $value)
            <div>
                <span style="font-family: 'Cormorant Garamond', serif; font-size: 3.5rem; font-weight: 700; color: #C4843C; opacity: 0.4; line-height: 1; display: block; margin-bottom: 1rem;">{{ $value['number'] ?? '' }}</span>
                <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 1.5rem; color: #F7F2E8; margin-bottom: 1rem;">{{ $value['title'] ?? '' }}</h3>
                <p style="font-size: 0.88rem; line-height: 2; color: rgba(247,242,232,0.6);">{{ $value['body'] ?? '' }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Team / Farmers --}}
<section class="sf-container sf-container--section">
    <div style="margin-bottom: 3rem;">
        <p class="section-label">{{ $site->story_team_label }}</p>
        <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 2.5rem; color: #1E3A2A; margin-top: 0.5rem;">{{ $site->story_team_title }}</h2>
    </div>
    <div class="sf-grid-4">
        @foreach($site->team_members ?? [] as $farmer)
        <div>
            <div style="aspect-ratio: 3/4; background: linear-gradient(135deg, #2D5440, #1E3A2A); margin-bottom: 1.25rem; display: flex; align-items: flex-end; padding: 1.25rem;">
                <div>
                    <p style="font-family: 'Cormorant Garamond', serif; font-size: 1.25rem; color: rgba(247,242,232,0.9); font-weight: 600; margin-bottom: 0.25rem;">{{ $farmer['name'] ?? '' }}</p>
                    <p style="font-size: 0.7rem; color: #C4843C; letter-spacing: 0.1em; text-transform: uppercase;">{{ $farmer['valley'] ?? '' }}</p>
                </div>
            </div>
            <p style="font-size: 0.82rem; color: rgba(30,58,42,0.6);">{{ $farmer['role'] ?? '' }}</p>
        </div>
        @endforeach
    </div>
</section>

{{-- CTA --}}
<section style="background: #EDE5D0; padding: 5rem 0; border-top: 1px solid #D8CCAD;">
    <div class="sf-container" style="max-width: 600px; text-align: center;">
        <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 2.5rem; color: #1E3A2A; margin-bottom: 1rem;">{{ $site->story_cta_title }}</h2>
        <p style="font-size: 0.95rem; color: rgba(30,58,42,0.65); line-height: 1.9; margin-bottom: 2.5rem;">
            {{ $site->story_cta_body }}
        </p>
        <a href="{{ route('shop') }}" class="btn-primary" style="text-decoration: none;">
            Explore our harvest
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
        </a>
    </div>
</section>

@endsection
