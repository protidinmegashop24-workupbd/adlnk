# adlnk Short Link Backend — InfinityFree / open_basedir-restricted হোস্টিং

এই ভার্সনটা সাধারণ `laravel-shortlink` প্যাকেজের থেকে আলাদা — এখানে
`public/` ফোল্ডারের বদলে **সবকিছু একই লেভেলে (htdocs-এর রুটে)** রাখা হয়েছে।

## কখন এই ভার্সন ব্যবহার করবেন
যদি আপনার হোস্টিং-এ (যেমন InfinityFree) `require()`/`include()` করার সময়
এই এরর দেখেন:
```
Warning: require(): open_basedir restriction in effect...
```
তার মানে আপনার হোস্টিং PHP-কে `htdocs`-এর বাইরের কোনো ফাইল পড়তে দেয় না —
এমনকি একই অ্যাকাউন্টের অন্য ফোল্ডার হলেও না। তাই পুরো অ্যাপটাকেই
`htdocs`-এর ভেতরে রাখতে হবে। এই zip ঠিক তার জন্যই প্রস্তুত করা।

## নিরাপত্তা নোট
`.env`, `vendor/`, `app/` ইত্যাদি এখন `htdocs`-এর ভেতরেই আছে (উপায় নেই),
কিন্তু সাথে দেওয়া `.htaccess` ফাইলটা এই ফোল্ডার/ফাইলগুলোতে **সরাসরি ওয়েব
অ্যাক্সেস ব্লক করে দেয়** (কেউ ব্রাউজারে `yourdomain.com/.env` বা
`yourdomain.com/vendor/...` ভিজিট করলে 403 Forbidden পাবে)। PHP নিজে
এগুলো ঠিকই পড়তে পারবে (require/include-এর মাধ্যমে), শুধু বাইরে থেকে HTTP
রিকোয়েস্ট ব্লক হবে।

**গুরুত্বপূর্ণ:** `.htaccess` আপলোড করার পর অবশ্যই টেস্ট করুন যে
`https://yourdomain.com/.env` এবং `https://yourdomain.com/vendor/autoload.php`
ভিজিট করলে সত্যিই 403/Forbidden দেখায় (খালি পেজ বা ফাইলের কনটেন্ট না)।

## সেটআপ ধাপ

1. **MySQL ডাটাবেস বানান** — আপনার control panel-এ MySQL Databases পেজ
   থেকে (InfinityFree-তে ডাটাবেস পাসওয়ার্ড = আপনার vPanel লগইন পাসওয়ার্ড,
   আলাদা পাসওয়ার্ড না)।
2. **এই zip-এর সবকিছু সরাসরি `htdocs`-এ আপলোড/এক্সট্র্যাক্ট করুন** —
   কোনো সাবফোল্ডার ছাড়া, htdocs-এর রুটেই।
3. `.env.example`-কে `.env` নাম দিয়ে কপি করুন, ডাটাবেসের তথ্য বসান
   (`DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`), `APP_URL`
   আপনার ডোমেইন দিয়ে ঠিক করুন।
4. `https://yourdomain.com/setup.php?run=1` ভিজিট করুন — APP_KEY তৈরি ও
   ডাটাবেস টেবিল বানাবে।
5. সব ✔ হলে `setup.php` ডিলিট করে দিন, আর `.env`-এ `APP_DEBUG=false`
   করে দিন।
6. Blogger থিমের `SHORTENER_API`-তে `https://yourdomain.com/api/shorten`
   বসান।
