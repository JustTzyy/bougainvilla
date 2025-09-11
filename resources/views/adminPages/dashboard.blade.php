@extends('layouts.admindashboard')

@section('title', 'Dashboard')

@section('content')
<div class="dashboard-page">
    <!-- Overview Section -->
    <div class="page-header">
        <h1 class="page-title">Overview</h1>
        <div class="page-actions">
            <i class="fas fa-info-circle info-icon"></i>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card sales-card">
            <div class="stat-card-bg">
                <div class="stat-card-pattern"></div>
            </div>
            <div class="stat-content">
                <div class="stat-header">
                    <h3 class="stat-title">Weekly Sales</h3>
                    <i class="fas fa-chart-line stat-icon"></i>
                </div>
                <div class="stat-value">$15,0000</div>
                <div class="stat-change positive">Increased by 60%</div>
            </div>
        </div>

        <div class="stat-card orders-card">
            <div class="stat-card-bg">
                <div class="stat-card-pattern"></div>
            </div>
            <div class="stat-content">
                <div class="stat-header">
                    <h3 class="stat-title">Weekly Orders</h3>
                    <i class="fas fa-bookmark stat-icon"></i>
                </div>
                <div class="stat-value">45,6334</div>
                <div class="stat-change negative">Decreased by 10%</div>
            </div>
        </div>

        <div class="stat-card visitors-card">
            <div class="stat-card-bg">
                <div class="stat-card-pattern"></div>
            </div>
            <div class="stat-content">
                <div class="stat-header">
                    <h3 class="stat-title">Visitors Online</h3>
                    <i class="fas fa-gem stat-icon"></i>
                </div>
                <div class="stat-value">95,5741</div>
                <div class="stat-change positive">Increased by 5%</div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-grid">
        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title">Visits And Sales Statistics</h3>
            </div>
            <div class="chart-content">
                <div class="chart-legend">
                    <div class="legend-item">
                        <div class="legend-color chn"></div>
                        <span>CHN</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color usa"></div>
                        <span>USA</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color uk"></div>
                        <span>UK</span>
                    </div>
                </div>
                <div class="bar-chart">
                    <div class="chart-bars">
                        <div class="month-group">
                            <div class="month-label">Jan</div>
                            <div class="bars">
                                <div class="bar chn" style="height: 60%"></div>
                                <div class="bar usa" style="height: 80%"></div>
                                <div class="bar uk" style="height: 45%"></div>
                            </div>
                        </div>
                        <div class="month-group">
                            <div class="month-label">Feb</div>
                            <div class="bars">
                                <div class="bar chn" style="height: 70%"></div>
                                <div class="bar usa" style="height: 65%"></div>
                                <div class="bar uk" style="height: 55%"></div>
                            </div>
                        </div>
                        <div class="month-group">
                            <div class="month-label">Mar</div>
                            <div class="bars">
                                <div class="bar chn" style="height: 85%"></div>
                                <div class="bar usa" style="height: 75%"></div>
                                <div class="bar uk" style="height: 70%"></div>
                            </div>
                        </div>
                        <div class="month-group">
                            <div class="month-label">Apr</div>
                            <div class="bars">
                                <div class="bar chn" style="height: 90%"></div>
                                <div class="bar usa" style="height: 85%"></div>
                                <div class="bar uk" style="height: 80%"></div>
                            </div>
                        </div>
                        <div class="month-group">
                            <div class="month-label">May</div>
                            <div class="bars">
                                <div class="bar chn" style="height: 75%"></div>
                                <div class="bar usa" style="height: 90%"></div>
                                <div class="bar uk" style="height: 65%"></div>
                            </div>
                        </div>
                        <div class="month-group">
                            <div class="month-label">Jun</div>
                            <div class="bars">
                                <div class="bar chn" style="height: 95%"></div>
                                <div class="bar usa" style="height: 80%"></div>
                                <div class="bar uk" style="height: 85%"></div>
                            </div>
                        </div>
                        <div class="month-group">
                            <div class="month-label">Jul</div>
                            <div class="bars">
                                <div class="bar chn" style="height: 100%"></div>
                                <div class="bar usa" style="height: 95%"></div>
                                <div class="bar uk" style="height: 90%"></div>
                            </div>
                        </div>
                        <div class="month-group">
                            <div class="month-label">Aug</div>
                            <div class="bars">
                                <div class="bar chn" style="height: 85%"></div>
                                <div class="bar usa" style="height: 100%"></div>
                                <div class="bar uk" style="height: 75%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title">Traffic Sources</h3>
            </div>
            <div class="chart-content">
                <div class="donut-chart">
                    <div class="donut-container">
                        <svg viewBox="0 0 100 100" class="donut-svg">
                            <circle cx="50" cy="50" r="40" fill="none" stroke="#e5e7eb" stroke-width="8"/>
                            <circle cx="50" cy="50" r="40" fill="none" stroke="#ec4899" stroke-width="8" 
                                    stroke-dasharray="125.6 251.2" stroke-dashoffset="0" class="donut-segment bookmarks"/>
                            <circle cx="50" cy="50" r="40" fill="none" stroke="#3b82f6" stroke-width="8" 
                                    stroke-dasharray="94.2 251.2" stroke-dashoffset="-125.6" class="donut-segment search"/>
                            <circle cx="50" cy="50" r="40" fill="none" stroke="#10b981" stroke-width="8" 
                                    stroke-dasharray="94.2 251.2" stroke-dashoffset="-219.8" class="donut-segment direct"/>
                        </svg>
                        <div class="donut-center">
                            <div class="donut-percentage">100%</div>
                        </div>
                    </div>
                </div>
                <div class="donut-legend">
                    <div class="legend-item">
                        <div class="legend-color bookmarks"></div>
                        <span>Bookmarks Click</span>
                        <span class="legend-percentage">40%</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color search"></div>
                        <span>Search Engines</span>
                        <span class="legend-percentage">30%</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color direct"></div>
                        <span>Direct Click</span>
                        <span class="legend-percentage">30%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
