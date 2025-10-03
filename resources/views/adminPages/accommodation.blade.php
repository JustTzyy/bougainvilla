@extends('layouts.admindashboard')

@section('title','Accommodations')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/adminrecords.css') }}">
<script src="{{ asset('js/ph-complete-address.js') }}"></script>
<style>
  /* Enhanced Accommodation Details Modal Styles */
  #accommodationDetailsModal .modal-card {
    max-width: 600px;
    border-radius: 16px;
    box-shadow: 0 15px 40px rgba(184,134,11,.15);
    background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
    border: 1px solid rgba(184,134,11,.1);
    overflow: hidden;
  }

  #accommodationDetailsModal .info-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(184,134,11,.15);
    background: rgba(184,134,11,.08);
  }

  #accommodationDetailsModal .user-info-section:hover,
  #accommodationDetailsModal .address-info-section:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 30px rgba(184,134,11,.12);
  }

  #accommodationDetailsModal .action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(184,134,11,.25);
    background: linear-gradient(135deg, var(--purple-primary), #DAA520) !important;
    color: white !important;
  }

  /* Room and Rate cards enhanced hover effects */
  #detail-rooms-list .room-card:hover,
  #detail-rates-list .rate-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 30px rgba(184,134,11,.2);
    background: linear-gradient(135deg, rgba(184,134,11,.08), rgba(184,134,11,.04)) !important;
  }

  /* Loading animation enhancement */
  @keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
  }

  #detail-rooms-list .loading,
  #detail-rates-list .loading {
    animation: pulse 1.5s ease-in-out infinite;
  }

  /* Responsive adjustments */
  @media (max-width: 768px) {
    #accommodationDetailsModal .modal-card {
      max-width: 95%;
      margin: 20px;
    }
    
    #accommodationDetailsModal .info-grid {
      grid-template-columns: 1fr !important;
    }
  }

  /* Pagination styling for accommodations */
  #pagination { display: flex; justify-content: center; margin-top: 12px; }
  #pagination ul.pagination { display: flex; gap: 6px; list-style: none; padding: 0; margin: 0; }
  #pagination .page-link {
    background: linear-gradient(135deg, #ffffff, #f8f9ff);
    border: 1px solid rgba(184,134,11,.2);
    color: var(--text-primary);
    padding: 8px 12px;
    border-radius: 10px;
    font-weight: 700;
    box-shadow: 0 3px 10px rgba(184,134,11,.08);
    transition: all .2s ease;
  }
  #pagination .page-link:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(184,134,11,.15); border-color: rgba(184,134,11,.35); }
  #pagination li.active .page-link {
    background: linear-gradient(135deg, var(--purple-primary), #DAA520);
    color: #fff;
    border-color: transparent;
    box-shadow: 0 8px 22px rgba(184,134,11,.35);
  }
  #pagination .page-link.disabled { opacity: .5; cursor: not-allowed; }
</style>
@endpush

@section('content')
<div class="dashboard-page">
  <div class="page-header">
    <h1 class="page-title">Accommodations</h1>
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
      <input id="adminSearch" type="text" placeholder="Search accommodations" class="search-input">
    </div>
    <div class="toolbar-actions">
      <a href="{{ route('accommodations.archive') }}" class="archive-btn">
        <i class="fas fa-archive"></i> Archive
      </a>
      <button id="openAddAdmin"><i class="fas fa-hotel"></i> Add Accommodation</button>
    </div>
  </div>

  <div class="chart-card card-tight">
    <div class="section-header-pad">
      <h3 class="chart-title">List</h3>
    </div>

    <div class="table-wrapper">
      <table class="table sortable-table" id="accommodationsTable">
        <thead>
          <tr>
            <th>Name</th>
            <th>Capacity</th>
            <th>Description</th>
            <th>Date Created</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @if(isset($accommodations) && $accommodations->count() > 0)
            @foreach($accommodations as $accommodation)
              <tr class="accommodation-row"
                  data-accommodation-id="{{ $accommodation->id }}"
                  data-name="{{ $accommodation->name }}"
                  data-capacity="{{ $accommodation->capacity }}"
                  data-description="{{ $accommodation->description }}"
                  data-created="{{ $accommodation->created_at }}">
                <td data-label="Name">{{ $accommodation->name }}</td>
                <td data-label="Capacity">{{ $accommodation->capacity }}</td>
                <td data-label="Description" class="accommodation-description">{{ $accommodation->description }}</td>
                <td data-label="Date Created">{{ $accommodation->created_at->format('M d, Y') }}</td>
                <td data-label="Actions">
                  <button class="action-btn small" data-update data-accommodation-id="{{ $accommodation->id }}">
                    <i class="fas fa-pen"></i>
                  </button>
                  <button class="action-btn small" data-archive data-accommodation-id="{{ $accommodation->id }}">
                    <i class="fas fa-archive"></i>
                  </button>
                </td>
              </tr>
            @endforeach
          @else
            <tr>
              <td colspan="5" class="text-center">No accommodations found</td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>
    <nav class="pagination no-print" aria-label="Table pagination" id="pagination" style="display:none;"></nav>
  </div>
</div>

<!-- Add Accommodation Modal -->
<div id="accommodationModal" class="modal accommodation-modal">
  <div class="modal-card">
    <div class="modal-header">
      <h3 class="chart-title">Add Accommodation</h3>
      <button id="closeAccommodationModal" class="action-btn ml-auto">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <form id="accommodationForm" action="{{ route('adminPages.accommodations.post') }}" class="modal-form" method="POST">
      @csrf
      <div class="form-grid">
        <div class="form-group">
          <label>Name</label>
          <input type="text" name="name" class="form-input" placeholder="e.g., Deluxe Room" required>
        </div>
        <div class="form-group">
          <label>Capacity</label>
          <input type="number" name="capacity" class="form-input" min="1" required>
        </div>
        <div class="form-group span-2">
          <label>Description</label>
          <textarea name="description" class="form-input" placeholder="Accommodation description"></textarea>
        </div>
      </div>

      <div class="modal-actions">
        <button type="button" id="cancelAccommodation" class="action-btn btn-outline">Cancel</button>
        <button type="submit" class="btn-primary inline">Save Accommodation</button>
      </div>
    </form>
  </div>
</div>

<!-- Update Accommodation Modal -->
<div id="updateModal" class="modal accommodation-modal">
  <div class="modal-card">
    <div class="modal-header">
      <h3 class="chart-title">Update Accommodation</h3>
      <button id="closeUpdateModal" class="action-btn ml-auto">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <form id="updateForm" class="modal-form" method="POST">
      @csrf
      @method('POST')
      <div class="form-grid">
        <div class="form-group">
          <label>Name</label>
          <input name="name" id="u_name" class="form-input" required>
        </div>
        <div class="form-group">
          <label>Capacity</label>
          <input type="number" name="capacity" id="u_capacity" class="form-input" min="1" required>
        </div>
        <div class="form-group span-2">
          <label>Description</label>
          <textarea name="description" id="u_description" class="form-input"></textarea>
        </div>
      </div>

      <div class="modal-actions">
        <button type="button" id="cancelUpdate" class="action-btn btn-outline">Cancel</button>
        <button type="submit" class="btn-primary inline">Update Accommodation</button>
      </div>
    </form>
  </div>
</div>

<!-- Accommodation Details Modal -->
<div id="accommodationDetailsModal" class="modal">
  <div class="modal-card user-details-card">
    <div class="modal-header" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); border-bottom: 1px solid rgba(184,134,11,.15);">
      <h3 class="chart-title" style="color: var(--purple-primary); font-size: 18px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px;">
        <i class="fas fa-hotel" style="background: linear-gradient(135deg, var(--purple-primary), #DAA520); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-size: 20px;"></i>
        Accommodation Details
      </h3>
      <button id="closeAccommodationDetailsModal" class="action-btn ml-auto"><i class="fas fa-times"></i></button>
    </div>
    
    <div class="user-details-content" style="padding: 8px; background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);">
      <!-- Accommodation Information Section -->
      <div class="user-info-section" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); border-radius: 10px; padding: 8px; margin-bottom: 8px; box-shadow: 0 2px 12px rgba(184,134,11,.08); border: 1px solid rgba(184,134,11,.1);">
        <h4 style="color: var(--purple-primary); font-size: 14px; font-weight: 700; margin: 0 0 8px 0; display: flex; align-items: center; gap: 6px; padding-bottom: 6px; border-bottom: 1px solid rgba(184,134,11,.15);">
          <i class="fas fa-hotel" style="background: linear-gradient(135deg, var(--purple-primary), #DAA520); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-size: 14px;"></i>
          Accommodation Information
        </h4>
        <div class="info-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 8px;">
          <div class="info-item" style="background: rgba(184,134,11,.05); padding: 8px; border-radius: 8px; border-left: 3px solid var(--purple-primary); transition: all 0.3s ease;">
            <label style="display: block; font-size: 10px; font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 2px;">
              <i class="fas fa-tag" style="margin-right: 4px; color: var(--purple-primary); font-size: 10px;"></i>Name
            </label> 
            <span id="detail-name" style="font-size: 14px; font-weight: 700; color: var(--text-primary);">-</span>
          </div>
          <div class="info-item" style="background: rgba(184,134,11,.05); padding: 8px; border-radius: 8px; border-left: 3px solid var(--purple-primary); transition: all 0.3s ease;">
            <label style="display: block; font-size: 10px; font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 2px;">
              <i class="fas fa-users" style="margin-right: 4px; color: var(--purple-primary); font-size: 10px;"></i>Capacity
            </label>
            <span id="detail-capacity" style="font-size: 14px; font-weight: 700; color: var(--text-primary);">-</span>
          </div>
          <div class="info-item" style="background: rgba(184,134,11,.05); padding: 8px; border-radius: 8px; border-left: 3px solid var(--purple-primary); transition: all 0.3s ease;">
            <label style="display: block; font-size: 10px; font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 2px;">
              <i class="fas fa-calendar-plus" style="margin-right: 4px; color: var(--purple-primary); font-size: 10px;"></i>Date Created
            </label>
            <span id="detail-created" style="font-size: 14px; font-weight: 700; color: var(--text-primary);">-</span>
          </div>
        </div>
        <div class="info-item" style="background: rgba(184,134,11,.05); padding: 8px; border-radius: 8px; border-left: 3px solid var(--purple-primary); transition: all 0.3s ease; margin-top: 8px;">
          <label style="display: block; font-size: 10px; font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 2px;">
            <i class="fas fa-align-left" style="margin-right: 4px; color: var(--purple-primary); font-size: 10px;"></i>Description
          </label>
          <span id="detail-description" style="font-size: 14px; font-weight: 700; color: var(--text-primary);">-</span>
        </div>
      </div>
      
      <!-- Rooms Information Section -->
      <div class="address-info-section" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); border-radius: 10px; padding: 8px; margin-bottom: 8px; box-shadow: 0 2px 12px rgba(184,134,11,.08); border: 1px solid rgba(184,134,11,.1);">
        <h4 style="color: var(--purple-primary); font-size: 12px; font-weight: 700; margin: 0 0 6px 0; display: flex; align-items: center; gap: 4px; padding-bottom: 4px; border-bottom: 1px solid rgba(184,134,11,.15);">
          <i class="fas fa-door-open" style="background: linear-gradient(135deg, var(--purple-primary), #DAA520); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-size: 12px;"></i>
          Rooms
        </h4>
        <div class="info-grid">
          <div class="info-item span-2" style="grid-column: span 2;">
            <div id="detail-rooms-list" style="background: rgba(184,134,11,.03); border-radius: 6px; padding: 6px; min-height: 50px; border: 1px dashed rgba(184,134,11,.2);">
              <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #6c757d; font-style: italic; font-size: 10px;">
                <i class="fas fa-spinner fa-spin" style="margin-right: 4px; color: var(--purple-primary); font-size: 10px;"></i>
                Loading...
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Rates Information Section -->
      <div class="address-info-section" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); border-radius: 10px; padding: 8px; box-shadow: 0 2px 12px rgba(184,134,11,.08); border: 1px solid rgba(184,134,11,.1);">
        <h4 style="color: var(--purple-primary); font-size: 12px; font-weight: 700; margin: 0 0 6px 0; display: flex; align-items: center; gap: 4px; padding-bottom: 4px; border-bottom: 1px solid rgba(184,134,11,.15);">
          <i class="fas fa-dollar-sign" style="background: linear-gradient(135deg, var(--purple-primary), #DAA520); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-size: 12px;"></i>
          Rates
        </h4>
        <div class="info-grid">
          <div class="info-item span-2" style="grid-column: span 2;">
            <div id="detail-rates-list" style="background: rgba(184,134,11,.03); border-radius: 6px; padding: 6px; min-height: 50px; border: 1px dashed rgba(184,134,11,.2);">
              <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #6c757d; font-style: italic; font-size: 10px;">
                <i class="fas fa-spinner fa-spin" style="margin-right: 4px; color: var(--purple-primary); font-size: 10px;"></i>
                Loading...
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="modal-actions" style="padding: 8px; background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%); border-top: 1px solid rgba(184,134,11,.15); border-radius: 0 0 16px 16px;">
      <button type="button" id="closeAccommodationDetails" class="action-btn btn-outline" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); border: 2px solid var(--purple-primary); color: var(--purple-primary); padding: 12px 24px; border-radius: 12px; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(184,134,11,.1);">
        <i class="fas fa-times" style="margin-right: 8px;"></i>Close
      </button>
    </div>
  </div>
</div>

<script>
  (function(){
    // State
    var allRows = [];
    var filteredRows = [];
    var currentPage = 1;
    var pageSize = 10;

    // Get all rows from the table
    var table = document.getElementById('accommodationsTable').getElementsByTagName('tbody')[0];
    var rows = Array.from(table.rows);
    
    // Convert table rows to data objects
    allRows = rows.map(function(row) {
      var cells = row.cells;
      return {
        name: cells[0] ? cells[0].textContent.trim() : '',
        capacity: cells[1] ? cells[1].textContent.trim() : '',
        description: cells[2] ? cells[2].textContent.trim() : '',
        created: cells[3] ? cells[3].textContent.trim() : '',
        element: row
      };
    });

    function applySearch(){
      var search = document.getElementById('adminSearch');
      var q = (search ? search.value : '').toLowerCase();
      if (!q) { 
        filteredRows = allRows.slice(); 
      } else {
        filteredRows = allRows.filter(function(r){
          var t = (''+r.name+' '+r.capacity+' '+r.description+' '+r.created).toLowerCase();
          return t.indexOf(q) !== -1;
        });
      }
      currentPage = 1;
      renderTable();
      renderPagination();
    }

    function renderTable(){
      var tbody = document.getElementById('accommodationsTable').getElementsByTagName('tbody')[0];
      tbody.innerHTML = '';
      var start = (currentPage - 1) * pageSize;
      var pageItems = filteredRows.slice(start, start + pageSize);
      pageItems.forEach(function(r){
        tbody.appendChild(r.element);
      });
    }

    function renderPagination(){
      var container = document.getElementById('pagination');
      var totalPages = Math.max(1, Math.ceil(filteredRows.length / pageSize));
      if (totalPages <= 1) { container.style.display = 'none'; container.innerHTML=''; return; }
      container.style.display = '';
      
      // Calculate the range of pages to show (max 10 pages)
      var maxVisiblePages = 10;
      var startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
      var endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
      
      // Adjust startPage if we're near the end
      if (endPage - startPage + 1 < maxVisiblePages) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
      }
      
      var html = '<ul class="pagination">';
      function pageItem(p, label, disabled, active){
        var liCls = active ? 'active' : '';
        var btnCls = 'page-link' + (disabled ? ' disabled' : '');
        return '<li class="'+liCls+'"><button type="button" class="'+btnCls+'" data-page="'+p+'">'+label+'</button></li>';
      }
      
      // Previous button
      html += pageItem(Math.max(1, currentPage-1), '&laquo;', currentPage===1, false);
      
      // First page if not in range
      if (startPage > 1) {
        html += pageItem(1, '1', false, false);
        if (startPage > 2) {
          html += '<li class="page-item disabled"><span class="page-link disabled">...</span></li>';
        }
      }
      
      // Page numbers in range
      for (var p = startPage; p <= endPage; p++){
        html += pageItem(p, p, false, p===currentPage);
      }
      
      // Last page if not in range
      if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
          html += '<li class="page-item disabled"><span class="page-link disabled">...</span></li>';
        }
        html += pageItem(totalPages, totalPages, false, false);
      }
      
      // Next button
      html += pageItem(Math.min(totalPages, currentPage+1), '&raquo;', currentPage===totalPages, false);
      html += '</ul>';
      container.innerHTML = html;
    }

    function attachPaginationHandler(){
      var container = document.getElementById('pagination');
      container.addEventListener('click', function(e){
        var btn = e.target.closest('button[data-page]');
        if (!btn || btn.classList.contains('disabled')) return;
        currentPage = parseInt(btn.getAttribute('data-page')) || 1;
        renderTable();
        renderPagination();
      });
    }

    // Client-side search
    var search = document.getElementById('adminSearch');
    if (search) search.addEventListener('input', applySearch);

    attachPaginationHandler();
    applySearch();

    var modal = document.getElementById('accommodationModal');
    var openBtn = document.getElementById('openAddAdmin');
    var closeBtn = document.getElementById('closeAccommodationModal');
    var cancelBtn = document.getElementById('cancelAccommodation');

    // Open Add Modal
    function openModal(){ modal.style.display = 'flex'; }
    
    // Add confirmation
    var form = document.getElementById('accommodationForm');
    if (form) {
      form.addEventListener('submit', function(e) {
        e.preventDefault();
        var name = document.querySelector('input[name="name"]').value;
        var capacity = document.querySelector('input[name="capacity"]').value;
        if (confirm('Are you sure you want to add "' + name + '" with capacity ' + capacity + '?')) {
          this.submit();
        }
      });
    }

    // Close Add Modal
    function closeModal(){ modal.style.display = 'none'; }
    if (openBtn) openBtn.addEventListener('click', openModal);
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    // Re-add handlers after pagination
    var originalRenderTable = renderTable;
    renderTable = function() {
      originalRenderTable();
      addRowClickHandlers();
    };

    // Update modal logic
    var updateModal = document.getElementById('updateModal');
    var closeUpdateModalBtn = document.getElementById('closeUpdateModal');
    var cancelUpdateBtn = document.getElementById('cancelUpdate');

    function openUpdateModal(){ updateModal.style.display = 'flex'; }

    // Confirmation for Update form
    var updateForm = document.getElementById('updateForm');
    if (updateForm) {
      updateForm.addEventListener('submit', function(e) {
        e.preventDefault();
        var name = document.getElementById('u_name').value;
        var capacity = document.getElementById('u_capacity').value;
        if (confirm('Update accommodation "' + name + '" with capacity ' + capacity + '?')) {
          this.submit();
        }
      });
    }

    // Close Update Modal
    function closeUpdateModal(){ updateModal.style.display = 'none'; }
    if (closeUpdateModalBtn) closeUpdateModalBtn.addEventListener('click', closeUpdateModal);
    if (cancelUpdateBtn) cancelUpdateBtn.addEventListener('click', closeUpdateModal);

    // Hook update buttons
    document.querySelectorAll('[data-update]').forEach(function(btn){
      btn.addEventListener('click', function(e){
        e.stopPropagation();
        var row = this.closest('tr');
        var d = row ? row.dataset : {};

        // Pre-fill fields
        document.getElementById('u_name').value = d.name || '';
        document.getElementById('u_capacity').value = d.capacity || '';
        document.getElementById('u_description').value = d.description || '';

        // Point form action
        var updateForm = document.getElementById('updateForm');
        var accId = this.getAttribute('data-accommodation-id');
        updateForm.setAttribute('action', '/adminPages/accommodations/update/' + accId);

        openUpdateModal();
      });
    });

    // Accommodation details modal functionality
    var accommodationDetailsModal = document.getElementById('accommodationDetailsModal');
    var closeAccommodationDetailsBtn = document.getElementById('closeAccommodationDetails');
    var closeAccommodationDetailsModalBtn = document.getElementById('closeAccommodationDetailsModal');
    
    function openAccommodationDetailsModal() { accommodationDetailsModal.style.display = 'flex'; }
    function closeAccommodationDetailsModal() { accommodationDetailsModal.style.display = 'none'; }
    
    if (closeAccommodationDetailsBtn) closeAccommodationDetailsBtn.addEventListener('click', closeAccommodationDetailsModal);
    if (closeAccommodationDetailsModalBtn) closeAccommodationDetailsModalBtn.addEventListener('click', closeAccommodationDetailsModal);

    // Accommodation row click handler
    function addRowClickHandlers() {
      var accommodationRows = document.querySelectorAll('.accommodation-row');
      accommodationRows.forEach(function(row) {
        row.addEventListener('click', function(e) {
          // Don't trigger if clicking on action buttons
          if (e.target.closest('button')) return;
          var a = this.dataset;
          populateAccommodationDetails({
            id: a.accommodationId,
            name: a.name,
            capacity: a.capacity,
            description: a.description,
            created_at: a.created
          });
          openAccommodationDetailsModal();
        });
      });
    }

    // Initialize row click handlers
    addRowClickHandlers();

    // Populate accommodation details in modal
    function populateAccommodationDetails(accommodation) {
      document.getElementById('detail-name').textContent = accommodation.name || '-';
      document.getElementById('detail-capacity').textContent = accommodation.capacity || '-';
      document.getElementById('detail-description').textContent = accommodation.description || '-';
      document.getElementById('detail-created').textContent = accommodation.created_at ? new Date(accommodation.created_at).toLocaleDateString() : '-';
      
      // Load rooms and rates for this accommodation
      loadAccommodationRooms(accommodation.id);
      loadAccommodationRates(accommodation.id);
    }

    // Load rooms for a specific accommodation
    function loadAccommodationRooms(accommodationId) {
      var roomsListElement = document.getElementById('detail-rooms-list');
      roomsListElement.innerHTML = '<div class="loading" style="display: flex; align-items: center; justify-content: center; gap: 6px; color: #6c757d; font-style: italic; padding: 20px; font-size: 12px;"><i class="fas fa-spinner fa-spin" style="color: var(--purple-primary); font-size: 12px;"></i><span>Loading...</span></div>';
      
      // Fetch rooms for this accommodation
      fetch('/adminPages/accommodations/' + accommodationId + '/rooms')
        .then(response => response.json())
        .then(data => {
          if (data.rooms && data.rooms.length > 0) {
            var roomsHtml = '<div style="display: grid; gap: 6px; max-height: 150px; overflow-y: auto; padding-right: 4px;">';
            data.rooms.forEach(function(room) {
              // Status color mapping
              var statusColor = '#6c757d';
              var statusBg = 'rgba(108,117,125,.1)';
              var statusIcon = 'fas fa-circle';
              if (room.status === 'Available') {
                statusColor = '#28a745';
                statusBg = 'rgba(40,167,69,.15)';
                statusIcon = 'fas fa-check-circle';
              } else if (room.status === 'Occupied') {
                statusColor = '#dc3545';
                statusBg = 'rgba(220,53,69,.15)';
                statusIcon = 'fas fa-user';
              } else if (room.status === 'Under Maintenance') {
                statusColor = '#ffc107';
                statusBg = 'rgba(255,193,7,.15)';
                statusIcon = 'fas fa-tools';
              }
              
              roomsHtml += '<div class="room-card" style="padding: 8px; background: linear-gradient(135deg, rgba(184,134,11,.05), rgba(184,134,11,.02)); border-radius: 8px; border-left: 3px solid var(--purple-primary); box-shadow: 0 2px 6px rgba(184,134,11,.08); transition: all 0.3s ease; position: relative;">';
              roomsHtml += '<div style="display: flex; justify-content: space-between; align-items: center; position: relative; z-index: 1;">';
              roomsHtml += '<div style="display: flex; align-items: center; gap: 6px;">';
              roomsHtml += '<div style="width: 24px; height: 24px; background: linear-gradient(135deg, var(--purple-primary), #DAA520); border-radius: 6px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(184,134,11,.3);">';
              roomsHtml += '<i class="fas fa-door-open" style="color: white; font-size: 10px;"></i>';
              roomsHtml += '</div>';
              roomsHtml += '<div>';
              roomsHtml += '<strong style="color: var(--text-primary); font-size: 12px; font-weight: 700; display: block;">' + room.room + '</strong>';
              roomsHtml += '<small style="color: #6c757d; font-size: 10px;">Level: ' + room.level + '</small>';
              roomsHtml += '</div>';
              roomsHtml += '</div>';
              roomsHtml += '<span style="padding: 3px 6px; border-radius: 12px; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px; background: ' + statusBg + '; color: ' + statusColor + '; display: flex; align-items: center; gap: 2px; box-shadow: 0 1px 4px rgba(0,0,0,.1);"><i class="' + statusIcon + '" style="font-size: 8px;"></i>' + room.status + '</span>';
              roomsHtml += '</div>';
              
              if (room.type) {
                roomsHtml += '<div style="margin-top: 4px; position: relative; z-index: 1;">';
                roomsHtml += '<span style="display: inline-flex; align-items: center; gap: 3px; padding: 2px 6px; background: rgba(184,134,11,.05); border-radius: 6px; border-left: 2px solid var(--purple-primary); font-size: 9px; color: #6c757d; font-weight: 600;"><i class="fas fa-tag" style="color: var(--purple-primary); font-size: 8px;"></i>' + room.type + '</span>';
                roomsHtml += '</div>';
              }
              
              roomsHtml += '</div>';
            });
            roomsHtml += '</div>';
            roomsListElement.innerHTML = roomsHtml;
          } else {
            roomsListElement.innerHTML = '<div style="text-align: center; padding: 20px; color: #6c757d;"><div style="width: 40px; height: 40px; background: linear-gradient(135deg, rgba(184,134,11,.1), rgba(184,134,11,.05)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px;"><i class="fas fa-door-closed" style="font-size: 16px; color: var(--purple-primary); opacity: 0.6;"></i></div><h4 style="color: #6c757d; margin: 0 0 4px 0; font-weight: 600; font-size: 12px;">No Rooms</h4><p style="font-style: italic; margin: 0; color: #6c757d; font-size: 10px;">No rooms found</p></div>';
          }
        })
        .catch(error => {
          console.error('Error loading rooms:', error);
          roomsListElement.innerHTML = '<div style="text-align: center; padding: 20px; color: #dc3545;"><div style="width: 40px; height: 40px; background: linear-gradient(135deg, rgba(220,53,69,.1), rgba(220,53,69,.05)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px;"><i class="fas fa-exclamation-triangle" style="font-size: 16px; color: #dc3545;"></i></div><h4 style="color: #dc3545; margin: 0 0 4px 0; font-weight: 600; font-size: 12px;">Error</h4><p style="font-style: italic; margin: 0; color: #dc3545; font-size: 10px;">Failed to load</p></div>';
        });
    }

    // Load rates for a specific accommodation
    function loadAccommodationRates(accommodationId) {
      var ratesListElement = document.getElementById('detail-rates-list');
      ratesListElement.innerHTML = '<div class="loading" style="display: flex; align-items: center; justify-content: center; gap: 6px; color: #6c757d; font-style: italic; padding: 20px; font-size: 12px;"><i class="fas fa-spinner fa-spin" style="color: var(--purple-primary); font-size: 12px;"></i><span>Loading...</span></div>';
      
      // Fetch rates for this accommodation
      fetch('/adminPages/accommodations/' + accommodationId + '/rates')
        .then(response => response.json())
        .then(data => {
          if (data.rates && data.rates.length > 0) {
            var ratesHtml = '<div style="display: grid; gap: 6px; max-height: 150px; overflow-y: auto; padding-right: 4px;">';
            data.rates.forEach(function(rate) {
              // Status color mapping
              var statusColor = '#6c757d';
              var statusBg = 'rgba(108,117,125,.1)';
              var statusIcon = 'fas fa-circle';
              if (rate.status === 'Active') {
                statusColor = '#28a745';
                statusBg = 'rgba(40,167,69,.15)';
                statusIcon = 'fas fa-check-circle';
              } else {
                statusColor = '#dc3545';
                statusBg = 'rgba(220,53,69,.15)';
                statusIcon = 'fas fa-times-circle';
              }
              
              ratesHtml += '<div class="rate-card" style="padding: 8px; background: linear-gradient(135deg, rgba(184,134,11,.05), rgba(184,134,11,.02)); border-radius: 8px; border-left: 3px solid var(--purple-primary); box-shadow: 0 2px 6px rgba(184,134,11,.08); transition: all 0.3s ease; position: relative;">';
              ratesHtml += '<div style="display: flex; justify-content: space-between; align-items: center; position: relative; z-index: 1;">';
              ratesHtml += '<div style="display: flex; align-items: center; gap: 6px;">';
              ratesHtml += '<div style="width: 24px; height: 24px; background: linear-gradient(135deg, var(--purple-primary), #DAA520); border-radius: 6px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(184,134,11,.3);">';
              ratesHtml += '<i class="fas fa-dollar-sign" style="color: white; font-size: 10px;"></i>';
              ratesHtml += '</div>';
              ratesHtml += '<div>';
              ratesHtml += '<strong style="color: var(--text-primary); font-size: 12px; font-weight: 700; display: block;">' + rate.duration + '</strong>';
              ratesHtml += '<small style="color: #6c757d; font-size: 10px;">₱' + parseFloat(rate.price).toLocaleString() + '</small>';
              ratesHtml += '</div>';
              ratesHtml += '</div>';
              ratesHtml += '<span style="padding: 3px 6px; border-radius: 12px; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px; background: ' + statusBg + '; color: ' + statusColor + '; display: flex; align-items: center; gap: 2px; box-shadow: 0 1px 4px rgba(0,0,0,.1);"><i class="' + statusIcon + '" style="font-size: 8px;"></i>' + rate.status + '</span>';
              ratesHtml += '</div>';
              ratesHtml += '</div>';
            });
            ratesHtml += '</div>';
            ratesListElement.innerHTML = ratesHtml;
          } else {
            ratesListElement.innerHTML = '<div style="text-align: center; padding: 20px; color: #6c757d;"><div style="width: 40px; height: 40px; background: linear-gradient(135deg, rgba(184,134,11,.1), rgba(184,134,11,.05)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px;"><i class="fas fa-dollar-sign" style="font-size: 16px; color: var(--purple-primary); opacity: 0.6;"></i></div><h4 style="color: #6c757d; margin: 0 0 4px 0; font-weight: 600; font-size: 12px;">No Rates</h4><p style="font-style: italic; margin: 0; color: #6c757d; font-size: 10px;">No rates found</p></div>';
          }
        })
        .catch(error => {
          console.error('Error loading rates:', error);
          ratesListElement.innerHTML = '<div style="text-align: center; padding: 20px; color: #dc3545;"><div style="width: 40px; height: 40px; background: linear-gradient(135deg, rgba(220,53,69,.1), rgba(220,53,69,.05)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px;"><i class="fas fa-exclamation-triangle" style="font-size: 16px; color: #dc3545;"></i></div><h4 style="color: #dc3545; margin: 0 0 4px 0; font-weight: 600; font-size: 12px;">Error</h4><p style="font-style: italic; margin: 0; color: #dc3545; font-size: 10px;">Failed to load</p></div>';
        });
    }

    // Archive functionality
    document.querySelectorAll('[data-archive]').forEach(function(btn){
      btn.addEventListener('click', function(e){
        e.stopPropagation();
        var accId = this.getAttribute('data-accommodation-id');
        var accName = this.closest('tr').querySelector('.accommodation-description').textContent;
        
        if (confirm('Are you sure you want to archive "' + accName + '"?')) {
          var form = document.createElement('form');
          form.method = 'POST';
          form.action = '/adminPages/accommodations/delete/' + accId;
          
          var csrfToken = document.createElement('input');
          csrfToken.type = 'hidden';
          csrfToken.name = '_token';
          csrfToken.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                            document.querySelector('input[name="_token"]')?.value;
          
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
