@extends('layouts.app')

@section('title', 'Sign Up — klikwit')

@section('content')
  <h1>Create Your Free Account</h1>
  <div class="card">
    @if ($errors->any())
      <div class="error">{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ route('register') }}">
      @csrf
      <label for="name">Name</label>
      <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus/>
      <label for="email">Email</label>
      <input id="email" type="email" name="email" value="{{ old('email') }}" required/>
      <label for="password">Password</label>
      <input id="password" type="password" name="password" required/>
      <label for="password_confirmation">Confirm Password</label>
      <input id="password_confirmation" type="password" name="password_confirmation" required/>
      <button type="submit">Create Account</button>
    </form>
  </div>
  <p class="auth-switch">Already have an account? <a href="{{ route('login') }}">Log in</a></p>
@endsection
