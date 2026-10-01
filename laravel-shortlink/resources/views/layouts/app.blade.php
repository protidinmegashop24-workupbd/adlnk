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
  body{font-family:Arial,Helvetica,sans-serif;background:#f4f6f8;margin:0;padding:0;color:#222}
  .nav{background:#fff;border-bottom:1px solid #e5e7eb;padding:14px 24px;display:flex;align-items:center;justify-content:space-between}
  .nav a{color:#0d6efd;text-decoration:none;font-size:14px;margin-left:16px}
  .nav a:hover{text-decoration:underline}
  .nav .brand{font-size:1.1rem;font-weight:bold;color:#222;text-decoration:none;margin-left:0}
  .nav form{display:inline;margin-left:16px}
  .nav button.linklike{background:none;border:0;color:#0d6efd;font-size:14px;cursor:pointer;padding:0;margin:0;width:auto}
  .page{max-width:560px;margin:0 auto;padding:24px}
  .page.wide{max-width:800px}
  h1{font-size:1.4rem;text-align:center}
  .tabs{display:flex;gap:8px;margin-top:20px}
  .tab{flex:1;background:#e9ecef;color:#444;border:0;padding:10px;border-radius:6px;font-size:14px;cursor:pointer}
  .tab.active{background:#0d6efd;color:#fff}
  .card{background:#fff;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,.1);padding:24px;margin-top:16px}
  label{display:block;font-size:13px;color:#555;margin:12px 0 4px}
  input[type=text],input[type=url],input[type=email],input[type=password]{width:100%;box-sizing:border-box;padding:10px;border:1px solid #ccc;border-radius:6px;font-size:15px}
  textarea{width:100%;box-sizing:border-box;padding:10px;border:1px solid #ccc;border-radius:6px;font-size:14px;font-family:inherit;resize:vertical}
  button{background:#0d6efd;color:#fff;border:0;padding:12px 20px;border-radius:6px;font-size:15px;cursor:pointer;margin-top:16px;width:100%}
  button:disabled{background:#9db8e8;cursor:not-allowed}
  .error{color:#c0392b;margin-top:10px;font-size:14px}
  .status{color:#1a7f37;background:#e8f8ee;border:1px solid #c3ecd2;padding:10px 14px;border-radius:6px;font-size:14px;margin-top:16px}
  .result{display:none;margin-top:20px;text-align:center;border-top:1px solid #eee;padding-top:20px}
  .result input{text-align:center;font-weight:bold;margin-bottom:12px}
  .result img{border:1px solid #eee;border-radius:8px;margin:8px 0}
  .copybtn{background:#28a745}
  .muted{color:#888;font-size:13px;text-align:center;margin-top:24px}
  .hint{color:#888;font-size:12px;margin-top:6px}
  .auth-switch{text-align:center;font-size:13px;color:#555;margin-top:16px}
  table{width:100%;border-collapse:collapse;margin-top:16px;font-size:13px}
  th,td{text-align:left;padding:8px;border-bottom:1px solid #eee;vertical-align:middle}
  td.url-col{max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
  .del-btn{background:#c0392b;width:auto;padding:6px 12px;margin:0;font-size:12px}
  .pagination{display:flex;gap:8px;justify-content:center;margin-top:16px;font-size:13px}
  .pagination a,.pagination span{padding:6px 10px;border:1px solid #ddd;border-radius:4px;color:#0d6efd;text-decoration:none}
  .bulk-results{display:none;margin-top:20px;border-top:1px solid #eee;padding-top:16px}
  .bulk-row{display:flex;justify-content:space-between;align-items:center;gap:8px;padding:8px 0;border-bottom:1px solid #f1f1f1;font-size:13px}
  .bulk-row:last-child{border-bottom:0}
  .bulk-row .orig{color:#888;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:45%}
  .bulk-row .short-link{font-weight:bold}
  .bulk-row .bulk-error{color:#c0392b}
  @yield('extra-style')
</style>
</head>
<body>
<div class="nav">
  <a class="brand" href="{{ route('home') }}">🔗 klikwit</a>
  <div>
    @auth
      <a href="{{ route('dashboard') }}">My Links</a>
      <a href="{{ route('bio.edit') }}">Link-in-Bio</a>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="linklike" type="submit">Log Out</button>
      </form>
    @else
      <a href="{{ route('login') }}">Log In</a>
      <a href="{{ route('register') }}">Sign Up</a>
    @endauth
  </div>
</div>
<div class="page @yield('page-class')">
@yield('content')
</div>
</body>
</html>
