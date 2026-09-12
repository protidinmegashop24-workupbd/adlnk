# adlnk Short Link Backend — ডেপ্লয়মেন্ট গাইড (বাংলা)

এই ফোল্ডারে আছে Cloudflare Worker কোড, যেটা আপনার নিজের ডোমেইনে
`yourdomain.com/AbC123` ফরম্যাটের প্রকৃত short link বানাবে এবং
সেফলি রিডাইরেক্ট করবে (bit.ly-এর মতো, তবে আপনার নিজের সাইট থেকে,
ও মাঝে একটা "সেফলি যাচাই" পেজ + বিজ্ঞাপন স্লট সহ)।

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
৩. ওই লিংকে ভিজিট করলে ৮ সেকেন্ডের একটা "যাচাই হচ্ছে" পেজ দেখাবে
   (এখানে বিজ্ঞাপন বসানো যাবে), তারপর "লিংকে যান" বাটনে ক্লিক করলে
   আসল লিংকে নিয়ে যাবে।

## বিজ্ঞাপন বসানো
`index.js` ফাইলে `<div class="ad-slot" id="ad-top">` এবং
`id="ad-bottom">` — এই দুই জায়গায় আপনার AdSense/অন্য নেটওয়ার্কের
বিজ্ঞাপন কোড বসিয়ে আবার Deploy করলেই ইন্টারস্টিশিয়াল পেজে বিজ্ঞাপন
দেখাবে।

## সীমাবদ্ধতা (Free Plan)
- Cloudflare Workers ফ্রি প্ল্যানে দৈনিক ১ লক্ষ রিকোয়েস্ট পর্যন্ত ফ্রি —
  সাধারণ ব্যবহারের জন্য যথেষ্ট বেশি।
- KV-তে দিনে ১০০,০০০ পড়া ও ১,০০০ লেখা ফ্রি — অনেক ট্রাফিক হলে
  পরে paid প্ল্যান লাগতে পারে।
