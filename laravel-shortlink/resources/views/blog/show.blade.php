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
  .comments-section{margin-top:24px}
  .comments-section h2{margin-top:0;font-size:1.1rem}
  .comment-item{border-top:1px solid #eee;padding:14px 0}
  .comment-item .c-name{font-weight:bold;font-size:14px}
  .comment-item .c-date{color:#888;font-size:12px;margin-bottom:6px}
  .comment-item .c-body{font-size:14px;color:#333;white-space:pre-line}
  .comment-honeypot{position:absolute;left:-9999px;top:auto}
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

      <div class="card comments-section" id="comments">
        <h2>Comments ({{ $comments->count() }})</h2>

        @forelse ($comments as $comment)
          <div class="comment-item">
            <div class="c-name">{{ $comment->name }}</div>
            <div class="c-date">{{ $comment->created_at->format('F j, Y') }}</div>
            <div class="c-body">{{ $comment->body }}</div>
          </div>
        @empty
          <p class="muted" style="margin-top:0">No comments yet. Be the first to share your thoughts!</p>
        @endforelse

        <h3 style="font-size:15px;margin-top:20px">Leave a Comment</h3>

        @if ($errors->any())
          <div class="error">{{ $errors->first() }}</div>
        @endif
        @if (session('status'))
          <div class="status">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('blog.comments.store', $post->slug) }}">
          @csrf
          <div class="comment-honeypot" aria-hidden="true">
            <label for="website">Leave this field blank</label>
            <input type="text" id="website" name="website" tabindex="-1" autocomplete="off"/>
          </div>
          <label for="comment_name">Name</label>
          <input id="comment_name" type="text" name="name" value="{{ old('name') }}" required maxlength="100"/>
          <label for="comment_email">Email <span class="hint" style="display:inline">(optional, never shown publicly)</span></label>
          <input id="comment_email" type="email" name="email" value="{{ old('email') }}" maxlength="255"/>
          <label for="comment_body">Comment</label>
          <textarea id="comment_body" name="body" rows="4" required maxlength="2000">{{ old('body') }}</textarea>
          <button type="submit">Post Comment</button>
        </form>
      </div>
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
