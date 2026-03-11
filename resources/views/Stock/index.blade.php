@extends('Layout.main')

@section('main')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="container-fluid">
                <!-- Header -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="page-title mb-1">Manajemen Stok</h4>
                                <p class="text-muted mb-0">Monitor persediaan, lokasi, dan mutasi barang.</p>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-primary" onclick="loadStockData()">
                                    <i class="iconoir-refresh me-1"></i> Refresh
                                </button>
                                <button class="btn btn-primary"
                                    onclick="window.location.href='{{ route('report.stock') }}'">
                                    <i class="iconoir-stats-report me-1"></i> Laporan Lengkap
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-circle">
                                        <i class="iconoir-box-iso fs-3 text-primary"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="text-muted text-uppercase fw-bold small mb-1">Total Produk</h6>
                                        <h3 class="mb-0 fw-bold" id="stat-total-items">0</h3>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between small text-muted">
                                    <span>Total Item Fisik: <span id="stat-total-qty"
                                            class="fw-bold text-dark">0</span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0 bg-danger bg-opacity-10 p-3 rounded-circle">
                                        <i class="iconoir-warning-triangle fs-3 text-danger"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="text-muted text-uppercase fw-bold small mb-1">Perlu Restock</h6>
                                        <h3 class="mb-0 fw-bold text-danger" id="stat-restock-count">0</h3>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between small text-muted">
                                    <span>Habis: <span id="stat-out-stock" class="fw-bold text-danger">0</span></span>
                                    <span>Menipis: <span id="stat-low-stock" class="fw-bold text-warning">0</span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0 bg-success bg-opacity-10 p-3 rounded-circle">
                                        <i class="iconoir-wallet fs-3 text-success"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="text-muted text-uppercase fw-bold small mb-1">Valuasi Stok</h6>
                                        <h3 class="mb-0 fw-bold text-success" id="stat-inventory-value">Rp 0</h3>
                                    </div>
                                </div>
                                <small class="text-muted d-block">Estimasi nilai aset saat ini</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0 bg-info bg-opacity-10 p-3 rounded-circle">
                                        <i class="iconoir-delivery-truck fs-3 text-info"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="text-muted text-uppercase fw-bold small mb-1">Akan Masuk</h6>
                                        <h3 class="mb-0 fw-bold" id="stat-pending-receive">Rp 0</h3>
                                    </div>
                                </div>
                                <small class="text-muted d-block">Dari Pembelian (PO)</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Tabs -->
                <style>
                    .nav-tabs-finance .nav-link.active {
                        color: var(--bs-primary) !important;
                    }
                </style>
                <div class="card shadow-sm border-0">
                    <div
                        class="card-header bg-white border-bottom py-0 px-4 d-flex align-items-center justify-content-between">
                        <ul class="nav nav-tabs nav-tabs-finance" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active fw-bold py-3" data-bs-toggle="tab" href="#tab-persediaan"
                                    role="tab">
                                    <i class="fa fa-cubes me-2"></i>Daftar Stok
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link fw-bold py-3" data-bs-toggle="tab" href="#tab-lokasi" role="tab">
                                    <i class="fa fa-location-pin me-2"></i>Lokasi & Gudang
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link fw-bold py-3" data-bs-toggle="tab" href="#tab-dokumen" role="tab">
                                    <i class="fa fa-clock-rotate-left me-2"></i>Riwayat Mutasi
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body p-4">
                        <div class="tab-content">
                            <!-- TAB 1: DAFTAR STOK -->
                            <div class="tab-pane fade show active" id="tab-persediaan" role="tabpanel">
                                <!-- Filter Section -->
                                <div class="card bg-light border-0 mb-4">
                                    <div class="card-body p-3">
                                        <div class="row g-3 align-items-center">
                                            <div class="col-md-4">
                                                <div class="form-floating">
                                                    <input type="text" id="filter-stock-search"
                                                        class="form-control border-0 shadow-none" placeholder="Cari..."
                                                        onkeyup="handleFilterChange()">
                                                    <label for="filter-stock-search">Cari Nama Produk / SKU</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-floating">
                                                    <select id="filter-stock-location"
                                                        class="form-select border-0 shadow-sm"
                                                        onchange="handleFilterChange()">
                                                        <option value="">Semua Lokasi</option>
                                                    </select>
                                                    <label for="filter-stock-location">Lokasi</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-floating">
                                                    <select id="filter-stock-kategori"
                                                        class="form-select border-0 shadow-sm"
                                                        onchange="handleFilterChange()">
                                                        <option value="">Semua Kategori</option>
                                                    </select>
                                                    <label for="filter-stock-kategori">Kategori</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-floating">
                                                    <select id="filter-stock-status" class="form-select border-0 shadow-sm"
                                                        onchange="handleFilterChange()">
                                                        <option value="">Semua Status</option>
                                                        <option value="safe">Aman</option>
                                                        <option value="low">Menipis (< 5)</option>
                                                        <option value="empty">Habis (0)</option>
                                                    </select>
                                                    <label for="filter-stock-status">Status Stok</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2 text-end">
                                                <div class="btn-group shadow-sm w-100 h-100">
                                                    <button
                                                        class="btn btn-white text-muted h-100 d-flex align-items-center justify-content-center"
                                                        title="Export Excel" onclick="exportStock()"><i
                                                            class="iconoir-download fs-4"></i></button>
                                                    <button
                                                        class="btn btn-white text-muted h-100 d-flex align-items-center justify-content-center"
                                                        title="Print" onclick="printStock()"><i
                                                            class="iconoir-printer fs-4"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Table -->
                                <div class="table-responsive rounded border-0 shadow-sm">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="bg-light text-muted small text-uppercase">
                                            <tr>
                                                <th class="py-3 ps-4 border-0" style="width: 35%;">Produk</th>
                                                <th class="py-3 border-0 text-center" style="width: 15%;">Status</th>
                                                <th class="py-3 border-0 text-center" style="width: 15%;">Stok Fisik</th>
                                                <th class="py-3 border-0 text-end" style="width: 20%;">Nilai Aset (IDR)</th>
                                                <th class="py-3 border-0 text-end pe-4" style="width: 15%;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="stock-table-body" class="bg-white">
                                            <tr>
                                                <td colspan="5" class="text-center py-5 text-muted">
                                                    <div class="spinner-border text-primary mb-2" role="status"></div>
                                                    <p class="mb-0 small">Sedang memuat data stok...</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top"
                                    id="persediaan-pagination-wrapper">
                                    <span id="stock-pagination-info" class="text-muted small"></span>
                                    <nav>
                                        <ul class="pagination pagination-sm mb-0" id="stock-pagination-container"></ul>
                                    </nav>
                                </div>
                            </div>

                            <!-- TAB 2: LOKASI -->
                            <div class="tab-pane fade" id="tab-lokasi" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div>
                                        <h5 class="fw-bold mb-1">Daftar Lokasi Penyimpanan</h5>
                                        <p class="text-muted small mb-0">Kelola gudang, rak, dan area penyimpanan stok.</p>
                                    </div>
                                    <button class="btn btn-primary btn-sm shadow-sm" onclick="showCreateLocationModal()">
                                        <i class="iconoir-plus-circle me-1"></i> Tambah Lokasi
                                    </button>
                                </div>

                                <div class="table-responsive rounded border-0 shadow-sm">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="bg-light text-muted small text-uppercase">
                                            <tr>
                                                <th class="py-3 ps-4 border-0">Nama Lokasi</th>
                                                <th class="py-3 border-0">Tipe</th>
                                                <th class="py-3 border-0 text-end pe-4">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="location-table-body" class="bg-white">
                                            <tr>
                                                <td colspan="3" class="text-center py-5 text-muted">Memuat data lokasi...
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
                                <div class="card bg-light border-0 mb-4">
                                    <div class="card-body p-3">
                                        <div class="row g-3 align-items-center">
                                            <div class="col-md-4">
                                                <div class="form-floating">
                                                    <input type="text" id="filter-mutation-search"
                                                        class="form-control border-0 shadow-none"
                                                        placeholder="Cari produk..." onkeyup="loadMutationData()">
                                                    <label for="filter-mutation-search">Cari Produk / SKU</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-floating">
                                                    <input type="date" id="filter-mutation-start"
                                                        class="form-control border-0 shadow-none"
                                                        onchange="loadMutationData()">
                                                    <label for="filter-mutation-start">Dari Tanggal</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-floating">
                                                    <input type="date" id="filter-mutation-end"
                                                        class="form-control border-0 shadow-none"
                                                        onchange="loadMutationData()">
                                                    <label for="filter-mutation-end">Sampai Tanggal</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-floating">
                                                    <select id="filter-mutation-type" class="form-select border-0 shadow-sm"
                                                        onchange="loadMutationData()">
                                                        <option value="">Semua Tipe</option>
                                                        <option value="IN">Masuk (IN)</option>
                                                        <option value="OUT">Keluar (OUT)</option>
                                                    </select>
                                                    <label for="filter-mutation-type">Tipe Mutasi</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-floating">
                                                    <select id="filter-mutation-location"
                                                        class="form-select border-0 shadow-sm"
                                                        onchange="loadMutationData()">
                                                        <option value="">Semua Lokasi</option>
                                                    </select>
                                                    <label for="filter-mutation-location">Lokasi</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive rounded border-0 shadow-sm">
                                    <table class="table table-hover mb-0 align-middle">
                                        <thead class="bg-light text-muted small text-uppercase">
                                            <tr>
                                                <th class="py-3 ps-4 border-0">Tanggal</th>
                                                <th class="py-3 border-0">Produk</th>
                                                <th class="py-3 border-0 text-center">Tipe</th>
                                                <th class="py-3 border-0 text-center">Qty</th>
                                                <th class="py-3 border-0">Lokasi</th>
                                                <th class="py-3 border-0">Aksi</th>
                                                <th class="py-3 border-0">User</th>
                                                <th class="py-3 border-0">Catatan</th>
                                            </tr>
                                        </thead>
                                        <tbody id="mutation-table-body" class="bg-white">
                                            <tr>
                                                <td colspan="8" class="text-center py-5 text-muted">Memuat riwayat...</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- Mutation Pagination -->
                                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                    <span id="mutation-pagination-info" class="text-muted small"></span>
                                    <nav>
                                        <ul class="pagination pagination-sm mb-0" id="mutation-pagination-container"></ul>
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

    <!-- FIFO Modal (New) -->
    <div class="modal fade" id="modalFifo" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Stok (FIFO)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
        let debounceTimer;

        // Init
        document.addEventListener('DOMContentLoaded', function () {
            loadStockDashboard();
            loadStockData();
            loadLocations(); // For filter dropdown
            loadMutationData();

            // Initial load of categories for filter
            fetchCategories();
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
                        document.getElementById('stat-total-items').innerText = new Intl.NumberFormat('id-ID').format(ov.total_items || 0);
                        document.getElementById('stat-total-qty').innerText = new Intl.NumberFormat('id-ID').format(ov.total_qty || 0);

                        document.getElementById('stat-inventory-value').innerText = formatCurrency(ov.inventory_value || 0);

                        document.getElementById('stat-pending-receive').innerText = formatCurrency(ov.pending_receive || 0);

                        // Restock Logic
                        const low = ov.low_stock_count || 0;
                        const out = ov.out_of_stock_count || 0;
                        document.getElementById('stat-restock-count').innerText = (low + out);
                        document.getElementById('stat-low-stock').innerText = low;
                        document.getElementById('stat-out-stock').innerText = out;
                    }
                })
                .catch(err => console.error('Error loading dashboard:', err));
        }

        // --- STOCK LIST ---
        function handleFilterChange() {
            // Debounce for search input
            const tbody = document.getElementById('stock-table-body');
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-5 text-muted"><div class="spinner-border text-primary mb-2"></div><p>Memuat data...</p></td></tr>';

            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                currentPage = 1;
                loadStockData();
                loadStockDashboard(); // Update stats based on location filter if needed
            }, 500);
        }

        function loadStockData(page = 1) {
            currentPage = page;
            const search = document.getElementById('filter-stock-search').value;
            const location = document.getElementById('filter-stock-location').value;
            const category = document.getElementById('filter-stock-kategori').value;
            const status = document.getElementById('filter-stock-status') ? document.getElementById('filter-stock-status').value : '';

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
                            // Calculate status
                            const locationQty = item.location_qty;
                            const globalQty = item.qty;

                            // Use location_qty if available (even if 0/null), otherwise global
                            let qty = globalQty;
                            if (location !== '') {
                                qty = locationQty !== null ? parseFloat(locationQty) : 0;
                            } else {
                                qty = parseFloat(globalQty);
                            }

                            let statusBadge = '';
                            if (qty <= 0) {
                                statusBadge = '<span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3">Habis</span>';
                            } else if (qty < 5) {
                                statusBadge = '<span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3">Menipis</span>';
                            } else {
                                statusBadge = '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">Aman</span>';
                            }

                            // Optional: Client-side filter for status (simple implementation)
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
                                                                                                                                <small class="text-muted d-block">${item.sku_kode} <span class="mx-1">•</span> <span class="badge bg-light text-secondary border">${item.category?.nama_kategori || '-'}</span></small>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </td>
                                                                                                                    <td class="text-center">${statusBadge}</td>
                                                                                                                    <td class="text-center fw-bold fs-6">${new Intl.NumberFormat('id-ID').format(qty)} <small class="text-muted fw-normal">${item.satuan}</small></td>
                                                                                                                    <td class="text-end text-muted font-monospace">${formatCurrency(qty * item.harga_beli)}</td>
                                                                                                                    <td class="text-end pe-4">
                                                                                                                        <div class="btn-group">
                                                                                                                            <button class="btn btn-sm btn-light border" onclick="showFifoDetail(${item.id})" title="Detail FIFO"><i class="iconoir-list"></i></button>
                                                                                                                            <button class="btn btn-sm btn-light border" onclick="showAdjustStockModal(${item.id}, '${item.nama_produk}', '${item.sku_kode}', ${qty})" title="Adjust"><i class="iconoir-edit-pencil"></i></button>
                                                                                                                        </div>
                                                                                                                    </td>
                                                                                                                `;
                            tbody.appendChild(tr);
                        });

                        if (tbody.children.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-5 text-muted">Tidak ada data yang cocok dengan filter status.</td></tr>';
                        }

                        renderPagination(res.data, 'stock-pagination-container', 'stock-pagination-info', loadStockData);
                    } else {
                        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-5 text-muted">Belum ada data stok.</td></tr>';
                    }
                })
                .catch(err => {
                    console.error(err);
                    document.getElementById('stock-table-body').innerHTML = '<tr><td colspan="5" class="text-center py-5 text-danger">Gagal memuat data.</td></tr>';
                });
        }

        // --- LOCATIONS ---
        function loadLocations() {
            // Populate Dropdown Filter
            fetch('{{ route('stock-location-api.index') }}')
                .then(res => res.json())
                .then(data => {
                    const select = document.getElementById('filter-stock-location');
                    const selectMut = document.getElementById('filter-mutation-location');

                    // Keep first option
                    const first = select.firstElementChild;
                    select.innerHTML = '';
                    select.appendChild(first);

                    if (selectMut) {
                        const firstMut = selectMut.firstElementChild;
                        selectMut.innerHTML = '';
                        selectMut.appendChild(firstMut);
                    }

                    data.data.forEach(loc => {
                        const opt = document.createElement('option');
                        opt.value = loc.id;
                        opt.textContent = loc.name;
                        select.appendChild(opt);

                        if (selectMut) {
                            const optMut = document.createElement('option');
                            optMut.value = loc.id;
                            optMut.textContent = loc.name;
                            selectMut.appendChild(optMut);
                        }
                    });

                    // Also populate table
                    renderLocationTable(data.data);
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
            const location = document.getElementById('filter-mutation-location') ? document.getElementById('filter-mutation-location').value : '';
            const search = document.getElementById('filter-mutation-search') ? document.getElementById('filter-mutation-search').value : '';

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
                            const badge = isInc
                                ? '<span class="badge bg-success-subtle text-success">MASUK</span>'
                                : '<span class="badge bg-danger-subtle text-danger">KELUAR</span>';

                            const actor = m.actor?.name || m.actor?.username || '-';
                            const action = m.action_label || m.reference_type || 'Mutasi Stok';

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
                                                                                                                    <td><small class="text-muted">${action}</small></td>
                                                                                                                    <td><small class="text-muted">${actor}</small></td>
                                                                                                                    <td><small>${m.notes || '-'}</small></td>
                                                                                                                `;
                            tbody.appendChild(tr);
                        });

                        renderPagination(res.data, 'mutation-pagination-container', 'mutation-pagination-info', loadMutationData);
                    } else {
                        tbody.innerHTML = '<tr><td colspan="8" class="text-center py-5 text-muted">Belum ada riwayat mutasi.</td></tr>';
                    }
                });
        }

        // --- MODAL FUNCTIONS (Restored) ---

        // Adjust Stock
        function showAdjustStockModal(id, name, sku, currentQty) {
            document.getElementById('adjust-stock-id').value = id;
            document.getElementById('adjust-stock-sku').innerText = sku;
            document.getElementById('adjust-stock-name').innerText = name;
            document.getElementById('adjust-stock-current').value = currentQty;
            document.getElementById('adjust-stock-new').value = currentQty;
            document.getElementById('adjust-stock-note').value = '';

            // Populate Location Dropdown from Filter options
            const filterLoc = document.getElementById('filter-stock-location');
            const modalLoc = document.getElementById('adjust-stock-location');
            modalLoc.innerHTML = '<option value="">-- Pilih Lokasi (Opsional) --</option>';

            // Copy options skipping the first one ("Semua Lokasi")
            for (let i = 1; i < filterLoc.options.length; i++) {
                const opt = filterLoc.options[i];
                const newOpt = document.createElement('option');
                newOpt.value = opt.value;
                newOpt.textContent = opt.textContent;
                modalLoc.appendChild(newOpt);
            }

            // Set selected if filter is active
            if (filterLoc.value) {
                modalLoc.value = filterLoc.value;
            }

            const modal = new bootstrap.Modal(document.getElementById('modalAdjustStock'));
            modal.show();
        }

        function submitAdjustStock() {
            const id = document.getElementById('adjust-stock-id').value;
            const newQty = document.getElementById('adjust-stock-new').value;
            const note = document.getElementById('adjust-stock-note').value;
            const locationId = document.getElementById('adjust-stock-location').value;

            if (!id) return;

            fetch(`{{ url('api/stock-api') }}/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    qty: newQty,
                    notes: note,
                    stock_location_id: locationId || null
                })
            })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        bootstrap.Modal.getInstance(document.getElementById('modalAdjustStock')).hide();
                        loadStockData(currentPage);
                        loadStockDashboard();
                        alert('Stok berhasil diperbarui');
                    } else {
                        alert('Gagal: ' + res.message);
                    }
                })
                .catch(err => alert('Terjadi kesalahan'));
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
                body: JSON.stringify({ name, type })
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
                    data.data.forEach(cat => {
                        const opt = document.createElement('option');
                        opt.value = cat.id;
                        opt.textContent = cat.nama_kategori;
                        select.appendChild(opt);
                    });
                });
        }

        function formatCurrency(value) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
        }

        function renderPagination(data, containerId, infoId, loadFunction) {
            const container = document.getElementById(containerId);
            const info = document.getElementById(infoId);

            info.innerText = `Menampilkan ${data.from || 0} sampai ${data.to || 0} dari ${data.total} data`;

            let html = '';
            if (data.prev_page_url) {
                html += `<li class="page-item"><button class="page-link" onclick="${loadFunction.name}(${data.current_page - 1})">Prev</button></li>`;
            } else {
                html += `<li class="page-item disabled"><button class="page-link">Prev</button></li>`;
            }

            html += `<li class="page-item active"><button class="page-link">${data.current_page}</button></li>`;

            if (data.next_page_url) {
                html += `<li class="page-item"><button class="page-link" onclick="${loadFunction.name}(${data.current_page + 1})">Next</button></li>`;
            } else {
                html += `<li class="page-item disabled"><button class="page-link">Next</button></li>`;
            }

            container.innerHTML = html;
        }

        // --- FIFO MODAL ---
        let fifoModalInstance = null;

        function showFifoDetail(productId) {
            const modalEl = document.getElementById('modalFifo');
            if (!fifoModalInstance) {
                fifoModalInstance = new bootstrap.Modal(modalEl);
            }

            document.getElementById('fifo-content').innerHTML = '<div class="text-center p-5"><div class="spinner-border text-primary"></div></div>';
            fifoModalInstance.show();

            const locationId = document.getElementById('filter-stock-location').value;
            let url = `{{ url('api/stock-api') }}/${productId}/fifo`;
            if (locationId) url += `?location_id=${locationId}`;

            fetch(url)
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        let html = `
                                                                                                                <div class="alert alert-light border mb-3">
                                                                                                                    <h6 class="mb-0 fw-bold">${res.data.product.nama_produk}</h6>
                                                                                                                    <small class="text-muted">Total Stok: ${new Intl.NumberFormat('id-ID').format(res.data.total_stock)} ${res.data.product.satuan}</small>
                                                                                                                </div>
                                                                                                                <h6 class="small fw-bold text-uppercase text-muted mb-2">Batch Stok (FIFO)</h6>
                                                                                                                <div class="table-responsive border rounded">
                                                                                                                    <table class="table table-sm table-striped mb-0">
                                                                                                                        <thead class="bg-light">
                                                                                                                            <tr>
                                                                                                                                <th>Tgl Masuk</th>
                                                                                                                                <th>Harga Beli</th>
                                                                                                                                <th>Sisa Qty</th>
                                                                                                                                <th>Lokasi</th>
                                                                                                                            </tr>
                                                                                                                        </thead>
                                                                                                                        <tbody>
                                                                                                            `;

                        if (res.data.batches.length > 0) {
                            res.data.batches.forEach(b => {
                                html += `
                                                                                                                        <tr>
                                                                                                                            <td>${new Date(b.created_at).toLocaleDateString('id-ID')}</td>
                                                                                                                            <td>${formatCurrency(b.cost_price)}</td>
                                                                                                                            <td class="fw-bold text-success">${b.remaining_qty}</td>
                                                                                                                            <td>${b.stock_location?.name || '-'}</td>
                                                                                                                        </tr>
                                                                                                                    `;
                            });
                        } else {
                            html += `<tr><td colspan="4" class="text-center text-muted py-3">Tidak ada batch aktif.</td></tr>`;
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
