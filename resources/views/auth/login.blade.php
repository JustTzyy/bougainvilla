@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">

<div class="login-page">
  <div class="login-card">
    <div class="login-header">
      <div class="login-badge"></div>
      <h1 class="login-title">Purple</h1>
    </div>
    <p class="login-subtitle">Hello! let's get started<br>Sign in to continue.</p>

    <form method="POST" action="{{ route('login.post') }}">
      @csrf
      <div class="form-field">
        <input type="email" name="email" class="form-input" placeholder="Username" required autofocus value="{{ old('email') }}">
      </div>
      <div class="form-field">
        <div class="password-field">
          <input id="password" type="password" name="password" class="form-input" placeholder="Password" required >
          <button type="button" id="togglePassword" class="toggle-password" aria-label="Toggle password visibility" title="Show/Hide password">
            <!-- Unique rounded eye icon -->
            <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <path d="M2 12c2.8-4.2 6-6.5 10-6.5s7.2 2.3 10 6.5c-2.8 4.2-6 6.5-10 6.5S4.8 16.2 2 12z"/>
              <circle cx="12" cy="12" r="3.2"/>
              <path d="M16.5 7.5l1.5-1.5"/>
            </svg>
            <!-- Unique eye-off variant -->
            <svg class="icon-eye-off" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="display:none">
              <path d="M2 12c2.8-4.2 6-6.5 10-6.5 2.1 0 4 .5 5.7 1.5"/>
              <path d="M22 12c-2.8 4.2-6 6.5-10 6.5-2.1 0-4-.5-5.7-1.5"/>
              <circle cx="12" cy="12" r="3.2"/>
              <path d="M3 3l18 18"/>
            </svg>
          </button>
        </div>
      </div>

      @if ($errors->any())
        <div class="error-alert">
          @foreach ($errors->all() as $error)
            {{ $error }}
          @endforeach
        </div>
      @endif

      @if (session('error'))
        <div class="error-alert">{{ session('error') }}</div>
      @endif

      <button type="submit" class="btn-primary">SIGN IN</button>

    </form>
  </div>
</div>

<script>
  (function () {
    var input = document.getElementById('password');
    var toggle = document.getElementById('togglePassword');
    if (!input || !toggle) return;
    var eye = toggle.querySelector('.icon-eye');
    var eyeOff = toggle.querySelector('.icon-eye-off');
    function updateIcons() {
      var isHidden = input.type === 'password';
      if (eye && eyeOff) {
        eye.style.display = isHidden ? 'inline' : 'none';
        eyeOff.style.display = isHidden ? 'none' : 'inline';
      }
    }
    toggle.addEventListener('click', function () {
      input.type = input.type === 'password' ? 'text' : 'password';
      updateIcons();
    });
    updateIcons();
  })();
</script>

@endsection
