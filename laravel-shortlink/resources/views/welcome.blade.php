@extends('layouts.app')

@section('title', 'klikwit — Short Links. Simple Tools. Free to Use.')
@section('description', 'Create short, shareable links, generate QR codes, track clicks, and manage your links—all in one simple platform.')
@section('page-class', 'wide')
@section('extra-style')
  .hero-wrap{max-width:560px;margin:-60px auto 0}
  .trust-strip{text-align:center;color:#888;font-size:13px;margin-top:16px}
  .section{margin-top:48px}
  .section h2{text-align:center;font-size:1.4rem;margin-bottom:20px}
  .tool-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:14px}
  .tool-card{background:#fff;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,.1);padding:18px;text-decoration:none;color:#222;display:block}
  .tool-card:hover{box-shadow:0 2px 8px rgba(0,0,0,.15)}
  .tool-card .icon{font-size:1.5rem}
  .tool-card h3{font-size:.95rem;margin:6px 0 4px}
  .tool-card p{font-size:12px;color:#666;margin:0}
  .steps{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:16px;text-align:center}
  .step-num{width:32px;height:32px;border-radius:50%;background:#0d6efd;color:#fff;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;font-weight:bold}
  .steps p{font-size:13px;color:#444;margin:0}
  .usecase-grid{display:flex;flex-wrap:wrap;gap:10px;justify-content:center}
  .usecase-badge{background:#fff;border:1px solid #e0e0e0;border-radius:20px;padding:8px 16px;font-size:13px}
  details.faq-item{background:#fff;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,.1);padding:14px 18px;margin-bottom:10px;max-width:700px;margin-left:auto;margin-right:auto}
  details.faq-item summary{cursor:pointer;font-weight:bold;font-size:14px}
  details.faq-item p{margin:10px 0 0;color:#555;font-size:14px}
  .final-cta{text-align:center;margin-top:48px;padding:36px 20px;background:#0d6efd;border-radius:12px;color:#fff}
  .final-cta h2{color:#fff;margin-top:0}
  .final-cta a.cta-btn{display:inline-block;background:#fff;color:#0d6efd;padding:12px 28px;border-radius:6px;font-weight:bold;text-decoration:none;margin-top:8px}
@endsection

@section('hero')
  <h1>Short Links. Simple Tools. Free to Use.</h1>
  <p>Create short, shareable links, generate QR codes, track clicks, and manage your links—all in one simple platform.</p>
@endsection

@section('content')
@if (session('status'))
  <div class="status" style="max-width:560px;margin:0 auto 16px">{{ session('status') }}</div>
@endif
<div class="hero-wrap" id="shortener">
  <div class="tabs">
    <button class="tab active" id="tab-single" type="button">Single Link</button>
    <button class="tab" id="tab-bulk" type="button">Bulk Shorten</button>
  </div>

  <div class="card" id="single-section">
    <form id="f">
      <label for="url">Long link</label>
      <input id="url" type="url" placeholder="https://example.com/your-long-link" required/>
      <label for="alias">Custom name (optional)</label>
      <input id="alias" type="text" placeholder="mybrand"/>

      <div style="margin-top:12px">
        <a href="#" id="toggleAdvanced" style="font-size:13px">+ Advanced options (password, expiration)</a>
      </div>
      <div id="advancedOptions" style="display:none">
        <label for="password">Password protect (optional)</label>
        <input id="password" type="password" placeholder="Leave blank for no password"/>
        <label for="expires_at">Expires on (optional)</label>
        <input id="expires_at" type="datetime-local"/>
      </div>

      <button id="btn" type="submit">Shorten</button>
      <div class="error" id="err"></div>
    </form>
    <div class="result" id="result">
      <input id="short" type="text" readonly onclick="this.select()"/>
      <button class="copybtn" id="copy" type="button">Copy Link</button>
      <div><img id="qr" alt="QR code" width="180" height="180"/></div>
      <a id="qrdl" download="qr.png">Download QR</a>
    </div>
  </div>

  <div class="card" id="bulk-section" style="display:none">
    <form id="bf">
      <label for="urls">Long links (one per line, up to 20)</label>
      <textarea id="urls" rows="6" placeholder="https://example.com/page-one&#10;https://example.com/page-two&#10;https://example.com/page-three" required></textarea>
      <div class="hint">One link per line. Custom names aren't available in bulk mode.</div>
      <button id="bbtn" type="submit">Shorten All</button>
      <div class="error" id="berr"></div>
    </form>
    <div class="bulk-results" id="bulkResults">
      <button class="copybtn" id="copyAll" type="button">Copy All Short Links</button>
      <div id="bulkList"></div>
    </div>
  </div>

  <p class="trust-strip">No complicated setup &middot; Fast &middot; Easy to use &middot; Free tools</p>

  <p class="muted">More tools: <a href="{{ route('tools.expand') }}">URL Expander</a> &middot; <a href="{{ route('tools.check') }}">Link Checker</a> &middot; <a href="{{ route('tools.utm') }}">UTM Builder</a></p>

  @guest
    <p class="muted">Powered by klikwit — <a href="{{ route('register') }}">Sign up free</a> to save your links and track clicks.</p>
  @else
    <p class="muted">Powered by klikwit — <a href="{{ route('dashboard') }}">view your saved links</a>.</p>
  @endguest
</div>

<div class="section">
  <h2>Everything You Need to Manage Links</h2>
  <div class="tool-grid">
    <a class="tool-card" href="#shortener">
      <div class="icon">🔗</div>
      <h3>URL Shortener</h3>
      <p>Create short, clean links in seconds.</p>
    </a>
    <a class="tool-card" href="#bulk-section">
      <div class="icon">📦</div>
      <h3>Bulk URL Shortener</h3>
      <p>Shorten multiple links at once.</p>
    </a>
    <a class="tool-card" href="#shortener">
      <div class="icon">▣</div>
      <h3>QR Generator</h3>
      <p>Turn any URL into a downloadable QR code.</p>
    </a>
    <a class="tool-card" href="{{ route('dashboard') }}">
      <div class="icon">📊</div>
      <h3>Link Analytics</h3>
      <p>Understand clicks and link performance.</p>
    </a>
    <a class="tool-card" href="#shortener">
      <div class="icon">🎨</div>
      <h3>Custom Links</h3>
      <p>Create memorable custom slugs.</p>
    </a>
    <a class="tool-card" href="{{ route('bio.edit') }}">
      <div class="icon">👤</div>
      <h3>Link-in-Bio</h3>
      <p>Share multiple links from one simple page.</p>
    </a>
  </div>
</div>

<div class="section">
  <h2>How It Works</h2>
  <div class="steps">
    <div><div class="step-num">1</div><p>Paste your URL</p></div>
    <div><div class="step-num">2</div><p>Customize your link</p></div>
    <div><div class="step-num">3</div><p>Share it anywhere</p></div>
    <div><div class="step-num">4</div><p>Track performance</p></div>
  </div>
</div>

<div class="section">
  <h2>Built for Creators, Businesses and Everyday Sharing</h2>
  <div class="usecase-grid">
    <span class="usecase-badge">Social media creators</span>
    <span class="usecase-badge">Small businesses</span>
    <span class="usecase-badge">Bloggers</span>
    <span class="usecase-badge">YouTubers</span>
    <span class="usecase-badge">Marketing teams</span>
    <span class="usecase-badge">Online sellers</span>
    <span class="usecase-badge">Students</span>
    <span class="usecase-badge">Event organizers</span>
  </div>
</div>

<div class="section">
  <h2>Frequently Asked Questions</h2>
  <details class="faq-item">
    <summary>What is a URL shortener?</summary>
    <p>A URL shortener takes a long web address and turns it into a short, easy-to-share link. When someone clicks the short link, they're instantly redirected to the original page.</p>
  </details>
  <details class="faq-item">
    <summary>Are klikwit short links free?</summary>
    <p>Yes. Creating short links, QR codes, and using the bulk shortener are all free, with no signup required to get started.</p>
  </details>
  <details class="faq-item">
    <summary>Can I track clicks?</summary>
    <p>Yes. Sign up for a free account and every link you shorten while logged in is saved to your dashboard with click counts, device breakdown, and referrer data.</p>
  </details>
  <details class="faq-item">
    <summary>Can I create a custom short link?</summary>
    <p>Yes. Enter your own custom name in the "Custom name" field instead of using an auto-generated code.</p>
  </details>
  <details class="faq-item">
    <summary>Can I generate a QR code?</summary>
    <p>Yes. Every short link automatically gets a downloadable QR code you can use on print materials, packaging, or posters.</p>
  </details>
  <details class="faq-item">
    <summary>How does Bulk URL Shortener work?</summary>
    <p>Switch to the "Bulk Shorten" tab, paste up to 20 links (one per line), and click "Shorten All" to get all your short links at once.</p>
  </details>
</div>

<div class="final-cta">
  <h2>Start shortening links for free.</h2>
  <a class="cta-btn" href="#shortener">Shorten a Link Now</a>
</div>

  <script>
  (function(){
    var tabSingle = document.getElementById('tab-single');
    var tabBulk = document.getElementById('tab-bulk');
    var singleSection = document.getElementById('single-section');
    var bulkSection = document.getElementById('bulk-section');

    function showBulkTab(){
      tabBulk.classList.add('active');
      tabSingle.classList.remove('active');
      bulkSection.style.display = 'block';
      singleSection.style.display = 'none';
    }

    tabSingle.addEventListener('click', function(){
      tabSingle.classList.add('active');
      tabBulk.classList.remove('active');
      singleSection.style.display = 'block';
      bulkSection.style.display = 'none';
    });
    tabBulk.addEventListener('click', showBulkTab);

    if (window.location.hash === '#bulk-section') {
      showBulkTab();
    }

    var form = document.getElementById('f');
    var btn = document.getElementById('btn');
    var err = document.getElementById('err');
    var result = document.getElementById('result');
    var shortInput = document.getElementById('short');
    var qr = document.getElementById('qr');
    var qrdl = document.getElementById('qrdl');
    var toggleAdvanced = document.getElementById('toggleAdvanced');
    var advancedOptions = document.getElementById('advancedOptions');

    toggleAdvanced.addEventListener('click', function(e){
      e.preventDefault();
      var showing = advancedOptions.style.display === 'block';
      advancedOptions.style.display = showing ? 'none' : 'block';
      toggleAdvanced.textContent = showing ? '+ Advanced options (password, expiration)' : '− Hide advanced options';
    });

    form.addEventListener('submit', function(e){
      e.preventDefault();
      err.textContent = '';
      result.style.display = 'none';
      btn.disabled = true;
      btn.textContent = 'Please wait...';
      var expiresLocal = document.getElementById('expires_at').value;
      var expiresUtc = '';
      if (expiresLocal) {
        var d = new Date(expiresLocal);
        if (!isNaN(d.getTime())) { expiresUtc = d.toISOString(); }
      }
      fetch('{{ url('/api/shorten') }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
          url: document.getElementById('url').value.trim(),
          alias: document.getElementById('alias').value.trim(),
          password: document.getElementById('password').value,
          expires_at: expiresUtc
        })
      }).then(function(r){ return r.json().then(function(j){ return {ok:r.ok, body:j}; }); })
        .then(function(res){
          btn.disabled = false;
          btn.textContent = 'Shorten';
          if (!res.ok || !res.body.short) {
            err.textContent = (res.body && res.body.error) ? res.body.error : 'Something went wrong.';
            return;
          }
          shortInput.value = res.body.short;
          var qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=' + encodeURIComponent(res.body.short);
          qr.src = qrUrl;
          qrdl.href = qrUrl;
          result.style.display = 'block';
        })
        .catch(function(){
          btn.disabled = false;
          btn.textContent = 'Shorten';
          err.textContent = 'Network error, please try again.';
        });
    });

    document.getElementById('copy').addEventListener('click', function(){
      shortInput.select();
      try { document.execCommand('copy'); } catch (e) {}
      if (navigator.clipboard) { navigator.clipboard.writeText(shortInput.value).catch(function(){}); }
    });

    var bform = document.getElementById('bf');
    var bbtn = document.getElementById('bbtn');
    var berr = document.getElementById('berr');
    var bulkResults = document.getElementById('bulkResults');
    var bulkList = document.getElementById('bulkList');
    var copyAll = document.getElementById('copyAll');
    var lastShortLinks = [];

    bform.addEventListener('submit', function(e){
      e.preventDefault();
      berr.textContent = '';
      bulkResults.style.display = 'none';
      bulkList.innerHTML = '';
      bbtn.disabled = true;
      bbtn.textContent = 'Please wait...';
      fetch('{{ url('/api/bulk-shorten') }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ urls: document.getElementById('urls').value })
      }).then(function(r){ return r.json().then(function(j){ return {ok:r.ok, body:j}; }); })
        .then(function(res){
          bbtn.disabled = false;
          bbtn.textContent = 'Shorten All';
          if (!res.ok || !res.body.results) {
            berr.textContent = (res.body && res.body.error) ? res.body.error : 'Something went wrong.';
            return;
          }
          lastShortLinks = [];
          res.body.results.forEach(function(item){
            var row = document.createElement('div');
            row.className = 'bulk-row';
            var orig = document.createElement('span');
            orig.className = 'orig';
            orig.textContent = item.url;
            var right = document.createElement('span');
            if (item.short) {
              lastShortLinks.push(item.short);
              right.className = 'short-link';
              right.textContent = item.short;
            } else {
              right.className = 'bulk-error';
              right.textContent = item.error;
            }
            row.appendChild(orig);
            row.appendChild(right);
            bulkList.appendChild(row);
          });
          bulkResults.style.display = 'block';
        })
        .catch(function(){
          bbtn.disabled = false;
          bbtn.textContent = 'Shorten All';
          berr.textContent = 'Network error, please try again.';
        });
    });

    copyAll.addEventListener('click', function(){
      if (!lastShortLinks.length) return;
      var text = lastShortLinks.join('\n');
      if (navigator.clipboard) {
        navigator.clipboard.writeText(text).catch(function(){});
      } else {
        var ta = document.createElement('textarea');
        ta.value = text;
        document.body.appendChild(ta);
        ta.select();
        try { document.execCommand('copy'); } catch (e) {}
        document.body.removeChild(ta);
      }
    });
  })();
  </script>
@endsection
