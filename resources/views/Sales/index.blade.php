@extends('Layout.main')

@section('main')
<div class="page-wrapper">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box d-md-flex justify-content-between align-items-center">
                        <h4 class="page-title">Manajemen Invoice</h4>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#">Approx</a></li>
                            <li class="breadcrumb-item active">Invoice Penjualan</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom-0 pb-0">
                            <ul class="nav nav-pills nav-justified" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active fw-bold" data-bs-toggle="tab" href="#invoice-active" role="tab">
                                        <i class="fa fa-file-invoice me-1"></i> Invoice Penjualan
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link fw-bold" data-bs-toggle="tab" href="#invoice-deleted" role="tab">
                                        <i class="fa fa-trash-alt me-1"></i> Invoice Terhapus
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link fw-bold" data-bs-toggle="tab" href="#settings" role="tab">
                                        <i class="fa fa-cog me-1"></i> Settings
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane active" id="invoice-active" role="tabpanel">
                                    
                                    <div class="row g-2 mb-4 align-items-end">
                                        <div class="col-md-3">
                                            <label class="small fw-bold text-muted">Cari Invoice / Pelanggan</label>
                                            <input type="text" id="filter-search" class="form-control" placeholder="Ketik nomor atau nama...">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="small fw-bold text-muted">Status Dokumen</label>
                                            <select id="filter-status-dok" class="form-select">
                                                <option value="">Semua Status</option>
                                                <option value="Draft">Draft</option>
                                                <option value="Cetak">Cetak</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="small fw-bold text-muted">Status Bayar</label>
                                            <select id="filter-status-bayar" class="form-select">
                                                <option value="">Semua Bayar</option>
                                                <option value="Paid">Paid</option>
                                                <option value="Unpaid">Unpaid</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <button onclick="loadInvoiceData()" class="btn btn-dark w-100 fw-bold">
                                                <i class="fa fa-filter me-1"></i> Filter
                                            </button>
                                        </div>
                                        <div class="col-md-3 text-end">
                                            <button class="btn btn-primary px-4 fw-bold shadow-sm" data-bs-toggle="dropdown">
                                                <i class="fa fa-plus me-1"></i> TAMBAH DATA
                                            </button>
                                        </div>
                                    </div>

                                    <div class="bg-dark text-white rounded-top header-table-custom shadow-sm">
                                        <div class="d-flex align-items-center py-3">
                                            <div class="col-fixed-check text-center">
                                                <input type="checkbox" class="form-check-input" id="master-checkbox">
                                            </div>
                                            <div class="row flex-grow-1 m-0 text-uppercase fw-bold text-center" style="font-size: 11px;">
                                                <div class="col-3 text-start">Invoice & Mitra</div>
                                                <div class="col-2">Ref & Status Dok.</div>
                                                <div class="col-1">Bayar</div>
                                                <div class="col-2">Total Akhir</div>
                                                <div class="col-2">Tgl Invoice</div>
                                                <div class="col-1">Tempo</div>
                                                <div class="col-fixed-aksi">Aksi</div>
                                            </div>
                                            <div class="col-fixed-arrow"></div>
                                        </div>
                                    </div>

                                    <div class="accordion custom-coa-accordion mb-3" id="invoiceAccordion">
                                        </div>

                                    <div class="d-flex justify-content-between align-items-center px-2">
                                        <span id="pagination-info" class="text-muted small"></span>
                                        <nav><ul class="pagination pagination-sm mb-0" id="pagination-container"></ul></nav>
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

<style>
    /* Fixed Widths */
    .col-fixed-check { width: 60px; flex-shrink: 0; }
    .col-fixed-aksi { width: 60px; flex-shrink: 0; }
    .col-fixed-arrow { width: 50px; flex-shrink: 0; }

    .header-table-custom { 
        border-bottom: 2px solid #222; 
        padding-left: 5px;
    }

    /* Accordion Button Overrides */
    .custom-coa-accordion .accordion-button {
        padding: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .accordion-content-wrapper {
        display: flex;
        align-items: center;
        width: 100%;
        transition: background 0.2s ease-in-out;
    }

    .custom-coa-accordion .accordion-item {
        border: 1px solid #eef1f6 !important;
        border-radius: 10px !important;
        margin-bottom: 12px !important; /* Jarak antar baris */
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .custom-coa-accordion .accordion-item:hover {
        transform: translateY(-1px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05) !important;
    }

    /* Padding Vertikal lebih besar (Lega) */
    .row-spacious {
        padding-top: 18px !important;
        padding-bottom: 18px !important;
    }

    .accordion-button::after { 
        margin-right: 20px; 
        transform: scale(0.9);
    }

    .font-10 { font-size: 10px; }
    .font-11 { font-size: 11px; }
    .bg-soft-secondary { background-color: #f8f9fc; }

    .badge {
        padding: 6px 10px;
        letter-spacing: 0.3px;
    }
</style>
<script>
    const API_URL = 'http://localhost:8000/api/invoice-api';

    document.addEventListener('DOMContentLoaded', function() {
        // Panggil tanpa argumen saat pertama kali muat
        loadInvoiceData();
    });

    const formatIDR = (val) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(val);
    };

    // PERBAIKAN: Gunakan pengecekan tipe data pada argumen url
    async function loadInvoiceData(url = API_URL) {
        // Jika url yang masuk adalah sebuah Event (bukan string), reset ke API_URL
        if (typeof url !== 'string') {
            url = API_URL;
        }

        const accordion = document.getElementById('invoiceAccordion');
        accordion.innerHTML = '<div class="text-center p-5"><div class="spinner-border text-primary"></div></div>';
        
        try {
            // Ambil parameter filter dari input
            const search = document.getElementById('filter-search').value;
            const statusDok = document.getElementById('filter-status-dok').value;
            const statusBayar = document.getElementById('filter-status-bayar').value;

            // Buat URL object untuk mengelola query params
            let fetchUrl = new URL(url);
            if (search) fetchUrl.searchParams.append('search', search);
            if (statusDok) fetchUrl.searchParams.append('status_dok', statusDok);
            if (statusBayar) fetchUrl.searchParams.append('status_pembayaran', statusBayar);

            const response = await fetch(fetchUrl);
            const result = await response.json();

            if (result.success) {
                // Sesuai JSON: result.data.data adalah array-nya
                const invoices = result.data.data; 
                renderInvoiceList(invoices);
                renderPagination(result.data);
            }
        } catch (error) {
            console.error(error);
            accordion.innerHTML = '<div class="alert alert-danger text-center">Gagal memuat data API. Pastikan server sudah jalan.</div>';
        }
    }

    function renderInvoiceList(data) {
        const accordion = document.getElementById('invoiceAccordion');
        accordion.innerHTML = '';

        if (!data || data.length === 0) {
            accordion.innerHTML = '<div class="text-center p-5 text-muted">Tidak ada data ditemukan.</div>';
            return;
        }

        data.forEach(item => {
            const badgeClass = item.status_pembayaran === 'Paid' ? 'bg-success' : 'bg-danger';
            
            let itemRows = '';
            if (item.items && item.items.length > 0) {
                item.items.forEach(it => {
                    const namaProduk = it.nama_produk_manual || (it.product ? it.product.nama_produk : 'Produk');
                    itemRows += `
                        <tr class="align-middle">
                            <td class="ps-3">
                                <div class="fw-bold text-dark">${namaProduk}</div>
                                <div class="text-muted font-10">${it.product ? it.product.sku_kode : ''}</div>
                            </td>
                            <td class="text-muted small">${it.deskripsi_produk || '-'}</td>
                            <td class="text-center">${Math.floor(it.qty)}</td>
                            <td class="text-end">${formatIDR(it.harga_satuan)}</td>
                            <td class="text-end pe-3 fw-bold text-dark">${formatIDR(it.total_harga_item)}</td>
                        </tr>`;
                });
            } else {
                itemRows = '<tr><td colspan="5" class="text-center text-muted p-4">Detail item tidak tersedia.</td></tr>';
            }

            const html = `
                <div class="accordion-item border-0 shadow-sm">
                    <div class="accordion-header bg-white">
                        <div class="accordion-content-wrapper">
                            <div class="col-fixed-check text-center">
                                <input type="checkbox" class="form-check-input mt-0">
                            </div>
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#inv-${item.id}">
                                <div class="row flex-grow-1 m-0 align-items-center text-center row-spacious" style="font-size: 12.5px;">
                                    <div class="col-3 text-start p-0">
                                        <div class="fw-bold text-primary mb-1">${item.nomor_invoice}</div>
                                        <div class="text-muted small fw-medium">${item.mitra ? item.mitra.nama : 'Mitra Umum'}</div>
                                    </div>
                                    <div class="col-2">
                                        <div class="text-muted small mb-1">${item.ref_no || '-'}</div>
                                        <span class="badge bg-soft-secondary text-secondary font-10 fw-bold border text-uppercase">${item.status_dok}</span>
                                    </div>
                                    <div class="col-1">
                                        <span class="badge ${badgeClass} font-10 text-white fw-bold shadow-sm text-uppercase">${item.status_pembayaran}</span>
                                    </div>
                                    <div class="col-2 text-center">
                                        <div class="fw-bold text-dark mb-1">${formatIDR(item.total_akhir)}</div>
                                        <div class="text-muted font-10">Subtotal: ${formatIDR(item.subtotal)}</div>
                                    </div>
                                    <div class="col-2">
                                        <div class="fw-medium text-dark mb-1">${item.tgl_invoice}</div>
                                        <div class="text-muted font-10">ID: ${item.id}</div>
                                    </div>
                                    <div class="col-1">
                                        <div class="fw-bold text-dark small">${item.tgl_jatuh_tempo || '-'}</div>
                                    </div>
                                    <div class="col-fixed-aksi text-end">
                                        <a href="#" class="btn btn-sm btn-soft-primary p-1 border-0"><i class="fa fa-edit"></i></a>
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>
                    <div id="inv-${item.id}" class="accordion-collapse collapse" data-bs-parent="#invoiceAccordion">
                        <div class="accordion-body bg-light border-top p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-items mb-0">
                                    <thead>
                                        <tr class="align-middle">
                                            <th class="ps-3 py-3">Nama Produk</th>
                                            <th class="py-3">Deskripsi</th>
                                            <th class="text-center py-3">Qty</th>
                                            <th class="text-end py-3">Harga</th>
                                            <th class="text-end pe-3 py-3">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white">${itemRows}</tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>`;
            accordion.insertAdjacentHTML('beforeend', html);
        });
    }

    function renderPagination(meta) {
        document.getElementById('pagination-info').innerText = `Menampilkan ${meta.from || 0} ke ${meta.to || 0} dari ${meta.total} data`;
        const container = document.getElementById('pagination-container');
        container.innerHTML = '';

        meta.links.forEach(link => {
            const active = link.active ? 'active' : '';
            const disabled = !link.url ? 'disabled' : '';
            const label = link.label.replace('&laquo;', '').replace('&raquo;', '');
            
            const li = `
                <li class="page-item ${active} ${disabled}">
                    <a class="page-link shadow-none" href="javascript:void(0)" onclick="loadInvoiceData('${link.url}')">${label}</a>
                </li>`;
            container.insertAdjacentHTML('beforeend', li);
        });
    }
</script>
@endsection