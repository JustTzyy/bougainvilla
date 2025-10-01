@extends('layouts.admindashboard')

@section('title','Archived Rooms')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/adminrecords.css') }}">
<script src="{{ asset('js/ph-complete-address.js') }}"></script>
@endpush

@section('content')
<div class="dashboard-page">
  <div class="page-header">
    <h1 class="page-title">Archived Rooms</h1>
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
      <input id="adminSearch" type="text" placeholder="Search archived rooms" class="search-input">
    </div>
    <div class="toolbar-actions">
      <a href="{{ route('adminPages.rooms') }}" class="archive-btn">
        <i class="fas fa-arrow-left"></i> Back to Records
      </a>
    </div>
  </div>
  
  <!-- Client-side pagination -->
  <nav class="pagination no-print" aria-label="Table pagination" id="pagination" style="display:none;"></nav>

  <div class="chart-card card-tight">
    <div class="section-header-pad">
      <h3 class="chart-title">Archived List</h3>
    </div>

    <div class="table-wrapper">
      <table class="table sortable-table" id="archivedRoomsTable">
        <thead>
          <tr>
            <th>Room No.</th>
            <th>Level</th>
            <th>Status</th>
            <th>Type</th>
            <th>Date Archived</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @if(isset($rooms) && $rooms->count() > 0)
            @foreach($rooms as $room)
              <tr class="room-row"
                  data-room-id="{{ $room->id }}"
                  data-room="{{ $room->room }}"
                  data-level-id="{{ $room->level_id }}"
                  data-status="{{ $room->status }}"
                  data-type="{{ $room->type }}"
                  data-accommodations="{{ $room->accommodations->pluck('id')->implode(',') }}"
                  data-archived="{{ $room->deleted_at }}">
                <td data-label="Room No.">{{ $room->room }}</td>
                <td data-label="Level">{{ optional($room->level)->description ?? '-' }}</td>
                <td data-label="Status">{{ $room->status }}</td>
                <td data-label="Type">{{ $room->type }}</td>
                <td data-label="Date Archived">{{ $room->deleted_at->format('M d, Y H:i') }}</td>
                <td data-label="Actions">
                  @php($floorArchived = $room->level && method_exists($room->level, 'trashed') ? $room->level->trashed() : false)
                  <button class="action-btn small" data-restore data-room-id="{{ $room->id }}" {{ $floorArchived ? 'disabled' : '' }} title="{{ $floorArchived ? 'Restore floor first' : '' }}">
                    <i class="fas fa-undo"></i>
                  </button>
                </td>
              </tr>
            @endforeach
          @else
            <tr>
              <td colspan="6" class="text-center">No archived rooms found</td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>
</div>


<!-- Archived Room Details Modal -->
<div id="archivedRoomDetailsModal" class="modal">
  <div class="modal-card user-details-card">
    <div class="modal-header" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); border-bottom: 1px solid rgba(184,134,11,.15);">
      <h3 class="chart-title" style="color: var(--purple-primary); font-size: 18px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px;">
        <i class="fas fa-bed" style="background: linear-gradient(135deg, var(--purple-primary), #DAA520); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-size: 20px;"></i>
        Archived Room Details
      </h3>
      <button id="closeArchivedRoomDetailsModal" class="action-btn ml-auto"><i class="fas fa-times"></i></button>
    </div>

    <div class="user-details-content" style="padding: 8px; background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);">
      <div class="user-info-section" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); border-radius: 10px; padding: 8px; margin-bottom: 8px; box-shadow: 0 2px 12px rgba(184,134,11,.08); border: 1px solid rgba(184,134,11,.1);">
        <h4 style="color: var(--purple-primary); font-size: 14px; font-weight: 700; margin: 0 0 8px 0; display: flex; align-items: center; gap: 6px; padding-bottom: 6px; border-bottom: 1px solid rgba(184,134,11,.15);">
          <i class="fas fa-info-circle" style="margin-right: 4px; background: linear-gradient(135deg, var(--purple-primary), #DAA520); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-size: 14px;"></i>
          Room Information
        </h4>
        <div class="info-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 8px;">
          <div class="info-item" style="background: rgba(184,134,11,.05); padding: 8px; border-radius: 8px; border-left: 3px solid var(--purple-primary);">
            <label style="display: block; font-size: 10px; font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 2px;">Room Number</label>
            <span id="arch-detail-room-number" style="font-size: 14px; font-weight: 700; color: var(--text-primary);">-</span>
          </div>
          <div class="info-item" style="background: rgba(184,134,11,.05); padding: 8px; border-radius: 8px; border-left: 3px solid var(--purple-primary);">
            <label style="display: block; font-size: 10px; font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 2px;">Level</label>
            <span id="arch-detail-level" style="font-size: 14px; font-weight: 700; color: var(--text-primary);">-</span>
          </div>
          <div class="info-item" style="background: rgba(184,134,11,.05); padding: 8px; border-radius: 8px; border-left: 3px solid var(--purple-primary);">
            <label style="display: block; font-size: 10px; font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 2px;">Status</label>
            <span id="arch-detail-status" style="font-size: 14px; font-weight: 700; color: var(--text-primary);">-</span>
          </div>
          <div class="info-item" style="background: rgba(184,134,11,.05); padding: 8px; border-radius: 8px; border-left: 3px solid var(--purple-primary);">
            <label style="display: block; font-size: 10px; font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 2px;">Type</label>
            <span id="arch-detail-type" style="font-size: 14px; font-weight: 700; color: var(--text-primary);">-</span>
          </div>
          <div class="info-item" style="background: rgba(184,134,11,.05); padding: 8px; border-radius: 8px; border-left: 3px solid var(--purple-primary);">
            <label style="display: block; font-size: 10px; font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 2px;">Date Archived</label>
            <span id="arch-detail-archived" style="font-size: 14px; font-weight: 700; color: var(--text-primary);">-</span>
          </div>
        </div>
      </div>

      <div class="address-info-section" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); border-radius: 10px; padding: 8px; box-shadow: 0 2px 12px rgba(184,134,11,.08); border: 1px solid rgba(184,134,11,.1);">
        <h4 style="color: var(--purple-primary); font-size: 12px; font-weight: 700; margin: 0 0 6px 0; display: flex; align-items: center; gap: 4px; padding-bottom: 4px; border-bottom: 1px solid rgba(184,134,11,.15);">
          <i class="fas fa-hotel" style="background: linear-gradient(135deg, var(--purple-primary), #DAA520); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-size: 12px;"></i>
          Accommodations
        </h4>
        <div class="info-grid">
          <div class="info-item span-2" style="grid-column: span 2;">
            <div id="arch-detail-accommodations-list" style="background: rgba(184,134,11,.03); border-radius: 6px; padding: 6px; min-height: 50px; border: 1px dashed rgba(184,134,11,.2);">
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
      <button type="button" id="closeArchivedRoomDetails" class="action-btn btn-outline" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); border: 2px solid var(--purple-primary); color: var(--purple-primary); padding: 12px 24px; border-radius: 12px; font-weight: 600;">
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
    var table = document.getElementById('archivedRoomsTable').getElementsByTagName('tbody')[0];
    var rows = Array.from(table.rows);
    
    // Convert table rows to data objects
    allRows = rows.map(function(row) {
      var cells = row.cells;
      return {
        room: cells[0] ? cells[0].textContent.trim() : '',
        level: cells[1] ? cells[1].textContent.trim() : '',
        status: cells[2] ? cells[2].textContent.trim() : '',
        type: cells[3] ? cells[3].textContent.trim() : '',
        archived: cells[4] ? cells[4].textContent.trim() : '',
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
          return (r.room + ' ' + r.level + ' ' + r.status + ' ' + r.type + ' ' + r.archived).toLowerCase().indexOf(q) !== -1; 
        }); 
      }
      currentPage = 1;
      renderTable();
      renderPagination();
    }

    function renderTable(){
      var tbody = document.getElementById('archivedRoomsTable').getElementsByTagName('tbody')[0];
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
      var html = '<ul class="pagination">';
      function pageItem(p, label, disabled, active){
        var liCls = active ? 'active' : '';
        var btnCls = 'page-link' + (disabled ? ' disabled' : '');
        return '<li class="'+liCls+'"><button type="button" class="'+btnCls+'" data-page="'+p+'">'+label+'</button></li>';
      }
      html += pageItem(Math.max(1, currentPage-1), '&laquo;', currentPage===1, false);
      for (var p=1; p<=totalPages; p++){
        html += pageItem(p, p, false, p===currentPage);
      }
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

    var search = document.getElementById('adminSearch');
    if (search) search.addEventListener('input', applySearch);

    // Archived room details modal
    var archivedRoomDetailsModal = document.getElementById('archivedRoomDetailsModal');
    var closeArchivedRoomDetailsBtn = document.getElementById('closeArchivedRoomDetails');
    var closeArchivedRoomDetailsModalBtn = document.getElementById('closeArchivedRoomDetailsModal');

    function openArchivedRoomDetailsModal() { archivedRoomDetailsModal.style.display = 'flex'; }
    function closeArchivedRoomDetailsModal() { archivedRoomDetailsModal.style.display = 'none'; }

    if (closeArchivedRoomDetailsBtn) closeArchivedRoomDetailsBtn.addEventListener('click', closeArchivedRoomDetailsModal);
    if (closeArchivedRoomDetailsModalBtn) closeArchivedRoomDetailsModalBtn.addEventListener('click', closeArchivedRoomDetailsModal);

    archivedRoomDetailsModal.addEventListener('click', function(e){ if (e.target === archivedRoomDetailsModal) e.stopPropagation(); });

    // Row click opens details (ignore action buttons)
    document.querySelectorAll('.room-row').forEach(function(row){
      row.addEventListener('click', function(e){
        if (e.target.closest('button')) return;
        var d = this.dataset;
        populateArchivedRoomDetails({
          id: d.roomId,
          room: d.room,
          level_id: d.levelId,
          status: d.status,
          type: d.type,
          deleted_at: d.archived
        });
        openArchivedRoomDetailsModal();
      });
    });

    function populateArchivedRoomDetails(room){
      document.getElementById('arch-detail-room-number').textContent = room.room || '-';
      document.getElementById('arch-detail-level').textContent = room.level_id || '-';
      document.getElementById('arch-detail-status').textContent = room.status || '-';
      document.getElementById('arch-detail-type').textContent = room.type || '-';
      document.getElementById('arch-detail-archived').textContent = room.deleted_at ? new Date(room.deleted_at).toLocaleString() : '-';

      loadArchivedRoomAccommodations(room.id);
    }

    function loadArchivedRoomAccommodations(roomId){
      var el = document.getElementById('arch-detail-accommodations-list');
      el.innerHTML = '<div class="loading" style="display:flex;align-items:center;justify-content:center;gap:6px;color:#6c757d;font-style:italic;padding:20px;font-size:12px;"><i class="fas fa-spinner fa-spin" style="color: var(--purple-primary); font-size: 12px;"></i><span>Loading...</span></div>';

      fetch('/adminPages/rooms/' + roomId + '/accommodations')
        .then(function(res){ return res.json(); })
        .then(function(data){
          if (data.accommodations && data.accommodations.length){
            var html = '<div style="display:grid;gap:6px;max-height:150px;overflow-y:auto;padding-right:4px;">';
            data.accommodations.forEach(function(a){
              html += '<div class="accommodation-card" style="padding: 8px; background: linear-gradient(135deg, rgba(184,134,11,.05), rgba(184,134,11,.02)); border-radius: 8px; border-left: 3px solid var(--purple-primary); box-shadow: 0 2px 6px rgba(184,134,11,.08); transition: all 0.3s ease;">';
              html += '<div style=\'display:flex;align-items:center;gap:6px;\'>';
              html += '<div style=\'width:24px;height:24px;background:linear-gradient(135deg,var(--purple-primary),#DAA520);border-radius:6px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 6px rgba(184,134,11,.3);\'>';
              html += '<i class="fas fa-hotel" style=\'color:white;font-size:10px;\'></i>';
              html += '</div>';
              html += '<div>';
              html += '<strong style=\'color:var(--text-primary);font-size:12px;font-weight:700;display:block;\'>' + (a.name || '-') + '</strong>';
              html += '<small style=\'color:#6c757d;font-size:10px;\'>Capacity: ' + (a.capacity ?? '-') + '</small>';
              html += '</div>';
              html += '</div>';
              html += '</div>';
            });
            html += '</div>';
            el.innerHTML = html;
          } else {
            el.innerHTML = '<div style="text-align:center;padding:20px;color:#6c757d;"><div style="width:40px;height:40px;background:linear-gradient(135deg, rgba(184,134,11,.1), rgba(184,134,11,.05));border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;"><i class="fas fa-hotel" style="font-size:16px;color:var(--purple-primary);opacity:.6;"></i></div><h4 style="color:#6c757d;margin:0 0 4px 0;font-weight:600;font-size:12px;">No Accommodations</h4><p style="font-style:italic;margin:0;color:#6c757d;font-size:10px;">No accommodations found</p></div>';
          }
        })
        .catch(function(){
          el.innerHTML = '<div style="text-align:center;padding:20px;color:#dc3545;"><div style="width:40px;height:40px;background:linear-gradient(135deg, rgba(220,53,69,.1), rgba(220,53,69,.05));border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;"><i class="fas fa-exclamation-triangle" style="font-size:16px;color:#dc3545;"></i></div><h4 style="color:#dc3545;margin:0 0 4px 0;font-weight:600;font-size:12px;">Error</h4><p style="font-style:italic;margin:0;color:#dc3545;font-size:10px;">Failed to load</p></div>';
        });
    }

    // Restore functionality
    document.querySelectorAll('[data-restore]').forEach(function(btn){
      btn.addEventListener('click', function(e){
        e.stopPropagation();
        var roomId = this.getAttribute('data-room-id');
        var roomNumber = this.closest('tr').querySelector('td[data-label="Room No."]').textContent;
        
        if (confirm('Are you sure you want to restore "' + roomNumber + '"?')) {
          var form = document.createElement('form');
          form.method = 'POST';
          form.action = '/adminPages/rooms/restore/' + roomId;
          
          // CSRF
          var csrfToken = document.createElement('input');
          csrfToken.type = 'hidden';
          csrfToken.name = '_token';
          csrfToken.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                            document.querySelector('input[name="_token"]')?.value;
          
          // Method override for PATCH
          var methodField = document.createElement('input');
          methodField.type = 'hidden';
          methodField.name = '_method';
          methodField.value = 'PATCH';
          
          // Status field - set to Available
          var statusField = document.createElement('input');
          statusField.type = 'hidden';
          statusField.name = 'status';
          statusField.value = 'Available';
          
          form.appendChild(csrfToken);
          form.appendChild(methodField);
          form.appendChild(statusField);
          document.body.appendChild(form);
          form.submit();
        }
      });
    });
  })();
</script>

@endsection
