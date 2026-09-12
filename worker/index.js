/**
 * adlnk safelink/short-link backend — Cloudflare Worker
 *
 * Endpoints:
 *   POST /api/shorten   { url: "https://..." }  -> { short: "https://yourdomain.com/AbC123" }
 *   GET  /:code                                 -> interstitial "safe" page, then redirect
 *   GET  /                                      -> plain landing message
 *
 * Storage: Cloudflare KV namespace bound as LINKS.
 *   key   = short code (e.g. "AbC123")
 *   value = JSON string {"url": "...", "created": 1234567890, "clicks": 0}
 *
 * Configure CORS_ORIGIN below (or via the CORS_ORIGIN environment variable
 * in wrangler.toml) to your Blogger domain so only your own site's form can
 * call the API from a browser.
 */

const CODE_ALPHABET = "23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ"; // no 0/O/1/l/I to avoid confusion
const CODE_LENGTH = 6;
const INTERSTITIAL_SECONDS = 8;

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

  // Generate a code that isn't already taken (retry a few times on collision).
  let code;
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
      <div class="ad-slot" id="ad-top"><!-- এখানে আপনার AdSense/বিজ্ঞাপন কোড বসান --></div>
      <div class="progress"><div class="progress-bar" id="bar"></div></div>
      <p class="muted"><span id="secs">${INTERSTITIAL_SECONDS}</span> সেকেন্ড অপেক্ষা করুন...</p>
      <div class="ad-slot" id="ad-bottom"><!-- এখানে আপনার AdSense/বিজ্ঞাপন কোড বসান --></div>
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

async function handleRedirectPage(code, env) {
  const record = await env.LINKS.get(code);
  if (!record) {
    return new Response("লিংকটি খুঁজে পাওয়া যায়নি অথবা মেয়াদ শেষ হয়ে গেছে।", { status: 404 });
  }
  return new Response(interstitialHtml(code), {
    status: 200,
    headers: { "Content-Type": "text/html; charset=utf-8" },
  });
}

async function handleGo(code, env) {
  const record = await env.LINKS.get(code);
  if (!record) {
    return new Response("লিংকটি খুঁজে পাওয়া যায়নি অথবা মেয়াদ শেষ হয়ে গেছে।", { status: 404 });
  }
  let data;
  try {
    data = JSON.parse(record);
  } catch (e) {
    return new Response("লিংক ডেটা ক্ষতিগ্রস্ত।", { status: 500 });
  }
  // best-effort click counter; ignore failures (KV write limits)
  data.clicks = (data.clicks || 0) + 1;
  try {
    await env.LINKS.put(code, JSON.stringify(data));
  } catch (e) {
    // ignore
  }
  return Response.redirect(data.url, 302);
}

export default {
  async fetch(request, env) {
    const url = new URL(request.url);
    const selfHost = url.hostname;

    if (request.method === "OPTIONS") {
      return new Response(null, { headers: corsHeaders(env) });
    }

    if (url.pathname === "/api/shorten" && request.method === "POST") {
      return handleShorten(request, env, selfHost);
    }

    if (url.pathname === "/" || url.pathname === "") {
      return new Response("adlnk shortener is running.", { status: 200 });
    }

    const goMatch = url.pathname.match(/^\/go\/([A-Za-z0-9]{4,12})$/);
    if (goMatch) {
      return handleGo(goMatch[1], env);
    }

    const codeMatch = url.pathname.match(/^\/([A-Za-z0-9]{4,12})$/);
    if (codeMatch) {
      return handleRedirectPage(codeMatch[1], env);
    }

    return new Response("Not found", { status: 404 });
  },
};
