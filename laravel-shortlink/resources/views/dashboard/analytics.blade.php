@extends('layouts.app')

@section('title', 'Link Analytics — klikwit')
@section('page-class', 'wide')
@section('extra-style')
  .stat-row{display:flex;gap:12px;margin-top:16px;flex-wrap:wrap}
  .stat-box{background:#fff;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,.1);padding:16px 20px;flex:1;min-width:120px;text-align:center}
  .stat-box .num{font-size:1.6rem;font-weight:bold;color:#0d6efd}
  .stat-box .label{font-size:12px;color:#888;margin-top:4px;text-transform:uppercase}
  .back-link{display:inline-block;margin-bottom:12px;font-size:14px}
  .no-data{color:#888;font-size:13px;padding:10px 0}
@endsection

@section('content')
  <a class="back-link" href="{{ route('dashboard') }}">&laquo; Back to My Links</a>
  <h1>Analytics</h1>
  <p class="muted" style="margin-top:0;word-break:break-all">
    <strong>{{ url('/'.$link->code) }}</strong> &rarr; {{ $link->url }}
  </p>

  <div class="stat-row">
    <div class="stat-box">
      <div class="num">{{ $link->clicks }}</div>
      <div class="label">Total Clicks</div>
    </div>
    @foreach (['desktop', 'mobile', 'tablet'] as $device)
      <div class="stat-box">
        <div class="num">{{ $deviceCounts[$device] ?? 0 }}</div>
        <div class="label">{{ ucfirst($device) }}</div>
      </div>
    @endforeach
  </div>

  <div class="card">
    <h2 style="margin-top:0;font-size:1.05rem">Top Referrers</h2>
    @if ($topReferrers->isEmpty())
      <p class="no-data">No referrer data yet — this fills in once people click your link from another site.</p>
    @else
      <table>
        <thead><tr><th>Referrer</th><th>Clicks</th></tr></thead>
        <tbody>
          @foreach ($topReferrers as $ref)
            <tr>
              <td class="url-col" title="{{ $ref->referrer }}">{{ $ref->referrer }}</td>
              <td>{{ $ref->count }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>

  <div class="card">
    <h2 style="margin-top:0;font-size:1.05rem">Recent Clicks</h2>
    @if ($recentClicks->isEmpty())
      <p class="no-data">No clicks recorded yet.</p>
    @else
      <table>
        <thead><tr><th>Date &amp; Time</th><th>Device</th><th>Referrer</th></tr></thead>
        <tbody>
          @foreach ($recentClicks as $click)
            <tr>
              <td>{{ $click->created_at->format('M j, Y g:i A') }}</td>
              <td>{{ ucfirst($click->device) }}</td>
              <td class="url-col" title="{{ $click->referrer }}">{{ $click->referrer ?? 'Direct / unknown' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>
@endsection
