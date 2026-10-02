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

  <h2 style="margin-top:40px;font-size:1.2rem">SEO Tools</h2>
  <p class="muted" style="margin-top:0">Check your pages, preview search snippets, and generate technical SEO files.</p>
  <div class="tool-grid">
    <a class="tool-card" href="{{ route('seo-tools.meta-tag-checker') }}">
      <div class="icon">🔍</div>
      <h2>Meta Tag Checker</h2>
      <p>Check title, description, canonical and more.</p>
    </a>
    <a class="tool-card" href="{{ route('seo-tools.serp-preview') }}">
      <div class="icon">🔎</div>
      <h2>SERP Preview</h2>
      <p>Preview how a search result snippet might look.</p>
    </a>
    <a class="tool-card" href="{{ route('seo-tools.keyword-density-checker') }}">
      <div class="icon">📊</div>
      <h2>Keyword Density Checker</h2>
      <p>See how often words and phrases repeat.</p>
    </a>
    <a class="tool-card" href="{{ route('seo-tools.word-counter') }}">
      <div class="icon">🔢</div>
      <h2>Word Counter</h2>
      <p>Live word, character and reading-time counts.</p>
    </a>
    <a class="tool-card" href="{{ route('seo-tools.seo-url-checker') }}">
      <div class="icon">🔗</div>
      <h2>SEO URL Checker</h2>
      <p>Check a URL's structure for common issues.</p>
    </a>
    <a class="tool-card" href="{{ route('seo-tools.slug-generator') }}">
      <div class="icon">✂️</div>
      <h2>Slug Generator</h2>
      <p>Turn a title into a clean URL slug.</p>
    </a>
    <a class="tool-card" href="{{ route('seo-tools.robots-txt-generator') }}">
      <div class="icon">🤖</div>
      <h2>Robots.txt Generator</h2>
      <p>Generate a valid robots.txt file.</p>
    </a>
    <a class="tool-card" href="{{ route('seo-tools.xml-sitemap-generator') }}">
      <div class="icon">🗺️</div>
      <h2>XML Sitemap Generator</h2>
      <p>Turn a list of URLs into a valid sitemap.</p>
    </a>
    <a class="tool-card" href="{{ route('seo-tools.canonical-checker') }}">
      <div class="icon">🧭</div>
      <h2>Canonical Checker</h2>
      <p>Check a page's canonical tag.</p>
    </a>
    <a class="tool-card" href="{{ route('seo-tools.open-graph-checker') }}">
      <div class="icon">🖼️</div>
      <h2>Open Graph Checker</h2>
      <p>Check social share tags and preview a share.</p>
    </a>
  </div>
  <p class="muted"><a href="{{ route('seo-tools.index') }}">See the full SEO Tools page</a></p>
@endsection
