<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Othbar Horticulture Project — Certified organic food grown in the pristine highlands of Bhutan.">
    <title>@yield('title', $site->company_name) — From the Dragon Kingdom's Earth</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="texture-bg min-h-screen">

    {{-- Announcement bar --}}
    <div class="sf-announcement" style="background: #1E3A2A; color: #F7F2E8;">
        <p style="font-weight: 500;" class="uppercase">
            {{ $site->announcement_text }}
        </p>
    </div>

    {{-- Navigation --}}
    <header id="nav" style="background: #F7F2E8; border-bottom: 1px solid #D8CCAD; position: sticky; top: 0; z-index: 50; transition: all 0.3s ease;">
        <div class="sf-container">
            <div class="sf-header-inner">

                <button type="button" class="sf-nav-mobile-toggle" id="nav-toggle" aria-expanded="false" aria-controls="nav-mobile-menu" aria-label="Open menu">
                    <svg id="nav-icon-open" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                    <svg id="nav-icon-close" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="display: none;" aria-hidden="true">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>

                <nav class="sf-nav-desktop" aria-label="Primary">
                    <a href="{{ route('shop') }}" class="nav-link">Shop</a>
                    <a href="{{ route('story') }}" class="nav-link">Our Story</a>
                    <a href="#farms" class="nav-link">Farms</a>
                </nav>

                <a href="{{ route('home') }}" class="sf-logo-wrap" style="text-decoration: none;">
                    <div style="display: flex; flex-direction: column; align-items: center; gap: 1px;">
                        <span style="font-family: 'Cormorant Garamond', serif; font-size: clamp(1.25rem, 4vw, 1.6rem); font-weight: 600; color: #1E3A2A; line-height: 1; letter-spacing: 0.05em;">{{ $site->company_name }}</span>
                        <span style="font-size: 0.5rem; letter-spacing: 0.2em; font-weight: 500; color: #C4843C; text-transform: uppercase;">{{ $site->company_subtitle }}</span>
                    </div>
                </a>

                <div class="sf-header-actions">
                    <nav class="sf-nav-desktop" aria-label="Secondary">
                        <a href="{{ route('journal') }}" class="nav-link">Journal</a>
                        <a href="#contact" class="nav-link">Contact</a>
                        <a href="{{ route('storefront.login') }}" class="nav-link">Login</a>
                    </nav>
                    @php
                        $navCartCount = collect(app(\App\Services\CartSessionService::class)->lineRows())->sum('quantity');
                    @endphp
                    <a href="{{ route('cart') }}" style="position: relative; display: flex; align-items: center; text-decoration: none; color: #1E3A2A;" aria-label="Cart ({{ $navCartCount }} items)">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path d="M6 2 3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/>
                        </svg>
                        @if($navCartCount > 0)
                        <span style="position: absolute; top: -6px; right: -6px; min-width: 16px; height: 16px; background: #C4843C; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.6rem; color: white; font-weight: 600; padding: 0 2px;">{{ $navCartCount }}</span>
                        @endif
                    </a>
                </div>

            </div>
        </div>

        <nav id="nav-mobile-menu" class="sf-nav-mobile-menu" aria-label="Mobile">
            <a href="{{ route('shop') }}">Shop</a>
            <a href="{{ route('story') }}">Our Story</a>
            <a href="#farms">Farms</a>
            <a href="{{ route('journal') }}">Journal</a>
            <a href="#contact">Contact</a>
            <a href="{{ route('storefront.login') }}">Login</a>
        </nav>

        <div class="footer-weave" style="opacity: 0.7;"></div>
    </header>

    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="sf-footer" style="background: #1E3A2A; color: #EDE5D0;">
        <div class="footer-weave"></div>
        <div class="sf-container sf-footer-inner">
            <div class="sf-footer-grid">

                <div>
                    <div style="margin-bottom: 1.5rem;">
                        <span style="font-family: 'Cormorant Garamond', serif; font-size: clamp(1.5rem, 4vw, 2rem); font-weight: 600; letter-spacing: 0.05em;">{{ $site->company_name }}</span>
                        <p style="font-size: 0.55rem; letter-spacing: 0.25em; color: #C4843C; text-transform: uppercase; margin-top: 2px;">{{ $site->company_subtitle }}</p>
                    </div>
                    <p style="font-size: 0.9rem; line-height: 1.8; color: rgba(237,229,208,0.75); max-width: 280px;">
                        {{ $site->footer_about }}
                    </p>
                    <div style="margin-top: 1.5rem; display: flex; gap: 0.75rem; flex-wrap: wrap;">
                        <span class="badge-organic" style="background: rgba(237,229,208,0.1); border-color: rgba(237,229,208,0.2); color: #EDE5D0;">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            Certified Organic
                        </span>
                    </div>
                </div>

                <div>
                    <h4 style="font-family: 'Jost', sans-serif; font-size: 0.7rem; font-weight: 600; letter-spacing: 0.2em; text-transform: uppercase; color: #C4843C; margin-bottom: 1.25rem;">Shop</h4>
                    <ul style="list-style: none; padding: 0; display: flex; flex-direction: column; gap: 0.75rem;">
                        <li><a href="{{ route('shop') }}" style="font-size: 0.88rem; color: rgba(237,229,208,0.75); text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='#EDE5D0'" onmouseout="this.style.color='rgba(237,229,208,0.75)'">All Products</a></li>
                        <li><a href="{{ route('shop') }}?category=vegetables" style="font-size: 0.88rem; color: rgba(237,229,208,0.75); text-decoration: none;" onmouseover="this.style.color='#EDE5D0'" onmouseout="this.style.color='rgba(237,229,208,0.75)'">Fresh Vegetables</a></li>
                        <li><a href="{{ route('shop') }}?category=grains" style="font-size: 0.88rem; color: rgba(237,229,208,0.75); text-decoration: none;" onmouseover="this.style.color='#EDE5D0'" onmouseout="this.style.color='rgba(237,229,208,0.75)'">Heritage Grains</a></li>
                        <li><a href="{{ route('shop') }}?category=honey" style="font-size: 0.88rem; color: rgba(237,229,208,0.75); text-decoration: none;" onmouseover="this.style.color='#EDE5D0'" onmouseout="this.style.color='rgba(237,229,208,0.75)'">Wild Honey</a></li>
                        <li><a href="{{ route('shop') }}?category=herbs" style="font-size: 0.88rem; color: rgba(237,229,208,0.75); text-decoration: none;" onmouseover="this.style.color='#EDE5D0'" onmouseout="this.style.color='rgba(237,229,208,0.75)'">Himalayan Herbs</a></li>
                    </ul>
                </div>

                <div>
                    <h4 style="font-family: 'Jost', sans-serif; font-size: 0.7rem; font-weight: 600; letter-spacing: 0.2em; text-transform: uppercase; color: #C4843C; margin-bottom: 1.25rem;">Company</h4>
                    <ul style="list-style: none; padding: 0; display: flex; flex-direction: column; gap: 0.75rem;">
                        <li><a href="{{ route('story') }}" style="font-size: 0.88rem; color: rgba(237,229,208,0.75); text-decoration: none;" onmouseover="this.style.color='#EDE5D0'" onmouseout="this.style.color='rgba(237,229,208,0.75)'">Our Story</a></li>
                        <li><a href="#farms" style="font-size: 0.88rem; color: rgba(237,229,208,0.75); text-decoration: none;" onmouseover="this.style.color='#EDE5D0'" onmouseout="this.style.color='rgba(237,229,208,0.75)'">Our Farms</a></li>
                        <li><a href="{{ route('journal') }}" style="font-size: 0.88rem; color: rgba(237,229,208,0.75); text-decoration: none;" onmouseover="this.style.color='#EDE5D0'" onmouseout="this.style.color='rgba(237,229,208,0.75)'">Journal</a></li>
                        <li><a href="#contact" style="font-size: 0.88rem; color: rgba(237,229,208,0.75); text-decoration: none;" onmouseover="this.style.color='#EDE5D0'" onmouseout="this.style.color='rgba(237,229,208,0.75)'">Contact</a></li>
                    </ul>
                </div>

                <div id="contact">
                    <h4 style="font-family: 'Jost', sans-serif; font-size: 0.7rem; font-weight: 600; letter-spacing: 0.2em; text-transform: uppercase; color: #C4843C; margin-bottom: 1.25rem;">Find Us</h4>
                    <address style="font-style: normal; display: flex; flex-direction: column; gap: 0.75rem;">
                        <p style="font-size: 0.88rem; color: rgba(237,229,208,0.75); line-height: 1.6;">{!! nl2br(e($site->contact_address)) !!}</p>
                        @if($site->contact_phone)
                        <a href="tel:{{ preg_replace('/\s+/', '', $site->contact_phone) }}" style="font-size: 0.88rem; color: rgba(237,229,208,0.75); text-decoration: none;" onmouseover="this.style.color='#EDE5D0'" onmouseout="this.style.color='rgba(237,229,208,0.75)'">{{ $site->contact_phone }}</a>
                        @endif
                        @if($site->contact_email)
                        <a href="mailto:{{ $site->contact_email }}" style="font-size: 0.88rem; color: rgba(237,229,208,0.75); text-decoration: none;" onmouseover="this.style.color='#EDE5D0'" onmouseout="this.style.color='rgba(237,229,208,0.75)'">{{ $site->contact_email }}</a>
                        @endif
                    </address>
                </div>

            </div>

            <div class="sf-footer-bottom">
                <p style="font-size: 0.78rem; color: rgba(237,229,208,0.4);">
                    &copy; {{ date('Y') }} Othbar Horticulture Project. All rights reserved.
                </p>
                <p style="font-size: 0.78rem; color: rgba(237,229,208,0.4);">
                    Grown with reverence in the Kingdom of Bhutan
                </p>
            </div>
        </div>
    </footer>

    <script>
        (function () {
            const nav = document.getElementById('nav');
            const toggle = document.getElementById('nav-toggle');
            const menu = document.getElementById('nav-mobile-menu');
            const iconOpen = document.getElementById('nav-icon-open');
            const iconClose = document.getElementById('nav-icon-close');

            if (toggle && menu) {
                toggle.addEventListener('click', function () {
                    const open = menu.classList.toggle('is-open');
                    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                    toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
                    if (iconOpen) iconOpen.style.display = open ? 'none' : 'block';
                    if (iconClose) iconClose.style.display = open ? 'block' : 'none';
                });
                menu.querySelectorAll('a').forEach(function (link) {
                    link.addEventListener('click', function () {
                        menu.classList.remove('is-open');
                        toggle.setAttribute('aria-expanded', 'false');
                        toggle.setAttribute('aria-label', 'Open menu');
                        if (iconOpen) iconOpen.style.display = 'block';
                        if (iconClose) iconClose.style.display = 'none';
                    });
                });
            }

            window.addEventListener('scroll', function () {
                if (!nav) return;
                nav.style.boxShadow = window.scrollY > 20 ? '0 2px 20px rgba(30,58,42,0.12)' : 'none';
            });
        })();
    </script>

</body>
</html>
