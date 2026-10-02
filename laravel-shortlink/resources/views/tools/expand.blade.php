@extends('layouts.app')

@section('title', 'URL Expander — See Where a Short Link Really Goes | klikwit')
@section('description', 'Paste any shortened link and see its full redirect chain and final destination before you click it.')
@section('extra-style')
  .hop{display:flex;align-items:center;gap:8px;padding:10px 0;border-bottom:1px solid #f1f1f1;font-size:13px}
  .hop:last-child{border-bottom:0}
  .hop .hop-url{color:#555;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1}
  .hop .hop-status{flex-shrink:0;font-weight:bold;color:#888}
  .final-box{background:#e8f8ee;border:1px solid #c3ecd2;border-radius:6px;padding:14px;margin-top:16px;word-break:break-all;font-weight:bold}
@endsection

@section('content')
  <h1>🔎 URL Expander</h1>
  <p class="muted" style="margin-top:0">See the full redirect chain and final destination of any shortened link — before you click it.</p>

  <div class="card">
    <form id="f">
      <label for="url">Shortened or redirecting link</label>
      <input id="url" type="url" placeholder="https://bit.ly/example" required/>
      <button id="btn" type="submit">Expand</button>
      <div class="error" id="err"></div>
    </form>
    <div class="result" id="result">
      <div id="hops"></div>
      <div class="final-box">Final destination: <span id="finalUrl"></span></div>
    </div>
  </div>

  <p class="muted">Want to shorten a link instead? <a href="{{ route('home') }}">Try the URL Shortener</a>.</p>

  <script>
  (function(){
    var form = document.getElementById('f');
    var btn = document.getElementById('btn');
    var err = document.getElementById('err');
    var result = document.getElementById('result');
    var hops = document.getElementById('hops');
    var finalUrl = document.getElementById('finalUrl');

    form.addEventListener('submit', function(e){
      e.preventDefault();
      err.textContent = '';
      result.style.display = 'none';
      hops.innerHTML = '';
      btn.disabled = true;
      btn.textContent = 'Please wait...';
      fetch('{{ url('/api/expand') }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ url: document.getElementById('url').value.trim() })
      }).then(function(r){ return r.json().then(function(j){ return {ok:r.ok, body:j}; }); })
        .then(function(res){
          btn.disabled = false;
          btn.textContent = 'Expand';
          if (!res.ok) {
            err.textContent = (res.body && res.body.error) ? res.body.error : 'Something went wrong.';
            return;
          }
          res.body.hops.forEach(function(hop, i){
            var row = document.createElement('div');
            row.className = 'hop';
            var urlSpan = document.createElement('span');
            urlSpan.className = 'hop-url';
            urlSpan.textContent = (i + 1) + '. ' + hop.url;
            var statusSpan = document.createElement('span');
            statusSpan.className = 'hop-status';
            statusSpan.textContent = hop.status;
            row.appendChild(urlSpan);
            row.appendChild(statusSpan);
            hops.appendChild(row);
          });
          finalUrl.textContent = res.body.final_url;
          result.style.display = 'block';
        })
        .catch(function(){
          btn.disabled = false;
          btn.textContent = 'Expand';
          err.textContent = 'Network error, please try again.';
        });
    });
  })();
  </script>
@endsection
