@extends('frontend.layout')

@section('meta_title', 'Track Your Order')
@section('meta_description', 'Track your Degchi Dine order status online using your order number and phone number.')
@section('meta_robots', 'noindex, follow')

@section('frontend_content')

<x-frontend.page-header
    title="Track Your Order"
    eyebrow="Order Tracking"
    subtitle="Find your order status anytime using your order number and phone."
    :backLink="route('frontend.home')"
    backText="Back to Home"
>
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
                            <p class="contact-page-info-tagline">Quick &amp; Easy Order Lookup</p>
                        </div>
                        <span class="contact-page-open-badge"><i class="bi bi-truck me-1"></i> Live Tracking</span>
                    </div>

                    <div class="contact-page-cards">
                        <article class="contact-page-card">
                            <div class="contact-page-card-icon"><i class="bi bi-1-circle-fill"></i></div>
                            <div class="contact-page-card-body">
                                <h3>Find your order number</h3>
                                <p>From your confirmation page or SMS — e.g. Order #6</p>
                            </div>
                        </article>

                        <article class="contact-page-card">
                            <div class="contact-page-card-icon"><i class="bi bi-2-circle-fill"></i></div>
                            <div class="contact-page-card-body">
                                <h3>Enter your phone number</h3>
                                <p>Must match the number used when you placed the order</p>
                            </div>
                        </article>

                        <article class="contact-page-card">
                            <div class="contact-page-card-icon"><i class="bi bi-3-circle-fill"></i></div>
                            <div class="contact-page-card-body">
                                <h3>View full confirmation</h3>
                                <p>See items, total, status, and contact info for help</p>
                            </div>
                        </article>

                        <article class="contact-page-card contact-page-card-accent">
                            <div class="contact-page-card-icon contact-page-card-icon-gold"><i class="bi bi-info-circle-fill"></i></div>
                            <div class="contact-page-card-body">
                                <h3>Example</h3>
                                <p>Order: <code style="background: var(--secondary-10); padding: 2px 6px; border-radius: 4px;">6</code> &nbsp; Phone: <code style="background: var(--secondary-10); padding: 2px 6px; border-radius: 4px;">01712345678</code></p>
                            </div>
                        </article>
                    </div>

                    <p class="mt-3 mb-0" style="font-size: 0.82rem; color: #fff;">
                        @guest('member')
                        Registered members can <a href="{{ route('frontend.member.login') }}" style="color: var(--brand-secondary); font-weight: 600;">sign in</a> to see all orders in one dashboard.
                        @else
                        View all orders on your <a href="{{ route('frontend.member.dashboard') }}" style="color: var(--brand-gold); font-weight: 600;">member dashboard</a>.
                        @endguest
                    </p>
                </div>
            </aside>

            <div class="contact-page-forms order-1 order-lg-2">
                <div class="contact-form-sticky">
                    <div class="cp-form-card cp-form-card-verify">
                        <div class="cp-form-card-head">
                            <span class="cp-form-step">Look Up</span>
                            <h3>Track Your Order</h3>
                            <p>Enter your details below to view order confirmation.</p>
                        </div>

                        @auth('member')
                        <div class="cp-alert-success mb-4" role="alert">
                            <i class="bi bi-person-check"></i>
                            <div>
                                <strong>Signed in as {{ $member->name }}</strong>
                                <span>Your phone is pre-filled. Enter an order number to view it, or <a href="{{ route('frontend.member.dashboard') }}" style="color: var(--brand);">view all orders</a>.</span>
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
                            <div class="cp-alert-success mb-4" role="alert" style="background: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.2); color: #dc2626;">
                                <i class="bi bi-exclamation-triangle"></i>
                                <div><span>{{ $errors->first() }}</span></div>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('frontend.order.track.submit') }}">
                            @csrf

                            <div class="cp-field mb-4">
                                <label for="track_order_id" class="cp-label">Order Number *</label>
                                <input type="number" name="order_id" id="track_order_id" class="cp-input" required min="1" placeholder="Ex: 6" value="{{ old('order_id', $prefillOrderId ?? '') }}">
                            </div>

                            <div class="cp-field mb-4">
                                <label for="track_phone" class="cp-label">Phone Number at Checkout *</label>
                                <input type="tel" name="phone" id="track_phone" class="cp-input" placeholder="Ex: 01712345678" value="{{ old('phone', $prefillPhone ?? '') }}" @guest('member') required @endguest @auth('member') @if($prefillPhone) readonly @endif @endauth>
                            </div>

                            @auth('member')
                            <p class="mb-3" style="font-size: 0.78rem; color: var(--text-muted); margin-top: -12px;">Optional if the order is linked to your account. We use your member phone by default.</p>
                            @endauth

                            <button type="submit" class="btn-dine btn-dine-primary btn-dine-sm w-100">
                                <i class="bi bi-search me-2"></i>
                                <span>Track My Order</span>
                            </button>
                        </form>

                        <div class="text-center mt-4 pt-3" style="border-top: 1px solid var(--border-color);">
                            @guest('member')
                            <p class="mb-2" style="font-size: 0.85rem; color: var(--text-muted);">Order often? Get a membership card.</p>
                            <a href="{{ route('frontend.card.apply') }}" class="btn-dine btn-dine-outline btn-dine-sm me-2">Apply Now</a>
                            <a href="{{ route('frontend.member.login') }}" class="btn-dine btn-dine-ghost btn-dine-sm">Member Login</a>
                            @else
                            <a href="{{ route('frontend.member.dashboard') }}" class="btn-dine btn-dine-ghost btn-dine-sm">
                                <i class="bi bi-speedometer2 me-1"></i> Go to My Dashboard
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