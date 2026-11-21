@extends('frontend.layout')

@section('meta_title', 'Track Your Order')
@section('meta_description', 'Track your Degchi Dine order status online using your order number and phone number.')
@section('meta_robots', 'noindex, follow')

@section('frontend_content')

    <x-frontend.page-header title="Track Order" eyebrow="Find Your Order"
        subtitle="Check the current status of your order at any time using your order number and phone number." :backLink="route('frontend.home')"
        backText="Back to Home">
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
                                <h2 class="contact-page-info-title">How It Works</h2>
                                <p class="contact-page-info-tagline">Find Your Order Easily</p>
                            </div>
                            <span class="contact-page-open-badge">
                                <i class="bi bi-truck me-1"></i> Live Tracking
                            </span>
                        </div>

                        <div class="contact-page-cards">
                            <article class="contact-page-card">
                                <div class="contact-page-card-icon">
                                    <i class="bi bi-1-circle-fill"></i>
                                </div>
                                <div class="contact-page-card-body">
                                    <h3>Find Your Order Number</h3>
                                    <p>Get your order number from the order confirmation page or SMS — e.g. Order #6</p>
                                </div>
                            </article>

                            <article class="contact-page-card">
                                <div class="contact-page-card-icon">
                                    <i class="bi bi-2-circle-fill"></i>
                                </div>
                                <div class="contact-page-card-body">
                                    <h3>Enter Your Phone Number</h3>
                                    <p>Use the same phone number you used when placing your order.</p>
                                </div>
                            </article>

                            <article class="contact-page-card">
                                <div class="contact-page-card-icon">
                                    <i class="bi bi-3-circle-fill"></i>
                                </div>
                                <div class="contact-page-card-body">
                                    <h3>View Order Details</h3>
                                    <p>See your food, total bill, current order status, and contact details if needed.</p>
                                </div>
                            </article>

                            <article class="contact-page-card contact-page-card-accent">
                                <div class="contact-page-card-icon contact-page-card-icon-gold">
                                    <i class="bi bi-info-circle-fill"></i>
                                </div>
                                <div class="contact-page-card-body">
                                    <h3>Example</h3>
                                    <p>
                                        Order:
                                        <span
                                            style="background: var(--secondary-30); color: #fff; padding: 2px 6px; border-radius: 4px;">6</span>
                                        &nbsp;
                                        Phone:
                                        <span
                                            style="background: var(--secondary-30); color: #fff; padding: 2px 6px; border-radius: 4px;">017123**678</span>
                                    </p>
                                </div>
                            </article>
                        </div>

                        <p class="mt-3 mb-0" style="font-size: 0.82rem; color: #fff;">
                            @guest('member')
                                Registered members can
                                <a href="{{ route('frontend.member.login') }}"
                                    style="color: var(--brand-secondary); font-weight: 600;">
                                    Log In
                                </a>
                                view all orders from a single dashboard.
                            @else
                                View all orders from your
                                <a href="{{ route('frontend.member.dashboard') }}"
                                    style="color: var(--brand-gold); font-weight: 600;">
                                    Member Dashboard
                                </a>.
                            @endguest
                        </p>
                    </div>
                </aside>

                <div class="contact-page-forms order-1 order-lg-2">
                    <div class="contact-form-sticky">
                        <div class="cp-form-card cp-form-card-verify">
                            <div class="cp-form-card-head">
                                <span class="cp-form-step">Find Order</span>
                                <h3>Track Order</h3>
                                <p>Enter the details below to view your order.</p>
                            </div>

                            @auth('member')
                                <div class="cp-alert-success text-white mb-4" role="alert">
                                    <i class="bi bi-person-check"></i>
                                    <div>
                                        <strong>Logged in as {{ $member->name }}</strong>
                                        <span>
                                            Your phone number is already provided. Just enter the order number to view
                                            details, or
                                            <a href="{{ route('frontend.member.dashboard') }}" style="color: var(--brand);">
                                                view all orders
                                            </a>.
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
                                    <label for="track_order_id" class="cp-label">Order Number *</label>
                                    <input type="number" name="order_id" id="track_order_id" class="cp-input" required
                                        min="1" placeholder="e.g. 6"
                                        value="{{ old('order_id', $prefillOrderId ?? '') }}">
                                </div>

                                <div class="cp-field mb-4">
                                    <label for="track_phone" class="cp-label">Phone Number Used for the Order *</label>
                                    <input type="tel" name="phone" id="track_phone" class="cp-input"
                                        placeholder="e.g. 01712345678" value="{{ old('phone', $prefillPhone ?? '') }}"
                                        @guest('member') required @endguest
                                        @auth('member') @if ($prefillPhone) readonly @endif @endauth>
                                </div>

                                @auth('member')
                                    <p class="mb-3 text-white" style="font-size: 0.78rem; margin-top: -12px;">
                                        If the order is linked to your account, the phone number is optional.
                                        Your member number will be used by default.
                                    </p>
                                @endauth

                                <button type="submit" class="btn-dine btn-dine-primary btn-dine-sm w-100">
                                    <i class="bi bi-search me-2"></i>
                                    <span>Track Order</span>
                                </button>
                            </form>

                            <div class="text-center mt-4 pt-3" style="border-top: 1px solid var(--border-color);">
                                @guest('member')
                                    <p class="mb-2" style="font-size: 0.85rem; color:#fff">
                                        Order regularly? Get a membership card.
                                    </p>

                                    <a href="{{ route('frontend.card.apply') }}"
                                        class="btn-dine btn-dine-outline btn-dine-sm me-2">
                                        Apply
                                    </a>

                                    <a href="{{ route('frontend.member.login') }}" class="btn-dine btn-dine-ghost btn-dine-sm">
                                        Member Login
                                    </a>
                                @else
                                    <a href="{{ route('frontend.member.dashboard') }}"
                                        class="btn-dine btn-dine-ghost btn-dine-sm">
                                        <i class="bi bi-speedometer2 me-1"></i>
                                        My Dashboard
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
