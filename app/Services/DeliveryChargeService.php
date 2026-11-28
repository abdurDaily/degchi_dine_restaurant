<?php

namespace App\Services;

use App\Models\Setting;

/**
 * Backend-configurable delivery fee settings.
 *
 * The values live in the general settings table so the admin can change them
 * from General Setting without a migration. Checkout reads them for display,
 * and order placement reads the same base charge so the posted total and the
 * saved order can never drift apart.
 */
class DeliveryChargeService
{
    /** Legacy values kept as defaults when an admin has not saved anything yet. */
    private const DEFAULTS = [
        'delivery_charge' => OrderPricingService::DEFAULT_DELIVERY_CHARGE,
        'delivery_per_km_charge' => 20.0,
        'delivery_included_km' => 1.0,
    ];

    /**
     * @return array{base_charge:float, per_km_charge:float, included_km:float}
     */
    public static function settings(): array
    {
        $values = Setting::query()
            ->whereIn('key', array_keys(self::DEFAULTS))
            ->pluck('value', 'key');

        return [
            'base_charge' => self::number($values->get('delivery_charge'), self::DEFAULTS['delivery_charge']),
            'per_km_charge' => self::number($values->get('delivery_per_km_charge'), self::DEFAULTS['delivery_per_km_charge']),
            'included_km' => self::number($values->get('delivery_included_km'), self::DEFAULTS['delivery_included_km']),
        ];
    }

    public static function baseCharge(): float
    {
        return self::settings()['base_charge'];
    }

    private static function number(mixed $value, float $default): float
    {
        if ($value === null || $value === '') {
            return $default;
        }

        return max(0.0, round((float) $value, 2));
    }
}
