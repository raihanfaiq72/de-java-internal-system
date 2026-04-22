@extends('Layout.main')

@include('Layout._dejava_promo')
@include('Layout._tour_guide')

@push('css')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        :root {
            --dr-bg: #ffffff;
            --dr-panel: #ffffff;
            --dr-panel-soft: #fbfaf8;
            --dr-line: #e7e2dc;
            --dr-line-strong: #d8d0c7;
            --dr-text: #181818;
            --dr-muted: #8f8a83;
            --dr-muted-2: #a9a39c;
            --dr-green: #0b9d68;
            --dr-red: #ea544b;
            --dr-amber: #d7a238;
            --dr-shadow: 0 22px 50px rgba(34, 28, 21, 0.08);
        }

        body:has(.dashboard-reference-page) {
            background: #ffffff !important;
            font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif !important;
            color: var(--dr-text);
        }

        .dashboard-reference-page {
            min-height: calc(100vh - 80px);
        }

        .dashboard-reference-shell {
            background: #ffffff;
            border: 0;
            border-radius: 0;
            min-height: calc(100vh - 80px);
            box-shadow: none;
            padding: 12px 24px 24px;
        }

        .dashboard-reference-shell .container-fluid {
            padding: 0;
        }

        .dr-breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--dr-muted);
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 20px;
        }

        .dr-breadcrumb strong {
            color: var(--dr-text);
            font-weight: 700;
        }

        .dr-breadcrumb-icon {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            border: 1px solid var(--dr-line);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--dr-text);
            font-size: 13px;
        }

        .dr-hero {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 32px;
        }

        .dr-title {
            margin: 0 0 10px;
            font-size: 24px;
            font-weight: 700;
            line-height: 1.2;
            color: var(--dr-text);
        }

        .dr-subtitle {
            margin: 0;
            font-size: 15px;
            color: #4f4b47;
            font-weight: 500;
        }

        .dr-actions {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        .dr-btn,
        .dr-select {
            height: 42px;
            border-radius: 12px;
            border: 1px solid var(--dr-line-strong);
            background: #fff;
            color: var(--dr-text);
            padding: 0 16px;
            font-size: 15px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: none;
        }

        .dr-btn--icon {
            width: 42px;
            justify-content: center;
            padding: 0;
            background: #1f1f1f;
            color: #fff;
            border-color: #1f1f1f;
        }

        .dr-card {
            background: var(--dr-panel);
            border: 1px solid var(--dr-line-strong);
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(33, 29, 25, 0.04);
        }

        .dr-stat-card {
            padding: 14px 18px 12px;
            min-height: auto;
            display: flex;
            flex-direction: column;
        }

        .dr-stat-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding-bottom: 12px;
            border-bottom: 1px dashed var(--dr-line);
        }

        .dr-stat-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            border: 1px solid var(--dr-line);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--dr-panel-soft);
            color: var(--dr-text);
            font-size: 16px;
            flex: 0 0 auto;
            order: 2;
        }

        .dr-stat-title {
            margin: 0;
            font-size: 15px;
            font-weight: 600;
            color: var(--dr-text);
            order: 1;
        }

        .dr-stat-value {
            margin: 12px 0 16px;
            font-size: 32px;
            font-weight: 700;
            line-height: 1;
            color: var(--dr-text);
        }

        .dr-stat-foot {
            margin-top: auto;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 12px;
        }

        .dr-diff {
            font-size: 15px;
            font-weight: 700;
        }

        .dr-diff span {
            color: var(--dr-muted);
            font-weight: 500;
            margin-left: 4px;
        }

        .dr-diff.up {
            color: var(--dr-green);
        }

        .dr-diff.down {
            color: var(--dr-red);
        }

        .dr-sparkline {
            width: 78px;
            height: 34px;
            flex: 0 0 auto;
        }

        .dr-chart-card {
            padding: 14px 18px 16px;
            overflow: hidden;
        }

        .dr-card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 8px;
        }

        .dr-card-title {
            margin: 0;
            font-size: 17px;
            font-weight: 700;
            color: var(--dr-text);
        }

        .dr-card-subtitle {
            margin: 4px 0 0;
            font-size: 14px;
            color: var(--dr-muted);
            font-weight: 500;
        }

        .dr-overview-metric {
            margin: 10px 0 14px;
        }

        .dr-overview-label {
            margin: 0 0 2px;
            color: #4f4b47;
            font-size: 13px;
            font-weight: 500;
        }

        .dr-overview-value {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 16px;
        }

        .dr-overview-value strong {
            font-size: 24px;
            line-height: 1;
            font-weight: 700;
        }

        .dr-pill {
            height: 30px;
            padding: 0 11px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
        }

        .dr-pill.green {
            background: #e7f8ef;
            color: var(--dr-green);
        }

        .dr-bars-card {
            position: relative;
            margin-top: 8px;
            padding-top: 30px;
            min-height: 298px;
        }

        .dr-bars-grid {
            position: absolute;
            inset: 0 0 42px;
            display: grid;
            grid-template-rows: repeat(3, 1fr);
            pointer-events: none;
        }

        .dr-bars-grid span {
            border-top: 1px dashed #ece7e2;
        }

        .dr-bars {
            position: relative;
            z-index: 1;
            height: 270px;
            display: flex;
            align-items: end;
            gap: 12px;
            overflow-x: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--dr-line-strong) transparent;
            padding-bottom: 4px;
            padding-top: 25px; /* Give room for tooltips */
        }

        .dr-bars::-webkit-scrollbar {
            height: 4px;
        }

        .dr-bars::-webkit-scrollbar-thumb {
            background: var(--dr-line-strong);
            border-radius: 10px;
        }

        .dr-bar-group {
            position: relative;
            text-align: center;
            min-width: 50px;
            flex: 1;
        }

        .dr-bar {
            width: 100%;
            max-width: 58px;
            margin: 0 auto;
            border-radius: 14px;
            border: 1px solid #e7e2dc;
            background:
                repeating-linear-gradient(
                    -25deg,
                    #f7f6f4 0,
                    #f7f6f4 8px,
                    #ededeb 8px,
                    #ededeb 10px
                );
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.75);
        }

        .dr-bar.active {
            border-color: #454545;
            background:
                repeating-linear-gradient(
                    -25deg,
                    #a6a6a6 0,
                    #a6a6a6 12px,
                    #8d8d8d 12px,
                    #8d8d8d 14px
                );
        }

        .dr-bar-label {
            margin-top: 10px;
            font-size: 14px;
            color: #4f4b47;
            font-weight: 500;
        }

        .dr-bar-tooltip {
            position: absolute;
            left: 50%;
            bottom: calc(100% + 12px);
            transform: translateX(-50%);
            padding: 6px 10px;
            border-radius: 10px;
            background: #232323;
            color: #fff;
            min-width: 85px;
            text-align: center;
            box-shadow: 0 10px 20px rgba(25, 25, 25, 0.15);
            opacity: 0;
            pointer-events: none;
            transition: all 0.2s ease;
            z-index: 100;
        }

        .dr-bar:hover .dr-bar-tooltip,
        .dr-bar.active .dr-bar-tooltip {
            opacity: 1;
            pointer-events: auto;
        }

        .dr-bar-tooltip::after {
            content: "";
            position: absolute;
            left: 50%;
            bottom: -10px;
            transform: translateX(-50%);
            border-width: 10px 9px 0;
            border-style: solid;
            border-color: #232323 transparent transparent;
        }

        .dr-bar-tooltip small {
            display: block;
            font-size: 10px;
            color: rgba(255, 255, 255, 0.78);
            margin-bottom: 2px;
        }

        .dr-bar-tooltip strong {
            font-size: 12px;
            font-weight: 700;
        }

        .dr-bar-pin {
            position: absolute;
            left: 50%;
            top: -12px;
            transform: translateX(-50%);
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #232323;
            border: 4px solid #fff;
            box-shadow: 0 2px 0 rgba(0, 0, 0, 0.15);
        }

        .dr-table-card,
        .dr-feed-card {
            padding: 14px 18px;
        }

        .dr-table-card .table-responsive {
            height: 285px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--dr-line-strong) transparent;
        }

        .dr-feed-card .dr-feed-list {
            height: 480px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--dr-line-strong) transparent;
        }

        .dr-table-card .table-responsive::-webkit-scrollbar,
        .dr-feed-card .dr-feed-list::-webkit-scrollbar {
            width: 4px;
        }

        .dr-table-card .table-responsive::-webkit-scrollbar-thumb,
        .dr-feed-card .dr-feed-list::-webkit-scrollbar-thumb {
            background-color: var(--dr-line-strong);
            border-radius: 10px;
        }

        .dr-data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 0;
            border: 1px solid var(--dr-line-strong);
            border-top: 0;
            border-left: 0;
            border-right: 0;
            border-radius: 0;
            overflow: hidden;
        }

        .dr-data-table thead th {
            background: #fbfaf8;
            color: var(--dr-muted);
            font-size: 12px;
            font-weight: 700;
            padding: 10px 14px;
            border-bottom: 1px solid var(--dr-line);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .dr-data-table tbody td {
            padding: 12px 14px;
            font-size: 15px;
            font-weight: 500;
            color: #2b2a28;
            border-bottom: 1px solid var(--dr-line);
            vertical-align: middle;
        }

        .dr-data-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .dr-table-code {
            font-weight: 700;
            color: var(--dr-text);
        }

        .dr-check {
            width: 16px;
            height: 16px;
            border-radius: 5px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            border: 1px solid var(--dr-line-strong);
            color: transparent;
            font-size: 10px;
        }

        .dr-check.active {
            background: #1f1f1f;
            border-color: #1f1f1f;
            color: #fff;
        }

        .dr-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #2b2a28;
        }

        .dr-status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: currentColor;
        }

        .dr-status.green {
            color: var(--dr-green);
        }

        .dr-status.amber {
            color: var(--dr-amber);
        }

        .dr-status.red {
            color: var(--dr-red);
        }

        .dr-feed-list {
            margin-top: 8px;
            border-top: 1px dashed var(--dr-line);
        }

        .dr-feed-item {
            display: grid;
            grid-template-columns: 44px 1fr auto;
            gap: 14px;
            padding: 14px 0;
            border-bottom: 1px dashed var(--dr-line);
        }

        .dr-feed-item:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }

        .dr-feed-thumb {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            border: 1px solid var(--dr-line);
            background:
                linear-gradient(140deg, rgba(255, 255, 255, 0.82), rgba(246, 241, 235, 0.9)),
                repeating-linear-gradient(
                    -25deg,
                    rgba(202, 197, 191, 0.26) 0,
                    rgba(202, 197, 191, 0.26) 8px,
                    rgba(202, 197, 191, 0.26) 8px,
                    rgba(236, 232, 226, 0.2) 12px
                );
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 800;
            color: #4d4a46;
        }

        .dr-feed-title {
            margin: 0 0 4px;
            font-size: 15px;
            font-weight: 700;
            color: var(--dr-text);
        }

        .dr-feed-meta,
        .dr-feed-submeta {
            margin: 0 0 4px;
            font-size: 13px;
            color: #6e6963;
            font-weight: 500;
        }

        .dr-feed-time {
            font-size: 14px;
            font-weight: 600;
            color: var(--dr-muted);
            white-space: nowrap;
        }

        .dr-tag {
            display: inline-flex;
            align-items: center;
            height: 24px;
            padding: 0 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .dr-tag.green {
            background: #e9f8f0;
            color: var(--dr-green);
        }

        .dr-tag.amber {
            background: #fff5de;
            color: #b47e18;
        }

        .dr-tag.red {
            background: #ffebea;
            color: var(--dr-red);
        }

        .dr-empty {
            padding: 24px 0;
            text-align: center;
            color: var(--dr-muted);
            font-size: 14px;
            font-weight: 500;
        }

        @media (max-width: 1199.98px) {
            body:has(.dashboard-reference-page) .topbar .top-search {
                width: 300px;
            }

            .dr-bars {
                gap: 10px;
            }
        }

        @media (max-width: 991.98px) {
            .dashboard-reference-shell {
                min-height: auto;
                padding: 6px 0 18px;
            }

            .dr-hero,
            .dr-card-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .dr-actions {
                width: 100%;
            }
        }

        @media (max-width: 767.98px) {
            body:has(.dashboard-reference-page) .topbar .app-search {
                display: none !important;
            }

            .dr-title {
                font-size: 24px;
            }

            .dr-subtitle {
                font-size: 14px;
            }

            .dr-bars {
                grid-template-columns: repeat(4, 1fr);
                row-gap: 18px;
                height: auto;
            }

            .dr-bar-group {
                min-height: 160px;
            }

            .dr-bar {
                width: 48px;
            }

            .dr-feed-item {
                grid-template-columns: 44px 1fr;
            }

            .dr-feed-time {
                grid-column: 2 / 3;
            }
        }
        .dr-action-card {
            background: #ffffff;
            border: 1px solid var(--dr-line-strong);
            border-radius: 16px;
            padding: 14px 18px;
            display: flex;
            align-items: center;
            gap: 16px;
            text-decoration: none !important;
            transition: all 0.2s ease;
            box-shadow: 0 8px 24px rgba(33, 29, 25, 0.04);
            height: 100%;
        }
        .dr-action-card:hover {
            border-color: #1f1f1f;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }

        /* Inventory Summary Styles */
        .dr-inventory-summary {
            margin-top: 12px;
            padding: 16px;
        }

        .dr-inv-metric {
            background: #fbfaf8;
            border: 1px solid var(--dr-line-strong);
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dr-inv-label {
            font-size: 13px;
            color: var(--dr-muted);
            font-weight: 600;
        }

        .dr-inv-value {
            font-size: 16px;
            font-weight: 800;
            color: var(--dr-text);
        }

        .dr-inv-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .dr-inv-sub {
            padding: 10px;
            border-radius: 10px;
            border: 1px solid var(--dr-line-strong);
            text-align: center;
            background: #fff;
        }

        .dr-inv-sub.danger { border-color: rgba(234, 84, 75, 0.2); background: #fff9f8; }
        .dr-inv-sub.warning { border-color: rgba(215, 162, 56, 0.2); background: #fffdf5; }

        .dr-inv-sub strong {
            display: block;
            font-size: 18px;
            color: var(--dr-text);
            margin-bottom: 2px;
        }

        .dr-inv-sub span {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            color: var(--dr-muted);
        }
        .dr-action-card i {
            font-size: 18px;
            color: var(--dr-text);
            width: 36px;
            height: 36px;
            background: #fbfaf8;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .dr-action-card:hover i {
            background: #1f1f1f;
            color: #ffffff;
        }

        .dr-action-info h4 {
            margin: 0;
            font-size: 14px;
            font-weight: 700;
            color: var(--dr-text);
        }

        .dr-action-info p {
            margin: 0;
            font-size: 12px;
            color: var(--dr-muted);
            font-weight: 500;
        }

        /* Alert Section */
        .dr-alerts {
            margin-bottom: 24px;
        }

        .dr-alert-item {
            background: #fff5de;
            border: 1px solid #f9e6bc;
            border-radius: 12px;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .dr-alert-item.danger {
            background: #ffebea;
            border-color: #f7d2d0;
        }

        .dr-alert-content {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .dr-alert-content i {
            font-size: 18px;
            color: #b47e18;
        }

        .dr-alert-item.danger .dr-alert-content i {
            color: var(--dr-red);
        }

        .dr-alert-text {
            font-size: 14px;
            font-weight: 600;
            color: #4d4a46;
        }

        .dr-alert-btn {
            font-size: 13px;
            font-weight: 700;
            color: #1f1f1f;
            text-decoration: underline; background: none; border: none; padding: 0;
        }

        /* Loading States */
        .dr-loading {
            opacity: 0.5;
            pointer-events: none;
            transition: opacity 0.3s;
        }

        .dr-pulse {
            animation: dr-pulse-anim 1.5s infinite ease-in-out;
        }

        @keyframes dr-pulse-anim {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.02); opacity: 0.7; }
            100% { transform: scale(1); opacity: 1; }
        }

        .dr-last-updated {
            font-size: 11px;
            color: var(--dr-muted);
            margin-top: 4px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 4px;
        }
    </style>
@endpush

@section('main')
    <div class="page-wrapper dashboard-reference-page">
        <div class="page-content">
            <div class="dashboard-reference-shell">
                <div class="container-fluid">
                    <div class="dr-breadcrumb">
                        <span>Main Menu</span>
                        <span>/</span>
                        <span class="dr-breadcrumb-icon"><i class="iconoir-view-grid"></i></span>
                        <strong>Dashboard</strong>
                    </div>

                    <div class="dr-hero">
                        <div>
                            <h1 class="dr-title">Welcome, <span id="dashboardUserName">{{ auth()->user()->name }}</span></h1>
                            <p class="dr-subtitle">Pantau operasional <span id="dashboardOfficeName">kantor aktif</span> dalam satu halaman.</p>
                            <div class="dr-last-updated">
                                <i class="iconoir-refresh"></i>
                                Terakhir update: <span id="dashboardLastUpdated">-</span>
                            </div>
                        </div>
                        <div class="dr-actions">
                            <div class="dropdown">
                                <button class="dr-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="globalFilterBtn">
                                    <i class="iconoir-calendar"></i>
                                    <span id="currentDateRangeLabel">Bulan Ini</span>
                                    <i class="iconoir-nav-arrow-down"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end p-2 border-0 shadow-lg rounded-4 mt-2">
                                    <li><a class="dropdown-item rounded-3 fw-600" href="#" onclick="changeGlobalFilter('today', 'Hari Ini')">Hari Ini</a></li>
                                    <li><a class="dropdown-item rounded-3 fw-600" href="#" onclick="changeGlobalFilter('week', 'Minggu Ini')">Minggu Ini</a></li>
                                    <li><a class="dropdown-item rounded-3 fw-600" href="#" onclick="changeGlobalFilter('month', 'Bulan Ini')">Bulan Ini</a></li>
                                    <li><a class="dropdown-item rounded-3 fw-600" href="#" onclick="changeGlobalFilter('year', 'Tahun Ini')">Tahun Ini</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item rounded-3 fw-600" href="#" onclick="changeGlobalFilter('all', 'Semua Waktu')">Semua Waktu</a></li>
                                </ul>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="dr-btn dr-btn--icon" aria-label="Settings">
                                <i class="iconoir-settings"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Actionable Alerts -->
                    <div id="dashboardAlerts" class="dr-alerts" style="display: none;">
                        <!-- Dynamic Alerts injected via JS -->
                    </div>

                    <!-- Quick Actions Launchpad -->
                    <div class="row g-3 mb-3">
                        <div class="col-xl-3 col-md-6">
                            <a href="{{ route('sales') }}" class="dr-action-card">
                                <i class="iconoir-plus"></i>
                                <div class="dr-action-info">
                                    <h4>Penjualan Baru</h4>
                                    <p>Input invoice baru</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <a href="{{ route('stok') }}" class="dr-action-card">
                                <i class="iconoir-box-iso"></i>
                                <div class="dr-action-info">
                                    <h4>Cek Stok</h4>
                                    <p>Monitoring inventaris</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <a href="{{ route('expenses.index') }}" class="dr-action-card">
                                <i class="iconoir-hand-cash"></i>
                                <div class="dr-action-info">
                                    <h4>Input Biaya</h4>
                                    <p>Catat pengeluaran</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <a href="{{ route('mitra') }}" class="dr-action-card">
                                <i class="iconoir-user-plus"></i>
                                <div class="dr-action-info">
                                    <h4>Tambah Mitra</h4>
                                    <p>Registrasi pelanggan</p>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-xl-3 col-md-6">
                            <div class="dr-card dr-stat-card">
                                <div class="dr-stat-head">
                                    <div class="dr-stat-icon">
                                        <i class="iconoir-reports"></i>
                                    </div>
                                    <p class="dr-stat-title">Total Pendapatan</p>
                                </div>
                                <div class="dr-stat-value">Rp <span id="dashboardRevenue">0</span></div>
                                <div class="dr-stat-foot">
                                    <div class="dr-diff up" id="dashboardRevenueDiff">+0% <span>vs bln lalu</span></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <div class="dr-card dr-stat-card">
                                <div class="dr-stat-head">
                                    <div class="dr-stat-icon">
                                        <i class="iconoir-hand-cash"></i>
                                    </div>
                                    <p class="dr-stat-title">Piutang Usaha</p>
                                </div>
                                <div class="dr-stat-value">Rp <span id="dashboardPiutang">0</span></div>
                                <div class="dr-stat-foot">
                                    <div class="dr-diff up" id="dashboardPiutangDiff">+0% <span>vs bln lalu</span></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <div class="dr-card dr-stat-card">
                                <div class="dr-stat-head">
                                    <div class="dr-stat-icon" style="color: var(--dr-red);">
                                        <i class="iconoir-handshake"></i>
                                    </div>
                                    <p class="dr-stat-title">Utang Usaha</p>
                                </div>
                                <div class="dr-stat-value">Rp <span id="dashboardUtang">0</span></div>
                                <div class="dr-stat-foot">
                                    <div class="dr-diff down" id="dashboardUtangDiff">-0% <span>vs bln lalu</span></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <div class="dr-card dr-stat-card">
                                <div class="dr-stat-head">
                                    <div class="dr-stat-icon" style="color: var(--dr-green);">
                                        <i class="iconoir-wallet"></i>
                                    </div>
                                    <p class="dr-stat-title">Saldo Aktif</p>
                                </div>
                                <div class="dr-stat-value">Rp <span id="dashboardSaldo">0</span></div>
                                <div class="dr-stat-foot">
                                    <div class="dr-diff up" id="dashboardSaldoDiff">+0% <span>vs bln lalu</span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-xl-8">
                            <div class="dr-card dr-chart-card mb-4" id="overviewCard">
                                <div class="dr-card-header">
                                    <div>
                                        <h2 class="dr-card-title">Overview Penjualan</h2>
                                    </div>
                                    <div class="dropdown">
                                        <button class="dr-select" type="button" data-bs-toggle="dropdown">
                                            <span id="widgetOverviewLabel">Filter Stats</span>
                                            <i class="iconoir-nav-arrow-down"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end p-2 border-0 shadow-lg rounded-4">
                                            <li><a class="dropdown-item rounded-3 fw-600" href="#" onclick="changeGlobalFilter('today', 'Hari Ini')">Hari Ini</a></li>
                                            <li><a class="dropdown-item rounded-3 fw-600" href="#" onclick="changeGlobalFilter('week', 'Minggu Ini')">Minggu Ini</a></li>
                                            <li><a class="dropdown-item rounded-3 fw-600" href="#" onclick="changeGlobalFilter('month', 'Bulan Ini')">Bulan Ini</a></li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="dr-overview-metric d-flex gap-4">
                                    <div>
                                        <p class="dr-overview-label">Rata-rata per bulan</p>
                                        <div class="dr-overview-value">
                                            <strong id="dashboardOverviewAvg">0/0</strong>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="dr-overview-label">Pendapatan Bersih (Profit)</p>
                                        <div class="dr-overview-value">
                                            <strong id="dashboardProfit" style="color: var(--dr-green);">0/0</strong>
                                        </div>
                                    </div>
                                </div>

                                <div class="dr-bars-card">
                                    <div class="dr-bars-grid" aria-hidden="true">
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </div>
                                    <div class="dr-bars" id="dashboardOverviewBars"></div>
                                </div>
                            </div>

                            <div class="dr-card dr-table-card">
                                <div class="dr-card-header">
                                    <div>
                                        <h2 class="dr-card-title">Pesanan Terbaru</h2>
                                    </div>
                                    <div class="dropdown">
                                        <button class="dr-select" type="button" data-bs-toggle="dropdown">
                                            <span id="widgetTableLabel">Bulan Ini</span>
                                            <i class="iconoir-filter"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end p-2 border-0 shadow-lg rounded-4">
                                            <li><a class="dropdown-item rounded-3 fw-600" href="#" onclick="changeGlobalFilter('today', 'Hari Ini')">Hari Ini</a></li>
                                            <li><a class="dropdown-item rounded-3 fw-600" href="#" onclick="changeGlobalFilter('month', 'Bulan Ini')">Bulan Ini</a></li>
                                            <li><a class="dropdown-item rounded-3 fw-600" href="#" onclick="changeGlobalFilter('all', 'Semua')">Semua</a></li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="dr-data-table">
                                        <thead style="position: sticky; top: 0; z-index: 5;">
                                            <tr>
                                                <th>No. Invoice</th>
                                                <th>Pelanggan</th>
                                                <th>Total</th>
                                                <th>Tanggal</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="dashboardRecentTransactionsTbody">
                                            <tr>
                                                <td colspan="5" class="dr-empty">Memuat transaksi...</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4">
                            <div class="dr-card dr-feed-card" id="activityCard">
                                <div class="dr-card-header">
                                    <div>
                                        <h2 class="dr-card-title">Riwayat Aktivitas</h2>
                                    </div>
                                    <div class="dropdown">
                                        <button class="dr-select" type="button" data-bs-toggle="dropdown">
                                            <span id="widgetActivityLabel">Otomatis / Range</span>
                                            <i class="iconoir-nav-arrow-down"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end p-2 border-0 shadow-lg rounded-4">
                                            <li><a class="dropdown-item rounded-3 fw-600" href="#" onclick="changeGlobalFilter('today', 'Hari Ini')">Hari Ini</a></li>
                                            <li><a class="dropdown-item rounded-3 fw-600" href="#" onclick="changeGlobalFilter('month', 'Bulan Ini')">Bulan Ini</a></li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="dr-feed-list" id="dashboardActivityList">
                                    <div class="dr-empty">Memuat aktivitas...</div>
                                </div>
                            </div>

                            <div class="dr-card dr-inventory-summary">
                                <div class="dr-card-header mb-3">
                                    <h2 class="dr-card-title">Ringkasan Inventaris</h2>
                                    <i class="iconoir-box-iso" style="color: var(--dr-muted);"></i>
                                </div>
                                <div class="dr-inv-metric">
                                    <span class="dr-inv-label">Nilai Aset Gudang</span>
                                    <span class="dr-inv-value" id="dashboardInventoryValue">Rp 0</span>
                                </div>
                                <div class="dr-inv-grid">
                                    <div class="dr-inv-sub danger">
                                        <strong id="dashboardZeroStock">0</strong>
                                        <span>Stok Kosong</span>
                                    </div>
                                    <div class="dr-inv-sub warning">
                                        <strong id="dashboardLowStock">0</strong>
                                        <span>Stok Menipis</span>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('stok') }}" class="dr-btn w-100 justify-content-center" style="height: 38px; font-size: 13px; font-weight: 700;">
                                        Kelola Inventaris
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function formatCompactNumber(value) {
            const amount = Number(value || 0);
            if (Math.abs(amount) >= 1000000000) return (amount / 1000000000).toFixed(1).replace('.0', '') + 'B';
            if (Math.abs(amount) >= 1000000) return (amount / 1000000).toFixed(1).replace('.0', '') + 'M';
            if (Math.abs(amount) >= 1000) return (amount / 1000).toFixed(1).replace('.0', '') + 'K';
            return amount.toLocaleString('id-ID');
        }

        function formatCurrency(value) {
            return Number(value || 0).toLocaleString('id-ID');
        }

        function formatDate(value) {
            if (!value) return '-';
            const date = new Date(value);
            if (Number.isNaN(date.getTime())) return '-';
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        function formatTime(value) {
            if (!value) return '-';
            const date = new Date(value);
            if (Number.isNaN(date.getTime())) return '-';
            return date.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function statusMeta(status) {
            const normalized = String(status || '').toLowerCase();
            if (normalized === 'paid') {
                return {
                    label: 'Lunas',
                    tone: 'green'
                };
            }

            if (normalized === 'partial' || normalized === 'partially_paid') {
                return {
                    label: 'Parsial',
                    tone: 'amber'
                };
            }

            return {
                label: 'Belum Lunas',
                tone: 'red'
            };
        }

        function renderOverviewBars(chartData) {
            const container = document.getElementById('dashboardOverviewBars');
            if (!container) return;

            if (!chartData || !chartData.length) {
                container.innerHTML = '<div class="dr-empty">Belum ada data penjualan.</div>';
                return;
            }

            const maxValue = Math.max(...chartData.map(d => d.total), 1);
            const barCount = chartData.length;
            
            container.innerHTML = chartData.map((item, index) => {
                const heightPercent = Math.max(5, (item.total / maxValue) * 100);
                const isActive = index === chartData.length - 1;
                
                return `
                    <div class="dr-bar-group">
                        <div class="dr-bar ${isActive ? 'active' : ''}" style="height: ${heightPercent}%;">
                            <div class="dr-bar-tooltip">
                                <small>${item.label}</small>
                                <strong>Rp ${formatCompactNumber(item.total)}</strong>
                            </div>
                            ${isActive ? '<span class="dr-bar-pin"></span>' : ''}
                        </div>
                        <div class="dr-bar-label">${item.label}</div>
                    </div>
                `;
            }).join('');

            // Auto-scroll to end to show latest data
            container.scrollLeft = container.scrollWidth;
        }

        function renderTransactionsTable(transactions) {
            const tbody = document.getElementById('dashboardRecentTransactionsTbody');
            if (!tbody) return;

            if (!transactions.length) {
                tbody.innerHTML = '<tr><td colspan="5" class="dr-empty">Belum ada transaksi pada kantor aktif.</td></tr>';
                return;
            }

            tbody.innerHTML = transactions.slice(0, 5).map((tx, index) => {
                const status = statusMeta(tx.status_pembayaran);
                return `
                    <tr>
                        <td><a href="{{ url('sales') }}/${tx.id || ''}" class="dr-table-code">${escapeHtml(tx.nomor_invoice || '-')}</a></td>
                        <td>${escapeHtml(tx.mitra?.nama || '-')}</td>
                        <td>${formatCurrency(tx.total_akhir)}</td>
                        <td>${escapeHtml(formatDate(tx.created_at))}</td>
                        <td>
                            <span class="dr-status ${status.tone}">
                                <span class="dr-status-dot"></span>${status.label}
                            </span>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function renderActivityList(logs) {
            const container = document.getElementById('dashboardActivityList');
            if (!container) return;

            if (!logs || !logs.length) {
                container.innerHTML = '<div class="dr-empty">Belum ada aktivitas mutasi stok.</div>';
                return;
            }

            container.innerHTML = logs.map((log) => {
                const isOut = log.type === 'out';
                const thumbColor = isOut ? 'red' : 'green';
                const thumbIcon = isOut ? '-' : '+';
                return `
                    <div class="dr-feed-item">
                        <div class="dr-feed-thumb">${thumbIcon}</div>
                        <div>
                            <h3 class="dr-feed-title">${escapeHtml(log.product_name)}</h3>
                            <p class="dr-feed-meta">Status : <span class="dr-tag ${thumbColor}">${log.type.toUpperCase()}</span></p>
                            <p class="dr-feed-submeta">Qty : <strong>${log.qty} pcs</strong></p>
                            <p class="dr-feed-submeta">Info : ${escapeHtml(log.notes || '-')}</p>
                        </div>
                        <div class="dr-feed-time">${escapeHtml(log.time)}</div>
                    </div>
                `;
            }).join('');
        }

        function renderAlerts(alerts) {
            const container = document.getElementById('dashboardAlerts');
            if (!container) return;

            let html = '';
            if (alerts.pending_approvals > 0) {
                html += `
                    <div class="dr-alert-item">
                        <div class="dr-alert-content">
                            <i class="iconoir-info-empty"></i>
                            <span class="dr-alert-text">Ada <strong>${alerts.pending_approvals} Invoice</strong> menunggu persetujuan Anda.</span>
                        </div>
                        <a href="{{ route('sales.approval') }}" class="dr-alert-btn">Proses Sekarang</a>
                    </div>
                `;
            }

            if (alerts.zero_stock > 0) {
                html += `
                    <div class="dr-alert-item danger">
                        <div class="dr-alert-content">
                            <i class="iconoir-warning-triangle"></i>
                            <span class="dr-alert-text">Ada <strong>${alerts.zero_stock} Produk</strong> yang stoknya kosong!</span>
                        </div>
                        <a href="{{ route('stok') }}" class="dr-alert-btn">Cek Inventaris</a>
                    </div>
                `;
            }

            if (alerts.overdue_receivables > 0) {
                html += `
                    <div class="dr-alert-item danger">
                        <div class="dr-alert-content">
                            <i class="iconoir-clock"></i>
                            <span class="dr-alert-text">Ada <strong>${alerts.overdue_receivables} Tagihan</strong> yang sudah lewat jatuh tempo.</span>
                        </div>
                        <a href="{{ route('report.ar-aging') }}" class="dr-alert-btn">Tagih Sekarang</a>
                    </div>
                `;
            }

            if (html) {
                container.innerHTML = html;
                container.style.display = 'block';
            } else {
                container.style.display = 'none';
            }
        }

        async function fetchDashboardData(range = 'month') {
            const shell = document.querySelector('.dashboard-reference-shell');
            const cards = document.querySelectorAll('.dr-card');
            
            // Add loading feedback
            shell.classList.add('dr-loading');
            cards.forEach(c => c.classList.add('dr-pulse'));

            try {
                const response = await fetch(`{{ route('dashboard-api.summary') }}?range=${range}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const json = await response.json();
                if (!json.success) {
                    throw new Error(json.message || 'Gagal memuat data dashboard');
                }

                const data = json.data || {};
                const stats = data.stats || {};
                const transactions = Array.isArray(data.recentTransactions) ? data.recentTransactions : [];

                // Update Header Labels
                document.getElementById('dashboardUserName').textContent = data.user?.name || '{{ auth()->user()->name }}';
                document.getElementById('dashboardOfficeName').textContent = data.office?.name || 'kantor aktif';
                
                // 1. KPI Metrics
                document.getElementById('dashboardRevenue').innerText = formatCurrency(stats.total_pendapatan || 0);
                document.getElementById('dashboardPiutang').innerText = formatCurrency(stats.piutang_usaha || 0);
                document.getElementById('dashboardUtang').innerText = formatCurrency(stats.utang_usaha || 0);
                document.getElementById('dashboardSaldo').innerText = formatCurrency(stats.saldo_aktif || 0);
                
                // 2. Inventory Highlight
                document.getElementById('dashboardInventoryValue').innerText = 'Rp ' + formatCompactNumber(stats.inventory_value || 0);
                document.getElementById('dashboardZeroStock').innerText = stats.zero_stock || 0;
                document.getElementById('dashboardLowStock').innerText = stats.low_stock || 0;
                
                // 3. Pendapatan Bersih (Profit) Highlight
                const profitEl = document.getElementById('dashboardProfit');
                if (profitEl) {
                    profitEl.innerText = 'Rp ' + formatCurrency(stats.pendapatan_bersih || 0);
                }
                
                // 3. Overview Widget
                const avgPerMonth = data.charts?.monthlySales ? (data.charts.monthlySales.reduce((a, b) => a + b.total, 0) / Math.max(data.charts.monthlySales.length, 1)) : 0;
                document.getElementById('dashboardOverviewAvg').textContent = `Rp ${formatCompactNumber(avgPerMonth)}`;

                // Render Dynamic Components
                renderOverviewBars(data.charts?.monthlySales || []);
                renderTransactionsTable(transactions);
                renderActivityList(data.activityLog || []);
                renderAlerts(data.alerts || {});

                // Update Timestamp
                const now = new Date();
                const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                document.getElementById('dashboardLastUpdated').textContent = timeStr;

            } catch (error) {
                console.error('Dashboard Error:', error);
            } finally {
                shell.classList.remove('dr-loading');
                cards.forEach(c => c.classList.remove('dr-pulse'));
            }
        }

        function changeGlobalFilter(range, label) {
            // Update all labels to keep UI consistent
            const labelElements = [
                'currentDateRangeLabel',
                'widgetActivityLabel',
                'widgetOverviewLabel',
                'widgetTableLabel'
            ];

            labelElements.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.textContent = label;
            });
            
            // Re-fetch data
            fetchDashboardData(range);
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initial load
            fetchDashboardData('month');

            const searchInput = document.querySelector('.topbar .top-search');
            if (searchInput) {
                searchInput.placeholder = 'Ketik untuk mencari (Invoice, Produk, Mitra)...';
            }
        });
    </script>
@endpush
