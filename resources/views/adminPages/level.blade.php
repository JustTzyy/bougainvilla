@extends('layouts.admindashboard')

@section('title','Levels')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/adminrecords.css') }}">
<script src="{{ asset('js/ph-complete-address.js') }}"></script>
@endpush

@section('content')
<div class="dashboard-page">
  <div class="page-header">
    <h1 class="page-title">Levels</h1>
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
      <input id="adminSearch" type="text" placeholder="Search levels" class="search-input">
    </div>
    <div class="toolbar-actions">
      <a href="{{ route('levels.archive') }}" class="archive-btn">
        <i class="fas fa-archive"></i> Archive
      </a>
      <button id="openAddAdmin"><i class="fas fa-layer-group"></i> Add Level</button>
    </div>
  </div>

  <div class="chart-card card-tight">
    <div class="section-header-pad">
      <h3 class="chart-title">List</h3>
    </div>

    <div class="table-wrapper">
      <table class="table sortable-table" id="levelsTable">
        <thead>
          <tr>
            <th>Floor No.</th>
            <th>Description</th>
            <th>Status</th>
            <th>Date Created</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @if(isset($levels) && $levels->count() > 0)
            @foreach($levels as $level)
              <tr class="level-row"
                  data-level-id="{{ $level->id }}"
                  data-description="{{ $level->description }}"
                  data-status="{{ $level->status }}"
                  data-created="{{ $level->created_at }}">
                <td data-label="Floor No.">{{ $level->id }}</td>
                <td data-label="Description" class="level-description">{{ $level->description }}</td>
                <td data-label="Status">{{ $level->status }}</td>
                <td data-label="Date Created">{{ $level->created_at->format('M d, Y') }}</td>
                <td data-label="Actions">
                  <button class="action-btn small" data-update data-level-id="{{ $level->id }}">
                    <i class="fas fa-pen"></i>
                  </button>
                  <button class="action-btn small" data-archive data-level-id="{{ $level->id }}">
                    <i class="fas fa-archive"></i>
                  </button>
                </td>
              </tr>
            @endforeach
          @else
            <tr>
              <td colspan="5" class="text-center">No levels found</td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>
    @if(isset($levels) && $levels->hasPages())
      <nav class="pagination" aria-label="Table pagination">
        {{ $levels->links() }}
      </nav>
    @endif
  </div>
</div>

<!-- Add/Edit Modal -->
<div id="levelModal" class="modal">
  <div class="modal-card">
    <div class="modal-header">
      <h3 class="chart-title" id="modalTitle">Add Level</h3>
      <button id="closeLevelModal" class="action-btn ml-auto"><i class="fas fa-times"></i></button>
    </div>
    <form id="levelForm" action="{{ route('adminPages.levels.post') }}" class="modal-form"  method="POST">
      @csrf
      <div class="form-grid">
        <div class="form-group span-2">
          <label>Description</label>
          <input name="description" class="form-input" placeholder="e.g., Ground Floor" required>
        </div>
        <div class="form-group">
          <label>Status</label>
          <input class="form-input" value="Active" readonly>
        </div>
      </div>
      <div class="modal-actions">
        <button type="button" id="cancelLevel" class="action-btn btn-outline">Cancel</button>
        <button type="submit" class="btn-primary inline">Save Level</button>
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

<!-- Update Level Modal -->
<div id="updateModal" class="modal">
  <div class="modal-card">
    <div class="modal-header">
      <h3 class="chart-title">Update Level</h3>
      <button id="closeUpdateModal" class="action-btn ml-auto"><i class="fas fa-times"></i></button>
    </div>

    <form id="updateForm" class="modal-form" method="POST">
      @csrf
      @method('POST')
      <div class="form-grid">
        <div class="form-group span-2">
          <label>Description</label>
          <input name="description" id="u_description" class="form-input" required>
        </div>
        <div class="form-group">
          <label>Status</label>
          <select name="status" id="u_status" class="form-input" required>
            <option value="">Select Status</option>
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
          </select>
        </div>
      </div>

      <div class="modal-actions">
        <button type="button" id="cancelUpdate" class="action-btn btn-outline">Cancel</button>
        <button type="submit" class="btn-primary inline">Update Level</button>
      </div>
    </form>
  </div>
</div>
<script>
  (function(){
    var modal = document.getElementById('levelModal');
    var openBtn = document.getElementById('openAddAdmin');
    var closeBtn = document.getElementById('closeLevelModal');
    var cancelBtn = document.getElementById('cancelLevel');
    var search = document.getElementById('adminSearch');
    var table = document.getElementById('levelsTable').getElementsByTagName('tbody')[0];

    function openModal(){ 
      modal.style.display = 'flex'; 
    }
    
    // Add confirmation for Add Level form (mirrors admin add modal)
    var levelForm = document.getElementById('levelForm');
    if (levelForm) {
      levelForm.addEventListener('submit', function(e) {
        e.preventDefault();
        var description = document.querySelector('input[name="description"]').value;
        if (confirm('Are you sure you want to add Level "' + description + '" (status: Active)?')) {
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

    // Remove user-specific inputs

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
    
    // Add confirmation for Update Level form
    var updateForm = document.getElementById('updateForm');
    if (updateForm) {
      updateForm.addEventListener('submit', function(e) {
        e.preventDefault();
        var description = document.getElementById('u_description').value;
        var status = document.getElementById('u_status').value;
        if (confirm('Update level to "' + description + '" with status ' + status + '?')) {
          this.submit();
        }
      });
    }
    function closeUpdateModal(){ updateModal.style.display = 'none'; }
    if (closeUpdateModalBtn) closeUpdateModalBtn.addEventListener('click', closeUpdateModal);
    if (cancelUpdateBtn) cancelUpdateBtn.addEventListener('click', closeUpdateModal);

    // Hook update buttons
    document.querySelectorAll('[data-update]').forEach(function(btn){
      btn.addEventListener('click', function(e){
        e.stopPropagation();
        var row = this.closest('tr');
        var d = row ? row.dataset : {};
        // Pre-fill level fields
        document.getElementById('u_description').value = d.description || '';
        document.getElementById('u_status').value = d.status || '';

        // Point form action to update route
        var updateForm = document.getElementById('updateForm');
        var levelId = this.getAttribute('data-level-id');
        updateForm.setAttribute('action', '/adminPages/levels/update/' + levelId);

        openUpdateModal();
      });
    });

    // Remove user details modal logic (not needed for levels)


    // No address dropdowns for levels

    // Archive functionality
    document.querySelectorAll('[data-archive]').forEach(function(btn){
      btn.addEventListener('click', function(e){
        e.stopPropagation();
        var userId = this.getAttribute('data-level-id');
        var userName = this.closest('tr').querySelector('.level-description').textContent;
        
        if (confirm('Are you sure you want to archive ' + userName + '?')) {
          // Create form for DELETE request
          var form = document.createElement('form');
          form.method = 'POST';
          form.action = '/adminPages/levels/delete/' + userId;
          
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
@endsection


