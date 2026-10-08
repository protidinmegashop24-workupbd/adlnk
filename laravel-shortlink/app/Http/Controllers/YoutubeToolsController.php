<?php

namespace App\Http\Controllers;

use App\Services\Seo\KeywordSuggestionService;
use App\Services\YoutubeTitleIdeaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class YoutubeToolsController extends Controller
{
    public function __construct(
        private readonly KeywordSuggestionService $keywordSuggestionService,
        private readonly YoutubeTitleIdeaService $titleIdeaService,
    ) {}

    public function page(): View
    {
        return view('tools.youtube-tag-generator');
    }

    public function generate(Request $request): JsonResponse
    {
        $data = $request->validate(['topic' => ['required', 'string', 'max:80']]);
        $topic = trim($data['topic']);

        // Title ideas are pure templates (no network call), so they're
        // generated regardless of whether the tag fetch below succeeds —
        // a suggestion-API hiccup shouldn't hide the part that can't fail.
        $titles = $this->titleIdeaService->generate($topic);
        $tagResult = $this->keywordSuggestionService->suggest($topic, 'yt');

        // Flatten the grouped suggestions into a single deduped tag list —
        // for YouTube tags, unlike the general Keyword Suggestions tool,
        // one flat list to copy is more useful than grouped sections.
        $tags = [];
        $seen = [];
        foreach ($tagResult['groups'] as $group) {
            foreach ($group as $tag) {
                $key = mb_strtolower($tag);
                if (! isset($seen[$key])) {
                    $seen[$key] = true;
                    $tags[] = $tag;
                }
            }
        }

        return response()->json([
            'topic' => $topic,
            'tags' => $tags,
            'tags_error' => $tagResult['error'],
            'titles' => $titles,
        ]);
    }
}
