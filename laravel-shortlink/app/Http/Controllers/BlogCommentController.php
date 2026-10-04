<?php

namespace App\Http\Controllers;

use App\Models\BlogComment;
use App\Models\BlogPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BlogCommentController extends Controller
{
    public function store(Request $request, string $slug): RedirectResponse
    {
        $post = BlogPost::published()->where('slug', $slug)->first();

        abort_unless($post, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'body' => ['required', 'string', 'max:2000'],
        ]);

        // Honeypot: the "website" field is hidden from real visitors via CSS,
        // so only a bot filling every input would set it. Pretend success
        // without saving, rather than returning a validation error, so bots
        // get no signal their post was blocked.
        if ($request->filled('website')) {
            return redirect(route('blog.show', $post->slug).'#comments')
                ->with('status', 'Thanks! Your comment has been submitted and will appear after review.');
        }

        BlogComment::create([
            'blog_post_id' => $post->id,
            'name' => trim($data['name']),
            'email' => $data['email'] ?? null,
            'body' => trim($data['body']),
            'ip_address' => $request->ip(),
            'approved' => false,
        ]);

        return redirect(route('blog.show', $post->slug).'#comments')
            ->with('status', 'Thanks! Your comment has been submitted and will appear after review.');
    }
}
