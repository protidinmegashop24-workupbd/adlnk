@extends('layouts.app')

@section('title', 'UTM Builder — Create Campaign Tracking URLs | klikwit')
@section('description', 'Build UTM-tagged campaign URLs for Google Analytics in seconds, then shorten them with one click.')

@section('content')
  <h1>🏷️ UTM Builder</h1>
  <p class="muted" style="margin-top:0">Add campaign tracking parameters to any URL so you can see where your traffic comes from in Google Analytics.</p>

  <div class="card">
    <form id="f" onsubmit="return false;">
      <label for="base_url">Website URL</label>
      <input id="base_url" type="url" placeholder="https://example.com/landing-page" required/>

      <label for="source">Campaign Source <span class="hint">(e.g. facebook, newsletter, google)</span></label>
      <input id="source" type="text" placeholder="facebook" required/>

      <label for="medium">Campaign Medium <span class="hint">(e.g. cpc, email, social)</span></label>
      <input id="medium" type="text" placeholder="social" required/>

      <label for="campaign">Campaign Name <span class="hint">(e.g. autumn_sale)</span></label>
      <input id="campaign" type="text" placeholder="autumn_sale" required/>

      <label for="term">Campaign Term <span class="hint">(optional — paid keyword)</span></label>
      <input id="term" type="text" placeholder=""/>

      <label for="content">Campaign Content <span class="hint">(optional — to A/B test ads)</span></label>
      <input id="content" type="text" placeholder=""/>

      <button id="btn" type="submit">Build URL</button>
    </form>
    <div class="result" id="result">
      <input id="built" type="text" readonly onclick="this.select()"/>
      <button class="copybtn" id="copy" type="button">Copy URL</button>
      <button id="shortenBtn" type="button" style="margin-top:8px">Shorten This URL</button>
      <div id="shortResult" style="display:none;margin-top:12px;font-weight:bold"></div>
    </div>
    <div class="error" id="err"></div>
  </div>

  <script>
  (function(){
    var form = document.getElementById('f');
    var btn = document.getElementById('btn');
    var err = document.getElementById('err');
    var result = document.getElementById('result');
    var built = document.getElementById('built');
    var shortenBtn = document.getElementById('shortenBtn');
    var shortResult = document.getElementById('shortResult');

    form.addEventListener('submit', function(){
      err.textContent = '';
      shortResult.style.display = 'none';
      var baseUrl = document.getElementById('base_url').value.trim();
      if (!/^https?:\/\//i.test(baseUrl)) {
        err.textContent = 'Please enter a valid URL starting with http:// or https://.';
        result.style.display = 'none';
        return;
      }
      var url;
      try {
        url = new URL(baseUrl);
      } catch (e) {
        err.textContent = 'That does not look like a valid URL.';
        result.style.display = 'none';
        return;
      }
      url.searchParams.set('utm_source', document.getElementById('source').value.trim());
      url.searchParams.set('utm_medium', document.getElementById('medium').value.trim());
      url.searchParams.set('utm_campaign', document.getElementById('campaign').value.trim());
      var term = document.getElementById('term').value.trim();
      var content = document.getElementById('content').value.trim();
      if (term) url.searchParams.set('utm_term', term);
      if (content) url.searchParams.set('utm_content', content);

      built.value = url.toString();
      result.style.display = 'block';
    });

    document.getElementById('copy').addEventListener('click', function(){
      built.select();
      try { document.execCommand('copy'); } catch (e) {}
      if (navigator.clipboard) { navigator.clipboard.writeText(built.value).catch(function(){}); }
    });

    shortenBtn.addEventListener('click', function(){
      shortenBtn.disabled = true;
      shortenBtn.textContent = 'Please wait...';
      fetch('{{ url('/api/shorten') }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ url: built.value })
      }).then(function(r){ return r.json().then(function(j){ return {ok:r.ok, body:j}; }); })
        .then(function(res){
          shortenBtn.disabled = false;
          shortenBtn.textContent = 'Shorten This URL';
          if (!res.ok || !res.body.short) {
            shortResult.style.display = 'block';
            shortResult.style.color = '#c0392b';
            shortResult.textContent = (res.body && res.body.error) ? res.body.error : 'Something went wrong.';
            return;
          }
          shortResult.style.display = 'block';
          shortResult.style.color = '#222';
          shortResult.textContent = res.body.short;
        })
        .catch(function(){
          shortenBtn.disabled = false;
          shortenBtn.textContent = 'Shorten This URL';
          shortResult.style.display = 'block';
          shortResult.style.color = '#c0392b';
          shortResult.textContent = 'Network error, please try again.';
        });
    });
  })();
  </script>
@endsection
