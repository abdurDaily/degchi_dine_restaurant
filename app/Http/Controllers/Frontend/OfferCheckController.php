<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\MenuVariation;
use Illuminate\Http\Request;

class OfferCheckController extends Controller
{
    /**
     * Get active offers for a specific menu variation
     * 
     * @param int $variationId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOffersForVariation($variationId)
    {
        $variation = MenuVariation::find($variationId);

        if (!$variation) {
            return response()->json(['error' => 'Variation not found'], 404);
        }

        $offers = $variation->resolveApplicableOffers(null, false)
            ->map(fn ($offer) => $offer->only([
                'id', 'name', 'description', 'discount_percent', 'popup_badge', 'offer_type', 'is_first_order', 'applicable_to',
            ]))
            ->values();

        return response()->json([
            'has_offers' => $offers->isNotEmpty(),
            'offers' => $offers,
            'best_discount' => $offers->max('discount_percent') ?? 0,
        ]);
    }

    /**
     * Get all menu variations with their offers (for bulk display)
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllVariationsWithOffers()
    {
        $globalOffers = \App\Models\Offer::activeAllItemOffers();

        $variations = MenuVariation::with(['offers' => function ($q) {
            $q->where('is_active', true)
                ->where(function ($sub) {
                    $sub->whereNull('valid_from')->orWhere('valid_from', '<=', now());
                })
                ->where(function ($sub) {
                    $sub->whereNull('valid_until')->orWhere('valid_until', '>=', now());
                });
        }])->get(['id', 'menu_id', 'name', 'price', 'image']);

        $variations->each(function ($variation) use ($globalOffers) {
            $variation->setRelation(
                'activeOffers',
                $variation->offers->concat($globalOffers)->unique('id')->sortByDesc('discount_percent')->values()
            );
        });

        return response()->json([
            'variations' => $variations,
        ]);
    }
}
