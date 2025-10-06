@extends('layouts.admindashboard')

@section('title', 'Rates')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/adminrecords.css') }}">
    <script src="{{ asset('js/ph-complete-address.js') }}"></script>
    <style>
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
        border: 2px solid rgba(184,134,11,.1);
        border-radius: 12px;
        width: 500px;
        padding: 20px;
        margin-top: 8px;
        box-shadow: 0 4px 12px rgba(184,134,11,.08);
        transition: all 0.3s ease;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 350px;
        overflow-y: auto;
      }

      .accommodation-selection:hover {
        border-color: rgba(184,134,11,.2);
        box-shadow: 0 6px 16px rgba(184,134,11,.12);
        transform: translateY(-1px);
      }

      .checkbox-label {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px 20px;
        margin-bottom: 12px;
        background: linear-gradient(135deg, rgba(255,255,255,.8), rgba(248,249,255,.6));
        border: 1px solid rgba(184,134,11,.1);
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
        background: linear-gradient(180deg, var(--purple-primary), #DAA520);
        opacity: 0;
        transition: opacity 0.3s ease;
      }

      .checkbox-label:hover {
        background: linear-gradient(135deg, rgba(184,134,11,.05), rgba(184,134,11,.02));
        border-color: rgba(184,134,11,.2);
        transform: translateX(4px);
        box-shadow: 0 4px 12px rgba(184,134,11,.1);
      }

      .checkbox-label:hover::before {
        opacity: 1;
      }

      .checkbox-label:last-child {
        margin-bottom: 0;
      }

      /* Enhanced Accommodation Selection Styling - RESTORED */
      .accommodation-item {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px 20px;
        margin-bottom: 12px;
        background: linear-gradient(135deg, rgba(255,255,255,.8), rgba(248,249,255,.6));
        border: 1px solid rgba(184,134,11,.1);
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: visible;
        min-height: 60px;
      }

      .accommodation-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, var(--purple-primary), #DAA520);
        opacity: 0;
        transition: opacity 0.3s ease;
      }

      .accommodation-item:hover {
        background: linear-gradient(135deg, rgba(184,134,11,.05), rgba(184,134,11,.02));
        border-color: rgba(184,134,11,.2);
        transform: translateX(4px);
        box-shadow: 0 4px 12px rgba(184,134,11,.1);
      }

      .accommodation-item:hover::before {
        opacity: 1;
      }

      .accommodation-checkbox {
        width: 24px;
        height: 24px;
        margin: 0;
        cursor: pointer;
        border: 2px solid rgba(184,134,11,.3);
        border-radius: 8px;
        background: #fff;
        transition: all 0.3s ease;
        flex-shrink: 0;
        position: relative;
        /* Keep native checkbox behavior for better functionality */
        appearance: auto;
        -webkit-appearance: checkbox;
        -moz-appearance: checkbox;
      }

      .accommodation-item:hover .accommodation-checkbox {
        border-color: var(--purple-primary);
        box-shadow: 0 2px 8px rgba(184,134,11,.2);
      }

      .accommodation-checkbox:checked {
        background: linear-gradient(135deg, var(--purple-primary), #DAA520);
        border-color: var(--purple-primary);
        box-shadow: 0 4px 12px rgba(184,134,11,.3);
      }

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
      .accommodation-checkbox:checked ~ .accommodation-text {
        color: var(--purple-primary);
      }

      .accommodation-checkbox:checked ~ .accommodation-text strong {
        color: var(--purple-primary);
        font-weight: 700;
      }

      /* Focus States */
      .accommodation-item:focus-within {
        outline: none;
        box-shadow: 0 0 0 3px rgba(184,134,11,.15);
        border-color: var(--purple-primary);
      }

      .checkmark {
        display: none !important; /* Hide custom checkmark to avoid interference */
      }

      .checkbox-label:hover .checkmark {
        border-color: var(--purple-primary);
        box-shadow: 0 2px 8px rgba(184,134,11,.2);
      }

      .checkbox-label input[type="checkbox"]:checked + .checkmark {
        background: linear-gradient(135deg, var(--purple-primary), #DAA520);
        border-color: var(--purple-primary);
        box-shadow: 0 4px 12px rgba(184,134,11,.3);
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

      /* Unchecked state - ensure it's visible */
      .checkbox-label input[type="checkbox"]:not(:checked) + .checkmark {
        background: #fff;
        border: 2px solid rgba(184,134,11,.3);
        box-shadow: none;
      }

      .checkbox-label input[type="checkbox"]:not(:checked) + .checkmark::after {
        content: '';
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
        box-shadow: 0 0 0 3px rgba(184,134,11,.15);
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
        border: 2px solid rgba(184,134,11,.3);
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

      /* Enhanced Modal Form Styling */
      .rate-modal .modal-card {
        max-width: 600px;
        border-radius: 16px;
        box-shadow: 0 15px 40px rgba(184,134,11,.15);
        background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
        border: 1px solid rgba(184,134,11,.1);
        overflow: hidden;
      }

      .rate-modal .modal-header {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
        border-bottom: 1px solid rgba(184,134,11,.15);
        padding: 20px 24px;
      }

      .rate-modal .modal-header .chart-title {
        color: var(--purple-primary);
        font-size: 18px;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
      }

      .rate-modal .modal-header .chart-title i {
        background: linear-gradient(135deg, var(--purple-primary), #DAA520);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-size: 20px;
      }

      .rate-modal .modal-form {
        padding: 24px;
        background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
      }

      .rate-modal .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
      }

      .rate-modal .form-group {
        display: flex;
        flex-direction: column;
      }

      .rate-modal .form-group label {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 8px;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 6px;
      }

      .rate-modal .form-group label::before {
        content: '';
        width: 4px;
        height: 16px;
        background: linear-gradient(135deg, var(--purple-primary), #DAA520);
        border-radius: 2px;
      }

      .rate-modal .form-input {
        padding: 12px 16px;
        border: 2px solid rgba(184,134,11,.1);
        border-radius: 10px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: rgba(255,255,255,.8);
        color: var(--text-primary);
      }

      .rate-modal .form-input:focus {
        outline: none;
        border-color: var(--purple-primary);
        box-shadow: 0 0 0 3px rgba(184,134,11,.15);
        background: #ffffff;
        transform: translateY(-1px);
      }

      .rate-modal .form-input:hover {
        border-color: rgba(184,134,11,.2);
        background: #ffffff;
      }

      .rate-modal .modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding: 20px 24px;
        background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
        border-top: 1px solid rgba(184,134,11,.15);
      }

      .rate-modal .action-btn {
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
      }

      .rate-modal .action-btn.btn-outline {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
        border-color: var(--purple-primary);
        color: var(--purple-primary);
        box-shadow: 0 2px 8px rgba(184,134,11,.1);
      }

      .rate-modal .action-btn.btn-outline:hover {
        background: var(--purple-primary);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(184,134,11,.25);
      }

      .rate-modal .btn-primary {
        background: linear-gradient(135deg, var(--purple-primary), #DAA520);
        color: white;
        border: none;
        box-shadow: 0 4px 12px rgba(184,134,11,.3);
      }

      .rate-modal .btn-primary:hover {
        background: linear-gradient(135deg, #7c3aed, #8b5cf6);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(184,134,11,.4);
      }

      .rate-modal .action-btn.ml-auto {
        margin-left: auto;
        background: rgba(184,134,11,.1);
        color: var(--purple-primary);
        border: 1px solid rgba(184,134,11,.2);
        padding: 8px 12px;
        border-radius: 8px;
      }

      .rate-modal .action-btn.ml-auto:hover {
        background: rgba(184,134,11,.2);
        transform: scale(1.05);
      }

      /* Responsive Modal */
      @media (max-width: 768px) {
        .rate-modal .modal-card {
          max-width: 95%;
          margin: 20px;
        }
        
        .rate-modal .form-grid {
          grid-template-columns: 1fr;
          gap: 16px;
        }
        
        .rate-modal .modal-actions {
          flex-direction: column;
        }
        
        .rate-modal .action-btn {
          width: 100%;
          justify-content: center;
        }
      }

      /* Pagination styling for rates */
      #pagination { display: flex; justify-content: center; margin-top: 12px; }
      #pagination ul.pagination { display: flex; gap: 6px; list-style: none; padding: 0; margin: 0; }
      #pagination .page-link {
        background: linear-gradient(135deg, #ffffff, #f8f9ff);
        border: 1px solid rgba(184,134,11,.2);
        color: var(--text-primary);
        padding: 8px 12px;
        border-radius: 10px;
        font-weight: 700;
        box-shadow: 0 3px 10px rgba(184,134,11,.08);
        transition: all .2s ease;
      }
      #pagination .page-link:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(184,134,11,.15); border-color: rgba(184,134,11,.35); }
      #pagination li.active .page-link {
        background: linear-gradient(135deg, var(--purple-primary), #DAA520);
        color: #fff;
        border-color: transparent;
        box-shadow: 0 8px 22px rgba(184,134,11,.35);
      }
      #pagination .page-link.disabled { opacity: .5; cursor: not-allowed; }
    </style>
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
                            <th>Status</th>
                            <th>Date Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($rates) && $rates->count() > 0)
                            @foreach($rates as $rate)
                                <tr class="rate-row" data-rate-id="{{ $rate->id }}" data-duration="{{ $rate->duration }}"
                                    data-price="{{ $rate->price }}" data-status="{{ $rate->status }}"
                                    data-accommodation-ids="{{ $rate->accommodationsWithTrashed()->whereNull('rate_accommodations.deleted_at')->pluck('accommodations.id')->implode(',') }}"
                                    data-all-accommodation-ids="{{ $rate->accommodationsWithTrashed->pluck('accommodations.id')->implode(',') }}"
                                    data-accommodations="{{ $rate->accommodations->pluck('name')->implode(', ') }}"
                                    data-created="{{ $rate->created_at }}">
                                    <td data-label="ID">{{ $rate->id }}</td>
                                    <td data-label="Duration" class="rate-duration">{{ $rate->duration }}</td>
                                    <td data-label="Price">₱{{ number_format($rate->price, 2) }}</td>
                                    <td data-label="Status">{{ $rate->status }}</td>
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
            <nav class="pagination no-print" aria-label="Table pagination" id="pagination" style="display:none;"></nav>
        </div>
    </div>

    <!-- Rate Details Modal -->
    <div id="rateDetailsModal" class="modal">
        <div class="modal-card user-details-card">
            <div class="modal-header" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); border-bottom: 1px solid rgba(184,134,11,.15);">
                <h3 class="chart-title" style="color: var(--purple-primary); font-size: 18px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-tags" style="background: linear-gradient(135deg, var(--purple-primary), #DAA520); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-size: 20px;"></i>
                    Rate Details
                </h3>
                <button id="closeRateDetailsModal" class="action-btn ml-auto"><i class="fas fa-times"></i></button>
            </div>

            <div class="user-details-content" style="padding: 8px; background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);">
                <div class="user-info-section" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); border-radius: 10px; padding: 8px; margin-bottom: 8px; box-shadow: 0 2px 12px rgba(184,134,11,.08); border: 1px solid rgba(184,134,11,.1);">
                    <h4 style="color: var(--purple-primary); font-size: 14px; font-weight: 700; margin: 0 0 8px 0; display: flex; align-items: center; gap: 6px; padding-bottom: 6px; border-bottom: 1px solid rgba(184,134,11,.15);">
                        <i class="fas fa-info-circle" style="background: linear-gradient(135deg, var(--purple-primary), #DAA520); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-size: 14px;"></i>
                        Rate Information
                    </h4>
                    <div class="info-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 8px;">
                        <div class="info-item" style="background: rgba(184,134,11,.05); padding: 8px; border-radius: 8px; border-left: 3px solid var(--purple-primary);">
                            <label style="display:block;font-size:10px;font-weight:600;color:#6c757d;">ID</label>
                            <span id="detail-rate-id" style="font-size:14px;font-weight:700;color:var(--text-primary);">-</span>
                        </div>
                        <div class="info-item" style="background: rgba(184,134,11,.05); padding: 8px; border-radius: 8px; border-left: 3px solid var(--purple-primary);">
                            <label style="display:block;font-size:10px;font-weight:600;color:#6c757d;">Duration</label>
                            <span id="detail-rate-duration" style="font-size:14px;font-weight:700;color:var(--text-primary);">-</span>
                        </div>
                        <div class="info-item" style="background: rgba(184,134,11,.05); padding: 8px; border-radius: 8px; border-left: 3px solid var(--purple-primary);">
                            <label style="display:block;font-size:10px;font-weight:600;color:#6c757d;">Price</label>
                            <span id="detail-rate-price" style="font-size:14px;font-weight:700;color:var(--text-primary);">-</span>
                        </div>
                        <div class="info-item" style="background: rgba(184,134,11,.05); padding: 8px; border-radius: 8px; border-left: 3px solid var(--purple-primary);">
                            <label style="display:block;font-size:10px;font-weight:600;color:#6c757d;">Status</label>
                            <span id="detail-rate-status" style="font-size:14px;font-weight:700;color:var(--text-primary);">-</span>
                        </div>
                        <div class="info-item" style="background: rgba(184,134,11,.05); padding: 8px; border-radius: 8px; border-left: 3px solid var(--purple-primary);">
                            <label style="display:block;font-size:10px;font-weight:600;color:#6c757d;">Date Created</label>
                            <span id="detail-rate-created" style="font-size:14px;font-weight:700;color:var(--text-primary);">-</span>
                        </div>
                    </div>
                </div>

                <div class="address-info-section" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); border-radius: 10px; padding: 8px; box-shadow: 0 2px 12px rgba(184,134,11,.08); border: 1px solid rgba(184,134,11,.1);">
                    <h4 style="color: var(--purple-primary); font-size: 12px; font-weight: 700; margin: 0 0 6px 0; display: flex; align-items: center; gap: 4px; padding-bottom: 4px; border-bottom: 1px solid rgba(184,134,11,.15);">
                        <i class="fas fa-hotel" style="background: linear-gradient(135deg, var(--purple-primary), #DAA520); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-size: 12px;"></i>
                        Accommodations
                    </h4>
                    <div class="info-grid">
                      <div class="info-item span-2" style="grid-column: span 2;">
                          <div id="detail-rate-accommodations" style="background: rgba(184,134,11,.03); border-radius: 6px; padding: 6px; min-height: 50px; border: 1px dashed rgba(184,134,11,.2);"></div>
                      </div>
                    </div>
                </div>
            </div>

            <div class="modal-actions" style="padding: 8px; background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%); border-top: 1px solid rgba(184,134,11,.15); border-radius: 0 0 16px 16px;">
                <button type="button" id="closeRateDetails" class="action-btn btn-outline" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); border: 2px solid var(--purple-primary); color: var(--purple-primary); padding: 12px 24px; border-radius: 12px; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(184,134,11,.1);">
                    <i class="fas fa-times" style="margin-right: 8px;"></i>Close
                </button>
            </div>
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
                        <select name="status" class="form-input" required>
                            <option value="Standard">Standard</option>
                            <option value="Extending">Extending</option>
                            <option value="Extending/Standard">Extending/Standard</option>
                        </select>
                    </div>
                    <div class="form-group accommodation-field">
                        <label>Accommodations</label>
                        <div class="accommodation-selection">
                            @foreach($accommodations as $accommodation)
                              <label class="accommodation-item" style="display: block; margin-bottom: 12px; cursor: pointer;">
                                <input type="checkbox" name="accommodation_ids[]" value="{{ $accommodation->id }}" class="accommodation-checkbox" style="margin-right: 10px;">
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
                            <option value="Standard">Standard</option>
                            <option value="Extending">Extending</option>
                            <option value="Extending/Standard">Extending/Standard</option>
                        </select>
                    </div>
                    <div class="form-group accommodation-field">
                        <label>Accommodations</label>
                        <div class="accommodation-selection" id="updateAccommodations">
                            @foreach($accommodations as $accommodation)
                              <label class="accommodation-item" style="display: block; margin-bottom: 12px; cursor: pointer;">
                                <input type="checkbox" name="accommodation_ids[]" value="{{ $accommodation->id }}" class="accommodation-checkbox" style="margin-right: 10px;">
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
                    <button type="submit" class="btn-primary inline">Update Rate</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function () {
            // State
            var allRows = [];
            var filteredRows = [];
            var currentPage = 1;
            var pageSize = 10;

            // Get all rows from the table
            var table = document.getElementById('ratesTable').getElementsByTagName('tbody')[0];
            var rows = Array.from(table.rows);
            
            // Convert table rows to data objects
            allRows = rows.map(function(row) {
              var cells = row.cells;
              return {
                id: cells[0] ? cells[0].textContent.trim() : '',
                duration: cells[1] ? cells[1].textContent.trim() : '',
                price: cells[2] ? cells[2].textContent.trim() : '',
                status: cells[3] ? cells[3].textContent.trim() : '',
                created: cells[4] ? cells[4].textContent.trim() : '',
                element: row
              };
            });

            function applySearch(){
              var search = document.getElementById('adminSearch');
              var q = (search ? search.value : '').toLowerCase();
              if (!q) { 
                filteredRows = allRows.slice(); 
              } else {
                filteredRows = allRows.filter(function(r){
                  var t = (''+r.id+' '+r.duration+' '+r.price+' '+r.status+' '+r.created).toLowerCase();
                  return t.indexOf(q) !== -1;
                });
              }
              currentPage = 1;
              renderTable();
              renderPagination();
            }

            function renderTable(){
              var tbody = document.getElementById('ratesTable').getElementsByTagName('tbody')[0];
              tbody.innerHTML = '';
              var start = (currentPage - 1) * pageSize;
              var pageItems = filteredRows.slice(start, start + pageSize);
              pageItems.forEach(function(r){
                tbody.appendChild(r.element);
              });
              
              // Re-attach event listeners for edit and delete buttons after rendering
              attachButtonEventListeners();
            }

            function renderPagination(){
              var container = document.getElementById('pagination');
              var totalPages = Math.max(1, Math.ceil(filteredRows.length / pageSize));
              if (totalPages <= 1) { container.style.display = 'none'; container.innerHTML=''; return; }
              container.style.display = '';
              
              // Calculate the range of pages to show (max 10 pages)
              var maxVisiblePages = 10;
              var startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
              var endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
              
              // Adjust startPage if we're near the end
              if (endPage - startPage + 1 < maxVisiblePages) {
                startPage = Math.max(1, endPage - maxVisiblePages + 1);
              }
              
              var html = '<ul class="pagination">';
              function pageItem(p, label, disabled, active){
                var liCls = active ? 'active' : '';
                var btnCls = 'page-link' + (disabled ? ' disabled' : '');
                return '<li class="'+liCls+'"><button type="button" class="'+btnCls+'" data-page="'+p+'">'+label+'</button></li>';
              }
              
              // Previous button
              html += pageItem(Math.max(1, currentPage-1), '&laquo;', currentPage===1, false);
              
              // First page if not in range
              if (startPage > 1) {
                html += pageItem(1, '1', false, false);
                if (startPage > 2) {
                  html += '<li class="page-item disabled"><span class="page-link disabled">...</span></li>';
                }
              }
              
              // Page numbers in range
              for (var p = startPage; p <= endPage; p++){
                html += pageItem(p, p, false, p===currentPage);
              }
              
              // Last page if not in range
              if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                  html += '<li class="page-item disabled"><span class="page-link disabled">...</span></li>';
                }
                html += pageItem(totalPages, totalPages, false, false);
              }
              
              // Next button
              html += pageItem(Math.min(totalPages, currentPage+1), '&raquo;', currentPage===totalPages, false);
              html += '</ul>';
              container.innerHTML = html;
            }

            function attachPaginationHandler(){
              var container = document.getElementById('pagination');
              container.addEventListener('click', function(e){
                var btn = e.target.closest('button[data-page]');
                if (!btn || btn.classList.contains('disabled')) return;
                currentPage = parseInt(btn.getAttribute('data-page')) || 1;
                renderTable();
                renderPagination();
              });
            }

            // Client-side search
            var search = document.getElementById('adminSearch');
            if (search) search.addEventListener('input', applySearch);

            attachPaginationHandler();
            applySearch();
            
            // Attach initial event listeners
            attachButtonEventListeners();
            
            // Fix accommodation checkbox functionality
            fixAccommodationCheckboxes();
            console.log('Initial accommodation checkboxes setup completed');

            var modal = document.getElementById('rateModal');
            var openBtn = document.getElementById('openAddAdmin');
            var closeBtn = document.getElementById('closeRateModal');
            var cancelBtn = document.getElementById('cancelRate');

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
                    
                    // Get selected accommodations
                    var selectedAccommodations = [];
                    document.querySelectorAll('.accommodation-selection input[type="checkbox"]:checked').forEach(function(cb) {
                        selectedAccommodations.push(cb.value);
                    });
                    
                    console.log('Selected accommodations for add:', selectedAccommodations);
                    
                    // Remove unchecked checkboxes from form data to prevent empty values
                    document.querySelectorAll('.accommodation-selection input[type="checkbox"]:not(:checked)').forEach(function(cb) {
                        cb.disabled = true;
                    });
                    
                    if (confirm('Add rate "' + duration + '" at ₱' + price + ' (' + status + ') with ' + selectedAccommodations.length + ' accommodations?')) {
                        this.submit();
                    } else {
                        // Re-enable checkboxes if user cancels
                        document.querySelectorAll('.accommodation-selection input[type="checkbox"]:not(:checked)').forEach(function(cb) {
                            cb.disabled = false;
                        });
                    }
                });
            }

            // Close Add Modal
            function closeModal() { modal.style.display = 'none'; }
            if (openBtn) openBtn.addEventListener('click', openModal);
            if (closeBtn) closeBtn.addEventListener('click', closeModal);
            if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

            // Re-add handlers after pagination
            var originalRenderTable = renderTable;
            renderTable = function() {
              originalRenderTable();
              addRowClickHandlers();
            };

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
                    
                    // Get selected accommodations
                    var selectedAccommodations = [];
                    document.querySelectorAll('#updateAccommodations input[type="checkbox"]:checked').forEach(function(cb) {
                        selectedAccommodations.push(cb.value);
                    });
                    
                    
                    // Remove unchecked checkboxes from form data to prevent empty values
                    document.querySelectorAll('#updateAccommodations input[type="checkbox"]:not(:checked)').forEach(function(cb) {
                        cb.disabled = true;
                    });
                    
                    if (confirm('Update rate to "' + duration + '" ₱' + price + ' with status ' + status + ' and ' + selectedAccommodations.length + ' accommodations?')) {
                        this.submit();
                    } else {
                        // Re-enable checkboxes if user cancels
                        document.querySelectorAll('#updateAccommodations input[type="checkbox"]:not(:checked)').forEach(function(cb) {
                            cb.disabled = false;
                        });
                    }
                });
            }

            // Close Update Modal
            function closeUpdateModal() { updateModal.style.display = 'none'; }
            if (closeUpdateModalBtn) closeUpdateModalBtn.addEventListener('click', closeUpdateModal);
            if (cancelUpdateBtn) cancelUpdateBtn.addEventListener('click', closeUpdateModal);

            // Function to attach event listeners for edit and delete buttons
            function attachButtonEventListeners() {
              // Hook update buttons
              document.querySelectorAll('[data-update]').forEach(function(btn){
                // Remove existing listeners to prevent duplicates
                btn.removeEventListener('click', handleUpdateClick);
                btn.addEventListener('click', handleUpdateClick);
              });
              
              // Hook archive buttons
              document.querySelectorAll('[data-archive]').forEach(function(btn){
                // Remove existing listeners to prevent duplicates
                btn.removeEventListener('click', handleArchiveClick);
                btn.addEventListener('click', handleArchiveClick);
              });
            }
            
            // Update button click handler
            function handleUpdateClick(e) {
              e.stopPropagation();
              var row = this.closest('tr');
              var d = row ? row.dataset : {};

              // Pre-fill fields
              // set accommodations multi-select
              var activeAccIds = (d.accommodationIds || '').split(',').filter(Boolean);
              var allAccIds = (d.allAccommodationIds || '').split(',').filter(Boolean);
              
              document.querySelectorAll('#updateAccommodations input[type="checkbox"]').forEach(function(cb){
                var isChecked = activeAccIds.includes(cb.value);
                var isInAllList = allAccIds.includes(cb.value);
                
                // Only set checked if it's in the active list
                cb.checked = isChecked;
                
                
                // Ensure checkbox is visible and clickable
                cb.style.opacity = '1';
                cb.style.visibility = 'visible';
                cb.style.display = 'inline-block';
                cb.style.pointerEvents = 'auto';
              });
              document.getElementById('u_duration').value = d.duration || '';
              document.getElementById('u_price').value = d.price || '';
              document.getElementById('u_status').value = d.status || 'Standard';

              // Point form action
              var updateForm = document.getElementById('updateForm');
              var rateId = this.getAttribute('data-rate-id');
              updateForm.setAttribute('action', '/adminPages/rates/update/' + rateId);

              openUpdateModal();
              
              // Fix checkbox functionality in update modal
              setTimeout(function() {
                fixAccommodationCheckboxes();
                console.log('Accommodation checkboxes fixed for update modal');
              }, 100);
            }
            
            // Archive button click handler
            function handleArchiveClick(e) {
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
            }
            
            // Fix accommodation checkbox functionality - SIMPLE APPROACH
            function fixAccommodationCheckboxes() {
              // Get all accommodation checkboxes
              var checkboxes = document.querySelectorAll('#updateAccommodations .accommodation-checkbox');
              
              // Ensure checkboxes are clickable
              checkboxes.forEach(function(checkbox, index) {
                // Ensure maximum clickability
                checkbox.style.pointerEvents = 'auto';
                checkbox.style.cursor = 'pointer';
                checkbox.style.opacity = '1';
                checkbox.style.visibility = 'visible';
                checkbox.style.display = 'inline-block';
              });
              
            }

            // Rate details modal functionality
            var rateDetailsModal = document.getElementById('rateDetailsModal');
            var closeRateDetailsBtn = document.getElementById('closeRateDetails');
            var closeRateDetailsModalBtn = document.getElementById('closeRateDetailsModal');

            function openRateDetailsModal() { rateDetailsModal.style.display = 'flex'; }
            function closeRateDetailsModal() { rateDetailsModal.style.display = 'none'; }

            if (closeRateDetailsBtn) closeRateDetailsBtn.addEventListener('click', closeRateDetailsModal);
            if (closeRateDetailsModalBtn) closeRateDetailsModalBtn.addEventListener('click', closeRateDetailsModal);

            // Row click opens details
            function addRowClickHandlers() {
              var ratesTable = document.getElementById('ratesTable');
              if (ratesTable) {
                ratesTable.addEventListener('click', function(e){
                  var row = e.target.closest('.rate-row');
                  if (!row) return;
                  if (e.target.closest('button')) return;
                  var d = row.dataset;
                      document.getElementById('detail-rate-id').textContent = d.rateId || '-';
                      document.getElementById('detail-rate-duration').textContent = d.duration || '-';
                      document.getElementById('detail-rate-price').textContent = d.price ? '₱' + (parseFloat(d.price).toFixed(2)) : '-';
                      document.getElementById('detail-rate-status').textContent = d.status || '-';
                      document.getElementById('detail-rate-created').textContent = d.created ? new Date(d.created).toLocaleDateString() : '-';
                      var accContainer = document.getElementById('detail-rate-accommodations');
                      accContainer.innerHTML = '<div class="loading" style="display: flex; align-items: center; justify-content: center; gap: 6px; color: #6c757d; font-style: italic; padding: 12px; font-size: 12px;"><i class="fas fa-spinner fa-spin" style="color: var(--purple-primary); font-size: 12px;"></i><span>Loading...</span></div>';
                      fetch('/adminPages/rates/' + (d.rateId || '') + '/accommodations')
                        .then(function(resp){ return resp.json(); })
                        .then(function(data){
                          if (data.accommodations && data.accommodations.length) {
                            var html = '<div style="display: grid; gap: 6px; max-height: 150px; overflow-y: auto; padding-right: 4px;">';
                            data.accommodations.forEach(function(a){
                              html += '<div class="accommodation-card" style="padding: 8px; background: linear-gradient(135deg, rgba(184,134,11,.05), rgba(184,134,11,.02)); border-radius: 8px; border-left: 3px solid var(--purple-primary); box-shadow: 0 2px 6px rgba(184,134,11,.08); transition: all 0.3s ease; position: relative;">';
                              html += '<div style="display:flex;align-items:center;gap:6px;">';
                              html += '<div style="width: 24px; height: 24px; background: linear-gradient(135deg, var(--purple-primary), #DAA520); border-radius: 6px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(184,134,11,.3);">';
                              html += '<i class="fas fa-hotel" style="color: white; font-size: 10px;"></i>';
                              html += '</div>';
                              html += '<div>';
                              html += '<strong style="color: var(--text-primary); font-size: 12px; font-weight: 700; display: block;">' + a.name + '</strong>';
                              html += '<small style="color: #6c757d; font-size: 10px;">Capacity: ' + (a.capacity ?? '-') + '</small>';
                              html += '</div>';
                              html += '</div></div>';
                            });
                            html += '</div>';
                            accContainer.innerHTML = html;
                          } else {
                            accContainer.innerHTML = '<div style="text-align:center;color:#6c757d;font-size:10px;font-style:italic;">No accommodations</div>';
                          }
                        })
                        .catch(function(){
                          accContainer.innerHTML = '<div style="text-align:center;color:#dc3545;font-size:10px;font-style:italic;">Failed to load</div>';
                        });
                  openRateDetailsModal();
                });
              }
            }

            // Initialize row click handlers
            addRowClickHandlers();
            
            
        })();
    </script>

@endsection
