<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="dark" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <title>De Java</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ url('') }}/assets/images/favicon.ico">

    <!-- App css -->
    <link href="{{ url('') }}/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('') }}/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('') }}/assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('') }}/assets/css/custom.css" rel="stylesheet" type="text/css" />

    <style>
        :root {
            --apple-blue: #007AFF;
            --apple-gray: #F5F5F7;
            --glass-bg: rgba(255, 255, 255, 0.85);
            --glass-border: rgba(0, 0, 0, 0.05);
            --sidebar-width: 260px;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif !important;
            background-color: #F2F2F7 !important;
            color: #1d1d1f;
        }

        /* Sidebar Layout - Flexbox for perfect height */
        .startbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-right: 1px solid var(--glass-border);
            width: var(--sidebar-width);
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1000;
            box-shadow: none !important;
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .startbar .brand {
            background: transparent !important;
            border-bottom: 1px solid var(--glass-border);
            padding: 20px 0;
            flex-shrink: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .startbar .brand .logo {
            text-align: center;
            width: 100%;
        }

        .startbar .brand .logo-lg h4 {
            color: #1d1d1f !important;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .startbar .brand .logo-sm {
            color: var(--apple-blue) !important;
            background: rgba(0, 122, 255, 0.1);
            padding: 5px 8px;
            border-radius: 8px;
        }

        .startbar .brand .logo-lg small {
            color: #86868b !important;
        }

        .startbar-menu {
            padding: 0 !important;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .startbar-menu .menu-label {
            color: #6e6e73;
            /* Darker for readability */
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 24px 0 12px 12px;
        }

        .startbar-menu .nav-item .nav-link {
            color: #1d1d1f !important;
            /* Apple Black */
            font-weight: 500;
            font-size: 14px;
            padding: 10px 12px;
            border-radius: 10px;
            transition: background-color 0.2s, color 0.2s;
            /* Performance optimization */
            margin-bottom: 4px;
            display: flex;
            align-items: center;
        }

        .startbar-menu .nav-item .nav-link i {
            font-size: 18px;
            margin-right: 12px;
            color: #86868b;
            transition: color 0.2s;
        }

        .startbar-menu .nav-item .nav-link:not(.active):not([aria-expanded="true"]):hover {
            background-color: rgba(0, 0, 0, 0.05);
            color: #000 !important;
        }

        .startbar-menu .nav-item .nav-link:not(.active):not([aria-expanded="true"]):hover i {
            color: #86868b;
        }

        /* Active State - Only for child/leaf items */
        .startbar-menu .nav-item .nav-link.active:not([data-bs-toggle="collapse"]) {
            background-color: var(--apple-blue) !important;
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(0, 122, 255, 0.3);
        }

        .startbar-menu .nav-item .nav-link.active:not([data-bs-toggle="collapse"]) i,
        .startbar-menu .nav-item .nav-link.active:not([data-bs-toggle="collapse"]) span {
            color: #fff !important;
        }

        /* Keep parent normal, but maybe just expanded style if needed (bootstrap handles this usually as collapsed class removal) */

        /* Topbar Redesign */
        .topbar {
            background: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
            box-shadow: none !important;
            position: fixed;
            /* Fixed as requested */
            top: 0;
            right: 0;
            width: calc(100% - var(--sidebar-width));
            /* Adjust for fixed sidebar */
            z-index: 999;
            transition: width 0.3s ease;
        }

        /* Main Content Adjustment */
        .page-content {
            margin-left: var(--sidebar-width) !important;
            /* Override default margin */
            padding-top: 80px;
            /* Space for fixed topbar */
            transition: margin-left 0.3s ease;
        }

        /* Hide Toggle Button on Desktop to prevent auto-close confusion */
        @media (min-width: 992px) {
            #togglemenu {
                display: none !important;
            }
        }

        /* Card Restyling */
        .card {
            background: #fff;
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            margin-bottom: 24px;
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 16px 20px;
        }

        .card-title {
            font-weight: 600;
            color: #1d1d1f;
            font-size: 16px;
            letter-spacing: -0.3px;
        }

        /* Button Styling */
        .btn-primary {
            background-color: var(--apple-blue);
            border-color: var(--apple-blue);
            border-radius: 8px;
            font-weight: 500;
            padding: 8px 16px;
            box-shadow: 0 2px 5px rgba(0, 122, 255, 0.2);
        }

        .btn-primary:hover {
            background-color: #0062cc;
            border-color: #0062cc;
        }

        /* Table Styling */
        .table thead th {
            font-weight: 600;
            color: #86868b;
            font-size: 13px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        /* Collapse Menu Fix & Styling */
        .collapse .nav-link {
            padding-left: 44px !important;
            font-size: 12.5px !important;
            padding-top: 6px !important;
            padding-bottom: 6px !important;
            color: #6e6e73 !important;
            position: relative;
        }

        .collapse .nav-link::before {
            content: '';
            position: absolute;
            left: 26px;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background-color: #d2d2d7;
            transition: all 0.2s;
        }

        .collapse .nav-link:hover {
            color: #1d1d1f !important;
            background-color: rgba(0, 0, 0, 0.03) !important;
        }

        .collapse .nav-link:hover::before {
            background-color: #86868b;
            transform: translateY(-50%) scale(1.2);
        }

        .collapse .nav-link.active {
            background-color: var(--apple-blue) !important;
            color: #fff !important;
            box-shadow: none !important;
            font-weight: 600;
        }

        .collapse .nav-link.active::before {
            background-color: #fff;
            transform: translateY(-50%) scale(1.5);
        }

        /* Mobile adjustments */
        @media (max-width: 991.98px) {
            .startbar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .startbar.show {
                transform: translateX(0);
            }

            .page-content,
            .topbar {
                margin-left: 0 !important;
            }
        }

        /* Dropdown Optimization */
        .dropdown-menu {
            background: rgba(255, 255, 255, 0.95) !important;
            /* Less transparency for performance */
            backdrop-filter: blur(10px);
            /* Reduced blur radius */
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 0, 0, 0.08) !important;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12) !important;
            border-radius: 12px !important;
            padding: 6px !important;
            animation: fadeInDropdown 0.15s ease-out;
            /* Faster animation */
            will-change: opacity, transform;
            /* Hardware acceleration hint */
        }

        @keyframes fadeInDropdown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown-item {
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 500;
            color: #1d1d1f;
            transition: all 0.2s;
        }

        .dropdown-item:hover {
            background: rgba(0, 0, 0, 0.05);
            color: #000;
        }

        .dropdown-item.text-danger:hover {
            background: rgba(255, 59, 48, 0.1);
            color: #FF3B30 !important;
        }

        /* Header Profile adjustments */
        .dropdown-menu .bg-secondary-subtle {
            background: transparent !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            margin-bottom: 5px;
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.3);
        }

        /* Avatar Styling */
        .user-avatar {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #007AFF 0%, #0051a8 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 2px 5px rgba(0, 122, 255, 0.3);
        }

        /* Mobile Overlay */
        .startbar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            /* Below sidebar (1000) */
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .startbar-overlay.show {
            opacity: 1;
            visibility: visible;
        }
    </style>

    @stack('css')

    @vite(['resources/js/app.js'])
</head>

<body>

    <!-- Top Bar Start -->
    @unless (Request::is('select-your-outlet'))
        <div class="topbar d-print-none">
            <div class="container-fluid">
                <nav class="topbar-custom d-flex justify-content-between" id="topbar-custom">


                    <ul class="topbar-item list-unstyled d-inline-flex align-items-center mb-0">
                        <li>
                            <button class="nav-link mobile-menu-btn nav-icon" id="togglemenu">
                                <i class="iconoir-menu"></i>
                            </button>
                        </li>
                        <li class="hide-phone app-search">
                            <form role="search" action="#" method="get">
                                <input type="search" name="search" class="form-control top-search mb-0"
                                    placeholder="Search here...">
                                <button type="submit"><i class="iconoir-search"></i></button>
                            </form>
                        </li>
                    </ul>
                    <ul class="topbar-item list-unstyled d-inline-flex align-items-center mb-0">



                        @include('Layout._notification')

                        @include('Layout._profile')
                    </ul>
                </nav>
            </div>
        </div>
    @endunless
    @unless (Request::is('select-your-outlet'))
        <div class="startbar d-print-none">
            <div class="brand text-center py-3">
                <a href="{{ route('dashboard') }}" class="logo text-decoration-none d-block">

                    <span class="logo-sm fw-bold text-white">
                        DJ
                    </span>

                    <span class="logo-lg d-block">
                        <h4 class="mb-0 fw-bold text-white">Dejava</h4>
                        <small class="text-muted">
                            @php
                                $officeId = session('active_office_id');
                                $office = App\Models\Office::find($officeId);
                            @endphp
                            {{ $office->name }}
                        </small>
                    </span>

                </a>
            </div>
            @include('Layout._sidebar')
        </div>
        <div class="startbar-overlay d-print-none"></div>
    @endunless
    @yield('main')

    @include('Layout._alert_helper')

    <script src="{{ url('') }}/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{ url('') }}/assets/libs/simplebar/simplebar.min.js"></script>
    @stack('js')
    <!-- App js -->
    <script src="{{ url('') }}/assets/js/app.js"></script>

    <!-- Force Sidebar Open Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function enforceSidebar() {
                if (window.innerWidth >= 992) {
                    if (document.body.getAttribute('data-sidebar-size') === 'collapsed') {
                        document.body.setAttribute('data-sidebar-size', 'default');
                    }
                    document.body.classList.remove('vertical-collapsed');
                }
            }

            // Initial check
            enforceSidebar();

            // Check on resize
            window.addEventListener('resize', enforceSidebar);

            // Observe changes to body attributes to fight app.js auto-collapse
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.attributeName === 'data-sidebar-size') {
                        enforceSidebar();
                    }
                });
            });

            observer.observe(document.body, {
                attributes: true
            });

            // Mobile Sidebar Toggle Logic
            const toggleBtn = document.getElementById('togglemenu');
            const startbar = document.querySelector('.startbar');
            const overlay = document.querySelector('.startbar-overlay');

            if (toggleBtn && startbar && overlay) {
                toggleBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    startbar.classList.toggle('show');
                    overlay.classList.toggle('show');
                });

                overlay.addEventListener('click', function () {
                    startbar.classList.remove('show');
                    overlay.classList.remove('show');
                });
            }
        });
    </script>
</body>


</html>