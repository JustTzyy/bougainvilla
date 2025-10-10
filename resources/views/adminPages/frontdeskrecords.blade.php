@extends('layouts.admindashboard')

@section('title','Admin Records')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/adminrecords.css') }}">
<script src="{{ asset('js/ph-complete-address.js') }}"></script>
<style>
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

.tab-btn.error-tab {
  background-color: #f8d7da !important;
  border-color: #dc3545 !important;
  color: #721c24 !important;
  position: relative;
}

.tab-btn.error-tab::after {
  content: "!";
  position: absolute;
  top: -5px;
  right: -5px;
  background-color: #dc3545;
  color: white;
  border-radius: 50%;
  width: 18px;
  height: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: bold;
}

.tab-btn.error-tab:hover {
  background-color: #f5c6cb !important;
  border-color: #dc3545 !important;
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
    <h1 class="page-title">Front Desk Records</h1>
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

  <div class="records-toolbar">
    <div class="search-container admin-search">
      <i class="fas fa-search search-icon"></i>
      <input id="adminSearch" type="text" placeholder="Search Front Desks" class="search-input">
    </div>
    <div class="toolbar-actions">
      <a href="{{ route('frontdeskrecords.archive') }}" class="archive-btn">
        <i class="fas fa-archive"></i> Archive
      </a>
      <button id="openAddAdmin"><i class="fas fa-user-plus"></i> Add Front Desk</button>
    </div>
  </div>

  <div class="chart-card card-tight">
    <div class="section-header-pad">
      <h3 class="chart-title">List</h3>
    </div>

    <div class="table-wrapper">
      <table class="table sortable-table" id="adminsTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Date Created</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @if(isset($users) && $users->count() > 0)
            @foreach($users as $user)
              <tr class="user-row"
                  data-user-id="{{ $user->id }}"
                  data-name="{{ $user->name }}"
                  data-middle="{{ $user->middleName }}"
                  data-email="{{ $user->email }}"
                  data-birthday="{{ $user->birthday }}"
                  data-age="{{ $user->age }}"
                  data-sex="{{ $user->sex }}"
                  data-contact="{{ $user->contactNumber }}"
                  data-status="{{ $user->status ?? 'Active' }}"
                  data-created="{{ $user->created_at }}"
                  data-street="{{ optional($user->address)->street }}"
                  data-city="{{ optional($user->address)->city }}"
                  data-province="{{ optional($user->address)->province }}"
                  data-zipcode="{{ optional($user->address)->zipcode }}">
                <td data-label="ID">{{ $user->id }}</td>
                <td data-label="Name" class="user-name">{{ $user->name }}</td>
                <td data-label="Email">{{ $user->email }}</td>
                <td data-label="Date Created">{{ $user->created_at->format('M d, Y') }}</td>
                <td data-label="Actions">
                  <button class="action-btn small" data-update data-user-id="{{ $user->id }}"> 
                    <i class="fas fa-pen"></i> 
                  </button>
                  <button class="action-btn small" data-archive data-user-id="{{ $user->id }}"> 
                    <i class="fas fa-archive"></i> 
                  </button>
                </td>
              </tr>
            @endforeach
          @else
            <tr>
              <td colspan="5" class="text-center">No Front Desk records found</td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>
    @if(isset($accommodations) && $accommodations->hasPages())
    <nav class="pagination-wrapper" aria-label="Table pagination">
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($accommodations->onFirstPage())
                <li class="page-item disabled"><span>&laquo;</span></li>
            @else
                <li class="page-item"><a href="{{ $accommodations->previousPageUrl() }}">&laquo;</a></li>
            @endif

            {{-- Pagination Elements --}}
            @php
                $currentPage = $accommodations->currentPage();
                $lastPage = $accommodations->lastPage();
                $maxVisiblePages = 10;
                $startPage = max(1, $currentPage - floor($maxVisiblePages / 2));
                $endPage = min($lastPage, $startPage + $maxVisiblePages - 1);
                
                // Adjust startPage if we're near the end
                if ($endPage - $startPage + 1 < $maxVisiblePages) {
                    $startPage = max(1, $endPage - $maxVisiblePages + 1);
                }
            @endphp
            
            {{-- First page if not in range --}}
            @if($startPage > 1)
                <li class="page-item"><a href="{{ $accommodations->url(1) }}">1</a></li>
                @if($startPage > 2)
                    <li class="page-item disabled"><span class="page-link disabled">...</span></li>
                @endif
            @endif
            
            {{-- Page numbers in range --}}
            @for($i = $startPage; $i <= $endPage; $i++)
                @if($i == $currentPage)
                    <li class="page-item active"><span>{{ $i }}</span></li>
                @else
                    <li class="page-item"><a href="{{ $accommodations->url($i) }}">{{ $i }}</a></li>
                @endif
            @endfor
            
            {{-- Last page if not in range --}}
            @if($endPage < $lastPage)
                @if($endPage < $lastPage - 1)
                    <li class="page-item disabled"><span class="page-link disabled">...</span></li>
                @endif
                <li class="page-item"><a href="{{ $accommodations->url($lastPage) }}">{{ $lastPage }}</a></li>
            @endif

            {{-- Next Page Link --}}
            @if ($accommodations->hasMorePages())
                <li class="page-item"><a href="{{ $accommodations->nextPageUrl() }}">&raquo;</a></li>
            @else
                <li class="page-item disabled"><span>&raquo;</span></li>
            @endif
        </ul>
    </nav>
@endif
  </div>
</div>

<!-- Add/Edit Modal -->
<div id="adminModal" class="modal">
  <div class="modal-card">
    <div class="modal-header">
      <h3 class="chart-title" id="modalTitle">Add Front Desk</h3>
      <button id="closeAdminModal" class="action-btn ml-auto"><i class="fas fa-times"></i></button>
    </div>
    
    <!-- Tab Navigation -->
    <div class="modal-tabs">
      <button class="tab-btn active" data-tab="user">
        <i class="fas fa-user"></i>
        <span>User Information</span>
      </button>
      <button class="tab-btn" data-tab="address">
        <i class="fas fa-map-marker-alt"></i>
        <span>Address Information</span>
      </button>
    </div>

    <form id="adminForm" action="{{ route('adminPages.frontdeskrecords') }}" class="modal-form"  method="POST">
    @csrf
    <input type="hidden" name="roleID" value="2">
      <!-- User Information Tab -->
      <div class="tab-content active" id="user-tab">
        <div class="form-grid">
          <div class="form-group">
            <label>First Name</label>
            <input name="firstName" class="form-input @error('firstName') error-highlight @enderror" placeholder="Enter first name" value="{{ old('firstName') }}" pattern="[A-Za-z\s]+" title="Only letters and spaces are allowed" oninput="validateTextInput(this)" required>
            @error('firstName')
              <div class="error-message">{{ $message }}</div>
            @enderror
          </div>
          <div class="form-group">
            <label>Middle Name</label>
            <input name="middleName" class="form-input @error('middleName') error-highlight @enderror" placeholder="Enter middle name (optional)" value="{{ old('middleName') }}" pattern="[A-Za-z\s]+" title="Only letters and spaces are allowed" oninput="validateTextInput(this)">
            @error('middleName')
              <div class="error-message">{{ $message }}</div>
            @enderror
          </div>
          <div class="form-group">
            <label>Last Name</label>
            <input name="lastName" class="form-input @error('lastName') error-highlight @enderror" placeholder="Enter last name" value="{{ old('lastName') }}" pattern="[A-Za-z\s]+" title="Only letters and spaces are allowed" oninput="validateTextInput(this)" required>
            @error('lastName')
              <div class="error-message">{{ $message }}</div>
            @enderror
          </div>
 
          <div class="form-group span-2">
            <label>Email</label>
            <input type="email" name="email" class="form-input @error('email') error-highlight @enderror" placeholder="FrontDesk@example.com" value="{{ old('email') }}" required>
            @error('email')
              <div class="error-message">{{ $message }}</div>
            @enderror
          </div>
          <div class="form-group">
            <label>Birthday</label>
            <input type="date" name="birthday" id="birthday" class="form-input @error('birthday') error-highlight @enderror" value="{{ old('birthday') }}" required>
            @error('birthday')
              <div class="error-message">{{ $message }}</div>
            @enderror
          </div>
          <div class="form-group">
            <label>Age</label>
            <input type="number" name="age" id="age" class="form-input" placeholder="25" readonly>
          </div>
          <div class="form-group">
            <label>Sex</label>
            <select name="sex" class="form-input @error('sex') error-highlight @enderror" required>
              <option value="">Select Sex</option>
              <option value="Male" {{ old('sex') == 'Male' ? 'selected' : '' }}>Male</option>
              <option value="Female" {{ old('sex') == 'Female' ? 'selected' : '' }}>Female</option>
            </select>
            @error('sex')
              <div class="error-message">{{ $message }}</div>
            @enderror
          </div>
          <div class="form-group">
            <label>Contact Number</label>
            <input type="tel" name="contactNumber" class="form-input @error('contactNumber') error-highlight @enderror" placeholder="+63 912 345 6789" value="{{ old('contactNumber') }}" required>
            @error('contactNumber')
              <div class="error-message">{{ $message }}</div>
            @enderror
          </div>
          
         
        </div>
      </div>

      <!-- Address Information Tab -->
      <div class="tab-content" id="address-tab">
        <div class="form-grid">
          <div class="form-group span-2">
            <label>Street Address</label>
            <input name="street" class="form-input @error('street') error-highlight @enderror" placeholder="123 Main Street, Barangay Name" value="{{ old('street') }}" required>
            @error('street')
              <div class="error-message">{{ $message }}</div>
            @enderror
          </div>
          <div class="form-group">
            <label>Province</label>
            <select name="province" id="province" class="form-input @error('province') error-highlight @enderror" required>
              <option value="">Select Province</option>
            </select>
            @error('province')
              <div class="error-message">{{ $message }}</div>
            @enderror
          </div>
          <div class="form-group">
            <label>City</label>
            <select name="city" id="city" class="form-input @error('city') error-highlight @enderror" required>
              <option value="">Select City</option>
            </select>
            @error('city')
              <div class="error-message">{{ $message }}</div>
            @enderror
          </div>
          <div class="form-group">
            <label>Zip Code</label>
            <input name="zipcode" id="zipcode" class="form-input" placeholder="Auto-filled" readonly>
          </div>
        </div>
      </div>

      <div class="modal-actions">
        <button type="button" id="cancelAdmin" class="action-btn btn-outline">Cancel</button>
        <button type="submit" class="btn-primary inline">Save Front Desk</button>
      </div>
    </form>
  </div>
</div>

<!-- User Details Modal -->
<div id="userDetailsModal" class="modal">
  <div class="modal-card user-details-card">
    <div class="modal-header">
      <h3 class="chart-title">User Details</h3>
      <button id="closeUserDetailsModal" class="action-btn ml-auto"><i class="fas fa-times"></i></button>
    </div>
    
    <div class="user-details-content">
      <div class="user-info-section">
        <h4><i class="fas fa-user"></i> Personal Information</h4>
        <div class="info-grid">
          <div class="info-item">
            <label>Full Name:</label> 
            <span id="detail-name">-</span>
          </div>
          <div class="info-item">
            <label>Email:</label>
            <span id="detail-email">-</span>
          </div>
          <div class="info-item">
            <label>Birthday:</label>
            <span id="detail-birthday">-</span>
          </div>
          <div class="info-item">
            <label>Age:</label>
            <span id="detail-age">-</span>
          </div>
          <div class="info-item">
            <label>Sex:</label>
            <span id="detail-sex">-</span>
          </div>
          <div class="info-item">
            <label>Contact Number:</label>
            <span id="detail-contact">-</span>
          </div>
          <div class="info-item">
            <label>Status:</label>
            <span id="detail-status" class="status-badge">-</span>
          </div>
          <div class="info-item">
            <label>Date Created:</label>
            <span id="detail-created">-</span>
          </div>
        </div>
      </div>
      
      <div class="address-info-section">
        <h4><i class="fas fa-map-marker-alt"></i> Address Information</h4>
        <div class="info-grid">
          <div class="info-item span-2">
            <label>Street Address:</label>
            <span id="detail-street">-</span>
          </div>
          <div class="info-item">
            <label>City:</label>
            <span id="detail-city">-</span>
          </div>
          <div class="info-item">
            <label>Province:</label>
            <span id="detail-province">-</span>
          </div>
          <div class="info-item">
            <label>Zip Code:</label>
            <span id="detail-zipcode">-</span>
          </div>
        </div>
      </div>
    </div>
    
    <div class="modal-actions">
      <button type="button" id="closeUserDetails" class="action-btn btn-outline">Close</button>
    </div>
  </div>
</div>

<!-- Update Admin Modal -->
<div id="updateModal" class="modal">
  <div class="modal-card">
    <div class="modal-header">
      <h3 class="chart-title">Update Front Desk</h3>
      <button id="closeUpdateModal" class="action-btn ml-auto"><i class="fas fa-times"></i></button>
    </div>

    <div class="modal-tabs">
      <button class="tab-btn active" data-tab="update-user">
        <i class="fas fa-user"></i>
        <span>User Information</span>
      </button>
      <button class="tab-btn" data-tab="update-address">
        <i class="fas fa-map-marker-alt"></i>
        <span>Address Information</span>
      </button>
    </div>

    <form id="updateForm" class="modal-form" method="POST">
      @csrf
      @method('POST')
      <div class="tab-content active" id="update-user-tab">
        <div class="form-grid">
          <div class="form-group">
            <label>First Name</label>
            <input name="firstName" id="u_firstName" class="form-input @error('firstName') error-highlight @enderror" value="{{ old('firstName') }}" pattern="[A-Za-z\s]+" title="Only letters and spaces are allowed" oninput="validateTextInput(this)" required>
            @error('firstName')
              <div class="error-message">{{ $message }}</div>
            @enderror
          </div>
          <div class="form-group">
            <label>Middle Name</label>
            <input name="middleName" id="u_middleName" class="form-input @error('middleName') error-highlight @enderror" value="{{ old('middleName') }}" pattern="[A-Za-z\s]+" title="Only letters and spaces are allowed" oninput="validateTextInput(this)">
            @error('middleName')
              <div class="error-message">{{ $message }}</div>
            @enderror
          </div>
          <div class="form-group">
            <label>Last Name</label>
            <input name="lastName" id="u_lastName" class="form-input @error('lastName') error-highlight @enderror" value="{{ old('lastName') }}" pattern="[A-Za-z\s]+" title="Only letters and spaces are allowed" oninput="validateTextInput(this)" required>
            @error('lastName')
              <div class="error-message">{{ $message }}</div>
            @enderror
          </div>
          <div class="form-group span-2">
            <label>Email</label>
            <input type="email" name="email" id="u_email" class="form-input @error('email') error-highlight @enderror" value="{{ old('email') }}" required>
            @error('email')
              <div class="error-message">{{ $message }}</div>
            @enderror
          </div>
          <div class="form-group">
            <label>Birthday</label>
            <input type="date" name="birthday" id="u_birthday" class="form-input @error('birthday') error-highlight @enderror" value="{{ old('birthday') }}" required>
            @error('birthday')
              <div class="error-message">{{ $message }}</div>
            @enderror
          </div>
          <div class="form-group">
            <label>Age</label>
            <input type="number" name="age" id="u_age" class="form-input" readonly>
          </div>
          <div class="form-group">
            <label>Sex</label>
            <select name="sex" id="u_sex" class="form-input @error('sex') error-highlight @enderror" required>
              <option value="">Select Sex</option>
              <option value="Male" {{ old('sex') == 'Male' ? 'selected' : '' }}>Male</option>
              <option value="Female" {{ old('sex') == 'Female' ? 'selected' : '' }}>Female</option>
            </select>
            @error('sex')
              <div class="error-message">{{ $message }}</div>
            @enderror
          </div>
          <div class="form-group">
            <label>Contact Number</label>
            <input type="tel" name="contactNumber" id="u_contactNumber" class="form-input @error('contactNumber') error-highlight @enderror" value="{{ old('contactNumber') }}" required>
            @error('contactNumber')
              <div class="error-message">{{ $message }}</div>
            @enderror
          </div>
        </div>
      </div>

      <div class="tab-content" id="update-address-tab">
        <div class="form-grid">
          <div class="form-group span-2">
            <label>Street Address</label>
            <input name="street" id="u_street" class="form-input @error('street') error-highlight @enderror" value="{{ old('street') }}" required>
            @error('street')
              <div class="error-message">{{ $message }}</div>
            @enderror
          </div>
          <div class="form-group">
            <label>Province</label>
            <select name="province" id="u_province" class="form-input @error('province') error-highlight @enderror" required>
              <option value="">Select Province</option>
            </select>
            @error('province')
              <div class="error-message">{{ $message }}</div>
            @enderror
          </div>
          <div class="form-group">
            <label>City</label>
            <select name="city" id="u_city" class="form-input @error('city') error-highlight @enderror" required>
              <option value="">Select City</option>
            </select>
            @error('city')
              <div class="error-message">{{ $message }}</div>
            @enderror
          </div>
          <div class="form-group">
            <label>Zip Code</label>
            <input name="zipcode" id="u_zipcode" class="form-input" placeholder="Auto-filled" readonly>
          </div>
        </div>
      </div>

      <div class="modal-actions">
        <button type="button" id="cancelUpdate" class="action-btn btn-outline">Cancel</button>
        <button type="submit" class="btn-primary inline">Update Front Desk</button>
      </div>
    </form>
  </div>
</div>
<script>
  (function(){
    var modal = document.getElementById('adminModal');
    var openBtn = document.getElementById('openAddAdmin');
    var closeBtn = document.getElementById('closeAdminModal');
    var cancelBtn = document.getElementById('cancelAdmin');
    var search = document.getElementById('adminSearch');
    var table = document.getElementById('adminsTable').getElementsByTagName('tbody')[0];

    // Auto-align table cells: numbers right, text left
    function isNumericValue(text){
      if (text == null) return false;
      var t = String(text).trim().replace(/[,\s]/g, '');
      if (t === '') return false;
      // Allow leading currency symbol and negative sign
      t = t.replace(/^[-₱$€¥£]/, '');
      return !isNaN(t) && isFinite(t);
    }
    function alignTableCells(){
      try {
        var tbody = document.getElementById('adminsTable').getElementsByTagName('tbody')[0];
        Array.prototype.forEach.call(tbody.rows, function(row){
          Array.prototype.forEach.call(row.cells, function(cell){
            var text = cell.textContent || '';
            if (isNumericValue(text)) {
              cell.style.textAlign = 'right';
            } else {
              cell.style.textAlign = 'left';
            }
          });
        });
      } catch(e) {}
    }

    function openModal(){ 
      modal.style.display = 'flex'; 
      // Ensure User Information tab is active when modal opens
      document.querySelectorAll('.tab-btn').forEach(function(tb) { tb.classList.remove('active'); });
      document.querySelectorAll('.tab-content').forEach(function(tc) { tc.classList.remove('active'); });
      document.querySelector('[data-tab="user"]').classList.add('active');
      document.getElementById('user-tab').classList.add('active');
    }
    
    // Add confirmation for Add Admin form
    var adminForm = document.getElementById('adminForm');
    if (adminForm) {
      adminForm.addEventListener('submit', function(e) {
        e.preventDefault();
        var firstName = document.querySelector('input[name="firstName"]').value;
        var lastName = document.querySelector('input[name="lastName"]').value;
        var email = document.querySelector('input[name="email"]').value;
        
        if (confirm('Are you sure you want to add ' + firstName + ' ' + lastName + ' (' + email + ') as a new Front Desk?')) {
          this.submit();
        }
      });
    }
    function closeModal(){ modal.style.display = 'none'; }
    if (openBtn) openBtn.addEventListener('click', openModal);
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    // simple client-side search
    if (search) search.addEventListener('input', function(){
      var q = this.value.toLowerCase();
      Array.prototype.forEach.call(table.rows, function(row){
        var text = row.innerText.toLowerCase();
        row.style.display = text.indexOf(q) !== -1 ? '' : 'none';
      });
    });

    // Initial alignment
    alignTableCells();

    // Tab functionality
    var tabBtns = document.querySelectorAll('.tab-btn');
    var tabContents = document.querySelectorAll('.tab-content');
    
    tabBtns.forEach(function(btn) {
      btn.addEventListener('click', function() {
        var targetTab = this.getAttribute('data-tab');
        
        // Remove active class from all tabs and contents
        tabBtns.forEach(function(tb) { tb.classList.remove('active'); });
        tabContents.forEach(function(tc) { tc.classList.remove('active'); });
        
        // Add active class to clicked tab and corresponding content
        this.classList.add('active');
        document.getElementById(targetTab + '-tab').classList.add('active');
      });
    });

    // Auto-calculate age from birthday
    var birthdayInput = document.getElementById('birthday');
    var ageInput = document.getElementById('age');
    
    function setComputedAge() {
      if (!birthdayInput || !ageInput || !birthdayInput.value) return;
      var birthday = new Date(birthdayInput.value);
      if (isNaN(birthday.getTime())) return;
      var today = new Date();
      var age = today.getFullYear() - birthday.getFullYear();
      var monthDiff = today.getMonth() - birthday.getMonth();
      if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthday.getDate())) {
        age--;
      }
      ageInput.value = age > 0 ? age : '';
    }

    if (birthdayInput && ageInput) {
      birthdayInput.addEventListener('change', setComputedAge);
      // Compute on load in case a value exists
      setComputedAge();
      // Ensure value is present before submit
      var adminForm = document.getElementById('adminForm');
      if (adminForm) {
        adminForm.addEventListener('submit', function() {
          setComputedAge();
        });
      }
    }

    // Update modal logic
    var updateModal = document.getElementById('updateModal');
    var closeUpdateModalBtn = document.getElementById('closeUpdateModal');
    var cancelUpdateBtn = document.getElementById('cancelUpdate');
    function openUpdateModal(){ 
      updateModal.style.display = 'flex'; 
      // Ensure User Information tab is active when update modal opens
      document.querySelectorAll('#updateModal .tab-btn').forEach(function(tb) { tb.classList.remove('active'); });
      document.querySelectorAll('#updateModal .tab-content').forEach(function(tc) { tc.classList.remove('active'); });
      document.querySelector('#updateModal [data-tab="update-user"]').classList.add('active');
      document.getElementById('update-user-tab').classList.add('active');
    }
    
    // Add confirmation for Update Admin form
    var updateForm = document.getElementById('updateForm');
    if (updateForm) {
      updateForm.addEventListener('submit', function(e) {
        e.preventDefault();
        var firstName = document.getElementById('u_firstName').value;
        var lastName = document.getElementById('u_lastName').value;
        var email = document.getElementById('u_email').value;
        
        if (confirm('Are you sure you want to update ' + firstName + ' ' + lastName + ' (' + email + ')?')) {
          this.submit();
        }
      });
    }
    function closeUpdateModal(){ updateModal.style.display = 'none'; }
    if (closeUpdateModalBtn) closeUpdateModalBtn.addEventListener('click', closeUpdateModal);
    if (cancelUpdateBtn) cancelUpdateBtn.addEventListener('click', closeUpdateModal);

    // Switch tabs inside update modal
    document.querySelectorAll('#updateModal .tab-btn').forEach(function(btn){
      btn.addEventListener('click', function(){
        var target = this.getAttribute('data-tab');
        document.querySelectorAll('#updateModal .tab-btn').forEach(function(b){ b.classList.remove('active'); });
        document.querySelectorAll('#updateModal .tab-content').forEach(function(c){ c.classList.remove('active'); });
        this.classList.add('active');
        document.getElementById(target + '-tab').classList.add('active');
      });
    });

    // Hook update buttons
    document.querySelectorAll('[data-update]').forEach(function(btn){
      btn.addEventListener('click', function(e){
        e.stopPropagation();
        var row = this.closest('tr');
        var d = row ? row.dataset : {};
        // Pre-fill user fields
        document.getElementById('u_firstName').value = (d.name || '').split(' ')[0] || '';
        // data-* attributes map to camelCase on dataset. data-middle => d.middle
        document.getElementById('u_middleName').value = d.middle || '';
        document.getElementById('u_lastName').value = (d.name || '').split(' ').slice(-1)[0] || '';
        document.getElementById('u_email').value = d.email || '';
        document.getElementById('u_birthday').value = d.birthday || '';
        // Compute age for update modal
        (function(){
          var b = d.birthday ? new Date(d.birthday) : null;
          if (b && !isNaN(b.getTime())) {
            var t = new Date();
            var a = t.getFullYear() - b.getFullYear();
            var m = t.getMonth() - b.getMonth();
            if (m < 0 || (m === 0 && t.getDate() < b.getDate())) a--;
            document.getElementById('u_age').value = a > 0 ? a : '';
          } else {
            document.getElementById('u_age').value = d.age || '';
          }
        })();
        document.getElementById('u_sex').value = d.sex || '';
        document.getElementById('u_contactNumber').value = d.contact || '';
        // Address - Set province first, then populate cities, then select city
        var uProvinceSelect = document.getElementById('u_province');
        var uCitySelect = document.getElementById('u_city');
        var uZipInput = document.getElementById('u_zipcode');
        
        // Set province
        uProvinceSelect.value = d.province || '';
        
        // Populate cities for the selected province
        if (d.province && phAddressData[d.province]) {
          uCitySelect.innerHTML = '<option value="">Select City</option>';
          Object.keys(phAddressData[d.province]).forEach(function(city) {
            var option = document.createElement('option');
            option.value = city;
            option.textContent = city;
            uCitySelect.appendChild(option);
          });
          
          // Then set the specific city
          uCitySelect.value = d.city || '';
          
          // Set zip code
          if (d.city && phAddressData[d.province][d.city]) {
            uZipInput.value = phAddressData[d.province][d.city];
          } else {
            uZipInput.value = d.zipcode || '';
          }
        } else {
          // If no province data, just set the values directly
          uCitySelect.value = d.city || '';
          uZipInput.value = d.zipcode || '';
        }
        
        // Set street address
        document.getElementById('u_street').value = d.street || '';

        // Point form action to update route
        var updateForm = document.getElementById('updateForm');
        var userId = this.getAttribute('data-user-id');
        updateForm.setAttribute('action', '/adminPages/frontdeskrecords/update/' + userId);

        openUpdateModal();
      });
    });

    // User details modal functionality
    var userDetailsModal = document.getElementById('userDetailsModal');
    var closeUserDetailsBtn = document.getElementById('closeUserDetails');
    var closeUserDetailsModalBtn = document.getElementById('closeUserDetailsModal');
    
    function openUserDetailsModal() { userDetailsModal.style.display = 'flex'; }
    function closeUserDetailsModal() { userDetailsModal.style.display = 'none'; }
    
    if (closeUserDetailsBtn) closeUserDetailsBtn.addEventListener('click', closeUserDetailsModal);
    if (closeUserDetailsModalBtn) closeUserDetailsModalBtn.addEventListener('click', closeUserDetailsModal);

    // User row click handler (no network; populate from data-* attributes)
    var userRows = document.querySelectorAll('.user-row');
    userRows.forEach(function(row) {
      row.addEventListener('click', function(e) {
        // Don't trigger if clicking on action buttons
        if (e.target.closest('button')) return;
        var u = this.dataset;
        populateUserDetails({
          name: u.name,
          middleName: u.middle,
          email: u.email,
          birthday: u.birthday,
          age: u.age,
          sex: u.sex,
          contactNumber: u.contact,
          status: u.status,
          created_at: u.created,
          address: {
            street: u.street,
            city: u.city,
            province: u.province,
            zipcode: u.zipcode
          }
        });
        openUserDetailsModal();
      });
    });

    // Populate user details in modal
    function populateUserDetails(user) {
      document.getElementById('detail-name').textContent = user.name || '-';
      document.getElementById('detail-email').textContent = user.email || '-';
      document.getElementById('detail-birthday').textContent = user.birthday ? new Date(user.birthday).toLocaleDateString() : '-';
      document.getElementById('detail-age').textContent = user.age || '-';
      document.getElementById('detail-sex').textContent = user.sex || '-';
      document.getElementById('detail-contact').textContent = user.contactNumber || '-';
      document.getElementById('detail-status').textContent = user.status || '-';
      document.getElementById('detail-created').textContent = user.created_at ? new Date(user.created_at).toLocaleDateString() : '-';
      
      // Address details
      if (user.address) {
        document.getElementById('detail-street').textContent = user.address.street || '-';
        document.getElementById('detail-city').textContent = user.address.city || '-';
        document.getElementById('detail-province').textContent = user.address.province || '-';
        document.getElementById('detail-zipcode').textContent = user.address.zipcode || '-';
      } else {
        document.getElementById('detail-street').textContent = '-';
        document.getElementById('detail-city').textContent = '-';
        document.getElementById('detail-province').textContent = '-';
        document.getElementById('detail-zipcode').textContent = '-';
      }
    }

    // Philippine Address Data - All 81 Provinces
    var phAddressData = {
      "Abra": {
        "Bangued": "2800",
        "Boliney": "2801",
        "Bucay": "2802",
        "Bucloc": "2803",
        "Daguioman": "2804",
        "Danglas": "2805",
        "Dolores": "2806",
        "La Paz": "2807",
        "Lacub": "2808",
        "Lagangilang": "2809",
        "Lagayan": "2810",
        "Langiden": "2811",
        "Licuan-Baay": "2812",
        "Luba": "2813",
        "Malibcong": "2814",
        "Manabo": "2815",
        "Peñarrubia": "2816",
        "Pidigan": "2817",
        "Pilar": "2818",
        "Sallapadan": "2819",
        "San Isidro": "2820",
        "San Juan": "2821",
        "San Quintin": "2822",
        "Tayum": "2823",
        "Tineg": "2824",
        "Tubo": "2825",
        "Villaviciosa": "2826"
      },
      "Agusan del Norte": {
        "Butuan City": "8600",
        "Buenavista": "8601",
        "Carmen": "8602",
        "Jabonga": "8603",
        "Kitcharao": "8604",
        "Las Nieves": "8605",
        "Magallanes": "8606",
        "Nasipit": "8607",
        "Remedios T. Romualdez": "8608",
        "Santiago": "8609",
        "Tubay": "8610"
      },
      "Agusan del Sur": {
        "Bayugan City": "8502",
        "Bunawan": "8501",
        "Esperanza": "8503",
        "La Paz": "8504",
        "Loreto": "8505",
        "Prosperidad": "8506",
        "Rosario": "8507",
        "San Francisco": "8508",
        "San Luis": "8509",
        "Santa Josefa": "8510",
        "Sibagat": "8511",
        "Talacogon": "8512",
        "Trento": "8513",
        "Veruela": "8514"
      },
      "Aklan": {
        "Kalibo": "5600",
        "Altavas": "5601",
        "Balete": "5602",
        "Banga": "5603",
        "Batan": "5604",
        "Buruanga": "5605",
        "Ibajay": "5606",
        "Lezo": "5607",
        "Libacao": "5608",
        "Madalag": "5609",
        "Makato": "5610",
        "Malay": "5608",
        "Malinao": "5611",
        "Nabas": "5612",
        "New Washington": "5613",
        "Numancia": "5614",
        "Tangalan": "5615"
      },
      "Albay": {
        "Legazpi City": "4500",
        "Ligao City": "4504",
        "Tabaco City": "4511",
        "Bacacay": "4501",
        "Camalig": "4502",
        "Daraga": "4501",
        "Guinobatan": "4503",
        "Jovellar": "4505",
        "Libon": "4506",
        "Malilipot": "4507",
        "Malinao": "4508",
        "Manito": "4509",
        "Oas": "4510",
        "Pio Duran": "4512",
        "Polangui": "4513",
        "Rapu-Rapu": "4514",
        "Santo Domingo": "4515",
        "Tiwi": "4516"
      },
      "Antique": {
        "San Jose de Buenavista": "5700",
        "Anini-y": "5701",
        "Barbaza": "5702",
        "Belison": "5703",
        "Bugasong": "5704",
        "Caluya": "5705",
        "Culasi": "5706",
        "Hamtic": "5707",
        "Laua-an": "5708",
        "Libertad": "5709",
        "Pandan": "5710",
        "Patnongon": "5711",
        "San Remigio": "5712",
        "Sebaste": "5713",
        "Sibalom": "5714",
        "Tibiao": "5715",
        "Tobias Fornier": "5716",
        "Valderrama": "5717"
      },
      "Apayao": {
        "Kabugao": "3800",
        "Calanasan": "3801",
        "Conner": "3802",
        "Flora": "3803",
        "Luna": "3804",
        "Pudtol": "3805",
        "Santa Marcela": "3806"
      },
      "Aurora": {
        "Baler": "3200",
        "Casiguran": "3201",
        "Dilasag": "3202",
        "Dinalungan": "3203",
        "Dingalan": "3204",
        "Dipaculao": "3205",
        "Maria Aurora": "3206",
        "San Luis": "3207"
      },
      "Basilan": {
        "Isabela City": "7300",
        "Akbar": "7301",
        "Al-Barka": "7302",
        "Hadji Mohammad Ajul": "7303",
        "Hadji Muhtamad": "7304",
        "Lamitan City": "7305",
        "Lantawan": "7306",
        "Maluso": "7307",
        "Sumisip": "7308",
        "Tabuan-Lasa": "7309",
        "Tipo-Tipo": "7310",
        "Tuburan": "7311",
        "Ungkaya Pukan": "7312"
      },
      "Bataan": {
        "Balanga City": "2100",
        "Abucay": "2101",
        "Bagac": "2102",
        "Dinalupihan": "2103",
        "Hermosa": "2104",
        "Limay": "2105",
        "Mariveles": "2106",
        "Morong": "2107",
        "Orani": "2108",
        "Orion": "2109",
        "Pilar": "2110",
        "Samal": "2111"
      },
      "Batanes": {
        "Basco": "3900",
        "Itbayat": "3901",
        "Ivana": "3902",
        "Mahatao": "3903",
        "Sabtang": "3904",
        "Uyugan": "3905"
      },
      "Batangas": {
        "Batangas City": "4200",
        "Lipa City": "4217",
        "Tanauan City": "4232",
        "Santo Tomas City": "4234",
        "Agoncillo": "4201",
        "Alitagtag": "4202",
        "Balayan": "4203",
        "Balete": "4204",
        "Bauan": "4201",
        "Calaca": "4212",
        "Calatagan": "4215",
        "Cuenca": "4222",
        "Ibaan": "4230",
        "Laurel": "4221",
        "Lemery": "4209",
        "Lian": "4216",
        "Lobo": "4207",
        "Mabini": "4202",
        "Malvar": "4233",
        "Mataasnakahoy": "4223",
        "Nasugbu": "4231",
        "Padre Garcia": "4224",
        "Rosario": "4225",
        "San Jose": "4226",
        "San Luis": "4227",
        "San Nicolas": "4211",
        "San Pascual": "4228",
        "Santa Teresita": "4229",
        "Taal": "4208",
        "Talisay": "4210",
        "Taysan": "4214",
        "Tingloy": "4213"
      },
      "Benguet": {
        "Baguio City": "2600",
        "Atok": "2601",
        "Bakun": "2602",
        "Bokod": "2603",
        "Buguias": "2604",
        "Itogon": "2605",
        "Kabayan": "2606",
        "Kapangan": "2607",
        "Kibungan": "2608",
        "La Trinidad": "2601",
        "Mankayan": "2610",
        "Sablan": "2611",
        "Tuba": "2612",
        "Tublay": "2613"
      }
    };

    // Initialize province dropdowns
    function initializeProvinceDropdowns() {
      var provinceSelects = ['province', 'u_province'];
      provinceSelects.forEach(function(selectId) {
        var select = document.getElementById(selectId);
        if (select) {
          // Clear existing options except first
          select.innerHTML = '<option value="">Select Province</option>';
          
          // Add provinces
          Object.keys(phAddressData).forEach(function(province) {
            var option = document.createElement('option');
            option.value = province;
            option.textContent = province;
            select.appendChild(option);
          });
        }
      });
    }

    // Update city dropdown based on selected province
    function updateCityDropdown(provinceSelectId, citySelectId) {
      var provinceSelect = document.getElementById(provinceSelectId);
      var citySelect = document.getElementById(citySelectId);
      
      if (provinceSelect && citySelect) {
        provinceSelect.addEventListener('change', function() {
          var selectedProvince = this.value;
          
          // Clear city options
          citySelect.innerHTML = '<option value="">Select City</option>';
          
          if (selectedProvince && phAddressData[selectedProvince]) {
            // Add cities for selected province
            Object.keys(phAddressData[selectedProvince]).forEach(function(city) {
              var option = document.createElement('option');
              option.value = city;
              option.textContent = city;
              citySelect.appendChild(option);
            });
          }
          
          // Clear zip code
          var zipInput = document.getElementById(citySelectId.replace('city', 'zipcode'));
          if (zipInput) zipInput.value = '';
        });
      }
    }

    // Update zip code based on selected city
    function updateZipCode(citySelectId, zipInputId) {
      var citySelect = document.getElementById(citySelectId);
      var zipInput = document.getElementById(zipInputId);
      
      if (citySelect && zipInput) {
        citySelect.addEventListener('change', function() {
          var selectedCity = this.value;
          var provinceSelect = document.getElementById(citySelectId.replace('city', 'province'));
          
          if (selectedCity && provinceSelect && phAddressData[provinceSelect.value]) {
            zipInput.value = phAddressData[provinceSelect.value][selectedCity] || '';
          } else {
            zipInput.value = '';
          }
        });
      }
    }

    // Initialize all address functionality
    function initializeAddressDropdowns() {
      initializeProvinceDropdowns();
      updateCityDropdown('province', 'city');
      updateCityDropdown('u_province', 'u_city');
      updateZipCode('city', 'zipcode');
      updateZipCode('u_city', 'u_zipcode');
    }

    // Merge additional address data if available
    if (typeof window.additionalPhAddressData !== 'undefined') {
      Object.assign(phAddressData, window.additionalPhAddressData);
    }

    // Call initialization when page loads
    initializeAddressDropdowns();

    // Archive functionality
    document.querySelectorAll('[data-archive]').forEach(function(btn){
      btn.addEventListener('click', function(e){
        e.stopPropagation();
        var userId = this.getAttribute('data-user-id');
        var userName = this.closest('tr').querySelector('.user-name').textContent;
        
        if (confirm('Are you sure you want to archive ' + userName + '?')) {
          // Create form for DELETE request
          var form = document.createElement('form');
          form.method = 'POST';
          form.action = '/adminPages/frontdeskrecords/delete/' + userId;
          
          // Add CSRF token
          var csrfToken = document.createElement('input');
          csrfToken.type = 'hidden';
          csrfToken.name = '_token';
          csrfToken.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                           document.querySelector('input[name="_token"]')?.value;
          
          // Add method override for DELETE
          var methodField = document.createElement('input');
          methodField.type = 'hidden';
          methodField.name = '_method';
          methodField.value = 'DELETE';
          
          form.appendChild(csrfToken);
          form.appendChild(methodField);
          document.body.appendChild(form);
          form.submit();
        }
      });
    });
  })();
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


