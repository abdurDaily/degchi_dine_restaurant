@extends('frontend.layout')

@section('meta_title', 'About Us — Degchi Dine')
@section('meta_description', 'Learn about Degchi Dine — our heritage, story, authentic Dum-style cooking and the passion behind every clay-pot meal.')

@section('frontend_content')

<section class="page-hero">
  <div class="container">
    <div class="page-hero-inner">
      <div class="page-hero-eyebrow">
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
        </svg>
        Our Stories
      </div>
      <h1>About Degchi Dine</h1>
      <p>Discover the heritage, passion, and authentic Dum-style cooking traditions behind every clay-pot meal at Degchi Dine.</p>
    </div>
  </div>
</section>

{{-- ─── About Content Section (shared partial) ─── --}}
@include('frontend.partials.home.about_partials', ['ctaText' => 'Contact Us', 'aboutPage' => true])

{{-- ─── Video Showcase Section ─── --}}
@if($videos->isNotEmpty())
<section class="about-videos-section py-5" id="videos">
  <div class="container px-4 px-lg-5">

    <div class="text-center mb-5 reveal">
      <span class="about-kicker text-uppercase d-block mb-2">
        <i class="bi bi-play-circle-fill me-1"></i> Watch & Experience
      </span>
      <h2 class="section-title">Watch Us on facebook</h2>
      <p class="about-lead mx-auto" style="max-width: 600px;">
        Kitchen energy, chef moments, and guest vibes from Degchi Dine. Fresh reels every week.
      </p>
    </div>

    <div class="row g-4 justify-content-center">
      @foreach($videos as $video)
        <div class="col-12 col-sm-6 col-lg-4 reveal">
          <div class="about-video-card">
            <a href="{{ $video->facebook_url }}" target="_blank" rel="noopener noreferrer" class="about-video-thumb-wrap">
              @if($video->thumbnail)
                <img src="{{ asset('uploads/reels/' . $video->thumbnail) }}"
                     alt="{{ $video->title }}"
                     class="about-video-thumb"
                     onerror="this.src='{{ asset('assets/frontend/images/about/about.jpg') }}'" />
              @else
                <div class="about-video-thumb-placeholder">
                  <i class="bi bi-play-circle-fill"></i>
                </div>
              @endif
              <div class="about-video-play-overlay">
                <div class="about-video-play-btn">
                  <i class="bi bi-play-fill"></i>
                </div>
              </div>
            </a>
            <div class="about-video-info">
              <p class="about-video-title">{{ $video->title }}</p>
              <a href="{{ $video->facebook_url }}" target="_blank" rel="noopener noreferrer" class="about-video-link">
                <i class="bi bi-facebook me-1"></i> Watch on Facebook
              </a>
            </div>
          </div>
        </div>
      @endforeach
    </div>

  </div>
</section>
@endif

{{-- ─── Values / Mission Strip ─── --}}
<section class="about-values-section py-5">
  <div class="container px-4 px-lg-5">
    <div class="row g-4 text-center">
      <div class="col-12 col-md-4 reveal">
        <div class="about-value-card">
          <div class="about-value-icon"><i class="bi bi-award-fill"></i></div>
          <h5 class="about-value-title">Authentic Recipes</h5>
          <p class="about-value-text">Rooted in traditional Dum cooking — every dish is slow-cooked to perfection in clay vessels.</p>
        </div>
      </div>
      <div class="col-12 col-md-4 reveal">
        <div class="about-value-card">
          <div class="about-value-icon"><i class="bi bi-people-fill"></i></div>
          <h5 class="about-value-title">Warm Hospitality</h5>
          <p class="about-value-text">We don't just serve food — we create experiences. Every guest is welcomed like family.</p>
        </div>
      </div>
      <div class="col-12 col-md-4 reveal">
        <div class="about-value-card">
          <div class="about-value-icon"><i class="bi bi-stars"></i></div>
          <h5 class="about-value-title">Premium Quality</h5>
          <p class="about-value-text">Only the finest cuts, freshest produce, and authentic spice blends make it to your plate.</p>
        </div>
      </div>
    </div>
  </div>
</section>

@push('front_css')
<style>
/* ─── Video Cards ─── */
.about-videos-section {
  background: var(--bg-soft);
}
.about-video-card {
  background: #fff;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: var(--card-shadow);
  transition: transform 0.35s ease, box-shadow 0.35s ease;
  height: 100%;
}
.about-video-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 24px 48px rgba(17, 107, 131, 0.18);
}

/* FIX: <a> tag (thumb wrap) — force block + full width explicitly,
   overriding any inline/inline-block default anchor behavior */
.about-video-thumb-wrap {
  display: block;
  position: relative;
  width: 100% !important;
  overflow: hidden;
  aspect-ratio: 9/16;
  max-height: 340px;
}

.about-video-thumb {
  display: block;
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
  transition: transform 0.4s ease;
}
.about-video-card:hover .about-video-thumb {
  transform: scale(1.06);
}
.about-video-thumb-placeholder {
  width: 100%; height: 100%;
  background: linear-gradient(135deg, var(--brand-teal-light), var(--brand-gold-light));
  display: flex; align-items: center; justify-content: center;
  font-size: 3rem;
  color: var(--brand);
}

/* Play overlay — always visible (no hover-only opacity) */
.about-video-play-overlay {
  position: absolute;
  inset: 0;
  background: rgba(8, 56, 68, 0.25);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 1;
}

/* Play button — always visible, centered, new bg color */
.about-video-play-btn {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: #116b83;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-size: 1.4rem;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
  transform: scale(1);
}
.about-video-card:hover .about-video-play-btn {
  transform: scale(1.08);
}

.about-video-info {
  padding: 1rem 1.2rem;
}
.about-video-title {
  font-weight: 600;
  color: var(--text-main);
  margin-bottom: 0.4rem;
  font-size: 0.95rem;
}
.about-video-link {
  font-size: 0.85rem;
  color: var(--brand);
  text-decoration: none;
  font-weight: 600;
}
.about-video-link:hover { color: var(--brand-gold); }

/* ─── Values ─── */
.about-values-section { background: var(--bg-accent); }
.about-value-card {
  background: #fff;
  border-radius: 16px;
  padding: 2rem 1.5rem;
  box-shadow: var(--shadow-sm);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  height: 100%;
}
.about-value-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--card-shadow);
}
.about-value-icon {
  width: 60px; height: 60px;
  background: var(--brand-teal-light);
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto 1rem;
  font-size: 1.5rem;
  color: var(--brand);
}
.about-value-title {
  font-family: 'Poppins', sans-serif;
  font-weight: 700;
  color: var(--text-main);
  margin-bottom: 0.5rem;
}
.about-value-text {
  color: var(--text-muted);
  font-size: 0.95rem;
  margin: 0;
}
</style>
@endpush

@endsection
