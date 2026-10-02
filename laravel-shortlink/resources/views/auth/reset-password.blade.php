@extends('layouts.app')

@section('title', 'Reset Password — klikwit')

@section('content')
  <h1>Reset Your Password</h1>
  <div class="card">
    @if ($errors->any())
      <div class="error">{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ route('password.update') }}">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}"/>
      <label for="email">Email</label>
      <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required autofocus/>
      <label for="password">New Password</label>
      <input id="password" type="password" name="password" required/>
      <label for="password_confirmation">Confirm New Password</label>
      <input id="password_confirmation" type="password" name="password_confirmation" required/>
      <button type="submit">Reset Password</button>
    </form>
  </div>
@endsection
