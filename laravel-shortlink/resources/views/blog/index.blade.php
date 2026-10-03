@extends('layouts.app')

@section('title', 'Blog & Tutorials — klikwit')
@section('description', 'Guides on URL shortening, QR codes, and link-in-bio pages — written to help you get more out of klikwit.')
@section('page-class', 'blog-wide')

@section('content')
  <h1>Blog &amp; Tutorials</h1>
  <p class="muted" style="margin-top:0;text-align:left">Guides to help you get the most out of klikwit's shortener, QR codes, and link-in-bio pages.</p>

  <div class="blog-layout">
    <div>
      <form method="GET" action="{{ route('blog.index') }}" class="blog-search">
        <input type="text" name="q" value="{{ $search }}" placeholder="Search articles..."/>
        @if ($activeCategory !== '')
          <input type="hidden" name="category" value="{{ $activeCategory }}"/>
        @endif
        <button type="submit">Search</button>
      </form>

      @if ($search !== '' || $activeCategory !== '')
        <div class="blog-filter-note">
          @if ($activeCategory !== '')
            Showing <strong>{{ $activeCategory }}</strong> posts
          @endif
          @if ($search !== '')
            matching "<strong>{{ $search }}</strong>"
          @endif
          — <a href="{{ route('blog.index') }}">clear filters</a>
        </div>
      @endif

      @forelse ($posts as $post)
        <div class="post-card">
          <img src="{{ asset($post->thumbnail ?? 'images/blog/default.png') }}" alt=""/>
          <div class="post-card-body">
            <div class="post-card-meta">
              <span class="post-date">{{ $post->published_at?->format('F j, Y') }}</span>
              <a class="post-cat-badge" href="{{ route('blog.index', ['category' => $post->category]) }}">{{ $post->category }}</a>
            </div>
            <h2><a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a></h2>
            <p class="post-excerpt">{{ $post->excerpt }}</p>
          </div>
        </div>
      @empty
        <p class="muted" style="text-align:left">No posts match your search. <a href="{{ route('blog.index') }}">Clear filters</a>.</p>
      @endforelse
    </div>

    @include('partials.blog-sidebar')
  </div>
@endsection
