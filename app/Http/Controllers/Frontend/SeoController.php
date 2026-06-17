<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Support\SeoSettings;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function robots(SeoSettings $seo): Response
    {
        return response($seo->robotsTxt(), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    public function sitemap(): Response
    {
        $urls = [
            ['loc' => route('frontend.home'), 'priority' => '1.0', 'changefreq' => 'daily'],
            ['loc' => route('frontend.completeMenu'), 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['loc' => route('frontend.cards'), 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => route('frontend.card.apply'), 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => route('frontend.contact'), 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => route('frontend.reviews.index'), 'priority' => '0.7', 'changefreq' => 'weekly'],
            ['loc' => route('frontend.order.track'), 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['loc' => route('frontend.branches.index'), 'priority' => '0.8', 'changefreq' => 'weekly'],
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>' . e($url['loc']) . "</loc>\n";
            $xml .= '    <changefreq>' . $url['changefreq'] . "</changefreq>\n";
            $xml .= '    <priority>' . $url['priority'] . "</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }
}
