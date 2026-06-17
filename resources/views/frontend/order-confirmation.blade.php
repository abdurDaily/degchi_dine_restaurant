@extends('frontend.layout')

@section('meta_title', 'Order Confirmation')
@section('meta_robots', 'noindex, nofollow')

@push('front_css')
<style>
.order-confirm .oc-main-box { margin-top: -100px; position: relative; z-index: 2; }
.order-confirm .oc-success-ring {
    width: 88px; height: 88px; border-radius: 50%;
    background: linear-gradient(135deg, #28a745, #20c997);
    color: #fff; display: inline-flex; align-items: center; justify-content: center;
    font-size: 2.4rem; margin-bottom: 16px;
    box-shadow: 0 12px 32px rgba(40,167,69,0.35);
    animation: ocPop 0.5s cubic-bezier(.34,1.56,.64,1) both;
}
@keyframes ocPop { from { transform: scale(0.5); opacity: 0; } to { transform: scale(1); opacity: 1; } }
.order-confirm .oc-grid { display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 24px; }
.order-confirm .oc-panel {
    background: #fff; border-radius: 20px; border: 1px solid var(--dd-border);
    padding: 28px; box-shadow: 0 20px 50px rgba(31,20,18,0.06);
}
.order-confirm .oc-panel-title {
    font-size: 1.15rem; font-weight: 700; color: var(--dd-text-main);
    margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid var(--dd-border);
    display: flex; align-items: center; gap: 10px;
}
.order-confirm .oc-meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
.order-confirm .oc-meta-item small { display: block; font-size: 0.75rem; color: var(--dd-text-muted); text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 4px; }
.order-confirm .oc-meta-item strong { font-size: 0.95rem; color: var(--dd-text-main); }
.order-confirm .oc-meta-item strong.oc-total { font-size: 1.25rem; color: #28a745; }
.order-confirm .oc-badge {
    display: inline-block; padding: 5px 12px; border-radius: 50px;
    font-size: 0.72rem; font-weight: 700; text-transform: uppercase;
}
.order-confirm .oc-badge-pending { background: #fff3cd; color: #856404; }
.order-confirm .oc-badge-confirmed { background: #cce5ff; color: #004085; }
.order-confirm .oc-badge-completed { background: #d4edda; color: #155724; }
.order-confirm .oc-badge-canceled { background: #f8d7da; color: #721c24; }
.order-confirm .oc-item-row {
    display: flex; justify-content: space-between; align-items: flex-start; gap: 12px;
    padding: 14px 0; border-bottom: 1px dashed var(--dd-border);
}
.order-confirm .oc-item-row:last-child { border-bottom: none; }
.order-confirm .oc-item-name { font-weight: 600; font-size: 0.92rem; color: var(--dd-text-main); }
.order-confirm .oc-item-qty { font-size: 0.8rem; color: var(--dd-text-muted); margin-top: 2px; }
.order-confirm .oc-item-price { font-weight: 700; color: var(--dd-text-main); white-space: nowrap; }
.order-confirm .oc-summary-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 0.9rem; }
.order-confirm .oc-summary-row.total { border-top: 2px solid var(--dd-border); margin-top: 8px; padding-top: 14px; font-size: 1.05rem; font-weight: 700; }
.order-confirm .oc-contact-card {
    background: linear-gradient(160deg, #1f1412 0%, #2d1f1a 100%);
    border-radius: 16px; padding: 24px; color: #fff; margin-bottom: 20px;
}
.order-confirm .oc-contact-card h6 { color: var(--dd-gold); font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 10px; }
.order-confirm .oc-contact-card p { font-size: 0.88rem; color: rgba(255,255,255,0.7); line-height: 1.55; margin-bottom: 16px; }
.order-confirm .oc-call-btn {
    display: inline-flex; align-items: center; gap: 8px;
    background: var(--dd-gold); color: #1f1412; padding: 10px 20px;
    border-radius: 50px; font-weight: 700; font-size: 0.88rem; text-decoration: none;
    transition: background 0.2s;
}
.order-confirm .oc-call-btn:hover { background: var(--dd-gold-hover); color: #1f1412; }
.order-confirm .oc-track-tip {
    background: rgba(223,166,83,0.08); border: 1px dashed rgba(223,166,83,0.35);
    border-radius: 14px; padding: 18px; margin-bottom: 20px;
}
.order-confirm .oc-track-tip h6 { font-size: 0.82rem; font-weight: 700; color: var(--dd-gold); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.06em; }
.order-confirm .oc-track-tip p { font-size: 0.85rem; color: var(--dd-text-muted); margin-bottom: 12px; line-height: 1.55; }
.order-confirm .oc-track-tip code { background: rgba(0,0,0,0.06); padding: 2px 8px; border-radius: 4px; font-size: 0.82rem; }
.order-confirm .oc-actions { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 24px; }
.order-confirm .oc-action-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 18px; border-radius: 50px; font-size: 0.85rem;
    font-weight: 600; text-decoration: none; transition: all 0.25s ease;
}
.order-confirm .oc-action-btn-solid {
    background: var(--dd-gold); border: 1px solid var(--dd-gold); color: #1f1412;
}
.order-confirm .oc-action-btn-solid:hover { background: var(--dd-gold-hover); color: #1f1412; }
.order-confirm .oc-action-btn-outline {
    background: transparent; border: 1px solid var(--dd-border); color: var(--dd-text-main);
}
.order-confirm .oc-action-btn-outline:hover { border-color: var(--dd-gold); color: var(--dd-gold); }
.order-confirm .oc-timeline { position: relative; padding-left: 28px; }
.order-confirm .oc-timeline::before {
    content: ''; position: absolute; left: 8px; top: 8px; bottom: 8px;
    width: 2px; background: var(--dd-border);
}
.order-confirm .oc-timeline-step { position: relative; padding-bottom: 18px; }
.order-confirm .oc-timeline-step:last-child { padding-bottom: 0; }
.order-confirm .oc-timeline-dot {
    position: absolute; left: -28px; top: 2px; width: 18px; height: 18px;
    border-radius: 50%; background: #fff; border: 2px solid var(--dd-border);
}
.order-confirm .oc-timeline-step.is-done .oc-timeline-dot { background: #28a745; border-color: #28a745; }
.order-confirm .oc-timeline-step.is-active .oc-timeline-dot { background: var(--dd-gold); border-color: var(--dd-gold); box-shadow: 0 0 0 4px rgba(223,166,83,0.2); }
.order-confirm .oc-timeline-step strong { display: block; font-size: 0.88rem; }
.order-confirm .oc-timeline-step span { font-size: 0.78rem; color: var(--dd-text-muted); }
.order-confirm .oc-verify-box {
    max-width: 520px; margin: 0 auto;
    background: #fff; border-radius: 20px; border: 1px solid var(--dd-border);
    padding: 32px; box-shadow: 0 20px 50px rgba(17, 107, 131, 0.1);
}
.order-confirm .oc-verify-box h2 { font-size: 1.45rem; font-weight: 700; margin-bottom: 8px; }
.order-confirm .oc-verify-box > p { color: var(--dd-text-muted); font-size: 0.9rem; margin-bottom: 24px; line-height: 1.55; }
@media (max-width: 991px) {
    .order-confirm .oc-grid { grid-template-columns: 1fr; }
    .order-confirm .oc-main-box { margin-top: -60px; }
    .order-confirm .oc-meta-grid { grid-template-columns: 1fr; }
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

            <div class="oc-success-ring">@if (!empty($needsPhoneVerification))<iconify-icon icon="solar:lock-keyhole-linear"></iconify-icon>@else✓@endif</div>
            <span class="dd-apply-badge">Order Tracking</span>
            <h1 class="dd-apply-headline" style="font-size: 2.4rem;">Order #{{ $order->id }}</h1>
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
                <div class="alert alert-danger border-0 mb-4" style="border-radius: 12px;">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('frontend.order.track.submit') }}" class="dd-apply-form-element">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <div class="dd-input-group">
                    <input type="tel" name="phone" id="oc_verify_phone" class="dd-input-field" placeholder=" " value="{{ old('phone') }}" required autofocus>
                    <label for="oc_verify_phone" class="dd-floating-label">Phone Number at Checkout</label>
                </div>
                <button type="submit" class="dd-submit-btn">
                    <span>View Order Tracking </span>
                    <iconify-icon icon="solar:magnifer-linear" class="dd-btn-icon"></iconify-icon>
                </button>
            </form>

            <div class="text-center mt-4 pt-3" style="border-top: 1px solid var(--dd-border);">
                <p class="text-muted mb-2" style="font-size: 0.85rem;">Have a member account?</p>
                <a href="{{ route('frontend.member.login') }}" class="btn btn-sm btn-outline-dark me-1">Member Login</a>
                <a href="{{ route('frontend.order.track') }}" class="btn btn-sm btn-outline-dark">Track Another Order</a>
            </div>
        </div>
        @else
        <div class="oc-grid">
            {{-- Left: order details --}}
            <div>
                <div class="oc-panel mb-4">
                    <div class="oc-panel-title">
                        <iconify-icon icon="solar:clipboard-list-linear" style="color: var(--dd-gold);"></iconify-icon>
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
                            <span class="oc-badge {{ $statusClass }}">{{ ucfirst($order->status) }}</span>
                        </div>
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
                        <div class="oc-panel-title" style="border-top: 1px solid var(--dd-border); padding-top: 18px; margin-top: 4px;">
                            <iconify-icon icon="solar:bag-3-linear" style="color: var(--dd-gold);"></iconify-icon>
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

                        <div class="mt-3 pt-2">
                            <div class="oc-summary-row"><span>Subtotal</span><span>৳{{ number_format($order->total_amount, 2) }}</span></div>
                            @if ((float) $order->discount_amount > 0)
                                <div class="oc-summary-row text-success"><span>Discount</span><span>- ৳{{ number_format($order->discount_amount, 2) }}</span></div>
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
                        <iconify-icon icon="solar:delivery-linear" style="color: var(--dd-gold);"></iconify-icon>
                        Order Progress
                    </div>
                    <div class="oc-timeline">
                        @php
                            $steps = [
                                ['key' => 'placed', 'label' => 'Order Placed', 'desc' => 'We received your order'],
                                ['key' => 'confirmed', 'label' => 'Confirmed', 'desc' => 'Kitchen is preparing'],
                                ['key' => 'completed', 'label' => 'Delivered', 'desc' => 'Enjoy your meal!'],
                            ];
                            $current = $order->status === 'canceled' ? 'placed' : $order->status;
                        @endphp
                        @foreach ($steps as $i => $step)
                            @php
                                $isDone = ($current === 'completed') || ($current === 'confirmed' && $i < 2) || ($current === 'pending' && $i === 0);
                                $isActive = ($current === 'pending' && $i === 0) || ($current === 'confirmed' && $i === 1) || ($current === 'completed' && $i === 2);
                                if ($order->status === 'canceled') { $isDone = $i === 0; $isActive = false; }
                            @endphp
                            <div class="oc-timeline-step {{ $isDone ? 'is-done' : '' }} {{ $isActive ? 'is-active' : '' }}">
                                <div class="oc-timeline-dot"></div>
                                <strong>{{ $step['label'] }}</strong>
                                <span>{{ $step['desc'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="oc-contact-card">
                    <h6><iconify-icon icon="solar:phone-calling-linear" class="me-1"></iconify-icon> Need Help?</h6>
                    <p>For order updates or any questions about order <strong>#{{ $order->id }}</strong>, call us and mention your order number.</p>
                    <a href="tel:{{ preg_replace('/\s+/', '', $contactPhone) }}" class="oc-call-btn">
                        <iconify-icon icon="solar:phone-linear"></iconify-icon>
                        Call {{ $contactPhone }}
                    </a>
                </div>

                <div class="oc-track-tip">
                    <h6><iconify-icon icon="solar:bookmark-linear" class="me-1"></iconify-icon> View this order later</h6>
                    <p>
                        Save your <strong>Order #{{ $order->id }}</strong> and phone number <strong>{{ $order->customer_phone }}</strong>.
                        Go to <a href="{{ route('frontend.order.track') }}">Track Order</a> anytime and enter both to see this page again.
                    </p>
                    <a href="{{ route('frontend.order.track', ['order' => $order->id]) }}" class="btn btn-sm btn-outline-dark">
                        <iconify-icon icon="solar:magnifer-linear" class="me-1"></iconify-icon>
                        Track Order #{{ $order->id }}
                    </a>
                </div>

                <div class="oc-panel">
                    @guest('member')
                    <div class="oc-panel-title" style="margin-bottom: 12px;">
                        <iconify-icon icon="solar:card-2-linear" style="color: var(--dd-gold);"></iconify-icon>
                        Get a Member Account
                    </div>
                    <p class="text-muted mb-3" style="font-size: 0.88rem; line-height: 1.55;">Apply for a membership card to track all orders in one dashboard and unlock exclusive discounts.</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('frontend.card.apply') }}" class="btn btn-sm btn-dark">Apply Now</a>
                        <a href="{{ route('frontend.member.login') }}" class="btn btn-sm btn-outline-dark">Member Login</a>
                    </div>
                    @else
                    <div class="oc-panel-title" style="margin-bottom: 12px;">
                        <iconify-icon icon="solar:widget-5-linear" style="color: var(--dd-gold);"></iconify-icon>
                        Member Account
                    </div>
                    <p class="text-muted mb-3" style="font-size: 0.88rem; line-height: 1.55;">View all your orders and membership details from your dashboard.</p>
                    <a href="{{ route('frontend.member.dashboard') }}" class="btn btn-sm" style="background:#116b83;color:#fff;">Open Dashboard</a>
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
