@extends('layouts.app')

@section('title', 'DMCA / Copyright Policy — klikwit')
@section('description', 'How to submit a copyright infringement notice for a klikwit short link.')
@section('page-class', 'wide')
@section('extra-style')
  .legal{background:#fff;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,.1);padding:28px;margin-top:16px;line-height:1.7;max-width:700px;margin-left:auto;margin-right:auto}
  .legal h2{font-size:1.1rem;margin-top:24px}
  .legal p,.legal li{font-size:14px;color:#444}
@endsection

@section('content')
  <h1>DMCA / Copyright Policy</h1>
  <div class="legal">
    <p>klikwit respects the intellectual property rights of others and expects users to do the same. Because klikwit only shortens and redirects links — it does not host the destination content — a copyright claim about what a short link points to should ideally also go to the destination site's own host. That said, we will disable a klikwit short link that is clearly being used to distribute infringing content.</p>

    <h2>How to submit a notice</h2>
    <p>Send a notice via our <a href="{{ route('report-abuse') }}">Report Abuse</a> form (select "Copyright / DMCA infringement") or by <a href="{{ route('pages.contact') }}">emailing us</a>, including:</p>
    <ul>
      <li>Identification of the copyrighted work you claim has been infringed</li>
      <li>The klikwit short link (e.g. klikwit.com/abc123) you're reporting</li>
      <li>Your contact information (name, email, and mailing address)</li>
      <li>A statement that you have a good-faith belief the use is not authorized by the copyright owner, its agent, or the law</li>
      <li>A statement, under penalty of perjury, that the information in your notice is accurate and that you are the copyright owner or authorized to act on their behalf</li>
      <li>Your physical or electronic signature</li>
    </ul>

    <h2>Counter-notice</h2>
    <p>If you believe a link was disabled in error, you may submit a counter-notice via the same channels, including the information above plus a statement under penalty of perjury that you have a good-faith belief the link was removed as a result of mistake or misidentification.</p>

    <h2>Repeat infringers</h2>
    <p>We will disable links and may suspend accounts found to be repeat infringers.</p>
  </div>
@endsection
