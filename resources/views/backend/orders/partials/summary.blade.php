@php
    $items = $order->normalizedItems();
    $offerDiscountTotal = round(collect($items)->sum(fn ($i) => (float) ($i['offer_discount'] ?? 0)), 2);
    $couponDiscountTotal = round((float) $order->coupon_discount, 2);
    $memberDiscountTotal = round(max(0, (float) $order->discount_amount - $offerDiscountTotal - $couponDiscountTotal), 2);
    $deliveryCharge = round((float) ($order->delivery_charge ?? 0), 2);

    // Match checkout: Subtotal = sum of each item's offer-discounted price
    // (item offers are already baked in — do not subtract them again below).
    $displaySubtotal = round(max(0, (float) $order->total_amount - $offerDiscountTotal), 2);
@endphp
<div class="d-flex justify-content-between mb-2">
    <span>Subtotal</span>
    <span>৳{{ number_format($displaySubtotal, 2) }}</span>
</div>
@if($memberDiscountTotal > 0.004)
    <div class="d-flex justify-content-between mb-2 text-success">
        <span>Membership/Student Discount</span>
        <span>- ৳{{ number_format($memberDiscountTotal, 2) }}</span>
    </div>
@endif
@if($couponDiscountTotal > 0.004)
    <div class="d-flex justify-content-between mb-2 text-success">
        <span>
            🏷️ Coupon
            @if($order->coupon_code)
                <span class="badge bg-light text-dark border">{{ $order->coupon_code }}</span>
            @endif
        </span>
        <span>- ৳{{ number_format($couponDiscountTotal, 2) }}</span>
    </div>
@endif
@if($memberDiscountTotal <= 0.004 && $couponDiscountTotal <= 0.004 && (float) $order->discount_amount - $offerDiscountTotal > 0.004)
    <div class="d-flex justify-content-between mb-2 text-success">
        <span>Discount</span>
        <span>- ৳{{ number_format((float) $order->discount_amount - $offerDiscountTotal, 2) }}</span>
    </div>
@endif
<div class="d-flex justify-content-between mb-2">
    <span>Delivery Charge</span>
    <span>৳{{ number_format($deliveryCharge, 2) }}</span>
</div>
<hr class="my-2">
<div class="d-flex justify-content-between fw-bold fs-5 order-total-row">
    <span>Total</span>
    <span>৳{{ number_format($order->final_amount, 2) }}</span>
</div>
