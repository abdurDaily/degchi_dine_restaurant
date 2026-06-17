<?php

namespace App\Support;

use App\Models\Setting;

class SslCommerzConfig
{
    public static function get(string $key, mixed $default = null): mixed
    {
        $value = Setting::where('setting_group', 'sslcommerz_setting')
            ->where('key', $key)
            ->value('value');

        if ($value !== null && $value !== '') {
            if ($key === 'sandbox') {
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            }

            return $value;
        }

        return config('sslcommerz.' . $key, $default);
    }

    public static function isConfigured(): bool
    {
        return self::get('store_id') !== '' && self::get('store_password') !== '';
    }
}
