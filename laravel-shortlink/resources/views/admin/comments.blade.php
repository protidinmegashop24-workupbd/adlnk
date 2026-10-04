@extends('layouts.dashboard')

@section('title', 'Comments — klikwit Admin')
@section('extra-style')
  .badge{display:inline-block;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:bold}
  .badge.pending{background:#fff3cd;color:#856404}
  .badge.approved{background:#d4edda;color:#155724}
  .comment-actions form{display:inline-block;margin-right:6px}
  .comment-actions button{width:auto;padding:6px 12px;margin:0;font-size:12px}
@endsection

@section('content')
  <h1>Comments</h1>

  @if (session('status'))
    <div class="status">{{ session('status') }}</div>
  @endif

  <div class="card">
    @if ($comments->isEmpty())
      <p class="muted" style="margin-top:0">No comments yet.</p>
    @else
      <table>
        <thead>
          <tr>
            <th>Status</th>
            <th>Post</th>
            <th>Name</th>
            <th>Comment</th>
            <th>Posted</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach ($comments as $comment)
            <tr>
              <td><span class="badge {{ $comment->approved ? 'approved' : 'pending' }}">{{ $comment->approved ? 'Approved' : 'Pending' }}</span></td>
              <td>
                @if ($comment->post)
                  <a href="{{ route('blog.show', $comment->post->slug) }}" target="_blank">{{ \Illuminate\Support\Str::limit($comment->post->title, 30) }}</a>
                @else
                  <span class="muted">(post deleted)</span>
                @endif
              </td>
              <td>{{ $comment->name }}{{ $comment->email ? ' ('.$comment->email.')' : '' }}</td>
              <td class="url-col" title="{{ $comment->body }}">{{ \Illuminate\Support\Str::limit($comment->body, 60) }}</td>
              <td>{{ $comment->created_at->format('M j, Y') }}</td>
              <td class="comment-actions">
                @unless ($comment->approved)
                  <form method="POST" action="{{ route('admin.comments.approve', $comment) }}">
                    @csrf
                    <button type="submit">Approve</button>
                  </form>
                @endunless
                <form method="POST" action="{{ route('admin.comments.destroy', $comment) }}" onsubmit="return confirm('Delete this comment?');">
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
        @if ($comments->previousPageUrl())
          <a href="{{ $comments->previousPageUrl() }}">&laquo; Previous</a>
        @endif
        <span>Page {{ $comments->currentPage() }} of {{ $comments->lastPage() }}</span>
        @if ($comments->nextPageUrl())
          <a href="{{ $comments->nextPageUrl() }}">Next &raquo;</a>
        @endif
      </div>
    @endif
  </div>
@endsection
