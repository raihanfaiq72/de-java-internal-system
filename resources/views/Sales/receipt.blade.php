@extends('Layout.main')

@section('main')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

<div class="page-wrapper">
    <div class="page-content">
        <div class="container-fluid">
            <!-- Page Title -->
            <div class="row mb-3">
                <div class="col-sm-12">
                    <div class="page-title-box d-md-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="page-title fw-bold">Semua Kuitansi Penjualan</h4>
                            <p class="text-muted mb-0 small">Kelola kuitansi dan riwayat pembayaran pelanggan.</p>
                        </div>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Kuitansi</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                        <div class="card-body p-4">
                            <!-- Filters -->
                            <div class="row g-2 mb-4 p-3 bg-light rounded-3 align-items-end border">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted">Cari Kuitansi / Pelanggan</label>
                                    <input type="text" id="filter-search" class="form-control" placeholder="Ketik nomor kuitansi...">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-muted">Metode</label>
                                    <select id="filter-metode" class="form-select">
                                        <option value="">Semua Metode</option>
                                        <option value="Cash">Cash</option>
                                        <option value="Transfer">Transfer</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button onclick="loadReceiptData()" class="btn btn-dark w-100 fw-bold shadow-sm">
                                        <i class="fa fa-filter me-1"></i> FILTER
                                    </button>
                                </div>
                                <div class="col-md-3 text-end">
                                    <button class="btn btn-primary px-4 fw-bold shadow-sm" onclick="openCreateModal()">
                                        <i class="fa fa-plus-circle me-1"></i> TAMBAH KUITANSI
                                    </button>
                                </div>
                            </div>

                            <!-- List Header -->
                            <div class="bg-light border-bottom header-table-custom py-2 px-1 rounded-top border">
                                <div class="d-flex align-items-center">
                                    <div class="col-fixed-check text-center px-2">#</div>
                                    <div class="row flex-grow-1 m-0 text-uppercase fw-bold text-muted text-center" style="font-size: 10px; letter-spacing: 1px;">
                                        <div class="col-3 text-start ps-0">No. Kuitansi & Pelanggan</div>
                                        <div class="col-2">Metode / Akun</div>
                                        <div class="col-2">Status</div>
                                        <div class="col-2">Tgl Bayar</div>
                                        <div class="col-3 text-end">Jumlah Bayar</div>
                                    </div>
                                    <div class="col-fixed-aksi text-center">Aksi</div>
                                </div>
                            </div>

                            <!-- Accordion List -->
                            <div class="accordion custom-coa-accordion mt-3" id="receiptAccordion">
                                <!-- Data will be populated by JS -->
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                                <span id="pagination-info" class="text-muted small"></span>
                                <nav><ul class="pagination pagination-sm mb-0 shadow-sm" id="pagination-container"></ul></nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Create Receipt -->
<div class="modal fade" id="modalCreateReceipt" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header">
                <h5 class="fw-bold mb-0">Terima Pembayaran untuk Invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="form-receipt" enctype="multipart/form-data">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">Pembayaran Dari</label>
                            <select id="mitra_id" name="mitra_id" class="tom-select-init" placeholder="No Client Selected">
                                <option value="">No Client Selected</option>
                                @foreach($mitras as $m)
                                    <option value="{{$m->id}}">{{$m->nama}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-muted small">Ref. No.</label>
                            <input type="text" name="ref_no" id="ref_no" class="form-control" placeholder="Contoh: REF-123">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-muted small">Setoran Ke (Akun Keuangan)</label>
                            <select id="akun_keuangan_id" name="akun_keuangan_id" class="form-select">
                                @foreach($accounts as $acc)
                                    <option value="{{$acc->id}}">{{$acc->nama_akun}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold m-0"><i class="fa fa-file-invoice me-2 text-primary"></i>Rincian Tagihan</h6>
                        <button type="button" class="btn btn-outline-primary btn-sm fw-bold px-3" onclick="openInvoiceSelection()">
                            <i class="fa fa-plus-circle me-1"></i> Pilih Invoice Penjualan
                        </button>
                    </div>

                    <div class="table-responsive border rounded-3 mb-4">
                        <table class="table table-hover align-middle mb-0" id="table-selected-invoices">
                            <thead class="bg-light">
                                <tr>
                                    <th class="small fw-bold text-muted py-3 ps-3">No. Invoice</th>
                                    <th class="small fw-bold text-muted py-3">Pelanggan</th>
                                    <th class="small fw-bold text-muted py-3 text-center">Tgl / Jatuh Tempo</th>
                                    <th class="small fw-bold text-muted py-3 text-end">Total Jumlah</th>
                                    <th class="small fw-bold text-muted py-3 text-end">Jumlah Tertagih</th>
                                    <th class="small fw-bold text-muted py-3 text-end pe-3" width="200">Jumlah Terbayar</th>
                                </tr>
                            </thead>
                            <tbody id="invoice-details-body">
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fa fa-info-circle me-1"></i> Belum ada invoice yang dipilih.
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-light border-top-0">
                                <tr>
                                    <td colspan="5" class="text-end fw-bold py-3">Total Jumlah Terbayar</td>
                                    <td class="text-end fw-bold py-3 pe-3 text-primary" id="total-payment-display">IDR 0.00</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 bg-light">
                                <span class="small fw-bold text-muted d-block mb-1">Estimasi dalam SGD* (1 SGD = 13,128.53 IDR)</span>
                                <div class="d-flex align-items-baseline">
                                    <h4 class="fw-bold mb-0 text-dark" id="sgd-display">SGD 0.00</h4>
                                    <button type="button" class="btn btn-link btn-sm p-0 ms-2" onclick="refreshSgd()"><i class="fa fa-sync-alt fa-xs"></i></button>
                                </div>
                                <p class="text-muted small mt-1 mb-0">Estimasi Konversi Nominal</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">Catatan</label>
                            <textarea name="catatan" id="catatan" class="form-control" rows="3" placeholder="Masukkan catatan tambahan..."></textarea>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small d-block">Tanda Tangan dan Meterai (Opsional)</label>
                            <div class="upload-area text-center border p-4 rounded-3 bg-light" id="upload-signature-area" style="cursor: pointer;" onclick="document.getElementById('signature_file').click()">
                                <i class="fa fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                <p class="mb-1 small fw-bold">Unggah Tanda Tangan</p>
                                <p class="mb-0 text-muted" style="font-size: 10px;">Maksimal 20MB (JPEG, PNG)</p>
                            </div>
                            <input type="file" name="signature_file" id="signature_file" class="d-none" accept="image/*">
                            <div id="signature-preview" class="mt-2 text-center d-none">
                                <img src="" class="img-thumbnail" style="max-height: 100px;">
                                <button type="button" class="btn btn-sm btn-outline-danger border-0 d-block mx-auto mt-1" onclick="clearSignature()">Hapus</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light px-4 fw-bold" data-bs-dismiss="modal">BATAL</button>
                <button type="button" onclick="submitReceipt('save')" class="btn btn-primary px-4 fw-bold shadow-sm" id="btn-save-submit">SIMPAN</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Invoice Selection -->
<div class="modal fade" id="modalInvoiceSelection" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header">
                <h5 class="fw-bold mb-0">Pilih Invoice Penjualan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="selection-list-container">
                <!-- Population by JS -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary px-4 fw-bold" onclick="addSelectedInvoices()">Tambahkan Invoice</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('css')
<style>
    .col-fixed-check { width: 50px; flex-shrink: 0; }
    .col-fixed-aksi { width: 80px; flex-shrink: 0; }
    .header-table-custom { background: #f8f9fa; }
    .accordion-item { border: 1px solid #edf2f9 !important; margin-bottom: 10px; border-radius: 12px !important; }
    .row-spacious { padding: 15px 0; }
    .upload-area:hover { border-color: #0d6efd !important; background-color: #f0f7ff !important; }
    .accordion-button:not(.collapsed) { background-color: #fff; color: inherit; }
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    const API_PAYMENT = '{{url("api/payment-api")}}';
    const API_INVOICE = '{{url("api/invoice-api")}}';
    const SGD_RATE = 13128.53;
    let tomMitra;
    let selectedInvoices = [];

    document.addEventListener('DOMContentLoaded', () => {
        tomMitra = new TomSelect("#mitra_id", {
            onChange: () => { selectedInvoices = []; renderSelectedTable(); }
        });

        document.getElementById('signature_file').addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    document.getElementById('upload-signature-area').classList.add('d-none');
                    document.getElementById('signature-preview').classList.remove('d-none');
                    document.querySelector('#signature-preview img').src = ev.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        loadReceiptData();
    });

    function formatIDR(val) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val);
    }

    function formatNumber(val) {
        return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2 }).format(val);
    }

    // --- LOGIKA LIST ---
    async function loadReceiptData(url = API_PAYMENT) {
        const accordion = document.getElementById('receiptAccordion');
        accordion.innerHTML = '<div class="text-center p-5"><div class="spinner-border text-primary"></div></div>';
        
        try {
            const search = document.getElementById('filter-search').value;
            const metode = document.getElementById('filter-metode').value;
            let fetchUrl = new URL(url);
            if (search) fetchUrl.searchParams.append('search', search);
            if (metode) fetchUrl.searchParams.append('metode_pembayaran', metode);

            const res = await fetch(fetchUrl);
            const result = await res.json();
            if (result.success) {
                renderReceiptList(result.data.data);
                renderPagination(result.data);
            }
        } catch (e) {
            accordion.innerHTML = '<div class="alert alert-danger">Gagal memuat data.</div>';
        }
    }

    function renderReceiptList(data) {
        const accordion = document.getElementById('receiptAccordion');
        accordion.innerHTML = '';
        if (data.length === 0) {
            accordion.innerHTML = '<div class="text-center p-5 text-muted">Belum ada data kuitansi.</div>';
            return;
        }

        data.forEach(item => {
            const html = `
            <div class="accordion-item shadow-none">
                <div class="accordion-header bg-white">
                    <div class="d-flex align-items-center">
                        <div class="col-fixed-check text-center text-muted small">${item.id}</div>
                        <button class="accordion-button collapsed shadow-none p-0" type="button" data-bs-toggle="collapse" data-bs-target="#pay-${item.id}">
                            <div class="row flex-grow-1 m-0 align-items-center text-center row-spacious">
                                <div class="col-3 text-start ps-0">
                                    <div class="fw-bold text-dark">${item.nomor_pembayaran}</div>
                                    <div class="text-muted small">${item.invoice?.mitra?.nama || 'N/A'}</div>
                                </div>
                                <div class="col-2 small text-muted">${item.metode_pembayaran}<br><span class="badge badge-soft-primary px-2">${item.akun_keuangan?.nama_akun || 'Settlement'}</span></div>
                                <div class="col-2 small"><span class="badge badge-soft-secondary">Belum Dikirim</span></div>
                                <div class="col-2 text-muted small">${item.tgl_pembayaran}</div>
                                <div class="col-3 text-end fw-bold text-primary pe-4">${formatIDR(item.jumlah_bayar)}</div>
                            </div>
                        </button>
                        <div class="col-fixed-aksi text-center pe-2">
                             <a href="{{ url('sales-receipt/print') }}/${item.id}" target="_blank" class="btn btn-sm btn-light border shadow-none"><i class="fa fa-print"></i></a>
                        </div>
                    </div>
                </div>
                <div id="pay-${item.id}" class="accordion-collapse collapse" data-bs-parent="#receiptAccordion">
                    <div class="accordion-body bg-light border-top p-4">
                        <div class="table-responsive bg-white rounded-3 border">
                            <table class="table table-sm table-hover mb-0 align-middle">
                                <thead class="bg-dark text-white">
                                    <tr style="font-size: 10px;">
                                        <th class="ps-3 py-2">Invoice No.</th>
                                        <th class="py-2">Pelanggan</th>
                                        <th class="py-2">Status Invoice</th>
                                        <th class="py-2">Tipe Invoice</th>
                                        <th class="py-2 text-end">Jumlah</th>
                                        <th class="py-2">Tgl. Invoice</th>
                                        <th class="py-2">Tgl. Jatuh Tempo</th>
                                        <th class="py-2 text-end pe-3">Jumlah Terbayar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="ps-3 fw-bold">${item.invoice?.nomor_invoice || '-'}</td>
                                        <td>${item.invoice?.mitra?.nama || '-'}</td>
                                        <td><span class="badge ${item.invoice?.status_pembayaran == 'Paid' ? 'bg-success':'bg-warning'}">${item.invoice?.status_pembayaran || '-'}</span></td>
                                        <td>${item.invoice?.tipe_invoice || 'Sales Invoice'}</td>
                                        <td class="text-end">${formatNumber(item.invoice?.total_akhir || 0)}</td>
                                        <td>${item.invoice?.tgl_invoice || '-'}</td>
                                        <td>${item.invoice?.tgl_jatuh_tempo || '-'}</td>
                                        <td class="text-end fw-bold text-primary pe-3">${formatIDR(item.jumlah_bayar)}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 small text-muted"><strong>Catatan:</strong> ${item.catatan || '-'}</div>
                    </div>
                </div>
            </div>`;
            accordion.insertAdjacentHTML('beforeend', html);
        });
    }

    // --- LOGIKA MODAL CREATE ---
    function openCreateModal() {
        new bootstrap.Modal(document.getElementById('modalCreateReceipt')).show();
    }

    function openInvoiceSelection() {
        const mitraId = document.getElementById('mitra_id').value;
        if (!mitraId) { alert('Silakan pilih pelanggan terlebih dahulu.'); return; }
        
        const container = document.getElementById('selection-list-container');
        container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';
        new bootstrap.Modal(document.getElementById('modalInvoiceSelection')).show();

        fetch(`${API_INVOICE}?mitra_id=${mitraId}&status_pembayaran=Unpaid`)
            .then(res => res.json())
            .then(result => {
                if (result.success && result.data.data.length > 0) {
                    let html = `<table class="table table-sm"><thead><tr class="bg-light"><th width="30"></th><th>No. Invoice</th><th>Tanggal</th><th class="text-end">Total</th></tr></thead><tbody>`;
                    result.data.data.forEach(inv => {
                        const isChecked = selectedInvoices.find(s => s.id == inv.id) ? 'checked' : '';
                        html += `<tr><td><input type="checkbox" class="invoice-check" value="${inv.id}" data-raw='${JSON.stringify(inv)}' ${isChecked}></td><td class="fw-bold">${inv.nomor_invoice}</td><td>${inv.tgl_invoice}</td><td class="text-end fw-bold">${formatIDR(inv.total_akhir)}</td></tr>`;
                    });
                    html += `</tbody></table>`;
                    container.innerHTML = html;
                } else { container.innerHTML = '<div class="alert alert-info text-center">Tidak ada invoice "Unpaid" ditemukan.</div>'; }
            });
    }

    function addSelectedInvoices() {
        document.querySelectorAll('.invoice-check:checked').forEach(ch => {
            const data = JSON.parse(ch.dataset.raw);
            if (!selectedInvoices.find(s => s.id == data.id)) {
                selectedInvoices.push({ id: data.id, nomor_invoice: data.nomor_invoice, pelanggan: data.mitra?.nama || '-', tgl: data.tgl_invoice, jatuh_tempo: data.tgl_jatuh_tempo || '-', total: data.total_akhir, tertagih: data.total_akhir, bayar: 0 });
            }
        });
        bootstrap.Modal.getInstance(document.getElementById('modalInvoiceSelection')).hide();
        renderSelectedTable();
    }

    function renderSelectedTable() {
        const tbody = document.getElementById('invoice-details-body');
        if (selectedInvoices.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-5 text-muted"><i class="fa fa-info-circle me-1"></i> Belum ada invoice yang dipilih.</td></tr>';
            updateTotals(); return;
        }
        tbody.innerHTML = '';
        selectedInvoices.forEach((inv, i) => {
            tbody.innerHTML += `<tr><td class="ps-3 fw-bold">${inv.nomor_invoice}</td><td>${inv.pelanggan}</td><td class="text-center small">${inv.tgl} / ${inv.jatuh_tempo}</td><td class="text-end">${formatNumber(inv.total)}</td><td class="text-end text-danger fw-bold">${formatNumber(inv.tertagih)}</td><td class="pe-3"><div class="input-group input-group-sm"><span class="input-group-text bg-white border-end-0">IDR</span><input type="number" class="form-control text-end border-start-0" value="${inv.bayar}" onkeyup="updateRowBayar(${i}, this.value)"><button class="btn btn-outline-danger btn-sm border-0 ms-1" onclick="removeInvoice(${i})"><i class="fa fa-trash"></i></button></div></td></tr>`;
        });
        updateTotals();
    }

    function updateRowBayar(i, val) { selectedInvoices[i].bayar = parseFloat(val) || 0; updateTotals(); }
    function removeInvoice(i) { selectedInvoices.splice(i, 1); renderSelectedTable(); }
    function updateTotals() { const t = selectedInvoices.reduce((a, c) => a + c.bayar, 0); document.getElementById('total-payment-display').innerText = formatIDR(t); refreshSgd(); }
    function refreshSgd() { const t = selectedInvoices.reduce((a, c) => a + c.bayar, 0); const s = t / SGD_RATE; document.getElementById('sgd-display').innerText = 'SGD ' + (isNaN(s) ? '0.00' : s.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })); }
    function clearSignature() { document.getElementById('signature_file').value = ''; document.getElementById('upload-signature-area').classList.remove('d-none'); document.getElementById('signature-preview').classList.add('d-none'); }

    async function submitReceipt(action) {
        if (selectedInvoices.length === 0) { alert('Pilih minimal satu invoice.'); return; }
        const t = selectedInvoices.reduce((a, c) => a + c.bayar, 0);
        if (t <= 0) { alert('Masukkan jumlah bayar yang valid.'); return; }

        const btn = document.getElementById('btn-save-submit');
        btn.disabled = true; btn.innerHTML = 'MEMPROSES...';

        try {
            const results = [];
            const tgl = new Date().toISOString().split('T')[0];
            const akunId = document.getElementById('akun_keuangan_id').value;
            const refNo = document.getElementById('ref_no').value;
            const catatan = document.getElementById('catatan').value;

            for (const inv of selectedInvoices) {
                if (inv.bayar > 0) {
                    const payload = { invoice_id: inv.id, nomor_pembayaran: 'PYI/' + Date.now() + '/' + inv.id, tgl_pembayaran: tgl, metode_pembayaran: 'Cash', jumlah_bayar: inv.bayar, akun_keuangan_id: akunId, ref_no: refNo, catatan: catatan };
                    const res = await fetch(API_PAYMENT, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
                    results.push(await res.json());
                }
            }

            if (results.every(r => r.success)) { alert('Kuitansi berhasil disimpan.'); location.reload(); }
            else { alert('Gagal menyimpan beberapa data.'); btn.disabled = false; btn.innerHTML = 'SIMPAN'; }
        } catch (e) { alert('Kesalahan server.'); btn.disabled = false; btn.innerHTML = 'SIMPAN'; }
    }

    function renderPagination(meta) {
        document.getElementById('pagination-info').innerText = `Total ${meta.total} Kuitansi`;
        const container = document.getElementById('pagination-container');
        container.innerHTML = '';
        meta.links.forEach(link => {
            const li = `<li class="page-item ${link.active?'active':''} ${!link.url?'disabled':''}"><a class="page-link" href="javascript:void(0)" onclick="loadReceiptData('${link.url}')">${link.label.replace('&laquo;', '').replace('&raquo;', '')}</a></li>`;
            container.insertAdjacentHTML('beforeend', li);
        });
    }
</script>
@endpush