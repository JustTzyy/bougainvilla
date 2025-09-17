@extends('layouts.admindashboard')

@section('title', 'Rates')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/adminrecords.css') }}">
    <script src="{{ asset('js/ph-complete-address.js') }}"></script>
@endpush

@section('content')
    <div class="dashboard-page">
        <div class="page-header">
            <h1 class="page-title">Rates</h1>
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
                setTimeout(function () {
                    alerts.forEach(function (el) {
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
                <input id="adminSearch" type="text" placeholder="Search rates" class="search-input">
            </div>
            <div class="toolbar-actions">
                <a href="{{ route('rates.archive') }}" class="archive-btn">
                    <i class="fas fa-archive"></i> Archive
                </a>
                <button id="openAddAdmin"><i class="fas fa-dollar-sign"></i> Add Rate</button>
            </div>
        </div>

        <div class="chart-card card-tight">
            <div class="section-header-pad">
                <h3 class="chart-title">List</h3>
            </div>

            <div class="table-wrapper">
                <table class="table sortable-table" id="ratesTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Duration</th>
                            <th>Price</th>
                            <th>Accommodation</th>
                            <th>Date Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($rates) && $rates->count() > 0)
                            @foreach($rates as $rate)
                                <tr class="rate-row" data-rate-id="{{ $rate->id }}" data-duration="{{ $rate->duration }}"
                                    data-price="{{ $rate->price }}" data-accommodation-id="{{ $rate->accommodation_id }}"
                                    data-created="{{ $rate->created_at }}">
                                    <td data-label="ID">{{ $rate->id }}</td>
                                    <td data-label="Duration" class="rate-duration">{{ $rate->duration }}</td>
                                    <td data-label="Price">₱{{ number_format($rate->price, 2) }}</td>
                                    <td data-label="Status">{{ $rate->accommodation->name }}</td>
                                    <td data-label="Date Created">{{ $rate->created_at->format('M d, Y') }}</td>
                                    <td data-label="Actions">
                                        <button class="action-btn small" data-update data-rate-id="{{ $rate->id }}">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                        <button class="action-btn small" data-archive data-rate-id="{{ $rate->id }}">
                                            <i class="fas fa-archive"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center">No rates found</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @if(isset($rates) && $rates->hasPages())
                <nav class="pagination" aria-label="Table pagination">
                    {{ $rates->links() }}
                </nav>
            @endif
        </div>
    </div>

    <!-- Add Rate Modal -->
    <div id="rateModal" class="modal rate-modal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 class="chart-title">Add Rate</h3>
                <button id="closeRateModal" class="action-btn ml-auto">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="rateForm" action="{{ route('adminPages.rates.post') }}" class="modal-form" method="POST">
                @csrf
                <div class="form-grid">
                    <div class="form-group">
                        <label>Accommodation</label>
                        <select name="accommodation_id" class="form-input" required>
                            <option value="">Select Accommodation</option>
                            @foreach($accommodations as $accommodation)
                                <option value="{{ $accommodation->id }}">{{ $accommodation->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Duration</label>
                        <input type="text" name="duration" class="form-input" placeholder="e.g., 1 Hour" required>
                    </div>
                    <div class="form-group">
                        <label>Price (₱)</label>
                        <input type="number" name="price" class="form-input" step="0.01" placeholder="e.g., 150.00"
                            required>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-input" required enabled>
                            <option value="Active" selected>Active</option>
                        </select>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" id="cancelRate" class="action-btn btn-outline">Cancel</button>
                    <button type="submit" class="btn-primary inline">Save Rate</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Update Rate Modal -->
    <div id="updateModal" class="modal rate-modal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 class="chart-title">Update Rate</h3>
                <button id="closeUpdateModal" class="action-btn ml-auto">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="updateForm" class="modal-form" method="POST">
                @csrf
                @method('POST')
                <div class="form-grid">
                    <div class="form-group">
                        <label>Accommodation</label>
                        <select name="accommodation_id" id="u_accommodation" class="form-input" required>
                            <option value="">Select Accommodation</option>
                            @foreach($accommodations as $accommodation)
                                <option value="{{ $accommodation->id }}">{{ $accommodation->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Duration</label>
                        <input name="duration" id="u_duration" class="form-input" placeholder="e.g., 1 Hour" required>
                    </div>
                    <div class="form-group">
                        <label>Price (₱)</label>
                        <input type="number" name="price" id="u_price" class="form-input" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="u_status" class="form-input" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" id="cancelUpdate" class="action-btn btn-outline">Cancel</button>
                    <button type="submit" class="btn-primary inline">Update Rate</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function () {
            var modal = document.getElementById('rateModal');
            var openBtn = document.getElementById('openAddAdmin');
            var closeBtn = document.getElementById('closeRateModal');
            var cancelBtn = document.getElementById('cancelRate');
            var search = document.getElementById('adminSearch');
            var table = document.getElementById('ratesTable').getElementsByTagName('tbody')[0];

            // Open Add Modal
            function openModal() { modal.style.display = 'flex'; }

            // Confirmation for Add Rate form
            var rateForm = document.getElementById('rateForm');
            if (rateForm) {
                rateForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    var duration = document.querySelector('input[name="duration"]').value;
                    var price = document.querySelector('input[name="price"]').value;
                    var status = document.querySelector('select[name="status"]').value;
                    if (confirm('Add rate "' + duration + '" at ₱' + price + ' (status: ' + status + ')?')) {
                        this.submit();
                    }
                });
            }

            // Close Add Modal
            function closeModal() { modal.style.display = 'none'; }
            if (openBtn) openBtn.addEventListener('click', openModal);
            if (closeBtn) closeBtn.addEventListener('click', closeModal);
            if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

            // Search
            if (search) search.addEventListener('input', function () {
                var q = this.value.toLowerCase();
                Array.prototype.forEach.call(table.rows, function (row) {
                    var text = row.innerText.toLowerCase();
                    row.style.display = text.indexOf(q) !== -1 ? '' : 'none';
                });
            });

            // Update modal logic
            var updateModal = document.getElementById('updateModal');
            var closeUpdateModalBtn = document.getElementById('closeUpdateModal');
            var cancelUpdateBtn = document.getElementById('cancelUpdate');

            function openUpdateModal() { updateModal.style.display = 'flex'; }

            // Confirmation for Update Rate form
            var updateForm = document.getElementById('updateForm');
            if (updateForm) {
                updateForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    var duration = document.getElementById('u_duration').value;
                    var price = document.getElementById('u_price').value;
                    var status = document.getElementById('u_status').value;
                    if (confirm('Update rate to "' + duration + '" ₱' + price + ' with status ' + status + '?')) {
                        this.submit();
                    }
                });
            }

            // Close Update Modal
            function closeUpdateModal() { updateModal.style.display = 'none'; }
            if (closeUpdateModalBtn) closeUpdateModalBtn.addEventListener('click', closeUpdateModal);
            if (cancelUpdateBtn) cancelUpdateBtn.addEventListener('click', closeUpdateModal);

            // Hook update buttons
            document.querySelectorAll('[data-update]').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    var row = this.closest('tr');
                    var d = row ? row.dataset : {};

                    // Pre-fill fields
                    document.getElementById('u_accommodation').value = d.accommodationId || '';

                    document.getElementById('u_duration').value = d.duration || '';
                    document.getElementById('u_price').value = d.price || '';
                    document.getElementById('u_status').value = d.status || '';

                    // Point form action
                    var updateForm = document.getElementById('updateForm');
                    var rateId = this.getAttribute('data-rate-id');
                    updateForm.setAttribute('action', '/adminPages/rates/update/' + rateId);

                    openUpdateModal();
                });
            });

            // Archive functionality
            document.querySelectorAll('[data-archive]').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    var rateId = this.getAttribute('data-rate-id');
                    var rateName = this.closest('tr').querySelector('.rate-duration').textContent;

                    if (confirm('Are you sure you want to archive "' + rateName + '"?')) {
                        var form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '/adminPages/rates/delete/' + rateId;

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