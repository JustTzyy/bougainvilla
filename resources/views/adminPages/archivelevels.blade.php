@extends('layouts.admindashboard')

@section('title', 'Archived Level Records')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/archiveadminrecords.css') }}">
@endpush

@section('content')
    <div class="dashboard-page">
        <div class="page-header">
            <h1 class="page-title">Archived Levels</h1>
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
                <input id="archiveSearch" type="text" placeholder="Search archived levels" class="search-input">
            </div>
            <div class="toolbar-actions">
                <a href="{{ route('adminPages.levels') }}" class="archive-btn">
                    <i class="fas fa-arrow-left"></i> Back to Records
                </a>
            </div>
        </div>

        <div class="chart-card card-tight">
            <div class="section-header-pad">
                <h3 class="chart-title">List</h3>
            </div>

            <div class="table-wrapper">
                <table class="table sortable-table" id="archivedTable">
                    <thead>
                        <tr>
                            <th>
                                Floor No.</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Archived Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($levels) && $levels->count() > 0)
                            @foreach($levels as $level)
                                <tr class="level-row" data-id="{{ $level->id }}" data-description="{{ $level->description }}"
                                    data-status="{{ $level->status }}" data-created="{{ $level->created_at }}"
                                    data-archived="{{ $level->deleted_at }}">
                                    <td data-label="ID">{{ $level->id }}</td>
                                    <td data-label="Description">{{ $level->description }}</td>
                                    <td data-label="Status">{{ $level->status }}</td>
                                    <td data-label="Archived Date">{{ optional($level->deleted_at)->format('M d, Y') }}</td>
                                    <td data-label="Actions">
                                        <button class="action-btn small" data-restore>
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="text-center">No archived levels found</td>
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

    <!-- Level Details Modal -->
    <div id="levelDetailsModal" class="modal">
        <div class="modal-card user-details-card">
            <div class="modal-header">
                <h3 class="chart-title">Level Details</h3>
                <button id="closeLevelDetailsModal" class="action-btn ml-auto"><i class="fas fa-times"></i></button>
            </div>

            <div class="user-details-content">
                <div class="user-info-section">
                    <h4><i class="fas fa-layer-group"></i> Level Information</h4>
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Description:</label>
                            <span id="detail-description">-</span>
                        </div>
                        <div class="info-item">
                            <label>Status:</label>
                            <span id="detail-status" class="status-badge">-</span>
                        </div>
                        <div class="info-item">
                            <label>Date Created:</label>
                            <span id="detail-created">-</span>
                        </div>
                        <div class="info-item">
                            <label>Date Archived:</label>
                            <span id="detail-archived">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-actions">
                <button type="button" id="closeLevelDetails" class="action-btn btn-outline">Close</button>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var search = document.getElementById('archiveSearch');
            var table = document.getElementById('archivedTable').getElementsByTagName('tbody')[0];

            // Search functionality
            if (search) search.addEventListener('input', function () {
                var q = this.value.toLowerCase();
                Array.prototype.forEach.call(table.rows, function (row) {
                    var text = row.innerText.toLowerCase();
                    row.style.display = text.indexOf(q) !== -1 ? '' : 'none';
                });
            });

            // Level details modal functionality
            var modal = document.getElementById('levelDetailsModal');
            var closeBtn = document.getElementById('closeLevelDetails');
            var closeX = document.getElementById('closeLevelDetailsModal');

            function openModal() { modal.style.display = 'flex'; }
            function closeModal() { modal.style.display = 'none'; }

            if (closeBtn) closeBtn.addEventListener('click', closeModal);
            if (closeX) closeX.addEventListener('click', closeModal);

            var rows = document.querySelectorAll('.level-row');
            rows.forEach(function (row) {
                row.addEventListener('click', function (e) {
                    if (e.target.closest('button')) return;
                    var l = this.dataset;
                    populateLevelDetails({
                        description: l.description,
                        status: l.status,
                        created_at: l.created,
                        archived_at: l.archived
                    });
                    openModal();
                });
            });

            // Populate details
            function populateLevelDetails(l) {
                document.getElementById('detail-description').textContent = l.description || '-';
                document.getElementById('detail-status').textContent = l.status || '-';
                document.getElementById('detail-created').textContent = l.created_at ? new Date(l.created_at).toLocaleDateString() : '-';
                document.getElementById('detail-archived').textContent = l.archived_at ? new Date(l.archived_at).toLocaleDateString() : '-';
            }

            // Restore functionality
            document.querySelectorAll('[data-restore]').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    var row = this.closest('tr');
                    var id = row.querySelector('td[data-label="ID"]').textContent;
                    var desc = row.querySelector('td[data-label="Description"]').textContent;

                    if (confirm('Are you sure you want to restore "' + desc + '"?')) {
                        var form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '/adminPages/levels/restore/' + id;

                        var csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                            document.querySelector('input[name="_token"]')?.value;

                        var methodField = document.createElement('input');
                        methodField.type = 'hidden';
                        methodField.name = '_method';
                        methodField.value = 'PATCH';

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