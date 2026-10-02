@extends('layouts.app')

@section('title', 'Keyword Density Checker | Klikwit')
@section('description', 'Check how often words and phrases repeat in your text, and measure the density of a target keyword. Free, runs entirely in your browser.')
@section('page-class', 'wide')
@section('extra-style')
  .kd-columns{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:16px}
  @media (max-width:640px){.kd-columns{grid-template-columns:1fr}}
  .kd-list{list-style:none;padding:0;margin:8px 0 0}
  .kd-list li{display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #f1f1f1;font-size:14px}
  .kd-list li:last-child{border-bottom:0}
  .kd-list .kd-count{color:#888;font-size:13px}
@endsection

@section('content')
<div class="tool-section">
  @include('partials.seo-breadcrumb', ['crumbs' => [
    ['label' => 'Home', 'url' => route('home')],
    ['label' => 'SEO Tools', 'url' => route('seo-tools.index')],
    ['label' => 'Keyword Density Checker'],
  ]])

  <h1>Keyword Density Checker</h1>
  <p>Paste your text below to see how often words and phrases repeat, and optionally measure the density of a specific target keyword. Everything runs in your browser — your text is never sent to a server.</p>

  <div class="card">
    <label for="kdText">Text</label>
    <textarea id="kdText" rows="10" placeholder="Paste your article, page content, or any text here..."></textarea>
    <label for="kdKeyword">Target Keyword (optional)</label>
    <input id="kdKeyword" type="text" placeholder="e.g. url shortener"/>
  </div>

  <div class="stat-grid" id="statGrid">
    <div class="stat-tile"><div class="snum" id="statWords">0</div><div class="slabel">Total Words</div></div>
    <div class="stat-tile"><div class="snum" id="statChars">0</div><div class="slabel">Total Characters</div></div>
    <div class="stat-tile" id="kwCountTile" style="display:none"><div class="snum" id="statKwCount">0</div><div class="slabel">Keyword Count</div></div>
    <div class="stat-tile" id="kwDensityTile" style="display:none"><div class="snum" id="statKwDensity">0%</div><div class="slabel">Keyword Density</div></div>
  </div>

  <div class="kd-columns">
    <div>
      <h3>Top Single Words</h3>
      <ul class="kd-list" id="topWords"></ul>
    </div>
    <div>
      <h3>Top 2-3 Word Phrases</h3>
      <ul class="kd-list" id="topPhrases"></ul>
    </div>
  </div>

  <div class="notice-box">
    Keyword density is one basic text metric — it does not guarantee rankings, and search engines don't use a target percentage. Writing naturally for readers works better than repeating a phrase to hit a number. Avoid keyword stuffing; it can make content harder to read and may be treated as a negative signal.
  </div>

  <h2>What Is Keyword Density?</h2>
  <p>Keyword density is the percentage of words in a piece of text that are part of a specific word or phrase. It's a simple way to see whether a topic is mentioned rarely, reasonably, or excessively often.</p>

  <h2>Why Is It Useful?</h2>
  <p>It can help you notice if you've barely mentioned your main topic, or — more commonly — if you've repeated a phrase so often that the writing starts to feel unnatural or spammy.</p>

  <h2>How to Use It</h2>
  <p>Paste your text, optionally enter a target keyword or phrase, and the results update as you type. The tool also shows the most frequent single words, 2-word phrases, and 3-word phrases in your text even without a target keyword.</p>

  <h2>How to Interpret the Results</h2>
  <p>There's no single "correct" density percentage — this varies by topic, content length, and writing style. Use this as a sanity check, not a target to hit. If a phrase dominates the top of the list far more than everything else, it's worth rereading that section for natural flow.</p>

  <h2>Common Mistakes</h2>
  <ul>
    <li>Repeating an exact phrase unnaturally often to try to hit a target percentage.</li>
    <li>Ignoring related terms and synonyms in favor of one exact phrase.</li>
    <li>Treating density as a ranking factor rather than a writing-quality check.</li>
  </ul>

  <h2>Limitations</h2>
  <p>This tool counts exact word matches (case-insensitive) — it doesn't understand synonyms, plurals as different forms, or grammatical variations unless they're typed as separate entries. Very short texts can produce misleadingly high percentages.</p>

  @include('partials.seo-faq', ['faqs' => [
    ['q' => 'What is a "good" keyword density?', 'a' => 'There isn\'t one — search engines don\'t publish or use a target percentage. Write naturally for your reader and use this tool as a sanity check, not a goal.'],
    ['q' => 'Will a high keyword density help me rank higher?', 'a' => 'No. Keyword stuffing can make content harder to read and is more likely to hurt than help. Relevance and usefulness matter far more than repetition.'],
    ['q' => 'Does this tool send my text anywhere?', 'a' => 'No — all counting happens locally in your browser with JavaScript. Your text is never sent to klikwit\'s servers.'],
    ['q' => 'Why does my keyword count seem low?', 'a' => 'The tool matches the exact phrase you typed. Plurals, different word order, or synonyms won\'t be counted as the same phrase.'],
    ['q' => 'Can I check a multi-word phrase?', 'a' => 'Yes — type the full phrase (e.g. "url shortener") into the Target Keyword field and it will be matched as a sequence, not as separate words.'],
  ]])

  @include('partials.seo-related-tools', ['tools' => [
    ['label' => 'Word Counter', 'url' => route('seo-tools.word-counter'), 'icon' => '🔢'],
    ['label' => 'Slug Generator', 'url' => route('seo-tools.slug-generator'), 'icon' => '✂️'],
    ['label' => 'Meta Tag Checker', 'url' => route('seo-tools.meta-tag-checker'), 'icon' => '🔍'],
  ]])

  <div class="final-cta">
    <h2>Also Managing Links?</h2>
    <p>klikwit's free URL shortener, QR codes and link analytics live right alongside these SEO tools.</p>
    <a class="cta-btn" href="{{ route('home') }}">Try the URL Shortener</a>
  </div>
</div>

<script>
(function(){
  var textEl = document.getElementById('kdText');
  var keywordEl = document.getElementById('kdKeyword');
  var statWords = document.getElementById('statWords');
  var statChars = document.getElementById('statChars');
  var kwCountTile = document.getElementById('kwCountTile');
  var kwDensityTile = document.getElementById('kwDensityTile');
  var statKwCount = document.getElementById('statKwCount');
  var statKwDensity = document.getElementById('statKwDensity');
  var topWordsEl = document.getElementById('topWords');
  var topPhrasesEl = document.getElementById('topPhrases');

  var STOPWORDS = {'the':1,'a':1,'an':1,'and':1,'or':1,'but':1,'is':1,'are':1,'was':1,'were':1,'be':1,'been':1,'to':1,'of':1,'in':1,'on':1,'for':1,'with':1,'at':1,'by':1,'this':1,'that':1,'it':1,'as':1,'from':1,'your':1,'you':1,'we':1,'our':1,'can':1,'will':1,'not':1,'has':1,'have':1,'i':1};

  function tokenize(text) {
    var matches = text.toLowerCase().match(/[\p{L}\p{N}\p{M}']+/gu);
    return matches || [];
  }

  function topN(freqMap, n) {
    return Object.keys(freqMap)
      .map(function (k) { return [k, freqMap[k]]; })
      .sort(function (a, b) { return b[1] - a[1]; })
      .slice(0, n);
  }

  function renderList(el, entries) {
    el.textContent = '';
    if (entries.length === 0) {
      var li = document.createElement('li');
      li.textContent = 'Nothing to show yet.';
      el.appendChild(li);
      return;
    }
    entries.forEach(function (entry) {
      var li = document.createElement('li');
      var label = document.createElement('span');
      label.textContent = entry[0];
      var count = document.createElement('span');
      count.className = 'kd-count';
      count.textContent = entry[1] + 'x';
      li.appendChild(label);
      li.appendChild(count);
      el.appendChild(li);
    });
  }

  function update() {
    var text = textEl.value;
    var tokens = tokenize(text);
    var totalWords = tokens.length;

    statWords.textContent = totalWords;
    statChars.textContent = text.length;

    var wordFreq = {};
    tokens.forEach(function (t) {
      if (STOPWORDS[t]) return;
      wordFreq[t] = (wordFreq[t] || 0) + 1;
    });
    renderList(topWordsEl, topN(wordFreq, 10));

    var phrase2 = {}, phrase3 = {};
    for (var i = 0; i < tokens.length - 1; i++) {
      var p2 = tokens[i] + ' ' + tokens[i + 1];
      phrase2[p2] = (phrase2[p2] || 0) + 1;
    }
    for (var j = 0; j < tokens.length - 2; j++) {
      var p3 = tokens[j] + ' ' + tokens[j + 1] + ' ' + tokens[j + 2];
      phrase3[p3] = (phrase3[p3] || 0) + 1;
    }
    var combinedPhrases = topN(phrase2, 6).concat(topN(phrase3, 6))
      .sort(function (a, b) { return b[1] - a[1]; })
      .filter(function (entry) { return entry[1] > 1; })
      .slice(0, 10);
    renderList(topPhrasesEl, combinedPhrases);

    var keyword = keywordEl.value.trim().toLowerCase();
    if (keyword === '' || totalWords === 0) {
      kwCountTile.style.display = 'none';
      kwDensityTile.style.display = 'none';
      return;
    }

    var kwTokens = tokenize(keyword);
    var occurrences = 0;
    if (kwTokens.length > 0) {
      for (var k = 0; k <= tokens.length - kwTokens.length; k++) {
        var match = true;
        for (var m = 0; m < kwTokens.length; m++) {
          if (tokens[k + m] !== kwTokens[m]) { match = false; break; }
        }
        if (match) occurrences++;
      }
    }

    var density = (occurrences * kwTokens.length / totalWords) * 100;
    kwCountTile.style.display = 'block';
    kwDensityTile.style.display = 'block';
    statKwCount.textContent = occurrences;
    statKwDensity.textContent = density.toFixed(1) + '%';
  }

  textEl.addEventListener('input', update);
  keywordEl.addEventListener('input', update);
  update();
})();
</script>
@endsection
