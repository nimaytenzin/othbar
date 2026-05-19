@extends('storefront.layout')

@section('title', $post->title . ' — Journal — ' . $site->company_name)

@section('content')

<div style="background: #EDE5D0; border-bottom: 1px solid #D8CCAD; padding: 0.875rem 0;">
    <div class="sf-container">
        <nav class="sf-breadcrumb" style="font-size: 0.75rem; color: rgba(30,58,42,0.5);">
            <a href="{{ route('home') }}" style="color: rgba(30,58,42,0.5); text-decoration: none;">Home</a>
            <span>/</span>
            <a href="{{ route('journal') }}" style="color: rgba(30,58,42,0.5); text-decoration: none;">Journal</a>
            <span>/</span>
            <span style="color: #1E3A2A;">{{ $post->title }}</span>
        </nav>
    </div>
</div>

<article>
    @if($post->featuredImageUrl())
    <div style="max-height: 70vh; overflow: hidden; background: #1E3A2A;">
        <img src="{{ $post->featuredImageUrl() }}" alt="" style="width: 100%; height: 100%; max-height: 70vh; object-fit: cover;">
    </div>
    @endif

    <div class="sf-container sf-page-body" style="max-width: 760px;">
        <p style="font-size: 0.68rem; color: #C4843C; font-weight: 600; letter-spacing: 0.12em; text-transform: uppercase; margin-bottom: 0.75rem;">
            {{ $post->published_at?->format('F j, Y') }}
            @if($post->author_name) &nbsp;&bull;&nbsp; {{ $post->author_name }} @endif
        </p>
        <h1 style="font-family: 'Cormorant Garamond', serif; font-size: clamp(2rem, 5vw, 3rem); color: #1E3A2A; line-height: 1.1; margin-bottom: 1.5rem;">{{ $post->title }}</h1>
        @if($post->excerpt)
        <p style="font-size: 1.05rem; line-height: 1.9; color: rgba(30,58,42,0.65); margin-bottom: 2rem; font-weight: 300;">{{ $post->excerpt }}</p>
        @endif
        <div class="journal-body" style="font-size: 0.95rem; line-height: 2; color: rgba(30,58,42,0.75);">
            {!! $post->body !!}
        </div>
    </div>
</article>

@if($recentPosts->isNotEmpty())
<section class="sf-band" style="background: #EDE5D0;">
    <div class="sf-container">
        <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 1.75rem; color: #1E3A2A; margin-bottom: 2rem;">More from the journal</h2>
        <div class="sf-grid-3">
            @foreach($recentPosts as $recent)
            <a href="{{ route('journal.show', $recent->slug) }}" style="text-decoration: none;">
                <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 1.15rem; color: #1E3A2A; margin-bottom: 0.35rem;">{{ $recent->title }}</h3>
                <p style="font-size: 0.75rem; color: #C4843C;">{{ $recent->published_at?->format('M j, Y') }}</p>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection
