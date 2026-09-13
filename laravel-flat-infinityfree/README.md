# adlnk Short Link Backend (Laravel) — flat layout for open_basedir hosts

A minimal Laravel app that powers real short links (`yourdomain.com/AbC123`)
for the `adlnk` Blogger safelink tool. It stores short-code → URL mappings
in MySQL and serves a countdown/ad interstitial page before redirecting.

This variant keeps the whole app (not just `public/`) directly at the web
root, because some free/shared hosts (InfinityFree and similar) enforce a
PHP `open_basedir` restriction that only allows reading files inside the
web root — a sibling folder outside it (the normal Laravel deployment
layout, see the sibling `laravel-shortlink/` package) is not readable by
PHP at all on those hosts. Sensitive folders (`app/`, `vendor/`, `.env`,
etc.) are blocked from direct HTTP access via `.htaccess` instead.

If your host lets you point the domain's document root at a `public/`
subfolder (most normal cPanel hosts), use `../laravel-shortlink/` instead
— that layout is the standard, more defensible one.

- `POST /api/shorten` — called by the Blogger theme's Generate button.
- `GET /{code}` — safety-check interstitial page.
- `GET /go/{code}` — performs the actual redirect and counts the click.

See **`README-INFINITYFREE-BN.md`** (বাংলা) for step-by-step deployment
instructions.
