@extends('storefront.layout')

@section('title', 'Journal — ' . $site->company_name)

@section('content')

<div class="sf-page-header" style="background: #EDE5D0; border-bottom: 1px solid #D8CCAD;">
    <div class="sf-container">
        <p class="section-label">Stories from the valley</p>
        <h1 style="font-family: 'Cormorant Garamond', serif; font-size: clamp(2.5rem, 5vw, 4rem); color: #1E3A2A; margin-top: 0.5rem;">Journal</h1>
        <p style="font-size: 0.95rem; color: rgba(30,58,42,0.65); line-height: 1.8; max-width: 560px; margin-top: 1rem;">
            Articles on farming, harvest seasons, and life in the Bhutanese highlands.
        </p>
    </div>
</div>

<div class="sf-container sf-page-body">
    @if($posts->isEmpty())
    <div style="text-align: center; padding: 4rem 0;">
        <p style="font-family: 'Cormorant Garamond', serif; font-size: 1.5rem; color: rgba(30,58,42,0.4);">New stories coming soon.</p>
    </div>
    @else
    <div class="sf-grid-3">
        @foreach($posts as $post)
        <article class="product-card">
            <a href="{{ route('journal.show', $post->slug) }}" style="text-decoration: none; display: block;">
                @if($post->featuredImageUrl())
                <div style="aspect-ratio: 4/5; overflow: hidden; margin-bottom: 1rem; background: #DAE0BF;">
                    <img src="{{ $post->featuredImageUrl() }}" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                @else
                <div style="aspect-ratio: 4/5; background: linear-gradient(135deg, #2D5440, #1E3A2A); margin-bottom: 1rem;"></div>
                @endif
                <p style="font-size: 0.68rem; color: #C4843C; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; margin-bottom: 0.35rem;">
                    {{ $post->published_at?->format('M j, Y') }}
                    @if($post->author_name) &nbsp;&bull;&nbsp; {{ $post->author_name }} @endif
                </p>
                <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 1.35rem; color: #1E3A2A; font-weight: 600; margin-bottom: 0.5rem; line-height: 1.25;">{{ $post->title }}</h2>
                @if($post->excerpt)
                <p style="font-size: 0.88rem; line-height: 1.7; color: rgba(30,58,42,0.6);">{{ $post->excerpt }}</p>
                @endif
            </a>
        </article>
        @endforeach
    </div>

    <div style="margin-top: 3rem;">{{ $posts->links() }}</div>
    @endif
</div>

@endsection
