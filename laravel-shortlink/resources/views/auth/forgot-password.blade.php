@extends('layouts.app')

@section('title', 'Forgot Password — klikwit')

@section('content')
  <h1>Forgot Your Password?</h1>
  <div class="card">
    @if (session('status'))
      <div class="status">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
      <div class="error">{{ $errors->first() }}</div>
    @endif
    <p class="muted" style="margin-top:0">Enter your email and we'll send you a link to reset your password.</p>
    <form method="POST" action="{{ route('password.email') }}">
      @csrf
      <label for="email">Email</label>
      <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus/>
      <button type="submit">Send Reset Link</button>
    </form>
  </div>
  <p class="auth-switch"><a href="{{ route('login') }}">Back to Log In</a></p>
@endsection
