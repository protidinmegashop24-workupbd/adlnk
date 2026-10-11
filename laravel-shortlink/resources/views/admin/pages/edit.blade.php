@extends('layouts.dashboard')

@section('title', 'Edit '.$page->title.' — klikwit Admin')

@section('content')
  <h1>Edit: {{ $page->title }}</h1>
  <div class="card">
    <form method="POST" action="{{ route('admin.pages.update', $page) }}">
      @csrf
      @method('PUT')

      @if ($errors->any())
        <div class="error">{{ $errors->first() }}</div>
      @endif

      <label for="title">Page Title <span class="hint" style="display:inline">(shown as the H1 heading)</span></label>
      <input id="title" type="text" name="title" value="{{ old('title', $page->title) }}" required/>

      <div style="border:1px solid #e5e7eb;border-radius:8px;padding:14px 16px;margin:18px 0;background:#fafbfc">
        <strong style="font-size:14px">Search engine (SEO) settings — optional</strong>
        <div class="hint" style="margin:4px 0 12px">Leave blank to use the Page Title above automatically.</div>

        <label for="meta_title">SEO Title</label>
        <input id="meta_title" type="text" name="meta_title" maxlength="255" value="{{ old('meta_title', $page->meta_title) }}" placeholder="{{ old('title', $page->title) }} — klikwit"/>

        <label for="meta_description" style="margin-top:10px">SEO Description</label>
        <textarea id="meta_description" name="meta_description" rows="2" maxlength="500">{{ old('meta_description', $page->meta_description) }}</textarea>
      </div>

      <label for="body">Page Content</label>
      <div style="display:flex;gap:18px;margin:4px 0 10px">
        <label style="display:flex;align-items:center;gap:6px;font-weight:normal;margin:0">
          <input type="radio" name="body_mode" value="text" class="body-mode-input" style="width:auto" {{ old('body_mode', $page->body_mode ?? 'html') === 'text' ? 'checked' : '' }}/>
          ✍️ সাধারণ লেখা (Plain Text)
        </label>
        <label style="display:flex;align-items:center;gap:6px;font-weight:normal;margin:0">
          <input type="radio" name="body_mode" value="html" class="body-mode-input" style="width:auto" {{ old('body_mode', $page->body_mode ?? 'html') === 'html' ? 'checked' : '' }}/>
          💻 HTML কোড (Advanced)
        </label>
      </div>
      <div class="hint" style="margin-bottom:6px;line-height:1.6">
        This page already uses HTML formatting (headings, lists, links), so it's currently in <strong>HTML কোড</strong> mode — you can edit the text directly (like swapping an email address) without worrying about breaking the formatting, as long as you don't remove any <code>&lt;...&gt;</code> tags by accident.
        <br/>Switch to <strong>সাধারণ লেখা (Plain Text)</strong> if you want to clear this box and write fresh in plain words instead — then anything you type or paste just shows as text (nothing can break), and the same shortcuts as Blog Posts apply: <code>## Heading</code>, <code>**bold**</code>, <code>[link text](https://example.com)</code>, <code>-</code> for bullets.
        <br/>Click <strong>Preview</strong> below to see how it'll look before saving.
      </div>
      <textarea id="body" name="body" rows="20" style="font-family:monospace;font-size:13px" required>{{ old('body', $page->body_raw ?? $page->body) }}</textarea>

      <button type="button" id="previewBtn" style="margin-top:10px;width:auto;background:#fff;color:#0d6efd;border:1px solid #0d6efd;padding:10px 18px">👁️ Preview</button>

      <button type="submit" style="margin-top:16px">Save Page</button>
    </form>
  </div>

  @include('admin.partials.content-preview-modal', ['previewUrl' => route('admin.pages.preview')])

  <p class="muted" style="margin-top:16px">
    @php
      $liveUrl = match ($page->slug) {
          'about' => '/about',
          'contact' => '/contact',
          'privacy' => '/privacy-policy',
          'terms' => '/terms',
          'cookies' => '/cookie-policy',
          'acceptable-use' => '/acceptable-use-policy',
          'dmca' => '/dmca',
          default => '/',
      };
    @endphp
    <a href="{{ $liveUrl }}" target="_blank" rel="noopener">View live page &rarr;</a>
  </p>
@endsection
