@extends('Layout.main')

@section('main')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<div class="page-wrapper" style="background-color: #f4f7f9; min-height: 100vh; font-family: 'Inter', sans-serif;">
    <div class="page-content">
        <div class="container-fluid">
            
            <div class="row align-items-center mb-4">
                <div class="col-md-6">
                    <h4 class="fw-bold text-dark mb-1">Invoice Penjualan</h4>
                    <p class="text-muted small mb-0">Manajemen piutang dan dokumentasi transaksi komersial.</p>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <button class="btn btn-white border fw-bold px-3 me-2 text-dark shadow-sm">
                        <i class="fa fa-file-excel me-1"></i> Export .xls
                    </button>
                    <button class="btn btn-primary fw-bold px-4 shadow-sm" onclick="openInvoiceModal()">
                        <i class="fa fa-plus me-1"></i> Buat Invoice
                    </button>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-3 bg-white">
                        <div class="card-body p-3 border-start border-primary border-4 rounded-3">
                            <p class="text-muted small mb-1 fw-bold text-uppercase">Total Outstanding</p>
                            <h5 class="mb-0 fw-bold" id="stat-outstanding">Rp 0</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-3 bg-white">
                        <div class="card-body p-3 border-start border-danger border-4 rounded-3">
                            <p class="text-muted small mb-1 fw-bold text-uppercase">Melewati Tempo</p>
                            <h5 class="mb-0 fw-bold text-danger" id="stat-overdue">0 Invoice</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-3 bg-white">
                        <div class="card-body p-3 border-start border-success border-4 rounded-3">
                            <p class="text-muted small mb-1 fw-bold text-uppercase">Terbayar (Bulan Ini)</p>
                            <h5 class="mb-0 fw-bold text-success" id="stat-paid">Rp 0</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-3 bg-white">
                        <div class="card-body p-3 border-start border-secondary border-4 rounded-3">
                            <p class="text-muted small mb-1 fw-bold text-uppercase">Total Invoice</p>
                            <h5 class="mb-0 fw-bold" id="stat-total-count">0</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="card-header bg-white border-bottom py-0 px-4">
                    <ul class="nav nav-tabs nav-tabs-finance" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active fw-bold py-3" data-bs-toggle="tab" href="#invoice-active">
                                <i class="fa-solid fa-list-check me-2"></i>Invoice Aktif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fw-bold py-3" data-bs-toggle="tab" href="#invoice-deleted">
                                <i class="fa-solid fa-archive me-2"></i>Arsip
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-4 bg-white">
                    <div class="tab-content">
                        <div class="tab-pane active" id="invoice-active">
                            
                            <div class="row g-2 mb-4 align-items-end p-3 rounded-3" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                                <div class="col-lg-4">
                                    <label class="form-label small fw-bold text-secondary">Cari Transaksi</label>
                                    <div class="input-group bg-white border rounded-2">
                                        <span class="input-group-text bg-transparent border-0"><i class="fa fa-search text-muted"></i></span>
                                        <input type="text" id="filter-search" class="form-control border-0 shadow-none" placeholder="Nomor invoice atau nama mitra...">
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <label class="form-label small fw-bold text-secondary">Status Dok.</label>
                                    <select id="filter-status-dok" class="tom-select-init">
                                        <option value="">Semua Status</option>
                                        <option value="Draft">Draft</option>
                                        <option value="Sent">Sent</option>
                                        <option value="Approved">Approved</option>
                                    </select>
                                </div>
                                <div class="col-lg-2">
                                    <label class="form-label small fw-bold text-secondary">Pembayaran</label>
                                    <select id="filter-status-bayar" class="tom-select-init">
                                        <option value="">Semua Bayar</option>
                                        <option value="Paid">Paid</option>
                                        <option value="Unpaid">Unpaid</option>
                                        <option value="Partially Paid">Partial</option>
                                    </select>
                                </div>
                                <div class="col-lg-2">
                                    <button onclick="loadInvoiceData()" class="btn btn-dark w-100 fw-bold py-2">
                                        <i class="fa fa-filter me-1 small"></i> Filter
                                    </button>
                                </div>
                                <div class="col-lg-2">
                                    <button onclick="resetFilter()" class="btn btn-outline-secondary w-100 fw-bold py-2">
                                        Reset
                                    </button>
                                </div>
                            </div>

                            <div class="bg-light border rounded-top py-2 px-3 d-none d-md-block">
                                <div class="row m-0 text-uppercase fw-bold text-secondary" style="font-size: 11px; letter-spacing: 0.8px;">
                                    <div class="col-3">Identitas Invoice</div>
                                    <div class="col-2 text-center">Status Bayar</div>
                                    <div class="col-2 text-end">Total Akhir</div>
                                    <div class="col-2 text-center">Tanggal</div>
                                    <div class="col-2 text-center">Jatuh Tempo</div>
                                </div>
                            </div>

                            <div class="accordion accordion-finance mt-2" id="invoiceAccordion">
                                </div>

                            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                <span id="pagination-info" class="text-muted small fw-medium"></span>
                                <nav><ul class="pagination pagination-sm mb-0 shadow-sm" id="pagination-container"></ul></nav>
                            </div>
                        </div>

                        <div class="tab-pane" id="invoice-deleted">
                            <div class="text-center py-5">
                                <i class="fa fa-box-open text-muted mb-3 opacity-25" style="font-size: 3rem;"></i>
                                <h6 class="text-secondary fw-bold">Tidak ada data arsip</h6>
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
    .nav-tabs-finance { border-bottom: none; }
    .nav-tabs-finance .nav-link {
        border: none;
        color: #94a3b8;
        padding: 1rem 1.5rem;
        position: relative;
    }
    .nav-tabs-finance .nav-link.active {
        color: #0f172a;
        background: transparent;
    }
    .nav-tabs-finance .nav-link.active::after {
        content: "";
        position: absolute;
        bottom: 0; left: 0; right: 0;
        height: 3px;
        background: #3b82f6;
    }

    .accordion-finance .accordion-item {
        border: 1px solid #e2e8f0 !important;
        margin-bottom: 8px !important;
        border-radius: 6px !important;
        background: #fff;
    }
    .accordion-finance .accordion-button {
        padding: 0.75rem 1rem;
        background-color: #fff !important;
        box-shadow: none !important;
    }
    .accordion-finance .accordion-button:not(.collapsed) {
        border-bottom: 1px dashed #e2e8f0;
    }

    .strip-Approved { border-left: 4px solid #3b82f6 !important; }
    .strip-Draft { border-left: 4px solid #94a3b8 !important; }
    .strip-Sent { border-left: 4px solid #f59e0b !important; }

    .f-badge {
        font-size: 10px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 4px;
        text-transform: uppercase;
    }
    .f-paid { background: #dcfce7; color: #166534; }
    .f-unpaid { background: #fee2e2; color: #991b1b; }
    .f-partial { background: #fef3c7; color: #92400e; }

    .inner-table thead th {
        background: #f8fafc;
        color: #64748b;
        font-size: 11px;
        text-transform: uppercase;
        border: none;
        padding: 10px;
    }
    .inner-table tbody td {
        font-size: 13px;
        padding: 12px 10px;
        border-bottom: 1px solid #f1f5f9;
    }

    .col-label-sub { font-size: 10px; color: #94a3b8; text-transform: uppercase; font-weight: 600; margin-bottom: 2px; }
    .text-finance-val { font-size: 14px; color: #1e293b; font-weight: 600; }
    .ts-wrapper.single .ts-control { border-radius: 6px; padding: 8px 12px; }
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    const API_URL = 'http://localhost:8000/api/invoice-api';

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.tom-select-init').forEach(el => {
            new TomSelect(el, { create: false, allowEmptyOption: true });
        });
        loadInvoiceData();
    });

    const formatIDR = (val) => new Intl.NumberFormat('id-ID', {
        style: 'currency', currency: 'IDR', minimumFractionDigits: 0
    }).format(val);

    async function loadInvoiceData(url = API_URL) {
        const accordion = document.getElementById('invoiceAccordion');
        accordion.innerHTML = '<div class="text-center p-5"><div class="spinner-border spinner-border-sm text-primary"></div><p class="mt-2 text-muted small">Sinkronisasi data...</p></div>';
        
        try {
            const params = new URLSearchParams({
                search: document.getElementById('filter-search').value,
                status_dok: document.getElementById('filter-status-dok').value,
                status_pembayaran: document.getElementById('filter-status-bayar').value
            });

            const response = await fetch(`${url}?${params.toString()}`);
            const result = await response.json();

            if (result.success) {
                renderInvoiceList(result.data.data);
                renderPagination(result.data);
            }
        } catch (error) {
            accordion.innerHTML = '<div class="alert alert-danger mx-3 my-4">Gagal terhubung ke server. Silakan coba lagi.</div>';
        }
    }

    function renderInvoiceList(data) {
        const accordion = document.getElementById('invoiceAccordion');
        accordion.innerHTML = '';

        if (!data || data.length === 0) {
            accordion.innerHTML = '<div class="text-center p-5 border border-dashed rounded-3 mt-3"><p class="text-muted mb-0">Tidak ditemukan catatan invoice yang sesuai.</p></div>';
            return;
        }

        data.forEach(item => {
            const payClass = item.status_pembayaran === 'Paid' ? 'f-paid' : (item.status_pembayaran === 'Unpaid' ? 'f-unpaid' : 'f-partial');
            
            let itemRows = '';
            item.items?.forEach(it => {
                const prodName = it.nama_produk_manual || (it.product?.nama_produk || 'Item Tanpa Nama');
                itemRows += `
                    <tr>
                        <td><strong>${prodName}</strong><br><span class="text-muted small">${it.deskripsi_produk || '-'}</span></td>
                        <td class="text-center">${Math.floor(it.qty)}</td>
                        <td class="text-end">${formatIDR(it.harga_satuan)}</td>
                        <td class="text-end fw-bold">${formatIDR(it.total_harga_item)}</td>
                    </tr>`;
            });

            const html = `
                <div class="accordion-item strip-${item.status_dok} shadow-none">
                    <div class="accordion-header">
                        <button class="accordion-button collapsed shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#inv-${item.id}">
                            <div class="row w-100 align-items-center m-0">
                                <div class="col-3">
                                    <div class="col-label-sub">Nomor & Pelanggan</div>
                                    <div class="text-finance-val">${item.nomor_invoice}</div>
                                    <div class="small text-muted text-truncate">${item.mitra ? item.mitra.nama : 'Mitra Umum'}</div>
                                </div>
                                <div class="col-2 text-center">
                                    <span class="f-badge ${payClass}">${item.status_pembayaran}</span>
                                </div>
                                <div class="col-2 text-end">
                                    <div class="col-label-sub">Nilai Invoice</div>
                                    <div class="text-finance-val text-primary">${formatIDR(item.total_akhir)}</div>
                                </div>
                                <div class="col-2 text-center">
                                    <div class="col-label-sub">Terbit</div>
                                    <div class="small fw-medium">${item.tgl_invoice}</div>
                                </div>
                                <div class="col-2 text-center">
                                    <div class="col-label-sub">Jatuh Tempo</div>
                                    <div class="small fw-bold ${item.tgl_jatuh_tempo ? 'text-danger' : 'text-muted'}">${item.tgl_jatuh_tempo || '-'}</div>
                                </div>
                                <div class="col-1 text-end">
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-light border" onclick="openInvoiceModal(${item.id}, event)">
                                            <i class="fa fa-pencil text-secondary"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </button>
                    </div>
                    <div id="inv-${item.id}" class="accordion-collapse collapse" data-bs-parent="#invoiceAccordion">
                        <div class="accordion-body p-3 bg-light">
                            <div class="table-responsive bg-white rounded border shadow-sm">
                                <table class="table inner-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Deskripsi Item</th>
                                            <th class="text-center" width="80">Qty</th>
                                            <th class="text-end" width="160">Harga Satuan</th>
                                            <th class="text-end" width="160">Total Harga</th>
                                        </tr>
                                    </thead>
                                    <tbody>${itemRows}</tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-end mt-3 me-2">
                                <div class="text-end">
                                    <span class="text-muted small fw-bold">Subtotal:</span>
                                    <span class="ms-3 fw-bold text-dark h6 mb-0">${formatIDR(item.subtotal)}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
            accordion.insertAdjacentHTML('beforeend', html);
        });
    }

    function renderPagination(meta) {
        document.getElementById('pagination-info').innerText = `Menampilkan ${meta.from || 0} sampai ${meta.to || 0} dari ${meta.total} entitas`;
        const container = document.getElementById('pagination-container');
        container.innerHTML = '';
        meta.links.forEach(link => {
            const active = link.active ? 'active' : '';
            const disabled = !link.url ? 'disabled' : '';
            const label = link.label.replace('&laquo;', '').replace('&raquo;', '');
            const li = `<li class="page-item ${active} ${disabled}"><a class="page-link border-0 mx-1 rounded px-3" href="javascript:void(0)" onclick="loadInvoiceData('${link.url}')">${label}</a></li>`;
            container.insertAdjacentHTML('beforeend', li);
        });
    }

    function resetFilter() {
        document.getElementById('filter-search').value = '';
        document.querySelectorAll('.tom-select-init').forEach(el => el.tomselect.clear());
        loadInvoiceData();
    }
</script>
@endpush