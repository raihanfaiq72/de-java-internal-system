<div class="modal fade" id="invoiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content bg-light">
            <div class="modal-header bg-white shadow-sm sticky-top py-3 px-4">
                <div class="d-flex align-items-center">
                    <div class="bg-primary text-white p-2 rounded-3 me-3">
                        <i class="fa fa-file-invoice fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-dark fw-bold mb-0" id="modalTitle">Buat Invoice Penjualan</h5>
                        <small class="text-muted">Lengkapi rincian produk dan informasi transaksi.</small>
                    </div>
                </div>
                <div class="ms-auto d-flex gap-2">
                    <button type="button" class="btn btn-outline-danger border-0 fw-bold" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i> Tutup
                    </button>
                    <button type="button" class="btn btn-primary px-4 fw-bold shadow-sm" onclick="saveFullInvoice()">
                        <i class="fa fa-check-circle me-1"></i> Simpan Invoice
                    </button>
                </div>
            </div>

            <div class="modal-body p-4">
                <form id="invoiceForm">
                    <input type="hidden" id="form_mode" value="create">
                    <input type="hidden" id="edit_invoice_id" value="">

                    <div class="row g-4 mb-4">
                        <div class="col-lg-8">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-4">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-secondary">Pilih Mitra / Pelanggan *</label>
                                            <select id="modal_mitra_id" class="form-select border-0 bg-light py-2" onchange="renderMitraDetail()">
                                                <option value="">-- Cari Pelanggan --</option>
                                            </select>
                                            <div id="mitra_detail_display" class="mt-3 p-3 rounded-3 border-start border-primary border-4 bg-light small text-muted" style="min-height: 80px;">
                                                Detail pelanggan akan muncul otomatis.
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold text-secondary">Nomor Invoice *</label>
                                                    <input type="text" id="modal_nomor_invoice" class="form-control border-0 bg-light fw-bold" readonly>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label small fw-bold text-secondary">Tanggal Invoice *</label>
                                                    <input type="date" id="modal_tgl_invoice" class="form-control border-0 bg-light">
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label small fw-bold text-secondary">Jatuh Tempo</label>
                                                    <input type="date" id="modal_tgl_jatuh_tempo" class="form-control border-0 bg-light">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card border-0 shadow-sm h-100 bg-white">
                                <div class="card-body p-4">
                                    <label class="form-label small fw-bold text-secondary">Referensi / No. PO</label>
                                    <input type="text" id="modal_ref_no" class="form-control border-0 bg-light mb-3" placeholder="REF-2026-001">
                                    <label class="form-label small fw-bold text-secondary">Salesperson</label>
                                    <select id="modal_sales_id" class="form-select border-0 bg-light">
                                        <option value="">-- Pilih Sales --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="mainItemTable">
                                <thead class="bg-dark text-white text-uppercase font-10">
                                    <tr>
                                        <th width="35%" class="ps-4 py-3 text-white">Produk & Deskripsi *</th>
                                        <th width="10%" class="text-center text-white">Kuantitas</th>
                                        <th width="15%" class="text-center text-white">Harga Satuan</th>
                                        <th width="10%" class="text-center text-white">Diskon (Rp)</th>
                                        <th width="15%" class="text-center text-white">Pajak</th>
                                        <th width="15%" class="text-end pe-4 text-white">Subtotal</th>
                                        <th width="5%"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemBodyList"></tbody>
                            </table>
                        </div>
                        <div class="p-3 bg-white border-top">
                            <button type="button" class="btn btn-outline-primary btn-sm fw-bold rounded-pill px-3" onclick="addNewProductRow()">
                                <i class="fa fa-plus-circle me-1"></i> Tambah Item Baru
                            </button>
                        </div>
                    </div>

                    <div class="row g-4 pb-5">
                        <div class="col-md-7">
                            <div class="card border-0 shadow-sm p-4 h-100 bg-white">
                                <label class="form-label small fw-bold text-secondary">Keterangan Internal</label>
                                <textarea id="modal_keterangan" class="form-control border-0 bg-light mb-3" rows="3" placeholder="Catatan..."></textarea>
                                <label class="form-label small fw-bold text-secondary">Syarat & Ketentuan</label>
                                <textarea id="modal_syarat" class="form-control border-0 bg-light" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="card border-0 shadow-sm bg-white p-4">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted">Subtotal Produk</span>
                                    <span id="summary_subtotal" class="fw-bold">Rp 0</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 align-items-center">
                                    <span class="text-muted">Potongan Tambahan</span>
                                    <div class="input-group input-group-sm" style="width: 180px;">
                                        <span class="input-group-text border-0 bg-light">Rp</span>
                                        <input type="number" id="modal_diskon_tambahan" class="form-control border-0 bg-light text-end fw-bold" value="0" min="0" oninput="calculateInvoiceTotal()">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted text-danger">Total Pajak</span>
                                    <span id="summary_tax" class="fw-bold text-danger">Rp 0</span>
                                </div>
                                <hr class="my-3 opacity-10">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="fw-bold text-dark mb-0">Total Tagihan</h5>
                                    <h2 id="summary_grand_total" class="fw-bold text-primary mb-0">Rp 0</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let mitraCollection = [], productCollection = [], taxCollection = [];

    // Muat data master satu kali saat startup
    async function initializeInvoiceData() {
        try {
            const [mRes, pRes, tRes] = await Promise.all([
                fetch('/api/mitra-api').then(r => r.json()),
                fetch('/api/product-api').then(r => r.json()),
                fetch('/api/tax-api').then(r => r.json())
            ]);
            mitraCollection = mRes.data.data || mRes.data;
            productCollection = pRes.data.data || pRes.data;
            taxCollection = tRes.data.data || tRes.data;

            const select = document.getElementById('modal_mitra_id');
            mitraCollection.forEach(m => select.insertAdjacentHTML('beforeend', `<option value="${m.id}">${m.nama}</option>`));
        } catch (e) { console.error("Master data fail", e); }
    }

    // Fungsi Buka Modal Reusable
    async function openInvoiceModal(id = null, event = null) {
        if (event) event.stopPropagation();
        const modalEl = document.getElementById('invoiceModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        resetModalForm();

        if (id) {
            document.getElementById('modalTitle').innerText = 'Edit Invoice Penjualan';
            document.getElementById('form_mode').value = 'edit';
            document.getElementById('edit_invoice_id').value = id;
            
            // Ambil Detail Edit via API
            try {
                const res = await fetch(`${API_URL}/${id}`);
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
                    renderMitraDetail();
                    inv.items.forEach(it => addNewProductRow(it));
                    calculateInvoiceTotal();
                }
            } catch (e) { console.error("Edit fetch fail", e); }
        } else {
            document.getElementById('modalTitle').innerText = 'Buat Invoice Penjualan';
            document.getElementById('form_mode').value = 'create';
            const ts = Date.now().toString().slice(-4);
            document.getElementById('modal_nomor_invoice').value = `INV/${new Date().getFullYear()}/${ts}`;
            addNewProductRow();
        }
        modal.show();
    }

    function addNewProductRow(existing = null) {
        const rowId = existing ? existing.id : Date.now();
        let optProd = productCollection.map(p => `<option value="${p.id}" data-price="${p.harga_jual}" ${existing && existing.produk_id == p.id ? 'selected' : ''}>${p.nama_produk}</option>`).join('');
        let optTax = taxCollection.map(t => `<option value="${t.id}" data-rate="${t.nilai_pajak}" ${existing && existing.taxes && existing.taxes[0]?.tax_id == t.id ? 'selected' : ''}>${t.nama_pajak}</option>`).join('');

        const html = `
            <tr id="item_row_${rowId}" class="align-middle">
                <td class="ps-4 py-2">
                    <select class="form-select form-select-sm mb-1 prod-select fw-bold" onchange="autoFillPrice(${rowId})">
                        <option value="">-- Cari Produk --</option>${optProd}
                    </select>
                    <input type="text" class="form-control form-control-sm border-0 bg-light prod-desc" value="${existing ? (existing.deskripsi_produk || '') : ''}" placeholder="Deskripsi">
                </td>
                <td><input type="number" class="form-control form-control-sm text-center prod-qty fw-bold" value="${existing ? Math.floor(existing.qty) : 1}" oninput="calculateInvoiceTotal()"></td>
                <td><input type="number" class="form-control form-control-sm text-end prod-price" value="${existing ? existing.harga_satuan : 0}" oninput="calculateInvoiceTotal()"></td>
                <td><input type="number" class="form-control form-control-sm text-end prod-disc" value="${existing ? existing.diskon_nilai : 0}" oninput="calculateInvoiceTotal()"></td>
                <td>
                    <select class="form-select form-select-sm prod-tax" onchange="calculateInvoiceTotal()">
                        <option value="" data-rate="0">Pajak 0%</option>${optTax}
                    </select>
                </td>
                <td class="text-end pe-4 fw-bold prod-subtotal">Rp 0</td>
                <td class="text-center"><button type="button" class="btn btn-link text-danger p-0 shadow-none" onclick="document.getElementById('item_row_${rowId}').remove();calculateInvoiceTotal()"><i class="fa fa-times-circle fa-lg"></i></button></td>
            </tr>`;
        document.getElementById('itemBodyList').insertAdjacentHTML('beforeend', html);
        if(existing) calculateInvoiceTotal();
    }

    function calculateInvoiceTotal() {
        let sub = 0, tax = 0;
        document.querySelectorAll('#itemBodyList tr').forEach(row => {
            const q = parseFloat(row.querySelector('.prod-qty').value) || 0,
                  p = parseFloat(row.querySelector('.prod-price').value) || 0,
                  d = parseFloat(row.querySelector('.prod-disc').value) || 0,
                  tr = parseFloat(row.querySelector('.prod-tax option:checked').dataset.rate) || 0;
            let lineSub = (q * p) - d;
            if(lineSub < 0) lineSub = 0;
            row.querySelector('.prod-subtotal').innerText = formatCurrency(lineSub);
            sub += lineSub;
            tax += (lineSub * (tr / 100));
        });
        const extra = parseFloat(document.getElementById('modal_diskon_tambahan').value) || 0;
        const grand = Math.max(0, sub + tax - extra);
        document.getElementById('summary_subtotal').innerText = formatCurrency(sub);
        document.getElementById('summary_tax').innerText = formatCurrency(tax);
        document.getElementById('summary_grand_total').innerText = formatCurrency(grand);
    }

    function renderMitraDetail() {
        const id = document.getElementById('modal_mitra_id').value;
        const item = mitraCollection.find(x => x.id == id);
        document.getElementById('mitra_detail_display').innerHTML = item ? `<strong>${item.nama}</strong><br>${item.alamat || '-'}<br>HP: ${item.no_hp || '-'}` : "Detail muncul otomatis.";
    }

    function resetModalForm() {
        document.getElementById('invoiceForm').reset();
        document.getElementById('itemBodyList').innerHTML = '';
        document.getElementById('summary_grand_total').innerText = 'Rp 0';
    }

    function formatCurrency(val) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val); }

    async function saveFullInvoice() {
        const mode = document.getElementById('form_mode').value;
        const id = document.getElementById('edit_invoice_id').value;
        const lines = [];
        document.querySelectorAll('#itemBodyList tr').forEach(row => {
            const txRate = parseFloat(row.querySelector('.prod-tax option:checked').dataset.rate) || 0,
                  txId = row.querySelector('.prod-tax').value,
                  lineSub = parseFloat(row.querySelector('.prod-subtotal').innerText.replace(/[^0-9]/g, ''));
            lines.push({
                produk_id: row.querySelector('.prod-select').value,
                deskripsi_produk: row.querySelector('.prod-desc').value,
                qty: row.querySelector('.prod-qty').value,
                harga_satuan: row.querySelector('.prod-price').value,
                diskon_nilai: row.querySelector('.prod-disc').value,
                total_harga_item: lineSub,
                taxes: txId ? [{ tax_id: txId, nilai_pajak_diterapkan: lineSub * (txRate / 100) }] : []
            });
        });

        const getCleanNum = (id) => parseFloat(document.getElementById(id).innerText.replace(/[^0-9]/g, '')) || 0;
        const body = {
            invoice: {
                tipe_invoice: 'Sales', nomor_invoice: document.getElementById('modal_nomor_invoice').value,
                tgl_invoice: document.getElementById('modal_tgl_invoice').value,
                tgl_jatuh_tempo: document.getElementById('modal_tgl_jatuh_tempo').value,
                mitra_id: document.getElementById('modal_mitra_id').value,
                subtotal: getCleanNum('summary_subtotal'), total_akhir: getCleanNum('summary_grand_total'),
                ref_no: document.getElementById('modal_ref_no').value,
                keterangan: document.getElementById('modal_keterangan').value,
                syarat_ketentuan: document.getElementById('modal_syarat').value,
                diskon_tambahan_nilai: parseFloat(document.getElementById('modal_diskon_tambahan').value) || 0
            },
            items: lines
        };

        const endpoint = mode === 'edit' ? `${API_URL}/${id}` : '/api/invoice-create-api';
        const method = mode === 'edit' ? 'PUT' : 'POST'; // Sesuaikan API Anda jika edit pakai PUT

        const res = await fetch(endpoint, {
            method: method, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify(body)
        });
        const out = await res.json();
        if(out.success) location.reload(); else alert("Error: " + JSON.stringify(out.errors || out.message));
    }
</script>