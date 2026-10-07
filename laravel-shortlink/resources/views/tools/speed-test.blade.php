@extends('layouts.app')

@section('title', 'Internet Speed Test | Klikwit')
@section('description', 'Test your internet connection speed — download, upload and latency — free, right in your browser.')
@section('page-class', 'wide')
@section('extra-style')
  .st-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-top:20px}
  @media (max-width:560px){.st-grid{grid-template-columns:1fr}}
  .st-tile{background:#fff;border:1px solid #eee;border-radius:10px;padding:24px 16px;text-align:center}
  .st-tile .st-icon{font-size:1.4rem}
  .st-tile .st-num{font-size:2rem;font-weight:bold;color:#0d6efd;margin-top:6px}
  .st-tile .st-unit{font-size:12px;color:#888;margin-top:2px}
  .st-tile .st-label{font-size:13px;color:#555;margin-top:8px;font-weight:bold}
  .st-status{text-align:center;color:#666;font-size:14px;margin-top:16px;min-height:20px}
@endsection

@section('content')
<div class="tool-section">
  @include('partials.seo-breadcrumb', ['crumbs' => [
    ['label' => 'Home', 'url' => route('home')],
    ['label' => 'Tools', 'url' => route('tools.index')],
    ['label' => 'Internet Speed Test'],
  ]])

  <h1>Internet Speed Test</h1>
  <p>Test your connection's download speed, upload speed, and latency — free, right in your browser.</p>

  <div class="card" style="text-align:center">
    <button id="startBtn" type="button" style="max-width:280px;margin:0 auto">Start Test</button>
    <div class="st-status" id="status"></div>

    <div class="st-grid">
      <div class="st-tile">
        <div class="st-icon">📶</div>
        <div class="st-num" id="pingNum">–</div>
        <div class="st-unit">ms</div>
        <div class="st-label">Ping</div>
      </div>
      <div class="st-tile">
        <div class="st-icon">⬇️</div>
        <div class="st-num" id="downloadNum">–</div>
        <div class="st-unit">Mbps</div>
        <div class="st-label">Download</div>
      </div>
      <div class="st-tile">
        <div class="st-icon">⬆️</div>
        <div class="st-num" id="uploadNum">–</div>
        <div class="st-unit">Mbps</div>
        <div class="st-label">Upload</div>
      </div>
    </div>
  </div>

  <div class="notice-box">
    This measures the connection between your browser and klikwit's own server — not an average across many global servers the way some dedicated speed-test services work. It's a real, direct measurement, just scoped to this one route.
  </div>

  <h2>What Does This Measure?</h2>
  <p><strong>Ping</strong> is the round-trip time for a small request to reach klikwit's server and come back — lower is better, and it affects how responsive a connection feels (video calls, gaming). <strong>Download</strong> and <strong>upload</strong> measure how fast your connection can receive and send data, by timing a real file transfer of known size to and from klikwit's server.</p>

  <h2>Why Might This Differ From Other Speed Tests?</h2>
  <p>Every speed test measures the path to whichever server it uses. A dedicated speed-test service often picks a server near you from a large network; this test always uses klikwit's own server. Results can differ from other tools for that reason — neither is "wrong," they're measuring different paths.</p>

  <h2>Tips for an Accurate Reading</h2>
  <ul>
    <li>Close other apps or browser tabs using the internet (video calls, downloads, streaming) while testing.</li>
    <li>Run the test a few times — results can vary between runs.</li>
    <li>Wi-Fi results are usually lower and less consistent than a wired connection.</li>
  </ul>

  @include('partials.seo-faq', ['faqs' => [
    ['q' => 'Does this test use a lot of data?', 'a' => 'Each full test transfers a few megabytes in each direction — comparable to loading a couple of web pages. It\'s not something to run constantly on a limited data plan.'],
    ['q' => 'Why is my upload speed lower than download?', 'a' => 'Most home internet connections (especially cable and DSL) are designed asymmetrically, with more capacity allocated to download than upload. This is normal.'],
    ['q' => 'Why did my result change between tests?', 'a' => 'Network conditions fluctuate — other devices on your network, your ISP\'s current load, and even background activity on your own device can all affect a single test run.'],
  ]])

  <div class="final-cta">
    <h2>Also Managing Links?</h2>
    <p>klikwit's free URL shortener, QR codes and link analytics live right alongside this tool.</p>
    <a class="cta-btn" href="{{ route('home') }}">Try the URL Shortener</a>
  </div>
</div>

<script>
(function(){
  var startBtn = document.getElementById('startBtn');
  var status = document.getElementById('status');
  var pingNum = document.getElementById('pingNum');
  var downloadNum = document.getElementById('downloadNum');
  var uploadNum = document.getElementById('uploadNum');

  function reset() {
    pingNum.textContent = '–';
    downloadNum.textContent = '–';
    uploadNum.textContent = '–';
  }

  function measurePing() {
    var samples = [];
    var runs = 5;

    function oneRun(i) {
      if (i >= runs) {
        samples.sort(function(a,b){ return a - b; });
        var median = samples[Math.floor(samples.length / 2)];
        pingNum.textContent = Math.round(median);
        return Promise.resolve();
      }
      var start = performance.now();
      return fetch('{{ url('/api/speedtest/download') }}?bytes=256&_=' + Math.random(), { cache: 'no-store' })
        .then(function(r){ return r.arrayBuffer(); })
        .then(function(){
          if (i > 0) samples.push(performance.now() - start); // discard first (connection warm-up)
          return oneRun(i + 1);
        });
    }

    return oneRun(0);
  }

  function measureDownload() {
    var size = 4 * 1024 * 1024; // 4 MB
    var start = performance.now();
    return fetch('{{ url('/api/speedtest/download') }}?bytes=' + size + '&_=' + Math.random(), { cache: 'no-store' })
      .then(function(r){ return r.arrayBuffer(); })
      .then(function(buf){
        var seconds = (performance.now() - start) / 1000;
        var mbps = (buf.byteLength * 8 / 1000000) / seconds;
        downloadNum.textContent = mbps.toFixed(1);
      });
  }

  function measureUpload() {
    var size = 2 * 1024 * 1024; // 2 MB
    var payload = new Blob([new ArrayBuffer(size)]);
    var start = performance.now();
    return fetch('{{ url('/api/speedtest/upload') }}', { method: 'POST', body: payload })
      .then(function(r){ return r.json(); })
      .then(function(){
        var seconds = (performance.now() - start) / 1000;
        var mbps = (size * 8 / 1000000) / seconds;
        uploadNum.textContent = mbps.toFixed(1);
      });
  }

  startBtn.addEventListener('click', function(){
    startBtn.disabled = true;
    reset();

    status.textContent = 'Testing ping...';
    measurePing()
      .then(function(){
        status.textContent = 'Testing download speed...';
        return measureDownload();
      })
      .then(function(){
        status.textContent = 'Testing upload speed...';
        return measureUpload();
      })
      .then(function(){
        status.textContent = 'Done.';
        startBtn.disabled = false;
      })
      .catch(function(){
        status.textContent = 'Something went wrong. Please try again.';
        startBtn.disabled = false;
      });
  });
})();
</script>
@endsection
