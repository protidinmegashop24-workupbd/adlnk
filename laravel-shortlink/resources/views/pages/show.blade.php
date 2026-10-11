@extends('layouts.app')

@section('title', $page->meta_title ?: $page->title.' — klikwit')
@section('description', $page->meta_description ?: '')
@section('page-class', 'wide')
@section('extra-style')
  .legal{background:#fff;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,.1);padding:28px;margin-top:16px;line-height:1.7;max-width:700px;margin-left:auto;margin-right:auto}
  .legal h2{font-size:1.1rem;margin-top:24px}
  .legal p,.legal li{font-size:14px;color:#444}
  .legal .updated{color:#888;font-size:13px}
  .legal table{width:100%;border-collapse:collapse;margin-top:12px;font-size:13px}
  .legal th,.legal td{text-align:left;padding:8px;border-bottom:1px solid #eee}
@endsection

@section('content')
  <h1>{{ $page->title }}</h1>
  <div class="legal">
    {!! $page->body !!}
  </div>
@endsection
