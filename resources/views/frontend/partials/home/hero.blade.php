<!-- HERO -->
<section id="home" class="hero">
    <video class="hero-video" autoplay muted loop playsinline preload="metadata"
        poster="https://images.unsplash.com/photo-1559339352-11d035aa65de?auto=format&fit=crop&w=800&q=75">
        <source src="{{ asset('assets/frontend/video/degchi_dine_banner_video.mp4') }}" type="video/mp4" />
    </video>
    <div class="hero-overlay"></div>

    <div class="hero-side-rail hero-side-right d-none d-lg-flex">
        <a href="https://www.facebook.com/DegchiDine" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i
                class="bi bi-facebook"></i></a>
        <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
        <a href="#" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
    </div>

    {{-- Desktop hero content --}}
    <div class="container hero-content px-4 px-lg-5 d-none d-lg-flex align-items-center justify-content-center">
        <div class="text-center w-100">
            <h2 class="hero-title">
                Home Of Traditional Mezban, Kacchi & Bangla Food
            </h2>
            <p class="hero-copy mt-3 mb-4 mx-auto">
                From slow-cooked Kacchi to flavorful Biriyani, we bring generations of tradition, rich spices, and
                unforgettable aromas together to create a dining experience that feels like a celebration in every bite.
            </p>
            <div class="hero-cta-group d-flex flex-wrap gap-3 justify-content-center">
                <a href="{{ route('frontend.home') }}#menu" class="btn-dine btn-dine-primary btn-dine-lg">Order Now</a>
                <a href="{{ route('frontend.partyBooking') }}" class="btn-dine btn-dine-outline btn-dine-lg">Party
                    Booking</a>
            </div>
        </div>
    </div>

    {{-- Mobile hero content --}}
    <div class="hero-mobile-stack d-flex d-lg-none align-items-center justify-content-center">
        <div class="hero-mobile-inner text-center px-3">
            <h2 class="hero-mobile-title">
                Home Of Traditional Mezban, Kacchi &amp; Bangla Food
            </h2>
            <p class="hero-mobile-copy mx-auto mb-3">
                Generations of tradition, rich spices, and unforgettable aromas in every bite.
            </p>
            <div class="hero-cta-group d-flex flex-column gap-2">
                <a href="{{ route('frontend.home') }}#menu" class="btn-dine btn-dine-primary btn-dine-sm w-100">Order Now</a>
                <a href="{{ route('frontend.partyBooking') }}" class="btn-dine btn-dine-outline btn-dine-sm w-100">Party Booking</a>
            </div>
        </div>
    </div>
</section>
