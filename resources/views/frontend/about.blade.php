@extends('frontend.layout')

@section('meta_title', 'About Us')
@section('meta_description',
    'Learn about Degchi Dine — our heritage, story, authentic Dum-style cooking and the passion
    behind every clay-pot meal.')

@section('frontend_content')

    <x-frontend.page-header title="About Degchi Dine" eyebrow=""
        subtitle="A love for authentic flavours, traditional cooking and unforgettable moments." />

    {{-- ─── About Content Section (shared partial) ─── --}}
    @include('frontend.partials.home.about_partials', ['ctaText' => 'Contact Us', 'aboutPage' => true])

    {{-- ─── Video Showcase Section (Reels) ─── --}}
    @if ($facebookReels->isNotEmpty())
        @include('frontend.partials.home.reels')
    @endif

    {{-- ─── Values / Mission Strip ─── --}}
    <section class="about-values-section section-block py-5">
        <div class="container px-4 px-lg-5">
            <div class="row g-4 text-center">
                <div class="col-12 col-md-4 reveal">
                    <div class="dark-glass-card">
                        <div class="dark-glass-card-icon"><i class="bi bi-award-fill"></i></div>
                        <h5 class="dark-glass-card-title">Traditional Flavours</h5>
                        <p class="dark-glass-card-text">Every dish is made with traditional cooking methods and a touch of
                            home-style recipes, for a truly authentic taste.</p>
                    </div>
                </div>
                <div class="col-12 col-md-4 reveal">
                    <div class="dark-glass-card">
                        <div class="dark-glass-card-icon"><i class="bi bi-people-fill"></i></div>
                        <h5 class="dark-glass-card-title">Warm Hospitality</h5>
                        <p class="dark-glass-card-text">We offer more than just food — for every guest, we create a warm,
                            heartfelt atmosphere that feels like family.</p>
                    </div>
                </div>
                <div class="col-12 col-md-4 reveal">
                    <div class="dark-glass-card">
                        <div class="dark-glass-card-icon"><i class="bi bi-stars"></i></div>
                        <h5 class="dark-glass-card-title">Guaranteed Quality</h5>
                        <p class="dark-glass-card-text">With carefully selected ingredients, fresh produce and quality
                            spices in every dish, we ensure great taste and consistent quality.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
