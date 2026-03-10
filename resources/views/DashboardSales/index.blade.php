@extends('Layout.main')

@section('main')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

<div class="page-wrapper">
    <div class="page-content">
        <div class="container-fluid">
            
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box d-md-flex justify-content-between align-items-center mb-3">
                        <h4 class="page-title">Dashboard Analisis Penjualan</h4>
                        <div class="text-muted small">Data diperbarui: {{ date('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <form action="" method="GET" class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="small fw-bold text-muted">Periode Tanggal</label>
                            <div class="input-group input-group-sm">
                                <input type="date" name="start_date" class="form-control" value="">
                                <span class="input-group-text">s/d</span>
                                <input type="date" name="end_date" class="form-control" value="">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="small fw-bold text-muted">Cari Pelanggan</label>
                            <select name="mitra_id" id="tom-mitra" placeholder="Ketik nama PT atau orang...">
                                <option value="">Semua Pelanggan</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="small fw-bold text-muted">Status Pembayaran</label>
                            <select name="status" id="tom-status">
                                <option value="">Semua Status</option>
                                <option value="NOT_PAID">Belum Lunas (Bukan Paid)</option>
                                <option value="Unpaid">Unpaid</option>
                                <option value="Partially Paid">Partially Paid</option>
                                <option value="Paid">Paid</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-dark w-100 fw-bold">
                                <i class="fa fa-filter me-1"></i> TERAPKAN
                            </button>
                        </div>
                        <div class="col-md-1">
                            <a href="{{ url()->current() }}" class="btn btn-outline-secondary w-100">
                                <i class="fa fa-sync"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 bg-primary shadow-sm">
                        <div class="card-body text-white">
                            <p class="mb-1 opacity-75 small text-uppercase">Total Sales Periode</p>
                            <h3 class="fw-bold">Rp <span id="dsTotalSales">0</span></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 bg-danger shadow-sm">
                        <div class="card-body text-white">
                            <p class="mb-1 opacity-75 small text-uppercase">Total Piutang Berjalan</p>
                            <h3 class="fw-bold">Rp <span id="dsTotalPiutang">0</span></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 bg-white shadow-sm">
                        <div class="card-body">
                            <p class="mb-1 text-muted small text-uppercase">Rata-rata Umur Nota</p>
                            <h3 class="fw-bold text-dark"><span id="dsAvgAging">0</span> Hari</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0">Daftar Nota Penjualan</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">No. Invoice</th>
                                    <th>Pelanggan</th>
                                    <th>Tanggal</th>
                                    <th class="text-end">Total Akhir</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aging</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="dsInvoicesTbody"></tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="small text-muted" id="dsPageInfo">Halaman 1 dari 1</div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="dsPrevPage">Sebelumnya</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="dsNextPage">Berikutnya</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header bg-dark text-white">
                <h6 class="modal-title">Rincian Nota: <span id="textNoInv"></span></h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Item Produk</th>
                            <th class="text-center">Kuantitas</th>
                            <th class="text-end">Harga</th>
                            <th class="text-end pe-3">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="listDetail"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

@push('js')
<script>
    const DASHBOARD_SALES_API = "{{ route('dashboard-sales-api.index') }}";
    const DASHBOARD_SALES_DETAIL_API = "{{ route('dashboard-sales-api.detail', ['id' => '__ID__']) }}";

    function rupiah(n) {
        return Number(n || 0).toLocaleString('id-ID');
    }

    function setQueryParam(key, value) {
        const url = new URL(window.location.href);
        if (value === null || value === undefined || value === '') url.searchParams.delete(key);
        else url.searchParams.set(key, value);
        url.searchParams.delete('page');
        window.location.href = url.toString();
    }

    function setPage(page) {
        const url = new URL(window.location.href);
        url.searchParams.set('page', String(page));
        window.location.href = url.toString();
    }

    function badgeClass(status) {
        if (status === 'Paid') return 'bg-success';
        if (status === 'Unpaid') return 'bg-danger';
        if (status === 'Partially Paid') return 'bg-warning text-dark';
        if (status === 'Overdue') return 'bg-dark';
        return 'bg-secondary';
    }

    let tsMitra = null;
    let tsStatus = null;
    let lastPage = 1;
    let currentPage = 1;

    async function loadSalesData() {
        const qs = new URLSearchParams(window.location.search);
        const startDate = qs.get('start_date') || new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().slice(0, 10);
        const endDate = qs.get('end_date') || new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0).toISOString().slice(0, 10);
        const mitraId = qs.get('mitra_id') || '';
        const status = qs.get('status') || '';
        currentPage = parseInt(qs.get('page') || '1', 10);
        if (!currentPage || currentPage < 1) currentPage = 1;

        const form = document.querySelector('form');
        if (form) {
            form.querySelector('input[name="start_date"]').value = startDate;
            form.querySelector('input[name="end_date"]').value = endDate;
        }

        const apiUrl = new URL(DASHBOARD_SALES_API, window.location.origin);
        apiUrl.searchParams.set('start_date', startDate);
        apiUrl.searchParams.set('end_date', endDate);
        if (mitraId) apiUrl.searchParams.set('mitra_id', mitraId);
        if (status) apiUrl.searchParams.set('status', status);
        apiUrl.searchParams.set('per_page', '10');
        apiUrl.searchParams.set('page', String(currentPage));

        const res = await fetch(apiUrl);
        const json = await res.json();
        if (!json.success) throw new Error(json.message || 'Gagal memuat data');

        const d = json.data || {};
        const summary = d.summary || {};
        document.getElementById('dsTotalSales').textContent = rupiah(summary.total_sales || 0);
        document.getElementById('dsTotalPiutang').textContent = rupiah(summary.total_piutang || 0);
        document.getElementById('dsAvgAging').textContent = summary.avg_aging || 0;

        const listMitra = Array.isArray(d.listMitra) ? d.listMitra : [];
        const mitraSelect = document.getElementById('tom-mitra');
        if (mitraSelect && !tsMitra) {
            listMitra.forEach(m => {
                const opt = document.createElement('option');
                opt.value = String(m.id);
                opt.textContent = m.nama;
                mitraSelect.appendChild(opt);
            });
            tsMitra = new TomSelect("#tom-mitra", { create: false, sortField: { field: "text", order: "asc" } });
            if (mitraId) tsMitra.setValue(mitraId, true);
        } else if (tsMitra) {
            if (mitraId) tsMitra.setValue(mitraId, true);
            else tsMitra.clear(true);
        }

        if (!tsStatus) {
            tsStatus = new TomSelect("#tom-status", { create: false });
            if (status) tsStatus.setValue(status, true);
        } else {
            if (status) tsStatus.setValue(status, true);
            else tsStatus.clear(true);
        }

        const inv = d.invoices || {};
        const data = Array.isArray(inv.data) ? inv.data : [];
        lastPage = inv.last_page || 1;
        currentPage = inv.current_page || currentPage;
        document.getElementById('dsPageInfo').textContent = `Halaman ${currentPage} dari ${lastPage}`;
        document.getElementById('dsPrevPage').disabled = currentPage <= 1;
        document.getElementById('dsNextPage').disabled = currentPage >= lastPage;

        const tbody = document.getElementById('dsInvoicesTbody');
        tbody.innerHTML = '';
        if (!data.length) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80" class="mb-3 opacity-25">
                        <p class="text-muted">Tidak ada data penjualan ditemukan.</p>
                    </td>
                </tr>
            `;
            return;
        }

        data.forEach((row) => {
            const tgl = row.tgl_invoice ? new Date(row.tgl_invoice).toLocaleDateString('id-ID') : '-';
            const aging = Number(row.umur_nota || 0);
            const statusText = row.status_pembayaran || '-';
            const agingClass = (aging > 30 && statusText !== 'Paid') ? 'text-danger fw-bold' : '';
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="ps-3 fw-bold text-primary">${row.nomor_invoice || '-'}</td>
                <td>${row.nama_pelanggan || '-'}</td>
                <td>${tgl}</td>
                <td class="text-end fw-bold">Rp ${rupiah(row.total_akhir || 0)}</td>
                <td class="text-center"><span class="badge ${badgeClass(statusText)}">${statusText}</span></td>
                <td class="text-center"><span class="${agingClass}">${aging} Hari</span></td>
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-primary px-3 ds-detail-btn" data-id="${row.id}" data-no="${row.nomor_invoice || ''}">Rincian</button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        document.querySelectorAll('.ds-detail-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                loadDetail(btn.dataset.id, btn.dataset.no);
            });
        });
    }

    function loadDetail(id, noInv) {
        document.getElementById('textNoInv').innerText = noInv;
        const container = document.getElementById('listDetail');
        container.innerHTML = '<tr><td colspan="4" class="text-center py-4">Memuat data...</td></tr>';
        
        const modal = new bootstrap.Modal(document.getElementById('modalDetail'));
        modal.show();

        fetch(DASHBOARD_SALES_DETAIL_API.replace('__ID__', id))
            .then(res => {
                if (!res.ok) throw new Error('Data tidak dapat dimuat');
                return res.json();
            })
            .then(json => {
                if (!json.success) throw new Error(json.message || 'Data tidak dapat dimuat');
                const data = json.data || [];
                container.innerHTML = '';
                if(data.length === 0) {
                    container.innerHTML = '<tr><td colspan="4" class="text-center">Nota ini tidak memiliki rincian item.</td></tr>';
                    return;
                }
                data.forEach(item => {
                    let namaProd = item.nama_produk || item.nama_produk_manual || 'Produk Umum';
                    container.innerHTML += `
                        <tr>
                            <td class="ps-3">
                                <span class="fw-bold d-block text-dark">${item.sku_kode || 'TANPA SKU'}</span>
                                <small class="text-muted">${namaProd}</small>
                            </td>
                            <td class="text-center">${parseFloat(item.qty)}</td>
                            <td class="text-end">Rp ${parseFloat(item.harga_satuan).toLocaleString('id-ID')}</td>
                            <td class="text-end pe-3 fw-bold text-dark">Rp ${parseFloat(item.total_harga_item).toLocaleString('id-ID')}</td>
                        </tr>
                    `;
                });
            })
            .catch(err => {
                container.innerHTML = `<tr><td colspan="4" class="text-center text-danger py-4">${err.message}</td></tr>`;
            });
    }

    document.addEventListener("DOMContentLoaded", function() {
        const qs = new URLSearchParams(window.location.search);
        const form = document.querySelector('form');
        if (form) {
            const startDefault = new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().slice(0, 10);
            const endDefault = new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0).toISOString().slice(0, 10);
            form.querySelector('input[name="start_date"]').value = qs.get('start_date') || startDefault;
            form.querySelector('input[name="end_date"]').value = qs.get('end_date') || endDefault;
            form.querySelector('#tom-status').value = qs.get('status') || '';
        }
        document.getElementById('dsPrevPage').addEventListener('click', () => setPage(Math.max(1, currentPage - 1)));
        document.getElementById('dsNextPage').addEventListener('click', () => setPage(Math.min(lastPage, currentPage + 1)));
        loadSalesData().catch(e => console.error(e));
    });
</script>
@endpush

<style>
    .pagination { margin-bottom: 0; }
    .table thead th { font-size: 11px; letter-spacing: 0.05rem; }
    .ts-control { border-radius: 0.375rem !important; }
</style>
@endsection
