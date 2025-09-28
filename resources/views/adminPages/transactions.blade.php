@extends('layouts.admindashboard')

@section('title','Transactions')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/adminrecords.css') }}">
<link rel="stylesheet" href="{{ asset('css/room-dashboard.css') }}">
<script src="{{ asset('js/ph-complete-address.js') }}"></script>
<style>
/* Floor and Status Filter Button Styles */
.floor-selector .floor-btn,
.status-filters .status-btn {
  background-color: transparent;
  color: var(--purple-primary);
  border: 2px solid var(--purple-primary);
  padding: 8px 16px;
  border-radius: 8px;
  font-weight: 500;
  transition: all 0.2s ease;
  cursor: pointer;
}

.floor-selector .floor-btn:hover,
.status-filters .status-btn:hover {
  background-color: var(--purple-primary);
  color: white;
  border-color: var(--purple-primary);
}

.floor-selector .floor-btn.active,
.status-filters .status-btn.active {
  background-color: var(--purple-primary);
  color: white;
  border-color: var(--purple-primary);
}

/* Modal Header Fixed and Progress Bar Styles */
.modal-content {
  display: flex;
  flex-direction: column;
  height: 90vh;
  max-height: 90vh;
}

.modal-header {
  flex-shrink: 0;
  position: sticky;
  top: 0;
  z-index: 10;
  background: #f8f9fa;
  border-bottom: 1px solid #e0e0e0;
}

.modal-body {
  flex: 1;
  overflow-y: auto;
  padding: 20px;
}

/* Progress Bar Styles */
.progress-container {
  background: #f8f9fa;
  padding: 15px 20px;
  border-bottom: 1px solid #e0e0e0;
  flex-shrink: 0;
}

.progress-bar {
  width: 100%;
  height: 8px;
  background-color: #e9ecef;
  border-radius: 4px;
  overflow: hidden;
  margin-bottom: 10px;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, var(--purple-primary), #8B5CF6);
  border-radius: 4px;
  transition: width 0.3s ease;
  width: 0%;
}

.progress-steps {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.progress-step {
  display: flex;
  flex-direction: column;
  align-items: center;
  flex: 1;
  position: relative;
}

.progress-step:not(:last-child)::after {
  content: '';
  position: absolute;
  top: 12px;
  left: 50%;
  width: 100%;
  height: 2px;
  background-color: #e9ecef;
  z-index: 1;
}

.progress-step.completed:not(:last-child)::after {
  background-color: var(--purple-primary);
}

.progress-step-icon {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background-color: #e9ecef;
  color: #6c757d;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: bold;
  position: relative;
  z-index: 2;
  transition: all 0.3s ease;
}

.progress-step.active .progress-step-icon {
  background-color: var(--purple-primary);
  color: white;
}

.progress-step.completed .progress-step-icon {
  background-color: var(--purple-primary);
  color: white;
}

.progress-step-label {
  font-size: 11px;
  color: #6c757d;
  margin-top: 5px;
  text-align: center;
  font-weight: 500;
}

.progress-step.active .progress-step-label {
  color: var(--purple-primary);
  font-weight: 600;
}

.progress-step.completed .progress-step-label {
  color: var(--purple-primary);
  font-weight: 600;
}

/* Guest Form Header and Delete Button Styles */
.guest-form-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
  padding-bottom: 10px;
  border-bottom: 1px solid #e0e0e0;
}

.guest-form-header h4 {
  margin: 0;
  color: var(--purple-primary);
  font-size: 16px;
  font-weight: 600;
}

.delete-guest-btn {
  background-color: transparent;
  color: #dc3545;
  border: 2px solid #dc3545;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 500;
  transition: all 0.2s ease;
  cursor: pointer;
}

.delete-guest-btn:hover {
  background-color: #dc3545;
  color: white;
  border-color: #dc3545;
}

.delete-guest-btn i {
  margin-right: 4px;
}

/* Room Card Styles */
.room-box {
  border: 3px solid;
  background-color: transparent;
  transition: all 0.2s ease;
}

.room-box.available {
  border-color: #28a745;
  color: #28a745;
}

.room-box.available:hover {
  background-color: rgba(40, 167, 69, 0.1);
  color: #28a745;
}

.room-box.in-use {
  border-color: #dc3545;
  color: #dc3545;
}

.room-box.in-use:hover {
  background-color: rgba(220, 53, 69, 0.1);
  color: #dc3545;
}

.room-box.active {
  border-color: #28a745;
  color: #28a745;
}

.room-box.active:hover {
  background-color: rgba(40, 167, 69, 0.1);
  color: #28a745;
}

/* Receipt Styling */
.receipt-container {
  background: white;
  padding: 20px;
  font-family: 'Courier New', monospace;
  font-size: 12px;
  line-height: 1.4;
  color: #000;
  max-width: 300px;
  margin: 0 auto;
}

.receipt-header {
  text-align: center;
  border-bottom: 2px dashed #000;
  padding-bottom: 15px;
  margin-bottom: 15px;
}

.receipt-title {
  font-size: 18px;
  font-weight: bold;
  margin-bottom: 5px;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.receipt-subtitle {
  font-size: 10px;
  margin-bottom: 5px;
  color: #666;
}

.receipt-address {
  font-size: 10px;
  margin-bottom: 10px;
  color: #666;
}

.receipt-details {
  margin-bottom: 15px;
}

.receipt-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 3px;
}

.receipt-row.total {
  border-top: 1px dashed #000;
  padding-top: 8px;
  margin-top: 8px;
  font-weight: bold;
  font-size: 14px;
}

.receipt-row.grand-total {
  border-top: 2px solid #000;
  padding-top: 8px;
  margin-top: 8px;
  font-weight: bold;
  font-size: 16px;
  text-transform: uppercase;
}

.receipt-footer {
  text-align: center;
  border-top: 2px dashed #000;
  padding-top: 15px;
  margin-top: 15px;
  font-size: 10px;
  color: #666;
}

.receipt-thank-you {
  font-size: 12px;
  font-weight: bold;
  margin-bottom: 10px;
  text-transform: uppercase;
}

.receipt-date-time {
  margin-bottom: 5px;
}

.receipt-receipt-no {
  font-weight: bold;
  margin-bottom: 10px;
}

.receipt-cashier {
  margin-bottom: 5px;
}


/* Print Styles for Receipt */
@media print {
  .receipt-actions {
    display: none !important;
  }
  
  .modal-content {
    box-shadow: none !important;
    border: none !important;
    margin: 0 !important;
    padding: 0 !important;
  }
  
  .receipt-container {
    max-width: none !important;
    width: 100% !important;
    margin: 0 !important;
    padding: 15px !important;
  }
  
  body {
    margin: 0 !important;
    padding: 0 !important;
  }
  
  .modal {
    position: static !important;
    display: block !important;
  }
}
</style>
@endpush

@section('content')
<div class="room-dashboard">
  <div class="page-header">
        <h1 class="page-title">Room Management</h1>
  </div>

    <!-- Floor Selector -->
    <div class="floor-selector">
      <button class="floor-btn active" data-floor="all">All Floors</button>
      @if(isset($levels))
        @foreach($levels as $level)
          <button class="floor-btn" data-floor="{{ $level->id }}">{{ $level->description }}</button>
        @endforeach
      @endif
    </div>

    <!-- Status Filters -->
    <div class="status-filters">
      <button class="status-btn active" data-status="all">All Rooms</button>
      <button class="status-btn" data-status="Available">Available</button>
      <button class="status-btn" data-status="In Use">In Use</button>
    </div>

    <!-- Rooms Grid -->
    <div class="rooms-grid" id="roomsGrid">
      @if(isset($rooms))
        @foreach($rooms as $room)
          <div class="room-box {{ strtolower(str_replace(' ', '-', $room->status)) }}" 
               data-room-id="{{ $room->id }}"
               data-level="{{ $room->level_id }}"
               data-status="{{ $room->status }}"
               data-capacity="{{ $room->accommodations->sum('capacity') }}">
            <div class="room-number">{{ $room->room }}</div>
            <div class="room-level">{{ optional($room->level)->description ?? '-' }}</div>
            <div class="room-status">{{ $room->status }}</div>
            @php($__status = strtolower(trim($room->status ?? '')))
            @if(strpos($__status, 'use') !== false)
              <div class="room-timer" id="timer-{{ $room->id }}">--:--:--</div>
            @endif
          </div>
        @endforeach
      @endif
    </div>
  </div>

<!-- Accommodation Modal -->
<div id="accommodationModal" class="modal">
    <div class="modal-content">
    <div class="modal-header">
            <h2 class="modal-title" id="modalTitle">Room Details</h2>
            <span class="close">&times;</span>
        </div>
        
        <!-- Progress Bar -->
        <div class="progress-container">
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill"></div>
            </div>
            <div class="progress-steps">
                <div class="progress-step" id="step1">
                    <div class="progress-step-icon">1</div>
                    <div class="progress-step-label">Accommodation</div>
                </div>
                <div class="progress-step" id="step2">
                    <div class="progress-step-icon">2</div>
                    <div class="progress-step-label">Guest Info</div>
                </div>
                <div class="progress-step" id="step3">
                    <div class="progress-step-icon">3</div>
                    <div class="progress-step-label">Duration</div>
                </div>
                <div class="progress-step" id="step4">
                    <div class="progress-step-icon">4</div>
                    <div class="progress-step-label">Payment</div>
                </div>
            </div>
        </div>
        
        <div class="modal-body">
            <div id="accommodationInfo" class="accommodation-info">
                <!-- Accommodation details will be loaded here -->
        </div>

            <div id="guestFormSection" class="guest-form-section hidden">
                <h3 class="section-title">Guest Information</h3>
                <div id="guestForms">
                    <!-- Guest forms will be generated here -->
        </div>
                <button type="button" class="btn btn-secondary" id="addGuestBtn">Add Another Guest</button>
        </div>

            <div id="rateSelection" class="rate-selection hidden">
                <h3 class="section-title">Select Duration</h3>
                <div id="rateOptions" class="rate-options">
                    <!-- Rate options will be loaded here -->
        </div>
      </div>

            <div id="paymentSummary" class="payment-summary hidden">
                <h3 class="section-title">Payment Summary</h3>
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span id="subtotalAmount">₱0.00</span>
      </div>
                <div class="summary-row">
                    <span>Tax (12%):</span>
                    <span id="taxAmount">₱0.00</span>
  </div>
                <div class="summary-row summary-total">
                    <span>Total:</span>
                    <span id="totalAmount">₱0.00</span>
</div>

                <div class="payment-inputs">
                    <div class="form-group">
                        <label class="form-label">Amount Paid</label>
                        <input type="number" class="form-input" id="amountPaid" step="0.01" min="0">
    </div>
                    <div class="form-group">
                        <label class="form-label">Change</label>
                        <input type="number" class="form-input" id="changeAmount" step="0.01" readonly>
          </div>
        </div>
      </div>
      
            <div class="loading" id="loading">
                <div class="spinner"></div>
                <p>Processing...</p>
            </div>

            <div style="text-align: center; margin-top: 20px;">
                <button type="button" class="btn btn-primary hidden" id="proceedBtn">Proceed with Accommodation</button>
                <button type="button" class="btn btn-success hidden" id="processPaymentBtn">Process Payment</button>
                <button type="button" class="btn btn-danger hidden" id="deleteStayBtn">Delete Stay</button>
                <button type="button" class="btn btn-secondary" id="cancelBtn">Cancel</button>
          </div>
        </div>
      </div>
    </div>
    
  <!-- Extend/Timeout Modal -->
  <div id="extendTimeoutModal" class="modal" style="display:none;">
    <div class="modal-content" style="max-width: 420px;">
      <div class="modal-header">
        <h2 class="modal-title">Time's Up</h2>
        <span class="close" id="closeExtendTimeout">&times;</span>
      </div>
      <div class="modal-body">
        <p id="extendTimeoutMessage">The time for this room has ended. Would you like to extend the stay or mark it as timed out?</p>
        <div style="display:flex; gap: 8px; justify-content: flex-end; margin-top: 12px;">
          <button type="button" class="btn btn-secondary" id="timeoutBtn">Time Out</button>
          <button type="button" class="btn btn-primary" id="extendBtn">Extend</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Receipt Modal -->
  <div id="receiptModal" class="modal" style="display:none;">
    <div class="modal-content" style="max-width: 400px; padding: 0;">
      <div class="receipt-container" id="receiptContent">
        <!-- Receipt content will be generated here -->
      </div>
      <div class="receipt-actions" style="padding: 20px; text-align: center; background: #f8f9fa;">
        <button type="button" class="btn btn-primary" id="printReceiptBtn" style="margin-right: 10px;">
          <i class="fas fa-print"></i> Print Receipt
        </button>
        <button type="button" class="btn btn-secondary" id="closeReceiptBtn">
          <i class="fas fa-times"></i> Close
        </button>
      </div>
    </div>
  </div>

<script>
// Room Dashboard Functionality
let currentRoom = null;
let selectedRate = null;
let guestCount = 0;
let maxCapacity = 0;
let selectedAccommodation = null;
let roomIdToCheckout = {};
let roomIdToStayId = {};
let roomIdToAccommodationId = {};
let roomIdToGuestCount = {};
let extensionMode = false; // false => new stay (Standard/Extending-Standard), true => extend (Extending/Extending-Standard)
let pendingExtend = null; // { roomId, stayId }
let currentStep = 1; // Track current step for progress bar
function enableProcessPaymentButton() {
    var btn = document.getElementById('processPaymentBtn');
    if (!btn) return;
    
    // Remove hidden class first
    btn.classList.remove('hidden');
    
    // Force display properties
    btn.style.display = 'inline-block';
    btn.style.visibility = 'visible';
    btn.style.opacity = '1';
    btn.style.pointerEvents = 'auto';
    
    // Enable button functionality
    btn.removeAttribute('disabled');
    btn.disabled = false;
    btn.setAttribute('aria-disabled', 'false');
    btn.tabIndex = 0;
    
    // Reset submitting state
    btn.dataset.submitting = '0';
}

function updateProgressBar(step) {
    currentStep = step;
    
    // Update progress fill
    const progressFill = document.getElementById('progressFill');
    const progressPercentage = ((step - 1) / 3) * 100; // 4 steps total, so divide by 3
    progressFill.style.width = progressPercentage + '%';
    
    // Update step indicators
    for (let i = 1; i <= 4; i++) {
        const stepElement = document.getElementById(`step${i}`);
        stepElement.classList.remove('active', 'completed');
        
        if (i < step) {
            stepElement.classList.add('completed');
        } else if (i === step) {
            stepElement.classList.add('active');
        }
    }
}

function resetProgressBar() {
    currentStep = 1;
    updateProgressBar(1);
}

// Initialize the dashboard
document.addEventListener('DOMContentLoaded', function() {
    initializeEventListeners();
    
    // Load active stays for timers then start ticking
    fetchActiveStays().then(() => {
        updateRoomTimers();
        setInterval(updateRoomTimers, 1000);
    });
});

function initializeEventListeners() {
    // Floor filter buttons
    document.querySelectorAll('.floor-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.floor-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            filterRooms();
        });
    });

    // Status filter buttons
    document.querySelectorAll('.status-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.status-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            filterRooms();
        });
    });

    // Room box clicks
    document.querySelectorAll('.room-box').forEach(box => {
        box.addEventListener('click', function() {
            const status = (this.dataset.status || '').toLowerCase();
            if (status === 'available' || status === 'active') {
                extensionMode = false;
                openAccommodationModal(this.dataset.roomId);
            } else if (status === 'in use') {
                extensionMode = true;
                openAccommodationModal(this.dataset.roomId);
            }
        });
    });

    // Modal close
    document.querySelector('.close').addEventListener('click', closeModal);
    document.getElementById('cancelBtn').addEventListener('click', closeModal);

  // Extend/Timeout modal handlers
  var extendTimeoutModal = document.getElementById('extendTimeoutModal');
  var closeExtendTimeout = document.getElementById('closeExtendTimeout');
  var extendBtn = document.getElementById('extendBtn');
  var timeoutBtn = document.getElementById('timeoutBtn');
  if (closeExtendTimeout) closeExtendTimeout.addEventListener('click', function(){ extendTimeoutModal.style.display = 'none'; pendingExtend = null; });
  if (extendBtn) extendBtn.addEventListener('click', function(){
    if (!pendingExtend) return;
    if (!confirm('Are you sure you want to extend this stay?')) return;
    extendTimeoutModal.style.display = 'none';
    // Directly extend using the last selected accommodation's extending statuses
    // We will prompt to choose duration only, no guests again
    extensionMode = true;
    openAccommodationModal(String(pendingExtend.roomId));
  });
  if (timeoutBtn) timeoutBtn.addEventListener('click', function(){
    if (!pendingExtend) return;
    const stayId = String(pendingExtend.stayId);
    extendTimeoutModal.style.display = 'none';
    fetch(`/adminPages/stays/end/${stayId}`, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
    }).then(() => setTimeout(() => location.reload(), 400)).catch(()=>{});
    pendingExtend = null;
  });
    
    // Proceed button
    document.getElementById('proceedBtn').addEventListener('click', showGuestForm);
    
    // Add guest button
    document.getElementById('addGuestBtn').addEventListener('click', addGuestForm);
    
    // Process payment button (branch based on mode)
    (function(){
        var btn = document.getElementById('processPaymentBtn');
        if (btn) {
            btn.addEventListener('click', function handler(){
                // Guard against duplicate submissions
                if (btn.dataset.submitting === '1') return;
                btn.dataset.submitting = '1';
                
                if (extensionMode) {
                    processExtension();
                } else {
                    processPayment();
                }
                // release flag after a short delay (request will reload on success)
                setTimeout(function(){ btn.dataset.submitting = '0'; }, 1500);
            });
        }
    })();
    
    // Amount paid input
    document.getElementById('amountPaid').addEventListener('input', calculateChange);
    
    // Delete stay button
    document.getElementById('deleteStayBtn').addEventListener('click', deleteStay);
    
    // Receipt modal buttons
    document.getElementById('printReceiptBtn').addEventListener('click', printReceipt);
    document.getElementById('closeReceiptBtn').addEventListener('click', closeReceiptModal);
}

function filterRooms() {
    const selectedFloor = document.querySelector('.floor-btn.active').dataset.floor;
    const selectedStatus = document.querySelector('.status-btn.active').dataset.status;
    
    document.querySelectorAll('.room-box').forEach(box => {
        const roomFloor = box.dataset.level;
        let roomStatus = box.dataset.status || '';
        // Normalize 'Active' to 'Available' so filters and clicks behave consistently
        if (roomStatus.toLowerCase() === 'active') {
            roomStatus = 'Available';
        }
        
        let showRoom = true;
        
        if (selectedFloor !== 'all' && roomFloor !== selectedFloor) {
            showRoom = false;
        }
        
        if (selectedStatus !== 'all' && roomStatus !== selectedStatus) {
            showRoom = false;
        }
        
        box.style.display = showRoom ? 'flex' : 'none';
    });
}

function openAccommodationModal(roomId) {
    fetch(`/adminPages/rooms/details/${roomId}`)
        .then(response => response.json())
        .then(data => {
            // Support both payloads: { success: true, room: {...} } and direct room object
            const isWrapped = typeof data === 'object' && data !== null && Object.prototype.hasOwnProperty.call(data, 'success');
            const success = isWrapped ? data.success : !!data && !!data.id;
            const roomPayload = isWrapped ? data.room : data;

            if (success && roomPayload) {
                currentRoom = roomPayload;
                // Wait for user to select an accommodation before setting capacity
                maxCapacity = 0;
                displayAccommodationInfo(roomPayload);
                document.getElementById('accommodationModal').style.display = 'block';
                // Auto-select accommodation for extension mode
                if (extensionMode) {
                    try {
                        const accId = roomIdToAccommodationId[String(roomId)];
                        if (accId) {
                            // Find matching accommodation object from currentRoom
                            const accObj = (currentRoom.accommodations || []).find(a => String(a.id) === String(accId));
                            if (accObj) {
                                selectedAccommodation = accObj;
                            }
                        }
                        if (!selectedAccommodation && currentRoom.accommodations && currentRoom.accommodations.length > 0) {
                            selectedAccommodation = currentRoom.accommodations[0];
                        }
                        if (selectedAccommodation) {
                            document.getElementById('proceedBtn').classList.add('hidden');
                            loadRateOptions();
                            document.getElementById('rateSelection').classList.remove('hidden');
                            // For extension flow we still require payment input; show summary after duration selection
                        }
                    } catch (e) {}
                }
            } else {
                const message = isWrapped ? (data.message || 'Unknown error') : 'Unexpected response format';
                alert('Failed to load room details: ' + message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load room details');
        });
}

function displayAccommodationInfo(room) {
    const infoDiv = document.getElementById('accommodationInfo');
    const title = document.getElementById('modalTitle');
    
    const modeSuffix = extensionMode ? ' (Extend Stay)' : '';
    const levelDesc = room.level && room.level.description ? room.level.description : '-';
    title.textContent = `Room ${room.room} - ${levelDesc}${modeSuffix}`;
    
    // Reset progress bar to step 1
    resetProgressBar();
    
    if (extensionMode) {
        // Hide accommodation chooser when extending (auto-selected)
        infoDiv.innerHTML = '';
        infoDiv.style.display = 'none';
        const proceedBtn = document.getElementById('proceedBtn');
        proceedBtn.classList.add('hidden');
        
        // Show delete button for "In Use" rooms
        const deleteBtn = document.getElementById('deleteStayBtn');
        deleteBtn.classList.remove('hidden');
        
        // Skip to step 3 for extension mode
        updateProgressBar(3);
        return;
    }

    infoDiv.style.display = 'block';
    let html = `
        <div class="accommodation-name">Choose an Accommodation</div>
        <div class="accommodation-options" id="accommodationOptions"></div>
        <div class="helper-text">Select one to proceed.</div>
    `;
    infoDiv.innerHTML = html;

    const optionsContainer = document.getElementById('accommodationOptions');
    optionsContainer.innerHTML = '';

    room.accommodations.forEach(acc => {
        const option = document.createElement('div');
        option.className = 'accommodation-option';
        option.dataset.accommodationId = acc.id;
        option.innerHTML = `
            <div class="acc-name">${acc.name}</div>
            <div class="acc-meta">
                <span class="acc-capacity">Capacity: ${acc.capacity}</span>
            </div>
            <div class="acc-desc">${acc.description || 'No description provided.'}</div>
        `;
        option.addEventListener('click', function() {
            document.querySelectorAll('.accommodation-option').forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');
            selectedAccommodation = acc;
            maxCapacity = acc.capacity;
            // Enable proceed button after selection
            const proceedBtn = document.getElementById('proceedBtn');
            proceedBtn.disabled = false;
            proceedBtn.classList.remove('hidden');
            // Update progress to step 2
            updateProgressBar(2);
        });
        optionsContainer.appendChild(option);
    });

    // Show proceed button but keep disabled until a selection is made
    const proceedBtn = document.getElementById('proceedBtn');
    proceedBtn.classList.remove('hidden');
    proceedBtn.disabled = true;
    proceedBtn.textContent = 'Proceed with Accommodation';
}

function showGuestForm() {
    if (!selectedAccommodation) {
        alert('Please select an accommodation first.');
        return;
    }
    document.getElementById('proceedBtn').classList.add('hidden');
    if (extensionMode) {
        // In extension mode, skip guest forms and directly load rate options
        guestCount = 1; // charge baseline of 1 unless you want to persist last guest count
        document.getElementById('guestFormSection').classList.add('hidden');
        loadRateOptions();
        document.getElementById('rateSelection').classList.remove('hidden');
        // Update progress to step 3
        updateProgressBar(3);
    } else {
        document.getElementById('guestFormSection').classList.remove('hidden');
        // Generate initial guest form
        addGuestForm();
        // Update progress to step 2
        updateProgressBar(2);
    }
}

function addGuestForm() {
    if (guestCount >= maxCapacity) {
        alert(`Maximum capacity is ${maxCapacity} guests`);
        return;
    }
    
    guestCount++;
    const formsDiv = document.getElementById('guestForms');
    
    // If this is the first guest form, also show rate selection
    if (guestCount === 1) {
        loadRateOptions();
    }
    
    const guestForm = document.createElement('div');
    guestForm.className = 'guest-form';
    guestForm.dataset.guestIndex = guestCount - 1;
    guestForm.innerHTML = `
        <div class="guest-form-header">
        <h4>Guest ${guestCount}</h4>
            <button type="button" class="btn btn-danger btn-sm delete-guest-btn" onclick="deleteGuestForm(${guestCount - 1})">
                <i class="fas fa-trash"></i> Remove Guest
            </button>
        </div>
        <div class="form-row">
        <div class="form-group">
                <label class="form-label">First Name *</label>
                <input type="text" class="form-input" name="guests[${guestCount-1}][firstName]" required>
        </div>
        <div class="form-group">
                <label class="form-label">Middle Name</label>
                <input type="text" class="form-input" name="guests[${guestCount-1}][middleName]">
        </div>
        <div class="form-group">
                <label class="form-label">Last Name *</label>
                <input type="text" class="form-input" name="guests[${guestCount-1}][lastName]" required>
            </div>
        </div>
        <div class="form-row">
        <div class="form-group">
                <label class="form-label">Contact Number</label>
                <input type="text" class="form-input" name="guests[${guestCount-1}][number]">
            </div>
        </div>
        <div class="address-section">
            <div class="address-title">Address Information</div>
            <div class="form-row">
        <div class="form-group">
                    <label class="form-label">Street *</label>
                    <input type="text" class="form-input" name="guests[${guestCount-1}][address][street]" required>
        </div>
      </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Province *</label>
                    <select name="guests[${guestCount-1}][address][province]" class="form-input" required>
                        <option value="">Select Province</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">City *</label>
                    <select name="guests[${guestCount-1}][address][city]" class="form-input" required>
                        <option value="">Select City</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">ZIP Code *</label>
                    <input type="text" class="form-input" name="guests[${guestCount-1}][address][zipcode]" readonly>
      </div>
  </div>
</div>
    `;
    
    formsDiv.appendChild(guestForm);
    
    // Initialize address dropdowns for this guest form
    initializeGuestAddressDropdowns(guestCount - 1);
}

function deleteGuestForm(guestIndex) {
    // Show confirmation dialog
    if (!confirm('Are you sure you want to remove this guest? This action cannot be undone.')) {
        return;
    }
    
    // Find the guest form to remove
    const guestForm = document.querySelector(`[data-guest-index="${guestIndex}"]`);
    if (!guestForm) {
        console.error('Guest form not found for index:', guestIndex);
        return;
    }
    
    // Remove the guest form
    guestForm.remove();
    
    // Decrease guest count
    guestCount--;
    
    // Re-number remaining guest forms
    renumberGuestForms();
    
    // Recalculate total if rate is selected
    if (selectedRate) {
        calculateTotal();
    }
}

function renumberGuestForms() {
    const guestForms = document.querySelectorAll('.guest-form');
    guestForms.forEach((form, index) => {
        const header = form.querySelector('.guest-form-header h4');
        const deleteBtn = form.querySelector('.delete-guest-btn');
        
        if (header) {
            header.textContent = `Guest ${index + 1}`;
        }
        
        if (deleteBtn) {
            // Update the onclick attribute with new index
            deleteBtn.setAttribute('onclick', `deleteGuestForm(${index})`);
        }
        
        // Update the data-guest-index attribute
        form.dataset.guestIndex = index;
        
        // Update all input names to reflect new indices
        const inputs = form.querySelectorAll('input, select');
        inputs.forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                // Replace the guest index in the name attribute
                const newName = name.replace(/guests\[\d+\]/, `guests[${index}]`);
                input.setAttribute('name', newName);
            }
        });
    });
}

function loadRateOptions() {
    // Get rates for the selected accommodation
    const accommodationId = selectedAccommodation ? selectedAccommodation.id : null;
    if (!accommodationId) return;
    
    fetch(`/adminPages/stays/rates/${accommodationId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Filter by status depending on mode
                const allowed = extensionMode
                    ? ['Extending', 'Extending/Standard']
                    : ['Standard', 'Extending/Standard'];
                const filtered = (data.rates || []).filter(r => allowed.includes(r.status));
                displayRateOptions(filtered);
                document.getElementById('rateSelection').classList.remove('hidden');
                // Update progress to step 3
                updateProgressBar(3);
            } else {
                alert('Failed to load rates: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load rates');
        });
}

function displayRateOptions(rates) {
    const optionsDiv = document.getElementById('rateOptions');
    optionsDiv.innerHTML = '';
    
    rates.forEach(rate => {
        const option = document.createElement('div');
        option.className = 'rate-option';
        option.dataset.rateId = rate.id;
        option.innerHTML = `
            <div class="rate-duration">${formatDurationDisplay(rate.duration)}</div>
            <div class="rate-price">₱${parseFloat(rate.price).toFixed(2)}</div>
        `;
        
        option.addEventListener('click', function() {
            document.querySelectorAll('.rate-option').forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            selectedRate = rate;
            
            // Debug: Log the selected rate
            console.log('Selected Rate:', {
                id: rate.id,
                duration: rate.duration,
                formattedDuration: formatDurationDisplay(rate.duration),
                price: rate.price,
                status: rate.status
            });
            
            // Calculate totals and show payment UI for both new stays and extensions
            if (extensionMode) {
                // For extensions, use the previous guest count
                const gc = roomIdToGuestCount[String(currentRoom.id)] || 1;
                guestCount = gc; // Update guestCount for consistency
            }
            
            calculateTotal();
        });
        
        optionsDiv.appendChild(option);
    });
}

function calculateTotal() {
    if (!selectedRate) return;
    
    // Ensure guestCount is at least 1
    if (guestCount < 1) {
        guestCount = 1;
    }
    
    const subtotal = selectedRate.price * guestCount;
    const tax = subtotal * 0.12;
    const total = subtotal + tax;
    
    document.getElementById('subtotalAmount').textContent = `₱${subtotal.toFixed(2)}`;
    document.getElementById('taxAmount').textContent = `₱${tax.toFixed(2)}`;
    document.getElementById('totalAmount').textContent = `₱${total.toFixed(2)}`;
    
    // Show payment summary and process button for both new stays and extensions
    document.getElementById('paymentSummary').classList.remove('hidden');
    document.getElementById('processPaymentBtn').classList.remove('hidden');
    
    // Enable the process payment button
    enableProcessPaymentButton();
    
    // Pre-fill amount paid with total for convenience
    document.getElementById('amountPaid').value = total.toFixed(2);
    document.getElementById('changeAmount').value = '0.00';
    
    // Update progress to step 4
    updateProgressBar(4);
}

function calculateChange() {
    const amountPaid = parseFloat(document.getElementById('amountPaid').value) || 0;
    const total = parseFloat(document.getElementById('totalAmount').textContent.replace('₱', '').replace(',', ''));
    const change = amountPaid - total;
    
    document.getElementById('changeAmount').value = change >= 0 ? change.toFixed(2) : '0.00';
}

function processPayment() {
    const amountPaid = parseFloat(document.getElementById('amountPaid').value) || 0;
    const total = parseFloat(document.getElementById('totalAmount').textContent.replace('₱', '').replace(',', ''));
    const computedChange = Math.max(amountPaid - total, 0);
    
    if (amountPaid < total) {
        alert('Amount paid must be at least the total amount');
        return;
    }
    
    // Collect guest data
    const guests = [];
    const guestForms = document.querySelectorAll('.guest-form');
    
    guestForms.forEach((form, index) => {
        // Add error handling for missing form elements
        const firstNameInput = form.querySelector(`input[name="guests[${index}][firstName]"]`);
        const middleNameInput = form.querySelector(`input[name="guests[${index}][middleName]"]`);
        const lastNameInput = form.querySelector(`input[name="guests[${index}][lastName]"]`);
        const numberInput = form.querySelector(`input[name="guests[${index}][number]"]`);
        const streetInput = form.querySelector(`input[name="guests[${index}][address][street]"]`);
        const cityInput = form.querySelector(`select[name="guests[${index}][address][city]"]`);
        const provinceInput = form.querySelector(`select[name="guests[${index}][address][province]"]`);
        const zipcodeInput = form.querySelector(`input[name="guests[${index}][address][zipcode]"]`);
        
        if (!firstNameInput || !lastNameInput) {
            console.error(`Missing required form elements for guest ${index}`);
            return;
        }
        
        const guestData = {
            firstName: firstNameInput ? firstNameInput.value : '',
            middleName: middleNameInput ? middleNameInput.value : '',
            lastName: lastNameInput ? lastNameInput.value : '',
            number: numberInput ? numberInput.value : '',
            address: {
                street: streetInput ? streetInput.value : '',
                city: cityInput ? cityInput.value : '',
                province: provinceInput ? provinceInput.value : '',
                zipcode: zipcodeInput ? zipcodeInput.value : ''
            }
        };
        guests.push(guestData);
    });
    
    const paymentData = {
        room_id: currentRoom.id,
        rate_id: selectedRate.id,
        guests: guests,
        payment_amount: amountPaid,
        payment_change: Number.isFinite(computedChange) ? parseFloat(computedChange.toFixed(2)) : 0
    };
    
    // Show loading
    document.getElementById('loading').style.display = 'block';
    
    fetch('/adminPages/stays/process', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(paymentData)
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('loading').style.display = 'none';
        
        if (data.success) {
            // Show receipt instead of alert
            // Calculate the actual total (subtotal + tax)
            const actualSubtotal = selectedRate ? selectedRate.price * guestCount : 0;
            const actualTax = actualSubtotal * 0.12;
            const actualTotal = actualSubtotal + actualTax;
            
            showReceipt(data.receipt_data || {
                receipt_id: data.receipt_id,
                room: currentRoom.room,
                level: currentRoom.level ? currentRoom.level.description : '-',
                accommodation: selectedAccommodation ? selectedAccommodation.name : '-',
                duration: selectedRate ? formatDurationDisplay(selectedRate.duration) : '-',
                guest_count: guestCount,
                subtotal: actualSubtotal,
                tax: actualTax,
                total: actualTotal,
                amount_paid: amountPaid,
                change: computedChange,
                cashier: '{{ Auth::user()->name ?? "Staff" }}',
                date_time: new Date().toLocaleString()
            });
            closeModal();
            // Don't reload immediately, let user print receipt first
        } else {
            alert('Failed to process payment: ' + data.message);
        }
    })
    .catch(error => {
        document.getElementById('loading').style.display = 'none';
        console.error('Error:', error);
        alert('Failed to process payment');
    });
}

function processExtension() {
    if (!selectedRate) return;
    // Resolve stay id whether triggered by timer modal or by clicking an in-use room card
    var resolvedStayId = null;
    if (pendingExtend && pendingExtend.stayId) {
        resolvedStayId = String(pendingExtend.stayId);
    } else if (currentRoom && roomIdToStayId[String(currentRoom.id)]) {
        resolvedStayId = String(roomIdToStayId[String(currentRoom.id)]);
    }
    if (!resolvedStayId) {
        alert('Cannot determine active stay for this room. Please refresh and try again.');
        return;
    }
    const amountPaid = parseFloat(document.getElementById('amountPaid').value) || 0;
    const total = parseFloat(document.getElementById('totalAmount').textContent.replace('₱', '').replace(',', ''));
    const change = Math.max(amountPaid - total, 0);
    if (amountPaid < total) {
        alert('Amount paid must be at least the total amount');
        return;
    }
    const payload = { 
        rate_id: selectedRate.id,
        payment_amount: amountPaid,
        payment_change: change
    };
    document.getElementById('loading').style.display = 'block';
    fetch(`/adminPages/stays/extend/${resolvedStayId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('loading').style.display = 'none';
        if (data.success) {
            // Show receipt for extension
            // Calculate the actual total for extension (subtotal + tax)
            const extensionGuestCount = roomIdToGuestCount[String(currentRoom.id)] || 1;
            const extensionSubtotal = selectedRate ? selectedRate.price * extensionGuestCount : 0;
            const extensionTax = extensionSubtotal * 0.12;
            const extensionTotal = extensionSubtotal + extensionTax;
            
            showReceipt(data.receipt_data || {
                receipt_id: data.receipt_id,
                room: currentRoom.room,
                level: currentRoom.level ? currentRoom.level.description : '-',
                accommodation: selectedAccommodation ? selectedAccommodation.name : '-',
                duration: selectedRate ? formatDurationDisplay(selectedRate.duration) : '-',
                guest_count: extensionGuestCount,
                subtotal: extensionSubtotal,
                tax: extensionTax,
                total: extensionTotal,
                amount_paid: amountPaid,
                change: change,
                cashier: '{{ Auth::user()->name ?? "Staff" }}',
                date_time: new Date().toLocaleString(),
                type: 'Extension'
            });
            closeModal();
        } else {
            alert('Failed to extend: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(() => {
        document.getElementById('loading').style.display = 'none';
        alert('Failed to extend');
    });
}

function closeModal() {
    document.getElementById('accommodationModal').style.display = 'none';
    resetModal();
}

function deleteStay() {
    if (!currentRoom) return;
    
    // Get the stay ID for this room
    const stayId = roomIdToStayId[String(currentRoom.id)];
    if (!stayId) {
        alert('Cannot find active stay for this room.');
        return;
    }
    
    if (!confirm('Are you sure you want to delete this stay? This will make the room available again.')) {
        return;
    }
    
    // Show loading
    document.getElementById('loading').style.display = 'block';
    
    fetch(`/adminPages/stays/delete/${stayId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('loading').style.display = 'none';
        
        if (data.success) {
            alert('Stay deleted successfully. Room is now available.');
            closeModal();
            location.reload(); // Refresh to update room statuses
        } else {
            alert('Failed to delete stay: ' + data.message);
        }
    })
    .catch(error => {
        document.getElementById('loading').style.display = 'none';
        console.error('Error:', error);
        alert('Failed to delete stay');
    });
}

function resetModal() {
    currentRoom = null;
    selectedRate = null;
    guestCount = 0;
    maxCapacity = 0;
    selectedAccommodation = null;
    
    document.getElementById('accommodationInfo').innerHTML = '';
    document.getElementById('guestForms').innerHTML = '';
    document.getElementById('rateOptions').innerHTML = '';
    
    document.getElementById('guestFormSection').classList.add('hidden');
    document.getElementById('rateSelection').classList.add('hidden');
    document.getElementById('paymentSummary').classList.add('hidden');
    document.getElementById('proceedBtn').classList.add('hidden');
    document.getElementById('processPaymentBtn').classList.add('hidden');
    document.getElementById('deleteStayBtn').classList.add('hidden');
    document.getElementById('loading').style.display = 'none';
    
    // Reset form inputs
    document.getElementById('amountPaid').value = '';
    document.getElementById('changeAmount').value = '';
    
    // Reset button submitting state
    const btn = document.getElementById('processPaymentBtn');
    if (btn) {
        btn.dataset.submitting = '0';
        btn.disabled = false;
    }
    
    // Reset progress bar
    resetProgressBar();
}

function updateRoomTimers() {
    const now = new Date().getTime();
    document.querySelectorAll('.room-timer').forEach(timer => {
        const roomId = timer.id.replace('timer-', '');
        const checkoutIso = roomIdToCheckout[roomId];
        if (!checkoutIso) {
            timer.textContent = '--:--:--';
            return;
        }
        const diffMs = new Date(checkoutIso).getTime() - now;
        if (diffMs <= 0) {
            timer.textContent = '00:00:00';
      // Show extend/timeout modal once
      const stayId = roomIdToStayId[roomId];
      if (stayId && !timer.dataset.prompted) {
        timer.dataset.prompted = '1';
        pendingExtend = { roomId, stayId };
        var msg = document.getElementById('extendTimeoutMessage');
        if (msg) msg.textContent = `Room ${roomId} time ended. Extend the stay or time out?`;
        document.getElementById('extendTimeoutModal').style.display = 'block';
      }
            return;
        }
        const hours = Math.floor(diffMs / (1000 * 60 * 60));
        const minutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diffMs % (1000 * 60)) / 1000);
        timer.textContent = `${String(hours).padStart(2,'0')}:${String(minutes).padStart(2,'0')}:${String(seconds).padStart(2,'0')}`;
    });
}

async function fetchActiveStays() {
    try {
        const res = await fetch('/adminPages/stays/active');
        const data = await res.json();
        if (!data.success) return;
        // Map checkout time by room id
        roomIdToCheckout = {};
        roomIdToStayId = {};
        roomIdToAccommodationId = {};
        (data.stays || []).forEach(stay => {
            if (stay.room && stay.checkOut) {
                roomIdToCheckout[String(stay.room.id)] = stay.checkOut;
                if (stay.id) roomIdToStayId[String(stay.room.id)] = stay.id;
                if (stay.accommodation_id) roomIdToAccommodationId[String(stay.room.id)] = stay.accommodation_id;
                if (typeof stay.guest_count === 'number') roomIdToGuestCount[String(stay.room.id)] = stay.guest_count;
            } else if (stay.room && stay.rate && stay.rate.duration && stay.checkIn) {
                // Fallback: derive checkout by parsing duration
                const hours = parseDurationToHours(stay.rate.duration);
                const checkIn = new Date(stay.checkIn);
                const checkout = new Date(checkIn.getTime() + hours * 60 * 60 * 1000);
                roomIdToCheckout[String(stay.room.id)] = checkout.toISOString();
                if (stay.id) roomIdToStayId[String(stay.room.id)] = stay.id;
                if (stay.accommodation_id) roomIdToAccommodationId[String(stay.room.id)] = stay.accommodation_id;
                if (typeof stay.guest_count === 'number') roomIdToGuestCount[String(stay.room.id)] = stay.guest_count;
            }
        });
    } catch (e) {
        console.error('Failed to fetch active stays', e);
    }
}

function parseDurationToHours(durationStr) {
    if (!durationStr) return 1;
    const s = String(durationStr).trim().toLowerCase();
    const match = s.match(/(\d+(?:\.\d+)?)\s*(hour|hours|hr|hrs|minute|minutes|min|day|days)/);
    if (!match) return 1;
    const value = parseFloat(match[1]);
    const unit = match[2];
    if (unit.startsWith('day')) return value * 24;
    if (unit.startsWith('min')) return value / 60;
    if (unit.startsWith('hour') || unit.startsWith('hr')) return value;
    return 1;
}

function formatDurationDisplay(durationStr) {
    if (!durationStr) return '-';
    const s = String(durationStr).trim().toLowerCase();
    const match = s.match(/(\d+(?:\.\d+)?)\s*(hour|hours|hr|hrs|minute|minutes|min|day|days|week|weeks|month|months)/);
    if (!match) return durationStr; // Return original if no match
    
    const value = parseFloat(match[1]);
    const unit = match[2];
    
    // Format with clear unit indicators
    if (unit.startsWith('day')) {
        return `${value} ${value === 1 ? 'Day' : 'Days'}`;
    } else if (unit.startsWith('week')) {
        return `${value} ${value === 1 ? 'Week' : 'Weeks'}`;
    } else if (unit.startsWith('month')) {
        return `${value} ${value === 1 ? 'Month' : 'Months'}`;
    } else if (unit.startsWith('min')) {
        return `${value} ${value === 1 ? 'Minute' : 'Minutes'}`;
    } else if (unit.startsWith('hour') || unit.startsWith('hr')) {
        return `${value} ${value === 1 ? 'Hour' : 'Hours'}`;
    }
    
    return durationStr; // Return original if no recognized unit
}

function showReceipt(receiptData) {
    // Debug: Log the receipt data being displayed
    console.log('Receipt Data:', {
        duration: receiptData.duration,
        room: receiptData.room,
        accommodation: receiptData.accommodation,
        total: receiptData.total
    });
    
    const receiptContent = document.getElementById('receiptContent');
    const now = new Date();
    const receiptId = receiptData.receipt_id || 'RCP-' + now.getTime();
    
    receiptContent.innerHTML = `
        <div class="receipt-header">
            <div class="receipt-title">Bougainvilla Hotel</div>
            <div class="receipt-subtitle">Accommodation Services</div>
            <div class="receipt-address">
                123 Hotel Street<br>
                City, Province 1234<br>
                Tel: (02) 123-4567
            </div>
        </div>
        
        <div class="receipt-details">
            <div class="receipt-row">
                <span>Receipt No:</span>
                <span>${receiptId}</span>
            </div>
            <div class="receipt-row">
                <span>Date & Time:</span>
                <span>${receiptData.date_time || now.toLocaleString()}</span>
            </div>
            <div class="receipt-row">
                <span>Cashier:</span>
                <span>${receiptData.cashier || 'Staff'}</span>
            </div>
            <div class="receipt-row">
                <span>Room:</span>
                <span>${receiptData.room} - ${receiptData.level}</span>
            </div>
            <div class="receipt-row">
                <span>Accommodation:</span>
                <span>${receiptData.accommodation}</span>
            </div>
            <div class="receipt-row">
                <span>Duration:</span>
                <span>${receiptData.duration}</span>
            </div>
            <div class="receipt-row">
                <span>Guests:</span>
                <span>${receiptData.guest_count} person(s)</span>
            </div>
            <div class="receipt-row">
                <span>Type:</span>
                <span>${receiptData.type || 'New Stay'}</span>
            </div>
        </div>
        
        <div class="receipt-details">
            <div class="receipt-row">
                <span>Subtotal:</span>
                <span>₱${parseFloat(receiptData.subtotal || 0).toFixed(2)}</span>
            </div>
            <div class="receipt-row">
                <span>Tax (12%):</span>
                <span>₱${parseFloat(receiptData.tax || 0).toFixed(2)}</span>
            </div>
            <div class="receipt-row total">
                <span>Total Amount:</span>
                <span>₱${parseFloat(receiptData.total || 0).toFixed(2)}</span>
            </div>
            <div class="receipt-row">
                <span>Amount Paid:</span>
                <span>₱${parseFloat(receiptData.amount_paid || receiptData.total || 0).toFixed(2)}</span>
            </div>
            <div class="receipt-row">
                <span>Change:</span>
                <span>₱${parseFloat(receiptData.change || 0).toFixed(2)}</span>
            </div>
        </div>
        
        <div class="receipt-footer">
            <div class="receipt-thank-you">Thank You!</div>
            <div>Please keep this receipt for your records</div>
            <div>Visit us again soon!</div>
        </div>
    `;
    
    document.getElementById('receiptModal').style.display = 'block';
}

function printReceipt() {
    // Get the receipt content
    const receiptContent = document.getElementById('receiptContent');
    const originalContent = document.body.innerHTML;
    
    // Replace body content with only the receipt
    document.body.innerHTML = `
        <div style="font-family: 'Courier New', monospace; font-size: 12px; line-height: 1.4; color: #000; max-width: 300px; margin: 0 auto; padding: 20px;">
            ${receiptContent.innerHTML}
        </div>
    `;
    
    // Print the receipt
    window.print();
    
    // Restore original content
    document.body.innerHTML = originalContent;
    
    // Re-initialize event listeners
    initializeEventListeners();
    
    // Close modal and reload after printing
    setTimeout(() => {
        closeReceiptModal();
        location.reload();
    }, 1000);
}

function closeReceiptModal() {
    document.getElementById('receiptModal').style.display = 'none';
    // Reload the page to update room statuses
    location.reload();
}


// Philippine Address Data - Use complete data from external file
var phAddressData = {};

// Initialize with basic data, then merge with complete data from external file
function initializeAddressData() {
    // Start with empty object
    phAddressData = {};
    
    // Merge with additional data from external file if available
    if (typeof window.additionalPhAddressData !== 'undefined') {
        Object.assign(phAddressData, window.additionalPhAddressData);
    }
}

// Initialize address dropdowns for a specific guest form
function initializeGuestAddressDropdowns(guestIndex) {
    // Ensure address data is initialized
    initializeAddressData();
    
    const provinceSelect = document.querySelector(`select[name="guests[${guestIndex}][address][province]"]`);
    const citySelect = document.querySelector(`select[name="guests[${guestIndex}][address][city]"]`);
    const zipInput = document.querySelector(`input[name="guests[${guestIndex}][address][zipcode]"]`);
    
    if (!provinceSelect || !citySelect || !zipInput) return;
    
    // Populate province dropdown
    provinceSelect.innerHTML = '<option value="">Select Province</option>';
    Object.keys(phAddressData).forEach(function(province) {
        const option = document.createElement('option');
        option.value = province;
        option.textContent = province;
        provinceSelect.appendChild(option);
    });
    
    // Province change handler
    provinceSelect.addEventListener('change', function() {
        const selectedProvince = this.value;
        
        // Clear city options
        citySelect.innerHTML = '<option value="">Select City</option>';
        zipInput.value = '';
        
        if (selectedProvince && phAddressData[selectedProvince]) {
            // Add cities for selected province
            Object.keys(phAddressData[selectedProvince]).forEach(function(city) {
                const option = document.createElement('option');
                option.value = city;
                option.textContent = city;
                citySelect.appendChild(option);
            });
        }
    });
    
    // City change handler
    citySelect.addEventListener('change', function() {
        const selectedCity = this.value;
        const selectedProvince = provinceSelect.value;
        
        if (selectedCity && selectedProvince && phAddressData[selectedProvince]) {
            zipInput.value = phAddressData[selectedProvince][selectedCity] || '';
        } else {
            zipInput.value = '';
        }
    });
}


</script>
@endsection
