<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<title>klikwit — Free URL Shortener & QR Code Generator</title>
<style>
  body{font-family:Arial,Helvetica,sans-serif;background:#f4f6f8;margin:0;padding:24px;color:#222}
  .wrap{max-width:560px;margin:0 auto}
  h1{font-size:1.4rem;text-align:center}
  .tabs{display:flex;gap:8px;margin-top:20px}
  .tab{flex:1;background:#e9ecef;color:#444;border:0;padding:10px;border-radius:6px;font-size:14px;cursor:pointer}
  .tab.active{background:#0d6efd;color:#fff}
  .card{background:#fff;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,.1);padding:24px;margin-top:16px}
  label{display:block;font-size:13px;color:#555;margin:12px 0 4px}
  input[type=text],input[type=url]{width:100%;box-sizing:border-box;padding:10px;border:1px solid #ccc;border-radius:6px;font-size:15px}
  textarea{width:100%;box-sizing:border-box;padding:10px;border:1px solid #ccc;border-radius:6px;font-size:14px;font-family:inherit;resize:vertical}
  button{background:#0d6efd;color:#fff;border:0;padding:12px 20px;border-radius:6px;font-size:15px;cursor:pointer;margin-top:16px;width:100%}
  button:disabled{background:#9db8e8;cursor:not-allowed}
  .error{color:#c0392b;margin-top:10px;font-size:14px}
  .result{display:none;margin-top:20px;text-align:center;border-top:1px solid #eee;padding-top:20px}
  .result input{text-align:center;font-weight:bold;margin-bottom:12px}
  .result img{border:1px solid #eee;border-radius:8px;margin:8px 0}
  .copybtn{background:#28a745}
  .muted{color:#888;font-size:13px;text-align:center;margin-top:24px}
  .hint{color:#888;font-size:12px;margin-top:6px}
  .bulk-results{display:none;margin-top:20px;border-top:1px solid #eee;padding-top:16px}
  .bulk-row{display:flex;justify-content:space-between;align-items:center;gap:8px;padding:8px 0;border-bottom:1px solid #f1f1f1;font-size:13px}
  .bulk-row:last-child{border-bottom:0}
  .bulk-row .orig{color:#888;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:45%}
  .bulk-row .short-link{font-weight:bold}
  .bulk-row .bulk-error{color:#c0392b}
</style>
</head>
<body>
<div class="wrap">
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

  <p class="muted">Powered by klikwit</p>
</div>
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
</body>
</html>
