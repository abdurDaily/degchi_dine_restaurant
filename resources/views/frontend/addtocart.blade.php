@extends('frontend.layout')

@section('meta_title', 'Your Cart')
@section('meta_robots', 'noindex, nofollow')

@section('frontend_content')

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
                        <!-- <div class="cart-summary-row">
                            <span>Delivery</span>
                            <span class="cart-free-tag">Free</span>
                        </div> -->
                        <div class="cart-summary-divider"></div>
                        <div class="cart-summary-total-row">
                            <span>Total</span>
                            <strong id="cartPageTotal" class="cart-summary-total-val">৳ 1,140</strong>
                        </div>
                    </div>

                    <div class="cart-summary-footer">
                        <a href="{{ route('frontend.checkout') }}" class="btn cart-checkout-btn w-100 d-flex justify-content-center align-items-center">
                            Checkout <i class="bi bi-arrow-right ms-2"></i>
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