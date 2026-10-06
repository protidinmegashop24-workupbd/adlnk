<?php

use App\Http\Controllers\LinkController;
use App\Http\Controllers\SeoToolsController;
use App\Http\Controllers\UrlToolsController;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;

// CORS is applied automatically to api/* routes (see config/cors.php / Laravel defaults).
// Called from the Blogger theme's Generate button, and from the homepage itself.
// These routes stay CSRF-exempt (so cross-origin callers like Blogger keep working),
// but still start the session so a logged-in homepage visitor's links are
// attributed to their account for the dashboard.
Route::middleware([EncryptCookies::class, StartSession::class])->group(function () {
    Route::post('/shorten', [LinkController::class, 'store'])->middleware('throttle:20,1,shorten');

    // Bulk shortening from the homepage's "Bulk Shorten" tab. Throttled tighter since it
    // creates several links per request from an unauthenticated endpoint.
    Route::post('/bulk-shorten', [LinkController::class, 'bulkStore'])->middleware('throttle:5,1,bulk-shorten');
});

// URL Expander / Link Checker: each call makes an outbound HTTP request on
// the server's behalf, so these are throttled harder than the shortener.
// Each gets its own prefix — the throttle middleware's default signature is
// shared per IP across every route unless given a distinct one, so without
// this, hitting one of these endpoints would eat into the other's budget.
Route::post('/expand', [UrlToolsController::class, 'expand'])->middleware('throttle:15,1,expand');
Route::post('/check', [UrlToolsController::class, 'check'])->middleware('throttle:15,1,check');

// SEO Tools (Phase 2) that analyze another site's HTML — same SSRF-safe
// fetch path as expand/check above, each with its own throttle bucket.
Route::post('/seo/meta-tag-checker', [SeoToolsController::class, 'metaTagCheckerAnalyze'])->middleware('throttle:15,1,seo-meta');
Route::post('/seo/canonical-checker', [SeoToolsController::class, 'canonicalCheckerAnalyze'])->middleware('throttle:15,1,seo-canonical');
Route::post('/seo/open-graph-checker', [SeoToolsController::class, 'openGraphCheckerAnalyze'])->middleware('throttle:15,1,seo-og');

// Keyword Suggestions fires several outbound requests per submission (one
// per modifier), so it gets a tighter budget than the single-fetch tools above.
Route::post('/seo/keyword-suggestions', [SeoToolsController::class, 'keywordSuggestionsAnalyze'])->middleware('throttle:8,1,seo-keyword-suggestions');
Route::post('/seo/on-page-checker', [SeoToolsController::class, 'onPageSeoCheckerAnalyze'])->middleware('throttle:15,1,seo-on-page');
