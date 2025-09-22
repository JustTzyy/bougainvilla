@extends('layouts.admindashboard')

@section('title','Archived Transactions')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/adminrecords.css') }}">
@endpush

@section('content')
<div class="dashboard-page">
  <div class="page-header">
    <h1 class="page-title">Archived Transactions</h1>
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
      <a href="{{ route('adminPages.transactions') }}" class="archive-btn">
        <i class="fas fa-arrow-left"></i> Back to Records
      </a>
    </div>
  </div>

  <div class="chart-card card-tight">
    <div class="section-header-pad">
      <h3 class="chart-title">Archived List</h3>
    </div>

    <div class="table-wrapper">
      <table class="table sortable-table" id="archivedTransactionsTable">
        <thead>
          <tr>
            <th>Transaction ID</th>
            <th>Guest Name</th>
            <th>Room</th>
            <th>Check-in</th>
            <th>Check-out</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Date Archived</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @if(isset($transactions) && $transactions->count() > 0)
            @foreach($transactions as $transaction)
              <tr class="transaction-row"
                  data-transaction-id="{{ $transaction->id }}"
                  data-guest-name="{{ $transaction->guest_name }}"
                  data-room="{{ $transaction->room }}"
                  data-checkin="{{ $transaction->check_in }}"
                  data-checkout="{{ $transaction->check_out }}"
                  data-amount="{{ $transaction->amount }}"
                  data-status="{{ $transaction->status }}"
                  data-archived="{{ $transaction->deleted_at }}">
                <td data-label="Transaction ID">#{{ $transaction->id }}</td>
                <td data-label="Guest Name">{{ $transaction->guest_name }}</td>
                <td data-label="Room">{{ $transaction->room }}</td>
                <td data-label="Check-in">{{ $transaction->check_in ? \Carbon\Carbon::parse($transaction->check_in)->format('M d, Y') : '-' }}</td>
                <td data-label="Check-out">{{ $transaction->check_out ? \Carbon\Carbon::parse($transaction->check_out)->format('M d, Y') : '-' }}</td>
                <td data-label="Amount">₱{{ number_format($transaction->amount, 2) }}</td>
                <td data-label="Status">{{ $transaction->status }}</td>
                <td data-label="Date Archived">{{ $transaction->deleted_at->format('M d, Y H:i') }}</td>
                <td data-label="Actions">
                  <button class="action-btn small" data-restore data-transaction-id="{{ $transaction->id }}">
                    <i class="fas fa-undo"></i>
                  </button>
                </td>
              </tr>
            @endforeach
          @else
            <tr>
              <td colspan="9" class="text-center">No archived transactions found</td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>
    @if(isset($transactions) && $transactions->hasPages())
      <nav class="pagination" aria-label="Table pagination">
        {{ $transactions->links() }}
      </nav>
    @endif
  </div>
</div>

<script>
  (function(){
    var search = document.getElementById('transactionSearch');
    var table = document.getElementById('archivedTransactionsTable').getElementsByTagName('tbody')[0];

    // Client-side search
    if (search) search.addEventListener('input', function(){
      var q = this.value.toLowerCase();
      Array.prototype.forEach.call(table.rows, function(row){
        var text = row.innerText.toLowerCase();
        row.style.display = text.indexOf(q) !== -1 ? '' : 'none';
      });
    });

    // Restore functionality
    document.querySelectorAll('[data-restore]').forEach(function(btn){
      btn.addEventListener('click', function(e){
        e.stopPropagation();
        var transactionId = this.getAttribute('data-transaction-id');
        var guestName = this.closest('tr').querySelector('td[data-label="Guest Name"]').textContent;
        
        if (confirm('Are you sure you want to restore transaction for "' + guestName + '"?')) {
          var form = document.createElement('form');
          form.method = 'POST';
          form.action = '/adminPages/transactions/restore/' + transactionId;
          
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

