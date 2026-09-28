@extends('frontend.layout')

@section('meta_title', 'About Us — Degchi Dine')
@section('meta_description', 'Learn about Degchi Dine — our heritage, story, authentic Dum-style cooking and the passion
    behind every clay-pot meal.')

@section('frontend_content')

    <x-frontend.page-header title="ডেগচি ডাইন সম্পর্কে" eyebrow="আমাদের গল্প"
        subtitle="আসল স্বাদ, ঐতিহ্যবাহী রান্না এবং অবিস্মরণীয় মুহূর্তের প্রতি ভালোবাসা।" />

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
                        <h5 class="dark-glass-card-title">ঐতিহ্যবাহী স্বাদ</h5>
                        <p class="dark-glass-card-text">ঐতিহ্যবাহী রান্নার পদ্ধতি ও ঘরোয়া রেসিপির ছোঁয়ায় তৈরি প্রতিটি পদে
                            পাবেন আসল স্বাদের অভিজ্ঞতা।</p>
                    </div>
                </div>
                <div class="col-12 col-md-4 reveal">
                    <div class="dark-glass-card">
                        <div class="dark-glass-card-icon"><i class="bi bi-people-fill"></i></div>
                        <h5 class="dark-glass-card-title">আন্তরিক আতিথেয়তা</h5>
                        <p class="dark-glass-card-text">শুধু খাবার নয়, প্রতিটি অতিথির জন্য আমরা তৈরি করি আপনজনের মতো উষ্ণ ও
                            আন্তরিক পরিবেশ।</p>
                    </div>
                </div>
                <div class="col-12 col-md-4 reveal">
                    <div class="dark-glass-card">
                        <div class="dark-glass-card-icon"><i class="bi bi-stars"></i></div>
                        <h5 class="dark-glass-card-title">সেরা মানের নিশ্চয়তা</h5>
                        <p class="dark-glass-card-text">বাছাই করা উপকরণ, তাজা পণ্য ও মানসম্মত মসলার সমন্বয়ে প্রতিটি পদে
                            নিশ্চিত করি স্বাদ ও গুণগত মান।</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
