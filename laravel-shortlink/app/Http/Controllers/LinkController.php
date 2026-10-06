<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Services\SafeBrowsingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LinkController extends Controller
{
    private const CODE_ALPHABET = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
    private const CODE_LENGTH = 6;
    public const INTERSTITIAL_SECONDS = 8;

    private const RESERVED_CODES = ['api', 'go', 'favicon.ico', 'robots.txt', 'register', 'login', 'logout', 'dashboard', 'bio', 'u', 'sitemap.xml', 'blog', 'tools', 'about', 'contact', 'privacy-policy', 'terms', 'cookie-policy', 'acceptable-use-policy', 'dmca', 'report-abuse', 'admin', 'forgot-password', 'reset-password', 'profile', 'seo-tools'];
    private const MAX_BULK_LINKS = 20;

    public function __construct(private SafeBrowsingService $safeBrowsing) {}

    /**
     * POST /api/shorten — create a short code (or custom alias) for a long URL.
     */
    public function store(Request $request)
    {
        $longUrl = trim((string) $request->input('url', ''));

        if ($longUrl === '' || ! preg_match('#^https?://#i', $longUrl) || strlen($longUrl) > 2048) {
            return response()->json([
                'error' => 'Please enter a valid link starting with http:// or https://.',
            ], 422);
        }

        $host = parse_url($longUrl, PHP_URL_HOST);
        if ($host !== null && strcasecmp($host, $request->getHost()) === 0) {
            return response()->json([
                'error' => 'You cannot shorten a link to this site itself.',
            ], 422);
        }

        if ($this->safeBrowsing->isUnsafe($longUrl)) {
            return response()->json([
                'error' => 'This link was flagged by Google Safe Browsing as unsafe (phishing/malware) and cannot be shortened.',
            ], 422);
        }

        $password = trim((string) $request->input('password', ''));
        if ($password !== '' && strlen($password) < 4) {
            return response()->json(['error' => 'Password must be at least 4 characters.'], 422);
        }

        $expiresAt = $this->parseExpiry((string) $request->input('expires_at', ''));
        if ($expiresAt === false) {
            return response()->json(['error' => 'Please enter a valid expiration date in the future.'], 422);
        }

        $alias = trim((string) $request->input('alias', ''));

        if ($alias !== '') {
            if (! preg_match('/^[A-Za-z0-9_-]{3,30}$/', $alias)) {
                return response()->json([
                    'error' => 'Custom name can only use letters, numbers, - and _ (3-30 characters).',
                ], 422);
            }
            if (in_array(strtolower($alias), self::RESERVED_CODES, true)) {
                return response()->json(['error' => 'This name is not allowed, please choose another.'], 422);
            }
            if (Link::where('code', $alias)->exists()) {
                return response()->json(['error' => 'This custom name is already taken, please choose another.'], 409);
            }
            $code = $alias;
        } else {
            $code = null;
            for ($attempt = 0; $attempt < 5; $attempt++) {
                $candidate = $this->randomCode();
                if (! Link::where('code', $candidate)->exists()) {
                    $code = $candidate;
                    break;
                }
            }

            if ($code === null) {
                return response()->json(['error' => 'Server is busy, please try again.'], 503);
            }
        }

        Link::create([
            'code' => $code,
            'url' => $longUrl,
            'clicks' => 0,
            'user_id' => $request->user()?->id,
            'password' => $password !== '' ? Hash::make($password) : null,
            'expires_at' => $expiresAt,
        ]);

        return response()->json([
            'short' => url("/{$code}"),
        ]);
    }

    /**
     * POST /api/bulk-shorten — shorten several URLs at once (one per line).
     * No custom aliases here; each line gets an auto-generated code.
     */
    public function bulkStore(Request $request)
    {
        $raw = (string) $request->input('urls', '');
        $lines = preg_split('/\r\n|\r|\n/', $raw);
        $lines = array_values(array_filter(array_map('trim', $lines), fn ($line) => $line !== ''));

        if (count($lines) === 0) {
            return response()->json([
                'error' => 'Please enter at least one link, one per line.',
            ], 422);
        }

        if (count($lines) > self::MAX_BULK_LINKS) {
            return response()->json([
                'error' => 'You can shorten up to '.self::MAX_BULK_LINKS.' links at a time.',
            ], 422);
        }

        $results = array_map(fn ($longUrl) => $this->shortenOne($longUrl, $request), $lines);

        return response()->json(['results' => $results]);
    }

    /**
     * Validate and shorten a single URL for the bulk endpoint. Mirrors the
     * validation in store() but always auto-generates the code (no alias).
     */
    private function shortenOne(string $longUrl, Request $request): array
    {
        if (! preg_match('#^https?://#i', $longUrl) || strlen($longUrl) > 2048) {
            return ['url' => $longUrl, 'error' => 'Invalid link (must start with http:// or https://).'];
        }

        $host = parse_url($longUrl, PHP_URL_HOST);
        if ($host !== null && strcasecmp($host, $request->getHost()) === 0) {
            return ['url' => $longUrl, 'error' => 'Cannot shorten a link to this site itself.'];
        }

        if ($this->safeBrowsing->isUnsafe($longUrl)) {
            return ['url' => $longUrl, 'error' => 'Flagged by Google Safe Browsing as unsafe (phishing/malware).'];
        }

        $code = null;
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $candidate = $this->randomCode();
            if (! Link::where('code', $candidate)->exists()) {
                $code = $candidate;
                break;
            }
        }

        if ($code === null) {
            return ['url' => $longUrl, 'error' => 'Server is busy, please try again.'];
        }

        Link::create(['code' => $code, 'url' => $longUrl, 'clicks' => 0, 'user_id' => $request->user()?->id]);

        return ['url' => $longUrl, 'short' => url("/{$code}")];
    }

    /**
     * GET /{code} — by default, redirect instantly (bit.ly-style, AdSense-safe).
     * If config('app.show_interstitial') is enabled, show the countdown/ad
     * page instead and let the visitor continue via /go/{code}.
     */
    public function show(Request $request, string $code)
    {
        $link = Link::where('code', $code)->first();

        if (! $link) {
            return response()->view('link-not-found', [], 404);
        }

        if ($link->disabled) {
            return response()->view('link-disabled', [], 403);
        }

        if ($link->expires_at && $link->expires_at->isPast()) {
            return response()->view('link-expired', [], 410);
        }

        if ($link->password && ! $request->session()->get('unlocked_links.'.$link->code)) {
            return view('link-password', ['code' => $code]);
        }

        if (! config('app.show_interstitial')) {
            $link->increment('clicks');
            $this->recordClick($request, $link);

            return redirect()->away($link->url);
        }

        return view('redirect', [
            'code' => $code,
            'seconds' => self::INTERSTITIAL_SECONDS,
        ]);
    }

    /**
     * POST /{code}/unlock — verify a password-protected link's password.
     * On success, the code is remembered in the session so the visitor
     * isn't asked again, then they're sent back to GET /{code} to proceed.
     */
    public function unlock(Request $request, string $code)
    {
        $link = Link::where('code', $code)->first();

        if (! $link) {
            return response()->view('link-not-found', [], 404);
        }

        if ($link->disabled) {
            return response()->view('link-disabled', [], 403);
        }

        if ($link->expires_at && $link->expires_at->isPast()) {
            return response()->view('link-expired', [], 410);
        }

        $password = (string) $request->input('password', '');

        if (! $link->password || ! Hash::check($password, $link->password)) {
            return view('link-password', ['code' => $code, 'error' => 'Incorrect password, please try again.']);
        }

        $request->session()->put('unlocked_links.'.$code, true);

        return redirect("/{$code}");
    }

    /**
     * GET /go/{code} — used only in interstitial mode: perform the actual
     * redirect and count the click after the wait/ad page.
     */
    public function go(Request $request, string $code)
    {
        $link = Link::where('code', $code)->first();

        if (! $link) {
            return response()->view('link-not-found', [], 404);
        }

        if ($link->disabled) {
            return response()->view('link-disabled', [], 403);
        }

        if ($link->expires_at && $link->expires_at->isPast()) {
            return response()->view('link-expired', [], 410);
        }

        $link->increment('clicks');
        $this->recordClick($request, $link);

        return redirect()->away($link->url);
    }

    /**
     * @return \Carbon\Carbon|null|false null if no expiry given, false if invalid/not in the future
     */
    private function parseExpiry(string $expiresAt): \Carbon\Carbon|null|false
    {
        $expiresAt = trim($expiresAt);
        if ($expiresAt === '') {
            return null;
        }

        try {
            $parsed = \Carbon\Carbon::parse($expiresAt);
        } catch (\Throwable $e) {
            return false;
        }

        return $parsed->isFuture() ? $parsed : false;
    }

    private function recordClick(Request $request, Link $link): void
    {
        $referrer = $request->header('referer');

        $link->clickEvents()->create([
            'referrer' => $referrer ? substr($referrer, 0, 2048) : null,
            'device' => $this->detectDevice($request->userAgent()),
            'user_agent' => $request->userAgent() ? substr($request->userAgent(), 0, 512) : null,
        ]);
    }

    private function detectDevice(?string $userAgent): string
    {
        if (! $userAgent) {
            return 'unknown';
        }

        $ua = strtolower($userAgent);

        if (str_contains($ua, 'ipad') || str_contains($ua, 'tablet') || (str_contains($ua, 'android') && ! str_contains($ua, 'mobile'))) {
            return 'tablet';
        }

        if (str_contains($ua, 'mobi') || str_contains($ua, 'iphone') || str_contains($ua, 'android')) {
            return 'mobile';
        }

        return 'desktop';
    }

    private function randomCode(): string
    {
        $alphabet = self::CODE_ALPHABET;
        $max = strlen($alphabet) - 1;
        $code = '';
        for ($i = 0; $i < self::CODE_LENGTH; $i++) {
            $code .= $alphabet[random_int(0, $max)];
        }

        return $code;
    }
}
