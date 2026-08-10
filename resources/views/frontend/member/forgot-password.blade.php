@extends('frontend.layout')

@section('meta_title', 'Forgot Member Password')
@section('meta_robots', 'noindex, nofollow')

@push('front_css')
<style>
.member-login .md-login-box {
    margin-top: -100px;
    position: relative;
    z-index: 2;
    max-width: 960px;
    margin-left: auto;
    margin-right: auto;
}
.member-login .md-login-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0;
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 24px 60px rgba(17, 107, 131, 0.12);
    border: 1px solid var(--dd-border);
}
.member-login .md-login-help {
    background: linear-gradient(160deg, #083844 0%, #116b83 100%);
    padding: 40px 36px;
    color: #fff;
}
.member-login .md-login-help h3 {
    font-family: var(--section-title-font, "Poppins", sans-serif);
    font-size: 1.35rem;
    font-weight: 700;
    margin-bottom: 8px;
    color: #fff;
}
.member-login .md-login-help > p {
    color: rgba(255, 255, 255, 0.78);
    font-size: 0.92rem;
    line-height: 1.6;
    margin-bottom: 24px;
}
.member-login .md-step {
    display: flex;
    gap: 14px;
    margin-bottom: 18px;
    align-items: flex-start;
}
.member-login .md-step-num {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: rgba(231, 174, 7, 0.18);
    border: 1px solid rgba(231, 174, 7, 0.45);
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
    color: rgba(255, 255, 255, 0.72);
    line-height: 1.5;
}
.member-login .md-step-text code {
    color: var(--dd-gold);
    background: rgba(0, 0, 0, 0.2);
    padding: 1px 6px;
    border-radius: 4px;
    font-size: 0.75rem;
}
.member-login .md-example-box {
    margin-top: 22px;
    padding: 16px;
    border-radius: 12px;
    background: rgba(231, 174, 7, 0.1);
    border: 1px dashed rgba(231, 174, 7, 0.4);
}
.member-login .md-example-box h6 {
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--dd-gold);
    margin-bottom: 10px;
}
.member-login .md-example-row {
    font-size: 0.82rem;
    color: rgba(255, 255, 255, 0.85);
    margin-bottom: 6px;
}
.member-login .md-example-row code {
    color: var(--dd-gold);
    background: rgba(0, 0, 0, 0.2);
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 0.78rem;
}
.member-login .md-login-form-side {
    padding: 40px 36px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.member-login .md-form-header {
    margin-bottom: 24px;
}
.member-login .md-form-header h2 {
    font-family: var(--section-title-font, "Poppins", sans-serif);
    font-size: 1.55rem;
    font-weight: 700;
    color: var(--dd-text-main);
    margin-bottom: 6px;
}
.member-login .md-form-header p {
    font-size: 0.9rem;
    color: var(--dd-text-muted);
    margin: 0;
}
.member-login .md-icon-ring {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(17, 107, 131, 0.15), rgba(231, 174, 7, 0.18));
    border: 2px solid rgba(231, 174, 7, 0.4);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: var(--dd-gold);
    margin-bottom: 16px;
}
.member-login .md-field-hint {
    font-size: 0.78rem;
    color: var(--dd-text-muted);
    margin-top: -6px;
    margin-bottom: 1rem;
    line-height: 1.45;
}
.member-login .md-remember {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1.25rem;
    padding: 0.65rem 0.85rem;
    background: var(--dd-input-bg);
    border: 1px solid var(--dd-border);
    border-radius: 10px;
}
.member-login .md-remember .form-check-input {
    width: 1.1rem;
    height: 1.1rem;
    margin: 0;
    border-color: rgba(17, 107, 131, 0.35);
    flex-shrink: 0;
}
.member-login .md-remember .form-check-input:checked {
    background-color: #116b83;
    border-color: #116b83;
}
.member-login .md-remember .form-check-label {
    font-size: 0.88rem;
    color: var(--dd-text-main);
    cursor: pointer;
}
.member-login .md-alert {
    border-radius: 12px;
    border: none;
    font-size: 0.88rem;
    margin-bottom: 1.25rem;
}
.member-login .md-footer-cta {
    text-align: center;
    margin-top: 1.5rem;
    padding-top: 1.25rem;
    border-top: 1px solid var(--dd-border);
}
.member-login .md-footer-cta p {
    font-size: 0.88rem;
    color: var(--dd-text-muted);
    margin-bottom: 0.75rem;
}
.member-login .md-apply-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.55rem 1.15rem;
    font-size: 0.85rem;
    font-weight: 600;
    color: #116b83;
    border: 1.5px solid rgba(17, 107, 131, 0.35);
    border-radius: 999px;
    text-decoration: none;
    transition: all 0.25s ease;
}
.member-login .md-apply-btn:hover {
    background: #116b83;
    color: #fff;
    border-color: #116b83;
}
.member-login .dd-apply-hero-banner .container,
.track-order .dd-apply-hero-banner .container {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.member-login .dd-apply-back-btn,
.track-order .dd-apply-back-btn {
    position: relative;
    top: auto;
    left: auto;
    align-self: center;
    margin-bottom: 1rem;
}

.member-login .dd-apply-hero-content,
.track-order .dd-apply-hero-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
    max-width: 560px;
}

.member-login .dd-apply-headline,
.track-order .dd-apply-headline {
    width: 100%;
    text-align: center;
}

.member-login .dd-apply-subhead,
.track-order .dd-apply-subhead {
    text-align: center;
    margin-left: auto;
    margin-right: auto;
}

.member-login .md-icon-ring,
.track-order .to-icon-ring {
    margin-left: auto;
    margin-right: auto;
}

.member-login .dd-apply-badge,
.track-order .dd-apply-badge {
    margin-left: auto;
    margin-right: auto;
}

.member-login .dd-apply-subhead {
    color: rgba(255, 255, 255, 0.82) !important;
}
@media (max-width: 991px) {
    .member-login .md-login-grid {
        grid-template-columns: 1fr;
    }
    .member-login .md-login-box {
        margin-top: -60px;
    }
    .member-login .md-login-form-side {
        order: 1;
        padding: 28px 24px;
    }
    .member-login .md-login-help {
        order: 2;
        padding: 28px 24px;
    }
    .member-login .dd-apply-headline {
        font-size: 2rem !important;
    }
}
@media (max-width: 575px) {
    .member-login .md-login-box {
        margin-top: -40px;
    }
    .member-login .md-login-help,
    .member-login .md-login-form-side {
        padding: 22px 18px;
    }
    .member-login .md-form-header h2 {
        font-size: 1.3rem;
    }
    .member-login .md-step {
        gap: 10px;
        margin-bottom: 14px;
    }
    .member-login .dd-apply-hero-banner {
        padding-bottom: 120px !important;
    }
    .member-login .dd-apply-back-btn {
        position: relative;
        left: 0;
        margin-bottom: 1rem;
        font-size: 0.78rem;
    }
    .member-login .dd-submit-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endpush

@section('frontend_content')
<section class="dd-apply-wrapper member-login">

    <div class="dd-apply-hero-banner">
        <div class="container px-4 px-lg-5 position-relative">
            <a href="{{ route('frontend.home') }}" class="dd-apply-back-btn">
                <iconify-icon icon="solar:alt-arrow-left-linear"></iconify-icon>
                <span>Back to Home</span>
            </a>

            <div class="dd-apply-hero-content">
                <div class="md-icon-ring">
                    <iconify-icon icon="solar:user-circle-bold"></iconify-icon>
                </div>
                <h1 class="dd-apply-headline">Forgot Password</h1>
                <p class="dd-apply-subhead">Enter your membership email and we will send you a reset link.</p>
            </div>
        </div>
    </div>

    <div class="container px-3 px-sm-4 px-lg-5 md-login-box">
        <div class="md-login-grid">
            <div class="md-login-help">
                <h3>How password reset works</h3>
                <p>Use the email address you registered with when you applied for your membership card.</p>

                <div class="md-step">
                    <div class="md-step-num">1</div>
                    <div class="md-step-text">
                        <strong>Enter your email</strong>
                        <span>This must match the email on your membership account</span>
                    </div>
                </div>
                <div class="md-step">
                    <div class="md-step-num">2</div>
                    <div class="md-step-text">
                        <strong>Check your inbox</strong>
                        <span>Open the reset link from Degchi Dine (also check spam)</span>
                    </div>
                </div>
                <div class="md-step">
                    <div class="md-step-num">3</div>
                    <div class="md-step-text">
                        <strong>Set a new password</strong>
                        <span>Then sign in with your phone/card number and new password</span>
                    </div>
                </div>

                <div class="md-example-box">
                    <h6>Need help?</h6>
                    <div class="md-example-row">Contact: <code>support@degchidine.com</code></div>
                </div>
            </div>

            <div class="md-login-form-side">
                <div class="md-form-header">
                    <h2>Reset Link</h2>
                    <p>We will email a secure password reset link to your membership email.</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger md-alert">{{ $errors->first() }}</div>
                @endif

                @if (session('status'))
                    <div class="alert alert-success md-alert">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('frontend.member.password.email') }}" class="dd-apply-form-element">
                    @csrf
                    <div class="dd-input-group">
                        <input type="email" name="email" id="member_reset_email" class="dd-input-field" placeholder=" " value="{{ old('email') }}" required autofocus autocomplete="email">
                        <label for="member_reset_email" class="dd-floating-label">Email Address</label>
                    </div>
                    <p class="md-field-hint">Must be the email saved on your membership profile.</p>

                    <button type="submit" class="dd-submit-btn">
                        <span>Send Reset Link</span>
                        <iconify-icon icon="solar:letter-linear" class="dd-btn-icon"></iconify-icon>
                    </button>
                </form>

                <div class="md-footer-cta">
                    <p>Remembered your password?</p>
                    <a href="{{ route('frontend.member.login') }}" class="md-apply-btn">
                        <iconify-icon icon="solar:login-2-linear"></iconify-icon>
                        Back to Member Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
