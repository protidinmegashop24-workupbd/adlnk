<?php

namespace App\Services\Seo;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Calls Google's own free PageSpeed Insights API (Lighthouse under the
 * hood) to get a real performance score and Core Web Vitals for a page.
 * Deliberately does not attempt to run Lighthouse locally — that needs a
 * headless Chrome, which shared cPanel hosting can't run — and never
 * estimates or fabricates a score when the API is unavailable.
 *
 * Requires a free API key (GOOGLE_PAGESPEED_API_KEY in .env) from Google
 * Cloud Console with the "PageSpeed Insights API" enabled — the same
 * Google Cloud project used for GOOGLE_SAFE_BROWSING_API_KEY can usually
 * just have this second API enabled too, reusing the same key value.
 */
class PageSpeedService
{
    private const ENDPOINT = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed';

    private const TIMEOUT_SECONDS = 25;

    /**
     * @return array{error: ?string, score: ?int, metrics: array<string, ?string>, opportunities: array<int, string>}
     */
    public function analyze(string $url, string $strategy = 'mobile'): array
    {
        $apiKey = config('services.pagespeed.key');

        if (! $apiKey) {
            return $this->errorResult('Page speed checking isn\'t configured yet — it needs a free Google API key. See the setup note below.');
        }

        try {
            $response = Http::timeout(self::TIMEOUT_SECONDS)->get(self::ENDPOINT, [
                'url' => $url,
                'key' => $apiKey,
                'strategy' => $strategy,
                'category' => 'performance',
            ]);
        } catch (\Throwable $e) {
            Log::warning('PageSpeed API call errored', ['message' => $e->getMessage()]);

            return $this->errorResult('Could not reach the PageSpeed service. Please try again.');
        }

        if (! $response->successful()) {
            $message = $response->json('error.message');
            Log::warning('PageSpeed API call failed', ['status' => $response->status(), 'message' => $message]);

            return $this->errorResult($message ?: 'The PageSpeed service could not analyze this URL.');
        }

        $json = $response->json();
        $lighthouse = $json['lighthouseResult'] ?? null;

        if (! $lighthouse) {
            return $this->errorResult('The PageSpeed service did not return a result for this URL.');
        }

        $scoreRaw = $lighthouse['categories']['performance']['score'] ?? null;
        $audits = $lighthouse['audits'] ?? [];

        $metric = fn (string $key) => $audits[$key]['displayValue'] ?? null;

        $opportunities = [];
        foreach ($audits as $audit) {
            if (($audit['score'] ?? 1) < 0.9 && ($audit['details']['type'] ?? null) === 'opportunity' && ! empty($audit['title'])) {
                $opportunities[] = $audit['title'];
            }
        }

        return [
            'error' => null,
            'score' => $scoreRaw !== null ? (int) round($scoreRaw * 100) : null,
            'metrics' => [
                'Largest Contentful Paint' => $metric('largest-contentful-paint'),
                'Cumulative Layout Shift' => $metric('cumulative-layout-shift'),
                'Total Blocking Time' => $metric('total-blocking-time'),
                'First Contentful Paint' => $metric('first-contentful-paint'),
                'Speed Index' => $metric('speed-index'),
            ],
            'opportunities' => array_slice($opportunities, 0, 6),
        ];
    }

    /**
     * @return array{error: string, score: null, metrics: array<string, null>, opportunities: array<int, string>}
     */
    private function errorResult(string $message): array
    {
        return ['error' => $message, 'score' => null, 'metrics' => [], 'opportunities' => []];
    }
}
