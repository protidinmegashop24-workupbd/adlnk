@extends('layouts.app')

@section('content')
  <h1>🔗 klikwit</h1>
  <p style="text-align:center;color:#555;margin-top:-8px;font-size:14px">Free URL Shortener &amp; QR Code Generator</p>

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

  <p class="muted">More tools: <a href="{{ route('tools.expand') }}">URL Expander</a> &middot; <a href="{{ route('tools.check') }}">Link Checker</a></p>

  @guest
    <p class="muted">Powered by klikwit — <a href="{{ route('register') }}">Sign up free</a> to save your links and track clicks.</p>
  @else
    <p class="muted">Powered by klikwit — <a href="{{ route('dashboard') }}">view your saved links</a>.</p>
  @endguest

  <script>
  (function(){
    var tabSingle = document.getElementById('tab-single');
    var tabBulk = document.getElementById('tab-bulk');
    var singleSection = document.getElementById('single-section');
    var bulkSection = document.getElementById('bulk-section');

    tabSingle.addEventListener('click', function(){
      tabSingle.classList.add('active');
      tabBulk.classList.remove('active');
      singleSection.style.display = 'block';
      bulkSection.style.display = 'none';
    });
    tabBulk.addEventListener('click', function(){
      tabBulk.classList.add('active');
      tabSingle.classList.remove('active');
      bulkSection.style.display = 'block';
      singleSection.style.display = 'none';
    });

    var form = document.getElementById('f');
    var btn = document.getElementById('btn');
    var err = document.getElementById('err');
    var result = document.getElementById('result');
    var shortInput = document.getElementById('short');
    var qr = document.getElementById('qr');
    var qrdl = document.getElementById('qrdl');

    form.addEventListener('submit', function(e){
      e.preventDefault();
      err.textContent = '';
      result.style.display = 'none';
      btn.disabled = true;
      btn.textContent = 'Please wait...';
      fetch('{{ url('/api/shorten') }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
          url: document.getElementById('url').value.trim(),
          alias: document.getElementById('alias').value.trim()
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
