@extends('layouts.app')

@section('title', 'Word Counter - Words, Characters & Reading Time | Klikwit')
@section('description', 'Free live word counter: words, characters, sentences, paragraphs and estimated reading time. Runs entirely in your browser.')
@section('page-class', 'wide')

@section('content')
<div class="tool-section">
  @include('partials.seo-breadcrumb', ['crumbs' => [
    ['label' => 'Home', 'url' => route('home')],
    ['label' => 'SEO Tools', 'url' => route('seo-tools.index')],
    ['label' => 'Word Counter'],
  ]])

  <h1>Word Counter</h1>
  <p>Type or paste text below for a live count of words, characters, sentences, paragraphs and estimated reading time. Everything is calculated in your browser — nothing is sent to a server.</p>

  <div class="card">
    <label for="wcText">Text</label>
    <textarea id="wcText" rows="12" placeholder="Start typing or paste your text here..."></textarea>
  </div>

  <div class="stat-grid">
    <div class="stat-tile"><div class="snum" id="wcWords">0</div><div class="slabel">Words</div></div>
    <div class="stat-tile"><div class="snum" id="wcChars">0</div><div class="slabel">Characters</div></div>
    <div class="stat-tile"><div class="snum" id="wcCharsNoSpace">0</div><div class="slabel">Chars (no spaces)</div></div>
    <div class="stat-tile"><div class="snum" id="wcSentences">0</div><div class="slabel">Sentences</div></div>
    <div class="stat-tile"><div class="snum" id="wcParagraphs">0</div><div class="slabel">Paragraphs</div></div>
    <div class="stat-tile"><div class="snum" id="wcReadTime">0 min</div><div class="slabel">Reading Time</div></div>
  </div>

  <div class="notice-box">
    Your text is processed locally in your browser with JavaScript and is never uploaded or stored by klikwit.
  </div>

  <h2>What Is a Word Counter?</h2>
  <p>A word counter measures the basic size of a piece of text — how many words, characters, sentences and paragraphs it contains — and estimates how long it would take an average reader to get through it.</p>

  <h2>Why Is It Useful?</h2>
  <p>Many platforms have length limits or recommendations: meta descriptions, social posts, author bios, or word-count targets for articles. A live counter saves you from copying text elsewhere just to check its length.</p>

  <h2>How to Use It</h2>
  <p>Type or paste your text into the box. All six statistics update instantly as you type, with no button to click and no server round-trip.</p>

  <h2>How to Interpret the Results</h2>
  <p>Reading time is estimated at roughly 200 words per minute, a commonly used average for adult silent reading — actual reading speed varies a lot by reader, content difficulty, and language.</p>

  <h2>Common Mistakes</h2>
  <ul>
    <li>Confusing "characters" with "characters excluding spaces" when checking a strict limit.</li>
    <li>Not accounting for the fact that very short paragraphs without blank lines between them may be counted as one paragraph.</li>
  </ul>

  <h2>Limitations</h2>
  <p>Sentence counting is based on simple punctuation detection (periods, question marks, exclamation points) and can miscount in unusual cases, such as abbreviations like "e.g." or decimal numbers. Paragraph counting looks for blank lines between blocks of text.</p>

  @include('partials.seo-faq', ['faqs' => [
    ['q' => 'How is reading time calculated?', 'a' => 'Using an average reading speed of about 200 words per minute. It\'s a rough estimate, not a measurement of any specific reader.'],
    ['q' => 'Why is my sentence count off?', 'a' => 'The counter looks for periods, question marks and exclamation points. Abbreviations (like "Dr." or "e.g.") or unusual punctuation can throw off the count slightly.'],
    ['q' => 'Does this tool store or upload my text?', 'a' => 'No — all counting happens in your browser with JavaScript. Nothing is sent to klikwit\'s servers.'],
    ['q' => 'Does this support languages other than English?', 'a' => 'Word and character counting works for any language using standard Unicode text, including Bangla and other non-Latin scripts.'],
  ]])

  @include('partials.seo-related-tools', ['tools' => [
    ['label' => 'Keyword Density Checker', 'url' => route('seo-tools.keyword-density-checker'), 'icon' => '📊'],
    ['label' => 'Slug Generator', 'url' => route('seo-tools.slug-generator'), 'icon' => '✂️'],
    ['label' => 'SERP Preview', 'url' => route('seo-tools.serp-preview'), 'icon' => '🔎'],
  ]])

  <div class="final-cta">
    <h2>Also Managing Links?</h2>
    <p>klikwit's free URL shortener, QR codes and link analytics live right alongside these SEO tools.</p>
    <a class="cta-btn" href="{{ route('home') }}">Try the URL Shortener</a>
  </div>
</div>

<script>
(function(){
  var textEl = document.getElementById('wcText');
  var wcWords = document.getElementById('wcWords');
  var wcChars = document.getElementById('wcChars');
  var wcCharsNoSpace = document.getElementById('wcCharsNoSpace');
  var wcSentences = document.getElementById('wcSentences');
  var wcParagraphs = document.getElementById('wcParagraphs');
  var wcReadTime = document.getElementById('wcReadTime');

  function update() {
    var text = textEl.value;
    var trimmed = text.trim();

    var words = trimmed === '' ? [] : (trimmed.match(/[\p{L}\p{N}\p{M}']+/gu) || []);
    var sentences = trimmed === '' ? [] : trimmed.split(/[.!?]+/).map(function (s) { return s.trim(); }).filter(function (s) { return s.length > 0; });
    var paragraphs = trimmed === '' ? [] : trimmed.split(/\n\s*\n/).map(function (p) { return p.trim(); }).filter(function (p) { return p.length > 0; });

    wcWords.textContent = words.length;
    wcChars.textContent = text.length;
    wcCharsNoSpace.textContent = text.replace(/\s/g, '').length;
    wcSentences.textContent = sentences.length;
    wcParagraphs.textContent = paragraphs.length || (trimmed === '' ? 0 : 1);

    var minutes = words.length / 200;
    wcReadTime.textContent = words.length === 0 ? '0 min' : (minutes < 1 ? '< 1 min' : Math.ceil(minutes) + ' min');
  }

  textEl.addEventListener('input', update);
  update();
})();
</script>
@endsection
