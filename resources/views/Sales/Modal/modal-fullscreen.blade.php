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
                    <button type="button" class="btn btn-link text-secondary text-decoration-none fw-semibold" data-bs-dismiss="modal">Batalkan</button>
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
                                            <select id="modal_mitra_id" class="form-select f-input-lg" onchange="renderMitraDetail()">
                                                <option value="">Cari dan pilih mitra...</option>
                                            </select>
                                            <div id="mitra_detail_display" class="mt-3 p-3 rounded bg-light border-start border-3 border-primary small text-secondary">
                                                <i class="fa fa-info-circle me-2 text-primary"></i>Detail alamat dan kontak akan muncul otomatis.
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="f-label">Nomor Invoice</label>
                                                    <input type="text" id="modal_nomor_invoice" class="form-control f-input fw-bold bg-light" readonly>
                                                </div>
                                                <div class="col-6">
                                                    <label class="f-label">Tanggal Terbit</label>
                                                    <input type="date" id="modal_tgl_invoice" class="form-control f-input">
                                                </div>
                                                <div class="col-6">
                                                    <label class="f-label">Jatuh Tempo</label>
                                                    <input type="date" id="modal_tgl_jatuh_tempo" class="form-control f-input">
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
                                        <input type="text" id="modal_ref_no" class="form-control f-input" placeholder="Misal: PO/2026/001">
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
                        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold text-dark text-uppercase small" style="letter-spacing: 1px;">Daftar Barang & Jasa</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0" id="mainItemTable">
                                <thead class="bg-light">
                                    <tr class="text-secondary small fw-bold">
                                        <th class="ps-4 py-3" width="30%">ITEM & DESKRIPSI</th>
                                        <th class="text-center" width="10%">QTY</th>
                                        <th class="text-end" width="15%">HARGA SATUAN</th>
                                        <th class="text-end" width="12%">DISKON (RP)</th>
                                        <th class="text-center" width="13%">PAJAK</th>
                                        <th class="text-end pe-4" width="15%">TOTAL</th>
                                        <th width="5%"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemBodyList">
                                    </tbody>
                            </table>
                        </div>
                        <div class="p-3 bg-white border-top text-center">
                            <button type="button" class="btn btn-outline-primary btn-sm fw-bold px-4 rounded-pill" onclick="addNewProductRow()">
                                <i class="fa fa-plus-circle me-1"></i> Tambah Item Baru
                            </button>
                        </div>
                    </div>

                    <div class="row g-4 pb-5">
                        <div class="col-md-7">
                            <div class="card border-0 shadow-sm p-4 h-100">
                                <div class="mb-3">
                                    <label class="f-label text-primary">Keterangan Tambahan</label>
                                    <textarea id="modal_keterangan" class="form-control border-0 bg-light f-text" rows="2" placeholder="Tulis instruksi khusus atau catatan..."></textarea>
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
                                        <input type="number" id="modal_diskon_tambahan" class="form-control bg-transparent border-secondary text-white text-end fw-bold" value="0" oninput="calculateInvoiceTotal()">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mb-3 text-warning">
                                    <span class="small fw-semibold">Akumulasi Pajak</span>
                                    <span id="summary_tax" class="f-mono">Rp 0</span>
                                </div>
                                <div class="border-top border-secondary my-3 opacity-25"></div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold mb-0 text-uppercase small" style="letter-spacing: 1px;">Total Netto Tagihan</h6>
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

<style>
    /* Utility CSS untuk Modal agar Seragam dengan Main Page */
    .f-label { font-size: 11px; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 700; color: #64748b; margin-bottom: 6px; display: block; }
    .f-input { border: 1px solid #e2e8f0; padding: 0.6rem 0.8rem; border-radius: 6px; font-size: 13px; transition: all 0.2s; }
    .f-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); outline: none; }
    .f-input-lg { padding: 0.75rem 1rem; font-size: 14px; font-weight: 600; border-radius: 8px; border: 1px solid #e2e8f0; }
    .f-text { font-size: 13px; }
    .f-mono { font-family: 'JetBrains Mono', monospace; }
    
    #mainItemTable .f-row-input { border: 1px solid transparent; background: transparent; padding: 6px; font-size: 13px; transition: all 0.2s; }
    #mainItemTable tr:hover .f-row-input { background: #fff; border-color: #e2e8f0; }
    #mainItemTable .f-row-input:focus { background: #fff; border-color: #3b82f6; outline: none; }
</style>

<script>
    // Memastikan Helper IDR menggunakan Namespace Global agar tidak redeclare
    window.financeHelpers = window.financeHelpers || {
        formatIDR: (val) => new Intl.NumberFormat('id-ID', {
            style: 'currency', currency: 'IDR', minimumFractionDigits: 0
        }).format(val)
    };

    let productCollection = [], taxCollection = [], mitraCollection = [];

    // Initialize Master Data
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

    async function openInvoiceModal(id = null, event = null) {
        if (event) event.stopPropagation();
        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('invoiceModal'));
        resetModalForm();

        if (id) {
            document.getElementById('modalTitle').innerText = 'Edit Invoice Penjualan';
            document.getElementById('form_mode').value = 'edit';
            document.getElementById('edit_invoice_id').value = id;
            
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
            document.getElementById('modalTitle').innerText = 'Buat Invoice Baru';
            document.getElementById('form_mode').value = 'create';
            document.getElementById('modal_nomor_invoice').value = `INV/${new Date().getFullYear()}/${Date.now().toString().slice(-4)}`;
            document.getElementById('modal_tgl_invoice').valueAsDate = new Date();
            addNewProductRow();
        }
        modal.show();
    }

    function addNewProductRow(existing = null) {
        const rowId = existing ? existing.id : Date.now();
        let optProd = productCollection.map(p => `<option value="${p.id}" data-price="${p.harga_jual}" ${existing && existing.produk_id == p.id ? 'selected' : ''}>${p.nama_produk}</option>`).join('');
        let optTax = taxCollection.map(t => `<option value="${t.id}" data-rate="${t.nilai_pajak}" ${existing && existing.taxes && existing.taxes[0]?.tax_id == t.id ? 'selected' : ''}>${t.nama_pajak}</option>`).join('');

        const html = `
            <tr id="row_${rowId}">
                <td class="ps-4">
                    <select class="form-select f-row-input fw-bold prod-select" onchange="autoFillPrice(${rowId})">
                        <option value="">Cari Produk...</option>${optProd}
                    </select>
                    <input type="text" class="form-control f-row-input small text-muted prod-desc" value="${existing?.deskripsi_produk || ''}" placeholder="Tambahkan deskripsi (opsional)">
                </td>
                <td><input type="number" class="form-control f-row-input text-center fw-bold prod-qty" value="${existing ? Math.floor(existing.qty) : 1}" oninput="calculateInvoiceTotal()"></td>
                <td><input type="number" class="form-control f-row-input text-end prod-price" value="${existing ? existing.harga_satuan : 0}" oninput="calculateInvoiceTotal()"></td>
                <td><input type="number" class="form-control f-row-input text-end prod-disc" value="${existing ? existing.diskon_nilai : 0}" oninput="calculateInvoiceTotal()"></td>
                <td>
                    <select class="form-select f-row-input text-center prod-tax" onchange="calculateInvoiceTotal()">
                        <option value="" data-rate="0">N/A</option>${optTax}
                    </select>
                </td>
                <td class="text-end pe-4 fw-bold f-mono prod-subtotal">Rp 0</td>
                <td class="text-center"><button type="button" class="btn btn-link text-danger p-0" onclick="removeRow(${rowId})"><i class="fa fa-times-circle"></i></button></td>
            </tr>`;
        document.getElementById('itemBodyList').insertAdjacentHTML('beforeend', html);
        if(existing) calculateInvoiceTotal();
    }

    function removeRow(id) {
        document.getElementById(`row_${id}`).remove();
        calculateInvoiceTotal();
    }

    function autoFillPrice(id) {
        const row = document.getElementById(`row_${id}`);
        const sel = row.querySelector('.prod-select');
        const price = sel.options[sel.selectedIndex].dataset.price || 0;
        row.querySelector('.prod-price').value = price;
        calculateInvoiceTotal();
    }

    function calculateInvoiceTotal() {
        let sub = 0, taxAccum = 0;
        document.querySelectorAll('#itemBodyList tr').forEach(row => {
            const q = parseFloat(row.querySelector('.prod-qty').value) || 0,
                  p = parseFloat(row.querySelector('.prod-price').value) || 0,
                  d = parseFloat(row.querySelector('.prod-disc').value) || 0,
                  tr = parseFloat(row.querySelector('.prod-tax option:checked').dataset.rate) || 0;
            
            let lineSub = (q * p) - d;
            if(lineSub < 0) lineSub = 0;
            
            row.querySelector('.prod-subtotal').innerText = window.financeHelpers.formatIDR(lineSub);
            sub += lineSub;
            taxAccum += (lineSub * (tr / 100));
        });

        const extra = parseFloat(document.getElementById('modal_diskon_tambahan').value) || 0;
        const grand = Math.max(0, sub + taxAccum - extra);

        document.getElementById('summary_subtotal').innerText = window.financeHelpers.formatIDR(sub);
        document.getElementById('summary_tax').innerText = window.financeHelpers.formatIDR(taxAccum);
        document.getElementById('summary_grand_total').innerText = window.financeHelpers.formatIDR(grand);
    }

    function renderMitraDetail() {
        const id = document.getElementById('modal_mitra_id').value;
        const item = mitraCollection.find(x => x.id == id);
        document.getElementById('mitra_detail_display').innerHTML = item ? 
            `<strong>${item.nama}</strong><br>${item.alamat || 'Alamat tidak tersedia'}<br><i class="fa fa-phone me-1 small"></i> ${item.no_hp || '-'}` : 
            "Detail muncul otomatis.";
    }

    function resetModalForm() {
        document.getElementById('invoiceForm').reset();
        document.getElementById('itemBodyList').innerHTML = '';
        document.getElementById('mitra_detail_display').innerHTML = "Detail muncul otomatis.";
        calculateInvoiceTotal();
    }

    // Load Data on Start
    document.addEventListener('DOMContentLoaded', initializeInvoiceData);
</script>