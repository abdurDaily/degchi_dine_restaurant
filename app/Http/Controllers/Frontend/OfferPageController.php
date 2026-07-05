<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Offer;
use App\Models\Category;
use App\Models\MenuVariation;
use Illuminate\Http\Request;

class OfferPageController extends Controller
{
    public function index(Request $request)
    {
        // Fetch menu items that have at least one active offer on their variations
        $query = Menu::where('is_available', 1)
            ->with([
                'variations' => function ($q) {
                    $q->with(['offers' => function ($offerQuery) {
                        $offerQuery->where('is_active', true)
                            ->where(function ($q) {
                                $q->whereNull('valid_from')->orWhere('valid_from', '<=', now());
                            })
                            ->where(function ($q) {
                                $q->whereNull('valid_until')->orWhere('valid_until', '>=', now());
                            })
                            ->select(['offers.id', 'offers.name', 'offers.discount_percent']);
                    }]);
                },
                'category',
            ])
            ->whereHas('variations.offers', function ($q) {
                $q->where('is_active', true)
                    ->where(function ($subQ) {
                        $subQ->whereNull('valid_from')->orWhere('valid_from', '<=', now());
                    })
                    ->where(function ($subQ) {
                        $subQ->whereNull('valid_until')->orWhere('valid_until', '>=', now());
                    });
            });

        // Price filter
        $minPriceLimit = (float) (MenuVariation::min('price') ?? 0);
        $maxPriceLimit = (float) (MenuVariation::max('price') ?? 1000);
        $minPrice = $request->query('min_price', $minPriceLimit);
        $maxPrice = $request->query('max_price', $maxPriceLimit);

        $query->whereHas('variations', function ($q) use ($minPrice, $maxPrice) {
            $q->whereBetween('price', [$minPrice, $maxPrice]);
        });

        $menus = $query->orderBy('name')->paginate(12)->withQueryString();

        // Active offer summaries for header banner
        $activeOffers = Offer::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('valid_from')->orWhere('valid_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('valid_until')->orWhere('valid_until', '>=', now());
            })
            ->select(['id', 'name', 'discount_percent', 'description'])
            ->orderByDesc('discount_percent')
            ->get();

        $categories = Category::where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        if ($request->ajax()) {
            return view('frontend.partials.menu_grid', compact('menus'))->render();
        }

        return view('frontend.offers.index', compact(
            'menus',
            'activeOffers',
            'categories',
            'minPriceLimit',
            'maxPriceLimit',
            'minPrice',
            'maxPrice'
        ));
    }
}
