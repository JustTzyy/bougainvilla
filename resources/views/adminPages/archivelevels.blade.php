@extends('layouts.admindashboard')

@section('title', 'Archived Level Records')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/archiveadminrecords.css') }}">
    <style>
        /* Enhanced Level Details Modal Styles (match active Levels page) */
        #levelDetailsModal .modal-card { max-width: 600px; border-radius: 16px; box-shadow: 0 15px 40px rgba(138,92,246,.15); background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); border: 1px solid rgba(138,92,246,.1); overflow: hidden; }
        #levelDetailsModal .info-item:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(138,92,246,.15); background: rgba(138,92,246,.08); }
        #levelDetailsModal .user-info-section:hover, #levelDetailsModal .address-info-section:hover { transform: translateY(-1px); box-shadow: 0 8px 30px rgba(138,92,246,.12); }
        /* Status badge dynamic colors */
        #detail-status.status-active { }
        #detail-status.status-inactive { background: linear-gradient(135deg, #dc3545, #e74c3c) !important; color: #fff !important; box-shadow: 0 2px 8px rgba(220,53,69,.3); }
        /* Room cards */
        #detail-rooms-list .room-card:hover { transform: translateY(-3px); box-shadow: 0 12px 30px rgba(138,92,246,.2); background: linear-gradient(135deg, rgba(138,92,246,.08), rgba(138,92,246,.04)) !important; }
        @keyframes pulse { 0%{opacity:1} 50%{opacity:.5} 100%{opacity:1} }
        #detail-rooms-list .loading { animation: pulse 1.5s ease-in-out infinite; }
        @media (max-width: 768px) { #levelDetailsModal .modal-card{ max-width:95%; margin:20px;} #levelDetailsModal .info-grid{ grid-template-columns:1fr !important; } }
    </style>
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
            <div class="modal-header" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); border-bottom: 1px solid rgba(138,92,246,.15);">
                <h3 class="chart-title" style="color: var(--purple-primary); font-size: 18px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-layer-group" style="background: linear-gradient(135deg, var(--purple-primary), #a29bfe); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-size: 20px;"></i>
                    Level Details
                </h3>
                <button id="closeLevelDetailsModal" class="action-btn ml-auto"><i class="fas fa-times"></i></button>
            </div>

            <div class="user-details-content" style="padding: 12px; background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);">
                <!-- Level Information Section -->
                <div class="user-info-section" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); border-radius: 12px; padding: 12px; margin-bottom: 12px; box-shadow: 0 2px 12px rgba(138,92,246,.08); border: 1px solid rgba(138,92,246,.1);">
                    <h4 style="color: var(--purple-primary); font-size: 16px; font-weight: 700; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px; padding-bottom: 12px; border-bottom: 2px solid rgba(138,92,246,.15);">
                        <i class="fas fa-layer-group" style="background: linear-gradient(135deg, var(--purple-primary), #a29bfe); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-size: 18px;"></i>
                        Level Information
                    </h4>
                    <div class="info-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px;">
                        <div class="info-item" style="background: rgba(138,92,246,.05); padding: 10px; border-radius: 10px; border-left: 4px solid var(--purple-primary);">
                            <label style="display:block;font-size:12px;font-weight:600;color:#6c757d;">
                                <i class="fas fa-hashtag" style="margin-right:6px;color:var(--purple-primary);"></i>Floor Number
                            </label>
                            <span id="detail-floor-number" style="font-size:16px;font-weight:700;color:var(--text-primary);">-</span>
                        </div>
                        <div class="info-item" style="background: rgba(138,92,246,.05); padding: 10px; border-radius: 10px; border-left: 4px solid var(--purple-primary);">
                            <label style="display:block;font-size:12px;font-weight:600;color:#6c757d;">
                                <i class="fas fa-align-left" style="margin-right:6px;color:var(--purple-primary);"></i>Description
                            </label>
                            <span id="detail-description" style="font-size:16px;font-weight:700;color:var(--text-primary);">-</span>
                        </div>
                        <div class="info-item" style="background: rgba(138,92,246,.05); padding: 10px; border-radius: 10px; border-left: 4px solid var(--purple-primary);">
                            <label style="display:block;font-size:12px;font-weight:600;color:#6c757d;">
                                <i class="fas fa-toggle-on" style="margin-right:6px;color:var(--purple-primary);"></i>Status
                            </label>
                            <span id="detail-status" class="status-badge" style="display:inline-block;padding:6px 12px;border-radius:20px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">-</span>
                        </div>
                        <div class="info-item" style="background: rgba(138,92,246,.05); padding: 10px; border-radius: 10px; border-left: 4px solid var(--purple-primary);">
                            <label style="display:block;font-size:12px;font-weight:600;color:#6c757d;">
                                <i class="fas fa-calendar-plus" style="margin-right:6px;color:var(--purple-primary);"></i>Date Created
                            </label>
                            <span id="detail-created" style="font-size:16px;font-weight:700;color:var(--text-primary);">-</span>
                        </div>
                        <div class="info-item" style="background: rgba(138,92,246,.05); padding: 10px; border-radius: 10px; border-left: 4px solid var(--purple-primary);">
                            <label style="display:block;font-size:12px;font-weight:600;color:#6c757d;">
                                <i class="fas fa-calendar-times" style="margin-right:6px;color:var(--purple-primary);"></i>Date Archived
                            </label>
                            <span id="detail-archived" style="font-size:16px;font-weight:700;color:var(--text-primary);">-</span>
                        </div>
                    </div>
                </div>

                <!-- Rooms Section -->
                <div class="address-info-section" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); border-radius: 12px; padding: 12px; box-shadow: 0 2px 12px rgba(138,92,246,.08); border: 1px solid rgba(138,92,246,.1);">
                    <h4 style="color: var(--purple-primary); font-size: 14px; font-weight: 700; margin: 0 0 8px 0; display: flex; align-items: center; gap: 6px; padding-bottom: 8px; border-bottom: 1px solid rgba(138,92,246,.15);">
                        <i class="fas fa-door-open" style="background: linear-gradient(135deg, var(--purple-primary), #a29bfe); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-size: 14px;"></i>
                        Rooms
                    </h4>
                    <div class="info-grid">
                        <div class="info-item span-2" style="grid-column: span 2;">
                            <div id="detail-rooms-list" style="background: rgba(138,92,246,.03); border-radius: 8px; padding: 8px; min-height: 60px; border: 1px dashed rgba(138,92,246,.2);">
                                <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #6c757d; font-style: italic; font-size: 12px;">
                                    <i class="fas fa-spinner fa-spin" style="margin-right: 6px; color: var(--purple-primary); font-size: 12px;"></i>
                                    Loading...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-actions" style="padding: 12px; background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%); border-top: 1px solid rgba(138,92,246,.15); border-radius: 0 0 16px 16px;">
                <button type="button" id="closeLevelDetails" class="action-btn btn-outline" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); border: 2px solid var(--purple-primary); color: var(--purple-primary); padding: 12px 24px; border-radius: 12px; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(138,92,246,.1);">
                    <i class="fas fa-times" style="margin-right: 8px;"></i>Close
                </button>
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
                        id: l.id,
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
                document.getElementById('detail-floor-number').textContent = l.id || '-';
                document.getElementById('detail-description').textContent = l.description || '-';
                var statusEl = document.getElementById('detail-status');
                statusEl.textContent = l.status || '-';
                statusEl.className = 'status-badge';
                if ((l.status || '').toLowerCase() === 'active') { statusEl.classList.add('status-active'); } else { statusEl.classList.add('status-inactive'); }
                document.getElementById('detail-created').textContent = l.created_at ? new Date(l.created_at).toLocaleDateString() : '-';
                document.getElementById('detail-archived').textContent = l.archived_at ? new Date(l.archived_at).toLocaleDateString() : '-';
                // Load rooms list like active page
                loadLevelRooms(l.id);
            }

            function loadLevelRooms(levelId) {
                var el = document.getElementById('detail-rooms-list');
                el.innerHTML = '<div class="loading" style="display:flex;align-items:center;justify-content:center;gap:6px;color:#6c757d;font-style:italic;padding:20px;font-size:12px;"><i class="fas fa-spinner fa-spin" style="color:var(--purple-primary);font-size:12px;"></i><span>Loading...</span></div>';
                fetch('/adminPages/levels/' + levelId + '/rooms')
                  .then(r=>r.json())
                  .then(data=>{
                    if (data.rooms && data.rooms.length) {
                        var html = '<div style="display:grid;gap:6px;max-height:200px;overflow-y:auto;padding-right:4px;">';
                        data.rooms.forEach(function(room){
                            var statusColor = '#6c757d', statusBg='rgba(108,117,125,.1)', statusIcon='fas fa-circle';
                            if (room.status === 'Available') { statusColor='#28a745'; statusBg='rgba(40,167,69,.15)'; statusIcon='fas fa-check-circle'; }
                            else if (room.status === 'Occupied') { statusColor='#dc3545'; statusBg='rgba(220,53,69,.15)'; statusIcon='fas fa-user'; }
                            else if (room.status === 'Under Maintenance') { statusColor='#ffc107'; statusBg='rgba(255,193,7,.15)'; statusIcon='fas fa-tools'; }
                            html += '<div class="room-card" style="padding:8px;background:linear-gradient(135deg, rgba(138,92,246,.05), rgba(138,92,246,.02));border-radius:8px;border-left:3px solid var(--purple-primary);box-shadow:0 2px 6px rgba(138,92,246,.08);transition:all .3s ease;position:relative;">';
                            html += '<div style="display:flex;justify-content:space-between;align-items:center;position:relative;z-index:1;">';
                            html += '<div style="display:flex;align-items:center;gap:6px;">';
                            html += '<div style="width:24px;height:24px;background:linear-gradient(135deg,var(--purple-primary),#a29bfe);border-radius:6px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 6px rgba(138,92,246,.3);"><i class="fas fa-door-open" style="color:white;font-size:10px;"></i></div>';
                            html += '<div><strong style="color:var(--text-primary);font-size:12px;font-weight:700;display:block;">' + (room.name || room.id) + '</strong><small style="color:#6c757d;font-size:10px;">ID: ' + room.id + '</small></div>';
                            html += '</div>';
                            html += '<span style="padding:3px 6px;border-radius:12px;font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.3px;background:' + statusBg + ';color:' + statusColor + ';display:flex;align-items:center;gap:2px;box-shadow:0 1px 4px rgba(0,0,0,.1);"><i class="' + statusIcon + '" style="font-size:8px;"></i>' + room.status + '</span>';
                            html += '</div>';
                            html += '</div>';
                        });
                        html += '</div>';
                        el.innerHTML = html;
                    } else {
                        el.innerHTML = '<div style="text-align:center;padding:20px;color:#6c757d;"><div style="width:40px;height:40px;background:linear-gradient(135deg,rgba(138,92,246,.1),rgba(138,92,246,.05));border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;"><i class="fas fa-door-closed" style="font-size:16px;color:var(--purple-primary);opacity:.6;"></i></div><h4 style="color:#6c757d;margin:0 0 4px 0;font-weight:600;font-size:12px;">No Rooms</h4><p style="font-style:italic;margin:0;color:#6c757d;font-size:10px;">No rooms found</p></div>';
                    }
                  })
                  .catch(()=>{ el.innerHTML = '<div style="text-align:center;padding:20px;color:#dc3545;"><div style="width:40px;height:40px;background:linear-gradient(135deg,rgba(220,53,69,.1),rgba(220,53,69,.05));border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;"><i class="fas fa-exclamation-triangle" style="font-size:16px;color:#dc3545;"></i></div><h4 style="color:#dc3545;margin:0 0 4px 0;font-weight:600;font-size:12px;">Error</h4><p style="font-style:italic;margin:0;color:#dc3545;font-size:10px;">Failed to load</p></div>'; });
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