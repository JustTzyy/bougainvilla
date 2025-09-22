@extends('layouts.admindashboard')

@section('title','Archived Accommodation Records')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/archiveadminrecords.css') }}">
@endpush

@section('content')
<div class="dashboard-page">
  <div class="page-header">
    <h1 class="page-title">Archived Accommodation</h1>
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
      <input id="archiveSearch" type="text" placeholder="Search archived accommodations" class="search-input">
    </div>
    <div class="toolbar-actions">
      <a href="{{ route('adminPages.accommodations') }}" class="archive-btn">
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
            <th>Name</th>
            <th>Capacity</th>
            <th>Description</th>
            <th>Archive Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @if(isset($accommodations) && $accommodations->count() > 0)
            @foreach($accommodations as $accommodation)
              <tr class="accommodation-row"
                  data-id="{{ $accommodation->id }}"
                  data-name="{{ $accommodation->name }}"
                  data-capacity="{{ $accommodation->capacity }}"
                  data-description="{{ $accommodation->description }}"
                  data-status="{{ $accommodation->status ?? 'Archived' }}"
                  data-archived="{{ $accommodation->deleted_at }}">
                <td data-label="Name" class="accommodation-name">{{ $accommodation->name }}</td>
                <td data-label="Capacity">{{ $accommodation->capacity ?? 'N/A' }}</td>
                <td data-label="Description">{{ $accommodation->description ?? 'N/A' }}</td>
                <td data-label="Archive Date">{{ $accommodation->deleted_at ? $accommodation->deleted_at->format('M d, Y') : '-' }}</td>
                <td data-label="Actions">
                  <button class="action-btn small" data-restore> 
                    <i class="fas fa-undo"></i> 
                  </button>
                </td>
              </tr>
            @endforeach
          @else
            <tr>
              <td colspan="5" class="text-center">No archived accommodations found</td>
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

<!-- Accommodation Details Modal -->
<div id="accommodationDetailsModal" class="modal">
  <div class="modal-card user-details-card">
    <div class="modal-header">
      <h3 class="chart-title">Accommodation Details</h3>
      <button id="closeAccommodationDetailsModal" class="action-btn ml-auto"><i class="fas fa-times"></i></button>
    </div>
    
    <div class="user-details-content">
      <div class="user-info-section">
        <h4><i class="fas fa-bed"></i> Accommodation Information</h4>
        <div class="info-grid">
          <div class="info-item">
            <label>Name:</label> 
            <span id="detail-name">-</span>
          </div>
          <div class="info-item">
            <label>Capacity:</label>
            <span id="detail-capacity">-</span>
          </div>
          <div class="info-item">
            <label>Description:</label>
            <span id="detail-description">-</span>
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
      <button type="button" id="closeAccommodationDetails" class="action-btn btn-outline">Close</button>
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

    // Accommodation details modal functionality
    var modal = document.getElementById('accommodationDetailsModal');
    var closeBtn = document.getElementById('closeAccommodationDetails');
    var closeX = document.getElementById('closeAccommodationDetailsModal');
    
    function openModal() { modal.style.display = 'flex'; }
    function closeModal() { modal.style.display = 'none'; }
    
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (closeX) closeX.addEventListener('click', closeModal);

    var rows = document.querySelectorAll('.accommodation-row');
    rows.forEach(function(row) {
      row.addEventListener('click', function(e) {
        if (e.target.closest('button')) return;
        var a = this.dataset;
        populateAccommodationDetails({
          name: a.name,
          capacity: a.capacity,
          description: a.description,
          status: a.status,
          archived_at: a.archived
        });
        openModal();
      });
    });

    // Populate details
    function populateAccommodationDetails(a) {
      document.getElementById('detail-name').textContent = a.name || '-';
      document.getElementById('detail-capacity').textContent = a.capacity || 'N/A';
      document.getElementById('detail-description').textContent = a.description || 'N/A';
      document.getElementById('detail-status').textContent = a.status || '-';
      document.getElementById('detail-archived').textContent = a.archived_at ? new Date(a.archived_at).toLocaleDateString() : '-';
    }

    // Restore functionality
    document.querySelectorAll('[data-restore]').forEach(function(btn){
      btn.addEventListener('click', function(e){
        e.stopPropagation();
        var row = this.closest('tr');
        var id = row.dataset.id;
        var name = row.dataset.name;
        
        if (confirm('Are you sure you want to restore "' + name + '"?')) {
          var form = document.createElement('form');
          form.method = 'POST';
          form.action = '/adminPages/accommodations/restore/' + id;
          
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
