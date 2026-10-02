@extends('layouts.app')

@section('title', 'Terms of Service — klikwit')
@section('description', 'The terms that govern your use of klikwit.')
@section('page-class', 'wide')
@section('extra-style')
  .legal{background:#fff;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,.1);padding:28px;margin-top:16px;line-height:1.7;max-width:700px;margin-left:auto;margin-right:auto}
  .legal h2{font-size:1.1rem;margin-top:24px}
  .legal p,.legal li{font-size:14px;color:#444}
  .legal .updated{color:#888;font-size:13px}
@endsection

@section('content')
  <h1>Terms of Service</h1>
  <div class="legal">
    <p class="updated">Last updated: October 2026</p>

    <p>These terms govern your use of klikwit (the "Service"). By using klikwit, you agree to these terms.</p>

    <h2>The service</h2>
    <p>klikwit provides URL shortening, QR code generation, link-in-bio pages, and related link-management tools. Core features are free to use. We may introduce paid features in the future; if we do, this page will be updated.</p>

    <h2>Your account</h2>
    <p>You're responsible for keeping your account credentials secure and for all activity under your account. You must provide accurate information when registering.</p>

    <h2>Acceptable use</h2>
    <p>You agree not to use klikwit for phishing, malware distribution, spam, illegal content, or anything else prohibited by our <a href="{{ route('pages.aup') }}">Acceptable Use Policy</a>, which is part of these terms.</p>

    <h2>Content responsibility</h2>
    <p>klikwit does not control, review, or endorse the destinations of links created by users. You are solely responsible for the content you link to. We may disable any link that violates these terms or our Acceptable Use Policy, without prior notice.</p>

    <h2>Termination</h2>
    <p>We may suspend or terminate accounts, or disable links, that violate these terms or are used for abuse, at our discretion.</p>

    <h2>Disclaimer of warranties</h2>
    <p>klikwit is provided "as is" without warranties of any kind. We don't guarantee the service will be uninterrupted, error-free, or available at all times.</p>

    <h2>Limitation of liability</h2>
    <p>To the extent permitted by law, klikwit is not liable for any indirect, incidental, or consequential damages arising from your use of the service, including damages caused by content at the destination of a shortened link.</p>

    <h2>Changes to these terms</h2>
    <p>We may update these terms from time to time. Continued use of klikwit after a change means you accept the updated terms.</p>

    <h2>Governing law</h2>
    <p>These terms are governed by the laws of Bangladesh, without regard to conflict-of-law principles.</p>

    <h2>Contact</h2>
    <p>Questions about these terms? <a href="{{ route('pages.contact') }}">Contact us</a>.</p>
  </div>
@endsection
