@extends('layouts.frontdeskdashboard')

@section('title','FrontDesk Settings')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/adminsettings.css') }}">
<link rel="stylesheet" href="{{ asset('css/adminrecords.css') }}">
<script src="{{ asset('js/ph-complete-address.js') }}"></script>
<style>
.form-separator {
  position: relative;
  text-align: center;
  margin: 30px 0;
}

.form-separator hr {
  border: none;
  height: 1px;
  background: linear-gradient(to right, transparent, #e0e0e0, transparent);
  margin: 0;
}

.form-separator span {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: white;
  padding: 0 20px;
  color: #666;
  font-size: 14px;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* Form validation error styles */
.error-highlight {
  border: 2px solid #dc3545 !important;
  background-color: #f8d7da !important;
  box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
}

.error-highlight:focus {
  border-color: #dc3545 !important;
  box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
}

.error-message {
  color: #dc3545;
  font-size: 0.875rem;
  margin-top: 0.25rem;
  display: block;
}
</style>
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
      <button class="nav-tab" data-tab="address">
        <i class="fas fa-map-marker-alt"></i>
        <span>Address</span>
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
          
          <form method="POST" action="{{ route('frontdesk.settings.personal') }}">
            @csrf
            @method('PUT')
            
            <div class="form-grid">
              <div class="form-group">
                <label for="firstName">First Name</label>
                <input type="text" id="firstName" name="firstName" class="@error('firstName') error-highlight @enderror" value="{{ old('firstName', Auth::user()->firstName) }}" pattern="[A-Za-z\s]+" title="Only letters and spaces are allowed" oninput="validateTextInput(this)" required>
                @error('firstName')
                  <div class="error-message">{{ $message }}</div>
                @enderror
              </div>
              
              <div class="form-group">
                <label for="middleName">Middle Name</label>
                <input type="text" id="middleName" name="middleName" class="@error('middleName') error-highlight @enderror" value="{{ old('middleName', Auth::user()->middleName) }}" pattern="[A-Za-z\s]+" title="Only letters and spaces are allowed" oninput="validateTextInput(this)">
                @error('middleName')
                  <div class="error-message">{{ $message }}</div>
                @enderror
              </div>
              
              <div class="form-group">
                <label for="lastName">Last Name</label>
                <input type="text" id="lastName" name="lastName" class="@error('lastName') error-highlight @enderror" value="{{ old('lastName', Auth::user()->lastName) }}" pattern="[A-Za-z\s]+" title="Only letters and spaces are allowed" oninput="validateTextInput(this)" required>
                @error('lastName')
                  <div class="error-message">{{ $message }}</div>
                @enderror
              </div>
              
              <div class="form-group">
                <label for="contactNumber">Contact Number</label>
                <input type="text" id="contactNumber" name="contactNumber" class="@error('contactNumber') error-highlight @enderror" value="{{ old('contactNumber', Auth::user()->contactNumber) }}" required>
              @error('contactNumber')
                <div class="error-message">{{ $message }}</div>
              @enderror
              </div>
              
              <div class="form-group">
                <label for="birthday">Birthday</label>
                <input type="date" id="birthday" name="birthday" class="@error('birthday') error-highlight @enderror" value="{{ old('birthday', Auth::user()->birthday) }}" required onchange="calculateAge()">
                @error('birthday')
                  <div class="error-message">{{ $message }}</div>
                @enderror
              </div>
              
              <div class="form-group">
                <label for="age">Age</label>
                <input type="number" id="age" name="age" value="{{ old('age', Auth::user()->age) }}" readonly>
              </div>
              
              <div class="form-group">
                <label for="sex">Sex</label>
                <select id="sex" name="sex" class="@error('sex') error-highlight @enderror" required>
                @error('sex')
                  <div class="error-message">{{ $message }}</div>
                @enderror
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

      <!-- Address Tab -->
      <div class="tab-content" id="address-tab">
        <div class="chart-card">
          <div class="section-header-pad">
            <h3 class="chart-title">Address Information</h3>
          </div>
          
          <form method="POST" action="{{ route('frontdesk.settings.personal') }}">
            @csrf
            @method('PUT')
            
            <!-- Address Section -->
            <div class="address-section">
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label">Street *</label>
                  <input type="text" class="form-input" name="street" value="{{ old('street', Auth::user()->address->street ?? '') }}" required>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label">Province *</label>
                  <select name="province" class="form-input" required>
                    <option value="">Select Province</option>
                  </select>
                </div>
                <div class="form-group">
                  <label class="form-label">City *</label>
                  <select name="city" class="form-input" required>
                    <option value="">Select City</option>
                  </select>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label">ZIP Code *</label>
                  <input type="text" class="form-input" name="zipcode" value="{{ old('zipcode', Auth::user()->address->zipcode ?? '') }}" readonly>
                </div>
              </div>
            </div>
            
            <div class="form-actions">
              <button type="submit" class="action-btn">Update Address</button>
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
          
          <form method="POST" action="{{ route('frontdesk.settings.email') }}">
            @csrf
            @method('PUT')
            
            <div class="form-group">
              <label for="current_email">Current Email</label>
              <input type="email" id="current_email" value="{{ Auth::user()->email }}" readonly>
            </div>
            
            <div class="form-group">
              <label for="new_email">New Email</label>
              <input type="email" id="new_email" name="email" class="@error('email') error-highlight @enderror" value="{{ old('email') }}" required>
              @error('email')
                <div class="error-message">{{ $message }}</div>
              @enderror
            </div>
            
            <div class="form-group">
              <label for="confirm_email">Confirm New Email</label>
              <input type="email" id="confirm_email" name="email_confirmation" class="@error('email_confirmation') error-highlight @enderror" value="{{ old('email_confirmation') }}" required>
              @error('email_confirmation')
                <div class="error-message">{{ $message }}</div>
              @enderror
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
          
          <form method="POST" action="{{ route('frontdesk.settings.password') }}">
            @csrf
            @method('PUT')
            
            <div class="form-group">
              <label for="current_password">Current Password</label>
              <input type="password" id="current_password" name="current_password" class="@error('current_password') error-highlight @enderror" required>
              @error('current_password')
                <div class="error-message">{{ $message }}</div>
              @enderror
              <button type="button" class="toggle-password" onclick="togglePassword('current_password')">
                <i class="fas fa-eye"></i>
              </button>
            </div>
            
            <div class="form-group">
              <label for="new_password">New Password</label>
              <input type="password" id="new_password" name="password" class="@error('password') error-highlight @enderror" required>
              @error('password')
                <div class="error-message">{{ $message }}</div>
              @enderror
              <button type="button" class="toggle-password" onclick="togglePassword('new_password')">
                <i class="fas fa-eye"></i>
              </button>
            </div>
            
            <div class="form-group">
              <label for="confirm_password">Confirm New Password</label>
              <input type="password" id="confirm_password" name="password_confirmation" class="@error('password_confirmation') error-highlight @enderror" required>
              @error('password_confirmation')
                <div class="error-message">{{ $message }}</div>
              @enderror
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
          
          <form method="POST" action="{{ route('frontdesk.settings.deactivate') }}" onsubmit="return confirmDeactivation()">
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

    // Check if URL has hash for password tab
    if (window.location.hash === '#password-tab') {
      // Remove active class from all tabs and contents
      tabs.forEach(t => t.classList.remove('active'));
      tabContents.forEach(tc => tc.classList.remove('active'));
      
      // Activate password tab
      const passwordTab = document.querySelector('[data-tab="password"]');
      const passwordContent = document.getElementById('password-tab');
      if (passwordTab && passwordContent) {
        passwordTab.classList.add('active');
        passwordContent.classList.add('active');
      }
    }

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

  // Philippine Address Data - Use complete data from external file
  var phAddressData = {};

  // Initialize with basic data, then merge with complete data from external file
  function initializeAddressData() {
    // Start with empty object
    phAddressData = {};
    
    // Merge with additional data from external file if available
    if (typeof window.additionalPhAddressData !== 'undefined') {
      Object.assign(phAddressData, window.additionalPhAddressData);
    }
  }

  // Initialize address dropdowns
  function initializeAddressDropdowns() {
    // Ensure address data is initialized
    initializeAddressData();
    
    // Look for address dropdowns in the address tab
    const addressTab = document.getElementById('address-tab');
    if (!addressTab) return;
    
    const provinceSelect = addressTab.querySelector('select[name="province"]');
    const citySelect = addressTab.querySelector('select[name="city"]');
    const zipInput = addressTab.querySelector('input[name="zipcode"]');
    
    if (!provinceSelect || !citySelect || !zipInput) return;
    
    // Get current values from the form
    const currentProvince = '{{ old("province", Auth::user()->address->province ?? "") }}';
    const currentCity = '{{ old("city", Auth::user()->address->city ?? "") }}';
    const currentZipcode = '{{ old("zipcode", Auth::user()->address->zipcode ?? "") }}';
    
    // Populate province dropdown
    provinceSelect.innerHTML = '<option value="">Select Province</option>';
    Object.keys(phAddressData).forEach(function(province) {
      const option = document.createElement('option');
      option.value = province;
      option.textContent = province;
      if (province === currentProvince) {
        option.selected = true;
      }
      provinceSelect.appendChild(option);
    });
    
    // If current province is set, populate cities
    if (currentProvince && phAddressData[currentProvince]) {
      citySelect.innerHTML = '<option value="">Select City</option>';
      Object.keys(phAddressData[currentProvince]).forEach(function(city) {
        const option = document.createElement('option');
        option.value = city;
        option.textContent = city;
        if (city === currentCity) {
          option.selected = true;
        }
        citySelect.appendChild(option);
      });
      
      // Set current zipcode if available
      if (currentZipcode) {
        zipInput.value = currentZipcode;
      }
    }
    
    // Province change handler
    provinceSelect.addEventListener('change', function() {
      const selectedProvince = this.value;
      
      // Clear city options
      citySelect.innerHTML = '<option value="">Select City</option>';
      zipInput.value = '';
      
      if (selectedProvince && phAddressData[selectedProvince]) {
        // Add cities for selected province
        Object.keys(phAddressData[selectedProvince]).forEach(function(city) {
          const option = document.createElement('option');
          option.value = city;
          option.textContent = city;
          citySelect.appendChild(option);
        });
      }
    });
    
    // City change handler
    citySelect.addEventListener('change', function() {
      const selectedCity = this.value;
      const selectedProvince = provinceSelect.value;
      
      if (selectedCity && selectedProvince && phAddressData[selectedProvince]) {
        zipInput.value = phAddressData[selectedProvince][selectedCity] || '';
      } else {
        zipInput.value = '';
      }
    });
  }

  // Initialize address dropdowns when page loads and when address tab is clicked
  document.addEventListener('DOMContentLoaded', function() {
    initializeAddressDropdowns();
    
    // Also initialize when address tab is clicked
    const addressTab = document.querySelector('[data-tab="address"]');
    if (addressTab) {
      addressTab.addEventListener('click', function() {
        setTimeout(initializeAddressDropdowns, 100); // Small delay to ensure tab is active
      });
    }
  });
</script>

<script>
  // Input validation functions
  function validateTextInput(input) {
    // Remove any numbers or special characters (keep only letters and spaces)
    input.value = input.value.replace(/[^A-Za-z\s]/g, '');
  }

  function validateNumberInput(input) {
    // Remove any non-numeric characters
    input.value = input.value.replace(/[^0-9]/g, '');
  }
</script>
@endsection

