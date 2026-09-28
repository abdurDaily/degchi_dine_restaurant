@extends('frontend.layout')

@section('meta_title', 'Track Your Order')
@section('meta_description', 'Track your Degchi Dine order status online using your order number and phone number.')
@section('meta_robots', 'noindex, follow')

@section('frontend_content')

    <x-frontend.page-header title="অর্ডার ট্র্যাক করুন" eyebrow="অর্ডারের খোঁজ নিন"
        subtitle="আপনার অর্ডার নম্বর ও ফোন নম্বর ব্যবহার করে যেকোনো সময় অর্ডারের বর্তমান অবস্থা দেখুন।" :backLink="route('frontend.home')"
        backText="হোমে ফিরে যান">
        <x-slot:eyebrowIcon>
            <iconify-icon icon="solar:delivery-bold"></iconify-icon>
        </x-slot:eyebrowIcon>
    </x-frontend.page-header>

    <section class="contact-page" style="background: var(--brand-gredient); min-height: 60vh;">
        <div class="container px-4 px-lg-5 contact-page-main-box">
            <div class="contact-page-grid">
                <aside class="contact-page-info order-2 order-lg-1">
                    <div class="contact-page-info-panel">
                        <div class="contact-page-info-top">
                            <div>
                                <h2 class="contact-page-info-title">যেভাবে কাজ করে</h2>
                                <p class="contact-page-info-tagline">সহজেই অর্ডারের খোঁজ নিন</p>
                            </div>
                            <span class="contact-page-open-badge">
                                <i class="bi bi-truck me-1"></i> লাইভ ট্র্যাকিং
                            </span>
                        </div>

                        <div class="contact-page-cards">
                            <article class="contact-page-card">
                                <div class="contact-page-card-icon">
                                    <i class="bi bi-1-circle-fill"></i>
                                </div>
                                <div class="contact-page-card-body">
                                    <h3>অর্ডার নম্বর খুঁজে নিন</h3>
                                    <p>অর্ডার কনফার্মেশন পেজ বা SMS থেকে আপনার অর্ডার নম্বরটি নিন — যেমন: Order #6</p>
                                </div>
                            </article>

                            <article class="contact-page-card">
                                <div class="contact-page-card-icon">
                                    <i class="bi bi-2-circle-fill"></i>
                                </div>
                                <div class="contact-page-card-body">
                                    <h3>ফোন নম্বর দিন</h3>
                                    <p>অর্ডার করার সময় যে ফোন নম্বরটি ব্যবহার করেছেন, সেটিই ব্যবহার করুন।</p>
                                </div>
                            </article>

                            <article class="contact-page-card">
                                <div class="contact-page-card-icon">
                                    <i class="bi bi-3-circle-fill"></i>
                                </div>
                                <div class="contact-page-card-body">
                                    <h3>অর্ডারের বিস্তারিত দেখুন</h3>
                                    <p>আপনার খাবার, মোট বিল, অর্ডারের বর্তমান অবস্থা এবং প্রয়োজনে যোগাযোগের তথ্য দেখুন।</p>
                                </div>
                            </article>

                            <article class="contact-page-card contact-page-card-accent">
                                <div class="contact-page-card-icon contact-page-card-icon-gold">
                                    <i class="bi bi-info-circle-fill"></i>
                                </div>
                                <div class="contact-page-card-body">
                                    <h3>উদাহরণ</h3>
                                    <p>
                                        অর্ডার:
                                        <code
                                            style="background: var(--secondary-10); padding: 2px 6px; border-radius: 4px;">6</code>
                                        &nbsp;
                                        ফোন:
                                        <code
                                            style="background: var(--secondary-10); padding: 2px 6px; border-radius: 4px;">01712345678</code>
                                    </p>
                                </div>
                            </article>
                        </div>

                        <p class="mt-3 mb-0" style="font-size: 0.82rem; color: #fff;">
                            @guest('member')
                                রেজিস্টার করা সদস্যরা
                                <a href="{{ route('frontend.member.login') }}"
                                    style="color: var(--brand-secondary); font-weight: 600;">
                                    লগইন
                                </a>
                                করে একটি ড্যাশবোর্ড থেকেই সব অর্ডার দেখতে পারবেন।
                            @else
                                আপনার
                                <a href="{{ route('frontend.member.dashboard') }}"
                                    style="color: var(--brand-gold); font-weight: 600;">
                                    মেম্বার ড্যাশবোর্ড
                                </a>
                                থেকে সব অর্ডার দেখুন।
                            @endguest
                        </p>
                    </div>
                </aside>

                <div class="contact-page-forms order-1 order-lg-2">
                    <div class="contact-form-sticky">
                        <div class="cp-form-card cp-form-card-verify">
                            <div class="cp-form-card-head">
                                <span class="cp-form-step">অর্ডার খুঁজুন</span>
                                <h3>অর্ডার ট্র্যাক করুন</h3>
                                <p>অর্ডারের বিস্তারিত দেখতে নিচের তথ্যগুলো দিন।</p>
                            </div>

                            @auth('member')
                                <div class="cp-alert-success text-white mb-4" role="alert">
                                    <i class="bi bi-person-check"></i>
                                    <div>
                                        <strong>{{ $member->name }} হিসেবে লগইন করা আছে</strong>
                                        <span>
                                            আপনার ফোন নম্বর দেওয়া আছে। অর্ডারের বিস্তারিত দেখতে শুধু অর্ডার নম্বর দিন,
                                            অথবা
                                            <a href="{{ route('frontend.member.dashboard') }}" style="color: var(--brand);">
                                                সব অর্ডার দেখুন
                                            </a>।
                                        </span>
                                    </div>
                                </div>
                            @endauth

                            @if (session('info'))
                                <div class="cp-alert-success mb-4" role="alert">
                                    <i class="bi bi-info-circle"></i>
                                    <div><span>{{ session('info') }}</span></div>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="cp-alert-success mb-4" role="alert"
                                    style="background: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.2); color: #dc2626;">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    <div><span>{{ $errors->first() }}</span></div>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('frontend.order.track.submit') }}">
                                @csrf

                                <div class="cp-field mb-4">
                                    <label for="track_order_id" class="cp-label">অর্ডার নম্বর *</label>
                                    <input type="number" name="order_id" id="track_order_id" class="cp-input" required
                                        min="1" placeholder="যেমন: 6"
                                        value="{{ old('order_id', $prefillOrderId ?? '') }}">
                                </div>

                                <div class="cp-field mb-4">
                                    <label for="track_phone" class="cp-label">অর্ডারের সময় ব্যবহৃত ফোন নম্বর *</label>
                                    <input type="tel" name="phone" id="track_phone" class="cp-input"
                                        placeholder="যেমন: 01712345678" value="{{ old('phone', $prefillPhone ?? '') }}"
                                        @guest('member') required @endguest
                                        @auth('member') @if ($prefillPhone) readonly @endif @endauth>
                                </div>

                                @auth('member')
                                    <p class="mb-3 text-white" style="font-size: 0.78rem; margin-top: -12px;">
                                        আপনার অ্যাকাউন্টের সাথে অর্ডার যুক্ত থাকলে ফোন নম্বর দেওয়া ঐচ্ছিক।
                                        ডিফল্টভাবে আপনার সদস্য নম্বরটি ব্যবহার করা হবে।
                                    </p>
                                @endauth

                                <button type="submit" class="btn-dine btn-dine-primary btn-dine-sm w-100">
                                    <i class="bi bi-search me-2"></i>
                                    <span>অর্ডার ট্র্যাক করুন</span>
                                </button>
                            </form>

                            <div class="text-center mt-4 pt-3" style="border-top: 1px solid var(--border-color);">
                                @guest('member')
                                    <p class="mb-2" style="font-size: 0.85rem; color:#fff">
                                        নিয়মিত অর্ডার করেন? মেম্বারশিপ কার্ড নিন।
                                    </p>

                                    <a href="{{ route('frontend.card.apply') }}"
                                        class="btn-dine btn-dine-outline btn-dine-sm me-2">
                                        আবেদন করুন
                                    </a>

                                    <a href="{{ route('frontend.member.login') }}" class="btn-dine btn-dine-ghost btn-dine-sm">
                                        মেম্বার লগইন
                                    </a>
                                @else
                                    <a href="{{ route('frontend.member.dashboard') }}"
                                        class="btn-dine btn-dine-ghost btn-dine-sm">
                                        <i class="bi bi-speedometer2 me-1"></i>
                                        আমার ড্যাশবোর্ড
                                    </a>
                                @endguest
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
