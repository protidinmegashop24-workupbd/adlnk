<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 60)->unique();
            $table->string('title');
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->longText('body');
            $table->timestamps();
        });

        // Carries over the 7 pages that previously lived as hard-coded
        // Blade views (resources/views/pages/*.blade.php), so the admin can
        // edit them (fixing the contact email, for example) without a code
        // deploy. Their {{ route(...) }} calls are resolved to plain paths
        // here since stored body HTML is no longer passed through Blade's
        // compiler.
        $now = now();
        DB::table('pages')->insert([
            [
                'slug' => 'about',
                'title' => 'About klikwit',
                'meta_title' => 'About klikwit',
                'meta_description' => 'klikwit is a free link-management toolkit: a URL shortener, QR code generator, bulk shortening, link-in-bio pages, and link analytics.',
                'body' => <<<'HTML'
<p>klikwit is a free link-management toolkit. It started as a simple URL shortener and has grown to include bulk link shortening, QR code generation, custom short links, password-protected and expiring links, link-in-bio pages, and click analytics — all built around one goal: making it easy to shorten, share, and understand your links.</p>

<h2>What we offer</h2>
<ul>
<li><strong>URL Shortener &amp; Bulk Shortener</strong> — turn long links into short, shareable ones, one at a time or up to 20 at once.</li>
<li><strong>QR Code Generator</strong> — every short link gets a downloadable QR code automatically.</li>
<li><strong>Custom Links</strong> — pick your own short name instead of a random code.</li>
<li><strong>Link-in-Bio</strong> — a free page to share multiple links from one place, for social media bios.</li>
<li><strong>Link Analytics</strong> — click counts, device type, referrers, and timestamps for links you create while signed in.</li>
<li><strong>UTM Builder, URL Expander, and Link Checker</strong> — small utilities to help you work with links.</li>
</ul>

<h2>Who runs klikwit</h2>
<p>klikwit is operated as an independent project. We don't have a large team or a long history to point to — just a tool we've built carefully and keep improving. If you have questions, see our <a href="/contact">Contact page</a>.</p>

<h2>Our commitment</h2>
<p>We aim to keep the core tools free, avoid deceptive design (no fake "download" buttons, no misleading ads), and respond to abuse reports so klikwit isn't used to harm people. See our <a href="/acceptable-use-policy">Acceptable Use Policy</a> and <a href="/report-abuse">Report Abuse</a> page for more.</p>
HTML,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'contact',
                'title' => 'Contact Us',
                'meta_title' => 'Contact klikwit',
                'meta_description' => 'Get in touch with the klikwit team for support, questions, or feedback.',
                'body' => <<<'HTML'
<p>Questions, feedback, or need help with your account? Email us and we'll get back to you as soon as we can:</p>
<p style="font-size:1.1rem"><strong><a href="mailto:support@klikwit.com">support@klikwit.com</a></strong></p>

<h2>Reporting abuse</h2>
<p>If you're reporting a short link being used for phishing, malware, spam, or other abuse, please use our <a href="/report-abuse">Report Abuse</a> form instead — it gets reviewed faster than email.</p>

<h2>Copyright/DMCA claims</h2>
<p>For copyright infringement claims, see our <a href="/dmca">DMCA Policy</a> for what to include in your notice.</p>
HTML,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'privacy',
                'title' => 'Privacy Policy',
                'meta_title' => 'Privacy Policy — klikwit',
                'meta_description' => 'How klikwit collects, uses, and protects your information.',
                'body' => <<<'HTML'
<p class="updated">Last updated: October 2026</p>

<p>This page explains what information klikwit collects, how we use it, and the choices you have. We try to collect as little as we reasonably can.</p>

<h2>Information we collect</h2>
<ul>
<li><strong>Account information</strong> — if you create a free account, we store your name, email address, and a securely hashed password (we never store your password in plain text).</li>
<li><strong>Links you create</strong> — the destination URL, the short code, and (for signed-in users) the link to your account.</li>
<li><strong>Click data</strong> — when someone clicks a short link, we record the date/time, the referring page (if any), and a general device type (desktop, mobile, or tablet) derived from the browser's user agent. We do not store the visitor's IP address.</li>
<li><strong>Link-in-Bio pages</strong> — the page title, your chosen page name, and the links you add to it.</li>
</ul>

<h2>Cookies</h2>
<p>We use essential cookies only: a session cookie to keep you signed in, and a security cookie (CSRF token) to protect forms from cross-site attacks. We do not currently use third-party advertising or tracking cookies. If that changes (for example, if we add display advertising), we will update this policy and our <a href="/cookie-policy">Cookie Policy</a> first.</p>

<h2>How we use your information</h2>
<p>We use it to operate the service: creating and redirecting short links, showing you your own link analytics, and keeping your account secure. We do not sell your personal information to third parties.</p>

<h2>Data retention and deletion</h2>
<p>We keep your account and link data for as long as your account is active. If you'd like your account or data deleted, <a href="/contact">contact us</a> and we'll process the request.</p>

<h2>Children's privacy</h2>
<p>klikwit is not directed at children under 13, and we do not knowingly collect personal information from them.</p>

<h2>Changes to this policy</h2>
<p>If we make material changes to this policy, we'll update the date at the top of this page.</p>

<h2>Contact</h2>
<p>Questions about this policy? <a href="/contact">Contact us</a>.</p>
HTML,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'terms',
                'title' => 'Terms of Service',
                'meta_title' => 'Terms of Service — klikwit',
                'meta_description' => 'The terms that govern your use of klikwit.',
                'body' => <<<'HTML'
<p class="updated">Last updated: October 2026</p>

<p>These terms govern your use of klikwit (the "Service"). By using klikwit, you agree to these terms.</p>

<h2>The service</h2>
<p>klikwit provides URL shortening, QR code generation, link-in-bio pages, and related link-management tools. Core features are free to use. We may introduce paid features in the future; if we do, this page will be updated.</p>

<h2>Your account</h2>
<p>You're responsible for keeping your account credentials secure and for all activity under your account. You must provide accurate information when registering.</p>

<h2>Acceptable use</h2>
<p>You agree not to use klikwit for phishing, malware distribution, spam, illegal content, or anything else prohibited by our <a href="/acceptable-use-policy">Acceptable Use Policy</a>, which is part of these terms.</p>

<h2>Content responsibility</h2>
<p>klikwit does not control, review, or endorse the destinations of links created by users. You are solely responsible for the content you link to. We may disable any link that violates these terms or our Acceptable Use Policy, without prior notice.</p>

<h2>Termination</h2>
<p>We may suspend or terminate accounts, or disable links, that violate these terms or are used for abuse, at our discretion.</p>

<h2>Disclaimer of warranties</h2>
<p>klikwit is provided "as is" without warranties of any kind. We don't guarantee the service will be uninterrupted, error-free, or available at all times.</p>

<h2>Limitation of liability</h2>
<p>To the extent permitted by law, klikwit is not liable for any indirect, incidental, or consequential damages arising from your use of the service, including damages caused by content at the destination of a shortened link.</p>

<h2>Changes to these terms</h2>
<p>We may update these terms from time to time. Continued use of klikwit after a change means you accept the updated terms.</p>

<h2>Governing law</h2>
<p>These terms are governed by the laws of Bangladesh, without regard to conflict-of-law principles.</p>

<h2>Contact</h2>
<p>Questions about these terms? <a href="/contact">Contact us</a>.</p>
HTML,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'cookies',
                'title' => 'Cookie Policy',
                'meta_title' => 'Cookie Policy — klikwit',
                'meta_description' => 'What cookies klikwit uses and why.',
                'body' => <<<'HTML'
<p>klikwit uses a small number of essential cookies to make the site work. We do not currently use advertising or third-party tracking cookies.</p>

<table>
<thead><tr><th>Cookie</th><th>Purpose</th><th>Duration</th></tr></thead>
<tbody>
<tr><td>klikwit-session</td><td>Keeps you signed in and remembers things like your unlocked password-protected links.</td><td>Session / a few hours</td></tr>
<tr><td>XSRF-TOKEN</td><td>Security token that protects forms from cross-site request forgery.</td><td>Session / a few hours</td></tr>
</tbody>
</table>

<h2>Controlling cookies</h2>
<p>Most browsers let you block or delete cookies through their settings. Since klikwit's cookies are essential for sign-in and security, blocking them will prevent you from logging in, but the public shortening tools will still work without an account.</p>

<h2>Future changes</h2>
<p>If we add advertising or analytics that use additional cookies, we'll update this page to reflect that before it happens.</p>
HTML,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'acceptable-use',
                'title' => 'Acceptable Use Policy',
                'meta_title' => 'Acceptable Use Policy — klikwit',
                'meta_description' => 'What you may not use klikwit for, and what happens if you do.',
                'body' => <<<'HTML'
<p>klikwit's link tools are free and open to anyone, which means we have to be clear about what they must not be used for.</p>

<h2>Prohibited uses</h2>
<p>You may not use klikwit to create or share links to:</p>
<ul>
<li>Phishing pages or anything designed to steal credentials or personal information</li>
<li>Malware, viruses, or other malicious software</li>
<li>Spam, including unsolicited bulk messaging campaigns</li>
<li>Content that infringes someone else's copyright or trademark</li>
<li>Illegal content under applicable law</li>
<li>Content that harasses, threatens, or impersonates a real person</li>
<li>Fraud, scams, or deceptive schemes</li>
</ul>

<p>You also may not attempt to circumvent klikwit's security measures, abuse our systems with excessive automated requests, or use the service to artificially inflate click counts (click fraud).</p>

<h2>Enforcement</h2>
<p>If a link is reported or we otherwise discover it violates this policy, we may disable it without prior notice. Repeat violations may result in account suspension or termination. See our <a href="/report-abuse">Report Abuse</a> page to report a link.</p>

<h2>Questions</h2>
<p>If you're unsure whether something is allowed, <a href="/contact">contact us</a> before you create the link.</p>
HTML,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'dmca',
                'title' => 'DMCA / Copyright Policy',
                'meta_title' => 'DMCA / Copyright Policy — klikwit',
                'meta_description' => 'How to submit a copyright infringement notice for a klikwit short link.',
                'body' => <<<'HTML'
<p>klikwit respects the intellectual property rights of others and expects users to do the same. Because klikwit only shortens and redirects links — it does not host the destination content — a copyright claim about what a short link points to should ideally also go to the destination site's own host. That said, we will disable a klikwit short link that is clearly being used to distribute infringing content.</p>

<h2>How to submit a notice</h2>
<p>Send a notice via our <a href="/report-abuse">Report Abuse</a> form (select "Copyright / DMCA infringement") or by <a href="/contact">emailing us</a>, including:</p>
<ul>
<li>Identification of the copyrighted work you claim has been infringed</li>
<li>The klikwit short link (e.g. klikwit.com/abc123) you're reporting</li>
<li>Your contact information (name, email, and mailing address)</li>
<li>A statement that you have a good-faith belief the use is not authorized by the copyright owner, its agent, or the law</li>
<li>A statement, under penalty of perjury, that the information in your notice is accurate and that you are the copyright owner or authorized to act on their behalf</li>
<li>Your physical or electronic signature</li>
</ul>

<h2>Counter-notice</h2>
<p>If you believe a link was disabled in error, you may submit a counter-notice via the same channels, including the information above plus a statement under penalty of perjury that you have a good-faith belief the link was removed as a result of mistake or misidentification.</p>

<h2>Repeat infringers</h2>
<p>We will disable links and may suspend accounts found to be repeat infringers.</p>
HTML,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
