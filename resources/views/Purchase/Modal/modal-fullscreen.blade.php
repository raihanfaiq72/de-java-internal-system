<div class="modal fade" id="invoiceModal" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content border-0" style="background-color: #f8fafc;">

            {{-- Header --}}
            <div class="modal-header bg-white border-bottom px-4 py-3 sticky-top shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-2 me-3">
                        <i class="fa fa-shopping-cart fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="modalTitle">Invoice Pembelian</h5>
                        <p class="text-muted small mb-0">Input data tagihan dari supplier/vendor.</p>
                    </div>
                </div>
                <div class="ms-auto d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-light border text-secondary fw-semibold px-4"
                        data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary px-4 fw-bold shadow-sm" onclick="saveFullInvoice()">
                        <i class="fa fa-save me-2"></i>Simpan
                    </button>
                </div>
            </div>

            {{-- Body --}}
            <div class="modal-body p-0">
                <form id="invoiceForm" class="h-100">
                    <input type="hidden" id="form_mode" value="create">
                    <input type="hidden" id="edit_invoice_id" value="">

                    <div class="row g-0 h-100">
                        {{-- Main Form --}}
                        <div class="col-lg-9 h-100 overflow-auto custom-scrollbar p-4">

                            {{-- Customer & Doc Details --}}
                            <div class="card border-0 shadow-sm rounded-3 mb-4">
                                <div class="card-body p-4">
                                    <div class="row g-4">
                                        {{-- Supplier --}}
                                        <div class="col-lg-6 border-end">
                                            <label class="f-label mb-2">Supplier / Vendor <span
                                                    class="text-danger">*</span></label>
                                            <select id="modal_mitra_id" class="form-select f-input-lg mb-3"
                                                onchange="renderMitraDetail()">
                                                <option value="">Cari Supplier...</option>
                                            </select>
                                            <div id="mitra_detail_display" class="d-none bg-light p-3 rounded border">
                                                <div class="d-flex align-items-center mb-2">
                                                    <div class="fw-bold text-dark" id="disp_mitra_nama">-</div>
                                                    <span class="badge bg-primary ms-auto" id="disp_mitra_tipe">-</span>
                                                </div>
                                                <div class="small text-muted mb-1"><i
                                                        class="fa fa-map-marker-alt me-2 text-secondary"></i><span
                                                        id="disp_mitra_alamat">-</span></div>
                                                <div class="small text-muted"><i
                                                        class="fa fa-phone me-2 text-secondary"></i><span
                                                        id="disp_mitra_telp">-</span></div>
                                            </div>
                                            <div id="mitra_empty_state"
                                                class="text-center py-4 text-muted border border-dashed rounded bg-light">
                                                <i class="fa fa-user-plus mb-2 fs-4 text-secondary"></i>
                                                <div class="small">Pilih supplier untuk melihat detail</div>
                                            </div>
                                        </div>

                                        {{-- Document --}}
                                        <div class="col-lg-6">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="f-label">Nomor Invoice</label>
                                                    <input type="text" id="modal_nomor_invoice"
                                                        class="form-control f-input fw-bold bg-light text-primary"
                                                        readonly placeholder="Auto Generated">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="f-label">Referensi / PO</label>
                                                    <input type="text" id="modal_ref_no" class="form-control f-input"
                                                        placeholder="PO-SUP-001">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="f-label">Tanggal Invoice</label>
                                                    <input type="text" id="modal_tgl_invoice"
                                                        class="form-control f-input">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="f-label">Tanggal Jatuh Tempo</label>
                                                    <input type="text" id="modal_tgl_jatuh_tempo"
                                                        class="form-control f-input">
                                                </div>
                                                <div class="col-12">
                                                    <label class="f-label">Purchasing Staff</label>
                                                    <select id="modal_sales_id" class="form-select f-input">
                                                        <option value="">Pilih Staff...</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Items --}}
                            <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
                                <div
                                    class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold text-dark text-uppercase small">
                                        <i class="fa fa-box me-2 text-primary"></i> Rincian Barang & Jasa
                                    </h6>

                                    <div class="d-flex gap-2">
                                        <button type="button"
                                            class="btn btn-outline-secondary btn-sm fw-bold px-3 rounded-pill"
                                            onclick="openStockModal()">
                                            <i class="fa fa-search me-1"></i> Cari Stok
                                        </button>
                                        <button type="button" class="btn btn-primary btn-sm fw-bold px-3 rounded-pill"
                                            onclick="addNewProductRow()">
                                            <i class="fa fa-plus me-1"></i> Tambah Baris
                                        </button>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table align-middle mb-0 table-hover" id="mainItemTable">
                                        <thead class="bg-light text-secondary small fw-bold text-uppercase">
                                            <tr>
                                                <th class="ps-4 py-3" width="35%">Produk / Deskripsi</th>
                                                <th class="text-center" width="10%">Qty</th>
                                                <th class="text-center" width="10%">Satuan</th>
                                                <th class="text-end" width="15%">Harga Beli</th>
                                                <th class="text-end" width="10%">Disc (Rp)</th>
                                                <th class="text-end pe-4" width="15%">Total</th>
                                                <th width="5%"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemBodyList" class="border-top-0"></tbody>
                                        <tfoot class="bg-light">
                                            <tr>
                                                <td colspan="7"
                                                    class="text-center py-2 text-muted small fst-italic">
                                                    Klik tombol tambah untuk memasukkan item
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            {{-- Bottom Notes --}}
                            <div class="card border-0 shadow-sm rounded-3">
                                <div class="card-body p-4">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="f-label mb-2">Catatan Internal</label>
                                            <textarea id="modal_keterangan" class="form-control f-text bg-light border-0" rows="3"
                                                placeholder="Catatan untuk tim internal..."></textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="f-label mb-2">Syarat & Ketentuan</label>
                                            <textarea id="modal_syarat" class="form-control f-text bg-light border-0" rows="3">1. Pembayaran ditujukan ke rekening perusahaan.&#10;2. Barang yang dibeli tidak dapat dikembalikan.</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Summary --}}

                        <div class="col-lg-3 bg-white border-start h-100 d-flex flex-column shadow-sm z-1">
                            <div class="p-4 border-bottom bg-light">
                                <h6 class="fw-bold text-dark mb-0">Ringkasan Pembayaran</h6>
                            </div>
                            <div class="p-4 flex-grow-1 overflow-auto">
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Subtotal</span>
                                        <span class="fw-bold text-dark" id="summary_subtotal">Rp 0</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2 align-items-center">
                                        <span class="text-muted">Potongan Lumpsum</span>
                                        <div style="width: 120px;">
                                            <input type="number" id="modal_diskon_tambahan"
                                                class="form-control form-control-sm text-end fw-bold" value="0"
                                                oninput="if(this.value<0)this.value=0; calculateInvoiceTotal()">
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="p-3 rounded bg-primary bg-opacity-10 border border-primary border-opacity-25">
                                    <div class="text-center">
                                        <div class="small opacity-75 fw-bold text-uppercase mb-1">Total Tagihan</div>
                                        <h3 class="fw-bold text-primary mb-0" id="summary_grand_total">Rp 0</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 border-top bg-light">
                                <button type="button" class="btn btn-primary w-100 fw-bold py-2 shadow-sm"
                                    onclick="saveFullInvoice()">
                                    <i class="fa fa-save me-2"></i> SIMPAN INVOICE
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="stockModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-white border-bottom py-3">
                <h5 class="modal-title fw-bold">Pilih Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="p-3 bg-light border-bottom">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="text" class="form-control col-filter" data-col="1"
                                placeholder="Cari Kode Produk...">
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control col-filter" data-col="2"
                                placeholder="Cari Nama Produk...">
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control col-filter" data-col="3"
                                placeholder="Cari Kategori...">
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="stockTable">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th width="5%" class="text-center py-3"><input type="checkbox"
                                        class="form-check-input" id="checkAllStock" onclick="toggleAllStock(this)">
                                </th>
                                <th class="py-3">Produk</th>
                                <th class="py-3">Kategori</th>
                                <th class="text-center py-3">Stok</th>
                                <th class="text-end py-3">Harga Jual</th>
                                <th class="text-center py-3">Track</th>
                            </tr>
                        </thead>
                        <tbody id="stockBodyList"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light border-top py-3">
                <div class="d-flex justify-content-between w-100 align-items-center">
                    <div class="small text-muted">
                        <i class="fa fa-info-circle me-1"></i> Pilih produk yang akan ditambahkan ke invoice.
                    </div>
                    <button type="button" class="btn btn-primary fw-bold px-4 rounded-pill"
                        onclick="addSelectedStocks()">
                        <i class="fa fa-check me-2"></i> Tambahkan Terpilih
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<template id="productRowTemplate">
    <tr class="border-bottom border-light product-row">
        <td class="ps-4 py-3">
            <input type="hidden" class="prod-id">
            <div class="mb-1">
                <select class="prod-select-item"></select>
            </div>
            <input type="text" class="form-control form-control-sm text-muted small prod-desc"
                placeholder="Deskripsi (Opsional)">
        </td>
        <td class="py-3">
            <input type="number" class="form-control form-control-sm text-center prod-qty" value="1"
                min="1" oninput="calculateInvoiceTotal()">
        </td>
        <td class="py-3 text-center">
            <span class="badge bg-light text-secondary border prod-unit-label">-</span>
        </td>
        <td class="py-3">
            <input type="number" class="form-control form-control-sm text-end prod-price" value="0"
                oninput="calculateInvoiceTotal()">
        </td>
        <td class="py-3">
            <input type="number" class="form-control form-control-sm text-end prod-disc" value="0"
                oninput="calculateInvoiceTotal()">
        </td>
        <td class="py-3 text-end pe-4 fw-bold text-dark prod-subtotal">Rp 0</td>
        <td class="py-3 text-center">
            <button type="button" class="btn btn-link text-danger p-0 btn-remove-row">
                <i class="fa fa-times"></i>
            </button>
        </td>
    </tr>
</template>

<template id="stockRowTemplate">
    <tr class="stock-item-row">
        <td class="text-center">
            <input type="checkbox" class="form-check-input stock-check">
        </td>
        <td>
            <div class="fw-bold text-dark col-stock-nama"></div>
            <div class="small text-muted col-stock-sku"></div>
        </td>
        <td><span class="badge bg-light text-dark border col-stock-kategori"></span></td>
        <td class="text-center fw-bold col-stock-qty"></td>
        <td class="text-end font-monospace col-stock-harga"></td>
        <td class="text-center"><span class="badge col-stock-track"></span></td>
    </tr>
</template>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<style>
    .f-input {
        padding: 0.75rem 1rem !important;
        font-size: 14px !important;
    }

    .flatpickr-wrapper {
        width: 100% !important;
        display: block !important;
    }

    .flatpickr-input.form-control[readonly] {
        background-color: #fff !important;
    }

    .flatpickr-input,
    .flatpickr-input+input {
        width: 100% !important;
        display: block !important;
    }

    .flatpickr-calendar {
        z-index: 10000 !important;
    }

    .flatpickr-calendar.fp-has-sidebar {
        width: 440px !important;
    }

    .flatpickr-calendar .fp-sidebar-abs {
        position: absolute;
        left: 0;
        width: 130px;
        top: 0;
        bottom: 0;
        border-right: 1px solid #e5e7eb;
        background: #f8fafc;
        padding: 12px 8px;
        z-index: 5;
    }

    .flatpickr-calendar.fp-has-sidebar .flatpickr-innerContainer {
        margin-left: 130px;
        width: 310px;
    }

    .fp-title {
        font-size: 10px;
        font-weight: 800;
        color: #94a3b8;
        text-transform: uppercase;
        margin-bottom: 10px;
        letter-spacing: 1px;
    }

    .fp-sidebar-abs .btn {
        width: 100%;
        font-size: 11px;
        padding: 6px;
        margin-bottom: 5px;
        text-align: left;
        font-weight: 600;
    }

    /* Pastikan bulan/tahun tetap di atas sidebar */
    .flatpickr-months {
        z-index: 10 !important;
        pointer-events: auto !important;
    }

    .flatpickr-current-month select {
        appearance: auto !important;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

<script>
    let fpInvoice = null,
        fpDue = null;

    let masterProduk = [];

    async function fetchMasterProduk() {
        try {
            const res = await fetch('{{ route('product-api.index') }}');
            const result = await res.json();
            if (result.success) {
                masterProduk = result.data.data || result.data;
            }
        } catch (error) {
            console.error('Gagal load master:', e);
        }
    }

    function toggleAllStock(el) {
        const checkboxes = document.querySelectorAll('.stock-check:not(:disabled)');
        checkboxes.forEach(chk => {
            chk.checked = el.checked;
        });
    }

    function applyDueShortcut(days) {
        const base = document.getElementById('modal_tgl_invoice').value;
        if (!base) return;
        const date = new Date(base);
        date.setDate(date.getDate() + parseInt(days));
        if (fpDue) fpDue.setDate(date, true);
    }

    function initInvoiceDatePickers() {
        if (fpInvoice) fpInvoice.destroy();
        if (fpDue) fpDue.destroy();

        const common = {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd/m/Y',
            allowInput: true,
            disableMobile: true,
            monthSelectorType: 'dropdown',
            static: true,
            altInputClass: 'form-control f-input bg-white'
        };

        fpInvoice = flatpickr('#modal_tgl_invoice', {
            ...common,
            onChange: () => {
                if (fpDue && !document.getElementById('modal_tgl_jatuh_tempo').value) applyDueShortcut(30);
            }
        });

        fpDue = flatpickr('#modal_tgl_jatuh_tempo', {
            ...common,
            onReady: (sd, ds, inst) => injectDueSidebar(inst)
        });
    }

    function injectDueSidebar(instance) {
        const cal = instance.calendarContainer;
        if (!cal || cal.querySelector('.fp-sidebar-abs')) return;

        const sidebar = document.createElement('div');
        sidebar.className = 'fp-sidebar-abs';
        sidebar.innerHTML = '<div class="fp-title">Tempo</div>';

        const options = [{
                l: 'Hari Ini',
                d: 0
            },
            {
                l: '+7 Hari',
                d: 7
            },
            {
                l: '+14 Hari',
                d: 14
            },
            {
                l: '+30 Hari',
                d: 30
            },
            {
                l: '+45 Hari',
                d: 45
            },
            {
                l: '+60 Hari',
                d: 60
            }
        ];

        options.forEach(opt => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-sm btn-outline-primary shadow-none';
            btn.textContent = opt.l;
            btn.onclick = (e) => {
                e.preventDefault();
                applyDueShortcut(opt.d);
                instance.close();
            };
            sidebar.appendChild(btn);
        });

        cal.appendChild(sidebar);
        cal.classList.add('fp-has-sidebar');
    }

    const originalOpenInvoiceModal = openInvoiceModal;
    openInvoiceModal = async function(id = null, type = null, mode = 'create') {
        await originalOpenInvoiceModal(id, type, mode);
        initInvoiceDatePickers();
    };

    let tomSelectMitraModal = null;

    function initTomSelectMitraModal(selectedId = null) {
        if (!window.TomSelect) {
            const sel = document.getElementById('modal_mitra_id');
            sel.innerHTML = '<option value="">Cari Pelanggan...</option>';
            masterMitra.forEach(m => sel.insertAdjacentHTML('beforeend', `<option value="${m.id}">${m.nama}</option>`));
            if (selectedId) sel.value = selectedId;
            return;
        }

        if (tomSelectMitraModal) {
            try {
                tomSelectMitraModal.destroy();
            } catch (_) {}
            tomSelectMitraModal = null;
        }

        const sel = document.getElementById('modal_mitra_id');
        sel.innerHTML = '';

        const options = (masterMitra || []).map(m => ({
            id: m.id,
            nama: m.nama,
            info: [m.no_hp, m.alamat].filter(Boolean).join(' • ') || ''
        }));

        tomSelectMitraModal = new TomSelect('#modal_mitra_id', {
            options,
            valueField: 'id',
            labelField: 'nama',
            searchField: ['nama', 'info'],
            allowEmptyOption: true,
            placeholder: 'Cari Pelanggan...',
            maxOptions: 1000,
            create: false,
            dropdownParent: 'body',
            render: {
                option: (data, escape) => `
                <div>
                    <div class="fw-bold">${escape(data.nama || '')}</div>
                    ${data.info ? `<div class="small text-muted">${escape(data.info)}</div>` : ''}
                </div>
            `,
                item: (data, escape) => `<div>${escape(data.nama || '')}</div>`
            },
            onChange: function(val) {
                renderMitraDetail();
            }
        });

        if (selectedId) {
            tomSelectMitraModal.setValue(String(selectedId), true);
        }
    }

    document.getElementById('invoiceModal').addEventListener('hidden.bs.modal', () => {
        if (tomSelectMitraModal) {
            try {
                tomSelectMitraModal.destroy();
            } catch (_) {}
            tomSelectMitraModal = null;
        }
    });

    function addNewProductRow(data = null) {
        const tbody = document.getElementById('itemBodyList');
        const template = document.getElementById('productRowTemplate');
        const clone = template.content.cloneNode(true);
        const tr = clone.querySelector('tr');

        const rowId = 'row_' + Date.now() + Math.random().toString(36).substr(2, 5);
        tr.id = rowId;

        const selectEl = tr.querySelector('.prod-select-item');
        const priceInput = tr.querySelector('.prod-price');
        const qtyInput = tr.querySelector('.prod-qty');
        const discInput = tr.querySelector('.prod-disc');
        const idInput = tr.querySelector('.prod-id');
        const unitLabel = tr.querySelector('.prod-unit-label');
        const descInput = tr.querySelector('.prod-desc');

        const ts = new TomSelect(selectEl, {
            options: masterProduk.map(p => ({
                id: p.id,
                nama: p.nama_produk,
                sku: p.sku_kode,
                harga: p.harga_jual,
                unit: p.unit?.nama_unit || 'Pcs'
            })),
            valueField: 'id',
            labelField: 'nama',
            searchField: ['nama', 'sku'],
            placeholder: 'Cari barang...',
            render: {
                option: (d, esc) =>
                    `<div><div class="fw-bold">${esc(d.nama)}</div><small class="text-muted">${esc(d.sku)}</small></div>`,
                item: (d, esc) => `<div>${esc(d.nama)}</div>`
            },
            onChange: function(val) {
                const selected = this.options[val];
                if (selected) {
                    idInput.value = selected.id;
                    priceInput.value = selected.harga;
                    unitLabel.innerText = selected.unit;
                    calculateInvoiceTotal();
                }
            }
        });

        if (data) {
            ts.setValue(data.product_id || data.id, true); // true agar tidak trigger onChange ganda
            idInput.value = data.product_id || data.id;
            qtyInput.value = data.qty || 1;
            priceInput.value = data.harga_satuan || data.harga_jual || 0;
            discInput.value = data.diskon_item || data.diskon_nilai || 0;
            descInput.value = data.deskripsi_item || data.deskripsi_produk || '';
            unitLabel.innerText = data.product?.unit?.nama_unit || data.unit || 'Pcs';
        }

        tr.querySelector('.btn-remove-row').onclick = () => {
            ts.destroy();
            tr.remove();
            calculateInvoiceTotal();
        };

        tbody.appendChild(tr);
        calculateInvoiceTotal();
    }

    function removeProductRow(id) {
        const row = document.getElementById(`row_${id}`);
        if (row) row.remove();
        calculateInvoiceTotal();
    }

    const _origOpenModal = openInvoiceModal;
    openInvoiceModal = async function(id = null, type = null, mode = 'create') {
        await _origOpenModal(id, type, mode);
        initInvoiceDatePickers();
    };

    // Helper IDR & Number
    window.financeHelpers = window.financeHelpers || {
        formatIDR: (val) => new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(val),
        unformatIDR: (str) => {
            if (!str) return 0;
            return parseFloat(str.replace(/[^0-9,-]+/g, "").replace(",", "."));
        }
    };

    let productCollection = [],
        mitraCollection = [];

    // Initialize Master Data
    async function initializeInvoiceData() {
        try {
            const [mRes, pRes] = await Promise.all([
                fetch('/api/mitra-api').then(r => r.json()),
                fetch('/api/product-api').then(r => r.json())
            ]);
            mitraCollection = mRes.data.data || mRes.data;
            productCollection = pRes.data.data || pRes.data;

            const select = document.getElementById('modal_mitra_id');
            select.innerHTML = '<option value="">Cari dan pilih supplier...</option>';
            mitraCollection.forEach(m => select.insertAdjacentHTML('beforeend',
                `<option value="${m.id}">${m.nama}</option>`));
        } catch (e) {
            console.error("Master data fail", e);
        }
    }

    async function openInvoiceModal(id = null, type = null, mode = 'create') {
        const modal = new bootstrap.Modal(document.getElementById('invoiceModal'));
        document.getElementById('invoiceForm').reset();
        document.getElementById('itemBodyList').innerHTML = '';
        document.getElementById('mitra_detail_display').classList.add('d-none');
        document.getElementById('mitra_empty_state').classList.remove('d-none');

        // Reset totals
        document.getElementById('summary_subtotal').innerText = 'Rp 0';
        document.getElementById('summary_grand_total').innerText = 'Rp 0';

        // Load Master Data if empty
        if (masterProduk.length === 0) await fetchMasterProduk();
        if (masterMitra.length === 0) await initializeMasterData();

        initTomSelectMitraModal();

        if (mode === 'edit' && id) {
            document.getElementById('modalTitle').innerText = 'Edit Invoice Penjualan';
            document.getElementById('form_mode').value = 'edit';
            document.getElementById('edit_invoice_id').value = id;

            try {
                const res = await fetch(`${window.financeApp.API_URL}/${id}`);
                const result = await res.json();
                if (result.success) {
                    const inv = result.data;
                    document.getElementById('modal_nomor_invoice').value = inv.nomor_invoice;
                    document.getElementById('modal_ref_no').value = inv.ref_no || '';
                    document.getElementById('modal_tgl_invoice').value = inv.tgl_invoice;
                    document.getElementById('modal_tgl_jatuh_tempo').value = inv.tgl_jatuh_tempo;
                    document.getElementById('modal_mitra_id').value = inv.mitra_id;
                    document.getElementById('modal_keterangan').value = inv.keterangan || '';
                    document.getElementById('modal_syarat').value = inv.syarat_ketentuan || '';
                    document.getElementById('modal_diskon_tambahan').value = inv.diskon_tambahan_nilai || 0;


                    if (inv.mitra_id && tomSelectMitraModal) {
                        tomSelectMitraModal.setValue(String(inv.mitra_id), true);
                    } else {
                        initTomSelectMitraModal(inv.mitra_id || null);
                    }

                    renderMitraDetail();

                    // Items
                    if (inv.items) {
                        inv.items.forEach(item => addNewProductRow(item));
                    }
                }
            } catch (e) {
                console.error(e);
                alert('Gagal load data invoice');
            }

        } else {
            document.getElementById('modalTitle').innerText = 'Invoice Penjualan Baru';
            document.getElementById('form_mode').value = 'create';
            document.getElementById('modal_tgl_invoice').value = new Date().toISOString().split('T')[0];
            // Set default due date (e.g. +30 days)
            const d = new Date();
            d.setDate(d.getDate() + 30);
            document.getElementById('modal_tgl_jatuh_tempo').value = d.toISOString().split('T')[0];
            document.getElementById('modal_nomor_invoice').value =
                `INV/${new Date().getFullYear()}/${Date.now().toString().slice(-4)}`;


            if (tomSelectMitraModal) {
                tomSelectMitraModal.clear(true);
            }


            addNewProductRow(); // Add 1 empty row
        }

        modal.show();
    }

    function removeRow(id) {
        const row = document.getElementById(`row_${id}`);
        if (row) row.remove();
        calculateInvoiceTotal();
    }

    function autoFillPrice(id) {
        // Warning: Product API returns 'harga_jual'. For Purchase, we might want 'harga_beli' ideally.
        // But assuming Product API only gives 'harga_jual' for now, or user overrides it manually.
        const row = document.getElementById(`row_${id}`);
        const sel = row.querySelector('.prod-select');
        const price = sel.options[sel.selectedIndex].dataset.price || 0;
        row.querySelector('.prod-price').value = price;
        calculateInvoiceTotal();
    }

    function calculateInvoiceTotal() {
        let subtotal = 0;

        document.querySelectorAll('#itemBodyList tr').forEach(row => {
            const qtyEl = row.querySelector('.prod-qty');
            const priceEl = row.querySelector('.prod-price');
            const discEl = row.querySelector('.prod-disc');
            const subtotalDisplay = row.querySelector('.prod-subtotal');

            if (!qtyEl || !priceEl || !discEl) return;

            const qty = parseFloat(qtyEl.value) || 0;
            const price = parseFloat(priceEl.value) || 0;
            const disc = parseFloat(discEl.value) || 0;

            let lineTotal = (qty * price) - disc;
            if (lineTotal < 0) lineTotal = 0;

            if (subtotalDisplay) {
                subtotalDisplay.innerText = window.financeApp.formatIDR(lineTotal);
            }
            subtotal += lineTotal;
        });

        const extraDisc = parseFloat(document.getElementById('modal_diskon_tambahan').value) || 0;
        const grandTotal = Math.max(0, subtotal - extraDisc);

        document.getElementById('summary_subtotal').innerText = window.financeApp.formatIDR(subtotal);
        document.getElementById('summary_grand_total').innerText = window.financeApp.formatIDR(grandTotal);
    }

    function renderMitraDetail() {
        const id = document.getElementById('modal_mitra_id').value;
        const disp = document.getElementById('mitra_detail_display');
        const empty = document.getElementById('mitra_empty_state');

        if (!id) {
            disp.classList.add('d-none');
            empty.classList.remove('d-none');
            return;
        }

        const m = masterMitra.find(x => x.id == id);
        if (m) {
            document.getElementById('disp_mitra_nama').innerText = m.nama;
            document.getElementById('disp_mitra_tipe').innerText = m.tipe_mitra;
            document.getElementById('disp_mitra_alamat').innerText = m.alamat || '-';
            document.getElementById('disp_mitra_telp').innerText = m.no_hp || '-';

            disp.classList.remove('d-none');
            empty.classList.add('d-none');
        } else {
            disp.classList.add('d-none');
            empty.classList.remove('d-none');
        }

    }

    function resetModalForm() {
        document.getElementById('invoiceForm').reset();
        document.getElementById('itemBodyList').innerHTML = '';
        document.getElementById('mitra_detail_display').innerHTML =
            `<i class="fa fa-info-circle me-2 text-primary"></i>Detail muncul otomatis.`;
        document.getElementById('summary_subtotal').innerText = 'Rp 0';
        document.getElementById('summary_grand_total').innerText = 'Rp 0';
    }

    async function saveFullInvoice() {
        const mode = document.getElementById('form_mode').value;
        const id = document.getElementById('edit_invoice_id').value;
        const url = mode === 'edit' ? `/api/invoice-api/${id}` : '/api/invoice-create-api';
        const method = mode === 'edit' ? 'PUT' : 'POST';

        const mitraIdVal = tomSelectMitraModal ? tomSelectMitraModal.getValue() : document.getElementById(
            'modal_mitra_id').value;

        // Build Payload
        const items = [];
        document.querySelectorAll('#itemBodyList tr').forEach(row => {
            const qty = parseFloat(row.querySelector('.prod-qty').value) || 0;
            const price = parseFloat(row.querySelector('.prod-price').value) || 0;
            const disc = parseFloat(row.querySelector('.prod-disc').value) || 0;
            const subtotal = Math.max(0, (qty * price) - disc);

            const productId = row.querySelector('.prod-id').value;

            const productData = masterProduk.find(p => String(p.id) === String(productId));
            const namaProduk = productData ? productData.nama_produk : '';

            items.push({
                produk_id: row.querySelector('.prod-id').value || null,
                nama_produk_manual: namaProduk,
                deskripsi_produk: row.querySelector('.prod-desc').value,
                qty: qty,
                harga_satuan: price,
                diskon_nilai: disc,
                total_harga_item: subtotal
            });
        });

        const payload = {
            invoice: {
                tipe_invoice: 'Purchase',
                nomor_invoice: document.getElementById('modal_nomor_invoice').value,
                tgl_invoice: document.getElementById('modal_tgl_invoice').value,
                tgl_jatuh_tempo: document.getElementById('modal_tgl_jatuh_tempo').value,
                mitra_id: mitraIdVal,
                ref_no: document.getElementById('modal_ref_no').value,
                keterangan: document.getElementById('modal_keterangan').value,
                syarat_ketentuan: document.getElementById('modal_syarat').value,
                diskon_tambahan_nilai: parseFloat(document.getElementById('modal_diskon_tambahan').value) || 0,
                total_akhir: parseFloat(document.getElementById('summary_grand_total').innerText.replace(
                    /[^0-9,-]+/g, '').replace(',', '.')) || 0,
                status_dok: 'Approved',
                status_pembayaran: 'Unpaid',
                biaya_kirim: 0,
                uang_muka: 0
            },
            items: items
        };

        // Basic Validation
        if (!payload.invoice.mitra_id) {
            alert('Harap pilih Pelanggan/Mitra!');
            return;
        }
        if (!payload.invoice.tgl_invoice) {
            alert('Harap isi Tanggal Invoice!');
            return;
        }
        if (!payload.invoice.nomor_invoice) {
            alert('Nomor invoice belum diisi.');
            return;
        }
        if (items.length === 0) {
            alert('Harap tambahkan minimal satu item.');
            return;
        }

        try {
            const res = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            });

            const result = await res.json();
            if (result.success) {
                alert('Invoice berhasil disimpan!');
                bootstrap.Modal.getInstance(document.getElementById('invoiceModal')).hide();
                // Redirect to detail page
                window.location.href = `{{ url('purchase') }}/${result.data.invoice.id}`;
            } else {
                if (result.errors) {
                    let errorMsg = "Terjadi kesalahan:\n";
                    Object.values(result.errors).forEach(err => errorMsg += `- ${err}\n`);
                    alert(errorMsg);
                } else {
                    alert('Gagal: ' + result.message);
                }
            }
        } catch (e) {
            console.error(e);
            alert('Terjadi kesalahan saat menyimpan.');
        }
    }

    let stockCache = [];

    async function openStockModal() {
        const modal = new bootstrap.Modal(document.getElementById('stockModal'));
        document.getElementById('stockBodyList').innerHTML =
            '<tr><td colspan="6" class="text-center p-4"><div class="spinner-border text-primary"></div></td></tr>';
        modal.show();

        try {
            // Adjust API endpoint as needed
            const res = await fetch('/api/stock-api');
            const result = await res.json();
            if (result.success) {
                stockCache = result.data.data || result.data; // Handle pagination structure
                renderStockList(stockCache);
            }
        } catch (e) {
            document.getElementById('stockBodyList').innerHTML =
                '<tr><td colspan="6" class="text-center text-danger p-4">Gagal load stock</td></tr>';
        }
    }

    function renderStockList(data) {
        const tbody = document.getElementById('stockBodyList');
        const template = document.getElementById('stockRowTemplate');
        tbody.innerHTML = '';

        const existingIds = Array.from(document.querySelectorAll('#itemBodyList .prod-id'))
            .map(input => input.value);

        data.forEach(item => {
            const clone = template.content.cloneNode(true);
            const tr = clone.querySelector('tr');
            const chk = clone.querySelector('.stock-check');

            // Set Data Checkbox
            chk.value = item.id;
            chk.dataset.raw = JSON.stringify(item);

            if (existingIds.includes(String(item.id))) {
                chk.checked = true;
            }

            // Isi Konten
            clone.querySelector('.col-stock-nama').textContent = item.nama_produk;
            clone.querySelector('.col-stock-sku').textContent = item.sku_kode || item.kode_produk || '-';
            clone.querySelector('.col-stock-kategori').textContent = item.category?.nama_kategori || '-';
            clone.querySelector('.col-stock-qty').textContent =
                `${item.qty || 0} ${item.unit?.nama_unit || ''}`;
            clone.querySelector('.col-stock-harga').textContent = window.financeApp.formatIDR(item.harga_jual);

            const trackBadge = clone.querySelector('.col-stock-track');
            trackBadge.textContent = item.track_stock ? 'YES' : 'NO';
            trackBadge.className = `badge ${item.track_stock ? 'bg-success' : 'bg-secondary'}`;

            tbody.appendChild(clone);
        });

        const masterChk = document.getElementById('checkAllStock');
        if (masterChk) masterChk.checked = false;
    }

    // Filter Stock
    document.querySelectorAll('.col-filter').forEach(inp => {
        inp.addEventListener('keyup', function() {
            const val = this.value.toLowerCase();
            const col = this.dataset.col; // 1=code, 2=name, 3=cat

            const filtered = stockCache.filter(item => {
                if (col == 1) return (item.kode_produk || '').toLowerCase().includes(val);
                if (col == 2) return (item.nama_produk || '').toLowerCase().includes(val);
                if (col == 3) return (item.kategori?.nama_kategori || '').toLowerCase()
                    .includes(val);
                return true;
            });
            renderStockList(filtered);
        });
    });

    function addSelectedStocks() {
        const checked = document.querySelectorAll('.stock-check:checked');

        const existingIds = Array.from(document.querySelectorAll('#itemBodyList .prod-id'))
            .map(input => input.value);

        checked.forEach(chk => {
            const data = JSON.parse(chk.dataset.raw);
            const productId = String(data.id);

            if (!existingIds.includes(productId)) {
                addNewProductRow({
                    product_id: data.id,
                    id: data.id,
                    nama_produk_manual: data.nama_produk,
                    harga_jual: data.harga_jual,
                    qty: 1,
                    unit: data.unit?.nama_unit || 'Pcs',
                    product: data
                });
            }
        });

        bootstrap.Modal.getInstance(document.getElementById('stockModal')).hide();
    }
</script>
