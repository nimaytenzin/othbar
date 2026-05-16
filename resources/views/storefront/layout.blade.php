<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Othbar Horticulture Project — Certified organic food grown in the pristine highlands of Bhutan.">
    <title>@yield('title', 'Othbar') — From the Dragon Kingdom's Earth</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="texture-bg min-h-screen">

    {{-- Announcement bar --}}
    <div style="background: #1E3A2A; color: #F7F2E8;" class="py-2 text-center">
        <p style="font-size: 0.7rem; letter-spacing: 0.2em; font-weight: 500;" class="uppercase">
            Free delivery within Thimphu &nbsp;&bull;&nbsp; Certified Organic &nbsp;&bull;&nbsp; Grown at 2,400m elevation
        </p>
    </div>

    {{-- Navigation --}}
    <header id="nav" style="background: #F7F2E8; border-bottom: 1px solid #D8CCAD; position: sticky; top: 0; z-index: 50; transition: all 0.3s ease;">
        <div style="max-width: 1280px; margin: 0 auto; padding: 0 2rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; height: 72px;">

                {{-- Left nav --}}
                <nav style="display: flex; gap: 2rem; align-items: center;">
                    <a href="{{ route('shop') }}" class="nav-link">Shop</a>
                    <a href="{{ route('story') }}" class="nav-link">Our Story</a>
                    <a href="#farms" class="nav-link">Farms</a>
                </nav>

                {{-- Logo --}}
                <a href="{{ route('home') }}" style="text-decoration: none; text-align: center; flex-shrink: 0;">
                    <div style="display: flex; flex-direction: column; align-items: center; gap: 1px;">
                        <span style="font-family: 'Cormorant Garamond', serif; font-size: 1.6rem; font-weight: 600; color: #1E3A2A; line-height: 1; letter-spacing: 0.05em;">OTHBAR</span>
                        <span style="font-size: 0.55rem; letter-spacing: 0.25em; font-weight: 500; color: #C4843C; text-transform: uppercase;">Horticulture &bull; Bhutan</span>
                    </div>
                </a>

                {{-- Right nav --}}
                <nav style="display: flex; gap: 2rem; align-items: center;">
                    <a href="#" class="nav-link">Journal</a>
                    <a href="#contact" class="nav-link">Contact</a>
                    <a href="{{ route('storefront.login') }}" class="nav-link">Login</a>
                    @php
                        $navCartCount = collect(app(\App\Services\CartSessionService::class)->lineRows())->sum('quantity');
                    @endphp
                    <a href="{{ route('cart') }}" style="position: relative; display: flex; align-items: center; text-decoration: none; color: #1E3A2A;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M6 2 3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/>
                        </svg>
                        <span style="position: absolute; top: -6px; right: -6px; min-width: 16px; height: 16px; background: #C4843C; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.6rem; color: white; font-weight: 600; padding: 0 2px;">{{ $navCartCount }}</span>
                    </a>
                </nav>

            </div>
        </div>
        {{-- Woven border accent --}}
        <div class="footer-weave" style="opacity: 0.7;"></div>
    </header>

    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer style="background: #1E3A2A; color: #EDE5D0; margin-top: 6rem;">
        <div class="footer-weave"></div>
        <div style="max-width: 1280px; margin: 0 auto; padding: 4rem 2rem 2rem;">
            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 4rem; padding-bottom: 3rem; border-bottom: 1px solid rgba(237,229,208,0.15);">

                <div>
                    <div style="margin-bottom: 1.5rem;">
                        <span style="font-family: 'Cormorant Garamond', serif; font-size: 2rem; font-weight: 600; letter-spacing: 0.05em;">OTHBAR</span>
                        <p style="font-size: 0.55rem; letter-spacing: 0.25em; color: #C4843C; text-transform: uppercase; margin-top: 2px;">Horticulture Project &bull; Bhutan</p>
                    </div>
                    <p style="font-size: 0.9rem; line-height: 1.8; color: rgba(237,229,208,0.75); max-width: 280px;">
                        Nestled in the sacred valleys of Bhutan at 2,400 metres, we cultivate organic food with reverence for the land, guided by ancient Bhutanese agricultural wisdom.
                    </p>
                    <div style="margin-top: 1.5rem; display: flex; gap: 0.75rem;">
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
                        <li><a href="#" style="font-size: 0.88rem; color: rgba(237,229,208,0.75); text-decoration: none;" onmouseover="this.style.color='#EDE5D0'" onmouseout="this.style.color='rgba(237,229,208,0.75)'">Journal</a></li>
                        <li><a href="#contact" style="font-size: 0.88rem; color: rgba(237,229,208,0.75); text-decoration: none;" onmouseover="this.style.color='#EDE5D0'" onmouseout="this.style.color='rgba(237,229,208,0.75)'">Contact</a></li>
                    </ul>
                </div>

                <div id="contact">
                    <h4 style="font-family: 'Jost', sans-serif; font-size: 0.7rem; font-weight: 600; letter-spacing: 0.2em; text-transform: uppercase; color: #C4843C; margin-bottom: 1.25rem;">Find Us</h4>
                    <address style="font-style: normal; display: flex; flex-direction: column; gap: 0.75rem;">
                        <p style="font-size: 0.88rem; color: rgba(237,229,208,0.75); line-height: 1.6;">Othbar Valley<br>Punakha Dzongkhag<br>Bhutan</p>
                        <a href="tel:+97502123456" style="font-size: 0.88rem; color: rgba(237,229,208,0.75); text-decoration: none;" onmouseover="this.style.color='#EDE5D0'" onmouseout="this.style.color='rgba(237,229,208,0.75)'">+975 02 123 456</a>
                        <a href="mailto:hello@othbar.bt" style="font-size: 0.88rem; color: rgba(237,229,208,0.75); text-decoration: none;" onmouseover="this.style.color='#EDE5D0'" onmouseout="this.style.color='rgba(237,229,208,0.75)'">hello@othbar.bt</a>
                    </address>
                </div>

            </div>

            <div style="padding-top: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
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
        // Subtle parallax on scroll for nav
        window.addEventListener('scroll', () => {
            const nav = document.getElementById('nav');
            if (window.scrollY > 20) {
                nav.style.boxShadow = '0 2px 20px rgba(30,58,42,0.12)';
            } else {
                nav.style.boxShadow = 'none';
            }
        });
    </script>

</body>
</html>
