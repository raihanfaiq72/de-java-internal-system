<div class="modal fade" id="invoiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content border-0" style="background-color: #f8fafc;">
            <div class="modal-header bg-white border-bottom px-4 py-3 sticky-top shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="bg-dark text-white p-2 rounded-2 me-3">
                        <i class="fa fa-file-invoice-dollar fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="modalTitle">Formulir Penagihan Penjualan</h5>
                        <p class="text-muted small mb-0">Input rincian transaksi komersial sesuai standar akuntansi.</p>
                    </div>
                </div>
                <div class="ms-auto d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-link text-secondary text-decoration-none fw-semibold"
                        data-bs-dismiss="modal">Batalkan</button>
                    <button type="button" class="btn btn-primary px-4 fw-bold shadow-sm" onclick="saveFullInvoice()">
                        <i class="fa fa-save me-2"></i>Simpan Dokumen
                    </button>
                </div>
            </div>

            <div class="modal-body p-4">
                <form id="invoiceForm">
                    <input type="hidden" id="form_mode" value="create">
                    <input type="hidden" id="edit_invoice_id" value="">

                    <div class="row g-4 mb-4">
                        <div class="col-lg-8">
                            <div class="card border-0 shadow-sm rounded-3">
                                <div class="card-body p-4">
                                    <div class="row g-4">
                                        <div class="col-md-7">
                                            <label class="f-label">Pelanggan / Mitra Bisnis *</label>
                                            <select id="modal_mitra_id" class="form-select f-input-lg"
                                                onchange="renderMitraDetail()">
                                                <option value="">Cari dan pilih mitra...</option>
                                            </select>
                                            <div id="mitra_detail_display"
                                                class="mt-3 p-3 rounded bg-light border-start border-3 border-primary small text-secondary">
                                                <i class="fa fa-info-circle me-2 text-primary"></i>Detail alamat dan
                                                kontak akan muncul otomatis.
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="f-label">Nomor Invoice</label>
                                                    <input type="text" id="modal_nomor_invoice"
                                                        class="form-control f-input fw-bold bg-light" readonly>
                                                </div>
                                                <div class="col-6">
                                                    <label class="f-label">Tanggal Terbit</label>
                                                    <input type="date" id="modal_tgl_invoice"
                                                        class="form-control f-input">
                                                </div>
                                                <div class="col-6">
                                                    <label class="f-label">Jatuh Tempo</label>
                                                    <input type="text" id="modal_tgl_jatuh_tempo"
                                                        class="form-control f-input" placeholder="Pilih tanggal ...">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                                <div class="card-body p-4">
                                    <div class="mb-3">
                                        <label class="f-label">Referensi / No. PO</label>
                                        <input type="text" id="modal_ref_no" class="form-control f-input"
                                            placeholder="Misal: PO/2026/001">
                                    </div>
                                    <div>
                                        <label class="f-label">Salesperson</label>
                                        <select id="modal_sales_id" class="form-select f-input">
                                            <option value="">Pilih Personel Sales...</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
                        <div
                            class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold text-dark text-uppercase small" style="letter-spacing: 1px;">Daftar
                                Barang & Jasa</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0" id="mainItemTable">
                                <thead class="bg-light">
                                    <tr class="text-secondary small fw-bold">
                                        <th class="ps-4 py-3" width="40%">ITEM & DESKRIPSI</th>
                                        <th class="text-center" width="10%">QTY</th>
                                        <th class="text-end" width="20%">HARGA SATUAN</th>
                                        <th class="text-end" width="15%">DISKON (RP)</th>
                                        <th class="text-end pe-4" width="20%">TOTAL</th>
                                        <th width="5%"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemBodyList">
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3 bg-white border-top text-center d-flex justify-content-evenly">
                            <button type="button" class="btn btn-outline-primary btn-sm fw-bold px-4 rounded-pill"
                                onclick="addNewProductRow()">
                                <i class="fa fa-plus-circle me-1"></i> Tambah Item Baru
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm fw-bold px-4 rounded-pill"
                                onclick="openStockModal()">
                                <i class="fa fa-plus-circle me-1"></i>Tambah dari Stok
                            </button>
                        </div>
                    </div>

                    <div class="row g-4 pb-5">
                        <div class="col-md-7">
                            <div class="card border-0 shadow-sm p-4 h-100">
                                <div class="mb-3">
                                    <label class="f-label text-primary">Keterangan Tambahan</label>
                                    <textarea id="modal_keterangan" class="form-control border-0 bg-light f-text" rows="2"
                                        placeholder="Tulis instruksi khusus atau catatan..."></textarea>
                                </div>
                                <div>
                                    <label class="f-label text-primary">Syarat & Ketentuan</label>
                                    <textarea id="modal_syarat" class="form-control border-0 bg-light f-text" rows="2">1. Pembayaran ditujukan ke rekening perusahaan.&#10;2. Penagihan ini sah setelah dicap resmi.</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="card border-0 shadow-sm bg-dark text-white p-4 h-100 rounded-3">
                                <div class="d-flex justify-content-between mb-3 opacity-75">
                                    <span class="small fw-semibold">Subtotal Bruto</span>
                                    <span id="summary_subtotal" class="f-mono">Rp 0</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 align-items-center">
                                    <span class="small fw-semibold opacity-75">Potongan (Lumpsum)</span>
                                    <div class="input-group input-group-sm" style="width: 160px;">
                                        <input type="number" id="modal_diskon_tambahan"
                                            class="form-control bg-transparent border-secondary text-white text-end fw-bold"
                                            value="0" oninput="calculateInvoiceTotal()">
                                    </div>
                                </div>
                                <div class="border-top border-secondary my-3 opacity-25"></div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold mb-0 text-uppercase small" style="letter-spacing: 1px;">Total
                                        Netto Tagihan</h6>
                                    <h3 id="summary_grand_total" class="fw-bold text-info mb-0 f-mono">Rp 0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="stockModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-light">
                <h6 class="modal-title fw-bold" style="color: #000;">Pilih Produk dari Stok</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 w-100" id="stockTable">
                        <thead class="bg-light">
                            <tr>
                                <th width="5%"><input type="checkbox" class="form-check-input"
                                        id="checkAllStock" onclick="toggleAllStock(this)"></th>
                                <th>Kode Produk</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th class="text-center">Stok</th>
                                <th class="text-end">Harga Jual</th>
                                <th>Track Stok</th>
                                <th>Keterangan</th>
                            </tr>
                            <tr class="filter-row bg-white">
                                <th></th>
                                <th><input type="text" class="form-control form-control-sm col-filter"
                                        data-col="1" placeholder="Cari Kode..."></th>
                                <th><input type="text" class="form-control form-control-sm col-filter"
                                        data-col="2" placeholder="Cari Nama..."></th>
                                <th><input type="text" class="form-control form-control-sm col-filter"
                                        data-col="3" placeholder="Cari Kategori..."></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="stockBodyList">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary fw-bold" onclick="addSelectedStockToInvoice()">
                    Tambahkan Produk Terpilih
                </button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<style>
    .f-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 6px;
        display: block;
    }

    .f-input {
        border: 1px solid #e2e8f0;
        padding: 0.6rem 0.8rem;
        border-radius: 6px;
        font-size: 13px;
        transition: all 0.2s;
    }

    .f-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    .f-input-lg {
        padding: 0.75rem 1rem;
        font-size: 14px;
        font-weight: 600;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }

    .f-text {
        font-size: 13px;
    }

    .f-mono {
        font-family: 'JetBrains Mono', monospace;
    }

    .table-responsive {
        overflow-y: visible !important;
        overflow-x: clip !important;
    }

    .ts-dropdown {
        z-index: 2000 !important;
        position: absolute !important;
        background: #ffffff !important;
        border: 1px solid #e2e8e0 !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
    }

    #itemBodyList tr:focus-within {
        z-index: 100;
        position: relative;
    }

    #mainItemTable .f-row-input {
        border: 1px solid transparent;
        background: transparent;
        padding: 6px;
        font-size: 13px;
        transition: all 0.2s;
    }

    #mainItemTable .table-responsive {
        overflow: visible !important;
    }

    #mainItemTable td:first-child {
        position: relative;
        overflow: visible !important;
    }

    #mainItemTable tr:hover .f-row-input {
        background: #fff;
        border-color: #e2e8f0;
    }

    #mainItemTable .f-row-input:focus {
        background: #fff;
        border-color: #3b82f6;
        outline: none;
    }

    #stockModal {
        z-index: 2100 !important;
    }

    #stockModal .table-responsive {
        max-height: 400px;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        border: 1px solid #e2e8f0;
        display: block;
    }

    #stockModal .modal-footer {
        z-index: 2101 !important;
        position: relative;
        background-color: #ffffff;
    }

    #stockModal .sticky-top {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #f8fafc;
    }

    #stockModal .modal-body {
        overflow: hidden !important;
    }

    .modal-backdrop.show:nth-of-type(2) {
        z-index: 2095 !important;
    }

    .flatpickr-calendar {
        width: auto !important;
    }

    .flatpickr-calendar .has-sidebar {
        display: flex !important;
        flex-direction: row;
        width: auto !important;
    }

    .flatpickr-innerContainer {
        display: flex;
        flex-direction: row;
    }

    .fp-sidebar {
        width: 100px;
        border-left: 1px solid #e2e8f0;
        padding: 10px;
        display: flex;
        flex-direction: column;
        gap: 5px;
        background: #f8fafc;
    }

    .fp-sidebar-btn {
        background: white;
        border: 1px solid #e2e8f0;
        padding: 4px 6px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: bold;
        color: #64748b;
        cursor: pointer;
        text-align: center;
        transition: all 0.2s;
    }

    .fp-sidebar-btn:hover {
        background: #3b82f6;
        color: white;
        border-color: #3b82f6;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    window.financeHelpers = window.financeHelpers || {
        formatIDR: (val) => new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(val),
        formatNumber: (val) => {
            if (!val) return "";
            let num = val.toString().replace(/[^0-9]/g, "");
            return num.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        },
        unformatIDR: (str) => {
            if (!str) return 0;
            return parseFloat(str.replace(/[^0-9,-]+/g, "").replace(",", "."));
        }
    };

    function formatInput(el) {
        let rawValue = el.value;
        el.value = window.financeHelpers.formatNumber(rawValue);
        calculateInvoiceTotal();
    }

    let productCollection = [],
        mitraCollection = [];

    let tomSelectMitra = null;
    let tomSelectSales = null;
    let fpJatuhTempo;
    let stockDataTable = null;

    async function initializeInvoiceData() {
        try {
            const [mRes, pRes] = await Promise.all([
                fetch('/api/mitra-api').then(r => r.json()),
                fetch('/api/product-api').then(r => r.json())
            ]);
            mitraCollection = mRes.data.data || mRes.data;
            productCollection = pRes.data.data || pRes.data;
            salesCollection = [];

            if (!tomSelectMitra) {
                tomSelectMitra = new TomSelect('#modal_mitra_id', {
                    create: false,
                    sortField: {
                        field: 'text',
                        direction: 'asc'
                    }
                });
            }

            if (!tomSelectSales) {
                tomSelectSales = new TomSelect('#modal_sales_id', {
                    create: false,
                    sortField: {
                        field: 'text',
                        direction: 'asc'
                    }
                });
            }

            tomSelectMitra.clearOptions();
            mitraCollection.forEach(m => {
                tomSelectMitra.addOption({
                    value: m.id,
                    text: m.nama
                });
            });
            tomSelectMitra.refreshOptions(false);

            tomSelectSales.clearOptions();
            tomSelectSales.refreshOptions(false);
        } catch (e) {
            console.error("Master data fail", e);
        }
    }

    async function openInvoiceModal(id = null, event = null, mode = 'create') {
        if (event) event.stopPropagation();

        const modalElement = document.getElementById('invoiceModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        const saveBtn = document.querySelector('button[onclick="saveFullInvoice()"]');

        resetModalForm();
        document.getElementById('form_mode').value = mode;
        document.getElementById('edit_invoice_id').value = id || '';

        if (mode === 'create') {
            document.getElementById('modalTitle').innerText = 'Buat Invoice Penjualan';
            saveBtn.classList.remove('d-none');
            toggleReadOnly(false);

            document.getElementById('modal_nomor_invoice').value =
                `INV/${new Date().getFullYear()}/${Date.now().toString().slice(-4)}`;
            document.getElementById('modal_tgl_invoice').valueAsDate = new Date();
            addNewProductRow();
            modal.show();
        } else {
            document.getElementById('modalTitle').innerText = (mode === 'show') ?
                'Rincian Invoice Penjualan' :
                'Edit Invoice Penjualan';

            (mode === 'show') ? saveBtn.classList.add('d-none'): saveBtn.classList.remove('d-none');
            toggleReadOnly(mode === 'show');

            try {
                const baseUrl = (window.financeApp && window.financeApp.API_URL) ? window.financeApp.API_URL :
                    '/api/invoice-api';
                const res = await fetch(`${baseUrl}/${id}`);
                const result = await res.json();

                if (result.success) {
                    const inv = result.data;
                    document.getElementById('modal_nomor_invoice').value = inv.nomor_invoice;
                    document.getElementById('modal_tgl_invoice').value = inv.tgl_invoice;
                    document.getElementById('modal_tgl_jatuh_tempo').value = inv.tgl_jatuh_tempo;
                    document.getElementById('modal_mitra_id').value = inv.mitra_id;
                    document.getElementById('modal_ref_no').value = inv.ref_no;
                    document.getElementById('modal_keterangan').value = inv.keterangan;
                    document.getElementById('modal_syarat').value = inv.syarat_ketentuan;
                    document.getElementById('modal_diskon_tambahan').value = inv.diskon_tambahan_nilai || 0;

                    if (fpJatuhTempo) fpJatuhTempo.setDate(inv.tgl_jatuh_tempo);

                    if (tomSelectMitra) {
                        if (mode === 'show' && !inv.mitra_id) {
                            tomSelectMitra.clear();
                            tomSelectMitra.addOption({
                                value: '',
                                text: 'Belum ada data'
                            });
                            tomSelectMitra.setValue('');
                            console.log(`tomSelectMitra: ${tomSelectMitra}`);
                        } else {
                            tomSelectMitra.setValue(inv.mitra_id);
                        }
                    }
                    renderMitraDetail();

                    if (tomSelectSales) {
                        if (mode === 'show' && !inv.sales_id) {
                            tomSelectSales.clear();
                            tomSelectSales.addOption({
                                value: '',
                                text: 'Belum ada data'
                            });
                            tomSelectSales.setValue('');
                        } else {
                            tomSelectSales.setValue(inv.sales_id);
                        }
                    }

                    if (inv.items && inv.items.length > 0) {
                        inv.items.forEach(it => addNewProductRow(it, mode === 'show'));
                    }
                    calculateInvoiceTotal();
                    modal.show();
                }
            } catch (e) {
                console.error("Edit fetch fail", e);
                alert('Gagal mengambil data invoice.');
            }
        }
    }

    function toggleReadOnly(isReadOnly) {
        const form = document.getElementById('invoiceForm');
        const allInputs = form.querySelectorAll('input, textarea, select');

        allInputs.forEach(input => {
            if (!['form_mode', 'edit_invoice_id'].includes(input.id)) {
                input.disabled = isReadOnly;
                if (isReadOnly) {
                    if (input.placeholder) input.dataset.oldPlaceholder = input.placeholder;
                    input.placeholder = '';
                } else if (input.dataset.oldPlaceholder) {
                    input.placeholder = input.dataset.oldPlaceholder;
                }
            }
        });

        if (tomSelectMitra) {
            isReadOnly ? tomSelectMitra.disable() : tomSelectMitra.enable();
        }

        if (tomSelectSales) {
            isReadOnly ? tomSelectSales.disable() : tomSelectSales.enable();
        }
    }

    const valOrDash = (val, mode) => (mode === 'show' && (val === null || val === '' || val === undefined)) ? '-' : (
        val ?? '');

    function addNewProductRow(existing = null, isReadOnly = false, mode = 'edit') {
        const rowId = existing && existing.id ? existing.id : 'row_' + Math.random().toString(36).substr(2, 9);
        const selectId = `select_prod_${rowId}`;

        const priceVal = existing ? existing.harga_satuan : 0;
        const discVal = existing ? existing.diskon_nilai : 0;
        const qtyVal = existing ? parseInt(existing.qty) : (mode === 'create' ? 1 : 0);

        let optProd = productCollection.map(p =>
            `<option value="${p.id}" data-price="${p.harga_jual}" ${existing && existing.produk_id == p.id ? 'selected' : ''}>${p.nama_produk}</option>`
        ).join('');

        const html = `
            <tr id="${rowId}">
                <td class="ps-4">
                    <select id="${selectId}" class="form-select f-row-input prod-select" ${isReadOnly ? 'disabled' : ''} onchange="autoFillPrice(${rowId})">
                        <option value="">Cari Produk...</option>${optProd}
                    </select>
                    <input type="text" class="form-control f-row-input small text-muted prod-desc mt-2" 
                        value="${existing?.deskripsi_produk || ''}" placeholder="${isReadOnly ? 'Belum ada data': 'Tambahkan deskripsi...'}" ${isReadOnly ? 'disabled' : ''}>
                </td>
                <td><input type="number" class="form-control f-row-input text-center fw-bold prod-qty" 
                    value="${qtyVal}" oninput="calculateInvoiceTotal()" ${isReadOnly ? 'disabled' : ''}></td>
                <td><input type="number" class="form-control f-row-input text-end prod-price" 
                    value="${priceVal}" oninput="calculateInvoiceTotal()" ${isReadOnly ? 'disabled' : ''}></td>
                <td><input type="number" class="form-control f-row-input text-end prod-disc" 
                    value="${discVal}" oninput="calculateInvoiceTotal()" ${isReadOnly ? 'disabled' : ''}></td>
                <td class="text-end pe-4 fw-bold f-mono prod-subtotal">Rp 0</td>
                <td class="text-center">
                    ${!isReadOnly ? `<button type="button" class="btn btn-link text-danger p-0" onclick="removeRow('${rowId}')"><i class="fa fa-times-circle"></i></button>` : ''}
                </td>
            </tr>`;
        document.getElementById('itemBodyList').insertAdjacentHTML('beforeend', html);

        const ts = new TomSelect(`#${selectId}`, {
            create: false,
            placeholder: isReadOnly ? '' : 'Cari Produk...',
            onChange: function(val) {
                autoFillPrice(rowId, val);
            }
        });

        if (existing) ts.setValue(existing.produk_id);
        if (isReadOnly) ts.disable();

        calculateInvoiceTotal();
    }

    function removeRow(rowId) {
        const row = document.getElementById(rowId);
        if (row) row.remove();
        calculateInvoiceTotal();
    }

    function autoFillPrice(rowId, productId) {
        const row = document.getElementById(rowId);
        if (!row || !productId) return;

        const product = productCollection.find(p => p.id == productId);
        if (product) {
            const priceInput = row.querySelector('.prod-price');
            const qtyInput = row.querySelector('.prod-qty');

            priceInput.value = product.harga_jual;

            if (!qtyInput.value || qtyInput.value == 0) {
                qtyInput.value = 1;
            }
        }
        calculateInvoiceTotal();
    }

    function openStockModal() {
        const modalElement = document.getElementById('stockModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        const body = document.getElementById('stockBodyList');

        if (stockDataTable) {
            stockDataTable.destroy();
            stockDataTable = null;
        }

        body.innerHTML = '';

        productCollection.forEach(p => {
            const currentStock = p.qty || 0;
            const trackLabel = p.track_stock == 0 ? 'Tidak' : 'Ya';
            const priceFormatted = window.financeHelpers.formatIDR(p.harga_jual);

            body.insertAdjacentHTML('beforeend', `
            <tr>
                <td class="ps-3">
                    <input type="checkbox" class="form-check-input stock-checkbox" value="${p.id}" 
                        data-name="${p.nama_produk}" data-price="${p.harga_jual}">
                </td>
                <td><code class="text-primary fw-bold">${p.sku_kode}</code></td>
                <td><span class="fw-bold text-dark">${p.nama_produk}</span></td>
                <td><span class="badge bg-light text-secondary border">${p.category.nama_kategori}</span></td>
                <td class="text-center">
                    <span class="badge ${currentStock > 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'} border">
                        ${currentStock}
                    </span>
                </td>
                <td class="text-end fw-bold" data-order="${p.harga_jual}">${priceFormatted}</td>
                <td class="text-center">${trackLabel}</td>
                <td class="small text-muted">${p.unit.keterangan || '-'}</td>
            </tr>
        `);
        });

        modal.show();

        modalElement.addEventListener('shown.bs.modal', function() {
            initStockDataTable();
        }, {
            once: true
        });
    }

    function initStockDataTable() {
        $.fn.dataTable.ext.search = [];

        stockDataTable = $('#stockTable').DataTable({
            paging: true,
            ordering: true,
            info: true,
            autoWidth: false,
            dom: 'rtip',
            columnDefs: [{
                orderable: false,
                targets: [0, 6, 7]
            }, {
                type: 'num',
                targets: 5
            }]
        });

        $('.col-filter').on('keyup change', function() {
            const colIndex = $(this).data('col');
            stockDataTable.column(colIndex).search(this.value).draw();
        });
    }

    function filterStockTable() {
        const rawInput = document.getElementById('searchStock').value.toLowerCase();
        const cleanedInput = rawInput.replace(/\./g, '');
        const rows = document.querySelectorAll('#stockBodyList tr');

        rows.forEach(row => {
            const originalText = row.innerText.toLowerCase();
            const cleanedText = originalText.replace(/\./g, '');

            if (originalText.includes(rawInput) || cleanedText.includes(cleanedInput)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function resetStockFilter() {
        document.getElementById('searchStock').value = '';
        const rows = document.querySelectorAll('#stockBodyList tr');
        rows.forEach(row => row.style.display = '');
    }

    function toggleAllStock(source) {
        const checkboxes = document.querySelectorAll('.stock-checkbox');
        checkboxes.forEach(cb => cb.checked = source.checked);
    }

    function addSelectedStockToInvoice() {
        const selected = document.querySelectorAll('.stock-checkbox:checked');

        if (selected.length === 0) {
            alert('Silakan pilih minimal satu produk!');
            return;
        }

        const currentRows = document.querySelectorAll('#itemBodyList tr');
        if (currentRows.length === 1) {
            const firstSelect = currentRows[0].querySelector('.prod-select');
            if (firstSelect && !firstSelect.value) {
                currentRows[0].remove();
            }
        }

        selected.forEach(cb => {
            const productId = cb.value;
            let existingRow = null;

            document.querySelectorAll('#itemBodyList tr').forEach(row => {
                const selectElement = row.querySelector('.prod-select');
                if (selectElement && selectElement.value == productId) {
                    existingRow = row;
                }
            });

            if (existingRow) {
                const qtyInput = existingRow.querySelector('.prod-qty');
                qtyInput.value = parseFloat(qtyInput.value) + 1;
            } else {
                const itemData = {
                    produk_id: productId,
                    harga_satuan: cb.getAttribute('data-price'),
                    qty: 1,
                    diskon_nilai: 0
                };
                addNewProductRow(itemData);
            }
        });

        document.getElementById('checkAllStock').checked = false;
        calculateInvoiceTotal();

        const modalStock = document.getElementById('stockModal');
        bootstrap.Modal.getInstance(modalStock).hide();
    }

    function calculateInvoiceTotal() {
        let sub = 0;
        document.querySelectorAll('#itemBodyList tr').forEach(row => {
            const q = parseFloat(row.querySelector('.prod-qty').value) || 0,
                p = parseFloat(row.querySelector('.prod-price').value) || 0,
                d = parseFloat(row.querySelector('.prod-disc').value) || 0;

            let lineSub = (q * p) - d;
            if (lineSub < 0) lineSub = 0;

            row.querySelector('.prod-subtotal').innerText = window.financeHelpers.formatIDR(lineSub);
            sub += lineSub;
        });

        const extra = parseFloat(document.getElementById('modal_diskon_tambahan').value) || 0;
        const grand = Math.max(0, sub - extra);

        document.getElementById('summary_subtotal').innerText = window.financeHelpers.formatIDR(sub);
        document.getElementById('summary_grand_total').innerText = window.financeHelpers.formatIDR(grand);
    }

    function renderMitraDetail() {
        const id = document.getElementById('modal_mitra_id').value;
        const item = mitraCollection.find(x => x.id == id);
        document.getElementById('mitra_detail_display').innerHTML = item ?
            `<strong>${item.nama}</strong><br>${item.alamat || 'Alamat tidak tersedia'}<br><i class="fa fa-phone me-1 small"></i> ${item.no_hp || '-'}` :
            `<i class="fa fa-info-circle me-2 text-primary"></i>Detail alamat dan kontak akan muncul otomatis.`;
    }

    function resetModalForm() {
        const form = document.getElementById('invoiceForm');
        form.reset();
        document.getElementById('itemBodyList').innerHTML = '';
        document.getElementById('mitra_detail_display').innerHTML =
            '<i class="fa fa-info-circle me-2 text-primary"></i>Detail alamat dan kontak akan muncul otomatis.';

        if (tomSelectMitra) {
            tomSelectMitra.enable();
            tomSelectMitra.clear();
        }
    }

    async function saveFullInvoice() {
        const mode = document.getElementById('form_mode').value;
        const id = document.getElementById('edit_invoice_id').value;

        const payload = {
            invoice: {
                tipe_invoice: 'Sales',
                nomor_invoice: document.getElementById('modal_nomor_invoice').value,
                tgl_invoice: document.getElementById('modal_tgl_invoice').value,
                tgl_jatuh_tempo: document.getElementById('modal_tgl_jatuh_tempo').value,
                mitra_id: document.getElementById('modal_mitra_id').value,
                ref_no: document.getElementById('modal_ref_no').value,
                keterangan: document.getElementById('modal_keterangan').value,
                syarat_ketentuan: document.getElementById('modal_syarat').value,
                diskon_tambahan_nilai: parseFloat(document.getElementById('modal_diskon_tambahan').value) || 0,
                total_akhir: parseFloat(document.getElementById('summary_grand_total').innerText.replace(
                    /[^0-9,-]+/g, "").replace(",", ".")) || 0,
                status_dok: 'Approved',
                status_pembayaran: 'Unpaid',
                biaya_kirim: 0,
                uang_muka: 0
            },
            items: []
        };

        if (!payload.invoice.mitra_id) {
            alert('Harap pilih Mitra!');
            return;
        }
        if (!payload.invoice.tgl_invoice) {
            alert('Harap isi Tanggal Invoice!');
            return;
        }

        let totalValid = 0;
        document.querySelectorAll('#itemBodyList tr').forEach(row => {
            const sel = row.querySelector('.prod-select');
            const qty = parseFloat(row.querySelector('.prod-qty').value) || 0;
            const price = parseFloat(row.querySelector('.prod-price').value) || 0;

            if (sel.value && qty > 0) {
                payload.items.push({
                    produk_id: sel.value,
                    qty: qty,
                    harga_satuan: price,
                    diskon_nilai: parseFloat(row.querySelector('.prod-disc').value) || 0,
                    total_harga_item: (qty * price) - (parseFloat(row.querySelector('.prod-disc')
                        .value) || 0),
                    taxes: []
                });
                totalValid++;
            }
        });

        if (totalValid === 0) {
            alert('Harap tambahkan minimal satu item produk valid!');
            return;
        }

        const url = mode === 'edit' ? `/api/invoice-api/${id}` : '/api/invoice-create-api';
        const method = mode === 'edit' ? 'PUT' : 'POST';

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                        'content') || ''
                },
                body: JSON.stringify(payload)
            });

            const result = await response.json();

            if (result.success) {
                alert('Dokumen berhasil disimpan!');
                bootstrap.Modal.getInstance(document.getElementById('invoiceModal')).hide();
                if (typeof loadInvoiceData === 'function') loadInvoiceData();
            } else {
                alert('Gagal menyimpan: ' + (result.message || JSON.stringify(result.errors)));
            }
        } catch (error) {
            console.error('Save error', error);
            alert('Terjadi kesalahan sistem saat menyimpan.');
        }
    }

    function addDaysToDueDate(days) {
        const tglTerbit = document.getElementById('modal_tgl_invoice').value;

        if (!tglTerbit) {
            alert('Isi tanggal terbit terlebih dahulu');
            return;
        }

        const date = new Date(tglTerbit);
        date.setDate(date.getDate() + days);

        fpJatuhTempo.setDate(date);
    }

    document.addEventListener('DOMContentLoaded', () => {
        initializeInvoiceData();

        fpJatuhTempo = flatpickr('#modal_tgl_jatuh_tempo', {
            altInput: true,
            altFormat: 'd/m/Y',
            dateFormat: 'Y-m-d',
            defaultDate: 'today',
            minDate: 'today',
            allowInput: true,
            disableMobile: "true",
            onReady: function(selectedDates, dateStr, instance) {
                const sidebar = document.createElement('div');
                sidebar.classList.add('fp-sidebar');

                const dueAddDays = [0, 3, 7, 14, 30, 45, 60];

                dueAddDays.forEach(days => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.classList.add('fp-sidebar-btn');
                    btn.innerHTML = days === 0 ? 'HARI INI' : `+${days} HARI`;

                    btn.addEventListener('click', () => {
                        addDaysToDueDate(days);
                        instance.close();
                    });

                    sidebar.appendChild(btn);
                })

                instance.innerContainer.appendChild(sidebar);
            }
        });
    });
</script>
