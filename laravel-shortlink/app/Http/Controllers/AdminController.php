<?php

namespace App\Http\Controllers;

use App\Models\AbuseReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function reports(): View
    {
        $reports = AbuseReport::with('link')
            ->orderByRaw("status = 'pending' desc")
            ->latest()
            ->paginate(20);

        return view('admin.reports', ['reports' => $reports]);
    }

    public function disableLink(AbuseReport $report): RedirectResponse
    {
        abort_unless($report->link, 404);

        $report->link->update([
            'disabled' => true,
            'disabled_reason' => $report->reason,
        ]);

        $report->update(['status' => 'actioned']);

        return back()->with('status', 'Link disabled.');
    }

    public function dismiss(AbuseReport $report): RedirectResponse
    {
        $report->update(['status' => 'dismissed']);

        return back()->with('status', 'Report dismissed.');
    }
}
