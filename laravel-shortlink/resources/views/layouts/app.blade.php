<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="description" content="@yield('description', 'Free URL shortener, custom links, QR code generator, and bulk link shortening — no signup required.')"/>
<title>@yield('title', 'klikwit — Free URL Shortener & QR Code Generator')</title>
<link rel="canonical" href="{{ url()->current() }}"/>
@include('partials.favicons')
<meta property="og:site_name" content="klikwit"/>
<meta property="og:title" content="@yield('title', 'klikwit — Free URL Shortener & QR Code Generator')"/>
<meta property="og:description" content="@yield('description', 'Free URL shortener, custom links, QR code generator, and bulk link shortening — no signup required.')"/>
<meta property="og:type" content="website"/>
<meta property="og:url" content="{{ url()->current() }}"/>
<style>
@include('partials.base-style')
  .nav{background:#fff;border-bottom:1px solid #e5e7eb;padding:16px 24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px 0}
  .nav > div{display:flex;flex-wrap:wrap;align-items:center}
  .nav a{color:#444;text-decoration:none;font-size:15px;margin-left:20px}
  .nav a:hover{color:#0d6efd}
  .nav .brand{font-size:1.3rem;font-weight:bold;color:#222;text-decoration:none;margin-left:0;display:inline-flex;align-items:center;gap:8px}
  .nav form{display:inline;margin-left:20px}
  .btn-pill{background:#0d6efd;color:#fff !important;padding:9px 20px;border-radius:999px;font-weight:bold;font-size:14px !important}
  .btn-pill:hover{background:#0b5ed7;color:#fff !important}
  @media (max-width:480px){.nav a{margin-left:12px;font-size:14px}}
  .page{max-width:560px;margin:0 auto;padding:24px}
  .page.wide{max-width:800px}
  .page.blog-wide{max-width:980px}
  h1{font-size:1.6rem;text-align:center}
  .hero-dark{background:linear-gradient(135deg,#0a1628,#102844);padding:56px 20px 90px;text-align:center}
  .hero-dark .hero-inner{max-width:680px;margin:0 auto}
  .hero-dark h1{color:#fff;font-size:2.25rem;margin:0 0 14px;line-height:1.25;text-align:center}
  .hero-dark p{color:#c3cedd;font-size:17px;margin:0}
  .site-footer{background:#fff;border-top:1px solid #e5e7eb;margin-top:48px;padding:32px 24px}
  .footer-inner{max-width:800px;margin:0 auto}
  .footer-links{display:flex;flex-wrap:wrap;gap:8px 24px;justify-content:center}
  .footer-links a{color:#666;text-decoration:none;font-size:14px}
  .footer-links a:hover{color:#0d6efd}
  .footer-copy{text-align:center;color:#999;font-size:13px;margin-top:16px}
  .nav-profile{position:relative;display:inline-block;margin-left:20px}
  .avatar{width:32px;height:32px;border-radius:50%;background:#0d6efd;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:bold;cursor:pointer;border:0;font-size:14px;margin:0;padding:0;vertical-align:middle}
  .profile-dropdown{position:absolute;top:42px;right:0;background:#fff;border:1px solid #e5e7eb;border-radius:8px;box-shadow:0 4px 16px rgba(0,0,0,.12);min-width:200px;display:none;z-index:20;overflow:hidden;text-align:left}
  .profile-dropdown.show{display:block}
  .profile-dropdown .pd-header{padding:12px 16px;border-bottom:1px solid #eee}
  .profile-dropdown .pd-name{font-weight:bold;font-size:15px}
  .profile-dropdown .pd-email{font-size:13px;color:#888;margin-top:2px;word-break:break-all}
  .profile-dropdown form{display:block;margin:0}
  .profile-dropdown a,.profile-dropdown button{display:block;width:100%;text-align:left;padding:10px 16px;font-size:14px;color:#444;text-decoration:none;background:none;border:0;cursor:pointer;margin:0 !important;border-radius:0}
  .profile-dropdown a:hover,.profile-dropdown button:hover{background:#f4f6f8}
  .nav-dropdown{position:relative;display:inline-block;margin-left:20px}
  .nav-dropdown-trigger{background:none;border:0;color:#444;font-size:15px;cursor:pointer;padding:0;margin:0;width:auto;font-family:inherit}
  .nav-dropdown-trigger:hover{color:#0d6efd}
  .nav-dropdown-panel{display:none;position:absolute;top:30px;right:0;background:#fff;border:1px solid #e5e7eb;border-radius:8px;box-shadow:0 4px 16px rgba(0,0,0,.12);padding:16px;z-index:30;min-width:440px;grid-template-columns:1fr 1fr;gap:8px 28px}
  .nav-dropdown-panel.show{display:grid}
  .nav-dropdown-heading{font-size:12px;text-transform:uppercase;letter-spacing:.03em;color:#888;font-weight:bold;margin-bottom:6px}
  .nav-dropdown-col a{display:block;padding:6px 0;color:#333;text-decoration:none;font-size:14px;margin:0 !important}
  .nav-dropdown-col a:hover{color:#0d6efd}
  @media (max-width:640px){
    .nav-dropdown{position:static;margin-left:12px}
    .nav-dropdown.open{flex-basis:100%}
    .nav-dropdown-panel{position:static;box-shadow:none;border:0;min-width:0;width:100%;grid-template-columns:1fr;padding:8px 0 0;margin-top:4px}
  }
  @yield('extra-style')
</style>
</head>
<body>
<div class="nav">
  <a class="brand" href="{{ route('home') }}"><img src="{{ asset('images/logo-mark.png') }}" alt="" width="26" height="26"/> klikwit</a>
  <div>
    @include('partials.tools-nav-dropdown')
    <a href="{{ route('dashboard') }}">Analytics</a>
    <a href="{{ route('blog.index') }}">Blog</a>
    @auth
      <a href="{{ route('dashboard') }}">My Links</a>
      <div class="nav-profile">
        <button class="avatar" id="avatarBtn" type="button">{{ strtoupper(substr(auth()->user()->name ?? '?', 0, 1)) }}</button>
        <div class="profile-dropdown" id="profileDropdown">
          <div class="pd-header">
            <div class="pd-name">{{ auth()->user()->name }}</div>
            <div class="pd-email">{{ auth()->user()->email }}</div>
          </div>
          <a href="{{ route('profile.edit') }}">Profile Settings</a>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Sign Out</button>
          </form>
        </div>
      </div>
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
<script>
(function(){
  var btn = document.getElementById('avatarBtn');
  var dd = document.getElementById('profileDropdown');
  if (!btn || !dd) return;
  btn.addEventListener('click', function(e){
    e.stopPropagation();
    dd.classList.toggle('show');
  });
  document.addEventListener('click', function(){ dd.classList.remove('show'); });
})();
(function(){
  var trigger = document.getElementById('toolsTrigger');
  var panel = document.getElementById('toolsPanel');
  var wrapper = trigger ? trigger.closest('.nav-dropdown') : null;
  if (!trigger || !panel || !wrapper) return;
  function close() {
    panel.classList.remove('show');
    wrapper.classList.remove('open');
    trigger.setAttribute('aria-expanded', 'false');
  }
  trigger.addEventListener('click', function(e){
    e.stopPropagation();
    var isOpen = panel.classList.toggle('show');
    wrapper.classList.toggle('open', isOpen);
    trigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
  });
  panel.addEventListener('click', function(e){ e.stopPropagation(); });
  document.addEventListener('click', close);
  document.addEventListener('keydown', function(e){
    if (e.key === 'Escape') close();
  });
})();
</script>
</body>
</html>
