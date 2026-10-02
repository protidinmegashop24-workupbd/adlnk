@extends('layouts.app')

@section('title', 'Link Checker — Is This URL Working? | klikwit')
@section('description', 'Check whether a link is live, broken, or redirecting somewhere else — free, no signup.')
@section('extra-style')
  .status-badge{padding:16px;border-radius:8px;text-align:center;font-weight:bold;font-size:16px;margin-bottom:14px}
  .status-badge.good{background:#e8f8ee;color:#1a7f37;border:1px solid #c3ecd2}
  .status-badge.bad{background:#fdecea;color:#c0392b;border:1px solid #f5c6cb}
  .check-detail{font-size:13px;color:#555;padding:4px 0}
@endsection

@section('content')
  <h1>✅ Link Checker</h1>
  <p class="muted" style="margin-top:0">Check whether a link is live, broken, or redirecting somewhere else.</p>

  <div class="card">
    <form id="f">
      <label for="url">Link to check</label>
      <input id="url" type="url" placeholder="https://example.com" required/>
      <button id="btn" type="submit">Check Link</button>
      <div class="error" id="err"></div>
    </form>
    <div class="result" id="result">
      <div class="status-badge" id="badge"></div>
      <div class="check-detail">Status code: <strong id="statusCode"></strong></div>
      <div class="check-detail" id="redirectInfo" style="display:none">Redirected <span id="redirectCount"></span> time(s) to: <span id="finalUrl"></span></div>
    </div>
  </div>

  <p class="muted">Need to see the full redirect path? <a href="{{ route('tools.expand') }}">Try the URL Expander</a>.</p>

  <script>
  (function(){
    var form = document.getElementById('f');
    var btn = document.getElementById('btn');
    var err = document.getElementById('err');
    var result = document.getElementById('result');
    var badge = document.getElementById('badge');
    var statusCode = document.getElementById('statusCode');
    var redirectInfo = document.getElementById('redirectInfo');
    var redirectCount = document.getElementById('redirectCount');
    var finalUrl = document.getElementById('finalUrl');

    form.addEventListener('submit', function(e){
      e.preventDefault();
      err.textContent = '';
      result.style.display = 'none';
      redirectInfo.style.display = 'none';
      btn.disabled = true;
      btn.textContent = 'Checking...';
      fetch('{{ url('/api/check') }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ url: document.getElementById('url').value.trim() })
      }).then(function(r){ return r.json(); })
        .then(function(body){
          btn.disabled = false;
          btn.textContent = 'Check Link';
          if (body.error) {
            err.textContent = body.error;
            return;
          }
          badge.className = 'status-badge ' + (body.ok ? 'good' : 'bad');
          badge.textContent = body.ok ? '✓ This link is working' : '✗ This link is not working';
          statusCode.textContent = body.status;
          if (body.redirect_count > 0) {
            redirectCount.textContent = body.redirect_count;
            finalUrl.textContent = body.final_url;
            redirectInfo.style.display = 'block';
          }
          result.style.display = 'block';
        })
        .catch(function(){
          btn.disabled = false;
          btn.textContent = 'Check Link';
          err.textContent = 'Network error, please try again.';
        });
    });
  })();
  </script>
@endsection
