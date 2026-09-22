@extends('frontend.layout')

@section('meta_title', 'Order Confirmation')
@section('meta_robots', 'noindex, nofollow')

@section('frontend_content')
@php
    $statusClass = match($order->status) {
        'completed' => 'oc-badge-completed',
        'confirmed' => 'oc-badge-confirmed',
        'canceled' => 'oc-badge-canceled',
        default => 'oc-badge-pending',
    };
    $items = is_array($orderItems ?? null) ? $orderItems : [];
@endphp

<style>
    /* Order confirmation — aligned with checkout theme */
    .order-confirm .oc-badge {
        display: inline-block;
        padding: 0.3rem 0.75rem;
        border-radius: 20px;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.03em;
        text-transform: capitalize;
    }
    .order-confirm .oc-badge-pending { background: var(--secondary-10); color: var(--brand-secondary); }
    .order-confirm .oc-badge-confirmed { background: rgba(16,185,129,0.15); color: #37d28a; }
    .order-confirm .oc-badge-completed { background: rgba(16,185,129,0.15); color: #37d28a; }
    .order-confirm .oc-badge-canceled { background: rgba(239,68,68,0.15); color: #ff6b6b; }

    .order-confirm .oc-timeline { position: relative; }
    .order-confirm .oc-timeline-step { position: relative; padding: 0 0 1.5rem 2rem; }
    .order-confirm .oc-timeline-step:last-child { padding-bottom: 0; }
    .order-confirm .oc-timeline-step::before {
        content: '';
        position: absolute;
        left: 6px;
        top: 18px;
        bottom: 0;
        width: 2px;
        background: var(--card-dark-border);
    }
    .order-confirm .oc-timeline-step:last-child::before { display: none; }
    .order-confirm .oc-timeline-dot {
        position: absolute;
        left: 0;
        top: 0;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 2px solid var(--card-dark-border-hover);
        background: var(--card-dark-bg);
        z-index: 1;
    }
    .order-confirm .oc-timeline-step.is-done .oc-timeline-dot {
        background: var(--brand-secondary);
        border-color: var(--brand-secondary);
        box-shadow: 0 0 0 4px var(--secondary-10);
    }
    .order-confirm .oc-timeline-step.is-done .oc-timeline-dot::after {
        content: '✓';
        position: absolute;
        inset: -3px;
        color: #fff;
        font-size: 0.6rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .order-confirm .oc-timeline-step.is-active .oc-timeline-dot {
        border-color: var(--brand-secondary);
        box-shadow: 0 0 0 4px var(--secondary-10), 0 0 18px var(--secondary-25);
        animation: oc-pulse 1.6s ease-in-out infinite;
    }
    @keyframes oc-pulse {
        0%, 100% { box-shadow: 0 0 0 4px var(--secondary-10), 0 0 12px var(--secondary-25); }
        50% { box-shadow: 0 0 0 6px var(--secondary-10), 0 0 22px var(--secondary-30); }
    }
    .order-confirm .oc-timeline-step.is-canceled .oc-timeline-dot {
        background: #ff6b6b;
        border-color: #ff6b6b;
        box-shadow: 0 0 0 4px rgba(239,68,68,0.15);
    }
    .order-confirm .oc-timeline-step.is-canceled .oc-timeline-dot::after {
        content: '✕';
        position: absolute;
        inset: -3px;
        color: #fff;
        font-size: 0.6rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .order-confirm .oc-timeline-step strong {
        display: block;
        font-size: 0.92rem;
        font-weight: 600;
        color: var(--text-on-dark);
    }
    .order-confirm .oc-timeline-step span {
        display: block;
        font-size: 0.8rem;
        color: var(--text-on-dark-muted);
        margin-top: 3px;
    }
    .order-confirm .oc-timeline-step.is-canceled strong { color: #ff6b6b; }

    .order-confirm .oc-remarks {
        background: var(--secondary-10);
        border: 1px solid var(--card-dark-border);
        border-radius: 12px;
        padding: 0.85rem 1rem;
        margin-top: 1rem;
    }
    .order-confirm .oc-remarks small {
        display: block;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-on-dark-subtle);
        margin-bottom: 4px;
    }
    .order-confirm .oc-remarks p { margin: 0; font-size: 0.88rem; color: var(--text-on-dark); }

    .order-confirm .oc-action-btn {
        flex: 1;
        min-width: 140px;
    }
    .order-confirm .oc-action-btn-outline {
        background: transparent;
        border: 1.5px solid var(--brand-secondary);
        color: var(--brand-secondary);
        box-shadow: none;
    }
    .order-confirm .oc-action-btn-outline:hover {
        background: var(--secondary-10);
        transform: translateY(-2px);
        color: var(--brand-secondary);
    }
    .order-confirm .oc-contact-btn { display: flex; align-items: center; justify-content: center; gap: 0.4rem; }
</style>

<x-frontend.page-header
    title="Order #{{ $order->id }}"
    :subtitle="!empty($needsPhoneVerification) ? 'Enter the phone number used at checkout to view your order status and receipt.' : ($order->status === 'canceled' ? 'This order has been canceled. See the details below or contact us for help.' : 'Hi ' . explode(' ', $order->customer_name)[0] . ' — here is your live order status and full receipt.')"
    eyebrow="ORDER CONFIRMATION"
>
    <x-slot:eyebrowIcon>
        @if (!empty($needsPhoneVerification))
            <iconify-icon icon="solar:lock-keyhole-linear"></iconify-icon>
        @elseif ($order->status === 'canceled')
            <iconify-icon icon="solar:close-circle-bold"></iconify-icon>
        @elseif ($order->status === 'completed')
            <iconify-icon icon="solar:check-circle-bold"></iconify-icon>
        @else
            <iconify-icon icon="solar:delivery-linear"></iconify-icon>
        @endif
    </x-slot:eyebrowIcon>
    <x-slot:meta>
        <span><i class="bi bi-bag-check"></i> Cart</span>
        <span><i class="bi bi-chevron-right"></i></span>
        <span><i class="bi bi-credit-card"></i> Checkout</span>
        <span><i class="bi bi-chevron-right"></i></span>
        <span style="opacity: 0.5;"><i class="bi bi-check-circle"></i> Confirmed</span>
    </x-slot:meta>
</x-frontend.page-header>

<section class="cart-page-section order-confirm">
    <div class="container px-4 px-lg-5">

        @if (!empty($needsPhoneVerification))
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9">
                <div class="checkout-form-panel">
                    <h5 class="checkout-form-heading">
                        <span class="checkout-form-heading-icon"><i class="bi bi-shield-lock"></i></span>
                        Verify to view order
                    </h5>
                    <p class="mb-4" style="color: var(--text-on-dark-muted); font-size: 0.9rem; line-height: 1.6;">
                        For your security, confirm the phone number on order <strong style="color: var(--text-on-dark);">#{{ $order->id }}</strong> to see tracking details.
                    </p>

                    @if ($errors->any())
                        <div class="alert alert-danger border-0 mb-4" style="border-radius: 12px; padding: 14px 18px; font-size: 0.88rem;">{{ $errors->first() }}</div>
                    @endif

                    <form method="POST" action="{{ route('frontend.order.track.submit') }}">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">

                        <div class="checkout-input-wrap">
                            <i class="bi bi-telephone checkout-input-icon"></i>
                            <input type="tel" name="phone" id="oc_verify_phone" class="form-control checkout-input"
                                placeholder="Phone Number at Checkout" value="{{ old('phone') }}" required autofocus />
                        </div>

                        <button type="submit" class="btn checkout-btn-primary d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-search"></i> View Order Tracking
                        </button>
                    </form>

                    <div class="text-center mt-4 pt-3" style="border-top: 1px solid var(--card-dark-border);">
                        <p class="mb-3" style="color: var(--text-on-dark-muted); font-size: 0.85rem;">Have a member account?</p>
                        <div class="d-flex justify-content-center flex-wrap gap-3">
                            <a href="{{ route('frontend.member.login') }}" class="checkout-continue-link">
                                <i class="bi bi-person me-1"></i> Member Login
                            </a>
                            <a href="{{ route('frontend.order.track') }}" class="checkout-continue-link">
                                <i class="bi bi-search me-1"></i> Track Another Order
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else

        <div class="row g-4 align-items-start">

            {{-- Left: order details --}}
            <div class="col-lg-7">
                <div class="checkout-form-panel">
                    <h5 class="checkout-form-heading">
                        <span class="checkout-form-heading-icon"><iconify-icon icon="solar:clipboard-list-linear"></iconify-icon></span>
                        Order Details
                    </h5>

                    <div class="checkout-summary-row">
                        <span>Order Number</span>
                        <span class="checkout-summary-val">#{{ $order->id }}</span>
                    </div>
                    <div class="checkout-summary-row">
                        <span>Placed On</span>
                        <span class="checkout-summary-val">{{ $order->created_at->format('d M Y, h:i A') }}</span>
                    </div>
                    <div class="checkout-summary-row">
                        <span>Payment</span>
                        <span class="checkout-summary-val">{{ strtoupper($order->payment_method ?? 'N/A') }}</span>
                    </div>
                    <div class="checkout-summary-row">
                        <span>Status</span>
                        <span><span class="oc-badge {{ $statusClass }}">{{ ucfirst($order->status) }}</span></span>
                    </div>
                    @if ($order->status === 'canceled' && !empty($order->status_remarks))
                    <div class="checkout-summary-row">
                        <span>Cancellation Remarks</span>
                        <span class="checkout-summary-val" style="color: #ff6b6b; text-align: right;">{{ $order->status_remarks }}</span>
                    </div>
                    @endif
                    <div class="checkout-summary-divider"></div>
                    <div class="checkout-summary-row">
                        <span>Delivery Address</span>
                        <span class="checkout-summary-val" style="text-align: right; max-width: 60%;">{{ $order->customer_address }}</span>
                    </div>
                    <div class="checkout-summary-row">
                        <span>Customer Phone</span>
                        <span class="checkout-summary-val">{{ $order->customer_phone }}</span>
                    </div>
                    <div class="checkout-summary-divider"></div>
                    <div class="checkout-summary-total-row">
                        <span>Order Total</span>
                        <strong class="checkout-summary-total-val">৳{{ number_format($order->final_amount, 2) }}</strong>
                    </div>
                </div>

                @if (!empty($items))
                <div class="checkout-form-panel">
                    <h5 class="checkout-form-heading">
                        <span class="checkout-form-heading-icon"><iconify-icon icon="solar:bag-3-linear"></iconify-icon></span>
                        Items Ordered
                    </h5>

                    @foreach ($items as $item)
                        @php
                            $qty = $item['quantity'] ?? 1;
                            $price = $item['price'] ?? 0;
                            $title = $item['title'] ?? $item['name'] ?? 'Item';
                            $image = $item['image'] ?? null;
                            $note = $item['note'] ?? '';
                        @endphp
                        <div class="checkout-order-item">
                            @if($image)
                                <div class="checkout-order-img-wrap">
                                    <img src="{{ $image }}" alt="{{ $title }}" class="checkout-order-img" />
                                </div>
                            @endif
                            <div class="checkout-order-body">
                                <div class="checkout-order-top">
                                    <p class="checkout-order-name">{{ $title }}</p>
                                    <span class="checkout-order-tag">{{ $note }}{{ $note && $qty ? ' · ' : '' }}Qty: {{ $qty }}</span>
                                </div>
                                <div class="checkout-order-bottom">
                                    <span class="checkout-order-price">৳{{ number_format($price, 2) }} × {{ $qty }}</span>
                                    <span class="checkout-order-subtotal">৳{{ number_format($price * $qty, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="mt-3 pt-2">
                        <div class="checkout-summary-row">
                            <span>Subtotal</span>
                            <span class="checkout-summary-val">৳{{ number_format($order->total_amount, 2) }}</span>
                        </div>
                        @if ((float) $order->discount_amount > 0)
                            <div class="checkout-summary-row  fw-semibold">
                                <span>Discount</span>
                                <span>- ৳{{ number_format($order->discount_amount, 2) }}</span>
                            </div>
                        @endif
                        <div class="checkout-summary-divider"></div>
                        <div class="checkout-summary-total-row">
                            <span>Total Paid</span>
                            <strong class="checkout-summary-total-val ">৳{{ number_format($order->final_amount, 2) }}</strong>
                        </div>
                    </div>
                </div>
                @endif

                <div class="d-flex flex-wrap gap-2 mt-1">
                    @auth('member')
                    <a href="{{ route('frontend.member.dashboard', ['order' => $order->id]) }}" class="btn checkout-btn-primary oc-action-btn d-flex align-items-center justify-content-center gap-2">
                        <iconify-icon icon="solar:widget-5-linear"></iconify-icon> My Dashboard
                    </a>
                    @endauth
                    <a href="{{ route('frontend.order.invoice.download', $order->id) }}" class="btn checkout-btn-primary oc-action-btn d-flex align-items-center justify-content-center gap-2">
                        <iconify-icon icon="solar:download-linear"></iconify-icon> Download Invoice
                    </a>
                    <a href="{{ route('frontend.completeMenu') }}" class="btn checkout-btn-primary oc-action-btn d-flex align-items-center justify-content-center gap-2">
                        <iconify-icon icon="solar:chef-hat-linear"></iconify-icon> Order Again
                    </a>
                    <a href="{{ route('frontend.home') }}" class="btn checkout-btn-primary oc-action-btn oc-action-btn-outline d-flex align-items-center justify-content-center gap-2">
                        <iconify-icon icon="solar:home-2-linear"></iconify-icon> Back to Home
                    </a>
                </div>
            </div>

            {{-- Right: status + help --}}
            <div class="col-lg-5">
                <div class="checkout-summary-card checkout-summary-sticky">
                    <div class="checkout-summary-header">
                        <iconify-icon icon="solar:delivery-linear" class="me-2" style="color: var(--brand-secondary); font-size: 1.3rem;"></iconify-icon>
                        Order Progress
                    </div>
                    <div class="checkout-summary-body">
                        <div class="oc-timeline">
                            @php
                                $steps = [
                                    ['key' => 'pending', 'label' => 'Order Placed', 'desc' => 'We received your order'],
                                    ['key' => 'confirmed', 'label' => 'Confirmed', 'desc' => 'Kitchen is preparing'],
                                    ['key' => 'completed', 'label' => 'Delivered', 'desc' => 'Enjoy your meal!'],
                                ];
                                $statusIndex = match ($order->status) {
                                    'pending' => 0,
                                    'confirmed' => 1,
                                    'completed' => 2,
                                    default => -1,
                                };
                                $isCanceled = $order->status === 'canceled';
                            @endphp

                            @if ($isCanceled)
                                <div class="oc-timeline-step is-done">
                                    <div class="oc-timeline-dot"></div>
                                    <strong>Order Placed</strong>
                                    <span>We received your order</span>
                                </div>
                                <div class="oc-timeline-step is-canceled">
                                    <div class="oc-timeline-dot"></div>
                                    <strong>Order Canceled</strong>
                                    <span>This order will not be delivered.</span>
                                </div>
                                @if (!empty($order->status_remarks))
                                    <div class="oc-remarks">
                                        <small>Remarks</small>
                                        <p>{{ $order->status_remarks }}</p>
                                    </div>
                                @endif
                            @else
                                @foreach ($steps as $i => $step)
                                    @php
                                        $isDone = $i <= $statusIndex;
                                        $isActive = $i === $statusIndex && $order->status !== 'completed';
                                    @endphp
                                    <div class="oc-timeline-step {{ $isDone ? 'is-done' : '' }} {{ $isActive ? 'is-active' : '' }}">
                                        <div class="oc-timeline-dot"></div>
                                        <strong>{{ $step['label'] }}</strong>
                                        <span>{{ $step['desc'] }}</span>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <div class="checkout-summary-card mt-4">
                    <div class="checkout-summary-header">
                        <i class="bi bi-phone me-2" style="color: var(--brand-secondary); font-size: 1.15rem;"></i>
                        Need Help?
                    </div>
                    <div class="checkout-summary-body">
                        <p style="color: var(--text-on-dark-muted); font-size: 0.88rem; line-height: 1.6; margin-bottom: 1rem;">
                            For order updates or any questions about order <strong style="color: var(--text-on-dark);">#{{ $order->id }}</strong>, call us and mention your order number.
                        </p>
                        <a href="tel:{{ preg_replace('/\s+/', '', $contactPhone) }}" class="btn checkout-btn-primary oc-contact-btn">
                            <iconify-icon icon="solar:phone-linear"></iconify-icon>
                            Call {{ $contactPhone }}
                        </a>
                    </div>
                </div>

                <div class="checkout-summary-card mt-4">
                    <div class="checkout-summary-header">
                        <iconify-icon icon="solar:bookmark-linear" class="me-2" style="color: var(--brand-secondary); font-size: 1.3rem;"></iconify-icon>
                        View this order later
                    </div>
                    <div class="checkout-summary-body">
                        <p style="color: var(--text-on-dark-muted); font-size: 0.88rem; line-height: 1.6; margin-bottom: 1rem;">
                            Save your <strong style="color: var(--text-on-dark);">Order #{{ $order->id }}</strong> and phone number <strong style="color: var(--text-on-dark);">{{ $order->customer_phone }}</strong>.
                            Go to <a href="{{ route('frontend.order.track') }}" class="text-decoration-underline fw-semibold" style="color: var(--brand-secondary);">Track Order</a> anytime to see this page again.
                        </p>
                        <a href="{{ route('frontend.order.track', ['order' => $order->id]) }}" class="btn checkout-btn-primary oc-action-btn-outline d-flex align-items-center justify-content-center gap-2">
                            <iconify-icon icon="solar:magnifer-linear"></iconify-icon>
                            Track Order #{{ $order->id }}
                        </a>
                    </div>
                </div>

                <div class="checkout-summary-card mt-4">
                    @guest('member')
                    <div class="checkout-summary-header">
                        <iconify-icon icon="solar:card-2-linear" class="me-2" style="color: var(--brand-secondary); font-size: 1.3rem;"></iconify-icon>
                        Get a Member Account
                    </div>
                    <div class="checkout-summary-body">
                        <p style="color: var(--text-on-dark-muted); font-size: 0.88rem; line-height: 1.6; margin-bottom: 1rem;">
                            Apply for a membership card to track all orders in one dashboard and unlock exclusive discounts.
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('frontend.card.apply') }}" class="btn checkout-btn-primary oc-action-btn d-flex align-items-center justify-content-center gap-2">
                                Apply Now
                            </a>
                            <a href="{{ route('frontend.member.login') }}" class="btn checkout-btn-primary oc-action-btn oc-action-btn-outline d-flex align-items-center justify-content-center gap-2">
                                Member Login
                            </a>
                        </div>
                    </div>
                    @else
                    <div class="checkout-summary-header">
                        <iconify-icon icon="solar:widget-5-linear" class="me-2" style="color: var(--brand-secondary); font-size: 1.3rem;"></iconify-icon>
                        Member Account
                    </div>
                    <div class="checkout-summary-body">
                        <p style="color: var(--text-on-dark-muted); font-size: 0.88rem; line-height: 1.6; margin-bottom: 1rem;">
                            View all your orders and membership details from your dashboard.
                        </p>
                        <a href="{{ route('frontend.member.dashboard') }}" class="btn checkout-btn-primary oc-action-btn d-flex align-items-center justify-content-center gap-2">
                            <iconify-icon icon="solar:widget-5-linear"></iconify-icon>
                            Open Dashboard
                        </a>
                    </div>
                    @endguest
                </div>
            </div>
        </div>
        @endif
    </div>
</section>
@endsection

@push('front_js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (new URLSearchParams(window.location.search).get('clear_cart') === '1') {
        localStorage.removeItem('degchi_cart');
    }
});
</script>
@endpush