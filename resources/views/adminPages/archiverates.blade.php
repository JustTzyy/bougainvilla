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

  <div class="chart-card card-tight">
    <div class="section-header-pad">
      <h3 class="chart-title">List</h3>
    </div>

    <div class="table-wrapper">
      <table class="table sortable-table" id="archivedTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Duration</th>
            <th>Price</th>
            <th>Accommodation</th>
            <th>Archive Date</th>
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
                  data-accommodation="{{ $rate->accommodation->name ?? 'N/A' }}"
                  data-status="{{ $rate->status ?? 'Archived' }}"
                  data-archived="{{ $rate->deleted_at }}">
                <td data-label="ID">{{ $rate->id }}</td>
                <td data-label="Duration">{{ $rate->duration }}</td>
                <td data-label="Price">₱{{ number_format($rate->price, 2) }}</td>
                <td data-label="Accommodation">{{ $rate->accommodation->name ?? 'N/A' }}</td>
                <td data-label="Archive Date">{{ optional($rate->deleted_at)->format('M d, Y') }}</td>
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
    
    @if(isset($accommodations) && $accommodations->hasPages())
    <nav class="pagination-wrapper" aria-label="Table pagination">
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($accommodations->onFirstPage())
                <li class="page-item disabled"><span>&laquo;</span></li>
            @else
                <li class="page-item"><a href="{{ $accommodations->previousPageUrl() }}">&laquo;</a></li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($accommodations->getUrlRange(1, $accommodations->lastPage()) as $page => $url)
                @if ($page == $accommodations->currentPage())
                    <li class="page-item active"><span>{{ $page }}</span></li>
                @else
                    <li class="page-item"><a href="{{ $url }}">{{ $page }}</a></li>
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($accommodations->hasMorePages())
                <li class="page-item"><a href="{{ $accommodations->nextPageUrl() }}">&raquo;</a></li>
            @else
                <li class="page-item disabled"><span>&raquo;</span></li>
            @endif
        </ul>
    </nav>
@endif
  </div>
</div>

<!-- Rate Details Modal -->
<div id="rateDetailsModal" class="modal">
  <div class="modal-card user-details-card">
    <div class="modal-header">
      <h3 class="chart-title">Rate Details</h3>
      <button id="closeRateDetailsModal" class="action-btn ml-auto"><i class="fas fa-times"></i></button>
    </div>
    
    <div class="user-details-content">
      <div class="user-info-section">
        <h4><i class="fas fa-tags"></i> Rate Information</h4>
        <div class="info-grid">
          <div class="info-item">
            <label>ID:</label> 
            <span id="detail-id">-</span>
          </div>
          <div class="info-item">
            <label>Duration:</label> 
            <span id="detail-duration">-</span>
          </div>
          <div class="info-item">
            <label>Price:</label>
            <span id="detail-price">-</span>
          </div>
          <div class="info-item">
            <label>Accommodation:</label>
            <span id="detail-accommodation">-</span>
          </div>
          <div class="info-item">
            <label>Status:</label>
            <span id="detail-status" class="status-badge">-</span>
          </div>
          <div class="info-item">
            <label>Archive Date:</label>
            <span id="detail-archived">-</span>
          </div>
        </div>
      </div>
    </div>
    
    <div class="modal-actions">
      <button type="button" id="closeRateDetails" class="action-btn btn-outline">Close</button>
    </div>
  </div>
</div>

<script>
  (function(){
    var search = document.getElementById('archiveSearch');
    var table = document.getElementById('archivedTable').getElementsByTagName('tbody')[0];

    // Search functionality
    if (search) search.addEventListener('input', function(){
      var q = this.value.toLowerCase();
      Array.prototype.forEach.call(table.rows, function(row){
        var text = row.innerText.toLowerCase();
        row.style.display = text.indexOf(q) !== -1 ? '' : 'none';
      });
    });

    // Rate details modal
    var modal = document.getElementById('rateDetailsModal');
    var closeBtn = document.getElementById('closeRateDetails');
    var closeX = document.getElementById('closeRateDetailsModal');
    
    function openModal() { modal.style.display = 'flex'; }
    function closeModal() { modal.style.display = 'none'; }
    
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (closeX) closeX.addEventListener('click', closeModal);

    var rows = document.querySelectorAll('.rate-row');
    rows.forEach(function(row) {
      row.addEventListener('click', function(e) {
        if (e.target.closest('button')) return;
        var r = this.dataset;
        populateRateDetails(r);
        openModal();
      });
    });

    // Populate details
    function populateRateDetails(r) {
      document.getElementById('detail-id').textContent = r.id || '-';
      document.getElementById('detail-duration').textContent = r.duration || '-';
      document.getElementById('detail-price').textContent = r.price ? '₱' + r.price : '-';
      document.getElementById('detail-accommodation').textContent = r.accommodation || 'N/A';
      document.getElementById('detail-status').textContent = r.status || '-';
      document.getElementById('detail-archived').textContent = r.archived ? new Date(r.archived).toLocaleDateString() : '-';
    }

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
