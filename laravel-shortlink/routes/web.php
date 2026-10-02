<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BioPageController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\UrlToolsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::delete('/dashboard/{link}', [DashboardController::class, 'destroy'])->name('dashboard.destroy');
    Route::get('/dashboard/{link}/analytics', [DashboardController::class, 'analytics'])->name('dashboard.analytics');
    Route::get('/bio', [BioPageController::class, 'edit'])->name('bio.edit');
    Route::post('/bio', [BioPageController::class, 'update'])->name('bio.update');
});

// Public link-in-bio page, e.g. klikwit.com/u/yourname.
Route::get('/u/{slug}', [BioPageController::class, 'show'])
    ->where('slug', '[A-Za-z0-9_-]{3,30}')
    ->name('bio.show');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])
    ->where('slug', '[A-Za-z0-9_-]{3,80}')
    ->name('blog.show');

Route::get('/tools/expand', [UrlToolsController::class, 'expandPage'])->name('tools.expand');
Route::get('/tools/check', [UrlToolsController::class, 'checkPage'])->name('tools.check');
Route::get('/tools/utm-builder', [UrlToolsController::class, 'utmBuilderPage'])->name('tools.utm');

// Order matters: specific routes (above, and /go/{code} below) must be
// registered before the catch-all {code} pattern, since words like
// "login" or "dashboard" would otherwise also match that pattern.
Route::get('/go/{code}', [LinkController::class, 'go'])
    ->where('code', '[A-Za-z0-9_-]{3,30}');

Route::get('/{code}', [LinkController::class, 'show'])
    ->where('code', '[A-Za-z0-9_-]{3,30}');
