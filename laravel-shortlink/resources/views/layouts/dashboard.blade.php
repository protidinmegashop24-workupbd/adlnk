<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="robots" content="noindex,nofollow"/>
<title>@yield('title', 'klikwit')</title>
<link rel="icon" href="{{ asset('favicon.ico') }}"/>
<style>
@include('partials.base-style')
  h1{font-size:1.3rem;text-align:left;margin-top:0}
  .app-shell{display:flex;min-height:100vh;align-items:stretch}
  .sidebar{width:210px;background:#fff;border-right:1px solid #e5e7eb;flex-shrink:0;padding:20px 0}
  .sidebar .brand{display:block;padding:0 20px 24px;font-size:1.15rem;font-weight:bold;color:#222;text-decoration:none}
  .sidebar nav a{display:flex;align-items:center;gap:10px;padding:11px 20px;color:#444;text-decoration:none;font-size:14px}
  .sidebar nav a:hover{background:#f4f6f8}
  .sidebar nav a.active{background:#eaf2ff;color:#0d6efd;font-weight:bold;border-right:3px solid #0d6efd}
  .main-area{flex:1;min-width:0;display:flex;flex-direction:column}
  .topbar{background:#fff;border-bottom:1px solid #e5e7eb;padding:12px 24px;display:flex;justify-content:flex-end;align-items:center;position:relative}
  .avatar{width:36px;height:36px;border-radius:50%;background:#0d6efd;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:bold;cursor:pointer;border:0;font-size:14px;margin:0;padding:0}
  .profile-dropdown{position:absolute;top:58px;right:24px;background:#fff;border:1px solid #e5e7eb;border-radius:8px;box-shadow:0 4px 16px rgba(0,0,0,.12);min-width:220px;display:none;z-index:10;overflow:hidden}
  .profile-dropdown.show{display:block}
  .profile-dropdown .pd-header{padding:14px 16px;border-bottom:1px solid #eee}
  .profile-dropdown .pd-name{font-weight:bold;font-size:14px}
  .profile-dropdown .pd-email{font-size:12px;color:#888;margin-top:2px;word-break:break-all}
  .profile-dropdown a,.profile-dropdown button{display:block;width:100%;text-align:left;padding:10px 16px;font-size:13px;color:#444;text-decoration:none;background:none;border:0;cursor:pointer;margin:0;border-radius:0}
  .profile-dropdown a:hover,.profile-dropdown button:hover{background:#f4f6f8}
  .content{padding:24px;max-width:900px;width:100%;margin:0 auto;box-sizing:border-box}
  @media (max-width:640px){
    .sidebar{width:58px}
    .sidebar .brand{padding:0 0 20px;text-align:center}
    .sidebar .label{display:none}
    .sidebar nav a{justify-content:center;padding:12px 0}
    .sidebar nav a.active{border-right:3px solid #0d6efd}
  }
  @yield('extra-style')
</style>
</head>
<body>
<div class="app-shell">
  <div class="sidebar">
    <a class="brand" href="{{ route('home') }}">🔗<span class="label"> klikwit</span></a>
    <nav>
      <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') || request()->routeIs('dashboard.analytics') ? 'active' : '' }}">🏠 <span class="label">My Links</span></a>
      <a href="{{ route('bio.edit') }}" class="{{ request()->routeIs('bio.*') ? 'active' : '' }}">👤 <span class="label">Link-in-Bio</span></a>
      <a href="{{ route('tools.index') }}">🧰 <span class="label">Tools</span></a>
      @if (auth()->user()?->is_admin)
        <a href="{{ route('admin.reports') }}" class="{{ request()->routeIs('admin.*') ? 'active' : '' }}">🛡️ <span class="label">Abuse Reports</span></a>
      @endif
      <a href="{{ route('home') }}">🌐 <span class="label">Visit Site</span></a>
    </nav>
  </div>
  <div class="main-area">
    <div class="topbar">
      <button class="avatar" id="avatarBtn" type="button">{{ strtoupper(substr(auth()->user()->name ?? '?', 0, 1)) }}</button>
      <div class="profile-dropdown" id="profileDropdown">
        <div class="pd-header">
          <div class="pd-name">{{ auth()->user()->name }}</div>
          <div class="pd-email">{{ auth()->user()->email }}</div>
        </div>
        <a href="{{ route('profile.edit') }}">Profile Settings</a>
        <a href="{{ route('blog.index') }}">Blog</a>
        <a href="{{ route('tools.index') }}">All Tools</a>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit">Sign Out</button>
        </form>
      </div>
    </div>
    <div class="content">
      @yield('content')
    </div>
  </div>
</div>
<script>
(function(){
  var btn = document.getElementById('avatarBtn');
  var dd = document.getElementById('profileDropdown');
  btn.addEventListener('click', function(e){
    e.stopPropagation();
    dd.classList.toggle('show');
  });
  document.addEventListener('click', function(){ dd.classList.remove('show'); });
})();
</script>
</body>
</html>
