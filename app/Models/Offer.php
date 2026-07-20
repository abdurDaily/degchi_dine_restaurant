<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

class Offer extends Model
{
    protected $fillable = [
        'name',
        'description',
        'discount_percent',
        'applicable_to',
        'offer_type',
        'min_total',
        'is_first_order',
        'is_active',
        'show_as_popup',
        'popup_image',
        'popup_badge',
        'popup_expires_at',
        'valid_from',
        'valid_until',
    ];

    protected $casts = [
        'discount_percent'  => 'integer',
        'min_total'         => 'decimal:2',
        'is_first_order'    => 'boolean',
        'is_active'         => 'boolean',
        'show_as_popup'     => 'boolean',
        'popup_expires_at'  => 'date',
        'valid_from'        => 'datetime',
        'valid_until'       => 'datetime',
    ];

    /**
     * Get the menu variations that have this offer.
     */
    public function menuVariations(): BelongsToMany
    {
        return $this->belongsToMany(
            MenuVariation::class,
            'menu_variation_offer',
            'offer_id',
            'menu_variation_id'
        )->withTimestamps();
    }

    /**
     * Scope to get active offers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get valid offers (within time period)
     */
    public function scopeValid($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('valid_from')->orWhere('valid_from', '<=', now());
        })->where(function ($q) {
            $q->whereNull('valid_until')->orWhere('valid_until', '>=', now());
        });
    }

    /**
     * Check if offer is valid now
     */
    public function isValid(): bool
    {
        if ($this->valid_from && $this->valid_from > now()) {
            return false;
        }
        if ($this->valid_until && $this->valid_until < now()) {
            return false;
        }
        return true;
    }

    /**
     * Check if a menu variation has this offer
     */
    public function hasMenuItem(MenuVariation $variation): bool
    {
        if ($this->offer_type === 'all_items') {
            return true;
        }
        return $this->menuVariations()->where('menu_variation_id', $variation->id)->exists();
    }

    /**
     * Active, in-date "all food items" promos only (not membership/student/golden tier deals).
     */
    public static function activeAllItemOffers(): Collection
    {
        return cache()->remember('active_all_item_offers', 60, function () {
            return static::query()
                ->active()
                ->valid()
                ->where('offer_type', 'all_items')
                ->where('applicable_to', 'all')
                ->where('discount_percent', '>', 0)
                ->orderByDesc('discount_percent')
                ->get();
        });
    }

    /**
     * Food-menu promo (All / Specific items). Competes for largest badge on product cards.
     * Member-tier rows (membership / student / golden) are checkout benefits, not menu badges.
     */
    public function isFoodMenuOffer(): bool
    {
        return $this->applicable_to === 'all' && (int) $this->discount_percent > 0;
    }

    /**
     * Built-in membership benefit rows (30% / 35% / golden) — applied via member card at checkout.
     */
    public function isMemberTierOffer(): bool
    {
        return in_array($this->applicable_to, ['membership', 'student', 'golden'], true);
    }

    /**
     * Guest cannot use first-order food promos without signing in as a member.
     */
    public function requiresMemberLogin(): bool
    {
        return (bool) $this->is_first_order;
    }

    /**
     * Public food promo anyone can use without membership.
     */
    public function isPublicPromo(): bool
    {
        return $this->isFoodMenuOffer() && ! $this->is_first_order;
    }

    /**
     * Whether this food offer should appear on menu cards for the current viewer.
     * Guests still see first-order food offers (login required on add).
     * Members who already ordered must not see any first-order food offer.
     */
    public function isVisibleOnMenuFor(?Member $member): bool
    {
        if (! $this->isFoodMenuOffer() || $this->is_active === false || ! $this->isValid()) {
            return false;
        }

        if ($this->is_first_order) {
            if (! $member) {
                return true;
            }

            return $this->isEligibleForMember($member);
        }

        return true;
    }

    /**
     * All-items food offers currently visible on the menu for this viewer.
     */
    public static function visibleAllItemOffersFor(?Member $member = null): Collection
    {
        return static::activeAllItemOffers()
            ->filter(fn (self $offer) => $offer->isVisibleOnMenuFor($member))
            ->values();
    }

    /**
     * Whether this offer may apply for the given member (checkout / order).
     * Student first-order offers require admin approval; membership vs student are mutually exclusive.
     */
    public function isEligibleForMember(?Member $member): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        if ($this->is_first_order) {
            if (!$member) {
                return false;
            }
            if ($member->expires_at && $member->expires_at->isPast()) {
                return false;
            }
            if ($member->type === 'golden') {
                return false;
            }
            if ($member->first_order_discount_used || $member->hasCompletedOrders()) {
                return false;
            }
        }

        return match ($this->applicable_to) {
            'student' => $member
                && $member->is_student
                && $member->approval_status === 'approved',
            'membership' => $member && !$member->is_student,
            'golden' => $member && $member->type === 'golden',
            'all' => $this->is_first_order
                ? ($member !== null
                    && $member->type !== 'golden'
                    && !$member->first_order_discount_used
                    && !$member->hasCompletedOrders())
                : true,
            default => true,
        };
    }

    /**
     * Pick the best eligible offer for a member from a collection (highest discount wins among eligible only).
     */
    public static function bestEligibleForMember(iterable $offers, ?Member $member): ?self
    {
        $best = null;
        foreach ($offers as $offer) {
            if (!$offer instanceof self || !$offer->isEligibleForMember($member)) {
                continue;
            }
            if (!$best || $offer->discount_percent > $best->discount_percent) {
                $best = $offer;
            }
        }

        return $best;
    }
}
