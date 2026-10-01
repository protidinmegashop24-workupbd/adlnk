<?php

namespace App\Http\Controllers;

class BlogController extends Controller
{
    /**
     * Posts live as Blade partials in resources/views/blog/posts/{slug}.blade.php.
     * Metadata lives here since there's no database table for a handful of
     * site-written articles — add a new entry here plus the matching partial
     * to publish a new post.
     */
    public static function posts(): array
    {
        return [
            [
                'slug' => 'what-is-a-url-shortener',
                'title' => 'What Is a URL Shortener and Why Should You Use One?',
                'excerpt' => 'Long links are hard to share, remember, and track. Here\'s what a URL shortener actually does and when it helps.',
                'date' => '2026-10-01',
            ],
            [
                'slug' => 'how-to-create-a-qr-code',
                'title' => 'How to Create a QR Code for Any Link (Free, No Signup)',
                'excerpt' => 'Turn any link into a scannable QR code in seconds — for print, packaging, or a storefront sign.',
                'date' => '2026-10-01',
            ],
            [
                'slug' => 'what-is-link-in-bio',
                'title' => 'What Is Link-in-Bio? A Simple Guide for Creators and Small Businesses',
                'excerpt' => 'Instagram and TikTok only let you put one link in your profile. Here\'s how to share many, the right way.',
                'date' => '2026-10-01',
            ],
        ];
    }

    public function index()
    {
        return view('blog.index', ['posts' => self::posts()]);
    }

    public function show(string $slug)
    {
        $post = collect(self::posts())->firstWhere('slug', $slug);

        abort_unless($post, 404);
        abort_unless(view()->exists('blog.posts.'.$slug), 404);

        return view('blog.show', ['post' => $post]);
    }
}
