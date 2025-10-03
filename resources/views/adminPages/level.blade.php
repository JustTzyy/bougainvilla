@extends('layouts.admindashboard')

@section('title','Levels')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/adminrecords.css') }}">
<script src="{{ asset('js/ph-complete-address.js') }}"></script>
<style>
  /* Enhanced Level Details Modal Styles */
  #levelDetailsModal .modal-card {
    max-width: 600px;
    border-radius: 16px;
    box-shadow: 0 15px 40px rgba(184,134,11,.15);
    background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
    border: 1px solid rgba(184,134,11,.1);
    overflow: hidden;
  }

  #levelDetailsModal .info-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(184,134,11,.15);
    background: rgba(184,134,11,.08);
  }

  #levelDetailsModal .user-info-section:hover,
  #levelDetailsModal .address-info-section:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 30px rgba(184,134,11,.12);
  }

  #levelDetailsModal .action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(184,134,11,.25);
    background: linear-gradient(135deg, var(--purple-primary), #DAA520) !important;
    color: white !important;
  }

  /* Status badge dynamic colors */
  #detail-status.status-active {
    /* No special styling for active status */
  }

  #detail-status.status-inactive {
    background: linear-gradient(135deg, #dc3545, #e74c3c) !important;
    color: white !important;
    box-shadow: 0 2px 8px rgba(220,53,69,.3);
  }

  /* Room cards enhanced hover effects */
  #detail-rooms-list .room-card:hover {
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

  #detail-rooms-list .loading {
    animation: pulse 1.5s ease-in-out infinite;
  }

  /* Responsive adjustments */
  @media (max-width: 768px) {
    #levelDetailsModal .modal-card {
      max-width: 95%;
      margin: 20px;
    }
    
    #levelDetailsModal .info-grid {
      grid-template-columns: 1fr !important;
    }
  }

  /* Pagination styling for levels */
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
                  data-created="{{ $level->created_at }}"
                  data-rooms-count="{{ $level->rooms->count() }}">
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
    <nav class="pagination no-print" aria-label="Table pagination" id="pagination" style="display:none;"></nav>
  </div>
</div>

<!-- Add Level Modal -->
<div id="levelModal" class="modal level-modal">
  <div class="modal-card">
    <div class="modal-header">
      <h3 class="chart-title">Add Level</h3>
      <button id="closeLevelModal" class="action-btn ml-auto">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <form id="levelForm" action="{{ route('adminPages.levels.post') }}" class="modal-form" method="POST">
      @csrf
      <div class="form-grid">
        <div class="form-group span-2">
          <label>Description</label>
          <input type="text" name="description" class="form-input" placeholder="e.g., Ground Floor" required>
        </div>
        <div class="form-group">
          <label>Status</label>
          <select name="status" class="form-input" required>
            <option value="Active" selected>Active</option>
          </select>
        </div>
      </div>

      <div class="modal-actions">
        <button type="button" id="cancelLevel" class="action-btn btn-outline">Cancel</button>
        <button type="submit" class="btn-primary inline">Save Level</button>
      </div>
    </form>
  </div>
</div>

<!-- Update Level Modal (fixed) -->
<div id="updateModal" class="modal level-modal">
  <div class="modal-card">
    <div class="modal-header">
      <h3 class="chart-title">Update Level</h3>
      <button id="closeUpdateModal" class="action-btn ml-auto">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <form id="updateForm" class="modal-form" method="POST">
      @csrf
      @method('POST')
      <div class="form-grid">
        <div class="form-group span-2">
          <label>Description</label>
          <input name="description" id="u_description" class="form-input" placeholder="e.g., Ground Floor" required>
        </div>
        <div class="form-group">
          <label>Status</label>
          <select name="status" id="u_status" class="form-input" required>
            <option value="Active">Active</option>
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

<!-- Level Details Modal -->
<div id="levelDetailsModal" class="modal">
  <div class="modal-card user-details-card">
    <div class="modal-header" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); border-bottom: 1px solid rgba(184,134,11,.15);">
      <h3 class="chart-title" style="color: var(--purple-primary); font-size: 18px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px;">
        <i class="fas fa-layer-group" style="background: linear-gradient(135deg, var(--purple-primary), #DAA520); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-size: 20px;"></i>
        Level Details
      </h3>
      <button id="closeLevelDetailsModal" class="action-btn ml-auto"><i class="fas fa-times"></i></button>
    </div>
    
    <div class="user-details-content" style="padding: 12px; background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);">
      <!-- Level Information Section -->
      <div class="user-info-section" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); border-radius: 12px; padding: 12px; margin-bottom: 12px; box-shadow: 0 2px 12px rgba(184,134,11,.08); border: 1px solid rgba(184,134,11,.1);">
        <h4 style="color: var(--purple-primary); font-size: 16px; font-weight: 700; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px; padding-bottom: 12px; border-bottom: 2px solid rgba(184,134,11,.15);">
          <i class="fas fa-layer-group" style="background: linear-gradient(135deg, var(--purple-primary), #DAA520); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-size: 18px;"></i>
          Level Information
        </h4>
        <div class="info-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
          <div class="info-item" style="background: rgba(184,134,11,.05); padding: 12px; border-radius: 10px; border-left: 4px solid var(--purple-primary); transition: all 0.3s ease;">
            <label style="display: block; font-size: 12px; font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
              <i class="fas fa-hashtag" style="margin-right: 6px; color: var(--purple-primary);"></i>Floor Number
            </label> 
            <span id="detail-floor-number" style="font-size: 16px; font-weight: 700; color: var(--text-primary);">-</span>
          </div>
          <div class="info-item" style="background: rgba(184,134,11,.05); padding: 12px; border-radius: 10px; border-left: 4px solid var(--purple-primary); transition: all 0.3s ease;">
            <label style="display: block; font-size: 12px; font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
              <i class="fas fa-align-left" style="margin-right: 6px; color: var(--purple-primary);"></i>Description
            </label>
            <span id="detail-description" style="font-size: 16px; font-weight: 700; color: var(--text-primary);">-</span>
          </div>
          <div class="info-item" style="background: rgba(184,134,11,.05); padding: 12px; border-radius: 10px; border-left: 4px solid var(--purple-primary); transition: all 0.3s ease;">
            <label style="display: block; font-size: 12px; font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
              <i class="fas fa-toggle-on" style="margin-right: 6px; color: var(--purple-primary);"></i>Status
            </label>
            <span id="detail-status" class="status-badge" style="display: inline-block; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">-</span>
          </div>
          <div class="info-item" style="background: rgba(184,134,11,.05); padding: 12px; border-radius: 10px; border-left: 4px solid var(--purple-primary); transition: all 0.3s ease;">
            <label style="display: block; font-size: 12px; font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
              <i class="fas fa-calendar-plus" style="margin-right: 6px; color: var(--purple-primary);"></i>Date Created
            </label>
            <span id="detail-created" style="font-size: 16px; font-weight: 700; color: var(--text-primary);">-</span>
          </div>
          <div class="info-item" style="background: rgba(184,134,11,.05); padding: 12px; border-radius: 10px; border-left: 4px solid var(--purple-primary); transition: all 0.3s ease;">
            <label style="display: block; font-size: 12px; font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
              <i class="fas fa-door-open" style="margin-right: 6px; color: var(--purple-primary);"></i>Total Rooms
            </label>
            <span id="detail-rooms-count" style="font-size: 16px; font-weight: 700; color: var(--text-primary);">-</span>
          </div>
        </div>
      </div>
      
      <!-- Room Information Section -->
      <div class="address-info-section" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); border-radius: 12px; padding: 12px; box-shadow: 0 2px 12px rgba(184,134,11,.08); border: 1px solid rgba(184,134,11,.1);">
        <h4 style="color: var(--purple-primary); font-size: 14px; font-weight: 700; margin: 0 0 8px 0; display: flex; align-items: center; gap: 6px; padding-bottom: 8px; border-bottom: 1px solid rgba(184,134,11,.15);">
          <i class="fas fa-door-open" style="background: linear-gradient(135deg, var(--purple-primary), #DAA520); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-size: 14px;"></i>
          Rooms
        </h4>
        <div class="info-grid">
          <div class="info-item span-2" style="grid-column: span 2;">
            <div id="detail-rooms-list" style="background: rgba(184,134,11,.03); border-radius: 8px; padding: 8px; min-height: 60px; border: 1px dashed rgba(184,134,11,.2);">
              <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #6c757d; font-style: italic; font-size: 12px;">
                <i class="fas fa-spinner fa-spin" style="margin-right: 6px; color: var(--purple-primary); font-size: 12px;"></i>
                Loading...
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="modal-actions" style="padding: 12px; background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%); border-top: 1px solid rgba(184,134,11,.15); border-radius: 0 0 16px 16px;">
      <button type="button" id="closeLevelDetails" class="action-btn btn-outline" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); border: 2px solid var(--purple-primary); color: var(--purple-primary); padding: 12px 24px; border-radius: 12px; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(184,134,11,.1);">
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
    var table = document.getElementById('levelsTable').getElementsByTagName('tbody')[0];
    var rows = Array.from(table.rows);
    
    // Convert table rows to data objects
    allRows = rows.map(function(row) {
      var cells = row.cells;
      return {
        floor: cells[0] ? cells[0].textContent.trim() : '',
        description: cells[1] ? cells[1].textContent.trim() : '',
        status: cells[2] ? cells[2].textContent.trim() : '',
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
          var t = (''+r.floor+' '+r.description+' '+r.status+' '+r.created).toLowerCase();
          return t.indexOf(q) !== -1;
        });
      }
      currentPage = 1;
      renderTable();
      renderPagination();
    }

    function renderTable(){
      var tbody = document.getElementById('levelsTable').getElementsByTagName('tbody')[0];
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

    var modal = document.getElementById('levelModal');
    var openBtn = document.getElementById('openAddAdmin');
    var closeBtn = document.getElementById('closeLevelModal');
    var cancelBtn = document.getElementById('cancelLevel');

    // Open Add Modal
    function openModal(){ 
      modal.style.display = 'flex'; 
    }
    
    // Add confirmation for Add Level form
    var levelForm = document.getElementById('levelForm');
    if (levelForm) {
      levelForm.addEventListener('submit', function(e) {
        e.preventDefault();
        var description = document.querySelector('input[name="description"]').value;
        var status = document.querySelector('select[name="status"]').value;
        if (confirm('Are you sure you want to add Level "' + description + '" (status: ' + status + ')?')) {
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

    function openUpdateModal(){ 
      updateModal.style.display = 'flex'; 
    }

    // Confirmation for Update Level form
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
        document.getElementById('u_description').value = d.description || '';
        document.getElementById('u_status').value = d.status || '';

        // Point form action to update route
        var updateForm = document.getElementById('updateForm');
        var levelId = this.getAttribute('data-level-id');
        updateForm.setAttribute('action', '/adminPages/levels/update/' + levelId);

        openUpdateModal();
      });
    });

    // Level details modal functionality
    var levelDetailsModal = document.getElementById('levelDetailsModal');
    var closeLevelDetailsBtn = document.getElementById('closeLevelDetails');
    var closeLevelDetailsModalBtn = document.getElementById('closeLevelDetailsModal');
    
    function openLevelDetailsModal() { levelDetailsModal.style.display = 'flex'; }
    function closeLevelDetailsModal() { levelDetailsModal.style.display = 'none'; }
    
    if (closeLevelDetailsBtn) closeLevelDetailsBtn.addEventListener('click', closeLevelDetailsModal);
    if (closeLevelDetailsModalBtn) closeLevelDetailsModalBtn.addEventListener('click', closeLevelDetailsModal);

    // Level row click handler
    function addRowClickHandlers() {
      var levelRows = document.querySelectorAll('.level-row');
      levelRows.forEach(function(row) {
        row.addEventListener('click', function(e) {
          // Don't trigger if clicking on action buttons
          if (e.target.closest('button')) return;
          var l = this.dataset;
          populateLevelDetails({
            id: l.levelId,
            description: l.description,
            status: l.status,
            created_at: l.created,
            rooms_count: l.roomsCount
          });
          openLevelDetailsModal();
        });
      });
    }

    // Initialize row click handlers
    addRowClickHandlers();

    // Populate level details in modal
    function populateLevelDetails(level) {
      document.getElementById('detail-floor-number').textContent = level.id || '-';
      document.getElementById('detail-description').textContent = level.description || '-';
      
      // Enhanced status badge with dynamic styling
      var statusElement = document.getElementById('detail-status');
      statusElement.textContent = level.status || '-';
      statusElement.className = 'status-badge';
      if (level.status === 'Active') {
        statusElement.classList.add('status-active');
      } else {
        statusElement.classList.add('status-inactive');
      }
      
      document.getElementById('detail-created').textContent = level.created_at ? new Date(level.created_at).toLocaleDateString() : '-';
      document.getElementById('detail-rooms-count').textContent = level.rooms_count || '0';
      
      // Load rooms for this level
      loadLevelRooms(level.id);
    }

    // Load rooms for a specific level
    function loadLevelRooms(levelId) {
      var roomsListElement = document.getElementById('detail-rooms-list');
      roomsListElement.innerHTML = '<div class="loading" style="display: flex; align-items: center; justify-content: center; gap: 6px; color: #6c757d; font-style: italic; padding: 20px; font-size: 12px;"><i class="fas fa-spinner fa-spin" style="color: var(--purple-primary); font-size: 12px;"></i><span>Loading...</span></div>';
      
      // Fetch rooms for this level
      fetch('/adminPages/levels/' + levelId + '/rooms')
        .then(response => response.json())
        .then(data => {
          if (data.rooms && data.rooms.length > 0) {
            var roomsHtml = '<div style="display: grid; gap: 6px; max-height: 200px; overflow-y: auto; padding-right: 4px;">';
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
              roomsHtml += '<strong style="color: var(--text-primary); font-size: 12px; font-weight: 700; display: block;">' + room.name + '</strong>';
              roomsHtml += '<small style="color: #6c757d; font-size: 10px;">ID: ' + room.id + '</small>';
              roomsHtml += '</div>';
              roomsHtml += '</div>';
              roomsHtml += '<span style="padding: 3px 6px; border-radius: 12px; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px; background: ' + statusBg + '; color: ' + statusColor + '; display: flex; align-items: center; gap: 2px; box-shadow: 0 1px 4px rgba(0,0,0,.1);"><i class="' + statusIcon + '" style="font-size: 8px;"></i>' + room.status + '</span>';
              roomsHtml += '</div>';
              
              if (room.type || (room.accommodations && room.accommodations.length > 0)) {
                roomsHtml += '<div style="margin-top: 4px; position: relative; z-index: 1; display: flex; gap: 4px; flex-wrap: wrap;">';
                if (room.type) {
                  roomsHtml += '<span style="display: inline-flex; align-items: center; gap: 3px; padding: 2px 6px; background: rgba(184,134,11,.05); border-radius: 6px; border-left: 2px solid var(--purple-primary); font-size: 9px; color: #6c757d; font-weight: 600;"><i class="fas fa-tag" style="color: var(--purple-primary); font-size: 8px;"></i>' + room.type + '</span>';
                }
                if (room.accommodations && room.accommodations.length > 0) {
                  var accommodationNames = room.accommodations.map(function(acc) { return acc.name; }).join(', ');
                  roomsHtml += '<span style="display: inline-flex; align-items: center; gap: 3px; padding: 2px 6px; background: rgba(184,134,11,.05); border-radius: 6px; border-left: 2px solid var(--purple-primary); font-size: 9px; color: #6c757d; font-weight: 600;"><i class="fas fa-bed" style="color: var(--purple-primary); font-size: 8px;"></i>' + accommodationNames + '</span>';
                }
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

    // Archive functionality with reason
document.querySelectorAll('[data-archive]').forEach(function(btn){
  btn.addEventListener('click', function(e){
    e.stopPropagation();

    var levelId = this.getAttribute('data-level-id');
    var levelName = this.closest('tr').querySelector('.level-description').textContent;

    // Ask for reason before deleting
    var reason = prompt('You are about to delete "' + levelName + '". Please provide a reason:');
    
    if(reason && reason.trim() !== '') {
      // Show confirmation with reason
      if (confirm('Are you sure you want to archive "' + levelName + '" with reason: "' + reason + '"?')) {
        // Create form dynamically
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '/adminPages/levels/delete/' + levelId;

        // CSRF token
        var csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                          document.querySelector('input[name="_token"]')?.value;

        // Method override
        var methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';

        // Reason input
        var reasonField = document.createElement('input');
        reasonField.type = 'hidden';
        reasonField.name = 'reason';
        reasonField.value = reason;

        // Append fields
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        form.appendChild(reasonField);

        document.body.appendChild(form);
        form.submit();
      }
    } else if(reason !== null) {
      alert('Action canceled. Reason is required.');
    }
  });
});

  })();
</script>

@endsection


