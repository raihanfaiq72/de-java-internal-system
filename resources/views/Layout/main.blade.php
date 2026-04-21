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
    @yield('styles')

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

        body.sidebar-desktop-collapsed .startbar {
            transform: translateX(-100%);
        }

        body.sidebar-desktop-collapsed .topbar {
            width: 100%;
        }

        body.sidebar-desktop-collapsed .page-content {
            margin-left: 0 !important;
        }

        /* Mobile Hamburger - Better touch target */
        .mobile-menu-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border: none;
            border-radius: 10px;
            background: transparent;
            color: #1d1d1f;
            cursor: pointer;
            padding: 0;
        }

        .mobile-menu-btn:active,
        .mobile-menu-btn:focus {
            outline: none;
            background: rgba(0, 0, 0, 0.06);
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

            .topbar {
                width: 100%;
            }
        }

        /* Extra Mobile Friendly tweaks */
        @media (max-width: 575.98px) {
            .page-title-box {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 8px;
            }

            .page-title-box .breadcrumb {
                font-size: 12px;
            }

            .card-body {
                padding: 12px !important;
            }

            .card-header {
                padding: 12px !important;
            }

            .nav,
            .nav-tabs,
            .nav-pills {
                flex-wrap: nowrap;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .nav .nav-item {
                white-space: nowrap;
            }

            .table {
                font-size: 12px;
            }

            .input-group>.form-control,
            .form-select,
            .btn {
                font-size: 0.9rem;
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

        .page-loader {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.92);
            z-index: 2000;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity 0.18s ease, visibility 0s linear 0.18s;
        }

        .page-loader.active {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
            transition: opacity 0.18s ease;
        }

        .page-loader-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            padding: 18px 22px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.85);
            border: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 14px 34px rgba(16, 24, 40, 0.10);
        }

        .page-loader-spinner {
            width: 46px;
            height: 46px;
            border-radius: 999px;
            border: 4px solid #e9ecef;
            border-top-color: #0d6efd;
            animation: pageLoaderSpin 0.75s linear infinite;
        }

        .page-loader-text {
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.02em;
            color: #6c757d;
        }

        @keyframes pageLoaderSpin {
            to {
                transform: rotate(360deg);
            }
        }

        .page-content {
            transition: opacity 0.18s ease;
        }

        body.page-transition-start .page-content {
            opacity: 0.6;
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

        /* Prevent background scroll when sidebar open */
        body.sidebar-open {
            overflow: hidden;
            touch-action: none;
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
                            <button class="nav-link mobile-menu-btn nav-icon" id="togglemenu" type="button"
                                aria-label="Toggle sidebar" aria-expanded="true">
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
    <div class="page-loader d-print-none active" id="pageLoader" aria-live="polite" aria-busy="true">
        <div class="page-loader-card">
            <div class="page-loader-spinner" role="status" aria-label="Memuat"></div>
            <div class="page-loader-text">Memuat...</div>
        </div>
    </div>
    <script>
        (function() {
            const el = document.getElementById('pageLoader');
            if (!el) return;
            let shownAt = performance.now();
            let pending = 0;
            const minMs = 240;
            const setActive = (on) => {
                if (on) el.classList.add('active');
                else el.classList.remove('active');
            };
            const show = () => {
                shownAt = performance.now();
                setActive(true);
            };
            const hide = () => {
                if (pending > 0) return;
                const dt = performance.now() - shownAt;
                const wait = dt < minMs ? (minMs - dt) : 0;
                window.setTimeout(() => setActive(false), wait);
            };
            const track = (p) => {
                pending += 1;
                show();
                return Promise.resolve(p).finally(() => {
                    pending = Math.max(0, pending - 1);
                    hide();
                });
            };
            window.PageLoader = {
                show,
                hide,
                track,
                pending: () => pending
            };
            document.addEventListener('DOMContentLoaded', function() {
                hide();
            }, {
                once: true
            });
            window.addEventListener('pageshow', function() {
                hide();
            });
            document.addEventListener('submit', function(ev) {
                const f = ev.target;
                if (!f || !(f instanceof HTMLFormElement)) return;
                if (f.hasAttribute('data-no-loader')) return;
                show();
            }, true);
            document.addEventListener('click', function(ev) {
                if (ev.defaultPrevented) return;
                if (ev.button !== 0) return;
                if (ev.metaKey || ev.ctrlKey || ev.shiftKey || ev.altKey) return;
                const a = ev.target.closest('a');
                if (!a) return;
                if (a.hasAttribute('data-no-loader')) return;
                if (a.hasAttribute('download')) return;
                const target = (a.getAttribute('target') || '').toLowerCase();
                if (target === '_blank') return;
                const href = a.getAttribute('href') || '';
                if (!href || href.startsWith('#')) return;
                const lower = href.toLowerCase();
                if (lower.startsWith('javascript:') || lower.startsWith('mailto:') || lower.startsWith('tel:'))
                    return;
                show();
            }, true);
        })();
    </script>
    @unless (Request::is('select-your-outlet'))
        <div class="startbar d-print-none">
            <div class="brand text-center py-3">
                <a href="{{ route('dashboard') }}" class="logo text-decoration-none d-block">

                    <span class="logo-sm fw-bold text-white">
                        DJ
                    </span>

                    <span class="logo-lg d-block">
                        <h4 class="mb-0 fw-bold text-white">Djava</h4>
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

    <script>
        window.__jq_loaded = !!window.jQuery;
    </script>
    <script src="/assets/vendor/jquery/jquery-3.7.1.min.js" onerror="window.__jq_loaded=false"></script>
    <script>
        if (!window.__jq_loaded) {
            var s = document.createElement('script');
            s.src = 'https://code.jquery.com/jquery-3.7.1.min.js';
            document.head.appendChild(s);
        }
    </script>
    <script src="{{ url('') }}/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{ url('') }}/assets/libs/simplebar/simplebar.min.js"></script>
    @stack('js')
    <!-- App js -->
    <script src="{{ url('') }}/assets/js/app.js"></script>
    @yield('scripts')

    <!-- Sidebar Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const BODY = document.body;
            const DESKTOP_BREAKPOINT = 992;
            const STORAGE_KEY = 'sidebar-desktop-collapsed';
            const toggleBtn = document.getElementById('togglemenu');
            const startbar = document.querySelector('.startbar');
            const overlay = document.querySelector('.startbar-overlay');
            const pageLoader = document.getElementById('pageLoader');

            function isDesktop() {
                return window.innerWidth >= DESKTOP_BREAKPOINT;
            }

            function syncSidebarState() {
                if (!toggleBtn || !startbar) return;

                if (isDesktop()) {
                    const isCollapsed = BODY.classList.contains('sidebar-desktop-collapsed');
                    BODY.classList.remove('sidebar-open');
                    startbar.classList.remove('show');
                    overlay?.classList.remove('show');
                    startbar.setAttribute('aria-hidden', isCollapsed ? 'true' : 'false');
                    toggleBtn.setAttribute('aria-expanded', isCollapsed ? 'false' : 'true');
                    BODY.setAttribute('data-sidebar-size', 'default');
                    BODY.classList.remove('vertical-collapsed');
                    return;
                }

                const isOpen = startbar.classList.contains('show');
                startbar.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
                toggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            }

            function openMobileSidebar() {
                if (!startbar) return;
                startbar.classList.add('show');
                overlay?.classList.add('show');
                BODY.classList.add('sidebar-open');
                syncSidebarState();
            }

            function closeMobileSidebar() {
                if (!startbar) return;
                startbar.classList.remove('show');
                overlay?.classList.remove('show');
                BODY.classList.remove('sidebar-open');
                syncSidebarState();
            }

            function toggleDesktopSidebar() {
                const willCollapse = !BODY.classList.contains('sidebar-desktop-collapsed');
                BODY.classList.toggle('sidebar-desktop-collapsed', willCollapse);
                BODY.setAttribute('data-sidebar-size', 'default');
                BODY.classList.remove('vertical-collapsed');
                try {
                    window.localStorage.setItem(STORAGE_KEY, willCollapse ? '1' : '0');
                } catch (e) {}
                syncSidebarState();
            }

            function applyDesktopPreference() {
                if (!isDesktop()) {
                    BODY.classList.remove('sidebar-desktop-collapsed');
                    syncSidebarState();
                    return;
                }

                let shouldCollapse = false;
                try {
                    shouldCollapse = window.localStorage.getItem(STORAGE_KEY) === '1';
                } catch (e) {}
                BODY.classList.toggle('sidebar-desktop-collapsed', shouldCollapse);
                syncSidebarState();
            }

            applyDesktopPreference();

            window.addEventListener('resize', function() {
                applyDesktopPreference();
                if (isDesktop()) {
                    closeMobileSidebar();
                }
            });

            if (toggleBtn && startbar && overlay) {
                toggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    if (isDesktop()) {
                        toggleDesktopSidebar();
                        return;
                    }

                    if (startbar.classList.contains('show')) {
                        closeMobileSidebar();
                    } else {
                        openMobileSidebar();
                    }
                });

                overlay.addEventListener('click', function() {
                    closeMobileSidebar();
                });

                startbar.addEventListener('click', function(ev) {
                    const link = ev.target.closest('.nav-link');
                    if (!link) return;
                    const isCollapseToggle = link.getAttribute('data-bs-toggle') === 'collapse';
                    const href = link.getAttribute('href') || '';
                    const isHash = href.startsWith('#');
                    if (window.innerWidth < 992) {
                        if (isCollapseToggle || isHash) {
                            ev.stopPropagation();
                            return;
                        }
                        if (pageLoader) {
                            pageLoader.classList.add('active');
                        }
                        BODY.classList.add('page-transition-start');
                        closeMobileSidebar();
                    }
                });

                // ESC to close
                document.addEventListener('keydown', function(ev) {
                    if (ev.key === 'Escape' && startbar.classList.contains('show')) {
                        closeMobileSidebar();
                    }
                });
            }
            window.addEventListener('load', function() {
                BODY.classList.remove('page-transition-start');
                pageLoader?.classList.remove('active');
            });
        });
    </script>
</body>


</html>
