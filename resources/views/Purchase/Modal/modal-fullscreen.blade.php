<div class="modal fade" id="purchaseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content bg-light">
            <div class="modal-header bg-white shadow-sm sticky-top py-3 px-4">
                <div class="d-flex align-items-center">
                    <div class="bg-dark text-white p-2 rounded-3 me-3"><i class="fa fa-shopping-cart fa-lg"></i></div>
                    <div>
                        <h5 class="modal-title text-dark fw-bold mb-0" id="modalTitle">Buat Faktur Pembelian</h5>
                        <small class="text-muted">Mencatat tagihan masuk dari supplier.</small>
                    </div>
                </div>
                <div class="ms-auto d-flex gap-2">
                    <button type="button" class="btn btn-outline-danger border-0 fw-bold" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary px-4 fw-bold shadow-sm" onclick="saveFullPurchase()">Simpan Faktur</button>
                </div>
            </div>

            <div class="modal-body p-4">
                <form id="purchaseForm">
                    <input type="hidden" id="form_mode" value="create">
                    <input type="hidden" id="edit_invoice_id" value="">

                    <div class="row g-4 mb-4">
                        <div class="col-lg-8">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-4">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">Pilih Supplier *</label>
                                            <select id="modal_mitra_id" class="form-select border-0 bg-light py-2" onchange="renderMitraDetail()"></select>
                                            <div id="mitra_detail_display" class="mt-3 p-3 rounded border-start border-dark border-4 bg-light small text-muted">Detail supplier muncul di sini.</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row g-3">
                                                <div class="col-12"><label class="form-label small fw-bold">Nomor Faktur *</label><input type="text" id="modal_nomor_invoice" class="form-control border-0 bg-light fw-bold" readonly></div>
                                                <div class="col-6"><label class="form-label small fw-bold">Tanggal Faktur *</label><input type="date" id="modal_tgl_invoice" class="form-control border-0 bg-light"></div>
                                                <div class="col-6"><label class="form-label small fw-bold">Jatuh Tempo</label><input type="date" id="modal_tgl_jatuh_tempo" class="form-control border-0 bg-light"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card border-0 shadow-sm h-100 p-4">
                                <label class="form-label small fw-bold">Referensi / No. Surat Jalan</label>
                                <input type="text" id="modal_ref_no" class="form-control border-0 bg-light mb-3">
                                <label class="form-label small fw-bold">Gudang Penerima</label>
                                <select id="modal_warehouse_id" class="form-select border-0 bg-light"><option value="">-- Pilih Gudang --</option></select>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-4">
                        <table class="table align-middle mb-0" id="mainItemTable">
                            <thead class="bg-dark text-white font-10">
                                <tr>
                                    <th width="35%" class="ps-4 py-3 text-white">Produk</th>
                                    <th width="10%" class="text-center text-white">Qty</th>
                                    <th width="15%" class="text-center text-white">Harga Beli</th>
                                    <th width="10%" class="text-center text-white">Diskon</th>
                                    <th width="15%" class="text-center text-white">Pajak</th>
                                    <th width="15%" class="text-end pe-4 text-white">Subtotal</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody id="itemBodyList"></tbody>
                        </table>
                        <div class="p-3 bg-white"><button type="button" class="btn btn-outline-primary btn-sm rounded-pill" onclick="addNewPurchaseRow()">+ Tambah Item</button></div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-7">
                            <textarea id="modal_keterangan" class="form-control border-0 bg-light mb-3" rows="3" placeholder="Catatan internal..."></textarea>
                        </div>
                        <div class="col-md-5">
                            <div class="card border-0 shadow-sm p-4">
                                <div class="d-flex justify-content-between mb-2"><span>Subtotal</span><span id="summary_subtotal">Rp 0</span></div>
                                <div class="d-flex justify-content-between mb-2 text-danger"><span>Total Pajak</span><span id="summary_tax">Rp 0</span></div>
                                <hr>
                                <div class="d-flex justify-content-between align-items-center"><h5 class="fw-bold mb-0">Total Hutang</h5><h2 id="summary_grand_total" class="fw-bold text-primary mb-0">Rp 0</h2></div>
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

    async function initializePurchaseData() {
        const [mRes, pRes, tRes] = await Promise.all([
            fetch('/api/mitra-api?kategori=Supplier').then(r => r.json()),
            fetch('/api/product-api').then(r => r.json()),
            fetch('/api/tax-api').then(r => r.json())
        ]);
        mitraCollection = mRes.data.data || mRes.data;
        productCollection = pRes.data.data || pRes.data;
        taxCollection = tRes.data.data || tRes.data;

        const select = document.getElementById('modal_mitra_id');
        select.innerHTML = '<option value="">-- Pilih Supplier --</option>';
        mitraCollection.forEach(m => select.insertAdjacentHTML('beforeend', `<option value="${m.id}">${m.nama}</option>`));
    }

    function openPurchaseModal(id = null, event = null) {
        if (event) event.stopPropagation();
        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('purchaseModal'));
        document.getElementById('purchaseForm').reset();
        document.getElementById('itemBodyList').innerHTML = '';
        
        if (id) {
            document.getElementById('modalTitle').innerText = 'Edit Faktur Pembelian';
            document.getElementById('form_mode').value = 'edit';
            fetch(`${API_URL}/${id}`).then(r => r.json()).then(res => {
                const inv = res.data;
                document.getElementById('modal_nomor_invoice').value = inv.nomor_invoice;
                document.getElementById('modal_mitra_id').value = inv.mitra_id;
                inv.items.forEach(it => addNewPurchaseRow(it));
                calculatePurchaseTotal();
            });
        } else {
            document.getElementById('modalTitle').innerText = 'Buat Faktur Pembelian';
            document.getElementById('modal_nomor_invoice').value = `PUR/${new Date().getFullYear()}/${Date.now().toString().slice(-4)}`;
            addNewPurchaseRow();
        }
        modal.show();
    }

    function addNewPurchaseRow(existing = null) {
        const rowId = Date.now();
        let optProd = productCollection.map(p => `<option value="${p.id}" data-price="${p.harga_beli || 0}" ${existing && existing.produk_id == p.id ? 'selected' : ''}>${p.nama_produk}</option>`).join('');
        let optTax = taxCollection.map(t => `<option value="${t.id}" data-rate="${t.nilai_pajak}" ${existing && existing.taxes && existing.taxes[0]?.tax_id == t.id ? 'selected' : ''}>${t.nama_pajak}</option>`).join('');

        const html = `
            <tr id="row_${rowId}">
                <td class="ps-4"><select class="form-select form-select-sm prod-select" onchange="autoFillPurchasePrice(${rowId})"><option value="">-- Produk --</option>${optProd}</select></td>
                <td><input type="number" class="form-control form-control-sm text-center prod-qty" value="${existing ? existing.qty : 1}" oninput="calculatePurchaseTotal()"></td>
                <td><input type="number" class="form-control form-control-sm text-end prod-price" value="${existing ? existing.harga_satuan : 0}" oninput="calculatePurchaseTotal()"></td>
                <td><input type="number" class="form-control form-control-sm text-end prod-disc" value="${existing ? existing.diskon_nilai : 0}" oninput="calculatePurchaseTotal()"></td>
                <td><select class="form-select form-select-sm prod-tax" onchange="calculatePurchaseTotal()"><option value="" data-rate="0">0%</option>${optTax}</select></td>
                <td class="text-end pe-4 fw-bold prod-subtotal">Rp 0</td>
                <td><button type="button" class="btn btn-link text-danger" onclick="this.closest('tr').remove();calculatePurchaseTotal()"><i class="fa fa-times"></i></button></td>
            </tr>`;
        document.getElementById('itemBodyList').insertAdjacentHTML('beforeend', html);
    }

    function autoFillPurchasePrice(id) {
        const row = document.getElementById(`row_${id}`);
        const price = row.querySelector('.prod-select option:checked').dataset.price;
        row.querySelector('.prod-price').value = price;
        calculatePurchaseTotal();
    }

    function calculatePurchaseTotal() {
        let sub = 0, tax = 0;
        document.querySelectorAll('#itemBodyList tr').forEach(row => {
            const q = row.querySelector('.prod-qty').value || 0, p = row.querySelector('.prod-price').value || 0, d = row.querySelector('.prod-disc').value || 0;
            const rate = row.querySelector('.prod-tax option:checked').dataset.rate || 0;
            const line = (q * p) - d;
            row.querySelector('.prod-subtotal').innerText = formatIDR(line);
            sub += line;
            tax += (line * (rate / 100));
        });
        document.getElementById('summary_subtotal').innerText = formatIDR(sub);
        document.getElementById('summary_tax').innerText = formatIDR(tax);
        document.getElementById('summary_grand_total').innerText = formatIDR(sub + tax);
    }

    async function saveFullPurchase() {
        const lines = [];
        document.querySelectorAll('#itemBodyList tr').forEach(row => {
            lines.push({
                produk_id: row.querySelector('.prod-select').value,
                qty: row.querySelector('.prod-qty').value,
                harga_satuan: row.querySelector('.prod-price').value,
                diskon_nilai: row.querySelector('.prod-disc').value,
                total_harga_item: row.querySelector('.prod-subtotal').innerText.replace(/[^0-9]/g, '')
            });
        });

        const body = {
            invoice: {
                tipe_invoice: 'Purchase',
                nomor_invoice: document.getElementById('modal_nomor_invoice').value,
                tgl_invoice: document.getElementById('modal_tgl_invoice').value,
                mitra_id: document.getElementById('modal_mitra_id').value,
                total_akhir: document.getElementById('summary_grand_total').innerText.replace(/[^0-9]/g, '')
            },
            items: lines
        };

        const res = await fetch('/api/invoice-create-api', {
            method: 'POST', headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body)
        });
        if((await res.json()).success) location.reload();
    }

    document.addEventListener('DOMContentLoaded', initializePurchaseData);
</script>