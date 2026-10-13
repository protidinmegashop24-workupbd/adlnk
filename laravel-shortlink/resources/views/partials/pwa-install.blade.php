<button id="pwaInstallBtn" type="button" style="display:none;position:fixed;right:16px;bottom:16px;z-index:999;width:auto;margin:0;padding:12px 18px;border-radius:30px;background:#0d6efd;color:#fff;border:0;box-shadow:0 4px 14px rgba(13,110,253,.4);font-size:14px;font-weight:bold;cursor:pointer">
  📱 অ্যাপ ইনস্টল করুন
</button>

<script>
(function () {
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
      navigator.serviceWorker.register('/sw.js').catch(function () {});
    });
  }

  var btn = document.getElementById('pwaInstallBtn');
  var deferredPrompt = null;

  var isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
  if (isStandalone) return;

  window.addEventListener('beforeinstallprompt', function (e) {
    e.preventDefault();
    deferredPrompt = e;
    btn.style.display = 'block';
  });

  btn.addEventListener('click', function () {
    if (!deferredPrompt) return;
    btn.style.display = 'none';
    deferredPrompt.prompt();
    deferredPrompt.userChoice.finally(function () {
      deferredPrompt = null;
    });
  });

  window.addEventListener('appinstalled', function () {
    btn.style.display = 'none';
    deferredPrompt = null;
  });
})();
</script>
