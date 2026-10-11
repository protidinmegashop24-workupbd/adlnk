<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Generates an original first-draft blog post from a topic + optional notes,
 * via the Anthropic API — not a rewrite/spin of any existing article, since
 * republishing reworded copy is the kind of "scaled content abuse" Google's
 * spam policies penalize an entire site for. Output always needs a human
 * admin to read, fact-check, and edit before publishing (the form this
 * feeds enforces that — it never auto-publishes).
 *
 * Requires ANTHROPIC_API_KEY in .env. Unlike the optional SEO-check
 * integrations elsewhere in this app, this doesn't fail open: it's a
 * user-triggered action with nothing useful to fall back to, so a missing
 * key or API error is reported back to the admin instead of being silently
 * swallowed.
 */
class AiContentGeneratorService
{
    private const ENDPOINT = 'https://api.anthropic.com/v1/messages';

    private const MODEL = 'claude-sonnet-5-5';

    private const TIMEOUT_SECONDS = 60;

    public function generate(string $topic, ?string $notes, string $language): array
    {
        $apiKey = config('services.anthropic.key');

        if (! $apiKey) {
            throw new RuntimeException('AI লেখা তৈরির ফিচারটা এখনো চালু করা হয়নি — সার্ভারে ANTHROPIC_API_KEY সেট করা নেই।');
        }

        $languageLabel = $language === 'bn' ? 'Bengali (Bangla)' : 'English';

        $prompt = <<<PROMPT
            You are writing an original first-draft blog post for klikwit, a free link-shortening and link-in-bio SaaS. The audience is ordinary small-business owners and creators, not developers — keep it practical and easy to follow, not generic or keyword-stuffed, and never robotic-sounding.

            Topic: {$topic}
            {$this->notesLine($notes)}
            Write entirely in {$languageLabel}.

            Formatting rules (plain text only, no raw HTML):
            - Leave one blank line between paragraphs.
            - Use "## " at the start of a line for a section heading.
            - Use "**text**" for bold.
            - Use "- " at the start of a line for a bullet list.
            - Do not invent or include any links/URLs.
            - Do not copy or closely paraphrase any specific existing article — write original wording based on general knowledge of the topic.

            Respond with ONLY a single raw JSON object (no markdown code fence, no commentary before or after) with exactly these keys:
            {"title": "a concise, specific, SEO-friendly title under 60 characters", "excerpt": "a 1-2 sentence summary under 160 characters", "body": "the full post body using the formatting rules above"}
            PROMPT;

        try {
            $response = Http::timeout(self::TIMEOUT_SECONDS)
                ->withHeaders([
                    'x-api-key' => $apiKey,
                    'anthropic-version' => '2023-06-01',
                ])
                ->post(self::ENDPOINT, [
                    'model' => self::MODEL,
                    'max_tokens' => 2048,
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ]);
        } catch (\Throwable $e) {
            throw new RuntimeException('AI সার্ভিসে সংযোগ করা যায়নি। একটু পর আবার চেষ্টা করুন।');
        }

        if (! $response->successful()) {
            throw new RuntimeException('AI লেখা তৈরি করতে ব্যর্থ হয়েছে (সার্ভার জবাব: '.$response->status().')। একটু পর আবার চেষ্টা করুন।');
        }

        $text = $response->json('content.0.text');

        if (! $text) {
            throw new RuntimeException('AI থেকে কোনো লেখা পাওয়া যায়নি। আবার চেষ্টা করুন।');
        }

        $parsed = json_decode(trim($text), true);

        if (! is_array($parsed) || empty($parsed['title']) || empty($parsed['excerpt']) || empty($parsed['body'])) {
            throw new RuntimeException('AI-এর জবাব ঠিকভাবে পড়া যায়নি। আবার চেষ্টা করুন।');
        }

        return [
            'title' => (string) $parsed['title'],
            'excerpt' => (string) $parsed['excerpt'],
            'body' => (string) $parsed['body'],
        ];
    }

    private function notesLine(?string $notes): string
    {
        $notes = trim((string) $notes);

        return $notes !== '' ? "Make sure to cover these specific points: {$notes}" : '';
    }
}
