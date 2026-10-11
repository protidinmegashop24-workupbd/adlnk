@extends('layouts.dashboard')

@section('title', 'Site Pages — klikwit Admin')
@section('extra-style')
  .page-actions a{width:auto;padding:6px 12px;margin:0;font-size:12px}
@endsection

@section('content')
  <h1>Site Pages</h1>
  <p class="muted" style="margin-top:0">About, Contact, Privacy, Terms, and other fixed site pages. Edit their content here — no code deploy needed.</p>

  @if (session('status'))
    <div class="status">{{ session('status') }}</div>
  @endif

  <div class="card">
    <table>
      <thead>
        <tr>
          <th>Page</th>
          <th>Last Updated</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach ($pages as $page)
          <tr>
            <td>{{ $page->title }}</td>
            <td>{{ $page->updated_at?->format('M j, Y') ?? '—' }}</td>
            <td class="page-actions">
              <a href="{{ route('admin.pages.edit', $page) }}">Edit</a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
@endsection
