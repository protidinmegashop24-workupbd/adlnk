<?php

/**
 * One-time helper to grant admin access (needed to review Abuse Reports),
 * for hosts without SSH/artisan access.
 *
 * 1. Open your .env file and add a line:  ADMIN_SETUP_TOKEN=some-long-random-string
 * 2. Visit: https://yourdomain.com/make-admin.php?email=you@example.com&token=that-same-string&run=1
 * 3. Delete this file once it says success.
 */

if (($_GET['run'] ?? '') !== '1') {
    http_response_code(400);
    echo 'Add ?email=you@example.com&token=YOUR_ADMIN_SETUP_TOKEN&run=1 to the URL.';
    exit;
}

require __DIR__.'/../vendor/autoload.php';

/** @var \Illuminate\Foundation\Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

header('Content-Type: text/plain');

$configuredToken = env('ADMIN_SETUP_TOKEN');

if (! $configuredToken) {
    http_response_code(400);
    echo "Add ADMIN_SETUP_TOKEN=some-long-random-string to your .env file first, then try again.\n";
    exit;
}

$givenToken = $_GET['token'] ?? '';

if (! hash_equals((string) $configuredToken, (string) $givenToken)) {
    http_response_code(403);
    echo "Token does not match ADMIN_SETUP_TOKEN in your .env file.\n";
    exit;
}

$email = trim((string) ($_GET['email'] ?? ''));

if ($email === '') {
    http_response_code(400);
    echo "Add &email=you@example.com to the URL.\n";
    exit;
}

$user = \App\Models\User::where('email', $email)->first();

if (! $user) {
    http_response_code(404);
    echo "No account found with email: {$email}\n";
    echo "Make sure you've registered an account with this email first.\n";
    exit;
}

$user->is_admin = true;
$user->save();

echo "Success: {$email} is now an admin.\n";
echo "You can now sign in and visit /admin/reports.\n";
echo "Delete this file (make-admin.php) now for security.\n";
