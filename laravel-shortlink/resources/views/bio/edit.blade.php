@extends('layouts.app')

@section('title', 'Link-in-Bio — klikwit')
@section('page-class', 'wide')
@section('extra-style')
  .link-row{display:flex;gap:8px;align-items:flex-start;margin-top:10px}
  .link-row input{margin-top:0}
  .link-row .remove-link{background:#c0392b;width:auto;padding:10px 14px;margin:0;flex-shrink:0}
  .add-link{background:#6c757d;margin-top:12px}
  .slug-preview{font-size:13px;color:#555;margin-top:4px}
@endsection

@section('content')
  <h1>Link-in-Bio</h1>

  @if (session('status'))
    <div class="status">{{ session('status') }}</div>
  @endif

  @if ($bioPage)
    <p class="muted" style="margin-top:0">
      Your page is live at
      <a href="{{ route('bio.show', $bioPage->slug) }}" target="_blank" rel="noopener">{{ url('/u/'.$bioPage->slug) }}</a>
    </p>
  @endif

  <div class="card">
    @if ($errors->any())
      <div class="error">{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ route('bio.update') }}" id="bioForm">
      @csrf

      <label for="title">Page title</label>
      <input id="title" type="text" name="title" placeholder="e.g. John's Links" value="{{ old('title', $bioPage->title ?? '') }}" required/>

      <label for="slug">Your page name</label>
      <input id="slug" type="text" name="slug" placeholder="johnsmith" value="{{ old('slug', $bioPage->slug ?? '') }}" required/>
      <div class="slug-preview">Your page will be at: <span id="slugPreview">{{ url('/u/') }}/{{ old('slug', $bioPage->slug ?? 'your-name') }}</span></div>

      <label>Your links</label>
      <div id="linkRows">
        @php $links = old('links', $bioPage->links ?? [['label' => '', 'url' => '']]); @endphp
        @foreach ($links as $i => $link)
          <div class="link-row">
            <input type="text" name="links[{{ $i }}][label]" placeholder="Label (e.g. My Shop)" value="{{ $link['label'] ?? '' }}" required/>
            <input type="url" name="links[{{ $i }}][url]" placeholder="https://..." value="{{ $link['url'] ?? '' }}" required/>
            <button class="remove-link" type="button">Remove</button>
          </div>
        @endforeach
      </div>
      <button class="add-link" type="button" id="addLink">+ Add Link</button>

      <button type="submit">Save Page</button>
    </form>
  </div>

  <script>
  (function(){
    var rows = document.getElementById('linkRows');
    var addBtn = document.getElementById('addLink');
    var slugInput = document.getElementById('slug');
    var slugPreview = document.getElementById('slugPreview');
    var baseUrl = '{{ url('/u') }}/';
    var nextIndex = rows.querySelectorAll('.link-row').length;

    function makeRow(index){
      var row = document.createElement('div');
      row.className = 'link-row';
      row.innerHTML =
        '<input type="text" name="links[' + index + '][label]" placeholder="Label (e.g. My Shop)" required/>' +
        '<input type="url" name="links[' + index + '][url]" placeholder="https://..." required/>' +
        '<button class="remove-link" type="button">Remove</button>';
      return row;
    }

    addBtn.addEventListener('click', function(){
      rows.appendChild(makeRow(nextIndex));
      nextIndex++;
    });

    rows.addEventListener('click', function(e){
      if (e.target.classList.contains('remove-link')) {
        if (rows.querySelectorAll('.link-row').length > 1) {
          e.target.closest('.link-row').remove();
        }
      }
    });

    slugInput.addEventListener('input', function(){
      slugPreview.textContent = baseUrl + (slugInput.value || 'your-name');
    });
  })();
  </script>
@endsection
