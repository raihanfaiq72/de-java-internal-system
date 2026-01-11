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
        }).format(val),
        unformatIDR: (str) => {
            if(!str) return 0;
            return parseFloat(str.replace(/[^0-9,-]+/g,"").replace(",","."));
        }
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
            select.innerHTML = '<option value="">Cari dan pilih mitra...</option>';
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
                // Use window.financeApp.API_URL if available, else default
                const baseUrl = (window.financeApp && window.financeApp.API_URL) ? window.financeApp.API_URL : '/api/invoice-api';
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
                    renderMitraDetail();
                    
                    // Populate Items
                    if(inv.items && inv.items.length > 0) {
                        inv.items.forEach(it => addNewProductRow(it));
                    } else {
                        addNewProductRow();
                    }
                    calculateInvoiceTotal();
                }
            } catch (e) { console.error("Edit fetch fail", e); alert('Gagal mengambil data invoice.'); }
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
        const rowId = existing ? existing.id : Date.now() + Math.floor(Math.random() * 1000);
        let optProd = productCollection.map(p => `<option value="${p.id}" data-price="${p.harga_jual}" ${existing && existing.produk_id == p.id ? 'selected' : ''}>${p.nama_produk}</option>`).join('');
        
        // Handle Taxes
        let currentTaxId = existing && existing.taxes && existing.taxes.length > 0 ? existing.taxes[0].tax_id : '';
        let optTax = taxCollection.map(t => `<option value="${t.id}" data-rate="${t.nilai_pajak}" ${currentTaxId == t.id ? 'selected' : ''}>${t.nama_pajak}</option>`).join('');

        const html = `
            <tr id="row_${rowId}">
                <td class="ps-4">
                    <select class="form-select f-row-input fw-bold prod-select" onchange="autoFillPrice(${rowId})">
                        <option value="">Cari Produk...</option>${optProd}
                    </select>
                    <input type="text" class="form-control f-row-input small text-muted prod-desc" value="${existing?.deskripsi_produk || ''}" placeholder="Tambahkan deskripsi (opsional)">
                </td>
                <td><input type="number" class="form-control f-row-input text-center fw-bold prod-qty" value="${existing ? parseFloat(existing.qty) : 1}" oninput="calculateInvoiceTotal()"></td>
                <td><input type="number" class="form-control f-row-input text-end prod-price" value="${existing ? parseFloat(existing.harga_satuan) : 0}" oninput="calculateInvoiceTotal()"></td>
                <td><input type="number" class="form-control f-row-input text-end prod-disc" value="${existing ? parseFloat(existing.diskon_nilai) : 0}" oninput="calculateInvoiceTotal()"></td>
                <td>
                    <select class="form-select f-row-input text-center prod-tax" onchange="calculateInvoiceTotal()">
                        <option value="" data-rate="0">N/A</option>${optTax}
                    </select>
                </td>
                <td class="text-end pe-4 fw-bold f-mono prod-subtotal">Rp 0</td>
                <td class="text-center"><button type="button" class="btn btn-link text-danger p-0" onclick="removeRow('${rowId}')"><i class="fa fa-times-circle"></i></button></td>
            </tr>`;
        document.getElementById('itemBodyList').insertAdjacentHTML('beforeend', html);
        
        // Calculate initial total for this row
        // Give slight delay or immediate call
        if(existing || true) calculateInvoiceTotal(); 
    }

    function removeRow(id) {
        const row = document.getElementById(`row_${id}`);
        if(row) row.remove();
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
                  taxSel = row.querySelector('.prod-tax'),
                  tr = parseFloat(taxSel.options[taxSel.selectedIndex].dataset.rate) || 0;
            
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
            `<i class="fa fa-info-circle me-2 text-primary"></i>Detail alamat dan kontak akan muncul otomatis.`;
    }

    function resetModalForm() {
        document.getElementById('invoiceForm').reset();
        document.getElementById('itemBodyList').innerHTML = '';
        document.getElementById('mitra_detail_display').innerHTML = `<i class="fa fa-info-circle me-2 text-primary"></i>Detail muncul otomatis.`;
        document.getElementById('summary_subtotal').innerText = 'Rp 0';
        document.getElementById('summary_tax').innerText = 'Rp 0';
        document.getElementById('summary_grand_total').innerText = 'Rp 0';
    }

    async function saveFullInvoice() {
        const mode = document.getElementById('form_mode').value;
        const id = document.getElementById('edit_invoice_id').value;
        
        // Prepare Payload
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
                // Basic Defaults
                status_dok: 'Approved', 
                status_pembayaran: 'Unpaid',
                biaya_kirim: 0,
                uang_muka: 0
            },
            items: []
        };

        // Validate basic fields
        if(!payload.invoice.mitra_id) { alert('Harap pilih Mitra!'); return; }
        if(!payload.invoice.tgl_invoice) { alert('Harap isi Tanggal Invoice!'); return; }

        let totalValid = 0;
        document.querySelectorAll('#itemBodyList tr').forEach(row => {
            const sel = row.querySelector('.prod-select');
            const taxSel = row.querySelector('.prod-tax');
            const taxRate = parseFloat(taxSel.options[taxSel.selectedIndex].dataset.rate) || 0;
            const qty = parseFloat(row.querySelector('.prod-qty').value) || 0;
            const price = parseFloat(row.querySelector('.prod-price').value) || 0;
            
            if(sel.value && qty > 0) {
                payload.items.push({
                    produk_id: sel.value,
                    qty: qty,
                    harga_satuan: price,
                    diskon_nilai: parseFloat(row.querySelector('.prod-disc').value) || 0,
                    taxes: taxSel.value ? [{ tax_id: taxSel.value, nilai_pajak_diterapkan: taxRate }] : []
                });
                totalValid++;
            }
        });

        if(totalValid === 0) { alert('Harap tambahkan minimal satu item produk valid!'); return; }

        const url = mode === 'edit' ? `/api/invoice-api/${id}` : '/api/invoice-create-api';
        const method = mode === 'edit' ? 'PUT' : 'POST';

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(payload)
            });
            
            const result = await response.json();
            
            if(result.success) {
                alert('Dokumen berhasil disimpan!');
                bootstrap.Modal.getInstance(document.getElementById('invoiceModal')).hide();
                if(typeof loadInvoiceData === 'function') loadInvoiceData(); // Reload table
            } else {
                alert('Gagal menyimpan: ' + (result.message || JSON.stringify(result.errors)));
            }
        } catch(error) {
            console.error('Save error', error);
            alert('Terjadi kesalahan sistem saat menyimpan.');
        }
    }

    // Load Data on Start
    document.addEventListener('DOMContentLoaded', initializeInvoiceData);
</script>