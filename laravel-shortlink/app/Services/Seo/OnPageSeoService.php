<?php

namespace App\Services\Seo;

/**
 * Body-content checks for the On-Page SEO Checker: word count, heading
 * structure, image alt coverage, internal/external link counts, and
 * (optionally) whether a target keyword shows up in the places that
 * matter. Combined with MetaAnalyzerService::evaluate()'s head-level
 * checks, this gives one consolidated on-page report — everything here
 * comes straight from the page's own HTML, never a fabricated score.
 */
class OnPageSeoService
{
    /**
     * @param  array  $parsed  MetaAnalyzerService::parse() output, for title/h1/meta access
     * @return array<int, array{status: string, label: string, finding: string, explanation: string, action: ?string}>
     */
    public function check(string $html, string $finalUrl, array $parsed, ?string $targetKeyword = null): array
    {
        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="utf-8" ?>'.$html, LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();

        $xpath = new \DOMXPath($doc);
        $checks = [];

        // Body word count — strips <script>/<style> content first so it
        // doesn't inflate the count with code that was never meant to be read.
        foreach (iterator_to_array($xpath->query('//script|//style')) as $node) {
            $node->parentNode?->removeChild($node);
        }
        $bodyNode = $xpath->query('//body')->item(0);
        $bodyText = $bodyNode ? trim(preg_replace('/\s+/u', ' ', $bodyNode->textContent) ?? '') : '';
        $wordCount = $bodyText === '' ? 0 : str_word_count($bodyText);

        $checks[] = [
            'status' => $wordCount < 200 ? 'warning' : 'pass',
            'label' => 'Word Count',
            'finding' => "{$wordCount} words",
            'explanation' => 'Thin pages (very little text) tend to struggle to rank for competitive terms — there\'s less content for search engines or readers to find useful.',
            'action' => $wordCount < 200 ? 'Consider expanding this page with more useful, relevant content.' : null,
        ];

        // Heading structure outline (H1-H6 counts).
        $headingCounts = [];
        for ($level = 1; $level <= 6; $level++) {
            $headingCounts[$level] = $xpath->query("//h{$level}")->length;
        }
        $hasSubheadings = ($headingCounts[2] + $headingCounts[3]) > 0;
        $outline = collect($headingCounts)->filter(fn ($count) => $count > 0)
            ->map(fn ($count, $level) => "H{$level}: {$count}")
            ->implode(', ');
        $checks[] = [
            'status' => $wordCount > 300 && ! $hasSubheadings ? 'warning' : 'pass',
            'label' => 'Heading Structure',
            'finding' => $outline !== '' ? $outline : 'No headings found.',
            'explanation' => 'Subheadings (H2, H3) break up long content and help both readers and search engines understand its structure.',
            'action' => $wordCount > 300 && ! $hasSubheadings ? 'This is a longer page with no H2/H3 subheadings — consider breaking it into sections.' : null,
        ];

        // Image alt text coverage.
        $images = $xpath->query('//img');
        $totalImages = $images->length;
        $missingAlt = 0;
        foreach ($images as $img) {
            if (! $img instanceof \DOMElement || trim($img->getAttribute('alt')) === '') {
                $missingAlt++;
            }
        }
        $checks[] = [
            'status' => $totalImages === 0 ? 'pass' : ($missingAlt > 0 ? 'warning' : 'pass'),
            'label' => 'Image Alt Text',
            'finding' => $totalImages === 0 ? 'No images found on this page.' : "{$missingAlt} of {$totalImages} images missing alt text.",
            'explanation' => 'Alt text describes images for screen readers and for search engines, which can\'t "see" images directly.',
            'action' => $missingAlt > 0 ? 'Add descriptive alt text to the images missing it.' : null,
        ];

        // Internal vs. external link counts.
        $finalHost = parse_url($finalUrl, PHP_URL_HOST);
        $internal = 0;
        $external = 0;
        foreach ($xpath->query('//a[@href]') as $a) {
            if (! $a instanceof \DOMElement) {
                continue;
            }
            $href = trim($a->getAttribute('href'));
            if ($href === '' || str_starts_with($href, '#') || str_starts_with($href, 'mailto:') || str_starts_with($href, 'tel:') || str_starts_with($href, 'javascript:')) {
                continue;
            }
            $linkHost = parse_url($href, PHP_URL_HOST);
            if ($linkHost === null || ($finalHost !== null && strcasecmp($linkHost, $finalHost) === 0)) {
                $internal++;
            } else {
                $external++;
            }
        }
        $checks[] = [
            'status' => $internal === 0 ? 'warning' : 'pass',
            'label' => 'Internal & External Links',
            'finding' => "{$internal} internal, {$external} external links.",
            'explanation' => 'Internal links help search engines discover other pages on your site; external links to relevant sources can add credibility.',
            'action' => $internal === 0 ? 'Consider linking to other relevant pages on your own site.' : null,
        ];

        // Optional target keyword usage.
        if ($targetKeyword !== null && trim($targetKeyword) !== '') {
            $keyword = mb_strtolower(trim($targetKeyword));
            $inTitle = $parsed['title'] !== null && str_contains(mb_strtolower($parsed['title']), $keyword);
            $inDescription = $parsed['meta_description'] !== null && str_contains(mb_strtolower($parsed['meta_description']), $keyword);
            $inH1 = $parsed['h1'] !== null && str_contains(mb_strtolower($parsed['h1']), $keyword);
            $inBody = str_contains(mb_strtolower($bodyText), $keyword);

            $foundIn = array_filter([
                $inTitle ? 'title' : null,
                $inH1 ? 'H1' : null,
                $inDescription ? 'meta description' : null,
                $inBody ? 'body text' : null,
            ]);

            $checks[] = [
                'status' => $foundIn === [] ? 'missing' : ($inTitle && $inH1 ? 'pass' : 'warning'),
                'label' => 'Target Keyword Usage: "'.$targetKeyword.'"',
                'finding' => $foundIn === [] ? 'Not found in title, H1, meta description, or body text.' : 'Found in: '.implode(', ', $foundIn).'.',
                'explanation' => 'Checks whether your target keyword appears in the places that most help readers and search engines understand what the page is about.',
                'action' => $foundIn === []
                    ? 'Consider including this keyword naturally in the title, heading, or content, if relevant.'
                    : (! $inTitle || ! $inH1 ? 'Consider including the keyword in the title and H1 heading if it fits naturally.' : null),
            ];
        }

        return $checks;
    }
}
