@extends('layouts.app')

@section('title', 'About klikwit')
@section('description', 'klikwit is a free link-management toolkit: a URL shortener, QR code generator, bulk shortening, link-in-bio pages, and link analytics.')
@section('page-class', 'wide')
@section('extra-style')
  .legal{background:#fff;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,.1);padding:28px;margin-top:16px;line-height:1.7;max-width:700px;margin-left:auto;margin-right:auto}
  .legal h2{font-size:1.1rem;margin-top:24px}
  .legal p,.legal li{font-size:14px;color:#444}
@endsection

@section('content')
  <h1>About klikwit</h1>
  <div class="legal">
    <p>klikwit is a free link-management toolkit. It started as a simple URL shortener and has grown to include bulk link shortening, QR code generation, custom short links, password-protected and expiring links, link-in-bio pages, and click analytics — all built around one goal: making it easy to shorten, share, and understand your links.</p>

    <h2>What we offer</h2>
    <ul>
      <li><strong>URL Shortener &amp; Bulk Shortener</strong> — turn long links into short, shareable ones, one at a time or up to 20 at once.</li>
      <li><strong>QR Code Generator</strong> — every short link gets a downloadable QR code automatically.</li>
      <li><strong>Custom Links</strong> — pick your own short name instead of a random code.</li>
      <li><strong>Link-in-Bio</strong> — a free page to share multiple links from one place, for social media bios.</li>
      <li><strong>Link Analytics</strong> — click counts, device type, referrers, and timestamps for links you create while signed in.</li>
      <li><strong>UTM Builder, URL Expander, and Link Checker</strong> — small utilities to help you work with links.</li>
    </ul>

    <h2>Who runs klikwit</h2>
    <p>klikwit is operated as an independent project. We don't have a large team or a long history to point to — just a tool we've built carefully and keep improving. If you have questions, see our <a href="{{ route('pages.contact') }}">Contact page</a>.</p>

    <h2>Our commitment</h2>
    <p>We aim to keep the core tools free, avoid deceptive design (no fake "download" buttons, no misleading ads), and respond to abuse reports so klikwit isn't used to harm people. See our <a href="{{ route('pages.aup') }}">Acceptable Use Policy</a> and <a href="{{ route('report-abuse') }}">Report Abuse</a> page for more.</p>
  </div>
@endsection
