@extends('layouts.admindashboard')

@section('title', 'My Activity Logs')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/adminrecords.css') }}">
@endpush

@section('content')
<style>
  /* Activity Logs Styling */
  .activity-row:hover {
    background: #f8f9fa;
  }
  
  .activity-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .badge-created {
    background-color: #d4edda;
    color: #155724;
  }

  .badge-updated {
    background-color: #fff3cd;
    color: #856404;
  }

  .badge-deleted {
    background-color: #f8d7da;
    color: #721c24;
  }

  .badge-restored {
    background-color: #cce5ff;
    color: #004085;
  }

  .badge-login {
    background-color: #e2e3e5;
    color: #383d41;
  }

  .badge-logout {
    background-color: #f8d7da;
    color: #721c24;
  }

  /* Pagination styling for activity logs */
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

  /* Custom Pagination Styling */
  .custom-pagination {
    background: #ffffff;
    border-radius: 15px;
    padding: 8px;
    box-shadow: 0 4px 20px rgba(184,134,11,.1);
    display: inline-block;
  }
  .custom-pagination .pagination {
    display: flex;
    gap: 4px;
    list-style: none;
    padding: 0;
    margin: 0;
    align-items: center;
  }
  .custom-pagination .pagination li {
    display: inline-block;
  }
  .custom-pagination .pagination .page-link {
    background: #ffffff;
    border: 1px solid rgba(184,134,11,.15);
    color: #6c757d;
    padding: 10px 14px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 14px;
    transition: all .2s ease;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
  }
  .custom-pagination .pagination .page-link:hover {
    background: rgba(184,134,11,.05);
    border-color: rgba(184,134,11,.25);
    color: #B8860B;
    text-decoration: none;
    transform: translateY(-1px);
  }
  .custom-pagination .pagination li.active .page-link {
    background: linear-gradient(135deg, #B8860B, #DAA520);
    color: #fff;
    border-color: transparent;
    box-shadow: 0 4px 15px rgba(184,134,11,.3);
  }
  .custom-pagination .pagination .page-link.disabled {
    opacity: .4;
    cursor: not-allowed;
    background: #f8f9fa;
  }
  .custom-pagination .pagination .page-link.disabled:hover {
    transform: none;
    background: #f8f9fa;
    border-color: rgba(184,134,11,.15);
    color: #6c757d;
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

<div class="dashboard-page">
  <div class="page-header">
    <h1 class="page-title">My Activity Logs</h1>
  </div>

  <div class="records-toolbar">
    <div class="search-container admin-search">
      <i class="fas fa-search search-icon"></i>
      <input id="activitySearch" type="text" placeholder="Search my activity logs" class="search-input">
    </div>
    <div class="toolbar-actions">
      <button type="button" id="printBtn" class="btn btn-primary" style="background: linear-gradient(135deg, var(--purple-primary), #DAA520); border: 0; color: #fff; padding: 10px 16px; border-radius: 10px; font-weight: 700; box-shadow: 0 6px 18px rgba(184,134,11,.25); transition: all .2s ease;">
        <i class="fas fa-print" style="margin-right: 6px;"></i>Print Report
      </button>
    </div>
  </div>

  <div class="chart-card card-tight no-print">
    <div class="section-header-pad" style="display:flex; align-items:center; gap:8px;">
      <i class="fas fa-filter" style="color:var(--purple-primary);"></i>
      <h3 class="chart-title" style="margin:0;">Filters</h3>
    </div>
    <form class="filters-wrap" id="filterForm" style="display:flex; flex-wrap:wrap; gap:12px; align-items:center; background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%); border:1px solid rgba(184,134,11,.15); padding:12px; border-radius:12px; box-shadow: 0 4px 14px rgba(184,134,11,.06);">
      <div style="display:flex; gap:8px; align-items:center; background: rgba(184,134,11,.06); border:1px solid rgba(184,134,11,.15); padding:8px 10px; border-radius:10px;">
        <span class="filter-label" style="font-weight:600; color:var(--text-secondary);">From</span>
        <input type="date" id="from" class="date-input form-input" value="{{ request('from', now()->subDays(29)->toDateString()) }}" style="min-height:36px;">
      </div>
      <div style="display:flex; gap:8px; align-items:center; background: rgba(184,134,11,.06); border:1px solid rgba(184,134,11,.15); padding:8px 10px; border-radius:10px;">
        <span class="filter-label" style="font-weight:600; color:var(--text-secondary);">To</span>
        <input type="date" id="to" class="date-input form-input" value="{{ request('to', now()->toDateString()) }}" style="min-height:36px;">
      </div>
    </form>
  </div>

  <div class="printable-content">
    <div class="chart-card card-tight">
      <div class="section-header-pad">
        <h3 class="chart-title">List</h3>
      </div>

      <div class="table-wrapper">
        <table class="table sortable-table" id="activityLogsTable">
          <thead>
            <tr>
              <th>User</th>
              <th>Activity</th>
              <th>Type</th>
              <th>Timestamp</th>
            </tr>
          </thead>
          <tbody>
            @if(isset($histories) && $histories->count() > 0)
              @foreach($histories as $history)
                <tr class="activity-row">
                  <td data-label="User">
                    <div>
                      <div style="font-weight: 600;">{{ $history->user->name }}</div>
                      <div style="font-size: 11px; color: #6c757d;">{{ $history->user->roleID == 1 ? 'Admin' : 'Front Desk' }}</div>
                    </div>
                  </td>
                  <td data-label="Activity">
                    <div style="font-weight: 500;">{{ $history->status }}</div>
                  </td>
                  <td data-label="Type">
                    <span>
                      {{ $history->getActivityType() }}
                    </span>
                  </td>
                  <td data-label="Timestamp">
                    <div style="font-weight: 500;">{{ $history->created_at->format('M d, Y') }}</div>
                    <div style="font-size: 11px; color: #6c757d;">{{ $history->created_at->format('H:i:s') }}</div>
                  </td>
                </tr>
              @endforeach
            @else
              <tr>
                <td colspan="4" class="text-center" style="padding:40px; color:#6c757d;">
                  <i class="fas fa-history" style="font-size:48px; margin-bottom:16px; display:block;"></i>
                  <p style="margin:0; font-size:16px;">No activity logs found</p>
                  <p style="margin:8px 0 0 0; font-size:14px; color:#adb5bd;">Your activities will appear here</p>
                </td>
              </tr>
            @endif
          </tbody>
        </table>
      </div>
      <nav class="pagination no-print" aria-label="Table pagination" id="pagination" style="display:none;"></nav>
      
      <!-- Laravel Pagination -->
      @if(isset($histories) && $histories->hasPages())
        <div class="no-print" style="display: flex; justify-content: center; margin-top: 20px;">
          <div class="custom-pagination">
            <ul class="pagination">
              {{-- Previous Page Link --}}
              @if ($histories->onFirstPage())
                <li class="disabled">
                  <span class="page-link disabled">
                    <i class="fas fa-chevron-left"></i>
                  </span>
                </li>
              @else
                <li>
                  <a href="{{ $histories->previousPageUrl() }}" class="page-link" rel="prev">
                    <i class="fas fa-chevron-left"></i>
                  </a>
                </li>
              @endif

              {{-- Pagination Elements --}}
              @foreach ($histories->getUrlRange(1, $histories->lastPage()) as $page => $url)
                @if ($page == $histories->currentPage())
                  <li class="active">
                    <span class="page-link">{{ $page }}</span>
                  </li>
                @else
                  <li>
                    <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                  </li>
                @endif
              @endforeach

              {{-- Next Page Link --}}
              @if ($histories->hasMorePages())
                <li>
                  <a href="{{ $histories->nextPageUrl() }}" class="page-link" rel="next">
                    <i class="fas fa-chevron-right"></i>
                  </a>
                </li>
              @else
                <li class="disabled">
                  <span class="page-link disabled">
                    <i class="fas fa-chevron-right"></i>
                  </span>
                </li>
              @endif
            </ul>
          </div>
        </div>
      @endif
    </div>
  </div>
</div>

<script>
  (function(){
    // State
    var allRows = [];
    var filteredRows = [];

    // Get all rows from the table
    var table = document.getElementById('activityLogsTable').getElementsByTagName('tbody')[0];
    var rows = Array.from(table.rows);
    
    // Convert table rows to data objects
    allRows = rows.map(function(row) {
      var cells = row.cells;
      return {
        user_name: cells[0] ? cells[0].textContent.trim() : '',
        activity: cells[1] ? cells[1].textContent.trim() : '',
        type: cells[2] ? cells[2].textContent.trim() : '',
        timestamp: cells[3] ? cells[3].textContent.trim() : '',
        element: row
      };
    });

    function applySearch(){
      var search = document.getElementById('activitySearch');
      var q = (search ? search.value : '').toLowerCase();
      if (!q) { 
        filteredRows = allRows.slice(); 
      } else {
        filteredRows = allRows.filter(function(r){
          var t = (''+r.user_name+' '+r.activity+' '+r.type+' '+r.timestamp).toLowerCase();
          return t.indexOf(q) !== -1;
        });
      }
      renderTable();
    }

    function renderTable(){
      var tbody = document.getElementById('activityLogsTable').getElementsByTagName('tbody')[0];
      tbody.innerHTML = '';
      
      if (filteredRows.length === 0) {
        var tr = document.createElement('tr');
        tr.innerHTML = '<td colspan="4" class="text-center" style="padding:40px; color:#6c757d;">' +
                       '<i class="fas fa-history" style="font-size:48px; margin-bottom:16px; display:block;"></i>' +
                       '<p style="margin:0; font-size:16px;">No activity logs found</p>' +
                       '<p style="margin:8px 0 0 0; font-size:14px; color:#adb5bd;">Your activities will appear here</p>' +
                       '</td>';
        tbody.appendChild(tr);
      } else {
        filteredRows.forEach(function(r){
          tbody.appendChild(r.element);
        });
      }
    }


    // Automatic filter event listeners
    document.getElementById('from').addEventListener('change', function() {
      var from = this.value;
      var to = document.getElementById('to').value;
      window.location.href = '{{ route("adminPages.auditlogs") }}?from=' + from + '&to=' + to;
    });
    
    document.getElementById('to').addEventListener('change', function() {
      var from = document.getElementById('from').value;
      var to = this.value;
      window.location.href = '{{ route("adminPages.auditlogs") }}?from=' + from + '&to=' + to;
    });

    // Client-side search
    var search = document.getElementById('activitySearch');
    if (search) search.addEventListener('input', applySearch);

    applySearch();

    // Print functionality
    document.getElementById('printBtn').addEventListener('click', function() {
      // Trigger print dialog
      window.print();
    });
  })();
</script>
@endsection
