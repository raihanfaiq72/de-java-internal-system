@extends('Layout.main')

@section('main')
<div class="page-wrapper">
    <div class="page-content">
        <div class="container-fluid">
            <!-- Header Section -->
            <div class="row align-items-center mb-4">
                <div class="col-sm-12 col-md-8">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary-subtle text-primary p-3 rounded-circle me-3">
                            <i class="iconoir-archive fs-3"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-bold">Dashboard Stok</h4>
                            <p class="text-muted mb-0 small text-wrap" style="max-width: 600px;">
                                Lihat persebaran stokmu disini. Kamu juga bisa lihat produk mana saja yang menguntungkan dan kurang menguntungkan bisnis kamu.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4 text-md-end mt-3 mt-md-0">
                    <div class="form-check form-switch d-inline-block text-start">
                        <label class="form-check-label small fw-bold text-muted d-block mb-1" for="auto-stock-toggle">Otomatis Kurangi/Tambahi Stok dari Invoice</label>
                        <div class="d-flex align-items-center">
                            <input class="form-check-input" type="checkbox" id="auto-stock-toggle" checked>
                            <span class="ms-2 badge bg-success-subtle text-success">ON</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="row mb-4 g-3 align-items-end">
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Stock</label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-0"><i class="iconoir-home-simple"></i></span>
                        <select id="filter-warehouse" class="form-select border-0" onchange="loadStockDashboard()">
                            <option value="">Gudang belum dipilih</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-5">
                    <label class="small fw-bold text-muted mb-1">Filter Produk</label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-0">Dari:</span>
                        <input type="date" id="filter-date-from" class="form-control border-0" onchange="loadStockDashboard()">
                        <span class="input-group-text bg-white border-0">Sampai:</span>
                        <input type="date" id="filter-date-to" class="form-control border-0" onchange="loadStockDashboard()">
                    </div>
                </div>
                <div class="col-md-auto">
                    <button class="btn btn-primary px-4 shadow-sm" onclick="loadStockDashboard()">
                        Terapkan
                    </button>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom-0 pb-0">
                            <ul class="nav nav-pills" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active fw-bold" data-bs-toggle="tab" href="#tab-dashboard" role="tab">
                                        <i class="iconoir-home-simple me-1"></i> Dashboard
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link fw-bold" data-bs-toggle="tab" href="#tab-persediaan" role="tab">
                                        <i class="iconoir-package me-1"></i> Persediaan
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link fw-bold" data-bs-toggle="tab" href="#tab-lokasi" role="tab">
                                        <i class="iconoir-map-pin me-1"></i> Lokasi
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link fw-bold" data-bs-toggle="tab" href="#tab-dokumen" role="tab">
                                        <i class="iconoir-page me-1"></i> Dokumen Stok
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="card-body pt-4">
                            <div class="tab-content">
                                {{-- TAB 1: DASHBOARD --}}
                                <div class="tab-pane fade show active" id="tab-dashboard" role="tabpanel">
                                    <div class="row g-4 mb-4">
                                        <div class="col-md-3">
                                            <div class="card border-0 shadow-sm bg-info-subtle border-start border-4 border-info">
                                                <div class="card-body">
                                                    <h6 class="text-info fw-bold mb-2">Order untuk Diterima</h6>
                                                    <h3 id="stat-pending-receive" class="fw-bold">0.00</h3>
                                                    <a href="{{ route('purchase') }}" class="small text-decoration-none text-info">Lihat Detail <i class="iconoir-arrow-right"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card border-0 shadow-sm bg-warning-subtle border-start border-4 border-warning">
                                                <div class="card-body">
                                                    <h6 class="text-warning fw-bold mb-2">Order untuk Dikirim</h6>
                                                    <h3 id="stat-pending-ship" class="fw-bold">0.00</h3>
                                                    <a href="{{ route('sales') }}" class="small text-decoration-none text-warning">Lihat Detail <i class="iconoir-arrow-right"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card border-0 shadow-sm bg-primary-subtle border-start border-4 border-primary">
                                                <div class="card-body">
                                                    <h6 class="text-primary fw-bold mb-2">Jumlah Kuantitas</h6>
                                                    <h3 id="stat-total-qty" class="fw-bold">0</h3>
                                                    <p class="small text-muted mb-0">Total unit di gudang</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card border-0 shadow-sm bg-success-subtle border-start border-4 border-success">
                                                <div class="card-body">
                                                    <h6 class="text-success fw-bold mb-2">Sisa Nilai Kuantitas</h6>
                                                    <h3 id="stat-inventory-value" class="fw-bold">Rp 0</h3>
                                                    <p class="small text-muted mb-0">Valuasi inventaris</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-4 mb-4">
                                        <div class="col-md-6">
                                            <div class="card border-0 shadow-sm">
                                                <div class="card-body">
                                                    <h6 class="fw-bold mb-3">Grafik Quantity Movement</h6>
                                                    <div id="qtyChart" style="min-height: 300px;"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card border-0 shadow-sm">
                                                <div class="card-body">
                                                    <h6 class="fw-bold mb-3">Grafik Value Movement</h6>
                                                    <div id="valueChart" style="min-height: 300px;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="card border-0 shadow-sm">
                                                <div class="card-header bg-success text-white">
                                                    <h6 class="mb-0 fw-bold">Produk Terlaku</h6>
                                                </div>
                                                <div class="card-body p-0">
                                                    <table class="table table-hover mb-0">
                                                        <thead class="small text-muted bg-light">
                                                            <tr><th>Produk</th><th class="text-end">Terkirim</th></tr>
                                                        </thead>
                                                        <tbody id="top-products-body"></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card border-0 shadow-sm">
                                                <div class="card-header bg-danger text-white">
                                                    <h6 class="mb-0 fw-bold">Produk Tidak Terlaku</h6>
                                                </div>
                                                <div class="card-body p-0">
                                                    <table class="table table-hover mb-0">
                                                        <thead class="small text-muted bg-light">
                                                            <tr><th>Produk</th><th class="text-end">Terkirim</th></tr>
                                                        </thead>
                                                        <tbody id="bottom-products-body"></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- TAB 2: PERSEDIAAN --}}
                                <div class="tab-pane fade" id="tab-persediaan" role="tabpanel">
                                    <div class="row g-2 mb-3 align-items-end">
                                        <div class="col-md-3">
                                            <label class="small fw-bold text-muted">Cari SKU / Nama</label>
                                            <input type="text" id="filter-stock-search" class="form-control form-control-sm"
                                                   placeholder="Ketik pencarian..." onkeyup="loadStockData()">
                                        </div>

                                        <div class="col-md-2">
                                            <label class="small fw-bold text-muted">Kategori</label>
                                            <select id="filter-stock-kategori" class="form-select form-select-sm" onchange="loadStockData()">
                                                <option value="">Semua Kategori</option>
                                            </select>
                                        </div>

                                        <div class="col-md-auto">
                                            <button onclick="loadStockData()" class="btn btn-sm btn-dark px-3">
                                                <i class="fa fa-sync me-1"></i> Refresh
                                            </button>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="120">SKU</th>
                                                    <th>Nama Produk</th>
                                                    <th>Kategori</th>
                                                    <th>Unit</th>
                                                    <th class="text-center">Stok Saat Ini</th>
                                                    <th width="150" class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="stock-table-body">
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">Memuat data...</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center px-2 mt-3">
                                        <span id="stock-pagination-info" class="text-muted small"></span>
                                        <nav>
                                            <ul class="pagination pagination-sm mb-0" id="stock-pagination-container"></ul>
                                        </nav>
                                    </div>
                                </div>

                                {{-- TAB 3: LOKASI --}}
                                <div class="tab-pane fade" id="tab-lokasi" role="tabpanel">
                                    <p class="text-muted text-center py-5">Modul Lokasi belum tersedia atau tidak ada data.</p>
                                </div>

                                {{-- TAB 4: DOKUMEN STOK --}}
                                <div class="tab-pane fade" id="tab-dokumen" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>Produk</th>
                                                    <th>Tipe</th>
                                                    <th>Qty</th>
                                                    <th>Referensi</th>
                                                    <th>Notes</th>
                                                </tr>
                                            </thead>
                                            <tbody id="mutation-table-body">
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">Memuat Riwayat...</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('Stock.Modal._AdjustStock')

@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    const API_STOCK_URL = '/api/stock-api';
    const API_CATEGORY_URL = '/api/product-categories-api';
    const API_MUTATION_URL = '/api/stock-api/mutations';
    const API_OFFICE_URL = '/api/office-api';

    let qtyChart = null;
    let valueChart = null;

    document.addEventListener('DOMContentLoaded', () => {
        loadOffices();
        loadKategoriStock();
        loadStockDashboard();
        
        // Load data based on active tab
        document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(tab => {
            tab.addEventListener('shown.bs.tab', (e) => {
                const target = e.target.getAttribute('href');
                if (target === '#tab-persediaan') loadStockData();
                if (target === '#tab-dokumen') loadMutationData();
                if (target === '#tab-dashboard') loadStockDashboard();
            });
        });
    });

    async function loadOffices() {
        const select = document.getElementById('filter-warehouse');
        try {
            const res = await fetch(API_OFFICE_URL);
            const result = await res.json();
            if (result.success) {
                result.data.forEach(item => {
                    select.insertAdjacentHTML('beforeend', `<option value="${item.id}">${item.name}</option>`);
                });
            }
        } catch (error) {
            console.warn('Gagal load offices:', error);
        }
    }

    async function loadStockDashboard() {
        try {
            const warehouse = document.getElementById('filter-warehouse').value;
            const from = document.getElementById('filter-date-from').value;
            const to = document.getElementById('filter-date-to').value;

            let url = `/api/stock-api/dashboard?`;
            if (warehouse) url += `warehouse_id=${warehouse}&`;
            if (from) url += `date_from=${from}&`;
            if (to) url += `date_to=${to}&`;

            const res = await fetch(url);
            const result = await res.json();
            if (result.success) {
                const d = result.data;
                const fmt = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 2 });
                
                document.getElementById('stat-pending-receive').innerText = fmt.format(d.overview.pending_receive).replace('Rp', '').trim();
                document.getElementById('stat-pending-ship').innerText = fmt.format(d.overview.pending_ship).replace('Rp', '').trim();
                document.getElementById('stat-total-qty').innerText = d.overview.total_qty;
                document.getElementById('stat-inventory-value').innerText = fmt.format(d.overview.inventory_value);

                renderCharts(d.charts);
                renderRankings(d.top_products, 'top-products-body');
                renderRankings(d.bottom_products, 'bottom-products-body');
            }
        } catch (error) {
            console.error('Gagal load dashboard:', error);
        }
    }

    function renderCharts(data) {
        const commonOptions = {
            chart: { type: 'area', height: 300, toolbar: { show: false }, zoom: { enabled: false } },
            stroke: { curve: 'smooth', width: 2 },
            xaxis: { categories: data.labels },
            dataLabels: { enabled: false },
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.1 } }
        };

        // Qty Chart
        const qtyOptions = {
            ...commonOptions,
            series: [
                { name: 'Amount Incoming', data: data.qty.incoming },
                { name: 'Amount Outgoing', data: data.qty.outgoing }
            ],
            colors: ['#0d6efd', '#dc3545'],
        };
        if (qtyChart) qtyChart.destroy();
        qtyChart = new ApexCharts(document.querySelector("#qtyChart"), qtyOptions);
        qtyChart.render();

        // Value Chart
        const valOptions = {
            ...commonOptions,
            series: [
                { name: 'Value Incoming', data: data.value.incoming },
                { name: 'Value Outgoing', data: data.value.outgoing }
            ],
            colors: ['#198754', '#6c757d'],
            yaxis: { labels: { formatter: (v) => v.toLocaleString('id-ID') } }
        };
        if (valueChart) valueChart.destroy();
        valueChart = new ApexCharts(document.querySelector("#valueChart"), valOptions);
        valueChart.render();
    }

    function renderRankings(products, containerId) {
        const tbody = document.getElementById(containerId);
        tbody.innerHTML = '';
        if (!products || products.length === 0) {
            tbody.innerHTML = '<tr><td colspan="2" class="text-center py-3 text-muted">No data</td></tr>';
            return;
        }

        products.forEach(p => {
            tbody.insertAdjacentHTML('beforeend', `
                <tr>
                    <td class="small fw-bold">${p.nama_produk}</td>
                    <td class="text-end fw-bold text-muted">${p.sent_qty || 0}</td>
                </tr>
            `);
        });
    }

    async function loadMutationData(url = API_MUTATION_URL) {
        const tbody = document.getElementById('mutation-table-body');
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4"><div class="spinner-border text-primary"></div></td></tr>';

        try {
            const res = await fetch(url);
            const result = await res.json();
            if (result.success) {
                renderMutationTable(result.data.data);
            }
        } catch (error) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Gagal memuat riwayat</td></tr>';
        }
    }

    function renderMutationTable(data) {
        const tbody = document.getElementById('mutation-table-body');
        tbody.innerHTML = '';
        if (!data || data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Belum ada mutasi</td></tr>';
            return;
        }

        data.forEach(item => {
            const badge = item.type === 'IN' ? 'bg-success' : 'bg-danger';
            tbody.insertAdjacentHTML('beforeend', `
                <tr>
                    <td>${new Date(item.created_at).toLocaleString('id-ID')}</td>
                    <td>${item.product?.nama_produk}<br><small class="text-muted">${item.product?.sku_kode}</small></td>
                    <td><span class="badge ${badge}">${item.type}</span></td>
                    <td class="fw-bold text-end">${item.qty}</td>
                    <td>${item.reference_type} #${item.reference_id || '-'}</td>
                    <td><small>${item.notes || '-'}</small></td>
                </tr>
            `);
        });
    }

    async function loadKategoriStock() {
        const select = document.getElementById('filter-stock-kategori');
        if (!select) return;
        try {
            const res = await fetch(API_CATEGORY_URL);
            const result = await res.json();
            if (result.success) {
                result.data.data.forEach(item => {
                    select.insertAdjacentHTML('beforeend', `<option value="${item.id}">${item.nama_kategori}</option>`);
                });
            }
        } catch (error) {
            console.error('Gagal load kategori:', error);
        }
    }

    async function loadStockData(url = API_STOCK_URL) {
        const tbody = document.getElementById('stock-table-body');
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4"><div class="spinner-border text-primary"></div></td></tr>';

        try {
            const search = document.getElementById('filter-stock-search').value;
            const kategori = document.getElementById('filter-stock-kategori').value;

            const fetchUrl = new URL(url, window.location.origin);
            if (search) fetchUrl.searchParams.append('search', search);
            if (kategori) fetchUrl.searchParams.append('category', kategori);

            const res = await fetch(fetchUrl);
            const result = await res.json();

            if (result.success) {
                renderStockTable(result.data.data);
                renderStockPagination(result.data);
            }
        } catch (error) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Gagal memuat data</td></tr>';
        }
    }

    function renderStockTable(data) {
        const tbody = document.getElementById('stock-table-body');
        tbody.innerHTML = '';

        if (!data || data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Data tidak ditemukan</td></tr>';
            return;
        }

        data.forEach(item => {
            const stockBadge = item.qty <= 5 ? 'bg-danger' : 'bg-success';
            tbody.insertAdjacentHTML('beforeend', `
                <tr>
                    <td class="fw-bold">${item.sku_kode}</td>
                    <td>${item.nama_produk}</td>
                    <td>${item.category?.nama_kategori ?? '-'}</td>
                    <td>${item.unit?.nama_unit ?? '-'}</td>
                    <td class="text-center">
                        <span class="badge ${stockBadge}">${item.qty ?? 0}</span>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-xs btn-primary shadow-sm" onclick="adjustStock(${item.id})">
                            <i class="fa fa-edit me-1"></i> SESUAIKAN
                        </button>
                    </td>
                </tr>
            `);
        });
    }

    function renderStockPagination(meta) {
        document.getElementById('stock-pagination-info').innerText =
            `Menampilkan ${meta.from || 0} ke ${meta.to || 0} dari ${meta.total} data`;

        const container = document.getElementById('stock-pagination-container');
        container.innerHTML = '';

        meta.links.forEach(link => {
            const active = link.active ? 'active' : '';
            const disabled = !link.url ? 'disabled' : '';
            const label = link.label.replace('&laquo;', '').replace('&raquo;', '');

            container.insertAdjacentHTML('beforeend', `
                <li class="page-item ${active} ${disabled}">
                    <a class="page-link shadow-none" href="javascript:void(0)" onclick="loadStockData('${link.url}')">
                        ${label}
                    </a>
                </li>
            `);
        });
    }

    async function adjustStock(id) {
        const detailRes = await fetch(`/api/product-api/${id}`);
        const result = await detailRes.json();

        if (!result.success) return alert('Gagal mengambil data produk');

        const p = result.data;
        document.getElementById('adjust-stock-id').value = p.id;
        document.getElementById('adjust-stock-sku').innerText = p.sku_kode;
        document.getElementById('adjust-stock-name').innerText = p.nama_produk;
        document.getElementById('adjust-stock-current').value = p.qty;
        document.getElementById('adjust-stock-new').value = p.qty;
        document.getElementById('adjust-stock-note').value = '';

        new bootstrap.Modal('#modalAdjustStock').show();
    }

    async function submitAdjustStock() {
        const id = document.getElementById('adjust-stock-id').value;
        const newQty = document.getElementById('adjust-stock-new').value;
        const notes = document.getElementById('adjust-stock-note').value;

        const res = await fetch(`/api/stock-api/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ qty: newQty, notes: notes })
        });

        const result = await res.json();
        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalAdjustStock')).hide();
            loadStockDashboard();
            loadStockData();
        } else {
            alert(result.message);
        }
    }
</script>
@endpush

@push('css')
<style>
    .btn-xs {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        border-radius: 0.2rem;
    }
</style>
@endpush
