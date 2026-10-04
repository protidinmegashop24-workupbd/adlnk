<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogComment;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BlogCommentController extends Controller
{
    public function index(): View
    {
        $comments = BlogComment::with('post')
            ->orderByRaw('approved asc')
            ->latest()
            ->paginate(20);

        return view('admin.comments', ['comments' => $comments]);
    }

    public function approve(BlogComment $comment): RedirectResponse
    {
        $comment->update(['approved' => true]);

        return back()->with('status', 'Comment approved.');
    }

    public function destroy(BlogComment $comment): RedirectResponse
    {
        $comment->delete();

        return back()->with('status', 'Comment deleted.');
    }
}
