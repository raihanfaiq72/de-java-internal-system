@extends('Layout.main')

@section('main')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/libs/leaflet/leaflet.css" />

    <div class="page-wrapper">
        <div class="page-content bg-white">
            <div class="dr-page-shell pt-3">
                <div class="container-fluid p-0">
                    <!-- Breadcrumb -->
                    <div class="dr-breadcrumb">
                        <i class="iconoir-home dr-breadcrumb-icon"></i>
                        <a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Dashboard</a>
                        <i class="iconoir-nav-arrow-right dr-breadcrumb-icon" style="font-size: 14px;"></i>
                        <strong>Manajemen Mitra</strong>
                    </div>

                    <!-- Hero Header -->
                    <div class="dr-hero mb-3">
                        <div>
                            <h4 class="dr-title">Daftar Mitra & Rekanan</h4>
                            <p class="dr-subtitle">Kelola data supplier, klien, dan rekanan bisnis secara terpusat.</p>
                        </div>
                        <div class="dr-actions">
                            <button class="dr-btn dr-btn-outline" onclick="exportMitra()">
                                <i class="iconoir-download"></i> Export
                            </button>
                            <button class="dr-btn dr-btn-primary" onclick="openMitraModal()">
                                <i class="iconoir-plus-circle"></i> Tambah Mitra
                            </button>
                        </div>
                    </div>

                    <!-- Main Content Card -->
                    <div class="dr-card p-0 overflow-hidden bg-white mt-3">
                        <div class="dr-card-header border-bottom px-4 py-2 bg-white d-flex align-items-center justify-content-between">
                            <ul class="nav dr-tabs-segmented my-2" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#mitra-active" type="button" role="tab">
                                        <i class="iconoir-group me-2"></i> Mitra Aktif
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#mitra-deleted" type="button" role="tab">
                                        <i class="iconoir-trash me-2"></i> Terhapus
                                    </button>
                                </li>
                            </ul>
                            <div class="d-flex align-items-center gap-2">
                                <button class="dr-btn-icon" title="Refresh Data" onclick="loadMitraData()">
                                    <i class="iconoir-refresh"></i>
                                </button>
                            </div>
                        </div>

                        <div class="tab-content">
                            <!-- TAB 1: MITRA AKTIF -->
                            <div class="tab-pane fade show active" id="mitra-active" role="tabpanel">
                                <!-- Filter Bar -->
                                <div class="dr-filter-bar px-4 py-3 bg-light-subtle border-bottom gap-3">
                                    <div class="dr-filter-group grow" style="min-width: 200px;">
                                        <label class="dr-label">Cari Mitra</label>
                                        <div class="dr-search-wrap">
                                            <i class="iconoir-search dr-search-icon"></i>
                                            <input type="text" id="filter-search" class="dr-input dr-search-input" placeholder="Nama, telepon, atau alamat...">
                                        </div>
                                    </div>
                                    <div class="dr-filter-group" style="min-width: 180px;">
                                        <label class="dr-label">Tipe Mitra</label>
                                        <select id="filter-type" class="dr-input" onchange="loadMitraData()">
                                            <option value="">Semua Tipe</option>
                                            <option value="Supplier">Supplier</option>
                                            <option value="Client">Client</option>
                                            <option value="Both">Both</option>
                                        </select>
                                    </div>
                                    <div class="dr-filter-actions">
                                        <label class="dr-label" style="visibility: hidden;">-</label>
                                        <div class="d-flex gap-1">
                                            <button onclick="resetFilter()" class="dr-btn-icon" title="Reset Filter">
                                                <i class="iconoir-undo"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="dr-table align-middle mb-0" id="mitraTable">
                                        <thead>
                                            <tr>
                                                <th width="40" class="ps-4 text-center">#</th>
                                                <th width="120">No. Mitra</th>
                                                <th width="150">Identitas</th>
                                                <th width="250">Perusahaan</th>
                                                <th width="100" class="text-center">Tipe</th>
                                                <th width="200">Kontak Person</th>
                                                <th width="200">Info Kontak</th>
                                                <th width="80" class="text-end pe-4">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="mitraTableBody">
                                            <!-- Rows injected by JS -->
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center px-4 py-3 border-top">
                                    <span id="pagination-info" class="text-muted small fw-medium"></span>
                                    <nav>
                                        <ul class="pagination pagination-sm mb-0 gap-1" id="pagination-container"></ul>
                                    </nav>
                                </div>
                            </div>

                            <!-- TAB 2: MITRA TERHAPUS -->
                            <div class="tab-pane fade" id="mitra-deleted" role="tabpanel">
                                <!-- Filter Bar (Deleted) -->
                                <div class="px-4 py-3 bg-light-subtle border-bottom d-flex align-items-center gap-3">
                                    <div class="position-relative" style="width: 300px;">
                                        <i class="iconoir-search text-muted position-absolute start-0 top-50 translate-middle-y ms-3" style="font-size: 16px;"></i>
                                        <input type="text" id="filter-search-deleted" class="dr-input ps-5" placeholder="Cari di arsip..." autocomplete="off">
                                    </div>
                                    <button class="dr-btn dr-btn-outline btn-sm py-1" onclick="resetFilter()">Reset</button>
                                </div>

                                <div class="table-responsive">
                                    <table class="dr-table align-middle mb-0" id="mitraTableDeleted">
                                        <thead>
                                            <tr>
                                                <th width="40" class="ps-4 text-center">#</th>
                                                <th width="120">No. Mitra</th>
                                                <th width="150">Identitas</th>
                                                <th width="250">Perusahaan</th>
                                                <th width="100" class="text-center">Tipe</th>
                                                <th width="200">Kontak Person</th>
                                                <th width="200">Info Kontak</th>
                                                <th width="100" class="text-end pe-4">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="mitraTableBodyDeleted">
                                            <!-- Rows injected by JS -->
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination (Deleted) -->
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center px-4 py-3 border-top">
                                    <span id="pagination-info-deleted" class="text-muted small fw-medium"></span>
                                    <nav>
                                        <ul class="pagination pagination-sm mb-0 gap-1" id="pagination-container-deleted"></ul>
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
    <script src="/assets/libs/leaflet/leaflet.js"
        onerror="(function(){var s=document.createElement('script');s.src='https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';document.head.appendChild(s)})()">
    </script>
@endsection

@push('css')
    <style>
        .f-mono {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
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

        function exportMitra() {
            let params = new URLSearchParams();

            if (activeTab === 'deleted') {
                params.append('trashed', '1');
                let search = document.getElementById('filter-search-deleted').value;
                if (search) params.append('search', search);
            } else {
                let search = document.getElementById('filter-search').value;
                if (search) params.append('search', search);

                if (tsFilterType) {
                    let type = tsFilterType.getValue();
                    if (type) params.append('tipe_mitra', type);
                } else {
                    let type = document.getElementById('filter-type').value;
                    if (type) params.append('tipe_mitra', type);
                }
            }

            window.location.href = '/mitra/export?' + params.toString();
        }

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

        async function loadMitraData(url = '/api/mitra-api', type = null, page = 1) {
            // Determine type based on active tab if not provided
            if (!type) type = activeTab;

            // Fix for null URL
            if (!url || url === 'null') url = '/api/mitra-api';

            // Ensure page is added to URL
            if (url === '/api/mitra-api') {
                url += `?page=${page}`;
            }

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
                let typeClass = 'bg-soft-info text-info';
                if (item.tipe_mitra === 'Supplier') typeClass = 'bg-soft-danger text-danger';
                if (item.tipe_mitra === 'Client') typeClass = 'bg-soft-success text-success';

                let actionButtons = '';

                if (isDeleted) {
                    actionButtons = `
                        <div class="d-flex justify-content-end gap-1">
                            <button class="dr-btn-icon dr-btn-icon-view" onclick="restoreMitra(${item.id})" title="Pulihkan">
                                <i class="iconoir-undo"></i>
                            </button>
                            <button class="dr-btn-icon dr-btn-icon-delete" onclick="forceDeleteMitra(${item.id})" title="Hapus Permanen">
                                <i class="iconoir-xmark"></i>
                            </button>
                        </div>
                    `;
                } else {
                    actionButtons = `
                        <div class="d-flex justify-content-end gap-1">
                            <button class="dr-btn-icon dr-btn-icon-view" onclick="openMitraModal(${item.id})" title="Edit Data">
                                <i class="iconoir-edit-pencil"></i>
                            </button>
                            <button class="dr-btn-icon dr-btn-icon-delete" onclick="deleteMitra(${item.id})" title="Hapus">
                                <i class="iconoir-trash"></i>
                            </button>
                        </div>
                    `;
                }

                const tr = `
                    <tr>
                        <td class="text-center ps-4 text-muted small">${index + 1}</td>
                        <td class="fw-bold text-dark font-monospace" style="font-size:12px;">${item.nomor_mitra || '-'}</td>
                        <td class="font-monospace small text-muted">${item.ktp_npwp || '-'}</td>
                        <td>
                            <div class="fw-bold text-dark mb-0">${item.nama}</div>
                            <div class="small text-muted">${item.badan_usaha || '-'}</div>
                        </td>
                        <td class="text-center">
                            <span class="dr-badge ${typeClass}">${item.tipe_mitra}</span>
                            ${item.is_cash_customer ? '<div class="mt-1"><span class="dr-badge bg-soft-success text-success" style="font-size:9px;">CASH</span></div>' : ''}
                        </td>
                        <td>
                            <div class="fw-bold text-dark small">${item.kontak_nama || '-'}</div>
                            <div class="small text-muted">${item.kontak_jabatan || ''}</div>
                        </td>
                        <td>
                            <div class="text-dark small"><i class="iconoir-mail me-1 text-muted"></i> ${item.email || '-'}</div>
                            <div class="text-dark small mt-1"><i class="iconoir-phone me-1 text-muted"></i> ${item.no_hp || '-'}</div>
                        </td>
                        <td class="text-end pe-4">
                            ${actionButtons}
                        </td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', tr);
            });
        }

        async function deleteMitra(id) {
            if (!await macConfirm('Hapus mitra',
                    'Hapus mitra ini? Data akan dipindahkan ke tab Mitra Terhapus dan dapat dipulihkan.')) return;
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
            if (!await macConfirm('Pulihkan data mitra', 'Pulihkan data mitra ini?', {
                    confirmText: 'Ya',
                    confirmType: 'success'
                })) return;
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
            const info = document.getElementById(infoId);

            if (!meta || !meta.links) return;

            if (info) {
                info.innerText = `Menampilkan ${meta.from || 0} - ${meta.to || 0} dari ${meta.total} data`;
            }

            c.innerHTML = '';

            const type = isDeleted ? 'deleted' : 'active';

            meta.links.forEach(l => {
                const activeClass = l.active ? 'active' : '';
                const disabledClass = !l.url ? 'disabled' : '';

                let pageNum = 1;
                if (l.url) {
                    const url = new URL(l.url, window.location.origin);
                    pageNum = url.searchParams.get('page') || 1;
                }

                const onclick = l.url ? `onclick="loadMitraData(undefined, '${type}', ${pageNum})"` : '';
                const label = l.label.includes('Previous') ? '<i class="iconoir-nav-arrow-left"></i>' : (l.label.includes('Next') ? '<i class="iconoir-nav-arrow-right"></i>' : l.label);

                c.insertAdjacentHTML('beforeend', `
                    <li class="page-item ${activeClass} ${disabledClass}">
                        <button class="page-link border-0 mx-0 rounded shadow-none fw-bold" ${onclick}>${label}</button>
                    </li>`);
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

        let debounceTimer;
        document.addEventListener('DOMContentLoaded', () => {
            initFilters();
            loadMitraData();

            // Search with Debounce
            ['filter-search', 'filter-search-deleted'].forEach(id => {
                document.getElementById(id).addEventListener('keyup', () => {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        loadMitraData();
                    }, 500);
                });
            });

            // Tab Change Listener
            const tabEls = document.querySelectorAll('button[data-bs-toggle="tab"]');
            tabEls.forEach(el => {
                el.addEventListener('shown.bs.tab', function(event) {
                    if (event.target.getAttribute('data-bs-target') === '#mitra-deleted') {
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
