<?php

namespace App\Http\Controllers;

use App\Models\BioPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BioPageController extends Controller
{
    private const RESERVED_SLUGS = ['api', 'go', 'login', 'register', 'logout', 'dashboard', 'bio', 'u'];

    public function edit(Request $request): View
    {
        $bioPage = $request->user()->bioPage;

        return view('bio.edit', ['bioPage' => $bioPage]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'slug' => ['required', 'string', 'regex:/^[A-Za-z0-9_-]{3,30}$/'],
            'title' => ['required', 'string', 'max:100'],
            'links' => ['required', 'array', 'min:1', 'max:20'],
            'links.*.label' => ['required', 'string', 'max:60'],
            'links.*.url' => ['required', 'string', 'max:2048', 'regex:#^https?://#i'],
        ], [
            'slug.regex' => 'Your page name can only use letters, numbers, - and _ (3-30 characters).',
        ]);

        if (in_array(strtolower($data['slug']), self::RESERVED_SLUGS, true)) {
            return back()->withErrors(['slug' => 'This page name is not allowed, please choose another.'])->withInput();
        }

        $taken = BioPage::where('slug', $data['slug'])
            ->where('user_id', '!=', $user->id)
            ->exists();

        if ($taken) {
            return back()->withErrors(['slug' => 'This page name is already taken, please choose another.'])->withInput();
        }

        // Drop any empty rows the "Add Link" button left behind.
        $links = array_values(array_filter($data['links'], fn ($link) => trim($link['label']) !== '' && trim($link['url']) !== ''));

        BioPage::updateOrCreate(
            ['user_id' => $user->id],
            ['slug' => $data['slug'], 'title' => $data['title'], 'links' => $links],
        );

        return redirect()->route('bio.edit')->with('status', 'Your link-in-bio page has been saved.');
    }

    public function show(string $slug): View
    {
        $bioPage = BioPage::where('slug', $slug)->firstOrFail();

        return view('bio.show', ['bioPage' => $bioPage]);
    }
}
