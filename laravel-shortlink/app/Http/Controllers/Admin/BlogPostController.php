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
            'slug' => ['nullable', 'string', 'max:100', 'regex:/^[a-z0-9-]*$/'],
            'excerpt' => ['required', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'category' => ['required', 'string', 'max:60'],
            'published' => ['nullable', 'boolean'],
            'thumbnail_upload' => ['nullable', 'image', 'max:2048'],
        ]);

        // 'slug' is nullable, so $request->validate() omits the key entirely
        // when it's absent from the request rather than including it as
        // null — normalize it so callers can always rely on the key existing.
        $validated['slug'] = $validated['slug'] ?? '';
        $validated['published'] = $request->boolean('published');

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
     * Non-technical-friendly default: if the admin typed plain text (no HTML
     * tags at all), auto-wrap blank-line-separated blocks into <p> tags and
     * escape the content. If they wrote real HTML tags, trust it as-is —
     * this is single-admin-authored content, the same trust model the
     * previous hard-coded Blade post partials already had.
     */
    private function autoFormatBody(string $raw): string
    {
        if (str_contains($raw, '<')) {
            return $raw;
        }

        $blocks = preg_split('/\n\s*\n/', trim($raw));

        return collect($blocks)
            ->filter(fn ($b) => trim($b) !== '')
            ->map(fn ($b) => '<p>'.nl2br(e(trim($b))).'</p>')
            ->implode("\n");
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
