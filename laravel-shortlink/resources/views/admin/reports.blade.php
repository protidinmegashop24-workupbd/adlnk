@extends('layouts.dashboard')

@section('title', 'Abuse Reports — klikwit Admin')
@section('extra-style')
  .badge{display:inline-block;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:bold}
  .badge.pending{background:#fff3cd;color:#856404}
  .badge.actioned{background:#f8d7da;color:#721c24}
  .badge.dismissed{background:#e2e3e5;color:#41464b}
  .report-actions form{display:inline-block;margin-right:6px}
  .report-actions button{width:auto;padding:6px 12px;margin:0;font-size:12px}
  .dismiss-btn{background:#6c757d}
@endsection

@section('content')
  <h1>Abuse Reports</h1>

  @if (session('status'))
    <div class="status">{{ session('status') }}</div>
  @endif

  <div class="card">
    @if ($reports->isEmpty())
      <p class="muted" style="margin-top:0">No reports yet.</p>
    @else
      <table>
        <thead>
          <tr>
            <th>Status</th>
            <th>Code</th>
            <th>Destination</th>
            <th>Reason</th>
            <th>Details</th>
            <th>Reported</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach ($reports as $report)
            <tr>
              <td><span class="badge {{ $report->status }}">{{ ucfirst($report->status) }}</span></td>
              <td>{{ $report->code }}</td>
              <td class="url-col" title="{{ $report->link->url ?? '' }}">{{ $report->link->url ?? '(link no longer exists)' }}</td>
              <td>{{ ucfirst($report->reason) }}</td>
              <td class="url-col" title="{{ $report->details }}">{{ $report->details ?? '—' }}</td>
              <td>{{ $report->created_at->format('M j, Y') }}</td>
              <td class="report-actions">
                @if ($report->status === 'pending')
                  @if ($report->link && ! $report->link->disabled)
                    <form method="POST" action="{{ route('admin.reports.disable', $report) }}" onsubmit="return confirm('Disable this link?');">
                      @csrf
                      <button type="submit" class="del-btn">Disable Link</button>
                    </form>
                  @endif
                  <form method="POST" action="{{ route('admin.reports.dismiss', $report) }}">
                    @csrf
                    <button type="submit" class="dismiss-btn">Dismiss</button>
                  </form>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <div class="pagination">
        @if ($reports->previousPageUrl())
          <a href="{{ $reports->previousPageUrl() }}">&laquo; Previous</a>
        @endif
        <span>Page {{ $reports->currentPage() }} of {{ $reports->lastPage() }}</span>
        @if ($reports->nextPageUrl())
          <a href="{{ $reports->nextPageUrl() }}">Next &raquo;</a>
        @endif
      </div>
    @endif
  </div>
@endsection
