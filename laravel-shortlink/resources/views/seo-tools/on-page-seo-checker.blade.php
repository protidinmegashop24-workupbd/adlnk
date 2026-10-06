@extends('layouts.app')

@section('title', 'On-Page SEO Checker | Klikwit')
@section('description', 'Check a page\'s on-page SEO in one pass: title, meta description, headings, word count, image alt text, internal links, and optional keyword usage. Free, no signup.')
@section('page-class', 'wide')

@section('content')
<div class="tool-section">
  @include('partials.seo-breadcrumb', ['crumbs' => [
    ['label' => 'Home', 'url' => route('home')],
    ['label' => 'SEO Tools', 'url' => route('seo-tools.index')],
    ['label' => 'On-Page SEO Checker'],
  ]])

  <h1>On-Page SEO Checker</h1>
  <p>Paste a URL to run a full on-page SEO pass in one go: title and meta description, heading structure, word count, image alt text coverage, internal/external links, and — if you give it a target keyword — where that keyword actually shows up on the page.</p>

  <div class="card">
    <form id="f">
      <label for="url">Page URL</label>
      <input id="url" type="url" placeholder="https://example.com/your-page" required/>
      <label for="keyword">Target Keyword <span class="hint" style="display:inline">(optional)</span></label>
      <input id="keyword" type="text" placeholder="e.g. url shortener" maxlength="80"/>
      <button id="btn" type="submit">Run Check</button>
      <div class="error" id="err"></div>
    </form>
    <div class="tool-result" id="result" style="display:none"></div>
  </div>

  <h2>What Is an On-Page SEO Checker?</h2>
  <p>It's a single pass over everything on a page that's directly under your control: title and meta tags, heading structure, how much content is actually there, whether images have alt text, and how the page links internally — the "on-page" factors, as opposed to off-page signals like backlinks.</p>

  <h2>Why Is It Useful?</h2>
  <p>Instead of running five separate checks, this combines the most common on-page signals into one report, so you can see the overall health of a page at a glance before digging into any single issue with a more specialized tool.</p>

  <h2>How to Use It</h2>
  <p>Paste a public page URL and click "Run Check." Add a target keyword if you want to see where (if anywhere) it appears in the title, heading, meta description, and body text.</p>

  <h2>How to Interpret the Results</h2>
  <p>A "warning" doesn't always mean something is broken — short word counts or missing subheadings can be perfectly fine for simple pages. Use this as a checklist to review deliberately, not a score to chase.</p>

  <h2>Limitations</h2>
  <p>This only reads server-rendered HTML — content added by JavaScript after load may not be seen. It does not measure rankings, traffic, backlinks, or page speed, and the target keyword check is a simple text match, not a relevance or intent analysis.</p>

  @include('partials.seo-faq', ['faqs' => [
    ['q' => 'Does this check search rankings or traffic?', 'a' => 'No. It only reports what\'s present in the page\'s own HTML — titles, headings, content length, links, and so on. It can\'t see ranking position or visitor numbers.'],
    ['q' => 'Why no overall score?', 'a' => 'A single score hides which specific things matter for your page. We\'d rather show you each finding individually so you can judge what\'s actually worth fixing.'],
    ['q' => 'Is a low word count always bad?', 'a' => 'Not necessarily — it depends on the page\'s purpose. A contact page needs far less text than an in-depth guide. Use judgment alongside this check.'],
    ['q' => 'What counts as an "internal" link?', 'a' => 'Any link pointing to the same host as the page you checked (after following redirects). Everything else is counted as external.'],
  ]])

  @include('partials.seo-related-tools', ['tools' => [
    ['label' => 'Meta Tag Checker', 'url' => route('seo-tools.meta-tag-checker'), 'icon' => '🔍'],
    ['label' => 'Keyword Density Checker', 'url' => route('seo-tools.keyword-density-checker'), 'icon' => '📊'],
    ['label' => 'Keyword Suggestions', 'url' => route('seo-tools.keyword-suggestions'), 'icon' => '💡'],
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
    fetch('{{ url('/api/seo/on-page-checker') }}', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({
        url: document.getElementById('url').value.trim(),
        keyword: document.getElementById('keyword').value.trim()
      })
    }).then(function(r){ return r.json().then(function(j){ return {ok:r.ok, body:j}; }); })
      .then(function(res){
        btn.disabled = false;
        btn.textContent = 'Run Check';
        if (!res.ok || !res.body.checks) {
          err.textContent = (res.body && res.body.error) ? res.body.error : 'Something went wrong.';
          return;
        }
        result.style.display = 'block';
        renderSeoResults(result, res.body.checks);
      }).catch(function(){
        btn.disabled = false;
        btn.textContent = 'Run Check';
        err.textContent = 'Something went wrong. Please try again.';
      });
  });
})();
</script>
@endsection
