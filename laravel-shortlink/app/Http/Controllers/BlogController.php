<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * @return array<string, int> category name => published post count, ordered by count desc
     */
    public static function categories(): array
    {
        $counts = BlogPost::published()
            ->selectRaw('category, count(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->all();

        arsort($counts);

        return $counts;
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $category = trim((string) $request->query('category', ''));

        $posts = BlogPost::published()
            ->when($search !== '', fn ($query) => $query->where(
                fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('excerpt', 'like', "%{$search}%")
            ))
            ->when($category !== '', fn ($query) => $query->where('category', $category))
            ->latest('published_at')
            ->get();

        $latest = BlogPost::published()->latest('published_at')->take(5)->get();

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
        $post = BlogPost::published()->where('slug', $slug)->first();

        abort_unless($post, 404);

        $latest = BlogPost::published()->where('slug', '!=', $slug)->latest('published_at')->take(5)->get();

        return view('blog.show', [
            'post' => $post,
            'comments' => $post->approvedComments,
            'latest' => $latest,
            'categories' => self::categories(),
        ]);
    }
}
