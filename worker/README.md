# adlnk Short Link Backend — ডেপ্লয়মেন্ট গাইড (বাংলা)

এই ফোল্ডারে আছে Cloudflare Worker কোড, যেটা আপনার নিজের ডোমেইনে
`yourdomain.com/AbC123` ফরম্যাটের প্রকৃত short link বানাবে।

**ডিফল্ট ব্যবহার (সুপারিশকৃত): সাথে সাথে রিডাইরেক্ট**, বিজ্ঞাপন/ওয়েট
পেজ ছাড়া — ঠিক bit.ly/TinyURL-এর মতো। এটা Google AdSense-এর জন্য
সম্পূর্ণ নিরাপদ, তাই আপনার মূল AdSense-চালিত ডোমেইনেই রাখা যায়।

চাইলে `SHOW_INTERSTITIAL=true` সেট করে "যাচাই হচ্ছে + বিজ্ঞাপন" ওয়েট
পেজও চালু করা যায় (PropellerAds/Adsterra-এর মতো নেটওয়ার্কের জন্য —
**Google AdSense না**, কারণ AdSense পলিসি এই ধরনের forced-wait পেজে
বিজ্ঞাপন নিষিদ্ধ করে)। এই মোড ব্যবহার করলে এটাকে আপনার AdSense
ডোমেইন থেকে আলাদা একটা ডোমেইনে রাখুন।

এছাড়াও **কাস্টম লিংক** (custom alias) সাপোর্ট আছে — চাইলে
`yourdomain.com/মনেরমতোনাম` বানানো যায়, র‍্যান্ডম কোডের বদলে।

## কেন Blogger একা এটা করতে পারে না?

Blogger একটা static (কোনো নিজস্ব সার্ভার/ডাটাবেস ছাড়া) হোস্টিং।
তাই কোনো ছোট কোড (`abc123`) মনে রেখে আসল লিংকে পাঠানো — এটা করতে
হলে একটা ছোট ব্যাকএন্ড সার্ভার ও ডাটাবেস লাগবেই। এখানে সেটা
**Cloudflare Workers** (সার্ভার) + **Cloudflare KV** (ডাটাবেস) দিয়ে
বানানো হয়েছে — দুটোই ফ্রি, এবং আপনার নিজের ডোমেইনে চলবে।

## ধাপে ধাপে সেটআপ

### ১. Cloudflare অ্যাকাউন্ট খুলুন
https://dash.cloudflare.com/sign-up — ফ্রি অ্যাকাউন্ট।

### ২. আপনার ডোমেইন Cloudflare-এ যোগ করুন
Cloudflare ড্যাশবোর্ডে "Add a Site" দিয়ে আপনার ডোমেইন যোগ করুন।
Cloudflare আপনাকে দুটো Nameserver ঠিকানা দেবে — সেগুলো আপনার
ডোমেইন যেখান থেকে কেনা (Namecheap, GoDaddy, ইত্যাদি) সেখানে গিয়ে
বসিয়ে দিন। DNS আপডেট হতে কয়েক ঘণ্টা লাগতে পারে।

> এই ধাপ ছাড়া বাকি সব করা যাবে, কিন্তু নিজের ডোমেইনে Worker চালানো
> যাবে না — Cloudflare-এর দেওয়া বিনামূল্যের `*.workers.dev` সাবডোমেইনে
> সাথে সাথেই টেস্ট করা যাবে।

### ৩. KV Namespace তৈরি করুন (ডাটাবেস)
Cloudflare ড্যাশবোর্ডে: **Workers & Pages → KV** → "Create a namespace"
→ নাম দিন `LINKS` → তৈরি করুন। এটার একটা ID দেখাবে, সেটা কপি করে রাখুন।

### ৪. Worker তৈরি করুন
**Workers & Pages → Create Application → Create Worker** → একটা নাম দিন
(যেমন `adlnk-shortener`) → Deploy চাপুন (খালি ডিফল্ট কোড দিয়েই)।
এরপর **Edit code** এ ক্লিক করে ওই এডিটরের ভেতরের সব কোড মুছে
এই ফোল্ডারের `index.js` ফাইলের পুরো কোড পেস্ট করে **Deploy** চাপুন।

### ৫. Worker-এর সাথে KV Namespace যুক্ত করুন
Worker-এর **Settings → Variables → KV Namespace Bindings** এ যান →
"Add binding" → Variable name: `LINKS` → Namespace: (৩ নং ধাপে বানানো
`LINKS` নেমস্পেস বেছে নিন) → Save করুন।

### ৬. আপনার ডোমেইন Worker-এর সাথে যুক্ত করুন
Worker-এর **Settings → Triggers → Custom Domains** → "Add Custom Domain"
→ আপনার ডোমেইন (বা সাবডোমেইন, যেমন `go.yourdomain.com`) লিখুন → Add করুন।
এখন `https://yourdomain.com/` এ গেলে "adlnk shortener is running." দেখাবে।

### ৭. CORS ঠিক করুন (ঐচ্ছিক কিন্তু সুপারিশকৃত)
Worker-এর **Settings → Variables** এ `CORS_ORIGIN` নামে একটা environment
variable যোগ করুন, ভ্যালু দিন আপনার Blogger সাইটের ঠিকানা, যেমন
`https://youradlnk.blogspot.com` (এতে শুধু আপনার নিজের সাইট থেকেই
লিংক শর্ট করার API কল করা যাবে, অন্য কেউ আপনার সার্ভার ব্যবহার
করতে পারবে না)।

### ৮. Blogger থিমে ঠিকানা বসান
`template.xml` ফাইলে `SHORTENER_API` নামের একটা লাইন আছে (কমেন্ট সহ
চিহ্নিত) — সেখানে আপনার Worker-এর ঠিকানা বসান, যেমন:
```js
var SHORTENER_API = "https://yourdomain.com/api/shorten";
```
এরপর `template.xml`-এর পুরো কোড Blogger → Theme → Edit HTML এ পেস্ট
করে Save করুন।

## পরীক্ষা করুন
১. আপনার Blogger হোমপেজে গিয়ে যেকোনো একটা লিংক (http:// বা https://
   দিয়ে শুরু) বসিয়ে **Generate** চাপুন।
২. একটা ছোট লিংক পাবেন, যেমন `https://yourdomain.com/aB3xY9`।
৩. ডিফল্ট মোডে ওই লিংকে ভিজিট করলে সাথে সাথে আসল লিংকে চলে যাবে।

## কাস্টম লিংক (custom alias)
API-তে `alias` ফিল্ড পাঠালে র‍্যান্ডম কোডের বদলে সেই নামটা ব্যবহার
হবে:
```
POST /api/shorten
{"url": "https://example.com", "alias": "mybrand"}
```
→ `{"short": "https://yourdomain.com/mybrand"}`
নামটা ৩-৩০ অক্ষরের, শুধু ইংরেজি অক্ষর/সংখ্যা/-/_ ব্যবহার করা যাবে,
এবং আগে থেকে ব্যবহৃত না হলে।

## ইন্টারস্টিশিয়াল (ওয়েট + বিজ্ঞাপন) মোড চালু করা — ঐচ্ছিক
Worker-এর **Settings → Variables** এ `SHOW_INTERSTITIAL` নামে একটা
ভ্যারিয়েবল যোগ করুন, ভ্যালু দিন `true`। এরপর `index.js` ফাইলে
`<div class="ad-slot" id="ad-top">` এবং `id="ad-bottom">` — এই দুই
জায়গায় আপনার PropellerAds/Adsterra বিজ্ঞাপন কোড বসিয়ে আবার Deploy
করলেই ওয়েট পেজে বিজ্ঞাপন দেখাবে। **এখানে Google AdSense কোড বসাবেন
না** — AdSense পলিসি ভঙ্গ হয়ে অ্যাকাউন্ট ব্যান হতে পারে।

## সীমাবদ্ধতা (Free Plan)
- Cloudflare Workers ফ্রি প্ল্যানে দৈনিক ১ লক্ষ রিকোয়েস্ট পর্যন্ত ফ্রি —
  সাধারণ ব্যবহারের জন্য যথেষ্ট বেশি।
- KV-তে দিনে ১০০,০০০ পড়া ও ১,০০০ লেখা ফ্রি — অনেক ট্রাফিক হলে
  পরে paid প্ল্যান লাগতে পারে।
