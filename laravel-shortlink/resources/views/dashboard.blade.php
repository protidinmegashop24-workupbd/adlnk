@extends('layouts.dashboard')

@section('title', 'My Links — klikwit')

@section('content')
  <h1>My Links</h1>

  @if (session('status'))
    <div class="status">{{ session('status') }}</div>
  @endif

  <div class="card">
    @if ($links->isEmpty())
      <p class="muted" style="margin-top:0">You haven't shortened any links while logged in yet. <a href="{{ route('home') }}">Create one</a>.</p>
    @else
      <table>
        <thead>
          <tr>
            <th>Short Link</th>
            <th>Original URL</th>
            <th>Clicks</th>
            <th>Created</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach ($links as $link)
            <tr>
              <td><a href="{{ url('/'.$link->code) }}" target="_blank" rel="noopener">{{ url('/'.$link->code) }}</a></td>
              <td class="url-col" title="{{ $link->url }}">{{ $link->url }}</td>
              <td><a href="{{ route('dashboard.analytics', $link) }}">{{ $link->clicks }}</a></td>
              <td>{{ $link->created_at->format('M j, Y') }}</td>
              <td>
                <a href="{{ route('dashboard.analytics', $link) }}" style="font-size:12px;margin-right:10px">Details</a>
                <form method="POST" action="{{ route('dashboard.destroy', $link) }}" onsubmit="return confirm('Delete this link?');" style="display:inline">
                  @csrf
                  @method('DELETE')
                  <button class="del-btn" type="submit">Delete</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <div class="pagination">
        @if ($links->previousPageUrl())
          <a href="{{ $links->previousPageUrl() }}">&laquo; Previous</a>
        @endif
        <span>Page {{ $links->currentPage() }} of {{ $links->lastPage() }}</span>
        @if ($links->nextPageUrl())
          <a href="{{ $links->nextPageUrl() }}">Next &raquo;</a>
        @endif
      </div>
    @endif
  </div>
@endsection
