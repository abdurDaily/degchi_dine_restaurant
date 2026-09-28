@extends('frontend.layout')

@section('meta_title', 'Your Cart')
@section('meta_robots', 'noindex, nofollow')

@section('frontend_content')

    <x-frontend.page-header title="Your Cart" eyebrow="Your Order" subtitle="Review your items before checkout.">
        <x-slot:eyebrowIcon>
            <iconify-icon icon="solar:bag-bold"></iconify-icon>
        </x-slot:eyebrowIcon>
        <x-slot:meta>
            <span>
                <i class="bi bi-bag-check"></i>
                Cart
            </span>
            <span>
                <i class="bi bi-chevron-right"></i>
            </span>
            <span style="opacity: 0.5;">
                <i class="bi bi-credit-card"></i>
                Checkout
            </span>
            <span>
                <i class="bi bi-chevron-right"></i>
            </span>
            <span style="opacity: 0.5;">
                <i class="bi bi-check-circle"></i>
                Confirmed
            </span>
        </x-slot:meta>
    </x-frontend.page-header>

    <section class="section-block cart-page-section"
        style="background: linear-gradient(160deg, #1a3a4a 0%, #0f2a38 50%, #162e3c 100%); padding: 3rem 0;">
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
                <a href="{{ route('frontend.home') }}#menu" class="btn-dine btn-dine-primary btn-dine-sm">
                    <i class="bi bi-grid"></i> Browse Menu
                </a>
            </div>

            <!-- Cart Items + Summary -->
            <div class="row g-4 align-items-start">

                <!-- Left: Items List -->
                <div class="col-lg-8">
                    <div class="d-flex align-items-center justify-content-between mb-4 gap-2">
                        <h6 class="cart-section-label mb-0 d-flex align-items-center" style="color: #fff;">
                            <i class="bi bi-list-check me-2 fs-5"></i> <span id="cartPageHeading">Loading your cart…</span>
                        </h6>
                        <button class="btn-dine btn-dine-outline btn-dine-sm cart-clear-btn" type="button">
                            <i class="bi bi-trash"></i> Clear All
                        </button>
                    </div>

                    <div id="cartPageItems" class="cart-items-list"></div>

                    <div class="mt-4">
                        <a href="{{ route('frontend.home') }}#menu" class="btn-dine btn-dine-ghost btn-dine-sm">
                            <i class="bi bi-arrow-left"></i> Continue Shopping
                        </a>
                    </div>
                </div>

                <!-- Right: Summary Card -->
                <div class="col-lg-4">
                    <div class="cart-summary-card">
                        <div class="cart-summary-header">
                            <i class="bi bi-receipt me-2 text-white"></i> Order Summary
                        </div>

                        <div class="cart-summary-body">
                            <div class="cart-summary-row">
                                <span>Subtotal <small class="ms-1 text-white" id="cartPageItemCount">(0 items)</small></span>
                                <span id="cartPageSubtotal">৳ 0.00</span>
                            </div>
                            <div class="cart-summary-divider"></div>
                            <div class="cart-summary-total-row">
                                <span>Total</span>
                                <strong id="cartPageTotal" class="cart-summary-total-val">৳ 0.00</strong>
                            </div>
                            <p class="checkout-offer-notice" id="cartPageOfferNotice" hidden></p>
                        </div>

                        <div class="cart-summary-footer">
                            <a href="{{ route('frontend.checkout') }}" class="btn-dine btn-dine-primary btn-dine-sm w-100">
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
