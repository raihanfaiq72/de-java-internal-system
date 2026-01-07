@extends('Layout.main')

@section('main')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

<div class="page-wrapper">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-sm-12">
                    <div class="page-title-box d-md-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="page-title fw-bold">Manajemen Faktur Pembelian</h4>
                            <p class="text-muted mb-0 small">Kelola hutang dagang dan dokumen pembelian ke supplier.</p>
                        </div>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-primary">Dashboard</a></li>
                            <li class="breadcrumb-item active">Faktur Pembelian</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                        <div class="card-header bg-white border-bottom-0 pt-3 px-4">
                            <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active fw-bold py-3" data-bs-toggle="tab" href="#purchase-active" role="tab">
                                        <i class="fa-solid fa-file-invoice me-2"></i>Faktur Aktif
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link fw-bold py-3" data-bs-toggle="tab" href="#purchase-deleted" role="tab">
                                        <i class="fa-solid fa-trash-can me-2"></i>Data Terhapus
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="card-body p-4">
                            <div class="tab-content">
                                <div class="tab-pane active" id="purchase-active" role="tabpanel">
                                    
                                    <div class="row g-2 mb-4 p-3 bg-light rounded-3 align-items-end border">
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold text-muted">Cari Faktur / Supplier</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white border-end-0"><i class="fa fa-search text-muted"></i></span>
                                                <input type="text" id="filter-search" class="form-control border-start-0" placeholder="Ketik nomor atau nama...">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small fw-bold text-muted">Status Dokumen</label>
                                            <select id="filter-status-dok" class="tom-select-init">
                                                <option value="">Semua Status</option>
                                                <option value="Draft">Draft</option>
                                                <option value="Approved">Approved</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small fw-bold text-muted">Status Bayar</label>
                                            <select id="filter-status-bayar" class="tom-select-init">
                                                <option value="">Semua Bayar</option>
                                                <option value="Paid">Paid</option>
                                                <option value="Unpaid">Unpaid</option>
                                                <option value="Partially Paid">Partial</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <button onclick="loadPurchaseData()" class="btn btn-dark w-100 fw-bold shadow-sm">
                                                <i class="fa fa-filter me-1"></i> FILTER
                                            </button>
                                        </div>
                                        <div class="col-md-3 text-end">
                                            <button class="btn btn-primary px-4 fw-bold shadow-sm" onclick="openPurchaseModal()">
                                                <i class="fa fa-plus-circle me-1"></i> BUAT FAKTUR
                                            </button>
                                        </div>
                                    </div>

                                    <div class="bg-light border-bottom header-table-custom py-2 px-1 rounded-top">
                                        <div class="d-flex align-items-center">
                                            <div style="width: 50px;" class="text-center">
                                                <input type="checkbox" class="form-check-input" id="master-checkbox">
                                            </div>
                                            <div class="row flex-grow-1 m-0 text-uppercase fw-bold text-muted" style="font-size: 10px; letter-spacing: 1px;">
                                                <div class="col-3 text-start">Faktur & Supplier</div>
                                                <div class="col-2 text-center">Status Dok.</div>
                                                <div class="col-1 text-center">Bayar</div>
                                                <div class="col-2 text-center">Total Tagihan</div>
                                                <div class="col-2 text-center">Tanggal</div>
                                                <div class="col-1 text-center">Tempo</div>
                                                <div style="width: 80px;" class="text-center">Aksi</div>
                                            </div>
                                            <div style="width: 40px;"></div>
                                        </div>
                                    </div>

                                    <div class="accordion mt-3" id="purchaseAccordion"></div>

                                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                        <span id="pagination-info" class="text-muted small fw-medium"></span>
                                        <nav><ul class="pagination pagination-sm mb-0 shadow-sm" id="pagination-container"></ul></nav>
                                    </div>
                                </div>

                                <div class="tab-pane" id="purchase-deleted" role="tabpanel">
                                    <div class="text-center py-5">
                                        <div class="mb-3"><i class="fa fa-trash-alt text-muted fs-1 opacity-25"></i></div>
                                        <h5 class="text-dark">Tidak Ada Data Terhapus</h5>
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

@include('Purchase.Modal.modal-fullscreen')

@endsection

@push('css')
<style>
    .nav-tabs-custom .nav-link { border: none; color: #6c757d; position: relative; transition: all 0.3s; }
    .nav-tabs-custom .nav-link.active { color: var(--bs-primary); background: transparent; }
    .nav-tabs-custom .nav-link.active::after { content: ""; position: absolute; bottom: 0; left: 0; width: 100%; height: 3px; background: var(--bs-primary); border-radius: 3px; }
    .accordion-item { border: 1px solid #edf2f9 !important; margin-bottom: 10px !important; border-radius: 12px !important; overflow: hidden; }
    .badge-soft-success { background: #e6f7ef; color: #00a65a; border: 1px solid #c3ecd7; }
    .badge-soft-danger { background: #feecef; color: #f33950; border: 1px solid #fbc9d0; }
    .badge-soft-primary { background: #e7f0ff; color: #3b7ddd; border: 1px solid #c6dcff; }
    .badge-soft-secondary { background: #f1f3f5; color: #495057; border: 1px solid #dee2e6; }
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    const API_URL = '{{url("api/invoice-api")}}';

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.tom-select-init').forEach(el => new TomSelect(el, { create: false }));
        loadPurchaseData();
    });

    const formatIDR = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val);

    async function loadPurchaseData(url = API_URL) {
        const accordion = document.getElementById('purchaseAccordion');
        accordion.innerHTML = '<div class="text-center p-5"><div class="spinner-border text-primary"></div></div>';
        
        try {
            const search = document.getElementById('filter-search').value;
            const statusDok = document.getElementById('filter-status-dok').value;
            const statusBayar = document.getElementById('filter-status-bayar').value;

            let fetchUrl = new URL(url);
            fetchUrl.searchParams.append('tipe_invoice', 'Purchase');
            if (search) fetchUrl.searchParams.append('search', search);
            if (statusDok) fetchUrl.searchParams.append('status_dok', statusDok);
            if (statusBayar) fetchUrl.searchParams.append('status_pembayaran', statusBayar);

            const response = await fetch(fetchUrl);
            const result = await response.json();

            if (result.success) {
                renderPurchaseList(result.data.data);
                renderPagination(result.data);
            }
        } catch (error) {
            accordion.innerHTML = '<div class="alert alert-danger text-center m-3">Gagal memuat data.</div>';
        }
    }

    function renderPurchaseList(data) {
        const accordion = document.getElementById('purchaseAccordion');
        accordion.innerHTML = '';

        if (!data || data.length === 0) {
            accordion.innerHTML = '<div class="text-center p-5 text-muted border rounded-3 bg-light">Tidak ada data faktur.</div>';
            return;
        }

        data.forEach(item => {
            const payBadge = item.status_pembayaran === 'Paid' ? 'badge-soft-success' : (item.status_pembayaran === 'Unpaid' ? 'badge-soft-danger' : 'badge-soft-warning');
            const dokBadge = item.status_dok === 'Approved' ? 'badge-soft-primary' : 'badge-soft-secondary';
            
            let itemRows = '';
            item.items.forEach(it => {
                itemRows += `
                    <tr>
                        <td class="ps-4 py-2 small fw-bold">${it.nama_produk_manual || it.product?.nama_produk}</td>
                        <td class="small text-muted">${it.deskripsi_produk || '-'}</td>
                        <td class="text-center small">${Math.floor(it.qty)}</td>
                        <td class="text-end small">${formatIDR(it.harga_satuan)}</td>
                        <td class="text-end pe-4 fw-bold small">${formatIDR(it.total_harga_item)}</td>
                    </tr>`;
            });

            const html = `
                <div class="accordion-item shadow-none">
                    <div class="accordion-header bg-white">
                        <div class="d-flex align-items-center">
                            <div style="width: 50px;" class="text-center"><input type="checkbox" class="form-check-input"></div>
                            <button class="accordion-button collapsed shadow-none p-3" type="button" data-bs-toggle="collapse" data-bs-target="#pur-${item.id}">
                                <div class="row flex-grow-1 m-0 align-items-center text-center">
                                    <div class="col-3 text-start ps-0">
                                        <div class="fw-bold text-dark">${item.nomor_invoice}</div>
                                        <div class="text-muted small">${item.mitra?.nama || 'Supplier Umum'}</div>
                                    </div>
                                    <div class="col-2"><span class="badge ${dokBadge}">${item.status_dok}</span></div>
                                    <div class="col-1"><span class="badge ${payBadge}">${item.status_pembayaran}</span></div>
                                    <div class="col-2 fw-bold text-dark">${formatIDR(item.total_akhir)}</div>
                                    <div class="col-2 text-muted small">${item.tgl_invoice}</div>
                                    <div class="col-1 fw-bold text-danger small">${item.tgl_jatuh_tempo || '-'}</div>
                                    <div style="width: 80px;" class="text-center">
                                        <button class="btn btn-sm btn-light border" onclick="openPurchaseModal(${item.id}, event)"><i class="fa fa-pencil-alt text-primary"></i></button>
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>
                    <div id="pur-${item.id}" class="accordion-collapse collapse" data-bs-parent="#purchaseAccordion">
                        <div class="accordion-body bg-light border-top p-3">
                            <table class="table table-sm bg-white rounded border mb-0">
                                <thead class="table-light"><tr><th class="ps-4">Item</th><th>Deskripsi</th><th class="text-center">Qty</th><th class="text-end">Harga</th><th class="text-end pe-4">Subtotal</th></tr></thead>
                                <tbody>${itemRows}</tbody>
                            </table>
                        </div>
                    </div>
                </div>`;
            accordion.insertAdjacentHTML('beforeend', html);
        });
    }

    function renderPagination(meta) {
        document.getElementById('pagination-info').innerText = `Menampilkan ${meta.from || 0} - ${meta.to || 0} dari ${meta.total}`;
        const container = document.getElementById('pagination-container');
        container.innerHTML = '';
        meta.links.forEach(link => {
            const li = `<li class="page-item ${link.active ? 'active' : ''} ${!link.url ? 'disabled' : ''}"><a class="page-link border-0 mx-1 rounded" href="javascript:void(0)" onclick="loadPurchaseData('${link.url}')">${link.label.replace('&laquo;', '').replace('&raquo;', '')}</a></li>`;
            container.insertAdjacentHTML('beforeend', li);
        });
    }
</script>
@endpush