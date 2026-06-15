<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
}
