@extends('layouts.admindashboard')

@section('title', 'Payments Report')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/adminrecords.css') }}">
@endpush

@section('content')
<div class="dashboard-page">
  <div class="page-header">
    <h1 class="page-title">Payments</h1>
  </div>

  <style>
    /* Payments report aesthetics for filters and pagination (dash-style) */
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
    }
  </style>

  <div class="kpi-grid no-print" style="display:grid; grid-template-columns: repeat(auto-fit,minmax(180px,1fr)); gap:12px; margin-bottom:12px;">
    <div class="chart-card" style="padding:12px; border-left:3px solid var(--purple-primary);">
      <div class="section-header-pad" style="padding:0 0 6px 0; display:flex; align-items:center; gap:8px;">
        <i class="fas fa-receipt" style="color:var(--purple-primary);"></i>
        <h3 class="chart-title" style="margin:0; font-size:12px;">Payments</h3>
      </div>
      <div id="kpiCount" style="font-size:20px; font-weight:800;">0</div>
    </div>
    <div class="chart-card" style="padding:12px; border-left:3px solid #7f8c8d;">
      <div class="section-header-pad" style="padding:0 0 6px 0; display:flex; align-items:center; gap:8px;">
        <i class="fas fa-calculator" style="color:#7f8c8d;"></i>
        <h3 class="chart-title" style="margin:0; font-size:12px;">Subtotal</h3>
      </div>
      <div id="kpiSubtotal" style="font-size:20px; font-weight:800;">₱0.00</div>
    </div>
    <div class="chart-card" style="padding:12px; border-left:3px solid #f39c12;">
      <div class="section-header-pad" style="padding:0 0 6px 0; display:flex; align-items:center; gap:8px;">
        <i class="fas fa-receipt" style="color:#f39c12;"></i>
        <h3 class="chart-title" style="margin:0; font-size:12px;">Tax</h3>
      </div>
      <div id="kpiTax" style="font-size:20px; font-weight:800;">₱0.00</div>
    </div>
    <div class="chart-card" style="padding:12px; border-left:3px solid #27ae60;">
      <div class="section-header-pad" style="padding:0 0 6px 0; display:flex; align-items:center; gap:8px;">
        <i class="fas fa-coins" style="color:#27ae60;"></i>
        <h3 class="chart-title" style="margin:0; font-size:12px;">Total Amount</h3>
      </div>
      <div id="kpiTotal" style="font-size:20px; font-weight:800;">₱0.00</div>
    </div>
    <div class="chart-card" style="padding:12px; border-left:3px solid #8e44ad;">
      <div class="section-header-pad" style="padding:0 0 6px 0; display:flex; align-items:center; gap:8px;">
        <i class="fas fa-chart-line" style="color:#8e44ad;"></i>
        <h3 class="chart-title" style="margin:0; font-size:12px;">Avg / Payment</h3>
      </div>
      <div id="kpiAvg" style="font-size:20px; font-weight:800;">₱0.00</div>
    </div>
  </div>

  <div class="chart-card card-tight no-print" style="margin-bottom:12px;">
    <div class="section-header-pad" style="display:flex; align-items:center; gap:8px;">
      <i class="fas fa-chart-area" style="color:var(--purple-primary);"></i>
      <h3 class="chart-title" style="margin:0;">Daily Payments Total</h3>
    </div>
    <div style="height:220px;">
      <canvas id="paymentsDailyChart" height="200"></canvas>
    </div>
  </div>

  <div class="printable-content">

  <div class="records-toolbar" style="margin-bottom: 12px;">
    <div class="search-container admin-search">
      <i class="fas fa-search search-icon"></i>
      <input id="adminSearch" type="text" placeholder="Search payments" class="search-input">
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

    <div class="table-wrapper" style="margin-top:12px;">
      <table class="table sortable-table" id="paymentsTable">
        <thead>
          <tr><th>User Full Name</th><th>Room</th><th>Accommodation </th><th>Subtotal</th><th>Tax</th><th>Total</th><th>Change</th><th>Date</th></tr>
        </thead>
        <tbody id="rows"></tbody>
      </table>
    </div>
    <nav class="pagination no-print" aria-label="Table pagination" id="pagination" style="display:none;"></nav>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function(){
  // State
  var allRows = [];
  var filteredRows = [];
  var currentPage = 1;
  var pageSize = 10;
  var dailyChart;

  // Utils
  function peso(n){ try { return '₱' + Number(n).toFixed(2); } catch(e){ return '₱0.00'; } }
  function fmtDate(s){ try { return new Date(s).toLocaleString(); } catch(e){ return s; } }
  function ymd(s){ try { var d=new Date(s); return d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0')+'-'+String(d.getDate()).padStart(2,'0'); } catch(e){ return String(s).slice(0,10); } }

  function applySearch(){
    var q = (document.getElementById('adminSearch').value||'').toLowerCase();
    if (!q) { filteredRows = allRows.slice(); return; }
    filteredRows = allRows.filter(function(r){
      var t = (''+r.user_name+' '+r.room_number+' '+r.accommodation_name+' '+r.subtotal+' '+r.tax+' '+r.amount+' '+r.change+' '+r.created_at).toLowerCase();
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
      tr.innerHTML = '<td colspan="8" class="text-center" style="padding:40px; color:#6c757d;">' +
                     '<i class="fas fa-credit-card" style="font-size:48px; margin-bottom:16px; display:block;"></i>' +
                     '<p style="margin:0; font-size:16px;">No payments found</p>' +
                     '<p style="margin:8px 0 0 0; font-size:14px; color:#adb5bd;">All payment history will appear here</p>' +
                     '</td>';
      tbody.appendChild(tr);
    } else {
      pageItems.forEach(function(r){
        var tr = document.createElement('tr');
        tr.innerHTML = '<td>'+r.user_name+'</td>'+
                       '<td>'+r.room_number+'</td>'+
                       '<td>'+r.accommodation_name+'</td>'+
                       '<td>'+peso(r.subtotal)+'</td>'+
                       '<td>'+peso(r.tax)+'</td>'+
                       '<td>'+peso(r.amount)+'</td>'+
                       '<td>'+peso(r.change)+'</td>'+
                       '<td>'+fmtDate(r.created_at)+'</td>';
        tbody.appendChild(tr);
      });
    }
  }

  function renderKpis(){
    var count = filteredRows.length;
    var subtotal = filteredRows.reduce(function(s,r){ return s + (Number(r.subtotal)||0); }, 0);
    var tax = filteredRows.reduce(function(s,r){ return s + (Number(r.tax)||0); }, 0);
    var total = filteredRows.reduce(function(s,r){ return s + (Number(r.amount)||0); }, 0);
    var avg = count ? (total / count) : 0;
    document.getElementById('kpiCount').textContent = count.toLocaleString();
    document.getElementById('kpiSubtotal').textContent = peso(subtotal);
    document.getElementById('kpiTax').textContent = peso(tax);
    document.getElementById('kpiTotal').textContent = peso(total);
    document.getElementById('kpiAvg').textContent = peso(avg);
  }

  function renderDailyChart(){
    var byDay = {};
    filteredRows.forEach(function(r){
      var k = ymd(r.created_at);
      byDay[k] = (byDay[k]||0) + (Number(r.amount)||0);
    });
    var labels = Object.keys(byDay).sort();
    var values = labels.map(function(k){ return byDay[k]; });
    var ctx = document.getElementById('paymentsDailyChart').getContext('2d');
    if (dailyChart) dailyChart.destroy();
    dailyChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Total',
          data: values,
          borderColor: 'rgba(138, 92, 246, 1)',
          backgroundColor: 'rgba(138, 92, 246, 0.12)',
          fill: true,
          tension: 0.3,
          pointRadius: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { x: { ticks: { autoSkip: true, maxTicksLimit: 10 } }, y: { beginAtZero: true } }
      }
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

  async function load(){
    var from = document.getElementById('from').value;
    var to = document.getElementById('to').value;
    const res = await fetch('/adminPages/reports/data?type=payments&from='+from+'&to='+to, { headers: { 'Accept':'application/json' }});
    const data = await res.json();
    allRows = (data.rows||[]);
    currentPage = 1;
    applySearch();
    renderTable();
    renderPagination();
    renderKpis();
    renderDailyChart();
  }

  // Quick filters
  function setRange(days){
    var to = new Date();
    var from = new Date();
    from.setDate(to.getDate() - days + 1);
    document.getElementById('from').value = from.toISOString().slice(0,10);
    document.getElementById('to').value = to.toISOString().slice(0,10);
    load();
  }

  // Automatic filter event listeners
  document.getElementById('from').addEventListener('change', load);
  document.getElementById('to').addEventListener('change', load);
  
  document.getElementById('adminSearch').addEventListener('input', function(){
    applySearch();
    currentPage = 1;
    renderTable();
    renderPagination();
    renderKpis();
    renderDailyChart();
  });

  // Print functionality
  document.getElementById('printBtn').addEventListener('click', function() {
    // Trigger print dialog
    window.print();
  });
  var qt = document.getElementById('quickToday');
  var q7 = document.getElementById('quick7');
  var q30 = document.getElementById('quick30');
  var qm = document.getElementById('quickMonth');
  if (qt) qt.addEventListener('click', function(){ setRange(1); });
  if (q7) q7.addEventListener('click', function(){ setRange(7); });
  if (q30) q30.addEventListener('click', function(){ setRange(30); });
  if (qm) qm.addEventListener('click', function(){
    var d = new Date();
    var y = d.getFullYear();
    var m = d.getMonth();
    var first = new Date(y, m, 1);
    var last = new Date(y, m+1, 0);
    document.getElementById('from').value = first.toISOString().slice(0,10);
    document.getElementById('to').value = last.toISOString().slice(0,10);
    load();
  });

  attachPaginationHandler();
  load();
})();
</script>
@endsection



