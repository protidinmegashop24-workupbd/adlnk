<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Checks a URL against Google's Safe Browsing threat lists before it's
 * allowed to be shortened, so klikwit can't be used to cloak a known
 * phishing/malware link in the first place — this is what gets a shortener
 * domain itself flagged by Safe Browsing and shown with a red warning in
 * Chrome.
 *
 * Requires a free API key (GOOGLE_SAFE_BROWSING_API_KEY in .env) from
 * Google Cloud Console with the "Safe Browsing API" enabled. Without a key
 * configured, isUnsafe() always returns false — shortening keeps working
 * exactly as before, just without this extra check.
 *
 * Fails open: if the API call errors or times out, the link is allowed
 * through rather than blocking every submission during a Google outage.
 * A disabled/missing key is logged once per request (not fatal) so it's
 * discoverable without breaking anything.
 */
class SafeBrowsingService
{
    private const ENDPOINT = 'https://safebrowsing.googleapis.com/v4/threatMatches:find';

    private const TIMEOUT_SECONDS = 3;

    public function isUnsafe(string $url): bool
    {
        $apiKey = config('services.safe_browsing.key');

        if (! $apiKey) {
            return false;
        }

        try {
            $response = Http::timeout(self::TIMEOUT_SECONDS)
                ->post(self::ENDPOINT.'?key='.$apiKey, [
                    'client' => [
                        'clientId' => 'klikwit',
                        'clientVersion' => '1.0.0',
                    ],
                    'threatInfo' => [
                        'threatTypes' => ['MALWARE', 'SOCIAL_ENGINEERING', 'UNWANTED_SOFTWARE', 'POTENTIALLY_HARMFUL_APPLICATION'],
                        'platformTypes' => ['ANY_PLATFORM'],
                        'threatEntryTypes' => ['URL'],
                        'threatEntries' => [['url' => $url]],
                    ],
                ]);

            if (! $response->successful()) {
                Log::warning('Safe Browsing API call failed', ['status' => $response->status()]);

                return false;
            }

            return ! empty($response->json('matches'));
        } catch (\Throwable $e) {
            Log::warning('Safe Browsing API call errored', ['message' => $e->getMessage()]);

            return false;
        }
    }
}
