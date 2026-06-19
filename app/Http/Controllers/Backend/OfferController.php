<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\MenuVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OfferController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:offers-show')->only(['index', 'create', 'edit']);
        $this->middleware('permission:offers-create')->only('store');
        $this->middleware('permission:offers-edit')->only(['update', 'toggleStatus']);
        $this->middleware('permission:offers-delete')->only('destroy');
    }

    public function index()
    {
        $offers = Offer::with('menuVariations')->latest()->get();
        return view('backend.offers.index', compact('offers'));
    }

    public function create()
    {
        $menuVariations = MenuVariation::with('menu')->get();
        return view('backend.offers.form', [
            'offer' => new Offer(),
            'menuVariations' => $menuVariations
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        if ($request->hasFile('popup_image')) {
            $data['popup_image'] = $request->file('popup_image')->store('offers', 'public');
        }

        $offer = Offer::create($data);

        // Attach selected menu variations if offer_type is 'specific_items'
        if ($request->offer_type === 'specific_items' && $request->filled('menu_variations')) {
            $offer->menuVariations()->sync($request->menu_variations);
        }

        return redirect()->route('offers.index')->with('success', 'Offer created successfully.');
    }

    public function edit(Offer $offer)
    {
        $menuVariations = MenuVariation::with('menu')->get();
        $selectedVariationIds = $offer->menuVariations()->pluck('menu_variation_id')->toArray();

        return view('backend.offers.form', [
            'offer' => $offer,
            'menuVariations' => $menuVariations,
            'selectedVariationIds' => $selectedVariationIds
        ]);
    }

    public function update(Request $request, Offer $offer)
    {
        $data = $this->validated($request, $offer->id);

        if ($request->hasFile('popup_image')) {
            // Delete old image
            if ($offer->popup_image) {
                Storage::disk('public')->delete($offer->popup_image);
            }
            $data['popup_image'] = $request->file('popup_image')->store('offers', 'public');
        }

        $offer->update($data);

        // Update menu variations relationship
        if ($request->offer_type === 'specific_items') {
            $offer->menuVariations()->sync($request->filled('menu_variations') ? $request->menu_variations : []);
        } else {
            // If changed to 'all_items', clear any specific items
            $offer->menuVariations()->detach();
        }

        return redirect()->route('offers.index')->with('success', 'Offer updated successfully.');
    }

    public function destroy(Offer $offer)
    {
        if ($offer->popup_image) {
            Storage::disk('public')->delete($offer->popup_image);
        }
        $offer->menuVariations()->detach();
        $offer->delete();

        return response()->json(['success' => true, 'message' => 'Offer deleted.']);
    }

    public function toggleStatus(Offer $offer)
    {
        $offer->update(['is_active' => !$offer->is_active]);
        return response()->json(['success' => true, 'is_active' => $offer->is_active]);
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string|max:1000',
            'discount_percent'  => 'required|integer|min:0|max:100',
            'applicable_to'     => 'required|string|max:100',
            'offer_type'        => 'required|in:all_items,specific_items',
            'menu_variations'   => 'nullable|array',
            'menu_variations.*' => 'integer|exists:menu_variations,id',
            'min_total'         => 'nullable|numeric|min:0',
            'is_first_order'    => 'sometimes|boolean',
            'is_active'         => 'sometimes|boolean',
            'show_as_popup'     => 'sometimes|boolean',
            'popup_image'       => 'nullable|file|image|mimes:webp,png,jpg,jpeg|max:2048',
            'popup_badge'       => 'nullable|string|max:50',
            'popup_expires_at'  => 'nullable|date',
            'valid_from'        => 'nullable|date',
            'valid_until'       => 'nullable|date',
        ]);
    }
}
