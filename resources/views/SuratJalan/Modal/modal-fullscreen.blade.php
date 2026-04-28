<style>
.modal-backdrop-custom { position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,.6);z-index:1065;display:none; }
</style>

<div class="modal fade" id="sjModal" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen">
    <div class="modal-content border-0" style="background:#f8fafc;">
      <div class="modal-header bg-white border-bottom px-4 py-3 sticky-top shadow-sm">
        <div class="d-flex align-items-center">
          <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-2 me-3">
            <i class="fa fa-truck fa-lg"></i>
          </div>
          <div>
            <h5 class="modal-title fw-bold text-dark mb-0" id="sjModalTitle">Surat Jalan Baru</h5>
            <p class="text-muted small mb-0">Pengiriman barang tanpa harga — akan dikonversi ke invoice.</p>
          </div>
        </div>
        <div class="ms-auto d-flex align-items-center gap-3">
          <button type="button" class="btn btn-light border text-secondary fw-semibold px-4" data-bs-dismiss="modal">Tutup</button>
          <button type="button" class="btn btn-warning px-4 fw-bold shadow-sm text-dark" onclick="saveSuratJalan()">
            <i class="fa fa-save me-2"></i>Simpan
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
              <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body p-4">
                  <div class="row g-4">
                    <!-- Mitra -->
                    <div class="col-lg-6 border-end">
                      <label class="f-label mb-2">Mitra (Client) <span class="text-danger">*</span></label>
                      <select id="sj_mitra_id" class="form-select mb-2" onchange="renderSjMitraDetail()">
                        <option value="">Cari Mitra...</option>
                      </select>
                      <div id="sj_mitra_detail" class="d-none bg-light p-3 rounded border">
                        <div class="fw-bold text-dark mb-1" id="sj_disp_nama">-</div>
                        <div class="small text-muted mb-1"><i class="fa fa-map-marker-alt me-2 text-secondary"></i><span id="sj_disp_alamat">-</span></div>
                        <div class="small text-muted"><i class="fa fa-phone me-2 text-secondary"></i><span id="sj_disp_telp">-</span></div>
                      </div>
                      <div id="sj_mitra_empty" class="text-center py-4 text-muted border border-dashed rounded bg-light">
                        <i class="fa fa-user-plus mb-2 fs-4 text-secondary"></i>
                        <div class="small">Pilih pelanggan untuk melihat detail</div>
                      </div>
                    </div>

                    <!-- Doc Details -->
                    <div class="col-lg-6">
                      <div class="row g-3">
                        <div class="col-md-6">
                          <label class="f-label">Nomor Surat Jalan</label>
                          <input type="text" id="sj_nomor" class="form-control fw-bold bg-light text-warning" placeholder="Auto Generated">
                        </div>
                        <div class="col-md-6">
                          <label class="f-label">Referensi / PO</label>
                          <input type="text" id="sj_ref_no" class="form-control" placeholder="Contoh: PO-001">
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
                              <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Items -->
              <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                  <h6 class="mb-0 fw-bold text-dark text-uppercase small">
                    <i class="fa fa-box me-2 text-warning"></i>Daftar Barang
                  </h6>
                  <button type="button" class="btn btn-warning btn-sm fw-bold px-3 rounded-pill text-dark" onclick="addSjRow()">
                    <i class="fa fa-plus me-1"></i> Tambah Baris
                  </button>
                </div>
                <div class="table-responsive">
                  <table class="table align-middle mb-0 table-hover" id="sjItemTable">
                    <thead class="bg-light text-secondary small fw-bold text-uppercase">
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
                        <td colspan="5" class="text-center py-2 text-muted small fst-italic">
                          Klik tombol tambah untuk memasukkan barang
                        </td>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>

              <!-- Notes -->
              <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                  <div class="row g-4">
                    <div class="col-md-6">
                      <label class="f-label mb-2">Catatan</label>
                      <textarea id="sj_keterangan" class="form-control bg-light border-0" rows="3" placeholder="Catatan pengiriman..."></textarea>
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
      <input type="text" class="form-control form-control-sm text-muted small sj-prod-desc" placeholder="Deskripsi (Opsional)">
    </td>
    <td class="py-3">
      <input type="number" class="form-control form-control-sm text-center sj-prod-qty" value="1" min="0.01" step="0.01">
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

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<script>
const SJ_API_CREATE = '/api/surat-jalan-create-api';
const SJ_API_BASE   = '/api/surat-jalan-api';
const SJ_MITRA_API  = '/api/mitra-api';
const SJ_PROD_API   = '/api/product-api';

let sjMasterProduk = [];
let sjMasterMitra  = [];
let sjTomMitra     = null;
let sjTomSales     = null;

// ── Open Modal ────────────────────────────────────────────────────────────────
async function openSuratJalanModal(id = null, mode = 'create') {
    const modal = new bootstrap.Modal(document.getElementById('sjModal'));

    // Reset form
    document.getElementById('sjForm').reset();
    document.getElementById('sjItemBody').innerHTML = '';
    document.getElementById('sj_mitra_detail').classList.add('d-none');
    document.getElementById('sj_mitra_empty').classList.remove('d-none');

    // Load master data if needed
    if (sjMasterProduk.length === 0) await loadSjMasterProduk();
    if (sjMasterMitra.length === 0)  await loadSjMasterMitra();

    initSjTomMitra();
    initSjTomSales();

    if (mode === 'edit' && id) {
        document.getElementById('sjModalTitle').innerText = 'Edit Surat Jalan';
        document.getElementById('sj_form_mode').value = 'edit';
        document.getElementById('sj_edit_id').value = id;

        try {
            const res    = await fetch(`${SJ_API_BASE}/${id}`);
            const result = await res.json();
            if (result.success) {
                const sj = result.data;
                document.getElementById('sj_nomor').value      = sj.nomor_invoice;
                document.getElementById('sj_ref_no').value     = sj.ref_no || '';
                document.getElementById('sj_tgl').value        = sj.tgl_invoice;
                document.getElementById('sj_keterangan').value = sj.keterangan || '';
                document.getElementById('sj_syarat').value     = sj.syarat_ketentuan || '';

                if (sjTomMitra) sjTomMitra.setValue(String(sj.mitra_id), true);
                if (sjTomSales) sjTomSales.setValue(String(sj.sales_id || 0), true);

                renderSjMitraDetail();

                (sj.items || []).forEach(item => addSjRow(item));
            }
        } catch (e) {
            console.error(e);
            alert('Gagal memuat data surat jalan');
        }
    } else {
        document.getElementById('sjModalTitle').innerText = 'Surat Jalan Baru';
        document.getElementById('sj_form_mode').value = 'create';
        document.getElementById('sj_edit_id').value   = '';
        document.getElementById('sj_tgl').value       = new Date().toISOString().split('T')[0];
        document.getElementById('sj_nomor').value     = 'SJ/' + new Date().getFullYear() + '/' + Date.now().toString().slice(-5);
        addSjRow();
    }

    modal.show();
}

// ── Master Data ───────────────────────────────────────────────────────────────
async function loadSjMasterProduk() {
    try {
        const res    = await fetch(`${SJ_PROD_API}?per_page=1000`);
        const result = await res.json();
        if (result.success) sjMasterProduk = result.data.data || result.data;
    } catch (e) { console.error('Gagal load produk', e); }
}

async function loadSjMasterMitra() {
    try {
        const res    = await fetch(`${SJ_MITRA_API}?per_page=1000&tipe_mitra=Client`);
        const result = await res.json();
        if (result.success) sjMasterMitra = result.data.data || result.data;
    } catch (e) { console.error('Gagal load mitra', e); }
}

// ── TomSelect ─────────────────────────────────────────────────────────────────
function initSjTomMitra(selectedId = null) {
    if (sjTomMitra) { try { sjTomMitra.destroy(); } catch(_){} sjTomMitra = null; }

    const sel = document.getElementById('sj_mitra_id');
    sel.innerHTML = '';

    sjTomMitra = new TomSelect('#sj_mitra_id', {
        options: sjMasterMitra.map(m => ({ id: m.id, nama: m.nama, info: [m.no_hp, m.alamat].filter(Boolean).join(' • ') })),
        valueField: 'id', labelField: 'nama', searchField: ['nama', 'info'],
        allowEmptyOption: true, placeholder: 'Cari Mitra (Client)...', maxOptions: 1000, create: false,
        dropdownParent: 'body',
        render: {
            option: (d, esc) => `<div><div class="fw-bold">${esc(d.nama)}</div>${d.info ? `<div class="small text-muted">${esc(d.info)}</div>` : ''}</div>`,
            item:   (d, esc) => `<div>${esc(d.nama)}</div>`
        },
        onChange: () => renderSjMitraDetail()
    });

    if (selectedId) sjTomMitra.setValue(String(selectedId), true);
}

function initSjTomSales(selectedId = null) {
    if (sjTomSales) { try { sjTomSales.destroy(); } catch(_){} sjTomSales = null; }

    const sel = document.getElementById('sj_sales_id');
    if (!sel) return;

    sjTomSales = new TomSelect('#sj_sales_id', {
        allowEmptyOption: true, placeholder: 'Pilih Sales...', create: false, dropdownParent: 'body'
    });

    if (selectedId) sjTomSales.setValue(String(selectedId), true);
}

// ── Mitra Detail ──────────────────────────────────────────────────────────────
function renderSjMitraDetail() {
    const id = sjTomMitra ? sjTomMitra.getValue() : document.getElementById('sj_mitra_id').value;
    const detail = document.getElementById('sj_mitra_detail');
    const empty  = document.getElementById('sj_mitra_empty');

    if (!id) { detail.classList.add('d-none'); empty.classList.remove('d-none'); return; }

    const m = sjMasterMitra.find(x => String(x.id) === String(id));
    if (m) {
        document.getElementById('sj_disp_nama').innerText   = m.nama;
        document.getElementById('sj_disp_alamat').innerText = m.alamat || '-';
        document.getElementById('sj_disp_telp').innerText   = m.no_hp  || '-';
        if (m.salesperson_id && sjTomSales) sjTomSales.setValue(String(m.salesperson_id), true);
        detail.classList.remove('d-none');
        empty.classList.add('d-none');
    }
}

// ── Add Row ───────────────────────────────────────────────────────────────────
function addSjRow(data = null) {
    const tbody    = document.getElementById('sjItemBody');
    const template = document.getElementById('sjRowTemplate');
    const clone    = template.content.cloneNode(true);
    const tr       = clone.querySelector('tr');

    const selectEl  = tr.querySelector('.sj-prod-select');
    const idInput   = tr.querySelector('.sj-prod-id');
    const qtyInput  = tr.querySelector('.sj-prod-qty');
    const unitLabel = tr.querySelector('.sj-prod-unit');
    const descInput = tr.querySelector('.sj-prod-desc');

    const ts = new TomSelect(selectEl, {
        options: sjMasterProduk.map(p => ({
            id: String(p.id), nama: p.nama_produk, sku: p.sku_kode || '',
            unit: p.satuan || 'Pcs', qty: Number(p.qty || 0)
        })),
        valueField: 'id', labelField: 'nama', searchField: ['nama', 'sku'],
        placeholder: 'Cari barang...', create: false, dropdownParent: 'body',
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
                idInput.value       = opt.id;
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
                ts.addOption({ id: prodId, nama: data.nama_produk_manual || data.nama_produk || 'Produk', unit: 'Pcs', qty: 999 });
                ts.setValue(prodId, true);
            }
            idInput.value       = prodId;
            qtyInput.value      = parseFloat(data.qty) || 1;
            descInput.value     = data.deskripsi_produk || '';
            unitLabel.innerText = data.product?.satuan || data.satuan || 'Pcs';
        }, 50);
    }

    tr.querySelector('.sj-btn-remove').onclick = () => { ts.destroy(); tr.remove(); };
    tbody.appendChild(tr);
}

// ── Save ──────────────────────────────────────────────────────────────────────
async function saveSuratJalan() {
    const mode = document.getElementById('sj_form_mode').value;
    const id   = document.getElementById('sj_edit_id').value;

    const mitraId = sjTomMitra ? sjTomMitra.getValue() : document.getElementById('sj_mitra_id').value;
    const salesId = sjTomSales ? sjTomSales.getValue() : document.getElementById('sj_sales_id').value;

    if (!mitraId) { alert('Harap pilih Mitra!'); return; }

    const nomor = document.getElementById('sj_nomor').value;
    const tgl   = document.getElementById('sj_tgl').value;
    if (!nomor) { alert('Nomor surat jalan wajib diisi.'); return; }
    if (!tgl)   { alert('Tanggal surat jalan wajib diisi.'); return; }

    // Collect items
    const items = [];
    document.querySelectorAll('#sjItemBody tr').forEach(row => {
        const qtyEl  = row.querySelector('.sj-prod-qty');
        const idEl   = row.querySelector('.sj-prod-id');
        const descEl = row.querySelector('.sj-prod-desc');
        if (!qtyEl) return;

        const qty = parseFloat(qtyEl.value) || 0;
        if (qty <= 0) return;

        // Get product name from TomSelect
        const selectEl = row.querySelector('.sj-prod-select');
        let namaProduk = '';
        if (selectEl && selectEl.tomselect) {
            const val = selectEl.tomselect.getValue();
            if (val && selectEl.tomselect.options[val]) namaProduk = selectEl.tomselect.options[val].nama;
        }

        items.push({
            produk_id:          idEl.value || null,
            nama_produk_manual: namaProduk,
            deskripsi_produk:   descEl ? descEl.value : '',
            qty:                qty,
            harga_satuan:       0,
            diskon_nilai:       0,
            total_harga_item:   0,
        });
    });

    if (items.length === 0) { alert('Harap tambahkan minimal satu barang.'); return; }

    const payload = {
        invoice: {
            nomor_invoice:    nomor,
            tgl_invoice:      tgl,
            ref_no:           document.getElementById('sj_ref_no').value,
            mitra_id:         mitraId,
            sales_id:         salesId || 0,
            keterangan:       document.getElementById('sj_keterangan').value,
            syarat_ketentuan: document.getElementById('sj_syarat').value,
        },
        items: items
    };

    const url    = mode === 'edit' ? `${SJ_API_BASE}/${id}` : SJ_API_CREATE;
    const method = mode === 'edit' ? 'PUT' : 'POST';

    const btn = document.querySelector('[onclick="saveSuratJalan()"]');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Menyimpan...';

    try {
        const res    = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
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
    if (sjTomMitra) { try { sjTomMitra.destroy(); } catch(_){} sjTomMitra = null; }
    if (sjTomSales) { try { sjTomSales.destroy(); } catch(_){} sjTomSales = null; }
});
</script>
