@extends('Layout.main')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
    <style>
        .f-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 700; color: #64748b; margin-bottom: 5px; display: block; }
        .f-mono  { font-family: 'JetBrains Mono', monospace; font-size: 13px; }
        #sjTable thead th { border-bottom: 1px solid #e2e8f0; border-top: 1px solid #e2e8f0; background: #f8fafc; padding-top: 12px; padding-bottom: 12px; }
        #sjTable tbody td { font-size: 13px; color: #334155; padding-top: 12px; padding-bottom: 12px; border-bottom: 1px solid #f1f5f9; }
        .clickable-row { cursor: pointer; transition: background-color 0.1s; }
        .clickable-row:hover { background-color: #f1f5f9 !important; }
        .f-badge { font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block; }
        .f-draft    { background: #f1f5f9; color: #475569; }
        .f-sent     { background: #dbeafe; color: #1e40af; }
        .ts-dropdown { z-index: 9999 !important; }
        .ts-wrapper.form-control { overflow: visible !important; }
        .ts-wrapper.tom-select-init { height: 33px !important; }
        .ts-wrapper.tom-select-init .ts-control { height: 33px !important; min-height: 33px !important; padding: 0 12px !important; border-radius: 6px !important; font-size: 13px !important; display: flex !important; align-items: center !important; background-color: #fff !important; border: 1px solid #dee2e6 !important; box-shadow: none !important; }
        .ts-wrapper.tom-select-init .ts-control .item { display: flex !important; align-items: center !important; margin: 0 !important; padding: 0 !important; line-height: 31px !important; height: 31px !important; }
        .ts-wrapper.tom-select-init .ts-control input { display: flex !important; align-items: center !important; margin: 0 !important; padding: 0 !important; font-size: 13px !important; height: 31px !important; }
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
                <div class="card-header bg-white border-bottom py-0 px-4 d-flex align-items-center justify-content-between">
                    <ul class="nav nav-tabs nav-tabs-finance" role="tablist">
                        <li class="nav-item"><a class="nav-link active fw-bold py-3" data-bs-toggle="tab" href="#sj-active">Aktif</a></li>
                        <li class="nav-item"><a class="nav-link fw-bold py-3" data-bs-toggle="tab" href="#sj-converted">Sudah Dikonversi</a></li>
                        <li class="nav-item"><a class="nav-link fw-bold py-3" data-bs-toggle="tab" href="#sj-trash">Terhapus</a></li>
                    </ul>
                    <div class="text-end">
                        <button class="btn btn-primary fw-bold px-4 shadow-sm" onclick="openSuratJalanModal(null, 'create')">
                            <i class="fa fa-plus-circle me-1"></i> Buat Surat Jalan
                        </button>
                    </div>
                </div>

                <div class="card-body p-4 bg-white">
                    <div class="mb-4 p-3 rounded-3 bg-light border shadow-sm">
                        <div class="d-flex flex-nowrap align-items-end gap-2 overflow-auto pb-1" style="scrollbar-width: thin;">
                            <div style="min-width: 0; flex: 1.5;">
                                <label class="f-label mb-1 fw-bold text-muted" style="font-size: 11px;">Pencarian</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white border-end-0"><i class="fa fa-search text-muted"></i></span>
                                    <input type="text" id="filter-search" class="form-control border-start-0 ps-0 shadow-none" placeholder="No. Surat Jalan / Mitra...">
                                </div>
                            </div>
                            <div style="min-width: 0; flex: 1.5;">
                                <label class="f-label mb-1 fw-bold text-muted" style="font-size: 11px;">Mitra</label>
                                <select id="filter-mitra-id" class="tom-select-init">
                                    <option value="">Semua Mitra...</option>
                                </select>
                            </div>
                            <div style="min-width: 0; flex: 1.2;">
                                <label class="f-label mb-1 fw-bold text-muted" style="font-size: 11px;">Sales Person</label>
                                <select id="filter-sales-id" class="tom-select-init">
                                    <option value="">Semua Sales...</option>
                                    @foreach ($users ?? [] as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div style="min-width: 0; flex: 1;">
                                <label class="f-label mb-1 fw-bold text-muted" style="font-size: 11px;">Tgl. Surat Jalan</label>
                                <input type="date" id="filter-tgl-invoice" class="form-control form-control-sm shadow-none">
                            </div>
                            <div style="min-width: 0; flex: 1;" class="d-flex gap-1">
                                <button onclick="loadSuratJalanData()" class="btn btn-dark fw-bold btn-sm flex-fill shadow-sm" title="Filter">
                                    <i class="fa fa-filter"></i>
                                </button>
                                <button onclick="resetFilter()" class="btn btn-light border border-danger fw-bold text-danger btn-sm flex-fill" title="Reset">
                                    <i class="fa fa-sync"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="sjTable">
                            <thead class="bg-light text-uppercase text-secondary fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">
                                <tr>
                                    <th width="40" class="text-center ps-3">#</th>
                                    <th width="160">No. Surat Jalan</th>
                                    <th width="220">Mitra</th>
                                    <th width="160">Sales Person</th>
                                    <th width="120" class="text-center">Status</th>
                                    <th width="120" class="text-center">Tgl. Surat Jalan</th>
                                    <th width="100" class="text-end pe-3">Tindakan</th>
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

                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 pt-3 border-top">
                        <span id="pagination-info" class="text-muted small fw-medium"></span>
                        <nav><ul class="pagination pagination-sm mb-0 shadow-sm" id="pagination-container"></ul></nav>
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
    const API_SJ   = '/api/surat-jalan-api';
    const API_MITRA = '/api/mitra-api';
    let currentPage = 1;
    let activeTab   = 'active'; // active | converted | trash

    // ── Tab switching ──────────────────────────────────────────────────────────
    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function (e) {
            const target = e.target.getAttribute('href');
            if (target === '#sj-active')    activeTab = 'active';
            if (target === '#sj-converted') activeTab = 'converted';
            if (target === '#sj-trash')     activeTab = 'trash';
            currentPage = 1;
            loadSuratJalanData();
        });
    });

    // ── TomSelect init ─────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        new TomSelect('#filter-mitra-id', { maxOptions: 200, placeholder: 'Semua Mitra...' });
        new TomSelect('#filter-sales-id', { maxOptions: 200, placeholder: 'Semua Sales...' });
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
                items.forEach(m => ts.addOption({ value: m.id, text: m.nama }));
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

        const search   = document.getElementById('filter-search').value;
        const mitraId  = document.getElementById('filter-mitra-id').value;
        const salesId  = document.getElementById('filter-sales-id').value;
        const tgl      = document.getElementById('filter-tgl-invoice').value;

        if (search)  params.append('search', search);
        if (mitraId) params.append('mitra_id', mitraId);
        if (salesId) params.append('sales_id', salesId);
        if (tgl)     params.append('tgl_invoice', tgl);

        if (activeTab === 'trash')     params.append('tab_status', 'trash');
        if (activeTab === 'converted') params.append('status_dok', 'Sent');

        const tbody = document.getElementById('sjTableBody');
        tbody.innerHTML = `<tr><td colspan="7" class="text-center p-5"><div class="spinner-border spinner-border-sm text-primary"></div><p class="mt-2 text-muted small mb-0">Memuat data...</p></td></tr>`;

        try {
            const res  = await fetch(`${API_SJ}?${params.toString()}`);
            const json = await res.json();
            renderTable(json.data);
        } catch (e) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center p-5 text-danger"><i class="fa fa-exclamation-circle me-2"></i>Gagal memuat data.</td></tr>`;
        }
    }

    function renderTable(data) {
        const tbody = document.getElementById('sjTableBody');
        const items = data.data ?? [];

        if (items.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center p-5 text-muted"><i class="fa fa-inbox fa-2x mb-2 d-block opacity-25"></i>Tidak ada data surat jalan.</td></tr>`;
            document.getElementById('pagination-info').textContent = '';
            document.getElementById('pagination-container').innerHTML = '';
            return;
        }

        let html = '';
        items.forEach((sj, idx) => {
            const no       = (data.current_page - 1) * data.per_page + idx + 1;
            const mitra    = sj.mitra ? sj.mitra.nama : '-';
            const sales    = sj.sales ? sj.sales.name : '<span class="text-muted fst-italic">Tanpa Sales</span>';
            const tgl      = sj.tgl_invoice ? new Date(sj.tgl_invoice).toLocaleDateString('id-ID') : '-';
            const statusBadge = sj.status_dok === 'Sent'
                ? `<span class="f-badge f-sent">Sudah Dikonversi</span>`
                : `<span class="f-badge f-draft">Draft</span>`;

            const actions = activeTab === 'trash'
                ? `<button class="btn btn-sm btn-outline-success fw-bold" onclick="restoreSJ(${sj.id})" title="Pulihkan"><i class="fa fa-undo"></i></button>
                   <button class="btn btn-sm btn-outline-danger fw-bold ms-1" onclick="forceDeleteSJ(${sj.id})" title="Hapus Permanen"><i class="fa fa-trash"></i></button>`
                : `<a href="/surat-jalan/${sj.id}" class="btn btn-sm btn-outline-primary fw-bold" title="Detail"><i class="fa fa-eye"></i></a>
                   <button class="btn btn-sm btn-outline-secondary fw-bold ms-1" onclick="openSuratJalanModal(${sj.id}, 'edit')" title="Edit"><i class="fa fa-pencil"></i></button>
                   <button class="btn btn-sm btn-outline-danger fw-bold ms-1" onclick="deleteSJ(${sj.id})" title="Hapus"><i class="fa fa-trash"></i></button>`;

            html += `
            <tr class="clickable-row" onclick="window.location='/surat-jalan/${sj.id}'">
                <td class="text-center ps-3 text-muted">${no}</td>
                <td class="fw-bold text-primary f-mono">${sj.nomor_invoice}</td>
                <td>${mitra}</td>
                <td>${sales}</td>
                <td class="text-center">${statusBadge}</td>
                <td class="text-center">${tgl}</td>
                <td class="text-end pe-3" onclick="event.stopPropagation()">${actions}</td>
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
        const res  = await fetch(`${API_SJ}/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
        const json = await res.json();
        alert(json.message);
        loadSuratJalanData(currentPage);
    }

    async function restoreSJ(id) {
        const res  = await fetch(`${API_SJ}/${id}/restore`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
        const json = await res.json();
        alert(json.message);
        loadSuratJalanData(currentPage);
    }

    async function forceDeleteSJ(id) {
        if (!confirm('Hapus permanen? Data tidak dapat dipulihkan.')) return;
        const res  = await fetch(`${API_SJ}/${id}/force-delete`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
        const json = await res.json();
        alert(json.message);
        loadSuratJalanData(currentPage);
    }
</script>
@endpush
