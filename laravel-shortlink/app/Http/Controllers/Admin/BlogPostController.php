<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BlogPostController extends Controller
{
    public function index(): View
    {
        $posts = BlogPost::latest('published_at')->latest()->paginate(20);

        return view('admin.blog.index', ['posts' => $posts]);
    }

    public function create(): View
    {
        return view('admin.blog.create', ['existingCategories' => $this->existingCategories()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePost($request);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?: $data['title']);
        $data['body'] = $this->autoFormatBody($data['body']);
        $data['author_id'] = $request->user()->id;

        if ($request->hasFile('thumbnail_upload')) {
            $data['thumbnail'] = $this->storeThumbnail($request, $data['slug']);
        }

        if ($data['published'] && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        BlogPost::create($data);

        return redirect()->route('admin.blog.index')->with('status', 'Post created.');
    }

    public function edit(BlogPost $post): View
    {
        return view('admin.blog.edit', ['post' => $post, 'existingCategories' => $this->existingCategories()]);
    }

    public function update(Request $request, BlogPost $post): RedirectResponse
    {
        $data = $this->validatePost($request, $post->id);
        $newSlug = $data['slug'] ?: $post->slug;
        $data['slug'] = $newSlug === $post->slug ? $post->slug : $this->uniqueSlug($newSlug, $post->id);
        $data['body'] = $this->autoFormatBody($data['body']);

        if ($request->hasFile('thumbnail_upload')) {
            $data['thumbnail'] = $this->storeThumbnail($request, $data['slug']);
        }

        if ($data['published'] && ! $post->published_at) {
            $data['published_at'] = now();
        }

        $post->update($data);

        return redirect()->route('admin.blog.index')->with('status', 'Post updated.');
    }

    public function destroy(BlogPost $post): RedirectResponse
    {
        $post->delete();

        return redirect()->route('admin.blog.index')->with('status', 'Post deleted.');
    }

    private function validatePost(Request $request, ?int $ignoreId = null): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:100', 'regex:/^[a-z0-9-]*$/'],
            'excerpt' => ['required', 'string', 'max:500'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'category' => ['required', 'string', 'max:60'],
            'published' => ['nullable', 'boolean'],
            'thumbnail_upload' => ['nullable', 'image', 'max:2048'],
        ]);

        // Several fields above are nullable, so $request->validate() omits
        // them entirely from the returned array when absent from the request
        // rather than including them as null — normalize so callers can
        // always rely on the keys existing.
        $validated['slug'] = $validated['slug'] ?? '';
        $validated['meta_title'] = $validated['meta_title'] ?: null;
        $validated['meta_description'] = $validated['meta_description'] ?: null;
        $validated['published'] = $request->boolean('published');
        $validated['category'] = trim($validated['category']);

        unset($validated['thumbnail_upload']);

        return $validated;
    }

    private function uniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($source);
        $slug = $base;
        $i = 2;

        while (BlogPost::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }

    /**
     * Non-technical-friendly default: if the admin typed plain text (no real
     * HTML tags), this converts a small set of easy, Markdown-like shortcuts
     * so they never need to write actual HTML:
     *   ## Heading / ### Smaller Heading
     *   - bullet  (every line in the block starting with - or *)
     *   1. numbered  (every line in the block starting with a number + ".")
     *   **bold**
     *   [link text](https://example.com)
     * Everything else becomes a plain paragraph. If the admin writes real
     * HTML tags anywhere in the post, the whole body is trusted as-is
     * instead — this is single-admin-authored content, the same trust
     * model the previous hard-coded Blade post partials already had, but
     * mixing the two styles in one post isn't supported (documented in the
     * admin form's hint text).
     */
    private function autoFormatBody(string $raw): string
    {
        // Looks for an actual HTML tag shape (<tag ...> or </tag>), not just any
        // "<" character — plain prose like "5 < 10" must not accidentally flip
        // the whole post into the untouched raw-HTML trust path.
        if (preg_match('/<\/?[a-zA-Z][a-zA-Z0-9]*(\s[^>]*)?>/', $raw)) {
            return $raw;
        }

        $blocks = preg_split('/\n\s*\n/', trim($raw));

        return collect($blocks)
            ->map(fn ($b) => trim($b))
            ->filter(fn ($b) => $b !== '')
            ->map(fn ($b) => $this->formatBlock($b))
            ->implode("\n");
    }

    private function formatBlock(string $block): string
    {
        if (preg_match('/^###\s+(.+)$/s', $block, $m)) {
            return '<h3>'.$this->inlineFormat($m[1]).'</h3>';
        }
        if (preg_match('/^##\s+(.+)$/s', $block, $m)) {
            return '<h2>'.$this->inlineFormat($m[1]).'</h2>';
        }

        $lines = preg_split('/\n/', $block);

        if (collect($lines)->every(fn ($l) => preg_match('/^[-*]\s+/', trim($l)))) {
            $items = collect($lines)
                ->map(fn ($l) => '<li>'.$this->inlineFormat(preg_replace('/^[-*]\s+/', '', trim($l))).'</li>')
                ->implode('');

            return '<ul>'.$items.'</ul>';
        }

        if (collect($lines)->every(fn ($l) => preg_match('/^\d+\.\s+/', trim($l)))) {
            $items = collect($lines)
                ->map(fn ($l) => '<li>'.$this->inlineFormat(preg_replace('/^\d+\.\s+/', '', trim($l))).'</li>')
                ->implode('');

            return '<ol>'.$items.'</ol>';
        }

        return '<p>'.nl2br($this->inlineFormat($block)).'</p>';
    }

    /**
     * Escapes the text first (so any literal HTML-looking characters the
     * admin typed are neutralized), then layers **bold** and [text](url)
     * on top of the already-escaped string. Links are restricted to
     * http(s):// or site-relative paths — never javascript:/data: etc.
     */
    private function inlineFormat(string $text): string
    {
        $escaped = e($text);
        $escaped = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $escaped);

        return preg_replace_callback('/\[([^\]]+)\]\(([^)]+)\)/', function ($m) {
            $url = trim($m[2]);
            if (! preg_match('#^(https?://|/)#i', $url)) {
                return $m[0];
            }

            return '<a href="'.$url.'">'.$m[1].'</a>';
        }, $escaped);
    }

    private function existingCategories(): array
    {
        return BlogPost::select('category')->distinct()->orderBy('category')->pluck('category')->all();
    }

    private function storeThumbnail(Request $request, string $slug): string
    {
        $file = $request->file('thumbnail_upload');
        $filename = $slug.'-'.time().'.'.$file->getClientOriginalExtension();
        $file->move(public_path('images/blog'), $filename);

        return 'images/blog/'.$filename;
    }
}
