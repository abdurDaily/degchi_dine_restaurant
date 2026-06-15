@extends('frontend.layout')
@section('frontend_content')

    <style>
        /* --- Premium Formal Glassmorphism Theme --- */
        :root {
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.9);
            --glass-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.05), 0 1px 3px rgba(0, 0, 0, 0.03);
            --glass-blur: blur(24px);
            --brand-dark: #111111;
            --text-muted: #6b7280;
            --border-light: rgba(0, 0, 0, 0.06);
            --brand-accent: #e2136e;
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
                radial-gradient(circle at 10% 20%, rgba(226, 19, 110, 0.03) 0%, transparent 40%),
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
            border: 1.5px solid rgba(226, 19, 110, 0.15);
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
            box-shadow: 0 10px 20px -6px #ef8b1f85;
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
            border-radius: 1.25rem;
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

        /* --- Payment Radio Cards --- */
        .checkout-payment-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .checkout-payment-card {
            cursor: pointer;
            margin: 0;
        }

        .checkout-payment-card input[type="radio"] {
            display: none;
        }

        .checkout-payment-body {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 1.1rem 1.25rem;
            font-size: 0.95rem;
            font-weight: 600;
            color: #4b5563;
            background: transparent;
            transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .checkout-payment-card:hover .checkout-payment-body {
            border-color: rgba(0, 0, 0, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.04);
        }

        .checkout-payment-card input:checked+.checkout-payment-body {
            border-color: var(--brand-dark);
            color: var(--brand-dark);
            background: rgba(255, 255, 255, 0.8);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .checkout-payment-icon {
            font-size: 1.4rem;
            color: #9ca3af;
            transition: color 0.3s ease;
        }

        .checkout-payment-card input:checked+.checkout-payment-body .checkout-payment-icon {
            color: var(--brand-dark);
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
                    <span class="cart-step">
                        <i class="bi bi-check-circle"></i> Confirmed
                    </span>
                </div>

            </div>
        </div>
    </div>

    <section class="section-block cart-page-section">
        <div class="container px-4 px-lg-5">
            <div class="row g-4 align-items-start">
                <div class="col-lg-7">
                    <form id="checkoutForm" class="checkout-form-card" action="{{ route('frontend.order.store') }}"
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

                        <div class="checkout-form-section px-0 pt-0">
                            <h6 class="checkout-form-heading">
                                <span class="checkout-step-num">1</span>
                                Shipping Address
                            </h6>
                            <div class="row g-4">
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
                                        style="font-size: 0.75rem;">Enter membership card number to check eligibility for first-order or golden card discounts. Student first-order offers require admin approval.</div>
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

                        <div class="checkout-form-divider my-4" style="height: 1px; background: var(--border-light);"></div>

                        <div class="checkout-form-section px-0 pb-0">
                            <h6 class="checkout-form-heading">
                                <span class="checkout-step-num">2</span>
                                Payment Method
                            </h6>
                            <div class="checkout-payment-options mt-3">
                                <label class="checkout-payment-card">
                                    <input type="radio" name="payment_method" value="cod" checked />
                                    <span class="checkout-payment-body">
                                        <i class="bi bi-cash-coin checkout-payment-icon"></i>
                                        <span>Cash on Delivery</span>
                                    </span>
                                </label>
                                <label class="checkout-payment-card">
                                    <input type="radio" name="payment_method" value="sslcommerz" />
                                    <span class="checkout-payment-body">
                                        <i class="bi bi-credit-card-fill checkout-payment-icon"
                                            style="color: var(--brand-accent);"></i>
                                        <span>Online Payment</span>
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
                            <div class="cart-summary-row">
                                <span>Shipping</span>
                                <span class="cart-free-tag">Free</span>
                            </div>
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

                // Track current membership discount amount (set when card is verified)
                let currentMemberDiscount = 0;
                let verifiedMember = {
                    verified: false,
                    eligible: false,
                    is_student: false,
                    approval_status: 'approved',
                };

                // Calculate offer discount for cart items
                function calculateOfferDiscount(cartItems) {
                    let totalOfferDiscount = 0;
                    let bestOfferName = '';
                    let bestOfferPercent = 0;

                    if (!Array.isArray(cartItems) || cartItems.length === 0 || activeOffers.length === 0) {
                        return { discount: 0, offerName: '', offerPercent: 0 };
                    }

                    const hasMembershipCard = memberCardInput && memberCardInput.value.trim() !== '';

                    const applicableOffers = activeOffers.filter(offer => {
                        if (offer.is_first_order === true || offer.is_first_order === 1) {
                            if (!hasMembershipCard) {
                                return false;
                            }
                            if (!verifiedMember.verified) {
                                return false;
                            }
                            if (verifiedMember.is_student && verifiedMember.approval_status !== 'approved') {
                                return false;
                            }
                            if (!verifiedMember.eligible) {
                                return false;
                            }
                        }
                        return true;
                    });

                    // For each cart item, find applicable offers
                    cartItems.forEach(item => {
                        const variationId = item.variation_id || item.id;
                        console.log('Checking item:', item.title, 'variation_id:', variationId);
                        
                        if (!variationId) {
                            console.warn('No variation_id for item:', item);
                            return;
                        }

                        // Find offers that apply to this variation
                        let bestItemOffer = null;
                        
                        applicableOffers.forEach(offer => {
                            // Check if offer applies to this item
                            const appliesTo = offer.offer_type === 'all_items' || 
                                (Array.isArray(offer.menu_variations) && 
                                 offer.menu_variations.some(v => v.id == variationId));
                            
                            console.log(`  Offer "${offer.name}" (${offer.offer_type}) applies:`, appliesTo);
                            
                            if (appliesTo && (!bestItemOffer || offer.discount_percent > bestItemOffer.discount_percent)) {
                                bestItemOffer = offer;
                            }
                        });

                        // Calculate discount for this item
                        if (bestItemOffer) {
                            const itemTotal = (item.price || 0) * (item.quantity || 1);
                            const itemDiscount = itemTotal * (bestItemOffer.discount_percent / 100);
                            totalOfferDiscount += itemDiscount;
                            
                            console.log(`  ✓ Applied ${bestItemOffer.name} (${bestItemOffer.discount_percent}%): ৳${itemDiscount.toFixed(2)}`);

                            if (bestItemOffer.discount_percent > bestOfferPercent) {
                                bestOfferPercent = bestItemOffer.discount_percent;
                                bestOfferName = bestItemOffer.name;
                            }
                        } else {
                            console.log('  ✗ No offer applies to this item');
                        }
                    });

                    console.log('Total offer discount:', totalOfferDiscount);

                    return {
                        discount: parseFloat(totalOfferDiscount.toFixed(2)),
                        offerName: bestOfferName,
                        offerPercent: bestOfferPercent
                    };
                }

                function updateTotals(subtotal, bestDiscount, offerInfo) {
                    offerInfo = offerInfo || { discount: 0, offerName: '', offerPercent: 0 };
                    const memberDiscount = currentMemberDiscount || 0;
                    const offerDiscount = offerInfo.discount;

                    console.log('updateTotals called:', { subtotal, bestDiscount, memberDiscount, offerDiscount });

                    // Update offer display
                    if (offerDiscount > 0) {
                        offerDiscountRow.style.display = '';
                        offerDiscountEl.textContent = `- ৳ ${offerDiscount.toFixed(2)}`;
                        if (offerNameEl) offerNameEl.textContent = offerInfo.offerName || 'Offer Discount';
                        if (offerBadgeEl) offerBadgeEl.textContent = `${offerInfo.offerPercent}% OFF`;
                    } else {
                        offerDiscountRow.style.display = 'none';
                    }

                    if (offerDiscount >= memberDiscount) {
                        // Offer wins — membership row shows 0
                        discountDisplay.textContent = `- ৳ 0.00`;
                    } else {
                        // Membership wins — offer row shows 0 (or hide it)
                        discountDisplay.textContent = `- ৳ ${memberDiscount.toFixed(2)}`;
                        if (offerDiscount > 0) {
                            offerDiscountRow.style.display = 'none';
                        }
                    }

                    const finalTotal = Math.max(0, subtotal - bestDiscount);
                    totalDisplay.textContent = `৳ ${finalTotal.toFixed(2)}`;
                    
                    // Update hidden input for form submission
                    if (orderTotalInput) {
                        orderTotalInput.value = subtotal.toFixed(2); // Send original subtotal, backend will recalculate discount
                    }
                    
                    console.log('Final total displayed:', finalTotal);
                }

                function updateDiscountDisplay(memberDiscountAmount) {
                    currentMemberDiscount = memberDiscountAmount;
                    const subtotal = parseFloat(orderTotalInput.value) || 0;
                    
                    // Get cart items from localStorage
                    const cart = JSON.parse(localStorage.getItem('degchi_cart') || '[]');
                    const offerInfo = calculateOfferDiscount(cart);
                    
                    const bestDiscount = Math.max(memberDiscountAmount, offerInfo.discount);
                    updateTotals(subtotal, bestDiscount, offerInfo);
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
                    }
                    window.history.replaceState({}, document.title, window.location.pathname);
                }

                async function checkMemberCardEligibility(cardNumber) {
                    if (!cardNumber) {
                        verifiedMember = { verified: false, eligible: false, is_student: false, approval_status: 'approved' };
                        membershipFeedback.textContent =
                            'Enter your membership card number to check eligibility for first-order or golden card discounts.';
                        membershipFeedback.classList.remove('text-success', 'text-danger', 'text-warning');
                        updateDiscountDisplay(0);
                        return;
                    }

                    try {
                        const response = await fetch(
                            `{{ route('frontend.member.check') }}?member_card_number=${encodeURIComponent(cardNumber)}`
                            );
                        const result = await response.json();

                        verifiedMember = {
                            verified: true,
                            eligible: !!result.eligible,
                            is_student: !!result.is_student,
                            approval_status: result.approval_status || 'approved',
                        };

                        if (!response.ok) {
                            membershipFeedback.textContent = result.message || 'Unable to verify membership card.';
                            membershipFeedback.classList.remove('text-success', 'text-warning');
                            membershipFeedback.classList.add('text-danger');
                            updateDiscountDisplay(0);
                            return;
                        }

                        membershipFeedback.textContent = result.message;
                        membershipFeedback.classList.remove('text-success', 'text-danger', 'text-warning');

                        if (result.is_student && result.approval_status === 'pending') {
                            membershipFeedback.classList.add('text-warning');
                        } else if (result.is_student && result.approval_status === 'rejected') {
                            membershipFeedback.classList.add('text-danger');
                        } else if (result.eligible) {
                            membershipFeedback.classList.add('text-success');
                        } else {
                            membershipFeedback.classList.add('text-danger');
                        }

                        const subtotal = parseFloat(orderTotalInput.value) || 0;
                        currentMemberDiscount = result.eligible
                            ? parseFloat((subtotal * (result.discount_rate / 100)).toFixed(2))
                            : 0;
                        updateDiscountDisplay(currentMemberDiscount);
                    } catch (error) {
                        verifiedMember = { verified: false, eligible: false, is_student: false, approval_status: 'approved' };
                        membershipFeedback.textContent = 'Unable to verify membership card at the moment.';
                        membershipFeedback.classList.remove('text-success', 'text-warning');
                        membershipFeedback.classList.add('text-danger');
                        updateDiscountDisplay(0);
                    }
                }

                function buildFormData(form) {
                    const formData = new FormData(form);
                    formData.set('_token', document.querySelector('input[name="_token"]').value);
                    return formData;
                }

                if (memberCardInput) {
                    memberCardInput.addEventListener('input', function() {
                        if (!this.value.trim()) {
                            verifiedMember = { verified: false, eligible: false, is_student: false, approval_status: 'approved' };
                            currentMemberDiscount = 0;
                        }
                        const subtotal = parseFloat(orderTotalInput.value) || 0;
                        const cart = JSON.parse(localStorage.getItem('degchi_cart') || '[]');
                        const offerInfo = calculateOfferDiscount(cart);
                        const bestDiscount = Math.max(currentMemberDiscount, offerInfo.discount);
                        updateTotals(subtotal, bestDiscount, offerInfo);
                    });

                    memberCardInput.addEventListener('change', function() {
                        checkMemberCardEligibility(this.value.trim());
                    });
                }

                // Re-apply discounts whenever app.js finishes rendering the cart summary
                document.addEventListener('cartSummaryRendered', function(e) {
                    const subtotal = e.detail.total || 0;
                    const cart = JSON.parse(localStorage.getItem('degchi_cart') || '[]');
                    const offerInfo = calculateOfferDiscount(cart);
                    const bestDiscount = Math.max(currentMemberDiscount, offerInfo.discount);
                    updateTotals(subtotal, bestDiscount, offerInfo);
                });

                if (checkoutForm) {
                    checkoutForm.addEventListener('submit', async function(event) {
                        event.preventDefault();

                        if (!submitButton) {
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
                                if (result.errors) {
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
                                }
                                window.location.href = result.redirect_url;
                                return;
                            }

                            if (result.success) {
                                if (result.clear_cart) {
                                    localStorage.removeItem('degchi_cart');
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

                @if($loggedInMember ?? null)
                if (memberCardInput && memberCardInput.value.trim()) {
                    checkMemberCardEligibility(memberCardInput.value.trim());
                }
                @endif

                // Initial offer discount calculation on page load
                console.log('=== Checkout Page Loaded ===');
                console.log('Active offers available:', activeOffers);
                
                // Trigger initial calculation
                const initialCart = JSON.parse(localStorage.getItem('degchi_cart') || '[]');
                console.log('Cart on page load:', initialCart);
                
                if (initialCart.length > 0) {
                    // Wait a bit for cart to render, then calculate
                    setTimeout(function() {
                        const subtotal = parseFloat(document.getElementById('order_total')?.value || 0);
                        console.log('Initial subtotal:', subtotal);
                        
                        if (subtotal > 0) {
                            const offerInfo = calculateOfferDiscount(initialCart);
                            const bestDiscount = Math.max(currentMemberDiscount, offerInfo.discount);
                            updateTotals(subtotal, bestDiscount, offerInfo);
                            console.log('Initial discount calculated and applied');
                        }
                    }, 500);
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