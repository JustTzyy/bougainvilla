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

  #pagination { 
    display: flex; 
    justify-content: center; 
    margin-top: 12px; 
    max-width: 100%;
    overflow-x: auto;
    padding: 8px 0;
  }
  #pagination ul.pagination { 
    display: flex; 
    gap: 4px; 
    list-style: none; 
    padding: 0; 
    margin: 0; 
    flex-wrap: nowrap;
    min-width: max-content;
  }
  #pagination .page-link {
    background: linear-gradient(135deg, #ffffff, #f8f9ff);
    border: 1px solid rgba(184,134,11,.2);
    color: var(--text-primary);
    padding: 8px 12px;
    border-radius: 8px;
    font-weight: 600;
    box-shadow: 0 2px 6px rgba(184,134,11,.08);
    transition: all .2s ease;
    white-space: nowrap;
    min-width: 40px;
    text-align: center;
  }
  #pagination .page-link:hover { 
    transform: translateY(-1px); 
    box-shadow: 0 4px 12px rgba(184,134,11,.15); 
    border-color: rgba(184,134,11,.35); 
  }
  #pagination li.active .page-link {
    background: linear-gradient(135deg, var(--purple-primary), #DAA520);
    color: #fff;
    border-color: transparent;
    box-shadow: 0 4px 16px rgba(184,134,11,.35);
  }
  #pagination .page-link.disabled { 
    opacity: .5; 
    cursor: not-allowed; 
    background: #f8f9fa;
    color: #6c757d;
  }
  #pagination .page-item.disabled .page-link {
    background: #f8f9fa;
    color: #6c757d;
    cursor: not-allowed;
  }

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

  /* Guest Details Modal Styling */
  .modal {
    animation: fadeIn 0.3s ease;
  }
  
  .modal-content {
    animation: slideIn 0.3s ease;
  }
  
  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }
  
  @keyframes slideIn {
    from { transform: translateY(-50px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
  }
  
  .close:hover {
    opacity: 1 !important;
  }
  
  .guest-detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #f0f0f0;
  }
  
  .guest-detail-item:last-child {
    border-bottom: none;
  }
  
  .guest-detail-label {
    font-weight: 600;
    color: var(--text-secondary);
    min-width: 120px;
  }
  
  .guest-detail-value {
    color: var(--text-primary);
    text-align: right;
    flex: 1;
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
        <input type="date" id="from" class="date-input form-input" value="{{ now()->subYear()->toDateString() }}" style="min-height:36px;">
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
<div id="guestModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); backdrop-filter: blur(4px);">
  <div class="modal-content" style="background-color: #fefefe; margin: 2% auto; padding: 0; border: none; border-radius: 16px; width: 90%; max-width: 800px; max-height: 90vh; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
    <div class="modal-header" style="background: linear-gradient(135deg, var(--purple-primary), #DAA520); color: white; padding: 20px 24px; border-radius: 16px 16px 0 0;">
      <h2 class="modal-title" style="margin: 0; font-size: 20px; font-weight: 700;">Guest Details</h2>
      <span class="close" id="closeModal" style="color: white; float: right; font-size: 28px; font-weight: bold; cursor: pointer; line-height: 1; opacity: 0.8; transition: opacity 0.2s;">&times;</span>
    </div>
    <div class="modal-body" style="padding: 24px; max-height: calc(90vh - 80px); overflow-y: auto;">
      <div id="guestDetailsContent">
        <!-- Guest details will be loaded here -->
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
    var html = '<div class="guests-section">';
    html += '<h4 style="margin: 0 0 16px 0; color: var(--text-primary);">Guest Information</h4>';
    html += '<div class="guest-card" style="background: #fff; border: 1px solid #e9ecef; border-radius: 10px; padding: 16px; margin-bottom: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">';
    html += '<h5 style="margin: 0 0 12px 0; color: var(--purple-primary);">Guest 1</h5>';
    html += '<div class="guest-detail-item"><span class="guest-detail-label">Name:</span><span class="guest-detail-value">' + (guestData.guest_name || 'N/A') + '</span></div>';
    html += '<div class="guest-detail-item"><span class="guest-detail-label">Phone:</span><span class="guest-detail-value">' + (guestData.guest_number || 'N/A') + '</span></div>';
    html += '<div class="guest-detail-item"><span class="guest-detail-label">Address:</span><span class="guest-detail-value">' + (guestData.address || 'N/A') + '</span></div>';
    html += '</div>';
    html += '</div>';

    document.getElementById('guestDetailsContent').innerHTML = html;
    document.getElementById('guestModal').style.display = 'flex';
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
  document.getElementById('closeModal').addEventListener('click', function() {
    document.getElementById('guestModal').style.display = 'none';
  });

  window.addEventListener('click', function(event) {
    if (event.target === document.getElementById('guestModal')) {
      document.getElementById('guestModal').style.display = 'none';
    }
  });

  // Close modal with Escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById('guestModal').style.display === 'flex') {
      document.getElementById('guestModal').style.display = 'none';
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




