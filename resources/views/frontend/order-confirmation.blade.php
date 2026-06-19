@extends('frontend.layout')

@section('meta_title', 'Order Confirmation')
@section('meta_robots', 'noindex, nofollow')

@push('front_css')
<style>
/* Layout Base Setup */
.order-confirm .oc-main-box { 
    margin-top: -120px; 
    position: relative; 
    z-index: 10; 
}

/* Success / Lock Ring Indicator */
.order-confirm .oc-success-ring {
    width: 96px; 
    height: 96px; 
    border-radius: 50%;
    background: linear-gradient(135deg, #24b44b, #1cb88a);
    color: #fff; 
    display: inline-flex; 
    align-items: center; 
    justify-content: center;
    font-size: 2.6rem; 
    margin-bottom: 20px;
    box-shadow: 0 16px 36px rgba(40,167,69,0.3);
    animation: ocPop 0.6s cubic-bezier(.34,1.56,.64,1) both;
}
@keyframes ocPop { 
    from { transform: scale(0.6); opacity: 0; } 
    to { transform: scale(1); opacity: 1; } 
}

/* Base Panel Definitions */
.order-confirm .oc-grid { 
    display: grid; 
    grid-template-columns: 1.25fr 0.75fr; 
    gap: 30px; 
}
.order-confirm .oc-panel {
    background: #fff; 
    border-radius: 24px; 
    border: 1px solid rgba(0, 0, 0, 0.04);
    padding: 32px; 
    box-shadow: 0 24px 64px rgba(0, 0, 0, 0.04);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.order-confirm .oc-panel:hover {
    box-shadow: 0 32px 72px rgba(0, 0, 0, 0.06);
}

/* Header UI Components */
.order-confirm .oc-panel-title {
    font-size: 1.25rem; 
    font-weight: 700; 
    color: var(--dd-text-main);
    margin-bottom: 24px; 
    padding-bottom: 16px; 
    border-bottom: 1px solid rgba(0, 0, 0, 0.06);
    display: flex; 
    align-items: center; 
    gap: 12px;
}

/* Meta grids details */
.order-confirm .oc-meta-grid { 
    display: grid; 
    grid-template-columns: repeat(2, 1fr); 
    gap: 20px; 
    margin-bottom: 24px; 
}
.order-confirm .oc-meta-item small { 
    display: block; 
    font-size: 0.75rem; 
    color: var(--dd-text-muted); 
    text-transform: uppercase; 
    letter-spacing: 0.08em; 
    margin-bottom: 6px; 
    font-weight: 600;
}
.order-confirm .oc-meta-item strong { 
    font-size: 1rem; 
    color: var(--dd-text-main); 
    font-weight: 600;
}
.order-confirm .oc-meta-item strong.oc-total { 
    font-size: 1.35rem; 
    color: #24b44b; 
    font-weight: 700;
}

/* Order Badges styling */
.order-confirm .oc-badge {
    display: inline-flex; 
    padding: 6px 14px; 
    border-radius: 50px;
    font-size: 0.75rem; 
    font-weight: 700; 
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.order-confirm .oc-badge-pending { background: #fef6dd; color: #856404; }
.order-confirm .oc-badge-confirmed { background: #e3f2fd; color: #0d47a1; }
.order-confirm .oc-badge-completed { background: #e8f5e9; color: #1b5e20; }
.order-confirm .oc-badge-canceled { background: #ffebee; color: #b71c1c; }

/* Item Rows definitions */
.order-confirm .oc-item-row {
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    gap: 16px;
    padding: 18px 0; 
    border-bottom: 1px dashed rgba(0, 0, 0, 0.08);
}
.order-confirm .oc-item-row:last-child { border-bottom: none; }
.order-confirm .oc-item-name { font-weight: 600; font-size: 1rem; color: var(--dd-text-main); }
.order-confirm .oc-item-qty { font-size: 0.85rem; color: var(--dd-text-muted); margin-top: 4px; }
.order-confirm .oc-item-price { font-weight: 700; color: var(--dd-text-main); white-space: nowrap; font-size: 1.05rem; }

/* Summaries blocks */
.order-confirm .oc-summary-row { display: flex; justify-content: space-between; padding: 10px 0; font-size: 0.95rem; color: var(--dd-text-muted); }
.order-confirm .oc-summary-row.total { 
    border-top: 2px dashed rgba(0, 0, 0, 0.08); 
    margin-top: 12px; 
    padding-top: 18px; 
    font-size: 1.15rem; 
    font-weight: 700; 
    color: var(--dd-text-main);
}

/* Dark Accent Cards styling */
.order-confirm .oc-contact-card {
    background: linear-gradient(145deg, #231815 0%, #150e0c 100%);
    border-radius: 24px; 
    padding: 32px; 
    color: #fff; 
    margin-bottom: 24px;
    box-shadow: 0 20px 48px rgba(35, 24, 21, 0.15);
}
.order-confirm .oc-contact-card h6 { color: var(--dd-gold); font-size: 0.88rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 14px; }
.order-confirm .oc-contact-card p { font-size: 0.92rem; color: rgba(255,255,255,0.75); line-height: 1.6; margin-bottom: 20px; }

/* Premium CTA styling */
.order-confirm .oc-call-btn {
    display: inline-flex; 
    align-items: center; 
    gap: 10px;
    background: var(--dd-gold); 
    color: #1f1412; 
    padding: 12px 26px;
    border-radius: 50px; 
    font-weight: 700; 
    font-size: 0.9rem; 
    text-decoration: none;
    transition: transform 0.25s, background 0.25s, box-shadow 0.25s;
    box-shadow: 0 10px 24px rgba(223, 166, 83, 0.2);
}
.order-confirm .oc-call-btn:hover { background: var(--dd-gold-hover); color: #1f1412; transform: translateY(-2px); box-shadow: 0 14px 30px rgba(223, 166, 83, 0.3); }

/* Tips contextual frame */
.order-confirm .oc-track-tip {
    background: rgba(223,166,83,0.05); 
    border: 1px dashed rgba(223,166,83,0.3);
    border-radius: 20px; 
    padding: 24px; 
    margin-bottom: 24px;
}
.order-confirm .oc-track-tip h6 { font-size: 0.88rem; font-weight: 700; color: var(--dd-gold); margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.08em; }
.order-confirm .oc-track-tip p { font-size: 0.9rem; color: var(--dd-text-muted); margin-bottom: 16px; line-height: 1.6; }
.order-confirm .oc-track-tip code { background: rgba(0,0,0,0.04); padding: 4px 10px; border-radius: 6px; font-size: 0.85rem; color: #1f1412; font-weight: 600; }

/* Global Buttons list setup */
.order-confirm .oc-actions { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 30px; }
.order-confirm .oc-action-btn {
    display: inline-flex; 
    align-items: center; 
    gap: 10px;
    padding: 12px 24px; 
    border-radius: 50px; 
    font-size: 0.9rem;
    font-weight: 600; 
    text-decoration: none; 
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
.order-confirm .oc-action-btn-solid {
    background: var(--dd-gold); 
    border: 1px solid var(--dd-gold); 
    color: #1f1412;
    box-shadow: 0 8px 20px rgba(223,166,83,0.15);
}
.order-confirm .oc-action-btn-solid:hover { background: var(--dd-gold-hover); border-color: var(--dd-gold-hover); color: #1f1412; transform: translateY(-2px); }
.order-confirm .oc-action-btn-outline {
    background: transparent; 
    border: 1px solid rgba(0, 0, 0, 0.12); 
    color: var(--dd-text-main);
}
.order-confirm .oc-action-btn-outline:hover { border-color: var(--dd-gold); color: var(--dd-gold); background: rgba(223,166,83,0.03); transform: translateY(-2px); }

/* Timeline UI Pipeline */
.order-confirm .oc-timeline { position: relative; padding-left: 32px; }
.order-confirm .oc-timeline::before {
    content: ''; 
    position: absolute; 
    left: 8px; 
    top: 12px; 
    bottom: 12px;
    width: 2px; 
    background: rgba(0,0,0,0.06);
}
.order-confirm .oc-timeline-step { position: relative; padding-bottom: 24px; }
.order-confirm .oc-timeline-step:last-child { padding-bottom: 0; }
.order-confirm .oc-timeline-dot {
    position: absolute; 
    left: -32px; 
    top: 3px; 
    width: 18px; 
    height: 18px;
    border-radius: 50%; 
    background: #fff; 
    border: 3px solid rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}
.order-confirm .oc-timeline-step.is-done .oc-timeline-dot {
  background: #24b44b;
  border-color: #24b44b;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.62rem;
  font-weight: 800;
  line-height: 1;
}
.order-confirm .oc-timeline-step.is-done .oc-timeline-dot::before {
  content: '✓';
}
.order-confirm .oc-timeline-step.is-active .oc-timeline-dot {
  background: var(--dd-gold);
  border-color: var(--dd-gold);
  box-shadow: 0 0 0 5px rgba(223,166,83,0.25);
}
.order-confirm .oc-timeline-step.is-active .oc-timeline-dot::before {
  content: none;
}
.order-confirm .oc-timeline-step.is-canceled .oc-timeline-dot {
  background: #e03a3a;
  border-color: #e03a3a;
  box-shadow: 0 0 0 5px rgba(224, 58, 58, 0.18);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.62rem;
  font-weight: 800;
}
.order-confirm .oc-timeline-step.is-canceled .oc-timeline-dot::before {
  content: '✕';
}
.order-confirm .oc-timeline-step.is-canceled strong {
  color: #b71c1c;
}
.order-confirm .oc-timeline-step.is-done:not(:last-child)::after {
  content: '';
  position: absolute;
  left: -24px;
  top: 21px;
  width: 2px;
  height: calc(100% - 6px);
  background: #24b44b;
  z-index: 0;
}
.order-confirm .oc-remarks-box {
  margin-top: 1rem;
  padding: 0.85rem 1rem;
  border-radius: 12px;
  background: #fff5f5;
  border: 1px solid rgba(224, 58, 58, 0.2);
}
.order-confirm .oc-remarks-box small {
  display: block;
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: #b54747;
  margin-bottom: 0.35rem;
}
.order-confirm .oc-remarks-box p {
  margin: 0;
  font-size: 0.84rem;
  line-height: 1.55;
  color: #5c3d3d;
}
.order-confirm .oc-success-ring.is-canceled {
  background: linear-gradient(135deg, #e03a3a, #c62828);
  box-shadow: 0 16px 36px rgba(224, 58, 58, 0.28);
}
.order-confirm .oc-timeline-step strong { display: block; font-size: 0.95rem; font-weight: 600; color: var(--dd-text-main); }
.order-confirm .oc-timeline-step span { font-size: 0.82rem; color: var(--dd-text-muted); }

/* Verification Box Styling */
.order-confirm .oc-verify-box {
    max-width: 540px; 
    margin: 0 auto;
    background: #fff; 
    border-radius: 24px; 
    border: 1px solid rgba(0,0,0,0.03);
    padding: 40px; 
    box-shadow: 0 32px 72px rgba(17, 107, 131, 0.08);
}
.order-confirm .oc-verify-box h2 { font-size: 1.6rem; font-weight: 700; margin-bottom: 10px; color: var(--dd-text-main); }
.order-confirm .oc-verify-box > p { color: var(--dd-text-muted); font-size: 0.95rem; margin-bottom: 28px; line-height: 1.6; }

/* Responsive Adaptations */
@media (max-width: 991px) {
    /* Center aligning header contents on small devices explicitly */
    .dd-apply-hero-banner .container {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    .dd-apply-badge {
        margin-left: auto;
        margin-right: auto;
        display: inline-block;
    }
    
    .order-confirm .oc-grid { grid-template-columns: 1fr; gap: 24px; }
    .order-confirm .oc-main-box { margin-top: -80px; }
    .order-confirm .oc-meta-grid { grid-template-columns: 1fr; gap: 14px; }
    .order-confirm .oc-panel { padding: 24px; }
    .order-confirm .oc-verify-box { padding: 30px 20px; }
    .order-confirm .oc-actions { justify-content: center; }
}
</style>
@endpush

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