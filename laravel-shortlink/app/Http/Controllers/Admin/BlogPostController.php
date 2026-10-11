<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Services\AiContentGeneratorService;
use App\Services\ContentFormatterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BlogPostController extends Controller
{
    public function __construct(private readonly ContentFormatterService $formatter) {}

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
        $data['body_raw'] = $data['body'];
        $data['body'] = $this->formatter->format($data['body'], $data['body_mode']);
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
        $data['body_raw'] = $data['body'];
        $data['body'] = $this->formatter->format($data['body'], $data['body_mode']);

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

    public function preview(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'body' => ['required', 'string'],
            'body_mode' => ['required', 'in:text,html'],
        ]);

        return response()->json([
            'html' => $this->formatter->format($validated['body'], $validated['body_mode']),
        ]);
    }

    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'max:4096'],
        ]);

        $file = $request->file('image');
        $filename = 'post-'.time().'-'.Str::random(8).'.'.$file->getClientOriginalExtension();
        $file->move(public_path('images/blog/content'), $filename);

        return response()->json(['url' => asset('images/blog/content/'.$filename)]);
    }

    public function generate(Request $request, AiContentGeneratorService $generator): JsonResponse
    {
        $validated = $request->validate([
            'topic' => ['required', 'string', 'max:200'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'language' => ['required', 'in:bn,en'],
        ]);

        try {
            $draft = $generator->generate($validated['topic'], $validated['notes'] ?? null, $validated['language']);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json($draft);
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
            'body_mode' => ['required', 'in:text,html'],
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
