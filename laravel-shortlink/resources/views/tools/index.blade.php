@extends('layouts.app')

@section('title', 'All Tools — klikwit')
@section('description', 'Every free tool on klikwit in one place: URL shortener, bulk shortener, QR codes, link-in-bio, UTM builder, URL expander, and link checker.')
@section('page-class', 'wide')
@section('extra-style')
  .tool-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:14px;margin-top:20px}
  .tool-card{background:#fff;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,.1);padding:20px;text-decoration:none;color:#222;display:block}
  .tool-card:hover{box-shadow:0 2px 8px rgba(0,0,0,.15)}
  .tool-card .icon{font-size:1.6rem}
  .tool-card h2{font-size:1rem;margin:8px 0 4px}
  .tool-card p{font-size:13px;color:#666;margin:0}
@endsection

@section('content')
  <h1>All Tools</h1>
  <p class="muted" style="margin-top:0">Everything you need to manage links — free, no signup required to get started.</p>

  <div class="tool-grid">
    <a class="tool-card" href="{{ route('home') }}">
      <div class="icon">🔗</div>
      <h2>URL Shortener</h2>
      <p>Create short, clean links in seconds.</p>
    </a>
    <a class="tool-card" href="{{ route('home') }}#bulk-section">
      <div class="icon">📦</div>
      <h2>Bulk URL Shortener</h2>
      <p>Shorten up to 20 links at once.</p>
    </a>
    <a class="tool-card" href="{{ route('home') }}">
      <div class="icon">▣</div>
      <h2>QR Generator</h2>
      <p>Turn any link into a downloadable QR code.</p>
    </a>
    <a class="tool-card" href="{{ route('dashboard') }}">
      <div class="icon">📊</div>
      <h2>Link Analytics</h2>
      <p>Clicks, devices, referrers, and timestamps.</p>
    </a>
    <a class="tool-card" href="{{ route('home') }}">
      <div class="icon">🎨</div>
      <h2>Custom Links</h2>
      <p>Pick your own short name instead of a random code.</p>
    </a>
    <a class="tool-card" href="{{ route('bio.edit') }}">
      <div class="icon">👤</div>
      <h2>Link-in-Bio</h2>
      <p>Share multiple links from one simple page.</p>
    </a>
    <a class="tool-card" href="{{ route('tools.utm') }}">
      <div class="icon">🏷️</div>
      <h2>UTM Builder</h2>
      <p>Build campaign tracking URLs for analytics.</p>
    </a>
    <a class="tool-card" href="{{ route('tools.expand') }}">
      <div class="icon">🔎</div>
      <h2>URL Expander</h2>
      <p>See the full redirect chain of any short link.</p>
    </a>
    <a class="tool-card" href="{{ route('tools.check') }}">
      <div class="icon">✅</div>
      <h2>Link Checker</h2>
      <p>Check whether a link is live or broken.</p>
    </a>
  </div>
@endsection
