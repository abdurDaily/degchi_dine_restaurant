@extends('frontend.layout')

@section('meta_title', 'Order Confirmation')
@section('meta_robots', 'noindex, nofollow')

@push('front_js')
    $statusClass = match($order->status) {
        'completed' => 'oc-badge-completed',
        'confirmed' => 'oc-badge-confirmed',
        'canceled' => 'oc-badge-canceled',
        default => 'oc-badge-pending',
    };
    $items = is_array($orderItems ?? null) ? $orderItems : [];
@endphp

<section class="dd-apply-wrapper order-confirm">

    <div class="dd-apply-hero-banner">
        <div class="container px-4 px-lg-5 text-center position-relative">
            <a href="{{ route('frontend.home') }}" class="dd-apply-back-btn">
                <iconify-icon icon="solar:alt-arrow-left-linear"></iconify-icon>
                <span>Back to Home</span>
            </a>

            <div class="oc-success-ring {{ $order->status === 'canceled' ? 'is-canceled' : '' }}">
                @if (!empty($needsPhoneVerification))
                    <iconify-icon icon="solar:lock-keyhole-linear"></iconify-icon>
                @elseif ($order->status === 'canceled')
                    ✕
                @elseif ($order->status === 'completed')
                    ✓
                @else
                    <iconify-icon icon="solar:delivery-linear"></iconify-icon>
                @endif
            </div>
            <br>
            <span class="dd-apply-badge">Order Tracking</span>
            <h1 class="dd-apply-headline" style="font-size: 2.5rem; font-weight: 800; letter-spacing: -0.02em; margin-top: 8px;">Order #{{ $order->id }}</h1>
            @if (!empty($needsPhoneVerification))
            <p class="dd-apply-subhead">Enter the phone number used at checkout to view your order status and receipt.</p>
            @else
            <p class="dd-apply-subhead">Hi {{ explode(' ', $order->customer_name)[0] }} — here is your live order status and full receipt.</p>
            @endif
        </div>
    </div>

    <div class="container px-4 px-lg-5 oc-main-box">
        @if (!empty($needsPhoneVerification))
        <div class="oc-verify-box">
            <h2>Verify to view order</h2>
            <p>For your security, confirm the phone number on order <strong>#{{ $order->id }}</strong> to see tracking details.</p>

            @if ($errors->any())
                <div class="alert alert-danger border-0 mb-4" style="border-radius: 14px; padding: 14px 20px; font-size: 0.9rem;">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('frontend.order.track.submit') }}" class="dd-apply-form-element">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <div class="dd-input-group mb-4">
                    <input type="tel" name="phone" id="oc_verify_phone" class="dd-input-field" placeholder=" " value="{{ old('phone') }}" required autofocus>
                    <label for="oc_verify_phone" class="dd-floating-label">Phone Number at Checkout</label>
                </div>
                <button type="submit" class="dd-submit-btn w-100 justify-content-center py-3">
                    <span>View Order Tracking</span>
                    <iconify-icon icon="solar:magnifer-linear" class="dd-btn-icon ms-2"></iconify-icon>
                </button>
            </form>

            <div class="text-center mt-4 pt-4" style="border-top: 1px solid rgba(0,0,0,0.06);">
                <p class="text-muted mb-3" style="font-size: 0.88rem;">Have a member account?</p>
                <a href="{{ route('frontend.member.login') }}" class="btn btn-sm btn-outline-dark me-2 px-3" style="border-radius: 50px;">Member Login</a>
                <a href="{{ route('frontend.order.track') }}" class="btn btn-sm btn-outline-dark px-3" style="border-radius: 50px;">Track Another Order</a>
            </div>
        </div>
        @else
        <div class="oc-grid">
            {{-- Left: order details --}}
            <div>
                <div class="oc-panel mb-4">
                    <div class="oc-panel-title">
                        <iconify-icon icon="solar:clipboard-list-linear" style="color: var(--dd-gold); font-size: 1.4rem;"></iconify-icon>
                        Order Details
                    </div>

                    <div class="oc-meta-grid">
                        <div class="oc-meta-item">
                            <small>Order Number</small>
                            <strong>#{{ $order->id }}</strong>
                        </div>
                        <div class="oc-meta-item">
                            <small>Placed On</small>
                            <strong>{{ $order->created_at->format('d M Y, h:i A') }}</strong>
                        </div>
                        <div class="oc-meta-item">
                            <small>Payment</small>
                            <strong>{{ strtoupper($order->payment_method ?? 'N/A') }}</strong>
                        </div>
                        <div class="oc-meta-item">
                            <small>Status</small>
                            <div>
                                <span class="oc-badge {{ $statusClass }}">{{ ucfirst($order->status) }}</span>
                            </div>
                        </div>
                        @if ($order->status === 'canceled' && !empty($order->status_remarks))
                        <div class="oc-meta-item" style="grid-column: 1 / -1;">
                            <small>Cancellation Remarks</small>
                            <strong style="font-weight: 500; color: #b54747; line-height: 1.5;">{{ $order->status_remarks }}</strong>
                        </div>
                        @endif
                        <div class="oc-meta-item" style="grid-column: 1 / -1;">
                            <small>Delivery Address</small>
                            <strong>{{ $order->customer_address }}</strong>
                        </div>
                        <div class="oc-meta-item">
                            <small>Customer Phone</small>
                            <strong>{{ $order->customer_phone }}</strong>
                        </div>
                        <div class="oc-meta-item">
                            <small>Order Total</small>
                            <strong class="oc-total">৳{{ number_format($order->final_amount, 2) }}</strong>
                        </div>
                    </div>

                    @if (!empty($items))
                        <div class="oc-panel-title" style="border-top: 1px solid rgba(0,0,0,0.06); padding-top: 24px; margin-top: 12px;">
                            <iconify-icon icon="solar:bag-3-linear" style="color: var(--dd-gold); font-size: 1.4rem;"></iconify-icon>
                            Items Ordered
                        </div>
                        @foreach ($items as $item)
                            @php
                                $qty = $item['quantity'] ?? 1;
                                $price = $item['price'] ?? 0;
                                $title = $item['title'] ?? $item['name'] ?? 'Item';
                            @endphp
                            <div class="oc-item-row">
                                <div>
                                    <div class="oc-item-name">{{ $title }}</div>
                                    <div class="oc-item-qty">Qty: {{ $qty }}</div>
                                </div>
                                <div class="oc-item-price">৳{{ number_format($price * $qty, 2) }}</div>
                            </div>
                        @endforeach

                        <div class="mt-4 pt-2">
                            <div class="oc-summary-row"><span>Subtotal</span><span>৳{{ number_format($order->total_amount, 2) }}</span></div>
                            @if ((float) $order->discount_amount > 0)
                                <div class="oc-summary-row text-success fw-semibold"><span>Discount</span><span>- ৳{{ number_format($order->discount_amount, 2) }}</span></div>
                            @endif
                            <div class="oc-summary-row total"><span>Total Paid</span><span class="text-success">৳{{ number_format($order->final_amount, 2) }}</span></div>
                        </div>
                    @endif
                </div>

                <div class="oc-actions">
                    @auth('member')
                    <a href="{{ route('frontend.member.dashboard', ['order' => $order->id]) }}" class="oc-action-btn oc-action-btn-solid">
                        <iconify-icon icon="solar:widget-5-linear"></iconify-icon> My Dashboard
                    </a>
                    @endauth
                    <a href="{{ route('frontend.completeMenu') }}" class="oc-action-btn oc-action-btn-solid">
                        <iconify-icon icon="solar:chef-hat-linear"></iconify-icon> Order Again
                    </a>
                    <a href="{{ route('frontend.home') }}" class="oc-action-btn oc-action-btn-outline">
                        <iconify-icon icon="solar:home-2-linear"></iconify-icon> Back to Home
                    </a>
                </div>
            </div>

            {{-- Right: status + help --}}
            <div>
                <div class="oc-panel mb-4">
                    <div class="oc-panel-title">
                        <iconify-icon icon="solar:delivery-linear" style="color: var(--dd-gold); font-size: 1.4rem;"></iconify-icon>
                        Order Progress
                    </div>
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
                                <div class="oc-remarks-box">
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

                <div class="oc-contact-card">
                    <h6><iconify-icon icon="solar:phone-calling-linear" class="me-2"></iconify-icon>Need Help?</h6>
                    <p>For order updates or any questions about order <strong>#{{ $order->id }}</strong>, call us and mention your order number.</p>
                    <a href="tel:{{ preg_replace('/\s+/', '', $contactPhone) }}" class="oc-call-btn w-100 justify-content-center">
                        <iconify-icon icon="solar:phone-linear"></iconify-icon>
                        Call {{ $contactPhone }}
                    </a>
                </div>

                <div class="oc-track-tip">
                    <h6><iconify-icon icon="solar:bookmark-linear" class="me-2"></iconify-icon>View this order later</h6>
                    <p>
                        Save your <strong>Order #{{ $order->id }}</strong> and phone number <strong>{{ $order->customer_phone }}</strong>.
                        Go to <a href="{{ route('frontend.order.track') }}" class="text-decoration-underline fw-semibold" style="color: var(--dd-text-main);">Track Order</a> anytime to see this page again.
                    </p>
                    <a href="{{ route('frontend.order.track', ['order' => $order->id]) }}" class="btn btn-sm btn-outline-dark p-3 w-100" style="border-radius: 50px;">
                        <iconify-icon icon="solar:magnifer-linear" class="me-1"></iconify-icon>
                        Track Order #{{ $order->id }}
                    </a>
                </div>

                <div class="oc-panel">
                    @guest('member')
                    <div class="oc-panel-title" style="margin-bottom: 14px; border-bottom: none; padding-bottom: 0;">
                        <iconify-icon icon="solar:card-2-linear" style="color: var(--dd-gold); font-size: 1.4rem;"></iconify-icon>
                        Get a Member Account
                    </div>
                    <p class="text-muted mb-4" style="font-size: 0.9rem; line-height: 1.6;">Apply for a membership card to track all orders in one dashboard and unlock exclusive discounts.</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('frontend.card.apply') }}" class="btn btn-sm btn-dark px-3 py-2" style="border-radius: 50px; font-weight: 600;">Apply Now</a>
                        <a href="{{ route('frontend.member.login') }}" class="btn btn-sm btn-outline-dark px-3 py-2" style="border-radius: 50px; font-weight: 600;">Member Login</a>
                    </div>
                    @else
                    <div class="oc-panel-title" style="margin-bottom: 14px; border-bottom: none; padding-bottom: 0;">
                        <iconify-icon icon="solar:widget-5-linear" style="color: var(--dd-gold); font-size: 1.4rem;"></iconify-icon>
                        Member Account
                    </div>
                    <p class="text-muted mb-4" style="font-size: 0.9rem; line-height: 1.6;">View all your orders and membership details from your dashboard.</p>
                    <a href="{{ route('frontend.member.dashboard') }}" class="btn btn-sm w-100 py-2 fw-semibold" style="background:#116b83; color:#fff; border-radius: 50px;">Open Dashboard</a>
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