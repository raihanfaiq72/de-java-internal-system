@extends('Layout.main')

@push('css')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
<style>
.f-label { font-size:10px;text-transform:uppercase;letter-spacing:.8px;font-weight:700;color:#64748b;margin-bottom:5px;display:block; }
.f-mono  { font-family:'JetBrains Mono',monospace;font-size:13px; }
</style>
@endpush

@section('main')
<div class="page-wrapper" style="background:#f8fafc;min-height:100vh;font-family:'Inter',sans-serif;">
  <div class="page-content">
    <div class="container-fluid">

      <!-- Header -->
      <div class="row align-items-center mt-3 mb-4">
        <div class="col-md-6">
          <div class="d-flex align-items-center">
            <a href="{{ route('surat-jalan') }}" class="btn btn-light border me-3 shadow-sm rounded-circle"
               style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;">
              <i class="fa fa-arrow-left"></i>
            </a>
            <div>
              <h4 class="fw-bold text-dark mb-0">Detail Surat Jalan</h4>
              <p class="text-muted small mb-0" id="header-subtitle">Memuat...</p>
            </div>
          </div>
        </div>
        <div class="col-md-6 text-md-end mt-2 mt-md-0">
          <span id="header-status-badge" class="badge bg-secondary fs-6 px-3 py-2 rounded-pill me-2">Memuat...</span>
          <a id="btn-print" href="/surat-jalan/print/{{ $id }}" target="_blank"
             class="btn btn-white border fw-bold px-3 shadow-sm text-dark me-2 d-none">
            <i class="fa fa-print me-1"></i> Cetak
          </a>
          <button id="btn-convert" class="btn btn-success fw-bold px-4 shadow-sm me-2 d-none"
                  onclick="openConvertModal()">
            <i class="fa fa-file-invoice me-1"></i> Buat Invoice
          </button>
          <button id="btn-edit" class="btn btn-primary fw-bold px-4 shadow-sm"
                  onclick="openSuratJalanModal({{ $id }}, 'edit')">
            <i class="fa fa-pencil me-1"></i> Edit
          </button>
        </div>
      </div>

      <div id="main-content" class="row" style="display:none;">
        <!-- Left: Surat Jalan Visual -->
        <div class="col-lg-8">
          <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-5 bg-white">
              <!-- Kop -->
              <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                  <h3 class="fw-bold text-dark mb-1">SURAT JALAN</h3>
                  <div class="f-mono text-warning fw-bold fs-5" id="detail-nomor">-</div>
                </div>
                <div class="text-end">
                  <div class="small text-muted">Tanggal</div>
                  <div class="fw-bold" id="detail-tgl">-</div>
                  <div class="small text-muted mt-1">Ref / PO</div>
                  <div class="fw-bold" id="detail-ref">-</div>
                </div>
              </div>

              <hr class="my-3">

              <!-- Mitra & Sales -->
              <div class="row mb-4">
                <div class="col-md-6">
                  <div class="f-label">Kepada</div>
                  <div class="fw-bold text-dark" id="detail-mitra-nama">-</div>
                  <div class="small text-muted" id="detail-mitra-alamat">-</div>
                  <div class="small text-muted" id="detail-mitra-telp">-</div>
                </div>
                <div class="col-md-6 text-md-end">
                  <div class="f-label">Sales Person</div>
                  <div class="fw-bold" id="detail-sales">-</div>
                </div>
              </div>

              <!-- Items Table -->
              <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle" id="detail-items-table">
                  <thead class="bg-light text-uppercase text-secondary fw-bold" style="font-size:11px;">
                    <tr>
                      <th class="ps-3" width="5%">No</th>
                      <th width="45%">Produk / Deskripsi</th>
                      <th class="text-center" width="15%">Qty</th>
                      <th class="text-center" width="15%">Satuan</th>
                      <th width="20%">Keterangan</th>
                    </tr>
                  </thead>
                  <tbody id="detail-items-body">
                    <tr><td colspan="5" class="text-center p-4 text-muted">Memuat...</td></tr>
                  </tbody>
                </table>
              </div>

              <!-- Notes -->
              <div class="row">
                <div class="col-md-6">
                  <div class="f-label">Catatan</div>
                  <div class="text-muted small" id="detail-keterangan">-</div>
                </div>
                <div class="col-md-6">
                  <div class="f-label">Syarat & Ketentuan</div>
                  <div class="text-muted small" id="detail-syarat">-</div>
                </div>
              </div>

              <!-- Signature area -->
              <div class="row mt-5 pt-3 border-top">
                <div class="col-4 text-center">
                  <div class="small text-muted mb-5">Pengirim</div>
                  <div class="border-top pt-2 small fw-bold">( ________________ )</div>
                </div>
                <div class="col-4 text-center">
                  <div class="small text-muted mb-5">Pengemudi</div>
                  <div class="border-top pt-2 small fw-bold">( ________________ )</div>
                </div>
                <div class="col-4 text-center">
                  <div class="small text-muted mb-5">Penerima</div>
                  <div class="border-top pt-2 small fw-bold">( ________________ )</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right: Sidebar -->
        <div class="col-lg-4">
          <!-- Status Card -->
          <div class="card border-0 shadow-sm rounded-4 mb-3">
            <div class="card-body p-4">
              <h6 class="fw-bold text-dark mb-3">Status Dokumen</h6>
              <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small">Status</span>
                <span id="sidebar-status" class="badge bg-secondary">-</span>
              </div>
              <div class="d-flex align-items-center justify-content-between">
                <span class="text-muted small">Dibuat</span>
                <span class="small fw-bold" id="sidebar-created">-</span>
              </div>
            </div>
          </div>

          <!-- Convert Info -->
          <div class="card border-0 shadow-sm rounded-4 mb-3" id="convert-info-card" style="display:none!important;">
            <div class="card-body p-4">
              <h6 class="fw-bold text-success mb-2"><i class="fa fa-check-circle me-2"></i>Sudah Dikonversi</h6>
              <p class="small text-muted mb-2">Surat jalan ini telah dikonversi ke invoice penjualan.</p>
              <div class="f-label">No. Invoice</div>
              <div class="fw-bold f-mono" id="sidebar-invoice-ref">-</div>
            </div>
          </div>

          <!-- Activity Log -->
          <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
              <h6 class="fw-bold text-dark mb-3">Riwayat Aktivitas</h6>
              <div id="activity-log" class="small text-muted">Memuat...</div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Convert to Invoice Modal -->
<div class="modal fade" id="convertModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header border-bottom-0 pt-4 px-4">
        <h5 class="modal-title fw-bold"><i class="fa fa-file-invoice text-success me-2"></i>Konversi ke Invoice Penjualan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body px-4 pb-2">
        <div class="alert alert-info small mb-3">
          <i class="fa fa-info-circle me-2"></i>
          Masukkan harga untuk setiap barang. Nomor invoice akan dibuat otomatis.
        </div>
        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label class="f-label">Nomor Invoice <span class="text-danger">*</span></label>
            <input type="text" id="conv_nomor" class="form-control fw-bold" placeholder="INV/2026/...">
          </div>
          <div class="col-md-6">
            <label class="f-label">Tanggal Invoice <span class="text-danger">*</span></label>
            <input type="date" id="conv_tgl" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="f-label">Jatuh Tempo</label>
            <input type="date" id="conv_jatuh_tempo" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="f-label">Referensi</label>
            <input type="text" id="conv_ref" class="form-control" placeholder="Otomatis dari surat jalan">
          </div>
        </div>

        <h6 class="fw-bold text-dark mb-2 mt-3">Harga Barang</h6>
        <div class="table-responsive">
          <table class="table table-bordered align-middle" id="conv-items-table">
            <thead class="bg-light text-uppercase text-secondary fw-bold" style="font-size:11px;">
              <tr>
                <th class="ps-3">Produk</th>
                <th class="text-center" width="10%">Qty</th>
                <th class="text-end" width="20%">Harga Satuan</th>
                <th class="text-end" width="20%">Total</th>
              </tr>
            </thead>
            <tbody id="conv-items-body"></tbody>
            <tfoot>
              <tr class="fw-bold">
                <td colspan="3" class="text-end pe-3">Grand Total</td>
                <td class="text-end pe-3" id="conv-grand-total">Rp 0</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      <div class="modal-footer border-top-0 pb-4 px-4">
        <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-success fw-bold px-4 shadow-sm" onclick="doConvertToInvoice()">
          <i class="fa fa-check me-2"></i>Buat Invoice
        </button>
      </div>
    </div>
  </div>
</div>

@include('SuratJalan.Modal.modal-fullscreen')
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
const SJ_ID = {{ $id }};
let sjDetailData = null;

document.addEventListener('DOMContentLoaded', () => loadSjDetail());

async function loadSjDetail() {
    try {
        const res    = await fetch(`/api/surat-jalan-api/${SJ_ID}`);
        const result = await res.json();
        if (!result.success) { alert('Data tidak ditemukan'); return; }

        sjDetailData = result.data;
        renderDetail(sjDetailData);
        document.getElementById('main-content').style.display = '';
    } catch (e) {
        console.error(e);
        alert('Gagal memuat detail surat jalan');
    }
}

function formatIDR(val) {
    return new Intl.NumberFormat('id-ID', { style:'currency', currency:'IDR', minimumFractionDigits:0 }).format(val || 0);
}
function formatDate(d) {
    if (!d) return '-';
    return new Date(d).toLocaleDateString('id-ID', { day:'2-digit', month:'long', year:'numeric' });
}

function renderDetail(sj) {
    document.getElementById('header-subtitle').textContent = sj.nomor_invoice;
    document.getElementById('detail-nomor').textContent    = sj.nomor_invoice;
    document.getElementById('detail-tgl').textContent      = formatDate(sj.tgl_invoice);
    document.getElementById('detail-ref').textContent      = sj.ref_no || '-';
    document.getElementById('detail-keterangan').textContent = sj.keterangan || '-';
    document.getElementById('detail-syarat').textContent   = sj.syarat_ketentuan || '-';
    document.getElementById('sidebar-created').textContent = formatDate(sj.created_at);

    // Mitra
    if (sj.mitra) {
        document.getElementById('detail-mitra-nama').textContent   = sj.mitra.nama;
        document.getElementById('detail-mitra-alamat').textContent = sj.mitra.alamat || '-';
        document.getElementById('detail-mitra-telp').textContent   = sj.mitra.no_hp  || '-';
    }

    // Sales
    document.getElementById('detail-sales').textContent = sj.sales ? sj.sales.name : 'Tanpa Sales';

    // Status badge
    const isConverted = sj.status_dok === 'Sent';
    const badge = document.getElementById('header-status-badge');
    const sidebarBadge = document.getElementById('sidebar-status');
    if (isConverted) {
        badge.className = 'badge bg-success fs-6 px-3 py-2 rounded-pill me-2';
        badge.textContent = 'Sudah Dikonversi';
        sidebarBadge.className = 'badge bg-success';
        sidebarBadge.textContent = 'Sudah Dikonversi';
        document.getElementById('convert-info-card').style.display = '';
        document.getElementById('sidebar-invoice-ref').textContent = sj.ref_no || '-';
        document.getElementById('btn-convert').classList.add('d-none');
    } else {
        badge.className = 'badge bg-warning text-dark fs-6 px-3 py-2 rounded-pill me-2';
        badge.textContent = 'Draft';
        sidebarBadge.className = 'badge bg-warning text-dark';
        sidebarBadge.textContent = 'Draft';
        document.getElementById('btn-convert').classList.remove('d-none');
    }

    document.getElementById('btn-print').classList.remove('d-none');

    // Items
    const tbody = document.getElementById('detail-items-body');
    if (!sj.items || sj.items.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted p-4">Tidak ada barang</td></tr>';
    } else {
        tbody.innerHTML = sj.items.map((item, i) => `
            <tr>
                <td class="ps-3 text-center text-muted">${i + 1}</td>
                <td>
                    <div class="fw-bold">${item.nama_produk_manual || item.product?.nama_produk || '-'}</div>
                    ${item.deskripsi_produk ? `<div class="small text-muted">${item.deskripsi_produk}</div>` : ''}
                </td>
                <td class="text-center fw-bold">${item.qty}</td>
                <td class="text-center">
                    <span class="badge bg-light text-secondary border">${item.product?.satuan || 'Pcs'}</span>
                </td>
                <td class="text-muted small">${item.deskripsi_produk || '-'}</td>
            </tr>
        `).join('');
    }

    // Activity log
    const actDiv = document.getElementById('activity-log');
    if (sj.activities && sj.activities.length > 0) {
        actDiv.innerHTML = sj.activities.slice(0, 10).map(a => `
            <div class="d-flex gap-2 mb-2">
                <div class="text-primary mt-1"><i class="fa fa-circle" style="font-size:6px;"></i></div>
                <div>
                    <div>${a.deskripsi || a.action || 'Aktivitas'}</div>
                    <div class="text-muted" style="font-size:11px;">${formatDate(a.created_at)} · ${a.user?.name || 'System'}</div>
                </div>
            </div>
        `).join('');
    } else {
        actDiv.innerHTML = '<div class="text-muted">Belum ada aktivitas tercatat.</div>';
    }
}

// ── Convert Modal ─────────────────────────────────────────────────────────────
function openConvertModal() {
    if (!sjDetailData) return;

    const today = new Date().toISOString().split('T')[0];
    const due   = new Date(); due.setDate(due.getDate() + 30);

    document.getElementById('conv_nomor').value       = 'INV/' + new Date().getFullYear() + '/' + Date.now().toString().slice(-5);
    document.getElementById('conv_tgl').value         = today;
    document.getElementById('conv_jatuh_tempo').value = due.toISOString().split('T')[0];
    document.getElementById('conv_ref').value         = sjDetailData.nomor_invoice;

    // Build items rows
    const tbody = document.getElementById('conv-items-body');
    tbody.innerHTML = (sjDetailData.items || []).map(item => `
        <tr data-item-id="${item.id}">
            <td class="ps-3">
                <div class="fw-bold">${item.nama_produk_manual || item.product?.nama_produk || '-'}</div>
                <input type="hidden" class="conv-item-id" value="${item.id}">
            </td>
            <td class="text-center">
                <input type="number" class="form-control form-control-sm text-center conv-qty" value="${item.qty}" min="0.01" step="0.01" oninput="recalcConvTotal()">
            </td>
            <td class="text-end">
                <input type="text" class="form-control form-control-sm text-end conv-harga rupiah-conv" value="0" oninput="formatConvRupiah(this); recalcConvTotal()">
            </td>
            <td class="text-end pe-3 fw-bold conv-row-total">Rp 0</td>
        </tr>
    `).join('');

    recalcConvTotal();
    new bootstrap.Modal(document.getElementById('convertModal')).show();
}

function formatConvRupiah(el) {
    let v = el.value.replace(/[^0-9]/g, '');
    el.value = parseInt(v || 0).toLocaleString('id-ID');
}

function cleanConvNum(v) {
    return parseInt(String(v).replace(/[^0-9]/g, '')) || 0;
}

function recalcConvTotal() {
    let grand = 0;
    document.querySelectorAll('#conv-items-body tr').forEach(row => {
        const qty   = parseFloat(row.querySelector('.conv-qty')?.value || 0);
        const harga = cleanConvNum(row.querySelector('.conv-harga')?.value || 0);
        const total = qty * harga;
        grand += total;
        const totalEl = row.querySelector('.conv-row-total');
        if (totalEl) totalEl.textContent = formatIDR(total);
    });
    document.getElementById('conv-grand-total').textContent = formatIDR(grand);
}

async function doConvertToInvoice() {
    const nomor = document.getElementById('conv_nomor').value;
    const tgl   = document.getElementById('conv_tgl').value;
    if (!nomor || !tgl) { alert('Nomor invoice dan tanggal wajib diisi.'); return; }

    const items = [];
    document.querySelectorAll('#conv-items-body tr').forEach(row => {
        const id    = row.querySelector('.conv-item-id')?.value;
        const qty   = parseFloat(row.querySelector('.conv-qty')?.value || 0);
        const harga = cleanConvNum(row.querySelector('.conv-harga')?.value || 0);
        if (id && qty > 0) {
            items.push({ id: parseInt(id), qty, harga_satuan: harga, diskon_nilai: 0, diskon_tipe: 'Fixed' });
        }
    });

    if (items.length === 0) { alert('Tidak ada item valid.'); return; }

    const payload = {
        nomor_invoice:    nomor,
        tgl_invoice:      tgl,
        tgl_jatuh_tempo:  document.getElementById('conv_jatuh_tempo').value,
        ref_no:           document.getElementById('conv_ref').value,
        items
    };

    const btn = document.querySelector('[onclick="doConvertToInvoice()"]');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Memproses...';

    try {
        const res    = await fetch(`/api/surat-jalan-api/${SJ_ID}/convert-to-invoice`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify(payload)
        });
        const result = await res.json();

        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('convertModal')).hide();
            const invId = result.data?.invoice?.id;
            if (invId) {
                if (confirm('Invoice berhasil dibuat! Buka halaman invoice sekarang?')) {
                    window.location.href = `/sales/${invId}`;
                } else {
                    loadSjDetail();
                }
            } else {
                loadSjDetail();
            }
        } else {
            if (result.errors) {
                let msg = 'Kesalahan:\n';
                Object.values(result.errors).forEach(e => msg += `- ${e}\n`);
                alert(msg);
            } else {
                alert('Gagal: ' + result.message);
            }
        }
    } catch (e) {
        console.error(e);
        alert('Terjadi kesalahan.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-check me-2"></i>Buat Invoice';
    }
}
</script>
@endpush
