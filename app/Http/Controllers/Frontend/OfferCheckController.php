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

        $offers = $variation->activeOffers()
            ->select(['id', 'name', 'description', 'discount_percent', 'popup_badge', 'offer_type'])
            ->get();

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
        $variations = MenuVariation::with(['activeOffers:id,name,discount_percent,popup_badge'])
            ->get(['id', 'menu_id', 'name', 'price', 'image']);

        return response()->json([
            'variations' => $variations,
        ]);
    }
}
