<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Services\ContentFormatterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Edit-only — unlike blog posts, these are a fixed set of pages each tied
 * to its own permanent route (/about, /contact, ...), so there's
 * deliberately no create or delete here: creating one would have nowhere
 * to be served from, and deleting one (Privacy Policy, Terms) would break
 * a real route and could have real legal consequences.
 */
class PageController extends Controller
{
    public function __construct(private readonly ContentFormatterService $formatter) {}

    public function index(): View
    {
        $pages = Page::orderBy('title')->get();

        return view('admin.pages.index', ['pages' => $pages]);
    }

    public function edit(Page $page): View
    {
        return view('admin.pages.edit', ['page' => $page]);
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'body_mode' => ['required', 'in:text,html'],
        ]);

        $data['meta_title'] = $data['meta_title'] ?? '';
        $data['meta_description'] = $data['meta_description'] ?? '';
        $data['meta_title'] = $data['meta_title'] !== '' ? $data['meta_title'] : null;
        $data['meta_description'] = $data['meta_description'] !== '' ? $data['meta_description'] : null;
        $data['body_raw'] = $data['body'];
        $data['body'] = $this->formatter->format($data['body'], $data['body_mode']);

        $page->update($data);

        return redirect()->route('admin.pages.index')->with('status', 'Page updated: '.$page->title);
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
}
