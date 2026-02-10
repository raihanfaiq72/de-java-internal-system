@extends('Layout.main')

@section('main')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap"
        rel="stylesheet">

    <div class="page-wrapper" style="background-color: #f8fafc; min-height: 100vh; font-family: 'Inter', sans-serif;">
        <div class="page-content">
            <div class="container-fluid">

                <div class="row align-items-center mb-4">
                    <div class="col-md-7">
                        <h4 class="fw-bold text-dark mb-1">Manajemen Mitra Bisnis</h4>
                        <p class="text-muted small mb-0">Kelola data klien, supplier, dan vendor perusahaan.</p>
                    </div>
                    <div class="col-md-5 text-md-end mt-3 mt-md-0">
                        <button class="btn btn-white border fw-bold px-3 me-2 shadow-sm text-dark">
                            <i class="fa fa-file-excel me-1 text-success"></i> Export .xls
                        </button>
                        <button class="btn btn-primary fw-bold px-4 shadow-sm" onclick="openMitraModal()">
                            <i class="fa fa-plus-circle me-1"></i> Tambah Mitra Baru
                        </button>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                    <div class="card-header bg-white border-bottom py-0 px-4">
                        <ul class="nav nav-tabs nav-tabs-finance" role="tablist">
                            <li class="nav-item"><a class="nav-link active fw-bold py-3" data-bs-toggle="tab"
                                    href="#mitra-active">Mitra Aktif</a></li>
                            <li class="nav-item"><a class="nav-link fw-bold py-3" data-bs-toggle="tab"
                                    href="#mitra-deleted">Mitra Terhapus</a></li>
                        </ul>
                    </div>

                    <div class="card-body p-4 bg-white">
                        <div class="tab-content">
                            <div class="tab-pane active" id="mitra-active">

                                <!-- Filter Section -->
                                <div class="row g-2 mb-4 p-3 rounded-3 align-items-end bg-light border">
                                    <div class="col-lg-4">
                                        <label class="f-label">Pencarian</label>
                                        <div class="input-group input-group-finance">
                                            <span class="input-group-text bg-white border-end-0"><i
                                                    class="fa fa-search text-muted"></i></span>
                                            <input type="text" id="filter-search"
                                                class="form-control border-start-0 ps-0 shadow-none"
                                                placeholder="Nama, No. Mitra, atau Email...">
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <label class="f-label">Tipe Mitra</label>
                                        <select id="filter-type" class="tom-select-init">
                                            <option value="">Semua Tipe</option>
                                            <option value="Supplier">Supplier</option>
                                            <option value="Client">Client</option>
                                            <option value="Both">Both</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-6 text-end">
                                        <button onclick="loadMitraData()"
                                            class="btn btn-dark fw-bold py-2 px-4 shadow-sm btn-sm">FILTER</button>
                                        <button onclick="resetFilter()"
                                            class="btn btn-light border fw-bold text-dark py-2 btn-sm">RESET</button>
                                    </div>
                                </div>

                                <!-- Table Structure -->
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0" id="mitraTable">
                                        <thead class="bg-light text-uppercase text-secondary fw-bold"
                                            style="font-size: 11px; letter-spacing: 0.5px;">
                                            <tr>
                                                <th width="40" class="ps-3 text-center">#</th>
                                                <th width="120">No. Mitra</th>
                                                <th width="150">No. KTP/NPWP</th>
                                                <th width="250">Nama Perusahaan</th>
                                                <th width="100" class="text-center">Tipe</th>
                                                <th width="200">Kontak Utama</th>
                                                <th width="200">Email & Telepon</th>
                                                <th width="80" class="text-end pe-3">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="mitraTableBody" class="border-top-0">
                                            <!-- Rows injected by JS -->
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <div
                                    class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 pt-3 border-top">
                                    <span id="pagination-info" class="text-muted small fw-medium"></span>
                                    <nav>
                                        <ul class="pagination pagination-sm mb-0 shadow-sm" id="pagination-container"></ul>
                                    </nav>
                                </div>
                            </div>

                            <div class="tab-pane" id="mitra-deleted">
                                <!-- Filter Section (Deleted) -->
                                <div class="row g-2 mb-4 p-3 rounded-3 align-items-end bg-light border">
                                    <div class="col-lg-4">
                                        <label class="f-label">Pencarian</label>
                                        <div class="input-group input-group-finance">
                                            <span class="input-group-text bg-white border-end-0"><i
                                                    class="fa fa-search text-muted"></i></span>
                                            <input type="text" id="filter-search-deleted"
                                                class="form-control border-start-0 ps-0 shadow-none"
                                                placeholder="Nama, No. Mitra, atau Email...">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 text-end">
                                        <button onclick="loadMitraData()"
                                            class="btn btn-dark fw-bold py-2 px-4 shadow-sm btn-sm">FILTER</button>
                                        <button onclick="resetFilter()"
                                            class="btn btn-light border fw-bold text-dark py-2 btn-sm">RESET</button>
                                    </div>
                                </div>

                                <!-- Table Structure (Deleted) -->
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0" id="mitraTableDeleted">
                                        <thead class="bg-light text-uppercase text-secondary fw-bold"
                                            style="font-size: 11px; letter-spacing: 0.5px;">
                                            <tr>
                                                <th width="40" class="ps-3 text-center">#</th>
                                                <th width="120">No. Mitra</th>
                                                <th width="150">No. KTP/NPWP</th>
                                                <th width="250">Nama Perusahaan</th>
                                                <th width="100" class="text-center">Tipe</th>
                                                <th width="200">Kontak Utama</th>
                                                <th width="200">Email & Telepon</th>
                                                <th width="100" class="text-end pe-3">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="mitraTableBodyDeleted" class="border-top-0"></tbody>
                                    </table>
                                </div>

                                <!-- Pagination (Deleted) -->
                                <div
                                    class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 pt-3 border-top">
                                    <span id="pagination-info-deleted" class="text-muted small fw-medium"></span>
                                    <nav>
                                        <ul class="pagination pagination-sm mb-0 shadow-sm"
                                            id="pagination-container-deleted"></ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('Mitra.Modal.modal-fullscreen')
@endsection

@push('css')
    <style>
        .f-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 5px;
            display: block;
        }

        .f-mono {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
        }

        /* Table Styling */
        #mitraTable thead th {
            border-bottom: 1px solid #e2e8f0;
            border-top: 1px solid #e2e8f0;
            background: #f8fafc;
            padding-top: 12px;
            padding-bottom: 12px;
        }

        #mitraTable tbody td {
            font-size: 13px;
            color: #334155;
            padding-top: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        .clickable-row {
            cursor: pointer;
            transition: background-color 0.1s;
        }

        .clickable-row:hover {
            background-color: #f1f5f9 !important;
        }

        /* Badges */
        .f-badge {
            font-size: 10px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
        }

        .f-supplier {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Red for expense related */
        .f-client {
            background: #dcfce7;
            color: #166534;
        }

        /* Green for income related */
        .f-both {
            background: #e0f2fe;
            color: #075985;
        }

        /* Blue for both */

        /* Dropdown */
        .dropdown-menu-finance {
            border: none;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            min-width: 140px;
            border-top: 3px solid #3b82f6;
        }

        .dropdown-menu-finance .dropdown-item {
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            padding: 6px 12px;
        }

        .dropdown-menu-finance .dropdown-item:hover {
            background-color: #f1f5f9;
            color: #3b82f6;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        if (!window.financeApp) {
            window.financeApp = {
                API_URL: '/api/mitra-api',
                formatIDR: (val) => new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(val),
            };
        } else {
            // Ensure API_URL for Mitra is handled if reusing global obj
            window.financeApp.MITRA_API_URL = '/api/mitra-api';
        }

        let tsFilterType;
        let activeTab = 'active'; // 'active' or 'deleted'

        function initFilters() {
            if (typeof TomSelect !== 'undefined') {
                tsFilterType = new TomSelect('#filter-type', {
                    create: false,
                    allowEmptyOption: true,
                    placeholder: 'Semua Tipe',
                    controlInput: null
                });
            }
        }

        async function loadMitraData(url = '/api/mitra-api', type = null) {
            // Determine type based on active tab if not provided
            if (!type) type = activeTab;

            // Fix for null URL
            if (!url || url === 'null') url = '/api/mitra-api';

            const isDeleted = type === 'deleted';
            const tbodyId = isDeleted ? 'mitraTableBodyDeleted' : 'mitraTableBody';

            const tbody = document.getElementById(tbodyId);
            tbody.innerHTML = `
                                            <tr>
                                                <td colspan="8" class="text-center p-5">
                                                    <div class="spinner-border spinner-border-sm text-primary"></div>
                                                    <p class="mt-2 text-muted small mb-0">Memuat data mitra...</p>
                                                </td>
                                            </tr>`;

            try {
                // Determine which filter inputs to use
                const searchId = isDeleted ? 'filter-search-deleted' : 'filter-search';
                const searchVal = document.getElementById(searchId).value;

                const fetchUrl = new URL(url, window.location.origin);

                if (searchVal) fetchUrl.searchParams.append('search', searchVal);

                // Only active tab has type filter
                if (!isDeleted) {
                    const typeVal = document.getElementById('filter-type').value;
                    if (typeVal) fetchUrl.searchParams.append('tipe_mitra', typeVal);
                } else {
                    fetchUrl.searchParams.append('trashed', '1');
                }

                const response = await fetch(fetchUrl, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                // Handle HTTP errors (e.g., 401, 500)
                if (!response.ok) {
                    const errRes = await response.json().catch(() => ({}));
                    throw new Error(errRes.message || `Server Error (${response.status})`);
                }

                const result = await response.json();

                if (result.success) {
                    const items = result.data.data ? result.data.data : result.data;
                    renderMitraList(items, isDeleted);
                    renderPagination(result.data, isDeleted);
                } else {
                    throw new Error(result.message || 'Unknown error');
                }
            } catch (error) {
                console.error("Gagal memuat data:", error);
                tbody.innerHTML =
                    `<tr><td colspan="8" class="text-center text-danger p-4">Gagal memuat data: ${error.message}</td></tr>`;
            }
        }

        function renderMitraList(data, isDeleted) {
            const tbodyId = isDeleted ? 'mitraTableBodyDeleted' : 'mitraTableBody';
            const tbody = document.getElementById(tbodyId);
            tbody.innerHTML = '';

            if (!data || data.length === 0) {
                tbody.innerHTML =
                    '<tr><td colspan="8" class="text-center p-5 text-muted fst-italic">Data mitra tidak ditemukan.</td></tr>';
                return;
            }

            data.forEach((item, index) => {
                let typeClass = 'f-both';
                if (item.tipe_mitra === 'Supplier') typeClass = 'f-supplier';
                if (item.tipe_mitra === 'Client') typeClass = 'f-client';

                let actionButtons = '';

                if (isDeleted) {
                    // Actions for deleted items: Restore, Force Delete
                    actionButtons = `
                                                    <div class="dropdown">
                                                         <button class="btn btn-sm btn-white border shadow-sm py-1 px-2 text-muted" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-h"></i></button>
                                                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-finance">
                                                            <li><a class="dropdown-item text-success" href="javascript:void(0)" onclick="restoreMitra(${item.id})"><i class="fa fa-undo me-2"></i> Pulihkan</a></li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="forceDeleteMitra(${item.id})"><i class="fa fa-trash me-2"></i> Hapus Permanen</a></li>
                                                        </ul>
                                                    </div>
                                                `;
                } else {
                    // Actions for active items: Edit, Soft Delete
                    actionButtons = `
                                                    <div class="dropdown">
                                                         <button class="btn btn-sm btn-white border shadow-sm py-1 px-2 text-muted" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-h"></i></button>
                                                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-finance">
                                                            <li><a class="dropdown-item" href="javascript:void(0)" onclick="openMitraModal(${item.id})"><i class="fa fa-pencil text-primary me-2"></i> Edit Data</a></li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="deleteMitra(${item.id})"><i class="fa fa-trash me-2"></i> Hapus</a></li>
                                                        </ul>
                                                    </div>
                                                `;
                }

                const tr = `
                                            <tr class="clickable-row">
                                                <td class="text-center ps-3 text-muted small">${index + 1}</td>
                                                <td class="fw-bold text-dark font-monospace" style="font-size:12px;">${item.nomor_mitra || '-'}</td>
                                                <td class="font-monospace small text-muted">${item.ktp_npwp || '-'}</td>
                                                <td>
                                                    <div class="fw-bold text-dark mb-0">${item.nama}</div>
                                                    <div class="small text-muted">${item.badan_usaha || 'Perorangan'}</div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="f-badge ${typeClass}">${item.tipe_mitra}</span>
                                                    ${item.is_cash_customer ? '<div class="mt-1"><span class="badge bg-success text-white" style="font-size:9px;">CASH</span></div>' : ''}
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-dark small">${item.kontak_nama || '-'}</div>
                                                    <div class="small text-muted">${item.kontak_jabatan || ''}</div>
                                                </td>
                                                <td>
                                                    <div class="text-dark small"><i class="fa fa-envelope me-1 text-muted"></i> ${item.email || '-'}</div>
                                                    <div class="text-dark small mt-1"><i class="fa fa-phone me-1 text-muted"></i> ${item.no_hp || '-'}</div>
                                                </td>
                                                <td class="text-end pe-3">
                                                    ${actionButtons}
                                                </td>
                                            </tr>
                                        `;
                tbody.insertAdjacentHTML('beforeend', tr);
            });
        }

        async function deleteMitra(id) {
            if (!await macConfirm('Hapus mitra', 'Hapus mitra ini? Data akan dipindahkan ke tab Mitra Terhapus dan dapat dipulihkan.')) return;
            try {
                const res = await fetch(`/api/mitra-api/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const dat = await res.json();
                if (dat.success) {
                    // alert('Berhasil dihapus'); // Optional: show toast instead
                    loadMitraData(); // Reload active list
                } else alert(dat.message);
            } catch (e) {
                alert('Gagal menghapus');
            }
        }

        async function restoreMitra(id) {
            if (!await macConfirm('Pulihkan data mitra', 'Pulihkan data mitra ini?')) return;
            try {
                const res = await fetch(`/api/mitra-api/${id}/restore`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const dat = await res.json();
                if (dat.success) {
                    loadMitraData(undefined, 'deleted'); // Reload deleted list
                } else alert(dat.message);
            } catch (e) {
                alert('Gagal memulihkan mitra');
            }
        }

        async function forceDeleteMitra(id) {
            if (!await macConfirm('Hapus Permanen', 'Hapus permanen mitra ini? Data TIDAK DAPAT dikembalikan.')) return;
            try {
                const res = await fetch(`/api/mitra-api/${id}/force-delete`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const dat = await res.json();
                if (dat.success) {
                    loadMitraData(undefined, 'deleted'); // Reload deleted list
                } else alert(dat.message);
            } catch (e) {
                alert('Gagal menghapus permanen');
            }
        }

        function renderPagination(meta, isDeleted) {
            const containerId = isDeleted ? 'pagination-container-deleted' : 'pagination-container';
            const infoId = isDeleted ? 'pagination-info-deleted' : 'pagination-info';

            const c = document.getElementById(containerId);
            c.innerHTML = '';

            if (!meta || !meta.links) return;

            document.getElementById(infoId).innerText = `${meta.from || 0}-${meta.to || 0} dari ${meta.total} data`;

            // Current type context for pagination clicks
            const type = isDeleted ? 'deleted' : 'active';

            meta.links.forEach(l => {
                const cls = l.active ? 'bg-primary text-white' : 'bg-white text-dark';
                const onclick = l.url ? `onclick="loadMitraData('${l.url}', '${type}')"` : '';
                const href = l.url ? '#' : 'javascript:void(0)';

                c.insertAdjacentHTML('beforeend',
                    `<li class="page-item ${!l.url ? 'disabled' : ''}"><a class="page-link border-0 mx-1 rounded shadow-sm fw-bold ${cls}" href="${href}" ${onclick}>${l.label}</a></li>`
                );
            });
        }

        function resetFilter() {
            if (activeTab === 'deleted') {
                document.getElementById('filter-search-deleted').value = '';
            } else {
                document.getElementById('filter-search').value = '';
                if (tsFilterType) {
                    tsFilterType.setValue('');
                } else {
                    document.getElementById('filter-type').value = '';
                }
            }
            loadMitraData();
        }

        document.addEventListener('DOMContentLoaded', () => {
            initFilters();
            loadMitraData();

            // Tab Change Listener
            const tabEls = document.querySelectorAll('a[data-bs-toggle="tab"]');
            tabEls.forEach(el => {
                el.addEventListener('shown.bs.tab', function (event) {
                    if (event.target.getAttribute('href') === '#mitra-deleted') {
                        activeTab = 'deleted';
                        loadMitraData(undefined, 'deleted');
                    } else {
                        activeTab = 'active';
                        loadMitraData(undefined, 'active');
                    }
                });
            });
        });
    </script>
@endpush