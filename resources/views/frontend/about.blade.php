@extends('frontend.layout')

@section('meta_title', 'About Us — Degchi Dine')
@section('meta_description', 'Learn about Degchi Dine — our heritage, story, authentic Dum-style cooking and the passion behind every clay-pot meal.')

@section('frontend_content')

@php
  $aboutKicker     = optional($aboutSettings->get('about_kicker'))->value ?? 'Our Heritage';
  $aboutTitle      = optional($aboutSettings->get('about_title'))->value ?? 'The Story of Degchi Dine';
  $aboutLead       = optional($aboutSettings->get('about_lead'))->value ?? 'Bringing the authentic, slow-cooked royal culinary traditions of heritage clay-pot dining straight to your contemporary table.';
  $aboutParagraph  = optional($aboutSettings->get('about_paragraph'))->value ?? 'At Degchi Dine, every recipe tells a story of patience, craft, and passion. We specialize in traditional Dum cooking methods—where premium cuts of meat, fragrant basmati rice, and freshly crushed spice masalas are sealed tightly inside heavy vessels, allowing the ingredients to mature perfectly in their own steam.';
  $aboutFeature1Icon = optional($aboutSettings->get('about_feature_1_icon'))->value ?? 'bi bi-fire';
  $aboutFeature1Text = optional($aboutSettings->get('about_feature_1_text'))->value ?? 'Authentic Dum Style';
  $aboutFeature2Icon = optional($aboutSettings->get('about_feature_2_icon'))->value ?? 'bi bi-patch-check-fill';
  $aboutFeature2Text = optional($aboutSettings->get('about_feature_2_text'))->value ?? 'Premium Ingredients';
  $aboutExpNumber  = optional($aboutSettings->get('about_exp_number'))->value ?? '10+';
  $aboutExpText    = optional($aboutSettings->get('about_exp_text'))->value ?? 'Years Of Culinary Craft';
  $aboutCtaUrl     = optional($aboutSettings->get('about_cta_url'))->value ?? route('frontend.completeMenu');
  $aboutImage      = optional($aboutSettings->get('about_image'))->value
    ? asset('uploads/about/' . optional($aboutSettings->get('about_image'))->value)
    : asset('assets/frontend/images/about/about.jpg');
@endphp

{{-- ─── Page hero banner ─── --}}
<div class="about-page-hero position-relative overflow-hidden">
  <div class="about-hero-overlay"></div>
  <div class="container px-4 px-lg-5 position-relative text-center py-5">
    <span class="about-kicker text-uppercase d-block mb-3 text-white">
      <i class="bi bi-heart-fill me-1" aria-hidden="true"></i> {{ $aboutKicker }}
    </span>
    <h1 class="about-hero-title">{{ $aboutTitle }}</h1>
    <p class="about-hero-sub mx-auto">{{ $aboutLead }}</p>
  </div>
</div>

{{-- ─── About Content Section ─── --}}
<section class="section-block py-5 about-section about-page" id="about">
  <div class="container px-4 px-lg-5">
    <div class="row align-items-center g-4 g-lg-5">

      {{-- Left: text content --}}
      <div class="col-12 col-lg-6 reveal">
        <div class="about-content-block">
          <h2 class="section-title mt-2 mb-3">{{ $aboutTitle }}</h2>
          <p class="about-lead mb-4">{{ $aboutLead }}</p>
          <div class="about-paragraph mb-4">{!! $aboutParagraph !!}</div>

          <div class="about-features-grid mb-4">
            <div class="about-feature-item">
              <div class="feature-icon-box"><i class="{{ $aboutFeature1Icon }}"></i></div>
              <span class="feature-text text-uppercase">{{ $aboutFeature1Text }}</span>
            </div>
            <div class="about-feature-item">
              <div class="feature-icon-box"><i class="{{ $aboutFeature2Icon }}"></i></div>
              <span class="feature-text text-uppercase">{{ $aboutFeature2Text }}</span>
            </div>
          </div>

          <div class="about-cta-wrap">
            <a href="{{ $aboutCtaUrl }}" class="btn about-explore-btn">
              <span>Order Now <i class="bi bi-arrow-right ms-2"></i></span>
            </a>
          </div>
        </div>
      </div>

      {{-- Right: image with badge --}}
      <div class="col-12 col-lg-6 reveal">
        <div class="about-media-frame position-relative">
          <div class="about-shape-backdrop"></div>
          <div class="about-img-container">
            <img src="{{ $aboutImage }}"
                 alt="About Degchi Dine"
                 class="about-main-img"
                 onerror="this.src='{{ asset('assets/frontend/images/about.png') }}'" />
            <div class="about-img-overlay"></div>
          </div>
          <div class="about-experience-badge text-center">
            <span class="exp-number">{{ $aboutExpNumber }}</span>
            <span class="exp-text text-uppercase">{{ $aboutExpText }}</span>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

{{-- ─── Video Showcase Section ─── --}}
@if($videos->isNotEmpty())
<section class="about-videos-section py-5" id="videos">
  <div class="container px-4 px-lg-5">

    <div class="text-center mb-5 reveal">
      <span class="about-kicker text-uppercase d-block mb-2">
        <i class="bi bi-play-circle-fill me-1"></i> Watch & Experience
      </span>
      <h2 class="section-title">Behind the Scenes</h2>
      <p class="about-lead mx-auto" style="max-width: 600px;">
        Get a glimpse of our kitchen, our craft, and our community straight from our social reels.
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
/* ─── Hero ─── */
.about-page-hero {
  background: linear-gradient(135deg, var(--brand-dark) 0%, var(--brand) 60%, var(--brand-teal-dark) 100%);
  padding: 100px 0 80px;
}
.about-hero-overlay {
  position: absolute; inset: 0;
  background: url('{{ asset('assets/frontend/images/about/about.jpg') }}') center/cover no-repeat;
  opacity: 0.08;
  pointer-events: none;
}
.about-hero-title {
  font-family: 'Poppins', sans-serif;
  font-size: clamp(2rem, 4vw, 3rem);
  font-weight: 700;
  color: #fff;
  margin-bottom: 1rem;
}
.about-hero-sub {
  font-size: 1.1rem;
  color: rgba(255,255,255,0.8);
  max-width: 640px;
}

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
.about-video-thumb-wrap {
  display: block;
  position: relative;
  overflow: hidden;
  aspect-ratio: 9/16;
  max-height: 340px;
}
.about-video-thumb {
  width: 100%;
  height: 100%;
  object-fit: cover;
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
.about-video-play-overlay {
  position: absolute; inset: 0;
  background: rgba(8, 56, 68, 0.35);
  display: flex; align-items: center; justify-content: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}
.about-video-card:hover .about-video-play-overlay { opacity: 1; }
.about-video-play-btn {
  width: 60px; height: 60px;
  border-radius: 50%;
  background: var(--brand-gold);
  display: flex; align-items: center; justify-content: center;
  color: #fff;
  font-size: 1.4rem;
  box-shadow: 0 4px 16px rgba(0,0,0,0.3);
  transform: scale(0.85);
  transition: transform 0.3s ease;
}
.about-video-card:hover .about-video-play-btn { transform: scale(1); }
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
