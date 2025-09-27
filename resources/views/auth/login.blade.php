@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
<script>
  // Add login-body class to body element
  document.addEventListener('DOMContentLoaded', function() {
    document.body.classList.add('login-body');
  });
</script>

<div class="login-page">
  <div class="login-card">
    <!-- Centered Logo -->
    <div class="logo-container">
      <div class="logo">
        <svg width="80" height="80" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
          <!-- Flower petals -->
          <path d="M16 4 C20 8, 20 12, 16 16 C12 12, 12 8, 16 4 Z" fill="#8B0000"/>
          <path d="M28 16 C24 20, 20 20, 16 16 C20 12, 24 12, 28 16 Z" fill="#8B0000"/>
          <path d="M16 28 C12 24, 12 20, 16 16 C20 20, 20 24, 16 28 Z" fill="#8B0000"/>
          <path d="M4 16 C8 12, 12 12, 16 16 C12 20, 8 20, 4 16 Z" fill="#8B0000"/>
          <!-- Flower center -->
          <circle cx="16" cy="16" r="3" fill="#B8860B"/>
          <!-- Stem -->
          <rect x="15" y="20" width="2" height="8" fill="#1a1a1a"/>
          <!-- Leaves -->
          <ellipse cx="12" cy="24" rx="2" ry="1" fill="#1a1a1a" transform="rotate(-30 12 24)"/>
          <ellipse cx="20" cy="24" rx="2" ry="1" fill="#1a1a1a" transform="rotate(30 20 24)"/>
        </svg>
      </div>
      <h1 class="brand-title">BOUGAINVILLA LODGE</h1>
      <p class="brand-subtitle">Welcome back!</p>
      <p class="brand-subtitle">Please sign in to your account</p>
    </div>

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

      <button type="submit" class="btn-primary">LOGIN</button>

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