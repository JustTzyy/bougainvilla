@extends('layouts.admindashboard')

@section('title','Rooms')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/adminrecords.css') }}">
<link rel="stylesheet" href="{{ asset('css/roommanagement.css') }}">
<script src="{{ asset('js/ph-complete-address.js') }}"></script>
@endpush

@section('content')
<div class="dashboard-page">
  <div class="page-header">
    <h1 class="page-title">Rooms</h1>
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
      <input id="adminSearch" type="text" placeholder="Search rooms" class="search-input">
    </div>
    <div class="toolbar-actions">
      <a href="{{ route('rooms.archive') }}" class="archive-btn">
        <i class="fas fa-archive"></i> Archive
      </a>
      <button id="openAddAdmin"><i class="fas fa-bed"></i> Add Room</button>
    </div>
  </div>

  <div class="chart-card card-tight">
    <div class="section-header-pad">
      <h3 class="chart-title">List</h3>
    </div>

    <div class="table-wrapper">
      <table class="table sortable-table" id="roomsTable">
        <thead>
          <tr>
            <th>Room No.</th>
            <th>Level</th>
            <th>Status</th>
            <th>Type</th>
            <th>Accommodations</th>
            <th>Date Created</th>
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
                  data-created="{{ $room->created_at }}">
                <td data-label="Room No.">{{ $room->room }}</td>
                <td data-label="Level">{{ $room->level->description }}</td>
                <td data-label="Status">
                  <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $room->status)) }}">
                    {{ $room->status }}
                  </span>
                </td>
                <td data-label="Type">
                  <span class="type-badge type-{{ strtolower(str_replace(' ', '-', $room->type)) }}">
                    {{ $room->type }}
                  </span>
                </td>
                <td data-label="Accommodations">
                  @if($room->accommodations->count() > 0)
                    <div class="accommodation-tags">
                      @foreach($room->accommodations as $accommodation)
                        <span class="accommodation-tag">{{ $accommodation->name }}</span>
                      @endforeach
                    </div>
                  @else
                    <span class="text-muted">None</span>
                  @endif
                </td>
                <td data-label="Date Created">{{ $room->created_at->format('M d, Y') }}</td>
                <td data-label="Actions">
                  <button class="action-btn small" data-update data-room-id="{{ $room->id }}">
                    <i class="fas fa-pen"></i>
                  </button>
                  <button class="action-btn small" data-archive data-room-id="{{ $room->id }}">
                    <i class="fas fa-archive"></i>
                  </button>
                </td>
              </tr>
            @endforeach
          @else
            <tr>
              <td colspan="7" class="text-center">No rooms found</td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>
    @if(isset($rooms) && $rooms->hasPages())
      <nav class="pagination" aria-label="Table pagination">
        {{ $rooms->links() }}
      </nav>
    @endif
  </div>
</div>

<!-- Add Room Modal -->
<div id="roomModal" class="modal room-modal">
  <div class="modal-card">
    <div class="modal-header">
      <h3 class="chart-title">Add Room</h3>
      <button id="closeRoomModal" class="action-btn ml-auto">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <form id="roomForm" action="{{ route('adminPages.rooms.post') }}" class="modal-form" method="POST">
      @csrf
      <div class="form-grid">
        <div class="form-group">
          <label>Room Number</label>
          <input type="text" name="room" class="form-input" placeholder="e.g., 101, A1, Suite 1" required>
        </div>
        <div class="form-group">
          <label>Level</label>
          <select name="level_id" class="form-input" required>
            <option value="">Select Level</option>
            @foreach($levels as $level)
              <option value="{{ $level->id }}">{{ $level->description }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label>Status</label>
          <select name="status" class="form-input" required readonly disabled>
            <option value="Active" selected>Active</option>
          </select>
          <input type="hidden" name="status" value="Active">
        </div>
        <div class="form-group">
          <label>Type</label>
          <input type="text" name="type" class="form-input" placeholder="e.g., Available, Occupied, Reserved, etc." required>
        </div>
        <div class="form-group span-2">
          <label>Accommodations</label>
          <div class="accommodation-selection">
            @foreach($accommodations as $accommodation)
              <label class="checkbox-label">
                <input type="checkbox" name="accommodations[]" value="{{ $accommodation->id }}">
                <span class="checkmark"></span>
                <span class="accommodation-text">
                  {{ $accommodation->name }}
                  @if($accommodation->capacity)
                    <small>(Capacity: {{ $accommodation->capacity }})</small>
                  @endif
                </span>
              </label>
            @endforeach
          </div>
        </div>
      </div>

      <div class="modal-actions">
        <button type="button" id="cancelRoom" class="action-btn btn-outline">Cancel</button>
        <button type="submit" class="btn-primary inline">Save Room</button>
      </div>
    </form>
  </div>
</div>

<!-- Update Room Modal -->
<div id="updateModal" class="modal room-modal">
  <div class="modal-card">
    <div class="modal-header">
      <h3 class="chart-title">Update Room</h3>
      <button id="closeUpdateModal" class="action-btn ml-auto">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <form id="updateForm" class="modal-form" method="POST">
      @csrf
      @method('POST')
      <div class="form-grid">
        <div class="form-group">
          <label>Room Number</label>
          <input name="room" id="u_room" class="form-input" placeholder="e.g., 101, A1, Suite 1" required>
        </div>
        <div class="form-group">
          <label>Level</label>
          <select name="level_id" id="u_level_id" class="form-input" required>
            <option value="">Select Level</option>
            @foreach($levels as $level)
              <option value="{{ $level->id }}">{{ $level->description }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label>Status</label>
          <select name="status" id="u_status" class="form-input" required readonly disabled>
            <option value="Active">Active</option>
            <option value="Under Maintenance">Under Maintenance</option>
          </select>
          <input type="hidden" name="status" id="u_status_hidden" value="Active">
        </div>
        <div class="form-group">
          <label>Type</label>
          <input type="text" name="type" id="u_type" class="form-input" placeholder="e.g., Available, Occupied, Reserved, etc." required>
        </div>
        <div class="form-group span-2">
          <label>Accommodations</label>
          <div class="accommodation-selection" id="updateAccommodations">
            @foreach($accommodations as $accommodation)
              <label class="checkbox-label">
                <input type="checkbox" name="accommodations[]" value="{{ $accommodation->id }}" class="accommodation-checkbox">
                <span class="checkmark"></span>
                <span class="accommodation-text">
                  {{ $accommodation->name }}
                  @if($accommodation->capacity)
                    <small>(Capacity: {{ $accommodation->capacity }})</small>
                  @endif
                </span>
              </label>
            @endforeach
          </div>
        </div>
      </div>

      <div class="modal-actions">
        <button type="button" id="cancelUpdate" class="action-btn btn-outline">Cancel</button>
        <button type="submit" class="btn-primary inline">Update Room</button>
      </div>
    </form>
  </div>
</div>


<script>
  (function(){
    var modal = document.getElementById('roomModal');
    var openBtn = document.getElementById('openAddAdmin');
    var closeBtn = document.getElementById('closeRoomModal');
    var cancelBtn = document.getElementById('cancelRoom');
    var search = document.getElementById('adminSearch');
    var table = document.getElementById('roomsTable').getElementsByTagName('tbody')[0];

    // Open Add Modal
    function openModal(){ 
      modal.style.display = 'flex'; 
    }
    
    // Add confirmation for Add Room form
    var roomForm = document.getElementById('roomForm');
    if (roomForm) {
      roomForm.addEventListener('submit', function(e) {
        e.preventDefault();
        var room = document.querySelector('input[name="room"]').value;
        var status = document.querySelector('select[name="status"]').value;
        var type = document.querySelector('select[name="type"]').value;
        if (confirm('Are you sure you want to add Room "' + room + '" (status: ' + status + ', type: ' + type + ')?')) {
          this.submit();
        }
      });
    }

    // Close Add Modal
    function closeModal(){ modal.style.display = 'none'; }
    if (openBtn) openBtn.addEventListener('click', openModal);
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
    
    // Prevent modal from closing when clicking outside
    modal.addEventListener('click', function(e) {
      if (e.target === modal) {
        e.stopPropagation();
      }
    });

    // Client-side search
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

    function openUpdateModal(){ 
      updateModal.style.display = 'flex'; 
    }

    // Confirmation for Update Room form
    var updateForm = document.getElementById('updateForm');
    if (updateForm) {
      updateForm.addEventListener('submit', function(e) {
        e.preventDefault();
        var room = document.getElementById('u_room').value;
        var status = document.getElementById('u_status').value;
        var type = document.getElementById('u_type').value;
        if (confirm('Update room to "' + room + '" with status ' + status + ' and type ' + type + '?')) {
          this.submit();
        }
      });
    }

    // Close Update Modal
    function closeUpdateModal(){ updateModal.style.display = 'none'; }
    if (closeUpdateModalBtn) closeUpdateModalBtn.addEventListener('click', closeUpdateModal);
    if (cancelUpdateBtn) cancelUpdateBtn.addEventListener('click', closeUpdateModal);
    
    // Prevent update modal from closing when clicking outside
    updateModal.addEventListener('click', function(e) {
      if (e.target === updateModal) {
        e.stopPropagation();
      }
    });

    // Hook update buttons
    document.querySelectorAll('[data-update]').forEach(function(btn){
      btn.addEventListener('click', function(e){
        e.stopPropagation();
        var row = this.closest('tr');
        var d = row ? row.dataset : {};

        // Pre-fill fields
        document.getElementById('u_room').value = d.room || '';
        document.getElementById('u_level_id').value = d.levelId || '';
        document.getElementById('u_status').value = d.status || '';
        document.getElementById('u_status_hidden').value = d.status || '';
        document.getElementById('u_type').value = d.type || '';

        // Handle accommodations
        var accommodationIds = d.accommodations ? d.accommodations.split(',') : [];
        document.querySelectorAll('#updateAccommodations input[type="checkbox"]').forEach(function(checkbox) {
          checkbox.checked = accommodationIds.includes(checkbox.value);
        });

        // Point form action to update route
        var updateForm = document.getElementById('updateForm');
        var roomId = this.getAttribute('data-room-id');
        updateForm.setAttribute('action', '/adminPages/rooms/update/' + roomId);

        openUpdateModal();
      });
    });

    // Archive functionality
    document.querySelectorAll('[data-archive]').forEach(function(btn){
      btn.addEventListener('click', function(e){
        e.stopPropagation();
        var roomId = this.getAttribute('data-room-id');
        var roomNumber = this.closest('tr').querySelector('td[data-label="Room No."]').textContent;
        
        // Prompt for archive reason
        var reason = prompt('Enter reason for archiving room "' + roomNumber + '":');
        if (reason === null) return; // User cancelled
        
        if (!reason.trim()) {
          alert('Archive reason is required.');
          return;
        }
        
        if (confirm('Are you sure you want to archive "' + roomNumber + '" with reason: "' + reason + '"?')) {
          var form = document.createElement('form');
          form.method = 'POST';
          form.action = '/adminPages/rooms/archive/' + roomId;
          
          // CSRF
          var csrfToken = document.createElement('input');
          csrfToken.type = 'hidden';
          csrfToken.name = '_token';
          csrfToken.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                            document.querySelector('input[name="_token"]')?.value;
          
          // Archive reason
          var reasonField = document.createElement('input');
          reasonField.type = 'hidden';
          reasonField.name = 'archive_reason';
          reasonField.value = reason;
          
          form.appendChild(csrfToken);
          form.appendChild(reasonField);
          document.body.appendChild(form);
          form.submit();
        }
      });
    });
  })();
</script>

@endsection
