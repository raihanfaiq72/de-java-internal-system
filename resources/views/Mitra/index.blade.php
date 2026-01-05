@extends('Layout.main')

@section('main')
<div class="page-wrapper">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box d-md-flex justify-content-between align-items-center">
                        <h4 class="page-title">Semua Mitra</h4>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item active">Mitra</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom-0 pb-0">
                            <ul class="nav nav-pills" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active fw-bold" data-bs-toggle="tab" href="#mitra-active" role="tab">
                                        <i class="fa fa-file-invoice me-1"></i> Mitra
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link fw-bold" data-bs-toggle="tab" href="#mitra-deleted" role="tab">
                                        <i class="fa fa-trash-alt me-1"></i> Mitra Terhapus
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane active" id="mitra-active" role="tabpanel">
                                    <div class="row g-2 mb-4 align-items-end">
                                        <div class="col-md-3">
                                            <label class="small fw-bold text-muted">Cari Mitra</label>
                                            <input type="text" id="filter-search" class="form-control" placeholder="Ketik nomor atau nama...">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="small fw-bold text-muted">Type Mitra</label>
                                            <select id="filter-type" class="form-select">
                                                <option value="">Semua Type</option>
                                                <option value="Supplier">Supplier</option>
                                                <option value="Client">Client</option>
                                                <option value="Both">Both</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <button onclick="loadMitraData()" class="btn btn-dark w-100 fw-bold">
                                                <i class="fa fa-filter me-1"></i> Filter
                                            </button>
                                        </div>
                                        <div class="col-md-3 text-end">
                                            <button class="btn btn-primary px-4 fw-bold shadow-sm" onclick="openMitraModal()">
                                                <i class="fa fa-plus me-1"></i> TAMBAH MITRA
                                            </button>
                                        </div>
                                    </div>

                                    <div class="bg-dark text-white rounded-top header-table-custom shadow-sm">
                                        <div class="d-flex align-items-center py-3">
                                            <div class="row flex-grow-1 m-0 text-uppercase fw-bold text-center" style="font-size: 11px;">
                                                <div class="col">No. Mitra</div>
                                                <div class="col">Nama</div>
                                                <div class="col">Type</div>
                                                <div class="col">Email</div>
                                                <div class="col">Nomor Telepon Perusahaan</div>
                                                <div class="col">Nomor Telepon Utama</div>
                                                <div class="col">Aksi</div>
                                            </div>
                                        </div>
                                        <div id="mitra-list" class="bg-white shadow-sm rounded-bottom"></div>

                                        <div class="d-flex justify-content-between align-items-center px-2 py-2">
                                            <span id="pagination-info" class="text-muted small"></span>
                                            <nav>
                                                <ul class="pagination pagination-sm mb-0" id="pagination-container"></ul>
                                            </nav>
                                        </div>

                                    </div>
                                </div>

                                <div class="tab-pane" id="mitra-deleted" role="tabpanel">
                                    <div class="text-center py-5 text-muted">
                                        <i class="fa fa-trash-alt fa-3x mb-3 opacity-25"></i>
                                        <p>Data mitra yang dihapus akan muncul di sini.</p>
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

@include('Mitra.Modal.modal-fullscreen')

<style>
    .col-fixed-aksi { width: 90px; flex-shrink: 0; }
    .mitra-row { transition: background 0.15s ease; }
    .mitra-row:hover { background-color: #f8f9fc; }
    .col-fixed-check { width: 60px; flex-shrink: 0; }
    .col-fixed-arrow { width: 50px; flex-shrink: 0; }
    .header-table-custom { border-bottom: 2px solid #222; padding-left: 5px; }
    .row-spacious { padding-top: 18px !important; padding-bottom: 18px !important; }
    .font-11 { font-size: 11px; }
    .bg-soft-secondary { background-color: #f8f9fc; }
    .badge { padding: 6px 10px; letter-spacing: 0.3px; }
</style>

<script>
    const API_URL = 'http://localhost:8000/api/mitra-api';

    document.addEventListener('DOMContentLoaded', () => {
        loadMitraData();
        if(typeof initializeMitraData === 'function') initializeMitraData();
    });

    async function loadMitraData(url = API_URL) {
        if (typeof url !== 'string') url = API_URL;
        
        try {
            const search = document.getElementById('filter-search').value;
            const typeMitra = document.getElementById('filter-type').value;

            let fetchUrl = new URL(url);
            if (search) fetchUrl.searchParams.append('search', search);
            if (typeMitra) fetchUrl.searchParams.append('status_dok', typeMitra);

            const response = await fetch(fetchUrl);
            const result = await response.json();

            if (result.success) {
                renderMitraList(result.data.data);
                renderPagination(result.data);
            }
        } catch (error) {
            document.getElementById('mitra-list').innerHTML =
                '<div class="alert alert-danger text-center">Gagal memuat data.</div>';
        }
    }

    function renderMitraList(data) {
        const container = document.getElementById('mitra-list');
        container.innerHTML = '';

        if (!data || data.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5 text-muted">
                    <i class="fa fa-folder-open fa-2x mb-2 opacity-25"></i>
                    <p class="mb-0">Data mitra tidak ditemukan</p>
                </div>
            `;
            return;
        }

        data.forEach(item => {
            const html = `
                <div class="row m-0 align-items-center text-center border-bottom py-3 mitra-row" style="font-size: 12.5px;">
                    
                    <div class="col fw-bold text-primary">
                        ${item.nomor_mitra ?? '-'}
                    </div>

                    <div class="col text-start">
                        <div class="fw-bold">${item.nama}</div>
                        <div class="text-muted small">${item.badan_usaha ?? '-'}</div>
                    </div>

                    <div class="col">
                        <span class="badge bg-soft-secondary text-secondary fw-bold">
                            ${item.tipe_mitra}
                        </span>
                    </div>

                    <div class="col text-muted">
                        ${item.email ?? '-'}
                    </div>

                    <div class="col">
                        ${item.no_hp ?? '-'}
                    </div>

                    <div class="col">
                        ${item.kontak_no_hp ?? '-'}
                    </div>

                    <div class="col-fixed-aksi">
                        <button class="btn btn-sm btn-soft-primary me-1"
                            onclick="openMitraModal(${item.id})">
                            <i class="fa fa-edit"></i>
                        </button>

                        <button class="btn btn-sm btn-soft-danger"
                            onclick="deleteMitra(${item.id})">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        });
    }

    function renderPagination(meta) {
        document.getElementById('pagination-info').innerText =
            `Menampilkan ${meta.from || 0}–${meta.to || 0} dari ${meta.total} data`;

        const container = document.getElementById('pagination-container');
        container.innerHTML = '';

        meta.links.forEach(link => {
            const active = link.active ? 'active' : '';
            const disabled = !link.url ? 'disabled' : '';
            const label = link.label.replace('&laquo;', '').replace('&raquo;', '');

            const li = `
                <li class="page-item ${active} ${disabled}">
                    <a class="page-link shadow-none"
                    href="javascript:void(0)"
                    onclick="loadMitraData('${link.url}')">
                        ${label}
                    </a>
                </li>
            `;
            container.insertAdjacentHTML('beforeend', li);
        });
    }

</script>
@endsection