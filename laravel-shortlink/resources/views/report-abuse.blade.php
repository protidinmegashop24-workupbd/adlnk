@extends('layouts.app')

@section('title', 'Report Abuse — klikwit')
@section('description', 'Report a klikwit short link for phishing, malware, spam, copyright infringement, or other abuse.')

@section('content')
  <h1>🚫 Report Abuse</h1>
  <p class="muted" style="margin-top:0">Found a klikwit short link being used for phishing, malware, spam, or something else harmful or illegal? Let us know and we'll review it.</p>

  <div class="card">
    @if (session('status'))
      <div class="status">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
      <div class="error">{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ route('report-abuse.submit') }}">
      @csrf
      <label for="link">The short link you want to report</label>
      <input id="link" type="text" name="link" placeholder="klikwit.com/abc123" value="{{ old('link') }}" required/>

      <label for="reason">Reason</label>
      <select id="reason" name="reason" required style="width:100%;box-sizing:border-box;padding:10px;border:1px solid #ccc;border-radius:6px;font-size:15px">
        <option value="">Select a reason</option>
        <option value="phishing" {{ old('reason') === 'phishing' ? 'selected' : '' }}>Phishing</option>
        <option value="malware" {{ old('reason') === 'malware' ? 'selected' : '' }}>Malware</option>
        <option value="spam" {{ old('reason') === 'spam' ? 'selected' : '' }}>Spam</option>
        <option value="copyright" {{ old('reason') === 'copyright' ? 'selected' : '' }}>Copyright / DMCA infringement</option>
        <option value="illegal" {{ old('reason') === 'illegal' ? 'selected' : '' }}>Illegal content</option>
        <option value="other" {{ old('reason') === 'other' ? 'selected' : '' }}>Other</option>
      </select>

      <label for="details">Details (optional)</label>
      <textarea id="details" name="details" rows="4" placeholder="Describe what you found...">{{ old('details') }}</textarea>

      <label for="reporter_email">Your email (optional, in case we need to follow up)</label>
      <input id="reporter_email" type="text" name="reporter_email" placeholder="you@example.com" value="{{ old('reporter_email') }}"/>

      <button type="submit">Submit Report</button>
    </form>
  </div>

  <p class="muted">Reporting a copyright/DMCA issue? See our <a href="{{ route('pages.dmca') }}">DMCA Policy</a> for the full process.</p>
@endsection
