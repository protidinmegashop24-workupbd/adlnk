<?php

use App\Http\Controllers\LinkController;
use Illuminate\Support\Facades\Route;

// CORS is applied automatically to api/* routes (see config/cors.php / Laravel defaults).
// Called from the Blogger theme's Generate button.
Route::post('/shorten', [LinkController::class, 'store']);
