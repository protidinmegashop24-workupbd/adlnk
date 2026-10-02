<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="description" content="@yield('description', 'Free URL shortener, custom links, QR code generator, and bulk link shortening — no signup required.')"/>
<title>@yield('title', 'klikwit — Free URL Shortener & QR Code Generator')</title>
<link rel="canonical" href="{{ url()->current() }}"/>
<link rel="icon" href="{{ asset('favicon.ico') }}"/>
<meta property="og:site_name" content="klikwit"/>
<meta property="og:title" content="@yield('title', 'klikwit — Free URL Shortener & QR Code Generator')"/>
<meta property="og:description" content="@yield('description', 'Free URL shortener, custom links, QR code generator, and bulk link shortening — no signup required.')"/>
<meta property="og:type" content="website"/>
<meta property="og:url" content="{{ url()->current() }}"/>
<style>
@include('partials.base-style')
  .nav{background:#fff;border-bottom:1px solid #e5e7eb;padding:16px 24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px 0}
  .nav > div{display:flex;flex-wrap:wrap;align-items:center}
  .nav a{color:#444;text-decoration:none;font-size:14px;margin-left:20px}
  .nav a:hover{color:#0d6efd}
  .nav .brand{font-size:1.2rem;font-weight:bold;color:#222;text-decoration:none;margin-left:0}
  .nav form{display:inline;margin-left:20px}
  .nav button.linklike{background:none;border:0;color:#444;font-size:14px;cursor:pointer;padding:0;margin:0;width:auto}
  .nav button.linklike:hover{color:#0d6efd}
  .btn-pill{background:#0d6efd;color:#fff !important;padding:9px 20px;border-radius:999px;font-weight:bold;font-size:13px !important}
  .btn-pill:hover{background:#0b5ed7;color:#fff !important}
  @media (max-width:480px){.nav a{margin-left:12px;font-size:13px}}
  .page{max-width:560px;margin:0 auto;padding:24px}
  .page.wide{max-width:800px}
  h1{font-size:1.4rem;text-align:center}
  .hero-dark{background:linear-gradient(135deg,#0a1628,#102844);padding:56px 20px 90px;text-align:center}
  .hero-dark .hero-inner{max-width:680px;margin:0 auto}
  .hero-dark h1{color:#fff;font-size:2rem;margin:0 0 14px;line-height:1.25;text-align:center}
  .hero-dark p{color:#c3cedd;font-size:16px;margin:0}
  .site-footer{background:#fff;border-top:1px solid #e5e7eb;margin-top:48px;padding:32px 24px}
  .footer-inner{max-width:800px;margin:0 auto}
  .footer-links{display:flex;flex-wrap:wrap;gap:8px 24px;justify-content:center}
  .footer-links a{color:#666;text-decoration:none;font-size:13px}
  .footer-links a:hover{color:#0d6efd}
  .footer-copy{text-align:center;color:#999;font-size:12px;margin-top:16px}
  @yield('extra-style')
</style>
</head>
<body>
<div class="nav">
  <a class="brand" href="{{ route('home') }}">🔗 klikwit</a>
  <div>
    <a href="{{ route('tools.index') }}">Tools</a>
    <a href="{{ route('dashboard') }}">Analytics</a>
    <a href="{{ route('blog.index') }}">Blog</a>
    @auth
      <a href="{{ route('dashboard') }}">My Links</a>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="linklike" type="submit">Log Out</button>
      </form>
    @else
      <a href="{{ route('login') }}">Log In</a>
      <a class="btn-pill" href="{{ route('register') }}">Sign Up Free</a>
    @endauth
  </div>
</div>
@hasSection('hero')
  <div class="hero-dark">
    <div class="hero-inner">
      @yield('hero')
    </div>
  </div>
@endif
<div class="page @yield('page-class')">
@yield('content')
</div>
<div class="site-footer">
  <div class="footer-inner">
    <div class="footer-links">
      <a href="{{ route('pages.about') }}">About</a>
      <a href="{{ route('pages.contact') }}">Contact</a>
      <a href="{{ route('blog.index') }}">Blog</a>
      <a href="{{ route('tools.index') }}">Tools</a>
      <a href="{{ route('pages.privacy') }}">Privacy Policy</a>
      <a href="{{ route('pages.terms') }}">Terms of Service</a>
      <a href="{{ route('pages.cookies') }}">Cookie Policy</a>
      <a href="{{ route('pages.aup') }}">Acceptable Use Policy</a>
      <a href="{{ route('pages.dmca') }}">DMCA / Copyright</a>
      <a href="{{ route('report-abuse') }}">Report Abuse</a>
    </div>
    <p class="footer-copy">&copy; {{ date('Y') }} klikwit. All rights reserved.</p>
  </div>
</div>
</body>
</html>
