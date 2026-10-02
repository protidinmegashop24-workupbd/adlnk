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

        $links = (clone $userLinks)->latest()->paginate(15);
        $totalLinks = (clone $userLinks)->count();
        $totalClicks = (clone $userLinks)->sum('clicks');

        return view('dashboard', compact('links', 'totalLinks', 'totalClicks'));
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
