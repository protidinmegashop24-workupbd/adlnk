<?php

use App\Http\Controllers\LinkController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Order matters: specific routes before the catch-all {code} pattern below.
// Pattern allows both random codes and custom aliases (3-30 chars, - and _ allowed).
Route::get('/go/{code}', [LinkController::class, 'go'])
    ->where('code', '[A-Za-z0-9_-]{3,30}');

Route::get('/{code}', [LinkController::class, 'show'])
    ->where('code', '[A-Za-z0-9_-]{3,30}');
