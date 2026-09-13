# adlnk Short Link Backend (Laravel)

A minimal Laravel app that powers real short links (`yourdomain.com/AbC123`)
for the `adlnk` Blogger safelink tool. It stores short-code → URL mappings
in MySQL and serves a countdown/ad interstitial page before redirecting.

- `POST /api/shorten` — called by the Blogger theme's Generate button.
- `GET /{code}` — safety-check interstitial page.
- `GET /go/{code}` — performs the actual redirect and counts the click.

See **`README-CPANEL-BN.md`** (বাংলা) for step-by-step cPanel deployment
instructions, or `README-CPANEL-BN.md`'s English-speaking equivalent below
for the short version:

1. Create a MySQL database + user in cPanel.
2. Upload this project (point the domain's document root at `public/`, or
   use the classic shared-hosting `public/*` copy trick — see the Bangla
   guide for both).
3. Copy `.env.example` to `.env`, fill in `APP_URL` and the DB credentials.
4. `php artisan key:generate --force && php artisan migrate --force`
5. Set `SHORTENER_API` in the Blogger theme's `template.xml` to
   `https://yourdomain.com/api/shorten`.
