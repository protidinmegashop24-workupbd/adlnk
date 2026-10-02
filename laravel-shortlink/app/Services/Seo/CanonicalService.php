<?php

namespace App\Services\Seo;

/**
 * Turns parsed <head> data into the specific checks the Canonical Checker
 * tool reports: does a canonical tag exist, is it well-formed, does it
 * point back at the page itself (or somewhere else), and does its scheme
 * or www-prefix disagree with the page actually served.
 */
class CanonicalService
{
    /**
     * @param  array  $parsed  MetaAnalyzerService::parse() output
     * @return array<int, array{status: string, label: string, finding: string, explanation: string, action: ?string}>
     */
    public function check(string $requestedUrl, string $finalUrl, array $parsed): array
    {
        $checks = [];
        $canonical = $parsed['canonical'];

        if ($canonical === null) {
            $checks[] = [
                'status' => 'missing',
                'label' => 'Canonical Tag',
                'finding' => 'No canonical tag was found on this page.',
                'explanation' => 'A canonical tag tells search engines which URL is the "main" version of a page when the same content can be reached through more than one URL.',
                'action' => 'Add a <link rel="canonical" href="..."> tag in the page\'s <head> pointing to the preferred URL.',
            ];

            return $checks;
        }

        $checks[] = [
            'status' => 'pass',
            'label' => 'Canonical Tag',
            'finding' => "Canonical tag found: {$canonical}",
            'explanation' => 'This page specifies a canonical URL.',
            'action' => null,
        ];

        if (! preg_match('#^https?://#i', $canonical)) {
            $checks[] = [
                'status' => 'warning',
                'label' => 'Canonical Format',
                'finding' => 'The canonical value is not a full, absolute URL.',
                'explanation' => 'A relative canonical (e.g. "/page" instead of "https://example.com/page") can be interpreted inconsistently by different crawlers.',
                'action' => 'Use a full absolute URL, including the scheme (http:// or https://) and domain.',
            ];

            return $checks;
        }

        $isSelf = $this->normalize($canonical) === $this->normalize($finalUrl);

        $checks[] = [
            'status' => $isSelf ? 'pass' : 'warning',
            'label' => 'Self-Referencing Canonical',
            'finding' => $isSelf
                ? 'The canonical tag points back to this same page.'
                : 'The canonical tag points to a different URL than the one analyzed.',
            'explanation' => $isSelf
                ? 'A self-referencing canonical is normal and expected for most pages.'
                : 'This is only a problem if it\'s unintentional — pointing elsewhere on purpose (e.g. from a filtered or paginated URL to the main page) is a valid, common use of canonical tags.',
            'action' => $isSelf ? null : 'If this page should be indexed on its own, point the canonical at itself. If it intentionally defers to another URL, no action is needed.',
        ];

        $canonicalScheme = parse_url($canonical, PHP_URL_SCHEME);
        $finalScheme = parse_url($finalUrl, PHP_URL_SCHEME);
        if ($canonicalScheme && $finalScheme && strcasecmp($canonicalScheme, $finalScheme) !== 0) {
            $checks[] = [
                'status' => 'warning',
                'label' => 'HTTP / HTTPS Match',
                'finding' => "The page is served over {$finalScheme} but the canonical uses {$canonicalScheme}.",
                'explanation' => 'Mixing schemes between the served page and its canonical can send mixed signals about which version is authoritative.',
                'action' => 'Point the canonical at the same scheme (preferably https://) the page is actually served on.',
            ];
        }

        $canonicalHost = (string) parse_url($canonical, PHP_URL_HOST);
        $finalHost = (string) parse_url($finalUrl, PHP_URL_HOST);
        $canonicalBare = preg_replace('/^www\./i', '', $canonicalHost);
        $finalBare = preg_replace('/^www\./i', '', $finalHost);

        if ($canonicalHost !== '' && $finalHost !== '' && strcasecmp($canonicalHost, $finalHost) !== 0) {
            if (strcasecmp($canonicalBare, $finalBare) === 0) {
                $checks[] = [
                    'status' => 'warning',
                    'label' => 'www / non-www Match',
                    'finding' => "The page is served from \"{$finalHost}\" but the canonical points to \"{$canonicalHost}\".",
                    'explanation' => 'www and non-www are technically different hosts to a browser, so this mismatch should be intentional (e.g. you\'ve chosen the www version as canonical) rather than an oversight.',
                    'action' => 'Confirm which version (www or non-www) you want indexed, and use it consistently.',
                ];
            } else {
                $checks[] = [
                    'status' => 'warning',
                    'label' => 'Canonical Target',
                    'finding' => "The canonical points to a completely different domain ({$canonicalHost}).",
                    'explanation' => 'This is sometimes intentional (e.g. syndicated content crediting an original source), but is worth double-checking.',
                    'action' => 'Confirm this is intentional. If not, update the canonical to point within your own domain.',
                ];
            }
        }

        return $checks;
    }

    private function normalize(string $url): string
    {
        $url = rtrim($url, '/');
        $url = preg_replace('#^https?://#i', '', $url);

        return strtolower((string) $url);
    }
}
