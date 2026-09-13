# adlnk
adlinkar site

## এই রিপোতে কী আছে

- `template.xml` — Blogger থিম (Safelink/Short-link জেনারেটর হোমপেজ ফর্ম সহ)।
- `laravel-shortlink/` — **(সুপারিশকৃত)** cPanel + MySQL-এ ডেপ্লয় করার
  জন্য Laravel ব্যাকএন্ড, যেটা নিজের ডোমেইনে সত্যিকারের ছোট লিংক
  (`yourdomain.com/AbC123`) বানায় ও নিরাপদে রিডাইরেক্ট করে। ডেপ্লয়মেন্ট
  ধাপ: `laravel-shortlink/README-CPANEL-BN.md`।
- `worker/` — বিকল্প হিসেবে Cloudflare Worker ব্যাকএন্ড (cPanel/ডোমেইন
  ছাড়াই, Cloudflare অ্যাকাউন্ট লাগবে)। ডেপ্লয়মেন্ট ধাপ: `worker/README.md`।

দুটো ব্যাকএন্ডই একই রকম কাজ করে (`POST /api/shorten` → ছোট লিংক,
`GET /কোড` → নিরাপত্তা-যাচাই পেজ → রিডাইরেক্ট), তাই যেকোনো একটা ডেপ্লয়
করে `template.xml`-এর `SHORTENER_API` ভ্যারিয়েবলে সেটার ঠিকানা বসালেই
Blogger সাইট কাজ শুরু করবে।
