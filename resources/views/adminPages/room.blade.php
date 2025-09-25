@extends('layouts.admindashboard')

@section('title','Rooms')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/adminrecords.css') }}">
<link rel="stylesheet" href="{{ asset('css/roommanagement.css') }}">
<script src="{{ asset('js/ph-complete-address.js') }}"></script>
<style>
  /* Enhanced Room Details Modal Styles */
  #roomDetailsModal .modal-card {
    max-width: 600px;
    border-radius: 16px;
    box-shadow: 0 15px 40px rgba(138,92,246,.15);
    background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
    border: 1px solid rgba(138,92,246,.1);
    overflow: hidden;
  }

  #roomDetailsModal .info-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(138,92,246,.15);
    background: rgba(138,92,246,.08);
  }

  #roomDetailsModal .user-info-section:hover,
  #roomDetailsModal .address-info-section:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 30px rgba(138,92,246,.12);
  }

  #roomDetailsModal .action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(138,92,246,.25);
    background: linear-gradient(135deg, var(--purple-primary), #a29bfe) !important;
    color: white !important;
  }

  /* Accommodation cards enhanced hover effects */
  #detail-accommodations-list .accommodation-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 30px rgba(138,92,246,.2);
    background: linear-gradient(135deg, rgba(138,92,246,.08), rgba(138,92,246,.04)) !important;
  }

  /* Loading animation enhancement */
  @keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
  }

  #detail-accommodations-list .loading {
    animation: pulse 1.5s ease-in-out infinite;
  }

  /* Responsive adjustments */
  @media (max-width: 768px) {
    #roomDetailsModal .modal-card {
      max-width: 95%;
      margin: 20px;
    }
    
    #roomDetailsModal .info-grid {
      grid-template-columns: 1fr !important;
    }
  }

  /* Enhanced Accommodation Selection Styling */
  .accommodation-field {
    grid-column: span 2;
    display: flex;
    flex-direction: column;
    height: 100%;
    min-height: 400px;
  }

  .accommodation-selection {
    background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
    border: 2px solid rgba(138,92,246,.1);
    border-radius: 12px;
    width: 500px;
    padding: 20px;
    margin-top: 8px;
    box-shadow: 0 4px 12px rgba(138,92,246,.08);
    transition: all 0.3s ease;
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 350px;
    overflow-y: auto;
  }

  .accommodation-selection:hover {
    border-color: rgba(138,92,246,.2);
    box-shadow: 0 6px 16px rgba(138,92,246,.12);
    transform: translateY(-1px);
  }

  .checkbox-label {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px 20px;
    margin-bottom: 12px;
    background: linear-gradient(135deg, rgba(255,255,255,.8), rgba(248,249,255,.6));
    border: 1px solid rgba(138,92,246,.1);
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    min-height: 60px;
  }

  .checkbox-label::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(180deg, var(--purple-primary), #a29bfe);
    opacity: 0;
    transition: opacity 0.3s ease;
  }

  .checkbox-label:hover {
    background: linear-gradient(135deg, rgba(138,92,246,.05), rgba(138,92,246,.02));
    border-color: rgba(138,92,246,.2);
    transform: translateX(4px);
    box-shadow: 0 4px 12px rgba(138,92,246,.1);
  }

  .checkbox-label:hover::before {
    opacity: 1;
  }

  .checkbox-label:last-child {
    margin-bottom: 0;
  }

  /* Custom Checkbox Styling */
  .checkbox-label input[type="checkbox"] {
    display: none;
  }

  .checkmark {
    width: 24px;
    height: 24px;
    border: 2px solid rgba(138,92,246,.3);
    border-radius: 8px;
    background: #fff;
    position: relative;
    transition: all 0.3s ease;
    flex-shrink: 0;
  }

  .checkbox-label:hover .checkmark {
    border-color: var(--purple-primary);
    box-shadow: 0 2px 8px rgba(138,92,246,.2);
  }

  .checkbox-label input[type="checkbox"]:checked + .checkmark {
    background: linear-gradient(135deg, var(--purple-primary), #a29bfe);
    border-color: var(--purple-primary);
    box-shadow: 0 4px 12px rgba(138,92,246,.3);
  }

  .checkbox-label input[type="checkbox"]:checked + .checkmark::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 14px;
    font-weight: bold;
    text-shadow: 0 1px 2px rgba(0,0,0,.2);
  }

  /* Accommodation Text Styling */
  .accommodation-text {
    flex: 1;
    color: var(--text-primary);
    font-weight: 500;
    line-height: 1.4;
  }

  .accommodation-text strong {
    display: block;
    font-size: 16px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 4px;
    line-height: 1.3;
  }

  .accommodation-text small {
    display: block;
    font-size: 13px;
    color: #6c757d;
    font-style: italic;
    line-height: 1.2;
  }

  /* Selected State Enhancement */
  .checkbox-label input[type="checkbox"]:checked ~ .accommodation-text {
    color: var(--purple-primary);
  }

  .checkbox-label input[type="checkbox"]:checked ~ .accommodation-text strong {
    color: var(--purple-primary);
    font-weight: 700;
  }

  /* Focus States */
  .checkbox-label:focus-within {
    outline: none;
    box-shadow: 0 0 0 3px rgba(138,92,246,.15);
    border-color: var(--purple-primary);
  }

  /* Loading State for Accommodations */
  .accommodation-selection.loading {
    opacity: 0.6;
    pointer-events: none;
  }

  .accommodation-selection.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 20px;
    height: 20px;
    border: 2px solid rgba(138,92,246,.3);
    border-top: 2px solid var(--purple-primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    0% { transform: translate(-50%, -50%) rotate(0deg); }
    100% { transform: translate(-50%, -50%) rotate(360deg); }
  }

  /* Responsive Accommodation Selection */
  @media (max-width: 600px) {
    .accommodation-field {
      grid-column: span 1;
      min-height: 300px;
    }
    
    .accommodation-selection {
      padding: 16px;
      min-height: 250px;
    }
    
    .checkbox-label {
      padding: 12px 16px;
      gap: 12px;
      min-height: 50px;
    }
    
    .checkmark {
      width: 20px;
      height: 20px;
    }
    
    .accommodation-text strong {
      font-size: 14px;
    }
    
    .accommodation-text small {
      font-size: 12px;
    }
  }
</style>
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
                <td data-label="Status">{{ $room->status }}</td>
                <td data-label="Type">{{ $room->type }}</td>
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
              <td colspan="6" class="text-center">No rooms found</td>
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
          <small id="roomDuplicateMsg" class="text-danger" style="display:none;">Room number already exists!</small>
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
          <select name="status" class="form-input" required readonly>
            <option value="Available" selected>Available</option>
          </select>
          <input type="hidden" name="status" value="Available">
        </div>
        <div class="form-group">
          <label>Type</label>
          <input type="text" name="type" class="form-input" placeholder="e.g., Available, Occupied, Reserved, etc." required>
        </div>
        <div class="form-group accommodation-field">
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
      <div class="form-grid">
        <div class="form-group">
          <label>Room Number</label>
          <input name="room" id="u_room" class="form-input" required>
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
            <option value="Available">Available</option>
            <option value="Under Maintenance">Under Maintenance</option>
          </select>
          <input type="hidden" name="status" id="u_status_hidden" value="Available">
        </div>
        <div class="form-group">
          <label>Type</label>
          <input type="text" name="type" id="u_type" class="form-input" required>
        </div>
        <div class="form-group accommodation-field">
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

<!-- Room Details Modal -->
<div id="roomDetailsModal" class="modal">
  <div class="modal-card user-details-card">
    <div class="modal-header" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); border-bottom: 1px solid rgba(138,92,246,.15);">
      <h3 class="chart-title" style="color: var(--purple-primary); font-size: 18px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px;">
        <i class="fas fa-bed" style="background: linear-gradient(135deg, var(--purple-primary), #a29bfe); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-size: 20px;"></i>
        Room Details
      </h3>
      <button id="closeRoomDetailsModal" class="action-btn ml-auto"><i class="fas fa-times"></i></button>
    </div>
    
    <div class="user-details-content" style="padding: 8px; background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);">
      <!-- Room Information Section -->
      <div class="user-info-section" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); border-radius: 10px; padding: 8px; margin-bottom: 8px; box-shadow: 0 2px 12px rgba(138,92,246,.08); border: 1px solid rgba(138,92,246,.1);">
        <h4 style="color: var(--purple-primary); font-size: 14px; font-weight: 700; margin: 0 0 8px 0; display: flex; align-items: center; gap: 6px; padding-bottom: 6px; border-bottom: 1px solid rgba(138,92,246,.15);">
          <i class="fas fa-bed" style="background: linear-gradient(135deg, var(--purple-primary), #a29bfe); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-size: 14px;"></i>
          Room Information
        </h4>
        <div class="info-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 8px;">
          <div class="info-item" style="background: rgba(138,92,246,.05); padding: 8px; border-radius: 8px; border-left: 3px solid var(--purple-primary); transition: all 0.3s ease;">
            <label style="display: block; font-size: 10px; font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 2px;">
              <i class="fas fa-hashtag" style="margin-right: 4px; color: var(--purple-primary); font-size: 10px;"></i>Room Number
            </label> 
            <span id="detail-room-number" style="font-size: 14px; font-weight: 700; color: var(--text-primary);">-</span>
          </div>
          <div class="info-item" style="background: rgba(138,92,246,.05); padding: 8px; border-radius: 8px; border-left: 3px solid var(--purple-primary); transition: all 0.3s ease;">
            <label style="display: block; font-size: 10px; font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 2px;">
              <i class="fas fa-layer-group" style="margin-right: 4px; color: var(--purple-primary); font-size: 10px;"></i>Level
            </label>
            <span id="detail-level" style="font-size: 14px; font-weight: 700; color: var(--text-primary);">-</span>
          </div>
          <div class="info-item" style="background: rgba(138,92,246,.05); padding: 8px; border-radius: 8px; border-left: 3px solid var(--purple-primary); transition: all 0.3s ease;">
            <label style="display: block; font-size: 10px; font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 2px;">
              <i class="fas fa-toggle-on" style="margin-right: 4px; color: var(--purple-primary); font-size: 10px;"></i>Status
            </label>
            <span id="detail-status" style="font-size: 14px; font-weight: 700; color: var(--text-primary);">-</span>
          </div>
          <div class="info-item" style="background: rgba(138,92,246,.05); padding: 8px; border-radius: 8px; border-left: 3px solid var(--purple-primary); transition: all 0.3s ease;">
            <label style="display: block; font-size: 10px; font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 2px;">
              <i class="fas fa-tag" style="margin-right: 4px; color: var(--purple-primary); font-size: 10px;"></i>Type
            </label>
            <span id="detail-type" style="font-size: 14px; font-weight: 700; color: var(--text-primary);">-</span>
          </div>
          <div class="info-item" style="background: rgba(138,92,246,.05); padding: 8px; border-radius: 8px; border-left: 3px solid var(--purple-primary); transition: all 0.3s ease;">
            <label style="display: block; font-size: 10px; font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 2px;">
              <i class="fas fa-calendar-plus" style="margin-right: 4px; color: var(--purple-primary); font-size: 10px;"></i>Date Created
            </label>
            <span id="detail-created" style="font-size: 14px; font-weight: 700; color: var(--text-primary);">-</span>
          </div>
        </div>
      </div>
      
      <!-- Accommodations Information Section -->
      <div class="address-info-section" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); border-radius: 10px; padding: 8px; box-shadow: 0 2px 12px rgba(138,92,246,.08); border: 1px solid rgba(138,92,246,.1);">
        <h4 style="color: var(--purple-primary); font-size: 12px; font-weight: 700; margin: 0 0 6px 0; display: flex; align-items: center; gap: 4px; padding-bottom: 4px; border-bottom: 1px solid rgba(138,92,246,.15);">
          <i class="fas fa-hotel" style="background: linear-gradient(135deg, var(--purple-primary), #a29bfe); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-size: 12px;"></i>
          Accommodations
        </h4>
        <div class="info-grid">
          <div class="info-item span-2" style="grid-column: span 2;">
            <div id="detail-accommodations-list" style="background: rgba(138,92,246,.03); border-radius: 6px; padding: 6px; min-height: 50px; border: 1px dashed rgba(138,92,246,.2);">
              <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #6c757d; font-style: italic; font-size: 10px;">
                <i class="fas fa-spinner fa-spin" style="margin-right: 4px; color: var(--purple-primary); font-size: 10px;"></i>
                Loading...
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="modal-actions" style="padding: 8px; background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%); border-top: 1px solid rgba(138,92,246,.15); border-radius: 0 0 16px 16px;">
      <button type="button" id="closeRoomDetails" class="action-btn btn-outline" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); border: 2px solid var(--purple-primary); color: var(--purple-primary); padding: 12px 24px; border-radius: 12px; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(138,92,246,.1);">
        <i class="fas fa-times" style="margin-right: 8px;"></i>Close
      </button>
    </div>
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
    
    // Add confirmation + duplicate check for Add Room form
    var roomForm = document.getElementById('roomForm');
    var duplicateMsg = document.getElementById('roomDuplicateMsg');
    if (roomForm) {
      var roomInput = roomForm.querySelector('input[name="room"]');

      // Real-time duplicate check
      roomInput.addEventListener('input', function(){
        var val = this.value.trim().toLowerCase();
        var exists = false;
        document.querySelectorAll('#roomsTable tbody tr').forEach(function(row){
          var roomNo = row.querySelector('td[data-label="Room No."]').textContent.trim().toLowerCase();
          if (roomNo === val) exists = true;
        });
        duplicateMsg.style.display = exists ? 'block' : 'none';
      });

      roomForm.addEventListener('submit', function(e) {
        e.preventDefault();
        var room = roomInput.value;
        var status = document.querySelector('select[name="status"]').value;
        var type = document.querySelector('input[name="type"]').value; // ✅ fixed input selector

        if (duplicateMsg.style.display === 'block') {
          alert('This room number already exists!');
          return;
        }

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
        var roomId = this.getAttribute('data-room-id');
        updateForm.setAttribute('action', '/adminPages/rooms/update/' + roomId);

        openUpdateModal();
      });
    });

    // Room details modal functionality
    var roomDetailsModal = document.getElementById('roomDetailsModal');
    var closeRoomDetailsBtn = document.getElementById('closeRoomDetails');
    var closeRoomDetailsModalBtn = document.getElementById('closeRoomDetailsModal');
    
    function openRoomDetailsModal() { roomDetailsModal.style.display = 'flex'; }
    function closeRoomDetailsModal() { roomDetailsModal.style.display = 'none'; }
    
    if (closeRoomDetailsBtn) closeRoomDetailsBtn.addEventListener('click', closeRoomDetailsModal);
    if (closeRoomDetailsModalBtn) closeRoomDetailsModalBtn.addEventListener('click', closeRoomDetailsModal);

    // Room row click handler
    var roomRows = document.querySelectorAll('.room-row');
    roomRows.forEach(function(row) {
      row.addEventListener('click', function(e) {
        // Don't trigger if clicking on action buttons
        if (e.target.closest('button')) return;
        var r = this.dataset;
        populateRoomDetails({
          id: r.roomId,
          room: r.room,
          level_id: r.levelId,
          status: r.status,
          type: r.type,
          created_at: r.created,
          accommodations: r.accommodations
        });
        openRoomDetailsModal();
      });
    });

    // Populate room details in modal
    function populateRoomDetails(room) {
      document.getElementById('detail-room-number').textContent = room.room || '-';
      document.getElementById('detail-level').textContent = room.level_id || '-';
      document.getElementById('detail-status').textContent = room.status || '-';
      document.getElementById('detail-type').textContent = room.type || '-';
      document.getElementById('detail-created').textContent = room.created_at ? new Date(room.created_at).toLocaleDateString() : '-';
      
      // Load accommodations for this room
      loadRoomAccommodations(room.id);
    }

    // Load accommodations for a specific room
    function loadRoomAccommodations(roomId) {
      var accommodationsListElement = document.getElementById('detail-accommodations-list');
      accommodationsListElement.innerHTML = '<div class="loading" style="display: flex; align-items: center; justify-content: center; gap: 6px; color: #6c757d; font-style: italic; padding: 20px; font-size: 12px;"><i class="fas fa-spinner fa-spin" style="color: var(--purple-primary); font-size: 12px;"></i><span>Loading...</span></div>';
      
      // Fetch accommodations for this room
      fetch('/adminPages/rooms/' + roomId + '/accommodations')
        .then(response => response.json())
        .then(data => {
          if (data.accommodations && data.accommodations.length > 0) {
            var accommodationsHtml = '<div style="display: grid; gap: 6px; max-height: 150px; overflow-y: auto; padding-right: 4px;">';
            data.accommodations.forEach(function(accommodation) {
              accommodationsHtml += '<div class="accommodation-card" style="padding: 8px; background: linear-gradient(135deg, rgba(138,92,246,.05), rgba(138,92,246,.02)); border-radius: 8px; border-left: 3px solid var(--purple-primary); box-shadow: 0 2px 6px rgba(138,92,246,.08); transition: all 0.3s ease; position: relative;">';
              accommodationsHtml += '<div style="display: flex; justify-content: space-between; align-items: center; position: relative; z-index: 1;">';
              accommodationsHtml += '<div style="display: flex; align-items: center; gap: 6px;">';
              accommodationsHtml += '<div style="width: 24px; height: 24px; background: linear-gradient(135deg, var(--purple-primary), #a29bfe); border-radius: 6px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(138,92,246,.3);">';
              accommodationsHtml += '<i class="fas fa-hotel" style="color: white; font-size: 10px;"></i>';
              accommodationsHtml += '</div>';
              accommodationsHtml += '<div>';
              accommodationsHtml += '<strong style="color: var(--text-primary); font-size: 12px; font-weight: 700; display: block;">' + accommodation.name + '</strong>';
              accommodationsHtml += '<small style="color: #6c757d; font-size: 10px;">Capacity: ' + accommodation.capacity + '</small>';
              accommodationsHtml += '</div>';

              accommodationsHtml += '</div>';
              accommodationsHtml += '</div>';
              accommodationsHtml += '</div>';
            });
            accommodationsHtml += '</div>';
            accommodationsListElement.innerHTML = accommodationsHtml;
          } else {
            accommodationsListElement.innerHTML = '<div style="text-align: center; padding: 20px; color: #6c757d;"><div style="width: 40px; height: 40px; background: linear-gradient(135deg, rgba(138,92,246,.1), rgba(138,92,246,.05)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px;"><i class="fas fa-hotel" style="font-size: 16px; color: var(--purple-primary); opacity: 0.6;"></i></div><h4 style="color: #6c757d; margin: 0 0 4px 0; font-weight: 600; font-size: 12px;">No Accommodations</h4><p style="font-style: italic; margin: 0; color: #6c757d; font-size: 10px;">No accommodations found</p></div>';
          }
        })
        .catch(error => {
          console.error('Error loading accommodations:', error);
          accommodationsListElement.innerHTML = '<div style="text-align: center; padding: 20px; color: #dc3545;"><div style="width: 40px; height: 40px; background: linear-gradient(135deg, rgba(220,53,69,.1), rgba(220,53,69,.05)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px;"><i class="fas fa-exclamation-triangle" style="font-size: 16px; color: #dc3545;"></i></div><h4 style="color: #dc3545; margin: 0 0 4px 0; font-weight: 600; font-size: 12px;">Error</h4><p style="font-style: italic; margin: 0; color: #dc3545; font-size: 10px;">Failed to load</p></div>';
        });
    }

    // Archive functionality
    document.querySelectorAll('[data-archive]').forEach(function(btn){
      btn.addEventListener('click', function(e){
        e.stopPropagation();
        var roomId = this.getAttribute('data-room-id');
        var roomNumber = this.closest('tr').querySelector('td[data-label="Room No."]').textContent;
        
        var reason = prompt('Enter reason for archiving room "' + roomNumber + '":');
        if (reason === null) return; 
        if (!reason.trim()) {
          alert('Archive reason is required.');
          return;
        }
        
        if (confirm('Are you sure you want to archive "' + roomNumber + '" with reason: "' + reason + '"?')) {
          var form = document.createElement('form');
          form.method = 'POST';
          form.action = '/adminPages/rooms/archive/' + roomId;
          
          var csrfToken = document.createElement('input');
          csrfToken.type = 'hidden';
          csrfToken.name = '_token';
          csrfToken.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                            document.querySelector('input[name="_token"]')?.value;
          
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

