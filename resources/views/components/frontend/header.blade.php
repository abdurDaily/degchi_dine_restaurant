{{-- Desktop Navbar --}}
<nav id="desktopNavbar" class="navbar py-0 desktop-navbar d-none d-lg-block sticky-top">
    <div class="container px-4 px-xxl-5">
        <a class="navbar-brand" href="{{ route('frontend.home') }}">
            <div class="logo-badge-wrapper">
                <img src="{{ asset('assets/frontend/images/degchi-dine-logo.webp') }}"
                    alt="Degchi Dine - Authentic Kacchi & Bangla Restaurant" class="nav-logo-img" loading="eager" />
            </div>
        </a>

        <ul class="navbar-nav flex-row desktop-nav2">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('frontend.home') ? 'active' : '' }}"
                    href="{{ route('frontend.home') }}#home">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('frontend.about') ? 'active' : '' }}"
                    href="{{ route('frontend.about') }}">About</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('frontend.blog.*') ? 'active' : '' }}"
                    href="{{ route('frontend.blog.index') }}">Blog</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->fullUrlIs(url('/menu?min_price=0&page=1&offerFilter=1')) ? 'active' : '' }}"
                    href="{{ url('/menu?min_price=0&page=1&offerFilter=1') }}">Offers</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('frontend.completeMenu') && !request()->has('offerFilter') ? 'active' : '' }}"
                    href="{{ route('frontend.completeMenu') }}">Full Menu</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('frontend.reviews.index') ? 'active' : '' }}"
                    href="{{ route('frontend.reviews.index') }}">Reviews</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('frontend.contact') ? 'active' : '' }}"
                    href="{{ route('frontend.contact') }}">Contacts</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('frontend.order.track') ? 'active' : '' }}"
                    href="{{ route('frontend.order.track') }}">Track Order</a>
            </li>
        </ul>

        <div class="d-flex align-items-center gap-2 nav-action-icons">
            @auth('member')
                <a href="{{ route('frontend.member.dashboard') }}" class="desktop-member-icon" aria-label="My Dashboard"
                    title="My Dashboard">
                    <iconify-icon class="desktop-member-symbol" icon="solar:widget-5-bold"
                        aria-hidden="true"></iconify-icon>
                </a>
            @else
                <a href="{{ route('frontend.member.login') }}" class="desktop-member-icon" aria-label="Member Login"
                    title="Member Login">
                    <iconify-icon class="desktop-member-symbol" icon="solar:user-circle-linear"
                        aria-hidden="true"></iconify-icon>
                </a>
            @endauth
            <a href="#cartDrawer" class="desktop-order-icon" aria-label="Open cart" data-bs-toggle="offcanvas"
                role="button" aria-controls="cartDrawer">
                <iconify-icon class="desktop-order-symbol" icon="solar:cart-large-2-outline"
                    aria-hidden="true"></iconify-icon>
                <span class="desktop-order-qty" aria-label="0 items">0</span>
            </a>
        </div>
    </div>
</nav>

{{-- Mobile Topbar --}}
<nav class="navbar py-0 navbar-light mobile-topbar d-lg-none sticky-top">
    <div class="container-fluid px-3 px-sm-4">
        <button id="mobileMenuToggle" class="navbar-toggler mobile-menu-toggle" type="button"
            aria-controls="mobileMenu" aria-label="Open menu">
            <iconify-icon class="mobile-menu-icon" icon="ri:menu-2-line" width="24" height="24"
                aria-hidden="true"></iconify-icon>
        </button>

        <a class="navbar-brand mobile-nav-brand" href="{{ route('frontend.home') }}#home">
            <img src="{{ asset('assets/frontend/images/degchi-dine-logo.webp') }}" class="mobile-logo-img"
                alt="Degchi Dine Restaurant" loading="eager" />
        </a>

        <div class="d-flex align-items-center gap-2 mobile-nav-actions">
            @auth('member')
                <a href="{{ route('frontend.member.dashboard') }}" class="mobile-member-icon" aria-label="My Dashboard"
                    title="My Dashboard">
                    <iconify-icon class="mobile-member-symbol" icon="solar:widget-5-bold" aria-hidden="true"></iconify-icon>
                </a>
            @else
                <a href="{{ route('frontend.member.login') }}" class="mobile-member-icon" aria-label="Member Login"
                    title="Member Login">
                    <iconify-icon class="mobile-member-symbol" icon="solar:user-circle-linear"
                        aria-hidden="true"></iconify-icon>
                </a>
            @endauth
            <a href="#cartDrawer" class="mobile-order-icon" aria-label="Open cart" data-bs-toggle="offcanvas"
                role="button" aria-controls="cartDrawer">
                <iconify-icon class="mobile-order-symbol" icon="solar:cart-large-2-outline"
                    aria-hidden="true"></iconify-icon>
                <span class="mobile-order-qty" aria-label="0 items">0</span>
            </a>
        </div>
    </div>
</nav>

{{-- Mobile Sidebar --}}
<div class="offcanvas offcanvas-start mobile-sidebar" tabindex="-1" id="mobileMenu"
    aria-labelledby="mobileMenuLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="mobileMenuLabel">
            <img src="{{ asset('assets/frontend/images/degchi-dine-logo.webp') }}" class="offcanvas-logo-img"
                alt="Degchi Dine Restaurant" />
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <ul class="nav flex-column side-nav-mbl">
            <li class="nav-item">
                <a data-bs-dismiss="offcanvas"
                    class="nav-link {{ request()->routeIs('frontend.home') ? 'active' : '' }}"
                    href="{{ route('frontend.home') }}#home">Home</a>
            </li>
            <li class="nav-item">
                <a data-bs-dismiss="offcanvas"
                    class="nav-link {{ request()->routeIs('frontend.about') ? 'active' : '' }}"
                    href="{{ route('frontend.about') }}">About</a>
            </li>
            <li class="nav-item">
                <a data-bs-dismiss="offcanvas"
                    class="nav-link {{ request()->routeIs('frontend.blog.*') ? 'active' : '' }}"
                    href="{{ route('frontend.blog.index') }}">Blog</a>
            </li>
            <li class="nav-item">
                <a data-bs-dismiss="offcanvas"
                    class="nav-link {{ request()->fullUrlIs(url('/menu?min_price=0&page=1&offerFilter=1')) ? 'active' : '' }}"
                    href="{{ url('/menu?min_price=0&page=1&offerFilter=1') }}">Offers</a>
            </li>
            <li class="nav-item">
                <a data-bs-dismiss="offcanvas"
                    class="nav-link {{ request()->routeIs('frontend.completeMenu') && !request()->has('offerFilter') ? 'active' : '' }}"
                    href="{{ route('frontend.completeMenu') }}">Full Menu</a>
            </li>
            <li class="nav-item">
                <a data-bs-dismiss="offcanvas"
                    class="nav-link {{ request()->routeIs('frontend.cards') ? 'active' : '' }}"
                    href="{{ route('frontend.cards') }}">Card</a>
            </li>
            <li class="nav-item">
                <a data-bs-dismiss="offcanvas"
                    class="nav-link {{ request()->routeIs('frontend.reviews.index') ? 'active' : '' }}"
                    href="{{ route('frontend.reviews.index') }}">Reviews</a>
            </li>
            <li class="nav-item">
                <a data-bs-dismiss="offcanvas"
                    class="nav-link {{ request()->routeIs('frontend.contact') ? 'active' : '' }}"
                    href="{{ route('frontend.contact') }}">Contact</a>
            </li>
            <li class="nav-item">
                <a data-bs-dismiss="offcanvas"
                    class="nav-link {{ request()->routeIs('frontend.order.track') ? 'active' : '' }}"
                    href="{{ route('frontend.order.track') }}">Track Order</a>
            </li>
            @auth('member')
                <li class="nav-item">
                    <a data-bs-dismiss="offcanvas"
                        class="nav-link {{ request()->routeIs('frontend.member.dashboard') ? 'active' : '' }}"
                        href="{{ route('frontend.member.dashboard') }}">My Dashboard</a>
                </li>
            @else
                <li class="nav-item">
                    <a data-bs-dismiss="offcanvas"
                        class="nav-link {{ request()->routeIs('frontend.member.login') ? 'active' : '' }}"
                        href="{{ route('frontend.member.login') }}">Member Login</a>
                </li>
            @endauth
        </ul>

        <div class="side-footer mt-4 pt-3 border-top">
            <p class="mb-1">
                <i class="bi bi-geo-alt me-2"></i>Boropool Circle, Kaptan Villa,
                Halishahar, Chittagong, Bangladesh
            </p>
            <p class="mb-1">
                <i class="bi bi-clock me-2"></i>Mon-Sun: 5:00 PM - 11:30 PM
            </p>
            <p class="mb-0"><i class="bi bi-telephone me-2"></i>01898-795400</p>
        </div>
    </div>
</div>
