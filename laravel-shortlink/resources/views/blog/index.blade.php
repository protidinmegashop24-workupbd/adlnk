@extends('layouts.app')

@section('title', 'Blog & Tutorials — klikwit')
@section('description', 'Guides on URL shortening, QR codes, and link-in-bio pages — written to help you get more out of klikwit.')
@section('page-class', 'wide')
@section('extra-style')
  .post-card{background:#fff;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,.1);padding:20px;margin-top:16px}
  .post-card h2{font-size:1.15rem;margin:0 0 6px}
  .post-card h2 a{color:#222;text-decoration:none}
  .post-card h2 a:hover{color:#0d6efd}
  .post-date{color:#888;font-size:12px;margin-bottom:8px}
  .post-excerpt{color:#555;font-size:14px;margin:0}
@endsection

@section('content')
  <h1>Blog &amp; Tutorials</h1>
  <p class="muted" style="margin-top:0">Guides to help you get the most out of klikwit's shortener, QR codes, and link-in-bio pages.</p>

  @foreach ($posts as $post)
    <div class="post-card">
      <div class="post-date">{{ \Carbon\Carbon::parse($post['date'])->format('F j, Y') }}</div>
      <h2><a href="{{ route('blog.show', $post['slug']) }}">{{ $post['title'] }}</a></h2>
      <p class="post-excerpt">{{ $post['excerpt'] }}</p>
    </div>
  @endforeach
@endsection
