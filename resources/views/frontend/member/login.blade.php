@extends('frontend.layout')

@push('front_css')
<style>
.member-login .md-login-box {
    margin-top: -100px;
    position: relative;
    z-index: 2;
}
.member-login .md-login-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0;
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 24px 60px rgba(31, 20, 18, 0.1);
    border: 1px solid var(--dd-border);
}
.member-login .md-login-help {
    background: linear-gradient(160deg, #1f1412 0%, #2d1f1a 100%);
    padding: 40px 36px;
    color: #fff;
}
.member-login .md-login-help h3 {
    font-size: 1.4rem;
    font-weight: 700;
    margin-bottom: 8px;
    color: #fff;
}
.member-login .md-login-help > p {
    color: rgba(255,255,255,0.65);
    font-size: 0.92rem;
    line-height: 1.6;
    margin-bottom: 28px;
}
.member-login .md-step {
    display: flex;
    gap: 14px;
    margin-bottom: 20px;
    align-items: flex-start;
}
.member-login .md-step-num {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(223, 166, 83, 0.2);
    border: 1px solid rgba(223, 166, 83, 0.4);
    color: var(--dd-gold);
    font-weight: 700;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.member-login .md-step-text strong {
    display: block;
    font-size: 0.92rem;
    color: #fff;
    margin-bottom: 2px;
}
.member-login .md-step-text span {
    font-size: 0.82rem;
    color: rgba(255,255,255,0.6);
    line-height: 1.5;
}
.member-login .md-example-box {
    margin-top: 24px;
    padding: 16px;
    border-radius: 12px;
    background: rgba(223, 166, 83, 0.08);
    border: 1px dashed rgba(223, 166, 83, 0.3);
}
.member-login .md-example-box h6 {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--dd-gold);
    margin-bottom: 10px;
}
.member-login .md-example-row {
    font-size: 0.82rem;
    color: rgba(255,255,255,0.75);
    margin-bottom: 6px;
}
.member-login .md-example-row code {
    color: var(--dd-gold);
    background: rgba(0,0,0,0.2);
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 0.78rem;
}
.member-login .md-login-form-side {
    padding: 40px 36px;
}
.member-login .md-login-form-side .dd-apply-form-header {
    margin-bottom: 24px;
}
.member-login .md-login-form-side .dd-apply-form-header h2 {
    font-size: 1.6rem;
}
@media (max-width: 991px) {
    .member-login .md-login-grid { grid-template-columns: 1fr; }
    .member-login .md-login-box { margin-top: -60px; }
    .member-login .md-login-help { padding: 28px 24px; }
    .member-login .md-login-form-side { padding: 28px 24px; }
}
</style>
@endpush

@section('frontend_content')
<section class="dd-apply-wrapper member-login">

    <div class="dd-apply-hero-banner">
        <div class="container px-4 px-lg-5 text-center position-relative">
            <a href="{{ route('frontend.home') }}" class="dd-apply-back-btn">
                <iconify-icon icon="solar:alt-arrow-left-linear"></iconify-icon>
                <span>Back to Home</span>
            </a>

            <span class="dd-apply-badge">Returning Member</span>
            <h1 class="dd-apply-headline">Member Sign In</h1>
            <p class="dd-apply-subhead">Access your dashboard to view orders and membership details anytime.</p>
        </div>
    </div>

    <div class="container px-4 px-lg-5 md-login-box">
        <div class="md-login-grid">
            {{-- Left: How to login --}}
            <div class="md-login-help">
                <h3>How to sign in</h3>
                <p>If you applied for a membership card, use the same credentials you created during registration.</p>

                <div class="md-step">
                    <div class="md-step-num">1</div>
                    <div class="md-step-text">
                        <strong>Go to Member Login</strong>
                        <span>Open this page from the top menu → <em>Member Login</em>, or visit <code style="color:var(--dd-gold);">/member/login</code></span>
                    </div>
                </div>
                <div class="md-step">
                    <div class="md-step-num">2</div>
                    <div class="md-step-text">
                        <strong>Enter phone or card number</strong>
                        <span>Use the phone number you registered with, or your membership card number (e.g. MEM0001_1234)</span>
                    </div>
                </div>
                <div class="md-step">
                    <div class="md-step-num">3</div>
                    <div class="md-step-text">
                        <strong>Enter your password</strong>
                        <span>The password you set when you filled out the card application form on /card-apply</span>
                    </div>
                </div>

                <div class="md-example-box">
                    <h6>Example login</h6>
                    <div class="md-example-row">Phone: <code>01712345678</code></div>
                    <div class="md-example-row">Or card: <code>MEM0001_5678</code></div>
                    <div class="md-example-row">Password: <code>your chosen password</code></div>
                </div>
            </div>

            {{-- Right: Login form --}}
            <div class="md-login-form-side">
                <div class="dd-apply-form-header">
                    <h2>Sign In</h2>
                    <p>Enter your phone number or card number and password below.</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger border-0" style="border-radius: 12px;">{{ $errors->first() }}</div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success border-0" style="border-radius: 12px;">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('frontend.member.login.submit') }}" class="dd-apply-form-element">
                    @csrf
                    <div class="dd-input-group">
                        <input type="text" name="login" id="member_login" class="dd-input-field" placeholder=" " value="{{ old('login') }}" required autofocus>
                        <label for="member_login" class="dd-floating-label">Phone Number or Card Number</label>
                    </div>
                    <p class="text-muted mb-3" style="font-size: 0.78rem; margin-top: -8px;">Use the same phone or card number from your membership application.</p>

                    <div class="dd-input-group">
                        <input type="password" name="password" id="member_password" class="dd-input-field" placeholder=" " required>
                        <label for="member_password" class="dd-floating-label">Password</label>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="remember" id="member_remember" value="1">
                        <label class="form-check-label" for="member_remember">Keep me signed in on this device</label>
                    </div>

                    <button type="submit" class="dd-submit-btn">
                        <span>Sign In to Dashboard</span>
                        <iconify-icon icon="solar:login-2-linear" class="dd-btn-icon"></iconify-icon>
                    </button>
                </form>

                <div class="text-center mt-4 pt-3" style="border-top: 1px solid var(--dd-border);">
                    <p class="text-muted mb-2" style="font-size: 0.88rem;">Don't have a membership yet?</p>
                    <a href="{{ route('frontend.card.apply') }}" class="btn btn-outline-dark btn-sm">
                        <iconify-icon icon="solar:card-2-linear" class="me-1"></iconify-icon>
                        Apply for Membership Card
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
