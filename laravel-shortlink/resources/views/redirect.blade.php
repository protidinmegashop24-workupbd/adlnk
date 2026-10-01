<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="robots" content="noindex,nofollow"/>
<title>Please wait...</title>
<style>
  body{font-family:Arial,Helvetica,sans-serif;background:#f4f6f8;margin:0;padding:0;color:#222}
  .wrap{max-width:640px;margin:0 auto;padding:24px 16px;text-align:center}
  .card{background:#fff;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,.1);padding:24px;margin-top:16px}
  .ad-slot{min-height:90px;display:flex;align-items:center;justify-content:center;color:#999;border:1px dashed #ccc;margin:16px 0;font-size:13px}
  .progress{height:8px;background:#e9ecef;border-radius:4px;overflow:hidden;margin:20px 0}
  .progress-bar{height:100%;width:0%;background:#0d6efd;transition:width .2s linear}
  button{background:#0d6efd;color:#fff;border:0;padding:12px 28px;border-radius:6px;font-size:16px;cursor:pointer}
  button:disabled{background:#9db8e8;cursor:not-allowed}
  .muted{color:#666;font-size:14px}
</style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <h3>Preparing your link...</h3>
      <p class="muted">Checking the link to protect you from viruses, malware, and harmful sites.</p>
      <div class="ad-slot" id="ad-top"><!-- place your AdSense/ad code here --></div>
      <div class="progress"><div class="progress-bar" id="bar"></div></div>
      <p class="muted">Please wait <span id="secs">{{ $seconds }}</span> seconds...</p>
      <div class="ad-slot" id="ad-bottom"><!-- place your AdSense/ad code here --></div>
      <button id="go" disabled>Continue to link</button>
    </div>
  </div>
<script>
(function(){
  var total = {{ $seconds }};
  var left = total;
  var bar = document.getElementById('bar');
  var secs = document.getElementById('secs');
  var btn = document.getElementById('go');
  var code = @json($code);
  var timer = setInterval(function(){
    left -= 1;
    secs.textContent = Math.max(left, 0);
    bar.style.width = (Math.min(total - left, total) / total * 100) + '%';
    if (left <= 0) {
      clearInterval(timer);
      btn.disabled = false;
      btn.textContent = 'Continue to link';
    }
  }, 1000);
  btn.addEventListener('click', function(){
    if (btn.disabled) return;
    window.location.href = '/go/' + code;
  });
})();
</script>
</body>
</html>
