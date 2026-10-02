@extends('layouts.app')

@section('title', 'Contact klikwit')
@section('description', 'Get in touch with the klikwit team for support, questions, or feedback.')
@section('page-class', 'wide')
@section('extra-style')
  .legal{background:#fff;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,.1);padding:28px;margin-top:16px;line-height:1.7;max-width:700px;margin-left:auto;margin-right:auto}
  .legal h2{font-size:1.1rem;margin-top:24px}
  .legal p,.legal li{font-size:14px;color:#444}
@endsection

@section('content')
  <h1>Contact Us</h1>
  <div class="legal">
    <p>Questions, feedback, or need help with your account? Email us and we'll get back to you as soon as we can:</p>
    <p style="font-size:1.1rem"><strong><a href="mailto:support@klikwit.com">support@klikwit.com</a></strong></p>

    <h2>Reporting abuse</h2>
    <p>If you're reporting a short link being used for phishing, malware, spam, or other abuse, please use our <a href="{{ route('report-abuse') }}">Report Abuse</a> form instead — it gets reviewed faster than email.</p>

    <h2>Copyright/DMCA claims</h2>
    <p>For copyright infringement claims, see our <a href="{{ route('pages.dmca') }}">DMCA Policy</a> for what to include in your notice.</p>
  </div>
@endsection
