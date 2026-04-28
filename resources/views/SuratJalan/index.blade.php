@extends('Layout.main')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap"
        rel="stylesheet">
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

        #sjTable thead th {
            border-bottom: 1px solid #e2e8f0;
            border-top: 1px solid #e2e8f0;
            background: #f8fafc;
            padding-top: 12px;
            padding-bottom: 12px;
        }

        #sjTable tbody td {
            font-size: 13.5px;
            color: #334155;
            padding-top: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f1f5f9;
        }

        .clickable-row {
            cursor: pointer;
            transition: background-color 0.1s;
        }

        .clickable-row:hover {
            background-color: #f8fafc !important;
        }

        .f-badge {
            font-size: 10px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
        }

        .f-draft {
            background: #f1f5f9;
            color: #475569;
        }

        .f-sent {
            background: #dbeafe;
            color: #1e40af;
        }

        .ts-dropdown {
            z-index: 9999 !important;
        }

        /* Dropdown Style */
        .btn-action-dot {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #64748b;
            transition: all 0.2s;
        }

        .btn-action-dot:hover {
            background: #f8fafc;
            color: #334155;
            border-color: #cbd5e0;
        }

        .dropdown-menu-finance {
            border: 0;
            shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border: 1px solid #f1f5f9;
            padding: 8px;
            border-radius: 12px;
            min-width: 180px;
        }

        .dropdown-item-finance {
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 500;
            color: #475569;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .dropdown-item-finance:hover {
            background: #f1f5f9;
            color: #1e293b;
        }

        .dropdown-item-finance i {
            font-size: 14px;
            width: 16px;
            text-align: center;
        }

        .dropdown-item-finance.text-danger:hover {
            background: #fef2f2;
            color: #dc2626;
        }

        .dropdown-item-finance.text-primary:hover {
            background: #eff6ff;
            color: #2563eb;
        }

        .dropdown-item-finance.text-warning:hover {
            background: #fffbeb;
            color: #d97706;
        }

        /* Checkbox Contrast */
        .form-check-input {
            border: 1px solid #94a3b8 !important;
        }

        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd !important;
        }
    </style>
@endpush

@section('main')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box d-md-flex justify-content-between align-items-center">
                            <h4 class="page-title">Surat Jalan</h4>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                                <li class="breadcrumb-item active">Surat Jalan</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                    <div
                        class="card-header bg-white border-bottom py-0 px-4 d-flex align-items-center justify-content-between">
                        <ul class="nav nav-tabs nav-tabs-finance" role="tablist">
                            <li class="nav-item"><a class="nav-link active fw-bold py-3" data-bs-toggle="tab"
                                    href="#sj-active">Aktif</a></li>
                            <li class="nav-item"><a class="nav-link fw-bold py-3" data-bs-toggle="tab"
                                    href="#sj-converted">Sudah Dikonversi</a></li>
                            <li class="nav-item"><a class="nav-link fw-bold py-3" data-bs-toggle="tab"
                                    href="#sj-trash">Terhapus</a></li>
                        </ul>
                        <div class="text-end">
                            <button class="btn btn-primary fw-bold px-4 shadow-sm"
                                onclick="openSuratJalanModal(null, 'create')">
                                <i class="fa fa-plus-circle me-1"></i> Buat Surat Jalan
                            </button>
                        </div>
                    </div>

                    <div class="card-body p-4 bg-white">
                        <div class="mb-4 p-3 rounded-3 bg-light border shadow-sm">
                            <div class="d-flex flex-nowrap align-items-end gap-2 overflow-auto pb-1"
                                style="scrollbar-width: thin;">
                                <div style="min-width: 0; flex: 1.5;">
                                    <label class="f-label mb-1 fw-bold text-muted"
                                        style="font-size: 11px;">Pencarian</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white border-end-0"><i
                                                class="fa fa-search text-muted"></i></span>
                                        <input type="text" id="filter-search"
                                            class="form-control border-start-0 ps-0 shadow-none"
                                            placeholder="No. Surat Jalan / Mitra...">
                                    </div>
                                </div>
                                <div style="min-width: 0; flex: 1.5;">
                                    <label class="f-label mb-1 fw-bold text-muted" style="font-size: 11px;">Mitra</label>
                                    <select id="filter-mitra-id" class="tom-select-init">
                                        <option value="">Semua Mitra...</option>
                                    </select>
                                </div>
                                <div style="min-width: 0; flex: 1.2;">
                                    <label class="f-label mb-1 fw-bold text-muted" style="font-size: 11px;">Sales
                                        Person</label>
                                    <select id="filter-sales-id" class="tom-select-init">
                                        <option value="">Semua Sales...</option>
                                        @foreach ($users ?? [] as $u)
                                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div style="min-width: 0; flex: 1;">
                                    <label class="f-label mb-1 fw-bold text-muted" style="font-size: 11px;">Tgl. Surat
                                        Jalan</label>
                                    <input type="date" id="filter-tgl-invoice"
                                        class="form-control form-control-sm shadow-none">
                                </div>
                                <div style="min-width: 0; flex: 1;" class="d-flex gap-1">
                                    <button onclick="loadSuratJalanData()"
                                        class="btn btn-dark fw-bold btn-sm flex-fill shadow-sm" title="Filter">
                                        <i class="fa fa-filter"></i>
                                    </button>
                                    <button onclick="resetFilter()"
                                        class="btn btn-light border border-danger fw-bold text-danger btn-sm flex-fill"
                                        title="Reset">
                                        <i class="fa fa-sync"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="sjTable">
                                <thead class="bg-light text-uppercase text-secondary fw-bold"
                                    style="font-size: 11px; letter-spacing: 0.5px;">
                                    <tr>
                                        <th width="40" class="text-center ps-3">
                                            <input type="checkbox" class="form-check-input" id="check-all-sj">
                                        </th>
                                        <th width="160">NO. SURAT JALAN</th>
                                        <th width="240">MITRA</th>
                                        <th width="180">SALES PERSON</th>
                                        <th width="120" class="text-center">STATUS</th>
                                        <th width="140" class="text-center">TGL. SURAT JALAN</th>
                                        <th width="80" class="text-end pe-4">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody id="sjTableBody" class="border-top-0">
                                    <tr>
                                        <td colspan="7" class="text-center p-5">
                                            <div class="spinner-border spinner-border-sm text-primary"></div>
                                            <p class="mt-2 text-muted small mb-0">Memuat data...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div
                            class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 pt-3 border-top">
                            <span id="pagination-info" class="text-muted small fw-medium"></span>
                            <nav>
                                <ul class="pagination pagination-sm mb-0 shadow-sm" id="pagination-container"></ul>
                            </nav>
                        </div>

                        <div class="tab-content">
                            <div class="tab-pane active" id="sj-active"></div>
                            <div class="tab-pane" id="sj-converted"></div>
                            <div class="tab-pane" id="sj-trash"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @include('SuratJalan.Modal.modal-fullscreen')
    @include('Mitra.Modal.modal-fullscreen')
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        const API_SJ = '/api/surat-jalan-api';
        const API_MITRA = '/api/mitra-api';
        let currentPage = 1;
        let activeTab = 'active'; // active | converted | trash

        // ── Tab switching ──────────────────────────────────────────────────────────
        document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
            tab.addEventListener('shown.bs.tab', function(e) {
                const target = e.target.getAttribute('href');
                if (target === '#sj-active') activeTab = 'active';
                if (target === '#sj-converted') activeTab = 'converted';
                if (target === '#sj-trash') activeTab = 'trash';
                currentPage = 1;
                loadSuratJalanData();
            });
        });

        // ── TomSelect init ─────────────────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', function() {
            new TomSelect('#filter-mitra-id', {
                maxOptions: 200,
                placeholder: 'Semua Mitra...'
            });
            new TomSelect('#filter-sales-id', {
                maxOptions: 200,
                placeholder: 'Semua Sales...'
            });
            loadMitraOptions();
            loadSuratJalanData();
        });

        async function loadMitraOptions() {
            try {
                const res = await fetch(`${API_MITRA}?per_page=1000`);
                const json = await res.json();
                const ts = document.querySelector('#filter-mitra-id').tomselect;
                if (ts && json.data) {
                    const items = json.data.data ?? json.data;
                    items.forEach(m => ts.addOption({
                        value: m.id,
                        text: m.nama
                    }));
                    ts.refreshOptions(false);
                }
            } catch (e) {}
        }

        // ── Load table data ────────────────────────────────────────────────────────
        async function loadSuratJalanData(page = 1) {
            currentPage = page;
            const params = new URLSearchParams();
            params.append('per_page', 10);
            params.append('page', page);

            const search = document.getElementById('filter-search').value;
            const mitraId = document.getElementById('filter-mitra-id').value;
            const salesId = document.getElementById('filter-sales-id').value;
            const tgl = document.getElementById('filter-tgl-invoice').value;

            if (search) params.append('search', search);
            if (mitraId) params.append('mitra_id', mitraId);
            if (salesId) params.append('sales_id', salesId);
            if (tgl) params.append('tgl_invoice', tgl);

            if (activeTab === 'trash') params.append('tab_status', 'trash');
            if (activeTab === 'converted') params.append('status_dok', 'Sent');

            const tbody = document.getElementById('sjTableBody');
            tbody.innerHTML =
                `<tr><td colspan="7" class="text-center p-5"><div class="spinner-border spinner-border-sm text-primary"></div><p class="mt-2 text-muted small mb-0">Memuat data...</p></td></tr>`;

            try {
                const res = await fetch(`${API_SJ}?${params.toString()}`);
                const json = await res.json();
                renderTable(json.data);
            } catch (e) {
                tbody.innerHTML =
                    `<tr><td colspan="7" class="text-center p-5 text-danger"><i class="fa fa-exclamation-circle me-2"></i>Gagal memuat data.</td></tr>`;
            }
        }

        function renderTable(data) {
            const tbody = document.getElementById('sjTableBody');
            const items = data.data ?? [];

            if (items.length === 0) {
                tbody.innerHTML =
                    `<tr><td colspan="7" class="text-center p-5 text-muted"><i class="fa fa-inbox fa-2x mb-2 d-block opacity-25"></i>Tidak ada data surat jalan.</td></tr>`;
                document.getElementById('pagination-info').textContent = '';
                document.getElementById('pagination-container').innerHTML = '';
                return;
            }

            let html = '';
            items.forEach((sj, idx) => {
                const mitraNama = sj.mitra ? sj.mitra.nama : '-';
                const mitraKode = sj.mitra ? (sj.mitra.nomor_mitra || `#${sj.mitra_id}`) : '';
                const sales = sj.sales ? `<strong>${sj.sales.name}</strong>` :
                    '<span class="text-muted fst-italic">Tanpa Sales</span>';

                let tgl = '-';
                if (sj.tgl_invoice) {
                    const d = new Date(sj.tgl_invoice);
                    const dd = String(d.getDate()).padStart(2, '0');
                    const mm = String(d.getMonth() + 1).padStart(2, '0');
                    const yyyy = d.getFullYear();
                    tgl = `<strong>${dd}/${mm}/${yyyy}</strong>`;
                }

                const statusBadge = sj.status_dok === 'Sent' ?
                    `<span class="f-badge f-sent">Sudah Dikonversi</span>` :
                    `<span class="f-badge f-draft">Draft</span>`;

                let actionsHtml = '';
                if (activeTab === 'trash') {
                    actionsHtml = `
                    <li><a class="dropdown-item dropdown-item-finance text-success" href="javascript:void(0)" onclick="restoreSJ(${sj.id})"><i class="fa fa-undo"></i> Pulihkan</a></li>
                    <li><a class="dropdown-item dropdown-item-finance text-danger" href="javascript:void(0)" onclick="forceDeleteSJ(${sj.id})"><i class="fa fa-trash"></i> Hapus Permanen</a></li>
                `;
                } else {
                    actionsHtml = `
                    <li><a class="dropdown-item dropdown-item-finance" href="/surat-jalan/${sj.id}"><i class="fa fa-eye text-primary"></i> Lihat Detail</a></li>
                    <li><a class="dropdown-item dropdown-item-finance" href="javascript:void(0)" onclick="openSuratJalanModal(${sj.id}, 'edit')"><i class="fa fa-pencil text-warning"></i> Edit Data</a></li>
                    <li><hr class="dropdown-divider opacity-50"></li>
                    <li><a class="dropdown-item dropdown-item-finance text-danger" href="javascript:void(0)" onclick="deleteSJ(${sj.id})"><i class="fa fa-trash"></i> Hapus</a></li>
                `;
                }

                html += `
            <tr class="clickable-row" onclick="window.location='/surat-jalan/${sj.id}'">
                <td class="text-center ps-3" onclick="event.stopPropagation()">
                    <input type="checkbox" class="form-check-input sj-checkbox" value="${sj.id}">
                </td>
                <td class="fw-bold text-dark f-mono">${sj.nomor_invoice}</td>
                <td>
                    <div class="fw-bold text-dark">${mitraNama}</div>
                    <div class="text-muted" style="font-size: 11px;">${mitraKode}</div>
                </td>
                <td>${sales}</td>
                <td class="text-center">${statusBadge}</td>
                <td class="text-center">${tgl}</td>
                <td class="text-end pe-4" onclick="event.stopPropagation()">
                    <div class="dropdown">
                        <button class="btn-action-dot mx-auto ms-auto" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 dropdown-menu-finance">
                            ${actionsHtml}
                        </ul>
                    </div>
                </td>
            </tr>`;
            });

            tbody.innerHTML = html;
            renderPagination(data);
        }

        function renderPagination(data) {
            const info = document.getElementById('pagination-info');
            const container = document.getElementById('pagination-container');
            info.textContent = `Menampilkan ${data.from ?? 0}–${data.to ?? 0} dari ${data.total ?? 0} data`;

            let html = '';
            for (let p = 1; p <= (data.last_page ?? 1); p++) {
                html += `<li class="page-item ${p === data.current_page ? 'active' : ''}">
                <button class="page-link" onclick="loadSuratJalanData(${p})">${p}</button>
            </li>`;
            }
            container.innerHTML = html;
        }

        function resetFilter() {
            document.getElementById('filter-search').value = '';
            document.getElementById('filter-tgl-invoice').value = '';
            const tsM = document.getElementById('filter-mitra-id').tomselect;
            const tsS = document.getElementById('filter-sales-id').tomselect;
            if (tsM) tsM.clear();
            if (tsS) tsS.clear();
            loadSuratJalanData();
        }

        // ── Delete / Restore ───────────────────────────────────────────────────────
        async function deleteSJ(id) {
            if (!confirm('Hapus surat jalan ini?')) return;
            const res = await fetch(`${API_SJ}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            const json = await res.json();
            alert(json.message);
            loadSuratJalanData(currentPage);
        }

        async function restoreSJ(id) {
            const res = await fetch(`${API_SJ}/${id}/restore`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            const json = await res.json();
            alert(json.message);
            loadSuratJalanData(currentPage);
        }

        async function forceDeleteSJ(id) {
            if (!confirm('Hapus permanen? Data tidak dapat dipulihkan.')) return;
            const res = await fetch(`${API_SJ}/${id}/force-delete`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            const json = await res.json();
            alert(json.message);
            loadSuratJalanData(currentPage);
        }
    </script>
@endpush
