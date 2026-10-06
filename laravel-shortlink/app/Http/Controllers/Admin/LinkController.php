<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Link;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LinkController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $links = Link::with('user')
            ->when($search !== '', fn ($query) => $query->where(
                fn ($q) => $q->where('code', 'like', "%{$search}%")->orWhere('url', 'like', "%{$search}%")
            ))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.links', ['links' => $links, 'search' => $search]);
    }

    public function disable(Request $request, Link $link): RedirectResponse
    {
        $data = $request->validate(['reason' => ['nullable', 'string', 'max:255']]);
        $reason = trim($data['reason'] ?? '');

        $link->update([
            'disabled' => true,
            'disabled_reason' => $reason !== '' ? $reason : 'Disabled by admin',
        ]);

        return back()->with('status', 'Link disabled: '.$link->code);
    }

    public function enable(Link $link): RedirectResponse
    {
        $link->update(['disabled' => false, 'disabled_reason' => null]);

        return back()->with('status', 'Link re-enabled: '.$link->code);
    }
}
