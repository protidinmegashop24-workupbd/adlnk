@extends('layouts.app')

@section('title', 'Page Speed Checker | Klikwit')
@section('description', 'Check a page\'s real performance score and Core Web Vitals, powered by Google PageSpeed Insights. Free, no signup.')
@section('page-class', 'wide')
@section('extra-style')
  .ps-strategy{display:flex;gap:8px;margin:12px 0 0}
  .ps-strategy button{width:auto;flex:1;margin:0;padding:10px;background:#eef1f5;color:#444;font-size:14px}
  .ps-strategy button.active{background:#0d6efd;color:#fff}
  .ps-score-wrap{text-align:center;margin-top:24px}
  .ps-score{display:inline-flex;align-items:center;justify-content:center;width:120px;height:120px;border-radius:50%;font-size:2.4rem;font-weight:bold;color:#fff}
  .ps-score.good{background:#1a7f37}
  .ps-score.ok{background:#b8860b}
  .ps-score.poor{background:#c0392b}
  .ps-score-label{margin-top:10px;color:#666;font-size:14px}
  .ps-opportunities{margin-top:8px;padding-left:20px}
  .ps-opportunities li{font-size:14px;color:#333;margin:6px 0}
  .ps-setup-note{margin-top:16px}
@endsection

@section('content')
<div class="tool-section">
  @include('partials.seo-breadcrumb', ['crumbs' => [
    ['label' => 'Home', 'url' => route('home')],
    ['label' => 'SEO Tools', 'url' => route('seo-tools.index')],
    ['label' => 'Page Speed Checker'],
  ]])

  <h1>Page Speed Checker</h1>
  <p>Paste a URL to see its real performance score and Core Web Vitals, powered by Google's own PageSpeed Insights (Lighthouse) data — not an estimate.</p>

  <div class="card">
    <form id="f">
      <label for="url">Page URL</label>
      <input id="url" type="url" placeholder="https://example.com/your-page" required/>
      <div class="ps-strategy" role="group" aria-label="Device">
        <button type="button" class="active" data-strategy="mobile">📱 Mobile</button>
        <button type="button" data-strategy="desktop">💻 Desktop</button>
      </div>
      <button id="btn" type="submit">Check Speed</button>
      <div class="error" id="err"></div>
    </form>
    <div class="tool-result" id="result" style="display:none">
      <div class="ps-score-wrap">
        <div class="ps-score" id="scoreCircle">–</div>
        <div class="ps-score-label">Performance Score (<span id="strategyLabel">Mobile</span>)</div>
      </div>
      <div class="stat-grid" id="metricsGrid"></div>
      <div id="opportunitiesWrap" style="display:none">
        <h3 style="margin-top:24px">Top Opportunities</h3>
        <ul class="ps-opportunities" id="opportunitiesList"></ul>
      </div>
    </div>
  </div>

  <div class="notice-box">
    This requires a free Google API key to be configured on the server. If you're the site owner and see a setup error below, add <code>GOOGLE_PAGESPEED_API_KEY</code> to your <code>.env</code> file (see <code>.env.example</code> for how to get one for free).
  </div>

  <h2>What Is a Page Speed Checker?</h2>
  <p>It runs Google's own PageSpeed Insights analysis (the same engine behind Lighthouse and Chrome DevTools) on a page and reports a 0-100 performance score, along with Core Web Vitals — the specific loading, interactivity, and visual-stability metrics Google uses to judge real-world page experience.</p>

  <h2>Why Is It Useful?</h2>
  <p>Page speed affects both user experience and search ranking. This gives you Google's own assessment, directly, so you're seeing the same data Google itself uses rather than a third-party guess.</p>

  <h2>How to Use It</h2>
  <p>Paste a public page URL, choose Mobile or Desktop, and click "Check Speed." Mobile results are usually lower than desktop since mobile devices and connections are slower — both are normal to check since Google evaluates mobile performance primarily.</p>

  <h2>How to Interpret the Results</h2>
  <p>Scores of 90+ are considered good, 50-89 need improvement, and below 50 are poor — these are Google's own published thresholds, not ours. The "Top Opportunities" list highlights the specific issues Lighthouse flagged as having the biggest potential impact.</p>

  <h2>Limitations</h2>
  <p>Results reflect a single test run under Google's lab conditions, which can vary slightly between runs. This tool shows lab data (a simulated test), not field data from real visitors over time — for that, Google's own Search Console reports actual Core Web Vitals from real users.</p>

  @include('partials.seo-faq', ['faqs' => [
    ['q' => 'Why is my mobile score lower than desktop?', 'a' => 'Mobile tests simulate a slower device and network connection by design, since that reflects how many real visitors experience your site. It\'s normal for mobile scores to be lower.'],
    ['q' => 'Does this tool fetch real data or estimate it?', 'a' => 'It calls Google\'s own PageSpeed Insights API directly — the results are Google\'s real Lighthouse analysis, not an estimate made by klikwit.'],
    ['q' => 'What are Core Web Vitals?', 'a' => 'A specific set of metrics Google uses to measure real-world page experience: loading speed (LCP), visual stability (CLS), and responsiveness (related to TBT/INP).'],
    ['q' => 'Why does a check sometimes take a while?', 'a' => 'Google runs a full page load simulation for every request, which can take 10-20 seconds depending on the page and current load on Google\'s service.'],
  ]])

  @include('partials.seo-related-tools', ['tools' => [
    ['label' => 'On-Page SEO Checker', 'url' => route('seo-tools.on-page-seo-checker'), 'icon' => '📋'],
    ['label' => 'Meta Tag Checker', 'url' => route('seo-tools.meta-tag-checker'), 'icon' => '🔍'],
    ['label' => 'Open Graph Checker', 'url' => route('seo-tools.open-graph-checker'), 'icon' => '🖼️'],
  ]])

  <div class="final-cta">
    <h2>Also Managing Links?</h2>
    <p>klikwit's free URL shortener, QR codes and link analytics live right alongside these SEO tools.</p>
    <a class="cta-btn" href="{{ route('home') }}">Try the URL Shortener</a>
  </div>
</div>

<script>
(function(){
  var form = document.getElementById('f');
  var btn = document.getElementById('btn');
  var err = document.getElementById('err');
  var result = document.getElementById('result');
  var scoreCircle = document.getElementById('scoreCircle');
  var strategyLabel = document.getElementById('strategyLabel');
  var metricsGrid = document.getElementById('metricsGrid');
  var opportunitiesWrap = document.getElementById('opportunitiesWrap');
  var opportunitiesList = document.getElementById('opportunitiesList');
  var strategy = 'mobile';

  document.querySelectorAll('.ps-strategy button').forEach(function(b){
    b.addEventListener('click', function(){
      document.querySelectorAll('.ps-strategy button').forEach(function(x){ x.classList.remove('active'); });
      b.classList.add('active');
      strategy = b.getAttribute('data-strategy');
    });
  });

  function scoreClass(score) {
    if (score >= 90) return 'good';
    if (score >= 50) return 'ok';
    return 'poor';
  }

  form.addEventListener('submit', function(e){
    e.preventDefault();
    err.textContent = '';
    result.style.display = 'none';
    btn.disabled = true;
    btn.textContent = 'Checking (this can take up to 20s)...';
    fetch('{{ url('/api/seo/page-speed-checker') }}', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ url: document.getElementById('url').value.trim(), strategy: strategy })
    }).then(function(r){ return r.json().then(function(j){ return {ok:r.ok, body:j}; }); })
      .then(function(res){
        btn.disabled = false;
        btn.textContent = 'Check Speed';
        if (!res.ok || res.body.score === undefined || res.body.score === null) {
          err.textContent = (res.body && res.body.error) ? res.body.error : 'Something went wrong.';
          return;
        }
        var score = res.body.score;
        scoreCircle.textContent = score;
        scoreCircle.className = 'ps-score ' + scoreClass(score);
        strategyLabel.textContent = strategy === 'mobile' ? 'Mobile' : 'Desktop';

        metricsGrid.textContent = '';
        Object.keys(res.body.metrics || {}).forEach(function(label){
          var value = res.body.metrics[label];
          if (!value) return;
          var tile = document.createElement('div');
          tile.className = 'stat-tile';
          var num = document.createElement('div');
          num.className = 'snum';
          num.style.fontSize = '1.1rem';
          num.textContent = value;
          var lbl = document.createElement('div');
          lbl.className = 'slabel';
          lbl.textContent = label;
          tile.appendChild(num);
          tile.appendChild(lbl);
          metricsGrid.appendChild(tile);
        });

        opportunitiesList.textContent = '';
        var opportunities = res.body.opportunities || [];
        if (opportunities.length > 0) {
          opportunitiesWrap.style.display = 'block';
          opportunities.forEach(function(title){
            var li = document.createElement('li');
            li.textContent = title;
            opportunitiesList.appendChild(li);
          });
        } else {
          opportunitiesWrap.style.display = 'none';
        }

        result.style.display = 'block';
      }).catch(function(){
        btn.disabled = false;
        btn.textContent = 'Check Speed';
        err.textContent = 'Something went wrong. Please try again.';
      });
  });
})();
</script>
@endsection
