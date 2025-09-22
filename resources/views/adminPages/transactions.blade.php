@extends('layouts.admindashboard')

@section('title','Transactions')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/adminrecords.css') }}">
<link rel="stylesheet" href="{{ asset('css/room-dashboard.css') }}">
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
      <button class="status-btn" data-status="Under Maintenance">Maintenance</button>
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
            <div class="room-level">{{ $room->level->description }}</div>
            <div class="room-status">{{ $room->status }}</div>
            <div class="room-capacity">Capacity: {{ $room->accommodations->sum('capacity') }}</div>
            @if($room->status === 'In Use')
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
                <button type="button" class="btn btn-secondary" id="cancelBtn">Cancel</button>
          </div>
        </div>
      </div>
    </div>
    
<script>
// Room Dashboard Functionality
let currentRoom = null;
let selectedRate = null;
let guestCount = 0;
let maxCapacity = 0;

// Initialize the dashboard
document.addEventListener('DOMContentLoaded', function() {
    initializeEventListeners();
    updateRoomTimers();
    setInterval(updateRoomTimers, 1000);
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
            if (this.dataset.status === 'Available') {
                openAccommodationModal(this.dataset.roomId);
            }
        });
    });

    // Modal close
    document.querySelector('.close').addEventListener('click', closeModal);
    document.getElementById('cancelBtn').addEventListener('click', closeModal);
    
    // Proceed button
    document.getElementById('proceedBtn').addEventListener('click', showGuestForm);
    
    // Add guest button
    document.getElementById('addGuestBtn').addEventListener('click', addGuestForm);
    
    // Process payment button
    document.getElementById('processPaymentBtn').addEventListener('click', processPayment);
    
    // Amount paid input
    document.getElementById('amountPaid').addEventListener('input', calculateChange);
}

function filterRooms() {
    const selectedFloor = document.querySelector('.floor-btn.active').dataset.floor;
    const selectedStatus = document.querySelector('.status-btn.active').dataset.status;
    
    document.querySelectorAll('.room-box').forEach(box => {
        const roomFloor = box.dataset.level;
        const roomStatus = box.dataset.status;
        
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
    fetch(`/admin/rooms/details/${roomId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentRoom = data.room;
                maxCapacity = data.room.accommodations.reduce((sum, acc) => sum + acc.capacity, 0);
                displayAccommodationInfo(data.room);
                document.getElementById('accommodationModal').style.display = 'block';
            } else {
                alert('Failed to load room details: ' + data.message);
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
    
    title.textContent = `Room ${room.room} - ${room.level.description}`;
    
    let html = `
        <div class="accommodation-name">Available Accommodations</div>
        <div class="accommodation-details">
    `;
    
    room.accommodations.forEach(acc => {
        html += `
            <div class="detail-item">
                <div class="detail-label">Accommodation</div>
                <div class="detail-value">${acc.name}</div>
                <div class="detail-label">Capacity</div>
                <div class="detail-value">${acc.capacity} guests</div>
                <div class="detail-label">Description</div>
                <div class="detail-value">${acc.description || 'N/A'}</div>
    </div>
        `;
    });
    
    html += '</div>';
    infoDiv.innerHTML = html;
    
    // Show proceed button
    document.getElementById('proceedBtn').classList.remove('hidden');
}

function showGuestForm() {
    document.getElementById('guestFormSection').classList.remove('hidden');
    document.getElementById('proceedBtn').classList.add('hidden');
    
    // Generate initial guest form
    addGuestForm();
}

function addGuestForm() {
    if (guestCount >= maxCapacity) {
        alert(`Maximum capacity is ${maxCapacity} guests`);
        return;
    }
    
    guestCount++;
    const formsDiv = document.getElementById('guestForms');
    
    const guestForm = document.createElement('div');
    guestForm.className = 'guest-form';
    guestForm.innerHTML = `
        <h4>Guest ${guestCount}</h4>
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
        <div class="form-group">
                    <label class="form-label">City *</label>
                    <input type="text" class="form-input" name="guests[${guestCount-1}][address][city]" required>
        </div>
      </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Province *</label>
                    <input type="text" class="form-input" name="guests[${guestCount-1}][address][province]" required>
                </div>
                <div class="form-group">
                    <label class="form-label">ZIP Code *</label>
                    <input type="text" class="form-input" name="guests[${guestCount-1}][address][zipcode]" required>
      </div>
  </div>
</div>
    `;
    
    formsDiv.appendChild(guestForm);
    
    // Show rate selection after first guest
    if (guestCount === 1) {
        loadRateOptions();
    }
}

function loadRateOptions() {
    // Get rates for the first accommodation (assuming all accommodations have same rates)
    const accommodationId = currentRoom.accommodations[0].id;
    
    fetch(`/admin/stays/rates/${accommodationId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayRateOptions(data.rates);
                document.getElementById('rateSelection').classList.remove('hidden');
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
            <div class="rate-duration">${rate.duration}</div>
            <div class="rate-price">₱${parseFloat(rate.price).toFixed(2)}</div>
        `;
        
        option.addEventListener('click', function() {
            document.querySelectorAll('.rate-option').forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            selectedRate = rate;
            calculateTotal();
        });
        
        optionsDiv.appendChild(option);
    });
}

function calculateTotal() {
    if (!selectedRate) return;
    
    const subtotal = selectedRate.price * guestCount;
    const tax = subtotal * 0.12;
    const total = subtotal + tax;
    
    document.getElementById('subtotalAmount').textContent = `₱${subtotal.toFixed(2)}`;
    document.getElementById('taxAmount').textContent = `₱${tax.toFixed(2)}`;
    document.getElementById('totalAmount').textContent = `₱${total.toFixed(2)}`;
    
    document.getElementById('paymentSummary').classList.remove('hidden');
    document.getElementById('processPaymentBtn').classList.remove('hidden');
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
    
    if (amountPaid < total) {
        alert('Amount paid must be at least the total amount');
        return;
    }
    
    // Collect guest data
    const guests = [];
    const guestForms = document.querySelectorAll('.guest-form');
    
    guestForms.forEach((form, index) => {
        const guestData = {
            firstName: form.querySelector(`input[name="guests[${index}][firstName]"]`).value,
            middleName: form.querySelector(`input[name="guests[${index}][middleName]"]`).value,
            lastName: form.querySelector(`input[name="guests[${index}][lastName]"]`).value,
            number: form.querySelector(`input[name="guests[${index}][number]"]`).value,
            address: {
                street: form.querySelector(`input[name="guests[${index}][address][street]"]`).value,
                city: form.querySelector(`input[name="guests[${index}][address][city]"]`).value,
                province: form.querySelector(`input[name="guests[${index}][address][province]"]`).value,
                zipcode: form.querySelector(`input[name="guests[${index}][address][zipcode]"]`).value
            }
        };
        guests.push(guestData);
    });
    
    const paymentData = {
        room_id: currentRoom.id,
        rate_id: selectedRate.id,
        guests: guests,
        payment_amount: amountPaid,
        payment_change: parseFloat(document.getElementById('changeAmount').value)
    };
    
    // Show loading
    document.getElementById('loading').style.display = 'block';
    
    fetch('/admin/stays/process', {
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
            alert('Payment processed successfully! Receipt #' + data.receipt_id);
            closeModal();
            location.reload(); // Refresh to update room statuses
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

function closeModal() {
    document.getElementById('accommodationModal').style.display = 'none';
    resetModal();
}

function resetModal() {
    currentRoom = null;
    selectedRate = null;
    guestCount = 0;
    maxCapacity = 0;
    
    document.getElementById('accommodationInfo').innerHTML = '';
    document.getElementById('guestForms').innerHTML = '';
    document.getElementById('rateOptions').innerHTML = '';
    
    document.getElementById('guestFormSection').classList.add('hidden');
    document.getElementById('rateSelection').classList.add('hidden');
    document.getElementById('paymentSummary').classList.add('hidden');
    document.getElementById('proceedBtn').classList.add('hidden');
    document.getElementById('processPaymentBtn').classList.add('hidden');
    document.getElementById('loading').style.display = 'none';
    
    // Reset form inputs
    document.getElementById('amountPaid').value = '';
    document.getElementById('changeAmount').value = '';
}

function updateRoomTimers() {
    document.querySelectorAll('.room-timer').forEach(timer => {
        const roomId = timer.id.replace('timer-', '');
        // This would typically fetch the actual checkout time from the server
        // For now, we'll use a placeholder
        timer.textContent = '--:--:--';
    });
}


</script>
@endsection