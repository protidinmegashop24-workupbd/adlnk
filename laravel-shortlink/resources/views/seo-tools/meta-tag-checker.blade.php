@extends('layouts.app')

@section('title', 'Meta Tag Checker - Check Title & Description | Klikwit')
@section('description', 'Check a page\'s title, meta description, canonical URL, robots tag, viewport, H1 and Open Graph tags for free. No signup required.')
@section('page-class', 'wide')
@section('extra-style')
@endsection

@section('content')
<div class="tool-section">
  @include('partials.seo-breadcrumb', ['crumbs' => [
    ['label' => 'Home', 'url' => route('home')],
    ['label' => 'SEO Tools', 'url' => route('seo-tools.index')],
    ['label' => 'Meta Tag Checker'],
  ]])

  <h1>Meta Tag Checker</h1>
  <p>Paste a URL below to check its title tag, meta description, canonical tag, robots meta tag, viewport, language, H1 heading, and basic Open Graph tags — all read directly from the page's HTML.</p>

  <div class="card">
    <form id="f">
      <label for="url">Page URL</label>
      <input id="url" type="url" placeholder="https://example.com/your-page" required/>
      <button id="btn" type="submit">Check Meta Tags</button>
      <div class="error" id="err"></div>
    </form>
    <div class="tool-result" id="result" style="display:none"></div>
  </div>

  <h2>What Is a Meta Tag Checker?</h2>
  <p>A meta tag checker reads a web page's HTML and reports which important SEO-related tags are present — things like the title tag, meta description, and canonical URL — so you can quickly see what search engines and social platforms will actually read from your page.</p>

  <h2>Why Is It Useful?</h2>
  <p>It's easy to publish a page and forget to fill in a description, or to accidentally duplicate a title across several pages. This tool gives you a fast way to spot missing or unusual tags without opening "View Source" and reading raw HTML yourself.</p>

  <h2>How to Use It</h2>
  <p>Paste the full URL of a public page (including <code>https://</code>) and click "Check Meta Tags." The tool fetches the page, reads its <code>&lt;head&gt;</code> section, and shows a pass/warning/missing result for each tag checked.</p>

  <h2>How to Interpret the Results</h2>
  <p><strong>PASS</strong> means the tag is present and looks reasonable. <strong>WARNING</strong> means the tag exists but something about it is worth a second look (for example, a title that's unusually long or short). <strong>MISSING</strong> means the tag wasn't found at all. None of these are a guarantee of ranking — they're a checklist of common technical basics.</p>

  <h2>Common Mistakes</h2>
  <ul>
    <li>Leaving the meta description empty, so search engines generate their own snippet from page content.</li>
    <li>Using the exact same title tag on many different pages.</li>
    <li>Writing a title so long it gets cut off in search results on some devices.</li>
    <li>Forgetting Open Graph tags, so shared links on social media show no image or a generic one.</li>
  </ul>

  <h2>Limitations</h2>
  <p>This tool reads whatever HTML the page returns to a normal request. If a page builds its content with JavaScript after the page loads, tags added that way may not appear here. It also can't check pages that require a login, or pages on localhost or a private network.</p>

  @include('partials.seo-faq', ['faqs' => [
    ['q' => 'What is a meta title?', 'a' => 'The meta title (or title tag) is the text usually shown as the clickable headline in search results and as the browser tab title. It\'s set with the HTML <title> tag.'],
    ['q' => 'What is a meta description?', 'a' => 'A short summary of the page, set with <meta name="description">. Search engines often, but not always, show it beneath the title in search results.'],
    ['q' => 'Does a meta description directly improve rankings?', 'a' => 'Not directly — most search engines don\'t use the meta description as a ranking factor. It can still influence whether someone clicks your result, which matters indirectly.'],
    ['q' => 'Why is my meta description missing?', 'a' => 'Either the <meta name="description"> tag was never added to the page, or it\'s present but empty. Some page builders and CMS themes don\'t add one unless you fill it in manually.'],
    ['q' => 'Can search engines rewrite my snippet?', 'a' => 'Yes. Search engines sometimes choose to show different text than your meta description if they judge another part of the page better answers the search query.'],
    ['q' => 'Why does this tool show different results than Google Search Console?', 'a' => 'This tool reads the raw HTML your server returns to a normal request. Google Search Console reflects what Google\'s own crawler saw and decided to use, which can differ — especially for JavaScript-heavy pages.'],
  ]])

  @include('partials.seo-related-tools', ['tools' => [
    ['label' => 'SERP Preview', 'url' => route('seo-tools.serp-preview'), 'icon' => '🔎'],
    ['label' => 'Open Graph Checker', 'url' => route('seo-tools.open-graph-checker'), 'icon' => '🖼️'],
    ['label' => 'Canonical Checker', 'url' => route('seo-tools.canonical-checker'), 'icon' => '🧭'],
    ['label' => 'SEO URL Checker', 'url' => route('seo-tools.seo-url-checker'), 'icon' => '🔗'],
  ]])

  <div class="final-cta" style="margin-top:36px">
    <h2 style="color:#fff">Also Managing Links?</h2>
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
    fetch('{{ url('/api/seo/meta-tag-checker') }}', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ url: document.getElementById('url').value.trim() })
    }).then(function(r){ return r.json().then(function(j){ return {ok:r.ok, body:j}; }); })
      .then(function(res){
        btn.disabled = false;
        btn.textContent = 'Check Meta Tags';
        if (!res.ok || !res.body.checks) {
          err.textContent = (res.body && res.body.error) ? res.body.error : 'Something went wrong.';
          return;
        }
        result.style.display = 'block';
        renderSeoResults(result, res.body.checks);
      }).catch(function(){
        btn.disabled = false;
        btn.textContent = 'Check Meta Tags';
        err.textContent = 'Something went wrong. Please try again.';
      });
  });
})();
</script>
@endsection
