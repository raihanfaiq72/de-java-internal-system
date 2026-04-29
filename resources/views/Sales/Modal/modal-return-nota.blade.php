{{-- Modal Return Nota - Fullscreen, same style as modal-fullscreen --}}
<div class="modal fade" id="returnNotaModal" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content border-0" style="background-color: #f8fafc;">
            <!-- Header -->
            <div class="modal-header bg-white border-bottom px-4 py-3 shadow-sm" style="min-height: 110px;">
                <div class="d-flex align-items-center">
                    <div class="bg-danger bg-opacity-10 text-danger p-2 rounded-2 me-3">
                        <i class="fa fa-undo fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0">Buat Nota Return</h5>
                        <p class="text-muted small mb-0">Koreksi atas kesalahan cetak nota penjualan.</p>
                    </div>
                </div>
                <div class="ms-auto d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-light border text-secondary fw-semibold px-4"
                        data-bs-dismiss="modal">Tutup</button>
                    <button type="button" id="rnBtnSave" class="btn btn-danger px-4 fw-bold shadow-sm"
                        onclick="saveReturnNota()">
                        <i class="fa fa-save me-2"></i>Simpan Return
                    </button>
                </div>
            </div>

            <div class="modal-body p-0">
                <form id="returnNotaForm" class="h-100">
                    <input type="hidden" id="rn_original_invoice_id" value="">
                    <input type="hidden" id="rn_original_nomor_invoice" value="">

                    <div class="row g-0 h-100">
                        <!-- Left Panel -->
                        <div class="col-lg-9 h-100 overflow-auto custom-scrollbar p-4">

                            <!-- Info Banner -->
                            <div
                                class="alert alert-warning border-0 shadow-sm rounded-3 mb-4 d-flex align-items-start gap-3">
                                <i class="fa fa-exclamation-triangle text-warning mt-1 fs-5"></i>
                                <div>
                                    <div class="fw-bold text-dark mb-1">Nota Return / Retur Penjualan</div>
                                    <div class="small text-muted">
                                        Nota return digunakan untuk mengoreksi kesalahan cetak nota — misalnya jumlah
                                        barang atau harga yang salah.
                                        Stok akan dikembalikan dan jurnal akuntansi akan dibuat otomatis (Debit Retur
                                        Penjualan, Kredit Piutang Usaha).
                                    </div>
                                </div>
                            </div>

                            <!-- Reference Invoice Info -->
                            <div class="card border-0 shadow-sm rounded-3 mb-4">
                                <div class="card-body p-4">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="f-label mb-1">Nota Asal (Referensi)</label>
                                            <div class="form-control bg-light text-primary fw-bold"
                                                id="rn_display_original">-</div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="f-label mb-1">Nomor Return <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="rn_nomor_invoice"
                                                class="form-control f-input fw-bold bg-light text-danger"
                                                placeholder="Contoh: RTR-001">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="f-label mb-1">Tanggal Return <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="rn_tgl_invoice" class="form-control f-input">
                                        </div>
                                        <div class="col-12">
                                            <label class="f-label mb-1">Alasan / Keterangan Return</label>
                                            <textarea id="rn_keterangan" class="form-control f-text bg-light border-0" rows="2"
                                                placeholder="Contoh: Kesalahan cetak harga satuan produk X..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Items Section -->
                            <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
                                <div
                                    class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold text-dark text-uppercase small">
                                        <i class="fa fa-box me-2 text-danger"></i>Item yang Diretur
                                    </h6>
                                    <div class="d-flex gap-2">
                                        <button type="button"
                                            class="btn btn-outline-secondary btn-sm fw-bold px-3 rounded-pill"
                                            onclick="rnLoadItemsFromOriginal()">
                                            <i class="fa fa-copy me-1"></i> Salin dari Nota Asal
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm fw-bold px-3 rounded-pill"
                                            onclick="rnAddNewRow()">
                                            <i class="fa fa-plus me-1"></i> Tambah Baris
                                        </button>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table align-middle mb-0 table-hover" id="rnItemTable">
                                        <thead class="bg-light text-secondary small fw-bold text-uppercase">
                                            <tr>
                                                <th class="ps-4 py-3" width="30%">Produk / Deskripsi</th>
                                                <th class="text-center" width="10%">Qty</th>
                                                <th class="text-center" width="8%">Satuan</th>
                                                <th class="text-end" width="15%">Harga Satuan</th>
                                                <th class="text-center" width="10%">Tipe Disc</th>
                                                <th class="text-end" width="12%">Disc</th>
                                                <th class="text-end" width="15%">Total</th>
                                                <th width="5%" class="pe-4"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="rnItemBody" class="border-top-0"></tbody>
                                        <tfoot class="bg-light">
                                            <tr id="rnEmptyRow">
                                                <td colspan="8"
                                                    class="text-center py-2 text-muted small fst-italic">
                                                    Klik "Salin dari Nota Asal" atau "Tambah Baris" untuk memasukkan
                                                    item
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                        </div>

                        <!-- Right Panel: Summary -->
                        <div class="col-lg-3 bg-white border-start h-100 d-flex flex-column shadow-sm z-1">
                            <div class="p-0 flex-grow-1 overflow-auto">
                                <div class="p-4 border-bottom bg-light mb-4">
                                    <h6 class="fw-bold text-dark mb-0">Ringkasan Return</h6>
                                </div>
                                <div class="px-4 pb-4">
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted small">Subtotal Item</span>
                                            <span class="fw-bold text-dark" id="rn_summary_subtotal">Rp 0</span>
                                        </div>
                                        <div class="mb-3">
                                            <label class="text-muted small d-block mb-1">Diskon Tambahan</label>
                                            <div class="input-group input-group-sm">
                                                <select id="rn_diskon_tambahan_tipe"
                                                    class="form-select border-end-0 text-dark"
                                                    style="max-width: 85px;" onchange="rnCalculateTotal()">
                                                    <option value="Fixed">Rp</option>
                                                    <option value="Percentage">%</option>
                                                </select>
                                                <input type="text" id="rn_diskon_tambahan"
                                                    class="form-control text-end rupiah-input-rn" value="0"
                                                    oninput="rnCalculateTotal()">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="text-muted small d-block mb-1">Biaya Lain-lain</label>
                                            <input type="text" id="rn_biaya_lain"
                                                class="form-control form-control-sm text-end rupiah-input-rn"
                                                value="0" oninput="rnCalculateTotal()">
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-danger small p-3 rounded-3 m-3">
                                    <i class="fa fa-info-circle me-1"></i>
                                    Total return ini akan mengurangi piutang pelanggan dan mengembalikan stok barang.
                                </div>
                            </div>
                            <div
                                class="p-3 rounded bg-danger bg-opacity-10 border border-danger border-opacity-25 m-3">
                                <div class="text-center">
                                    <div class="small text-danger fw-bold text-uppercase mb-1">Total Return</div>
                                    <h3 class="fw-bold text-danger mb-0" id="rn_summary_grand_total">Rp 0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<template id="rnProductRowTemplate">
    <tr class="border-bottom border-light rn-product-row">
        <td class="ps-4 py-3">
            <input type="hidden" class="rn-prod-id">
            <input type="hidden" class="rn-original-item-id">
            <div class="mb-1">
                <select class="rn-prod-select form-select form-select-sm"></select>
            </div>
            <input type="text" class="form-control form-control-sm text-muted small rn-prod-desc"
                placeholder="Deskripsi (Opsional)">
        </td>
        <td class="py-3">
            <input type="text" class="form-control form-control-sm text-center rn-prod-qty" value="1"
                oninput="rnValidateQty(this); rnCalculateTotal()">
        </td>
        <td class="py-3 text-center">
            <span class="badge bg-light text-secondary border rn-prod-unit">-</span>
        </td>
        <td class="py-3">
            <input type="text" class="form-control form-control-sm text-end rn-prod-price rupiah-input-rn"
                value="0">
        </td>
        <td class="py-3">
            <select class="form-select form-select-sm rn-prod-disc-tipe" onchange="rnCalculateTotal()">
                <option value="Fixed">Rp</option>
                <option value="Percentage">%</option>
            </select>
        </td>
        <td class="py-3">
            <input type="text" class="form-control form-control-sm text-end rn-prod-disc rupiah-input-rn"
                value="0">
        </td>
        <td class="py-3 text-end fw-bold text-dark rn-prod-subtotal">Rp 0</td>
        <td class="py-3 text-end pe-4">
            <button type="button" class="btn btn-link text-danger p-0 rn-btn-remove" style="text-decoration: none;">
                <i class="fa fa-times"></i>
            </button>
        </td>
    </tr>
</template>

<script>
    let rnOriginalItems = [];
    let rnFlatpickrInstance = null;

    // ─── Open Modal ───────────────────────────────────────────────────────────
    async function openReturnNotaModal(invoiceId, nomorInvoice) {
        document.getElementById('rn_original_invoice_id').value = invoiceId;
        document.getElementById('rn_original_nomor_invoice').value = nomorInvoice;
        document.getElementById('rn_display_original').textContent = nomorInvoice;

        // Auto-generate return number
        document.getElementById('rn_nomor_invoice').value = 'Memuat...';
        try {
            const res = await fetch('{{ route('invoice-api.next-return-number') }}');
            const result = await res.json();
            if (result.success) {
                document.getElementById('rn_nomor_invoice').value = result.data;
            } else {
                // Fallback
                const year = new Date().getFullYear();
                document.getElementById('rn_nomor_invoice').value = `RTR/${year}/0001`;
            }
        } catch (e) {
            console.error('Gagal fetch nomor return:', e);
            const year = new Date().getFullYear();
            document.getElementById('rn_nomor_invoice').value = `RTR/${year}/0001`;
        }

        // Reset form
        document.getElementById('rnItemBody').innerHTML = '';
        document.getElementById('rnEmptyRow').style.display = '';
        document.getElementById('rn_keterangan').value = '';
        document.getElementById('rn_diskon_tambahan').value = '0';
        document.getElementById('rn_diskon_tambahan_tipe').value = 'Fixed';
        document.getElementById('rn_biaya_lain').value = '0';
        rnCalculateTotal();

        // Init flatpickr for date
        if (rnFlatpickrInstance) rnFlatpickrInstance.destroy();
        rnFlatpickrInstance = flatpickr('#rn_tgl_invoice', {
            locale: 'id',
            dateFormat: 'Y-m-d',
            defaultDate: new Date(),
            allowInput: true,
        });

        // Load original invoice items for "copy" feature
        rnLoadOriginalItems(invoiceId);

        // Ensure masterProduk is loaded
        if (!window.masterProduk || window.masterProduk.length === 0) {
            initializeMasterData();
        }

        const modal = new bootstrap.Modal(document.getElementById('returnNotaModal'));
        modal.show();
    }

    // ─── Load original invoice items (for copy feature) ──────────────────────
    async function rnLoadOriginalItems(invoiceId) {
        try {
            const res = await fetch(`${window.API_URL}/${invoiceId}`);
            const result = await res.json();
            if (result.success) {
                rnOriginalItems = result.data.items || [];
            }
        } catch (e) {
            console.error('Gagal memuat item nota asal:', e);
        }
    }

    // ─── Copy items from original invoice ────────────────────────────────────
    function rnLoadItemsFromOriginal() {
        if (!rnOriginalItems.length) {
            alert('Item nota asal belum dimuat. Tunggu sebentar lalu coba lagi.');
            return;
        }

        document.getElementById('rnItemBody').innerHTML = '';
        document.getElementById('rnEmptyRow').style.display = 'none';

        rnOriginalItems.forEach(item => {
            const prodName = item.product?.nama_produk || item.nama_produk_manual || '-';
            const prodId = item.produk_id || '';
            const unit = item.product?.satuan || '-';

            rnAddRow({
                prodId,
                prodName,
                unit,
                qty: item.qty,
                original_qty: item.qty,
                original_item_id: item.id,
                harga: item.harga_satuan,
                discTipe: item.diskon_tipe || 'Fixed',
                disc: item.diskon_nilai || 0,
                desc: item.deskripsi_produk || '',
            });
        });

        rnCalculateTotal();
    }

    // ─── Add a new empty row ──────────────────────────────────────────────────
    function rnAddNewRow() {
        document.getElementById('rnEmptyRow').style.display = 'none';
        rnAddRow({});
    }

    function rnAddRow(data = {}) {
        const template = document.getElementById('rnProductRowTemplate');
        const clone = template.content.cloneNode(true);
        const row = clone.querySelector('tr');

        // Populate product select
        const select = row.querySelector('.rn-prod-select');
        const masterProduk = window.masterProduk || [];

        // Add blank option
        const blankOpt = document.createElement('option');
        blankOpt.value = '';
        blankOpt.textContent = 'Pilih / Ketik Produk...';
        select.appendChild(blankOpt);

        const populateSelect = (products) => {
            products.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.id;
                opt.textContent = p.nama_produk + (p.kode_produk ? ` (${p.kode_produk})` : '');
                opt.dataset.satuan = p.satuan || '-';
                opt.dataset.harga = p.harga_jual || 0;
                if (data.prodId && p.id == data.prodId) opt.selected = true;
                select.appendChild(opt);
            });
        };

        if (masterProduk.length > 0) {
            populateSelect(masterProduk);
        } else {
            // Fetch if not loaded
            fetch('/api/product-api?per_page=1000')
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        window.masterProduk = res.data.data || res.data;
                        populateSelect(window.masterProduk);
                        if (row.tomselect) {
                            row.tomselect.clearOptions();
                            row.tomselect.addOptions(window.masterProduk.map(p => ({
                                value: p.id,
                                text: p.nama_produk + (p.kode_produk ? ` (${p.kode_produk})` : ''),
                                harga: p.harga_jual || 0,
                                satuan: p.satuan || '-'
                            })));
                            if (data.prodId) row.tomselect.setValue(data.prodId);
                        }
                    }
                });
        }

        // If product not in master (manual), add it
        if (data.prodName && data.prodId === '') {
            const manualOpt = document.createElement('option');
            manualOpt.value = '';
            manualOpt.textContent = data.prodName;
            manualOpt.selected = true;
            select.insertBefore(manualOpt, select.children[1]);
        }

        // Set values
        console.log('rnAddRow data:', data);
        if (data.unit) row.querySelector('.rn-prod-unit').textContent = data.unit;

        const qInput = row.querySelector('.rn-prod-qty');
        if (qInput && data.qty !== undefined && data.qty !== null) {
            qInput.value = parseInt(data.qty) || 0;
        }
        if (qInput && data.original_qty !== undefined) {
            qInput.dataset.original = data.original_qty;
            qInput.title = `Jumlah Asli: ${data.original_qty}`;
        }

        if (data.original_item_id) {
            row.querySelector('.rn-original-item-id').value = data.original_item_id;
        }

        if (data.harga !== undefined && data.harga !== null) {
            row.querySelector('.rn-prod-price').value = formatRupiahInput(data.harga);
        }
        if (data.discTipe) row.querySelector('.rn-prod-disc-tipe').value = data.discTipe;
        if (data.disc !== undefined && data.disc !== null) {
            row.querySelector('.rn-prod-disc').value = formatRupiahInput(data.disc);
        }
        if (data.desc) row.querySelector('.rn-prod-desc').value = data.desc;
        if (data.prodId !== undefined && data.prodId !== null) {
            row.querySelector('.rn-prod-id').value = data.prodId;
        }

        // Init TomSelect
        if (typeof TomSelect !== 'undefined') {
            const ts = new TomSelect(select, {
                create: true,
                maxOptions: 200,
                onChange(val) {
                    const opt = select.querySelector(`option[value="${val}"]`);
                    if (opt) {
                        row.querySelector('.rn-prod-unit').textContent = opt.dataset.satuan || '-';
                        const priceInput = row.querySelector('.rn-prod-price');
                        if (opt.dataset.harga && parseFloat(opt.dataset.harga) > 0) {
                            priceInput.value = formatRupiahInput(opt.dataset.harga);
                        }
                        row.querySelector('.rn-prod-id').value = val;
                    } else if (val) {
                        row.querySelector('.rn-prod-id').value = '';
                    }
                    rnCalculateTotal();
                }
            });
            row.tomselect = ts;
        }

        // Remove button
        row.querySelector('.rn-btn-remove').addEventListener('click', function() {
            row.remove();
            if (!document.getElementById('rnItemBody').children.length) {
                document.getElementById('rnEmptyRow').style.display = '';
            }
            rnCalculateTotal();
        });

        // Rupiah formatting
        row.querySelectorAll('.rupiah-input-rn').forEach(input => {
            input.addEventListener('input', function() {
                const raw = this.value.replace(/\D/g, '');
                this.value = raw ? parseInt(raw).toLocaleString('id-ID') : '0';
                rnCalculateTotal();
            });
        });

        // Initialize rupiah for inputs not in row
        document.querySelectorAll('.rupiah-input-rn').forEach(input => {
            if (!input.dataset.listenerAdded) {
                input.addEventListener('input', function() {
                    const raw = this.value.replace(/\D/g, '');
                    this.value = raw ? parseInt(raw).toLocaleString('id-ID') : '0';
                    rnCalculateTotal();
                });
                input.dataset.listenerAdded = 'true';
            }
        });

        document.getElementById('rnItemBody').appendChild(clone);
        rnCalculateTotal();
    }

    // ─── Calculate totals ─────────────────────────────────────────────────────
    function rnCalculateTotal() {
        let subtotal = 0;
        document.querySelectorAll('#rnItemBody .rn-product-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.rn-prod-qty').value) || 0;
            const harga = parseRupiahInput(row.querySelector('.rn-prod-price').value);
            const discTipe = row.querySelector('.rn-prod-disc-tipe').value;
            const disc = parseRupiahInput(row.querySelector('.rn-prod-disc').value);

            let itemTotal = qty * harga;
            if (discTipe === 'Percentage') {
                itemTotal -= itemTotal * (disc / 100);
            } else {
                itemTotal -= disc;
            }
            if (itemTotal < 0) itemTotal = 0;

            row.querySelector('.rn-prod-subtotal').textContent = formatIDR(itemTotal);
            subtotal += itemTotal;
        });

        const discTipe = document.getElementById('rn_diskon_tambahan_tipe').value;
        const discVal = parseRupiahInput(document.getElementById('rn_diskon_tambahan').value);
        const biayaLain = parseRupiahInput(document.getElementById('rn_biaya_lain').value);

        let discAmount = discTipe === 'Percentage' ? (subtotal * discVal / 100) : discVal;
        let grand = subtotal - discAmount + biayaLain;
        if (grand < 0) grand = 0;

        document.getElementById('rn_summary_subtotal').textContent = formatIDR(subtotal);
        document.getElementById('rn_summary_grand_total').textContent = formatIDR(grand);
    }

    // ─── Save Return Nota ─────────────────────────────────────────────────────
    async function saveReturnNota() {
        const originalId = document.getElementById('rn_original_invoice_id').value;
        const nomorReturn = document.getElementById('rn_nomor_invoice').value.trim();
        const tglReturn = document.getElementById('rn_tgl_invoice').value;
        const keterangan = document.getElementById('rn_keterangan').value.trim();

        if (!nomorReturn) {
            alert('Nomor return wajib diisi.');
            return;
        }
        if (!tglReturn) {
            alert('Tanggal return wajib diisi.');
            return;
        }

        const rows = document.querySelectorAll('#rnItemBody .rn-product-row');
        if (!rows.length) {
            alert('Minimal satu item harus diisi.');
            return;
        }

        const items = [];
        let subtotal = 0;
        let valid = true;

        rows.forEach(row => {
            const prodId = row.querySelector('.rn-prod-id').value || null;
            const prodDesc = row.querySelector('.rn-prod-desc').value.trim();
            const qty = parseFloat(row.querySelector('.rn-prod-qty').value) || 0;
            const harga = parseRupiahInput(row.querySelector('.rn-prod-price').value);
            const discTipe = row.querySelector('.rn-prod-disc-tipe').value;
            const disc = parseRupiahInput(row.querySelector('.rn-prod-disc').value);

            if (qty <= 0) {
                valid = false;
                return;
            }

            let itemTotal = qty * harga;
            if (discTipe === 'Percentage') {
                itemTotal -= itemTotal * (disc / 100);
            } else {
                itemTotal -= disc;
            }
            if (itemTotal < 0) itemTotal = 0;
            subtotal += itemTotal;

            // Get product name from select
            const selectEl = row.querySelector('.rn-prod-select');
            const selectedOpt = selectEl ? selectEl.options[selectEl.selectedIndex] : null;
            const prodName = selectedOpt ? selectedOpt.textContent.split(' (')[0] : '-';

            const originalItemId = row.querySelector('.rn-original-item-id').value;

            items.push({
                produk_id: parseInt(prodId) || null,
                original_item_id: originalItemId ? parseInt(originalItemId) : null,
                nama_produk_manual: !prodId ? prodName : null,
                deskripsi_produk: prodDesc || null,
                qty: parseFloat(qty) || 0,
                harga_satuan: harga,
                diskon_tipe: discTipe,
                diskon_nilai: disc,
                total_harga_item: itemTotal,
                taxes: [],
            });
        });

        if (!valid) {
            alert('Qty semua item harus lebih dari 0.');
            return;
        }

        const discTipe = document.getElementById('rn_diskon_tambahan_tipe').value;
        const discVal = parseRupiahInput(document.getElementById('rn_diskon_tambahan').value);
        const biayaLain = parseRupiahInput(document.getElementById('rn_biaya_lain').value);
        let discAmount = discTipe === 'Percentage' ? (subtotal * discVal / 100) : discVal;
        const grandTotal = Math.max(0, subtotal - discAmount + biayaLain);

        const payload = {
            invoice: {
                return_of_invoice_id: parseInt(originalId),
                nomor_invoice: nomorReturn,
                tgl_invoice: tglReturn,
                keterangan: keterangan || null,
                subtotal,
                total_diskon_item: 0,
                diskon_tambahan_nilai: discVal,
                diskon_tambahan_tipe: discTipe,
                other_fee: biayaLain,
                total_akhir: grandTotal,
            },
            items,
        };

        // UI Feedback
        const btnSave = document.getElementById('rnBtnSave');
        const originalText = btnSave.innerHTML;
        btnSave.disabled = true;
        btnSave.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Menyimpan...';

        try {
            const response = await fetch(`${window.API_URL.replace('invoice-api', 'invoice-return-api')}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify(payload),
            });

            const result = await response.json();

            // Re-enable button
            btnSave.disabled = false;
            btnSave.innerHTML = originalText;

            if (result.success) {
                const returnId = result.data.id;

                // Close current modal first
                const currentModalEl = document.getElementById('returnNotaModal');
                const currentModal = bootstrap.Modal.getOrCreateInstance(currentModalEl);
                currentModal.hide();

                // Ask to print
                setTimeout(() => {
                    if (confirm('Nota return berhasil dibuat! Cetak sekarang?')) {
                        if (typeof openReturnPrintPreview === 'function') {
                            openReturnPrintPreview(returnId);
                        } else {
                            window.open(`{{ url('sales/print-return') }}/${returnId}`, '_blank');
                        }
                    }

                    // Reload invoice detail anyway
                    if (typeof loadInvoiceDetail === 'function') loadInvoiceDetail();
                }, 500); // Wait for modal hide animation
            } else {
                alert('Gagal membuat nota return: ' + (result.message || 'Error tidak diketahui'));
            }
        } catch (e) {
            console.error('Save Error:', e);
            alert('Gagal menyimpan nota return.\n' + e.message +
                '\n\nSilakan periksa koneksi server atau restart "composer run dev".');
        } finally {
            btnSave.disabled = false;
            btnSave.innerHTML = originalText;
        }
    }

    function rnValidateQty(input) {
        let val = parseInt(input.value) || 0;
        if (val < 0) input.value = 0;
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────
    function parseRupiahInput(val) {
        if (!val) return 0;
        return parseFloat(String(val).replace(/\./g, '').replace(',', '.')) || 0;
    }

    function formatRupiahInput(val) {
        const num = parseFloat(val) || 0;
        return num.toLocaleString('id-ID');
    }
</script>
