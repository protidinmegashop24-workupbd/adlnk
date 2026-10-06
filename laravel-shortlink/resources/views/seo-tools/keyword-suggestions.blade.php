@extends('layouts.app')

@section('title', 'Keyword Suggestions Tool | Klikwit')
@section('description', 'Find related keywords and search suggestions for any topic, pulled from real Google autocomplete data. Free, no signup.')
@section('page-class', 'wide')
@section('extra-style')
  .ks-group{margin-top:20px}
  .ks-group h3{font-size:14px;text-transform:uppercase;letter-spacing:.03em;color:#888;margin:0 0 10px}
  .ks-chip-list{display:flex;flex-wrap:wrap;gap:8px}
  .ks-chip{background:#fff;border:1px solid #e0e0e0;border-radius:20px;padding:8px 14px;font-size:13px;color:#222}
  .ks-copy-all{width:auto;margin:16px 0 0;padding:8px 16px;font-size:13px}
@endsection

@section('content')
<div class="tool-section">
  @include('partials.seo-breadcrumb', ['crumbs' => [
    ['label' => 'Home', 'url' => route('home')],
    ['label' => 'SEO Tools', 'url' => route('seo-tools.index')],
    ['label' => 'Keyword Suggestions'],
  ]])

  <h1>Keyword Suggestions Tool</h1>
  <p>Enter a topic or seed keyword to see related searches people actually type into Google — pulled live from Google's own autocomplete data, plus a handful of question and comparison variations (how, what, best, vs, near me, and more).</p>

  <div class="card">
    <form id="f">
      <label for="kw">Seed Keyword</label>
      <input id="kw" type="text" placeholder="e.g. url shortener" maxlength="80" required/>
      <button id="btn" type="submit">Find Keywords</button>
      <div class="error" id="err"></div>
    </form>
    <div class="tool-result" id="result" style="display:none">
      <button type="button" class="ks-copy-all" id="copyAllBtn">Copy All</button>
      <div id="groups"></div>
    </div>
  </div>

  <div class="notice-box">
    This tool shows real related searches from Google autocomplete — it does <strong>not</strong> show search volume, keyword difficulty, or CPC. Those numbers come from paid data providers (Ahrefs, SEMrush, and similar), and we'd rather show you nothing than a made-up number.
  </div>

  <h2>What Is a Keyword Suggestions Tool?</h2>
  <p>It takes a seed topic and returns related phrases that real people search for, sourced from the same autocomplete system you see when typing into Google's search box — plus a few common question and comparison variations to widen the list.</p>

  <h2>Why Is It Useful?</h2>
  <p>It helps you discover the actual words and phrases your audience uses, including long-tail variations (questions, comparisons, "near me" searches) you might not have thought of on your own — useful for blog topic ideas, headings, and FAQ sections.</p>

  <h2>How to Use It</h2>
  <p>Type a topic or seed keyword and click "Find Keywords." Results are grouped by the modifier used to find them (plain related searches, "how," "best," "vs," and so on). Click "Copy All" to copy every suggestion at once.</p>

  <h2>How to Interpret the Results</h2>
  <p>Every suggestion here is a real autocomplete result — not an estimate or a guess. There's no volume or difficulty score attached, so use this list to find angles and phrasing for your content, then judge relevance using your own knowledge of your audience.</p>

  <h2>Limitations</h2>
  <p>No search volume, competition, or trend data — this tool only surfaces what people search, not how often. Results can vary by region and language, and Google's autocomplete data is occasionally unavailable or rate-limited, in which case this tool will show an error rather than a guess.</p>

  @include('partials.seo-faq', ['faqs' => [
    ['q' => 'Why doesn\'t this show search volume?', 'a' => 'Accurate search volume requires a paid data provider with access to real query logs. We don\'t fabricate numbers, so we only show what we can verify is real: actual autocomplete suggestions.'],
    ['q' => 'Where do these suggestions come from?', 'a' => 'Google\'s own public autocomplete endpoint — the same data source behind the suggestions you see typing into google.com.'],
    ['q' => 'Can I use these as exact keywords in my content?', 'a' => 'They\'re a starting point for topics and phrasing. Always write naturally for your actual audience rather than forcing an exact phrase in.'],
    ['q' => 'Why do some searches return more results than others?', 'a' => 'Autocomplete favors popular, commonly-typed queries. A very niche or unusual seed keyword may return fewer (or no) suggestions for some modifiers.'],
  ]])

  @include('partials.seo-related-tools', ['tools' => [
    ['label' => 'Keyword Density Checker', 'url' => route('seo-tools.keyword-density-checker'), 'icon' => '📊'],
    ['label' => 'Word Counter', 'url' => route('seo-tools.word-counter'), 'icon' => '🔢'],
    ['label' => 'Slug Generator', 'url' => route('seo-tools.slug-generator'), 'icon' => '✂️'],
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
  var groupsEl = document.getElementById('groups');
  var copyAllBtn = document.getElementById('copyAllBtn');
  var lastSuggestions = [];

  function renderGroups(groups) {
    groupsEl.innerHTML = '';
    lastSuggestions = [];

    Object.keys(groups).forEach(function(label){
      var section = document.createElement('div');
      section.className = 'ks-group';

      var heading = document.createElement('h3');
      heading.textContent = label;
      section.appendChild(heading);

      var list = document.createElement('div');
      list.className = 'ks-chip-list';

      groups[label].forEach(function(suggestion){
        var chip = document.createElement('span');
        chip.className = 'ks-chip';
        chip.textContent = suggestion;
        list.appendChild(chip);
        lastSuggestions.push(suggestion);
      });

      section.appendChild(list);
      groupsEl.appendChild(section);
    });
  }

  form.addEventListener('submit', function(e){
    e.preventDefault();
    err.textContent = '';
    result.style.display = 'none';
    btn.disabled = true;
    btn.textContent = 'Searching...';
    fetch('{{ url('/api/seo/keyword-suggestions') }}', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ keyword: document.getElementById('kw').value.trim() })
    }).then(function(r){ return r.json().then(function(j){ return {ok:r.ok, body:j}; }); })
      .then(function(res){
        btn.disabled = false;
        btn.textContent = 'Find Keywords';
        if (!res.ok || !res.body.groups) {
          err.textContent = (res.body && res.body.error) ? res.body.error : 'Something went wrong.';
          return;
        }
        result.style.display = 'block';
        renderGroups(res.body.groups);
      }).catch(function(){
        btn.disabled = false;
        btn.textContent = 'Find Keywords';
        err.textContent = 'Something went wrong. Please try again.';
      });
  });

  copyAllBtn.addEventListener('click', function(){
    if (!lastSuggestions.length) return;
    navigator.clipboard.writeText(lastSuggestions.join('\n')).then(function(){
      copyAllBtn.textContent = 'Copied!';
      setTimeout(function(){ copyAllBtn.textContent = 'Copy All'; }, 1500);
    });
  });
})();
</script>
@endsection
