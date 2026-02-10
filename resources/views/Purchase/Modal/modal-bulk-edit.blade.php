<div class="modal fade" id="modalBulkEdit" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4" style="height: 90vh;">
            <div class="d-flex justify-content-between modal-header border-bottom py-3 px-4 bg-dark">
                <div>
                    <h5 class="modal-title fw-bold text-white">Bulk Edit Invoice Pembelian</h5>
                    <p class="text-white-50 small mb-0">Edit beberapa invoice pembelian sekaligus.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                        <span id="bulk-edit-count">0</span> Selected
                    </span>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-0">
                <div class="d-flex h-100">
                    <!-- Left: List -->
                    <div class="border-end bg-light d-flex flex-column" style="width: 320px; flex-shrink: 0;">
                        <div class="p-3 border-bottom bg-white sticky-top">
                            <input type="text" class="form-control form-control-sm" placeholder="Cari invoice..."
                                id="bulk-list-filter">
                        </div>
                        <div class="list-group list-group-flush overflow-auto custom-scrollbar" id="bulk-invoice-list">
                            <!-- Populated by JS -->
                            <div class="text-center p-4 text-muted">Loading...</div>
                        </div>
                    </div>

                    <!-- Right: Form -->
                    <div class="flex-grow-1 d-flex flex-column bg-white h-100 overflow-hidden">
                        <div class="flex-grow-1 overflow-auto custom-scrollbar p-4" id="bulk-form-container">
                            <!-- Loading State -->
                            <div id="bulk-form-loading"
                                class="d-flex flex-column align-items-center justify-content-center h-100 text-muted">
                                <div class="spinner-border text-primary mb-3" role="status"></div>
                                <div>Pilih invoice untuk diedit</div>
                            </div>

                            <!-- Actual Form -->
                            <div id="bulk-form-content" class="d-none">
                                <form id="bulk-active-form">
                                    <input type="hidden" id="bulk_active_id">
                                    <input type="hidden" id="bulk_mitra_id_hidden">

                                    <!-- Header Fields -->
                                    <div class="card border border-light shadow-sm rounded-3 mb-4">
                                        <div class="card-body bg-light bg-opacity-50">
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold text-muted">Nomor
                                                        Invoice</label>
                                                    <input type="text" class="form-control fw-bold text-dark bg-white"
                                                        id="bulk_nomor_invoice" readonly>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold text-muted">Supplier</label>
                                                    <select class="form-select" id="bulk_mitra_id"
                                                        style="display:none"></select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold text-muted">Status
                                                        Dokumen</label>
                                                    <select class="form-select" id="bulk_status_dok"
                                                        onchange="updateActiveBulkField('status_dok', this.value)">
                                                        <option value="Draft">Draft</option>
                                                        <option value="Sent">Sent</option>
                                                        <option value="Approved">Approved</option>
                                                        <option value="Rejected">Rejected</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small fw-bold text-muted">Tanggal
                                                        Invoice</label>
                                                    <input type="date" class="form-control" id="bulk_tgl_invoice"
                                                        onchange="updateActiveBulkField('tgl_invoice', this.value)">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small fw-bold text-muted">Jatuh
                                                        Tempo</label>
                                                    <input type="date" class="form-control" id="bulk_tgl_jatuh_tempo"
                                                        onchange="updateActiveBulkField('tgl_jatuh_tempo', this.value)">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small fw-bold text-muted">Purchasing
                                                        Staff</label>
                                                    <select class="form-select" id="bulk_sales_id"
                                                        onchange="updateActiveBulkField('sales_id', this.value)">
                                                        <option value="">Pilih Staff...</option>
                                                        <!-- Populated by JS -->
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small fw-bold text-muted">Ref No /
                                                        PO</label>
                                                    <input type="text" class="form-control" id="bulk_ref_no"
                                                        onchange="updateActiveBulkField('ref_no', this.value)">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Items -->
                                    <div class="mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="fw-bold mb-0 text-dark"><i
                                                    class="fa fa-box me-2 text-primary"></i>Items</h6>
                                            <div class="d-flex gap-2">
                                                <button type="button"
                                                    class="btn btn-outline-secondary btn-sm fw-bold px-3 rounded-pill"
                                                    onclick="openBulkStockModal()">
                                                    <i class="fa fa-search me-1"></i> Cari Stok
                                                </button>
                                                <button type="button"
                                                    class="btn btn-primary btn-sm fw-bold px-3 rounded-pill"
                                                    onclick="bulkAddNewProductRow()">
                                                    <i class="fa fa-plus me-1"></i> Tambah
                                                </button>
                                            </div>
                                        </div>
                                        <div class="table-responsive border rounded-3">
                                            <table class="table table-sm align-middle mb-0 bg-white">
                                                <thead class="bg-light text-secondary small text-uppercase">
                                                    <tr>
                                                        <th class="ps-3 py-2" width="35%">Produk</th>
                                                        <th class="text-center py-2" width="10%">Qty</th>
                                                        <th class="text-center py-2" width="10%">Unit</th>
                                                        <th class="text-end py-2" width="15%">Harga Beli</th>
                                                        <th class="text-end py-2" width="15%">Disc</th>
                                                        <th class="text-end py-2" width="15%">Total</th>
                                                        <th width="30"></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="bulk-items-body">
                                                    <!-- Items rendered here -->
                                                </tbody>
                                                <tfoot class="border-top bg-light fw-bold">
                                                    <tr>
                                                        <td colspan="5" class="text-end py-2">Subtotal</td>
                                                        <td class="text-end pe-3 py-2" id="bulk_subtotal">Rp 0</td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="5" class="text-end py-2 align-middle">Potongan
                                                            Lumpsum</td>
                                                        <td class="text-end pe-3 py-2">
                                                            <input type="text"
                                                                class="form-control form-control-sm text-end fw-bold"
                                                                id="bulk_diskon_tambahan" value="0"
                                                                onkeyup="bulkFormatAndCalc(this)">
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="5" class="text-end py-2">Total Tagihan</td>
                                                        <td class="text-end pe-3 py-2 text-primary"
                                                            id="bulk_grand_total">Rp 0</td>
                                                        <td></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Footer -->
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-muted">Catatan Internal</label>
                                            <textarea class="form-control" rows="2" id="bulk_catatan"
                                                onchange="updateActiveBulkField('catatan', this.value)"></textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-muted">Syarat &
                                                Ketentuan</label>
                                            <textarea class="form-control" rows="2" id="bulk_syarat"
                                                onchange="updateActiveBulkField('syarat_ketentuan', this.value)"></textarea>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="p-3 border-top bg-light d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-primary fw-bold px-4 shadow-sm"
                                onclick="saveBulkEdit()">
                                <i class="fa fa-save me-2"></i> Save All Changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<template id="bulkProductRowTemplate">
    <tr class="bulk-product-row border-bottom border-light">
        <td class="ps-3 py-2">
            <input type="hidden" class="prod-id">
            <select class="prod-select-item form-control-sm"></select>
            <input type="text" class="form-control form-control-sm text-muted small prod-desc mt-1"
                placeholder="Deskripsi" style="font-size: 11px;">
        </td>
        <td class="py-2">
            <input type="number" class="form-control form-control-sm text-center prod-qty" value="1" min="1"
                oninput="bulkCalculateTotal()">
        </td>
        <td class="text-center py-2 small text-muted prod-unit-label">-</td>
        <td class="py-2">
            <input type="text" class="form-control form-control-sm text-end prod-price" value="0"
                onkeyup="bulkFormatAndCalc(this)">
        </td>
        <td class="py-2">
            <input type="text" class="form-control form-control-sm text-end prod-disc" value="0"
                onkeyup="bulkFormatAndCalc(this)">
        </td>
        <td class="text-end py-2 pe-3 small fw-bold prod-subtotal">Rp 0</td>
        <td class="text-center py-2">
            <button type="button" class="btn btn-link text-danger p-0 btn-remove-row"
                onclick="bulkRemoveProductRow(this)">
                <i class="fa fa-times"></i>
            </button>
        </td>
    </tr>
</template>

@push('js')
    <script>
        let bulkData = {};
        let activeBulkId = null;
        let bulkSalesPersons = [];
        let tomSelectBulkMitra = null;

        async function initBulkEdit() {
            const selectedIds = window.financeApp.selectedIds;
            if (selectedIds.length === 0) return;

            const modalEl = document.getElementById('modalBulkEdit');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();

            document.getElementById('bulk-edit-count').innerText = selectedIds.length;
            document.getElementById('bulk-invoice-list').innerHTML = '<div class="text-center p-4 text-muted"><div class="spinner-border text-primary mb-2"></div><div>Loading data...</div></div>';
            document.getElementById('bulk-form-loading').classList.remove('d-none');
            document.getElementById('bulk-form-content').classList.add('d-none');
            document.getElementById('bulk-list-filter').value = '';

            // Load Salespersons/Staff
            if (bulkSalesPersons.length === 0) {
                await fetchSalesPersons();
            }
            renderBulkSalesOptions();

            // Init TomSelect for Mitra (Supplier)
            if (!tomSelectBulkMitra) {
                tomSelectBulkMitra = new TomSelect('#bulk_mitra_id', {
                    valueField: 'id',
                    labelField: 'nama',
                    searchField: ['nama'],
                    options: typeof masterMitra !== 'undefined' ? masterMitra : [],
                    create: false,
                    placeholder: 'Pilih Supplier...',
                    dropdownParent: 'body',
                    onChange: function (val) {
                        if (activeBulkId && bulkData[activeBulkId]) {
                            bulkData[activeBulkId].mitra_id = val;
                            const m = this.options[val];
                            if (m) {
                                bulkData[activeBulkId].mitra = { nama: m.nama };
                                renderBulkSidebar(document.getElementById('bulk-list-filter').value.toLowerCase());
                            }
                        }
                    }
                });
            } else {
                tomSelectBulkMitra.clear();
                if (typeof masterMitra !== 'undefined') {
                    tomSelectBulkMitra.clearOptions();
                    tomSelectBulkMitra.addOptions(masterMitra);
                }
            }

            bulkData = {};
            activeBulkId = null;

            try {
                // Fetch all data
                const promises = selectedIds.map(id =>
                    fetch(`${window.financeApp.API_URL}/${id}`).then(res => res.json())
                );

                const results = await Promise.all(promises);

                results.forEach(res => {
                    if (res.success) {
                        bulkData[res.data.id] = JSON.parse(JSON.stringify(res.data)); // Deep copy
                        if (!bulkData[res.data.id].items) bulkData[res.data.id].items = [];
                    }
                });

                renderBulkSidebar();

                const firstId = Object.keys(bulkData)[0];
                if (firstId) {
                    setActiveBulkInvoice(firstId);
                }

            } catch (error) {
                console.error(error);
                alert('Failed to load bulk data.');
            }
        }

        async function fetchSalesPersons() {
            try {
                const res = await fetch('/api/user-api');
                const json = await res.json();
                if (json.success) {
                    bulkSalesPersons = json.data.data || json.data;
                }
            } catch (e) { console.error('Failed to load users', e); }
        }

        function renderBulkSalesOptions() {
            const sel = document.getElementById('bulk_sales_id');
            sel.innerHTML = '<option value="">Pilih Staff...</option>';
            bulkSalesPersons.forEach(u => {
                sel.insertAdjacentHTML('beforeend', `<option value="${u.id}">${u.name}</option>`);
            });
        }

        document.getElementById('bulk-list-filter').addEventListener('keyup', function () {
            renderBulkSidebar(this.value.toLowerCase());
        });

        function renderBulkSidebar(filter = '') {
            const container = document.getElementById('bulk-invoice-list');
            container.innerHTML = '';

            Object.values(bulkData).forEach(inv => {
                if (filter) {
                    const matchObj = (inv.nomor_invoice || '').toLowerCase().includes(filter) ||
                        (inv.mitra ? inv.mitra.nama : '').toLowerCase().includes(filter);
                    if (!matchObj) return;
                }

                const isActive = inv.id == activeBulkId;
                const el = document.createElement('a');
                el.href = 'javascript:void(0)';
                el.className = `list-group-item list-group-item-action py-3 px-3 border-bottom ${isActive ? 'active border-primary border-start border-3' : ''}`;
                if (isActive) el.style.borderLeftWidth = '4px !important';

                el.onclick = () => {
                    if (activeBulkId) saveCurrentBulkStateToMemory();
                    setActiveBulkInvoice(inv.id);
                };

                el.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="fw-bold text-truncate ${isActive ? 'text-white' : 'text-dark'}" style="max-width: 140px;">${inv.nomor_invoice}</div>
                            <span class="badge ${isActive ? 'bg-white text-primary' : 'bg-light text-secondary'} border">${inv.status_dok}</span>
                        </div>
                        <div class="small ${isActive ? 'text-white-50' : 'text-muted'} text-truncate">${inv.mitra ? inv.mitra.nama : 'Umum'}</div>
                        <div class="small ${isActive ? 'text-white-50' : 'text-muted'} d-flex justify-content-between mt-1">
                            <span class="bulk-sidebar-total">${window.financeApp.formatIDR(inv.total_akhir)}</span>
                            <span>${new Date(inv.tgl_invoice).toLocaleDateString('id-ID', { day: '2-digit', month: 'short' })}</span>
                        </div>
                    `;
                container.appendChild(el);
            });
        }

        function saveCurrentBulkStateToMemory() {
            if (!activeBulkId || !bulkData[activeBulkId]) return;

            const currentItems = [];
            document.querySelectorAll('#bulk-items-body tr.bulk-product-row').forEach(row => {
                const idInput = row.querySelector('.prod-id');
                const qtyInput = row.querySelector('.prod-qty');
                const priceInput = row.querySelector('.prod-price');
                const discInput = row.querySelector('.prod-disc');
                const descInput = row.querySelector('.prod-desc');
                const tsSelect = row.querySelector('.prod-select-item');

                if (!idInput || !qtyInput) return;

                let prodName = '';
                if (tsSelect && tsSelect.tomselect) {
                    const val = tsSelect.tomselect.getValue();
                    if (val && tsSelect.tomselect.options[val]) {
                        prodName = tsSelect.tomselect.options[val].nama;
                    }
                }

                const price = cleanNumber(priceInput.value);
                const disc = cleanNumber(discInput.value);
                const qty = parseFloat(qtyInput.value) || 0;
                const total = Math.max(0, (qty * price) - disc);

                currentItems.push({
                    produk_id: idInput.value,
                    qty: qty,
                    harga_satuan: price, // Use generic field name, mapped from price
                    harga_beli: price,   // Specific for purchase
                    diskon_nilai: disc,
                    deskripsi_produk: descInput.value,
                    total_harga_item: total,
                    nama_produk_manual: prodName
                });
            });

            bulkData[activeBulkId].items = currentItems;
            bulkData[activeBulkId].diskon_tambahan_nilai = cleanNumber(document.getElementById('bulk_diskon_tambahan').value);

            const rawTotal = document.getElementById('bulk_grand_total').innerText;
            bulkData[activeBulkId].total_akhir = parseFloat(rawTotal.replace(/[^0-9,-]+/g, '').replace(',', '.')) || 0;
        }

        function setActiveBulkInvoice(id) {
            activeBulkId = id;
            renderBulkSidebar(document.getElementById('bulk-list-filter').value.toLowerCase());

            const data = bulkData[id];
            if (!data) return;

            document.getElementById('bulk-form-loading').classList.add('d-none');
            document.getElementById('bulk-form-content').classList.remove('d-none');
            document.getElementById('bulk_active_id').value = id;
            document.getElementById('bulk_mitra_id_hidden').value = data.mitra_id || '';

            document.getElementById('bulk_nomor_invoice').value = data.nomor_invoice;

            // Set Mitra in TomSelect
            if (tomSelectBulkMitra) {
                if (data.mitra_id) tomSelectBulkMitra.setValue(data.mitra_id, true);
                else tomSelectBulkMitra.clear(true);
            }

            document.getElementById('bulk_tgl_invoice').value = data.tgl_invoice;
            document.getElementById('bulk_tgl_jatuh_tempo').value = data.tgl_jatuh_tempo;
            document.getElementById('bulk_status_dok').value = data.status_dok;
            document.getElementById('bulk_catatan').value = data.catatan || data.keterangan || '';
            document.getElementById('bulk_syarat').value = data.syarat_ketentuan || '';
            document.getElementById('bulk_sales_id').value = data.sales_id || '';
            document.getElementById('bulk_ref_no').value = data.ref_no || '';

            document.getElementById('bulk_diskon_tambahan').value = formatRupiah(Math.round(data.diskon_tambahan_nilai || 0));

            renderBulkItems(data);
        }

        function renderBulkItems(data) {
            const tbody = document.getElementById('bulk-items-body');
            tbody.innerHTML = '';

            if (data.items && data.items.length > 0) {
                data.items.forEach(item => {
                    bulkAddNewProductRow(item);
                });
            }
            bulkCalculateTotal();
        }

        function bulkAddNewProductRow(data = null) {
            const tbody = document.getElementById('bulk-items-body');
            const template = document.getElementById('bulkProductRowTemplate');
            const clone = template.content.cloneNode(true);
            const tr = clone.querySelector('tr');

            const selectEl = tr.querySelector('.prod-select-item');
            const priceInput = tr.querySelector('.prod-price');
            const qtyInput = tr.querySelector('.prod-qty');
            const discInput = tr.querySelector('.prod-disc');
            const idInput = tr.querySelector('.prod-id');
            const unitLabel = tr.querySelector('.prod-unit-label');
            const descInput = tr.querySelector('.prod-desc');

            if (typeof masterProduk === 'undefined' || masterProduk.length === 0) {
                // Warning: might be empty if not loaded in parent
            }

            const ts = new TomSelect(selectEl, {
                options: (typeof masterProduk !== 'undefined' ? masterProduk : []).map(p => ({
                    id: String(p.id),
                    nama: p.nama_produk,
                    sku: p.sku_kode,
                    harga: parseFloat(p.harga_beli || 0), // Purchase Price
                    unit: p.unit?.nama_unit || 'Pcs'
                })),
                valueField: 'id',
                labelField: 'nama',
                searchField: ['nama', 'sku'],
                placeholder: 'Produk...',
                dropdownParent: 'body',
                render: {
                    option: (d, esc) => `<div><div class="fw-bold">${esc(d.nama)}</div><small class="text-muted">${esc(d.sku)}</small></div>`,
                    item: (d, esc) => `<div>${esc(d.nama)}</div>`
                },
                onChange: function (val) {
                    const selected = this.options[val];
                    if (selected) {
                        idInput.value = selected.id;
                        priceInput.value = formatRupiah(Math.round(selected.harga));
                        unitLabel.innerText = selected.unit;
                        bulkCalculateTotal();
                        updateCurrentInvoiceItemsInMemory();
                    }
                }
            });

            if (data) {
                setTimeout(() => {
                    let rawId = data.produk_id || data.product_id || data.id;
                    const prodId = String(rawId);

                    if (ts.options[prodId]) {
                        ts.setValue(prodId, true);
                    } else {
                        ts.addOption({
                            id: prodId,
                            nama: data.nama_produk_manual || data.nama_produk || 'Item',
                            harga: 0
                        });
                        ts.setValue(prodId, true);
                    }

                    idInput.value = prodId;
                    qtyInput.value = parseFloat(data.qty) || 1;

                    // Purchase uses harga_beli or generic harga_satuan
                    const rawPrice = data.harga_beli ?? data.harga_satuan ?? 0;
                    priceInput.value = formatRupiah(Math.round(rawPrice));

                    discInput.value = formatRupiah(Math.round(data.diskon_nilai ?? data.diskon_item ?? 0));
                    descInput.value = data.deskripsi_produk || data.deskripsi_item || '';

                    let unitName = 'Pcs';
                    if (data.unit) {
                        unitName = (typeof data.unit === 'object') ? data.unit.nama_unit : data.unit;
                    }
                    unitLabel.innerText = unitName;

                    bulkCalculateTotal();
                }, 50);
            }

            tbody.appendChild(tr);
        }

        function bulkRemoveProductRow(btn) {
            const tr = btn.closest('tr');
            const sel = tr.querySelector('.prod-select-item');
            if (sel && sel.tomselect) sel.tomselect.destroy();
            tr.remove();
            bulkCalculateTotal();
            updateCurrentInvoiceItemsInMemory();
        }

        function bulkFormatAndCalc(input) {
            input.value = formatRupiah(input.value);
            bulkCalculateTotal();
            updateCurrentInvoiceItemsInMemory();
        }

        function bulkCalculateTotal() {
            let subtotal = 0;
            document.querySelectorAll('#bulk-items-body tr.bulk-product-row').forEach(row => {
                const qty = parseFloat(row.querySelector('.prod-qty').value) || 0;
                const price = cleanNumber(row.querySelector('.prod-price').value);
                const disc = cleanNumber(row.querySelector('.prod-disc').value);
                const total = Math.max(0, (qty * price) - disc);

                row.querySelector('.prod-subtotal').innerText = window.financeApp.formatIDR(total);
                subtotal += total;
            });

            const extraDisc = cleanNumber(document.getElementById('bulk_diskon_tambahan').value);
            const grandTotal = Math.max(0, subtotal - extraDisc);

            document.getElementById('bulk_subtotal').innerText = window.financeApp.formatIDR(subtotal);
            document.getElementById('bulk_grand_total').innerText = window.financeApp.formatIDR(grandTotal);
        }

        function updateCurrentInvoiceItemsInMemory() {
            if (activeBulkId) saveCurrentBulkStateToMemory();
        }

        window.updateActiveBulkField = function (field, value) {
            if (!activeBulkId || !bulkData[activeBulkId]) return;
            bulkData[activeBulkId][field] = value;
        };

        function openBulkStockModal() {
            window.activeStockTarget = 'bulk';
            if (typeof openStockModal === 'function') {
                openStockModal();
            } else {
                alert('Stock modal not available');
            }
        }

        window.saveBulkEdit = async function () {
            saveCurrentBulkStateToMemory();

            const btn = document.querySelector('#modalBulkEdit .btn-primary');
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i> Saving...';
            btn.disabled = true;

            let successFn = 0;
            let failFn = 0;

            try {
                const promises = Object.values(bulkData).map(inv => {
                    const payload = {
                        invoice: {
                            tgl_invoice: inv.tgl_invoice,
                            tgl_jatuh_tempo: inv.tgl_jatuh_tempo,
                            status_dok: inv.status_dok,
                            catatan: inv.catatan,
                            keterangan: inv.catatan || inv.keterangan,
                            syarat_ketentuan: inv.syarat_ketentuan,
                            sales_id: inv.sales_id,
                            ref_no: inv.ref_no,
                            diskon_tambahan_nilai: inv.diskon_tambahan_nilai,
                            total_akhir: inv.total_akhir,
                            mitra_id: inv.mitra_id, // include Mitra (Supplier)
                        },
                        items: inv.items
                    };

                    return fetch(`${window.financeApp.API_URL}/${inv.id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    }).then(res => res.json())
                        .then(data => {
                            if (data.success) successFn++;
                            else {
                                console.error('Failed ID ' + inv.id, data);
                                failFn++;
                            }
                        })
                        .catch(err => {
                            console.error('Error updating ' + inv.id, err);
                            failFn++;
                        });
                });

                await Promise.all(promises);

                alert(`Bulk Save Complete.\nSuccess: ${successFn}\nFailed: ${failFn}`);

                const modal = bootstrap.Modal.getInstance(document.getElementById('modalBulkEdit'));
                modal.hide();

                if (window.loadInvoiceData) window.loadInvoiceData();

            } catch (e) {
                console.error(e);
                alert('System error during bulk save.');
            } finally {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }
        }
    </script>
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #aaa;
        }
    </style>
@endpush