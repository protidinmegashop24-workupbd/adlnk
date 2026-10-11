<div id="previewModalOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:1000;align-items:center;justify-content:center;padding:20px">
  <div style="background:#fff;border-radius:10px;max-width:720px;width:100%;max-height:85vh;overflow-y:auto">
    <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 18px;border-bottom:1px solid #eee;position:sticky;top:0;background:#fff">
      <strong>Preview</strong>
      <button type="button" id="previewCloseBtn" style="width:auto;background:none;border:0;font-size:22px;line-height:1;cursor:pointer;color:#888;padding:0;margin:0">&times;</button>
    </div>
    <div id="previewBody" style="margin:18px;padding:18px;border:1px solid #eee;border-radius:8px;line-height:1.7"></div>
  </div>
</div>
<style>
  #previewBody h2{font-size:1.2rem;margin-top:24px}
  #previewBody h3{font-size:1.05rem;margin-top:20px}
  #previewBody p{margin:12px 0}
  #previewBody ul,#previewBody ol{margin:12px 0;padding-left:24px}
  #previewBody img{max-width:100%;height:auto;border-radius:6px}
  #previewBody iframe{max-width:100%;width:100%;aspect-ratio:16/9;height:auto;border-radius:6px;border:0}
  #previewBody table{max-width:100%;display:block;overflow-x:auto}
</style>
<script>
(function(){
  var btn = document.getElementById('previewBtn');
  if (!btn) return;
  var overlay = document.getElementById('previewModalOverlay');
  var closeBtn = document.getElementById('previewCloseBtn');
  var previewBody = document.getElementById('previewBody');
  var previewUrl = @json($previewUrl);

  btn.addEventListener('click', function(){
    var bodyField = document.getElementById('body');
    var modeInput = document.querySelector('.body-mode-input:checked');
    var token = document.querySelector('input[name="_token"]').value;

    fetch(previewUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': token
      },
      body: JSON.stringify({
        body: bodyField.value,
        body_mode: modeInput ? modeInput.value : 'text'
      })
    })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        previewBody.innerHTML = data.html || '<p class="muted">Nothing to preview yet.</p>';
        overlay.style.display = 'flex';
      })
      .catch(function () {
        alert('Preview লোড করা যায়নি। পেজ রিফ্রেশ করে আবার চেষ্টা করুন।');
      });
  });

  closeBtn.addEventListener('click', function () { overlay.style.display = 'none'; });
  overlay.addEventListener('click', function (e) { if (e.target === overlay) overlay.style.display = 'none'; });
})();
</script>
