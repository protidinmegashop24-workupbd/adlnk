@extends('layouts.app')

@section('title', 'SEO URL Checker - Check URL Structure | Klikwit')
@section('description', 'Check a URL for HTTPS, length, readability, casing, spaces, special characters and other common structural issues. Free, runs in your browser.')
@section('page-class', 'wide')

@section('content')
<div class="tool-section">
  @include('partials.seo-breadcrumb', ['crumbs' => [
    ['label' => 'Home', 'url' => route('home')],
    ['label' => 'SEO Tools', 'url' => route('seo-tools.index')],
    ['label' => 'SEO URL Checker'],
  ]])

  <h1>SEO URL Checker</h1>
  <p>Paste a URL to check its structure for common readability and technical issues — all checked locally in your browser, with no request sent anywhere.</p>

  <div class="card">
    <label for="urlInput">URL</label>
    <input id="urlInput" type="text" placeholder="https://example.com/your-page-path"/>
  </div>

  <div class="tool-result" id="result"></div>

  <h2>What Does This Tool Check?</h2>
  <p>It looks at a URL's structure — not its content — for things that are broadly agreed to make URLs easier for both people and crawlers to read: HTTPS, reasonable length, lowercase letters, hyphens instead of spaces or underscores, and no unnecessary special characters.</p>

  <h2>Why Is It Useful?</h2>
  <p>A clean, readable URL is easier to share, read aloud, and remember, and avoids a few easy-to-make technical mistakes (like literal spaces or mismatched casing on case-sensitive servers).</p>

  <h2>How to Use It</h2>
  <p>Paste a full URL, including <code>https://</code>. Results update as you type, each marked Good or Needs Attention with an explanation.</p>

  <h2>How to Interpret the Results</h2>
  <p><strong>Good</strong> means that aspect of the URL follows a common best practice. <strong>Needs Attention</strong> flags something worth a second look — not necessarily a hard error, since there are legitimate reasons some sites use query parameters, mixed case, or longer paths.</p>

  <h2>Common Mistakes</h2>
  <ul>
    <li>Using underscores or literal spaces instead of hyphens between words.</li>
    <li>Stacking many query parameters when a clean path would work.</li>
    <li>Mixing uppercase and lowercase letters inconsistently across similar pages.</li>
    <li>Nesting pages many folders deep for no structural reason.</li>
  </ul>

  <h2>Limitations</h2>
  <p>A URL can follow every guideline here and still not rank well, or break every guideline and still work fine — structure is one small, cosmetic part of a much bigger picture. This tool does not check whether the URL actually loads; use the Link Checker for that.</p>

  @include('partials.seo-faq', ['faqs' => [
    ['q' => 'Does a clean URL guarantee better rankings?', 'a' => 'No. URL structure is a minor, mostly cosmetic factor. Content quality, relevance and overall site experience matter far more.'],
    ['q' => 'Are query parameters always bad?', 'a' => 'No — they\'re often necessary (search filters, tracking, pagination). They\'re flagged here only when there are a lot of them, since that can hurt readability.'],
    ['q' => 'Should URLs always be lowercase?', 'a' => 'It\'s a common convention that avoids confusion on case-sensitive servers, but it\'s not a strict requirement everywhere.'],
    ['q' => 'What counts as "too deep" a path?', 'a' => 'There\'s no official limit — this tool flags unusually deep nesting (several folders) as worth a look, since very deep paths can be harder to read and maintain.'],
  ]])

  @include('partials.seo-related-tools', ['tools' => [
    ['label' => 'Slug Generator', 'url' => route('seo-tools.slug-generator'), 'icon' => '✂️'],
    ['label' => 'Canonical Checker', 'url' => route('seo-tools.canonical-checker'), 'icon' => '🧭'],
    ['label' => 'Meta Tag Checker', 'url' => route('seo-tools.meta-tag-checker'), 'icon' => '🔍'],
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
  var input = document.getElementById('urlInput');
  var result = document.getElementById('result');

  function check(url) {
    var checks = [];
    var parsed;
    try {
      parsed = new URL(url);
    } catch (e) {
      checks.push({ status: 'warning', label: 'URL Format', finding: 'This does not look like a complete URL.', explanation: 'A full URL includes a scheme, e.g. https://example.com/page.', action: 'Include https:// at the start of the URL.' });
      return checks;
    }

    checks.push({
      status: parsed.protocol === 'https:' ? 'pass' : 'warning',
      label: 'HTTPS',
      finding: parsed.protocol === 'https:' ? 'This URL uses HTTPS.' : 'This URL does not use HTTPS (' + parsed.protocol + ').',
      explanation: 'HTTPS encrypts traffic between the browser and server and is the modern standard for all public pages.',
      action: parsed.protocol === 'https:' ? null : 'Serve this page over HTTPS.',
    });

    var len = url.length;
    checks.push({
      status: len <= 100 ? 'pass' : 'warning',
      label: 'URL Length',
      finding: len + ' characters.',
      explanation: 'Shorter URLs are generally easier to read, share and remember.',
      action: len <= 100 ? null : 'Consider shortening the path if possible.',
    });

    var path = parsed.pathname;
    var hasUpper = /[A-Z]/.test(path);
    checks.push({
      status: hasUpper ? 'warning' : 'pass',
      label: 'Lowercase',
      finding: hasUpper ? 'The path contains uppercase letters.' : 'The path is entirely lowercase.',
      explanation: 'Lowercase URLs avoid confusion on servers where capitalization matters (e.g. /Page and /page could be treated as different URLs).',
      action: hasUpper ? 'Consider using lowercase letters consistently.' : null,
    });

    var hasEncodedSpace = /%20|\+/.test(path);
    var hasUnderscore = /_/.test(path);
    checks.push({
      status: (hasEncodedSpace || hasUnderscore) ? 'warning' : 'pass',
      label: 'Word Separators',
      finding: hasEncodedSpace ? 'The path contains encoded spaces.' : (hasUnderscore ? 'The path uses underscores.' : 'No spaces or underscores found in the path.'),
      explanation: 'Hyphens are the generally preferred way to separate words in a URL path, over spaces or underscores.',
      action: (hasEncodedSpace || hasUnderscore) ? 'Consider using hyphens (-) instead of spaces or underscores.' : null,
    });

    var hasSpecialChars = /[^a-zA-Z0-9\-._~/]/.test(path);
    checks.push({
      status: hasSpecialChars ? 'warning' : 'pass',
      label: 'Special Characters',
      finding: hasSpecialChars ? 'The path contains characters outside letters, numbers, hyphens, dots and underscores.' : 'No unusual special characters found in the path.',
      explanation: 'Unusual characters in a URL path often need to be percent-encoded and can look messy when shared.',
      action: hasSpecialChars ? 'Consider removing or replacing special characters in the path.' : null,
    });

    var repeatedSeparators = /--|__|\/\//.test(path);
    checks.push({
      status: repeatedSeparators ? 'warning' : 'pass',
      label: 'Repeated Separators',
      finding: repeatedSeparators ? 'The path contains repeated hyphens, underscores, or slashes.' : 'No repeated separators found.',
      explanation: 'Doubled-up separators are usually accidental and can make a URL look broken.',
      action: repeatedSeparators ? 'Remove the duplicate separator characters.' : null,
    });

    var paramCount = Array.from(parsed.searchParams.keys()).length;
    checks.push({
      status: paramCount > 3 ? 'warning' : 'pass',
      label: 'Query Parameters',
      finding: paramCount === 0 ? 'No query parameters.' : (paramCount + ' query parameter(s) found.'),
      explanation: 'Query parameters are often necessary, but a long string of them can hurt readability.',
      action: paramCount > 3 ? 'Consider whether some parameters could be part of the path instead, if this is a primary page.' : null,
    });

    var depth = path.split('/').filter(function (s) { return s.length > 0; }).length;
    checks.push({
      status: depth > 4 ? 'warning' : 'pass',
      label: 'Path Depth',
      finding: depth + ' path segment(s).',
      explanation: 'Very deep nesting can make a URL harder to read and a site harder to navigate.',
      action: depth > 4 ? 'Consider a flatter URL structure if this depth isn\'t structurally necessary.' : null,
    });

    return checks;
  }

  function update() {
    var url = input.value.trim();
    if (url === '') {
      result.style.display = 'none';
      return;
    }
    result.style.display = 'block';
    renderSeoResults(result, check(url));
  }

  input.addEventListener('input', update);
})();
</script>
@endsection
