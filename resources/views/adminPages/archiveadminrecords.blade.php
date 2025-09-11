@extends('layouts.admindashboard')

@section('title','Archived Admin Records')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/archiveadminrecords.css') }}">
@endpush

@section('content')
<div class="dashboard-page">
  <div class="page-header">
    <h1 class="page-title">Archived Admin Records</h1>
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
      <input id="archiveSearch" type="text" placeholder="Search archived admins" class="search-input">
    </div>
    <div class="toolbar-actions">
      <a href="{{ route('adminPages.adminrecords') }}" class="archive-btn">
        <i class="fas fa-arrow-left"></i> Back to Records
      </a>
    </div>
  </div>

  <div class="chart-card card-tight">
    <div class="section-header-pad">
      <h3 class="chart-title">List</h3>
    </div>

    <div class="table-wrapper">
      <table class="table sortable-table" id="archivedTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Archived Date</th>
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
                  data-status="{{ $user->status ?? 'Archived' }}"
                  data-created="{{ $user->created_at }}"
                  data-archived="{{ $user->deleted_at }}"
                  data-street="{{ optional($user->address)->street }}"
                  data-city="{{ optional($user->address)->city }}"
                  data-province="{{ optional($user->address)->province }}"
                  data-zipcode="{{ optional($user->address)->zipcode }}">
                <td data-label="ID">{{ $user->id }}</td>
                <td data-label="Name" class="user-name">{{ $user->name }}</td>
                <td data-label="Email">{{ $user->email }}</td>
                <td data-label="Archived Date">{{ optional($user->deleted_at)->format('M d, Y') }}</td>
                <td data-label="Actions">
                  <button class="action-btn small" data-restore> 
                    <i class="fas fa-undo"></i> 
                  </button>
                </td>
              </tr>
            @endforeach
          @else
            <tr>
              <td colspan="5" class="text-center">No archived admin records found</td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>
    
    @if(isset($users) && $users->hasPages())
      <nav class="pagination" aria-label="Table pagination">
        {{ $users->links() }}
      </nav>
    @endif
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
          <div class="info-item">
            <label>Date Archived:</label>
            <span id="detail-archived">-</span>
          </div>
        </div>
      </div>
    </div>
    
    <div class="modal-actions">
      <button type="button" id="closeUserDetails" class="action-btn btn-outline">Close</button>
    </div>
  </div>
</div>

<script>
  (function(){
    var search = document.getElementById('archiveSearch');
    var table = document.getElementById('archivedTable').getElementsByTagName('tbody')[0];

    // Search functionality
    if (search) search.addEventListener('input', function(){
      var q = this.value.toLowerCase();
      Array.prototype.forEach.call(table.rows, function(row){
        var text = row.innerText.toLowerCase();
        row.style.display = text.indexOf(q) !== -1 ? '' : 'none';
      });
    });

    // Checkbox/restoreSelected UI removed

    // User details modal functionality
    var userDetailsModal = document.getElementById('userDetailsModal');
    var closeUserDetailsBtn = document.getElementById('closeUserDetails');
    var closeUserDetailsModalBtn = document.getElementById('closeUserDetailsModal');
    
    function openUserDetailsModal() { 
      console.log('Opening modal...');
      userDetailsModal.style.display = 'flex'; 
    }
    function closeUserDetailsModal() { 
      console.log('Closing modal...');
      userDetailsModal.style.display = 'none'; 
    }
    
    if (closeUserDetailsBtn) closeUserDetailsBtn.addEventListener('click', closeUserDetailsModal);
    if (closeUserDetailsModalBtn) closeUserDetailsModalBtn.addEventListener('click', closeUserDetailsModal);

    // User row click handler (no network; populate from data-* attributes)
    var userRows = document.querySelectorAll('.user-row');
    console.log('Found user rows:', userRows.length);
    userRows.forEach(function(row) {
      row.addEventListener('click', function(e) {
        console.log('Row clicked!');
        // Don't trigger if clicking on action buttons
        if (e.target.closest('button')) {
          console.log('Clicked on button, ignoring...');
          return;
        }
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
          archived_at: u.archived,
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
      document.getElementById('detail-archived').textContent = user.archived_at ? new Date(user.archived_at).toLocaleDateString() : '-';
      
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

    // Restore functionality
    document.querySelectorAll('[data-restore]').forEach(function(btn){
      btn.addEventListener('click', function(e){
        e.stopPropagation();
        var row = this.closest('tr');
        var userId = row.querySelector('td[data-label="ID"]').textContent;
        var userName = row.querySelector('td[data-label="Name"]').textContent;
        
        if (confirm('Are you sure you want to restore ' + userName + '? They will regain access to the system.')) {
          // Create form for restore request
          var form = document.createElement('form');
          form.method = 'POST';
          form.action = '/adminPages/adminrecords/restore/' + userId;
          
          // Add CSRF token
          var csrfToken = document.createElement('input');
          csrfToken.type = 'hidden';
          csrfToken.name = '_token';
          csrfToken.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                           document.querySelector('input[name="_token"]')?.value;
          
          // Add method override for PATCH
          var methodField = document.createElement('input');
          methodField.type = 'hidden';
          methodField.name = '_method';
          methodField.value = 'PATCH';
          
          form.appendChild(csrfToken);
          form.appendChild(methodField);
          document.body.appendChild(form);
          form.submit();
        }
      });
    });

    // Restore selected UI removed
  })();
</script>
@endsection
