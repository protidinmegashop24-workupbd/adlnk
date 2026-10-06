@extends('layouts.app')

@section('title', 'Free SEO Tools | Website SEO Tools | Klikwit')
@section('description', 'Free, browser-based SEO tools from klikwit: meta tag checker, SERP preview, keyword density, word counter, slug generator, robots.txt and sitemap generators, and more.')
@section('page-class', 'wide')
@section('extra-style')
  .seo-cat{margin-top:40px}
  .seo-cat:first-of-type{margin-top:24px}
  .seo-cat h2{font-size:1.15rem;margin-bottom:12px}
  .seo-tool-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:14px}
  .seo-tool-card{background:#fff;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,.1);padding:20px;display:flex;flex-direction:column}
  .seo-tool-card .icon{font-size:1.6rem}
  .seo-tool-card h3{font-size:1rem;margin:8px 0 4px}
  .seo-tool-card p{font-size:13px;color:#666;margin:0;flex:1}
  .seo-tool-card a.use-btn{display:inline-block;margin-top:14px;background:#0d6efd;color:#fff;text-decoration:none;text-align:center;padding:10px 14px;border-radius:6px;font-size:13px;font-weight:bold}
  .seo-tool-card a.use-btn:hover{background:#0b5ed7}
  .seo-disclaimer{max-width:700px;margin:28px auto 0;text-align:center}
@endsection

@section('hero')
  <h1>Free SEO Tools</h1>
  <p>Simple SEO tools to analyze, optimize and improve your website.</p>
@endsection

@section('content')
  <p class="muted" style="margin-top:0;max-width:700px;margin-left:auto;margin-right:auto">
    klikwit's SEO tools are practical, browser-based utilities for website owners, bloggers, creators and marketers — check your page tags, preview how a link looks in search results, measure basic text metrics, and generate files like robots.txt and sitemaps. No signup required.
  </p>
  <p class="seo-disclaimer notice-box">
    These tools report what's actually in your page's HTML and give practical suggestions. They don't guarantee search rankings, traffic, or ad approval — no tool honestly can.
  </p>

  <div class="seo-cat">
    <h2>SEO Analysis</h2>
    <div class="seo-tool-grid">
      <div class="seo-tool-card">
        <div class="icon">🔍</div>
        <h3>Meta Tag Checker</h3>
        <p>Check your page title, meta description, canonical URL and other important meta tags.</p>
        <a class="use-btn" href="{{ route('seo-tools.meta-tag-checker') }}">Check Meta Tags</a>
      </div>
      <div class="seo-tool-card">
        <div class="icon">🔗</div>
        <h3>SEO URL Checker</h3>
        <p>Check a URL's structure for HTTPS, length, readability and common issues.</p>
        <a class="use-btn" href="{{ route('seo-tools.seo-url-checker') }}">Check URL</a>
      </div>
      <div class="seo-tool-card">
        <div class="icon">🧭</div>
        <h3>Canonical Checker</h3>
        <p>Check whether a page's canonical tag exists, self-references, and matches its scheme and host.</p>
        <a class="use-btn" href="{{ route('seo-tools.canonical-checker') }}">Check Canonical</a>
      </div>
      <div class="seo-tool-card">
        <div class="icon">🖼️</div>
        <h3>Open Graph Checker</h3>
        <p>Check Open Graph and Twitter/X card tags, with a preview of how a share might look.</p>
        <a class="use-btn" href="{{ route('seo-tools.open-graph-checker') }}">Check Open Graph</a>
      </div>
    </div>
  </div>

  <div class="seo-cat">
    <h2>Keyword Research</h2>
    <div class="seo-tool-grid">
      <div class="seo-tool-card">
        <div class="icon">💡</div>
        <h3>Keyword Suggestions</h3>
        <p>Find related searches for any topic, pulled from real Google autocomplete data.</p>
        <a class="use-btn" href="{{ route('seo-tools.keyword-suggestions') }}">Find Keywords</a>
      </div>
    </div>
  </div>

  <div class="seo-cat">
    <h2>Content SEO</h2>
    <div class="seo-tool-grid">
      <div class="seo-tool-card">
        <div class="icon">📊</div>
        <h3>Keyword Density Checker</h3>
        <p>See how often words and phrases repeat in a block of text.</p>
        <a class="use-btn" href="{{ route('seo-tools.keyword-density-checker') }}">Check Density</a>
      </div>
      <div class="seo-tool-card">
        <div class="icon">🔢</div>
        <h3>Word Counter</h3>
        <p>Live word count, character count, sentences, paragraphs and reading time.</p>
        <a class="use-btn" href="{{ route('seo-tools.word-counter') }}">Count Words</a>
      </div>
    </div>
  </div>

  <div class="seo-cat">
    <h2>Search Preview</h2>
    <div class="seo-tool-grid">
      <div class="seo-tool-card">
        <div class="icon">🔎</div>
        <h3>SERP Preview</h3>
        <p>See a realistic preview of how your title and description might look in search results.</p>
        <a class="use-btn" href="{{ route('seo-tools.serp-preview') }}">Preview Snippet</a>
      </div>
    </div>
  </div>

  <div class="seo-cat">
    <h2>Technical SEO</h2>
    <div class="seo-tool-grid">
      <div class="seo-tool-card">
        <div class="icon">🤖</div>
        <h3>Robots.txt Generator</h3>
        <p>Generate a valid robots.txt file with allow/disallow rules and a sitemap link.</p>
        <a class="use-btn" href="{{ route('seo-tools.robots-txt-generator') }}">Generate Robots.txt</a>
      </div>
      <div class="seo-tool-card">
        <div class="icon">🗺️</div>
        <h3>XML Sitemap Generator</h3>
        <p>Turn a list of URLs into a valid XML sitemap, ready to copy or download.</p>
        <a class="use-btn" href="{{ route('seo-tools.xml-sitemap-generator') }}">Generate Sitemap</a>
      </div>
    </div>
  </div>

  <div class="seo-cat">
    <h2>Content Helpers</h2>
    <div class="seo-tool-grid">
      <div class="seo-tool-card">
        <div class="icon">✂️</div>
        <h3>Slug Generator</h3>
        <p>Turn a title into a clean, URL-friendly slug.</p>
        <a class="use-btn" href="{{ route('seo-tools.slug-generator') }}">Generate Slug</a>
      </div>
    </div>
  </div>

  <p class="muted">Looking for link tools instead? <a href="{{ route('tools.index') }}">See all klikwit tools</a>.</p>
@endsection
