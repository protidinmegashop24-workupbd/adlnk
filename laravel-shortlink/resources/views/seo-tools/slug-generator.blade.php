@extends('layouts.app')

@section('title', 'Slug Generator - Create SEO-Friendly URL Slugs | Klikwit')
@section('description', 'Turn any title into a clean, SEO-friendly URL slug. Free, runs in your browser, supports non-English text.')
@section('page-class', 'wide')
@section('extra-style')
  .slug-output{display:flex;gap:8px;align-items:stretch;margin-top:8px}
  .slug-output input{flex:1;font-family:monospace}
  .slug-output button{width:auto;margin:0;flex-shrink:0}
@endsection

@section('content')
<div class="tool-section">
  @include('partials.seo-breadcrumb', ['crumbs' => [
    ['label' => 'Home', 'url' => route('home')],
    ['label' => 'SEO Tools', 'url' => route('seo-tools.index')],
    ['label' => 'Slug Generator'],
  ]])

  <h1>Slug Generator</h1>
  <p>Type a title below to get a clean, URL-friendly slug — lowercase, hyphenated, with symbols and duplicate hyphens removed.</p>

  <div class="card">
    <label for="slugInput">Title</label>
    <input id="slugInput" type="text" placeholder="How to Create a QR Code for Your Website"/>
    <label for="slugOutput">Generated Slug</label>
    <div class="slug-output">
      <input id="slugOutput" type="text" readonly/>
      <button type="button" id="copyBtn" class="copybtn">Copy</button>
    </div>
  </div>

  <h2>What Is a Slug?</h2>
  <p>A slug is the part of a URL that identifies a specific page in a human-readable way — for example, in <code>example.com/blog/how-to-create-a-qr-code</code>, the slug is <code>how-to-create-a-qr-code</code>.</p>

  <h2>Why Is It Useful?</h2>
  <p>A clean slug is easier to read, share and remember than an auto-generated ID or a title with raw spaces and punctuation left in. Many content management systems generate slugs automatically but don't always clean them up well.</p>

  <h2>How to Use It</h2>
  <p>Type or paste a title. The slug updates as you type. Click "Copy" to copy it to your clipboard for use in a CMS, blog platform, or your own code.</p>

  <h2>How to Interpret the Results</h2>
  <p>The generated slug lowercases your text, replaces spaces and symbols with hyphens, removes duplicate hyphens, and trims any leading or trailing hyphens. Text in non-Latin scripts (such as Bangla) is preserved rather than stripped out, since those characters are valid in modern URLs.</p>

  <h2>Common Mistakes</h2>
  <ul>
    <li>Leaving stop words and filler text in a slug when a shorter version would be just as clear.</li>
    <li>Changing a page's slug after it's already indexed and linked to, without a redirect from the old URL.</li>
    <li>Including dates or numbers that will quickly become outdated.</li>
  </ul>

  <h2>Limitations</h2>
  <p>This tool only reformats the text you give it — it doesn't check whether the resulting slug is already in use on your site, or automatically update any existing page.</p>

  @include('partials.seo-faq', ['faqs' => [
    ['q' => 'Should a slug match the page title exactly?', 'a' => 'Not necessarily — a shorter, cleaner version of the title often makes a better slug than the full title verbatim.'],
    ['q' => 'Can I change a slug after publishing?', 'a' => 'You can, but it will change the page\'s URL. If other pages or external sites link to the old URL, set up a redirect from the old slug to the new one.'],
    ['q' => 'Does this tool support non-English titles?', 'a' => 'Yes — letters from non-Latin scripts (such as Bangla) are kept as-is rather than stripped out, since modern browsers and search engines handle Unicode URLs correctly.'],
    ['q' => 'Why were some characters removed from my title?', 'a' => 'Punctuation and symbols that aren\'t letters, numbers, or hyphens are removed, since they often need special encoding in a URL and can look messy.'],
  ]])

  @include('partials.seo-related-tools', ['tools' => [
    ['label' => 'SEO URL Checker', 'url' => route('seo-tools.seo-url-checker'), 'icon' => '🔗'],
    ['label' => 'Word Counter', 'url' => route('seo-tools.word-counter'), 'icon' => '🔢'],
    ['label' => 'Keyword Density Checker', 'url' => route('seo-tools.keyword-density-checker'), 'icon' => '📊'],
  ]])

  <div class="final-cta">
    <h2>Also Managing Links?</h2>
    <p>klikwit's free URL shortener, QR codes and link analytics live right alongside these SEO tools.</p>
    <a class="cta-btn" href="{{ route('home') }}">Try the URL Shortener</a>
  </div>
</div>

<script>
(function(){
  var input = document.getElementById('slugInput');
  var output = document.getElementById('slugOutput');
  var copyBtn = document.getElementById('copyBtn');

  function slugify(text) {
    return text
      .normalize('NFKC')
      .toLowerCase()
      .trim()
      .replace(/\s+/g, '-')
      .replace(/[^\p{L}\p{N}\p{M}-]+/gu, '')
      .replace(/-{2,}/g, '-')
      .replace(/^-+|-+$/g, '');
  }

  function update() {
    output.value = slugify(input.value);
  }

  copyBtn.addEventListener('click', function () {
    if (!output.value) return;
    navigator.clipboard.writeText(output.value).then(function () {
      var original = copyBtn.textContent;
      copyBtn.textContent = 'Copied!';
      setTimeout(function () { copyBtn.textContent = original; }, 1500);
    });
  });

  input.addEventListener('input', update);
})();
</script>
@endsection
