@extends('layouts.app')

@section('title', ($post->meta_title ?: $post->title).' — klikwit')
@section('description', $post->meta_description ?: $post->excerpt)
@section('og-type', 'article')
@section('og-image', asset($post->thumbnail ?? 'images/blog/default.png'))
@section('page-class', 'blog-wide')
@section('extra-style')
  .post-body{background:#fff;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,.1);padding:24px;margin-top:16px;line-height:1.7}
  .post-body h2{font-size:1.2rem;margin-top:28px}
  .post-body p{margin:12px 0}
  .post-body ul,.post-body ol{margin:12px 0;padding-left:24px}
  .post-body a{color:#0d6efd}
  .post-hero{width:100%;max-height:320px;object-fit:cover;border-radius:8px;margin-top:16px}
  .back-link{display:inline-block;margin-top:20px;font-size:14px}
@endsection

@section('content')
  <div class="blog-layout">
    <div>
      <div class="post-card-meta">
        <span class="post-date">{{ $post->published_at?->format('F j, Y') }}</span>
        <a class="post-cat-badge" href="{{ route('blog.index', ['category' => $post->category]) }}">{{ $post->category }}</a>
      </div>
      <h1>{{ $post->title }}</h1>
      <img class="post-hero" src="{{ asset($post->thumbnail ?? 'images/blog/default.png') }}" alt=""/>
      <div class="post-body">
        {!! $post->body !!}
      </div>
      <a class="back-link" href="{{ route('blog.index') }}">&laquo; Back to Blog</a>
    </div>

    @include('partials.blog-sidebar', ['activeCategory' => ''])
  </div>

  <script type="application/ld+json">
  {
    "@@context": "https://schema.org",
    "@@type": "Article",
    "headline": @json($post->title),
    "datePublished": @json($post->published_at?->toIso8601String()),
    "description": @json($post->excerpt)
  }
  </script>
@endsection
