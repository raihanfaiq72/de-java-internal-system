@extends('Layout.main')

@section('main')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">

<div class="page-wrapper" style="background-color: #f8fafc; min-height: 100vh; font-family: 'Inter', sans-serif;">
    <div class="page-content">
        <div class="container-fluid">
            
            <div class="row align-items-center mb-4">
                <div class="col-md-7">
                    <h4 class="fw-bold text-dark mb-1">Manajemen Invoice Penjualan</h4>
                    <p class="text-muted small mb-0">Otorisasi transaksi dan pantau piutang secara real-time.</p>
                </div>
                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                    <button class="btn btn-white border fw-bold px-3 me-2 shadow-sm text-dark">
                        <i class="fa fa-file-excel me-1 text-success"></i> Export .xls
                    </button>
                    <button class="btn btn-primary fw-bold px-4 shadow-sm" onclick="openInvoiceModal()">
                        <i class="fa fa-plus-circle me-1"></i> Buat Invoice Baru
                    </button>
                </div>
            </div>

            <div class="row mb-4 g-3">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-body p-3 border-start border-4 border-primary">
                            <label class="f-label">Total Outstanding</label>
                            <h5 class="fw-bold mb-0 text-dark">Rp 450.000.000</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-body p-3 border-start border-4 border-danger">
                            <label class="f-label">Melewati Tempo</label>
                            <h5 class="fw-bold mb-0 text-danger">12 Dokumen</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-body p-3 border-start border-4 border-success">
                            <label class="f-label">Lunas Bulan Ini</label>
                            <h5 class="fw-bold mb-0 text-success">Rp 125.000.000</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-body p-3 border-start border-4 border-secondary">
                            <label class="f-label">Total Volume</label>
                            <h5 class="fw-bold mb-0 text-dark">1.240 Unit</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="card-header bg-white border-bottom py-0 px-4">
                    <ul class="nav nav-tabs nav-tabs-finance" role="tablist">
                        <li class="nav-item"><a class="nav-link active fw-bold py-3" data-bs-toggle="tab" href="#invoice-active">Invoice Aktif</a></li>
                        <li class="nav-item"><a class="nav-link fw-bold py-3" data-bs-toggle="tab" href="#invoice-archive">Arsip</a></li>
                    </ul>
                </div>

                <div class="card-body p-4 bg-white">
                    <div class="tab-content">
                        <div class="tab-pane active" id="invoice-active">
                            
                            <div class="row g-2 mb-4 p-3 rounded-3 align-items-end" style="background-color: #f1f5f9; border: 1px solid #e2e8f0;">
                                <div class="col-lg-3">
                                    <label class="f-label">Pencarian</label>
                                    <div class="input-group input-group-finance">
                                        <span class="input-group-text bg-white border-end-0"><i class="fa fa-search text-muted"></i></span>
                                        <input type="text" id="filter-search" class="form-control border-start-0 ps-0 shadow-none" placeholder="Cari nomor atau pelanggan...">
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <label class="f-label">Status Dok.</label>
                                    <select id="filter-status-dok" class="tom-select-init">
                                        <option value="">Semua</option>
                                        <option value="Draft">Draft</option>
                                        <option value="Approved">Approved</option>
                                    </select>
                                </div>
                                <div class="col-lg-2">
                                    <label class="f-label">Bayar</label>
                                    <select id="filter-status-bayar" class="tom-select-init">
                                        <option value="">Semua</option>
                                        <option value="Paid">Lunas</option>
                                        <option value="Unpaid">Belum</option>
                                    </select>
                                </div>
                                <div class="col-lg-5 text-end">
                                    <div id="bulk-action-area" class="d-none d-inline-block me-2">
                                        <span class="small text-muted me-2 selected-count">0 terpilih</span>
                                        <button class="btn btn-outline-danger fw-bold py-2 px-3" onclick="bulkDelete()">
                                            <i class="fa fa-trash-can me-1"></i> HAPUS TERPILIH
                                        </button>
                                    </div>
                                    <button onclick="loadInvoiceData()" class="btn btn-dark fw-bold py-2 px-4">FILTER</button>
                                    <button onclick="resetFilter()" class="btn btn-light border fw-bold text-dark py-2">RESET</button>
                                </div>
                            </div>

                            <div class="bg-light border rounded-top py-2 px-3 d-none d-md-block shadow-sm">
                                <div class="row m-0 text-uppercase fw-bold text-secondary align-items-center" style="font-size: 11px; letter-spacing: 1px;">
                                    <div class="col-auto" style="width: 40px;">
                                        <input type="checkbox" class="form-check-input" id="master-checkbox" onclick="toggleSelectAll(this)">
                                    </div>
                                    <div class="col-auto text-center" style="width: 30px;"></div>
                                    <div class="col-3">Identitas Invoice</div>
                                    <div class="col-2 text-center">Status Bayar</div>
                                    <div class="col-2 text-end">Total Tagihan</div>
                                    <div class="col-2 text-center">Tgl. Terbit</div>
                                    <div class="col-1 text-center">Tempo</div>
                                    <div class="col-1 text-end">Aksi</div>
                                </div>
                            </div>

                            <div class="accordion accordion-finance mt-2" id="invoiceAccordion"></div>

                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 pt-3 border-top">
                                <span id="pagination-info" class="text-muted small fw-medium"></span>
                                <nav><ul class="pagination pagination-sm mb-0 shadow-sm" id="pagination-container"></ul></nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('Sales.Modal.modal-fullscreen')
@endsection

@push('css')
<style>
    .f-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 700; color: #64748b; margin-bottom: 5px; display: block; }
    .f-mono { font-family: 'JetBrains Mono', monospace; }
    
    /* Input Group & Tom Select Alignment */
    .input-group-finance { height: 38px; }
    .input-group-finance .form-control { border-color: #d1d5db; height: 38px; font-size: 14px; }
    .input-group-finance .input-group-text { border-color: #d1d5db; color: #9ca3af; }
    .ts-wrapper.tom-select-init .ts-control { min-height: 38px !important; display: flex; align-items: center; border-color: #d1d5db !important; }

    .nav-tabs-finance { border-bottom: none; }
    .nav-tabs-finance .nav-link { border: none; color: #94a3b8; padding: 1rem 1.5rem; position: relative; }
    .nav-tabs-finance .nav-link.active { color: #0f172a; background: transparent; }
    .nav-tabs-finance .nav-link.active::after { content: ""; position: absolute; bottom: 0; left: 0; right: 0; height: 3px; background: #3b82f6; border-radius: 3px 3px 0 0; }

    .accordion-finance .accordion-item { border: 1px solid #e2e8f0 !important; margin-bottom: 8px !important; border-radius: 8px !important; background: #fff; }
    .clickable-row:hover { background-color: #f8fafc; cursor: pointer; }
    
    .chevron-icon { transition: transform 0.2s; color: #cbd5e1; font-weight: bold; font-size: 14px; display: inline-block; }
    .accordion-item:not(.collapsed) .chevron-icon { transform: rotate(90deg); color: #3b82f6; }

    .strip-Approved { border-left: 5px solid #3b82f6 !important; }
    .strip-Draft { border-left: 5px solid #94a3b8 !important; }

    .f-badge { font-size: 10px; font-weight: 700; padding: 4px 10px; border-radius: 4px; text-transform: uppercase; }
    .f-paid { background: #dcfce7; color: #166534; }
    .f-unpaid { background: #fee2e2; color: #991b1b; }
    .f-partial { background: #fef3c7; color: #92400e; }

    .dropdown-menu-finance { border: none; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); border-radius: 8px; padding: 8px; min-width: 160px; border-top: 3px solid #3b82f6; }
    .dropdown-menu-finance .dropdown-item { border-radius: 6px; padding: 8px 12px; font-size: 13px; font-weight: 600; color: #475569; }
</style>
@endpush

@push('js')
<script>
    if (!window.financeApp) {
        window.financeApp = {
            API_URL: '/api/invoice-api',
            selectedIds: [],
            formatIDR: (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val)
        };
    }

    async function loadInvoiceData(url = window.financeApp.API_URL) {
        const accordion = document.getElementById('invoiceAccordion');
        accordion.innerHTML = '<div class="text-center p-5"><div class="spinner-border spinner-border-sm text-primary"></div></div>';
        resetSelection();

        try {
            let finalUrl = (typeof url === 'string' && url.includes('/')) ? url : window.financeApp.API_URL;
            const params = new URLSearchParams({
                search: document.getElementById('filter-search').value,
                status_dok: document.getElementById('filter-status-dok').value,
                status_pembayaran: document.getElementById('filter-status-bayar').value
            });

            const response = await fetch(`${finalUrl}${finalUrl.includes('?') ? '&' : '?'}${params.toString()}`);
            const result = await response.json();

            if (result.success) {
                renderInvoiceList(result.data.data);
                renderPagination(result.data);
            }
        } catch (error) {
            accordion.innerHTML = '<div class="alert alert-danger mx-3 my-4">Gagal memuat data.</div>';
        }
    }

    function renderInvoiceList(data) {
        const accordion = document.getElementById('invoiceAccordion');
        accordion.innerHTML = '';

        if (!data || data.length === 0) {
            accordion.innerHTML = '<div class="text-center p-5 border border-dashed rounded-3 mt-3"><p class="text-muted mb-0">Data tidak ditemukan.</p></div>';
            return;
        }

        data.forEach(item => {
            const payClass = item.status_pembayaran === 'Paid' ? 'f-paid' : (item.status_pembayaran === 'Unpaid' ? 'f-unpaid' : 'f-partial');
            let itemRows = '';
            item.items?.forEach(it => {
                itemRows += `<tr><td><strong>${it.nama_produk_manual || it.product?.nama_produk}</strong></td><td class="text-center">${Math.floor(it.qty)}</td><td class="text-end fw-bold f-mono">${window.financeApp.formatIDR(it.total_harga_item)}</td></tr>`;
            });

            const html = `
                <div class="accordion-item strip-${item.status_dok} collapsed" id="item-container-${item.id}">
                    <div class="d-flex align-items-center px-3 py-2">
                        <div class="col-auto" style="width: 40px;" onclick="event.stopPropagation()">
                            <input type="checkbox" class="form-check-input invoice-checkbox" data-id="${item.id}" onclick="updateSelection()">
                        </div>
                        <div class="row flex-grow-1 m-0 align-items-center clickable-row" data-bs-toggle="collapse" data-bs-target="#inv-${item.id}" onclick="this.closest('.accordion-item').classList.toggle('collapsed')">
                            <div class="col-auto text-center" style="width: 30px;"><span class="chevron-icon">></span></div>
                            <div class="col-3">
                                <div class="fw-bold text-dark" style="font-size: 14px;">${item.nomor_invoice}</div>
                                <div class="small text-muted text-truncate">${item.mitra ? item.mitra.nama : 'Mitra Umum'}</div>
                            </div>
                            <div class="col-2 text-center"><span class="f-badge ${payClass}">${item.status_pembayaran}</span></div>
                            <div class="col-2 text-end"><div class="fw-bold text-primary f-mono">${window.financeApp.formatIDR(item.total_akhir)}</div></div>
                            <div class="col-2 text-center small fw-semibold text-muted">${item.tgl_invoice}</div>
                            <div class="col-1 text-center small fw-bold text-danger">${item.tgl_jatuh_tempo || '-'}</div>
                        </div>
                        <div class="col-1 text-end" onclick="event.stopPropagation()">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border px-2 py-1 shadow-none fw-bold" type="button" data-bs-toggle="dropdown">AKSI <i class="fa fa-caret-down ms-1"></i></button>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-finance">
                                    <li><a class="dropdown-item py-2 fw-bold small" href="javascript:void(0)" onclick="openInvoiceModal(${item.id})"><i class="fa fa-pencil text-primary me-2"></i> Edit Data</a></li>
                                    <li><a class="dropdown-item py-2 fw-bold small" href="javascript:void(0)"><i class="fa fa-copy text-success me-2"></i> Duplikat</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item py-2 fw-bold small text-danger" href="javascript:void(0)"><i class="fa fa-trash me-2"></i> Hapus</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div id="inv-${item.id}" class="accordion-collapse collapse" data-bs-parent="#invoiceAccordion">
                        <div class="accordion-body p-3 bg-light border-top shadow-inner">
                            <div class="bg-white rounded border overflow-hidden">
                                <table class="table mb-0" style="font-size: 13px;">
                                    <thead class="bg-light"><tr><th class="ps-3">Item</th><th class="text-center">Qty</th><th class="text-end pe-3">Subtotal</th></tr></thead>
                                    <tbody>${itemRows}</tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>`;
            accordion.insertAdjacentHTML('beforeend', html);
        });
    }

    // Checkbox Logic
    function toggleSelectAll(master) {
        const checkboxes = document.querySelectorAll('.invoice-checkbox');
        checkboxes.forEach(cb => cb.checked = master.checked);
        updateSelection();
    }

    function updateSelection() {
        const checkboxes = document.querySelectorAll('.invoice-checkbox:checked');
        window.financeApp.selectedIds = Array.from(checkboxes).map(cb => cb.dataset.id);
        
        const bulkArea = document.getElementById('bulk-action-area');
        const countLabel = bulkArea.querySelector('.selected-count');
        
        if (window.financeApp.selectedIds.length > 0) {
            bulkArea.classList.remove('d-none');
            countLabel.innerText = `${window.financeApp.selectedIds.length} terpilih`;
        } else {
            bulkArea.classList.add('d-none');
        }
    }

    function resetSelection() {
        window.financeApp.selectedIds = [];
        const master = document.getElementById('master-checkbox');
        if (master) master.checked = false;
        updateSelection();
    }

    function bulkDelete() {
        if (confirm(`Hapus ${window.financeApp.selectedIds.length} invoice terpilih?`)) {
            console.log("Menghapus IDs:", window.financeApp.selectedIds);
            // Tambahkan fetch API delete massal di sini
        }
    }

    function renderPagination(meta) {
        document.getElementById('pagination-info').innerText = `Data ${meta.from || 0} - ${meta.to || 0} dari ${meta.total}`;
        const container = document.getElementById('pagination-container');
        container.innerHTML = '';
        meta.links.forEach(link => {
            const active = link.active ? 'active' : '';
            const disabled = !link.url ? 'disabled' : '';
            const label = link.label.replace('&laquo;', '').replace('&raquo;', '');
            const target = link.url ? `'${link.url}'` : 'null';
            const li = `<li class="page-item ${active} ${disabled}"><a class="page-link border-0 mx-1 rounded px-3 shadow-sm" href="javascript:void(0)" onclick="loadInvoiceData(${target})">${label}</a></li>`;
            container.insertAdjacentHTML('beforeend', li);
        });
    }

    function resetFilter() {
        document.getElementById('filter-search').value = '';
        document.querySelectorAll('.tom-select-init').forEach(el => el.tomselect?.clear());
        loadInvoiceData();
    }

    document.addEventListener('DOMContentLoaded', () => loadInvoiceData());
</script>
@endpush