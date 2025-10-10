@extends('layouts.admindashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/adminrecords.css') }}">
<style>
.status-badge { font-size: 12px; font-weight: 700; border-radius: 999px; padding: 4px 8px; }
.status-pending { background: linear-gradient(135deg, #fff3cd, #fde68a); color:#7a5a00; }
.table .action-btn.small[data-approve]{ border:2px solid #198754; color:#198754; }
.table .action-btn.small[data-approve]:hover{ background:#198754; color:#fff; }
.table .action-btn.small[data-reject]{ border:2px solid var(--dark-red-primary); color:var(--dark-red-primary); }
.table .action-btn.small[data-reject]:hover{ background:var(--dark-red-primary); color:#fff; }
</style>
@endpush

@section('content')
<div class="dashboard-page">
  <div class="page-header">
    <h1 class="page-title">Permission Requests</h1>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <div class="records-toolbar">
    <div class="search-container admin-search">
      <i class="fas fa-search search-icon"></i>
      <input id="permissionSearch" type="text" placeholder="Search requests" class="search-input">
    </div>
    <div></div>
  </div>

  <div class="chart-card card-tight">
    <div class="section-header-pad">
      <h3 class="chart-title">Pending</h3>
    </div>
    <div class="table-wrapper">
      <table class="table sortable-table" id="permissionTable">
        <thead>
          <tr>
            <th>User</th>
            <th>Request Type</th>
            <th>Current Data</th>
            <th>Requested Changes</th>
            <th>Requested Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @if($pendingRequests->count() > 0)
            @foreach($pendingRequests as $request)
              <tr>
                <td data-label="User">
                  <strong>{{ $request->user->name }}</strong><br>
                  <small class="text-muted">{{ $request->user->email }}</small>
                </td>
                <td data-label="Request Type">
                  <span class="status-badge status-pending">{{ ucfirst(str_replace('_', ' ', $request->request_type)) }}</span>
                </td>
                <td data-label="Current Data">
                  @if($request->request_type === 'personal_info')
                    <small>
                      <strong>Name:</strong> {{ $request->current_data['firstName'] }} {{ $request->current_data['lastName'] }}<br>
                      <strong>Contact:</strong> {{ $request->current_data['contactNumber'] }}<br>
                      <strong>Birthday:</strong> {{ $request->current_data['birthday'] }}<br>
                      <strong>Sex:</strong> {{ $request->current_data['sex'] }}
                      @if(isset($request->current_data['address']))
                        <br><strong>Address:</strong> {{ $request->current_data['address']['street'] }}, {{ $request->current_data['address']['city'] }}, {{ $request->current_data['address']['province'] }}
                      @endif
                    </small>
                  @elseif($request->request_type === 'email')
                    <small>{{ $request->current_data['email'] }}</small>
                  @endif
                </td>
                <td data-label="Requested Changes">
                  @if($request->request_type === 'personal_info')
                    <small>
                      <strong>Name:</strong> {{ $request->request_data['firstName'] }} {{ $request->request_data['lastName'] }}<br>
                      <strong>Contact:</strong> {{ $request->request_data['contactNumber'] }}<br>
                      <strong>Birthday:</strong> {{ $request->request_data['birthday'] }}<br>
                      <strong>Sex:</strong> {{ $request->request_data['sex'] }}
                      @if(isset($request->request_data['address']))
                        <br><strong>Address:</strong> {{ $request->request_data['address']['street'] }}, {{ $request->request_data['address']['city'] }}, {{ $request->request_data['address']['province'] }}
                      @endif
                    </small>
                  @elseif($request->request_type === 'email')
                    <small>{{ $request->request_data['email'] }}</small>
                  @endif
                </td>
                <td data-label="Requested Date">
                  <small>{{ $request->created_at->format('M d, Y H:i') }}</small>
                </td>
                <td data-label="Actions">
                  <button class="action-btn small" data-approve onclick="openApprove({{ $request->id }})"><i class="fas fa-check"></i></button>
                  <button class="action-btn small" data-reject onclick="openReject({{ $request->id }})"><i class="fas fa-times"></i></button>
                </td>
              </tr>
            @endforeach
          @else
            <tr>
              <td colspan="6" class="text-center">No pending permission requests</td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="modal">
  <div class="modal-card">
    <div class="modal-header">
      <h3 class="chart-title">Approve Request</h3>
      <button id="closeApprove" class="action-btn ml-auto"><i class="fas fa-times"></i></button>
    </div>
    <form id="approveForm" class="modal-form" method="POST">
      @csrf
      <div class="tab-content active" id="approve-tab">
        <div class="form-grid">
          <div class="form-group span-2">
            <label>Admin Notes (Optional)</label>
            <textarea class="form-input" id="approve_notes" name="admin_notes" rows="3" placeholder="Add any notes about this approval..."></textarea>
          </div>
        </div>
      </div>
      <div class="modal-actions">
        <button type="button" id="cancelApprove" class="action-btn btn-outline">Cancel</button>
        <button type="submit" class="btn-primary inline">Approve Request</button>
      </div>
    </form>
  </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="modal">
  <div class="modal-card">
    <div class="modal-header">
      <h3 class="chart-title">Reject Request</h3>
      <button id="closeReject" class="action-btn ml-auto"><i class="fas fa-times"></i></button>
    </div>
    <form id="rejectForm" class="modal-form" method="POST">
      @csrf
      <div class="tab-content active" id="reject-tab">
        <div class="form-grid">
          <div class="form-group span-2">
            <label>Reason for Rejection</label>
            <textarea class="form-input" id="reject_notes" name="admin_notes" rows="3" placeholder="Please provide a reason for rejecting this request..." required></textarea>
          </div>
        </div>
      </div>
      <div class="modal-actions">
        <button type="button" id="cancelReject" class="action-btn btn-outline">Cancel</button>
        <button type="submit" class="btn-primary inline">Reject Request</button>
      </div>
    </form>
  </div>
</div>

<script>
(function(){
  // search
  var search = document.getElementById('permissionSearch');
  var tableBody = document.getElementById('permissionTable').getElementsByTagName('tbody')[0];
  if (search) search.addEventListener('input', function(){
    var q = this.value.toLowerCase();
    Array.prototype.forEach.call(tableBody.rows, function(row){
      var text = row.innerText.toLowerCase();
      row.style.display = text.indexOf(q) !== -1 ? '' : 'none';
    });
  });

  // approve modal controls
  var approveModal = document.getElementById('approveModal');
  var closeApprove = document.getElementById('closeApprove');
  var cancelApprove = document.getElementById('cancelApprove');
  window.openApprove = function(id){
    var form = document.getElementById('approveForm');
    form.action = '{{ route("adminPages.permission-requests.approve", ":id") }}'.replace(':id', id);
    approveModal.style.display = 'flex';
  };
  function hideApprove(){ approveModal.style.display = 'none'; }
  if (closeApprove) closeApprove.addEventListener('click', hideApprove);
  if (cancelApprove) cancelApprove.addEventListener('click', hideApprove);

  // reject modal controls
  var rejectModal = document.getElementById('rejectModal');
  var closeReject = document.getElementById('closeReject');
  var cancelReject = document.getElementById('cancelReject');
  window.openReject = function(id){
    var form = document.getElementById('rejectForm');
    form.action = '{{ route("adminPages.permission-requests.reject", ":id") }}'.replace(':id', id);
    rejectModal.style.display = 'flex';
  };
  function hideReject(){ rejectModal.style.display = 'none'; }
  if (closeReject) closeReject.addEventListener('click', hideReject);
  if (cancelReject) cancelReject.addEventListener('click', hideReject);

  // Auto-align table cells: numbers right, text left
  function isNumericValue(text){
    if (text == null) return false;
    var t = String(text).trim().replace(/[,\s]/g, '');
    if (t === '') return false;
    t = t.replace(/^[-₱$€¥£]/, '');
    return !isNaN(t) && isFinite(t);
  }
  function alignPermissionTable(){
    try {
      var tbody = document.getElementById('permissionTable').getElementsByTagName('tbody')[0];
      Array.prototype.forEach.call(tbody.rows, function(row){
        Array.prototype.forEach.call(row.cells, function(cell){
          var text = cell.textContent || '';
          cell.style.textAlign = isNumericValue(text) ? 'right' : 'left';
        });
      });
    } catch(e) {}
  }
  // initial align and on search
  alignPermissionTable();
  if (search) search.addEventListener('input', alignPermissionTable);
})();
</script>
@endsection

