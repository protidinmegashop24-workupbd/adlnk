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

    /**
     * POST /api/shorten — create a short code (or custom alias) for a long URL.
     */
    public function store(Request $request)
    {
        $longUrl = trim((string) $request->input('url', ''));

        if ($longUrl === '' || ! preg_match('#^https?://#i', $longUrl) || strlen($longUrl) > 2048) {
            return response()->json([
                'error' => 'সঠিক http:// অথবা https:// দিয়ে শুরু হওয়া একটি লিংক দিন।',
            ], 422);
        }

        $host = parse_url($longUrl, PHP_URL_HOST);
        if ($host !== null && strcasecmp($host, $request->getHost()) === 0) {
            return response()->json([
                'error' => 'নিজের সাইটের লিংক শর্ট করা যাবে না।',
            ], 422);
        }

        $alias = trim((string) $request->input('alias', ''));

        if ($alias !== '') {
            if (! preg_match('/^[A-Za-z0-9_-]{3,30}$/', $alias)) {
                return response()->json([
                    'error' => 'কাস্টম নামে শুধু ইংরেজি অক্ষর, সংখ্যা, - ও _ ব্যবহার করা যাবে (৩-৩০ অক্ষর)।',
                ], 422);
            }
            if (in_array(strtolower($alias), self::RESERVED_CODES, true)) {
                return response()->json(['error' => 'এই নামটি ব্যবহার করা যাবে না, অন্য নাম দিন।'], 422);
            }
            if (Link::where('code', $alias)->exists()) {
                return response()->json(['error' => 'এই কাস্টম নামটি ইতিমধ্যে ব্যবহৃত হয়েছে, অন্য নাম দিন।'], 409);
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
                return response()->json(['error' => 'সার্ভার ব্যস্ত, আবার চেষ্টা করুন।'], 503);
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
