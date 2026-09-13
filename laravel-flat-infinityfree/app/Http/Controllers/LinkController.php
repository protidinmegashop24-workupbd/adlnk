<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LinkController extends Controller
{
    private const CODE_ALPHABET = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
    private const CODE_LENGTH = 6;
    public const INTERSTITIAL_SECONDS = 8;

    /**
     * POST /api/shorten — create a short code for a long URL.
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
     * GET /{code} — safety-check interstitial page, then the visitor
     * continues to /go/{code} for the actual redirect.
     */
    public function show(string $code)
    {
        $link = Link::where('code', $code)->first();

        if (! $link) {
            return response()->view('link-not-found', [], 404);
        }

        return view('redirect', [
            'code' => $code,
            'seconds' => self::INTERSTITIAL_SECONDS,
        ]);
    }

    /**
     * GET /go/{code} — perform the actual redirect and count the click.
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
