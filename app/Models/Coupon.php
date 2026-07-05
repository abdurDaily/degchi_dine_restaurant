<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'min_order_amount',
        'usage_limit',
        'used_count',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'is_active'        => 'boolean',
        'expires_at'       => 'date',
        'discount_value'   => 'decimal:2',
        'min_order_amount' => 'decimal:2',
    ];

    /**
     * Scope: only active, non-expired, under usage limit coupons.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()->toDateString());
            })
            ->where(function ($q) {
                $q->whereNull('usage_limit')->orWhereRaw('used_count < usage_limit');
            });
    }

    /**
     * Check if this coupon is valid for the given order total.
     */
    public function isValid(float $orderTotal): bool
    {
        if (!$this->is_active) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return false;
        if ($orderTotal < (float) $this->min_order_amount) return false;
        return true;
    }

    /**
     * Calculate discount amount for a given order total.
     */
    public function calculateDiscount(float $orderTotal): float
    {
        if ($this->discount_type === 'percent') {
            return round($orderTotal * ($this->discount_value / 100), 2);
        }
        return min((float) $this->discount_value, $orderTotal);
    }
}
