<?php

namespace App\Services\Seo;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Pulls real related-search suggestions from Google's public autocomplete
 * endpoint — the same free technique tools like AnswerThePublic's free tier
 * use. This deliberately does NOT report search volume, keyword difficulty,
 * or CPC: those numbers require a paid data provider (Ahrefs/SEMrush/
 * DataForSEO/Google Ads API with spend history), and this app's standing
 * rule is never to show fabricated numbers. What autocomplete gives us is
 * genuine — it's exactly what Google suggests to real searchers — just
 * without volume estimates attached.
 *
 * To go beyond the ~10 suggestions Google returns for a bare query, this
 * also queries a handful of common modifiers ("how", "best", "vs", "near
 * me", ...) in parallel and merges/dedupes the results — the same
 * "alphabet soup" expansion technique free keyword tools use, just capped
 * to a short, curated modifier list instead of the full A-Z sweep to keep
 * response times reasonable.
 */
class KeywordSuggestionService
{
    private const ENDPOINT = 'https://suggestqueries.google.com/complete/search';

    private const TIMEOUT_SECONDS = 4;

    private const MODIFIERS = ['how', 'what', 'why', 'best', 'vs', 'for', 'near me', 'free', 'alternative'];

    /**
     * @return array{seed: string, groups: array<string, array<int, string>>, error: ?string}
     */
    public function suggest(string $seed): array
    {
        $seed = trim($seed);

        if ($seed === '') {
            return ['seed' => $seed, 'groups' => [], 'error' => 'Please enter a keyword.'];
        }

        $queries = ['base' => $seed];
        foreach (self::MODIFIERS as $modifier) {
            $queries[$modifier] = $seed.' '.$modifier;
        }

        try {
            $responses = Http::pool(function ($pool) use ($queries) {
                $requests = [];
                foreach ($queries as $label => $query) {
                    $requests[] = $pool->as($label)->timeout(self::TIMEOUT_SECONDS)->get(self::ENDPOINT, [
                        'client' => 'firefox',
                        'q' => $query,
                    ]);
                }

                return $requests;
            });
        } catch (\Throwable $e) {
            Log::warning('Keyword suggestion fetch errored', ['message' => $e->getMessage()]);

            return ['seed' => $seed, 'groups' => [], 'error' => 'Could not reach the suggestion service. Please try again.'];
        }

        $groups = [];
        $seen = [];
        $anySuccess = false;

        foreach ($queries as $label => $query) {
            $response = $responses[$label] ?? null;

            if (! $response || $response instanceof \Throwable || ! $response->successful()) {
                continue;
            }

            $json = $response->json();
            $suggestions = is_array($json) && isset($json[1]) && is_array($json[1]) ? $json[1] : [];

            if ($suggestions === []) {
                continue;
            }

            $anySuccess = true;
            $fresh = [];

            foreach ($suggestions as $suggestion) {
                if (! is_string($suggestion)) {
                    continue;
                }
                $key = mb_strtolower(trim($suggestion));
                if ($key === '' || isset($seen[$key])) {
                    continue;
                }
                $seen[$key] = true;
                $fresh[] = $suggestion;
            }

            if ($fresh !== []) {
                $groups[$label === 'base' ? 'Related Searches' : ucfirst($label)] = $fresh;
            }
        }

        if (! $anySuccess) {
            return ['seed' => $seed, 'groups' => [], 'error' => 'No suggestions found, or the suggestion service is unavailable right now.'];
        }

        return ['seed' => $seed, 'groups' => $groups, 'error' => null];
    }
}
