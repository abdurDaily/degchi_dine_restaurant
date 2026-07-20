@extends('frontend.layout')

@section('meta_title', 'Checkout')
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
            --brand-accent: #116b83;
        }

        body {
            background-color: #f9fafb;
        }

        /* Deeper background gradient to make the glass effect pop */
        .cart-page-section {
            min-height: calc(100vh - 200px);
            padding-top: 3rem;
            padding-bottom: 5rem;
            background:
                radial-gradient(circle at 10% 20%, rgba(17, 107, 131, 0.03) 0%, transparent 40%),
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
            border: 1.5px solid rgba(17, 107, 131, 0.15);
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
            box-shadow: 0 10px 20px -6px #e7ae0785;
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
        .checkout-form-card,
        .cart-summary-card {
            background: var(--glass-bg);
            backdrop-filter: var(--glass-blur);
            -webkit-backdrop-filter: var(--glass-blur);
            border: 1px solid var(--glass-border);
            border-radius: 1rem;
            box-shadow: var(--glass-shadow);
            padding: 2.5rem;
        }

        .cart-summary-card {
            padding: 0;
            position: sticky;
            top: 100px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* Typography & Headers */
        .checkout-form-heading,
        .cart-summary-header {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--brand-dark);
            margin-bottom: 1.5rem;
            letter-spacing: -0.2px;
        }

        .cart-summary-header {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--border-light);
            background: rgba(255, 255, 255, 0.6);
            margin-bottom: 0;
        }

        .checkout-step-num {
            width: 28px;
            height: 28px;
            background: var(--brand-dark);
            color: #fff;
            font-size: 0.85rem;
            font-weight: 700;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* --- Form Inputs --- */
        .checkout-form-card .form-label {
            color: #4b5563;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .checkout-input-wrap {
            position: relative;
        }

        .checkout-input-icon {
            position: absolute;
            left: 1.2rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 1.1rem;
            pointer-events: none;
            z-index: 2;
            transition: color 0.3s ease;
        }

        .checkout-textarea-wrap .checkout-textarea-icon {
            top: 1.2rem;
            transform: none;
        }

        .checkout-input {
            background: transparent !important;
            padding: 0.85rem 1rem 0.85rem 3rem !important;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            font-size: 0.95rem;
            color: var(--brand-dark);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .checkout-input:focus {
            border-color: var(--brand-dark);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            outline: none;
        }

        .checkout-input:focus~.checkout-input-icon {
            color: var(--brand-dark);
        }

        /* --- Form sections --- */
        .checkout-form-panel {
            background: rgba(255, 255, 255, 0.72);
            border-radius: 1rem;
            padding: 10px;
        }

        .checkout-form-panel + .checkout-form-panel {
            margin-top: 1rem;
        }

        .checkout-form-heading {
            margin-bottom: 1.15rem;
        }

        .checkout-form-heading-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: rgba(17, 107, 131, 0.1);
            color: var(--brand-accent);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        /* --- Payment Radio Cards --- */
        .checkout-payment-options {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.85rem;
        }

        .checkout-payment-card {
            cursor: pointer;
            margin: 0;
        }

        .checkout-payment-card input[type="radio"] {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .checkout-payment-body {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            border: 1.5px solid #e5e7eb;
            border-radius: 1rem;
            padding: 1rem 1.1rem;
            background: #fff;
            transition: all 0.25s cubic-bezier(0.165, 0.84, 0.44, 1);
            min-height: 76px;
        }

        .checkout-payment-card:hover .checkout-payment-body {
            border-color: rgba(17, 107, 131, 0.35);
            box-shadow: 0 8px 22px rgba(17, 107, 131, 0.08);
        }

        .checkout-payment-card input:checked + .checkout-payment-body {
            border-color: var(--brand-accent);
            background: linear-gradient(135deg, rgba(17, 107, 131, 0.06), rgba(231, 174, 7, 0.05));
            box-shadow: 0 10px 24px rgba(17, 107, 131, 0.12);
        }

        .checkout-payment-icon-wrap {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.35rem;
            transition: transform 0.25s ease;
        }

        .checkout-payment-icon-wrap--cod {
            background: linear-gradient(135deg, rgba(17, 107, 131, 0.14), rgba(17, 107, 131, 0.06));
            color: #116b83;
        }

        .checkout-payment-icon-wrap--online {
            background: linear-gradient(135deg, rgba(231, 174, 7, 0.2), rgba(231, 174, 7, 0.08));
            color: #b8860b;
        }

        .checkout-payment-card input:checked + .checkout-payment-body .checkout-payment-icon-wrap {
            transform: scale(1.04);
        }

        .checkout-payment-text {
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
            flex: 1;
            min-width: 0;
        }

        .checkout-payment-text strong {
            font-size: 0.92rem;
            font-weight: 700;
            color: var(--brand-dark);
            line-height: 1.3;
        }

        .checkout-payment-text small {
            font-size: 0.74rem;
            color: var(--text-muted);
            line-height: 1.4;
        }

        .checkout-payment-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 0.35rem;
            margin-top: 0.35rem;
        }

        .checkout-payment-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.2rem;
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            padding: 0.18rem 0.45rem;
            border-radius: 999px;
            background: rgba(17, 107, 131, 0.08);
            color: #116b83;
        }

        .checkout-payment-check {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            border: 2px solid #d1d5db;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: transparent;
            font-size: 1.15rem;
            transition: all 0.25s ease;
        }

        .checkout-payment-card input:checked + .checkout-payment-body .checkout-payment-check {
            border-color: var(--brand-accent);
            background: var(--brand-accent);
            color: #fff;
        }

        .checkout-payment-check i {
            line-height: 1;
            font-size: 0.95rem;
        }

        /* =======================================================
       CRITICAL FIX: Order Summary Image & Text Alignment
       ======================================================= */

        .checkout-summary-items-wrap {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--border-light);
            background: transparent;
            max-height: 380px;
            overflow-y: auto;
        }

        /* Force ANY dynamically injected div inside the list to be a flex row */
        #orderSummaryList>div:not(.text-center) {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: flex-start !important;
            gap: 1.25rem !important;
            text-align: left !important;
            padding-bottom: 1.25rem !important;
            margin-bottom: 1.25rem !important;
            border-bottom: 1px dashed var(--border-light) !important;
            width: 100% !important;
        }

        /* Remove bottom border from the last item */
        #orderSummaryList>div:not(.text-center):last-child {
            border-bottom: none !important;
            margin-bottom: 0 !important;
            padding-bottom: 0 !important;
        }

        /* Force images to be small, square, and strictly on the left */
        #orderSummaryList img {
            width: 64px !important;
            height: 64px !important;
            min-width: 64px !important;
            border-radius: 0.5rem !important;
            object-fit: cover !important;
            flex-shrink: 0 !important;
            display: block !important;
            margin: 0 !important;
            border: 1px solid var(--border-light) !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04) !important;
        }

        /* Force the wrapper containing text/price to fill remaining space properly */
        #orderSummaryList div>div {
            display: flex !important;
            flex-direction: column !important;
            align-items: flex-start !important;
            justify-content: center !important;
            flex-grow: 1 !important;
            min-width: 0 !important;
        }

        /* Target the specific text elements to make them look premium */
        #orderSummaryList p,
        #orderSummaryList span,
        #orderSummaryList h6,
        #orderSummaryList div>div>*:first-child {
            margin: 0 !important;
            line-height: 1.4 !important;
            text-align: left !important;
        }

        /* ======================================================= */

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

        .cart-shipping-note {
            font-size: 0.72rem;
            color: var(--text-muted);
            line-height: 1.5;
            margin-top: -0.35rem;
            background: rgba(17, 107, 131, 0.05);
            border: 1px dashed var(--border-light);
            border-radius: 0.5rem;
            padding: 0.5rem 0.75rem;
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

        .cart-summary-total-row>span {
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

        .cart-summary-footer {
            padding: 0 2rem 2rem 2rem;
            background: transparent;
        }

        /* --- Premium Button Hover Effect --- */
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

        .cart-checkout-btn:active {
            transform: translateY(0);
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

        /* --- Link Hover Animation --- */
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

        /* Custom Scrollbar for summary items */
        .checkout-summary-items-wrap::-webkit-scrollbar {
            width: 6px;
        }

        .checkout-summary-items-wrap::-webkit-scrollbar-track {
            background: transparent;
        }

        .checkout-summary-items-wrap::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        @media (min-width: 768px) {
            .checkout-payment-options {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            
        }

        @media (max-width: 767.98px) {
            .cart-page-hero {
                padding: 1.35rem 0 1.15rem;
            }

            .cart-hero-title {
                font-size: clamp(1.35rem, 5vw, 1.65rem);
            }

            .cart-stepper {
                width: 100%;
                overflow-x: auto;
                padding-bottom: 0.15rem;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
            }

            .cart-stepper::-webkit-scrollbar {
                display: none;
            }

            .cart-step {
                flex-shrink: 0;
                padding: 0.42rem 0.85rem;
                font-size: 0.78rem;
            }

            .cart-step i {
                font-size: 0.9rem;
            }

            .cart-step-arrow {
                flex-shrink: 0;
            }

            .cart-page-section {
                padding-top: 1.35rem;
                padding-bottom: 2.5rem;
            }

            .checkout-form-card {
                padding: 1rem !important;
                border-radius: 1rem;
            }

            .checkout-form-panel {
                background: transparent;
                border: none;
                border-radius: 0.85rem;
            }

            .checkout-form-heading {
                font-size: 1rem;
                gap: 0.65rem;
            }

            .checkout-input {
                padding: 0.78rem 0.95rem 0.78rem 2.85rem !important;
                font-size: 0.9rem;
            }

            .checkout-input-icon {
                left: 0.95rem;
                font-size: 1rem;
            }

            .checkout-payment-body {
                padding: 0.9rem;
                min-height: auto;
            }

            .checkout-payment-icon-wrap {
                width: 44px;
                height: 44px;
                font-size: 1.2rem;
            }

            .checkout-payment-text strong {
                font-size: 0.86rem;
            }

            .checkout-payment-text small {
                font-size: 0.72rem;
            }

            .checkout-summary-sticky {
                position: static !important;
                top: auto !important;
            }

            .cart-summary-header,
            .checkout-summary-items-wrap,
            .cart-summary-body,
            .cart-summary-footer {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .cart-summary-header {
                padding-top: 1rem;
                padding-bottom: 1rem;
            }

            .cart-summary-footer {
                padding-bottom: 1.15rem;
            }

            .cart-summary-total-val {
                font-size: 1.35rem;
            }

            .cart-checkout-btn {
                padding: 0.9rem;
                font-size: 0.95rem;
            }
        }

        @media (max-width: 575.98px) {
            .cart-page-hero .d-flex {
                flex-direction: column;
                align-items: flex-start !important;
            }

            .checkout-payment-badges {
                display: none;
            }
        }
    </style>

    <div class="cart-page-hero">
        <div class="container px-4 px-lg-5">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <span class="menu-kicker"><i class="bi bi-shield-lock me-1"></i> Secure Checkout</span>
                    <h1 class="cart-hero-title mt-1">Shipping & Payment</h1>
                </div>

                <div class="cart-stepper">
                    <span class="cart-step">
                        <i class="bi bi-bag"></i> Cart
                    </span>
                    <i class="bi bi-chevron-right cart-step-arrow mx-1"></i>
                    <span class="cart-step cart-step-active">
                        <i class="bi bi-credit-card"></i> Checkout
                    </span>
                    <i class="bi bi-chevron-right cart-step-arrow mx-1"></i>
                    
                </div>

            </div>
        </div>
    </div>

    <section class="section-block cart-page-section">
        <div class="container px-4 px-lg-5">
            <div class="row g-4 align-items-start">
                <div class="col-lg-7">
                    <form id="checkoutForm" class="checkout-form-card p-3" action="{{ route('frontend.order.store') }}"
                        method="POST">
                        @csrf

                        <div id="checkoutMessages"></div>

                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if (session('clear_cart'))
                            <script>
                                localStorage.removeItem('degchi_cart');
                            </script>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger" style="border-radius: 0.75rem;">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="checkout-form-panel">
                            <h6 class="checkout-form-heading">
                                <span class="checkout-form-heading-icon" aria-hidden="true">
                                    <i class="bi bi-geo-alt-fill"></i>
                                </span>
                                Shipping Address
                            </h6>
                            <div class="row g-3 g-md-4">
                                <div class="col-md-6">
                                    <label class="form-label">Full Name*</label>
                                    <div class="checkout-input-wrap">
                                        <i class="bi bi-person checkout-input-icon"></i>
                                        <input class="form-control checkout-input" required type="text"
                                            name="customer_name" id="customer_name" placeholder="e.g. Rahim Uddin"
                                            value="{{ $loggedInMember->name ?? '' }}" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number*</label>
                                    <div class="checkout-input-wrap">
                                        <i class="bi bi-telephone checkout-input-icon"></i>
                                        <input class="form-control checkout-input" required type="tel"
                                            name="customer_phone" id="customer_phone" placeholder="01XXXXXXXXX"
                                            value="{{ $loggedInMember->phone ?? '' }}" />
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Delivery Address*</label>
                                    <div class="checkout-input-wrap checkout-textarea-wrap">
                                        <i class="bi bi-geo-alt checkout-input-icon checkout-textarea-icon"></i>
                                        <textarea class="form-control checkout-input" required rows="3" name="customer_address" id="customer_address"
                                            placeholder="House/Flat, Road, Area, City"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Membership Card</label>
                                    @if($loggedInMember ?? null)
                                        <div class="alert alert-success py-2 px-3 mb-2" style="font-size: 0.82rem;">
                                            Signed in as <strong>{{ $loggedInMember->name }}</strong>. Your card is applied automatically.
                                        </div>
                                    @endif
                                    <div class="checkout-input-wrap">
                                        <i class="bi bi-credit-card-2-front checkout-input-icon"></i>
                                        <input class="form-control checkout-input" type="text" name="member_card_number"
                                            id="memberCardNumber" placeholder="Enter registered card number"
                                            value="{{ $loggedInMember->unique_card_number ?? '' }}" />
                                    </div>
                                    <div id="membershipFeedback" class="form-text text-muted mt-2"
                                        style="font-size: 0.75rem;">30% or 35% off applies on your first order only. After ৳2,000 total spend, you unlock 10% off every order as a Golden member.</div>
                                </div>
                                {{-- <div class="col-md-5 d-flex align-items-center">
                                <div class="form-check mt-3 w-100 p-3 rounded" style="background: rgba(0,0,0,0.02); border: 1px dashed rgba(0,0,0,0.1);">
                                    <input class="form-check-input" type="checkbox" name="student_card" value="1" id="studentCard" style="cursor: pointer; border-color: rgba(0,0,0,0.3);" />
                                    <label class="form-check-label text-dark fw-semibold ms-1" for="studentCard" style="font-size: 0.85rem; cursor: pointer;">I can show a student card</label>
                                </div>
                            </div> --}}
                                <div class="col-md-12 d-none">
                                    <label class="form-label">Order Total (৳)</label>
                                    <div class="checkout-input-wrap">
                                        <i class="bi bi-cash-stack checkout-input-icon"></i>
                                        <input class="form-control checkout-input" required readonly type="number"
                                            step="0.01" min="0" name="order_total" id="order_total"
                                            value="0" />
                                    </div>
                                </div>
                                <input type="hidden" name="items" id="cart_items" value="[]" />
                            </div>
                        </div>

                        <div class="checkout-form-panel">
                            <h6 class="checkout-form-heading">
                                <span class="checkout-form-heading-icon" aria-hidden="true">
                                    <i class="bi bi-ticket-perforated"></i>
                                </span>
                                Coupon Code
                            </h6>
                            <div class="row g-3 g-md-4">
                                <div class="col-md-8">
                                    <div class="checkout-input-wrap">
                                        <i class="bi bi-tag checkout-input-icon"></i>
                                        <input class="form-control checkout-input" type="text" id="couponCodeInput"
                                            placeholder="Enter coupon code" style="text-transform:uppercase;" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <button type="button" id="applyCouponBtn" class="btn cart-checkout-btn w-100" style="padding: 0.85rem; font-size: 0.9rem;">
                                        Apply
                                    </button>
                                </div>
                            </div>
                            <div id="couponFeedback" class="form-text mt-2" style="font-size: 0.75rem;"></div>
                            <input type="hidden" name="coupon_code" id="couponCodeHidden" value="" />
                            <input type="hidden" name="coupon_discount" id="couponDiscountHidden" value="0" />
                        </div>

                        <div class="checkout-form-panel">
                            <h6 class="checkout-form-heading">
                                <span class="checkout-form-heading-icon" aria-hidden="true">
                                    <i class="bi bi-wallet2"></i>
                                </span>
                                Payment Method
                            </h6>
                            <div class="checkout-payment-options">
                                <label class="checkout-payment-card">
                                    <input type="radio" name="payment_method" value="cod" checked />
                                    <span class="checkout-payment-body">
                                        <span class="checkout-payment-icon-wrap checkout-payment-icon-wrap--cod" aria-hidden="true">
                                            <i class="bi bi-cash-coin"></i>
                                        </span>
                                        <span class="checkout-payment-text">
                                            <strong>Cash on Delivery</strong>
                                        </span>
                                        <span class="checkout-payment-check" aria-hidden="true">
                                            <i class="bi bi-check-lg"></i>
                                        </span>
                                    </span>
                                </label>
                                <label class="checkout-payment-card">
                                    <input type="radio" name="payment_method" value="sslcommerz" />
                                    <span class="checkout-payment-body">
                                        <span class="checkout-payment-icon-wrap checkout-payment-icon-wrap--online" aria-hidden="true">
                                            <i class="bi bi-credit-card-2-front"></i>
                                        </span>
                                        <span class="checkout-payment-text">
                                            <strong>Online Payment</strong>
                                        </span>
                                        <span class="checkout-payment-check" aria-hidden="true">
                                            <i class="bi bi-check-lg"></i>
                                        </span>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="col-lg-5">
                    <div class="cart-summary-card checkout-summary-sticky">
                        <div class="cart-summary-header">
                            <i class="bi bi-bag-check me-2 text-muted"></i> Order Summary
                        </div>

                        <div class="checkout-summary-items-wrap" id="orderSummaryList">
                            <div class="text-center py-5 text-muted" style="font-size: 0.9rem;">
                                Your selected products will appear here.
                            </div>
                        </div>

                        <div class="cart-summary-body">
                            <div class="cart-summary-row">
                                <span>Subtotal <small class="text-muted ms-1" id="itemCount">(0 items)</small></span>
                                <span id="checkoutSubtotal">৳ 0.00</span>
                            </div>
                            <div class="cart-summary-row">
                                <span>Membership Discount</span>
                                <span id="checkoutDiscount" class="text-success fw-bold">- ৳ 0.00</span>
                            </div>
                            <div class="cart-summary-row" id="offerDiscountRow" style="display: none;">
                                <span id="offerDiscountLabel">
                                    🎉 <span id="offerName">Offer Discount</span>
                                    <span class="badge ms-1" id="offerBadge" style="background:#e74c3c;color:#fff;font-size:.68rem;padding:2px 7px;border-radius:20px;"></span>
                                </span>
                                <span id="checkoutOfferDiscount" class="text-danger fw-bold">- ৳ 0.00</span>
                            </div>
                            <div class="cart-summary-row" id="couponDiscountRow" style="display: none;">
                                <span>
                                    🏷️ Coupon
                                    <span class="badge ms-1" id="couponCodeBadge" style="background:#116b83;color:#fff;font-size:.68rem;padding:2px 7px;border-radius:20px;"></span>
                                    <a href="#" id="removeCouponLink" class="ms-1 text-danger" style="font-size:.7rem;text-decoration:underline;">remove</a>
                                </span>
                                <span id="checkoutCouponDiscount" class="text-success fw-bold">- ৳ 0.00</span>
                            </div>
                            <div class="cart-summary-row">
                                <span>Delivery Charge</span>
                                <span id="shippingChargeDisplay" class="cart-free-tag">৳ 60.00</span>
                            </div>
                            <div class="cart-shipping-note">
                                <i class="bi bi-info-circle me-1"></i>ডেলিভারি লোকেশন ১ কিলোমিটারের বেশি হলে প্রতি কিলোমিটারে ২০ টাকা করে যুক্ত হবে।
                            </div>
                            <input type="hidden" name="shipping_charge" id="shippingChargeHidden" value="60" />
                            <div class="cart-summary-divider"></div>
                            <div class="cart-summary-total-row">
                                <span>Total</span>
                                <strong id="checkoutTotal" class="cart-summary-total-val">৳ 0.00</strong>
                            </div>
                        </div>

                        <div class="cart-summary-footer">
                            <button type="submit" id="placeOrderBtn" form="checkoutForm"
                                class="btn cart-checkout-btn w-100">
                                <i class="bi bi-lock-fill me-2" style="opacity: 0.7;"></i> Place Order Now
                            </button>

                            <p class="text-center text-muted mt-3 mb-0" style="font-size: 0.8rem;">
                                <i class="bi bi-shield-check me-1"></i> Your information is 100% encrypted & secure
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 text-center text-lg-start">
                        <a href="{{ route('frontend.addtocart') }}"
                            class="cart-continue-link d-inline-flex align-items-center">
                            <i class="bi bi-arrow-left me-2"></i> Return to Shopping Cart
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('front_js')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const checkoutForm = document.getElementById('checkoutForm');
                const submitButton = document.getElementById('placeOrderBtn');
                const memberCardInput = document.getElementById('memberCardNumber');
                const membershipFeedback = document.getElementById('membershipFeedback');
                const discountDisplay = document.getElementById('checkoutDiscount');
                const totalDisplay = document.getElementById('checkoutTotal');
                const subtotalDisplay = document.getElementById('checkoutSubtotal');
                const orderTotalInput = document.getElementById('order_total');

                // --- Delivery charge (default flat fee; +20tk per km beyond 1km is informational for now) ---
                const SHIPPING_CHARGE = 60;
                const shippingChargeDisplay = document.getElementById('shippingChargeDisplay');
                const shippingChargeHidden = document.getElementById('shippingChargeHidden');
                let shippingCharge = SHIPPING_CHARGE;
                if (shippingChargeDisplay) shippingChargeDisplay.textContent = `৳ ${shippingCharge.toFixed(2)}`;
                if (shippingChargeHidden) shippingChargeHidden.value = shippingCharge;

                function showToast(type, text) {
                    if (typeof toastr !== 'undefined') {
                        toastr.options = {
                            closeButton: true,
                            progressBar: true,
                            positionClass: 'toast-top-right',
                        };
                        toastr[type](text);
                    } else {
                        alert(text);
                    }
                }

                // Active offers data from PHP
                const activeOffers = @json($activeOffers ?? []);
                const offerDiscountEl = document.getElementById('checkoutOfferDiscount');
                const offerDiscountRow = document.getElementById('offerDiscountRow');
                const offerNameEl = document.getElementById('offerName');
                const offerBadgeEl = document.getElementById('offerBadge');

                // --- Coupon elements & state ---
                const couponCodeInput = document.getElementById('couponCodeInput');
                const applyCouponBtn = document.getElementById('applyCouponBtn');
                const couponFeedback = document.getElementById('couponFeedback');
                const couponDiscountRow = document.getElementById('couponDiscountRow');
                const checkoutCouponDiscountEl = document.getElementById('checkoutCouponDiscount');
                const couponCodeBadgeEl = document.getElementById('couponCodeBadge');
                const couponCodeHidden = document.getElementById('couponCodeHidden');
                const couponDiscountHidden = document.getElementById('couponDiscountHidden');
                const removeCouponLink = document.getElementById('removeCouponLink');

                let appliedCoupon = null; // { code, name, discount_type, discount_amount, discount, min_order_amount }

                // Track membership discount rate (recalculated when cart subtotal changes)
                let memberDiscountRate = 0;
                let verifiedMember = {
                    verified: false,
                    eligible: false,
                    is_student: false,
                    approval_status: 'approved',
                    member_type: 'standard',
                    first_order_discount_used: false,
                };

                @if($loggedInMemberDiscount ?? null)
                memberDiscountRate = {{ (
                    ($loggedInMemberDiscount['eligible'] ?? false)
                    && !(($loggedInMemberDiscount['first_order_discount_used'] ?? false) && ($loggedInMemberDiscount['member_type'] ?? '') !== 'golden')
                ) ? (int) ($loggedInMemberDiscount['rate'] ?? 0) : 0 }};
                verifiedMember = {
                    verified: true,
                    eligible: {{ (
                        ($loggedInMemberDiscount['eligible'] ?? false)
                        && !(($loggedInMemberDiscount['first_order_discount_used'] ?? false) && ($loggedInMemberDiscount['member_type'] ?? '') !== 'golden')
                    ) ? 'true' : 'false' }},
                    is_student: {{ ($loggedInMember->is_student ?? false) ? 'true' : 'false' }},
                    approval_status: @json($loggedInMember->approval_status ?? 'approved'),
                    member_type: @json($loggedInMemberDiscount['member_type'] ?? $loggedInMember->type ?? 'membership'),
                    first_order_discount_used: {{ ($loggedInMemberDiscount['first_order_discount_used'] ?? false) ? 'true' : 'false' }},
                };
                @endif

                function calculateMemberDiscount(subtotal) {
                    // First-order membership/student discount must not apply on later orders
                    if (verifiedMember.first_order_discount_used && verifiedMember.member_type !== 'golden') {
                        return 0;
                    }
                    if (!verifiedMember.eligible || memberDiscountRate <= 0) {
                        return 0;
                    }

                    return parseFloat((subtotal * (memberDiscountRate / 100)).toFixed(2));
                }

                function parseCurrencyText(text) {
                    const n = parseFloat((text || '').replace(/[^\d.]/g, ''));
                    return Number.isNaN(n) ? 0 : n;
                }

                function getCartMoneyTotals() {
                    try {
                        const cart = JSON.parse(localStorage.getItem('degchi_cart') || '[]');
                        let original = 0;
                        let effective = 0;
                        (Array.isArray(cart) ? cart : []).forEach(item => {
                            const qty = parseInt(item.quantity, 10) || 1;
                            const orig = parseFloat(item.original_price ?? item.price) || 0;
                            const price = parseFloat(item.price) || 0;
                            original += orig * qty;
                            effective += price * qty;
                        });
                        return {
                            cart: Array.isArray(cart) ? cart : [],
                            original: parseFloat(original.toFixed(2)),
                            effective: parseFloat(effective.toFixed(2)),
                        };
                    } catch (e) {
                        return { cart: [], original: 0, effective: 0 };
                    }
                }

                // Displayed subtotal = discounted item prices
                function getCurrentSubtotal() {
                    const { effective } = getCartMoneyTotals();
                    if (effective > 0) return effective;

                    const displayed = parseCurrencyText(subtotalDisplay?.textContent);
                    if (displayed > 0) return displayed;

                    return parseFloat(orderTotalInput?.value) || 0;
                }

                // NOTE: Coupon discount is always calculated against the product subtotal only
                // (never against the delivery charge), regardless of what is passed in.
                function calculateCouponDiscount(subtotal) {
                    if (!appliedCoupon) return 0;
                    if (subtotal < (appliedCoupon.min_order_amount || 0)) return 0;

                    const discount = appliedCoupon.discount_type === 'percentage'
                        ? subtotal * (appliedCoupon.discount_amount / 100)
                        : appliedCoupon.discount_amount;

                    return Math.min(parseFloat(discount.toFixed(2)), subtotal);
                }

                let isRefreshingCheckoutDiscounts = false;

                function refreshCheckoutDiscounts() {
                    // Prevent MutationObserver ↔ textContent update feedback loop
                    if (isRefreshingCheckoutDiscounts) {
                        return;
                    }
                    isRefreshingCheckoutDiscounts = true;

                    try {
                        const { cart, original, effective } = getCartMoneyTotals();
                        const offerInfo = calculateOfferDiscount(cart);
                        const memberDiscount = calculateMemberDiscount(original);
                        const foodOfferDiscount = Math.max(
                            offerInfo.discount || 0,
                            Math.max(0, parseFloat((original - effective).toFixed(2)))
                        );

                        // Membership/Student first-order is an all-items checkout benefit.
                        // Compare against food-item offers and take the better deal (membership wins ties).
                        const membershipWins = memberDiscount > 0 && memberDiscount >= foodOfferDiscount;

                        let displaySubtotal;
                        let promoToSubtract;
                        let displayMemberDiscount;
                        let displayOfferInfo;

                        if (membershipWins) {
                            // Show real catalog subtotal + Membership Discount (works even if cart has offer items)
                            displaySubtotal = original;
                            promoToSubtract = memberDiscount;
                            displayMemberDiscount = memberDiscount;
                            displayOfferInfo = { discount: 0, offerName: '', offerPercent: 0 };
                        } else if (foodOfferDiscount > 0) {
                            // Food promo is better — subtotal already uses offer unit prices
                            displaySubtotal = effective;
                            promoToSubtract = 0;
                            displayMemberDiscount = 0;
                            displayOfferInfo = {
                                discount: 0,
                                offerName: offerInfo.offerName,
                                offerPercent: offerInfo.offerPercent,
                            };
                        } else {
                            displaySubtotal = original > 0 ? original : effective;
                            promoToSubtract = 0;
                            displayMemberDiscount = 0;
                            displayOfferInfo = { discount: 0, offerName: '', offerPercent: 0 };
                        }

                        if (subtotalDisplay) {
                            const nextSubtotal = `৳ ${displaySubtotal.toFixed(2)}`;
                            if (subtotalDisplay.textContent !== nextSubtotal) {
                                subtotalDisplay.textContent = nextSubtotal;
                            }
                        }
                        if (orderTotalInput) {
                            const nextOriginal = original.toFixed(2);
                            if (orderTotalInput.value !== nextOriginal) {
                                orderTotalInput.value = nextOriginal;
                            }
                        }

                        const couponDiscount = calculateCouponDiscount(displaySubtotal);

                        updateTotals(
                            displaySubtotal,
                            promoToSubtract,
                            displayOfferInfo,
                            displayMemberDiscount,
                            couponDiscount,
                            {
                                original,
                                membershipWins,
                                offerMeta: offerInfo,
                            }
                        );
                    } finally {
                        isRefreshingCheckoutDiscounts = false;
                    }
                }

                function offerEligibleForVerifiedMember(offer) {
                    if (offer.applicable_to === 'golden') {
                        return verifiedMember.verified && verifiedMember.eligible && verifiedMember.member_type === 'golden';
                    }
                    if (offer.applicable_to === 'student') {
                        if (offer.is_first_order && verifiedMember.first_order_discount_used) {
                            return false;
                        }
                        return verifiedMember.verified
                            && verifiedMember.eligible
                            && verifiedMember.is_student
                            && verifiedMember.approval_status === 'approved';
                    }
                    if (offer.applicable_to === 'membership') {
                        if (offer.is_first_order && verifiedMember.first_order_discount_used) {
                            return false;
                        }
                        return verifiedMember.verified
                            && verifiedMember.eligible
                            && !verifiedMember.is_student;
                    }
                    if (offer.is_first_order === true || offer.is_first_order === 1) {
                        if (!verifiedMember.verified || !verifiedMember.eligible) {
                            return false;
                        }
                        if (verifiedMember.first_order_discount_used) {
                            return false;
                        }
                        if (verifiedMember.is_student && verifiedMember.approval_status !== 'approved') {
                            return false;
                        }
                        if (verifiedMember.member_type === 'golden') {
                            return false;
                        }
                    }
                    return true;
                }

                // Calculate offer discount for cart items (always from original unit price)
                function calculateOfferDiscount(cartItems) {
                    let totalOfferDiscount = 0;
                    let bestOfferName = '';
                    let bestOfferPercent = 0;

                    if (!Array.isArray(cartItems) || cartItems.length === 0) {
                        return { discount: 0, offerName: '', offerPercent: 0 };
                    }

                    const hasMembershipCard = memberCardInput && memberCardInput.value.trim() !== '';
                    const memberLoggedIn = !!(window.DEGCHI_MEMBER && window.DEGCHI_MEMBER.loggedIn);

                    const applicableOffers = (activeOffers || []).filter(offer => {
                        // Food menu promos only — membership/student/golden stay on member-card discount
                        if (offer.applicable_to !== 'all') {
                            return false;
                        }
                        if (!(offer.discount_percent > 0)) {
                            return false;
                        }
                        if (offer.is_first_order) {
                            if (!hasMembershipCard && !memberLoggedIn) {
                                return false;
                            }
                            return offerEligibleForVerifiedMember(offer);
                        }
                        return true;
                    });

                    cartItems.forEach(item => {
                        const variationId = item.variation_id || item.id;
                        if (!variationId) return;

                        const basePrice = parseFloat(item.original_price ?? item.price) || 0;
                        const qty = parseInt(item.quantity, 10) || 1;

                        // Cart already stored a public/member-eligible offer price
                        if (item.offer_applied && item.offer_percent > 0) {
                            const linked = applicableOffers.find(o => o.id == item.offer_id);
                            const raw = (activeOffers || []).find(o => o.id == item.offer_id);
                            if (raw && (raw.is_first_order || ['student', 'membership', 'golden'].includes(raw.applicable_to))) {
                                if (!linked && !offerEligibleForVerifiedMember(raw)) {
                                    return;
                                }
                            }

                            const itemDiscount = basePrice * qty * (item.offer_percent / 100);
                            totalOfferDiscount += itemDiscount;
                            if (item.offer_percent > bestOfferPercent) {
                                bestOfferPercent = item.offer_percent;
                                bestOfferName = linked?.name || raw?.name || 'Offer Discount';
                            }
                            return;
                        }

                        let bestItemOffer = null;
                        applicableOffers.forEach(offer => {
                            const appliesTo = offer.offer_type === 'all_items' ||
                                (Array.isArray(offer.menu_variations) &&
                                    offer.menu_variations.some(v => v.id == variationId));

                            if (appliesTo && (!bestItemOffer || offer.discount_percent > bestItemOffer.discount_percent)) {
                                bestItemOffer = offer;
                            }
                        });

                        if (bestItemOffer) {
                            const itemDiscount = basePrice * qty * (bestItemOffer.discount_percent / 100);
                            totalOfferDiscount += itemDiscount;
                            if (bestItemOffer.discount_percent > bestOfferPercent) {
                                bestOfferPercent = bestItemOffer.discount_percent;
                                bestOfferName = bestItemOffer.name;
                            }
                        }
                    });

                    return {
                        discount: parseFloat(totalOfferDiscount.toFixed(2)),
                        offerName: bestOfferName,
                        offerPercent: bestOfferPercent
                    };
                }

                function updateTotals(subtotal, bestDiscount, offerInfo, memberDiscount, couponDiscount, meta) {
                    offerInfo = offerInfo || { discount: 0, offerName: '', offerPercent: 0 };
                    meta = meta || {};
                    memberDiscount = typeof memberDiscount === 'number' ? memberDiscount : 0;
                    couponDiscount = typeof couponDiscount === 'number'
                        ? couponDiscount
                        : calculateCouponDiscount(subtotal);
                    const offerDiscount = offerInfo.discount || 0;

                    // Food offer is already included in the discounted Subtotal — do not show a
                    // second "− offer" line (that would look like double discounting).
                    if (offerDiscount > 0) {
                        offerDiscountRow.style.display = '';
                        offerDiscountEl.textContent = `- ৳ ${offerDiscount.toFixed(2)}`;
                        if (offerNameEl) offerNameEl.textContent = offerInfo.offerName || 'Offer Discount';
                        if (offerBadgeEl) offerBadgeEl.textContent = `${offerInfo.offerPercent}% OFF`;
                    } else {
                        offerDiscountRow.style.display = 'none';
                    }

                    if (memberDiscount > 0) {
                        discountDisplay.textContent = `- ৳ ${memberDiscount.toFixed(2)}`;
                    } else {
                        discountDisplay.textContent = `- ৳ 0.00`;
                    }

                    if (appliedCoupon && couponDiscount > 0) {
                        couponDiscountRow.style.display = '';
                        checkoutCouponDiscountEl.textContent = `- ৳ ${couponDiscount.toFixed(2)}`;
                        couponCodeBadgeEl.textContent = appliedCoupon.code;
                    } else {
                        couponDiscountRow.style.display = 'none';
                    }

                    couponCodeHidden.value = (appliedCoupon && couponDiscount > 0) ? appliedCoupon.code : '';
                    couponDiscountHidden.value = couponDiscount.toFixed(2);

                    // Subtotal is already discounted item prices.
                    // bestDiscount = extra membership amount only (when membership beats food offer).
                    // Do NOT subtract bakedOfferSavings again.
                    const discountedProductTotal = Math.max(0, subtotal - bestDiscount - couponDiscount);
                    const deliveryChargeToAdd = typeof shippingCharge === 'number' ? shippingCharge : SHIPPING_CHARGE;
                    const finalTotal = discountedProductTotal + deliveryChargeToAdd;
                    totalDisplay.textContent = `৳ ${finalTotal.toFixed(2)}`;

                    if (shippingChargeDisplay) shippingChargeDisplay.textContent = `৳ ${shippingCharge.toFixed(2)}`;
                    if (shippingChargeHidden) shippingChargeHidden.value = shippingCharge;

                    // Keep original catalog total for backend; never overwrite with discounted subtotal
                    if (orderTotalInput && typeof meta.original === 'number' && meta.original > 0) {
                        orderTotalInput.value = meta.original.toFixed(2);
                    }
                }

                function updateDiscountDisplay() {
                    refreshCheckoutDiscounts();
                }

                function applyMemberCheckResult(result, responseOk) {
                    if (result.account_restricted) {
                        memberDiscountRate = 0;
                        verifiedMember = { verified: false, eligible: false, is_student: false, approval_status: 'approved', member_type: 'standard', first_order_discount_used: false, account_restricted: true };
                        membershipFeedback.textContent = result.message || (window.DEGCHI_MEMBER && window.DEGCHI_MEMBER.accountRestrictedMessage) || 'Your account temporary suspand. contact our help line';
                        membershipFeedback.classList.remove('text-success', 'text-warning');
                        membershipFeedback.classList.add('text-danger');
                        alert(membershipFeedback.textContent);
                        refreshCheckoutDiscounts();
                        return;
                    }

                    const usedFirstOrder = !!result.first_order_discount_used;
                    const isGolden = (result.member_type || '') === 'golden';
                    // Never keep a first-order rate after it has been consumed
                    const allowRate = responseOk && result.eligible && (isGolden || !usedFirstOrder);

                    memberDiscountRate = allowRate ? (result.discount_rate || 0) : 0;
                    verifiedMember = {
                        verified: true,
                        eligible: !!allowRate,
                        is_student: !!result.is_student,
                        approval_status: result.approval_status || 'approved',
                        member_type: result.member_type || 'standard',
                        first_order_discount_used: usedFirstOrder,
                        account_restricted: false,
                    };

                    membershipFeedback.textContent = result.message || 'Unable to verify membership card.';
                    membershipFeedback.classList.remove('text-success', 'text-danger', 'text-warning');

                    if (result.is_student && result.approval_status === 'pending') {
                        membershipFeedback.classList.add('text-warning');
                    } else if (result.is_student && result.approval_status === 'rejected') {
                        membershipFeedback.classList.add('text-danger');
                    } else if (result.eligible) {
                        membershipFeedback.classList.add('text-success');
                    } else if (!responseOk) {
                        membershipFeedback.classList.add('text-danger');
                    } else {
                        membershipFeedback.classList.add('text-danger');
                    }

                    refreshCheckoutDiscounts();
                }

                function handleQueryMessage() {
                    const params = new URLSearchParams(window.location.search);
                    const result = params.get('payment_result');
                    if (!result) {
                        return;
                    }
                    const message = params.get('payment_message') || (result === 'success' ? 'Payment completed.' :
                        'Payment failed.');
                    showToast(result === 'success' ? 'success' : 'error', message);
                    if (params.get('clear_cart') === '1') {
                        localStorage.removeItem('degchi_cart');
                        if (typeof renderCartDrawer === 'function') {
                            renderCartDrawer();
                        }
                    }
                    window.history.replaceState({}, document.title, window.location.pathname);
                }

                async function checkMemberCardEligibility(cardNumber) {
                    if (!cardNumber) {
                        memberDiscountRate = 0;
                        verifiedMember = { verified: false, eligible: false, is_student: false, approval_status: 'approved', member_type: 'standard', first_order_discount_used: false };
                        membershipFeedback.textContent =
                            'Enter your membership card number to check eligibility for first-order or golden card discounts.';
                        membershipFeedback.classList.remove('text-success', 'text-danger', 'text-warning');
                        refreshCheckoutDiscounts(0);
                        return;
                    }

                    try {
                        const { original } = getCartMoneyTotals();
                        const response = await fetch(
                            `{{ route('frontend.member.check') }}?member_card_number=${encodeURIComponent(cardNumber)}&order_total=${encodeURIComponent(original || getCurrentSubtotal())}`
                            );
                        const result = await response.json();
                        applyMemberCheckResult(result, response.ok);
                    } catch (error) {
                        memberDiscountRate = 0;
                        verifiedMember = { verified: false, eligible: false, is_student: false, approval_status: 'approved', member_type: 'standard', first_order_discount_used: false };
                        membershipFeedback.textContent = 'Unable to verify membership card at the moment.';
                        membershipFeedback.classList.remove('text-success', 'text-warning');
                        membershipFeedback.classList.add('text-danger');
                        refreshCheckoutDiscounts();
                    }
                }

                async function applyCouponInternal(code, silent = false) {
                    const subtotal = getCurrentSubtotal();

                    if (!code) {
                        couponFeedback.textContent = 'Please enter a coupon code.';
                        couponFeedback.className = 'form-text mt-2 text-danger';
                        return;
                    }

                    applyCouponBtn.disabled = true;
                    const originalBtnText = applyCouponBtn.innerHTML;
                    applyCouponBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                    try {
                        const response = await fetch('{{ route('frontend.coupon.apply') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ code: code, order_total: subtotal }),
                        });

                        const result = await response.json();

                        if (response.ok && result.valid) {
                            appliedCoupon = result;
                            localStorage.setItem('degchi_coupon', JSON.stringify({ code: result.code }));
                            couponFeedback.textContent = result.message;
                            couponFeedback.className = 'form-text mt-2 text-success';
                            if (!silent) showToast('success', result.message);
                        } else {
                            appliedCoupon = null;
                            localStorage.removeItem('degchi_coupon');
                            couponFeedback.textContent = result.message || 'Invalid coupon code.';
                            couponFeedback.className = 'form-text mt-2 text-danger';
                            if (!silent) showToast('error', result.message || 'Invalid coupon code.');
                        }
                    } catch (error) {
                        appliedCoupon = null;
                        couponFeedback.textContent = 'Unable to verify coupon right now.';
                        couponFeedback.className = 'form-text mt-2 text-danger';
                    } finally {
                        applyCouponBtn.disabled = false;
                        applyCouponBtn.innerHTML = originalBtnText;
                        refreshCheckoutDiscounts(subtotal);
                    }
                }

                function loadStoredCoupon() {
                    try {
                        const stored = JSON.parse(localStorage.getItem('degchi_coupon') || 'null');
                        if (stored && stored.code) {
                            couponCodeInput.value = stored.code;
                            applyCouponInternal(stored.code, true);
                        }
                    } catch (e) {}
                }

                if (applyCouponBtn) {
                    applyCouponBtn.addEventListener('click', function () {
                        applyCouponInternal(couponCodeInput.value.trim().toUpperCase());
                    });
                }

                if (couponCodeInput) {
                    couponCodeInput.addEventListener('keydown', function (e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            applyCouponInternal(this.value.trim().toUpperCase());
                        }
                    });
                }

                if (removeCouponLink) {
                    removeCouponLink.addEventListener('click', function (e) {
                        e.preventDefault();
                        appliedCoupon = null;
                        couponCodeInput.value = '';
                        localStorage.removeItem('degchi_coupon');
                        couponFeedback.textContent = '';
                        refreshCheckoutDiscounts(parseFloat(orderTotalInput?.value) || 0);
                    });
                }

                function buildFormData(form) {
                    const formData = new FormData(form);
                    formData.set('_token', document.querySelector('input[name="_token"]').value);
                    return formData;
                }

                if (memberCardInput) {
                    memberCardInput.addEventListener('input', function() {
                        if (!this.value.trim()) {
                            memberDiscountRate = 0;
                            verifiedMember = { verified: false, eligible: false, is_student: false, approval_status: 'approved', member_type: 'standard', first_order_discount_used: false };
                        }
                        refreshCheckoutDiscounts();
                    });

                    memberCardInput.addEventListener('change', function() {
                        checkMemberCardEligibility(this.value.trim());
                    });
                }

                // Re-apply discounts whenever app.js finishes rendering the cart summary
                document.addEventListener('cartSummaryRendered', function(e) {
                    const subtotal = e.detail.total || 0;

                    if (memberCardInput && memberCardInput.value.trim() && !verifiedMember.verified) {
                        checkMemberCardEligibility(memberCardInput.value.trim());
                        return;
                    }

                    refreshCheckoutDiscounts(subtotal);
                });

                if (checkoutForm) {
                    checkoutForm.addEventListener('submit', async function(event) {
                        event.preventDefault();

                        if (!submitButton) {
                            return;
                        }

                        const restrictedMsg = (window.DEGCHI_MEMBER && window.DEGCHI_MEMBER.accountRestrictedMessage)
                            || 'Your account temporary suspand. contact our help line';

                        if (window.DEGCHI_MEMBER && window.DEGCHI_MEMBER.loggedIn && window.DEGCHI_MEMBER.canOrderAndComment === false) {
                            alert(restrictedMsg);
                            return;
                        }

                        if (verifiedMember && verifiedMember.account_restricted) {
                            alert(restrictedMsg);
                            return;
                        }

                        submitButton.disabled = true;
                        submitButton.innerHTML =
                            '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...';

                        try {
                            const response = await fetch(checkoutForm.action, {
                                method: 'POST',
                                body: buildFormData(checkoutForm),
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            });

                            const result = await response.json();

                            if (!response.ok) {
                                if (result.account_restricted) {
                                    alert(result.message || restrictedMsg);
                                } else if (result.errors) {
                                    const messages = Object.values(result.errors).flat().join('\n');
                                    showToast('error', messages);
                                } else {
                                    showToast('error', result.message || 'Unable to place order.');
                                }
                                return;
                            }

                            if (result.redirect_url) {
                                if (result.clear_cart) {
                                    localStorage.removeItem('degchi_cart');
                                    localStorage.removeItem('degchi_coupon');
                                }
                                window.location.href = result.redirect_url;
                                return;
                            }

                            if (result.success) {
                                if (result.clear_cart) {
                                    localStorage.removeItem('degchi_cart');
                                    localStorage.removeItem('degchi_coupon');
                                }
                                showToast('success', result.message || 'Order placed successfully.');
                                checkoutForm.reset();
                                document.getElementById('orderSummaryList').innerHTML =
                                    '<div class="text-center py-5 text-muted" style="font-size: 0.9rem;">Your selected products will appear here.</div>';
                                subtotalDisplay.textContent = '৳ 0.00';
                                totalDisplay.textContent = '৳ 0.00';
                                discountDisplay.textContent = '- ৳ 0.00';
                                document.getElementById('itemCount').textContent = '(0 items)';
                                membershipFeedback.textContent =
                                    'Enter your membership card number to check eligibility for a 10% discount.';
                                membershipFeedback.classList.remove('text-success', 'text-danger');

                                appliedCoupon = null;
                                couponCodeInput.value = '';
                                couponFeedback.textContent = '';
                                couponDiscountRow.style.display = 'none';

                                shippingCharge = SHIPPING_CHARGE;
                                if (shippingChargeDisplay) shippingChargeDisplay.textContent = `৳ ${shippingCharge.toFixed(2)}`;
                                if (shippingChargeHidden) shippingChargeHidden.value = shippingCharge;
                            }
                        } catch (error) {
                            showToast('error', error.message || 'Server error while placing order.');
                        } finally {
                            submitButton.disabled = false;
                            submitButton.innerHTML =
                                '<i class="bi bi-lock-fill me-2" style="opacity: 0.7;"></i> Place Order Now';
                        }
                    });
                }

                handleQueryMessage();
                loadStoredCoupon();

                @if($loggedInMemberDiscount ?? null)
                if (membershipFeedback && verifiedMember.verified) {
                    membershipFeedback.textContent = @json($loggedInMemberDiscount['message'] ?? '');
                    membershipFeedback.classList.remove('text-success', 'text-danger', 'text-warning');
                    @if($loggedInMemberDiscount['eligible'] ?? false)
                    membershipFeedback.classList.add('text-success');
                    @else
                    membershipFeedback.classList.add('text-danger');
                    @endif
                }
                @endif

                @if($loggedInMember ?? null)
                if (memberCardInput && memberCardInput.value.trim()) {
                    checkMemberCardEligibility(memberCardInput.value.trim());
                }
                @endif

                // Initial offer discount calculation on page load
                console.log('=== Checkout Page Loaded ===');
                console.log('Active offers available:', activeOffers);

                const initialCart = JSON.parse(localStorage.getItem('degchi_cart') || '[]');
                console.log('Cart on page load:', initialCart);

                // Initial totals (cartSummaryRendered also refreshes after app.js paints items)
                refreshCheckoutDiscounts();

                // Watch subtotal only when cart script changes it — skip while we update it ourselves
                if (subtotalDisplay && typeof MutationObserver !== 'undefined') {
                    const subtotalObserver = new MutationObserver(function() {
                        if (isRefreshingCheckoutDiscounts) {
                            return;
                        }
                        refreshCheckoutDiscounts();
                    });
                    subtotalObserver.observe(subtotalDisplay, { childList: true, characterData: true, subtree: true });
                }

                const flashMessage = @json(session('success') ?? session('error'));
                const flashType = @json(session('success') ? 'success' : 'error');
                if (flashMessage) {
                    showToast(flashType, flashMessage);
                }
            });
        </script>
    @endpush
@endsection



{{-- *

  When user will place order (COD/online payment) then autometically based on user info [name, email, phone]
  there will  create a dashboard. whereas they will see just there orders records. and after 
  creating the account he will recevie a SMS in phone number with dashboard access credientials. 

  01. for first register [membership card] user will get 30% discount upto every food item [based on there card number]
     - membership card holder validity is one year 
  02. if the card holder purchase upto 2000 then he will be converted into goden card holder.
     - golden card holder will get 10% for every food item
     - validity of the golden card is 5 years
     - when user will directly apply for the golden card then it will show a popup to show be eligible for the
     goden card turm and conditions
  03. order table dashboard have to add "Action" column to show particular user order details and add other stuffs 
  that you are thinking to make it better experience 
  04. if user is a student then he will get 35% for the first time order. but condition is he have to submit his 
  student card [student card is nullable for other applients]
  05. in /card-apply page application form have to be update 


* --}}