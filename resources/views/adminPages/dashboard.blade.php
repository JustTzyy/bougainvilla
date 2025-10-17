@extends('layouts.admindashboard')

@section('title', 'Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/adminrecords.css') }}">
@endpush

@section('content')
<div class="dashboard-page">
  <div class="page-header">
    <h1 class="page-title">Dashboard Reports</h1>
  </div>

  <div class="records-toolbar">
    <div class="search-container admin-search">
      <i class="fas fa-search search-icon"></i>
      <input id="dashboardSearch" type="text" placeholder="Search dashboard data" class="search-input">
    </div>
    <div class="toolbar-actions">
      <button type="button" id="printBtn" class="btn btn-primary" style="background: linear-gradient(135deg, var(--purple-primary), #DAA520); border: 0; color: #fff; padding: 10px 16px; border-radius: 10px; font-weight: 700; box-shadow: 0 6px 18px rgba(184,134,11,.25); transition: all .2s ease;">
        <i class="fas fa-print" style="margin-right: 6px;"></i>Print Report
      </button>
    </div>
  </div>

  <div class="printable-content">
  <div class="chart-card card-tight">
    <div class="section-header-pad">
      <h3 class="chart-title">💰 Financial Revenue Summary</h3>
      <p style="margin: 4px 0 0 0; font-size: 12px; color: #6c757d; text-align: center;">Overview of total revenue, payments, and financial performance</p>
    </div>

    <style>
      .summary-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(180px,1fr)); gap:12px; margin-top:10px; }
      .summary-card { background:white; border:1px solid #eee; border-radius:12px; padding:14px; cursor:pointer; transition:.2s; box-shadow: 0 2px 10px rgba(0,0,0,.03); }
       .summary-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(26,26,26,.12); border-color: rgba(26,26,26,.35); }
      .summary-title { font-size:12px; color:#6c757d; text-transform:uppercase; letter-spacing:.3px; margin-bottom:6px; }
      .summary-value { font-size:20px; font-weight:800; color:#2b2d42; text-align:right; }
      .filters-wrap { display:flex; gap:10px; align-items:center; flex-wrap:wrap; padding:10px; background:linear-gradient(135deg,#f8f9ff 0%,#ffffff 100%); border:1px solid #eee; border-radius:12px; }
      .filter-label { font-size:12px; font-weight:700; color:#6c757d; }
      .date-input { appearance:none; padding:10px 12px; border:2px solid rgba(184,134,11,.2); border-radius:10px; background:white; transition:.2s; }
      .date-input:focus { outline:none; border-color: var(--purple-primary); box-shadow: 0 0 0 3px rgba(184,134,11,.15); }
      .btn-cta { background:linear-gradient(135deg, var(--purple-primary), #DAA520); color:white; border:none; border-radius:10px; padding:10px 16px; font-weight:700; cursor:pointer; box-shadow:0 6px 20px rgba(184,134,11,.25); transition:.2s; }
      .btn-cta:hover { transform: translateY(-2px); box-shadow:0 10px 28px rgba(184,134,11,.35); }
      .quick-filters { display:flex; gap:8px; margin-top:10px; flex-wrap:wrap; }
       .quick-filter { padding:8px 12px; border:1px solid rgba(184,134,11,.25); border-radius:999px; background:white; color:#8B0000; font-weight:700; cursor:pointer; transition:.2s; }
      .quick-filter:hover { background:linear-gradient(135deg,rgba(184,134,11,.08),rgba(184,134,11,.03)); }
      .panel { background:white; border:1px solid #eee; border-radius:12px; padding:12px; }

      /* Pagination Styles */
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
        .summary-grid {
          display: grid;
          grid-template-columns: repeat(5, 1fr);
          gap: 10px;
          margin-bottom: 20px;
        }
        .summary-card {
          border: 1px solid #ddd;
          padding: 10px;
          text-align: center;
          background: #f9f9f9;
        }
        .summary-title {
          font-size: 10px;
          font-weight: bold;
          margin-bottom: 5px;
          color: #666;
        }
        .summary-value {
          font-size: 14px;
          font-weight: bold;
          color: #333;
        }
        canvas {
          display: none !important;
        }
      }
    </style>

    <form id="reportFilter" class="filters-wrap no-print">
      <div style="display:flex; gap:8px; align-items:center;">
        <span class="filter-label">From</span>
        <input type="date" id="from" class="date-input" value="{{ isset($reportFrom) ? $reportFrom : now()->subDays(29)->toDateString() }}">
      </div>
      <div style="display:flex; gap:8px; align-items:center;">
        <span class="filter-label">To</span>
        <input type="date" id="to" class="date-input" value="{{ isset($reportTo) ? $reportTo : now()->toDateString() }}">
      </div>
      <div class="quick-filters">
      
      </div>
    </form>

    <div id="summary" class="summary-grid">
      <div class="summary-card clickable-card" data-route="{{ route('reports.payments') }}">
        <div class="summary-title">Subtotal</div>
        <div class="summary-value" id="sumSubtotal">₱0.00</div>
      </div>
      <div class="summary-card clickable-card" data-route="{{ route('reports.payments') }}">
        <div class="summary-title">Tax</div>
        <div class="summary-value" id="sumTax">₱0.00</div>
      </div>
      <div class="summary-card clickable-card" data-route="{{ route('reports.payments') }}">
        <div class="summary-title">Total Amount</div>
        <div class="summary-value" id="sumAmount">₱0.00</div>
      </div>
      <div class="summary-card clickable-card" data-route="{{ route('reports.payments') }}">
        <div class="summary-title">Payments</div>
        <div class="summary-value" id="sumCount">0</div>
      </div>
      <div class="summary-card clickable-card" data-route="{{ route('reports.payments') }}">
        <div class="summary-title">Average per Payment</div>
        <div class="summary-value" id="sumAvg">₱0.00</div>
      </div>
    </div>

    <div class="chart-card" style="margin-top:16px;">
      <div class="section-header-pad">
        <h3 class="chart-title">🏨 Hotel Operations KPIs</h3>
        <p style="margin: 4px 0 0 0; font-size: 12px; color: #6c757d; text-align: center;">Key performance indicators for room occupancy and guest management</p>
      </div>
      <div class="summary-grid" id="kpiGrid">
        <div class="summary-card clickable-card" data-route="{{ route('adminPages.transactions') }}"><div class="summary-title">Rooms Available</div><div class="summary-value" id="kpiAvail">0</div></div>
        <div class="summary-card clickable-card" data-route="{{ route('adminPages.transactions') }}"><div class="summary-title">Rooms Occupied</div><div class="summary-value" id="kpiOcc">0</div></div>
        <div class="summary-card clickable-card" data-route="{{ route('adminPages.transactions') }}"><div class="summary-title">Occupancy Rate</div><div class="summary-value" id="kpiOccRate">0%</div></div>
        <div class="summary-card clickable-card" data-route="{{ route('reports.guests') }}"><div class="summary-title">Check-ins</div><div class="summary-value" id="kpiIn">0</div></div>
        <div class="summary-card clickable-card" data-route="{{ route('reports.guests') }}"><div class="summary-title">Guests</div><div class="summary-value" id="kpiGuests">0</div></div>
      </div>
    </div>

    <div class="chart-card no-print" style="margin-top:16px;">
      <div class="section-header-pad">
        <h3 class="chart-title">📊 Daily Revenue Analytics</h3>
        <p style="margin: 4px 0 0 0; font-size: 12px; color: #6c757d; text-align: center;">Visual analysis of daily revenue trends with detailed data breakdown</p>
      </div>
      <div style="display:grid; grid-template-columns: 2fr 1fr; gap: 16px; align-items:start;">
        <div class="panel">
          <div style="text-align: center; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid #eee;">
            <h4 style="margin: 0; font-size: 14px; font-weight: 600; color: #2b2d42;">📈 Daily Revenue Trend</h4>
            <p style="margin: 0; font-size: 11px; color: #6c757d;">Revenue performance over time</p>
          </div>
          <canvas id="dailyLine" height="120"></canvas>
        </div>
        <div class="panel">
          <div style="text-align: center; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid #eee;">
            <h4 style="margin: 0; font-size: 14px; font-weight: 600; color: #2b2d42;">🥧 Revenue Breakdown</h4>
            <p style="margin: 0; font-size: 11px; color: #6c757d;">Subtotal vs Tax distribution</p>
          </div>
          <canvas id="breakdownPie" height="120"></canvas>
        </div>
      </div>
      <div id="dailyTable" class="table-wrapper">
        <div style="text-align: center; margin-bottom: 12px; padding: 12px; background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%); border-radius: 8px;">
          <h4 style="margin: 0; font-size: 16px; font-weight: 700; color: #2b2d42;">📊 Daily Revenue Data Table</h4>
          <p style="margin: 4px 0 0 0; font-size: 12px; color: #6c757d;">Detailed breakdown of daily financial performance</p>
        </div>
        <table class="table">
          <thead>
            <tr><th>📅 Date</th><th>💰 Subtotal</th><th>🏛️ Tax</th><th>💵 Total</th></tr>
          </thead>
          <tbody id="dailyRows"></tbody>
        </table>
        <!-- Client-side pagination -->
        <nav class="pagination no-print" aria-label="Table pagination" id="pagination" style="display:none;"></nav>
      </div>
    </div>

    <div class="chart-card no-print" style="margin-top:16px;">
      <div class="section-header-pad"><h3 class="chart-title">📈 Daily Operational Metrics</h3></div>
      <div class="panel">
        <div style="text-align: center; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid #eee;">
          <h4 style="margin: 0; font-size: 14px; font-weight: 600; color: #2b2d42;">🏨 Operational Performance Chart</h4>
          <p style="margin: 0; font-size: 11px; color: #6c757d;">Daily revenue tracking and operational insights</p>
        </div>
        <canvas id="opsLine" height="110"></canvas>
      </div>
    </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function(){
  function peso(n){
    try { return '₱' + Number(n).toFixed(2); } catch(e){ return '₱0.00'; }
  }
  var dailyChart, pieChart;
  
  // Pagination variables
  var currentPage = 1;
  var pageSize = 10;
  var filteredRows = [];
  var allDailyData = [];
  function render(data){
    if(!data || !data.success) return;
    document.getElementById('sumSubtotal').textContent = peso(data.totals.subtotal);
    document.getElementById('sumTax').textContent = peso(data.totals.tax);
    document.getElementById('sumAmount').textContent = peso(data.totals.amount);
    document.getElementById('sumCount').textContent = String(data.totals.count);
    document.getElementById('sumAvg').textContent = peso(data.totals.avg_amount);
    // Store all daily data for pagination
    allDailyData = data.daily || [];
    filteredRows = allDailyData.map(function(row) {
      var tr = document.createElement('tr');
      tr.innerHTML = '<td>'+row.day+'</td>'+
                     '<td>'+peso(row.subtotal)+'</td>'+
                     '<td>'+peso(row.tax)+'</td>'+
                     '<td>'+peso(row.amount)+'</td>';
      return { element: tr, data: row };
    });
    
    // Render table with pagination
    renderTable();
    renderPagination();
    alignDailyTable();

    // Ops line chart (checkins, checkouts, guests per day)
    var opsCtx = document.getElementById('opsLine').getContext('2d');
    var group = {};
    (data.daily || []).forEach(function(d){ group[d.day] = { amount: Number(d.amount||0), subtotal: Number(d.subtotal||0), tax: Number(d.tax||0) }; });
    // For ops, we’ll synthesize from daily revenue as placeholder, since API doesn't provide daily checkins; KPI totals still accurate by date range.
    var opsLabels = Object.keys(group);
    var opsGuests = opsLabels.map(function(){ return null; });
    var opsCheckins = opsLabels.map(function(){ return null; });
    var opsCheckouts = opsLabels.map(function(){ return null; });
    if (window.opsChart) window.opsChart.destroy();
    window.opsChart = new Chart(opsCtx, {
      type: 'line',
      data: { labels: opsLabels, datasets: [
        { label: 'Revenue', data: opsLabels.map(function(k){ return group[k].amount; }), borderColor:'#8B0000', backgroundColor:'rgba(139,0,0,.15)', tension:.25, fill:true },
      ]},
      options: { plugins:{ legend:{ position:'bottom' }}, scales:{ y:{ ticks:{ callback:function(v){ return '₱'+v; }}}}}
    });

    // Charts
    var labels = (data.daily || []).map(function(d){ return d.day; });
    var totals = (data.daily || []).map(function(d){ return Number(d.amount || 0); });
    var ctx = document.getElementById('dailyLine').getContext('2d');
    if (dailyChart) dailyChart.destroy();
    dailyChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Total Amount',
          data: totals,
          borderColor: '#1a1a1a',
          backgroundColor: 'rgba(184,134,11,0.15)',
          borderWidth: 2,
          tension: 0.25,
          pointRadius: 3,
          fill: true,
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: true },
          tooltip: { callbacks: { label: function(ctx){ return ' ' + peso(ctx.raw); } } }
        },
        scales: {
          y: { ticks: { callback: function(v){ return '₱' + v; } }, grid: { color: 'rgba(0,0,0,0.06)' } },
          x: { grid: { display:false } }
        }
      }
    });

    var pctx = document.getElementById('breakdownPie').getContext('2d');
    if (pieChart) pieChart.destroy();
    pieChart = new Chart(pctx, {
      type: 'doughnut',
      data: {
        labels: ['Subtotal', 'Tax'],
        datasets: [{
          data: [Number(data.totals.subtotal||0), Number(data.totals.tax||0)],
          backgroundColor: ['#8B0000', '#1a1a1a'],
          borderWidth: 0
        }]
      },
      options: {
        plugins: { legend: { position: 'bottom' } },
        cutout: '60%'
      }
    });
  }

  function renderTable(){
    var tbody = document.getElementById('dailyRows');
    tbody.innerHTML = '';
    var start = (currentPage - 1) * pageSize;
    var pageItems = filteredRows.slice(start, start + pageSize);
    pageItems.forEach(function(r){
      tbody.appendChild(r.element);
    });
    alignDailyTable();
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
      var page = parseInt(btn.getAttribute('data-page'));
      if (page && page !== currentPage) {
        currentPage = page;
        renderTable();
        renderPagination();
        alignDailyTable();
      }
    });
  }

  async function load(){
    var from = document.getElementById('from').value;
    var to = document.getElementById('to').value;
    const res = await fetch('/adminPages/dashboard?from='+from+'&to='+to, { headers: { 'Accept': 'application/json' }});
    const data = await res.json();
    render(data);
    // KPIs
    if(data.kpis){
      document.getElementById('kpiAvail').textContent = data.kpis.rooms_available;
      document.getElementById('kpiOcc').textContent = data.kpis.rooms_occupied;
      document.getElementById('kpiOccRate').textContent = (data.kpis.occupancy_rate||0) + '%';
      document.getElementById('kpiIn').textContent = data.kpis.checkins;
      document.getElementById('kpiGuests').textContent = data.kpis.guests || 0;
    }
  }
  // Automatic filter event listeners
  document.getElementById('from').addEventListener('change', load);
  document.getElementById('to').addEventListener('change', load);
  
  // Initial load with server-provided defaults
  load();
  
  // Navigate to Payments report when clicking any summary card
  document.querySelectorAll('.clickable-card').forEach(function(card) {
    card.addEventListener('click', function() {
      window.location.href = this.dataset.route;
    });
  });

  // Print functionality
  document.getElementById('printBtn').addEventListener('click', function() {
    // Trigger print dialog
    window.print();
  });

  // Search functionality
  function applySearch() {
    var searchTerm = document.getElementById('dashboardSearch').value.toLowerCase();
    var summaryCards = document.querySelectorAll('#summary .summary-card');
    var kpiCards = document.querySelectorAll('#kpiGrid .summary-card');
    var dailyRows = document.querySelectorAll('#dailyRows tr');
    
    // Search through summary cards
    summaryCards.forEach(function(card) {
      var title = card.querySelector('.summary-title').textContent.toLowerCase();
      var value = card.querySelector('.summary-value').textContent.toLowerCase();
      var isVisible = !searchTerm || title.includes(searchTerm) || value.includes(searchTerm);
      card.style.display = isVisible ? 'block' : 'none';
    });
    
    // Search through KPI cards
    kpiCards.forEach(function(card) {
      var title = card.querySelector('.summary-title').textContent.toLowerCase();
      var value = card.querySelector('.summary-value').textContent.toLowerCase();
      var isVisible = !searchTerm || title.includes(searchTerm) || value.includes(searchTerm);
      card.style.display = isVisible ? 'block' : 'none';
    });
    
    // Search through daily revenue table rows with pagination
    if (searchTerm) {
      filteredRows = allDailyData.filter(function(row) {
        var rowText = (row.day + ' ' + peso(row.subtotal) + ' ' + peso(row.tax) + ' ' + peso(row.amount)).toLowerCase();
        return rowText.includes(searchTerm);
      }).map(function(row) {
        var tr = document.createElement('tr');
        tr.innerHTML = '<td>'+row.day+'</td>'+
                       '<td>'+peso(row.subtotal)+'</td>'+
                       '<td>'+peso(row.tax)+'</td>'+
                       '<td>'+peso(row.amount)+'</td>';
        return { element: tr, data: row };
      });
    } else {
      filteredRows = allDailyData.map(function(row) {
        var tr = document.createElement('tr');
        tr.innerHTML = '<td>'+row.day+'</td>'+
                       '<td>'+peso(row.subtotal)+'</td>'+
                       '<td>'+peso(row.tax)+'</td>'+
                       '<td>'+peso(row.amount)+'</td>';
        return { element: tr, data: row };
      });
    }
    
    // Reset to first page and re-render
    currentPage = 1;
    renderTable();
    renderPagination();
    alignDailyTable();
  }

  // Auto-align daily table cells: numbers right, text left
  function isNumericValue(text){
    if (text == null) return false;
    var t = String(text).trim().replace(/[,\s]/g, '');
    if (t === '') return false;
    t = t.replace(/^[-₱$€¥£]/, '');
    return !isNaN(t) && isFinite(t);
  }
  function alignDailyTable(){
    try {
      var tbody = document.getElementById('dailyRows');
      Array.prototype.forEach.call(tbody.rows, function(row){
        Array.prototype.forEach.call(row.cells, function(cell, idx){
          var text = cell.textContent || '';
          cell.style.textAlign = isNumericValue(text) ? 'right' : 'left';
        });
      });
    } catch(e) {}
  }

  // Add search event listener
  document.getElementById('dashboardSearch').addEventListener('input', applySearch);
  
  // Initialize pagination handler
  attachPaginationHandler();
})();
</script>
@endsection
