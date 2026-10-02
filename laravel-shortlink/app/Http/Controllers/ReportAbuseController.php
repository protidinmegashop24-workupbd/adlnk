<?php

namespace App\Http\Controllers;

use App\Models\AbuseReport;
use App\Models\Link;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportAbuseController extends Controller
{
    private const REASONS = ['phishing', 'malware', 'spam', 'copyright', 'illegal', 'other'];

    public function show(): View
    {
        return view('report-abuse');
    }

    public function submit(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'link' => ['required', 'string', 'max:2048'],
            'reason' => ['required', 'string', 'in:'.implode(',', self::REASONS)],
            'details' => ['nullable', 'string', 'max:2000'],
            'reporter_email' => ['nullable', 'email', 'max:255'],
        ]);

        $code = $this->extractCode(trim($data['link']));

        if ($code === null) {
            return back()->withErrors(['link' => 'Please enter a valid klikwit short link (e.g. klikwit.com/abc123).'])->withInput();
        }

        $link = Link::where('code', $code)->first();

        AbuseReport::create([
            'link_id' => $link?->id,
            'code' => $code,
            'reason' => $data['reason'],
            'details' => $data['details'] ?? null,
            'reporter_email' => $data['reporter_email'] ?? null,
        ]);

        return redirect()->route('report-abuse')->with('status', 'Thank you — your report has been submitted and will be reviewed.');
    }

    /**
     * Accept either a bare code or a full klikwit URL and pull out the code.
     */
    private function extractCode(string $input): ?string
    {
        $path = parse_url($input, PHP_URL_PATH) ?: $input;
        $code = trim($path, '/');

        return preg_match('/^[A-Za-z0-9_-]{3,30}$/', $code) ? $code : null;
    }
}
