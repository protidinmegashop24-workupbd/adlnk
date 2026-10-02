@extends('layouts.app')

@section('title', 'Privacy Policy — klikwit')
@section('description', 'How klikwit collects, uses, and protects your information.')
@section('page-class', 'wide')
@section('extra-style')
  .legal{background:#fff;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,.1);padding:28px;margin-top:16px;line-height:1.7;max-width:700px;margin-left:auto;margin-right:auto}
  .legal h2{font-size:1.1rem;margin-top:24px}
  .legal p,.legal li{font-size:14px;color:#444}
  .legal .updated{color:#888;font-size:13px}
@endsection

@section('content')
  <h1>Privacy Policy</h1>
  <div class="legal">
    <p class="updated">Last updated: October 2026</p>

    <p>This page explains what information klikwit collects, how we use it, and the choices you have. We try to collect as little as we reasonably can.</p>

    <h2>Information we collect</h2>
    <ul>
      <li><strong>Account information</strong> — if you create a free account, we store your name, email address, and a securely hashed password (we never store your password in plain text).</li>
      <li><strong>Links you create</strong> — the destination URL, the short code, and (for signed-in users) the link to your account.</li>
      <li><strong>Click data</strong> — when someone clicks a short link, we record the date/time, the referring page (if any), and a general device type (desktop, mobile, or tablet) derived from the browser's user agent. We do not store the visitor's IP address.</li>
      <li><strong>Link-in-Bio pages</strong> — the page title, your chosen page name, and the links you add to it.</li>
    </ul>

    <h2>Cookies</h2>
    <p>We use essential cookies only: a session cookie to keep you signed in, and a security cookie (CSRF token) to protect forms from cross-site attacks. We do not currently use third-party advertising or tracking cookies. If that changes (for example, if we add display advertising), we will update this policy and our <a href="{{ route('pages.cookies') }}">Cookie Policy</a> first.</p>

    <h2>How we use your information</h2>
    <p>We use it to operate the service: creating and redirecting short links, showing you your own link analytics, and keeping your account secure. We do not sell your personal information to third parties.</p>

    <h2>Data retention and deletion</h2>
    <p>We keep your account and link data for as long as your account is active. If you'd like your account or data deleted, <a href="{{ route('pages.contact') }}">contact us</a> and we'll process the request.</p>

    <h2>Children's privacy</h2>
    <p>klikwit is not directed at children under 13, and we do not knowingly collect personal information from them.</p>

    <h2>Changes to this policy</h2>
    <p>If we make material changes to this policy, we'll update the date at the top of this page.</p>

    <h2>Contact</h2>
    <p>Questions about this policy? <a href="{{ route('pages.contact') }}">Contact us</a>.</p>
  </div>
@endsection
