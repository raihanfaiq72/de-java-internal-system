@extends('Layout.main')

@section('main')
    @push('css')
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
        <style>
            /* TomSelect Custom Styling for Stock Adjustment */
            #modalAdjustStock .ts-wrapper {
                width: 100% !important;
            }

            #modalAdjustStock .ts-control {
                border: 1.5px solid #e2e8f0 !important;
                border-radius: 10px !important;
                padding: 10px 12px !important;
                background-color: #f8fafc !important;
                font-size: 14px !important;
                box-shadow: none !important;
                transition: all 0.2s ease !important;
            }

            #modalAdjustStock .ts-control input {
                display: inline-block !important;
                opacity: 1 !important;
                position: relative !important;
            }

            #modalAdjustStock .ts-wrapper.focus .ts-control {
                border-color: #4f46e5 !important;
                background-color: #ffffff !important;
                box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1) !important;
            }

            #modalAdjustStock .ts-dropdown {
                border-radius: 10px !important;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
                border: 1px solid #e2e8f0 !important;
                margin-top: 5px !important;
            }

            #modalAdjustStock .ts-dropdown .active {
                background-color: #4f46e5 !important;
                color: white !important;
            }

            #modalAdjustStock .ts-wrapper.disabled .ts-control {
                background-color: #f1f5f9 !important;
                opacity: 0.7;
            }

            /* Modal Backdrop Style */
            .modal-backdrop {
                background-color: #000 !important;
            }

            .modal-backdrop.show {
                opacity: 0.5 !important;
            }
        </style>
    @endpush
    <div class="page-wrapper">
        <div class="page-content bg-white">
            <div class="dr-page-shell">
                <div class="container-fluid p-0">
                    <!-- Hero Header (Dashboard Style) -->
                    <div class="dr-breadcrumb">
                        <span>Main Menu</span>
                        <span>/</span>
                        <span class="dr-breadcrumb-icon"><i class="iconoir-box-iso"></i></span>
                        <strong>Stok</strong>
                    </div>

                    <div class="dr-hero">
                        <div>
                            <h1 class="dr-title">Manajemen Stok</h1>
                            <p class="dr-subtitle">Monitor persediaan, lokasi, dan mutasi barang secara real-time.</p>
                            <div class="dr-last-updated">
                                <i class="iconoir-refresh"></i>
                                Terakhir update: <span id="stockLastUpdated">-</span>
                            </div>
                        </div>
                        <div class="dr-actions">
                            <select id="filter-stock-location" class="dr-input" style="width: 200px;"
                                onchange="handleFilterChange()">
                                <option value="" selected>Semua Gudang</option>
                            </select>
                            <button class="dr-btn dr-btn-outline" onclick="loadStockData()">
                                <i class="iconoir-refresh"></i> Refresh
                            </button>
                            <button class="dr-btn dr-btn-primary"
                                onclick="window.location.href='{{ route('report.stock') }}'">
                                <i class="iconoir-stats-report"></i> Laporan Lengkap
                            </button>
                        </div>
                    </div>

                    <!-- KPI Summary Cards (Dashboard Style) -->
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-3">
                            <div class="dr-stat-card">
                                <div class="dr-stat-head">
                                    <h4 class="dr-stat-title">Total Produk</h4>
                                    <div class="dr-stat-icon"><i class="iconoir-box-iso"></i></div>
                                </div>
                                <div class="dr-stat-value" id="stat-total-items">0</div>
                                <div class="dr-stat-footer">
                                    <span>Items: <strong id="stat-total-qty">0</strong></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="dr-stat-card">
                                <div class="dr-stat-head">
                                    <h4 class="dr-stat-title">Perlu Restock</h4>
                                    <div class="dr-stat-icon text-danger"><i class="iconoir-warning-triangle"></i></div>
                                </div>
                                <div class="dr-stat-value text-danger" id="stat-restock-count">0</div>
                                <div class="dr-stat-footer">
                                    <span>Habis: <strong id="stat-out-stock" class="text-danger">0</strong></span>
                                    <span class="mx-1">·</span>
                                    <span>Low: <strong id="stat-low-stock" class="text-warning">0</strong></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="dr-stat-card">
                                <div class="dr-stat-head">
                                    <h4 class="dr-stat-title">Valuasi Stok</h4>
                                    <div class="dr-stat-icon text-success"><i class="iconoir-wallet"></i></div>
                                </div>
                                <div class="dr-stat-value text-success" style="font-size: 24px;" id="stat-inventory-value">
                                    Rp 0</div>
                                <div class="dr-stat-footer">
                                    <span>Nilai aset saat ini</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="dr-stat-card">
                                <div class="dr-stat-head">
                                    <h4 class="dr-stat-title">Akan Masuk</h4>
                                    <div class="dr-stat-icon text-info"><i class="iconoir-delivery-truck"></i></div>
                                </div>
                                <div class="dr-stat-value" style="font-size: 24px;" id="stat-pending-receive">Rp 0</div>
                                <div class="dr-stat-footer">
                                    <span>Dari Pembelian (PO)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content Section -->
                    <div class="dr-card p-0 overflow-hidden bg-white">
                        <div
                            class="dr-card-header border-bottom px-4 py-3 bg-white d-flex align-items-center justify-content-between">
                            <ul class="nav dr-tabs-segmented" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-persediaan"
                                        type="button" role="tab">
                                        <i class="iconoir-box-iso me-2"></i> Daftar Stok
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-lokasi"
                                        type="button" role="tab">
                                        <i class="iconoir-map-pin me-2"></i> Lokasi
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-dokumen"
                                        type="button" role="tab">
                                        <i class="iconoir-clock-rotate-right me-2"></i> Riwayat
                                    </button>
                                </li>
                            </ul>

                            <div class="d-flex align-items-center gap-2">
                                <!-- Compact search or other quick tools could go here -->
                            </div>
                        </div>

                        <div class="p-4">
                            <div class="tab-content">
                                <!-- TAB 1: DAFTAR STOK -->
                                <div class="tab-pane fade show active" id="tab-persediaan" role="tabpanel">
                                    <div class="mb-4">
                                        <p class="dr-card-title fs-5 mb-1">Daftar Inventori Barang</p>
                                        <p class="text-muted small mb-0">Informasi detail posisi stok, nilai inventori, dan
                                            status ketersediaan produk secara real-time.</p>
                                    </div>
                                    <div class="dr-filter-bar mb-4">
                                        <div class="dr-filter-group" style="flex:2;min-width:200px;">
                                            <label class="dr-label">Cari Produk / SKU</label>
                                            <div class="dr-search-wrap">
                                                <i class="iconoir-search dr-search-icon"></i>
                                                <input type="text" id="filter-stock-search"
                                                    class="dr-input dr-search-input"
                                                    placeholder="Ketik nama produk atau SKU..."
                                                    onkeyup="handleFilterChange()">
                                            </div>
                                        </div>

                                        <div class="dr-filter-group" style="flex:1;min-width:140px;">
                                            <label class="dr-label">Kategori</label>
                                            <select id="filter-stock-kategori" class="dr-input"
                                                onchange="handleFilterChange()">
                                                <option value="">Semua Kategori</option>
                                            </select>
                                        </div>
                                        <div class="dr-filter-group" style="flex:1;min-width:140px;">
                                            <label class="dr-label">Status Stok</label>
                                            <select id="filter-stock-status" class="dr-input"
                                                onchange="handleFilterChange()">
                                                <option value="">Semua Status</option>
                                                <option value="safe">Aman</option>
                                                <option value="low">Menipis (< 5)</option>
                                                <option value="empty">Habis (0)</option>
                                            </select>
                                        </div>
                                        <div class="dr-filter-actions">
                                            <label class="dr-label" style="visibility:hidden;">-</label>
                                            <div class="d-flex gap-2">
                                                <button class="dr-btn dr-btn-outline dr-btn-icon" title="Export"
                                                    onclick="exportStock()">
                                                    <i class="iconoir-download"></i>
                                                </button>
                                                <button class="dr-btn dr-btn-outline dr-btn-icon" title="Print"
                                                    onclick="printStock()">
                                                    <i class="iconoir-printer"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="dr-table-container mb-4">
                                        <table class="dr-table align-middle">
                                            <thead>
                                                <tr>
                                                    <th class="ps-4" style="width:30%;">Produk</th>
                                                    <th style="width:12%;">Supplier</th>
                                                    <th class="text-center" style="width:10%;">Status</th>
                                                    <th class="text-center" style="width:10%;">Stok Fisik</th>
                                                    <th class="text-end" style="width:12%;">Nilai Aset (IDR)</th>
                                                    <th class="text-end" style="width:12%;">Nilai Jual (IDR)</th>
                                                    <th class="text-end pe-4" style="width:10%;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="stock-table-body">
                                                <tr>
                                                    <td colspan="7" class="text-center py-5 text-muted">
                                                        <div class="spinner-border spinner-border-sm text-primary me-2">
                                                        </div>
                                                        Sedang memuat data stok...
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center px-2"
                                        id="persediaan-pagination-wrapper">
                                        <span id="stock-pagination-info" class="text-muted small fw-medium"></span>
                                        <nav>
                                            <ul class="pagination pagination-sm mb-0 gap-1"
                                                id="stock-pagination-container"></ul>
                                        </nav>
                                    </div>
                                </div>

                                <!-- TAB 2: LOKASI -->
                                <div class="tab-pane fade" id="tab-lokasi" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <div>
                                            <p class="dr-card-title fs-5 mb-1">Daftar Lokasi Penyimpanan</p>
                                            <p class="text-muted small mb-0">Kelola gudang, rak, dan area penyimpanan stok.
                                            </p>
                                        </div>
                                        <button class="dr-btn dr-btn-primary" onclick="showCreateLocationModal()">
                                            <i class="iconoir-plus"></i> Tambah Lokasi
                                        </button>
                                    </div>

                                    <div class="dr-table-container mb-4" style="max-height: 500px; overflow-y: auto;">
                                        <table class="dr-table align-middle">
                                            <thead>
                                                <tr>
                                                    <th class="ps-4">Nama Lokasi</th>
                                                    <th>Tipe</th>
                                                    <th class="text-end pe-4">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="location-table-body">
                                                <tr>
                                                    <td colspan="3" class="text-center py-5 text-muted">
                                                        <div class="spinner-border spinner-border-sm text-primary me-2">
                                                        </div>
                                                        Memuat data lokasi...
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- TAB 3: RIWAYAT MUTASI -->
                                <div class="tab-pane fade" id="tab-dokumen" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <div>
                                            <h5 class="fw-bold mb-1">Riwayat Pergerakan Stok</h5>
                                            <p class="text-muted small mb-0">Log aktivitas keluar masuk barang.</p>
                                        </div>
                                        <button onclick="loadMutationData()" class="btn btn-outline-secondary btn-sm">
                                            <i class="iconoir-refresh me-1"></i> Refresh
                                        </button>
                                    </div>

                                    <!-- Mutation Filters -->
                                    <div class="dr-filter-bar mb-4">
                                        <div class="dr-filter-group" style="flex:2;min-width:200px;">
                                            <label class="dr-label">Cari Produk / SKU</label>
                                            <div class="dr-search-wrap">
                                                <i class="iconoir-search dr-search-icon"></i>
                                                <input type="text" id="filter-mutation-search"
                                                    class="dr-input dr-search-input" placeholder="Cari produk..."
                                                    onkeyup="loadMutationData()">
                                            </div>
                                        </div>
                                        <div class="dr-filter-group" style="flex:1;min-width:140px;">
                                            <label class="dr-label">Dari Tanggal</label>
                                            <input type="date" id="filter-mutation-start" class="dr-input"
                                                onchange="loadMutationData()">
                                        </div>
                                        <div class="dr-filter-group" style="flex:1;min-width:140px;">
                                            <label class="dr-label">Sampai Tanggal</label>
                                            <input type="date" id="filter-mutation-end" class="dr-input"
                                                onchange="loadMutationData()">
                                        </div>
                                        <div class="dr-filter-group" style="flex:1;min-width:130px;">
                                            <label class="dr-label">Tipe Mutasi</label>
                                            <select id="filter-mutation-type" class="dr-input"
                                                onchange="loadMutationData()">
                                                <option value="">Semua Tipe</option>
                                                <option value="IN">Masuk (IN)</option>
                                                <option value="OUT">Keluar (OUT)</option>
                                            </select>
                                        </div>

                                    </div>

                                    <div class="dr-table-container mb-4">
                                        <table class="dr-table align-middle">
                                            <thead>
                                                <tr>
                                                    <th class="ps-4">Tanggal</th>
                                                    <th>Produk</th>
                                                    <th class="text-center">Tipe</th>
                                                    <th class="text-center">Qty</th>
                                                    <th>Lokasi</th>
                                                    <th>Aksi / Referensi</th>
                                                    <th>User</th>
                                                    <th>Catatan</th>
                                                </tr>
                                            </thead>
                                            <tbody id="mutation-table-body">
                                                <tr>
                                                    <td colspan="8" class="text-center py-5 text-muted">
                                                        <div class="spinner-border spinner-border-sm text-primary me-2">
                                                        </div>
                                                        Memuat riwayat...
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center px-2">
                                        <span id="mutation-pagination-info" class="text-muted small fw-medium"></span>
                                        <nav>
                                            <ul class="pagination pagination-sm mb-0 gap-1"
                                                id="mutation-pagination-container"></ul>
                                        </nav>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modals -->
        @include('Stock.Modal._AdjustStock')
        @include('Stock.Modal._LocationModals')

        <!-- FIFO Modal -->
        <!-- FIFO Modal -->
        <div class="modal fade" id="modalFifo" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="dr-card-title fs-5">
                            <i class="iconoir-list-select me-2 text-primary"></i> Detail Stok (FIFO)
                        </h5>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div id="fifo-content"></div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            // State
            let currentPage = 1;
            let currentMutationPage = 1;
        </script>

        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
        <script>
            // TomSelect Initialization Helper
            let tsInstances = {};

            function initTS(id, icon = 'iconoir-filter') {
                const el = document.getElementById(id);
                if (!el) return null;
                if (tsInstances[id]) tsInstances[id].destroy();

                tsInstances[id] = new TomSelect(el, {
                    controlInput: null,
                    allowEmptyOption: true,
                    render: {
                        option: function(data, escape) {
                            return `<div class="option"><i class="${icon} me-2 text-muted"></i>${escape(data.text)}</div>`;
                        },
                        item: function(data, escape) {
                            return `<div class="item-with-icon"><i class="${icon} me-2 text-primary"></i>${escape(data.text)}</div>`;
                        }
                    },
                    onChange: function() {
                        // Manually trigger onchange if exists
                        const event = new Event('change', {
                            bubbles: true
                        });
                        el.dispatchEvent(event);
                    }
                });
                return tsInstances[id];
            }

            let debounceTimer;

            // Init
            document.addEventListener('DOMContentLoaded', function() {
                // Initial TS Load for static selects
                initTS('filter-stock-status', 'iconoir-check-circle');

                loadStockDashboard();
                loadStockData();
                loadLocations(); // For filter dropdown
                fetchCategories(); // Load categories for filter dropdown
                loadMutationData();
            });

            // --- DASHBOARD & STATS ---
            function loadStockDashboard() {
                const locId = document.getElementById('filter-stock-location').value;

                let url = `{{ route('stock-api.dashboard') }}?t=${new Date().getTime()}`;
                if (locId) url += `&location_id=${locId}`;

                fetch(url)
                    .then(response => response.json())
                    .then(res => {
                        if (res.success) {
                            const ov = res.data.overview;

                            // Update Stats Cards
                            document.getElementById('stat-total-items').innerText = new Intl.NumberFormat('id-ID').format(ov
                                .total_items || 0);
                            document.getElementById('stat-total-qty').innerText = new Intl.NumberFormat('id-ID').format(ov
                                .total_qty || 0);

                            document.getElementById('stat-inventory-value').innerText = formatCurrency(ov.inventory_value ||
                                0);

                            document.getElementById('stat-pending-receive').innerText = formatCurrency(ov.pending_receive ||
                                0);

                            // Restock Logic
                            const low = ov.low_stock_count || 0;
                            const out = ov.out_of_stock_count || 0;
                            document.getElementById('stat-restock-count').innerText = (low + out);
                            document.getElementById('stat-low-stock').innerText = low;
                            document.getElementById('stat-out-stock').innerText = out;

                            // Update Last Updated Time
                            const now = new Date();
                            const timeString = now.getHours().toString().padStart(2, '0') + '.' +
                                now.getMinutes().toString().padStart(2, '0') + '.' +
                                now.getSeconds().toString().padStart(2, '0');
                            document.getElementById('stockLastUpdated').innerText = timeString;
                        }
                    })
                    .catch(err => console.error('Error loading dashboard:', err));
            }

            // --- STOCK LIST ---
            function handleFilterChange() {
                // Debounce for search input
                const tbody = document.getElementById('stock-table-body');
                tbody.innerHTML =
                    '<tr><td colspan="7" class="text-center py-5 text-muted"><div class="spinner-border text-primary mb-2"></div><p>Memuat data...</p></td></tr>';

                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    currentPage = 1;
                    loadStockData();
                    loadMutationData(); // Also refresh mutation data with new global filters
                    loadStockDashboard(); // Update stats based on location filter if needed
                }, 500);
            }

            function loadStockData(page = 1) {
                currentPage = page;
                const search = document.getElementById('filter-stock-search').value;
                const location = document.getElementById('filter-stock-location').value;
                const category = document.getElementById('filter-stock-kategori').value;
                const status = document.getElementById('filter-stock-status') ? document.getElementById('filter-stock-status')
                    .value : '';

                let url = `{{ route('stock-api.index') }}?page=${page}`;
                if (search) url += `&search=${encodeURIComponent(search)}`;
                if (location) url += `&location_id=${location}`;
                if (category) url += `&category=${category}`;

                fetch(url)
                    .then(response => response.json())
                    .then(res => {
                        const tbody = document.getElementById('stock-table-body');
                        tbody.innerHTML = '';

                        if (res.success && res.data.data.length > 0) {
                            res.data.data.forEach(item => {
                                const locationQty = item.location_qty;
                                const globalQty = item.qty;
                                let qty = globalQty;

                                if (location !== '') {
                                    qty = locationQty !== null ? parseFloat(locationQty) : 0;
                                } else {
                                    qty = parseFloat(globalQty);
                                }

                                let statusBadge = '';
                                if (qty <= 0) {
                                    statusBadge =
                                        '<span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3">Habis</span>';
                                } else if (qty < 5) {
                                    statusBadge =
                                        '<span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3">Menipis</span>';
                                } else {
                                    statusBadge =
                                        '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">Aman</span>';
                                }

                                if (status === 'empty' && qty > 0) return;
                                if (status === 'low' && (qty <= 0 || qty >= 5)) return;
                                if (status === 'safe' && qty < 5) return;

                                const tr = document.createElement('tr');
                                tr.innerHTML = `
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            ${item.foto_produk
                                                ? `<img src="/storage/${item.foto_produk}" class="rounded border" style="width: 48px; height: 48px; object-fit: cover;">`
                                                : `<div class="bg-light rounded d-flex align-items-center justify-content-center text-muted border" style="width: 48px; height: 48px;"><i class="iconoir-box-iso fs-4"></i></div>`
                                            }
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-0 fw-bold text-dark">${item.nama_produk}</h6>
                                            <small class="text-muted d-block">${item.sku_kode} <span class="mx-1">•</span> <span class="badge bg-light text-secondary border fw-medium">${item.category?.nama_kategori || '-'}</span></small>
                                        </div>
                                    </div>
                                </td>
                                <td class="small text-muted">${item.supplier?.nama || '-'}</td>
                                <td class="text-center">${statusBadge}</td>
                                <td class="text-center fw-bold fs-6">${new Intl.NumberFormat('id-ID', {maximumFractionDigits: 0}).format(qty)} <small class="text-muted fw-normal">${item.satuan}</small></td>
                                <td class="text-end text-muted font-monospace">${formatCurrency(qty * item.harga_beli)}</td>
                                <td class="text-end text-muted font-monospace">${formatCurrency(qty * (item.harga_jual || 0))}</td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-1">
                                        <button class="dr-btn-icon dr-btn-icon-view" onclick="showFifoDetail(${item.id})" title="Detail FIFO">
                                            <i class="iconoir-list"></i>
                                        </button>
                                        <button class="dr-btn-icon dr-btn-icon-edit" onclick="showAdjustStockModal(${item.id}, '${item.nama_produk}', '${item.sku_kode}')" title="Adjust">
                                            <i class="iconoir-edit-pencil"></i>
                                        </button>
                                    </div>
                                </td>
                            `;
                                tbody.appendChild(tr);
                            });

                            if (tbody.children.length === 0) {
                                tbody.innerHTML =
                                    '<tr><td colspan="7" class="text-center py-5 text-muted">Tidak ada data yang cocok dengan filter status.</td></tr>';
                            }

                            renderPagination(res.data, 'stock-pagination-container', 'stock-pagination-info',
                                loadStockData);
                        } else {
                            tbody.innerHTML =
                                '<tr><td colspan="7" class="text-center py-5 text-muted">Belum ada data stok.</td></tr>';
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        document.getElementById('stock-table-body').innerHTML =
                            '<tr><td colspan="7" class="text-center py-5 text-danger">Gagal memuat data.</td></tr>';
                    });
            }

            // --- LOCATIONS ---
            function loadLocations() {
                // Populate Dropdown Filter (Hero)
                fetch('{{ route('stock-location-api.index') }}?per_page=1000')
                    .then(res => res.json())
                    .then(data => {
                        // Populate Dropdown Filter (Hero)
                        const select = document.getElementById('filter-stock-location');

                        // Always start with "Semua Gudang"
                        select.innerHTML = '<option value="" selected>Semua Gudang</option>';

                        if (data && data.data) {
                            data.data.forEach(loc => {
                                const opt = document.createElement('option');
                                opt.value = loc.id;
                                opt.textContent = loc.name;
                                select.appendChild(opt);
                            });
                        }

                        // Also populate table in Location Tab
                        renderLocationTable(data.data || []);

                        // Re-init TomSelect
                        initTS('filter-stock-location', 'iconoir-map-pin');
                    });
            }

            function renderLocationTable(data) {
                const tbody = document.getElementById('location-table-body');
                tbody.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(loc => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                                                                                                            <td class="ps-4 fw-bold">${loc.name}</td>
                                                                                                            <td><span class="badge bg-light text-dark border">${loc.type || 'Gudang'}</span></td>
                                                                                                            <td class="text-end pe-4">
                                                                                                                <button class="btn btn-sm btn-light border" onclick="editLocation(${loc.id}, '${loc.name}', '${loc.type}')"><i class="iconoir-edit-pencil"></i></button>
                                                                                                                <button class="btn btn-sm btn-light border text-danger" onclick="deleteLocation(${loc.id})"><i class="iconoir-trash"></i></button>
                                                                                                            </td>
                                                                                                        `;
                        tbody.appendChild(tr);
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="3" class="text-center py-5 text-muted">Belum ada lokasi.</td></tr>';
                }
            }

            // --- MUTATIONS ---
            function loadMutationData(page = 1) {
                currentMutationPage = page;
                const start = document.getElementById('filter-mutation-start').value;
                const end = document.getElementById('filter-mutation-end').value;
                const type = document.getElementById('filter-mutation-type').value;
                const location = document.getElementById('filter-stock-location').value;
                const search = document.getElementById('filter-mutation-search') ? document.getElementById(
                    'filter-mutation-search').value : '';

                let url = `{{ route('stock-api.mutations') }}?page=${page}`;
                if (start) url += `&date_from=${start}`;
                if (end) url += `&date_to=${end}`;
                if (type) url += `&type=${type}`;
                if (location) url += `&location_id=${location}`;
                if (search) url += `&search=${encodeURIComponent(search)}`;

                fetch(url)
                    .then(res => res.json())
                    .then(res => {
                        const tbody = document.getElementById('mutation-table-body');
                        tbody.innerHTML = '';

                        if (res.success && res.data.data.length > 0) {
                            res.data.data.forEach(m => {
                                const isInc = m.type === 'IN';
                                const badge = isInc ?
                                    '<span class="badge bg-success-subtle text-success">MASUK</span>' :
                                    '<span class="badge bg-danger-subtle text-danger">KELUAR</span>';

                                const actor = m.actor?.name || m.actor?.username || '-';
                                const action = m.action_label || m.reference_type || 'Mutasi Stok';

                                // Check if action contains invoice reference and make it clickable
                                let actionHtml = action;
                                if (action && (action.includes('Invoice') || action.includes('INV/'))) {
                                    // Extract invoice number if possible
                                    const invoiceMatch = action.match(/#([^#\s]+)/);
                                    const invoiceNumber = invoiceMatch ? invoiceMatch[1] : action;

                                    if (invoiceNumber) {
                                        actionHtml = `<a href="{{ route('report.invoice') }}?search=${encodeURIComponent(invoiceNumber)}" 
                                        class="text-primary text-decoration-none" 
                                        title="Klik untuk menuju ke invoice"
                                        style="cursor: pointer;">
                                        ${action}
                                    </a>`;
                                    } else {
                                        // Fallback if no invoice number found
                                        actionHtml = action;
                                    }
                                }

                                const tr = document.createElement('tr');
                                tr.innerHTML = `
                                                                                                                    <td class="ps-4 small">${new Date(m.created_at).toLocaleString('id-ID')}</td>
                                                                                                                    <td>
                                                                                                                        <div class="fw-bold text-dark">${m.product?.nama_produk || '-'}</div>
                                                                                                                        <small class="text-muted">${m.product?.sku_kode || ''}</small>
                                                                                                                    </td>
                                                                                                                    <td class="text-center">${badge}</td>
                                                                                                                    <td class="text-center fw-bold ${isInc ? 'text-success' : 'text-danger'}">
                                                                                                                        ${isInc ? '+' : '-'}${m.qty}
                                                                                                                    </td>
                                                                                                                    <td><small class="text-muted">${m.stock_location?.name || '-'}</small></td>
                                                                                                                    <td><small class="text-muted">${actionHtml}</small></td>
                                                                                                                    <td><small class="text-muted">${actor}</small></td>
                                                                                                                    <td><small>${m.notes || '-'}</small></td>
                                                                                                                `;
                                tbody.appendChild(tr);
                            });

                            renderPagination(res.data, 'mutation-pagination-container', 'mutation-pagination-info',
                                loadMutationData);
                        } else {
                            tbody.innerHTML =
                                '<tr><td colspan="8" class="text-center py-5 text-muted">Belum ada riwayat mutasi.</td></tr>';
                        }
                    });
            }

            // --- MODAL FUNCTIONS (Restored) ---

            // Adjust Stock (New Location-Based)
            function showAdjustStockModal(id, name, sku) {
                document.getElementById('adjust-stock-id').value = id;
                document.getElementById('adjust-stock-sku').value = sku;
                document.getElementById('adjust-stock-name').value = name;
                document.getElementById('adjust-stock-note').value = '';

                const listContainer = document.getElementById('adjust-stock-location-list');
                listContainer.innerHTML = '<tr><td colspan="3" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div> Loading data gudang...</td></tr>';

                const modalEl = document.getElementById('modalAdjustStock');
                let modal = bootstrap.Modal.getInstance(modalEl);
                if (!modal) modal = new bootstrap.Modal(modalEl);
                modal.show();

                Promise.all([
                    fetch(`{{ route('stock-location-api.index') }}?per_page=1000`).then(res => res.json()),
                    fetch(`{{ url('api/stock-api') }}/${id}/locations`).then(res => res.json())
                ])
                .then(([locationsRes, stockRes]) => {
                    listContainer.innerHTML = '';

                    if (locationsRes.data) {
                        const allLocations = locationsRes.data.data || locationsRes.data;
                        const stockData = stockRes.success ? stockRes.data : [];

                        if (allLocations.length === 0) {
                            listContainer.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-3">Belum ada lokasi gudang yang didaftarkan.</td></tr>';
                            return;
                        }

                        allLocations.forEach(loc => {
                            const existingStock = stockData.find(s => s.id === loc.id || s.location_id === loc.id);
                            const currentQty = existingStock ? parseFloat(existingStock.current_qty) : 0;

                            const row = `
                            <tr>
                                <td class="ps-3 py-3">
                                    <div class="fw-medium">${loc.name}</div>
                                    <div class="text-muted small text-uppercase">${loc.type || 'Warehouse'}</div>
                                </td>
                                <td class="text-center py-3">
                                    <span class="badge bg-light text-dark border">${Math.round(currentQty)}</span>
                                </td>
                                <td class="pe-3 py-3 text-end">
                                    <input type="number"
                                        class="dr-input text-center py-1 px-2 adjust-qty-input"
                                        style="width: 100px; display: inline-block;"
                                        data-location-id="${loc.id}"
                                        data-current="${currentQty}"
                                        value="${Math.round(currentQty)}"
                                        step="1"
                                        oninput="calculateAdjustTotal()">
                                </td>
                            </tr>
                            `;
                            listContainer.insertAdjacentHTML('beforeend', row);
                        });

                        calculateAdjustTotal();
                    } else {
                        listContainer.innerHTML = '<tr><td colspan="3" class="text-center text-danger">Gagal memuat data lokasi</td></tr>';
                    }
                })
                .catch(err => {
                    console.error(err);
                    listContainer.innerHTML = '<tr><td colspan="3" class="text-center text-danger">Kesalahan koneksi</td></tr>';
                });
            }

            function calculateAdjustTotal() {
                let total = 0;
                document.querySelectorAll('.adjust-qty-input').forEach(input => {
                    total += parseInt(input.value || 0);
                });
                document.getElementById('adjust-stock-total-calc').textContent = new Intl.NumberFormat('id-ID').format(total);
            }

            function submitAdjustStock() {
                const id = document.getElementById('adjust-stock-id').value;
                const note = document.getElementById('adjust-stock-note').value;

                const adjustments = [];
                document.querySelectorAll('.adjust-qty-input').forEach(input => {
                    const current = parseFloat(input.getAttribute('data-current'));
                    const target = parseInt(input.value || 0);
                    
                    const locId = input.getAttribute('data-location-id');

                    // Always send if current != target OR if it's the unlocated row (to trigger move)
                    if (current !== target || (locId === 'null' && current !== 0)) {
                        adjustments.push({
                            location_id: locId === 'null' ? null : locId,
                            qty: target
                        });
                    }
                });

                if (!id) return;
                if (adjustments.length === 0) {
                    alert('Tidak ada perubahan stok yang dilakukan');
                    return;
                }

                fetch(`{{ url('api/stock-api') }}/${id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            adjustments: adjustments,
                            notes: note
                        })
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            bootstrap.Modal.getInstance(document.getElementById('modalAdjustStock')).hide();
                            loadStockData(currentPage);
                            loadStockDashboard();
                            alert('Semua perubahan stok berhasil disimpan');
                        } else {
                            alert('Gagal: ' + res.message);
                        }
                    })
                    .catch(err => alert('Terjadi kesalahan sistem'));
            }

            // Location CRUD
            function showCreateLocationModal() {
                document.getElementById('location-id').value = '';
                document.getElementById('location-name').value = '';
                document.getElementById('location-type').value = 'stock';
                document.getElementById('location-modal-title').innerText = 'Buat Lokasi Stok';

                const modal = new bootstrap.Modal(document.getElementById('modalLocation'));
                modal.show();
            }

            function editLocation(id, name, type) {
                document.getElementById('location-id').value = id;
                document.getElementById('location-name').value = name;
                document.getElementById('location-type').value = type || 'stock';
                document.getElementById('location-modal-title').innerText = 'Edit Lokasi';

                const modal = new bootstrap.Modal(document.getElementById('modalLocation'));
                modal.show();
            }

            function submitLocation() {
                const id = document.getElementById('location-id').value;
                const name = document.getElementById('location-name').value;
                const type = document.getElementById('location-type').value;

                const url = id ? '{{ url('api/stock-location-api') }}/' + id : '{{ route('stock-location-api.store') }}';
                const method = id ? 'PUT' : 'POST';

                fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            name,
                            type
                        })
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            bootstrap.Modal.getInstance(document.getElementById('modalLocation')).hide();
                            loadLocations();
                        } else {
                            alert('Gagal: ' + res.message);
                        }
                    });
            }

            function deleteLocation(id) {
                if (!confirm('Hapus lokasi ini? Stok yang ada di lokasi ini mungkin akan kehilangan referensi.')) return;

                fetch(`{{ url('api/stock-location-api') }}/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) loadLocations();
                        else alert(res.message);
                    });
            }

            // Stock Opname (Placeholder for now as we removed the button)
            function showStockOpnameModal() {
                alert('Gunakan tombol "Adjust" pada tabel untuk penyesuaian stok per item.');
            }

            // --- HELPERS ---
            function fetchCategories() {
                fetch('{{ route('product-category-api.index') }}')
                    .then(res => res.json())
                    .then(data => {
                        const select = document.getElementById('filter-stock-kategori');

                        // Clear existing options except first one
                        select.innerHTML = '<option value="">Semua Kategori</option>';

                        // Add categories
                        if (data.success && data.data) {
                            const categories = data.data.data || data.data;
                            categories.forEach(cat => {
                                const opt = document.createElement('option');
                                opt.value = cat.id;
                                opt.textContent = cat.nama_kategori;
                                select.appendChild(opt);
                            });

                            // Re-init TomSelect
                            initTS('filter-stock-kategori', 'iconoir-nav-arrow-right');
                        }
                    })
                    .catch(err => {
                        console.error('Failed to load categories:', err);
                    });
            }

            function formatCurrency(value) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(value);
            }

            function renderPagination(data, containerId, infoId, loadFunction) {
                const container = document.getElementById(containerId);
                const info = document.getElementById(infoId);

                if (!data || !data.links) return;

                if (info) {
                    info.innerHTML =
                        `Menampilkan <span class="text-dark fw-bold">${data.from || 0}</span> - <span class="text-dark fw-bold">${data.to || 0}</span> dari <span class="text-dark fw-bold">${data.total}</span> data`;
                }

                let html = '';
                data.links.forEach((link, index) => {
                    const activeClass = link.active ? 'active' : '';
                    const disabledClass = !link.url ? 'disabled' : '';

                    // Label adjustments
                    let label = link.label;
                    if (label.includes('Previous')) label = '<i class="iconoir-nav-arrow-left"></i>';
                    if (label.includes('Next')) label = '<i class="iconoir-nav-arrow-right"></i>';

                    let pageNum = 1;
                    if (link.url) {
                        const url = new URL(link.url);
                        pageNum = url.searchParams.get('page') || 1;
                    }

                    const onclick = link.url ? `onclick="${loadFunction.name}(${pageNum})"` : '';

                    html += `
                    <li class="page-item ${activeClass} ${disabledClass}">
                        <button class="page-link d-flex align-items-center justify-content-center" style="min-width: 32px; height: 32px; border-radius: 8px !important;" ${onclick}>${label}</button>
                    </li>`;
                });

                container.innerHTML = html;
            }

            // --- FIFO MODAL ---
            let fifoModalInstance = null;

            function showFifoDetail(productId) {
                const modalEl = document.getElementById('modalFifo');
                if (!fifoModalInstance) {
                    fifoModalInstance = new bootstrap.Modal(modalEl);
                }

                document.getElementById('fifo-content').innerHTML =
                    '<div class="text-center p-5"><div class="spinner-border text-primary"></div></div>';
                fifoModalInstance.show();

                const locationId = document.getElementById('filter-stock-location').value;
                let url = `{{ url('api/stock-api') }}/${productId}/fifo`;
                if (locationId) url += `?location_id=${locationId}`;

                fetch(url)
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            let html = `
                            <div class="p-3 border mb-4 d-flex align-items-center gap-3" style="background-color: var(--dr-panel-soft); border-radius: var(--dr-radius-sm);">
                                <div class="bg-white border d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; border-radius: 8px;">
                                    <i class="iconoir-box-iso text-primary fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark">${res.data.product.nama_produk}</h6>
                                    <small class="text-muted">Total Stok: <strong class="text-dark">${new Intl.NumberFormat('id-ID').format(res.data.total_stock)}</strong> ${res.data.product.satuan}</small>
                                </div>
                            </div>

                            <h6 class="small fw-bold text-uppercase text-muted mb-3 tracking-wider" style="letter-spacing: 0.5px;">Batch Stok (FIFO)</h6>
                            <div class="dr-table-container border rounded overflow-hidden">
                                <table class="dr-table align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th style="font-size: 10px; padding: 10px 15px;">Tgl Masuk</th>
                                            <th style="font-size: 10px; padding: 10px 15px;">Harga Beli</th>
                                            <th style="font-size: 10px; padding: 10px 15px;">Sisa Qty</th>
                                            <th style="font-size: 10px; padding: 10px 15px;">Lokasi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        `;

                            if (res.data.batches.length > 0) {
                                res.data.batches.forEach(b => {
                                    html += `
                                    <tr>
                                        <td class="small text-muted" style="padding: 12px 15px;">${new Date(b.created_at).toLocaleDateString('id-ID')}</td>
                                        <td class="font-monospace small" style="padding: 12px 15px;">${formatCurrency(b.cost_price)}</td>
                                        <td class="fw-bold text-dark" style="padding: 12px 15px;">${b.remaining_qty}</td>
                                        <td class="small text-muted" style="padding: 12px 15px;">${b.stock_location?.name || '-'}</td>
                                    </tr>
                                `;
                                });
                            } else {
                                html +=
                                    `<tr><td colspan="4" class="text-center text-muted py-4 small">Tidak ada batch aktif untuk produk ini.</td></tr>`;
                            }

                            html += `</tbody></table></div>`;
                            document.getElementById('fifo-content').innerHTML = html;
                        }
                    });
            }

            function exportStock() {
                const search = document.getElementById('filter-stock-search').value;
                const location = document.getElementById('filter-stock-location').value;
                const category = document.getElementById('filter-stock-kategori').value;

                let url = `{{ route('stok.export') }}?t=${new Date().getTime()}`;
                if (search) url += `&search=${encodeURIComponent(search)}`;
                if (location) url += `&location_id=${location}`;
                if (category) url += `&category=${category}`;

                window.location.href = url;
            }

            function printStock() {
                const search = document.getElementById('filter-stock-search').value;
                const location = document.getElementById('filter-stock-location').value;
                const category = document.getElementById('filter-stock-kategori').value;

                let url = `{{ route('stok.print') }}?t=${new Date().getTime()}`;
                if (search) url += `&search=${encodeURIComponent(search)}`;
                if (location) url += `&location_id=${location}`;
                if (category) url += `&category=${category}`;

                window.open(url, '_blank', 'width=800,height=600');
            }
        </script>
    @endsection
