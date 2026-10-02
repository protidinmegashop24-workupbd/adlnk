@extends('layouts.app')

@section('title', 'Acceptable Use Policy — klikwit')
@section('description', 'What you may not use klikwit for, and what happens if you do.')
@section('page-class', 'wide')
@section('extra-style')
  .legal{background:#fff;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,.1);padding:28px;margin-top:16px;line-height:1.7;max-width:700px;margin-left:auto;margin-right:auto}
  .legal h2{font-size:1.1rem;margin-top:24px}
  .legal p,.legal li{font-size:14px;color:#444}
@endsection

@section('content')
  <h1>Acceptable Use Policy</h1>
  <div class="legal">
    <p>klikwit's link tools are free and open to anyone, which means we have to be clear about what they must not be used for.</p>

    <h2>Prohibited uses</h2>
    <p>You may not use klikwit to create or share links to:</p>
    <ul>
      <li>Phishing pages or anything designed to steal credentials or personal information</li>
      <li>Malware, viruses, or other malicious software</li>
      <li>Spam, including unsolicited bulk messaging campaigns</li>
      <li>Content that infringes someone else's copyright or trademark</li>
      <li>Illegal content under applicable law</li>
      <li>Content that harasses, threatens, or impersonates a real person</li>
      <li>Fraud, scams, or deceptive schemes</li>
    </ul>

    <p>You also may not attempt to circumvent klikwit's security measures, abuse our systems with excessive automated requests, or use the service to artificially inflate click counts (click fraud).</p>

    <h2>Enforcement</h2>
    <p>If a link is reported or we otherwise discover it violates this policy, we may disable it without prior notice. Repeat violations may result in account suspension or termination. See our <a href="{{ route('report-abuse') }}">Report Abuse</a> page to report a link.</p>

    <h2>Questions</h2>
    <p>If you're unsure whether something is allowed, <a href="{{ route('pages.contact') }}">contact us</a> before you create the link.</p>
  </div>
@endsection
