@extends('frontend.layout')

@section('meta_title', 'Member Login')
@section('meta_robots', 'noindex, nofollow')

@push('front_css')
    <style>
        .member-login .md-example-box {
            margin-top: 22px;
            padding: 16px;
            border-radius: 12px;
            background: var(--secondary-10);
            border: 1px dashed var(--secondary-30);
        }

        .member-login .md-example-box h6 {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #fff;
            margin-bottom: 10px;
        }

        .member-login .md-example-row {
            font-size: 0.82rem;
            color: var(--text-on-dark-muted);
            margin-bottom: 6px;
        }

        .member-login .md-example-row code {
            color: #fff;
            background: var(--card-dark-bg);
            border: 1px solid var(--shine-edge);
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.78rem;
        }
    </style>
@endpush

@section('frontend_content')
    <x-frontend.page-header title="মেম্বার লগইন" eyebrow="মেম্বার অ্যাক্সেস"
        subtitle="যেকোনো সময় আপনার অর্ডার ও মেম্বারশিপের বিস্তারিত দেখতে ড্যাশবোর্ডে প্রবেশ করুন।" :backLink="route('frontend.home')"
        backText="হোমে ফিরে যান">
        <x-slot:eyebrowIcon>
            <iconify-icon icon="solar:user-circle-bold"></iconify-icon>
        </x-slot:eyebrowIcon>
    </x-frontend.page-header>

    <section class="contact-page member-login" style="background: var(--brand-gredient); min-height: 60vh;">
        <div class="container px-4 px-lg-5 contact-page-main-box">
            <div class="contact-page-grid">
                <aside class="contact-page-info order-2 order-lg-1">
                    <div class="contact-page-info-panel">
                        <div class="contact-page-info-top">
                            <div>
                                <h2 class="contact-page-info-title">ডেগচি ডাইন</h2>
                                <p class="contact-page-info-tagline">আন্তরিক আতিথেয়তা · খাঁটি স্বাদ</p>
                            </div>
                            <span class="contact-page-open-badge"><i class="bi bi-shield-lock-fill me-1"></i> নিরাপদ
                                লগইন</span>
                        </div>

                        <div class="contact-page-cards">
                            <article class="contact-page-card">
                                <div class="contact-page-card-icon"><iconify-icon icon="solar:phone-bold"></iconify-icon>
                                </div>
                                <div class="contact-page-card-body">
                                    <h3>ফোন নম্বর</h3>
                                    <p>আপনার রেজিস্টার করা ফোন নম্বর ব্যবহার করুন</p>
                                </div>
                            </article>

                            <article class="contact-page-card">
                                <div class="contact-page-card-icon"><iconify-icon icon="solar:letter-bold"></iconify-icon>
                                </div>
                                <div class="contact-page-card-body">
                                    <h3>ইমেইল ঠিকানা</h3>
                                    <p>অথবা আপনার ইমেইল দিয়ে লগইন করুন</p>
                                </div>
                            </article>

                            <article class="contact-page-card">
                                <div class="contact-page-card-icon"><iconify-icon icon="solar:card-2-bold"></iconify-icon>
                                </div>
                                <div class="contact-page-card-body">
                                    <h3>কার্ড নম্বর</h3>
                                    <p>মেম্বারশিপ কার্ডের নম্বর দিয়েও লগইন করা যাবে</p>
                                </div>
                            </article>

                            <article class="contact-page-card contact-page-card-accent">
                                <div class="contact-page-card-icon contact-page-card-icon-gold"><iconify-icon
                                        icon="solar:key-bold"></iconify-icon></div>
                                <div class="contact-page-card-body">
                                    <h3>পাসওয়ার্ড</h3>
                                    <p>মেম্বারশিপের জন্য আবেদন করার সময় যে পাসওয়ার্ড সেট করেছেন</p>
                                </div>
                            </article>
                        </div>

                        <div class="md-example-box">
                            <h6>উদাহরণ</h6>
                            <div class="md-example-row">ফোন: <code>01712345678</code></div>
                            <div class="md-example-row">ইমেইল: <code>you@email.com</code></div>
                            <div class="md-example-row">কার্ড: <code>MEM0001_5678</code></div>
                        </div>

                        <div class="contact-page-actions">
                            <a href="{{ route('frontend.card.apply') }}" class="btn-dine btn-dine-primary btn-dine-sm">
                                <iconify-icon icon="solar:card-2-linear"></iconify-icon>
                                কার্ডের জন্য আবেদন করুন
                            </a>
                            <a href="{{ route('frontend.contact') }}" class="btn-dine btn-dine-ghost btn-dine-sm">
                                <iconify-icon icon="solar:chat-round-dots-bold"></iconify-icon>
                                যোগাযোগ করুন
                            </a>
                        </div>
                    </div>
                </aside>

                <div class="contact-page-forms order-1 order-lg-2">
                    <div class="contact-form-sticky">
                        <div class="cp-form-card cp-form-card-verify">
                            <div class="cp-form-card-head">
                                <span class="cp-form-step">মেম্বার পোর্টাল</span>
                                <h3>লগইন করুন</h3>
                                <p>নিচে আপনার ফোন, ইমেইল অথবা কার্ড নম্বর এবং পাসওয়ার্ড দিন।</p>
                            </div>

                            @if ($errors->any())
                                <div class="cp-alert-success mb-4" role="alert"
                                    style="background: rgba(239, 68, 68, 0.12); border-color: rgba(239, 68, 68, 0.3);">
                                    <i class="bi bi-exclamation-triangle-fill" style="color: var(--danger);"></i>
                                    <div>
                                        <strong style="color: var(--danger);">লগইন ব্যর্থ হয়েছে</strong>
                                        <span>{{ $errors->first() }}</span>
                                    </div>
                                </div>
                            @endif

                            @if (session('success'))
                                <div class="cp-alert-success mb-4" role="alert">
                                    <i class="bi bi-shield-check"></i>
                                    <div>
                                        <strong>সফল হয়েছে</strong>
                                        <span>{{ session('success') }}</span>
                                    </div>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('frontend.member.login.submit') }}">
                                @csrf

                                <div class="cp-field mb-4">
                                    <label for="member_login" class="cp-label">ফোন, ইমেইল অথবা কার্ড নম্বর</label>
                                    <input type="text" name="login" id="member_login" class="cp-input"
                                        placeholder="যেমন: 01712345678 অথবা you@email.com" value="{{ old('login') }}"
                                        required autofocus autocomplete="username">
                                </div>

                                <div class="cp-field mb-4">
                                    <label for="member_password" class="cp-label">পাসওয়ার্ড</label>
                                    <input type="password" name="password" id="member_password" class="cp-input"
                                        placeholder="আপনার পাসওয়ার্ড দিন" required autocomplete="current-password">
                                </div>

                                <div class="d-flex align-items-center justify-content-between gap-3 mb-4 flex-wrap">
                                    <div class="form-check"
                                        style="background: var(--secondary-10); border: 1px solid var(--shine-edge); border-radius: 10px; padding: 0.65rem 0.85rem; flex: 1 1 auto; min-width: 0;">
                                        <input class="form-check-input" type="checkbox" name="remember" id="member_remember"
                                            value="1" style="border-color: var(--brand);">
                                        <label class="form-check-label" for="member_remember"
                                            style="font-size: 0.88rem; color: #fff; cursor: pointer;">লগইন মনে রাখুন</label>
                                    </div>
                                    <a href="{{ route('frontend.member.password.request') }}" class="cp-link-gold"
                                        style="font-size: 0.88rem; font-weight: 600; white-space: nowrap;">
                                        পাসওয়ার্ড ভুলে গেছেন? <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>

                                <button type="submit" class="btn-dine btn-dine-primary btn-dine-sm w-100">
                                    <span>ড্যাশবোর্ডে লগইন করুন</span>
                                    <iconify-icon icon="solar:login-2-linear"></iconify-icon>
                                </button>
                            </form>

                            <p class="cp-form-footnote text-center mb-0 mt-4">
                                এখনও মেম্বারশিপ নেননি?
                                <a href="{{ route('frontend.card.apply') }}" class="cp-link-gold">
                                    মেম্বারশিপের জন্য আবেদন করুন <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
