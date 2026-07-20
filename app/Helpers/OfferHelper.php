<?php

/**
 * Get the best offer for a menu variation
 */
if (!function_exists('getVariationOffer')) {
    function getVariationOffer($variationId)
    {
        $variation = \App\Models\MenuVariation::find($variationId);
        if (!$variation) {
            return null;
        }
        return $variation->bestOffer();
    }
}

/**
 * Check if a menu variation has active offers
 */
if (!function_exists('hasVariationOffer')) {
    function hasVariationOffer($variationId)
    {
        $variation = \App\Models\MenuVariation::find($variationId);
        if (!$variation) {
            return false;
        }
        return $variation->hasActiveOffer();
    }
}

/**
 * Get all active offers for a menu variation (specific + all_items)
 */
if (!function_exists('getVariationOffers')) {
    function getVariationOffers($variationId)
    {
        $variation = \App\Models\MenuVariation::find($variationId);
        if (!$variation) {
            return collect();
        }
        return $variation->resolveApplicableOffers(null, false);
    }
}

/**
 * Get the best discount percentage for a variation
 */
if (!function_exists('getBestOfferDiscount')) {
    function getBestOfferDiscount($variationId)
    {
        $offer = getVariationOffer($variationId);
        return $offer ? $offer->discount_percent : 0;
    }
}

/**
 * Generate HTML for offer badge
 */
if (!function_exists('renderOfferBadge')) {
    function renderOfferBadge($variationId, $badgeClass = 'offer-badge')
    {
        $offer = getVariationOffer($variationId);

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
 */
if (!function_exists('getDiscountedPrice')) {
    function getDiscountedPrice($variationId, $price)
    {
        $offer = getVariationOffer($variationId);

        if (!$offer) {
            return $price;
        }

        $discountPercentage = $offer->discount_percent / 100;
        return round($price * (1 - $discountPercentage), 2);
    }
}
