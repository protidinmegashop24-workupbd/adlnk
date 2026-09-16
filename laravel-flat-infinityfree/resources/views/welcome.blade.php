<!DOCTYPE html>
<html lang="bn">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<title>adlnk — ফ্রি URL Shortener</title>
<style>
  body{font-family:Arial,Helvetica,sans-serif;background:#f4f6f8;margin:0;padding:24px;color:#222}
  .wrap{max-width:560px;margin:0 auto}
  h1{font-size:1.4rem;text-align:center}
  .card{background:#fff;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,.1);padding:24px;margin-top:16px}
  label{display:block;font-size:13px;color:#555;margin:12px 0 4px}
  input[type=text],input[type=url]{width:100%;box-sizing:border-box;padding:10px;border:1px solid #ccc;border-radius:6px;font-size:15px}
  button{background:#0d6efd;color:#fff;border:0;padding:12px 20px;border-radius:6px;font-size:15px;cursor:pointer;margin-top:16px;width:100%}
  button:disabled{background:#9db8e8;cursor:not-allowed}
  .error{color:#c0392b;margin-top:10px;font-size:14px}
  .result{display:none;margin-top:20px;text-align:center;border-top:1px solid #eee;padding-top:20px}
  .result input{text-align:center;font-weight:bold;margin-bottom:12px}
  .result img{border:1px solid #eee;border-radius:8px;margin:8px 0}
  .copybtn{background:#28a745}
  .muted{color:#888;font-size:13px;text-align:center;margin-top:24px}
</style>
</head>
<body>
<div class="wrap">
  <h1>🔗 adlnk — ফ্রি URL Shortener</h1>
  <div class="card">
    <form id="f">
      <label for="url">লম্বা লিংক</label>
      <input id="url" type="url" placeholder="https://example.com/your-long-link" required/>
      <label for="alias">কাস্টম নাম (ঐচ্ছিক)</label>
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
  <p class="muted">Powered by adlnk</p>
</div>
<script>
(function(){
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
    btn.textContent = 'অপেক্ষা করুন...';
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
        err.textContent = 'নেটওয়ার্ক এরর, আবার চেষ্টা করুন।';
      });
  });

  document.getElementById('copy').addEventListener('click', function(){
    shortInput.select();
    try { document.execCommand('copy'); } catch (e) {}
    if (navigator.clipboard) { navigator.clipboard.writeText(shortInput.value).catch(function(){}); }
  });
})();
</script>
</body>
</html>
