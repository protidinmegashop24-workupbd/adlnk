<?php

namespace App\Http\Controllers;

use App\Services\Seo\CanonicalService;
use App\Services\Seo\MetaAnalyzerService;
use App\Services\Seo\OpenGraphService;
use App\Services\SafeUrlResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SeoToolsController extends Controller
{
    public function __construct(
        private readonly SafeUrlResolver $resolver,
        private readonly MetaAnalyzerService $metaAnalyzer,
        private readonly CanonicalService $canonicalService,
        private readonly OpenGraphService $openGraphService,
    ) {}

    public function hub(): View
    {
        return view('seo-tools.index');
    }

    public function metaTagCheckerPage(): View
    {
        return view('seo-tools.meta-tag-checker');
    }

    public function serpPreviewPage(): View
    {
        return view('seo-tools.serp-preview');
    }

    public function keywordDensityPage(): View
    {
        return view('seo-tools.keyword-density-checker');
    }

    public function wordCounterPage(): View
    {
        return view('seo-tools.word-counter');
    }

    public function seoUrlCheckerPage(): View
    {
        return view('seo-tools.seo-url-checker');
    }

    public function slugGeneratorPage(): View
    {
        return view('seo-tools.slug-generator');
    }

    public function robotsGeneratorPage(): View
    {
        return view('seo-tools.robots-txt-generator');
    }

    public function sitemapGeneratorPage(): View
    {
        return view('seo-tools.xml-sitemap-generator');
    }

    public function canonicalCheckerPage(): View
    {
        return view('seo-tools.canonical-checker');
    }

    public function openGraphCheckerPage(): View
    {
        return view('seo-tools.open-graph-checker');
    }

    public function metaTagCheckerAnalyze(Request $request): JsonResponse
    {
        $data = $request->validate(['url' => ['required', 'string', 'max:2048']]);

        $fetch = $this->resolver->fetchHtml(trim($data['url']));
        if ($fetch['error']) {
            return response()->json(['error' => $fetch['error']], 422);
        }

        $parsed = $this->metaAnalyzer->parse($fetch['html']);

        return response()->json([
            'final_url' => $fetch['final_url'],
            'checks' => $this->metaAnalyzer->evaluate($parsed),
        ]);
    }

    public function canonicalCheckerAnalyze(Request $request): JsonResponse
    {
        $data = $request->validate(['url' => ['required', 'string', 'max:2048']]);
        $requestedUrl = trim($data['url']);

        $fetch = $this->resolver->fetchHtml($requestedUrl);
        if ($fetch['error']) {
            return response()->json(['error' => $fetch['error']], 422);
        }

        $parsed = $this->metaAnalyzer->parse($fetch['html']);

        return response()->json([
            'final_url' => $fetch['final_url'],
            'checks' => $this->canonicalService->check($requestedUrl, $fetch['final_url'], $parsed),
        ]);
    }

    public function openGraphCheckerAnalyze(Request $request): JsonResponse
    {
        $data = $request->validate(['url' => ['required', 'string', 'max:2048']]);

        $fetch = $this->resolver->fetchHtml(trim($data['url']));
        if ($fetch['error']) {
            return response()->json(['error' => $fetch['error']], 422);
        }

        $parsed = $this->metaAnalyzer->parse($fetch['html']);

        return response()->json([
            'final_url' => $fetch['final_url'],
            'checks' => $this->openGraphService->check($parsed),
            'preview' => $this->openGraphService->previewData($parsed),
        ]);
    }
}
