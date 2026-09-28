<?php

/**
 * Offer helpers.
 *
 * All of these accept an OPTIONAL $subtotal (the whole cart/order subtotal).
 *
 * - Pass it when the current cart is known: an offer whose `min_total` is not
 *   met is then excluded, so no discount/badged price is produced for a cart
 *   that cannot use the offer.
 * - Omit it when there is no cart context (e.g. plain menu rendering). The
 *   helpers then behave exactly as before and the offer is only informational;
 *   `min_total` is enforced for real during pricing/checkout via
 *   OrderPricingService. No fake subtotal is ever invented.
 */

/**
 * Get the best offer for a menu variation
 */
if (!function_exists('getVariationOffer')) {
    function getVariationOffer($variationId, ?float $subtotal = null)
    {
        $variation = \App\Models\MenuVariation::find($variationId);
        if (!$variation) {
            return null;
        }
        return $variation->bestOffer($subtotal);
    }
}

/**
 * Check if a menu variation has active offers
 */
if (!function_exists('hasVariationOffer')) {
    function hasVariationOffer($variationId, ?float $subtotal = null)
    {
        $variation = \App\Models\MenuVariation::find($variationId);
        if (!$variation) {
            return false;
        }
        return $variation->resolveApplicableOffers(null, false, $subtotal)->isNotEmpty();
    }
}

/**
 * Get all active offers for a menu variation (specific + all_items)
 */
if (!function_exists('getVariationOffers')) {
    function getVariationOffers($variationId, ?float $subtotal = null)
    {
        $variation = \App\Models\MenuVariation::find($variationId);
        if (!$variation) {
            return collect();
        }
        return $variation->resolveApplicableOffers(null, false, $subtotal);
    }
}

/**
 * Get the best discount percentage for a variation
 */
if (!function_exists('getBestOfferDiscount')) {
    function getBestOfferDiscount($variationId, ?float $subtotal = null)
    {
        $offer = getVariationOffer($variationId, $subtotal);
        return $offer ? $offer->discount_percent : 0;
    }
}

/**
 * Generate HTML for offer badge
 */
if (!function_exists('renderOfferBadge')) {
    function renderOfferBadge($variationId, $badgeClass = 'offer-badge', ?float $subtotal = null)
    {
        $offer = getVariationOffer($variationId, $subtotal);

        if (!$offer) {
            return '';
        }

        $badge = $offer->popup_badge ?? "{$offer->discount_percent}% OFF";

        return sprintf(
            '<span class="%s" title="%s">%s</span>',
            htmlspecialchars($badgeClass),
            htmlspecialchars($offer->name),
            htmlspecialchars($badge)
        );
    }
}

/**
 * Calculate discounted price for a menu variation
 *
 * Pass $subtotal so an offer with a minimum order total is only applied when the
 * current cart actually reaches it. Without $subtotal the previous behaviour is
 * kept (used by contexts with no cart, where the price shown is informational).
 */
if (!function_exists('getDiscountedPrice')) {
    function getDiscountedPrice($variationId, $price, ?float $subtotal = null)
    {
        $offer = getVariationOffer($variationId, $subtotal);

        if (!$offer) {
            return $price;
        }

        $discountPercentage = $offer->discount_percent / 100;
        return round($price * (1 - $discountPercentage), 2);
    }
}
