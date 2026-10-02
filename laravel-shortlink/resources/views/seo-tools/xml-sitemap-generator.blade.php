@extends('layouts.app')

@section('title', 'XML Sitemap Generator | Klikwit')
@section('description', 'Turn a list of URLs into a valid XML sitemap. Free, runs in your browser, with duplicate and validity checks. Copy or download instantly.')
@section('page-class', 'wide')
@section('extra-style')
  .robots-output{background:#1e1e1e;color:#d4d4d4;border-radius:8px;padding:16px;font-family:monospace;font-size:12px;white-space:pre-wrap;word-break:break-all;margin-top:12px;min-height:80px;max-height:400px;overflow-y:auto}
  .robots-btns{display:flex;gap:8px;margin-top:12px}
  .robots-btns button{width:auto;margin:0;flex:1}
@endsection

@section('content')
<div class="tool-section">
  @include('partials.seo-breadcrumb', ['crumbs' => [
    ['label' => 'Home', 'url' => route('home')],
    ['label' => 'SEO Tools', 'url' => route('seo-tools.index')],
    ['label' => 'XML Sitemap Generator'],
  ]])

  <h1>XML Sitemap Generator</h1>
  <p>Paste a list of URLs (one per line) to generate a valid XML sitemap. This tool only works with the URLs you provide — it does not crawl or discover pages on your site.</p>

  <div class="card">
    <label for="urlList">URLs (one per line)</label>
    <textarea id="urlList" rows="10" placeholder="https://example.com/&#10;https://example.com/about&#10;https://example.com/blog"></textarea>
  </div>

  <div class="tool-result" id="issues"></div>

  <div class="robots-output" id="xmlOutput"></div>
  <div class="robots-btns">
    <button type="button" id="copyBtn" class="copybtn">Copy</button>
    <button type="button" id="downloadBtn">Download sitemap.xml</button>
  </div>

  <h2>What Is an XML Sitemap?</h2>
  <p>An XML sitemap is a file listing a site's pages in a standard format, helping search engines discover URLs — especially useful for pages that might not be easy to find through regular links alone.</p>

  <h2>Why Is It Useful?</h2>
  <p>It gives search engines a direct, structured list of pages you'd like them to know about, rather than relying entirely on them finding every page by following links.</p>

  <h2>How to Use It</h2>
  <p>Paste your URLs, one per line, including the full <code>https://</code> address. The tool checks each one, flags any issues, removes duplicates, and generates valid sitemap XML you can copy or download.</p>

  <h2>How to Interpret the Results</h2>
  <p>Issues are shown above the generated XML — invalid URLs and exact duplicates are excluded from the output automatically, while URLs served over plain HTTP are flagged as worth switching to HTTPS, but are still included.</p>

  <h2>Common Mistakes</h2>
  <ul>
    <li>Including pages that redirect elsewhere, instead of their final destination.</li>
    <li>Mixing http and https versions of the same page as if they were different URLs.</li>
    <li>Forgetting to update the sitemap after adding or removing pages.</li>
    <li>Including pages you've blocked in robots.txt or marked noindex.</li>
  </ul>

  <h2>Limitations</h2>
  <p>This initial version only works with URLs you type or paste in — it does not crawl your website to discover pages automatically. For large sites, you'll need to compile the URL list yourself or export it from your CMS.</p>

  @include('partials.seo-faq', ['faqs' => [
    ['q' => 'Do I need a sitemap for a small site?', 'a' => 'It\'s less critical for a small, well-linked site, but it\'s still a simple, low-cost way to help search engines discover your pages.'],
    ['q' => 'Where do I upload the sitemap file?', 'a' => 'Typically at the root of your domain (e.g. example.com/sitemap.xml), referenced from your robots.txt file.'],
    ['q' => 'Does this tool crawl my website automatically?', 'a' => 'Not in this version — you provide the URL list directly. Automatic crawling may be added in a future update.'],
    ['q' => 'Why was a URL removed from my list?', 'a' => 'Either it wasn\'t a valid, complete URL, or it was an exact duplicate of another URL already in your list.'],
  ]])

  @include('partials.seo-related-tools', ['tools' => [
    ['label' => 'Robots.txt Generator', 'url' => route('seo-tools.robots-txt-generator'), 'icon' => '🤖'],
    ['label' => 'SEO URL Checker', 'url' => route('seo-tools.seo-url-checker'), 'icon' => '🔗'],
  ]])

  <div class="final-cta">
    <h2>Also Managing Links?</h2>
    <p>klikwit's free URL shortener, QR codes and link analytics live right alongside these SEO tools.</p>
    <a class="cta-btn" href="{{ route('home') }}">Try the URL Shortener</a>
  </div>
</div>

@include('partials.seo-result-renderer')
<script>
(function(){
  var urlList = document.getElementById('urlList');
  var issuesEl = document.getElementById('issues');
  var output = document.getElementById('xmlOutput');
  var copyBtn = document.getElementById('copyBtn');
  var downloadBtn = document.getElementById('downloadBtn');

  // eslint-disable-next-line no-control-regex
  var CONTROL_CHARS = /[\x00-\x08\x0B\x0C\x0E-\x1F]/;

  function escapeXml(s) {
    return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&apos;');
  }

  function update() {
    var lines = urlList.value.split('\n').map(function (l) { return l.trim(); }).filter(function (l) { return l.length > 0; });
    var seen = {};
    var valid = [];
    var checks = [];

    lines.forEach(function (line) {
      if (CONTROL_CHARS.test(line)) {
        checks.push({ status: 'warning', label: 'Invalid Characters', finding: 'Skipped a line with invalid control characters.', explanation: 'XML does not allow raw control characters in content.', action: 'Remove unusual characters from this URL.' });
        return;
      }
      var parsed;
      try {
        parsed = new URL(line);
      } catch (e) {
        checks.push({ status: 'warning', label: 'Invalid URL', finding: '"' + line + '" is not a valid, complete URL.', explanation: 'Each line must be a full URL including the scheme (https://).', action: 'Fix or remove this line.' });
        return;
      }
      if (parsed.protocol !== 'http:' && parsed.protocol !== 'https:') {
        checks.push({ status: 'warning', label: 'Unsupported Scheme', finding: '"' + line + '" uses an unsupported scheme.', explanation: 'Sitemaps should only contain http:// or https:// URLs.', action: 'Remove this line.' });
        return;
      }
      if (seen[parsed.href]) {
        checks.push({ status: 'warning', label: 'Duplicate URL', finding: '"' + line + '" appears more than once.', explanation: 'Duplicate entries were removed from the generated sitemap.', action: null });
        return;
      }
      seen[parsed.href] = true;
      if (parsed.protocol === 'http:') {
        checks.push({ status: 'warning', label: 'Not HTTPS', finding: '"' + line + '" is served over plain HTTP.', explanation: 'HTTPS is the modern standard; this URL is still included in the sitemap.', action: 'Consider switching this page to HTTPS.' });
      }
      valid.push(parsed.href);
    });

    if (checks.length > 0) {
      issuesEl.style.display = 'block';
      renderSeoResults(issuesEl, checks);
    } else {
      issuesEl.style.display = 'none';
    }

    var xml = '<?xml version="1.0" encoding="UTF-8"?>\n<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n';
    valid.forEach(function (url) {
      xml += '  <url>\n    <loc>' + escapeXml(url) + '</loc>\n  </url>\n';
    });
    xml += '</urlset>\n';

    output.textContent = valid.length > 0 ? xml : '(Add at least one valid URL above to generate a sitemap.)';
  }

  copyBtn.addEventListener('click', function () {
    navigator.clipboard.writeText(output.textContent).then(function () {
      var original = copyBtn.textContent;
      copyBtn.textContent = 'Copied!';
      setTimeout(function () { copyBtn.textContent = original; }, 1500);
    });
  });

  downloadBtn.addEventListener('click', function () {
    var blob = new Blob([output.textContent], { type: 'application/xml' });
    var link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'sitemap.xml';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  });

  urlList.addEventListener('input', update);
  update();
})();
</script>
@endsection
