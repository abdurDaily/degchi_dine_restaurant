@extends('frontend.layout')

@section('meta_title', 'Checkout')
@section('meta_robots', 'noindex, nofollow')

@section('frontend_content')

<x-frontend.page-header
    title="Complete Your Order"
    subtitle="Review your details and place your order."
    eyebrow="YOUR ORDER"
>
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
        <span>
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

<section class="cart-page-section">
    <div class="container px-4 px-lg-5">

        <!-- Checkout Grid -->
        <div class="row g-4 align-items-start">

            <!-- Left: Form -->
            <div class="col-lg-7">
                <form id="checkoutForm" class="checkout-form-card" action="{{ route('frontend.order.store') }}" method="POST">
                    @csrf

                    <div id="checkoutMessages"></div>

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if (session('clear_cart'))
                        <script>localStorage.removeItem('degchi_cart');</script>
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

                    <!-- Shipping Address -->
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
                                    <div class="alert alert-success py-2 px-3 mb-2" style="font-size: 0.82rem; border-radius: 0.75rem;">
                                        Signed in as <strong>{{ $loggedInMember->name }}</strong>. Your card is applied automatically.
                                    </div>
                                @endif
                                <div class="checkout-input-wrap">
                                    <i class="bi bi-credit-card-2-front checkout-input-icon"></i>
                                    <input class="form-control checkout-input" type="text" name="member_card_number"
                                        id="memberCardNumber" placeholder="Enter registered card number"
                                        value="{{ $loggedInMember->unique_card_number ?? '' }}" />
                                </div>
                                <div id="membershipFeedback" class="form-text mt-2"
                                    style="font-size: 0.75rem; color: rgba(255,255,255,0.55);">30% or 35% off applies on your first order only. After ৳2,000 total spend, you unlock 10% off every order as a Golden member.</div>
                            </div>
                            <div class="col-md-12 d-none">
                                <label class="form-label">Order Total (৳)</label>
                                <div class="checkout-input-wrap">
                                    <i class="bi bi-cash-stack checkout-input-icon"></i>
                                    <input class="form-control checkout-input" required readonly type="number"
                                        step="0.01" min="0" name="order_total" id="order_total" value="0" />
                                </div>
                            </div>
                            <input type="hidden" name="items" id="cart_items" value="[]" />
                        </div>
                    </div>

                    <!-- Coupon Code -->
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
                                <button type="button" id="applyCouponBtn" class="btn checkout-btn-primary w-100">
                                    Apply
                                </button>
                            </div>
                        </div>
                        <div id="couponFeedback" class="form-text mt-2" style="font-size: 0.75rem;"></div>
                        <input type="hidden" name="coupon_code" id="couponCodeHidden" value="" />
                        <input type="hidden" name="coupon_discount" id="couponDiscountHidden" value="0" />
                    </div>

                    <!-- Payment Method -->
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

            <!-- Right: Summary -->
            <div class="col-lg-5">
                <div class="checkout-summary-card checkout-summary-sticky">
                    <div class="checkout-summary-header">
                        <i class="bi bi-bag-check me-2"></i> Order Summary
                    </div>

                    <div class="checkout-summary-items-wrap" id="orderSummaryList">
                        <template id="checkoutItemTpl">
                            <div class="checkout-order-item" data-item-id="">
                                <div class="checkout-order-img-wrap">
                                    <img src="" alt="" class="checkout-order-img" />
                                </div>
                                <div class="checkout-order-body">
                                    <div class="checkout-order-top">
                                        <p class="checkout-order-name"></p>
                                        <span class="checkout-order-tag"></span>
                                    </div>
                                    <div class="checkout-order-bottom">
                                        <span class="checkout-order-price text-white"></span>
                                        <strong class="checkout-order-subtotal"></strong>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <div class="checkout-summary-empty text-center py-5" style="font-size: 0.9rem; color: rgba(255,255,255,0.45);">
                            <i class="bi bi-bag" style="font-size: 2rem; display: block; margin-bottom: 0.5rem; opacity: 0.3;"></i>
                            Your selected products will appear here.
                        </div>
                    </div>

                    <div class="checkout-summary-body">
                        <div class="checkout-summary-row">
                            <span>Subtotal <small class="ms-1" id="itemCount" style="color: rgba(255,255,255,0.45);">(0 items)</small></span>
                            <span id="checkoutSubtotal" class="checkout-summary-val">৳ 0.00</span>
                        </div>
                        <div class="checkout-summary-row">
                            <span>Membership Discount</span>
                            <span id="checkoutDiscount" class=" fw-bold">- ৳ 0.00</span>
                        </div>
                        <div class="checkout-summary-row" id="offerDiscountRow" style="display: none;">
                            <span id="offerDiscountLabel">
                                <span id="offerName">Offer Discount</span>
                                <span class="badge ms-1" id="offerBadge" style="background:var(--brand-secondary);color:#fff;font-size:.68rem;padding:2px 7px;border-radius:20px;"></span>
                            </span>
                            <span id="checkoutOfferDiscount" class="text-danger fw-bold">- ৳ 0.00</span>
                        </div>
                        <div class="checkout-summary-row" id="couponDiscountRow" style="display: none;">
                            <span>
                                Coupon
                                <span class="badge ms-1" id="couponCodeBadge" style="background:var(--brand);color:#fff;font-size:.68rem;padding:2px 7px;border-radius:20px;"></span>
                                <a href="#" id="removeCouponLink" class="ms-1 text-danger" style="font-size:.7rem;text-decoration:underline;">remove</a>
                            </span>
                            <span id="checkoutCouponDiscount" class="text-success fw-bold">- ৳ 0.00</span>
                        </div>
                        <div class="checkout-summary-row">
                            <span>Delivery Charge</span>
                            <span id="shippingChargeDisplay" class="fw-bold">৳ 60.00</span>
                        </div>
                        <div class="delivery-info-badge">
                            <i class="bi bi-info-circle-fill"></i>
                            ডেলিভারি লোকেশন ১ কিলোমিটারের বেশি হলে প্রতি কিলোমিটারে ২০ টাকা করে যুক্ত হবে।
                        </div>
                        <div class="checkout-summary-divider"></div>
                        <input type="hidden" name="shipping_charge" id="shippingChargeHidden" value="60" />
                        <div class="checkout-summary-total-row">
                            <span>Total</span>
                            <strong id="checkoutTotal" class="checkout-summary-total-val">৳ 0.00</strong>
                        </div>
                    </div>

                    <div class="checkout-summary-footer">
                        <button type="submit" id="placeOrderBtn" form="checkoutForm" class="btn checkout-btn-primary w-100">
                            <i class="bi bi-lock-fill me-2" style="opacity: 0.7;"></i> Place Order Now
                        </button>
                        <p class="text-center mt-3 mb-0" style="font-size: 0.78rem; color: rgba(255,255,255,0.45);">
                            <i class="bi bi-shield-check me-1"></i> 100% Secure & Safe Checkout
                        </p>
                    </div>
                </div>

                <div class="mt-4 text-center text-lg-start">
                    <a href="{{ route('frontend.addtocart') }}" class="checkout-continue-link d-inline-flex align-items-center">
                        <i class="bi bi-arrow-left me-2"></i> Continue Shopping
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

                const SHIPPING_CHARGE = 60;
                const shippingChargeDisplay = document.getElementById('shippingChargeDisplay');
                const shippingChargeHidden = document.getElementById('shippingChargeHidden');
                let shippingCharge = SHIPPING_CHARGE;

                function showToast(type, text) {
                    if (typeof toastr !== 'undefined') {
                        toastr.options = { closeButton: true, progressBar: true, positionClass: 'toast-top-right' };
                        toastr[type](text);
                    } else { alert(text); }
                }

                const activeOffers = @json($activeOffers ?? []);
                const offerDiscountEl = document.getElementById('checkoutOfferDiscount');
                const offerDiscountRow = document.getElementById('offerDiscountRow');
                const offerNameEl = document.getElementById('offerName');
                const offerBadgeEl = document.getElementById('offerBadge');

                const couponCodeInput = document.getElementById('couponCodeInput');
                const applyCouponBtn = document.getElementById('applyCouponBtn');
                const couponFeedback = document.getElementById('couponFeedback');
                const couponDiscountRow = document.getElementById('couponDiscountRow');
                const checkoutCouponDiscountEl = document.getElementById('checkoutCouponDiscount');
                const couponCodeBadgeEl = document.getElementById('couponCodeBadge');
                const couponCodeHidden = document.getElementById('couponCodeHidden');
                const couponDiscountHidden = document.getElementById('couponDiscountHidden');
                const removeCouponLink = document.getElementById('removeCouponLink');

                let appliedCoupon = null;
                let memberDiscountRate = 0;
                let verifiedMember = { verified: false, eligible: false, is_student: false, approval_status: 'approved', member_type: 'standard', first_order_discount_used: false };

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
                    if (verifiedMember.first_order_discount_used && verifiedMember.member_type !== 'golden') return 0;
                    if (!verifiedMember.eligible || memberDiscountRate <= 0) return 0;
                    return parseFloat((subtotal * (memberDiscountRate / 100)).toFixed(2));
                }
                function parseCurrencyText(text) {
                    const n = parseFloat((text || '').replace(/[^\d.]/g, ''));
                    return Number.isNaN(n) ? 0 : n;
                }
                function getCartMoneyTotals() {
                    try {
                        const cart = JSON.parse(localStorage.getItem('degchi_cart') || '[]');
                        let original = 0, effective = 0;
                        (Array.isArray(cart) ? cart : []).forEach(item => {
                            const qty = parseInt(item.quantity, 10) || 1;
                            original += (parseFloat(item.original_price ?? item.price) || 0) * qty;
                            effective += (parseFloat(item.price) || 0) * qty;
                        });
                        return { cart: Array.isArray(cart) ? cart : [], original: parseFloat(original.toFixed(2)), effective: parseFloat(effective.toFixed(2)) };
                    } catch (e) { return { cart: [], original: 0, effective: 0 }; }
                }
                function getCurrentSubtotal() {
                    const { effective } = getCartMoneyTotals();
                    if (effective > 0) return effective;
                    const displayed = parseCurrencyText(subtotalDisplay?.textContent);
                    if (displayed > 0) return displayed;
                    return parseFloat(orderTotalInput?.value) || 0;
                }
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
                    if (isRefreshingCheckoutDiscounts) return;
                    isRefreshingCheckoutDiscounts = true;
                    try {
                        const { cart, original, effective } = getCartMoneyTotals();
                        const offerInfo = calculateOfferDiscount(cart);
                        const foodOfferDiscount = Math.max(offerInfo.discount || 0, Math.max(0, parseFloat((original - effective).toFixed(2))));
                        const displaySubtotal = effective > 0 ? effective : original;
                        const memberDiscount = calculateMemberDiscount(displaySubtotal);
                        const displayOfferInfo = foodOfferDiscount > 0
                            ? { discount: 0, offerName: offerInfo.offerName, offerPercent: offerInfo.offerPercent }
                            : { discount: 0, offerName: '', offerPercent: 0 };
                        if (subtotalDisplay) {
                            const nextSubtotal = `৳ ${displaySubtotal.toFixed(2)}`;
                            if (subtotalDisplay.textContent !== nextSubtotal) subtotalDisplay.textContent = nextSubtotal;
                        }
                        if (orderTotalInput) {
                            const nextOriginal = original.toFixed(2);
                            if (orderTotalInput.value !== nextOriginal) orderTotalInput.value = nextOriginal;
                        }
                        const couponDiscount = calculateCouponDiscount(displaySubtotal);
                        updateTotals(displaySubtotal, memberDiscount, displayOfferInfo, memberDiscount, couponDiscount, { original, offerMeta: offerInfo });
                    } finally { isRefreshingCheckoutDiscounts = false; }
                }

                function offerEligibleForVerifiedMember(offer) {
                    if (offer.applicable_to === 'golden') return verifiedMember.verified && verifiedMember.eligible && verifiedMember.member_type === 'golden';
                    if (offer.applicable_to === 'student') {
                        if (offer.is_first_order && verifiedMember.first_order_discount_used) return false;
                        return verifiedMember.verified && verifiedMember.eligible && verifiedMember.is_student && verifiedMember.approval_status === 'approved';
                    }
                    if (offer.applicable_to === 'membership') {
                        if (offer.is_first_order && verifiedMember.first_order_discount_used) return false;
                        return verifiedMember.verified && verifiedMember.eligible && !verifiedMember.is_student;
                    }
                    if (offer.is_first_order === true || offer.is_first_order === 1) {
                        if (!verifiedMember.verified || !verifiedMember.eligible) return false;
                        if (verifiedMember.first_order_discount_used) return false;
                        if (verifiedMember.is_student && verifiedMember.approval_status !== 'approved') return false;
                        if (verifiedMember.member_type === 'golden') return false;
                    }
                    return true;
                }
                function calculateOfferDiscount(cartItems) {
                    let totalOfferDiscount = 0, bestOfferName = '', bestOfferPercent = 0;
                    if (!Array.isArray(cartItems) || cartItems.length === 0) return { discount: 0, offerName: '', offerPercent: 0 };
                    const hasMembershipCard = memberCardInput && memberCardInput.value.trim() !== '';
                    const memberLoggedIn = !!(window.DEGCHI_MEMBER && window.DEGCHI_MEMBER.loggedIn);
                    const applicableOffers = (activeOffers || []).filter(offer => {
                        if (offer.applicable_to !== 'all' || !(offer.discount_percent > 0)) return false;
                        if (offer.is_first_order) {
                            if (!hasMembershipCard && !memberLoggedIn) return false;
                            return offerEligibleForVerifiedMember(offer);
                        }
                        return true;
                    });
                    cartItems.forEach(item => {
                        const variationId = item.variation_id || item.id;
                        if (!variationId) return;
                        const basePrice = parseFloat(item.original_price ?? item.price) || 0;
                        const qty = parseInt(item.quantity, 10) || 1;
                        if (item.offer_applied && item.offer_percent > 0) {
                            const linked = applicableOffers.find(o => o.id == item.offer_id);
                            const raw = (activeOffers || []).find(o => o.id == item.offer_id);
                            if (raw && (raw.is_first_order || ['student', 'membership', 'golden'].includes(raw.applicable_to))) {
                                if (!linked && !offerEligibleForVerifiedMember(raw)) return;
                            }
                            const itemDiscount = basePrice * qty * (item.offer_percent / 100);
                            totalOfferDiscount += itemDiscount;
                            if (item.offer_percent > bestOfferPercent) { bestOfferPercent = item.offer_percent; bestOfferName = linked?.name || raw?.name || 'Offer Discount'; }
                            return;
                        }
                        let bestItemOffer = null;
                        applicableOffers.forEach(offer => {
                            const appliesTo = offer.offer_type === 'all_items' || (Array.isArray(offer.menu_variations) && offer.menu_variations.some(v => v.id == variationId));
                            if (appliesTo && (!bestItemOffer || offer.discount_percent > bestItemOffer.discount_percent)) bestItemOffer = offer;
                        });
                        if (bestItemOffer) {
                            const itemDiscount = basePrice * qty * (bestItemOffer.discount_percent / 100);
                            totalOfferDiscount += itemDiscount;
                            if (bestItemOffer.discount_percent > bestOfferPercent) { bestOfferPercent = bestItemOffer.discount_percent; bestOfferName = bestItemOffer.name; }
                        }
                    });
                    return { discount: parseFloat(totalOfferDiscount.toFixed(2)), offerName: bestOfferName, offerPercent: bestOfferPercent };
                }

                function updateTotals(subtotal, bestDiscount, offerInfo, memberDiscount, couponDiscount, meta) {
                    offerInfo = offerInfo || { discount: 0, offerName: '', offerPercent: 0 };
                    meta = meta || {};
                    memberDiscount = typeof memberDiscount === 'number' ? memberDiscount : 0;
                    couponDiscount = typeof couponDiscount === 'number' ? couponDiscount : calculateCouponDiscount(subtotal);
                    const offerDiscount = offerInfo.discount || 0;
                    if (offerDiscount > 0) {
                        offerDiscountRow.style.display = '';
                        offerDiscountEl.textContent = `- ৳ ${offerDiscount.toFixed(2)}`;
                        if (offerNameEl) offerNameEl.textContent = offerInfo.offerName || 'Offer Discount';
                        if (offerBadgeEl) offerBadgeEl.textContent = `${offerInfo.offerPercent}% OFF`;
                    } else { offerDiscountRow.style.display = 'none'; }
                    discountDisplay.textContent = memberDiscount > 0 ? `- ৳ ${memberDiscount.toFixed(2)}` : `- ৳ 0.00`;
                    if (appliedCoupon && couponDiscount > 0) {
                        couponDiscountRow.style.display = '';
                        checkoutCouponDiscountEl.textContent = `- ৳ ${couponDiscount.toFixed(2)}`;
                        couponCodeBadgeEl.textContent = appliedCoupon.code;
                    } else { couponDiscountRow.style.display = 'none'; }
                    couponCodeHidden.value = (appliedCoupon && couponDiscount > 0) ? appliedCoupon.code : '';
                    couponDiscountHidden.value = couponDiscount.toFixed(2);
                    const discountedProductTotal = Math.max(0, subtotal - bestDiscount - couponDiscount);
                    const deliveryChargeToAdd = typeof shippingCharge === 'number' ? shippingCharge : SHIPPING_CHARGE;
                    const finalTotal = discountedProductTotal + deliveryChargeToAdd;
                    totalDisplay.textContent = `৳ ${finalTotal.toFixed(2)}`;
                    if (orderTotalInput && typeof meta.original === 'number' && meta.original > 0) orderTotalInput.value = meta.original.toFixed(2);
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
                    const allowRate = responseOk && result.eligible && (isGolden || !usedFirstOrder);
                    memberDiscountRate = allowRate ? (result.discount_rate || 0) : 0;
                    verifiedMember = { verified: true, eligible: !!allowRate, is_student: !!result.is_student, approval_status: result.approval_status || 'approved', member_type: result.member_type || 'standard', first_order_discount_used: usedFirstOrder, account_restricted: false };
                    membershipFeedback.textContent = result.message || 'Unable to verify membership card.';
                    membershipFeedback.classList.remove('text-success', 'text-danger', 'text-warning');
                    if (result.is_student && result.approval_status === 'pending') membershipFeedback.classList.add('text-warning');
                    else if (result.is_student && result.approval_status === 'rejected') membershipFeedback.classList.add('text-danger');
                    else if (result.eligible) membershipFeedback.classList.add('text-success');
                    else if (!responseOk) membershipFeedback.classList.add('text-danger');
                    else membershipFeedback.classList.add('text-danger');
                    refreshCheckoutDiscounts();
                }

                function handleQueryMessage() {
                    const params = new URLSearchParams(window.location.search);
                    const result = params.get('payment_result');
                    if (!result) return;
                    const message = params.get('payment_message') || (result === 'success' ? 'Payment completed.' : 'Payment failed.');
                    showToast(result === 'success' ? 'success' : 'error', message);
                    if (params.get('clear_cart') === '1') { localStorage.removeItem('degchi_cart'); if (typeof renderCartDrawer === 'function') renderCartDrawer(); }
                    window.history.replaceState({}, document.title, window.location.pathname);
                }

                async function checkMemberCardEligibility(cardNumber) {
                    if (!cardNumber) {
                        memberDiscountRate = 0;
                        verifiedMember = { verified: false, eligible: false, is_student: false, approval_status: 'approved', member_type: 'standard', first_order_discount_used: false };
                        membershipFeedback.textContent = 'Enter your membership card number to check eligibility for first-order or golden card discounts.';
                        membershipFeedback.classList.remove('text-success', 'text-danger', 'text-warning');
                        refreshCheckoutDiscounts(0);
                        return;
                    }
                    try {
                        const { original } = getCartMoneyTotals();
                        const response = await fetch(`{{ route('frontend.member.check') }}?member_card_number=${encodeURIComponent(cardNumber)}&order_total=${encodeURIComponent(original || getCurrentSubtotal())}`);
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
                    if (!code) { couponFeedback.textContent = 'Please enter a coupon code.'; couponFeedback.className = 'form-text mt-2 text-danger'; return; }
                    applyCouponBtn.disabled = true;
                    const originalBtnText = applyCouponBtn.innerHTML;
                    applyCouponBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
                    try {
                        const response = await fetch('{{ route('frontend.coupon.apply') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value, 'Accept': 'application/json' },
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
                        if (stored && stored.code) { couponCodeInput.value = stored.code; applyCouponInternal(stored.code, true); }
                    } catch (e) {}
                }

                if (applyCouponBtn) applyCouponBtn.addEventListener('click', function () { applyCouponInternal(couponCodeInput.value.trim().toUpperCase()); });
                if (couponCodeInput) couponCodeInput.addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); applyCouponInternal(this.value.trim().toUpperCase()); } });
                if (removeCouponLink) removeCouponLink.addEventListener('click', function (e) { e.preventDefault(); appliedCoupon = null; couponCodeInput.value = ''; localStorage.removeItem('degchi_coupon'); couponFeedback.textContent = ''; refreshCheckoutDiscounts(parseFloat(orderTotalInput?.value) || 0); });

                function buildFormData(form) {
                    const formData = new FormData(form);
                    formData.set('_token', document.querySelector('input[name="_token"]').value);
                    return formData;
                }

                if (memberCardInput) {
                    memberCardInput.addEventListener('input', function() {
                        if (!this.value.trim()) { memberDiscountRate = 0; verifiedMember = { verified: false, eligible: false, is_student: false, approval_status: 'approved', member_type: 'standard', first_order_discount_used: false }; }
                        refreshCheckoutDiscounts();
                    });
                    memberCardInput.addEventListener('change', function() { checkMemberCardEligibility(this.value.trim()); });
                }

                document.addEventListener('cartSummaryRendered', function(e) {
                    const subtotal = e.detail.total || 0;
                    if (memberCardInput && memberCardInput.value.trim() && !verifiedMember.verified) { checkMemberCardEligibility(memberCardInput.value.trim()); return; }
                    refreshCheckoutDiscounts(subtotal);
                });

                if (checkoutForm) {
                    checkoutForm.addEventListener('submit', async function(event) {
                        event.preventDefault();
                        if (!submitButton) return;
                        const restrictedMsg = (window.DEGCHI_MEMBER && window.DEGCHI_MEMBER.accountRestrictedMessage) || 'Your account temporary suspand. contact our help line';
                        if (window.DEGCHI_MEMBER && window.DEGCHI_MEMBER.loggedIn && window.DEGCHI_MEMBER.canOrderAndComment === false) { alert(restrictedMsg); return; }
                        if (verifiedMember && verifiedMember.account_restricted) { alert(restrictedMsg); return; }
                        submitButton.disabled = true;
                        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...';
                        try {
                            const response = await fetch(checkoutForm.action, { method: 'POST', body: buildFormData(checkoutForm), headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                            const result = await response.json();
                            if (!response.ok) {
                                if (result.account_restricted) alert(result.message || restrictedMsg);
                                else if (result.errors) showToast('error', Object.values(result.errors).flat().join('\n'));
                                else showToast('error', result.message || 'Unable to place order.');
                                return;
                            }
                            if (result.redirect_url) { if (result.clear_cart) { localStorage.removeItem('degchi_cart'); localStorage.removeItem('degchi_coupon'); } window.location.href = result.redirect_url; return; }
                            if (result.success) {
                                if (result.clear_cart) { localStorage.removeItem('degchi_cart'); localStorage.removeItem('degchi_coupon'); }
                                showToast('success', result.message || 'Order placed successfully.');
                                checkoutForm.reset();
                                document.getElementById('orderSummaryList').querySelectorAll('.checkout-order-item').forEach(el => el.remove());
                                const emptyEl = document.querySelector('.checkout-summary-empty');
                                if (emptyEl) emptyEl.style.display = '';
                                subtotalDisplay.textContent = '৳ 0.00';
                                totalDisplay.textContent = '৳ 0.00';
                                discountDisplay.textContent = '- ৳ 0.00';
                                document.getElementById('itemCount').textContent = '(0 items)';
                                membershipFeedback.textContent = 'Enter your membership card number to check eligibility for a 10% discount.';
                                membershipFeedback.classList.remove('text-success', 'text-danger');
                                appliedCoupon = null;
                                couponCodeInput.value = '';
                                couponFeedback.textContent = '';
                                couponDiscountRow.style.display = 'none';
                                shippingCharge = SHIPPING_CHARGE;
                            }
                        } catch (error) { showToast('error', error.message || 'Server error while placing order.'); }
                        finally {
                            submitButton.disabled = false;
                            submitButton.innerHTML = '<i class="bi bi-lock-fill me-2" style="opacity: 0.7;"></i> Place Order Now';
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
                if (memberCardInput && memberCardInput.value.trim()) checkMemberCardEligibility(memberCardInput.value.trim());
                @endif

                refreshCheckoutDiscounts();
                if (subtotalDisplay && typeof MutationObserver !== 'undefined') {
                    new MutationObserver(function() { if (!isRefreshingCheckoutDiscounts) refreshCheckoutDiscounts(); }).observe(subtotalDisplay, { childList: true, characterData: true, subtree: true });
                }

                const flashMessage = @json(session('success') ?? session('error'));
                const flashType = @json(session('success') ? 'success' : 'error');
                if (flashMessage) showToast(flashType, flashMessage);
            });
        </script>
    @endpush

@endsection
