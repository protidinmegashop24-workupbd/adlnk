@extends('layouts.app')

@section('title', 'Open Graph Checker - Social Share Preview | Klikwit')
@section('description', 'Check a page\'s Open Graph and Twitter/X card tags and preview roughly how a shared link might look. Free, no signup.')
@section('page-class', 'wide')

@section('content')
<div class="tool-section">
  @include('partials.seo-breadcrumb', ['crumbs' => [
    ['label' => 'Home', 'url' => route('home')],
    ['label' => 'SEO Tools', 'url' => route('seo-tools.index')],
    ['label' => 'Open Graph Checker'],
  ]])

  <h1>Open Graph Checker</h1>
  <p>Paste a URL to check its Open Graph and Twitter/X card tags, and see a rough preview of what a shared link might look like.</p>

  <div class="card">
    <form id="f">
      <label for="url">Page URL</label>
      <input id="url" type="url" placeholder="https://example.com/your-page" required/>
      <button id="btn" type="submit">Check Open Graph</button>
      <div class="error" id="err"></div>
    </form>
    <div id="previewWrap" style="display:none">
      <div class="social-preview-card" id="previewCard">
        <img class="sp-image" id="previewImg" alt="" style="display:none"/>
        <div class="sp-body">
          <div class="sp-domain" id="previewDomain"></div>
          <div class="sp-title" id="previewTitle"></div>
          <div class="sp-desc" id="previewDesc"></div>
        </div>
      </div>
    </div>
    <div class="tool-result" id="result" style="display:none"></div>
  </div>

  <p class="hint" style="text-align:center">This is an approximate preview built from the tags found on the page. Actual appearance varies by platform (Facebook, LinkedIn, X, etc.) and can change without notice — this isn't an official preview from any of them.</p>

  <h2>What Are Open Graph and Twitter/X Card Tags?</h2>
  <p>Open Graph tags (like <code>og:title</code> and <code>og:image</code>) are meta tags that control how a link looks when shared on platforms like Facebook and LinkedIn. Twitter/X reads its own similar set of tags, falling back to Open Graph tags when a Twitter-specific one is missing.</p>

  <h2>Why Is It Useful?</h2>
  <p>A link shared without these tags often shows up as a bare title with no image, which gets far less attention than a card with a clear title, description, and image. Checking them before sharing helps catch a missing or broken image ahead of time.</p>

  <h2>How to Use It</h2>
  <p>Paste a public page's URL and click "Check Open Graph." The tool reads the page's Open Graph and Twitter tags and shows which are present, plus a preview built from whichever tags it found (falling back sensibly when some are missing).</p>

  <h2>How to Interpret the Results</h2>
  <p><strong>PASS</strong> means the tag was found. <strong>MISSING</strong> means it wasn't, but note that Twitter/X tags specifically fall back to their Open Graph equivalent — so a missing <code>twitter:title</code> isn't a problem if <code>og:title</code> is set.</p>

  <h2>Common Mistakes</h2>
  <ul>
    <li>No <code>og:image</code>, so shared links show as plain text.</li>
    <li>An <code>og:image</code> pointing to a broken or removed image URL.</li>
    <li>Using a relative image path instead of a full URL starting with <code>https://</code>.</li>
    <li>An image that's too small — most platforms prefer at least 1200×630px.</li>
  </ul>

  <h2>Limitations</h2>
  <p>This shows what's in your page's HTML, not a guaranteed rendering from any specific platform — Facebook, LinkedIn, and X each cache and render previews a little differently, and may cache an old version of your tags until their cache expires.</p>

  @include('partials.seo-faq', ['faqs' => [
    ['q' => 'What size should og:image be?', 'a' => 'Most platforms recommend at least 1200×630 pixels for a crisp, properly-cropped preview, with an aspect ratio close to 1.91:1.'],
    ['q' => 'Why does Facebook show an old image after I changed it?', 'a' => 'Facebook and other platforms cache share previews. You may need to use their respective debugging/cache-clearing tools to force a refresh.'],
    ['q' => 'Do I need both Open Graph and Twitter tags?', 'a' => 'Not strictly — Twitter/X falls back to Open Graph tags when its own are missing. Adding dedicated Twitter tags gives you more control but isn\'t required.'],
    ['q' => 'Will social platforms always show exactly what my tags say?', 'a' => 'No. Platforms can truncate text, resize or decline an image, or occasionally substitute their own content if yours looks incomplete.'],
    ['q' => 'Is this an official Facebook or X preview tool?', 'a' => 'No — this is klikwit\'s own approximation based on the tags found on your page, not an official tool from any social platform.'],
  ]])

  @include('partials.seo-related-tools', ['tools' => [
    ['label' => 'Meta Tag Checker', 'url' => route('seo-tools.meta-tag-checker'), 'icon' => '🔍'],
    ['label' => 'SERP Preview', 'url' => route('seo-tools.serp-preview'), 'icon' => '🔎'],
    ['label' => 'Canonical Checker', 'url' => route('seo-tools.canonical-checker'), 'icon' => '🧭'],
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
  var previewWrap = document.getElementById('previewWrap');
  var previewImg = document.getElementById('previewImg');
  var previewDomain = document.getElementById('previewDomain');
  var previewTitle = document.getElementById('previewTitle');
  var previewDesc = document.getElementById('previewDesc');

  function isSafeHttpUrl(value) {
    return typeof value === 'string' && /^https?:\/\//i.test(value);
  }

  form.addEventListener('submit', function(e){
    e.preventDefault();
    err.textContent = '';
    result.style.display = 'none';
    previewWrap.style.display = 'none';
    btn.disabled = true;
    btn.textContent = 'Checking...';
    var typedUrl = document.getElementById('url').value.trim();
    fetch('{{ url('/api/seo/open-graph-checker') }}', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ url: typedUrl })
    }).then(function(r){ return r.json().then(function(j){ return {ok:r.ok, body:j}; }); })
      .then(function(res){
        btn.disabled = false;
        btn.textContent = 'Check Open Graph';
        if (!res.ok || !res.body.checks) {
          err.textContent = (res.body && res.body.error) ? res.body.error : 'Something went wrong.';
          return;
        }

        var preview = res.body.preview || {};
        previewTitle.textContent = preview.title || '(no title found)';
        previewDesc.textContent = preview.description || '';
        try {
          previewDomain.textContent = new URL(res.body.final_url).hostname;
        } catch (e) {
          previewDomain.textContent = '';
        }
        if (isSafeHttpUrl(preview.image)) {
          previewImg.src = preview.image;
          previewImg.style.display = 'block';
          previewImg.onerror = function () { previewImg.style.display = 'none'; };
        } else {
          previewImg.removeAttribute('src');
          previewImg.style.display = 'none';
        }
        previewWrap.style.display = 'block';

        result.style.display = 'block';
        renderSeoResults(result, res.body.checks);
      }).catch(function(){
        btn.disabled = false;
        btn.textContent = 'Check Open Graph';
        err.textContent = 'Something went wrong. Please try again.';
      });
  });
})();
</script>
@endsection
