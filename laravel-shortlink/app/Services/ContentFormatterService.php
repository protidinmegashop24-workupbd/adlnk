<?php

namespace App\Services;

/**
 * Non-technical-friendly body formatter, shared by every admin content
 * editor (blog posts, static pages). The admin explicitly picks a mode
 * per post/page:
 *   - 'text': always treated as plain words. A small set of easy,
 *     Markdown-like shortcuts are converted so they never need to write
 *     actual HTML: ## Heading / ### Smaller Heading, - bullet (every line
 *     in the block starting with - or *), 1. numbered (every line
 *     starting with a number + "."), **bold**, [link text](url).
 *     Anything that looks like an HTML tag is escaped and shown as
 *     literal text instead of being interpreted — this is what keeps
 *     content copy-pasted from other pages/documents from breaking the
 *     layout.
 *   - 'html': trusted as raw HTML as-is (needed for embedding videos,
 *     etc.) — this is single-admin-authored content.
 */
class ContentFormatterService
{
    public function format(string $raw, string $mode = 'auto'): string
    {
        if ($mode === 'html') {
            return $raw;
        }

        if ($mode === 'text') {
            return $this->formatPlainText($raw);
        }

        // Legacy auto-detect, kept only for any caller that doesn't pass an
        // explicit mode: looks for an actual HTML tag shape (<tag ...> or
        // </tag>), not just any "<" character — plain prose like "5 < 10"
        // must not accidentally flip the whole body into the raw-HTML path.
        if (preg_match('/<\/?[a-zA-Z][a-zA-Z0-9]*(\s[^>]*)?>/', $raw)) {
            return $raw;
        }

        return $this->formatPlainText($raw);
    }

    private function formatPlainText(string $raw): string
    {
        $blocks = preg_split('/\n\s*\n/', trim($raw));

        return collect($blocks)
            ->map(fn ($b) => trim($b))
            ->filter(fn ($b) => $b !== '')
            ->map(fn ($b) => $this->formatBlock($b))
            ->implode("\n");
    }

    private function formatBlock(string $block): string
    {
        if (preg_match('/^###\s+(.+)$/s', $block, $m)) {
            return '<h3>'.$this->inlineFormat($m[1]).'</h3>';
        }
        if (preg_match('/^##\s+(.+)$/s', $block, $m)) {
            return '<h2>'.$this->inlineFormat($m[1]).'</h2>';
        }

        $lines = preg_split('/\n/', $block);

        if (collect($lines)->every(fn ($l) => preg_match('/^[-*]\s+/', trim($l)))) {
            $items = collect($lines)
                ->map(fn ($l) => '<li>'.$this->inlineFormat(preg_replace('/^[-*]\s+/', '', trim($l))).'</li>')
                ->implode('');

            return '<ul>'.$items.'</ul>';
        }

        if (collect($lines)->every(fn ($l) => preg_match('/^\d+\.\s+/', trim($l)))) {
            $items = collect($lines)
                ->map(fn ($l) => '<li>'.$this->inlineFormat(preg_replace('/^\d+\.\s+/', '', trim($l))).'</li>')
                ->implode('');

            return '<ol>'.$items.'</ol>';
        }

        return '<p>'.nl2br($this->inlineFormat($block)).'</p>';
    }

    /**
     * Escapes the text first (so any literal HTML-looking characters the
     * admin typed are neutralized), then layers **bold** and [text](url)
     * on top of the already-escaped string. Links are restricted to
     * http(s):// or site-relative paths — never javascript:/data: etc.
     */
    private function inlineFormat(string $text): string
    {
        $escaped = e($text);
        $escaped = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $escaped);

        return preg_replace_callback('/\[([^\]]+)\]\(([^)]+)\)/', function ($m) {
            $url = trim($m[2]);
            if (! preg_match('#^(https?://|/)#i', $url)) {
                return $m[0];
            }

            return '<a href="'.$url.'">'.$m[1].'</a>';
        }, $escaped);
    }
}
