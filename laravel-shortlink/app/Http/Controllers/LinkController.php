<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    private const CODE_ALPHABET = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
    private const CODE_LENGTH = 6;
    public const INTERSTITIAL_SECONDS = 8;

    private const RESERVED_CODES = ['api', 'go', 'favicon.ico', 'robots.txt'];
    private const MAX_BULK_LINKS = 20;

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

        Link::create(['code' => $code, 'url' => $longUrl, 'clicks' => 0]);

        return ['url' => $longUrl, 'short' => url("/{$code}")];
    }

    /**
     * GET /{code} — by default, redirect instantly (bit.ly-style, AdSense-safe).
     * If config('app.show_interstitial') is enabled, show the countdown/ad
     * page instead and let the visitor continue via /go/{code}.
     */
    public function show(string $code)
    {
        $link = Link::where('code', $code)->first();

        if (! $link) {
            return response()->view('link-not-found', [], 404);
        }

        if (! config('app.show_interstitial')) {
            $link->increment('clicks');

            return redirect()->away($link->url);
        }

        return view('redirect', [
            'code' => $code,
            'seconds' => self::INTERSTITIAL_SECONDS,
        ]);
    }

    /**
     * GET /go/{code} — used only in interstitial mode: perform the actual
     * redirect and count the click after the wait/ad page.
     */
    public function go(string $code)
    {
        $link = Link::where('code', $code)->first();

        if (! $link) {
            return response()->view('link-not-found', [], 404);
        }

        $link->increment('clicks');

        return redirect()->away($link->url);
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
