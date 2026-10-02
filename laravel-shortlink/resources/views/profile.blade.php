@extends('layouts.dashboard')

@section('title', 'Profile Settings — klikwit')
@section('extra-style')
  .danger-card{border:1px solid #f3c6c6;background:#fff8f8}
  .danger-card h2{color:#c0392b}
  .danger-card button{background:#c0392b}
@endsection

@section('content')
  <h1>Profile Settings</h1>

  @if (session('status'))
    <div class="status">{{ session('status') }}</div>
  @endif

  <div class="card">
    <h2 style="margin-top:0;font-size:1rem">Account Information</h2>
    @if ($errors->hasAny(['name', 'email']))
      <div class="error">{{ $errors->first('name') ?: $errors->first('email') }}</div>
    @endif
    <form method="POST" action="{{ route('profile.update') }}">
      @csrf
      @method('PUT')
      <label for="name">Name</label>
      <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required/>
      <label for="email">Email</label>
      <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required/>
      <button type="submit">Save Changes</button>
    </form>
  </div>

  <div class="card">
    <h2 style="margin-top:0;font-size:1rem">Change Password</h2>
    @if ($errors->hasAny(['current_password', 'password']))
      <div class="error">{{ $errors->first('current_password') ?: $errors->first('password') }}</div>
    @endif
    <form method="POST" action="{{ route('profile.password') }}">
      @csrf
      @method('PUT')
      <label for="current_password">Current Password</label>
      <input id="current_password" type="password" name="current_password" required/>
      <label for="password">New Password</label>
      <input id="password" type="password" name="password" required/>
      <label for="password_confirmation">Confirm New Password</label>
      <input id="password_confirmation" type="password" name="password_confirmation" required/>
      <button type="submit">Change Password</button>
    </form>
  </div>

  <div class="card danger-card">
    <h2 style="margin-top:0;font-size:1rem">Danger Zone</h2>
    <p class="hint" style="margin-top:0">Deleting your account is permanent. Your short links will keep working but will no longer be tied to your account; your link-in-bio page will be deleted.</p>
    <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Are you sure you want to permanently delete your account? This cannot be undone.');">
      @csrf
      @method('DELETE')
      <label for="delete_current_password">Enter your password to confirm</label>
      <input id="delete_current_password" type="password" name="current_password" required/>
      <button type="submit">Delete My Account</button>
    </form>
  </div>
@endsection
