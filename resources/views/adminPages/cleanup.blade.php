@extends('layouts.admindashboard')

@section('title', 'Guest Cleanup Management')

@push('styles')
<style>
.cleanup-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-align: center;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: var(--purple-primary);
    margin-bottom: 5px;
}

.stat-label {
    color: #666;
    font-size: 0.9rem;
}

.cleanup-section {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.section-title {
    color: var(--purple-primary);
    margin-bottom: 15px;
    font-size: 1.2rem;
    font-weight: 600;
}

.guest-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

.guest-table th,
.guest-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.guest-table th {
    background-color: #f8f9fa;
    font-weight: 600;
    color: #333;
}

.guest-table tr:hover {
    background-color: #f8f9fa;
}

.action-buttons {
    display: flex;
    gap: 8px;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 0.8rem;
    border-radius: 4px;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-warning {
    background-color: #ffc107;
    color: #212529;
}

.btn-warning:hover {
    background-color: #e0a800;
}

.btn-danger {
    background-color: #dc3545;
    color: white;
}

.btn-danger:hover {
    background-color: #c82333;
}

.cleanup-actions {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}
    
.btn-primary {
    background-color: var(--purple-primary);
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-primary:hover {
    background-color: #6f42c1;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-secondary:hover {
    background-color: #5a6268;
}

.output-container {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 15px;
    margin-top: 15px;
    font-family: monospace;
    white-space: pre-wrap;
    max-height: 300px;
    overflow-y: auto;
}

.empty-state {
    text-align: center;
    padding: 40px;
    color: #666;
}

.empty-state i {
    font-size: 3rem;
    color: #ddd;
    margin-bottom: 15px;
}

.age-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 500;
}

.age-badge.warning {
    background-color: #fff3cd;
    color: #856404;
}

.age-badge.danger {
    background-color: #f8d7da;
    color: #721c24;
}
</style>
@endpush

@section('content')
<div class="cleanup-management">
    <div class="page-header">
        <h1 class="page-title">Guest Cleanup Management</h1>
        <p class="page-subtitle">Automatically manage guest data retention and cleanup</p>
    </div>

    <!-- Statistics Cards -->
    <div class="cleanup-stats">
        <div class="stat-card">
            <div class="stat-number">{{ $totalGuests }}</div>
            <div class="stat-label">Total Guests</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $activeGuests }}</div>
            <div class="stat-label">Active Guests</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $softDeletedGuests }}</div>
            <div class="stat-label">Soft Deleted</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $guestsForSoftDelete->total() }}</div>
            <div class="stat-label">Ready for Soft Delete</div>
        </div>
    </div>

    <!-- Cleanup Actions -->
    <div class="cleanup-section">
        <h2 class="section-title">Cleanup Actions</h2>
        <div class="cleanup-actions">
            <button class="btn-primary" onclick="runCleanup(false)">
                <i class="fas fa-play"></i> Run Cleanup Now
            </button>
            <button class="btn-secondary" onclick="runCleanup(true)">
                <i class="fas fa-eye"></i> Preview Cleanup (Dry Run)
            </button>
        </div>
        <div id="cleanup-output" class="output-container" style="display: none;"></div>
    </div>

    <!-- Guests Ready for Soft Delete -->
    <div class="cleanup-section">
        <h2 class="section-title">
            Guests Ready for Soft Delete 
            <small>(Older than {{ $threeMonthsAgo->format('M d, Y') }})</small>
        </h2>
        
        @if($guestsForSoftDelete->count() > 0)
            <table class="guest-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Created</th>
                        <th>Age</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($guestsForSoftDelete as $guest)
                        <tr>
                            <td>{{ $guest->id }}</td>
                            <td>{{ $guest->firstName }} {{ $guest->lastName }}</td>
                            <td>{{ $guest->number ?? '-' }}</td>
                            <td>{{ $guest->created_at->format('M d, Y') }}</td>
                            <td>
                                <span class="age-badge warning">
                                    {{ $guest->created_at->diffInDays(now()) }} days
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-sm btn-warning" onclick="softDeleteGuest({{ $guest->id }})">
                                        <i class="fas fa-trash"></i> Soft Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="pagination-links">
                {{ $guestsForSoftDelete->links() }}
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-check-circle"></i>
                <p>No guests ready for soft delete</p>
            </div>
        @endif
    </div>

    <!-- Guests Ready for Hard Delete -->
    <div class="cleanup-section">
        <h2 class="section-title">
            Guests Ready for Hard Delete 
            <small>(Soft deleted before {{ $threeMonthsAgo->format('M d, Y') }})</small>
        </h2>
        
        @if($guestsForHardDelete->count() > 0)
            <table class="guest-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Created</th>
                        <th>Soft Deleted</th>
                        <th>Days Since Soft Delete</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($guestsForHardDelete as $guest)
                        <tr>
                            <td>{{ $guest->id }}</td>
                            <td>{{ $guest->firstName }} {{ $guest->lastName }}</td>
                            <td>{{ $guest->number ?? '-' }}</td>
                            <td>{{ $guest->created_at->format('M d, Y') }}</td>
                            <td>{{ $guest->deleted_at->format('M d, Y') }}</td>
                            <td>
                                <span class="age-badge danger">
                                    {{ $guest->deleted_at->diffInDays(now()) }} days
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-sm btn-danger" onclick="hardDeleteGuest({{ $guest->id }})">
                                        <i class="fas fa-trash-alt"></i> Permanently Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="pagination-links">
                {{ $guestsForHardDelete->links() }}
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-check-circle"></i>
                <p>No guests ready for permanent deletion</p>
            </div>
        @endif
    </div>

    <!-- Cleanup Schedule Info -->
    <div class="cleanup-section">
        <h2 class="section-title">Automatic Cleanup Schedule</h2>
        <div class="schedule-info">
            <p><strong>Schedule:</strong> Daily at 2:00 AM</p>
            <p><strong>Soft Delete:</strong> Guests older than 3 months</p>
            <p><strong>Hard Delete:</strong> Guests soft deleted 3+ months ago (6 months total age)</p>
            <p><strong>Status:</strong> <span style="color: green;">Active</span></p>
        </div>
    </div>
</div>

<script>
function runCleanup(dryRun) {
    const outputDiv = document.getElementById('cleanup-output');
    outputDiv.style.display = 'block';
    outputDiv.textContent = 'Running cleanup...';
    
    fetch('/adminPages/cleanup/run', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            dry_run: dryRun
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            outputDiv.textContent = data.output;
            if (!dryRun) {
                // Reload page after successful cleanup
                setTimeout(() => {
                    location.reload();
                }, 2000);
            }
        } else {
            outputDiv.textContent = 'Error: ' + data.message;
        }
    })
    .catch(error => {
        outputDiv.textContent = 'Error: ' + error.message;
    });
}

function softDeleteGuest(guestId) {
    if (!confirm('Are you sure you want to soft delete this guest?')) {
        return;
    }
    
    fetch(`/adminPages/cleanup/soft-delete/${guestId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Guest soft deleted successfully');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}

function hardDeleteGuest(guestId) {
    if (!confirm('Are you sure you want to PERMANENTLY DELETE this guest? This action cannot be undone!')) {
        return;
    }
    
    if (!confirm('This will permanently remove the guest from the database. Are you absolutely sure?')) {
        return;
    }
    
    fetch(`/adminPages/cleanup/hard-delete/${guestId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Guest permanently deleted successfully');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}
</script>
@endsection
