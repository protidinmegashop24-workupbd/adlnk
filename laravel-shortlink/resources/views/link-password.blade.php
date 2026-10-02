<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="robots" content="noindex,nofollow"/>
<title>Password Required</title>
<style>
  body{font-family:Arial,Helvetica,sans-serif;background:#f4f6f8;margin:0;padding:24px;color:#222}
  .wrap{max-width:380px;margin:60px auto;background:#fff;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,.1);padding:24px;text-align:center}
  input[type=password]{width:100%;box-sizing:border-box;padding:10px;border:1px solid #ccc;border-radius:6px;font-size:15px;margin-top:12px}
  button{background:#0d6efd;color:#fff;border:0;padding:12px 20px;border-radius:6px;font-size:15px;cursor:pointer;margin-top:14px;width:100%}
  .error{color:#c0392b;margin-top:10px;font-size:14px}
</style>
</head>
<body>
  <div class="wrap">
    <h3>🔒 Password Required</h3>
    <p>This link is password-protected. Enter the password to continue.</p>
    @if (! empty($error))
      <div class="error">{{ $error }}</div>
    @endif
    <form method="POST" action="{{ url('/'.$code.'/unlock') }}">
      @csrf
      <input type="password" name="password" placeholder="Password" required autofocus/>
      <button type="submit">Continue</button>
    </form>
  </div>
</body>
</html>
