@extends('layouts.admindashboard')

@section('title','Archived Admin Records')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/archiveadminrecords.css') }}">
<style>
  /* Enhanced Archive User Details Modal Styles */
  #userDetailsModal .modal-card {
    max-width: 720px;
    border-radius: 14px;
    box-shadow: 0 16px 36px rgba(0,0,0,.22);
    background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
    overflow: hidden;
    position: relative;
  }

  #userDetailsModal .modal-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--purple-primary), #DAA520, #F4D03F);
  }

  /* User Details Modal Header */
  #userDetailsModal .modal-header {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
    border-bottom: 1px solid rgba(184,134,11,.15);
    padding: 12px 16px;
    position: relative;
  }

  #userDetailsModal .modal-header h3 {
    color: var(--purple-primary);
    font-size: 18px;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  #userDetailsModal .modal-header h3::before {
    content: '\1F464';
    font-size: 18px;
    background: linear-gradient(135deg, var(--purple-primary), #DAA520);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  /* User Details Content */
  #userDetailsModal .user-details-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    padding: 16px;
    background: #ffffff;
  }

  /* Info Sections */
  #userDetailsModal .user-info-section,
  #userDetailsModal .address-info-section {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
    border-radius: 10px;
    padding: 12px;
    border: 1px solid rgba(184,134,11,.1);
    box-shadow: 0 4px 12px rgba(184,134,11,.08);
    position: relative;
    overflow: hidden;
  }

  #userDetailsModal .user-info-section::before,
  #userDetailsModal .address-info-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--purple-primary), #DAA520);
  }

  /* Section Headers */
  #userDetailsModal .user-info-section h4,
  #userDetailsModal .address-info-section h4 {
    color: var(--purple-primary);
    font-size: 14px;
    font-weight: 700;
    margin: 0 0 12px 0;
    display: flex;
    align-items: center;
    gap: 6px;
    padding-bottom: 8px;
    border-bottom: 2px solid rgba(184,134,11,.15);
  }

  #userDetailsModal .user-info-section h4 i {
    background: linear-gradient(135deg, var(--purple-primary), #DAA520);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-size: 14px;
  }

  #userDetailsModal .address-info-section h4 i {
    background: linear-gradient(135deg, #e17055, #fd79a8);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-size: 14px;
  }

  /* Info Grid */
  #userDetailsModal .info-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 8px;
  }

  #userDetailsModal .info-item {
    display: flex;
    flex-direction: column;
    gap: 3px;
    padding: 8px;
    background: rgba(255,255,255,.7);
    border-radius: 8px;
    border: 1px solid rgba(184,134,11,.08);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
  }

  #userDetailsModal .info-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(180deg, var(--purple-primary), #DAA520);
    opacity: 0;
    transition: opacity 0.3s ease;
  }

  #userDetailsModal .info-item:hover {
    background: rgba(255,255,255,.9);
    border-color: rgba(184,134,11,.2);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(184,134,11,.12);
  }

  #userDetailsModal .info-item:hover::before {
    opacity: 1;
  }

  #userDetailsModal .info-item.span-2 {
    grid-column: span 2;
  }

  /* Info Labels */
  #userDetailsModal .info-item label {
    font-weight: 600;
    color: #6c757d;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 3px;
  }

  #userDetailsModal .info-item label::before {
    content: '';
    width: 4px;
    height: 4px;
    border-radius: 50%;
    background: var(--purple-primary);
    opacity: 0.6;
  }

  /* Info Values */
  #userDetailsModal .info-item span {
    color: var(--text-primary);
    font-size: 13px;
    font-weight: 500;
    line-height: 1.3;
    word-break: break-word;
  }

  /* Status Badge */
  #userDetailsModal .status-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 8px;
    border-radius: 16px;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    max-width: fit-content;
  }

  #userDetailsModal .status-badge:not([class*="status-"]) {
    background: linear-gradient(135deg, #00b894, #00cec9);
    color: white;
    box-shadow: 0 2px 8px rgba(0,184,148,.3);
  }

  #userDetailsModal .status-badge::before {
    content: '●';
    font-size: 6px;
  }

  /* Modal Actions */
  #userDetailsModal .modal-actions {
    padding: 12px 16px;
    background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
    border-top: 1px solid rgba(184,134,11,.15);
    display: flex;
    justify-content: flex-end;
  }

  #userDetailsModal .modal-actions .action-btn.btn-outline {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 16px;
    border-radius: 20px;
    background: linear-gradient(135deg, #ffffff, #f8f9ff);
    color: var(--purple-primary);
    border: 2px solid var(--purple-primary);
    font-weight: 700;
    letter-spacing: 0.3px;
    box-shadow: 0 4px 12px rgba(184,134,11,.2);
    transition: all 0.3s ease;
    text-transform: uppercase;
    font-size: 12px;
  }

  #userDetailsModal .modal-actions .action-btn.btn-outline:hover {
    background: linear-gradient(135deg, var(--purple-primary), #DAA520);
    color: #fff;
    border-color: var(--purple-primary);
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(184,134,11,.35);
  }

  #userDetailsModal .modal-actions .action-btn.btn-outline:active {
    transform: translateY(0);
    box-shadow: 0 2px 8px rgba(184,134,11,.3);
  }

  #userDetailsModal .modal-actions .action-btn.btn-outline::before {
    content: '✕';
    font-size: 12px;
    font-weight: bold;
  }

  /* Responsive Design for User Details Modal */
  @media (max-width: 900px) {
    #userDetailsModal .modal-card {
      max-width: 95vw;
      margin: 20px;
    }
    
    #userDetailsModal .user-details-content {
      grid-template-columns: 1fr;
      gap: 12px;
      padding: 12px;
    }
    
    #userDetailsModal .modal-header {
      padding: 12px 16px;
    }
    
    #userDetailsModal .modal-header h3 {
      font-size: 16px;
    }
  }

  @media (max-width: 600px) {
    #userDetailsModal .user-details-content {
      padding: 8px;
      gap: 12px;
    }
    
    #userDetailsModal .user-info-section,
    #userDetailsModal .address-info-section {
      padding: 12px;
    }
    
    #userDetailsModal .info-item {
      padding: 6px;
    }
    
    #userDetailsModal .modal-header {
      padding: 10px 12px;
    }
    
    #userDetailsModal .modal-header h3 {
      font-size: 14px;
    }
    
    #userDetailsModal .info-item label {
      font-size: 10px;
    }
    
    #userDetailsModal .info-item span {
      font-size: 12px;
    }
  }
</style>
@endpush

@section('content')
<div class="dashboard-page">
  <div class="page-header">
    <h1 class="page-title">Archived Front Desk Records</h1>
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
      <a href="{{ route('adminPages.frontdeskrecords') }}" class="archive-btn">
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
            @foreach ($accommodations->getUrlRange(1, $accommodations->lastPage()) as $page => $url)
                @if ($page == $accommodations->currentPage())
                    <li class="page-item active"><span>{{ $page }}</span></li>
                @else
                    <li class="page-item"><a href="{{ $url }}">{{ $page }}</a></li>
                @endif
            @endforeach

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
