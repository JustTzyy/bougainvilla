@extends('layouts.admindashboard')

@section('title','Archived Rooms')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/adminrecords.css') }}">
<script src="{{ asset('js/ph-complete-address.js') }}"></script>
@endpush

@section('content')
<div class="dashboard-page">
  <div class="page-header">
    <h1 class="page-title">Archived Rooms</h1>
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
      <input id="adminSearch" type="text" placeholder="Search archived rooms" class="search-input">
    </div>
    <div class="toolbar-actions">
      <a href="{{ route('adminPages.rooms') }}" class="archive-btn">
        <i class="fas fa-arrow-left"></i> Back to Records
      </a>
    </div>
  </div>

  <div class="chart-card card-tight">
    <div class="section-header-pad">
      <h3 class="chart-title">Archived List</h3>
    </div>

    <div class="table-wrapper">
      <table class="table sortable-table" id="archivedRoomsTable">
        <thead>
          <tr>
            <th>Room No.</th>
            <th>Level</th>
            <th>Status</th>
            <th>Type</th>
            <th>Date Archived</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @if(isset($rooms) && $rooms->count() > 0)
            @foreach($rooms as $room)
              <tr class="room-row"
                  data-room-id="{{ $room->id }}"
                  data-room="{{ $room->room }}"
                  data-level-id="{{ $room->level_id }}"
                  data-status="{{ $room->status }}"
                  data-type="{{ $room->type }}"
                  data-accommodations="{{ $room->accommodations->pluck('id')->implode(',') }}"
                  data-archived="{{ $room->deleted_at }}">
                <td data-label="Room No.">{{ $room->room }}</td>
                <td data-label="Level">{{ $room->level->description }}</td>
                <td data-label="Status">{{ $room->status }}</td>
                <td data-label="Type">{{ $room->type }}</td>
                <td data-label="Date Archived">{{ $room->deleted_at->format('M d, Y H:i') }}</td>
                <td data-label="Actions">
                  <button class="action-btn small" data-restore data-room-id="{{ $room->id }}">
                    <i class="fas fa-undo"></i>
                  </button>
                </td>
              </tr>
            @endforeach
          @else
            <tr>
              <td colspan="6" class="text-center">No archived rooms found</td>
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


<script>
  (function(){
    var search = document.getElementById('adminSearch');
    var table = document.getElementById('archivedRoomsTable').getElementsByTagName('tbody')[0];

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
        var roomId = this.getAttribute('data-room-id');
        var roomNumber = this.closest('tr').querySelector('td[data-label="Room No."]').textContent;
        
        if (confirm('Are you sure you want to restore "' + roomNumber + '"?')) {
          var form = document.createElement('form');
          form.method = 'POST';
          form.action = '/adminPages/rooms/restore/' + roomId;
          
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
          
          // Status field - set to Active
          var statusField = document.createElement('input');
          statusField.type = 'hidden';
          statusField.name = 'status';
          statusField.value = 'Active';
          
          form.appendChild(csrfToken);
          form.appendChild(methodField);
          form.appendChild(statusField);
          document.body.appendChild(form);
          form.submit();
        }
      });
    });
  })();
</script>

@endsection
