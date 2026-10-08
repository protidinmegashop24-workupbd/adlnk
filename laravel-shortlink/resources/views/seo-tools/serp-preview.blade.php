@extends('layouts.app')

@section('title', 'SERP Preview Tool - Search Result Snippet Preview | Klikwit')
@section('description', 'Preview how your title, description and URL might look as a search result snippet, on both desktop and mobile. Free, no signup.')
@section('page-class', 'wide')
@section('extra-style')
  @media (max-width:480px){.serp-preview-card .sp-title{font-size:16px}}
  .serp-preview-card.mobile{max-width:380px}
  .serp-preview-card.mobile .sp-title{font-size:16px}
  .serp-preview-card .sp-title-text,.serp-preview-card .sp-desc-text{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
  .serp-preview-card .sp-title-text{-webkit-line-clamp:1}
  .char-count.good{color:#1a7f37}
  .char-count.warn{color:#b8860b}
@endsection

@section('content')
<div class="tool-section">
  @include('partials.seo-breadcrumb', ['crumbs' => [
    ['label' => 'Home', 'url' => route('home')],
    ['label' => 'SEO Tools', 'url' => route('seo-tools.index')],
    ['label' => 'SERP Preview'],
  ]])

  <h1>SERP Preview Tool</h1>
  <p>See a visual preview of how your title and description might look as a search result snippet, on both desktop and mobile widths — with a live character-length check so you know before publishing whether either is likely to get cut off.</p>

  <div class="card">
    <label for="serpUrl">URL</label>
    <input id="serpUrl" type="text" placeholder="https://example.com/your-page"/>
    <label for="serpTitle">Title</label>
    <input id="serpTitle" type="text" placeholder="Your page title"/>
    <div class="char-count" id="titleCount">0 characters</div>
    <label for="serpDesc">Meta Description</label>
    <textarea id="serpDesc" rows="3" placeholder="Your meta description"></textarea>
    <div class="char-count" id="descCount">0 characters</div>
  </div>

  <div class="serp-tabs">
    <button type="button" class="active" id="tabDesktop">Desktop</button>
    <button type="button" id="tabMobile">Mobile</button>
  </div>

  <div class="serp-preview-card" id="previewCard">
    <div class="sp-url" id="pUrl">https://example.com</div>
    <div class="sp-title"><span class="sp-title-text" id="pTitle">Your title will appear here</span></div>
    <div class="sp-desc"><span class="sp-desc-text" id="pDesc">Your meta description will appear here.</span></div>
  </div>

  <p class="hint" style="text-align:center">This is a visual preview only. Search engines may display a different title, description, or URL format than what you enter here — they can rewrite snippets or show different content depending on the query.</p>

  <h2>What Is a SERP Preview Tool?</h2>
  <p>SERP stands for "search engine results page." This tool renders your title, description and URL in a layout similar to a typical search result, so you can see roughly how they fit together before publishing.</p>

  <h2>Why Is It Useful?</h2>
  <p>It's hard to judge how a title or description will "read" as a search snippet just by looking at a plain text field. Seeing it styled and sized like an actual result makes it easier to spot an awkward cutoff or a title that doesn't quite make sense when truncated.</p>

  <h2>How to Use It</h2>
  <p>Type or paste your intended URL, title, and meta description. The preview updates as you type. Switch between the Desktop and Mobile tabs to see how the available width changes what fits on one line.</p>

  <h2>How to Interpret the Results</h2>
  <p>If your title or description looks cut off in the preview, it may also get cut off in actual search results — though the exact cutoff point varies by device, font, and the search engine itself, so treat this as a helpful approximation rather than an exact measurement.</p>

  <h2>Common Mistakes</h2>
  <ul>
    <li>Front-loading a title with your brand name, pushing the actually useful part off the visible end.</li>
    <li>Writing a description so long its most important sentence gets cut off.</li>
    <li>Using a URL with long, unreadable query strings or ID numbers in the visible path.</li>
  </ul>

  <h2>Limitations</h2>
  <p>This tool does not check search engines or fetch real ranking data — it's a layout preview built purely from what you type in. Search engines sometimes rewrite titles or descriptions entirely based on the search query, which this tool can't predict.</p>

  @include('partials.seo-faq', ['faqs' => [
    ['q' => 'Is this an official Google preview tool?', 'a' => 'No. This is klikwit\'s own visual approximation, not an official tool from Google or any other search engine.'],
    ['q' => 'Why did my title get cut off at a different length than expected?', 'a' => 'Search engines measure truncation by rendered pixel width, not a fixed character count — different letters take up different amounts of space, so there\'s no single "safe" character number.'],
    ['q' => 'Should I always keep my title short?', 'a' => 'Not necessarily — a short, clear title that fully displays is often better than a long one that gets cut off mid-thought, but there\'s no universal "correct" length for every situation.'],
    ['q' => 'Does this tool check my live website?', 'a' => 'No — this tool only uses the text you type into the form. Use the Meta Tag Checker if you want to check what\'s actually on a live page.'],
  ]])

  @include('partials.seo-related-tools', ['tools' => [
    ['label' => 'Meta Tag Checker', 'url' => route('seo-tools.meta-tag-checker'), 'icon' => '🔍'],
    ['label' => 'Open Graph Checker', 'url' => route('seo-tools.open-graph-checker'), 'icon' => '🖼️'],
    ['label' => 'Word Counter', 'url' => route('seo-tools.word-counter'), 'icon' => '🔢'],
  ]])

  <div class="final-cta">
    <h2>Also Managing Links?</h2>
    <p>klikwit's free URL shortener, QR codes and link analytics live right alongside these SEO tools.</p>
    <a class="cta-btn" href="{{ route('home') }}">Try the URL Shortener</a>
  </div>
</div>

<script>
(function(){
  var urlInput = document.getElementById('serpUrl');
  var titleInput = document.getElementById('serpTitle');
  var descInput = document.getElementById('serpDesc');
  var titleCount = document.getElementById('titleCount');
  var descCount = document.getElementById('descCount');
  var pUrl = document.getElementById('pUrl');
  var pTitle = document.getElementById('pTitle');
  var pDesc = document.getElementById('pDesc');
  var card = document.getElementById('previewCard');
  var tabDesktop = document.getElementById('tabDesktop');
  var tabMobile = document.getElementById('tabMobile');

  function update() {
    var url = urlInput.value.trim() || 'https://example.com/your-page';
    var title = titleInput.value.trim();
    var desc = descInput.value.trim();

    pTitle.textContent = title || 'Your title will appear here';
    pDesc.textContent = desc || 'Your meta description will appear here.';

    var titleLen = title.length;
    var titleGood = titleLen > 0 && titleLen <= 60 && titleLen >= 10;
    titleCount.textContent = titleLen + ' characters' + (titleLen > 0 ? (titleGood ? ' — good length' : (titleLen > 60 ? ' — may get cut off' : ' — quite short')) : '');
    titleCount.className = 'char-count' + (titleLen > 0 ? (titleGood ? ' good' : ' warn') : '');

    var descLen = desc.length;
    var descGood = descLen > 0 && descLen <= 160 && descLen >= 50;
    descCount.textContent = descLen + ' characters' + (descLen > 0 ? (descGood ? ' — good length' : (descLen > 160 ? ' — may get cut off' : ' — quite short')) : '');
    descCount.className = 'char-count' + (descLen > 0 ? (descGood ? ' good' : ' warn') : '');

    try {
      var u = new URL(url);
      var path = u.pathname === '/' ? '' : u.pathname;
      pUrl.textContent = u.hostname + (path ? ' › ' + path.replace(/^\//, '').replace(/\//g, ' › ') : '');
    } catch (e) {
      pUrl.textContent = url;
    }
  }

  [urlInput, titleInput, descInput].forEach(function (el) {
    el.addEventListener('input', update);
  });

  tabDesktop.addEventListener('click', function () {
    tabDesktop.classList.add('active');
    tabMobile.classList.remove('active');
    card.classList.remove('mobile');
  });
  tabMobile.addEventListener('click', function () {
    tabMobile.classList.add('active');
    tabDesktop.classList.remove('active');
    card.classList.add('mobile');
  });

  update();
})();
</script>
@endsection
