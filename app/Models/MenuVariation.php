<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

class MenuVariation extends Model
{
    protected $fillable = [
        'menu_id', // Make sure this matches your migration column
        'name',
        'price',
        'image',
    ];

    /**
     * Get the menu that owns this variation.
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    /**
     * Get the offers that apply to this menu variation (specific_items pivot).
     */
    public function offers(): BelongsToMany
    {
        return $this->belongsToMany(
            Offer::class,
            'menu_variation_offer',
            'menu_variation_id',
            'offer_id'
        )->withTimestamps();
    }

    /**
     * Active, valid offers attached via pivot (specific_items only).
     */
    public function activeOffers()
    {
        return $this->offers()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('valid_from')->orWhere('valid_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('valid_until')->orWhere('valid_until', '>=', now());
            });
    }

    /**
     * Food-menu offers only: specific_items on this variation + all_items (applicable_to = all).
     * Membership / Student / Golden tier offers are excluded — they apply at checkout via member card.
     * Highest discount % wins when both all-items and specific-items apply.
     *
     * $subtotal is the whole cart/order subtotal. When supplied, offers whose
     * `min_total` is not met are dropped from the result regardless of
     * $forMenuDisplay, so a cart-level minimum is never treated as an
     * item-level one. When null (browsing the menu with no cart context) the
     * minimum is not enforced here — badges stay informational and the gate is
     * applied for real at pricing/checkout time.
     *
     * @param  Member|null  $member
     * @param  bool  $forMenuDisplay  Hide first-order food offers from members who already ordered.
     * @param  float|null  $subtotal  Whole cart/order subtotal used to honour min_total.
     */
    public function resolveApplicableOffers(?Member $member = null, bool $forMenuDisplay = false, ?float $subtotal = null): Collection
    {
        $specific = $this->relationLoaded('offers')
            ? $this->offers
            : $this->activeOffers()->get();

        $merged = $specific
            ->concat(Offer::activeAllItemOffers())
            ->unique('id')
            ->filter(function ($offer) use ($subtotal) {
                if (! $offer instanceof Offer) {
                    return false;
                }
                // Only food promos compete on menu cards / item offer discount
                if (! $offer->isFoodMenuOffer()) {
                    return false;
                }
                if ($offer->is_active === false) {
                    return false;
                }
                if (! $offer->isValid()) {
                    return false;
                }
                // Cart-level minimum order total (no-op when no subtotal is known)
                return $offer->meetsMinimumTotal($subtotal);
            });

        if ($forMenuDisplay) {
            $merged = $merged->filter(
                fn (Offer $offer) => $offer->isVisibleOnMenuFor($member, $subtotal)
            );
        }

        return $merged->sortByDesc('discount_percent')->values();
    }

    /**
     * Best food-menu offer for product cards (highest %).
     */
    public function bestDisplayOffer(?Member $member = null, ?float $subtotal = null): ?Offer
    {
        return $this->resolveApplicableOffers($member, true, $subtotal)->first();
    }

    /**
     * Best food-menu offer for helpers.
     *
     * Pass $subtotal when the current cart is known so an offer that does not
     * meet its minimum order total is not returned.
     */
    public function bestOffer(?float $subtotal = null)
    {
        return $this->resolveApplicableOffers(null, false, $subtotal)->first();
    }

    /**
     * Check if this variation has any active food-menu offers.
     */
    public function hasActiveOffer(): bool
    {
        return $this->resolveApplicableOffers(null, false)->isNotEmpty();
    }
}
