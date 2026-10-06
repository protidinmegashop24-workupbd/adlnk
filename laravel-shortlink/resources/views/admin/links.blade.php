@extends('layouts.dashboard')

@section('title', 'Links — klikwit Admin')
@section('extra-style')
  .badge{display:inline-block;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:bold}
  .badge.active{background:#e8f8ee;color:#1a7f37}
  .badge.disabled{background:#fdecea;color:#c0392b}
  .link-actions form{display:inline-block;margin-right:6px}
  .link-actions button,.link-actions input{width:auto;padding:6px 8px;margin:0;font-size:12px}
  .link-search{display:flex;gap:8px;margin-bottom:16px}
  .link-search input{margin:0}
  .link-search button{width:auto;margin:0;flex-shrink:0;padding:10px 18px}
  .disable-form{display:flex;gap:4px;align-items:center}
  .disable-form input[type=text]{width:140px}
@endsection

@section('content')
  <h1>Links</h1>

  @if (session('status'))
    <div class="status">{{ session('status') }}</div>
  @endif

  <div class="card">
    <form method="GET" action="{{ route('admin.links.index') }}" class="link-search">
      <input type="text" name="q" value="{{ $search }}" placeholder="Search by code or destination URL…"/>
      <button type="submit">Search</button>
    </form>

    @if ($links->isEmpty())
      <p class="muted" style="margin-top:0">No links found.</p>
    @else
      <table>
        <thead>
          <tr>
            <th>Status</th>
            <th>Code</th>
            <th>Destination</th>
            <th>Owner</th>
            <th>Clicks</th>
            <th>Created</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach ($links as $link)
            <tr>
              <td><span class="badge {{ $link->disabled ? 'disabled' : 'active' }}">{{ $link->disabled ? 'Disabled' : 'Active' }}</span></td>
              <td>{{ $link->code }}</td>
              <td class="url-col" title="{{ $link->url }}">{{ $link->url }}</td>
              <td>{{ $link->user->email ?? 'anonymous' }}</td>
              <td>{{ $link->clicks }}</td>
              <td>{{ $link->created_at->format('M j, Y') }}</td>
              <td class="link-actions">
                @if ($link->disabled)
                  <form method="POST" action="{{ route('admin.links.enable', $link) }}">
                    @csrf
                    <button type="submit">Enable</button>
                  </form>
                  <span class="hint" title="{{ $link->disabled_reason }}">{{ \Illuminate\Support\Str::limit($link->disabled_reason, 24) }}</span>
                @else
                  <form method="POST" action="{{ route('admin.links.disable', $link) }}" class="disable-form" onsubmit="return confirm('Disable this link?');">
                    @csrf
                    <input type="text" name="reason" placeholder="Reason (optional)"/>
                    <button type="submit" class="del-btn">Disable</button>
                  </form>
                @endif
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
