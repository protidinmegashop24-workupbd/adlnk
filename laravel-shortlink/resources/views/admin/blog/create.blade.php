@extends('layouts.dashboard')

@section('title', 'New Blog Post — klikwit Admin')

@section('content')
  <h1>New Blog Post</h1>
  <div class="card">
    <form method="POST" action="{{ route('admin.blog.store') }}" enctype="multipart/form-data">
      @include('admin.blog._form')
    </form>
  </div>
@endsection
