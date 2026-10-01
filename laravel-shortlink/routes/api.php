<?php

use App\Http\Controllers\LinkController;
use Illuminate\Support\Facades\Route;

// CORS is applied automatically to api/* routes (see config/cors.php / Laravel defaults).
// Called from the Blogger theme's Generate button.
Route::post('/shorten', [LinkController::class, 'store']);

// Bulk shortening from the homepage's "Bulk Shorten" tab. Throttled since it
// creates several links per request from an unauthenticated endpoint.
Route::post('/bulk-shorten', [LinkController::class, 'bulkStore'])->middleware('throttle:5,1');
