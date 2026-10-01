@extends('layouts.app')

@section('title', 'Log In — klikwit')

@section('content')
  <h1>Log In</h1>
  <div class="card">
    @if ($errors->any())
      <div class="error">{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ route('login') }}">
      @csrf
      <label for="email">Email</label>
      <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus/>
      <label for="password">Password</label>
      <input id="password" type="password" name="password" required/>
      <label style="display:flex;align-items:center;gap:6px;margin-top:12px;font-size:13px;color:#555">
        <input type="checkbox" name="remember" style="width:auto"/> Remember me
      </label>
      <button type="submit">Log In</button>
    </form>
  </div>
  <p class="auth-switch">Don't have an account? <a href="{{ route('register') }}">Sign up</a></p>
@endsection
