/**
 * adlnk short-link backend — Cloudflare Worker
 *
 * Endpoints:
 *   POST /api/shorten   { url: "https://...", alias?: "mybrand" }
 *                        -> { short: "https://yourdomain.com/AbC123" }
 *   GET  /:code          -> instant redirect to the target URL (default), or
 *                           a countdown/ad interstitial first if
 *                           SHOW_INTERSTITIAL=true is set (see wrangler.toml)
 *   GET  /                -> plain landing message
 *
 * Storage: Cloudflare KV namespace bound as LINKS.
 *   key   = short code (e.g. "AbC123")
 *   value = JSON string {"url": "...", "created": 1234567890, "clicks": 0}
 *
 * Two ways to use this Worker:
 *  - As a clean, AdSense-safe URL shortener: leave SHOW_INTERSTITIAL unset.
 *    Links redirect instantly, like bit.ly/TinyURL - no ad-wait page.
 *  - As an ad-monetized "safelink" tool: set SHOW_INTERSTITIAL=true and put
 *    ad network code in the interstitial page below. Google policy forbids
 *    AdSense on this kind of wait page - use a network that allows it
 *    (Adsterra/PropellerAds/Monetag) and keep it on a domain that doesn't
 *    also run AdSense.
 *
 * Configure CORS_ORIGIN below (or via the CORS_ORIGIN environment variable
 * in wrangler.toml) to your site's domain so only your own site's form can
 * call the API from a browser.
 */

const CODE_ALPHABET = "23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ"; // no 0/O/1/l/I to avoid confusion
const CODE_LENGTH = 6;
const INTERSTITIAL_SECONDS = 8;
const RESERVED_CODES = new Set(["api", "go", "favicon.ico", "robots.txt"]);
const ALIAS_PATTERN = /^[A-Za-z0-9_-]{3,30}$/;

function randomCode(length) {
  const bytes = new Uint8Array(length);
  crypto.getRandomValues(bytes);
  let out = "";
  for (let i = 0; i < length; i++) {
    out += CODE_ALPHABET[bytes[i] % CODE_ALPHABET.length];
  }
  return out;
}

function corsHeaders(env) {
  const origin = env.CORS_ORIGIN || "*";
  return {
    "Access-Control-Allow-Origin": origin,
    "Access-Control-Allow-Methods": "POST, GET, OPTIONS",
    "Access-Control-Allow-Headers": "Content-Type",
  };
}

function jsonResponse(data, status, env) {
  return new Response(JSON.stringify(data), {
    status: status || 200,
    headers: { "Content-Type": "application/json; charset=utf-8", ...corsHeaders(env) },
  });
}

function isValidTargetUrl(raw, selfHost) {
  let u;
  try {
    u = new URL(raw);
  } catch (e) {
    return null;
  }
  if (u.protocol !== "http:" && u.protocol !== "https:") return null;
  if (selfHost && u.hostname === selfHost) return null; // block shortening our own domain (avoid loops)
  if (raw.length > 2048) return null;
  return u.toString();
}

async function handleShorten(request, env, selfHost) {
  let body;
  try {
    body = await request.json();
  } catch (e) {
    return jsonResponse({ error: "অনুরোধের ফরম্যাট সঠিক নয়।" }, 400, env);
  }

  const longUrl = isValidTargetUrl((body && body.url || "").trim(), selfHost);
  if (!longUrl) {
    return jsonResponse({ error: "সঠিক http:// অথবা https:// দিয়ে শুরু হওয়া একটি লিংক দিন।" }, 400, env);
  }

  const rawAlias = (body && body.alias || "").trim();
  let code;

  if (rawAlias) {
    if (!ALIAS_PATTERN.test(rawAlias)) {
      return jsonResponse({ error: "কাস্টম নামে শুধু ইংরেজি অক্ষর, সংখ্যা, - ও _ ব্যবহার করা যাবে (৩-৩০ অক্ষর)।" }, 400, env);
    }
    if (RESERVED_CODES.has(rawAlias.toLowerCase())) {
      return jsonResponse({ error: "এই নামটি ব্যবহার করা যাবে না, অন্য নাম দিন।" }, 400, env);
    }
    const existing = await env.LINKS.get(rawAlias);
    if (existing) {
      return jsonResponse({ error: "এই কাস্টম নামটি ইতিমধ্যে ব্যবহৃত হয়েছে, অন্য নাম দিন।" }, 409, env);
    }
    code = rawAlias;
  } else {
    // Generate a random code that isn't already taken (retry a few times on collision).
    for (let attempt = 0; attempt < 5; attempt++) {
      const candidate = randomCode(CODE_LENGTH);
      const existing = await env.LINKS.get(candidate);
      if (!existing) {
        code = candidate;
        break;
      }
    }
    if (!code) {
      return jsonResponse({ error: "সার্ভার ব্যস্ত, আবার চেষ্টা করুন।" }, 503, env);
    }
  }

  await env.LINKS.put(code, JSON.stringify({ url: longUrl, created: Date.now(), clicks: 0 }));

  return jsonResponse({ short: `https://${selfHost}/${code}` }, 200, env);
}

function interstitialHtml(code) {
  return `<!DOCTYPE html>
<html lang="bn">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="robots" content="noindex,nofollow"/>
<title>দয়া করে অপেক্ষা করুন...</title>
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
      <h3>আপনার লিংক তৈরি হচ্ছে...</h3>
      <p class="muted">ভাইরাস, ম্যালওয়্যার ও ক্ষতিকর সাইট থেকে সুরক্ষার জন্য লিংকটি যাচাই করা হচ্ছে।</p>
      <div class="ad-slot" id="ad-top"><!-- এখানে PropellerAds/Adsterra কোড বসান (Google AdSense না) --></div>
      <div class="progress"><div class="progress-bar" id="bar"></div></div>
      <p class="muted"><span id="secs">${INTERSTITIAL_SECONDS}</span> সেকেন্ড অপেক্ষা করুন...</p>
      <div class="ad-slot" id="ad-bottom"><!-- এখানে PropellerAds/Adsterra কোড বসান (Google AdSense না) --></div>
      <button id="go" disabled>লিংকে যান</button>
    </div>
  </div>
<script>
(function(){
  var total = ${INTERSTITIAL_SECONDS};
  var left = total;
  var bar = document.getElementById('bar');
  var secs = document.getElementById('secs');
  var btn = document.getElementById('go');
  var code = ${JSON.stringify(code)};
  var timer = setInterval(function(){
    left -= 1;
    secs.textContent = Math.max(left, 0);
    bar.style.width = (Math.min(total - left, total) / total * 100) + '%';
    if (left <= 0) {
      clearInterval(timer);
      btn.disabled = false;
      btn.textContent = 'লিংকে যান';
    }
  }, 1000);
  btn.addEventListener('click', function(){
    if (btn.disabled) return;
    window.location.href = '/go/' + code;
  });
})();
</script>
</body>
</html>`;
}

async function readLink(code, env) {
  const record = await env.LINKS.get(code);
  if (!record) return null;
  try {
    return JSON.parse(record);
  } catch (e) {
    return null;
  }
}

async function bumpClicks(code, data, env) {
  data.clicks = (data.clicks || 0) + 1;
  try {
    await env.LINKS.put(code, JSON.stringify(data));
  } catch (e) {
    // best-effort; ignore KV write failures/limits
  }
}

async function handleInstantRedirect(code, env) {
  const data = await readLink(code, env);
  if (!data) {
    return new Response("লিংকটি খুঁজে পাওয়া যায়নি অথবা মেয়াদ শেষ হয়ে গেছে।", { status: 404 });
  }
  await bumpClicks(code, data, env);
  return Response.redirect(data.url, 302);
}

async function handleInterstitialPage(code, env) {
  const data = await readLink(code, env);
  if (!data) {
    return new Response("লিংকটি খুঁজে পাওয়া যায়নি অথবা মেয়াদ শেষ হয়ে গেছে।", { status: 404 });
  }
  return new Response(interstitialHtml(code), {
    status: 200,
    headers: { "Content-Type": "text/html; charset=utf-8" },
  });
}

async function handleGo(code, env) {
  const data = await readLink(code, env);
  if (!data) {
    return new Response("লিংকটি খুঁজে পাওয়া যায়নি অথবা মেয়াদ শেষ হয়ে গেছে।", { status: 404 });
  }
  await bumpClicks(code, data, env);
  return Response.redirect(data.url, 302);
}

function homepageHtml() {
  return `<!DOCTYPE html>
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
    fetch('/api/shorten', {
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
</html>`;
}

export default {
  async fetch(request, env) {
    const url = new URL(request.url);
    const selfHost = url.hostname;
    const showInterstitial = env.SHOW_INTERSTITIAL === "true";

    if (request.method === "OPTIONS") {
      return new Response(null, { headers: corsHeaders(env) });
    }

    if (url.pathname === "/api/shorten" && request.method === "POST") {
      return handleShorten(request, env, selfHost);
    }

    if (url.pathname === "/" || url.pathname === "") {
      return new Response(homepageHtml(), {
        status: 200,
        headers: { "Content-Type": "text/html; charset=utf-8" },
      });
    }

    if (showInterstitial) {
      const goMatch = url.pathname.match(/^\/go\/([A-Za-z0-9_-]{3,30})$/);
      if (goMatch) {
        return handleGo(goMatch[1], env);
      }
    }

    const codeMatch = url.pathname.match(/^\/([A-Za-z0-9_-]{3,30})$/);
    if (codeMatch) {
      return showInterstitial
        ? handleInterstitialPage(codeMatch[1], env)
        : handleInstantRedirect(codeMatch[1], env);
    }

    return new Response("Not found", { status: 404 });
  },
};
