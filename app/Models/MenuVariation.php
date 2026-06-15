<?php

namespace App\Models;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MenuVariation extends Model
{
    protected $fillable = [
        'menu_id', // Make sure this matches your migration column
        'name',
        'price',
        'image'
    ];

    /**
     * Get the menu that owns this variation.
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    /**
     * Get the offers that apply to this menu variation.
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
     * Get active, valid offers for this menu variation.
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
     * Get the best offer (highest discount) for this variation.
     */
    public function bestOffer()
    {
        return $this->activeOffers()
            ->orderBy('discount_percent', 'desc')
            ->first();
    }

    /**
     * Check if this variation has any active offers.
     */
    public function hasActiveOffer(): bool
    {
        return $this->activeOffers()->exists();
    }
}
