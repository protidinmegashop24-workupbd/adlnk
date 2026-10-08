<?php

namespace App\Services;

/**
 * Generates YouTube title ideas from a topic using fixed text templates —
 * deliberately not an AI/LLM call. These are formula-based suggestions
 * (the same "fill in the blank" approach long-standing free title-idea
 * tools use), labeled as such rather than presented as AI-written, so
 * nothing here claims to be smarter than it is.
 */
class YoutubeTitleIdeaService
{
    private const TEMPLATES = [
        'How to {topic} (Step by Step)',
        '{topic}: Everything You Need to Know',
        'Top 10 {topic} Tips for Beginners',
        'The Ultimate Guide to {topic}',
        '{topic} Explained in 5 Minutes',
        'I Tried {topic} for 30 Days — Here\'s What Happened',
        'Why {topic} Is More Important Than You Think',
        '{topic} Mistakes Everyone Makes (And How to Fix Them)',
        'What Nobody Tells You About {topic}',
        '{topic} for Beginners: A Complete Walkthrough',
        '5 {topic} Hacks That Actually Work',
        'Is {topic} Worth It This Year?',
    ];

    /**
     * @return array<int, string>
     */
    public function generate(string $topic): array
    {
        $topic = trim($topic);

        if ($topic === '') {
            return [];
        }

        return array_map(
            fn (string $template) => str_replace('{topic}', $topic, $template),
            self::TEMPLATES
        );
    }
}
