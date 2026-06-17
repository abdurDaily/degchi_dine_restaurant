<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SeoSettings
{
    protected array $settings = [];

    public function __construct()
    {
        $this->settings = Cache::remember('seo_settings', 3600, function () {
            return Setting::where('setting_group', 'seo_setting')
                ->pluck('value', 'key')
                ->toArray();
        });
    }

    public static function clearCache(): void
    {
        Cache::forget('seo_settings');
    }

    public function get(string $key, ?string $default = null): ?string
    {
        $value = $this->settings[$key] ?? null;

        return ($value === null || $value === '') ? $default : $value;
    }

    public function siteName(): string
    {
        return $this->get('seo_site_name', config('app.name', 'Degchi Dine'));
    }

    public function title(?string $pageTitle = null): string
    {
        if ($pageTitle) {
            return $pageTitle . ' | ' . $this->siteName();
        }

        return $this->get('seo_default_title', $this->siteName());
    }

    public function description(?string $override = null): string
    {
        return $override ?: $this->get('seo_default_description', '');
    }

    public function keywords(?string $override = null): string
    {
        return $override ?: $this->get('seo_default_keywords', '');
    }

    public function robots(?string $override = null): string
    {
        return $override ?: $this->get('seo_robots_default', 'index, follow');
    }

    public function ogType(?string $override = null): string
    {
        return $override ?: $this->get('seo_og_type', 'website');
    }

    public function twitterCard(): string
    {
        return $this->get('seo_twitter_card', 'summary_large_image');
    }

    public function twitterHandle(): ?string
    {
        $handle = $this->get('seo_twitter_handle');

        return $handle ? ltrim($handle, '@') : null;
    }

    public function ogImage(?string $override = null): string
    {
        if ($override) {
            return $override;
        }

        $image = $this->get('seo_og_image');

        if ($image) {
            return asset('storage/seo/' . ltrim($image, '/'));
        }

        return asset('assets/frontend/images/logo.webp');
    }

    public function canonical(?string $override = null): string
    {
        if ($override) {
            return $override;
        }

        $base = rtrim($this->get('seo_canonical_url', config('app.url')), '/');
        $path = request()->getPathInfo();

        if ($path === '' || $path === '/') {
            return $base . '/';
        }

        return $base . $path;
    }

    public function googleAnalyticsId(): ?string
    {
        return $this->get('seo_google_analytics_id');
    }

    public function googleTagManagerId(): ?string
    {
        return $this->get('seo_google_tag_manager_id');
    }

    public function facebookPixelId(): ?string
    {
        return $this->get('seo_facebook_pixel_id');
    }

    public function headScripts(): ?string
    {
        return $this->get('seo_head_scripts');
    }

    public function robotsTxt(): string
    {
        return $this->get('seo_robots_txt', "User-agent: *\nAllow: /\n\nSitemap: " . url('/sitemap.xml'));
    }
}
