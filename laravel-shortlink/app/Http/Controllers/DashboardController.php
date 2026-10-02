<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userLinks = $request->user()->links();

        $search = trim((string) $request->query('q', ''));
        $sort = $request->query('sort', 'newest');

        $filtered = (clone $userLinks);
        if ($search !== '') {
            $filtered->where(function ($query) use ($search) {
                $query->where('code', 'like', "%{$search}%")
                    ->orWhere('url', 'like', "%{$search}%");
            });
        }

        match ($sort) {
            'oldest' => $filtered->oldest(),
            'most_clicks' => $filtered->orderByDesc('clicks'),
            'least_clicks' => $filtered->orderBy('clicks'),
            default => $filtered->latest(),
        };

        $links = $filtered->paginate(15)->withQueryString();
        $totalLinks = (clone $userLinks)->count();
        $totalClicks = (clone $userLinks)->sum('clicks');

        return view('dashboard', compact('links', 'totalLinks', 'totalClicks', 'search', 'sort'));
    }

    public function analytics(Request $request, Link $link): View
    {
        abort_unless($link->user_id === $request->user()->id, 403);

        $deviceCounts = $link->clickEvents()
            ->selectRaw('device, count(*) as count')
            ->groupBy('device')
            ->pluck('count', 'device');

        $topReferrers = $link->clickEvents()
            ->whereNotNull('referrer')
            ->selectRaw('referrer, count(*) as count')
            ->groupBy('referrer')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        $recentClicks = $link->clickEvents()
            ->latest('created_at')
            ->limit(20)
            ->get();

        return view('dashboard.analytics', [
            'link' => $link,
            'deviceCounts' => $deviceCounts,
            'topReferrers' => $topReferrers,
            'recentClicks' => $recentClicks,
        ]);
    }

    public function destroy(Request $request, Link $link): RedirectResponse
    {
        abort_unless($link->user_id === $request->user()->id, 403);

        $link->delete();

        return redirect()->route('dashboard')->with('status', 'Link deleted.');
    }
}
