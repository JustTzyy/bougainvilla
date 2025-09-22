@extends('layouts.admindashboard')

@section('title','Transaction Reports')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/adminrecords.css') }}">
<style>
  .report-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 4px 20px rgba(138,92,246,.1);
    border: 1px solid rgba(138,92,246,.1);
  }

  .report-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 2px solid rgba(138,92,246,.1);
  }

  .report-title {
    color: var(--purple-primary);
    font-size: 20px;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .report-title i {
    background: linear-gradient(135deg, var(--purple-primary), #a29bfe);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .report-filters {
    display: flex;
    gap: 12px;
    align-items: center;
  }

  .filter-group {
    display: flex;
    flex-direction: column;
    gap: 4px;
  }

  .filter-group label {
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .filter-input {
    padding: 8px 12px;
    border: 2px solid rgba(138,92,246,.1);
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
    background: #fff;
  }

  .filter-input:focus {
    outline: none;
    border-color: var(--purple-primary);
    box-shadow: 0 0 0 3px rgba(138,92,246,.1);
  }

  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
  }

  .stat-card {
    background: linear-gradient(135deg, rgba(138,92,246,.05), rgba(138,92,246,.02));
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    border: 1px solid rgba(138,92,246,.1);
    transition: all 0.3s ease;
  }

  .stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(138,92,246,.15);
  }

  .stat-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, var(--purple-primary), #a29bfe);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
    color: white;
    font-size: 20px;
  }

  .stat-value {
    font-size: 24px;
    font-weight: 700;
    color: var(--purple-primary);
    margin-bottom: 4px;
  }

  .stat-label {
    font-size: 12px;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
  }

  .chart-container {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    border: 1px solid rgba(138,92,246,.1);
    margin-bottom: 24px;
  }

  .chart-title {
    color: var(--purple-primary);
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .chart-placeholder {
    height: 300px;
    background: linear-gradient(135deg, rgba(138,92,246,.05), rgba(138,92,246,.02));
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    font-style: italic;
    border: 2px dashed rgba(138,92,246,.2);
  }

  .export-buttons {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    margin-bottom: 24px;
  }

  .export-btn {
    padding: 10px 20px;
    border: 2px solid var(--purple-primary);
    background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
    color: var(--purple-primary);
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
  }

  .export-btn:hover {
    background: linear-gradient(135deg, var(--purple-primary), #a29bfe);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(138,92,246,.3);
  }

  @media (max-width: 768px) {
    .report-filters {
      flex-direction: column;
      align-items: stretch;
    }
    
    .stats-grid {
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    }
    
    .export-buttons {
      justify-content: center;
    }
  }
</style>
@endpush

@section('content')
<div class="dashboard-page">
  <div class="page-header">
    <h1 class="page-title">Transaction Reports</h1>
  </div>

  @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
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

  <!-- Summary Statistics -->
  <div class="report-card">
    <div class="report-header">
      <h3 class="report-title">
        <i class="fas fa-chart-bar"></i>
        Summary Statistics
      </h3>
      <div class="report-filters">
        <div class="filter-group">
          <label>From Date</label>
          <input type="date" id="fromDate" class="filter-input" value="{{ date('Y-m-01') }}">
        </div>
        <div class="filter-group">
          <label>To Date</label>
          <input type="date" id="toDate" class="filter-input" value="{{ date('Y-m-d') }}">
        </div>
        <div class="filter-group">
          <label>Status</label>
          <select id="statusFilter" class="filter-input">
            <option value="">All Status</option>
            <option value="Pending">Pending</option>
            <option value="Confirmed">Confirmed</option>
            <option value="Completed">Completed</option>
            <option value="Cancelled">Cancelled</option>
          </select>
        </div>
        <button id="applyFilters" class="btn-primary inline" style="margin-top: 20px;">
          <i class="fas fa-filter"></i> Apply Filters
        </button>
      </div>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-credit-card"></i>
        </div>
        <div class="stat-value" id="totalTransactions">0</div>
        <div class="stat-label">Total Transactions</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-value" id="totalRevenue">₱0.00</div>
        <div class="stat-label">Total Revenue</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-value" id="completedTransactions">0</div>
        <div class="stat-label">Completed</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-clock"></i>
        </div>
        <div class="stat-value" id="pendingTransactions">0</div>
        <div class="stat-label">Pending</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-times-circle"></i>
        </div>
        <div class="stat-value" id="cancelledTransactions">0</div>
        <div class="stat-label">Cancelled</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-calculator"></i>
        </div>
        <div class="stat-value" id="averageAmount">₱0.00</div>
        <div class="stat-label">Average Amount</div>
      </div>
    </div>
  </div>

  <!-- Revenue Chart -->
  <div class="report-card">
    <div class="chart-container">
      <h3 class="chart-title">
        <i class="fas fa-chart-line"></i>
        Revenue Trend
      </h3>
      <div class="chart-placeholder">
        <div style="text-align: center;">
          <i class="fas fa-chart-line" style="font-size: 48px; color: var(--purple-primary); margin-bottom: 16px;"></i>
          <p>Revenue trend chart will be displayed here</p>
          <small>Integration with charting library required</small>
        </div>
      </div>
    </div>
  </div>

  <!-- Transaction Status Chart -->
  <div class="report-card">
    <div class="chart-container">
      <h3 class="chart-title">
        <i class="fas fa-chart-pie"></i>
        Transaction Status Distribution
      </h3>
      <div class="chart-placeholder">
        <div style="text-align: center;">
          <i class="fas fa-chart-pie" style="font-size: 48px; color: var(--purple-primary); margin-bottom: 16px;"></i>
          <p>Status distribution pie chart will be displayed here</p>
          <small>Integration with charting library required</small>
        </div>
      </div>
    </div>
  </div>

  <!-- Export Options -->
  <div class="export-buttons">
    <a href="#" class="export-btn" id="exportPDF">
      <i class="fas fa-file-pdf"></i>
      Export PDF
    </a>
    <a href="#" class="export-btn" id="exportExcel">
      <i class="fas fa-file-excel"></i>
      Export Excel
    </a>
    <a href="#" class="export-btn" id="exportCSV">
      <i class="fas fa-file-csv"></i>
      Export CSV
    </a>
  </div>

  <!-- Recent Transactions Table -->
  <div class="chart-card card-tight">
    <div class="section-header-pad">
      <h3 class="chart-title">Recent Transactions</h3>
    </div>

    <div class="table-wrapper">
      <table class="table sortable-table" id="recentTransactionsTable">
        <thead>
          <tr>
            <th>Transaction ID</th>
            <th>Guest Name</th>
            <th>Room</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          @if(isset($recentTransactions) && $recentTransactions->count() > 0)
            @foreach($recentTransactions as $transaction)
              <tr>
                <td data-label="Transaction ID">#{{ $transaction->id }}</td>
                <td data-label="Guest Name">{{ $transaction->guest_name }}</td>
                <td data-label="Room">{{ $transaction->room }}</td>
                <td data-label="Amount">₱{{ number_format($transaction->amount, 2) }}</td>
                <td data-label="Status">{{ $transaction->status }}</td>
                <td data-label="Date">{{ $transaction->created_at->format('M d, Y') }}</td>
              </tr>
            @endforeach
          @else
            <tr>
              <td colspan="6" class="text-center">No recent transactions found</td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
  (function(){
    // Sample data for demonstration
    var sampleData = {
      totalTransactions: 156,
      totalRevenue: 245678.50,
      completedTransactions: 89,
      pendingTransactions: 45,
      cancelledTransactions: 22,
      averageAmount: 1574.86
    };

    // Update statistics
    function updateStats(data) {
      document.getElementById('totalTransactions').textContent = data.totalTransactions;
      document.getElementById('totalRevenue').textContent = '₱' + data.totalRevenue.toLocaleString('en-US', {minimumFractionDigits: 2});
      document.getElementById('completedTransactions').textContent = data.completedTransactions;
      document.getElementById('pendingTransactions').textContent = data.pendingTransactions;
      document.getElementById('cancelledTransactions').textContent = data.cancelledTransactions;
      document.getElementById('averageAmount').textContent = '₱' + data.averageAmount.toLocaleString('en-US', {minimumFractionDigits: 2});
    }

    // Apply filters
    document.getElementById('applyFilters').addEventListener('click', function() {
      var fromDate = document.getElementById('fromDate').value;
      var toDate = document.getElementById('toDate').value;
      var status = document.getElementById('statusFilter').value;
      
      // Here you would typically make an AJAX call to get filtered data
      // For now, we'll just show a loading state
      console.log('Applying filters:', { fromDate, toDate, status });
      
      // Simulate loading
      setTimeout(function() {
        updateStats(sampleData);
      }, 500);
    });

    // Export functionality
    document.getElementById('exportPDF').addEventListener('click', function(e) {
      e.preventDefault();
      alert('PDF export functionality would be implemented here');
    });

    document.getElementById('exportExcel').addEventListener('click', function(e) {
      e.preventDefault();
      alert('Excel export functionality would be implemented here');
    });

    document.getElementById('exportCSV').addEventListener('click', function(e) {
      e.preventDefault();
      alert('CSV export functionality would be implemented here');
    });

    // Initialize with sample data
    updateStats(sampleData);
  })();
</script>

@endsection
