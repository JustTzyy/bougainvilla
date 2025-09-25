@extends('layouts.admindashboard')

@section('title', 'Logs Report')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/adminrecords.css') }}">
@endpush

@section('content')
<style>
  /* Logs Report Styling */
  .log-row:hover {
    background: #f8f9fa;
  }
  
  .text-muted {
    color: #6c757d;
    font-style: italic;
  }

  .log-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .badge-login {
    background-color: #d4edda;
    color: #155724;
  }

  .badge-logout {
    background-color: #f8d7da;
    color: #721c24;
  }

  /* Filter styling for logs report */
  #filterForm .btn-primary {
    background: linear-gradient(135deg, var(--purple-primary), #a29bfe);
    border: 0;
    color: #fff;
    padding: 10px 16px;
    border-radius: 10px;
    font-weight: 700;
    box-shadow: 0 6px 18px rgba(138,92,246,.25);
    transition: all .2s ease;
  }
  #filterForm .btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 24px rgba(138,92,246,.35);
  }
  #filterForm .btn-primary:active {
    transform: translateY(0);
    box-shadow: 0 4px 14px rgba(138,92,246,.25);
  }

  /* Pagination styling for logs report */
  #pagination { display: flex; justify-content: center; margin-top: 12px; }
  #pagination ul.pagination { display: flex; gap: 6px; list-style: none; padding: 0; margin: 0; }
  #pagination .page-link {
    background: linear-gradient(135deg, #ffffff, #f8f9ff);
    border: 1px solid rgba(138,92,246,.2);
    color: var(--text-primary);
    padding: 8px 12px;
    border-radius: 10px;
    font-weight: 700;
    box-shadow: 0 3px 10px rgba(138,92,246,.08);
    transition: all .2s ease;
  }
  #pagination .page-link:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(138,92,246,.15); border-color: rgba(138,92,246,.35); }
  #pagination li.active .page-link {
    background: linear-gradient(135deg, var(--purple-primary), #a29bfe);
    color: #fff;
    border-color: transparent;
    box-shadow: 0 8px 22px rgba(138,92,246,.35);
  }
  #pagination .page-link.disabled { opacity: .5; cursor: not-allowed; }

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

<div class="dashboard-page">
  <div class="page-header">
    <h1 class="page-title">Logs Report</h1>
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
      <input id="logsSearch" type="text" placeholder="Search login/logout logs" class="search-input">
    </div>
    <div class="toolbar-actions">
      <button type="button" id="printBtn" class="btn btn-primary" style="background: linear-gradient(135deg, var(--purple-primary), #a29bfe); border: 0; color: #fff; padding: 10px 16px; border-radius: 10px; font-weight: 700; box-shadow: 0 6px 18px rgba(138,92,246,.25); transition: all .2s ease;">
        <i class="fas fa-print" style="margin-right: 6px;"></i>Print Report
      </button>
    </div>
  </div>

  <div class="printable-content">
    <div class="chart-card card-tight no-print">
      <div class="section-header-pad" style="display:flex; align-items:center; gap:8px;">
        <i class="fas fa-filter" style="color:var(--purple-primary);"></i>
        <h3 class="chart-title" style="margin:0;">Filters</h3>
      </div>
      <form class="filters-wrap" id="filterForm" style="display:flex; flex-wrap:wrap; gap:12px; align-items:center; background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%); border:1px solid rgba(138,92,246,.15); padding:12px; border-radius:12px; box-shadow: 0 4px 14px rgba(138,92,246,.06);">
        <div style="display:flex; gap:8px; align-items:center; background: rgba(138,92,246,.06); border:1px solid rgba(138,92,246,.15); padding:8px 10px; border-radius:10px;">
          <span class="filter-label" style="font-weight:600; color:var(--text-secondary);">From</span>
          <input type="date" id="from" class="date-input form-input" value="{{ now()->subDays(29)->toDateString() }}" style="min-height:36px;">
        </div>
        <div style="display:flex; gap:8px; align-items:center; background: rgba(138,92,246,.06); border:1px solid rgba(138,92,246,.15); padding:8px 10px; border-radius:10px;">
          <span class="filter-label" style="font-weight:600; color:var(--text-secondary);">To</span>
          <input type="date" id="to" class="date-input form-input" value="{{ now()->toDateString() }}" style="min-height:36px;">
        </div>
      </form>
    </div>

    <div class="chart-card card-tight">
      <div class="section-header-pad no-print" style="margin-top:16px;">
        <h3 class="chart-title">Login/Logout Logs</h3>
      </div>

      <div class="table-wrapper">
        <table class="table sortable-table" id="logsTable">
          <thead>
            <tr>
              <th>User</th>
              <th>Activity</th>
              <th>Type</th>
              <th>Timestamp</th>
            </tr>
          </thead>
          <tbody>
            @if(isset($logs) && $logs->count() > 0)
              @foreach($logs as $log)
                <tr class="log-row"
                    data-user-name="{{ $log->user ? $log->user->name : 'Unknown User' }}"
                    data-activity="{{ $log->status }}"
                    data-type="{{ $log->getActivityType() }}"
                    data-timestamp="{{ $log->created_at }}">
                  <td data-label="User">
                    <div>
                      <div style="font-weight: 600;">{{ $log->user ? $log->user->name : 'Unknown User' }}</div>
                    </div>
                  </td>
                  <td data-label="Activity">
                    <div style="font-weight: 500;">{{ $log->status }}</div>
                  </td>
                  <td data-label="Type">
                    <span>
                      {{ $log->getActivityType() }}
                    </span>
                  </td>
                  <td data-label="Timestamp">
                    @if($log->timeIn && $log->timeIn instanceof \Carbon\Carbon)
                      <div style="font-weight: 500;">{{ $log->timeIn->format('M d, Y') }}</div>
                      <div style="font-size: 11px; color: #6c757d;">{{ $log->timeIn->format('H:i:s') }}</div>
                    @elseif($log->timeOut && $log->timeOut instanceof \Carbon\Carbon)
                      <div style="font-weight: 500;">{{ $log->timeOut->format('M d, Y') }}</div>
                      <div style="font-size: 11px; color: #6c757d;">{{ $log->timeOut->format('H:i:s') }}</div>
                    @else
                      <div style="font-weight: 500;">{{ $log->created_at->format('M d, Y') }}</div>
                      <div style="font-size: 11px; color: #6c757d;">{{ $log->created_at->format('H:i:s') }}</div>
                    @endif
                  </td>
                </tr>
              @endforeach
            @else
              <tr>
                <td colspan="4" class="text-center" style="padding:40px; color:#6c757d;">
                  <i class="fas fa-sign-in-alt" style="font-size:48px; margin-bottom:16px; display:block;"></i>
                  <p style="margin:0; font-size:16px;">No login/logout logs found</p>
                  <p style="margin:8px 0 0 0; font-size:14px; color:#adb5bd;">User login and logout activities will appear here</p>
                </td>
              </tr>
            @endif
          </tbody>
        </table>
      </div>
      <nav class="pagination no-print" aria-label="Table pagination" id="pagination" style="display:none;"></nav>
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
    var table = document.getElementById('logsTable').getElementsByTagName('tbody')[0];
    var rows = Array.from(table.rows);
    
    // Convert table rows to data objects
    allRows = rows.map(function(row) {
      var cells = row.cells;
      var rowData = {
        user_name: row.getAttribute('data-user-name') || (cells[0] ? cells[0].textContent.trim() : ''),
        activity: row.getAttribute('data-activity') || (cells[1] ? cells[1].textContent.trim() : ''),
        type: row.getAttribute('data-type') || (cells[2] ? cells[2].textContent.trim() : ''),
        timestamp: row.getAttribute('data-timestamp') || (cells[3] ? cells[3].textContent.trim() : ''),
        element: row
      };
      
      return rowData;
    });

    // Initialize filteredRows with all rows
    filteredRows = allRows.slice();

     function applySearch(){
       var search = document.getElementById('logsSearch');
       var q = (search ? search.value : '').toLowerCase();
       
       // First apply filters, then search within filtered results
       applyFilters();
       
       if (q) {
         filteredRows = filteredRows.filter(function(r){
           var t = (''+r.user_name+' '+r.activity+' '+r.type+' '+r.timestamp).toLowerCase();
           return t.indexOf(q) !== -1;
         });
       }
       
       currentPage = 1;
       renderTable();
       renderPagination();
     }

    function renderTable(){
      var tbody = document.getElementById('logsTable').getElementsByTagName('tbody')[0];
      tbody.innerHTML = '';
      var start = (currentPage - 1) * pageSize;
      var pageItems = filteredRows.slice(start, start + pageSize);
      
      if (pageItems.length === 0) {
        var tr = document.createElement('tr');
        tr.innerHTML = '<td colspan="4" class="text-center" style="padding:40px; color:#6c757d;">' +
                       '<i class="fas fa-sign-in-alt" style="font-size:48px; margin-bottom:16px; display:block;"></i>' +
                       '<p style="margin:0; font-size:16px;">No login/logout logs found</p>' +
                       '<p style="margin:8px 0 0 0; font-size:14px; color:#adb5bd;">User login and logout activities will appear here</p>' +
                       '</td>';
        tbody.appendChild(tr);
      } else {
        pageItems.forEach(function(r){
          tbody.appendChild(r.element);
        });
      }
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
        if (!btn || btn.classList.contains('disabled')) return;
        currentPage = parseInt(btn.getAttribute('data-page')) || 1;
        renderTable();
        renderPagination();
      });
    }

    // Client-side search
    var search = document.getElementById('logsSearch');
    if (search) search.addEventListener('input', function(){
      applySearch();
      renderPagination();
    });

     // Filter functionality
     function applyFilters() {
       var fromDate = document.getElementById('from').value;
       var toDate = document.getElementById('to').value;
       
       filteredRows = allRows.filter(function(r) {
         var matchesDate = true;
         
         // Date filtering (if dates are provided)
         if (fromDate || toDate) {
           // Parse the timestamp (format: "2025-09-25 16:10:05")
           var rowDate = new Date(r.timestamp);
           
           if (fromDate) {
             var from = new Date(fromDate);
             from.setHours(0, 0, 0, 0); // Start of day
             matchesDate = matchesDate && rowDate >= from;
           }
           if (toDate) {
             var to = new Date(toDate);
             to.setHours(23, 59, 59, 999); // End of day
             matchesDate = matchesDate && rowDate <= to;
           }
         }
         
         return matchesDate;
       });
       
       currentPage = 1;
       renderTable();
       renderPagination();
     }

     // Filter inputs event listeners
     document.getElementById('from').addEventListener('change', applyFilters);
     document.getElementById('to').addEventListener('change', applyFilters);

     // Print functionality
     document.getElementById('printBtn').addEventListener('click', function() {
       // Trigger print dialog
       window.print();
     });

     attachPaginationHandler();
     renderTable();
     renderPagination();
  })();
</script>
@endsection
