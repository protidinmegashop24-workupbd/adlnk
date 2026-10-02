<?php

namespace App\Services\Seo;

/**
 * Turns parsed <head> data into the specific checks the Open Graph Checker
 * reports, covering both Open Graph (Facebook/LinkedIn-style) and
 * Twitter/X Card tags, plus a best-effort "what a share would likely show"
 * preview built from whichever tags are actually present.
 */
class OpenGraphService
{
    /**
     * @param  array  $parsed  MetaAnalyzerService::parse() output
     * @return array<int, array{status: string, label: string, finding: string, explanation: string, action: ?string}>
     */
    public function check(array $parsed): array
    {
        $checks = [];

        $checks[] = $this->tagCheck(
            $parsed['og_title'],
            'og:title',
            'Controls the title shown when this page is shared on platforms that read Open Graph tags.',
            'Add <meta property="og:title" content="..."> with a clear, specific title for this page.'
        );

        $checks[] = $this->tagCheck(
            $parsed['og_description'],
            'og:description',
            'A short summary shown alongside the title on most share previews.',
            'Add <meta property="og:description" content="..."> summarizing the page in one or two sentences.'
        );

        $checks[] = $this->tagCheck(
            $parsed['og_image'],
            'og:image',
            'The image shown in the share preview. Pages without one often render as plain text links.',
            'Add <meta property="og:image" content="https://..."> pointing to an image at least 1200×630px.'
        );

        $checks[] = $this->tagCheck(
            $parsed['og_url'],
            'og:url',
            'Declares the canonical URL for this specific piece of shared content.',
            'Add <meta property="og:url" content="..."> with this page\'s full URL.'
        );

        $checks[] = $this->tagCheck(
            $parsed['og_type'],
            'og:type',
            'Tells platforms what kind of content this is (e.g. "website" or "article").',
            'Add <meta property="og:type" content="website"> (or a more specific type if applicable).'
        );

        $checks[] = $this->tagCheck(
            $parsed['twitter_card'],
            'twitter:card',
            'Chooses the Twitter/X card layout (e.g. "summary" or "summary_large_image").',
            'Add <meta name="twitter:card" content="summary_large_image">.'
        );

        $hasTwitterTitle = $parsed['twitter_title'] !== null;
        $hasOgTitle = $parsed['og_title'] !== null;
        $checks[] = [
            'status' => $hasTwitterTitle ? 'pass' : ($hasOgTitle ? 'warning' : 'missing'),
            'label' => 'twitter:title',
            'finding' => $hasTwitterTitle
                ? "Found: \"{$parsed['twitter_title']}\""
                : ($hasOgTitle ? 'Not set, but Twitter/X will fall back to og:title when present.' : 'Not set, and no og:title to fall back to either.'),
            'explanation' => 'Twitter/X reads its own tags first, falling back to Open Graph tags when a Twitter-specific one is missing.',
            'action' => $hasTwitterTitle ? null : ($hasOgTitle ? null : 'Add og:title (or a dedicated twitter:title) so shares on X have a title.'),
        ];

        $hasTwitterDesc = $parsed['twitter_description'] !== null;
        $hasOgDesc = $parsed['og_description'] !== null;
        $checks[] = [
            'status' => $hasTwitterDesc ? 'pass' : ($hasOgDesc ? 'warning' : 'missing'),
            'label' => 'twitter:description',
            'finding' => $hasTwitterDesc
                ? "Found: \"{$parsed['twitter_description']}\""
                : ($hasOgDesc ? 'Not set, but Twitter/X will fall back to og:description when present.' : 'Not set, and no og:description to fall back to either.'),
            'explanation' => 'Same fallback behavior as twitter:title.',
            'action' => $hasTwitterDesc ? null : ($hasOgDesc ? null : 'Add og:description (or a dedicated twitter:description).'),
        ];

        $hasTwitterImage = $parsed['twitter_image'] !== null;
        $hasOgImage = $parsed['og_image'] !== null;
        $checks[] = [
            'status' => $hasTwitterImage ? 'pass' : ($hasOgImage ? 'warning' : 'missing'),
            'label' => 'twitter:image',
            'finding' => $hasTwitterImage
                ? 'Found a dedicated Twitter/X image.'
                : ($hasOgImage ? 'Not set, but Twitter/X will fall back to og:image when present.' : 'Not set, and no og:image to fall back to either.'),
            'explanation' => 'Same fallback behavior as the other Twitter tags.',
            'action' => $hasTwitterImage ? null : ($hasOgImage ? null : 'Add og:image (or a dedicated twitter:image).'),
        ];

        return $checks;
    }

    /**
     * Best-effort "what would likely show" preview, preferring the
     * platform-specific tag and falling back through Open Graph to the
     * page's own <title>/meta description.
     *
     * @param  array  $parsed  MetaAnalyzerService::parse() output
     * @return array{title: ?string, description: ?string, image: ?string}
     */
    public function previewData(array $parsed): array
    {
        return [
            'title' => $parsed['og_title'] ?? $parsed['twitter_title'] ?? $parsed['title'],
            'description' => $parsed['og_description'] ?? $parsed['twitter_description'] ?? $parsed['meta_description'],
            'image' => $parsed['og_image'] ?? $parsed['twitter_image'],
        ];
    }

    private function tagCheck(?string $value, string $tagName, string $explanation, string $action): array
    {
        return [
            'status' => $value !== null ? 'pass' : 'missing',
            'label' => $tagName,
            'finding' => $value !== null ? "Found: \"{$value}\"" : "No {$tagName} tag was found.",
            'explanation' => $explanation,
            'action' => $value !== null ? null : $action,
        ];
    }
}
