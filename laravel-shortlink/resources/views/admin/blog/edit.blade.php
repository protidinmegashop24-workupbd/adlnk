@extends('layouts.dashboard')

@section('title', 'Edit Blog Post — klikwit Admin')

@section('content')
  <h1>Edit Blog Post</h1>
  <div class="card">
    <form method="POST" action="{{ route('admin.blog.update', $post) }}" enctype="multipart/form-data">
      @include('admin.blog._form')
    </form>
  </div>

  <p class="muted" style="margin-top:16px">
    <a href="{{ route('blog.show', $post->slug) }}" target="_blank" rel="noopener">View live post &rarr;</a>
  </p>
@endsection
