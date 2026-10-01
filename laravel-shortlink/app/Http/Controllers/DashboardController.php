<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $links = $request->user()
            ->links()
            ->latest()
            ->paginate(15);

        return view('dashboard', ['links' => $links]);
    }

    public function destroy(Request $request, Link $link): RedirectResponse
    {
        abort_unless($link->user_id === $request->user()->id, 403);

        $link->delete();

        return redirect()->route('dashboard')->with('status', 'Link deleted.');
    }
}
