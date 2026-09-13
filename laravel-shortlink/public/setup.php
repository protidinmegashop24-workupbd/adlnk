<?php

/**
 * One-time setup helper for hosts without SSH/Terminal access.
 *
 * Visit https://yourdomain.com/setup.php?run=1 once, after creating your
 * MySQL database and filling in .env (see README-CPANEL-BN.md). It will:
 *   1. Generate APP_KEY if it is still empty.
 *   2. Run the database migrations.
 *
 * IMPORTANT: delete this file (or at least rename it) once setup succeeds.
 * It is safe to leave visiting it again (it won't drop any data — it only
 * ever runs the non-destructive `migrate` command), but there is no reason
 * to leave a setup endpoint reachable by anyone who finds the URL.
 */

if (($_GET['run'] ?? '') !== '1') {
    http_response_code(400);
    echo 'Add ?run=1 to the URL to run setup, e.g. setup.php?run=1';
    exit;
}

require __DIR__.'/../vendor/autoload.php';

/** @var \Illuminate\Foundation\Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$steps = [];
$ok = true;

function adlnk_step(array &$steps, string $label, callable $fn): void
{
    try {
        $result = $fn();
        $steps[] = ['label' => $label, 'ok' => true, 'detail' => $result ?? ''];
    } catch (\Throwable $e) {
        $steps[] = ['label' => $label, 'ok' => false, 'detail' => $e->getMessage()];
        throw $e;
    }
}

try {
    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    // 1. Check DB connection first, with a clear error if it fails.
    adlnk_step($steps, 'ডাটাবেস সংযোগ পরীক্ষা', function () {
        \Illuminate\Support\Facades\DB::connection()->getPdo();

        return 'সংযোগ সফল হয়েছে।';
    });

    // 2. Generate APP_KEY if missing.
    adlnk_step($steps, 'APP_KEY তৈরি', function () use ($app) {
        $envPath = $app->environmentFilePath();
        $env = file_get_contents($envPath);

        if (preg_match('/^APP_KEY=(.*)$/m', $env, $m) && trim($m[1]) !== '') {
            return 'আগে থেকেই সেট করা আছে, বাদ দেওয়া হলো।';
        }

        $key = 'base64:'.base64_encode(random_bytes(32));
        $env = preg_match('/^APP_KEY=.*$/m', $env)
            ? preg_replace('/^APP_KEY=.*$/m', 'APP_KEY='.$key, $env, 1)
            : $env."\nAPP_KEY={$key}\n";
        file_put_contents($envPath, $env);

        return 'নতুন APP_KEY তৈরি করে .env-এ লেখা হয়েছে।';
    });

    // 3. Run migrations.
    adlnk_step($steps, 'ডাটাবেস মাইগ্রেশন', function () use ($kernel) {
        $kernel->call('migrate', ['--force' => true]);

        return nl2br(e($kernel->output()));
    });
} catch (\Throwable $e) {
    $ok = false;
}

?><!DOCTYPE html>
<html lang="bn">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="robots" content="noindex,nofollow"/>
<title>adlnk সেটআপ</title>
<style>
  body{font-family:Arial,Helvetica,sans-serif;background:#f4f6f8;margin:0;padding:24px;color:#222}
  .wrap{max-width:640px;margin:20px auto;background:#fff;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,.1);padding:24px}
  .step{padding:10px 0;border-bottom:1px solid #eee}
  .ok{color:#0a7d27}
  .fail{color:#c0392b}
  .banner{padding:14px;border-radius:6px;margin-bottom:16px;font-weight:bold}
  .banner.ok{background:#e6f7ea;color:#0a7d27}
  .banner.fail{background:#fdecea;color:#c0392b}
  code{background:#f1f1f1;padding:2px 6px;border-radius:4px}
</style>
</head>
<body>
<div class="wrap">
  <?php if ($ok): ?>
    <div class="banner ok">✅ সেটআপ সফল হয়েছে! এখন Blogger থিমের SHORTENER_API-তে আপনার ডোমেইন বসান।</div>
  <?php else: ?>
    <div class="banner fail">❌ সেটআপ শেষ পর্যন্ত সম্পন্ন হয়নি — নিচে কোন ধাপে আটকেছে দেখুন।</div>
  <?php endif; ?>

  <?php foreach ($steps as $s): ?>
    <div class="step">
      <strong class="<?= $s['ok'] ? 'ok' : 'fail' ?>"><?= $s['ok'] ? '✔' : '✘' ?> <?= htmlspecialchars($s['label']) ?></strong>
      <div><?= $s['detail'] ?></div>
    </div>
  <?php endforeach; ?>

  <p style="margin-top:20px;color:#666;font-size:14px;">
    ডাটাবেস সংযোগ ব্যর্থ হলে, <code>.env</code> ফাইলে <code>DB_DATABASE</code>,
    <code>DB_USERNAME</code>, <code>DB_PASSWORD</code> ঠিক আছে কিনা যাচাই করুন
    (এগুলো cPanel-এর MySQL Database Wizard-এ যা লিখেছিলেন তার সাথে হুবহু মিলতে হবে)।
  </p>
  <p style="color:#c0392b;font-weight:bold;">
    কাজ শেষে নিরাপত্তার জন্য এই <code>setup.php</code> ফাইলটা ডিলিট করে দিন।
  </p>
</div>
</body>
</html>
