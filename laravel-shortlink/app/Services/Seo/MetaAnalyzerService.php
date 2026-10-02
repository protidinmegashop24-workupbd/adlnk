<?php

namespace App\Services\Seo;

/**
 * Parses a page's <head> (and first <h1>) into a plain array. This is the
 * single place that reads raw HTML for the SEO tools — Canonical Checker
 * and Open Graph Checker both build on this instead of re-parsing the page
 * themselves, so there's one parser to keep correct rather than three.
 */
class MetaAnalyzerService
{
    /**
     * @return array{
     *   title: ?string,
     *   meta_description: ?string,
     *   canonical: ?string,
     *   robots_meta: ?string,
     *   viewport: ?string,
     *   lang: ?string,
     *   h1: ?string,
     *   h1_count: int,
     *   og_title: ?string,
     *   og_description: ?string,
     *   og_image: ?string,
     *   og_url: ?string,
     *   og_type: ?string,
     *   twitter_card: ?string,
     *   twitter_title: ?string,
     *   twitter_description: ?string,
     *   twitter_image: ?string,
     * }
     */
    public function parse(string $html): array
    {
        $doc = new \DOMDocument();

        libxml_use_internal_errors(true);
        // Prefix a meta charset so DOMDocument (which assumes ISO-8859-1 by
        // default) doesn't mangle UTF-8 page titles/descriptions.
        $doc->loadHTML('<?xml encoding="utf-8" ?>'.$html, LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();

        $xpath = new \DOMXPath($doc);

        $metaByName = fn (string $name): ?string => $this->firstAttr($xpath, "//meta[translate(@name,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')=\"{$name}\"]", 'content');
        $metaByProperty = fn (string $prop): ?string => $this->firstAttr($xpath, "//meta[@property=\"{$prop}\"]", 'content');

        $titleNode = $xpath->query('//title')->item(0);
        $h1Nodes = $xpath->query('//h1');
        $htmlNode = $xpath->query('//html')->item(0);

        return [
            'title' => $titleNode ? trim($titleNode->textContent) : null,
            'meta_description' => $metaByName('description'),
            'canonical' => $this->firstAttr($xpath, '//link[translate(@rel,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="canonical"]', 'href'),
            'robots_meta' => $metaByName('robots'),
            'viewport' => $metaByName('viewport'),
            'lang' => $htmlNode instanceof \DOMElement ? ($htmlNode->getAttribute('lang') ?: null) : null,
            'h1' => $h1Nodes->length > 0 ? trim($h1Nodes->item(0)->textContent) : null,
            'h1_count' => $h1Nodes->length,
            'og_title' => $metaByProperty('og:title'),
            'og_description' => $metaByProperty('og:description'),
            'og_image' => $metaByProperty('og:image'),
            'og_url' => $metaByProperty('og:url'),
            'og_type' => $metaByProperty('og:type'),
            'twitter_card' => $metaByName('twitter:card'),
            'twitter_title' => $metaByName('twitter:title'),
            'twitter_description' => $metaByName('twitter:description'),
            'twitter_image' => $metaByName('twitter:image'),
        ];
    }

    /**
     * The general PASS/WARNING/MISSING checklist shown by the Meta Tag
     * Checker tool. Title/description length guidance is deliberately
     * phrased as "can be cut off", never a hard character rule, since
     * actual display length varies by device and search engine.
     *
     * @param  array  $parsed  parse() output
     * @return array<int, array{status: string, label: string, finding: string, explanation: string, action: ?string}>
     */
    public function evaluate(array $parsed): array
    {
        $checks = [];

        $title = $parsed['title'];
        $titleLen = $title !== null ? mb_strlen($title) : 0;
        $checks[] = [
            'status' => $title === null ? 'missing' : ($titleLen > 60 || $titleLen < 10 ? 'warning' : 'pass'),
            'label' => 'Title Tag',
            'finding' => $title === null ? 'No <title> tag was found.' : "\"{$title}\" ({$titleLen} characters)",
            'explanation' => 'The title tag is usually the clickable headline shown in search results and browser tabs.',
            'action' => $title === null
                ? 'Add a unique, descriptive <title> tag.'
                : ($titleLen > 60 ? 'Longer titles are more likely to be cut off in some search results — consider shortening it.' : ($titleLen < 10 ? 'This title is very short — consider making it more descriptive.' : null)),
        ];

        $desc = $parsed['meta_description'];
        $descLen = $desc !== null ? mb_strlen($desc) : 0;
        $checks[] = [
            'status' => $desc === null ? 'missing' : ($descLen > 160 || $descLen < 50 ? 'warning' : 'pass'),
            'label' => 'Meta Description',
            'finding' => $desc === null ? 'No meta description was found.' : "\"{$desc}\" ({$descLen} characters)",
            'explanation' => 'A short summary search engines often (but not always) show beneath the title in results.',
            'action' => $desc === null
                ? 'Add a concise <meta name="description"> summarizing the page.'
                : ($descLen > 160 ? 'Longer descriptions are more likely to be truncated — consider tightening it.' : ($descLen < 50 ? 'This description is quite short — there may be room to say more about the page.' : null)),
        ];

        $checks[] = [
            'status' => $parsed['canonical'] === null ? 'warning' : 'pass',
            'label' => 'Canonical URL',
            'finding' => $parsed['canonical'] === null ? 'No canonical tag was found.' : "Points to: {$parsed['canonical']}",
            'explanation' => 'Tells search engines which URL is the authoritative version of this page.',
            'action' => $parsed['canonical'] === null ? 'Use the Canonical Checker tool for a deeper check, or add a <link rel="canonical"> tag.' : null,
        ];

        $robots = $parsed['robots_meta'];
        $blocksIndexing = $robots !== null && (stripos($robots, 'noindex') !== false);
        $checks[] = [
            'status' => $robots === null ? 'pass' : ($blocksIndexing ? 'warning' : 'pass'),
            'label' => 'Robots Meta Tag',
            'finding' => $robots === null ? 'No robots meta tag (defaults to indexable).' : "Found: \"{$robots}\"",
            'explanation' => 'Can instruct search engines not to index this page or not to follow its links.',
            'action' => $blocksIndexing ? 'This page is telling search engines not to index it — confirm that\'s intentional.' : null,
        ];

        $checks[] = [
            'status' => $parsed['viewport'] === null ? 'warning' : 'pass',
            'label' => 'Viewport Meta Tag',
            'finding' => $parsed['viewport'] === null ? 'No viewport meta tag was found.' : "Found: \"{$parsed['viewport']}\"",
            'explanation' => 'Tells mobile browsers how to scale the page. Missing it usually means a poor mobile experience.',
            'action' => $parsed['viewport'] === null ? 'Add <meta name="viewport" content="width=device-width, initial-scale=1">.' : null,
        ];

        $checks[] = [
            'status' => $parsed['lang'] === null ? 'warning' : 'pass',
            'label' => 'Language Attribute',
            'finding' => $parsed['lang'] === null ? 'No lang attribute was found on <html>.' : "Found: \"{$parsed['lang']}\"",
            'explanation' => 'Helps search engines and screen readers understand what language the page is written in.',
            'action' => $parsed['lang'] === null ? 'Add a lang attribute, e.g. <html lang="en">.' : null,
        ];

        $h1Count = $parsed['h1_count'];
        $checks[] = [
            'status' => $h1Count === 0 ? 'warning' : ($h1Count > 1 ? 'warning' : 'pass'),
            'label' => 'H1 Heading',
            'finding' => $h1Count === 0 ? 'No H1 heading was found.' : ($h1Count > 1 ? "Found {$h1Count} H1 headings." : "Found: \"{$parsed['h1']}\""),
            'explanation' => 'The main on-page heading, usually describing what the page is about.',
            'action' => $h1Count === 0
                ? 'Add a single, descriptive H1 heading.'
                : ($h1Count > 1 ? 'Most pages are clearer with a single H1 — consider using H2/H3 for the rest.' : null),
        ];

        $checks[] = [
            'status' => $parsed['og_title'] === null ? 'warning' : 'pass',
            'label' => 'Open Graph Title',
            'finding' => $parsed['og_title'] === null ? 'No og:title tag was found.' : "Found: \"{$parsed['og_title']}\"",
            'explanation' => 'Controls the title shown when this page is shared on social platforms.',
            'action' => $parsed['og_title'] === null ? 'Use the Open Graph Checker tool for a full check, or add <meta property="og:title">.' : null,
        ];

        $checks[] = [
            'status' => $parsed['og_image'] === null ? 'warning' : 'pass',
            'label' => 'Open Graph Image',
            'finding' => $parsed['og_image'] === null ? 'No og:image tag was found.' : 'Found an og:image tag.',
            'explanation' => 'The image shown when this page is shared on social platforms.',
            'action' => $parsed['og_image'] === null ? 'Add <meta property="og:image"> for a better-looking share preview.' : null,
        ];

        return $checks;
    }

    private function firstAttr(\DOMXPath $xpath, string $query, string $attr): ?string
    {
        $node = $xpath->query($query)->item(0);
        if (! $node instanceof \DOMElement) {
            return null;
        }

        $value = trim($node->getAttribute($attr));

        return $value !== '' ? $value : null;
    }
}
