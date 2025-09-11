@extends('layouts.admindashboard')

@section('title','Admin Settings')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/adminsettings.css') }}">
@endpush

@section('content')
<div class="dashboard-page">
  <div class="page-header">
    <h1 class="page-title">Settings</h1>
  </div>

  @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif
  @if ($errors->any())
    <div class="alert alert-danger">
      <ul style="margin:0; padding-left:18px;">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <script>
    (function () {
      var alerts = document.querySelectorAll('.alert');
      if (!alerts.length) return;
      setTimeout(function() {
        alerts.forEach(function(el){
          if (el && el.parentNode) {
            el.parentNode.removeChild(el);
          }
        });
      }, 10000); // 10 seconds
    })();
  </script>

  <div class="settings-container">
    <!-- Settings Navigation -->
    <div class="settings-nav">
      <button class="nav-tab active" data-tab="personal">
        <i class="fas fa-user"></i>
        <span>Personal Information</span>
      </button>
      <button class="nav-tab" data-tab="email">
        <i class="fas fa-envelope"></i>
        <span>Change Email</span>
      </button>
      <button class="nav-tab" data-tab="password">
        <i class="fas fa-lock"></i>
        <span>Change Password</span>
      </button>
      <button class="nav-tab" data-tab="deactivate">
        <i class="fas fa-user-times"></i>
        <span>Deactivate Account</span>
      </button>
    </div>

    <!-- Settings Content -->
    <div class="settings-content">
      <!-- Personal Information Tab -->
      <div class="tab-content active" id="personal-tab">
        <div class="chart-card">
          <div class="section-header-pad">
            <h3 class="chart-title">Personal Information</h3>
          </div>
          
          <form method="POST" action="{{ route('adminPages.settings.personal') }}">
            @csrf
            @method('PUT')
            
            <div class="form-grid">
              <div class="form-group">
                <label for="firstName">First Name</label>
                <input type="text" id="firstName" name="firstName" value="{{ old('firstName', Auth::user()->firstName) }}" required>
              </div>
              
              <div class="form-group">
                <label for="middleName">Middle Name</label>
                <input type="text" id="middleName" name="middleName" value="{{ old('middleName', Auth::user()->middleName) }}">
              </div>
              
              <div class="form-group">
                <label for="lastName">Last Name</label>
                <input type="text" id="lastName" name="lastName" value="{{ old('lastName', Auth::user()->lastName) }}" required>
              </div>
              
              <div class="form-group">
                <label for="contactNumber">Contact Number</label>
                <input type="text" id="contactNumber" name="contactNumber" value="{{ old('contactNumber', Auth::user()->contactNumber) }}" required>
              </div>
              
              <div class="form-group">
                <label for="birthday">Birthday</label>
                <input type="date" id="birthday" name="birthday" value="{{ old('birthday', Auth::user()->birthday) }}" required onchange="calculateAge()">
              </div>
              
              <div class="form-group">
                <label for="age">Age</label>
                <input type="number" id="age" name="age" value="{{ old('age', Auth::user()->age) }}" readonly>
              </div>
              
              <div class="form-group">
                <label for="sex">Sex</label>
                <select id="sex" name="sex" required>
                  <option value="">Select Sex</option>
                  <option value="Male" {{ old('sex', Auth::user()->sex) == 'Male' ? 'selected' : '' }}>Male</option>
                  <option value="Female" {{ old('sex', Auth::user()->sex) == 'Female' ? 'selected' : '' }}>Female</option>
                </select>
              </div>
            </div>
            
            <div class="form-actions">
              <button type="submit" class="action-btn">Update Personal Information</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Change Email Tab -->
      <div class="tab-content" id="email-tab">
        <div class="chart-card">
          <div class="section-header-pad">
            <h3 class="chart-title">Change Email</h3>
          </div>
          
          <form method="POST" action="{{ route('adminPages.settings.email') }}">
            @csrf
            @method('PUT')
            
            <div class="form-group">
              <label for="current_email">Current Email</label>
              <input type="email" id="current_email" value="{{ Auth::user()->email }}" readonly>
            </div>
            
            <div class="form-group">
              <label for="new_email">New Email</label>
              <input type="email" id="new_email" name="email" value="{{ old('email') }}" required>
            </div>
            
            <div class="form-group">
              <label for="confirm_email">Confirm New Email</label>
              <input type="email" id="confirm_email" name="email_confirmation" value="{{ old('email_confirmation') }}" required>
            </div>
            
            <div class="form-actions">
              <button type="submit" class="action-btn">Update Email</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Change Password Tab -->
      <div class="tab-content" id="password-tab">
        <div class="chart-card">
          <div class="section-header-pad">
            <h3 class="chart-title">Change Password</h3>
          </div>
          
          <form method="POST" action="{{ route('adminPages.settings.password') }}">
            @csrf
            @method('PUT')
            
            <div class="form-group">
              <label for="current_password">Current Password</label>
              <input type="password" id="current_password" name="current_password" required>
              <button type="button" class="toggle-password" onclick="togglePassword('current_password')">
                <i class="fas fa-eye"></i>
              </button>
            </div>
            
            <div class="form-group">
              <label for="new_password">New Password</label>
              <input type="password" id="new_password" name="password" required>
              <button type="button" class="toggle-password" onclick="togglePassword('new_password')">
                <i class="fas fa-eye"></i>
              </button>
            </div>
            
            <div class="form-group">
              <label for="confirm_password">Confirm New Password</label>
              <input type="password" id="confirm_password" name="password_confirmation" required>
              <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')">
                <i class="fas fa-eye"></i>
              </button>
            </div>
            
            <div class="form-actions">
              <button type="submit" class="action-btn">Update Password</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Account Deactivation Tab -->
      <div class="tab-content" id="deactivate-tab">
        <div class="chart-card">
          <div class="section-header-pad">
            <h3 class="chart-title">Account Deactivation</h3>
          </div>
          
          <div class="deactivate-warning">
            <div class="warning-icon">
              <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="warning-content">
              <h4>Deactivate Your Account</h4>
              <p>This action will deactivate your account and you will be logged out immediately. Your data will be preserved but you won't be able to access the system until an administrator reactivates your account.</p>
              <ul>
                <li>You will be logged out immediately</li>
                <li>Your account will be deactivated</li>
                <li>You cannot log back in until reactivated</li>
                <li>Contact an administrator to reactivate your account</li>
              </ul>
            </div>
          </div>
          
          <form method="POST" action="{{ route('adminPages.settings.deactivate') }}" onsubmit="return confirmDeactivation()">
            @csrf
            @method('DELETE')
            
            <div class="form-group">
              <label for="deactivate_password">Enter your password to confirm deactivation</label>
              <input type="password" id="deactivate_password" name="password" required>
              <button type="button" class="toggle-password" onclick="togglePassword('deactivate_password')">
                <i class="fas fa-eye"></i>
              </button>
            </div>
            
            <div class="form-actions">
              <button type="submit" class="action-btn danger">Deactivate Account</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Tab functionality
  document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.nav-tab');
    const tabContents = document.querySelectorAll('.tab-content');

    tabs.forEach(tab => {
      tab.addEventListener('click', function() {
        const targetTab = this.getAttribute('data-tab');
        
        // Remove active class from all tabs and contents
        tabs.forEach(t => t.classList.remove('active'));
        tabContents.forEach(content => content.classList.remove('active'));
        
        // Add active class to clicked tab and corresponding content
        this.classList.add('active');
        document.getElementById(targetTab + '-tab').classList.add('active');
      });
    });
  });

  // Password toggle functionality
  function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const button = input.nextElementSibling;
    const icon = button.querySelector('i');
    
    if (input.type === 'password') {
      input.type = 'text';
      icon.classList.remove('fa-eye');
      icon.classList.add('fa-eye-slash');
    } else {
      input.type = 'password';
      icon.classList.remove('fa-eye-slash');
      icon.classList.add('fa-eye');
    }
  }

  // Age calculation functionality
  function calculateAge() {
    const birthdayInput = document.getElementById('birthday');
    const ageInput = document.getElementById('age');
    
    if (birthdayInput.value) {
      const birthday = new Date(birthdayInput.value);
      const today = new Date();
      
      let age = today.getFullYear() - birthday.getFullYear();
      const monthDiff = today.getMonth() - birthday.getMonth();
      
      // Adjust age if birthday hasn't occurred this year
      if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthday.getDate())) {
        age--;
      }
      
      ageInput.value = age;
    } else {
      ageInput.value = '';
    }
  }

  // Calculate age on page load if birthday is already set
  document.addEventListener('DOMContentLoaded', function() {
    calculateAge();
  });

  // Account deactivation confirmation
  function confirmDeactivation() {
    return confirm('Are you sure you want to deactivate your account? This action cannot be undone and you will be logged out immediately. You will need an administrator to reactivate your account.');
  }
</script>
@endsection
