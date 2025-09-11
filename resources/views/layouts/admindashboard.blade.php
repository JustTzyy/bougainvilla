<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Purple Admin</title>
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
                    <div class="logo-icon"></div>
                    <span class="logo-text">Purple</span>
                </div>
            </div>
            
            <div class="user-profile">
                <div class="user-avatar">
                <i class="fas fa-user-circle" style="font-size:40px; color:#8a5cf6;"></i>
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
    
                    <li class="nav-item has-submenu {{ request()->routeIs('adminPages.adminrecords') || request()->routeIs('adminPages.frontdeskrecords') || request()->routeIs('adminPages.archiveadminrecords') || request()->routeIs('adminPages.archivefrontdeskrecords') ? 'active' : '' }}">
                        <a href="#" class="nav-link" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-user-lock"></i>
                            <span>Data</span>
                            <i class="fas fa-chevron-right submenu-arrow"></i>
                        </a>
                        <ul class="submenu" aria-label="Data submenu">
                            <li class="{{ request()->routeIs('adminPages.adminrecords') || request()->routeIs('adminPages.archiveadminrecords') ? 'active' : '' }}">
                                <a href="{{ route('adminPages.adminrecords') }}" class="submenu-link">
                                    <span>Admin</span>
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('adminPages.frontdeskrecords') || request()->routeIs('adminPages.archivefrontdeskrecords') ? 'active' : '' }}">
                                <a href="{{ route('adminPages.frontdeskrecords') }}" class="submenu-link">
                                    <span>FrontDesk</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <span>Widgets</span>
                        </a>
                    </li>
                    <li class="nav-item has-submenu">
                        <a href="#" class="nav-link">
                            <i class="fas fa-eye"></i>
                            <span>Basic UI Elements</span>
                            <i class="fas fa-chevron-right submenu-arrow"></i>
                        </a>
                    </li>
                    <li class="nav-item has-submenu">
                        <a href="#" class="nav-link">
                            <i class="fas fa-layer-group"></i>
                            <span>Advanced UI</span>
                            <i class="fas fa-chevron-right submenu-arrow"></i>
                        </a>
                    </li>
                    <li class="nav-item has-submenu">
                        <a href="#" class="nav-link">
                            <i class="fas fa-clipboard-list"></i>
                            <span>Form Elements</span>
                            <i class="fas fa-chevron-right submenu-arrow"></i>
                        </a>
                    </li>
                    <li class="nav-item has-submenu">
                        <a href="#" class="nav-link">
                            <i class="fas fa-th"></i>
                            <span>Tables</span>
                            <i class="fas fa-chevron-right submenu-arrow"></i>
                        </a>
                    </li>
                  
                 
                    <li class="nav-item has-submenu">
                        <a href="#" class="nav-link">
                            <i class="fas fa-th-large"></i>
                            <span>Icons</span>
                            <i class="fas fa-chevron-right submenu-arrow"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-edit"></i>
                            <span>Text Editor</span>
                        </a>
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
<i class="fas fa-user-circle" style="font-size:40px; color:#8a5cf6;"></i>
                        <span class="user-name-small">{{ Auth::user()->name ?? 'User' }}</span>
                        <i class="fas fa-chevron-down"></i>
                        
                        <!-- User Dropdown Menu -->
                        <div class="user-dropdown-menu" id="userDropdownMenu">
                            <a href="{{ route('adminPages.settings') }}" class="dropdown-item">
                                <i class="fas fa-cog dropdown-icon"></i>
                                <span>Settings</span>
                            </a>
                            <div class="dropdown-item">
                                <i class="fas fa-history dropdown-icon activity"></i>
                                <span>Activity Log</span>
                            </div>
                            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                                @csrf
                                <button type="submit" class="dropdown-item" style="background: none; border: none; width: 100%; text-align: left; cursor: pointer;">
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
        document.querySelector('.menu-toggle').addEventListener('click', function() {
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
            overlay.addEventListener('click', function() {
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
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdownMenu');
            const userDropdown = document.querySelector('.user-dropdown');
            
            if (!userDropdown.contains(event.target)) {
                dropdown.classList.remove('show');
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
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
        document.querySelectorAll('.nav-item.has-submenu > .nav-link').forEach(function(trigger){
            trigger.addEventListener('click', function(e){
                e.preventDefault();
                const parent = this.closest('.nav-item');
                const isOpen = parent.classList.contains('open');
                // close others (optional)
                document.querySelectorAll('.nav-item.has-submenu.open').forEach(function(openItem){
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

        // Auto-open Data submenu when on admin or frontdesk pages
        document.addEventListener('DOMContentLoaded', function() {
            const dataSubmenu = document.querySelector('.nav-item.has-submenu');
            if (dataSubmenu && dataSubmenu.classList.contains('active')) {
                dataSubmenu.classList.add('open');
                dataSubmenu.querySelector('.nav-link').setAttribute('aria-expanded', 'true');
            }
        });
    </script>
</body>
</html>
