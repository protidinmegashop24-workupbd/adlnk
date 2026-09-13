# adlnk Short Link Backend (Laravel) — cPanel ডেপ্লয়মেন্ট গাইড

এটা একটা সম্পূর্ণ Laravel অ্যাপ্লিকেশন যেটা আপনার নিজের ডোমেইনে সত্যিকারের
ছোট লিংক (`yourdomain.com/AbC123`) বানাবে এবং নিরাপদে (একটা "যাচাই হচ্ছে +
বিজ্ঞাপন" পেজ দেখিয়ে) রিডাইরেক্ট করবে — bit.ly-এর মতো।

এই zip-এ **`vendor/` ফোল্ডারসহ সবকিছু আগে থেকেই তৈরি করা আছে** — তাই
Composer/SSH ছাড়াই, শুধু File Manager দিয়ে আপলোড করলেই চলবে।

## যা যা লাগবে
- cPanel হোস্টিং, যেখানে PHP 8.2+ ও MySQL আছে (প্রায় সব cPanel হোস্টিংয়েই থাকে)।
- আপনার ডোমেইন/সাবডোমেইন ওই cPanel-এর সাথে যুক্ত থাকা।

## ধাপ ১: MySQL ডাটাবেস তৈরি করুন
cPanel → **MySQL Database Wizard**:
1. একটা ডাটাবেস বানান (যেমন `adlnk`) — cPanel এটার নাম দেবে
   `cpaneluser_adlnk` এর মতো।
2. একটা ইউজার বানান, পাসওয়ার্ড দিন (মনে রাখুন/লিখে রাখুন)।
3. ইউজারকে ডাটাবেসের সাথে যুক্ত করুন, **All Privileges** দিন।

## ধাপ ২: ফাইল আপলোড করুন
cPanel-এ দুইভাবে করা যায় — যেটা আপনার হোস্টিং সাপোর্ট করে সেটা করুন:

### পদ্ধতি A — হোস্টিং যদি "Application Root" আলাদা করে সেট করা যায় (ভালো, সুপারিশকৃত)
অনেক আধুনিক cPanel (Domains → Manage → Document Root, অথবা
"Setup PHP Application"/Softaculous-এর মাধ্যমে):
1. পুরো প্রজেক্ট ফোল্ডার (`adlnk-shortlink`) আপলোড করুন হোম ডিরেক্টরির
   বাইরে কোথাও, যেমন `~/adlnk-shortlink/`।
2. ডোমেইনের **Document Root**-কে `~/adlnk-shortlink/public` এ সেট করুন।
3. ব্যস, এটাই যথেষ্ট।

### পদ্ধতি B — সাধারণ শেয়ার্ড হোস্টিং (Document Root বদলানো যায় না)
তাহলে `public_html`-এর ভেতরেই বসাতে হবে, একটু অ্যাডজাস্ট করে:
1. পুরো প্রজেক্ট (ভেতরের সবকিছুসহ, `vendor` ফোল্ডারসহ) আপলোড করুন
   `public_html`-এর **বাইরে**, যেমন `~/adlnk-app/` নামে একটা ফোল্ডারে।
2. `~/adlnk-app/public/` ফোল্ডারের **ভেতরের সবকিছু** (`index.php`,
   `.htaccess`, `favicon.ico` ইত্যাদি) কপি করে `public_html/`-এ (অথবা
   আপনার সাবডোমেইনের ফোল্ডারে) নিয়ে আসুন।
3. `public_html/index.php` ফাইলটা খুলে এই দুই লাইন বদলে দিন:
   ```php
   require __DIR__.'/../vendor/autoload.php';
   ```
   → বদলে করুন:
   ```php
   require __DIR__.'/../adlnk-app/vendor/autoload.php';
   ```
   এবং
   ```php
   $app = require_once __DIR__.'/../bootstrap/app.php';
   ```
   → বদলে করুন:
   ```php
   $app = require_once __DIR__.'/../adlnk-app/bootstrap/app.php';
   ```
   (আপনি যদি `adlnk-app` ছাড়া অন্য ফোল্ডার-নাম ব্যবহার করেন, সেই নাম বসান।)

## ধাপ ৩: `.env` ফাইল বানান
প্রজেক্টের রুটে (public নয়, মূল ফোল্ডারে) `.env.example` ফাইলটাকে
`.env` নামে কপি করুন এবং cPanel File Manager দিয়ে খুলে এডিট করুন:
- `APP_URL` → আপনার ডোমেইন, যেমন `https://yourdomain.com`
- `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` → ধাপ ১-এ বানানো তথ্য দিয়ে পূরণ করুন
- `DB_HOST` সাধারণত `127.0.0.1` অথবা `localhost` থাকে, বেশিরভাগ cPanel-এ ঠিকই আছে

## ধাপ ৪: APP_KEY ও মাইগ্রেশন চালান
cPanel-এ **Terminal** থাকলে (Advanced → Terminal):
```bash
cd ~/adlnk-app   # আপনার প্রজেক্ট ফোল্ডারে যান
php artisan key:generate --force
php artisan migrate --force
```

Terminal না থাকলে, cPanel-এর **"Setup PHP App"** ফিচার ব্যবহার করলে
সেখান থেকেও এই কমান্ডগুলো "Run Composer"/"Execute" অপশন দিয়ে চালানো
যায় (হোস্টিং অনুযায়ী নাম আলাদা হতে পারে) — অথবা আপনার হোস্টিং সাপোর্টকে
বলুন "APP_KEY generate ও migrate করে দিতে", এটা একটা সাধারণ Laravel
কমান্ড, যেকোনো হোস্টিং সাপোর্ট চেনে।

## ধাপ ৫: `storage` ও `bootstrap/cache` ফোল্ডারে লেখার অনুমতি দিন
File Manager-এ `storage/` এবং `bootstrap/cache/` ফোল্ডারে right-click →
Permissions → **755** (বা প্রয়োজনে 775) সেট করুন।

## ধাপ ৬: টেস্ট করুন
- `https://yourdomain.com/` এ ভিজিট করলে "adlnk shortener is running" দেখাবে।
- `https://yourdomain.com/api/shorten`-এ একটা টুল (যেমন Postman বা
  ব্রাউজারের কনসোল থেকে) দিয়ে POST করে দেখুন:
  ```
  POST https://yourdomain.com/api/shorten
  Content-Type: application/json
  Body: {"url": "https://example.com"}
  ```
  সফল হলে `{"short": "https://yourdomain.com/AbC123"}` এর মতো ফেরত আসবে।

## ধাপ ৭: Blogger থিমে ঠিকানা বসান
`template.xml`-এর `SHORTENER_API` ভ্যারিয়েবলে বসান:
```js
var SHORTENER_API = "https://yourdomain.com/api/shorten";
```
এরপর Blogger হোমপেজ থেকে Generate করে সত্যিকারের শর্ট লিংক পাবেন।

## বিজ্ঞাপন বসানো
`resources/views/redirect.blade.php` ফাইলে `id="ad-top"` ও
`id="ad-bottom"` — এই দুই জায়গায় আপনার AdSense/অন্য নেটওয়ার্কের কোড
বসান।

## নিরাপত্তা (ঐচ্ছিক কিন্তু সুপারিশকৃত)
- `.env`-এ `APP_DEBUG=false` রাখুন (প্রোডাকশনে এটাই ডিফল্ট রাখা আছে) —
  এতে কোনো এরর হলে ভিজিটরদের কাছে টেকনিক্যাল ডিটেইল দেখাবে না।
- `config/cors.php`-এ `allowed_origins` কে `['*']` থেকে বদলে শুধু আপনার
  Blogger ঠিকানা দিয়ে দিন, যাতে অন্য কেউ আপনার শর্টেনার ব্যবহার করতে
  না পারে।
