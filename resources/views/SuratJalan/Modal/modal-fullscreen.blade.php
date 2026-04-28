<style>
    .modal-backdrop-custom {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, .6);
        z-index: 1065;
        display: none;
    }

    .sj-card {
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
    }

    .sj-table-header th {
        border-bottom: 2px solid #edf2f7 !important;
        background: #f8fafc !important;
        font-size: 11px !important;
        letter-spacing: 0.05em;
        padding: 15px !important;
        border-top: none !important;
        border-radius: 0 !important;
    }

    .sj-btn-add {
        border: 1px solid #ffc107;
        background: #ffc107;
        color: #000;
        transition: all 0.2s;
        font-size: 11px;
        letter-spacing: 0.05em;
    }

    .sj-btn-add:hover {
        background: #e0a800;
        border-color: #e0a800;
        color: #000;
        transform: translateY(-1px);
    }

    /* Stock Selection Styles */
    #selectedStockContainer .badge {
        transition: all 0.2s;
    }

    #selectedStockContainer .badge:hover {
        transform: scale(1.02);
    }

    .cursor-pointer {
        cursor: pointer;
    }
</style>

<div class="modal fade" id="sjModal" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content border-0" style="background:#f8fafc;">
            <div class="modal-header bg-white border-bottom px-4 sticky-top shadow-sm d-flex align-items-center"
                style="min-height: 110px;">
                <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-3 me-3">
                    <i class="fa fa-truck fa-xl"></i>
                </div>
                <div class="flex-grow-1">
                    <h4 class="modal-title fw-bold text-dark mb-1" id="sjModalTitle" style="letter-spacing: -0.02em;">
                        Surat Jalan Baru</h4>
                    <p class="text-muted small mb-0 fw-medium">Pengiriman barang tanpa harga — akan dikonversi ke
                        invoice secara otomatis.</p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-light border-2 fw-bold px-4 py-2 text-secondary shadow-sm"
                        data-bs-dismiss="modal" style="letter-spacing: 0.05em; min-width: 120px;">
                        BATAL
                    </button>
                    <button type="button" class="btn btn-warning fw-bold px-4 py-2 shadow-sm text-dark"
                        onclick="saveSuratJalan()" style="letter-spacing: 0.05em; min-width: 240px;">
                        <i class="fa fa-check-circle me-2"></i>SIMPAN SURAT JALAN
                    </button>
                </div>
            </div>

            <div class="modal-body p-0">
                <form id="sjForm" class="h-100">
                    <input type="hidden" id="sj_form_mode" value="create">
                    <input type="hidden" id="sj_edit_id" value="">

                    <div class="row g-0 h-100">
                        <!-- Left Panel -->
                        <div class="col-lg-12 h-100 overflow-auto p-4">

                            <!-- Header Info -->
                            <div class="card sj-card border-0 rounded-3 mb-4">
                                <div class="card-body p-4">
                                    <div class="row g-4">
                                        <!-- Mitra -->
                                        <div class="col-lg-6 border-end">
                                            <label class="f-label mb-2">Mitra (Client) <span
                                                    class="text-danger">*</span></label>
                                            <select id="sj_mitra_id" class="form-select mb-2"
                                                onchange="renderSjMitraDetail()">
                                                <option value="">Cari Mitra...</option>
                                            </select>
                                            <div id="sj_mitra_detail" class="d-none bg-light p-3 rounded border">
                                                <div class="fw-bold text-dark mb-1" id="sj_disp_nama">-</div>
                                                <div class="small text-muted mb-1"><i
                                                        class="fa fa-map-marker-alt me-2 text-secondary"></i><span
                                                        id="sj_disp_alamat">-</span></div>
                                                <div class="small text-muted"><i
                                                        class="fa fa-phone me-2 text-secondary"></i><span
                                                        id="sj_disp_telp">-</span></div>
                                            </div>
                                            <div id="sj_mitra_empty"
                                                class="text-center py-4 text-muted border border-dashed rounded bg-light">
                                                <i class="fa fa-user-plus mb-2 fs-4 text-secondary"></i>
                                                <div class="small">Pilih pelanggan untuk melihat detail</div>
                                            </div>
                                        </div>

                                        <!-- Doc Details -->
                                        <div class="col-lg-6">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="f-label">Nomor Surat Jalan</label>
                                                    <input type="text" id="sj_nomor"
                                                        class="form-control fw-bold bg-light text-warning"
                                                        placeholder="Auto Generated">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="f-label">Referensi / PO</label>
                                                    <input type="text" id="sj_ref_no" class="form-control"
                                                        placeholder="Contoh: PO-001">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="f-label">Tanggal Surat Jalan</label>
                                                    <input type="date" id="sj_tgl" class="form-control">
                                                </div>
                                                <div class="col-12">
                                                    <label class="f-label">Salesperson</label>
                                                    <select id="sj_sales_id" class="form-select">
                                                        <option value="0">Tanpa Sales Person</option>
                                                        @foreach ($users ?? [] as $u)
                                                            <option value="{{ $u->id }}">{{ $u->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Items -->
                            <div class="card sj-card border-0 rounded-3 overflow-hidden mb-4">
                                <div
                                    class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold text-dark text-uppercase small"
                                        style="letter-spacing: 0.05em;">
                                        <i class="fa fa-box me-2 text-warning"></i>Daftar Barang Pengiriman
                                    </h6>
                                    <div class="d-flex gap-2">
                                        <button type="button"
                                            class="btn btn-outline-secondary btn-sm fw-bold px-3 rounded-pill"
                                            onclick="openStockModal()">
                                            <i class="fa fa-search me-1"></i> CARI STOK
                                        </button>
                                        <button type="button"
                                            class="btn sj-btn-add btn-sm fw-bold px-4 py-2 rounded-pill shadow-sm"
                                            onclick="addSjRow()">
                                            <i class="fa fa-plus-circle me-2"></i>TAMBAH BARANG
                                        </button>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table align-middle mb-0 table-hover" id="sjItemTable">
                                        <thead class="text-secondary sj-table-header">
                                            <tr>
                                                <th class="ps-4 py-3" width="40%">Produk / Deskripsi</th>
                                                <th class="text-center" width="15%">Qty</th>
                                                <th class="text-center" width="15%">Satuan</th>
                                                <th class="py-3" width="25%">Keterangan Baris</th>
                                                <th width="5%"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="sjItemBody"></tbody>
                                        <tfoot class="bg-light">
                                            <tr>
                                                <td colspan="5"
                                                    class="text-center py-2 text-muted small fst-italic">
                                                    Klik tombol tambah untuk memasukkan barang
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="card sj-card border-0 rounded-3 mb-5">
                                <div class="card-body p-4">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="f-label mb-2">Catatan</label>
                                            <textarea id="sj_keterangan" class="form-control bg-light border-0" rows="3"
                                                placeholder="Catatan pengiriman..."></textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="f-label mb-2">Syarat & Ketentuan</label>
                                            <textarea id="sj_syarat" class="form-control bg-light border-0" rows="3">Barang diterima dalam kondisi baik.</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Row Template -->
<template id="sjRowTemplate">
    <tr class="border-bottom border-light sj-product-row">
        <td class="ps-4 py-3">
            <input type="hidden" class="sj-prod-id">
            <div class="mb-1"><select class="sj-prod-select"></select></div>
            <input type="text" class="form-control form-control-sm text-muted small sj-prod-desc"
                placeholder="Deskripsi (Opsional)">
        </td>
        <td class="py-3">
            <input type="number" class="form-control form-control-sm text-center sj-prod-qty" value="1"
                min="0.01" step="0.01">
        </td>
        <td class="py-3 text-center">
            <span class="badge bg-light text-secondary border sj-prod-unit">-</span>
        </td>
        <td class="py-3">
            <input type="text" class="form-control form-control-sm sj-prod-note" placeholder="Catatan baris...">
        </td>
        <td class="py-3 text-center">
            <button type="button" class="btn btn-link text-danger p-0 sj-btn-remove">
                <i class="fa fa-times"></i>
            </button>
        </td>
    </tr>
</template>

<!-- Stock Selection Modal -->
<div class="modal fade" id="stockModal" tabindex="-1" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4" style="height: 90vh;">
            <div class="modal-header bg-warning border-bottom py-3 px-4 shadow-sm">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="fa fa-search me-2"></i>PILIH PRODUK PENGIRIMAN
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="p-3 bg-light border-bottom">
                    <div class="row g-2">
                        <div class="col-md-5">
                            <label class="small fw-bold text-muted mb-1">CARI NAMA / KODE</label>
                            <input type="text" class="form-control border-secondary shadow-none"
                                id="stockSearchInput" placeholder="Ketik nama produk atau SKU...">
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold text-muted mb-1">FILTER KATEGORI</label>
                            <select class="form-select border-secondary shadow-none" id="stockCategoryFilter">
                                <option value="">Semua Kategori</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-dark fw-bold w-100 py-2 shadow-sm"
                                onclick="resetStockFilters()">
                                <i class="fa fa-refresh me-1"></i> RESET FILTER
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Selected Items Chips -->
                <div id="selectedStockContainer" class="p-3 border-bottom bg-white d-none"
                    style="max-height: 120px; overflow-y: auto;">
                    <div class="small text-muted mb-2 fw-bold text-uppercase"
                        style="font-size: 10px; letter-spacing: 0.5px;">
                        Item Terpilih:
                    </div>
                    <div class="d-flex flex-wrap gap-2" id="selectedStockList"></div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="stockTable">
                        <thead class="bg-dark text-white sticky-top">
                            <tr>
                                <th width="5%" class="text-center py-3">
                                    <input type="checkbox" class="form-check-input border-white" id="checkAllStock"
                                        onclick="toggleAllStock(this)">
                                </th>
                                <th class="py-3 text-white">PRODUK / SKU</th>
                                <th class="py-3 text-white">KATEGORI</th>
                                <th class="text-center py-3 text-white">STOK TERSEDIA</th>
                                <th class="text-center py-3 pe-4 text-white">SATUAN</th>
                            </tr>
                        </thead>
                        <tbody id="stockBodyList"></tbody>
                    </table>
                </div>
                <div id="stockPagination" class="p-3 bg-light border-top d-flex justify-content-center"></div>
            </div>
            <div class="modal-footer bg-light border-top py-3">
                <div class="d-flex justify-content-between w-100 align-items-center">
                    <div class="small text-muted">
                        <i class="fa fa-info-circle me-1"></i> Produk terpilih akan ditambahkan tanpa harga.
                    </div>
                    <button type="button" class="btn btn-warning fw-bold px-4 mt-2 rounded-pill text-dark shadow-sm"
                        onclick="addSelectedStocks()">
                        <i class="fa fa-check me-2"></i> Tambahkan Ke Daftar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<template id="stockRowTemplate">
    <tr class="stock-item-row">
        <td class="text-center">
            <input type="checkbox" class="form-check-input border-secondary stock-check">
        </td>
        <td>
            <div class="fw-bold text-dark col-stock-nama" style="font-size: 14px;"></div>
            <div class="small text-muted font-monospace col-stock-sku"></div>
        </td>
        <td><span class="badge bg-secondary text-white col-stock-kategori"></span></td>
        <td class="text-center fw-bolder col-stock-qty text-dark" style="font-size: 15px;"></td>
        <td class="text-center col-stock-unit pe-4"></td>
    </tr>
</template>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<script>
    const SJ_API_CREATE = '/api/surat-jalan-create-api';
    const SJ_API_BASE = '/api/surat-jalan-api';
    const SJ_MITRA_API = '/api/mitra-api';
    const SJ_PROD_API = '/api/product-api';

    let sjMasterProduk = [];
    let sjMasterMitra = [];
    let sjMasterSales = [];
    let sjTomMitra = null;
    let sjTomSales = null;

    let selectedStockItems = [];
    let stockCache = [];
    let isSjModalInitializing = false;

    // ── Open Modal ────────────────────────────────────────────────────────────────
    async function openSuratJalanModal(id = null, mode = 'create') {
        isSjModalInitializing = true;
        const modal = new bootstrap.Modal(document.getElementById('sjModal'));

        // Reset form
        document.getElementById('sjForm').reset();
        document.getElementById('sjItemBody').innerHTML = '';
        document.getElementById('sj_mitra_detail').classList.add('d-none');
        document.getElementById('sj_mitra_empty').classList.remove('d-none');

        // Load master data if needed
        if (sjMasterProduk.length === 0) await loadSjMasterProduk();
        if (sjMasterMitra.length === 0) await loadSjMasterMitra();
        if (sjMasterSales.length === 0) await loadSjMasterSales();

        initSjTomMitra();
        initSjTomSales();

        if (mode === 'edit' && id) {
            document.getElementById('sjModalTitle').innerText = 'Edit Surat Jalan';
            document.getElementById('sj_form_mode').value = 'edit';
            document.getElementById('sj_edit_id').value = id;

            try {
                const res = await fetch(`${SJ_API_BASE}/${id}`);
                const result = await res.json();
                if (result.success) {
                    const sj = result.data;
                    document.getElementById('sj_nomor').value = sj.nomor_invoice;
                    document.getElementById('sj_ref_no').value = sj.ref_no || '';
                    document.getElementById('sj_tgl').value = sj.tgl_invoice;
                    document.getElementById('sj_keterangan').value = sj.keterangan || '';
                    document.getElementById('sj_syarat').value = sj.syarat_ketentuan || '';

                    if (sjTomMitra) sjTomMitra.setValue(String(sj.mitra_id), true);
                    if (sjTomSales) sjTomSales.setValue(String(sj.sales_id || 0), true);

                    renderSjMitraDetail(false);

                    (sj.items || []).forEach(item => addSjRow(item));
                }
            } catch (e) {
                console.error(e);
                alert('Gagal memuat data surat jalan');
            }
        } else {
            document.getElementById('sjModalTitle').innerText = 'Surat Jalan Baru';
            document.getElementById('sj_form_mode').value = 'create';
            document.getElementById('sj_edit_id').value = '';
            document.getElementById('sj_tgl').value = new Date().toISOString().split('T')[0];
            document.getElementById('sj_nomor').value = 'SJ/' + new Date().getFullYear() + '/' + Date.now()
                .toString().slice(-5);
            renderSjMitraDetail(false);
            addSjRow();
        }

        isSjModalInitializing = false;
        modal.show();
    }

    // ── Master Data ───────────────────────────────────────────────────────────────
    async function loadSjMasterProduk() {
        try {
            const res = await fetch(`${SJ_PROD_API}?per_page=1000`);
            const result = await res.json();
            if (result.success) sjMasterProduk = result.data.data || result.data;
        } catch (e) {
            console.error('Gagal load produk', e);
        }
    }

    async function loadSjMasterMitra() {
        try {
            const res = await fetch(`${SJ_MITRA_API}?per_page=1000&tipe_mitra=Client`);
            const result = await res.json();
            if (result.success) sjMasterMitra = result.data.data || result.data;
        } catch (e) {
            console.error('Gagal load mitra', e);
        }
    }

    async function loadSjMasterSales() {
        try {
            const res = await fetch(`/api/user-api?per_page=1000&is_sales=1`);
            const result = await res.json();
            if (result.success) {
                sjMasterSales = result.data.data || result.data || [];
            }
        } catch (e) {
            console.error('Gagal load sales', e);
        }
    }

    // ── TomSelect ─────────────────────────────────────────────────────────────────
    function initSjTomMitra(selectedId = null) {
        if (sjTomMitra) {
            try {
                sjTomMitra.destroy();
            } catch (_) {}
            sjTomMitra = null;
        }

        const sel = document.getElementById('sj_mitra_id');
        sel.innerHTML = '';

        sjTomMitra = new TomSelect('#sj_mitra_id', {
            options: sjMasterMitra.map(m => ({
                id: m.id,
                nama: m.nama,
                info: [m.no_hp, m.alamat].filter(Boolean).join(' • ')
            })),
            valueField: 'id',
            labelField: 'nama',
            searchField: ['nama', 'info'],
            allowEmptyOption: true,
            placeholder: 'Cari Mitra (Client)...',
            maxOptions: 1000,
            create: false,
            dropdownParent: 'body',
            render: {
                option: (d, esc) =>
                    `<div><div class="fw-bold">${esc(d.nama)}</div>${d.info ? `<div class="small text-muted">${esc(d.info)}</div>` : ''}</div>`,
                item: (d, esc) => `<div>${esc(d.nama)}</div>`
            },
            onChange: () => renderSjMitraDetail(true)
        });

        if (selectedId) sjTomMitra.setValue(String(selectedId), true);
    }

    function initSjTomSales(selectedId = null) {
        if (sjTomSales) {
            try {
                sjTomSales.destroy();
            } catch (_) {}
            sjTomSales = null;
        }

        const sel = document.getElementById('sj_sales_id');
        if (!sel) return;

        sel.innerHTML = '<option value="0">Tanpa Sales Person</option>';

        sjTomSales = new TomSelect('#sj_sales_id', {
            options: [
                { id: '0', name: 'Tanpa Sales Person' },
                ...sjMasterSales.map(s => ({ id: String(s.id), name: s.name }))
            ],
            valueField: 'id',
            labelField: 'name',
            searchField: ['name'],
            allowEmptyOption: true,
            placeholder: 'Pilih Sales...',
            create: false,
            dropdownParent: 'body'
        });

        if (selectedId) sjTomSales.setValue(String(selectedId), true);
    }

    // ── Mitra Detail ──────────────────────────────────────────────────────────────
    function renderSjMitraDetail(autoSetSales = true) {
        if (isSjModalInitializing) autoSetSales = false;

        const id = sjTomMitra ? sjTomMitra.getValue() : document.getElementById('sj_mitra_id').value;
        const detail = document.getElementById('sj_mitra_detail');
        const empty = document.getElementById('sj_mitra_empty');

        if (!id) {
            detail.classList.add('d-none');
            empty.classList.remove('d-none');
            return;
        }

        const m = sjMasterMitra.find(x => String(x.id) === String(id));
        if (m) {
            document.getElementById('sj_disp_nama').innerText = m.nama;
            document.getElementById('sj_disp_alamat').innerText = m.alamat || '-';
            document.getElementById('sj_disp_telp').innerText = m.no_hp || '-';
            
            if (autoSetSales && sjTomSales) {
                if (m.salesperson_id) {
                    sjTomSales.setValue(String(m.salesperson_id), true);
                } else {
                    sjTomSales.setValue("0", true);
                }
            }
            
            detail.classList.remove('d-none');
            empty.classList.add('d-none');
        }
    }

    // ── Helper: Get Used Product IDs ──────────────────────────────────────────────
    function getUsedSjProductIds(excludeTs = null) {
        const ids = [];
        document.querySelectorAll('.sj-prod-select').forEach(sel => {
            if (sel.tomselect) {
                if (excludeTs && sel.tomselect === excludeTs) return;
                const val = sel.tomselect.getValue();
                if (val) ids.push(String(val));
            }
        });
        return ids;
    }

    // ── Add Row ───────────────────────────────────────────────────────────────────
    function addSjRow(data = null) {
        const tbody = document.getElementById('sjItemBody');
        const template = document.getElementById('sjRowTemplate');
        const clone = template.content.cloneNode(true);
        const tr = clone.querySelector('tr');

        const selectEl = tr.querySelector('.sj-prod-select');
        const idInput = tr.querySelector('.sj-prod-id');
        const qtyInput = tr.querySelector('.sj-prod-qty');
        const unitLabel = tr.querySelector('.sj-prod-unit');
        const descInput = tr.querySelector('.sj-prod-desc');

        const ts = new TomSelect(selectEl, {
            options: sjMasterProduk.map(p => ({
                id: String(p.id),
                nama: p.nama_produk,
                sku: p.sku_kode || '',
                unit: p.satuan || 'Pcs',
                qty: Number(p.qty || 0)
            })),
            valueField: 'id',
            labelField: 'nama',
            searchField: ['nama', 'sku'],
            onDropdownOpen: function() {
                const usedIds = getUsedSjProductIds(this);
                // We refresh the options to only show unused ones
                // But we must keep the currently selected one in the list!
                const currentVal = this.getValue();
                
                this.clearOptions();
                const filtered = sjMasterProduk.filter(p => !usedIds.includes(String(p.id)) || String(p.id) === String(currentVal));
                this.addOptions(filtered.map(p => ({
                    id: String(p.id),
                    nama: p.nama_produk,
                    sku: p.sku_kode || '',
                    unit: p.satuan || 'Pcs',
                    qty: Number(p.qty || 0)
                })));
                this.refreshOptions(false);
            },
            placeholder: 'Cari barang...',
            create: false,
            dropdownParent: 'body',
            render: {
                option: (d, esc) => {
                    const low = d.qty <= 0;
                    return `<div style="${low ? 'opacity:.5' : ''}">
                    <div class="fw-bold">${esc(d.nama)}
                        <span class="badge ${low ? 'bg-danger' : 'bg-light text-dark border'} float-end">${low ? 'Habis' : d.qty + ' ' + esc(d.unit)}</span>
                    </div>
                    <small class="text-muted">${esc(d.sku)}</small>
                </div>`;
                },
                item: (d, esc) => `<div>${esc(d.nama)}</div>`
            },
            onChange: function(val) {
                const opt = this.options[val];
                if (opt) {
                    idInput.value = opt.id;
                    unitLabel.innerText = opt.unit;
                }
            }
        });

        if (data) {
            setTimeout(() => {
                const prodId = String(data.produk_id || data.id || '');
                if (prodId && ts.options[prodId]) {
                    ts.setValue(prodId, true);
                } else if (prodId) {
                    ts.addOption({
                        id: prodId,
                        nama: data.nama_produk_manual || data.nama_produk || 'Produk',
                        unit: 'Pcs',
                        qty: 999
                    });
                    ts.setValue(prodId, true);
                }
                idInput.value = prodId;
                qtyInput.value = parseFloat(data.qty) || 1;
                descInput.value = data.deskripsi_produk || '';
                unitLabel.innerText = data.product?.satuan || data.satuan || 'Pcs';
            }, 50);
        }

        tr.querySelector('.sj-btn-remove').onclick = () => {
            ts.destroy();
            tr.remove();
        };
        tbody.appendChild(tr);
    }

    // ── Save ──────────────────────────────────────────────────────────────────────
    async function saveSuratJalan() {
        const mode = document.getElementById('sj_form_mode').value;
        const id = document.getElementById('sj_edit_id').value;

        const mitraId = sjTomMitra ? sjTomMitra.getValue() : document.getElementById('sj_mitra_id').value;
        const salesId = sjTomSales ? sjTomSales.getValue() : document.getElementById('sj_sales_id').value;

        if (!mitraId) {
            alert('Harap pilih Mitra!');
            return;
        }

        const nomor = document.getElementById('sj_nomor').value;
        const tgl = document.getElementById('sj_tgl').value;
        if (!nomor) {
            alert('Nomor surat jalan wajib diisi.');
            return;
        }
        if (!tgl) {
            alert('Tanggal surat jalan wajib diisi.');
            return;
        }

        // Collect items
        const items = [];
        document.querySelectorAll('#sjItemBody tr').forEach(row => {
            const qtyEl = row.querySelector('.sj-prod-qty');
            const idEl = row.querySelector('.sj-prod-id');
            const descEl = row.querySelector('.sj-prod-desc');
            if (!qtyEl) return;

            const qty = parseFloat(qtyEl.value) || 0;
            if (qty <= 0) return;

            // Get product name and ID from TomSelect
            const selectEl = row.querySelector('.sj-prod-select');
            let namaProduk = '';
            let productId = null;
            if (selectEl && selectEl.tomselect) {
                const val = selectEl.tomselect.getValue();
                if (val && selectEl.tomselect.options[val]) {
                    namaProduk = selectEl.tomselect.options[val].nama;
                    productId = val;
                }
            }

            // Skip if no product selected
            if (!productId && !namaProduk) return;

            items.push({
                produk_id: productId,
                nama_produk_manual: namaProduk,
                deskripsi_produk: descEl ? descEl.value : '',
                qty: qty,
                harga_satuan: 0,
                diskon_nilai: 0,
                total_harga_item: 0,
            });
        });

        if (items.length === 0) {
            alert('Harap tambahkan minimal satu barang.');
            return;
        }

        const payload = {
            invoice: {
                nomor_invoice: nomor,
                tgl_invoice: tgl,
                ref_no: document.getElementById('sj_ref_no').value,
                mitra_id: mitraId,
                sales_id: salesId || 0,
                keterangan: document.getElementById('sj_keterangan').value,
                syarat_ketentuan: document.getElementById('sj_syarat').value,
            },
            items: items
        };

        const url = mode === 'edit' ? `${SJ_API_BASE}/${id}` : SJ_API_CREATE;
        const method = mode === 'edit' ? 'PUT' : 'POST';

        const btn = document.querySelector('[onclick="saveSuratJalan()"]');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Menyimpan...';

        try {
            const res = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            });
            const result = await res.json();

            if (result.success) {
                bootstrap.Modal.getInstance(document.getElementById('sjModal')).hide();
                const newId = result.data?.id;
                if (newId) {
                    window.location.href = `/surat-jalan/${newId}`;
                } else {
                    loadSuratJalanData();
                }
            } else {
                if (result.errors) {
                    let msg = 'Terjadi kesalahan:\n';
                    Object.values(result.errors).forEach(e => msg += `- ${e}\n`);
                    alert(msg);
                } else {
                    alert('Gagal: ' + result.message);
                }
            }
        } catch (e) {
            console.error(e);
            alert('Terjadi kesalahan saat menyimpan.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-save me-2"></i>Simpan';
        }
    }

    // Clean up TomSelect on modal close
    document.getElementById('sjModal').addEventListener('hidden.bs.modal', () => {
        if (sjTomMitra) {
            try {
                sjTomMitra.destroy();
            } catch (_) {}
            sjTomMitra = null;
        }
        if (sjTomSales) {
            try {
                sjTomSales.destroy();
            } catch (_) {}
            sjTomSales = null;
        }
    });

    // ── Stock Modal Logic (Integrated from Invoice) ───────────────────────────────
    function showCustomBackdrop() {
        let backdrop = document.querySelector('.modal-backdrop-custom');
        if (!backdrop) {
            backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop-custom';
            document.body.appendChild(backdrop);
        }
        backdrop.style.display = 'block';
    }

    function hideCustomBackdrop() {
        const backdrop = document.querySelector('.modal-backdrop-custom');
        if (backdrop) backdrop.style.display = 'none';
    }

    async function openStockModal() {
        const modalEl = document.getElementById('stockModal');
        const modal = new bootstrap.Modal(modalEl);

        selectedStockItems = [];
        renderSelectedStockBadges();

        document.getElementById('stockBodyList').innerHTML =
            '<tr><td colspan="5" class="text-center p-4"><div class="spinner-border text-warning"></div></td></tr>';

        showCustomBackdrop();
        modal.show();

        modalEl.addEventListener('hidden.bs.modal', () => hideCustomBackdrop(), {
            once: true
        });

        loadStockCategories();
        setupStockSearch();
        fetchStockPage(1);
    }

    async function fetchStockPage(page = 1) {
        const searchInput = document.getElementById('stockSearchInput');
        const categoryFilter = document.getElementById('stockCategoryFilter');
        const searchTerm = searchInput ? searchInput.value.trim() : '';
        const categoryId = categoryFilter ? categoryFilter.value : '';

        const tbody = document.getElementById('stockBodyList');
        tbody.innerHTML =
            '<tr><td colspan="5" class="text-center p-4"><div class="spinner-border text-warning"></div></td></tr>';

        try {
            let url = `/api/product-api`;
            let params = new URLSearchParams({
                page: page,
                per_page: 10
            });
            if (searchTerm) params.append('search', searchTerm);
            if (categoryId) params.append('category', categoryId);

            const res = await fetch(url + '?' + params.toString());
            const result = await res.json();

            if (result.success) {
                const data = result.data.data || result.data;
                renderStockList(data);
                renderPaginationControls(result.data, 'stockPagination', fetchStockPage);
            }
        } catch (e) {
            console.error(e);
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger p-4">Gagal memuat data</td></tr>';
        }
    }

    function renderStockList(data) {
        const tbody = document.getElementById('stockBodyList');
        const template = document.getElementById('stockRowTemplate');
        tbody.innerHTML = '';

        const existingIds = Array.from(document.querySelectorAll('#sjItemBody .sj-prod-id')).map(i => i.value);

        const usedIds = getUsedSjProductIds();

        data.forEach(item => {
            // Exclude already selected items from list
            if (usedIds.includes(String(item.id))) return;

            const clone = template.content.cloneNode(true);
            const tr = clone.querySelector('tr');
            const chk = clone.querySelector('.stock-check');

            chk.value = item.id;
            const qty = Number(item.qty || 0);

            if (qty <= 0) {
                tr.style.opacity = '0.5';
                tr.style.backgroundColor = '#f8fafc';
                chk.disabled = true;
            }

            if (selectedStockItems.find(x => String(x.id) === String(item.id)) || existingIds.includes(String(
                    item.id))) {
                chk.checked = true;
            }

            chk.addEventListener('change', function() {
                toggleStockSelection(item, this.checked);
            });

            clone.querySelector('.col-stock-nama').textContent = item.nama_produk;
            clone.querySelector('.col-stock-sku').textContent = item.sku_kode || '-';
            clone.querySelector('.col-stock-kategori').textContent = item.category?.nama_kategori || '-';
            clone.querySelector('.col-stock-qty').textContent = qty;
            clone.querySelector('.col-stock-unit').textContent = item.unit?.nama_unit || 'Pcs';

            tbody.appendChild(clone);
        });
    }

    function toggleStockSelection(item, isChecked) {
        if (isChecked) {
            if (!selectedStockItems.find(x => String(x.id) === String(item.id))) {
                selectedStockItems.push(item);
            }
        } else {
            selectedStockItems = selectedStockItems.filter(x => String(x.id) !== String(item.id));
        }
        renderSelectedStockBadges();
    }

    function renderSelectedStockBadges() {
        const container = document.getElementById('selectedStockContainer');
        const list = document.getElementById('selectedStockList');

        if (selectedStockItems.length === 0) {
            container.classList.add('d-none');
            return;
        }

        container.classList.remove('d-none');
        list.innerHTML = '';

        selectedStockItems.forEach(item => {
            const badge = document.createElement('div');
            badge.className =
                'badge bg-warning text-dark d-flex align-items-center gap-2 py-2 px-3 rounded-pill shadow-sm';
            badge.style.fontSize = '12px';
            badge.innerHTML = `
                <span>${item.nama_produk}</span>
                <i class="fa fa-times-circle cursor-pointer" onclick="deselectStockItem('${item.id}')" style="font-size: 14px; opacity: 0.8;"></i>
            `;
            list.appendChild(badge);
        });
    }

    window.deselectStockItem = function(id) {
        selectedStockItems = selectedStockItems.filter(x => String(x.id) !== String(id));
        renderSelectedStockBadges();
        const checkboxes = document.querySelectorAll('.stock-check');
        checkboxes.forEach(chk => {
            if (String(chk.value) === String(id)) chk.checked = false;
        });
    }

    function setupStockSearch() {
        const searchInput = document.getElementById('stockSearchInput');
        const categoryFilter = document.getElementById('stockCategoryFilter');
        let timer = null;
        if (searchInput) {
            searchInput.oninput = () => {
                clearTimeout(timer);
                timer = setTimeout(() => fetchStockPage(1), 500);
            };
        }
        if (categoryFilter) categoryFilter.onchange = () => fetchStockPage(1);
    }

    async function loadStockCategories() {
        try {
            const res = await fetch('/api/product-categories-api');
            const result = await res.json();
            if (result.success) {
                const cats = result.data.data || result.data;
                const filter = document.getElementById('stockCategoryFilter');
                if (filter) {
                    filter.innerHTML = '<option value="">Semua Kategori</option>';
                    cats.forEach(c => filter.innerHTML += `<option value="${c.id}">${c.nama_kategori}</option>`);
                }
            }
        } catch (e) {
            console.error(e);
        }
    }

    function resetStockFilters() {
        document.getElementById('stockSearchInput').value = '';
        document.getElementById('stockCategoryFilter').value = '';
        fetchStockPage(1);
    }

    function toggleAllStock(master) {
        const checkboxes = document.querySelectorAll('.stock-check:not(:disabled)');
        checkboxes.forEach(chk => {
            chk.checked = master.checked;
            chk.dispatchEvent(new Event('change'));
        });
    }

    function addSelectedStocks() {
        const tbody = document.getElementById('sjItemBody');
        const existingIds = Array.from(tbody.querySelectorAll('.sj-prod-id')).map(i => i.value);

        // Remove empty row if any
        if (tbody.children.length === 1 && !tbody.querySelector('.sj-prod-id').value) {
            tbody.innerHTML = '';
        }

        selectedStockItems.forEach(item => {
            if (!existingIds.includes(String(item.id))) {
                addSjRow({
                    produk_id: item.id,
                    nama_produk: item.nama_produk,
                    qty: 1,
                    satuan: item.unit?.nama_unit || 'Pcs',
                    product: item
                });
            }
        });

        bootstrap.Modal.getInstance(document.getElementById('stockModal')).hide();
    }

    function renderPaginationControls(data, containerId, fetchFn, arg) {
        const container = document.getElementById(containerId);
        if (!container || data.last_page <= 1) {
            if (container) container.innerHTML = '';
            return;
        }
        const argStr = (arg !== undefined && arg !== null) ? `${arg}, ` : '';
        let html = '<ul class="pagination pagination-sm mb-0">';
        html +=
            `<li class="page-item ${data.current_page === 1 ? 'disabled' : ''}"><a class="page-link" href="javascript:void(0)" onclick="${fetchFn.name}(${argStr}${data.current_page - 1})">Prev</a></li>`;
        for (let i = 1; i <= data.last_page; i++) {
            if (i === 1 || i === data.last_page || (i >= data.current_page - 2 && i <= data.current_page + 2)) {
                html +=
                    `<li class="page-item ${data.current_page === i ? 'active' : ''}"><a class="page-link" href="javascript:void(0)" onclick="${fetchFn.name}(${argStr}${i})">${i}</a></li>`;
            } else if (i === data.current_page - 3 || i === data.current_page + 3) {
                html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }
        html +=
            `<li class="page-item ${data.current_page === data.last_page ? 'disabled' : ''}"><a class="page-link" href="javascript:void(0)" onclick="${fetchFn.name}(${argStr}${data.current_page + 1})">Next</a></li>`;
        html += '</ul>';
        container.innerHTML = html;
    }
</script>
