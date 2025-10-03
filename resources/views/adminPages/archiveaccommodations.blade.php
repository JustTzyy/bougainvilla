@extends('layouts.admindashboard')

@section('title','Archived Accommodation Records')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/archiveadminrecords.css') }}">
<style>
  /* Enhanced Accommodation Details Modal Styles (match active page) */
  #accommodationDetailsModal .modal-card { max-width: 600px; border-radius: 16px; box-shadow: 0 15px 40px rgba(184,134,11,.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); border: 1px solid rgba(184,134,11,.1); overflow: hidden; }
  #accommodationDetailsModal .info-item:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(184,134,11,.15); background: rgba(184,134,11,.08); }
  #accommodationDetailsModal .user-info-section:hover, #accommodationDetailsModal .address-info-section:hover { transform: translateY(-1px); box-shadow: 0 8px 30px rgba(184,134,11,.12); }
  #detail-rooms-list .room-card:hover, #detail-rates-list .rate-card:hover { transform: translateY(-3px); box-shadow: 0 12px 30px rgba(184,134,11,.2); background: linear-gradient(135deg, rgba(184,134,11,.08), rgba(184,134,11,.04)) !important; }
  @keyframes pulse { 0%{opacity:1} 50%{opacity:.5} 100%{opacity:1} }
  #detail-rooms-list .loading, #detail-rates-list .loading { animation: pulse 1.5s ease-in-out infinite; }
  @media (max-width: 768px) { #accommodationDetailsModal .modal-card{ max-width:95%; margin:20px;} #accommodationDetailsModal .info-grid{ grid-template-columns:1fr !important; } }
</style>
@endpush

@section('content')
<div class="dashboard-page">
  <div class="page-header">
    <h1 class="page-title">Archived Accommodation</h1>
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
      <input id="archiveSearch" type="text" placeholder="Search archived accommodations" class="search-input">
    </div>
    <div class="toolbar-actions">
      <a href="{{ route('adminPages.accommodations') }}" class="archive-btn">
        <i class="fas fa-arrow-left"></i> Back to Records
      </a>
    </div>
  </div>
  
  <!-- Client-side pagination -->
  <nav class="pagination no-print" aria-label="Table pagination" id="pagination" style="display:none;"></nav>

  <div class="chart-card card-tight">
    <div class="section-header-pad">
      <h3 class="chart-title">List</h3>
    </div>

    <div class="table-wrapper">
      <table class="table sortable-table" id="archivedTable">
        <thead>
          <tr>
            <th>Name</th>
            <th>Capacity</th>
            <th>Description</th>
            <th>Archive Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @if(isset($accommodations) && $accommodations->count() > 0)
            @foreach($accommodations as $accommodation)
              <tr class="accommodation-row"
                  data-id="{{ $accommodation->id }}"
                  data-name="{{ $accommodation->name }}"
                  data-capacity="{{ $accommodation->capacity }}"
                  data-description="{{ $accommodation->description }}"
                  data-status="{{ $accommodation->status ?? 'Archived' }}"
                  data-archived="{{ $accommodation->deleted_at }}">
                <td data-label="Name" class="accommodation-name">{{ $accommodation->name }}</td>
                <td data-label="Capacity">{{ $accommodation->capacity ?? 'N/A' }}</td>
                <td data-label="Description">{{ $accommodation->description ?? 'N/A' }}</td>
                <td data-label="Archive Date">{{ $accommodation->deleted_at ? $accommodation->deleted_at->format('M d, Y') : '-' }}</td>
                <td data-label="Actions">
                  <button class="action-btn small" data-restore> 
                    <i class="fas fa-undo"></i> 
                  </button>
                </td>
              </tr>
            @endforeach
          @else
            <tr>
              <td colspan="5" class="text-center">No archived accommodations found</td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>
    
  </div>
</div>

<!-- Accommodation Details Modal (styled like active page) -->
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
            <label style="display:block;font-size:10px;font-weight:600;color:#6c757d;"><i class="fas fa-tag" style="margin-right:4px;color:var(--purple-primary);font-size:10px;"></i>Name</label>
            <span id="detail-name" style="font-size:14px;font-weight:700;color:var(--text-primary);">-</span>
          </div>
          <div class="info-item" style="background: rgba(184,134,11,.05); padding: 8px; border-radius: 8px; border-left: 3px solid var(--purple-primary); transition: all 0.3s ease;">
            <label style="display:block;font-size:10px;font-weight:600;color:#6c757d;"><i class="fas fa-users" style="margin-right:4px;color:var(--purple-primary);font-size:10px;"></i>Capacity</label>
            <span id="detail-capacity" style="font-size:14px;font-weight:700;color:var(--text-primary);">-</span>
          </div>
          <div class="info-item" style="background: rgba(184,134,11,.05); padding: 8px; border-radius: 8px; border-left: 3px solid var(--purple-primary); transition: all 0.3s ease;">
            <label style="display:block;font-size:10px;font-weight:600;color:#6c757d;"><i class="fas fa-calendar-times" style="margin-right:4px;color:var(--purple-primary);font-size:10px;"></i>Date Archived</label>
            <span id="detail-archived" style="font-size:14px;font-weight:700;color:var(--text-primary);">-</span>
          </div>
        </div>
        <div class="info-item" style="background: rgba(184,134,11,.05); padding: 8px; border-radius: 8px; border-left: 3px solid var(--purple-primary); transition: all 0.3s ease; margin-top: 8px;">
          <label style="display:block;font-size:10px;font-weight:600;color:#6c757d;"><i class="fas fa-align-left" style="margin-right:4px;color:var(--purple-primary);font-size:10px;"></i>Description</label>
          <span id="detail-description" style="font-size:14px;font-weight:700;color:var(--text-primary);">-</span>
        </div>
      </div>

      <!-- Rooms Section -->
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

      <!-- Rates Section -->
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
    var table = document.getElementById('archivedTable').getElementsByTagName('tbody')[0];
    var rows = Array.from(table.rows);
    
    // Convert table rows to data objects
    allRows = rows.map(function(row) {
      var cells = row.cells;
      return {
        name: cells[0] ? cells[0].textContent.trim() : '',
        capacity: cells[1] ? cells[1].textContent.trim() : '',
        description: cells[2] ? cells[2].textContent.trim() : '',
        archived: cells[3] ? cells[3].textContent.trim() : '',
        element: row
      };
    });

    function applySearch(){
      var search = document.getElementById('archiveSearch');
      var q = (search ? search.value : '').toLowerCase();
      if (!q) { 
        filteredRows = allRows.slice(); 
      } else { 
        filteredRows = allRows.filter(function(r){ 
          return (r.name + ' ' + r.capacity + ' ' + r.description + ' ' + r.archived).toLowerCase().indexOf(q) !== -1; 
        }); 
      }
      currentPage = 1;
      renderTable();
      renderPagination();
    }

    function renderTable(){
      var tbody = document.getElementById('archivedTable').getElementsByTagName('tbody')[0];
      tbody.innerHTML = '';
      var start = (currentPage - 1) * pageSize;
      var end = start + pageSize;
      var pageItems = filteredRows.slice(start, end);
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
        if (!btn) return;
        var p = parseInt(btn.getAttribute('data-page'));
        if (p && p !== currentPage) { currentPage = p; renderTable(); renderPagination(); }
      });
    }

    // Initialize
    attachPaginationHandler();
    applySearch();

    var search = document.getElementById('archiveSearch');
    if (search) search.addEventListener('input', applySearch);

    // Accommodation details modal functionality (match active page)
    var modal = document.getElementById('accommodationDetailsModal');
    var closeBtn = document.getElementById('closeAccommodationDetails');
    var closeX = document.getElementById('closeAccommodationDetailsModal');
    
    function openModal() { modal.style.display = 'flex'; }
    function closeModal() { modal.style.display = 'none'; }
    
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (closeX) closeX.addEventListener('click', closeModal);

    var rows = document.querySelectorAll('.accommodation-row');
    rows.forEach(function(row) {
      row.addEventListener('click', function(e) {
        if (e.target.closest('button')) return;
        var a = this.dataset;
        populateAccommodationDetails({
          id: a.id,
          name: a.name,
          capacity: a.capacity,
          description: a.description,
          archived_at: a.archived
        });
        openModal();
      });
    });

    // Populate details
    function populateAccommodationDetails(a) {
      document.getElementById('detail-name').textContent = a.name || '-';
      document.getElementById('detail-capacity').textContent = a.capacity || 'N/A';
      document.getElementById('detail-description').textContent = a.description || 'N/A';
      document.getElementById('detail-archived').textContent = a.archived_at ? new Date(a.archived_at).toLocaleDateString() : '-';
      // Load rooms and rates (same as active page)
      loadAccommodationRooms(a.id);
      loadAccommodationRates(a.id);
    }

    // Load rooms for archived accommodation (same endpoints)
    function loadAccommodationRooms(accommodationId) {
      var roomsListElement = document.getElementById('detail-rooms-list');
      roomsListElement.innerHTML = '<div class="loading" style="display: flex; align-items: center; justify-content: center; gap: 6px; color: #6c757d; font-style: italic; padding: 20px; font-size: 12px;"><i class="fas fa-spinner fa-spin" style="color: var(--purple-primary); font-size: 12px;"></i><span>Loading...</span></div>';
      fetch('/adminPages/accommodations/' + accommodationId + '/rooms')
        .then(resp => resp.json())
        .then(data => {
          if (data.rooms && data.rooms.length) {
            var html = '<div style="display: grid; gap: 6px; max-height: 150px; overflow-y: auto; padding-right: 4px;">';
            data.rooms.forEach(function(room){
              var statusColor = '#6c757d', statusBg = 'rgba(108,117,125,.1)', statusIcon = 'fas fa-circle';
              if (room.status === 'Available') { statusColor = '#28a745'; statusBg='rgba(40,167,69,.15)'; statusIcon='fas fa-check-circle'; }
              else if (room.status === 'Occupied') { statusColor = '#dc3545'; statusBg='rgba(220,53,69,.15)'; statusIcon='fas fa-user'; }
              else if (room.status === 'Under Maintenance') { statusColor = '#ffc107'; statusBg='rgba(255,193,7,.15)'; statusIcon='fas fa-tools'; }
              html += '<div class="room-card" style="padding: 8px; background: linear-gradient(135deg, rgba(184,134,11,.05), rgba(184,134,11,.02)); border-radius: 8px; border-left: 3px solid var(--purple-primary); box-shadow: 0 2px 6px rgba(184,134,11,.08); transition: all 0.3s ease; position: relative;">';
              html += '<div style="display:flex;justify-content:space-between;align-items:center;position:relative;z-index:1;">';
              html += '<div style="display:flex;align-items:center;gap:6px;">';
              html += '<div style="width:24px;height:24px;background:linear-gradient(135deg,var(--purple-primary),#DAA520);border-radius:6px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 6px rgba(184,134,11,.3);"><i class="fas fa-door-open" style="color:white;font-size:10px;"></i></div>';
              html += '<div><strong style="color:var(--text-primary);font-size:12px;font-weight:700;display:block;">' + room.room + '</strong><small style="color:#6c757d;font-size:10px;">Level: ' + room.level + '</small></div>';
              html += '</div>';
              html += '<span style="padding:3px 6px;border-radius:12px;font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.3px;background:' + statusBg + ';color:' + statusColor + ';display:flex;align-items:center;gap:2px;box-shadow:0 1px 4px rgba(0,0,0,.1);"><i class="' + statusIcon + '" style="font-size:8px;"></i>' + room.status + '</span>';
              html += '</div>';
              html += '</div>';
            });
            html += '</div>';
            roomsListElement.innerHTML = html;
          } else {
            roomsListElement.innerHTML = '<div style="text-align:center;padding:20px;color:#6c757d;"><div style="width:40px;height:40px;background:linear-gradient(135deg,rgba(184,134,11,.1),rgba(184,134,11,.05));border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;"><i class="fas fa-door-closed" style="font-size:16px;color:var(--purple-primary);opacity:.6;"></i></div><h4 style="color:#6c757d;margin:0 0 4px 0;font-weight:600;font-size:12px;">No Rooms</h4><p style="font-style:italic;margin:0;color:#6c757d;font-size:10px;">No rooms found</p></div>';
          }
        })
        .catch(() => {
          roomsListElement.innerHTML = '<div style="text-align:center;padding:20px;color:#dc3545;"><div style="width:40px;height:40px;background:linear-gradient(135deg,rgba(220,53,69,.1),rgba(220,53,69,.05));border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;"><i class="fas fa-exclamation-triangle" style="font-size:16px;color:#dc3545;"></i></div><h4 style="color:#dc3545;margin:0 0 4px 0;font-weight:600;font-size:12px;">Error</h4><p style="font-style:italic;margin:0;color:#dc3545;font-size:10px;">Failed to load</p></div>';
        });
    }

    // Load rates for archived accommodation (same endpoints)
    function loadAccommodationRates(accommodationId) {
      var ratesListElement = document.getElementById('detail-rates-list');
      ratesListElement.innerHTML = '<div class="loading" style="display: flex; align-items: center; justify-content: center; gap: 6px; color: #6c757d; font-style: italic; padding: 20px; font-size: 12px;"><i class="fas fa-spinner fa-spin" style="color: var(--purple-primary); font-size: 12px;"></i><span>Loading...</span></div>';
      fetch('/adminPages/accommodations/' + accommodationId + '/rates')
        .then(resp => resp.json())
        .then(data => {
          if (data.rates && data.rates.length) {
            var html = '<div style=\"display: grid; gap: 6px; max-height: 150px; overflow-y: auto; padding-right: 4px;\">';
            data.rates.forEach(function(rate){
              html += '<div class="rate-card" style="padding: 8px; background: linear-gradient(135deg, rgba(184,134,11,.05), rgba(184,134,11,.02)); border-radius: 8px; border-left: 3px solid var(--purple-primary); box-shadow: 0 2px 6px rgba(184,134,11,.08); transition: all 0.3s ease; position: relative;">';
              html += '<div style="display:flex;justify-content:space-between;align-items:center;position:relative;z-index:1;">';
              html += '<div style="display:flex;align-items:center;gap:6px;">';
              html += '<div style="width: 24px; height: 24px; background: linear-gradient(135deg, var(--purple-primary), #DAA520); border-radius: 6px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(184,134,11,.3);">';
              html += '<i class="fas fa-dollar-sign" style="color: white; font-size: 10px;"></i>';
              html += '</div>';
              html += '<div><strong style="color:var(--text-primary);font-size:12px;font-weight:700;display:block;">' + rate.duration + '</strong><small style="color:#6c757d;font-size:10px;">₱' + parseFloat(rate.price).toLocaleString() + '</small></div>';
              html += '</div>';
              html += '<span style="padding:3px 6px;border-radius:12px;font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.3px;background:rgba(108,117,125,.1);color:#6c757d;display:flex;align-items:center;gap:2px;box-shadow:0 1px 4px rgba(0,0,0,.1);"><i class="fas fa-circle" style="font-size:8px;"></i>' + rate.status + '</span>';
              html += '</div>';
              html += '</div>';
            });
            html += '</div>';
            ratesListElement.innerHTML = html;
          } else {
            ratesListElement.innerHTML = '<div style="text-align:center;padding:20px;color:#6c757d;"><div style="width:40px;height:40px;background:linear-gradient(135deg,rgba(184,134,11,.1),rgba(184,134,11,.05));border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;"><i class="fas fa-dollar-sign" style="font-size:16px;color:var(--purple-primary);opacity:.6;"></i></div><h4 style="color:#6c757d;margin:0 0 4px 0;font-weight:600;font-size:12px;">No Rates</h4><p style="font-style:italic;margin:0;color:#6c757d;font-size:10px;">No rates found</p></div>';
          }
        })
        .catch(() => {
          ratesListElement.innerHTML = '<div style="text-align:center;padding:20px;color:#dc3545;"><div style="width:40px;height:40px;background:linear-gradient(135deg,rgba(220,53,69,.1),rgba(220,53,69,.05));border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;"><i class="fas fa-exclamation-triangle" style="font-size:16px;color:#dc3545;"></i></div><h4 style="color:#dc3545;margin:0 0 4px 0;font-weight:600;font-size:12px;">Error</h4><p style="font-style:italic;margin:0;color:#dc3545;font-size:10px;">Failed to load</p></div>';
        });
    }

    // Restore functionality
    document.querySelectorAll('[data-restore]').forEach(function(btn){
      btn.addEventListener('click', function(e){
        e.stopPropagation();
        var row = this.closest('tr');
        var id = row.dataset.id;
        var name = row.dataset.name;
        
        if (confirm('Are you sure you want to restore "' + name + '"?')) {
          var form = document.createElement('form');
          form.method = 'POST';
          form.action = '/adminPages/accommodations/restore/' + id;
          
          var csrfToken = document.createElement('input');
          csrfToken.type = 'hidden';
          csrfToken.name = '_token';
          csrfToken.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                           document.querySelector('input[name="_token"]')?.value;
          
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
  })();
</script>
@endsection
