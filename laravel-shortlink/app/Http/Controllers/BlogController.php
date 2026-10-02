<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Posts live as Blade partials in resources/views/blog/posts/{slug}.blade.php.
     * Metadata lives here since there's no database table for a handful of
     * site-written articles — add a new entry here plus the matching partial
     * to publish a new post. 'thumbnail' is a path under public/, relative
     * to the public root (e.g. "images/blog/my-post.png").
     */
    public static function posts(): array
    {
        return [
            [
                'slug' => 'what-is-a-url-shortener',
                'title' => 'What Is a URL Shortener and Why Should You Use One?',
                'excerpt' => 'Long links are hard to share, remember, and track. Here\'s what a URL shortener actually does and when it helps.',
                'date' => '2026-10-01',
                'category' => 'URL Shortening',
                'thumbnail' => 'images/blog/what-is-a-url-shortener.png',
            ],
            [
                'slug' => 'how-to-create-a-qr-code',
                'title' => 'How to Create a QR Code for Any Link (Free, No Signup)',
                'excerpt' => 'Turn any link into a scannable QR code in seconds — for print, packaging, or a storefront sign.',
                'date' => '2026-10-01',
                'category' => 'QR Codes',
                'thumbnail' => 'images/blog/how-to-create-a-qr-code.png',
            ],
            [
                'slug' => 'what-is-link-in-bio',
                'title' => 'What Is Link-in-Bio? A Simple Guide for Creators and Small Businesses',
                'excerpt' => 'Instagram and TikTok only let you put one link in your profile. Here\'s how to share many, the right way.',
                'date' => '2026-10-01',
                'category' => 'Link-in-Bio',
                'thumbnail' => 'images/blog/what-is-link-in-bio.png',
            ],
        ];
    }

    /**
     * @return array<string, int> category name => post count, ordered by count desc
     */
    public static function categories(): array
    {
        $counts = [];
        foreach (self::posts() as $post) {
            $counts[$post['category']] = ($counts[$post['category']] ?? 0) + 1;
        }
        arsort($counts);

        return $counts;
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $category = trim((string) $request->query('category', ''));

        $posts = collect(self::posts())
            ->when($search !== '', fn ($posts) => $posts->filter(
                fn ($post) => str_contains(strtolower($post['title']), strtolower($search))
                    || str_contains(strtolower($post['excerpt']), strtolower($search))
            ))
            ->when($category !== '', fn ($posts) => $posts->where('category', $category))
            ->values();

        $latest = collect(self::posts())->sortByDesc('date')->take(5)->values();

        return view('blog.index', [
            'posts' => $posts,
            'latest' => $latest,
            'categories' => self::categories(),
            'search' => $search,
            'activeCategory' => $category,
        ]);
    }

    public function show(string $slug)
    {
        $post = collect(self::posts())->firstWhere('slug', $slug);

        abort_unless($post, 404);
        abort_unless(view()->exists('blog.posts.'.$slug), 404);

        $latest = collect(self::posts())->where('slug', '!=', $slug)->sortByDesc('date')->take(5)->values();

        return view('blog.show', [
            'post' => $post,
            'latest' => $latest,
            'categories' => self::categories(),
        ]);
    }
}
