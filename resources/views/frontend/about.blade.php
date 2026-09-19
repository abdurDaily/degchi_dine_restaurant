@extends('frontend.layout')

@section('meta_title', 'About Us — Degchi Dine')
@section('meta_description', 'Learn about Degchi Dine — our heritage, story, authentic Dum-style cooking and the passion behind every clay-pot meal.')

@section('frontend_content')

<x-frontend.page-header
    title="About Degchi Dine"
    eyebrow="Our Stories"
    subtitle="Discover the heritage, passion, and authentic Dum-style cooking traditions behind every clay-pot meal at Degchi Dine."
/>

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
