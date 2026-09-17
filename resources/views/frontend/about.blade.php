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

{{-- ─── Video Showcase Section (Reels) ─── --}}
@if($facebookReels->isNotEmpty())
@include('frontend.partials.home.reels')
@endif

{{-- ─── Values / Mission Strip ─── --}}
<section class="about-values-section section-block py-5">
  <div class="container px-4 px-lg-5">
    <div class="row g-4 text-center">
      <div class="col-12 col-md-4 reveal">
        <div class="dark-glass-card">
          <div class="dark-glass-card-icon"><i class="bi bi-award-fill"></i></div>
          <h5 class="dark-glass-card-title">Authentic Recipes</h5>
          <p class="dark-glass-card-text">Rooted in traditional Dum cooking — every dish is slow-cooked to perfection in clay vessels.</p>
        </div>
      </div>
      <div class="col-12 col-md-4 reveal">
        <div class="dark-glass-card">
          <div class="dark-glass-card-icon"><i class="bi bi-people-fill"></i></div>
          <h5 class="dark-glass-card-title">Warm Hospitality</h5>
          <p class="dark-glass-card-text">We don't just serve food — we create experiences. Every guest is welcomed like family.</p>
        </div>
      </div>
      <div class="col-12 col-md-4 reveal">
        <div class="dark-glass-card">
          <div class="dark-glass-card-icon"><i class="bi bi-stars"></i></div>
          <h5 class="dark-glass-card-title">Premium Quality</h5>
          <p class="dark-glass-card-text">Only the finest cuts, freshest produce, and authentic spice blends make it to your plate.</p>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection
