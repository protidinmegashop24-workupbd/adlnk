@extends('layouts.app')

@section('title', $post['title'].' — klikwit')
@section('description', $post['excerpt'])
@section('page-class', 'wide')
@section('extra-style')
  .post-date{color:#888;font-size:13px;margin-bottom:4px}
  .post-body{background:#fff;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,.1);padding:24px;margin-top:16px;line-height:1.7}
  .post-body h2{font-size:1.2rem;margin-top:28px}
  .post-body p{margin:12px 0}
  .post-body ul,.post-body ol{margin:12px 0;padding-left:24px}
  .post-body a{color:#0d6efd}
  .back-link{display:inline-block;margin-top:20px;font-size:14px}
@endsection

@section('content')
  <div class="post-date">{{ \Carbon\Carbon::parse($post['date'])->format('F j, Y') }}</div>
  <h1>{{ $post['title'] }}</h1>
  <div class="post-body">
    @include('blog.posts.'.$post['slug'])
  </div>
  <a class="back-link" href="{{ route('blog.index') }}">&laquo; Back to Blog</a>

  <script type="application/ld+json">
  {
    "@@context": "https://schema.org",
    "@@type": "Article",
    "headline": @json($post['title']),
    "datePublished": @json($post['date']),
    "description": @json($post['excerpt'])
  }
  </script>
@endsection
