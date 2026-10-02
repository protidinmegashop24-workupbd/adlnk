@extends('layouts.dashboard')

@section('title', 'My Links — klikwit')
@section('extra-style')
  .stat-row{display:flex;gap:12px;margin-top:16px;flex-wrap:wrap}
  .stat-box{background:#fff;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,.1);padding:16px 20px;flex:1;min-width:120px;text-align:center}
  .stat-box .num{font-size:1.6rem;font-weight:bold;color:#0d6efd}
  .stat-box .label{font-size:12px;color:#888;margin-top:4px;text-transform:uppercase}
  .quick-actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:16px}
  .quick-actions a{flex:1;min-width:140px;background:#fff;border:1px solid #e0e0e0;border-radius:8px;padding:14px;text-align:center;text-decoration:none;color:#222;font-size:13px;font-weight:bold}
  .quick-actions a:hover{border-color:#0d6efd;color:#0d6efd}
@endsection

@section('content')
  <h1>My Links</h1>

  @if (session('status'))
    <div class="status">{{ session('status') }}</div>
  @endif

  <div class="stat-row">
    <div class="stat-box">
      <div class="num">{{ $totalLinks }}</div>
      <div class="label">Total Links</div>
    </div>
    <div class="stat-box">
      <div class="num">{{ $totalClicks }}</div>
      <div class="label">Total Clicks</div>
    </div>
  </div>

  <div class="quick-actions">
    <a href="{{ route('home') }}">🔗 Create Short Link</a>
    <a href="{{ route('home') }}#bulk-section">📦 Bulk Shorten</a>
    <a href="{{ route('home') }}">▣ Create QR Code</a>
    <a href="{{ route('bio.edit') }}">👤 Link-in-Bio</a>
  </div>

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
