@extends('layouts.admindashboard')

@section('title','Archived Rate Records')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/archiveadminrecords.css') }}">
@endpush

@section('content')
<div class="dashboard-page">
  <div class="page-header">
    <h1 class="page-title">Archived Rates</h1>
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
      <input id="archiveSearch" type="text" placeholder="Search archived rates" class="search-input">
    </div>
    <div class="toolbar-actions">
      <a href="{{ route('adminPages.rates') }}" class="archive-btn">
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
      <table class="table sortable-table" id="archivedTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Duration</th>
            <th>Price</th>
            <th>Status</th>
            <th>Date Archived</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @if(isset($rates) && $rates->count() > 0)
            @foreach($rates as $rate)
              <tr class="rate-row"
                  data-id="{{ $rate->id }}"
                  data-duration="{{ $rate->duration }}"
                  data-price="{{ $rate->price }}"
                  data-accommodations="{{ $rate->accommodations->pluck('name')->implode(', ') }}"
                  data-status="{{ $rate->status ?? 'Archived' }}"
                  data-archived="{{ $rate->deleted_at }}">
                <td data-label="ID">{{ $rate->id }}</td>
                <td data-label="Duration">{{ $rate->duration }}</td>
                <td data-label="Price">₱{{ number_format($rate->price, 2) }}</td>
                <td data-label="Status">{{ $rate->status }}</td>
                <td data-label="Date Archived">{{ optional($rate->deleted_at)->format('M d, Y H:i') }}</td>
                <td data-label="Actions">
                  <button class="action-btn small" data-restore> 
                    <i class="fas fa-undo"></i> 
                  </button>
                </td>
              </tr>
            @endforeach
          @else
            <tr>
              <td colspan="6" class="text-center">No archived rates found</td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>
    
  </div>
</div>

<!-- Rate Details Modal (for archived rates) -->
<div id="archivedRateDetailsModal" class="modal">
  <div class="modal-card user-details-card">
    <div class="modal-header" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); border-bottom: 1px solid rgba(184,134,11,.15);">
      <h3 class="chart-title" style="color: var(--purple-primary); font-size: 18px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px;">
        <i class="fas fa-tags" style="background: linear-gradient(135deg, var(--purple-primary), #DAA520); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-size: 20px;"></i>
        Rate Details
      </h3>
      <button id="closeArchivedRateDetailsModal" class="action-btn ml-auto"><i class="fas fa-times"></i></button>
    </div>
    
    <div class="user-details-content" style="padding: 8px; background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);">
      <div class="user-info-section" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); border-radius: 10px; padding: 8px; margin-bottom: 8px; box-shadow: 0 2px 12px rgba(184,134,11,.08); border: 1px solid rgba(184,134,11,.1);">
        <h4 style="color: var(--purple-primary); font-size: 14px; font-weight: 700; margin: 0 0 8px 0; display: flex; align-items: center; gap: 6px; padding-bottom: 6px; border-bottom: 1px solid rgba(184,134,11,.15);">
          <i class="fas fa-info-circle" style="background: linear-gradient(135deg, var(--purple-primary), #DAA520); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-size: 14px;"></i>
          Rate Information
        </h4>
        <div class="info-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 8px;">
          <div class="info-item" style="background: rgba(184,134,11,.05); padding: 8px; border-radius: 8px; border-left: 3px solid var(--purple-primary);">
            <label style="display:block;font-size:10px;font-weight:600;color:#6c757d;">ID</label>
            <span id="arch-detail-id" style="font-size:14px;font-weight:700;color:var(--text-primary);">-</span>
          </div>
          <div class="info-item" style="background: rgba(184,134,11,.05); padding: 8px; border-radius: 8px; border-left: 3px solid var(--purple-primary);">
            <label style="display:block;font-size:10px;font-weight:600;color:#6c757d;">Duration</label>
            <span id="arch-detail-duration" style="font-size:14px;font-weight:700;color:var(--text-primary);">-</span>
          </div>
          <div class="info-item" style="background: rgba(184,134,11,.05); padding: 8px; border-radius: 8px; border-left: 3px solid var(--purple-primary);">
            <label style="display:block;font-size:10px;font-weight:600;color:#6c757d;">Price</label>
            <span id="arch-detail-price" style="font-size:14px;font-weight:700;color:var(--text-primary);">-</span>
          </div>
          <div class="info-item" style="background: rgba(184,134,11,.05); padding: 8px; border-radius: 8px; border-left: 3px solid var(--purple-primary);">
            <label style="display:block;font-size:10px;font-weight:600;color:#6c757d;">Status</label>
            <span id="arch-detail-status" style="font-size:14px;font-weight:700;color:var(--text-primary);">-</span>
          </div>
          <div class="info-item" style="background: rgba(184,134,11,.05); padding: 8px; border-radius: 8px; border-left: 3px solid var(--purple-primary);">
            <label style="display:block;font-size:10px;font-weight:600;color:#6c757d;">Date Archived</label>
            <span id="arch-detail-archived" style="font-size:14px;font-weight:700;color:var(--text-primary);">-</span>
          </div>
        </div>
      </div>

      <div class="address-info-section" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); border-radius: 10px; padding: 8px; box-shadow: 0 2px 12px rgba(184,134,11,.08); border: 1px solid rgba(184,134,11,.1);">
        <h4 style="color: var(--purple-primary); font-size: 12px; font-weight: 700; margin: 0 0 6px 0; display: flex; align-items: center; gap: 4px; padding-bottom: 4px; border-bottom: 1px solid rgba(184,134,11,.15);">
          <i class="fas fa-hotel" style="background: linear-gradient(135deg, var(--purple-primary), #DAA520); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-size: 12px;"></i>
          Accommodations
        </h4>
        <div class="info-item span-2" style="grid-column: span 2;">
          <div id="arch-detail-accommodations" style="background: rgba(184,134,11,.03); border-radius: 6px; padding: 6px; min-height: 50px; border: 1px dashed rgba(184,134,11,.2);"></div>
        </div>
      </div>
    </div>
    
    <div class="modal-actions" style="padding: 8px; background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%); border-top: 1px solid rgba(184,134,11,.15); border-radius: 0 0 16px 16px;">
      <button type="button" id="closeArchivedRateDetails" class="action-btn btn-outline" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); border: 2px solid var(--purple-primary); color: var(--purple-primary); padding: 12px 24px; border-radius: 12px; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(184,134,11,.1);">
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
        id: cells[0] ? cells[0].textContent.trim() : '',
        duration: cells[1] ? cells[1].textContent.trim() : '',
        price: cells[2] ? cells[2].textContent.trim() : '',
        status: cells[3] ? cells[3].textContent.trim() : '',
        archived: cells[4] ? cells[4].textContent.trim() : '',
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
          return (r.id + ' ' + r.duration + ' ' + r.price + ' ' + r.status + ' ' + r.archived).toLowerCase().indexOf(q) !== -1; 
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

    var search = document.getElementById('archiveSearch');
    if (search) search.addEventListener('input', applySearch);

    // Archived rate details modal
    var archModal = document.getElementById('archivedRateDetailsModal');
    var closeArchBtn = document.getElementById('closeArchivedRateDetails');
    var closeArchX = document.getElementById('closeArchivedRateDetailsModal');
    function openArchModal(){ archModal.style.display = 'flex'; }
    function closeArchModal(){ archModal.style.display = 'none'; }
    if (closeArchBtn) closeArchBtn.addEventListener('click', closeArchModal);
    if (closeArchX) closeArchX.addEventListener('click', closeArchModal);

    var rows = document.querySelectorAll('.rate-row');
    rows.forEach(function(row){
      row.addEventListener('click', function(e){
        if (e.target.closest('button')) return;
        var r = this.dataset;
        document.getElementById('arch-detail-id').textContent = r.id || '-';
        document.getElementById('arch-detail-duration').textContent = r.duration || '-';
        document.getElementById('arch-detail-price').textContent = r.price ? '₱' + parseFloat(r.price).toFixed(2) : '-';
        document.getElementById('arch-detail-status').textContent = r.status || '-';
        document.getElementById('arch-detail-archived').textContent = r.archived ? new Date(r.archived).toLocaleString() : '-';
        var accContainer = document.getElementById('arch-detail-accommodations');
        var names = (r.accommodations || '').split(',').filter(function(s){ return s.trim().length; });
        if (names.length) {
          var html = '<div style="display: grid; gap: 6px; max-height: 150px; overflow-y: auto; padding-right: 4px;">';
          names.forEach(function(name){
            html += '<div class="accommodation-card" style="padding: 8px; background: linear-gradient(135deg, rgba(184,134,11,.05), rgba(184,134,11,.02)); border-radius: 8px; border-left: 3px solid var(--purple-primary); box-shadow: 0 2px 6px rgba(184,134,11,.08); transition: all 0.3s ease; position: relative;">';
            html += '<div style="display:flex;align-items:center;gap:6px;">';
            html += '<div style="width: 24px; height: 24px; background: linear-gradient(135deg, var(--purple-primary), #DAA520); border-radius: 6px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(184,134,11,.3);">';
            html += '<i class="fas fa-hotel" style="color: white; font-size: 10px;"></i>';
            html += '</div>';
            html += '<strong style="color: var(--text-primary); font-size: 12px; font-weight: 700; display: block;">' + name.trim() + '</strong>';
            html += '</div></div>';
          });
          html += '</div>';
          accContainer.innerHTML = html;
        } else {
          accContainer.innerHTML = '<div style="text-align:center;color:#6c757d;font-size:10px;font-style:italic;">No accommodations</div>';
        }
        openArchModal();
      });
    });

    // Restore functionality
    document.querySelectorAll('[data-restore]').forEach(function(btn){
      btn.addEventListener('click', function(e){
        e.stopPropagation();
        var row = this.closest('tr');
        var id = row.dataset.id;
        var duration = row.dataset.duration;
        
        if (confirm('Are you sure you want to restore the rate for "' + duration + '"?')) {
          var form = document.createElement('form');
          form.method = 'POST';
          form.action = '/adminPages/rates/restore/' + id;
          
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
