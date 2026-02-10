@extends('Layout.main')

@section('main')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

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
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
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
                                        <input type="text" id="filter-search" class="form-control"
                                            placeholder="Ketik nomor kuitansi...">
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
                                        <div class="row flex-grow-1 m-0 text-uppercase fw-bold text-muted text-center"
                                            style="font-size: 10px; letter-spacing: 1px;">
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
                                    <nav>
                                        <ul class="pagination pagination-sm mb-0 shadow-sm" id="pagination-container"></ul>
                                    </nav>
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
                    <h5 class="fw-bold mb-0 text-white">Terima Pembayaran untuk Invoice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="form-receipt" enctype="multipart/form-data">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">Pembayaran Dari</label>
                                <select id="mitra_id" name="mitra_id" class="tom-select-init"
                                    placeholder="Pilih Pelanggan...">
                                    <option value="">Loading...</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-muted small">Metode Pembayaran</label>
                                <select id="metode_pembayaran" name="metode_pembayaran" class="form-select">
                                    <option value="Transfer">Transfer</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Cek/Giro">Cek/Giro</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-muted small">Ref. No.</label>
                                <input type="text" name="ref_no" id="ref_no" class="form-control"
                                    placeholder="Contoh: REF-123">
                            </div>
                        </div>

                        <!-- Row 2: Setoran Ke & Catatan -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">Setoran Ke (Akun Keuangan)</label>
                                <select id="akun_keuangan_id" name="akun_keuangan_id" class="form-select">
                                    <option value="">Loading...</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">Catatan</label>
                                <textarea name="catatan" id="catatan" class="form-control" rows="1"
                                    placeholder="Masukkan catatan tambahan..."></textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold m-0"><i class="fa fa-file-invoice me-2 text-primary"></i>Rincian Tagihan</h6>
                            <button type="button" class="btn btn-outline-primary btn-sm fw-bold px-3"
                                onclick="openInvoiceSelection()">
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
                                        <th class="small fw-bold text-muted py-3 text-end pe-3" width="200">Jumlah
                                            Terbayar</th>
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
                                        <td class="text-end fw-bold py-3 pe-3 text-primary" id="total-payment-display">IDR
                                            0.00</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">

                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-muted small d-block">Tanda Tangan dan Meterai
                                    (Opsional)</label>
                                <div class="upload-area text-center border p-4 rounded-3 bg-light"
                                    id="upload-signature-area" style="cursor: pointer;"
                                    onclick="document.getElementById('signature_file').click()">
                                    <i class="fa fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                    <p class="mb-1 small fw-bold">Unggah Tanda Tangan</p>
                                    <p class="mb-0 text-muted" style="font-size: 10px;">Maksimal 20MB (JPEG, PNG)</p>
                                </div>
                                <input type="file" name="signature_file" id="signature_file" class="d-none"
                                    accept="image/*">
                                <div id="signature-preview" class="mt-2 text-center d-none">
                                    <img src="" class="img-thumbnail" style="max-height: 100px;">
                                    <button type="button"
                                        class="btn btn-sm btn-outline-danger border-0 d-block mx-auto mt-1"
                                        onclick="clearSignature()">Hapus</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light px-4 fw-bold" data-bs-dismiss="modal">BATAL</button>
                    <button type="button" onclick="submitReceipt('save')" class="btn btn-primary px-4 fw-bold shadow-sm"
                        id="btn-save-submit">SIMPAN</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Invoice Selection -->
    <div class="modal fade" id="modalInvoiceSelection" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header">
                    <h5 class="fw-bold mb-0 text-white">Pilih Invoice Penjualan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4" id="selection-list-container">
                    <!-- Population by JS -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary px-4 fw-bold" onclick="addSelectedInvoices()">Tambahkan
                        Invoice</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalPrintPreview" tabindex="-1" aria-labelledby="modalPrintPreviewLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-4">
                <div class="modal-header border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" id="modalPrintPreviewLabel">
                        <i class="fa fa-print text-primary me-2"></i> Preview Cetak Invoice
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="ratio ratio-16x9 border rounded bg-light" style="min-height: 70vh;">
                        <iframe id="print-iframe" src="" allowfullscreen></iframe>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary fw-bold px-4 shadow-sm" onclick="triggerPrint()">
                        <i class="fa fa-print me-1"></i> Cetak Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <style>
        .col-fixed-check {
            width: 50px;
            flex-shrink: 0;
        }

        .col-fixed-aksi {
            width: 80px;
            flex-shrink: 0;
        }

        .header-table-custom {
            background: #f8f9fa;
        }

        .accordion-item {
            border: 1px solid #edf2f9 !important;
            margin-bottom: 10px;
            border-radius: 12px !important;
        }

        .row-spacious {
            padding: 15px 0;
        }

        .upload-area:hover {
            border-color: #0d6efd !important;
            background-color: #f0f7ff !important;
        }

        .accordion-button:not(.collapsed) {
            background-color: #fff;
            color: inherit;
        }
    </style>
@endpush

@push('js')
    {{--
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script> --}}
    <script>
        const API_PAYMENT = '{{ url('api/payment-api') }}';
        const API_INVOICE = '{{ url('api/invoice-api') }}';
        const API_MITRA = '{{ url('api/mitra-api') }}';
        const API_FIN_ACC = '{{ url('api/financial-account-api') }}';
        const SGD_RATE = 13128.53;
        let tomMitra;
        let selectedInvoices = [];

        // --- MODAL FUNCTIONS ---
        function openCreateModal() {
            document.getElementById('form-receipt').reset();
            selectedInvoices = [];
            renderSelectedTable();
            if (tomMitra) tomMitra.clear();
            document.getElementById('upload-signature-area').classList.remove('d-none');
            document.getElementById('signature-preview').classList.add('d-none');

            const modal = new bootstrap.Modal(document.getElementById('modalCreateReceipt'));
            modal.show();
        }

        function clearSignature() {
            document.getElementById('signature_file').value = '';
            document.getElementById('upload-signature-area').classList.remove('d-none');
            document.getElementById('signature-preview').classList.add('d-none');
        }

        // --- INVOICE SELECTION ---
        async function openInvoiceSelection() {
            const mitraId = document.getElementById('mitra_id').value;
            if (!mitraId) {
                alert('Pilih Pelanggan terlebih dahulu!');
                return;
            }

            // Get Mitra Name for Search
            let mitraName = '';
            if (tomMitra) {
                const options = tomMitra.options;
                if (options[mitraId]) {
                    mitraName = options[mitraId].text;
                } else {
                    const item = tomMitra.getItem(mitraId);
                    if (item) mitraName = item.textContent;
                }
            }
            mitraName = mitraName.trim();

            const container = document.getElementById('selection-list-container');
            container.innerHTML = '<div class="text-center"><div class="spinner-border"></div></div>';

            const modal = new bootstrap.Modal(document.getElementById('modalInvoiceSelection'));
            modal.show();

            try {
                // Use Mitra Name for search
                const url = `${API_INVOICE}?search=${encodeURIComponent(mitraName)}&tipe_invoice=Sales&status_pembayaran=Unpaid&per_page=100`;

                const res = await fetch(url);
                const result = await res.json();

                if (result.success) {
                    let html = '<div class="list-group">';
                    const invoices = result.data.data.filter(inv =>
                        inv.mitra_id == mitraId &&
                        inv.status_pembayaran !== 'Paid'
                    );

                    if (invoices.length === 0) {
                        container.innerHTML = '<div class="alert alert-info">Tidak ada invoice unpaid untuk pelanggan ini.</div>';
                        return;
                    }

                    invoices.forEach(inv => {
                        const isSelected = selectedInvoices.find(s => s.id == inv.id) ? 'checked' : '';
                        const sisa = inv.total_akhir - (inv.payment_sum_jumlah_bayar || 0);

                        html += `
                                                                                                                <label class="list-group-item d-flex gap-3">
                                                                                                                    <input class="form-check-input flex-shrink-0" type="checkbox" 
                                                                                                                        value="${inv.id}" 
                                                                                                                        data-json='${JSON.stringify(inv).replace(/'/g, "&apos;")}' 
                                                                                                                        ${isSelected}>
                                                                                                                    <span class="pt-1 form-checked-content w-100">
                                                                                                                        <div class="d-flex justify-content-between w-100">
                                                                                                                            <strong>${inv.nomor_invoice}</strong>
                                                                                                                            <small class="text-muted">${inv.tgl_invoice}</small>
                                                                                                                        </div>
                                                                                                                        <div class="d-flex justify-content-between w-100 small">
                                                                                                                            <span>Total: ${formatIDR(inv.total_akhir)}</span>
                                                                                                                            <span class="text-danger fw-bold">Sisa: ${formatIDR(sisa)}</span>
                                                                                                                        </div>
                                                                                                                    </span>
                                                                                                                </label>
                                                                                                            `;
                    });
                    html += '</div>';
                    container.innerHTML = html;
                }
            } catch (e) {
                container.innerHTML = '<div class="text-danger">Gagal memuat invoice.</div>';
                console.error(e);
            }
        }

        function addSelectedInvoices() {
            const checkboxes = document.querySelectorAll('#selection-list-container input[type="checkbox"]:checked');
            checkboxes.forEach(cb => {
                const inv = JSON.parse(cb.getAttribute('data-json'));
                if (!selectedInvoices.find(s => s.id == inv.id)) {
                    const sisa = inv.total_akhir - (inv.payment_sum_jumlah_bayar || 0);
                    selectedInvoices.push({
                        id: inv.id,
                        nomor_invoice: inv.nomor_invoice,
                        pelanggan: inv.mitra?.nama || '-',
                        tgl: inv.tgl_invoice,
                        jatuh_tempo: inv.tgl_jatuh_tempo || '-',
                        total: inv.total_akhir,
                        tertagih: sisa,
                        bayar: sisa
                    });
                }
            });
            renderSelectedTable();
            bootstrap.Modal.getInstance(document.getElementById('modalInvoiceSelection')).hide();
        }

        function renderSelectedTable() {
            const tbody = document.getElementById('invoice-details-body');
            tbody.innerHTML = '';
            let totalBayar = 0;

            if (selectedInvoices.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="text-center py-5 text-muted"><i class="fa fa-info-circle me-1"></i> Belum ada invoice yang dipilih.</td></tr>`;
                document.getElementById('total-payment-display').innerText = 'IDR 0.00';
                return;
            }

            selectedInvoices.forEach((inv, index) => {
                totalBayar += parseFloat(inv.bayar);
                tbody.innerHTML += `
                                                                                                        <tr>
                                                                                                            <td>${inv.nomor_invoice}</td>
                                                                                                            <td>${inv.pelanggan}</td>
                                                                                                            <td class="text-center">${inv.tgl}</td>
                                                                                                            <td class="text-end">${formatNumber(inv.total)}</td>
                                                                                                            <td class="text-end">${formatNumber(inv.tertagih)}</td>
                                                                                                            <td class="text-end pe-3">
                                                                                                                <div class="input-group input-group-sm">
                                                                                                                    <span class="input-group-text">Rp</span>
                                                                                                                    <input type="text" class="form-control text-end" 
                                                                                                                        value="${formatRupiahSimple(inv.bayar)}" 
                                                                                                                        onkeyup="formatRupiahInput(this); updateBayar(${index}, this.value)"
                                                                                                                        >
                                                                                                                    <button class="btn btn-outline-danger" onclick="removeInvoice(${index})"><i class="fa fa-times"></i></button>
                                                                                                                </div>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                    `;
            });

            document.getElementById('total-payment-display').innerText = formatIDR(totalBayar);
        }

        function updateBayar(index, val) {
            // Remove dots to get raw number
            const rawValue = val.replace(/\./g, '');
            selectedInvoices[index].bayar = rawValue;
            renderSelectedTableCountOnly(); // Don't re-render whole table or we lose focus/cursor
        }

        // Optimized render for total only, to avoid input focus loss
        function renderSelectedTableCountOnly() {
            let totalBayar = 0;
            selectedInvoices.forEach(inv => {
                totalBayar += parseFloat(inv.bayar || 0);
            });
            document.getElementById('total-payment-display').innerText = formatIDR(totalBayar);
        }

        function removeInvoice(index) {
            selectedInvoices.splice(index, 1);
            renderSelectedTable();
        }

        // --- SUBMIT ---
        async function submitReceipt(action) {
            if (selectedInvoices.length === 0) {
                alert('Pilih minimal satu invoice!');
                return;
            }

            const akunId = document.getElementById('akun_keuangan_id').value;
            if (!akunId) {
                alert('Pilih Akun Keuangan (Setoran Ke) terlebih dahulu!');
                return;
            }

            const btn = document.getElementById('btn-save-submit');
            const originalText = btn.innerText;
            btn.disabled = true;
            btn.innerText = 'Menyimpan...';

            try {
                const formData = new FormData(document.getElementById('form-receipt'));

                // We handle single payment for single invoice or multiple? 
                // Backend PaymentController::store seems to handle ONE invoice per payment request: 'invoice_id' => 'required'
                // If we want bulk payment, we need to loop calls or update backend.
                // Looking at PaymentController: 
                // public function store(Request $request) { ... 'invoice_id' => 'required' ... }
                // So it only supports one invoice per payment. 
                // For now, let's assume we loop through selectedInvoices and create multiple payments.

                let successCount = 0;
                for (const inv of selectedInvoices) {
                    const payload = new FormData();
                    payload.append('invoice_id', inv.id);
                    payload.append('nomor_pembayaran', 'PAY-' + Date.now() + '-' + Math.floor(Math.random() * 1000)); // Auto gen or input? 
                    // Wait, usually backend auto-gens or we provide. Controller says: 'nomor_pembayaran' => 'required|unique...'
                    // Let's generate a unique one or ask user? The form doesn't have nomor_pembayaran input.
                    // Ideally backend should auto-gen if not provided, or we generate here.
                    // Let's generate a unique sequence here.

                    const uniqueSuffix = Math.random().toString(36).substr(2, 5).toUpperCase();
                    payload.append('nomor_pembayaran', `RCP/${new Date().getFullYear()}/${inv.id}/${uniqueSuffix}`);

                    payload.append('tgl_pembayaran', new Date().toISOString().split('T')[0]); // Today
                    payload.append('metode_pembayaran', document.getElementById('metode_pembayaran').value);

                    payload.append('jumlah_bayar', inv.bayar);
                    payload.append('akun_keuangan_id', formData.get('akun_keuangan_id'));
                    payload.append('catatan', formData.get('catatan'));
                    if (formData.get('ref_no')) payload.append('ref_no', formData.get('ref_no'));

                    // Signature? Backend doesn't seem to handle it in store(), maybe ActivityLog or ignored?
                    // PaymentController::store doesn't look like it saves file. 
                    // We'll skip file upload for now as backend doesn't support it yet in the code I saw.

                    const res = await fetch(API_PAYMENT, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: payload
                    });

                    const json = await res.json();
                    if (json.success) successCount++;
                }

                if (successCount === selectedInvoices.length) {
                    alert('Semua pembayaran berhasil disimpan!');
                    bootstrap.Modal.getInstance(document.getElementById('modalCreateReceipt')).hide();
                    loadReceiptData();
                } else {
                    alert(`Berhasil menyimpan ${successCount} dari ${selectedInvoices.length} pembayaran.`);
                    loadReceiptData();
                }

            } catch (e) {
                console.error(e);
                alert('Terjadi kesalahan saat menyimpan.');
            } finally {
                btn.disabled = false;
                btn.innerText = originalText;
            }
        }

        // --- RENDER LIST ---
        function renderReceiptList(data) {
            const accordion = document.getElementById('receiptAccordion');
            if (!data || data.length === 0) {
                accordion.innerHTML = '<div class="text-center p-5 text-muted">Belum ada data kuitansi.</div>';
                return;
            }

            let html = '';
            data.forEach((item, index) => {
                const invoice = item.invoice || {};
                const mitra = invoice.mitra || {};
                const account = item.akun_keuangan || {};

                html += `
                                                                                                    <div class="accordion-item shadow-sm border-0 overflow-hidden mb-3">
                                                                                                        <h2 class="accordion-header" id="heading${item.id}">
                                                                                                            <button class="accordion-button collapsed bg-white py-3" type="button" data-bs-toggle="collapse"
                                                                                                                data-bs-target="#collapse${item.id}">
                                                                                                                <div class="d-flex align-items-center w-100">
                                                                                                                    <div class="col-fixed-check text-center fw-bold text-muted">${index + 1}</div>
                                                                                                                    <div class="row flex-grow-1 m-0 align-items-center">
                                                                                                                        <div class="col-3">
                                                                                                                            <div class="fw-bold text-dark">${item.nomor_pembayaran}</div>
                                                                                                                            <div class="small text-muted text-uppercase">${mitra.nama || '-'}</div>
                                                                                                                        </div>
                                                                                                                        <div class="col-2">
                                                                                                                            <div class="badge bg-light text-dark border px-2 py-1">${item.metode_pembayaran}</div>
                                                                                                                            <div class="small text-muted mt-1">${account.nama_akun || '-'}</div>
                                                                                                                        </div>
                                                                                                                        <div class="col-2">
                                                                                                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">
                                                                                                                                SETTLED
                                                                                                                            </span>
                                                                                                                        </div>
                                                                                                                        <div class="col-2 small text-muted">
                                                                                                                            <i class="fa fa-calendar-alt me-1"></i> ${item.tgl_pembayaran}
                                                                                                                        </div>
                                                                                                                        <div class="col-3 text-end pe-4">
                                                                                                                            <div class="fw-bold text-dark" style="font-size: 1.1em;">${formatIDR(item.jumlah_bayar)}</div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    <div class="col-fixed-aksi text-center">
                                                                                                                        <a href="{{ url('sales-receipt') }}/${item.id}" class="btn btn-sm btn-light border text-primary rounded-circle shadow-sm"
                                                                                                                            title="Lihat Detail" target="_blank">
                                                                                                                            <i class="fa fa-arrow-right"></i>
                                                                                                                        </a>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </button>
                                                                                                        </h2>
                                                                                                        <div id="collapse${item.id}" class="accordion-collapse collapse" data-bs-parent="#receiptAccordion">
                                                                                                            <div class="accordion-body bg-light border-top p-4">
                                                                                                                <div class="row">
                                                                                                                    <div class="col-md-6">
                                                                                                                        <h6 class="fw-bold text-muted mb-3">Informasi Invoice</h6>
                                                                                                                        <table class="table table-sm table-borderless mb-0">
                                                                                                                            <tr>
                                                                                                                                <td class="text-muted" width="120">No. Invoice</td>
                                                                                                                                <td class="fw-bold">: ${invoice.nomor_invoice || '-'}</td>
                                                                                                                            </tr>
                                                                                                                            <tr>
                                                                                                                                <td class="text-muted">Total Tagihan</td>
                                                                                                                                <td class="fw-bold">: ${formatIDR(invoice.total_akhir)}</td>
                                                                                                                            </tr>
                                                                                                                            <tr>
                                                                                                                                <td class="text-muted">Catatan</td>
                                                                                                                                <td>: ${item.catatan || '-'}</td>
                                                                                                                            </tr>
                                                                                                                        </table>
                                                                                                                    </div>
                                                                                                                    <div class="col-md-6 text-end">
                                                                                                                        <a href="javascript:void(0)" onclick="openPrintPreview(${item.id})"
                                                                                                                           class="btn btn-outline-dark btn-sm fw-bold">
                                                                                                                            <i class="fa fa-print me-1"></i> CETAK KUITANSI
                                                                                                                        </a>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>`;
            });
            accordion.innerHTML = html;
        }

        function renderPagination(meta) {
            const container = document.getElementById('pagination-container');
            const info = document.getElementById('pagination-info');

            if (!meta || !meta.links) return;

            info.innerText = `Menampilkan ${meta.from || 0} sampai ${meta.to || 0} dari ${meta.total || 0} data`;

            let html = '';
            meta.links.forEach(link => {
                const activeClass = link.active ? 'active' : '';
                const disabledClass = link.url ? '' : 'disabled';
                html += `
                                                                                                        <li class="page-item ${activeClass} ${disabledClass}">
                                                                                                            <button class="page-link" onclick="loadReceiptData('${link.url}')" ${!link.url ? 'disabled' : ''}>
                                                                                                                ${link.label}
                                                                                                            </button>
                                                                                                        </li>
                                                                                                    `;
            });
            container.innerHTML = html;
        }

        document.addEventListener('DOMContentLoaded', async () => {
            // Load Dropdowns Data
            await loadDropdowns();

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

            // Check URL Params for Direct Create
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('open_create') === 'true') {
                openCreateModal();

                const mitraId = urlParams.get('mitra_id');
                const invoiceId = urlParams.get('invoice_id');

                // Wait for TomSelect to be ready if it's async (it's not here, but good practice)
                if (mitraId && tomMitra) {
                    tomMitra.skipClear = true;
                    tomMitra.setValue(mitraId);
                    tomMitra.skipClear = false;
                }

                if (invoiceId) {
                    try {
                        const res = await fetch(`${API_INVOICE}/${invoiceId}`);
                        const json = await res.json();
                        if (json.success) {
                            const data = json.data;
                            if (!selectedInvoices.find(s => s.id == data.id)) {
                                const tertagih = data.total_akhir - (data.payment_sum_jumlah_bayar || 0);
                                selectedInvoices.push({
                                    id: data.id,
                                    nomor_invoice: data.nomor_invoice,
                                    pelanggan: data.mitra?.nama || '-',
                                    tgl: data.tgl_invoice,
                                    jatuh_tempo: data.tgl_jatuh_tempo || '-',
                                    total: data.total_akhir,
                                    tertagih: tertagih,
                                    bayar: tertagih // Auto-fill full amount
                                });
                                renderSelectedTable();
                            }
                        }
                    } catch (e) {
                        console.error('Failed to load invoice for receipt', e);
                    }
                }
            }
        });

        async function loadDropdowns() {
            try {
                // Load Mitras
                const resMitra = await fetch(API_MITRA);
                const jsonMitra = await resMitra.json();

                let mitraData = [];
                if (jsonMitra.success) {
                    if (Array.isArray(jsonMitra.data)) {
                        mitraData = jsonMitra.data;
                    } else if (jsonMitra.data && Array.isArray(jsonMitra.data.data)) {
                        mitraData = jsonMitra.data.data;
                    }
                }

                const mitraSelect = document.getElementById('mitra_id');
                // Ensure clear before rebuild
                if (tomMitra) {
                    tomMitra.destroy();
                    tomMitra = null;
                }

                mitraSelect.innerHTML = '<option value="">Pilih Pelanggan...</option>';
                mitraData.forEach(m => {
                    mitraSelect.innerHTML += `<option value="${m.id}">${m.nama}</option>`;
                });

                // Initialize TomSelect for Mitra
                tomMitra = new TomSelect("#mitra_id", {
                    create: false,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    },
                    onChange: () => {
                        if (!tomMitra.skipClear) {
                            selectedInvoices = [];
                            renderSelectedTable();
                        }
                    }
                });

                // Load Financial Accounts
                const resFin = await fetch(API_FIN_ACC);
                const jsonFin = await resFin.json();

                let finData = [];
                if (jsonFin.success) {
                    if (Array.isArray(jsonFin.data)) {
                        finData = jsonFin.data;
                    } else if (jsonFin.data && Array.isArray(jsonFin.data.data)) {
                        finData = jsonFin.data.data;
                    }
                }

                const coaSelect = document.getElementById('akun_keuangan_id');
                // Destroy if already exists (check element property if needed, but for now just init)
                if (coaSelect.tomselect) {
                    coaSelect.tomselect.destroy();
                }

                coaSelect.innerHTML = '<option value="">Pilih Akun...</option>';
                finData.forEach(acc => {
                    coaSelect.innerHTML += `<option value="${acc.id}">${acc.name} (${acc.code})</option>`;
                });

                // Init TomSelect for Akun Keuangan
                new TomSelect("#akun_keuangan_id", {
                    create: false,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    }
                });

            } catch (e) {
                console.error("Failed to load dropdowns", e);
            }
        }

        // --- RUPIAH FORMATTER HELPER ---
        function formatRupiahSimple(angka) {
            if (!angka) return '';
            let number_string = angka.toString().replace(/[^,\d]/g, '');
            let split = number_string.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return rupiah;
        }

        function formatRupiahInput(input) {
            let val = input.value;
            // Allow only digits
            val = val.replace(/[^0-9]/g, '');
            // Convert to integer to remove leading zeros if any, then format
            if (val !== '') {
                val = parseInt(val, 10);
                input.value = formatRupiahSimple(val);
            } else {
                input.value = '';
            }
        }

        function formatIDR(val) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(val);
        }

        function formatNumber(val) {
            return new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(val);
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

        function openPrintPreview(id) {
            const printUrl = `{{ url('sales-receipt/print') }}/${id}`;

            const modalContainer = document.getElementById('modalPrintPreview');

            if (!modalContainer) {
                console.error('Modal container tidak ditemukan di halaman ini.');
                return;
            }

            const iframe = modalContainer.querySelector('iframe');

            if (!iframe) {
                console.error('Elemen iframe tidak ditemukan di dalam modal.');
                alert('Gagal memuat preview cetak.');
                return;
            }

            iframe.src = printUrl;

            const bModal = bootstrap.Modal.getOrCreateInstance(modalContainer);
            bModal.show();
        }

        function triggerPrint() {
            const iframe = document.getElementById('print-iframe');
            if (iframe && iframe.contentWindow) {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            }
        }
    </script>
@endpush