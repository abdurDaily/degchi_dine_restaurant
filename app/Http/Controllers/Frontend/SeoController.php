<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BlogPost;
use App\Models\Menu;
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
            ['loc' => route('frontend.about'), 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => route('frontend.cards'), 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => route('frontend.card.apply'), 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['loc' => route('frontend.contact'), 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => route('frontend.reviews.index'), 'priority' => '0.7', 'changefreq' => 'weekly'],
            ['loc' => route('frontend.order.track'), 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['loc' => route('frontend.partyBooking'), 'priority' => '0.7', 'changefreq' => 'monthly'],
        ];

        // Dynamic: branches
        Branch::select('slug', 'updated_at')->each(function ($branch) use (&$urls) {
            $urls[] = [
                'loc' => route('frontend.branches.show', $branch->slug),
                'priority' => '0.7',
                'changefreq' => 'monthly',
                'lastmod' => $branch->updated_at?->toIso8601String(),
            ];
        });

        // Dynamic: blog posts
        BlogPost::where('status', 'published')
            ->select('slug', 'updated_at')
            ->each(function ($post) use (&$urls) {
                $urls[] = [
                    'loc' => route('frontend.blog.show', $post->slug),
                    'priority' => '0.6',
                    'changefreq' => 'monthly',
                    'lastmod' => $post->updated_at?->toIso8601String(),
                ];
            });

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>' . e($url['loc']) . "</loc>\n";
            if (!empty($url['lastmod'])) {
                $xml .= '    <lastmod>' . e($url['lastmod']) . "</lastmod>\n";
            }
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
