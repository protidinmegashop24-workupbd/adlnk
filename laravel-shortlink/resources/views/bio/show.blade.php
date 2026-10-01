<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="description" content="{{ $bioPage->title }} — links"/>
<title>{{ $bioPage->title }}</title>
<link rel="canonical" href="{{ url()->current() }}"/>
<style>
  body{font-family:Arial,Helvetica,sans-serif;background:#f4f6f8;margin:0;padding:40px 16px;color:#222}
  .wrap{max-width:420px;margin:0 auto;text-align:center}
  h1{font-size:1.5rem;margin-bottom:28px}
  a.link-btn{display:block;background:#fff;border:1px solid #e0e0e0;border-radius:10px;padding:16px;margin-bottom:14px;color:#222;text-decoration:none;font-size:15px;font-weight:bold;box-shadow:0 1px 3px rgba(0,0,0,.06);transition:transform .1s}
  a.link-btn:hover{border-color:#0d6efd;transform:translateY(-1px)}
  .muted{color:#888;font-size:13px;margin-top:32px}
  .muted a{color:#0d6efd;text-decoration:none}
</style>
</head>
<body>
<div class="wrap">
  <h1>{{ $bioPage->title }}</h1>
  @foreach ($bioPage->links as $link)
    <a class="link-btn" href="{{ $link['url'] }}" target="_blank" rel="noopener">{{ $link['label'] }}</a>
  @endforeach
  <p class="muted">Powered by <a href="{{ url('/') }}">klikwit</a></p>
</div>
</body>
</html>
