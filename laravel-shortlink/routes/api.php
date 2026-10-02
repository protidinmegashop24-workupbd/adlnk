<?php

use App\Http\Controllers\LinkController;
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
    Route::post('/shorten', [LinkController::class, 'store']);

    // Bulk shortening from the homepage's "Bulk Shorten" tab. Throttled since it
    // creates several links per request from an unauthenticated endpoint.
    Route::post('/bulk-shorten', [LinkController::class, 'bulkStore'])->middleware('throttle:5,1');
});

// URL Expander / Link Checker: each call makes an outbound HTTP request on
// the server's behalf, so these are throttled harder than the shortener.
Route::post('/expand', [UrlToolsController::class, 'expand'])->middleware('throttle:15,1');
Route::post('/check', [UrlToolsController::class, 'check'])->middleware('throttle:15,1');
