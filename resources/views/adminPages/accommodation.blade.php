@extends('layouts.admindashboard')

@section('title','Accommodations')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/adminrecords.css') }}">
<script src="{{ asset('js/ph-complete-address.js') }}"></script>
@endpush

@section('content')
<div class="dashboard-page">
  <div class="page-header">
    <h1 class="page-title">Accommodations</h1>
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
      <input id="adminSearch" type="text" placeholder="Search accommodations" class="search-input">
    </div>
    <div class="toolbar-actions">
      <a href="{{ route('accommodations.archive') }}" class="archive-btn">
        <i class="fas fa-archive"></i> Archive
      </a>
      <button id="openAddAdmin"><i class="fas fa-hotel"></i> Add Accommodation</button>
    </div>
  </div>

  <div class="chart-card card-tight">
    <div class="section-header-pad">
      <h3 class="chart-title">List</h3>
    </div>

    <div class="table-wrapper">
      <table class="table sortable-table" id="accommodationsTable">
        <thead>
          <tr>
            <th>Name</th>
            <th>Capacity</th>
            <th>Description</th>
            <th>Date Created</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @if(isset($accommodations) && $accommodations->count() > 0)
            @foreach($accommodations as $accommodation)
              <tr class="accommodation-row"
                  data-accommodation-id="{{ $accommodation->id }}"
                  data-name="{{ $accommodation->name }}"
                  data-capacity="{{ $accommodation->capacity }}"
                  data-description="{{ $accommodation->description }}"
                  data-created="{{ $accommodation->created_at }}">
                <td data-label="Name">{{ $accommodation->name }}</td>
                <td data-label="Capacity">{{ $accommodation->capacity }}</td>
                <td data-label="Description" class="accommodation-description">{{ $accommodation->description }}</td>
                <td data-label="Date Created">{{ $accommodation->created_at->format('M d, Y') }}</td>
                <td data-label="Actions">
                  <button class="action-btn small" data-update data-accommodation-id="{{ $accommodation->id }}">
                    <i class="fas fa-pen"></i>
                  </button>
                  <button class="action-btn small" data-archive data-accommodation-id="{{ $accommodation->id }}">
                    <i class="fas fa-archive"></i>
                  </button>
                </td>
              </tr>
            @endforeach
          @else
            <tr>
              <td colspan="5" class="text-center">No accommodations found</td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>
    @if(isset($accommodations) && $accommodations->hasPages())
      <nav class="pagination" aria-label="Table pagination">
        {{ $accommodations->links() }}
      </nav>
    @endif
  </div>
</div>

<!-- Add Accommodation Modal -->
<div id="accommodationModal" class="modal accommodation-modal">
  <div class="modal-card">
    <div class="modal-header">
      <h3 class="chart-title">Add Accommodation</h3>
      <button id="closeAccommodationModal" class="action-btn ml-auto">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <form id="accommodationForm" action="{{ route('adminPages.accommodations.post') }}" class="modal-form" method="POST">
      @csrf
      <div class="form-grid">
        <div class="form-group">
          <label>Name</label>
          <input type="text" name="name" class="form-input" placeholder="e.g., Deluxe Room" required>
        </div>
        <div class="form-group">
          <label>Capacity</label>
          <input type="number" name="capacity" class="form-input" min="1" required>
        </div>
        <div class="form-group span-2">
          <label>Description</label>
          <textarea name="description" class="form-input" placeholder="Accommodation description"></textarea>
        </div>
      </div>

      <div class="modal-actions">
        <button type="button" id="cancelAccommodation" class="action-btn btn-outline">Cancel</button>
        <button type="submit" class="btn-primary inline">Save Accommodation</button>
      </div>
    </form>
  </div>
</div>

<!-- Update Accommodation Modal -->
<div id="updateModal" class="modal accommodation-modal">
  <div class="modal-card">
    <div class="modal-header">
      <h3 class="chart-title">Update Accommodation</h3>
      <button id="closeUpdateModal" class="action-btn ml-auto">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <form id="updateForm" class="modal-form" method="POST">
      @csrf
      @method('POST')
      <div class="form-grid">
        <div class="form-group">
          <label>Name</label>
          <input name="name" id="u_name" class="form-input" required>
        </div>
        <div class="form-group">
          <label>Capacity</label>
          <input type="number" name="capacity" id="u_capacity" class="form-input" min="1" required>
        </div>
        <div class="form-group span-2">
          <label>Description</label>
          <textarea name="description" id="u_description" class="form-input"></textarea>
        </div>
      </div>

      <div class="modal-actions">
        <button type="button" id="cancelUpdate" class="action-btn btn-outline">Cancel</button>
        <button type="submit" class="btn-primary inline">Update Accommodation</button>
      </div>
    </form>
  </div>
</div>

<script>
  (function(){
    var modal = document.getElementById('accommodationModal');
    var openBtn = document.getElementById('openAddAdmin');
    var closeBtn = document.getElementById('closeAccommodationModal');
    var cancelBtn = document.getElementById('cancelAccommodation');
    var search = document.getElementById('adminSearch');
    var table = document.getElementById('accommodationsTable').getElementsByTagName('tbody')[0];

    // Open Add Modal
    function openModal(){ modal.style.display = 'flex'; }
    
    // Add confirmation
    var form = document.getElementById('accommodationForm');
    if (form) {
      form.addEventListener('submit', function(e) {
        e.preventDefault();
        var name = document.querySelector('input[name="name"]').value;
        var capacity = document.querySelector('input[name="capacity"]').value;
        if (confirm('Are you sure you want to add "' + name + '" with capacity ' + capacity + '?')) {
          this.submit();
        }
      });
    }

    // Close Add Modal
    function closeModal(){ modal.style.display = 'none'; }
    if (openBtn) openBtn.addEventListener('click', openModal);
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    // Search
    if (search) search.addEventListener('input', function(){
      var q = this.value.toLowerCase();
      Array.prototype.forEach.call(table.rows, function(row){
        var text = row.innerText.toLowerCase();
        row.style.display = text.indexOf(q) !== -1 ? '' : 'none';
      });
    });

    // Update modal logic
    var updateModal = document.getElementById('updateModal');
    var closeUpdateModalBtn = document.getElementById('closeUpdateModal');
    var cancelUpdateBtn = document.getElementById('cancelUpdate');

    function openUpdateModal(){ updateModal.style.display = 'flex'; }

    // Confirmation for Update form
    var updateForm = document.getElementById('updateForm');
    if (updateForm) {
      updateForm.addEventListener('submit', function(e) {
        e.preventDefault();
        var name = document.getElementById('u_name').value;
        var capacity = document.getElementById('u_capacity').value;
        if (confirm('Update accommodation "' + name + '" with capacity ' + capacity + '?')) {
          this.submit();
        }
      });
    }

    // Close Update Modal
    function closeUpdateModal(){ updateModal.style.display = 'none'; }
    if (closeUpdateModalBtn) closeUpdateModalBtn.addEventListener('click', closeUpdateModal);
    if (cancelUpdateBtn) cancelUpdateBtn.addEventListener('click', closeUpdateModal);

    // Hook update buttons
    document.querySelectorAll('[data-update]').forEach(function(btn){
      btn.addEventListener('click', function(e){
        e.stopPropagation();
        var row = this.closest('tr');
        var d = row ? row.dataset : {};

        // Pre-fill fields
        document.getElementById('u_name').value = d.name || '';
        document.getElementById('u_capacity').value = d.capacity || '';
        document.getElementById('u_description').value = d.description || '';

        // Point form action
        var updateForm = document.getElementById('updateForm');
        var accId = this.getAttribute('data-accommodation-id');
        updateForm.setAttribute('action', '/adminPages/accommodations/update/' + accId);

        openUpdateModal();
      });
    });

    // Archive functionality
    document.querySelectorAll('[data-archive]').forEach(function(btn){
      btn.addEventListener('click', function(e){
        e.stopPropagation();
        var accId = this.getAttribute('data-accommodation-id');
        var accName = this.closest('tr').querySelector('.accommodation-description').textContent;
        
        if (confirm('Are you sure you want to archive "' + accName + '"?')) {
          var form = document.createElement('form');
          form.method = 'POST';
          form.action = '/adminPages/accommodations/delete/' + accId;
          
          var csrfToken = document.createElement('input');
          csrfToken.type = 'hidden';
          csrfToken.name = '_token';
          csrfToken.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                            document.querySelector('input[name="_token"]')?.value;
          
          var methodField = document.createElement('input');
          methodField.type = 'hidden';
          methodField.name = '_method';
          methodField.value = 'DELETE';
          
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
