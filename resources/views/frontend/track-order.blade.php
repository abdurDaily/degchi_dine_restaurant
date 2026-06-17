@extends('frontend.layout')

@section('meta_title', 'Track Your Order')
@section('meta_description', 'Track your Degchi Dine order status online using your order number and phone number.')
@section('meta_robots', 'noindex, follow')

@push('front_css')
<style>
.track-order .to-main-box { margin-top: -100px; position: relative; z-index: 2; }
.track-order .to-grid {
    display: grid; grid-template-columns: 1fr 1fr; gap: 0;
    background: #fff; border-radius: 20px; overflow: hidden;
    box-shadow: 0 24px 60px rgba(17, 107, 131, 0.12); border: 1px solid var(--dd-border);
}
.track-order .to-help-side {
    background: linear-gradient(160deg, #083844 0%, #116b83 100%);
    padding: 40px 36px; color: #fff;
}
.track-order .to-help-side h3 { font-size: 1.45rem; font-weight: 700; margin-bottom: 8px; color: #fff; }
.track-order .to-help-side > p { color: rgba(255,255,255,0.75); font-size: 0.92rem; line-height: 1.6; margin-bottom: 28px; }
.track-order .to-step {
    display: flex; gap: 14px; margin-bottom: 20px; align-items: flex-start;
}
.track-order .to-step-num {
    width: 34px; height: 34px; border-radius: 50%;
    background: rgba(231, 174, 7, 0.18); border: 1px solid rgba(231, 174, 7, 0.45);
    color: var(--dd-gold); font-weight: 700; font-size: 0.85rem;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.track-order .to-step-text strong { display: block; font-size: 0.92rem; color: #fff; margin-bottom: 2px; }
.track-order .to-step-text span { font-size: 0.82rem; color: rgba(255,255,255,0.7); line-height: 1.5; }
.track-order .to-example {
    margin-top: 24px; padding: 16px; border-radius: 12px;
    background: rgba(231, 174, 7, 0.1); border: 1px dashed rgba(231, 174, 7, 0.4);
}
.track-order .to-example h6 {
    font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.08em;
    color: var(--dd-gold); margin-bottom: 10px;
}
.track-order .to-example-row { font-size: 0.82rem; color: rgba(255,255,255,0.8); margin-bottom: 5px; }
.track-order .to-example-row code {
    color: var(--dd-gold); background: rgba(0,0,0,0.2);
    padding: 2px 8px; border-radius: 4px; font-size: 0.78rem;
}
.track-order .to-form-side { padding: 40px 36px; }
.track-order .to-form-header { margin-bottom: 24px; }
.track-order .to-form-header h2 { font-size: 1.55rem; font-weight: 700; color: var(--dd-text-main); margin-bottom: 6px; }
.track-order .to-form-header p { font-size: 0.9rem; color: var(--dd-text-muted); margin: 0; }
.track-order .to-icon-ring {
    width: 72px; height: 72px; border-radius: 50%;
    background: linear-gradient(135deg, rgba(17, 107, 131, 0.15), rgba(231, 174, 7, 0.18));
    border: 2px solid rgba(231, 174, 7, 0.4);
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 2rem; color: var(--dd-gold); margin-bottom: 16px;
}
@media (max-width: 991px) {
    .track-order .to-grid { grid-template-columns: 1fr; }
    .track-order .to-main-box { margin-top: -60px; }
    .track-order .to-help-side, .track-order .to-form-side { padding: 28px 24px; }
    .track-order .dd-apply-headline { font-size: 2rem !important; }
}
@media (max-width: 575px) {
    .track-order .to-main-box { margin-top: -40px; }
    .track-order .to-help-side, .track-order .to-form-side { padding: 22px 18px; }
    .track-order .to-form-header h2 { font-size: 1.3rem; }
    .track-order .to-step { gap: 10px; margin-bottom: 16px; }
    .track-order .dd-apply-hero-banner { padding-bottom: 120px !important; }
}
</style>
@endpush

@section('frontend_content')
<section class="dd-apply-wrapper track-order">

    <div class="dd-apply-hero-banner">
        <div class="container px-4 px-lg-5 text-center position-relative">
            <a href="{{ route('frontend.home') }}" class="dd-apply-back-btn ">
                <iconify-icon icon="solar:alt-arrow-left-linear"></iconify-icon>
                <span>Back to Home</span>
            </a>

            <div class="to-icon-ring">
                <iconify-icon icon="solar:delivery-linear"></iconify-icon>
            </div>
            <br> <br>
            <span class="dd-apply-badge">Order Tracking</span>
            <h1 class="dd-apply-headline" style="font-size: 2.4rem;">Track Your Order</h1>
            <p class="dd-apply-subhead">Find your order status anytime using your order number and phone.</p>
        </div>
    </div>

    <div class="container px-4 px-lg-5 to-main-box">
        <div class="to-grid">
            <div class="to-help-side">
                <h3>How it works</h3>
                <p>Use the details from your order confirmation email or receipt to look up your order.</p>

                <div class="to-step">
                    <div class="to-step-num">1</div>
                    <div class="to-step-text">
                        <strong>Find your order number</strong>
                        <span>From your confirmation page or SMS — e.g. Order #6</span>
                    </div>
                </div>
                <div class="to-step">
                    <div class="to-step-num">2</div>
                    <div class="to-step-text">
                        <strong>Enter your phone number</strong>
                        <span>Must match the number used when you placed the order</span>
                    </div>
                </div>
                <div class="to-step">
                    <div class="to-step-num">3</div>
                    <div class="to-step-text">
                        <strong>View full confirmation</strong>
                        <span>See items, total, status, and contact info for help</span>
                    </div>
                </div>

                <div class="to-example">
                    <h6>Example</h6>
                    <div class="to-example-row">Order: <code>6</code></div>
                    <div class="to-example-row">Phone: <code>01712345678</code></div>
                </div>

                <p class="mt-4 mb-0" style="font-size: 0.82rem; color: rgba(255,255,255,0.55);">
                    @guest('member')
                    Registered members can <a href="{{ route('frontend.member.login') }}" style="color: var(--dd-gold);">sign in</a> to see all orders in one dashboard.
                    @else
                    View all orders on your <a href="{{ route('frontend.member.dashboard') }}" style="color: var(--dd-gold);">member dashboard</a>.
                    @endguest
                </p>
            </div>

            <div class="to-form-side">
                @auth('member')
                <div class="alert alert-info border-0 mb-4 to-member-banner" style="border-radius: 12px;">
                    Signed in as <strong>{{ $member->name }}</strong>.
                    Your phone is pre-filled. Enter an order number to open it on your dashboard, or
                    <a href="{{ route('frontend.member.dashboard') }}">view all orders</a>.
                </div>
                @endauth

                <div class="to-form-header">
                    <h2>Look Up Order</h2>
                    <p>Enter your details below to view order confirmation.</p>
                </div>

                @if (session('info'))
                    <div class="alert alert-info border-0 mb-4" style="border-radius: 12px;">{{ session('info') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger border-0 mb-4" style="border-radius: 12px;">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('frontend.order.track.submit') }}" class="dd-apply-form-element">
                    @csrf
                    <div class="dd-input-group">
                        <input type="number" name="order_id" id="track_order_id" class="dd-input-field" placeholder=" " value="{{ old('order_id', $prefillOrderId ?? '') }}" required min="1">
                        <label for="track_order_id" class="dd-floating-label">Order Number</label>
                    </div>

                    <div class="dd-input-group">
                        <input type="tel" name="phone" id="track_phone" class="dd-input-field" placeholder=" " value="{{ old('phone', $prefillPhone ?? '') }}" @guest('member') required @endguest @auth('member') @if($prefillPhone) readonly @endif @endauth>
                        <label for="track_phone" class="dd-floating-label">Phone Number at Checkout</label>
                    </div>
                    @auth('member')
                    <p class="text-muted mb-3" style="font-size: 0.78rem; margin-top: -8px;">Optional if the order is linked to your account. We use your member phone by default.</p>
                    @endauth

                    <button type="submit" class="dd-submit-btn">
                        <span>Track My Order</span>
                        <iconify-icon icon="solar:magnifer-linear" class="dd-btn-icon"></iconify-icon>
                    </button>
                </form>

                <div class="text-center mt-4 pt-3" style="border-top: 1px solid var(--dd-border);">
                    @guest('member')
                    <p class="text-muted mb-2" style="font-size: 0.85rem;">Order often? Get a membership card.</p>
                    <a href="{{ route('frontend.card.apply') }}" class="btn btn-outline-dark btn-sm me-1">Apply Now</a>
                    <a href="{{ route('frontend.member.login') }}" class="btn btn-dark btn-sm">Member Login</a>
                    @else
                    <a href="{{ route('frontend.member.dashboard') }}" class="btn btn-sm" style="background:#116b83;color:#fff;">Go to My Dashboard</a>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
