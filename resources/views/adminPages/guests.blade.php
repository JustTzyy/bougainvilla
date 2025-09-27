@extends('layouts.admindashboard')
@section('title','Guests Report')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/adminrecords.css') }}">
@endpush

@section('content')

<style>
  /* Guests report aesthetics for filters and pagination (dash-style) */
  #filterForm .btn-primary {
    background: linear-gradient(135deg, var(--purple-primary), #DAA520);
    border: 0;
    color: #fff;
    padding: 10px 16px;
    border-radius: 10px;
    font-weight: 700;
    box-shadow: 0 6px 18px rgba(184,134,11,.25);
    transition: all .2s ease;
  }
  #filterForm .btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 24px rgba(184,134,11,.35);
  }
  #filterForm .btn-primary:active {
    transform: translateY(0);
    box-shadow: 0 4px 14px rgba(184,134,11,.25);
  }

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

  /* Modal Styles */
  .modal {
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    backdrop-filter: blur(5px);
  }

  .modal-content {
    animation: modalSlideIn 0.3s ease-out;
  }

  @keyframes modalSlideIn {
    from {
      opacity: 0;
      transform: translateY(-50px) scale(0.9);
    }
    to {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
  }

  /* Table row hover effect */
  .table tbody tr {
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .table tbody tr:hover {
    background: linear-gradient(135deg, rgba(184,134,11,.08), rgba(184,134,11,.12));
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(184,134,11,.15);
  }

  /* Print Styles */
  @media print {
    body * {
      visibility: hidden;
    }
    .printable-content, .printable-content * {
      visibility: visible;
    }
    .printable-content {
      position: absolute;
      left: 0;
      top: 0;
      width: 100%;
    }
    .no-print {
      display: none !important;
    }
    .table {
      border-collapse: collapse;
      width: 100%;
      font-size: 12px;
    }
    .table th, .table td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: left;
    }
    .table th {
      background-color: #f5f5f5;
      font-weight: bold;
    }
    .print-header {
      text-align: center;
      margin-bottom: 20px;
      border-bottom: 2px solid #333;
      padding-bottom: 10px;
    }
    .print-header h1 {
      margin: 0;
      font-size: 24px;
      color: #333;
    }
    .print-header p {
      margin: 5px 0 0 0;
      font-size: 14px;
      color: #666;
    }
  }
</style>
<div class="dashboard-page">
  <div class="page-header"><h1 class="page-title">Guests</h1></div>
  
  <div class="records-toolbar" style="margin-bottom: 12px;">
    <div class="search-container admin-search">
      <i class="fas fa-search search-icon"></i>
      <input id="adminSearch" type="text" placeholder="Search guests" class="search-input">
    </div>
    <div class="toolbar-actions">
      <button type="button" id="printBtn" class="btn btn-primary" style="background: linear-gradient(135deg, var(--purple-primary), #DAA520); border: 0; color: #fff; padding: 10px 16px; border-radius: 10px; font-weight: 700; box-shadow: 0 6px 18px rgba(184,134,11,.25); transition: all .2s ease;">
        <i class="fas fa-print" style="margin-right: 6px;"></i>Print Report
      </button>
    </div>
  </div>

  <div class="chart-card card-tight">
    <div class="section-header-pad no-print" style="display:flex; align-items:center; gap:8px;">
      <i class="fas fa-filter" style="color:var(--purple-primary);"></i>
      <h3 class="chart-title" style="margin:0;">Filters</h3>
    </div>
    <form class="filters-wrap no-print" id="filterForm" style="display:flex; flex-wrap:wrap; gap:12px; align-items:center; background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%); border:1px solid rgba(184,134,11,.15); padding:12px; border-radius:12px; box-shadow: 0 4px 14px rgba(184,134,11,.06);">
      <div style="display:flex; gap:8px; align-items:center; background: rgba(184,134,11,.06); border:1px solid rgba(184,134,11,.15); padding:8px 10px; border-radius:10px;">
        <span class="filter-label" style="font-weight:600; color:var(--text-secondary);">From</span>
        <input type="date" id="from" class="date-input form-input" value="{{ now()->subDays(29)->toDateString() }}" style="min-height:36px;">
      </div>
      <div style="display:flex; gap:8px; align-items:center; background: rgba(184,134,11,.06); border:1px solid rgba(184,134,11,.15); padding:8px 10px; border-radius:10px;">
        <span class="filter-label" style="font-weight:600; color:var(--text-secondary);">To</span>
        <input type="date" id="to" class="date-input form-input" value="{{ now()->toDateString() }}" style="min-height:36px;">
      </div>
    </form>
    
    <div class="printable-content">
      
      <div class="table-wrapper" style="margin-top:12px;">
        <table class="table sortable-table" id="guestsTable">
        <thead>
          <tr><th>Guest Full Name</th><th>Room</th><th>Accommodation</th><th>Check-in</th><th>Check-out</th><th>Date</th></tr>
        </thead>
        <tbody id="rows"></tbody>
      </table>
    </div>
    <nav class="pagination no-print" aria-label="Table pagination" id="pagination" style="display:none;"></nav>
    </div>
  </div>
</div>

<!-- Guest Details Modal -->
<div id="guestModal" class="modal" style="display: none;">
  <div class="modal-content" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); border-radius: 20px; box-shadow: 0 20px 60px rgba(184,134,11,.3); max-width: 600px; width: 90%;">
    <div class="modal-header" style="background: linear-gradient(135deg, var(--purple-primary), #DAA520); color: white; padding: 20px; border-radius: 20px 20px 0 0; display: flex; justify-content: space-between; align-items: center;">
      <h2 style="margin: 0; font-size: 24px; font-weight: 700;">
        <i class="fas fa-user" style="margin-right: 10px;"></i>Guest Details
      </h2>
      <button id="closeModal" style="background: none; border: none; color: white; font-size: 24px; cursor: pointer; padding: 5px;">
        <i class="fas fa-times"></i>
      </button>
    </div>
    <div class="modal-body" style="padding: 30px;">
      <div class="guest-info-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
        <div class="info-card" style="background: rgba(184,134,11,.08); border: 1px solid rgba(184,134,11,.2); border-radius: 15px; padding: 20px;">
          <div class="info-label" style="font-weight: 600; color: var(--text-secondary); margin-bottom: 8px; font-size: 14px;">Full Name</div>
          <div class="info-value" id="modalGuestName" style="font-size: 18px; font-weight: 700; color: var(--text-primary);"></div>
        </div>
        <div class="info-card" style="background: rgba(184,134,11,.08); border: 1px solid rgba(184,134,11,.2); border-radius: 15px; padding: 20px;">
          <div class="info-label" style="font-weight: 600; color: var(--text-secondary); margin-bottom: 8px; font-size: 14px;">Contact Number</div>
          <div class="info-value" id="modalGuestNumber" style="font-size: 18px; font-weight: 700; color: var(--text-primary);"></div>
        </div>
      </div>
      
      <div class="stay-info-section" style="margin-bottom: 25px;">
        <h3 style="color: var(--purple-primary); margin-bottom: 15px; font-size: 18px; font-weight: 700;">
          <i class="fas fa-bed" style="margin-right: 8px;"></i>Stay Information
        </h3>
        <div class="stay-info-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
          <div class="info-card" style="background: rgba(184,134,11,.08); border: 1px solid rgba(184,134,11,.2); border-radius: 15px; padding: 15px;">
            <div class="info-label" style="font-weight: 600; color: var(--text-secondary); margin-bottom: 5px; font-size: 12px;">Room</div>
            <div class="info-value" id="modalRoom" style="font-size: 16px; font-weight: 700; color: var(--text-primary);"></div>
          </div>
          <div class="info-card" style="background: rgba(184,134,11,.08); border: 1px solid rgba(184,134,11,.2); border-radius: 15px; padding: 15px;">
            <div class="info-label" style="font-weight: 600; color: var(--text-secondary); margin-bottom: 5px; font-size: 12px;">Accommodation</div>
            <div class="info-value" id="modalAccommodation" style="font-size: 16px; font-weight: 700; color: var(--text-primary);"></div>
          </div>
          <div class="info-card" style="background: rgba(184,134,11,.08); border: 1px solid rgba(184,134,11,.2); border-radius: 15px; padding: 15px;">
            <div class="info-label" style="font-weight: 600; color: var(--text-secondary); margin-bottom: 5px; font-size: 12px;">Check-in</div>
            <div class="info-value" id="modalCheckIn" style="font-size: 16px; font-weight: 700; color: var(--text-primary);"></div>
          </div>
          <div class="info-card" style="background: rgba(184,134,11,.08); border: 1px solid rgba(184,134,11,.2); border-radius: 15px; padding: 15px;">
            <div class="info-label" style="font-weight: 600; color: var(--text-secondary); margin-bottom: 5px; font-size: 12px;">Check-out</div>
            <div class="info-value" id="modalCheckOut" style="font-size: 16px; font-weight: 700; color: var(--text-primary);"></div>
          </div>
        </div>
      </div>

      <div class="address-section">
        <h3 style="color: var(--purple-primary); margin-bottom: 15px; font-size: 18px; font-weight: 700;">
          <i class="fas fa-map-marker-alt" style="margin-right: 8px;"></i>Address Information
        </h3>
        <div class="address-card" style="background: rgba(184,134,11,.08); border: 1px solid rgba(184,134,11,.2); border-radius: 15px; padding: 20px;">
          <div id="modalAddress" style="font-size: 16px; line-height: 1.6; color: var(--text-primary);"></div>
        </div>
      </div>
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

  // Utils
  function fmtDate(s){ try { return new Date(s).toLocaleString(); } catch(e){ return s; } }

  function applySearch(){
    var q = (document.getElementById('adminSearch').value||'').toLowerCase();
    if (!q) { filteredRows = allRows.slice(); return; }
    filteredRows = allRows.filter(function(r){
      var t = (''+r.guest_name+' '+r.room_number+' '+r.accommodation_name+' '+r.check_in+' '+r.check_out+' '+r.date).toLowerCase();
      return t.indexOf(q) !== -1;
    });
  }

  function renderTable(){
    var tbody = document.getElementById('rows');
    tbody.innerHTML = '';
    var start = (currentPage - 1) * pageSize;
    var pageItems = filteredRows.slice(start, start + pageSize);
    
    if (pageItems.length === 0) {
      var tr = document.createElement('tr');
      tr.innerHTML = '<td colspan="6" class="text-center" style="padding:40px; color:#6c757d;">' +
                     '<i class="fas fa-users" style="font-size:48px; margin-bottom:16px; display:block;"></i>' +
                     '<p style="margin:0; font-size:16px;">No guests found</p>' +
                     '<p style="margin:8px 0 0 0; font-size:14px; color:#adb5bd;">All guest history will appear here</p>' +
                     '</td>';
      tbody.appendChild(tr);
    } else {
      pageItems.forEach(function(r){
        var tr = document.createElement('tr');
        tr.innerHTML = '<td>'+r.guest_name+'</td>'+
                       '<td>'+r.room_number+'</td>'+
                       '<td>'+r.accommodation_name+'</td>'+
                       '<td>'+r.check_in+'</td>'+
                       '<td>'+r.check_out+'</td>'+
                       '<td>'+r.date+'</td>';
        tr.addEventListener('click', function() {
          showGuestModal(r);
        });
        tbody.appendChild(tr);
      });
    }
  }

  function showGuestModal(guestData) {
    document.getElementById('modalGuestName').textContent = guestData.guest_name;
    document.getElementById('modalGuestNumber').textContent = guestData.guest_number;
    document.getElementById('modalRoom').textContent = guestData.room_number;
    document.getElementById('modalAccommodation').textContent = guestData.accommodation_name;
    document.getElementById('modalCheckIn').textContent = guestData.check_in;
    document.getElementById('modalCheckOut').textContent = guestData.check_out;
    document.getElementById('modalAddress').textContent = guestData.address;
    
    document.getElementById('guestModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }

  function hideGuestModal() {
    document.getElementById('guestModal').style.display = 'none';
    document.body.style.overflow = 'auto';
  }

  function renderPagination(){
    var container = document.getElementById('pagination');
    var totalPages = Math.max(1, Math.ceil(filteredRows.length / pageSize));
    if (totalPages <= 1) { 
      container.style.display = 'none'; 
      container.innerHTML=''; 
      return; 
    }
    container.style.display = 'flex';
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
      if (!btn || btn.classList.contains('disabled')) return;
      currentPage = parseInt(btn.getAttribute('data-page')) || 1;
      renderTable();
      renderPagination();
    });
  }

  async function load(){
    var from = document.getElementById('from').value;
    var to = document.getElementById('to').value;
    const res = await fetch('/adminPages/reports/data?type=guests&from='+from+'&to='+to, { headers: { 'Accept':'application/json' }});
    const data = await res.json();
    allRows = (data.rows||[]);
    console.log('Total guests loaded:', allRows.length);
    console.log('Page size:', pageSize);
    console.log('Total pages:', Math.ceil(allRows.length / pageSize));
    currentPage = 1;
    applySearch();
    renderTable();
    renderPagination();
  }

  // Automatic filter event listeners
  document.getElementById('from').addEventListener('change', load);
  document.getElementById('to').addEventListener('change', load);
  
  document.getElementById('adminSearch').addEventListener('input', function(){
    applySearch();
    currentPage = 1;
    renderTable();
    renderPagination();
  });

  // Modal event listeners
  document.getElementById('closeModal').addEventListener('click', hideGuestModal);
  document.getElementById('guestModal').addEventListener('click', function(e) {
    if (e.target === this) {
      hideGuestModal();
    }
  });

  // Close modal with Escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById('guestModal').style.display === 'flex') {
      hideGuestModal();
    }
  });

  // Print functionality
  document.getElementById('printBtn').addEventListener('click', function() {
    // Trigger print dialog
    window.print();
  });

  attachPaginationHandler();
  load();
})();
</script>
@endsection




