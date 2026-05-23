<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'ระบบขนย้ายมูลไก่') - Farm Manure Transfer</title>

    <!-- Google Fonts: Kanit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Custom Premium Forest Theme Styles -->
    <style>
        :root {
            --primary-color: #1e4620; /* Deep Forest Green */
            --primary-accent: #2e7d32; /* Rich Grass Green */
            --primary-light: #e8f5e9; /* Light Mint Green */
            --secondary-color: #f8f9fa; /* Warm white */
            --text-dark: #212529;
            --text-muted: #6c757d;
            --border-radius-lg: 16px;
            --border-radius-md: 10px;
            --transition-smooth: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        body {
            font-family: 'Kanit', sans-serif;
            background-color: #f4f7f5;
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 280px;
            background: linear-gradient(135deg, #123014 0%, #1e4620 100%);
            color: #ffffff;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: var(--transition-smooth);
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
            padding: 1.5rem 1rem;
        }

        .sidebar .brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            padding-bottom: 1.5rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }

        .sidebar .brand img {
            width: 100%;
            max-width: 180px;
            height: auto;
            display: block;
            border-radius: 12px;
            background-color: rgba(255,255,255,0.96);
            padding: 8px 10px;
        }

        .sidebar .brand span {
            font-size: 0.95rem;
            font-weight: 600;
            line-height: 1.35;
            color: rgba(255,255,255,0.92);
        }

        .navbar-brand-logo {
            height: 42px;
            width: auto;
            display: block;
            background-color: #ffffff;
            border-radius: 10px;
            padding: 4px 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .sidebar-menu-item a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            border-radius: var(--border-radius-md);
            font-weight: 400;
            transition: var(--transition-smooth);
        }

        .sidebar-menu-item a:hover {
            background-color: rgba(255,255,255,0.1);
            color: #ffffff;
            transform: translateX(4px);
        }

        .sidebar-menu-item.active a {
            background-color: var(--primary-accent);
            color: #ffffff;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(46, 125, 50, 0.4);
        }

        .sidebar-menu-header {
            color: rgba(255,255,255,0.4);
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 600;
            padding: 1rem 1rem 0.5rem;
            letter-spacing: 1px;
        }

        /* Main Content wrapper */
        .main-wrapper {
            margin-left: 280px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: var(--transition-smooth);
        }

        /* Top Navbar */
        .top-navbar {
            background-color: #ffffff;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .top-navbar .toggle-btn {
            display: none;
            font-size: 1.5rem;
            background: none;
            border: none;
            color: var(--primary-color);
            cursor: pointer;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background-color: var(--primary-light);
            color: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .content-body {
            padding: 2rem;
            flex-grow: 1;
        }

        /* Premium CSS components */
        .card {
            border: none;
            border-radius: var(--border-radius-lg);
            box-shadow: 0 8px 24px rgba(149, 157, 165, 0.08);
            background-color: #ffffff;
            transition: var(--transition-smooth);
        }

        .card:hover {
            box-shadow: 0 12px 30px rgba(149, 157, 165, 0.12);
        }

        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            color: var(--primary-color);
            border-top-left-radius: var(--border-radius-lg) !important;
            border-top-right-radius: var(--border-radius-lg) !important;
        }

        .btn-primary {
            background-color: var(--primary-accent);
            border-color: var(--primary-accent);
            border-radius: var(--border-radius-md);
            padding: 10px 20px;
            font-weight: 500;
            transition: var(--transition-smooth);
        }

        .btn-primary:hover, .btn-primary:focus {
            background-color: #256328;
            border-color: #256328;
            box-shadow: 0 4px 12px rgba(46, 125, 50, 0.25);
        }

        .btn-outline-primary {
            color: var(--primary-accent);
            border-color: var(--primary-accent);
            border-radius: var(--border-radius-md);
            padding: 10px 20px;
            font-weight: 500;
            transition: var(--transition-smooth);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-accent);
            border-color: var(--primary-accent);
            color: #ffffff;
        }

        .btn-success {
            background-color: #00897b;
            border-color: #00897b;
            border-radius: var(--border-radius-md);
        }

        .badge-pending {
            background-color: #fff3e0;
            color: #ef6c00;
            font-weight: 500;
            padding: 0.5em 0.8em;
            border-radius: 8px;
        }

        .badge-received {
            background-color: #e8f5e9;
            color: #2e7d32;
            font-weight: 500;
            padding: 0.5em 0.8em;
            border-radius: 8px;
        }

        .badge-cancelled {
            background-color: #ffebee;
            color: #c62828;
            font-weight: 500;
            padding: 0.5em 0.8em;
            border-radius: 8px;
        }

        /* Large touch friendly forms */
        .form-control, .form-select {
            border-radius: var(--border-radius-md);
            padding: 12px 16px;
            border: 1px solid #ced4da;
            transition: var(--transition-smooth);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-accent);
            box-shadow: 0 0 0 0.25rem rgba(46, 125, 50, 0.15);
        }

        /* Footer */
        .footer {
            background-color: #ffffff;
            border-top: 1px solid rgba(0,0,0,0.05);
            padding: 1.5rem;
            text-align: center;
            font-size: 0.875rem;
            color: var(--text-muted);
            margin-top: auto;
        }

        /* Print styles */
        @media print {
            .sidebar, .top-navbar, .toggle-btn, .btn, .footer {
                display: none !important;
            }
            .main-wrapper {
                margin-left: 0 !important;
            }
            body {
                background-color: #ffffff !important;
            }
            .card {
                box-shadow: none !important;
                border: none !important;
            }
        }

        /* Responsive Breakpoints */
        @media (max-width: 991.98px) {
            .sidebar {
                left: -280px;
            }
            .sidebar.show {
                left: 0;
            }
            .main-wrapper {
                margin-left: 0;
            }
            .top-navbar .toggle-btn {
                display: block;
            }
            .content-body {
                padding: 1rem;
            }
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Sidebar Menu -->
    <div class="sidebar" id="sidebar">
        <div class="brand">
            <img src="{{ asset('images/cfarm-logo.png') }}" alt="CFARM Logo">
            <span>ระบบขนย้ายมูลไก่ออกจากฟาร์มและรับเข้ากอง</span>
        </div>
        
        <ul class="sidebar-menu">
            <li class="sidebar-menu-item {{ Request::is('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard (แผงควบคุม)</span>
                </a>
            </li>

            <!-- บันทึกรายการ -->
            <li class="sidebar-menu-header">ฟังก์ชันบันทึกงาน</li>
            
            @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
            <li class="sidebar-menu-item {{ Request::is('transfers/out*') ? 'active' : '' }}">
                <a href="{{ route('transfers.out') }}">
                    <i class="bi bi-box-arrow-up-right"></i>
                    <span>1. บันทึกขาออกจากฟาร์ม</span>
                </a>
            </li>
            <li class="sidebar-menu-item {{ Request::is('transfers/in*') ? 'active' : '' }}">
                <a href="{{ route('transfers.in') }}">
                    <i class="bi bi-box-arrow-in-down-left"></i>
                    <span>2. ตรวจรับเข้ากองปลายทาง</span>
                </a>
            </li>
            @endif

            <!-- สรุปรายงาน -->
            <li class="sidebar-menu-header">รายงาน</li>
            <li class="sidebar-menu-item {{ Request::is('reports*') ? 'active' : '' }}">
                <a href="{{ route('reports.index') }}">
                    <i class="bi bi-file-earmark-bar-graph"></i>
                    <span>รายงานประวัติและการค้นหา</span>
                </a>
            </li>

            <!-- ข้อมูลพื้นฐาน CRUD (แอดมินเท่านั้น) -->
            @if(auth()->user()->isAdmin())
            <li class="sidebar-menu-header">ข้อมูลระบบพื้นฐาน</li>
            
            <li class="sidebar-menu-item {{ Request::is('farms*') ? 'active' : '' }}">
                <a href="{{ route('farms.index') }}">
                    <i class="bi bi-house-gear"></i>
                    <span>จัดการข้อมูลฟาร์ม</span>
                </a>
            </li>
            <li class="sidebar-menu-item {{ Request::is('piles*') ? 'active' : '' }}">
                <a href="{{ route('piles.index') }}">
                    <i class="bi bi-layers-half"></i>
                    <span>จัดการกองมูลไก่</span>
                </a>
            </li>
            <li class="sidebar-menu-item {{ Request::is('users*') ? 'active' : '' }}">
                <a href="{{ route('users.index') }}">
                    <i class="bi bi-people"></i>
                    <span>จัดการบัญชีผู้ใช้</span>
                </a>
            </li>
            @endif

            <!-- Logout -->
            <li class="sidebar-menu-header">บัญชีผู้ใช้</li>
            <li class="sidebar-menu-item">
                <form method="POST" action="{{ route('logout') }}" id="logout-form" class="d-none">
                    @csrf
                </form>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-danger">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>ออกจากระบบ</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content Wrapper -->
    <div class="main-wrapper">
        <!-- Top Navbar -->
        <header class="top-navbar">
            <button class="toggle-btn" id="toggle-sidebar">
                <i class="bi bi-list"></i>
            </button>
            <div class="navbar-title d-none d-md-flex align-items-center gap-3">
                <img src="{{ asset('images/cfarm-logo.png') }}" alt="CFARM Logo" class="navbar-brand-logo">
                <h5 class="mb-0 fw-semibold text-success">@yield('page_title', 'แผงควบคุมระบบ')</h5>
            </div>
            
            <div class="user-profile">
                <div class="text-end d-none d-sm-block">
                    <div class="fw-semibold lh-1">{{ auth()->user()->name }}</div>
                    <small class="text-muted" style="font-size: 0.75rem;">
                        @if(auth()->user()->isAdmin())
                            ผู้ดูแลระบบ (Admin)
                        @elseif(auth()->user()->isStaff())
                            เจ้าหน้าที่ปฏิบัติงาน (Staff)
                        @else
                            ผู้บริหาร / ผู้ดูรายงาน (Viewer)
                        @endif
                    </small>
                </div>
                <div class="user-avatar">
                    {{ mb_substr(auth()->user()->name, 0, 1) }}
                </div>
            </div>
        </header>

        <!-- Content Body -->
        <main class="content-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: var(--border-radius-md);">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: var(--border-radius-md);">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="footer">
            <div class="container-fluid">
                <span>&copy; {{ date('Y') }} ระบบบันทึกการขนย้ายมูลไก่ออกจากฟาร์มและรับเข้ากอง. พัฒนาโดย IT CFARM (MAX)</span>
            </div>
        </footer>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Sidebar Toggle JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('toggle-sidebar');
            const sidebar = document.getElementById('sidebar');
            
            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    sidebar.classList.toggle('show');
                });
                
                document.body.addEventListener('click', function(e) {
                    if (sidebar.classList.contains('show') && !sidebar.contains(e.target) && e.target !== toggleBtn) {
                        sidebar.classList.remove('show');
                    }
                });
            }
        });
    </script>
    
    @yield('scripts')
</body>
</html>
