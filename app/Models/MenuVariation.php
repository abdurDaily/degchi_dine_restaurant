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
     * @param  Member|null  $member
     * @param  bool  $forMenuDisplay  Hide first-order food offers from members who already ordered.
     */
    public function resolveApplicableOffers(?Member $member = null, bool $forMenuDisplay = false): Collection
    {
        $specific = $this->relationLoaded('offers')
            ? $this->offers
            : $this->activeOffers()->get();

        $merged = $specific
            ->concat(Offer::activeAllItemOffers())
            ->unique('id')
            ->filter(function ($offer) {
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

                return $offer->isValid();
            });

        if ($forMenuDisplay) {
            $merged = $merged->filter(
                fn (Offer $offer) => $offer->isVisibleOnMenuFor($member)
            );
        }

        return $merged->sortByDesc('discount_percent')->values();
    }

    /**
     * Best food-menu offer for product cards (highest %).
     */
    public function bestDisplayOffer(?Member $member = null): ?Offer
    {
        return $this->resolveApplicableOffers($member, true)->first();
    }

    /**
     * Best food-menu offer for helpers.
     */
    public function bestOffer()
    {
        return $this->resolveApplicableOffers(null, false)->first();
    }

    /**
     * Check if this variation has any active food-menu offers.
     */
    public function hasActiveOffer(): bool
    {
        return $this->resolveApplicableOffers(null, false)->isNotEmpty();
    }
}
