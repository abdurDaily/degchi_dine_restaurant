@extends('frontend.layout')

@section('meta_title', 'Your Cart')
@section('meta_robots', 'noindex, nofollow')

@section('frontend_content')

<style>
/* --- Premium Formal Glassmorphism Theme --- */
:root {
    --glass-bg: rgba(255, 255, 255, 0.7);
    --glass-border: rgba(255, 255, 255, 0.9);
    --glass-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.05), 0 1px 3px rgba(0, 0, 0, 0.03);
    --glass-blur: blur(24px);
    --brand-dark: #0a4554;
    --text-muted: #5a7a85;
    --border-light: rgba(17, 107, 131, 0.08);
    --brand-accent: #e7ae07;
}

body {
    background-color: #f9fafb;
}

/* Deeper background gradient to make the glass effect pop (Using brand RGB: 239, 139, 31) */
.cart-page-section {
    min-height: calc(100vh - 200px);
    padding-top: 3rem;
    padding-bottom: 5rem;
    background: 
        radial-gradient(circle at 10% 20%, rgba(231, 174, 7, 0.04) 0%, transparent 40%),
        radial-gradient(circle at 90% 80%, rgba(0, 0, 0, 0.03) 0%, transparent 40%),
        linear-gradient(135deg, #f3f4f6 0%, #ffffff 100%);
}

.cart-page-hero {
    background: #ffffff;
    border-bottom: 1px solid var(--border-light);
    padding: 2.5rem 0;
}

.cart-hero-title {
    font-size: clamp(1.8rem, 4vw, 2.2rem);
    color: var(--brand-dark);
    font-weight: 800;
    margin: 0;
    letter-spacing: -0.5px;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.menu-kicker {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: var(--brand-accent);
    font-weight: 700;
    margin-bottom: 0.5rem;
    display: inline-block;
}

.cart-count-badge {
    display: inline-block;
    background: rgba(231, 174, 7, 0.1);
    color: var(--brand-accent);
    font-size: 0.9rem;
    font-weight: 700;
    letter-spacing: 0.3px;
    padding: 0.35rem 0.85rem;
    border-radius: 999px;
    vertical-align: middle;
    border: 1px solid rgba(231, 174, 7, 0.2);
}

/* --- Premium Breadcrumb Stepper --- */
.cart-stepper {
    display: flex;
    align-items: center;
    gap: 0.4rem; 
}

.cart-step {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.5rem 1.25rem;
    border-radius: 999px; 
    font-size: 0.9rem;
    font-weight: 600;
    background: #ffffff;
    color: #4b5563;
    border: 1.5px solid rgba(231, 174, 7, 0.2); /* Brand tinted border */
    transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
}

.cart-step i {
    font-size: 1rem;
    color: #6b7280;
}

.cart-step-active {
    background: var(--brand-dark);
    color: #ffffff;
    border-color: var(--brand-dark);
    box-shadow: 0 10px 20px -6px rgba(231, 174, 7, 0.4); /* Brand tinted glow */
    transform: translateY(-1px);
}

.cart-step-active i {
    color: #ffffff;
}

.cart-step-arrow {
    color: #d1d5db;
    font-size: 0.85rem;
}

/* --- Glassmorphism Cards --- */
.cart-summary-card {
    background: var(--glass-bg);
    backdrop-filter: var(--glass-blur);
    -webkit-backdrop-filter: var(--glass-blur);
    border: 1px solid var(--glass-border);
    border-radius: 1.25rem;
    box-shadow: var(--glass-shadow);
    padding: 0; 
    position: sticky;
    top: 100px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.cart-summary-header {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--brand-dark);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid var(--border-light);
    background: rgba(255, 255, 255, 0.6);
    letter-spacing: -0.2px;
}

/* --- Product Cards (Glassmorphism) --- */
.cart-items-list {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.cart-product-card {
    display: flex;
    gap: 1.5rem;
    background: var(--glass-bg);
    backdrop-filter: var(--glass-blur);
    -webkit-backdrop-filter: var(--glass-blur);
    border: 1px solid var(--glass-border);
    border-radius: 1.25rem;
    padding: 1.25rem;
    box-shadow: var(--glass-shadow);
    transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
    align-items: center;
}

.cart-product-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 30px -10px rgba(0, 0, 0, 0.08);
    background: rgba(255, 255, 255, 0.85);
}

.cart-product-img-wrap {
    flex-shrink: 0;
}

.cart-product-img {
    width: 90px;
    height: 90px;
    object-fit: cover;
    border-radius: 0.75rem;
    border: 1px solid var(--border-light);
    box-shadow: 0 4px 10px rgba(0,0,0,0.04);
}

.cart-product-body {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    flex-grow: 1;
    min-width: 0;
    gap: 0.75rem;
}

.cart-product-top, .cart-product-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.cart-product-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--brand-dark);
    margin: 0 0 0.35rem;
    line-height: 1.2;
}

.cart-product-tag {
    display: inline-block;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    color: var(--text-muted);
    background: rgba(0, 0, 0, 0.04);
    border-radius: 4px;
    padding: 0.25rem 0.6rem;
}

.cart-remove-btn {
    width: 34px;
    height: 34px;
    padding: 0;
    border-radius: 50%;
    background: rgba(220, 38, 38, 0.05);
    border: 1px solid rgba(220, 38, 38, 0.15);
    color: #dc2626;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.cart-remove-btn:hover {
    background: #dc2626;
    color: #fff;
    transform: scale(1.05);
    box-shadow: 0 4px 10px rgba(220, 38, 38, 0.2);
}

.cart-product-qty {
    display: inline-flex;
    align-items: center;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 50px;
    overflow: hidden;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.01);
}

.cart-qty-btn {
    padding: 0.35rem 0.85rem;
    border: none;
    background: transparent;
    color: var(--brand-dark);
    font-size: 1.1rem;
    transition: background 0.2s;
}

.cart-qty-btn:hover {
    background: rgba(0,0,0,0.04);
}

.cart-qty-val {
    min-width: 2rem;
    text-align: center;
    font-weight: 700;
    font-size: 0.95rem;
    color: var(--brand-dark);
}

.cart-product-price-wrap {
    text-align: right;
}

.cart-product-unit {
    display: block;
    font-size: 0.8rem;
    color: var(--text-muted);
    margin-bottom: 0.15rem;
}

.cart-product-total {
    font-size: 1.25rem;
    color: var(--brand-dark);
    font-weight: 800;
}

/* --- Buttons & Links --- */
.cart-section-label {
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--brand-dark);
}

.cart-clear-btn {
    font-size: 0.85rem;
    font-weight: 600;
    color: #6b7280;
    border: 1px solid var(--border-light);
    border-radius: 999px;
    padding: 0.4rem 1rem;
    background: #fff;
    transition: all 0.2s ease;
}

.cart-clear-btn:hover {
    background: rgba(220, 38, 38, 0.05);
    color: #dc2626;
    border-color: rgba(220, 38, 38, 0.2);
}

.cart-checkout-btn {
    background: var(--brand-dark);
    color: #fff;
    font-weight: 600;
    letter-spacing: 0.3px;
    padding: 1rem;
    border-radius: 0.75rem;
    border: none;
    transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
    position: relative;
    overflow: hidden;
    font-size: 1rem;
}

.cart-checkout-btn:hover {
    background: #000;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
}

.cart-continue-link {
    color: #6b7280;
    font-size: 0.95rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}

.cart-continue-link i {
    transition: transform 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
}

.cart-continue-link:hover {
    color: var(--brand-dark);
}

.cart-continue-link:hover i {
    transform: translateX(-5px); 
}

/* --- Summary Details --- */
.cart-summary-body {
    padding: 1.5rem 2rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.cart-summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.95rem;
    color: #4b5563;
    font-weight: 500;
}

.cart-summary-divider {
    height: 1px;
    background: var(--border-light);
    margin: 0.2rem 0;
}

.cart-summary-total-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 0.5rem;
}

.cart-summary-total-row > span {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--brand-dark);
}

.cart-summary-total-val {
    font-size: 1.7rem;
    color: var(--brand-dark);
    font-weight: 800;
    letter-spacing: -0.5px;
}

.cart-free-tag {
    background: transparent;
    color: var(--brand-dark);
    border: 1px solid var(--brand-dark);
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 700;
    padding: 0.25rem 0.6rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.cart-summary-footer {
    padding: 0 2rem 2rem 2rem;
    background: transparent;
}

.cart-secure-note {
    text-align: center;
    font-size: 0.8rem;
    color: var(--text-muted);
    margin: 1rem 0 0;
}

/* --- Empty State --- */
.cart-empty-block {
    background: var(--glass-bg);
    backdrop-filter: var(--glass-blur);
    -webkit-backdrop-filter: var(--glass-blur);
    border: 1px solid var(--glass-border);
    border-radius: 1.25rem;
    padding: 4rem 2rem;
    box-shadow: var(--glass-shadow);
}

.cart-empty-icon-wrap {
    width: 90px;
    height: 90px;
    background: rgba(231, 174, 7, 0.05);
    border: 1px dashed rgba(231, 174, 7, 0.3);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
}

.cart-empty-icon {
    font-size: 2.5rem;
    color: var(--brand-accent);
    opacity: 0.9;
}

.cart-empty-heading {
    color: var(--brand-dark);
    font-size: 1.6rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.cart-empty-text {
    color: var(--text-muted);
    font-size: 1rem;
    line-height: 1.6;
    margin-bottom: 2rem;
}

/* Mobile Responsiveness */
@media (max-width: 768px) {
    .cart-product-card {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    .cart-product-img {
        width: 100%;
        height: 160px;
    }
    .cart-product-body {
        width: 100%;
    }
}
</style>

<!-- Cart Hero Banner -->
<div class="cart-page-hero">
    <div class="container px-4 px-lg-5">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <span class="menu-kicker"><i class="bi bi-bag-check me-1"></i> Your Order</span>
                <h1 class="cart-hero-title mt-1">
                    Your Cart <span class="cart-count-badge">2 Items</span>
                </h1>
            </div>
            
            <div class="cart-stepper">
                <span class="cart-step cart-step-active">
                    <i class="bi bi-bag"></i> Cart
                </span>
                <i class="bi bi-chevron-right cart-step-arrow mx-1"></i>
                <span class="cart-step text-muted fw-semibold" style="font-size: 0.9rem;">
                    <i class="bi bi-credit-card me-1"></i>Checkout
                </span>
                <i class="bi bi-chevron-right cart-step-arrow mx-1"></i>
                <span class="cart-step text-muted fw-semibold" style="font-size: 0.9rem;">
                    <i class="bi bi-check-circle me-1"></i>Confirmed
                </span>
            </div>
            
        </div>
    </div>
</div>

<section class="section-block cart-page-section">
    <div class="container px-4 px-lg-5">
        
        <!-- Empty State -->
        <div id="cartPageEmpty" class="cart-empty-block text-center" style="display: none">
            <div class="cart-empty-icon-wrap">
                <i class="bi bi-bag-x cart-empty-icon"></i>
            </div>
            <h4 class="cart-empty-heading">Your cart is empty</h4>
            <p class="cart-empty-text">
                Looks like you haven't added anything yet.<br />Let's find something delicious!
            </p>
            <a href="{{ route('frontend.home') }}#menu" class="btn cart-checkout-btn px-5 d-inline-flex align-items-center">
                <i class="bi bi-grid me-2"></i>Browse Menu
            </a>
        </div>

        <!-- Cart Items + Summary -->
        <div class="row g-4 align-items-start">
            
            <!-- Left: Items List -->
            <div class="col-lg-8">
                <div class="d-flex align-items-center justify-content-between mb-4 gap-2">
                    <h6 class="cart-section-label mb-0 d-flex align-items-center">
                        <i class="bi bi-list-check me-2 fs-5"></i> 2 Items in your cart
                    </h6>
                    <button class="btn cart-clear-btn d-flex align-items-center" type="button">
                        <i class="bi bi-trash me-2"></i> Clear All
                    </button>
                </div>

                <div id="cartPageItems" class="cart-items-list">
                    <!-- Product Card 1 -->
                    <div class="cart-product-card">
                        <div class="cart-product-img-wrap">
                            <img src="images/signature_menu/1.jpg" alt="Mutton Kacchi" class="cart-product-img" />
                        </div>
                        <div class="cart-product-body">
                            <div class="cart-product-top">
                                <div>
                                    <h6 class="cart-product-name">Mutton Kacchi</h6>
                                    <span class="cart-product-tag">Signature Dish</span>
                                </div>
                                <button class="btn cart-remove-btn" type="button" aria-label="Remove item">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                            <div class="cart-product-bottom">
                                <div class="cart-product-qty">
                                    <button class="btn cart-qty-btn" type="button">
                                        <i class="bi bi-dash"></i>
                                    </button>
                                    <span class="cart-qty-val">1</span>
                                    <button class="btn cart-qty-btn" type="button">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </div>
                                <div class="cart-product-price-wrap">
                                    <span class="cart-product-unit">৳ 420 × 1</span>
                                    <strong class="cart-product-total">৳ 420</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Card 2 -->
                    <div class="cart-product-card">
                        <div class="cart-product-img-wrap">
                            <img src="images/signature_menu/4.jpg" alt="Kacchi Biryani" class="cart-product-img" />
                        </div>
                        <div class="cart-product-body">
                            <div class="cart-product-top">
                                <div>
                                    <h6 class="cart-product-name">Kacchi Biryani</h6>
                                    <span class="cart-product-tag">Fan Favourite</span>
                                </div>
                                <button class="btn cart-remove-btn" type="button" aria-label="Remove item">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                            <div class="cart-product-bottom">
                                <div class="cart-product-qty">
                                    <button class="btn cart-qty-btn" type="button">
                                        <i class="bi bi-dash"></i>
                                    </button>
                                    <span class="cart-qty-val">2</span>
                                    <button class="btn cart-qty-btn" type="button">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </div>
                                <div class="cart-product-price-wrap">
                                    <span class="cart-product-unit">৳ 360 × 2</span>
                                    <strong class="cart-product-total">৳ 720</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /cart-items-list -->

                <div class="mt-4">
                    <a href="{{ route('frontend.home') }}#menu" class="cart-continue-link d-inline-flex align-items-center">
                        <i class="bi bi-arrow-left me-2"></i>Continue Shopping
                    </a>
                </div>
            </div>

            <!-- Right: Summary Card -->
            <div class="col-lg-4">
                <div class="cart-summary-card">
                    <div class="cart-summary-header">
                        <i class="bi bi-receipt me-2 text-muted"></i> Order Summary
                    </div>

                    <div class="cart-summary-body">
                        <div class="cart-summary-row">
                            <span>Subtotal <small class="text-muted ms-1">(2 items)</small></span>
                            <span id="cartPageSubtotal">৳ 1,140</span>
                        </div>
                        <div class="cart-summary-row">
                            <span>Delivery</span>
                            <span class="cart-free-tag">Free</span>
                        </div>
                        <div class="cart-summary-divider"></div>
                        <div class="cart-summary-total-row">
                            <span>Total</span>
                            <strong id="cartPageTotal" class="cart-summary-total-val">৳ 1,140</strong>
                        </div>
                    </div>

                    <div class="cart-summary-footer">
                        <a href="{{ route('frontend.checkout') }}" class="btn cart-checkout-btn w-100 d-flex justify-content-center align-items-center">
                            Proceed to Checkout <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                        <p class="cart-secure-note">
                            <i class="bi bi-shield-check me-1"></i> 100% Secure &amp; Safe Checkout
                        </p>
                    </div>
                </div>
            </div>
            
        </div>
        <!-- /row -->
    </div>
</section>
@endsection