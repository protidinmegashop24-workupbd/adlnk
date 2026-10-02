@extends('layouts.app')

@section('title', 'Canonical URL Checker | Klikwit')
@section('description', 'Check whether a page has a canonical tag, whether it self-references, and whether it matches the page\'s scheme and host. Free, no signup.')
@section('page-class', 'wide')

@section('content')
<div class="tool-section">
  @include('partials.seo-breadcrumb', ['crumbs' => [
    ['label' => 'Home', 'url' => route('home')],
    ['label' => 'SEO Tools', 'url' => route('seo-tools.index')],
    ['label' => 'Canonical Checker'],
  ]])

  <h1>Canonical URL Checker</h1>
  <p>Paste a URL to check whether it has a canonical tag, whether that tag points back to itself, and whether its scheme or host (www vs. non-www) matches the page actually served.</p>

  <div class="card">
    <form id="f">
      <label for="url">Page URL</label>
      <input id="url" type="url" placeholder="https://example.com/your-page" required/>
      <button id="btn" type="submit">Check Canonical</button>
      <div class="error" id="err"></div>
    </form>
    <div class="tool-result" id="result" style="display:none"></div>
  </div>

  <h2>What Is a Canonical URL?</h2>
  <p>A canonical URL, set with <code>&lt;link rel="canonical" href="..."&gt;</code>, tells search engines which address is the "main" version of a page when the same or similar content is reachable through more than one URL — for example <code>example.com/page</code> and <code>example.com/page?ref=email</code>.</p>

  <h2>Why Is It Useful?</h2>
  <p>Without a canonical tag, search engines have to guess which version of a page to treat as authoritative when duplicates exist. Pointing them at the right one helps consolidate signals like links and avoids pages competing with themselves.</p>

  <h2>How to Use It</h2>
  <p>Paste the full URL of a public page and click "Check Canonical." The tool fetches the page, reads its canonical tag (if any), and compares it against the URL that was actually served after following any redirects.</p>

  <h2>How to Interpret the Results</h2>
  <p>A self-referencing canonical (pointing back at the same page) is normal for most pages. A canonical pointing elsewhere isn't automatically wrong — it's a deliberate, common pattern for filtered, paginated, or tracking-parameter URLs that should defer to a main page. Scheme (http vs. https) or www/non-www mismatches are worth checking since they can be unintentional.</p>

  <h2>Common Mistakes</h2>
  <ul>
    <li>No canonical tag at all on pages reachable through multiple URLs.</li>
    <li>A canonical pointing to an old URL after a page was moved or renamed.</li>
    <li>A relative canonical value (e.g. <code>/page</code>) instead of a full absolute URL.</li>
    <li>Canonical using http while the page is actually served over https (or the reverse).</li>
  </ul>

  <h2>Limitations</h2>
  <p>This tool only reads the canonical tag found in server-rendered HTML — if your site adds it with JavaScript after load, this check may not see it. It also can't tell you how a search engine ultimately decided to treat the page; it only reports what your page's HTML says.</p>

  @include('partials.seo-faq', ['faqs' => [
    ['q' => 'Do I need a canonical tag on every page?', 'a' => 'It\'s good practice, especially for any page reachable through more than one URL. A self-referencing canonical is harmless even on pages with no duplicates.'],
    ['q' => 'Is it bad if my canonical points to a different page?', 'a' => 'Not necessarily. Pointing a filtered, sorted, or paginated URL\'s canonical at the main page is a standard, intentional pattern — the key is that it should be deliberate, not accidental.'],
    ['q' => 'What happens if I have no canonical tag at all?', 'a' => 'Search engines will try to pick a canonical version on their own based on other signals. Setting it explicitly removes the guesswork.'],
    ['q' => 'Does a canonical tag redirect visitors?', 'a' => 'No. It\'s a hint for search engines only — visitors loading the page see it exactly as served, with no redirect.'],
    ['q' => 'Why does this tool show a www mismatch as a warning, not an error?', 'a' => 'www and non-www are different hosts technically, so this could be exactly what you intended (e.g. you\'ve standardized on the www version). It\'s flagged so you can confirm, not because it\'s always wrong.'],
  ]])

  @include('partials.seo-related-tools', ['tools' => [
    ['label' => 'Meta Tag Checker', 'url' => route('seo-tools.meta-tag-checker'), 'icon' => '🔍'],
    ['label' => 'Open Graph Checker', 'url' => route('seo-tools.open-graph-checker'), 'icon' => '🖼️'],
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
  var form = document.getElementById('f');
  var btn = document.getElementById('btn');
  var err = document.getElementById('err');
  var result = document.getElementById('result');

  form.addEventListener('submit', function(e){
    e.preventDefault();
    err.textContent = '';
    result.style.display = 'none';
    btn.disabled = true;
    btn.textContent = 'Checking...';
    fetch('{{ url('/api/seo/canonical-checker') }}', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ url: document.getElementById('url').value.trim() })
    }).then(function(r){ return r.json().then(function(j){ return {ok:r.ok, body:j}; }); })
      .then(function(res){
        btn.disabled = false;
        btn.textContent = 'Check Canonical';
        if (!res.ok || !res.body.checks) {
          err.textContent = (res.body && res.body.error) ? res.body.error : 'Something went wrong.';
          return;
        }
        result.style.display = 'block';
        renderSeoResults(result, res.body.checks);
      }).catch(function(){
        btn.disabled = false;
        btn.textContent = 'Check Canonical';
        err.textContent = 'Something went wrong. Please try again.';
      });
  });
})();
</script>
@endsection
