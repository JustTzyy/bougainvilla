@extends('layouts.admindashboard')

@section('title','All Archive Transactions')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/adminrecords.css') }}">
@endpush

@section('content')
<style>
  /* All Archive Transactions Styling */
  
  .transaction-row:hover {
    background: #f8f9fa;
  }
  
  .text-muted {
    color: #6c757d;
    font-style: italic;
  }
</style>

<div class="dashboard-page">
  <div class="page-header">
    <h1 class="page-title">All Archive Transactions</h1>
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
      <input id="transactionSearch" type="text" placeholder="Search archived transactions" class="search-input">
    </div>
    <div class="toolbar-actions">
      <button type="button" id="printBtn" class="btn btn-primary" style="background: linear-gradient(135deg, var(--purple-primary), #a29bfe); border: 0; color: #fff; padding: 10px 16px; border-radius: 10px; font-weight: 700; box-shadow: 0 6px 18px rgba(138,92,246,.25); transition: all .2s ease;">
        <i class="fas fa-print" style="margin-right: 6px;"></i>Print Report
      </button>
    </div>
  </div>

  <style>
    /* Filter styling for all archive transactions */
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

    /* Pagination styling for all archive transactions */
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

  <div class="chart-card card-tight">
    <div class="section-header-pad no-print" style="display:flex; align-items:center; gap:8px;">
      <i class="fas fa-filter" style="color:var(--purple-primary);"></i>
      <h3 class="chart-title" style="margin:0;">Filters</h3>
    </div>
    <form class="filters-wrap no-print" id="filterForm" style="display:flex; flex-wrap:wrap; gap:12px; align-items:center; background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%); border:1px solid rgba(138,92,246,.15); padding:12px; border-radius:12px; box-shadow: 0 4px 14px rgba(138,92,246,.06);">
      <div style="display:flex; gap:8px; align-items:center; background: rgba(138,92,246,.06); border:1px solid rgba(138,92,246,.15); padding:8px 10px; border-radius:10px;">
        <span class="filter-label" style="font-weight:600; color:var(--text-secondary);">From</span>
        <input type="date" id="from" class="date-input form-input" value="{{ request('from', now()->subDays(29)->toDateString()) }}" style="min-height:36px;">
      </div>
      <div style="display:flex; gap:8px; align-items:center; background: rgba(138,92,246,.06); border:1px solid rgba(138,92,246,.15); padding:8px 10px; border-radius:10px;">
        <span class="filter-label" style="font-weight:600; color:var(--text-secondary);">To</span>
        <input type="date" id="to" class="date-input form-input" value="{{ request('to', now()->toDateString()) }}" style="min-height:36px;">
      </div>
    </form>

    <div class="printable-content">

      <div class="section-header-pad" style="margin-top:16px;">
        <h3 class="chart-title">List</h3>
      </div>

      <div class="table-wrapper">
        <table class="table sortable-table" id="allTransactionsTable">
        <thead>
          <tr>
            <th>User Full Name</th>
            <th>Room</th>
            <th>Accommodation</th>
            <th>Check-in</th>
            <th>Check-out</th>
            <th>Amount</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          @if(isset($archivedTransactions) && $archivedTransactions->count() > 0)
            @foreach($archivedTransactions as $transaction)
              <tr class="transaction-row"
                  data-transaction-id="{{ $transaction->id }}"
                  data-user-name="{{ $transaction->user_name }}"
                  data-room="{{ $transaction->room_number }}"
                  data-accommodation="{{ $transaction->accommodation_name }}"
                  data-checkin="{{ $transaction->check_in }}"
                  data-checkout="{{ $transaction->check_out }}"
                  data-amount="{{ $transaction->amount }}"
                  data-date="{{ $transaction->created_at }}">
                <td data-label="User Full Name">
                  {{ $transaction->user_name }}
                </td>
                <td data-label="Room">{{ $transaction->room_number }}</td>
                <td data-label="Accommodation">{{ $transaction->accommodation_name }}</td>
                <td data-label="Check-in">
                  @if($transaction->check_in && $transaction->check_in !== 'N/A')
                    {{ $transaction->check_in->format('M d, Y H:i') }}
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td data-label="Check-out">
                  @if($transaction->check_out && $transaction->check_out !== 'N/A')
                    {{ $transaction->check_out->format('M d, Y H:i') }}
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td data-label="Amount">
                  ₱{{ number_format($transaction->amount, 2) }}
                </td>
                <td data-label="Date">{{ $transaction->created_at->format('M d, Y H:i') }}</td>
              </tr>
            @endforeach
          @else
            <tr>
              <td colspan="7" class="text-center" style="padding:40px; color:#6c757d;">
                <i class="fas fa-archive" style="font-size:48px; margin-bottom:16px; display:block;"></i>
                <p style="margin:0; font-size:16px;">No archived transactions found</p>
                <p style="margin:8px 0 0 0; font-size:14px; color:#adb5bd;">All archived transaction history will appear here</p>
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

<!-- Guest Details Modal -->
<div id="guestDetailsModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); backdrop-filter: blur(4px);">
  <div class="modal-content" style="background-color: #fefefe; margin: 2% auto; padding: 0; border: none; border-radius: 16px; width: 90%; max-width: 800px; max-height: 90vh; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
    <div class="modal-header" style="background: linear-gradient(135deg, var(--purple-primary), #a29bfe); color: white; padding: 20px 24px; border-radius: 16px 16px 0 0;">
      <h2 class="modal-title" style="margin: 0; font-size: 20px; font-weight: 700;">Guest Details</h2>
      <span class="close" style="color: white; float: right; font-size: 28px; font-weight: bold; cursor: pointer; line-height: 1; opacity: 0.8; transition: opacity 0.2s;">&times;</span>
    </div>
    <div class="modal-body" style="padding: 24px; max-height: calc(90vh - 80px); overflow-y: auto;">
      <div id="guestDetailsContent">
        <!-- Guest details will be loaded here -->
      </div>
    </div>
  </div>
</div>

<style>
  /* Modal Styles */
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
  
  .transaction-row {
    cursor: pointer;
    transition: all 0.2s ease;
  }
  
  .transaction-row:hover {
    background: #f8f9ff !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(138,92,246,0.1);
  }
</style>

<script>
  (function(){
    // State
    var allRows = [];
    var filteredRows = [];
    var currentPage = 1;
    var pageSize = 10;

    // Get all rows from the table
    var table = document.getElementById('allTransactionsTable').getElementsByTagName('tbody')[0];
    var rows = Array.from(table.rows);
    
    // Convert table rows to data objects
    allRows = rows.map(function(row) {
      var cells = row.cells;
      return {
        user_name: cells[0] ? cells[0].textContent.trim() : '',
        room: cells[1] ? cells[1].textContent.trim() : '',
        accommodation: cells[2] ? cells[2].textContent.trim() : '',
        check_in: cells[3] ? cells[3].textContent.trim() : '',
        check_out: cells[4] ? cells[4].textContent.trim() : '',
        amount: cells[5] ? cells[5].textContent.trim() : '',
        date: cells[6] ? cells[6].textContent.trim() : '',
        element: row
      };
    });

    function applySearch(){
      var search = document.getElementById('transactionSearch');
      var q = (search ? search.value : '').toLowerCase();
      if (!q) { 
        filteredRows = allRows.slice(); 
      } else {
        filteredRows = allRows.filter(function(r){
          var t = (''+r.user_name+' '+r.room+' '+r.accommodation+' '+r.check_in+' '+r.check_out+' '+r.amount+' '+r.date).toLowerCase();
          return t.indexOf(q) !== -1;
        });
      }
      currentPage = 1;
      renderTable();
      renderPagination();
    }

    function renderTable(){
      var tbody = document.getElementById('allTransactionsTable').getElementsByTagName('tbody')[0];
      tbody.innerHTML = '';
      var start = (currentPage - 1) * pageSize;
      var pageItems = filteredRows.slice(start, start + pageSize);
      pageItems.forEach(function(r){
        tbody.appendChild(r.element);
      });
      addRowClickHandlers(); // Add click handlers after rendering
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
        addRowClickHandlers(); // Re-add click handlers after pagination
      });
    }

    // Modal functionality
    var guestModal = document.getElementById('guestDetailsModal');
    var guestDetailsContent = document.getElementById('guestDetailsContent');
    var closeBtn = document.querySelector('.close');

    function addRowClickHandlers() {
      var rows = document.querySelectorAll('.transaction-row');
      rows.forEach(function(row) {
        row.addEventListener('click', function() {
          var transactionId = this.getAttribute('data-transaction-id');
          if (transactionId) {
            showGuestDetails(transactionId);
          }
        });
      });
    }

    function showGuestDetails(transactionId) {
      // Show loading state
      guestDetailsContent.innerHTML = '<div style="text-align: center; padding: 40px;"><i class="fas fa-spinner fa-spin" style="font-size: 24px; color: var(--purple-primary);"></i><p style="margin-top: 16px;">Loading guest details...</p></div>';
      guestModal.style.display = 'flex';

      // Fetch guest details
      fetch('/adminPages/transactions/guest-details/' + transactionId)
        .then(response => response.json())
        .then(data => {
          if (data.success && data.guests) {
            displayGuestDetails(data.guests, data.transaction);
          } else {
            guestDetailsContent.innerHTML = '<div style="text-align: center; padding: 40px; color: #6c757d;"><i class="fas fa-exclamation-triangle" style="font-size: 48px; margin-bottom: 16px; display: block;"></i><p>No guest details found for this transaction.</p></div>';
          }
        })
        .catch(error => {
          console.error('Error fetching guest details:', error);
          guestDetailsContent.innerHTML = '<div style="text-align: center; padding: 40px; color: #dc3545;"><i class="fas fa-exclamation-circle" style="font-size: 48px; margin-bottom: 16px; display: block;"></i><p>Error loading guest details. Please try again.</p></div>';
        });
    }

    function displayGuestDetails(guests, transaction) {
      var html = '<div class="transaction-info" style="margin-bottom: 24px; padding: 16px; background: #f8f9ff; border-radius: 10px; border-left: 4px solid var(--purple-primary);">';
      html += '<h4 style="margin: 0 0 12px 0; color: var(--purple-primary);">Transaction Information</h4>';
      html += '<div class="guest-detail-item"><span class="guest-detail-label">Room:</span><span class="guest-detail-value">' + (transaction.room || 'N/A') + '</span></div>';
      html += '<div class="guest-detail-item"><span class="guest-detail-label">Accommodation:</span><span class="guest-detail-value">' + (transaction.accommodation || 'N/A') + '</span></div>';
      html += '<div class="guest-detail-item"><span class="guest-detail-label">Amount:</span><span class="guest-detail-value">₱' + (transaction.amount ? parseFloat(transaction.amount).toFixed(2) : '0.00') + '</span></div>';
      html += '</div>';

      if (guests && guests.length > 0) {
        html += '<div class="guests-section">';
        html += '<h4 style="margin: 0 0 16px 0; color: var(--text-primary);">Guest Information</h4>';
        
        guests.forEach(function(guest, index) {
          html += '<div class="guest-card" style="background: #fff; border: 1px solid #e9ecef; border-radius: 10px; padding: 16px; margin-bottom: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">';
          html += '<h5 style="margin: 0 0 12px 0; color: var(--purple-primary);">Guest ' + (index + 1) + '</h5>';
          html += '<div class="guest-detail-item"><span class="guest-detail-label">Name:</span><span class="guest-detail-value">' + (guest.firstName || '') + ' ' + (guest.middleName || '') + ' ' + (guest.lastName || '') + '</span></div>';
          html += '<div class="guest-detail-item"><span class="guest-detail-label">Phone:</span><span class="guest-detail-value">' + (guest.number || 'N/A') + '</span></div>';
          
          if (guest.address) {
            html += '<div class="guest-detail-item"><span class="guest-detail-label">Address:</span><span class="guest-detail-value">' + (guest.address.street || '') + ', ' + (guest.address.city || '') + ', ' + (guest.address.province || '') + ' ' + (guest.address.zipcode || '') + '</span></div>';
          } else {
            html += '<div class="guest-detail-item"><span class="guest-detail-label">Address:</span><span class="guest-detail-value">N/A</span></div>';
          }
          
          html += '</div>';
        });
        
        html += '</div>';
      } else {
        html += '<div style="text-align: center; padding: 20px; color: #6c757d;"><i class="fas fa-user-slash" style="font-size: 32px; margin-bottom: 8px; display: block;"></i><p>No guest information available</p></div>';
      }

      guestDetailsContent.innerHTML = html;
    }

    // Close modal functionality
    closeBtn.addEventListener('click', function() {
      guestModal.style.display = 'none';
    });

    window.addEventListener('click', function(event) {
      if (event.target === guestModal) {
        guestModal.style.display = 'none';
      }
    });

    // Automatic filter event listeners
    document.getElementById('from').addEventListener('change', function() {
      var from = this.value;
      var to = document.getElementById('to').value;
      window.location.href = '{{ route("reports.all-archived-transactions") }}?from=' + from + '&to=' + to;
    });
    
    document.getElementById('to').addEventListener('change', function() {
      var from = document.getElementById('from').value;
      var to = this.value;
      window.location.href = '{{ route("reports.all-archived-transactions") }}?from=' + from + '&to=' + to;
    });

    // Client-side search
    var search = document.getElementById('transactionSearch');
    if (search) search.addEventListener('input', applySearch);

    // Print functionality
    document.getElementById('printBtn').addEventListener('click', function() {
      // Trigger print dialog
      window.print();
    });

    attachPaginationHandler();
    applySearch();
    addRowClickHandlers(); // Add initial click handlers
  })();
</script>

@endsection
