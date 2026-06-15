<?php

/**
 * OFFER SYSTEM IMPLEMENTATION GUIDE
 * 
 * This guide shows how to implement the new offer system across your frontend
 * with examples for different scenarios
 */

// ============================================
// 1. DISPLAYING MENU ITEMS WITH OFFER BADGES
// ============================================

/*
BLADE TEMPLATE EXAMPLE:
@foreach($categories as $category)
    <div class="category-section">
        <h2>{{ $category->name }}</h2>
        
        @foreach($category->menus as $menu)
            @foreach($menu->variations as $variation)
                <div class="menu-card">
                    <!-- OFFER BADGE - Shows on card top right -->
                    @if(hasVariationOffer($variation->id))
                        <div class="offer-badge-absolute">
                            {!! renderOfferBadge($variation->id, 'badge-offer') !!}
                        </div>
                    @endif
                    
                    <!-- MENU IMAGE -->
                    @if($variation->image)
                        <img src="{{ asset($variation->image) }}" alt="{{ $variation->name }}">
                    @endif
                    
                    <!-- MENU NAME -->
                    <h4>{{ $variation->name }}</h4>
                    
                    <!-- PRICING SECTION -->
                    @php 
                        $offer = getVariationOffer($variation->id);
                        $discountedPrice = getDiscountedPrice($variation->id, $variation->price);
                    @endphp
                    
                    <div class="price-section">
                        @if($offer)
                            <!-- Show both original and discounted price -->
                            <span class="original-price">
                                <del>৳{{ number_format($variation->price, 2) }}</del>
                            </span>
                            <span class="discounted-price">
                                ৳{{ number_format($discountedPrice, 2) }}
                            </span>
                            <small class="discount-info">
                                Save ৳{{ number_format($variation->price - $discountedPrice, 2) }}
                                ({{ $offer->discount_percent }}% OFF)
                            </small>
                        @else
                            <!-- Regular price if no offer -->
                            <span class="regular-price">
                                ৳{{ number_format($variation->price, 2) }}
                            </span>
                        @endif
                    </div>
                    
                    <!-- ADD TO CART BUTTON -->
                    <button class="btn btn-primary add-to-cart" 
                            data-variation-id="{{ $variation->id }}"
                            data-price="{{ $discountedPrice }}"
                            data-name="{{ $variation->name }}">
                        Add to Cart
                    </button>
                </div>
            @endforeach
        @endforeach
    </div>
@endforeach
*/

// ============================================
// 2. CHECKOUT PAGE - SHOW OFFER DETAILS
// ============================================

/*
BLADE TEMPLATE EXAMPLE:
@if($items)
    <table class="checkout-table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Offer</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @foreach($items as $item)
                @php
                    $variation = \App\Models\MenuVariation::find($item['variation_id'] ?? $item['id']);
                    $offer = $variation ? getVariationOffer($variation->id) : null;
                    $itemTotal = $item['price'] * $item['quantity'];
                    $grandTotal += $itemTotal;
                @endphp
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td>৳{{ number_format($item['price'], 2) }}</td>
                    <td>
                        @if($offer)
                            <span class="badge bg-success">{{ $offer->discount_percent }}% OFF</span>
                        @else
                            <span class="text-muted">No offer</span>
                        @endif
                    </td>
                    <td>৳{{ number_format($itemTotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <!-- ORDER SUMMARY -->
    <div class="order-summary">
        <div class="summary-row">
            <span>Subtotal:</span>
            <span>৳{{ number_format($grandTotal, 2) }}</span>
        </div>
        
        @if($memberDiscount)
            <div class="summary-row text-success">
                <span>Member Discount ({{ $memberDiscountPercent }}%):</span>
                <span>-৳{{ number_format($memberDiscount, 2) }}</span>
            </div>
        @endif
        
        @if($offerDiscount)
            <div class="summary-row text-success">
                <span>Offer Discount:</span>
                <span>-৳{{ number_format($offerDiscount, 2) }}</span>
            </div>
        @endif
        
        <div class="summary-row total">
            <strong>Total:</strong>
            <strong>৳{{ number_format($finalTotal, 2) }}</strong>
        </div>
    </div>
@endif
*/

// ============================================
// 3. JAVASCRIPT - DYNAMIC OFFER CHECKING
// ============================================

/*
// Fetch and display offer information when adding to cart
async function checkOfferOnAddToCart(variationId, priceElement) {
    try {
        const response = await fetch(`/api/variation/${variationId}/offers`);
        const data = await response.json();
        
        if (data.has_offers) {
            const offer = data.offers[0];
            const originalPrice = parseFloat(priceElement.textContent);
            const discountedPrice = originalPrice * (1 - offer.discount_percent / 100);
            
            // Update price display
            priceElement.innerHTML = `
                <del>${originalPrice.toFixed(2)}</del>
                <strong>${discountedPrice.toFixed(2)}</strong>
                <small class="text-success">${offer.discount_percent}% OFF</small>
            `;
        }
    } catch (error) {
        console.error('Error checking offers:', error);
    }
}

// Get all variations with their offers (for bulk display)
async function loadAllVariationsWithOffers() {
    try {
        const response = await fetch(`/api/variations/with-offers`);
        const data = await response.json();
        
        // data.variations contains all menu variations with their active offers
        console.log(data.variations);
        
        // Update UI with offer badges
        data.variations.forEach(variation => {
            if (variation.offers && variation.offers.length > 0) {
                const bestOffer = variation.offers[0];
                const badgeEl = document.querySelector(`[data-variation-id="${variation.id}"]`);
                if (badgeEl) {
                    badgeEl.innerHTML += `
                        <span class="badge badge-danger">${bestOffer.discount_percent}% OFF</span>
                    `;
                }
            }
        });
    } catch (error) {
        console.error('Error loading variations:', error);
    }
}
*/

// ============================================
// 4. CREATING OFFERS - ADMIN PANEL FORM
// ============================================

/*
BLADE FORM EXAMPLE (resources/views/backend/offers/form.blade.php):

<form action="{{ isset($offer->id) ? route('offers.update', $offer) : route('offers.store') }}" 
      method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($offer->id))
        @method('PUT')
    @endif
    
    <!-- Basic Info -->
    <div class="form-group mb-3">
        <label>Offer Name *</label>
        <input type="text" name="name" class="form-control" 
               value="{{ $offer->name ?? '' }}" required>
    </div>
    
    <div class="form-group mb-3">
        <label>Description</label>
        <textarea name="description" class="form-control" rows="3">{{ $offer->description ?? '' }}</textarea>
    </div>
    
    <div class="form-group mb-3">
        <label>Discount Percentage * (0-100)</label>
        <input type="number" name="discount_percent" class="form-control" 
               value="{{ $offer->discount_percent ?? 0 }}" min="0" max="100" required>
    </div>
    
    <!-- OFFER TYPE SELECTION -->
    <div class="form-group mb-3">
        <label>Offer Type *</label>
        <select name="offer_type" class="form-control" id="offerType" required 
                onchange="toggleMenuSelection()">
            <option value="all_items" 
                    {{ (isset($offer) && $offer->offer_type === 'all_items') ? 'selected' : '' }}>
                Apply to All Items
            </option>
            <option value="specific_items" 
                    {{ (isset($offer) && $offer->offer_type === 'specific_items') ? 'selected' : '' }}>
                Apply to Specific Items
            </option>
        </select>
    </div>
    
    <!-- MENU VARIATION SELECTION (hidden by default) -->
    <div class="form-group mb-3" id="menuVariationGroup" style="display: none;">
        <label>Select Menu Items to Apply Offer</label>
        <div class="menu-variation-list" style="max-height: 300px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;">
            @foreach($menuVariations ?? [] as $variation)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" 
                           name="menu_variation_ids[]" 
                           value="{{ $variation->id }}"
                           id="var_{{ $variation->id }}"
                           {{ (isset($selectedVariationIds) && in_array($variation->id, $selectedVariationIds)) ? 'checked' : '' }}>
                    <label class="form-check-label" for="var_{{ $variation->id }}">
                        {{ $variation->menu->name }} - {{ $variation->name }} 
                        (৳{{ $variation->price }})
                    </label>
                </div>
            @endforeach
        </div>
    </div>
    
    <div class="form-group mb-3">
        <label>Applicable To *</label>
        <select name="applicable_to" class="form-control" required>
            <option value="all" {{ (isset($offer) && $offer->applicable_to === 'all') ? 'selected' : '' }}>
                All Customers
            </option>
            <option value="membership" {{ (isset($offer) && $offer->applicable_to === 'membership') ? 'selected' : '' }}>
                Membership Only
            </option>
            <option value="student" {{ (isset($offer) && $offer->applicable_to === 'student') ? 'selected' : '' }}>
                Student Members
            </option>
            <option value="golden" {{ (isset($offer) && $offer->applicable_to === 'golden') ? 'selected' : '' }}>
                Golden Card Holders
            </option>
        </select>
    </div>
    
    <!-- TIME-BASED VALIDITY -->
    <div class="form-row">
        <div class="form-group mb-3 col-md-6">
            <label>Valid From (Optional)</label>
            <input type="datetime-local" name="valid_from" class="form-control" 
                   value="{{ isset($offer->valid_from) ? $offer->valid_from->format('Y-m-d\TH:i') : '' }}">
        </div>
        <div class="form-group mb-3 col-md-6">
            <label>Valid Until (Optional)</label>
            <input type="datetime-local" name="valid_until" class="form-control" 
                   value="{{ isset($offer->valid_until) ? $offer->valid_until->format('Y-m-d\TH:i') : '' }}">
        </div>
    </div>
    
    <!-- POPUP SETTINGS -->
    <h5 class="mt-4">Popup Banner Settings</h5>
    
    <div class="form-check mb-3">
        <input type="checkbox" name="show_as_popup" class="form-check-input" 
               id="showPopup" {{ (isset($offer) && $offer->show_as_popup) ? 'checked' : '' }}>
        <label class="form-check-label" for="showPopup">
            Show as Popup Banner on Home Page
        </label>
    </div>
    
    <div class="form-group mb-3">
        <label>Popup Image (PNG/JPG, Max 2MB)</label>
        <input type="file" name="popup_image" class="form-control" 
               accept="image/png,image/jpeg,image/webp">
        @if(isset($offer->popup_image))
            <small class="d-block mt-2">Current: 
                <a href="{{ asset('storage/' . $offer->popup_image) }}" target="_blank">View Image</a>
            </small>
        @endif
    </div>
    
    <div class="form-group mb-3">
        <label>Popup Badge Text (e.g., "Eid Special")</label>
        <input type="text" name="popup_badge" class="form-control" 
               value="{{ $offer->popup_badge ?? '' }}" maxlength="50">
    </div>
    
    <div class="form-group mb-3">
        <label>Popup Expiration Date</label>
        <input type="date" name="popup_expires_at" class="form-control" 
               value="{{ isset($offer->popup_expires_at) ? $offer->popup_expires_at->format('Y-m-d') : '' }}">
    </div>
    
    <!-- OTHER SETTINGS -->
    <div class="form-check mb-3">
        <input type="checkbox" name="is_first_order" class="form-check-input" 
               id="firstOrder" {{ (isset($offer) && $offer->is_first_order) ? 'checked' : '' }}>
        <label class="form-check-label" for="firstOrder">
            First Order Only
        </label>
    </div>
    
    <div class="form-group mb-3">
        <label>Minimum Order Total (Optional)</label>
        <input type="number" name="min_total" class="form-control" step="0.01"
               value="{{ $offer->min_total ?? '' }}">
    </div>
    
    <div class="form-check mb-3">
        <input type="checkbox" name="is_active" class="form-check-input" 
               id="isActive" {{ (!isset($offer) || $offer->is_active) ? 'checked' : '' }}>
        <label class="form-check-label" for="isActive">
            Active
        </label>
    </div>
    
    <button type="submit" class="btn btn-success">Save Offer</button>
    <a href="{{ route('offers.index') }}" class="btn btn-secondary">Cancel</a>
</form>

<script>
function toggleMenuSelection() {
    const offerType = document.getElementById('offerType').value;
    const menuGroup = document.getElementById('menuVariationGroup');
    menuGroup.style.display = offerType === 'specific_items' ? 'block' : 'none';
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', toggleMenuSelection);
</script>
*/

// ============================================
// 5. MEMBER DISCOUNT LOGIC FLOW
// ============================================

/*
DISCOUNT CALCULATION PRIORITY:

When an order is placed:

1. CHECK MEMBER STATUS
   └─ No member → No member discount
   └─ Expired card → No member discount
   └─ Golden card → 10% on all items
   └─ First order (not student) → 30% discount
   └─ First order (student) → 35% discount
   └─ After first order → Check for golden upgrade at ৳2000

2. CHECK ITEM-SPECIFIC OFFERS
   └─ For each item in cart:
      ├─ Check if it has active offers
      └─ Calculate discount (best offer)

3. APPLY HIGHER DISCOUNT
   └─ Use MAX(member_discount, item_offers_discount)

4. APPLY OFFER DETAILS TO ITEMS
   └─ Store offer_id and discount in items JSON

Example in code (app/Http/Controllers/Frontend/HomeController.php):

// Iterate through items and find offers
$items = json_decode($request->items, true);
foreach ($items as &$item) {
    $variation = MenuVariation::find($item['variation_id']);
    if ($variation && $variation->hasActiveOffer()) {
        $bestOffer = $variation->bestOffer();
        $item['offer_id'] = $bestOffer->id;
        $item['offer_discount'] = $variation->price * $bestOffer->discount_percent / 100;
    }
}
*/

// ============================================
// 6. TESTING THE SYSTEM
// ============================================

/*
QUICK TEST STEPS:

1. Create a test offer:
   - Admin → Offers → Create New
   - Name: "Test Offer - Biryani"
   - Discount: 25%
   - Offer Type: Specific Items
   - Select: Any menu variation
   - Click Save

2. Check home page:
   - Go to home page
   - Look for the selected menu item
   - It should show an offer badge
   - Price should be discounted

3. Test checkout:
   - Add the discounted item to cart
   - Go to checkout
   - Verify discount is shown

4. Test API:
   - Visit: /api/variation/{variation_id}/offers
   - Should return offer details in JSON

5. Test member purchase:
   - Use member card at checkout
   - Verify HIGHEST discount is applied (member or offer)

*/

// ============================================
// 7. COMMON BLADE HELPER USAGE
// ============================================

/*
Function: hasVariationOffer($variationId)
Returns: Boolean
Usage:   @if(hasVariationOffer($id)) ... @endif

Function: getVariationOffer($variationId)
Returns: Offer model or null
Usage:   @php $offer = getVariationOffer($id); @endphp

Function: getBestOfferDiscount($variationId)
Returns: Integer (0-100)
Usage:   {{ getBestOfferDiscount($id) }}% OFF

Function: renderOfferBadge($variationId, $class)
Returns: HTML string
Usage:   {!! renderOfferBadge($id, 'badge-danger') !!}

Function: getDiscountedPrice($variationId, $price)
Returns: Float
Usage:   ৳{{ getDiscountedPrice($id, $price) }}

Function: getVariationOffers($variationId)
Returns: Collection of offers
Usage:   @foreach(getVariationOffers($id) as $offer) ... @endforeach
*/

echo "✅ Offer System Implementation Guide Ready!";
