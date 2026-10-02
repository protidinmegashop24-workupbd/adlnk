@extends('layouts.app')

@section('title', 'Robots.txt Generator | Klikwit')
@section('description', 'Generate a valid robots.txt file with allow/disallow rules and a sitemap link. Free, runs in your browser. Download or copy instantly.')
@section('page-class', 'wide')
@section('extra-style')
  .robots-output{background:#1e1e1e;color:#d4d4d4;border-radius:8px;padding:16px;font-family:monospace;font-size:13px;white-space:pre-wrap;word-break:break-all;margin-top:12px;min-height:80px}
  .robots-btns{display:flex;gap:8px;margin-top:12px}
  .robots-btns button{width:auto;margin:0;flex:1}
@endsection

@section('content')
<div class="tool-section">
  @include('partials.seo-breadcrumb', ['crumbs' => [
    ['label' => 'Home', 'url' => route('home')],
    ['label' => 'SEO Tools', 'url' => route('seo-tools.index')],
    ['label' => 'Robots.txt Generator'],
  ]])

  <h1>Robots.txt Generator</h1>
  <p>Fill in the fields below to generate a valid <code>robots.txt</code> file. This is a generator only — it does not change your live website.</p>

  <div class="card">
    <label for="userAgent">User-agent</label>
    <input id="userAgent" type="text" value="*" placeholder="*"/>
    <label for="allowPaths">Allow (one path per line)</label>
    <textarea id="allowPaths" rows="3" placeholder="/">/</textarea>
    <label for="disallowPaths">Disallow (one path per line)</label>
    <textarea id="disallowPaths" rows="3" placeholder="/admin/&#10;/dashboard"></textarea>
    <label for="sitemapUrl">Sitemap URL (optional)</label>
    <input id="sitemapUrl" type="text" placeholder="https://example.com/sitemap.xml"/>
  </div>

  <div class="robots-output" id="robotsOutput"></div>
  <div class="robots-btns">
    <button type="button" id="copyBtn" class="copybtn">Copy</button>
    <button type="button" id="downloadBtn">Download robots.txt</button>
  </div>

  <h2>What Is Robots.txt?</h2>
  <p>A robots.txt file, placed at the root of a domain (e.g. <code>example.com/robots.txt</code>), tells well-behaved web crawlers which parts of a site they're allowed or asked not to crawl. It's a set of instructions, not an enforced lock.</p>

  <h2>Why Is It Useful?</h2>
  <p>It lets you point crawlers away from pages you don't want indexed — like an admin area or an internal dashboard — and can point them toward your sitemap to help them discover your content more efficiently.</p>

  <h2>How to Use It</h2>
  <p>Enter a user-agent (<code>*</code> applies the rules to all crawlers), list paths to allow and disallow, and optionally add your sitemap URL. Copy the generated text or download it, then upload it as <code>robots.txt</code> at the root of your site.</p>

  <h2>How to Interpret the Results</h2>
  <p>Disallow rules are requests, not guarantees — well-behaved crawlers (like major search engines) respect them, but robots.txt is not a security mechanism and shouldn't be relied on to keep sensitive content private.</p>

  <h2>Common Mistakes</h2>
  <ul>
    <li>Assuming a Disallow rule removes a page from search results — it only asks crawlers not to visit it. Use a noindex meta tag to actually keep a page out of search results.</li>
    <li>Accidentally disallowing the entire site with <code>Disallow: /</code> when only one section should be blocked.</li>
    <li>Forgetting to upload the file to the actual root of the domain — it won't work from a subfolder.</li>
    <li>Relying on robots.txt to hide genuinely sensitive or private information.</li>
  </ul>

  <h2>Limitations</h2>
  <p>This tool only generates text — it does not upload, validate against your live site, or verify that crawlers are respecting the rules. You're responsible for placing the file correctly on your own server.</p>

  @include('partials.seo-faq', ['faqs' => [
    ['q' => 'Does robots.txt keep pages out of Google?', 'a' => 'Not reliably by itself. A disallowed page can sometimes still appear in search results (e.g. if other sites link to it) without its content being shown. Use a noindex meta tag for pages you want fully excluded.'],
    ['q' => 'Where do I upload robots.txt?', 'a' => 'At the root of your domain, so it\'s reachable at https://yourdomain.com/robots.txt — not inside a subfolder.'],
    ['q' => 'What does "User-agent: *" mean?', 'a' => 'The asterisk (*) is a wildcard meaning "all crawlers." You can also target a specific crawler by name if needed.'],
    ['q' => 'Is robots.txt a security feature?', 'a' => 'No. It\'s a public file that crawlers may or may not respect. Never rely on it to hide sensitive or private data — use proper authentication for that.'],
  ]])

  @include('partials.seo-related-tools', ['tools' => [
    ['label' => 'XML Sitemap Generator', 'url' => route('seo-tools.xml-sitemap-generator'), 'icon' => '🗺️'],
    ['label' => 'SEO URL Checker', 'url' => route('seo-tools.seo-url-checker'), 'icon' => '🔗'],
  ]])

  <div class="final-cta">
    <h2>Also Managing Links?</h2>
    <p>klikwit's free URL shortener, QR codes and link analytics live right alongside these SEO tools.</p>
    <a class="cta-btn" href="{{ route('home') }}">Try the URL Shortener</a>
  </div>
</div>

<script>
(function(){
  var userAgent = document.getElementById('userAgent');
  var allowPaths = document.getElementById('allowPaths');
  var disallowPaths = document.getElementById('disallowPaths');
  var sitemapUrl = document.getElementById('sitemapUrl');
  var output = document.getElementById('robotsOutput');
  var copyBtn = document.getElementById('copyBtn');
  var downloadBtn = document.getElementById('downloadBtn');

  function lines(textarea) {
    return textarea.value.split('\n').map(function (l) { return l.trim(); }).filter(function (l) { return l.length > 0; });
  }

  function generate() {
    var ua = userAgent.value.trim() || '*';
    var out = 'User-agent: ' + ua + '\n';

    lines(allowPaths).forEach(function (p) { out += 'Allow: ' + p + '\n'; });
    lines(disallowPaths).forEach(function (p) { out += 'Disallow: ' + p + '\n'; });

    var sitemap = sitemapUrl.value.trim();
    if (sitemap) {
      out += '\nSitemap: ' + sitemap + '\n';
    }

    return out;
  }

  function update() {
    output.textContent = generate();
  }

  copyBtn.addEventListener('click', function () {
    navigator.clipboard.writeText(output.textContent).then(function () {
      var original = copyBtn.textContent;
      copyBtn.textContent = 'Copied!';
      setTimeout(function () { copyBtn.textContent = original; }, 1500);
    });
  });

  downloadBtn.addEventListener('click', function () {
    var blob = new Blob([output.textContent], { type: 'text/plain' });
    var link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'robots.txt';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  });

  [userAgent, allowPaths, disallowPaths, sitemapUrl].forEach(function (el) {
    el.addEventListener('input', update);
  });
  update();
})();
</script>
@endsection
