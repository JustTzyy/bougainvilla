@extends('layouts.frontdeskdashboard')

@section('title','Transactions')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/adminrecords.css') }}">
<link rel="stylesheet" href="{{ asset('css/room-dashboard.css') }}">
<script src="{{ asset('js/ph-complete-address.js') }}"></script>
<script src="{{ asset('js/transaction-validation.js') }}" defer></script>
<style>
/* Floor and Status Filter Button Styles */
.floor-selector .floor-btn,
.status-filters .status-btn {
  background-color: transparent;
  color: #B8860B;
  border: 2px solid #B8860B;
  padding: 8px 16px;
  border-radius: 8px;
  font-weight: 500;
  transition: all 0.2s ease;
  cursor: pointer;
}

.floor-selector .floor-btn:hover,
.status-filters .status-btn:hover {
  background-color: #B8860B;
  color: white;
  border-color: #B8860B;
}

.floor-selector .floor-btn.active,
.status-filters .status-btn.active {
  background-color: #B8860B;
  color: white;
  border-color: #B8860B;
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
  background: linear-gradient(90deg, #B8860B, #DAA520);
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
  background-color: #B8860B;
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
  background-color: #B8860B;
  color: white;
}

.progress-step.completed .progress-step-icon {
  background-color: #B8860B;
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
  color: #B8860B;
  font-weight: 600;
}

.progress-step.completed .progress-step-label {
  color: #B8860B;
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
  color: #B8860B;
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


/* Timer Alarm Animation */
@keyframes blink {
  0%, 50% { opacity: 1; }
  51%, 100% { opacity: 0.3; }
}

/* Custom Pagination Styling for Rooms */
.custom-pagination {
  background: #ffffff;
  border-radius: 15px;
  padding: 8px;
  box-shadow: 0 4px 20px rgba(184,134,11,.1);
  display: inline-block;
}

.custom-pagination .pagination {
  display: flex;
  gap: 4px;
  list-style: none;
  padding: 0;
  margin: 0;
  align-items: center;
}

.custom-pagination .pagination li {
  display: inline-block;
}

.custom-pagination .pagination .page-link {
  background: #ffffff;
  border: 1px solid rgba(184,134,11,.15);
  color: #6c757d;
  padding: 10px 14px;
  border-radius: 12px;
  font-weight: 600;
  font-size: 14px;
  transition: all .2s ease;
  text-decoration: none;
  display: flex;
  align-items: center;
  justify-content: center;
  min-width: 40px;
  height: 40px;
  cursor: pointer;
}

.custom-pagination .pagination .page-link:hover {
  background: rgba(184,134,11,.05);
  border-color: rgba(184,134,11,.25);
  color: #B8860B;
  text-decoration: none;
  transform: translateY(-1px);
}

.custom-pagination .pagination li.active .page-link {
  background: linear-gradient(135deg, #B8860B, #DAA520);
  color: #fff;
  border-color: transparent;
  box-shadow: 0 4px 15px rgba(184,134,11,.3);
}

.custom-pagination .pagination .page-link.disabled {
  opacity: .4;
  cursor: not-allowed;
  background: #f8f9fa;
  pointer-events: none;
}

.custom-pagination .pagination .page-link.disabled:hover {
  transform: none;
  background: #f8f9fa;
  border-color: rgba(184,134,11,.15);
  color: #6c757d;
}

  /* Selection style for Assign Cleaner modal */
  #assignCleanerModal #cleanerList .action-btn {
    border: 1px solid var(--border-color);
    background: #fff;
  }
  #assignCleanerModal #cleanerList .action-btn.selected {
    border: 2px solid var(--purple-primary);
    background: rgba(237,192,1,.08);
    box-shadow: 0 0 0 3px rgba(237,192,1,.18);
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

  .pagination-container {
    display: none !important;
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
      <button class="status-btn" data-status="Cleaning">Cleaning</button>
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
            @if($room->status === 'Cleaning')
              <button class="mark-ready-btn" data-room-id="{{ $room->id }}" style="margin-top: 8px; padding: 4px 8px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 12px;" title="Mark as Ready">
                <i class="fas fa-check"></i> Ready
              </button>
            @endif
          </div>
        @endforeach
      @endif
    </div>

    <!-- Pagination Controls -->
    <div class="pagination-container" style="display: flex; justify-content: center; margin-top: 30px;">
      <div class="custom-pagination">
        <ul class="pagination" id="roomsPagination">
          <!-- Pagination will be generated by JavaScript -->
        </ul>
      </div>
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
                
                <!-- Cleaner assignment moved to timeout flow -->

                <!-- Penalty UI removed from payment step; penalties are handled after checkout when reported by cleaners. -->

                <div class="summary-row summary-total">
                    <span>Subtotal:</span>
                    <span id="subtotalAmount">₱0.00</span>
                </div>
                <!-- No penalty line in initial transaction -->
                <div class="summary-row summary-total">
                    <span>Total:</span>
                    <span id="totalAmount">₱0.00</span>
                </div>
            </div>

            <div class="payment-inputs hidden">
                    <div class="form-group">
                        <label class="form-label">Amount Paid</label>
                        <input type="number" class="form-input" id="amountPaid" step="0.01" min="0" max="999999.99"
                               onblur="TransactionValidator.validatePayment()" 
                               oninput="calculateChange()"
                               placeholder="Enter amount paid">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Change</label>
                        <input type="number" class="form-input" id="changeAmount" step="0.01" readonly>
                    </div>
                </div>

            <!-- Terms and Conditions Section (kept subtle under payment) -->
            <div class="terms-section hidden" style="margin-top: 10px;">
                <div class="form-group">
                    <label class="form-label" style="display:flex; align-items:center; gap:10px; margin:0;">
                        <input type="checkbox" id="agreeTerms" required>
                        I agree to the <a href="#" onclick="showTermsModal()" class="terms-link">Terms and Conditions</a> of the lodge
                    </label>
                </div>
            </div>
            </div>
      
            <div class="loading" id="loading">
                <div class="spinner"></div>
                <p>Processing...</p>
            </div>

            <div style="text-align: center; margin: 20px auto 28px;">
                <button type="button" class="btn btn-primary hidden" id="proceedBtn">Proceed with Accommodation</button>
                <button type="button" class="btn btn-success hidden" id="processPaymentBtn">Process Payment</button>
                <button type="button" class="btn btn-danger hidden" id="deleteStayBtn">Delete Stay</button>
                <button type="button" class="btn btn-secondary" id="cancelBtn">Cancel</button>
          </div>
        </div>
      </div>
    </div>
    
  <!-- Extend/Timeout Modal -->
  <style>
    #extendTimeoutModal .modal-content {
      height: auto !important;
      min-height: auto !important;
      max-height: fit-content !important;
    }
    #extendTimeoutModal .modal-body {
      height: auto !important;
      min-height: auto !important;
      max-height: fit-content !important;
      padding: 20px !important;
    }
  </style>
  <div id="extendTimeoutModal" class="modal" style="display:none;">
    <div class="modal-content" style="max-width: 420px; height: auto !important; min-height: auto !important; max-height: fit-content !important;">
      <div class="modal-header" style="padding: 12px 20px; border-bottom: 1px solid #dee2e6;">
        <h2 class="modal-title" style="margin: 0; font-size: 18px;">Time's Up</h2>
        <span class="close" id="closeExtendTimeout">&times;</span>
      </div>
      <div class="modal-body" style="padding: 20px !important; height: auto !important; min-height: auto !important; max-height: fit-content !important;">
        <p id="extendTimeoutMessage" style="margin: 0 0 20px 0; line-height: 1.4;">Room 1 time ended. Extend the stay or time out?</p>
        <div style="display:flex; gap: 8px; justify-content: flex-end; margin: 0;">
          <button type="button" class="btn btn-secondary" id="timeoutBtn" style="padding: 8px 16px;">Time Out</button>
          <button type="button" class="btn btn-primary" id="extendBtn" style="padding: 8px 16px;">Extend</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Assign Cleaner + Casualty Modal (opens after clicking Time Out) -->
  <div id="assignCleanerModal" class="modal" style="display:none;">
    <div class="modal-card" style="max-width: 520px;">
      <div class="modal-header">
        <h3 class="chart-title">Assign Cleaner</h3>
        <button id="closeAssignCleaner" class="action-btn ml-auto"><i class="fas fa-times"></i></button>
      </div>
      <div class="modal-form" style="padding: 16px;">
        <div class="form-group">
          <label class="form-label">Select Cleaner</label>
          <div id="cleanerList" style="display:grid; grid-template-columns:1fr; gap:8px; max-height:260px; overflow:auto;"></div>
          <small id="cleanerListHelp" style="color:#6c757d;">Choose one cleaner. List is loaded automatically.</small>
        </div>
        <div class="form-group" style="margin-top:8px;">
          <label class="form-label"><input type="checkbox" id="assignHasPenalty"> Add Penalty (optional)</label>
          <div id="assignPenaltyFields" class="hidden" style="margin-top:8px; display:grid; gap:8px;">
            <input type="number" class="form-input" id="assignPenaltyAmount" placeholder="Penalty amount" step="0.01" min="0">
            <textarea class="form-input" id="assignPenaltyReason" rows="3" placeholder="Reason"></textarea>
          </div>
        </div>
      </div>
      <div class="modal-actions">
        <button type="button" class="action-btn btn-outline" id="cancelAssignCleaner">Cancel</button>
        <button type="button" class="btn-primary inline" id="confirmAssignCleaner">Confirm</button>
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

<!-- Terms and Conditions Modal -->
<div id="termsModal" class="modal" style="display: none;">
    <div class="modal-card" style="max-width: 800px; max-height: 90vh; overflow-y: auto;">
        <div class="modal-header">
            <h3 class="chart-title">Terms and Conditions</h3>
            <button id="closeTermsModal" class="action-btn ml-auto"><i class="fas fa-times"></i></button>
        </div>
        
        <div class="modal-body">
            <div class="terms-content">
                <h4>Lodge Terms and Conditions</h4>
                
                <h5>1. Check-in and Check-out</h5>
                <ul>
                    <li>Check-in time is at 2:00 PM</li>
                    <li>Check-out time is at 12:00 PM</li>
                    <li>Early check-in and late check-out may be subject to additional charges</li>
                    <li>Guests must present valid identification upon check-in</li>
                </ul>

                <h5>2. Payment Terms</h5>
                <ul>
                    <li>Full payment is required upon check-in</li>
                    <li>We accept cash and major credit cards</li>
                    <li>All rates are subject to applicable taxes</li>
                    <li>No refunds for early check-out</li>
                </ul>

                <h5>3. Room Occupancy</h5>
                <ul>
                    <li>Maximum occupancy per room must not be exceeded</li>
                    <li>Additional guests may incur extra charges</li>
                    <li>Room transfers are subject to availability and additional fees</li>
                </ul>

                <h5>4. Guest Responsibilities</h5>
                <ul>
                    <li>Guests are responsible for any damages to the room or property</li>
                    <li>Smoking is prohibited in all rooms and common areas</li>
                    <li>No pets allowed unless specifically authorized</li>
                    <li>Quiet hours are from 10:00 PM to 7:00 AM</li>
                    <li>Guests must respect other guests and lodge property</li>
                </ul>

                <h5>5. Lodge Policies</h5>
                <ul>
                    <li>The lodge reserves the right to refuse service to anyone</li>
                    <li>Personal belongings are the responsibility of the guest</li>
                    <li>The lodge is not liable for lost or stolen items</li>
                    <li>Emergency procedures are posted in each room</li>
                </ul>

                <h5>6. Cancellation Policy</h5>
                <ul>
                    <li>Cancellations must be made 24 hours before check-in</li>
                    <li>No-shows will be charged the full room rate</li>
                    <li>Modifications to reservations are subject to availability</li>
                </ul>

                <h5>7. Liability</h5>
                <ul>
                    <li>The lodge's liability is limited to the room rate paid</li>
                    <li>Guests use lodge facilities at their own risk</li>
                    <li>The lodge is not responsible for weather-related issues</li>
                </ul>

                <h5>8. Penalties and Damages</h5>
                <ul>
                    <li>Any damages or casualties will be charged to the guest</li>
                    <li>Penalty amounts will be determined by the lodge management</li>
                    <li>Guests will be notified of any additional charges</li>
                </ul>

                <p><strong>By checking the agreement box, you acknowledge that you have read, understood, and agree to abide by these terms and conditions.</strong></p>
            </div>
        </div>
        
        <div class="modal-actions">
            <button type="button" id="closeTermsBtn" class="action-btn btn-outline">Close</button>
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

// Room pagination variables
let allRoomBoxes = [];
let currentPage = 1;
let roomsPerPage = 12; // Adjust this number based on your preference

// Load cleaners for assignment
async function loadCleaners() {
    try {
        const response = await fetch('/adminPages/cleaners/list');
        const data = await response.json();
        
        const cleanerSelect = document.getElementById('assignedCleaner');
        cleanerSelect.innerHTML = '<option value="">Select Cleaner</option>';
        
        if (data.cleaners && data.cleaners.length > 0) {
            data.cleaners.forEach(cleaner => {
                const option = document.createElement('option');
                option.value = cleaner.id;
                option.textContent = cleaner.name;
                cleanerSelect.appendChild(option);
            });
        } else {
            const option = document.createElement('option');
            option.value = '';
            option.textContent = 'No cleaners available';
            option.disabled = true;
            cleanerSelect.appendChild(option);
        }
    } catch (error) {
        const cleanerSelect = document.getElementById('assignedCleaner');
        cleanerSelect.innerHTML = '<option value="">Error loading cleaners</option>';
    }
}

// Terms and Conditions Modal Functions
function showTermsModal() {
    document.getElementById('termsModal').style.display = 'flex';
}

function closeTermsModal() {
    document.getElementById('termsModal').style.display = 'none';
}

// Terms modal event listeners
document.addEventListener('DOMContentLoaded', function() {
    const termsModal = document.getElementById('termsModal');
    const closeTermsModalBtn = document.getElementById('closeTermsModal');
    const closeTermsBtn = document.getElementById('closeTermsBtn');
    
    if (closeTermsModalBtn) {
        closeTermsModalBtn.addEventListener('click', closeTermsModal);
    }
    
    if (closeTermsBtn) {
        closeTermsBtn.addEventListener('click', closeTermsModal);
    }
    
    // Close modal when clicking outside
    if (termsModal) {
        termsModal.addEventListener('click', function(e) {
            if (e.target === termsModal) {
                closeTermsModal();
            }
        });
    }
});

// Initialize the dashboard
document.addEventListener('DOMContentLoaded', function() {
    initializeEventListeners();
    initializePagination();
    loadCleaners();
    
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
        box.addEventListener('click', function(e) {
            // Don't trigger if clicking on mark ready button
            if (e.target.closest('.mark-ready-btn')) {
                return;
            }
            
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

    // Mark ready button clicks
    document.querySelectorAll('.mark-ready-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const roomId = this.dataset.roomId;
            const roomNumber = this.closest('.room-box').querySelector('.room-number').textContent;
            
            if (confirm('Mark room ' + roomNumber + ' as ready? (Cleaning completed)')) {
                // Make AJAX request to mark room as ready
                fetch('/frontdesk/rooms/mark-ready/' + roomId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Room marked as ready!');
                        // Reload the page to update the room status
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Failed to mark room as ready. Please try again.');
                });
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
  // Assign cleaner modal elements
  var assignCleanerModal = document.getElementById('assignCleanerModal');
  var cleanerListEl = document.getElementById('cleanerList');
  var closeAssignCleaner = document.getElementById('closeAssignCleaner');
  var cancelAssignCleaner = document.getElementById('cancelAssignCleaner');
  var confirmAssignCleaner = document.getElementById('confirmAssignCleaner');
  var assignHasPenalty = document.getElementById('assignHasPenalty');
  var assignPenaltyFields = document.getElementById('assignPenaltyFields');
  function hideAssign(){ if(assignCleanerModal) assignCleanerModal.style.display = 'none'; }
  if (closeAssignCleaner) closeAssignCleaner.addEventListener('click', hideAssign);
  if (cancelAssignCleaner) cancelAssignCleaner.addEventListener('click', hideAssign);
  if (assignHasPenalty) assignHasPenalty.addEventListener('change', function(){
    if (this.checked) assignPenaltyFields.classList.remove('hidden'); else assignPenaltyFields.classList.add('hidden');
  });
  async function openAssignCleanerModal(){
    try {
      const res = await fetch('/adminPages/cleaners/list');
      const data = await res.json();
      cleanerListEl.innerHTML = '';
      (data.cleaners || []).forEach(c => {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'action-btn';
        btn.textContent = c.name;
        btn.style.justifyContent = 'flex-start';
        btn.dataset.cleanerId = c.id;
        btn.addEventListener('click', function(){
          cleanerListEl.querySelectorAll('button').forEach(b => b.classList.remove('selected'));
          btn.classList.add('selected');
          cleanerListEl.dataset.selectedCleanerId = String(c.id);
        });
        cleanerListEl.appendChild(btn);
      });
    } catch(e){
      cleanerListEl.innerHTML = '<div style="color:#dc3545;">Failed to load cleaners</div>';
    }
    assignCleanerModal.style.display = 'flex';
  }
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
    extendTimeoutModal.style.display = 'none';
    openAssignCleanerModal();
    if (confirmAssignCleaner) confirmAssignCleaner.onclick = function(){
      const stayId = String(pendingExtend.stayId);
      const cleanerId = cleanerListEl.dataset.selectedCleanerId || '';
      const hasP = assignHasPenalty && assignHasPenalty.checked;
      const pa = hasP ? (parseFloat(document.getElementById('assignPenaltyAmount').value) || 0) : 0;
      const pr = hasP ? (document.getElementById('assignPenaltyReason').value || null) : null;
      hideAssign();
      fetch(`/adminPages/stays/end/${stayId}`, {
        method: 'POST',
        headers: { 
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') 
        },
        body: JSON.stringify({ assigned_cleaner_id: cleanerId || null, penalty_amount: pa, penalty_reason: pr })
      }).then(() => setTimeout(() => location.reload(), 400)).catch(()=>{});
      pendingExtend = null;
    };
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

function initializePagination() {
    // Store all room boxes for pagination
    allRoomBoxes = Array.from(document.querySelectorAll('.room-box'));
    currentPage = 1;
    
    // Initial display
    applyFiltersAndPagination();
}

function filterRooms() {
    currentPage = 1; // Reset to first page when filtering
    applyFiltersAndPagination();
}

function applyFiltersAndPagination() {
    const selectedFloor = document.querySelector('.floor-btn.active').dataset.floor;
    const selectedStatus = document.querySelector('.status-btn.active').dataset.status;
    
    // Filter rooms based on selected criteria
    const filteredRooms = allRoomBoxes.filter(box => {
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
        
        return showRoom;
    });
    
    // Hide all rooms first
    allRoomBoxes.forEach(box => {
        box.style.display = 'none';
    });
    
    // Calculate pagination
    const totalPages = Math.ceil(filteredRooms.length / roomsPerPage);
    const startIndex = (currentPage - 1) * roomsPerPage;
    const endIndex = startIndex + roomsPerPage;
    
    // Show rooms for current page
    const roomsToShow = filteredRooms.slice(startIndex, endIndex);
    roomsToShow.forEach(box => {
        box.style.display = 'flex';
    });
    
    // Update pagination controls
    updatePaginationControls(totalPages, filteredRooms.length);
}

function updatePaginationControls(totalPages, totalRooms) {
    const paginationContainer = document.querySelector('.pagination-container');
    const pagination = document.getElementById('roomsPagination');
    
    if (totalPages <= 1) {
        paginationContainer.style.display = 'none';
        return;
    }
    
    paginationContainer.style.display = 'flex';
    
    let html = '';
    
    // Previous button
    html += `<li ${currentPage === 1 ? 'class="disabled"' : ''}>
        <span class="page-link ${currentPage === 1 ? 'disabled' : ''}" data-page="${currentPage - 1}">
            <i class="fas fa-chevron-left"></i>
        </span>
    </li>`;
    
    // Page numbers
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        html += `<li ${i === currentPage ? 'class="active"' : ''}>
            <span class="page-link" data-page="${i}">${i}</span>
        </li>`;
    }
    
    // Next button
    html += `<li ${currentPage === totalPages ? 'class="disabled"' : ''}>
        <span class="page-link ${currentPage === totalPages ? 'disabled' : ''}" data-page="${currentPage + 1}">
            <i class="fas fa-chevron-right"></i>
        </span>
    </li>`;
    
    pagination.innerHTML = html;
    
    // Add click handlers
    pagination.addEventListener('click', function(e) {
        const pageLink = e.target.closest('.page-link');
        if (pageLink && !pageLink.classList.contains('disabled')) {
            const page = parseInt(pageLink.dataset.page);
            if (page && page !== currentPage && page >= 1 && page <= totalPages) {
                currentPage = page;
                applyFiltersAndPagination();
            }
        }
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
                <input type="text" class="form-input" name="guests[${guestCount-1}][firstName]" required
                       onblur="TransactionValidator.validateGuestForm(${guestCount-1})"
                       pattern="[a-zA-Z\\s\\-'\\.]+" title="Only letters, spaces, hyphens, and apostrophes allowed"
                       placeholder="e.g., Juan">
                <small style="color: #6c757d; font-size: 11px;">Enter your first name</small>
        </div>
        <div class="form-group">
                <label class="form-label">Middle Name</label>
                <input type="text" class="form-input" name="guests[${guestCount-1}][middleName]"
                       onblur="TransactionValidator.validateGuestForm(${guestCount-1})"
                       pattern="[a-zA-Z\\s\\-'\\.]+" title="Only letters, spaces, hyphens, and apostrophes allowed"
                       placeholder="e.g., Santos">
                <small style="color: #6c757d; font-size: 11px;">Optional - middle name or initial</small>
        </div>
        <div class="form-group">
                <label class="form-label">Last Name *</label>
                <input type="text" class="form-input" name="guests[${guestCount-1}][lastName]" required
                       onblur="TransactionValidator.validateGuestForm(${guestCount-1})"
                       pattern="[a-zA-Z\\s\\-'\\.]+" title="Only letters, spaces, hyphens, and apostrophes allowed"
                       placeholder="e.g., Dela Cruz">
                <small style="color: #6c757d; font-size: 11px;">Enter your family name</small>
            </div>
        </div>
        <div class="form-row">
        <div class="form-group">
                <label class="form-label">Contact Number</label>
                <input type="text" class="form-input" name="guests[${guestCount-1}][number]" 
                       placeholder="e.g., 0912-999-7211, 09129997211, +639129997211, 02-8123-4567"
                       onblur="TransactionValidator.validateGuestForm(${guestCount-1})">
                <small style="color: #6c757d; font-size: 11px;">Philippine phone number format</small>
            </div>
        </div>
        <div class="address-section">
            <div class="address-title">Address Information</div>
            <div class="form-row">
        <div class="form-group">
                    <label class="form-label">Street *</label>
                    <input type="text" class="form-input" name="guests[${guestCount-1}][address][street]" required
                           placeholder="e.g., 123 Main Street, Barangay Centro"
                           onblur="TransactionValidator.validateGuestForm(${guestCount-1})">
                    <small style="color: #6c757d; font-size: 11px;">Include house number, street name, and barangay</small>
        </div>
      </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Province *</label>
                    <select name="guests[${guestCount-1}][address][province]" class="form-input" required
                            onchange="TransactionValidator.validateGuestForm(${guestCount-1})">
                        <option value="">Select Province (e.g., Metro Manila, Cebu)</option>
                    </select>
                    <small style="color: #6c757d; font-size: 11px;">Choose your province from the dropdown</small>
                </div>
                <div class="form-group">
                    <label class="form-label">City *</label>
                    <select name="guests[${guestCount-1}][address][city]" class="form-input" required
                            onchange="TransactionValidator.validateGuestForm(${guestCount-1})">
                        <option value="">Select City (e.g., Manila, Quezon City)</option>
                    </select>
                    <small style="color: #6c757d; font-size: 11px;">City will be available after selecting province</small>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">ZIP Code *</label>
                    <input type="text" class="form-input" name="guests[${guestCount-1}][address][zipcode]" readonly
                           pattern="\\d{4,5}" title="ZIP code must be 4-5 digits"
                           placeholder="e.g., 1234 or 12345">
                    <small style="color: #6c757d; font-size: 11px;">ZIP code will be auto-filled when city is selected</small>
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
            
            // Selected rate
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
    
    // Transaction total should be the same as rate price (no tax calculation here)
    const total = selectedRate.price * guestCount;
    
    // Store rate price in data attribute for penalty calculations
    document.getElementById('totalAmount').dataset.ratePrice = total;
    
    // Initialize totals
    document.getElementById('subtotalAmount').textContent = '₱' + total.toFixed(2);
    document.getElementById('totalAmount').textContent = '₱' + total.toFixed(2);
    
    // Show payment summary and process button for both new stays and extensions
    document.getElementById('paymentSummary').classList.remove('hidden');
    document.getElementById('processPaymentBtn').classList.remove('hidden');
    // Reveal payment inputs and terms only at payment step
    document.querySelector('.payment-inputs').classList.remove('hidden');
    document.querySelector('.terms-section').classList.remove('hidden');
    
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

// Penalties are no longer applied during initial payment; they are handled post-transaction.

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
    
    // Get assigned cleaner
    // Cleaner assignment occurs on timeout, not during initial payment
    const assignedCleanerId = document.getElementById('assignedCleaner') ? document.getElementById('assignedCleaner').value : '';
    
    // Check terms agreement
    const agreeTerms = document.getElementById('agreeTerms').checked;
    if (!agreeTerms) {
        alert('You must agree to the Terms and Conditions to proceed.');
        return;
    }
    
    const paymentData = {
        room_id: currentRoom.id,
        rate_id: selectedRate.id,
        guests: guests,
        payment_amount: amountPaid,
        payment_change: Number.isFinite(computedChange) ? parseFloat(computedChange.toFixed(2)) : 0,
        // Penalty is not part of initial transaction; handled after cleaner inspection
        penalty_amount: 0,
        penalty_reason: null,
        assigned_cleaner_id: assignedCleanerId || null
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
            // Calculate tax for receipt display (this is where tax calculation happens)
            const ratePrice = selectedRate ? selectedRate.price * guestCount : 0;
            const tax = ratePrice * 0.12;
            const subtotal = ratePrice - tax;
            
            showReceipt(data.receipt_data || {
                receipt_id: data.receipt_id,
                room: currentRoom.room,
                level: currentRoom.level ? currentRoom.level.description : '-',
                accommodation: selectedAccommodation ? selectedAccommodation.name : '-',
                duration: selectedRate ? formatDurationDisplay(selectedRate.duration) : '-',
                guest_count: guestCount,
                subtotal: subtotal,
                tax: tax,
                total: ratePrice,
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
    fetch(`/frontdesk/stays/extend/${resolvedStayId}`, {
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
            // Calculate the actual total for extension
            const extensionGuestCount = roomIdToGuestCount[String(currentRoom.id)] || 1;
            const extensionRatePrice = selectedRate ? selectedRate.price * extensionGuestCount : 0;
            const extensionTax = extensionRatePrice * 0.12;
            const extensionSubtotal = extensionRatePrice - extensionTax;
            
            showReceipt(data.receipt_data || {
                receipt_id: data.receipt_id,
                room: currentRoom.room,
                level: currentRoom.level ? currentRoom.level.description : '-',
                accommodation: selectedAccommodation ? selectedAccommodation.name : '-',
                duration: selectedRate ? formatDurationDisplay(selectedRate.duration) : '-',
                guest_count: extensionGuestCount,
                subtotal: extensionSubtotal,
                tax: extensionTax,
                total: extensionRatePrice,
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
            // Add visual alarm effect
            timer.style.color = '#dc3545';
            timer.style.fontWeight = 'bold';
            timer.style.animation = 'blink 1s infinite';
            
            // Play alarm sound
            playAlarmSound();
            
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
        
        // Reset timer styling if time is still remaining
        timer.style.color = '';
        timer.style.fontWeight = '';
        timer.style.animation = '';
        
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
        // Failed to fetch active stays
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
    // Receipt data
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

// Alarm sound function
function playAlarmSound() {
    // Create audio context for generating alarm sound
    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
    
    // Create a beep sound using Web Audio API
    const oscillator = audioContext.createOscillator();
    const gainNode = audioContext.createGain();
    
    // Connect oscillator to gain node to audio context
    oscillator.connect(gainNode);
    gainNode.connect(audioContext.destination);
    
    // Set alarm sound properties
    oscillator.frequency.setValueAtTime(800, audioContext.currentTime); // 800Hz frequency
    oscillator.type = 'square'; // Square wave for more alarm-like sound
    
    // Set volume envelope (fade in/out for beep effect)
    gainNode.gain.setValueAtTime(0, audioContext.currentTime);
    gainNode.gain.linearRampToValueAtTime(0.3, audioContext.currentTime + 0.1);
    gainNode.gain.linearRampToValueAtTime(0, audioContext.currentTime + 0.3);
    
    // Play the alarm sound
    oscillator.start(audioContext.currentTime);
    oscillator.stop(audioContext.currentTime + 0.3);
    
    // Play multiple beeps for more noticeable alarm
    setTimeout(() => {
        const oscillator2 = audioContext.createOscillator();
        const gainNode2 = audioContext.createGain();
        
        oscillator2.connect(gainNode2);
        gainNode2.connect(audioContext.destination);
        
        oscillator2.frequency.setValueAtTime(1000, audioContext.currentTime);
        oscillator2.type = 'square';
        
        gainNode2.gain.setValueAtTime(0, audioContext.currentTime);
        gainNode2.gain.linearRampToValueAtTime(0.3, audioContext.currentTime + 0.1);
        gainNode2.gain.linearRampToValueAtTime(0, audioContext.currentTime + 0.3);
        
        oscillator2.start(audioContext.currentTime);
        oscillator2.stop(audioContext.currentTime + 0.3);
    }, 400);
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
            
            // Clear any ZIP code validation errors after auto-fill
            setTimeout(() => {
                if (typeof TransactionValidator !== 'undefined' && TransactionValidator.clearFieldError) {
                    TransactionValidator.clearFieldError(zipInput);
                }
            }, 100);
        } else {
            zipInput.value = '';
        }
    });
}


</script>
@endsection
