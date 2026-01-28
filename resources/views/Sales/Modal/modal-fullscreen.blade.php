<div class="modal fade" id="invoiceModal" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content border-0" style="background-color: #f8fafc;">
            <!-- Header -->
            <div class="modal-header bg-white border-bottom px-4 py-3 sticky-top shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-2 me-3">
                        <i class="fa fa-file-invoice fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="modalTitle">Invoice Penjualan</h5>
                        <p class="text-muted small mb-0">Kelola transaksi penjualan dengan pelanggan.</p>
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

            <div class="modal-body p-0">
                <form id="invoiceForm" class="h-100">
                    <input type="hidden" id="form_mode" value="create">
                    <input type="hidden" id="edit_invoice_id" value="">

                    <div class="row g-0 h-100">
                        <!-- Left Panel: Main Form -->
                        <div class="col-lg-9 h-100 overflow-auto custom-scrollbar p-4">

                            <!-- Top Section: Customer & Doc Details -->
                            <div class="card border-0 shadow-sm rounded-3 mb-4">
                                <div class="card-body p-4">
                                    <div class="row g-4">
                                        <!-- Customer Selection -->
                                        <div class="col-lg-6 border-end">
                                            <label class="f-label mb-2">Pelanggan / Mitra <span
                                                    class="text-danger">*</span></label>
                                            <select id="modal_mitra_id" class="form-select f-input-lg mb-3"
                                                onchange="renderMitraDetail()">
                                                <option value="">Cari Pelanggan...</option>
                                            </select>

                                            <!-- Mitra Detail Card -->
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
                                                <div class="small">Pilih pelanggan untuk melihat detail</div>
                                            </div>
                                        </div>

                                        <!-- Document Details -->
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
                                                        placeholder="Contoh: PO-001">
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
                                                    <label class="f-label">Salesperson</label>
                                                    <select id="modal_sales_id" class="form-select f-input">
                                                        <option value="">Pilih Sales...</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Items Section -->
                            <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
                                <div
                                    class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold text-dark text-uppercase small">
                                        <i class="fa fa-box me-2 text-primary"></i>Rincian Barang & Jasa
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
                                                <th class="text-end" width="15%">Harga</th>
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

                            <!-- Bottom Notes -->
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

                        <!-- Right Panel: Summary -->
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
                                        <span class="text-muted">Diskon Tambahan</span>
                                        <div style="width: 100px;">
                                            <input type="number" id="modal_diskon_tambahan"
                                                class="form-control form-control-sm text-end" value="0"
                                                oninput="calculateInvoiceTotal()">
                                        </div>
                                    </div>
                                    <!-- Tax could go here -->
                                </div>
                                <div
                                    class="p-3 rounded bg-primary bg-opacity-10 border border-primary border-opacity-25">
                                    <div class="text-center">
                                        <div class="small text-primary fw-bold text-uppercase mb-1">Total Tagihan</div>
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

<!-- Stock Modal remains largely the same, just refined styling -->
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

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<style>
    .f-input {
        padding: 0.75rem 1rem !important;
        font-size: 14px !important;
    }

    .flatpickr-input,
    .flatpickr-input+input {
        width: 100% !important;
        display: block !important;
    }

    .flatpickr-calendar {
        z-index: 10000 !important;
    }

    /* Lebar kalender diperluas agar sidebar 130px tidak memotong konten */
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
        /* Standar lebar Flatpickr */
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

    function applyDueShortcut(days) {
        const baseDateStr = document.getElementById('modal_tgl_invoice').value;
        if (!baseDateStr) return;

        const date = new Date(baseDateStr);
        date.setDate(date.getDate() + parseInt(days));
        if (fpDue) fpDue.setDate(date, true);
    }

    function initInvoiceDatePickers() {
        if (fpInvoice) fpInvoice.destroy();
        if (fpDue) fpDue.destroy();

        const commonCfg = {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd/m/Y',
            allowInput: true, // SESUDAH: Memungkinkan pengetikan manual tahun
            disableMobile: true,
            monthSelectorType: 'dropdown',
            static: true, // SESUDAH: Meletakkan elemen di dalam DOM modal agar tidak diblokir focus-trap
            altInputClass: 'form-control f-input bg-white'
        };

        fpInvoice = flatpickr('#modal_tgl_invoice', {
            ...commonCfg,
            onChange: (selectedDates, dateStr) => {
                if (fpDue && !document.getElementById('modal_tgl_jatuh_tempo').value) {
                    applyDueShortcut(30);
                }
            }
        });

        fpDue = flatpickr('#modal_tgl_jatuh_tempo', {
            ...commonCfg,
            onReady: (selectedDates, dateStr, instance) => injectDueSidebar(instance)
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

    // Override openInvoiceModal untuk memanggil inisialisasi picker
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

    // --- Modal Logic ---
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
        if (!Array.isArray(masterMitra) || masterMitra.length === 0) {
            await initializeMasterData();
        }

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

    // --- Item Logic ---
    function addNewProductRow(data = null) {
        const tbody = document.getElementById('itemBodyList');
        const rowId = Date.now() + Math.random().toString(36).substr(2, 5);

        const tr = document.createElement('tr');
        tr.id = `row_${rowId}`;
        tr.className = 'border-bottom border-light';

        const productName = data ? (data.product?.nama_produk || data.nama_produk_manual) : '';
        const qty = data ? parseFloat(data.qty) : 1;
        const price = data ? parseFloat(data.harga_satuan) : 0;
        const disc = data ? parseFloat(data.diskon_item) : 0;
        const unit = data?.product?.unit?.nama_unit || 'Pcs';
        const productId = data?.product_id || '';

        tr.innerHTML = `
            <td class="ps-4 py-3">
                <input type="hidden" class="prod-id" value="${productId}">
                <input type="text" class="form-control form-control-sm fw-bold mb-1 prod-name" placeholder="Nama Produk / Jasa" value="${productName}">
                <input type="text" class="form-control form-control-sm text-muted small prod-desc" placeholder="Deskripsi (Opsional)" value="${data?.deskripsi_item || ''}">
            </td>
            <td class="py-3">
                <input type="number" class="form-control form-control-sm text-center prod-qty" value="${qty}" min="1" oninput="calculateInvoiceTotal()">
            </td>
            <td class="py-3 text-center small text-muted pt-4">${unit}</td>
            <td class="py-3">
                <input type="number" class="form-control form-control-sm text-end prod-price" value="${price}" oninput="calculateInvoiceTotal()">
            </td>
            <td class="py-3">
                <input type="number" class="form-control form-control-sm text-end prod-disc" value="${disc}" oninput="calculateInvoiceTotal()">
            </td>
            <td class="py-3 text-end pe-4 fw-bold text-dark prod-subtotal">Rp 0</td>
            <td class="py-3 text-center">
                <button type="button" class="btn btn-link text-danger p-0" onclick="removeProductRow('${rowId}')"><i class="fa fa-times"></i></button>
            </td>
        `;

        tbody.appendChild(tr);
        calculateInvoiceTotal();
    }

    function removeProductRow(id) {
        const row = document.getElementById(`row_${id}`);
        if (row) row.remove();
        calculateInvoiceTotal();
    }

    function calculateInvoiceTotal() {
        let subtotal = 0;

        document.querySelectorAll('#itemBodyList tr').forEach(row => {
            const qty = parseFloat(row.querySelector('.prod-qty').value) || 0;
            const price = parseFloat(row.querySelector('.prod-price').value) || 0;
            const disc = parseFloat(row.querySelector('.prod-disc').value) || 0;

            let lineTotal = (qty * price) - disc;
            if (lineTotal < 0) lineTotal = 0;

            row.querySelector('.prod-subtotal').innerText = window.financeApp.formatIDR(lineTotal);
            subtotal += lineTotal;
        });

        const extraDisc = parseFloat(document.getElementById('modal_diskon_tambahan').value) || 0;
        const grandTotal = Math.max(0, subtotal - extraDisc);

        document.getElementById('summary_subtotal').innerText = window.financeApp.formatIDR(subtotal);
        document.getElementById('summary_grand_total').innerText = window.financeApp.formatIDR(grandTotal);
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
            items.push({
                produk_id: row.querySelector('.prod-id').value || null,
                nama_produk_manual: row.querySelector('.prod-name').value,
                deskripsi_produk: row.querySelector('.prod-desc').value,
                qty: qty,
                harga_satuan: price,
                diskon_nilai: disc,
                total_harga_item: subtotal
            });
        });

        const payload = {
            invoice: {
                tipe_invoice: 'Sales',
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
                window.location.href = `{{ url('sales') }}/${result.data.invoice.id}`;
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

    // --- Stock Modal Logic ---
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
        tbody.innerHTML = '';
        data.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="text-center"><input type="checkbox" class="form-check-input stock-check" value="${item.id}" data-raw='${JSON.stringify(item)}'></td>
                <td>
                    <div class="fw-bold text-dark">${item.nama_produk}</div>
                    <div class="small text-muted">${item.kode_produk}</div>
                </td>
                <td><span class="badge bg-light text-dark border">${item.kategori?.nama_kategori || '-'}</span></td>
                <td class="text-center fw-bold">${item.stok_akhir || 0} ${item.unit?.nama_unit || ''}</td>
                <td class="text-end font-monospace">${window.financeApp.formatIDR(item.harga_jual)}</td>
                <td class="text-center"><span class="badge ${item.lacak_stok ? 'bg-success' : 'bg-secondary'}">${item.lacak_stok ? 'YES' : 'NO'}</span></td>
            `;
            tbody.appendChild(tr);
        });
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
        checked.forEach(chk => {
            const data = JSON.parse(chk.dataset.raw);
            addNewProductRow({
                product_id: data.id,
                product: data,
                nama_produk_manual: data.nama_produk,
                qty: 1,
                harga_satuan: data.harga_jual,
                diskon_item: 0
            });
        });
        bootstrap.Modal.getInstance(document.getElementById('stockModal')).hide();
    }
</script>
