<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SeoSettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'seo_site_name' => 'Degchi Dine',
            'seo_default_title' => 'Degchi Dine | Authentic Kacchi & Biriyani in Chittagong',
            'seo_default_description' => 'Experience authentic dum-style kacchi, biriyani and traditional clay-pot dining at Degchi Dine in Halishahar, Chittagong. Order online or visit us today.',
            'seo_default_keywords' => 'Degchi Dine, kacchi biriyani, Chittagong restaurant, Halishahar food, clay pot dining, Bangladesh restaurant',
            'seo_robots_default' => 'index, follow',
            'seo_og_type' => 'website',
            'seo_twitter_card' => 'summary_large_image',
            'seo_robots_txt' => "User-agent: *\nAllow: /\n\nSitemap: /sitemap.xml",
        ];

        foreach ($defaults as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'setting_group' => 'seo_setting',
                    'value' => $value,
                    'user_id' => 1,
                ]
            );
        }
    }
}
