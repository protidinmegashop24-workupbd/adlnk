@extends('layouts.app')

@section('title', 'Cookie Policy — klikwit')
@section('description', 'What cookies klikwit uses and why.')
@section('page-class', 'wide')
@section('extra-style')
  .legal{background:#fff;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,.1);padding:28px;margin-top:16px;line-height:1.7;max-width:700px;margin-left:auto;margin-right:auto}
  .legal h2{font-size:1.1rem;margin-top:24px}
  .legal p,.legal li{font-size:14px;color:#444}
  .legal table{width:100%;border-collapse:collapse;margin-top:12px;font-size:13px}
  .legal th,.legal td{text-align:left;padding:8px;border-bottom:1px solid #eee}
@endsection

@section('content')
  <h1>Cookie Policy</h1>
  <div class="legal">
    <p>klikwit uses a small number of essential cookies to make the site work. We do not currently use advertising or third-party tracking cookies.</p>

    <table>
      <thead><tr><th>Cookie</th><th>Purpose</th><th>Duration</th></tr></thead>
      <tbody>
        <tr><td>klikwit-session</td><td>Keeps you signed in and remembers things like your unlocked password-protected links.</td><td>Session / a few hours</td></tr>
        <tr><td>XSRF-TOKEN</td><td>Security token that protects forms from cross-site request forgery.</td><td>Session / a few hours</td></tr>
      </tbody>
    </table>

    <h2>Controlling cookies</h2>
    <p>Most browsers let you block or delete cookies through their settings. Since klikwit's cookies are essential for sign-in and security, blocking them will prevent you from logging in, but the public shortening tools will still work without an account.</p>

    <h2>Future changes</h2>
    <p>If we add advertising or analytics that use additional cookies, we'll update this page to reflect that before it happens.</p>
  </div>
@endsection
