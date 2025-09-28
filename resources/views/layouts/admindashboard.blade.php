<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Bougainvilla Lodge Admin</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @stack('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="dashboard-body">
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <div class="logo-icon">
                        <svg width="32" height="32" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                            <!-- Flower petals -->
                            <path d="M16 4 C20 8, 20 12, 16 16 C12 12, 12 8, 16 4 Z" fill="#8B0000"/>
                            <path d="M28 16 C24 20, 20 20, 16 16 C20 12, 24 12, 28 16 Z" fill="#8B0000"/>
                            <path d="M16 28 C12 24, 12 20, 16 16 C20 20, 20 24, 16 28 Z" fill="#8B0000"/>
                            <path d="M4 16 C8 12, 12 12, 16 16 C12 20, 8 20, 4 16 Z" fill="#8B0000"/>
                            <!-- Flower center -->
                            <circle cx="16" cy="16" r="3" fill="#B8860B"/>
                            <!-- Stem -->
                            <rect x="15" y="20" width="2" height="8" fill="#1a1a1a"/>
                            <!-- Leaves -->
                            <ellipse cx="12" cy="24" rx="2" ry="1" fill="#1a1a1a" transform="rotate(-30 12 24)"/>
                            <ellipse cx="20" cy="24" rx="2" ry="1" fill="#1a1a1a" transform="rotate(30 20 24)"/>
                        </svg>
                    </div>
                    <div class="logo-text">
                        <div class="logo-main">BOUGAINVIILA</div>
                        <div class="logo-sub">LODGE</div>
                    </div>
                </div>
            </div>

            <div class="user-profile">
                <div class="user-avatar">
                    <i class="fas fa-user-circle" style="font-size:40px; color:#B8860B;"></i>
                </div>
                <div class="user-info">
                    <div class="user-name">{{ Auth::user()->name ?? 'User' }}</div>
                    <div class="user-role">{{ Auth::user()->role->role ?? 'Admin' }}</div>
                </div>
                <div class="bookmark-icon">
                    <i class="fas fa-bookmark"></i>
                </div>
            </div>

            <nav class="sidebar-nav">
                <ul class="nav-list">
                    <li class="nav-item {{ request()->routeIs('adminPages.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('adminPages.dashboard') }}" class="nav-link">
                            <i class="fas fa-home"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <li
                        class="nav-item has-submenu {{ request()->routeIs('adminPages.adminrecords') || request()->routeIs('adminPages.frontdeskrecords') || request()->routeIs('adminPages.archiveadminrecords') || request()->routeIs('adminPages.archivefrontdeskrecords') ? 'active' : '' }}">
                        <a href="#" class="nav-link" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-user-lock"></i>
                            <span>User Management</span>
                            <i class="fas fa-chevron-right submenu-arrow"></i>
                        </a>
                        <ul class="submenu" aria-label="Data submenu">
                            <li
                                class="{{ request()->routeIs('adminPages.adminrecords') || request()->routeIs('adminPages.archiveadminrecords') ? 'active' : '' }}">
                                <a href="{{ route('adminPages.adminrecords') }}" class="submenu-link">
                                    <span>Admin</span>
                                </a>
                            </li>
                            <li
                                class="{{ request()->routeIs('adminPages.frontdeskrecords') || request()->routeIs('adminPages.archivefrontdeskrecords') ? 'active' : '' }}">
                                <a href="{{ route('adminPages.frontdeskrecords') }}" class="submenu-link">
                                    <span>FrontDesk</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li
                        class="nav-item has-submenu 
    {{ request()->routeIs('adminPages.accommodations') || request()->routeIs('adminPages.levels') ||   request()->routeIs('adminPages.rooms') || request()->routeIs('adminPages.rates') || request()->routeIs('accommodations.*') ? 'active' : '' }}">
                        <a href="#" class="nav-link" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-user-lock"></i>
                            <span>Room Management</span>
                            <i class="fas fa-chevron-right submenu-arrow"></i>
                        </a>
                        <ul class="submenu" aria-label="RoomManagement submenu">

                            {{-- Accommodations --}}
                            <li
                                class="{{ request()->routeIs('adminPages.accommodations') || request()->routeIs('accommodations.archive') ? 'active' : '' }}">
                                <a href="{{ route('adminPages.accommodations') }}" class="submenu-link">
                                    <span>Accommodations</span>
                                </a>
                            </li>

                            {{-- Levels --}}
                            <li
                                class="{{ request()->routeIs('adminPages.levels') || request()->routeIs('levels.*') ? 'active' : '' }}">
                                <a href="{{ route('adminPages.levels') }}" class="submenu-link">
                                    <span>Levels</span>
                                </a>
                            </li>

                            {{-- Rooms --}}
                            <li
                                class="{{ request()->routeIs('adminPages.rooms') || request()->routeIs('rooms.*') ? 'active' : '' }}">
                                <a href="{{ route('adminPages.rooms') }}" class="submenu-link">
                                    <span>Rooms</span>
                                </a>
                            </li>

                            <li
                                class="{{ request()->routeIs('adminPages.rates') || request()->routeIs('rates.*') ? 'active' : '' }}">
                                <a href="{{ route('adminPages.rates') }}" class="submenu-link">
                                    <span>Rates</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li
                        class="nav-item has-submenu {{ request()->routeIs('adminPages.transactions') || request()->routeIs('adminPages.archivetransactions') || request()->routeIs('adminPages.transactionreports') || request()->routeIs('transactions.*') ? 'active' : '' }}">
                        <a href="#" class="nav-link" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-credit-card"></i>
                            <span>Transact Management</span>
                            <i class="fas fa-chevron-right submenu-arrow"></i>
                        </a>
                        <ul class="submenu" aria-label="Transaction Management submenu">
                            <li
                                class="{{ request()->routeIs('adminPages.transactions') ? 'active' : '' }}">
                                <a href="{{ route('adminPages.transactions') }}" class="submenu-link">
                                    <span>Transactions</span>
                                </a>
                            </li>
                            <li
                                class="{{ request()->routeIs('adminPages.archivetransactions') ? 'active' : '' }}">
                                <a href="{{ route('adminPages.archivetransactions') }}" class="submenu-link">
                                    <span>Archive Transactions</span>
                                </a>
                            </li>
                            <li
                                class="{{ request()->routeIs('adminPages.transactionreports') ? 'active' : '' }}">
                                <a href="{{ route('adminPages.transactionreports') }}" class="submenu-link">
                                    <span>Transaction Reports</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- Reports quick links (Dashboard KPIs) --}}
                    <li class="nav-item has-submenu {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <a href="#" class="nav-link" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-chart-pie"></i>
                            <span>Reports</span>
                            <i class="fas fa-chevron-right submenu-arrow"></i>
                        </a>
                        <ul class="submenu" aria-label="Reports submenu">
                            <li class="{{ request()->routeIs('reports.payments') ? 'active' : '' }}">
                                <a href="{{ route('reports.payments') }}" class="submenu-link"><span>Payments</span></a>
                            </li>
                            <li class="{{ request()->routeIs('reports.guests') ? 'active' : '' }}">
                                <a href="{{ route('reports.guests') }}" class="submenu-link"><span>Guests</span></a>
                            </li>
                            <li class="{{ request()->routeIs('reports.all-transactions') ? 'active' : '' }}">
                                <a href="{{ route('reports.all-transactions') }}" class="submenu-link"><span>Overall Transactions</span></a>
                            </li>
                            <li class="{{ request()->routeIs('reports.all-archived-transactions') ? 'active' : '' }}">
                                <a href="{{ route('reports.all-archived-transactions') }}" class="submenu-link"><span>Overall Archive Transactions</span></a>
                            </li>
                            <li class="{{ request()->routeIs('reports.logs') ? 'active' : '' }}">
                                <a href="{{ route('reports.logs') }}" class="submenu-link"><span>Logs Report</span></a>
                            </li>
                        </ul>
                    </li>

                
                   
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="header-left">
                    <button class="menu-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="search-container">
                    </div>
                </div>

                <div class="header-right">
                    <div class="user-dropdown" onclick="toggleUserDropdown()">
                        <i class="fas fa-user-circle" style="font-size:40px; color:#B8860B;"></i>
                        <span class="user-name-small">{{ Auth::user()->name ?? 'User' }}</span>
                        <i class="fas fa-chevron-down"></i>

                        <!-- User Dropdown Menu -->
                        <div class="user-dropdown-menu" id="userDropdownMenu">
                            <a href="{{ route('adminPages.settings') }}" class="dropdown-item">
                                <i class="fas fa-cog dropdown-icon"></i>
                                <span>Settings</span>
                            </a>
                            <a href="{{ route('adminPages.auditlogs') }}" class="dropdown-item">
                                <i class="fas fa-history dropdown-icon activity"></i>
                                <span>My Activity Log</span>
                            </a>
                            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                                @csrf
                                <button type="submit" class="dropdown-item"
                                    style="background: none; border: none; width: 100%; text-align: left; cursor: pointer;">
                                    <i class="fas fa-sign-out-alt dropdown-icon signout"></i>
                                    <span>Signout</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="header-actions">


                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="page-content">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        // Responsive sidebar toggle functionality
        document.querySelector('.menu-toggle').addEventListener('click', function () {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.sidebar-overlay');

            sidebar.classList.toggle('collapsed');

            // Handle mobile overlay
            if (window.innerWidth <= 768) {
                if (sidebar.classList.contains('collapsed')) {
                    if (!overlay) {
                        createOverlay();
                    }
                    document.querySelector('.sidebar-overlay').classList.add('show');
                } else {
                    if (overlay) {
                        overlay.classList.remove('show');
                    }
                }
            }
        });

        // Create mobile overlay
        function createOverlay() {
            const overlay = document.createElement('div');
            overlay.className = 'sidebar-overlay';
            overlay.addEventListener('click', function () {
                document.querySelector('.sidebar').classList.remove('collapsed');
                overlay.classList.remove('show');
            });
            document.body.appendChild(overlay);
        }

        // User dropdown toggle functionality
        function toggleUserDropdown() {
            const dropdown = document.getElementById('userDropdownMenu');
            dropdown.classList.toggle('show');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function (event) {
            const dropdown = document.getElementById('userDropdownMenu');
            const userDropdown = document.querySelector('.user-dropdown');

            if (!userDropdown.contains(event.target)) {
                dropdown.classList.remove('show');
            }
        });

        // Handle window resize
        window.addEventListener('resize', function () {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.sidebar-overlay');

            if (window.innerWidth > 768) {
                // Desktop: remove overlay if exists
                if (overlay) {
                    overlay.remove();
                }
            } else {
                // Mobile: ensure overlay exists when sidebar is open
                if (sidebar.classList.contains('collapsed') && !overlay) {
                    createOverlay();
                    document.querySelector('.sidebar-overlay').classList.add('show');
                }
            }
        });

        // Submenu toggle (accordion-like)
        document.querySelectorAll('.nav-item.has-submenu > .nav-link').forEach(function (trigger) {
            trigger.addEventListener('click', function (e) {
                e.preventDefault();
                const parent = this.closest('.nav-item');
                const isOpen = parent.classList.contains('open');
                // close others (optional)
                document.querySelectorAll('.nav-item.has-submenu.open').forEach(function (openItem) {
                    if (openItem !== parent) openItem.classList.remove('open');
                    openItem.querySelector('.nav-link').setAttribute('aria-expanded', 'false');
                });
                parent.classList.toggle('open');
                this.setAttribute('aria-expanded', String(!isOpen));
            });
        });

        // Initialize responsive behavior
        if (window.innerWidth <= 768) {
            const sidebar = document.querySelector('.sidebar');
            if (sidebar.classList.contains('collapsed')) {
                createOverlay();
                document.querySelector('.sidebar-overlay').classList.add('show');
            }
        }

        // Auto-open any active submenu (Data, Room Management, etc.)
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.nav-item.has-submenu').forEach(function (item) {
                if (item.classList.contains('active')) {
                    item.classList.add('open');
                    const link = item.querySelector('.nav-link');
                    if (link) link.setAttribute('aria-expanded', 'true');
                }
            });
        });
    </script>
</body>

</html>
