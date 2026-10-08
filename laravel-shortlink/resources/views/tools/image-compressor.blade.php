@extends('layouts.app')

@section('title', 'Image Compressor & WebP Converter | Klikwit')
@section('description', 'Compress images and convert to WebP, right in your browser upload — free, no signup. Reduce file size for faster-loading blog posts.')
@section('page-class', 'wide')
@section('extra-style')
  .ic-row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
  @media (max-width:560px){.ic-row{grid-template-columns:1fr}}
  .ic-quality-val{font-size:13px;color:#666;text-align:right}
  .ic-result{text-align:center}
  .ic-result img{max-width:100%;max-height:320px;border:1px solid #eee;border-radius:8px;margin-top:12px}
  .ic-sizes{display:flex;justify-content:center;gap:24px;margin-top:16px}
  .ic-sizes div{text-align:center}
  .ic-sizes .sval{font-size:1.3rem;font-weight:bold}
  .ic-sizes .slabel{font-size:12px;color:#888;text-transform:uppercase}
  .ic-sizes .sval.smaller{color:#1a7f37}
  .ic-download{display:inline-block;background:#28a745;color:#fff;text-decoration:none;padding:12px 24px;border-radius:6px;font-weight:bold;margin-top:16px}
@endsection

@section('content')
<div class="tool-section">
  @include('partials.seo-breadcrumb', ['crumbs' => [
    ['label' => 'Home', 'url' => route('home')],
    ['label' => 'Tools', 'url' => route('tools.index')],
    ['label' => 'Image Compressor'],
  ]])

  <h1>Image Compressor &amp; WebP Converter</h1>
  <p>Upload a JPEG, PNG, WebP or GIF to compress it and optionally convert to WebP — useful for shrinking blog images so pages load faster.</p>

  <div class="card">
    <form id="f">
      <label for="imageFile">Image File <span class="hint" style="display:inline">(max 5 MB)</span></label>
      <input id="imageFile" type="file" accept="image/jpeg,image/png,image/webp,image/gif" required/>

      <div class="ic-row">
        <div>
          <label for="maxWidth">Max Width (px) <span class="hint" style="display:inline">(optional)</span></label>
          <input id="maxWidth" type="number" min="50" max="10000" placeholder="e.g. 1200"/>
        </div>
        <div>
          <label for="format">Output Format</label>
          <select id="format">
            <option value="keep">Keep Original Format</option>
            <option value="webp">Convert to WebP</option>
            <option value="jpeg">Convert to JPEG</option>
            <option value="png">Convert to PNG</option>
          </select>
        </div>
      </div>

      <label for="quality">Quality: <span id="qualityVal">80</span></label>
      <input id="quality" type="range" min="10" max="100" value="80" style="width:100%"/>

      <button id="btn" type="submit">Compress Image</button>
      <div class="error" id="err"></div>
    </form>

    <div class="tool-result ic-result" id="result" style="display:none">
      <img id="resultImg" alt="Compressed result"/>
      <div class="ic-sizes">
        <div><div class="sval" id="originalSize">–</div><div class="slabel">Original</div></div>
        <div><div class="sval smaller" id="newSize">–</div><div class="slabel">Compressed</div></div>
        <div><div class="sval smaller" id="savedPct">–</div><div class="slabel">Smaller</div></div>
      </div>
      <a class="ic-download" id="downloadLink" download="compressed">Download</a>
    </div>
  </div>

  <h2>What Does This Tool Do?</h2>
  <p>It re-encodes an image with adjustable quality (and optionally resizes it to a maximum width), which usually produces a noticeably smaller file. Converting to WebP often shrinks file size further compared to JPEG or PNG at a similar visual quality.</p>

  <h2>Why Is It Useful?</h2>
  <p>Large, unoptimized images are one of the most common causes of slow-loading pages — which affects both visitor experience and page speed scores. Compressing images before uploading them to a blog or website is one of the simplest ways to speed up a page.</p>

  <h2>How to Use It</h2>
  <p>Choose an image, optionally set a maximum width (useful if the original is much larger than it will ever be displayed), pick an output format, and adjust the quality slider. Click "Compress Image," then download the result.</p>

  <h2>How to Interpret the Results</h2>
  <p>Lower quality values produce smaller files but can introduce visible artifacts, especially on images with sharp edges or text. There's no single "correct" quality setting — try a few values and compare the preview until the size/quality trade-off looks right for your use.</p>

  <h2>Limitations</h2>
  <p>Processing happens entirely on klikwit's server for this request — nothing is stored afterward. Very large images (by pixel count) are rejected to avoid server memory issues; resize them with another tool first if needed. This tool doesn't strip EXIF/metadata selectively or batch-process multiple files at once.</p>

  @include('partials.seo-faq', ['faqs' => [
    ['q' => 'Is my uploaded image stored anywhere?', 'a' => 'No — the image is processed for this one request and the result is returned directly to your browser. It isn\'t saved on klikwit\'s server afterward.'],
    ['q' => 'Why is WebP usually smaller than JPEG?', 'a' => 'WebP uses more modern compression techniques that typically achieve a smaller file size than JPEG at a similar visual quality, though results vary by image.'],
    ['q' => 'Will converting to WebP work everywhere?', 'a' => 'All modern browsers support WebP. If you need universal compatibility with very old software, keep a JPEG or PNG version as a fallback.'],
    ['q' => 'What quality setting should I use for blog images?', 'a' => 'A common starting point is 70-85 — high enough that compression artifacts aren\'t noticeable, low enough to meaningfully reduce file size. Compare the preview and adjust from there.'],
  ]])

  @include('partials.seo-related-tools', ['tools' => [
    ['label' => 'Page Speed Checker', 'url' => route('seo-tools.page-speed-checker'), 'icon' => '⚡'],
    ['label' => 'On-Page SEO Checker', 'url' => route('seo-tools.on-page-seo-checker'), 'icon' => '📋'],
  ]])

  <div class="final-cta">
    <h2>Also Managing Links?</h2>
    <p>klikwit's free URL shortener, QR codes and link analytics live right alongside these tools.</p>
    <a class="cta-btn" href="{{ route('home') }}">Try the URL Shortener</a>
  </div>
</div>

<script>
(function(){
  var form = document.getElementById('f');
  var btn = document.getElementById('btn');
  var err = document.getElementById('err');
  var result = document.getElementById('result');
  var quality = document.getElementById('quality');
  var qualityVal = document.getElementById('qualityVal');
  var resultImg = document.getElementById('resultImg');
  var originalSize = document.getElementById('originalSize');
  var newSize = document.getElementById('newSize');
  var savedPct = document.getElementById('savedPct');
  var downloadLink = document.getElementById('downloadLink');

  quality.addEventListener('input', function(){ qualityVal.textContent = quality.value; });

  function formatBytes(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
  }

  form.addEventListener('submit', function(e){
    e.preventDefault();
    err.textContent = '';
    result.style.display = 'none';

    var fileInput = document.getElementById('imageFile');
    if (!fileInput.files[0]) {
      err.textContent = 'Please choose an image file.';
      return;
    }

    var fd = new FormData();
    fd.append('image', fileInput.files[0]);
    var maxWidth = document.getElementById('maxWidth').value.trim();
    if (maxWidth) fd.append('max_width', maxWidth);
    fd.append('format', document.getElementById('format').value);
    fd.append('quality', quality.value);

    btn.disabled = true;
    btn.textContent = 'Compressing...';
    fetch('{{ url('/api/image/compress') }}', { method: 'POST', body: fd })
      .then(function(r){ return r.json().then(function(j){ return {ok:r.ok, body:j}; }); })
      .then(function(res){
        btn.disabled = false;
        btn.textContent = 'Compress Image';
        if (!res.ok || !res.body.data_url) {
          err.textContent = (res.body && res.body.error) ? res.body.error : 'Something went wrong.';
          return;
        }

        resultImg.src = res.body.data_url;
        originalSize.textContent = formatBytes(res.body.original_bytes);
        newSize.textContent = formatBytes(res.body.new_bytes);
        var pct = Math.max(0, Math.round((1 - res.body.new_bytes / res.body.original_bytes) * 100));
        savedPct.textContent = pct + '%';
        downloadLink.href = res.body.data_url;
        downloadLink.download = 'compressed.' + res.body.extension;

        result.style.display = 'block';
      }).catch(function(){
        btn.disabled = false;
        btn.textContent = 'Compress Image';
        err.textContent = 'Something went wrong. Please try again.';
      });
  });
})();
</script>
@endsection
