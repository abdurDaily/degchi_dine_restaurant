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

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="{{ asset('assets/frontend/images/logo.webp') }}">

  <!-- Preconnect for external resources -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />

  <!-- Critical CSS: Bootstrap + Icons (render-blocking by design for layout) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />

  <!-- Non-critical CSS: deferred -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" media="print" onload="this.media='all'" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lato:wght@400;600&family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Poppins:wght@400;500;600;700;800&family=Fraunces:wght@500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" media="print" onload="this.media='all'" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" media="print" onload="this.media='all'" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" media="print" onload="this.media='all'" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr.js/latest/toastr.min.css" media="print" onload="this.media='all'" />

  @stack('front_css')

  <!-- App CSS -->
  <link rel="stylesheet" href="{{ asset('assets/frontend/css/base.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/frontend/css/cart-checkout.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/frontend/css/member-auth.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/frontend/style.css') }}" />

  <!-- Inline critical JS to avoid render-blocking -->
  <script>
    // GTM noscript fallback handled in body
    window.DEGCHI = { version: '1.0' };
  </script>
</head>

<body class="hero-page">
  @if(app(\App\Support\SeoSettings::class)->googleTagManagerId())
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ app(\App\Support\SeoSettings::class)->googleTagManagerId() }}"
  height="0" width="0" style="display:none;visibility:hidden" title="Google Tag Manager"></iframe></noscript>
  @endif

  {{-- Header Component --}}
  <x-frontend.header />

  <main class="main-content">
    @yield('frontend_content')

    {{-- Footer Component --}}
    <x-frontend.footer />
  </main>

  <!-- Scripts: deferred for performance -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous" defer></script>
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js" defer></script>
  <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js" defer></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" defer></script>
  <script src="https://code.iconify.design/iconify-icon/1.0.8/iconify-icon.min.js" defer></script>

  {{-- Floating Actions Component --}}
  <x-frontend.floating-actions />

  {{-- Track Order Modal Component --}}
  <x-frontend.track-order-modal />

  {{-- Menu Quick View Modal Component --}}
  <x-frontend.menu-quick-view-modal />

  {{-- Cart Drawer Component --}}
  <x-frontend.cart-drawer />

  {{-- Member Prompt Modal Component --}}
  <x-frontend.member-prompt-modal />

  {{-- Offer Login Modal Component --}}
  <x-frontend.offer-login-modal />

  @php
    $degchiMember = Auth::guard('member')->user();
  @endphp
  <script>
    window.DEGCHI_MEMBER = {
      loggedIn: @json((bool) $degchiMember),
      canUseFirstOrder: @json($degchiMember ? $degchiMember->canUseFirstOrderDiscount() : false),
      canOrderAndComment: @json($degchiMember ? $degchiMember->canOrderAndComment() : false),
      accountRestrictedMessage: @json(\App\Models\Member::ACCOUNT_RESTRICTED_MESSAGE),
      loginUrl: @json(route('frontend.member.login')),
      registerUrl: @json(route('frontend.card.apply')),
    };
  </script>
  <script src="{{ asset('assets/frontend/js/cart.js') }}" defer></script>
  <script src="{{ asset('assets/frontend/app.js') }}" defer></script>
  <script src="{{ asset('assets/frontend/dd-credit.js') }}" defer></script>
  @stack('front_js')
</body>

</html>
