<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Replaces the static public/sitemap.xml that shipped in an earlier
     * phase (which Laravel's router never actually saw, since a static file
     * under public/ is served directly and takes priority over any route
     * with the same path) with a route built from the same named routes
     * used elsewhere in the app, so it can't silently drift out of date the
     * way the static file did.
     *
     * Deliberately excludes private pages (dashboard, profile, admin —
     * already noindex,nofollow via layouts.dashboard), auth pages (login,
     * register), and per-user dynamic pages (bio pages, short links).
     *
     * @return array<int, array{url: string, changefreq: string, priority: string}>
     */
    private function urls(): array
    {
        $urls = [
            ['url' => route('home'), 'changefreq' => 'weekly', 'priority' => '1.0'],
            ['url' => route('seo-tools.index'), 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['url' => route('tools.index'), 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['url' => route('tools.expand'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['url' => route('tools.check'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['url' => route('tools.utm'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['url' => route('seo-tools.meta-tag-checker'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['url' => route('seo-tools.serp-preview'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['url' => route('seo-tools.keyword-density-checker'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['url' => route('seo-tools.word-counter'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['url' => route('seo-tools.seo-url-checker'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['url' => route('seo-tools.slug-generator'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['url' => route('seo-tools.robots-txt-generator'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['url' => route('seo-tools.xml-sitemap-generator'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['url' => route('seo-tools.canonical-checker'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['url' => route('seo-tools.open-graph-checker'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['url' => route('seo-tools.keyword-suggestions'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['url' => route('blog.index'), 'changefreq' => 'weekly', 'priority' => '0.8'],
            ['url' => route('pages.about'), 'changefreq' => 'yearly', 'priority' => '0.4'],
            ['url' => route('pages.contact'), 'changefreq' => 'yearly', 'priority' => '0.4'],
            ['url' => route('pages.privacy'), 'changefreq' => 'yearly', 'priority' => '0.3'],
            ['url' => route('pages.terms'), 'changefreq' => 'yearly', 'priority' => '0.3'],
            ['url' => route('pages.cookies'), 'changefreq' => 'yearly', 'priority' => '0.3'],
            ['url' => route('pages.aup'), 'changefreq' => 'yearly', 'priority' => '0.3'],
            ['url' => route('pages.dmca'), 'changefreq' => 'yearly', 'priority' => '0.3'],
            ['url' => route('report-abuse'), 'changefreq' => 'yearly', 'priority' => '0.3'],
        ];

        foreach (BlogPost::published()->get() as $post) {
            $urls[] = ['url' => route('blog.show', $post->slug), 'changefreq' => 'monthly', 'priority' => '0.6'];
        }

        return $urls;
    }

    public function index(): Response
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
        foreach ($this->urls() as $entry) {
            $xml .= '  <url>'
                .'<loc>'.htmlspecialchars($entry['url'], ENT_XML1).'</loc>'
                .'<changefreq>'.$entry['changefreq'].'</changefreq>'
                .'<priority>'.$entry['priority'].'</priority>'
                .'</url>'."\n";
        }
        $xml .= '</urlset>'."\n";

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
