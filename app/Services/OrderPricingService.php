<?php

namespace App\Services;

use App\Models\Member;
use App\Models\MenuVariation;
use App\Models\Offer;

/**
 * Single source of truth for order line-item pricing and discount stacking.
 *
 * Shared by the checkout flow (HomeController::storeOrder) and the backend
 * order editor (Backend\OrderController) so the two never drift apart.
 *
 * Discount order (matches checkout.blade.php):
 *   1. Item-level promo offers subtract first (baked into each line's offer_discount).
 *   2. Membership / Student / Golden discount stacks on top of that already
 *      offer-discounted subtotal.
 *   3. Coupon discount (a fixed ৳ amount set once when the coupon was applied)
 *      is subtracted last, re-capped so the combined discount never exceeds
 *      the food subtotal.
 *   4. Delivery charge is a fixed fee added AFTER all discounts. It is never
 *      discounted, and backend item edits reuse the same stored amount (they
 *      do not add a second delivery charge).
 */
class OrderPricingService
{
    /** Flat delivery fee charged at checkout (৳). */
    public const DEFAULT_DELIVERY_CHARGE = 60.0;
    /**
     * Best currently-active, member-eligible offer for a menu variation, or null.
     */
    public function resolveBestOffer(MenuVariation $variation, ?Member $member): ?Offer
    {
        $applicable = $variation->resolveApplicableOffers($member, false);

        return $applicable->isNotEmpty()
            ? Offer::bestEligibleForMember($applicable, $member)
            : null;
    }

    /**
     * Live pricing preview for a variation — used by the "Add Extra Item" picker
     * to show both the real price and the current discounted price/badge.
     *
     * @return array{variation_id:int, name:string, image:?string, price:float, discounted_price:float, offer_id:?int, offer_percent:int, offer_name:?string}
     */
    public function previewVariationPricing(MenuVariation $variation, ?Member $member): array
    {
        $unitPrice = (float) $variation->price;
        $offer = $this->resolveBestOffer($variation, $member);

        return [
            'variation_id' => $variation->id,
            'name' => $variation->name,
            'image' => $variation->image,
            'image_url' => $variation->image
                ? (str_starts_with($variation->image, 'http') ? $variation->image : asset($variation->image))
                : null,
            'price' => $unitPrice,
            'discounted_price' => $offer
                ? round($unitPrice * (1 - $offer->discount_percent / 100), 2)
                : $unitPrice,
            'offer_id' => $offer?->id,
            'offer_percent' => $offer ? (int) $offer->discount_percent : 0,
            'offer_name' => $offer?->name,
        ];
    }

    /**
     * Build a brand-new order line item for a variation, priced from today's
     * catalog price and today's best eligible offer for the order's member.
     * Keeps the same field shape used everywhere else (title/name/price/
     * original_price/quantity/image/variation_id/offer_id/offer_percent/offer_discount).
     */
    public function buildLineItem(MenuVariation $variation, int $quantity, ?Member $member): array
    {
        $quantity = max(1, $quantity);
        $preview = $this->previewVariationPricing($variation, $member);

        $item = [
            'variation_id' => $variation->id,
            'title' => $variation->menu?->name ?: $variation->name,
            'name' => $variation->name,
            'note' => $variation->name,
            'image' => $variation->image,
            'price' => $preview['price'],
            'original_price' => $preview['price'],
            'quantity' => $quantity,
            'offer_id' => null,
            'offer_percent' => 0,
            'offer_discount' => 0.0,
            'added_by_admin' => true,
        ];

        if ($preview['offer_percent'] > 0) {
            $item['offer_id'] = $preview['offer_id'];
            $item['offer_percent'] = $preview['offer_percent'];
            $item['offer_discount'] = round($preview['price'] * $quantity * ($preview['offer_percent'] / 100), 2);
        }

        return $item;
    }

    /**
     * Re-price an existing line item after a quantity change. Keeps whichever
     * offer the line was already stored with — a quantity tweak shouldn't
     * silently switch which offer applies, it should just rescale the amount.
     */
    public function repriceLineItemForQuantity(array $item, int $quantity): array
    {
        $item['quantity'] = max(1, $quantity);
        $unitPrice = (float) ($item['original_price'] ?? $item['price'] ?? 0);
        $percent = (float) ($item['offer_percent'] ?? 0);

        $item['offer_discount'] = $percent > 0
            ? round($unitPrice * $item['quantity'] * ($percent / 100), 2)
            : 0.0;

        return $item;
    }

    /**
     * Recalculate order-level totals from a set of line items.
     *
     * $fixedCouponDiscount is the coupon's already-applied ৳ amount — it is
     * never re-validated or recomputed here, only re-capped against the new
     * food subtotal so a shrinking order can never end up with a negative total.
     *
     * $deliveryCharge is a fixed fee added after discounts. Pass the order's
     * already-stored charge on backend edits so delivery is never double-added.
     *
     * @return array{total_amount:float, offer_discount:float, member_discount:float, coupon_discount:float, discount_amount:float, delivery_charge:float, food_total:float, final_amount:float}
     */
    public function recalculateTotals(
        array $items,
        ?Member $member,
        float $fixedCouponDiscount = 0.0,
        float $deliveryCharge = 0.0
    ): array {
        $totalAmount = 0.0;
        $offerDiscount = 0.0;

        foreach ($items as $item) {
            $unitPrice = (float) ($item['original_price'] ?? $item['price'] ?? 0);
            $qty = max(1, (int) ($item['quantity'] ?? $item['qty'] ?? 1));
            $totalAmount += $unitPrice * $qty;
            $offerDiscount += (float) ($item['offer_discount'] ?? 0);
        }

        $totalAmount = round($totalAmount, 2);
        $offerDiscount = round(min($offerDiscount, $totalAmount), 2);
        $subtotalAfterOffers = max(0.0, round($totalAmount - $offerDiscount, 2));

        $memberDiscount = 0.0;
        if ($member) {
            $memberDiscount = (float) ($member->resolveMemberDiscount($subtotalAfterOffers, true)['amount'] ?? 0);
        }
        $memberDiscount = round($memberDiscount, 2);

        $couponDiscount = max(0.0, round($fixedCouponDiscount, 2));

        // Cap discounts against food subtotal only — delivery is never discounted.
        $discountAmount = min($totalAmount, round($offerDiscount + $memberDiscount + $couponDiscount, 2));
        $foodTotal = max(0.0, round($totalAmount - $discountAmount, 2));
        $deliveryCharge = max(0.0, round($deliveryCharge, 2));
        $finalAmount = round($foodTotal + $deliveryCharge, 2);

        return [
            'total_amount' => $totalAmount,
            'offer_discount' => $offerDiscount,
            'member_discount' => $memberDiscount,
            'coupon_discount' => $couponDiscount,
            'discount_amount' => round($discountAmount, 2),
            'delivery_charge' => $deliveryCharge,
            'food_total' => $foodTotal,
            'final_amount' => $finalAmount,
        ];
    }
}
