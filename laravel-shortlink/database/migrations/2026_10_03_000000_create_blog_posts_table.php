<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug', 100)->unique();
            $table->string('excerpt', 500);
            $table->longText('body');
            $table->string('category', 60);
            $table->string('thumbnail')->nullable();
            $table->boolean('published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Carries over the 3 posts that previously lived as hard-coded Blade
        // partials (resources/views/blog/posts/*.blade.php) plus a metadata
        // array in BlogController, so nothing is lost when the blog moves
        // to being admin-managed. Their {{ route(...) }} calls are resolved
        // to plain paths here since stored body HTML is no longer passed
        // through Blade's compiler.
        $now = now();
        DB::table('blog_posts')->insert([
            [
                'title' => 'What Is a URL Shortener and Why Should You Use One?',
                'slug' => 'what-is-a-url-shortener',
                'excerpt' => 'Long links are hard to share, remember, and track. Here\'s what a URL shortener actually does and when it helps.',
                'category' => 'URL Shortening',
                'thumbnail' => 'images/blog/what-is-a-url-shortener.png',
                'published' => true,
                'published_at' => '2026-10-01 00:00:00',
                'created_at' => $now,
                'updated_at' => $now,
                'body' => <<<'HTML'
<p>If you've ever tried to share a link on Instagram, print it on a flyer, or read it out loud over the phone, you've probably run into the same problem: modern URLs are long, messy, and full of random characters. A <strong>URL shortener</strong> fixes that by turning a link like this —</p>

<p><code>https://example.com/products/category/electronics?ref=newsletter&amp;utm_source=facebook&amp;utm_campaign=autumn2026</code></p>

<p>— into something short and clean, like <code>klikwit.com/sale26</code>. Both links go to the exact same page. The short one is just easier to use.</p>

<h2>How does it actually work?</h2>
<p>When you shorten a link with a tool like <a href="/">klikwit</a>, we generate a unique short code and store it alongside your original (long) URL in our database. When someone visits the short link, our server looks up the code, finds the matching long URL, and instantly redirects the visitor there — no waiting, no ad page, just a direct redirect.</p>

<h2>Why people use short links</h2>
<ul>
  <li><strong>They're easier to share.</strong> A short link fits in a text message, a business card, or a tweet without getting cut off.</li>
  <li><strong>They look cleaner.</strong> A custom short link, with a name you choose yourself instead of a random code, looks more professional than a long, auto-generated URL.</li>
  <li><strong>You can track clicks.</strong> If you sign up for a free klikwit account, every link you shorten is saved to your <a href="/dashboard">dashboard</a> with a running click count — so you can see how many people actually used it.</li>
  <li><strong>They work well with QR codes.</strong> Shorter links make for simpler, more reliable QR codes — more on that in our <a href="/blog/how-to-create-a-qr-code">QR code guide</a>.</li>
</ul>

<h2>Is it safe?</h2>
<p>A good shortener should redirect you immediately to the real destination — not make you sit through a countdown or an ad page before you get there. That's exactly how klikwit works by default: instant, direct redirects, so the experience is safe and trustworthy for whoever clicks your link.</p>

<h2>Try it yourself</h2>
<p>Head back to the <a href="/">klikwit homepage</a>, paste in any long link, and click Shorten. It's free, and you don't need an account — though creating one lets you keep track of every link you make.</p>
HTML,
            ],
            [
                'title' => 'How to Create a QR Code for Any Link (Free, No Signup)',
                'slug' => 'how-to-create-a-qr-code',
                'excerpt' => 'Turn any link into a scannable QR code in seconds — for print, packaging, or a storefront sign.',
                'category' => 'QR Codes',
                'thumbnail' => 'images/blog/how-to-create-a-qr-code.png',
                'published' => true,
                'published_at' => '2026-10-01 00:00:00',
                'created_at' => $now,
                'updated_at' => $now,
                'body' => <<<'HTML'
<p>A QR code is just a square barcode that a phone camera can scan to instantly open a link — no typing required. They're everywhere now: on restaurant menus, product packaging, event posters, and business cards. Here's how to make one for free in under a minute.</p>

<h2>Step 1: Shorten your link</h2>
<p>Go to the <a href="/">klikwit homepage</a> and paste the long URL you want people to visit — your website, a menu, a social profile, anything. Click <strong>Shorten</strong>.</p>

<h2>Step 2: Grab your QR code</h2>
<p>As soon as your short link is created, a QR code appears right below it automatically. Click <strong>Download QR</strong> to save it as an image you can print or share.</p>

<h2>Where to use it</h2>
<ul>
  <li><strong>Print materials</strong> — flyers, posters, business cards, product packaging. Anywhere someone can point a camera.</li>
  <li><strong>Physical storefronts</strong> — a QR code on your shop window or counter that links straight to your menu, catalog, or booking page.</li>
  <li><strong>Presentations</strong> — add a QR code to a slide so your audience can instantly open a link on their own phone.</li>
  <li><strong>Packaging</strong> — link to a product manual, warranty registration, or review page.</li>
</ul>

<h2>A tip: shorten first, then make the QR code</h2>
<p>You could generate a QR code directly from a long URL, but it's better to shorten the link first. Shorter links produce simpler QR codes that scan faster and more reliably, especially when printed small. It also means if you ever need to change where the link points, you can do it without reprinting anything — the short link stays the same even if the destination changes.</p>

<h2>Combine it with a Link-in-Bio page</h2>
<p>If you want one QR code to lead to several things at once — your shop, your social profiles, your latest promotion — create a free <a href="/blog/what-is-link-in-bio">Link-in-Bio page</a> first, then turn that single page into a QR code. One scan, multiple destinations.</p>

<p>Ready to try it? <a href="/">Create your free QR code now</a> — no signup required.</p>
HTML,
            ],
            [
                'title' => 'What Is Link-in-Bio? A Simple Guide for Creators and Small Businesses',
                'slug' => 'what-is-link-in-bio',
                'excerpt' => 'Instagram and TikTok only let you put one link in your profile. Here\'s how to share many, the right way.',
                'category' => 'Link-in-Bio',
                'thumbnail' => 'images/blog/what-is-link-in-bio.png',
                'published' => true,
                'published_at' => '2026-10-01 00:00:00',
                'created_at' => $now,
                'updated_at' => $now,
                'body' => <<<'HTML'
<p>Instagram, TikTok, and most social platforms only let you put <strong>one clickable link</strong> in your profile bio. But most creators and small businesses have more than one thing to share — a shop, a portfolio, a booking page, other social profiles. That's the problem a "link-in-bio" page solves.</p>

<h2>What it is</h2>
<p>A link-in-bio page is a single, simple webpage that lists several links in one place — your shop, your latest video, your contact form, whatever you want. Instead of putting one link in your Instagram bio, you put the link to your link-in-bio page, and it fans out into as many links as you need.</p>

<h2>Who uses it</h2>
<ul>
  <li><strong>Creators</strong> linking to their latest video, merch store, and other social accounts all at once.</li>
  <li><strong>Small businesses</strong> sharing their menu, booking page, and location in one tap.</li>
  <li><strong>Freelancers</strong> pointing people to their portfolio, resume, and contact details.</li>
  <li><strong>Event organizers</strong> sharing tickets, schedule, and venue map from a single bio link.</li>
</ul>

<h2>How to create one with klikwit</h2>
<ol>
  <li><a href="/register">Create a free account</a> (takes less than a minute).</li>
  <li>Go to <strong>Link-in-Bio</strong> in the menu.</li>
  <li>Give your page a title and choose your page name — this becomes your public URL, like <code>klikwit.com/u/yourname</code>.</li>
  <li>Add your links one by one: a label (like "My Shop") and the destination URL.</li>
  <li>Click <strong>Save Page</strong> — it's live immediately.</li>
</ol>

<p>Once it's live, put your bio page link (<code>klikwit.com/u/yourname</code>) into your Instagram, TikTok, or YouTube bio. You can come back and edit your links any time — add new ones, remove old ones, or update a URL — without changing the link in your social profile.</p>

<h2>Make it even shorter</h2>
<p>Want your bio link to look even cleaner? You can also turn it into a custom short link using klikwit's <a href="/">URL shortener</a>, or generate a QR code for it to put on business cards or event posters — see our <a href="/blog/how-to-create-a-qr-code">QR code guide</a> for that.</p>

<p><a href="/register">Create your free Link-in-Bio page now</a> — no credit card, no limits on edits.</p>
HTML,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
