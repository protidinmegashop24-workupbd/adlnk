# adlnk
adlinkar site

## এই রিপোতে কী আছে

- `template.xml` — Blogger থিম (Safelink/Short-link জেনারেটর হোমপেজ ফর্ম সহ)।
- `laravel-shortlink/` — cPanel + MySQL-এ ডেপ্লয় করার জন্য Laravel
  ব্যাকএন্ড (`public/` ফোল্ডার আলাদা ডকুমেন্ট রুট হিসেবে সেট করা যায় এমন
  হোস্টিং-এর জন্য), যেটা নিজের ডোমেইনে সত্যিকারের ছোট লিংক
  (`yourdomain.com/AbC123`) বানায় ও নিরাপদে রিডাইরেক্ট করে। ডেপ্লয়মেন্ট
  ধাপ: `laravel-shortlink/README-CPANEL-BN.md`।
- `laravel-flat-infinityfree/` — **InfinityFree ও একই রকম ফ্রি
  হোস্টিং-এর জন্য** (যেখানে `open_basedir` restriction থাকে, তাই
  `public/`-এর বাইরে কোনো ফাইল PHP পড়তে পারে না) — একই অ্যাপ কিন্তু
  সবকিছু একই ফোল্ডারে (htdocs-এর রুটে) রাখা, `.htaccess` দিয়ে সংবেদনশীল
  ফোল্ডার ব্লক করা। ডেপ্লয়মেন্ট ধাপ:
  `laravel-flat-infinityfree/README-INFINITYFREE-BN.md`।
- `worker/` — বিকল্প হিসেবে Cloudflare Worker ব্যাকএন্ড (cPanel/ডোমেইন
  ছাড়াই, Cloudflare অ্যাকাউন্ট লাগবে)। ডেপ্লয়মেন্ট ধাপ: `worker/README.md`।

দুটো ব্যাকএন্ডই একই রকম কাজ করে (`POST /api/shorten` → ছোট লিংক,
`GET /কোড` → নিরাপত্তা-যাচাই পেজ → রিডাইরেক্ট), তাই যেকোনো একটা ডেপ্লয়
করে `template.xml`-এর `SHORTENER_API` ভ্যারিয়েবলে সেটার ঠিকানা বসালেই
Blogger সাইট কাজ শুরু করবে।
