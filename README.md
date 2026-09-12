# adlnk
adlinkar site

## এই রিপোতে কী আছে

- `template.xml` — Blogger থিম (Safelink/Short-link জেনারেটর হোমপেজ ফর্ম সহ)।
- `worker/` — নিজের ডোমেইনে সত্যিকারের ছোট লিংক (bit.ly-এর মতো,
  `yourdomain.com/AbC123`) বানানোর ও নিরাপদে রিডাইরেক্ট করার
  Cloudflare Worker ব্যাকএন্ড। ডেপ্লয়মেন্ট ধাপ: `worker/README.md`।

Blogger থিম প্রথমে `worker/README.md`-এর ধাপ অনুসরণ করে ব্যাকএন্ড
ডেপ্লয় করার পর, `template.xml`-এর `SHORTENER_API` ভ্যারিয়েবলে
আপনার ডোমেইন বসিয়ে কাজ করবে।
