<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BioPageController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportAbuseController;
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
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])
        ->middleware('throttle:5,1')
        ->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::delete('/dashboard/{link}', [DashboardController::class, 'destroy'])->name('dashboard.destroy');
    Route::get('/dashboard/{link}/analytics', [DashboardController::class, 'analytics'])->name('dashboard.analytics');
    Route::get('/bio', [BioPageController::class, 'edit'])->name('bio.edit');
    Route::post('/bio', [BioPageController::class, 'update'])->name('bio.update');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Public link-in-bio page, e.g. klikwit.com/u/yourname.
Route::get('/u/{slug}', [BioPageController::class, 'show'])
    ->where('slug', '[A-Za-z0-9_-]{3,30}')
    ->name('bio.show');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])
    ->where('slug', '[A-Za-z0-9_-]{3,80}')
    ->name('blog.show');

Route::get('/tools', [UrlToolsController::class, 'indexPage'])->name('tools.index');
Route::get('/tools/expand', [UrlToolsController::class, 'expandPage'])->name('tools.expand');
Route::get('/tools/check', [UrlToolsController::class, 'checkPage'])->name('tools.check');
Route::get('/tools/utm-builder', [UrlToolsController::class, 'utmBuilderPage'])->name('tools.utm');

Route::get('/about', [PageController::class, 'about'])->name('pages.about');
Route::get('/contact', [PageController::class, 'contact'])->name('pages.contact');
Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('pages.privacy');
Route::get('/terms', [PageController::class, 'terms'])->name('pages.terms');
Route::get('/cookie-policy', [PageController::class, 'cookies'])->name('pages.cookies');
Route::get('/acceptable-use-policy', [PageController::class, 'acceptableUse'])->name('pages.aup');
Route::get('/dmca', [PageController::class, 'dmca'])->name('pages.dmca');

Route::get('/report-abuse', [ReportAbuseController::class, 'show'])->name('report-abuse');
Route::post('/report-abuse', [ReportAbuseController::class, 'submit'])
    ->middleware('throttle:5,1')
    ->name('report-abuse.submit');

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
    Route::post('/reports/{report}/disable-link', [AdminController::class, 'disableLink'])->name('admin.reports.disable');
    Route::post('/reports/{report}/dismiss', [AdminController::class, 'dismiss'])->name('admin.reports.dismiss');
});

// Order matters: specific routes (above, and /go/{code} below) must be
// registered before the catch-all {code} pattern, since words like
// "login" or "dashboard" would otherwise also match that pattern.
Route::get('/go/{code}', [LinkController::class, 'go'])
    ->where('code', '[A-Za-z0-9_-]{3,30}');

Route::post('/{code}/unlock', [LinkController::class, 'unlock'])
    ->where('code', '[A-Za-z0-9_-]{3,30}');

Route::get('/{code}', [LinkController::class, 'show'])
    ->where('code', '[A-Za-z0-9_-]{3,30}');
