<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  @php
    $pageMetaTitle = trim($__env->yieldContent('meta_title'));
    $pageMetaDescription = trim($__env->yieldContent('meta_description'));
    $pageMetaKeywords = trim($__env->yieldContent('meta_keywords'));
    $pageMetaImage = trim($__env->yieldContent('meta_image'));
    $pageMetaRobots = trim($__env->yieldContent('meta_robots'));
    $pageMetaType = trim($__env->yieldContent('meta_type'));
    $pageMetaCanonical = trim($__env->yieldContent('meta_canonical'));
  @endphp
  <x-seo-meta
    :title="$pageMetaTitle ?: null"
    :description="$pageMetaDescription ?: null"
    :keywords="$pageMetaKeywords ?: null"
    :image="$pageMetaImage ?: null"
    :robots="$pageMetaRobots ?: null"
    :type="$pageMetaType ?: null"
    :canonical="$pageMetaCanonical ?: null"
  />

  <link
    href="https://fonts.googleapis.com/css2?family=Lato:wght@400;600&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap"
    rel="stylesheet" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
    rel="stylesheet" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Manrope:wght@400;500;600;700;800&display=swap"
    rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <script src="https://code.iconify.design/iconify-icon/1.0.8/iconify-icon.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" />
  @stack('front_css')
  <link rel="stylesheet" href="{{ asset('assets/frontend/style.css') }}" />
</head>

<body class="hero-page">
  @if(app(\App\Support\SeoSettings::class)->googleTagManagerId())
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ app(\App\Support\SeoSettings::class)->googleTagManagerId() }}"
  height="0" width="0" style="display:none;visibility:hidden" title="Google Tag Manager"></iframe></noscript>
  @endif
  <nav id="desktopNavbar" class="navbar py-0 desktop-navbar d-none d-lg-block sticky-top">
    <div class="container px-4 px-xxl-5">
      <a class="navbar-brand" href="{{ route('frontend.home') }}">
        <div class="logo-badge-wrapper">
          <img src="{{ asset('assets/frontend/images/logo.webp') }}" alt="Logo" class="nav-logo-img" />
        </div>
      </a>

      <ul class="navbar-nav flex-row desktop-nav">
        <li class="nav-item">
          <a class="nav-link" href="{{ route('frontend.home') }}#home">Home</a>
        </li>
        <!-- <li class="nav-item">
          <a class="nav-link" href="{{ route('frontend.home') }}#about">About</a>
        </li> -->
        <li class="nav-item">
          <a class="nav-link" href="{{ route('frontend.completeMenu') }}">Full Menu</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('frontend.cards') }}">Card</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('frontend.reviews.index') }}">Reviews</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('frontend.contact') }}">Contacts</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('frontend.order.track') }}">Track Order</a>
        </li>
      </ul>

      <div class="d-flex align-items-center gap-2 nav-action-icons">
        @auth('member')
          <a href="{{ route('frontend.member.dashboard') }}" class="desktop-member-icon" aria-label="My Dashboard" title="My Dashboard">
            <iconify-icon class="desktop-member-symbol" icon="solar:widget-5-bold" aria-hidden="true"></iconify-icon>
          </a>
        @else
          <a href="{{ route('frontend.member.login') }}" class="desktop-member-icon" aria-label="Member Login" title="Member Login">
            <iconify-icon class="desktop-member-symbol" icon="solar:user-circle-linear" aria-hidden="true"></iconify-icon>
          </a>
        @endauth
        <a href="#cartDrawer" class="desktop-order-icon" aria-label="Open cart" data-bs-toggle="offcanvas" role="button"
          aria-controls="cartDrawer">
          <iconify-icon class="desktop-order-symbol" icon="solar:cart-large-2-outline"
            aria-hidden="true"></iconify-icon>
          <span class="desktop-order-qty" aria-label="0 items">0</span>
        </a>
      </div>
    </div>
  </nav>

  <nav class="navbar py-0 navbar-light mobile-topbar d-lg-none sticky-top">
    <div class="container-fluid px-3 px-sm-4">
      <button id="mobileMenuToggle" class="navbar-toggler mobile-menu-toggle" type="button" aria-controls="mobileMenu"
        aria-label="Open menu">
        <iconify-icon class="mobile-menu-icon" icon="ri:menu-2-line" width="24" height="24"
          aria-hidden="true"></iconify-icon>
      </button>

      <a class="navbar-brand mobile-nav-brand" href="{{ route('frontend.home') }}#home">
        <img src="{{ asset('assets/frontend/images/logo.webp') }}" class="mobile-logo-img" alt="Restaurant logo" />
      </a>

      <div class="d-flex align-items-center gap-2 mobile-nav-actions">
        @auth('member')
          <a href="{{ route('frontend.member.dashboard') }}" class="mobile-member-icon" aria-label="My Dashboard" title="My Dashboard">
            <iconify-icon class="mobile-member-symbol" icon="solar:widget-5-bold" aria-hidden="true"></iconify-icon>
          </a>
        @else
          <a href="{{ route('frontend.member.login') }}" class="mobile-member-icon" aria-label="Member Login" title="Member Login">
            <iconify-icon class="mobile-member-symbol" icon="solar:user-circle-linear" aria-hidden="true"></iconify-icon>
          </a>
        @endauth
        <a href="#cartDrawer" class="mobile-order-icon" aria-label="Open cart" data-bs-toggle="offcanvas" role="button"
          aria-controls="cartDrawer">
        <iconify-icon class="mobile-order-symbol" icon="solar:cart-large-2-outline" aria-hidden="true"></iconify-icon>
        <span class="mobile-order-qty" aria-label="0 items">0</span>
        </a>
      </div>
    </div>
  </nav>

  <div class="offcanvas offcanvas-start mobile-sidebar" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title" id="mobileMenuLabel">
        <img src="{{ asset('assets/frontend/images/logo.webp') }}" class="offcanvas-logo-img" alt="Restaurant logo" />
      </h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
      <ul class="nav flex-column side-nav">
        <li class="nav-item">
          <a data-bs-dismiss="offcanvas" class="nav-link" href="{{ route('frontend.home') }}#home">Home</a>
        </li>

        <!-- <li class="nav-item">
          <a data-bs-dismiss="offcanvas" class="nav-link" href="{{ route('frontend.home') }}#about">About</a>
        </li> -->

        <li class="nav-item">
          <a data-bs-dismiss="offcanvas" class="nav-link" href="{{ route('frontend.completeMenu') }}">Full Menu</a>
        </li>
        <li class="nav-item">
          <a data-bs-dismiss="offcanvas" class="nav-link" href="{{ route('frontend.cards') }}">Card</a>
        </li>
        <li class="nav-item">
          <a data-bs-dismiss="offcanvas" class="nav-link" href="{{ route('frontend.reviews.index') }}">Reviews</a>
        </li>
        <li class="nav-item">
          <a data-bs-dismiss="offcanvas" class="nav-link" href="{{ route('frontend.contact') }}">Contact</a>
        </li>
        <li class="nav-item">
          <a data-bs-dismiss="offcanvas" class="nav-link" href="{{ route('frontend.order.track') }}">Track Order</a>
        </li>
        @auth('member')
        <li class="nav-item">
          <a data-bs-dismiss="offcanvas" class="nav-link" href="{{ route('frontend.member.dashboard') }}">My Dashboard</a>
        </li>
        @else
        <li class="nav-item">
          <a data-bs-dismiss="offcanvas" class="nav-link" href="{{ route('frontend.member.login') }}">Member Login</a>
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

  <main class="main-content">

    @yield('frontend_content');
    <!-- Footer -->
    <footer id="contact" class="site-footer">
      <div class="footer-accent-bar"></div>

      <div class="footer-top">
        <div class="container px-4 px-lg-5">
          <div class="footer-grid">
            <div class="footer-brand-block">
              <img src="{{ asset('assets/frontend/images/logo.webp') }}" alt="Degchi Dine" class="footer-logo mb-3" />
              <p class="footer-tagline">Degchi Dine · ডেক্সি ডাইন</p>
              <p class="footer-about">
                A refined dining destination in Halishahar, Chittagong — warm hospitality, signature flavors, and memorable evenings.
              </p>
              <div class="footer-socials">
                <a href="https://www.facebook.com/DegchiDine" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                <a href="#" aria-label="Twitter / X"><i class="bi bi-twitter-x"></i></a>
                <a href="#" aria-label="TripAdvisor"><i class="bi bi-star"></i></a>
              </div>
            </div>

            <div class="footer-col">
              <h6 class="footer-heading"><span class="footer-heading-line"></span>Explore</h6>
              <ul class="footer-links footer-links-grid">
                <li><a href="{{ route('frontend.home') }}#home">Home</a></li>
                <li><a href="{{ route('frontend.home') }}#about">About Us</a></li>
                <li><a href="{{ route('frontend.completeMenu') }}">Full Menu</a></li>
                <li><a href="{{ route('frontend.cards') }}">Privilege Card</a></li>
                <li><a href="{{ route('frontend.reviews.index') }}">Reviews</a></li>
                <li><a href="{{ route('frontend.contact') }}">Contact</a></li>
              </ul>
            </div>

            <div class="footer-col">
              <h6 class="footer-heading"><span class="footer-heading-line"></span>Member &amp; Orders</h6>
              <ul class="footer-links">
                <li><a href="{{ route('frontend.card.apply') }}">Apply for Card</a></li>
                <li><a href="{{ route('frontend.member.login') }}">Member Login</a></li>
                <li><a href="{{ route('frontend.order.track') }}">Track Order</a></li>
                <li><a href="{{ route('frontend.addtocart') }}">View Cart</a></li>
                <li><a href="{{ route('frontend.checkout') }}">Checkout</a></li>
              </ul>
            </div>

            <div class="footer-col footer-contact-col">
              <h6 class="footer-heading"><span class="footer-heading-line"></span>Visit Us</h6>
              <ul class="footer-contact-list">
                <li class="footer-contact-item">
                  <span class="footer-contact-icon"><i class="bi bi-geo-alt"></i></span>
                  <span>Boropool Circle, Kaptan Villa, Halishahar, Chittagong</span>
                </li>
                <li class="footer-contact-item">
                  <span class="footer-contact-icon"><i class="bi bi-telephone"></i></span>
                  <a href="tel:01898795400">01898-795400</a>
                </li>
                <li class="footer-contact-item">
                  <span class="footer-contact-icon"><i class="bi bi-envelope"></i></span>
                  <a href="mailto:degchidine@gmail.com">degchidine@gmail.com</a>
                </li>
                <li class="footer-contact-item">
                  <span class="footer-contact-icon"><i class="bi bi-clock"></i></span>
                  <span>Daily · 5:00 PM – 11:30 PM</span>
                </li>
              </ul>
            </div>
          </div>

          <div class="footer-quick-actions">
            <a href="{{ route('frontend.completeMenu') }}" class="footer-action-pill"><i class="bi bi-grid-3x3-gap"></i> Browse Menu</a>
            <a href="{{ route('frontend.order.track') }}" class="footer-action-pill"><i class="bi bi-truck"></i> Track Order</a>
            <a href="{{ route('frontend.cards') }}" class="footer-action-pill footer-action-pill-gold"><i class="bi bi-credit-card-2-front"></i> Get Member Card</a>
          </div>
        </div>
      </div>

      <div class="footer-divider"></div>

      <div class="footer-bottom">
        <div class="container px-4 px-lg-5 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
          <p class="mb-0">&copy; 2026 Degchi Dine. All rights reserved.</p>
          <div class="d-flex gap-3">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Use</a>
          </div>
        </div>
      </div>
    </footer>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <!-- Floating action buttons (right) -->
<!-- Floating action buttons (right) -->
<div class="floating-right" id="floatingActionGroup">

    <!-- Sub action buttons (hidden until main FAB is clicked) -->
    <div class="fab-menu" id="fabMenu">

        @guest('member')
            @unless(request()->routeIs('frontend.order.track'))
            <button type="button" class="fab-item fab-track" data-bs-toggle="modal"
                data-bs-target="#trackOrderModal" aria-label="Track your order" title="Track Order">
                <span class="fab-item-icon"><iconify-icon icon="solar:delivery-linear" aria-hidden="true"></iconify-icon></span>
                <span class="fab-item-label">Track Order</span>
            </button>
            @endunless
        @else
            @unless(request()->routeIs('frontend.order.track'))
            <a href="{{ route('frontend.order.track') }}" class="fab-item fab-track"
                aria-label="Track an order" title="Track Order">
                <span class="fab-item-icon"><iconify-icon icon="solar:delivery-linear" aria-hidden="true"></iconify-icon></span>
                <span class="fab-item-label">Track Order</span>
            </a>
            @endunless
            <a href="{{ route('frontend.member.dashboard') }}" class="fab-item fab-dashboard"
                aria-label="My Dashboard" title="My Dashboard">
                <span class="fab-item-icon"><iconify-icon icon="solar:widget-5-bold" aria-hidden="true"></iconify-icon></span>
                <span class="fab-item-label">Dashboard</span>
            </a>
        @endguest

        <a class="fab-item fab-messenger"
            href="https://m.me/YOUR_PAGE_USERNAME"
            target="_blank" rel="noopener noreferrer"
            aria-label="Chat with us on Messenger" title="Messenger">
            <span class="fab-item-icon"><iconify-icon icon="ri:messenger-fill" aria-hidden="true"></iconify-icon></span>
            <span class="fab-item-label">Messenger</span>
        </a>

        <a class="fab-item fab-whatsapp"
            href="https://wa.me/8801898795400?text=Hello%20Degchi%20Dine%20I%20have%20a%20question%20about%20ordering"
            target="_blank" rel="noopener noreferrer"
            aria-label="Chat with us on WhatsApp" title="WhatsApp">
            <span class="fab-item-icon"><i class="bi bi-whatsapp" aria-hidden="true"></i></span>
            <span class="fab-item-label">WhatsApp</span>
        </a>

    </div>

    <!-- Main toggle button -->
    <button type="button" id="fabMainToggle" class="fab-main" aria-label="Open contact options" aria-expanded="false">
        <iconify-icon icon="solar:chat-round-dots-bold" class="fab-icon-open" aria-hidden="true"></iconify-icon>
        <iconify-icon icon="solar:close-circle-bold" class="fab-icon-close" aria-hidden="true"></iconify-icon>
    </button>

</div>

  {{-- Quick Track Order Modal (site-wide) --}}
  @guest('member')
  <div class="modal fade track-order-modal" id="trackOrderModal" tabindex="-1" aria-labelledby="trackOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="trackOrderModalLabel">
            <iconify-icon icon="solar:delivery-linear" style="color: var(--dd-gold); font-size: 1.4rem;"></iconify-icon>
            Track Your Order
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="text-muted mb-4" style="font-size: 0.88rem;">Enter your order number and the phone number you used at checkout.</p>

          <form method="POST" action="{{ route('frontend.order.track.submit') }}" class="dd-apply-form-element" style="margin-bottom: 0;">
            @csrf
            <div class="dd-input-group">
              <input type="number" name="order_id" id="modal_track_order_id" class="dd-input-field" placeholder=" " value="{{ old('order_id', request('order')) }}" required min="1">
              <label for="modal_track_order_id" class="dd-floating-label">Order Number</label>
            </div>
            <div class="dd-input-group">
              <input type="tel" name="phone" id="modal_track_phone" class="dd-input-field" placeholder=" " value="{{ old('phone') }}" required>
              <label for="modal_track_phone" class="dd-floating-label">Phone Number</label>
            </div>
            <button type="submit" class="dd-submit-btn" style="margin-top: 8px;">
              <span>View Order</span>
              <iconify-icon icon="solar:magnifer-linear" class="dd-btn-icon"></iconify-icon>
            </button>
          </form>
          <div class="modal-footer-tip">
            Or go to the full <a href="{{ route('frontend.order.track') }}">Track Order page</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endguest

  <div class="modal fade mc-quick-modal" id="mcQuickViewModal" tabindex="-1" aria-labelledby="mcQuickViewTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content mc-modal-content">
        <button type="button" class="btn-close mc-modal-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="row g-0">
          <div class="col-md-6">
            <div class="mc-modal-image-wrap">
              <img id="mcQuickViewImage" src="" alt="" class="mc-modal-image" />
            </div>
          </div>
          <div class="col-md-6">
            <div class="mc-modal-body">
              <p class="mc-modal-kicker mb-2">Menu Preview</p>
              <h4 id="mcQuickViewTitle" class="mc-modal-title mb-2"></h4>
              <p id="mcQuickViewDesc" class="mc-modal-desc"></p>
              <div class="mc-modal-meta">
                <span id="mcQuickViewServe" class="mc-serve-info"></span>
              </div>
              <div class="mc-modal-price-wrap mt-3">
                <span class="mc-price-label">Starts from</span>
                <span id="mcQuickViewPrice" class="mc-price"></span>
              </div>
              <a href="{{ route('frontend.home') }}#menu" class="mc-show-more-btn mt-4" data-bs-dismiss="modal">
                Explore Full Menu
                <i class="bi bi-arrow-right-short" aria-hidden="true"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- CART -->
  <div class="offcanvas offcanvas-end cart-drawer" tabindex="-1" id="cartDrawer" aria-labelledby="cartDrawerLabel">
    <div class="offcanvas-header cart-drawer-header">
      <div class="cart-drawer-heading">
        <h5 class="offcanvas-title" id="cartDrawerLabel">Your Cart</h5>
        <p class="cart-drawer-subtitle mb-0" id="cartDrawerCount">No items yet</p>
      </div>
      <button type="button" class="btn-close-custom" data-bs-dismiss="offcanvas" aria-label="Close">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>

    <div class="offcanvas-body cart-drawer-body d-flex flex-column">
      <div id="cartDrawerItems" class="cart-drawer-items flex-grow-1">
        <div class="cart-drawer-empty">
          <div class="cart-drawer-empty-icon" aria-hidden="true">
            <i class="bi bi-bag"></i>
          </div>
          <p class="cart-drawer-empty-title">Your cart is empty</p>
          <p class="cart-drawer-empty-text">Add dishes from the menu to get started.</p>
        </div>
      </div>

      <div class="cart-drawer-footer">
        <div class="cart-drawer-total-row">
          <span class="cart-total-label">Subtotal</span>
          <strong id="cartDrawerSubtotal" class="cart-total-value">৳ 0.00</strong>
        </div>
        <div class="cart-drawer-actions d-grid gap-2">
          <a href="{{ route('frontend.addtocart') }}" class="btn cart-view-btn">View Full Cart</a>
          <a href="{{ route('frontend.checkout') }}" class="btn cart-checkout-btn">
            <span>Proceed to Checkout <i class="bi bi-arrow-right ms-1"></i></span>
          </a>
        </div>
      </div>
    </div>
  </div>
  <!-- Membership prompt modal shown at checkout when user is not registered -->
  <div class="modal fade" id="memberPromptModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Become a Member</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p>Members get exclusive discounts, priority service and rewards. Register now to apply your membership benefits.</p>
        </div>
        <div class="modal-footer">
          <a href="{{ route('frontend.card.apply') }}" class="btn btn-primary">Register Now</a>
          <button id="continueAsGuestBtn" type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Continue as Guest</button>
        </div>
      </div>
    </div>
  </div>

  <script src="{{ asset('assets/frontend/app.js') }}"></script>
  <script src="{{ asset('assets/frontend/dd-credit.js') }}" defer></script>
  @stack('front_js')
</body>

</html>