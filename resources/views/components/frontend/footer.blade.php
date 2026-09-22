{{-- Footer --}}
@php
    $myContactSettings = cache()->remember('home_contact_settings', 1800, function () {
        return \App\Models\Setting::where('setting_group', 'contact_section')->get()->keyBy('key');
    });
    $cVal = fn ($key, $default = '') => optional($myContactSettings->get($key))->value ?? $default;
    $myPhone = $cVal('contact_phone', '01898-795400');
    $myPhoneDigits = preg_replace('/\D+/', '', $myPhone);
    $myEmail = $cVal('contact_email', 'degchidine@gmail.com');
    $myAddress = $cVal('contact_address', 'Boropool Circle, Kaptan Villa, Halishahar, Chittagong');
    $myHours = $cVal('contact_hours', 'Daily · 5:00 PM – 11:30 PM');
    $myFacebookUrl = $cVal('contact_facebook_url', '#');
    $myInstagramUrl = $cVal('contact_instagram_url', '#');
@endphp
<footer id="contact" class="site-footer">
  <div class="footer-accent-bar"></div>

  <div class="footer-top">
    <div class="container px-4 px-lg-5">
      <div class="footer-grid">
        <div class="footer-brand-block">
          <img src="{{ asset('assets/frontend/images/degchi-dine-logo-sm.webp') }}" alt="Degchi Dine - Authentic Kacchi & Bangla Restaurant" class="footer-logo mb-3" loading="lazy" width="262" height="259" />
          <p class="footer-tagline">Degchi Dine · ডেক্সি ডাইন</p>
          <p class="footer-about">
            A refined dining destination in Halishahar, Chittagong — warm hospitality, signature flavors, and memorable evenings.
          </p>
          <div class="footer-socials">
            <a href="{{ $myFacebookUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
            <a href="{{ $myInstagramUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
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
              <span>{{ $myAddress }}</span>
            </li>
            <li class="footer-contact-item">
              <span class="footer-contact-icon"><i class="bi bi-telephone"></i></span>
              <a href="tel:{{ $myPhoneDigits }}">{{ $myPhone }}</a>
            </li>
            <li class="footer-contact-item">
              <span class="footer-contact-icon"><i class="bi bi-envelope"></i></span>
              <a href="mailto:{{ $myEmail }}">{{ $myEmail }}</a>
            </li>
            <li class="footer-contact-item">
              <span class="footer-contact-icon"><i class="bi bi-clock"></i></span>
              <span>{{ $myHours }}</span>
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
