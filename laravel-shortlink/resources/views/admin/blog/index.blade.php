@extends('layouts.dashboard')

@section('title', 'Blog Posts — klikwit Admin')
@section('extra-style')
  .badge{display:inline-block;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:bold}
  .badge.published{background:#e8f8ee;color:#1a7f37}
  .badge.draft{background:#e2e3e5;color:#41464b}
  .post-actions form{display:inline-block;margin-right:6px}
  .post-actions a,.post-actions button{width:auto;padding:6px 12px;margin:0;font-size:12px}
  .new-post-btn{display:inline-block;background:#0d6efd;color:#fff;text-decoration:none;padding:12px 20px;border-radius:6px;font-size:14px;font-weight:bold}
  .new-post-btn:hover{background:#0b5ed7}
@endsection

@section('content')
  <h1>Blog Posts</h1>

  @if (session('status'))
    <div class="status">{{ session('status') }}</div>
  @endif

  <div style="margin-bottom:16px">
    <a class="new-post-btn" href="{{ route('admin.blog.create') }}">✍️ Write New Post</a>
  </div>

  <div class="card">
    @if ($posts->isEmpty())
      <p class="muted" style="margin-top:0">No posts yet. <a href="{{ route('admin.blog.create') }}">Write your first one</a>.</p>
    @else
      <table>
        <thead>
          <tr>
            <th>Status</th>
            <th>Title</th>
            <th>Category</th>
            <th>Published</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach ($posts as $post)
            <tr>
              <td><span class="badge {{ $post->published ? 'published' : 'draft' }}">{{ $post->published ? 'Published' : 'Draft' }}</span></td>
              <td>{{ $post->title }}</td>
              <td>{{ $post->category }}</td>
              <td>{{ $post->published_at?->format('M j, Y') ?? '—' }}</td>
              <td class="post-actions">
                <a href="{{ route('admin.blog.edit', $post) }}">Edit</a>
                <form method="POST" action="{{ route('admin.blog.destroy', $post) }}" onsubmit="return confirm('Delete this post? This cannot be undone.');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="del-btn">Delete</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <div class="pagination">
        @if ($posts->previousPageUrl())
          <a href="{{ $posts->previousPageUrl() }}">&laquo; Previous</a>
        @endif
        <span>Page {{ $posts->currentPage() }} of {{ $posts->lastPage() }}</span>
        @if ($posts->nextPageUrl())
          <a href="{{ $posts->nextPageUrl() }}">Next &raquo;</a>
        @endif
      </div>
    @endif
  </div>
@endsection
