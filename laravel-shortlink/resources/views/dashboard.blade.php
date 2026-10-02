@extends('layouts.dashboard')

@section('title', 'My Links — klikwit')
@section('extra-style')
  .stat-row{display:flex;gap:12px;margin-top:16px;flex-wrap:wrap}
  .stat-box{background:#fff;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,.1);padding:16px 20px;flex:1;min-width:120px;text-align:center}
  .stat-box .num{font-size:1.6rem;font-weight:bold;color:#0d6efd}
  .stat-box .label{font-size:13px;color:#888;margin-top:4px;text-transform:uppercase}
  .quick-actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:16px}
  .quick-actions a{flex:1;min-width:140px;background:#fff;border:1px solid #e0e0e0;border-radius:8px;padding:14px;text-align:center;text-decoration:none;color:#222;font-size:14px;font-weight:bold}
  .quick-actions a:hover{border-color:#0d6efd;color:#0d6efd}
  .filter-bar{display:flex;gap:8px;flex-wrap:wrap;align-items:flex-end;margin-bottom:16px}
  .filter-bar .field{flex:1;min-width:160px}
  .filter-bar label{margin:0 0 4px}
  .filter-bar input,.filter-bar select{margin:0}
  .filter-bar button{width:auto;margin:0;padding:10px 20px;flex-shrink:0}
  .filter-bar .clear-link{font-size:13px;align-self:center;white-space:nowrap}
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
    @if ($totalLinks === 0)
      <p class="muted" style="margin-top:0">You haven't shortened any links while logged in yet. <a href="{{ route('home') }}">Create one</a>.</p>
    @else
      <form method="GET" action="{{ route('dashboard') }}" class="filter-bar">
        <div class="field">
          <label for="q">Search</label>
          <input id="q" type="text" name="q" value="{{ $search }}" placeholder="Search by short code or URL"/>
        </div>
        <div class="field" style="flex:0 0 180px">
          <label for="sort">Sort by</label>
          <select id="sort" name="sort">
            <option value="newest" @selected($sort === 'newest')>Newest first</option>
            <option value="oldest" @selected($sort === 'oldest')>Oldest first</option>
            <option value="most_clicks" @selected($sort === 'most_clicks')>Most clicks</option>
            <option value="least_clicks" @selected($sort === 'least_clicks')>Least clicks</option>
          </select>
        </div>
        <button type="submit">Apply</button>
        @if ($search !== '' || $sort !== 'newest')
          <a class="clear-link" href="{{ route('dashboard') }}">Clear</a>
        @endif
      </form>

      @if ($links->isEmpty())
        <p class="muted" style="margin-top:0">No links match your search. <a href="{{ route('dashboard') }}">Clear filters</a>.</p>
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
    @endif
  </div>
@endsection
