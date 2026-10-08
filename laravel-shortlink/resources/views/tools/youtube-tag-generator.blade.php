@extends('layouts.app')

@section('title', 'YouTube Tag & Title Generator | Klikwit')
@section('description', 'Generate YouTube video tags from real YouTube search suggestions, plus title ideas from proven formulas. Free, no signup.')
@section('page-class', 'wide')
@section('extra-style')
  .yt-chip-list{display:flex;flex-wrap:wrap;gap:8px;margin-top:8px}
  .yt-chip{background:#fff;border:1px solid #e0e0e0;border-radius:20px;padding:8px 14px;font-size:13px;color:#222}
  .yt-title-list{list-style:none;padding:0;margin:8px 0 0}
  .yt-title-list li{background:#fff;border:1px solid #eee;border-radius:8px;padding:10px 14px;margin-top:8px;font-size:14px}
  .yt-copy-all{width:auto;margin:14px 0 0;padding:8px 16px;font-size:13px}
  .yt-section-head{display:flex;justify-content:space-between;align-items:center;margin-top:24px}
  .yt-section-head h3{margin:0}
@endsection

@section('content')
<div class="tool-section">
  @include('partials.seo-breadcrumb', ['crumbs' => [
    ['label' => 'Home', 'url' => route('home')],
    ['label' => 'SEO Tools', 'url' => route('seo-tools.index')],
    ['label' => 'YouTube Tag & Title Generator'],
  ]])

  <h1>YouTube Tag & Title Generator</h1>
  <p>Enter your video's topic to get real tag suggestions pulled from YouTube's own search data, plus a set of title ideas built from proven formulas.</p>

  <div class="card">
    <form id="f">
      <label for="topic">Video Topic</label>
      <input id="topic" type="text" placeholder="e.g. home workout" maxlength="80" required/>
      <button id="btn" type="submit">Generate</button>
      <div class="error" id="err"></div>
    </form>
    <div class="tool-result" id="result" style="display:none">
      <div class="yt-section-head">
        <h3>Tags</h3>
        <button type="button" class="yt-copy-all" id="copyTagsBtn">Copy All Tags</button>
      </div>
      <div class="error" id="tagsErr" style="display:none"></div>
      <div class="yt-chip-list" id="tagsList"></div>

      <div class="yt-section-head">
        <h3>Title Ideas</h3>
      </div>
      <ul class="yt-title-list" id="titlesList"></ul>
    </div>
  </div>

  <div class="notice-box">
    Tags are real suggestions from YouTube's own search autocomplete — not estimated search volume. Title ideas are built from common title formulas, not AI-generated; use them as a starting point and adjust to fit your actual video.
  </div>

  <h2>What Does This Tool Do?</h2>
  <p>It pulls real related searches from YouTube's own autocomplete for your topic — useful as tag candidates, since they reflect what people actually search for on YouTube — and generates a set of title ideas using well-known title formulas (how-to, listicle, guide, and so on).</p>

  <h2>Why Is It Useful?</h2>
  <p>Good tags help YouTube understand what your video is about and what it's similar to; a strong title affects click-through rate directly. This gives you a quick starting set of both, grounded in real search phrasing rather than guesses.</p>

  <h2>How to Use It</h2>
  <p>Type your video's topic and click "Generate." Copy the tags you want into YouTube Studio's tags field, and pick (or adapt) a title idea that fits your actual video content — never use a title that overpromises what the video delivers.</p>

  <h2>Limitations</h2>
  <p>Tag suggestions are real search phrases, not a ranked list by importance — use judgment about which are actually relevant to your video. Title ideas are formulas filled in with your topic, not a creative or factual check — always verify a generated title accurately represents your content before publishing.</p>

  @include('partials.seo-faq', ['faqs' => [
    ['q' => 'Are these tags guaranteed to help my video rank?', 'a' => 'No tool can guarantee that. These are real, relevant search phrases to consider as tags — how much any single tag helps depends on many other factors.'],
    ['q' => 'Should I use every tag suggested?', 'a' => 'No — pick the ones genuinely relevant to your specific video. Irrelevant tags don\'t help and can look like keyword stuffing.'],
    ['q' => 'Are the title ideas written by AI?', 'a' => 'No — they\'re generated from fixed title formulas with your topic filled in, not by an AI model. Treat them as starting templates to adapt, not finished titles.'],
    ['q' => 'Why do tag suggestions sometimes look similar to each other?', 'a' => 'YouTube\'s autocomplete reflects real common searches, which often cluster around popular phrasings for a topic.'],
  ]])

  @include('partials.seo-related-tools', ['tools' => [
    ['label' => 'Keyword Suggestions', 'url' => route('seo-tools.keyword-suggestions'), 'icon' => '💡'],
    ['label' => 'SERP Preview', 'url' => route('seo-tools.serp-preview'), 'icon' => '🔎'],
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
  var form = document.getElementById('f');
  var btn = document.getElementById('btn');
  var err = document.getElementById('err');
  var result = document.getElementById('result');
  var tagsList = document.getElementById('tagsList');
  var tagsErr = document.getElementById('tagsErr');
  var titlesList = document.getElementById('titlesList');
  var copyTagsBtn = document.getElementById('copyTagsBtn');
  var lastTags = [];

  form.addEventListener('submit', function(e){
    e.preventDefault();
    err.textContent = '';
    result.style.display = 'none';
    btn.disabled = true;
    btn.textContent = 'Generating...';
    fetch('{{ url('/api/youtube/generate') }}', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ topic: document.getElementById('topic').value.trim() })
    }).then(function(r){ return r.json().then(function(j){ return {ok:r.ok, body:j}; }); })
      .then(function(res){
        btn.disabled = false;
        btn.textContent = 'Generate';
        if (!res.ok || !res.body.titles) {
          err.textContent = (res.body && res.body.error) ? res.body.error : 'Something went wrong.';
          return;
        }

        tagsList.textContent = '';
        lastTags = res.body.tags || [];
        if (res.body.tags_error) {
          tagsErr.textContent = res.body.tags_error;
          tagsErr.style.display = 'block';
        } else {
          tagsErr.style.display = 'none';
          lastTags.forEach(function(tag){
            var chip = document.createElement('span');
            chip.className = 'yt-chip';
            chip.textContent = tag;
            tagsList.appendChild(chip);
          });
        }

        titlesList.textContent = '';
        (res.body.titles || []).forEach(function(title){
          var li = document.createElement('li');
          li.textContent = title;
          titlesList.appendChild(li);
        });

        result.style.display = 'block';
      }).catch(function(){
        btn.disabled = false;
        btn.textContent = 'Generate';
        err.textContent = 'Something went wrong. Please try again.';
      });
  });

  copyTagsBtn.addEventListener('click', function(){
    if (!lastTags.length) return;
    navigator.clipboard.writeText(lastTags.join(', ')).then(function(){
      copyTagsBtn.textContent = 'Copied!';
      setTimeout(function(){ copyTagsBtn.textContent = 'Copy All Tags'; }, 1500);
    });
  });
})();
</script>
@endsection
