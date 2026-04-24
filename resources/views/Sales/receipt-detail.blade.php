@extends('Layout.main')

@section('main')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <div class="page-wrapper" style="background-color: #f8fafc; min-height: 100vh; font-family: 'Inter', sans-serif;">
        <div class="page-content">
            <div class="container-fluid">

                <!-- Header -->
                <div class="row align-items-center mt-3 mb-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('sales.receipt') }}" class="btn btn-light border me-3 shadow-sm rounded-circle"
                                style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <i class="fa fa-arrow-left"></i>
                            </a>
                            <div>
                                <h4 class="fw-bold text-dark mb-0">Detail Kuitansi</h4>
                                <p class="text-muted small mb-0" id="header-subtitle">Loading...</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <span id="header-status-badge"
                            class="badge bg-success fs-6 px-3 py-2 rounded-pill me-2">SETTLED</span>
                        <a id="btn-print" href="#"
                            class="btn btn-white border fw-bold px-3 shadow-sm text-dark me-2 d-none">
                            <i class="fa fa-print me-1"></i> Cetak
                        </a>
                        <button class="btn btn-primary fw-bold px-4 shadow-sm" onclick="openEditModal()">
                            <i class="fa fa-pencil me-1"></i> Edit
                        </button>
                    </div>
                </div>

                <div class="row" id="main-content" style="display:none;">
                    <!-- Left Column: Receipt Visual -->
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-body p-5 bg-white">
                                <div class="d-flex justify-content-between mb-5">
                                    <div>
                                        <h3 class="fw-bold text-success mb-1">KUITANSI PEMBAYARAN</h3>
                                        <p class="text-muted mb-0" id="pay-no">-</p>
                                    </div>
                                    <div class="text-end">
                                        <h5 class="fw-bold text-dark" id="pay-date">-</h5>
                                        <p class="text-muted small">Tanggal Pembayaran</p>
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <div class="col-md-6">
                                        <label class="text-uppercase text-muted fw-bold small mb-2">Diterima Dari:</label>
                                        <h6 class="fw-bold text-dark mb-1" id="mitra-name">-</h6>
                                        <p class="text-muted small mb-0" id="mitra-address">-</p>
                                        <p class="text-muted small" id="mitra-phone">-</p>
                                    </div>
                                    <div class="col-md-6 text-md-end">
                                        <label class="text-uppercase text-muted fw-bold small mb-2">Metode
                                            Pembayaran:</label>
                                        <h6 class="fw-bold text-dark mb-1" id="pay-method">-</h6>
                                        <p class="mb-1"><span class="text-muted">Setoran Ke:</span> <span class="fw-bold"
                                                id="pay-account">-</span></p>
                                        <p class="mb-1"><span class="text-muted">Ref No:</span> <span class="fw-bold"
                                                id="pay-ref">-</span></p>
                                    </div>
                                </div>

                                <div class="table-responsive mb-4">
                                    <table class="table table-borderless align-middle">
                                        <thead class="bg-light text-secondary text-uppercase small fw-bold">
                                            <tr>
                                                <th class="py-3 ps-4 rounded-start">Untuk Pembayaran Invoice</th>
                                                <th class="py-3 text-end">Total Tagihan</th>
                                                <th class="py-3 text-end pe-4 rounded-end">Jumlah Bayar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="border-bottom border-light">
                                                <td class="ps-4 py-3">
                                                    <div class="fw-bold text-dark" id="inv-no">-</div>
                                                    <div class="small text-muted" id="inv-date">-</div>
                                                </td>
                                                <td class="text-end py-3" id="inv-total">-</td>
                                                <td class="text-end pe-4 py-3 fw-bold text-success" id="val-amount-paid"
                                                    style="font-size: 1.1em;">-</td>
                                            </tr>
                                        </tbody>
                                        <tfoot class="border-top">
                                            <tr>
                                                <td colspan="2" class="text-end py-4 fs-5 fw-bold text-dark">Total
                                                    Pembayaran</td>
                                                <td class="text-end py-4 fs-5 fw-bold text-success pe-4"
                                                    id="val-total-akhir">-</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <div class="row mt-5">
                                    <div class="col-12">
                                        <div class="p-3 bg-light rounded border border-light">
                                            <p class="mb-1 fw-bold text-dark small">Catatan:</p>
                                            <p class="mb-0 text-muted small fst-italic" id="pay-notes">Tidak ada catatan.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Sidebar -->
                    <div class="col-lg-4">
                        <!-- Documents Sidebar -->
                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-header bg-white border-bottom py-3 px-4">
                                <h6 class="fw-bold text-dark mb-0"><i class="fa fa-folder-open text-warning me-2"></i>
                                    Dokumen Pendukung</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item p-3 d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded p-2 me-3 text-primary">
                                                <i class="fa fa-receipt fs-5"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-dark fw-medium" style="font-size: 13px;">Kuitansi Resmi
                                                </h6>
                                                <small class="text-muted" style="font-size: 11px;"
                                                    id="sidebar-pay-no">Otomatis</small>
                                            </div>
                                        </div>
                                        <a id="sidebar-btn-print" href="#" class="btn btn-sm btn-light border"><i
                                                class="fa fa-eye"></i></a>
                                    </div>

                                    <div class="list-group-item p-3 d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded p-2 me-3 text-danger">
                                                <i class="fa fa-file-pdf fs-5"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-dark fw-medium" style="font-size: 13px;">Invoice
                                                    Penjualan
                                                </h6>
                                                <small class="text-muted" style="font-size: 11px;"
                                                    id="sidebar-inv-ref">-</small>
                                            </div>
                                        </div>
                                        <a id="sidebar-btn-inv" href="#"
                                            class="btn btn-sm btn-light border text-primary"><i
                                                class="fa fa-external-link-alt"></i></a>
                                    </div>

                                    <div class="list-group-item p-3 d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded p-2 me-3 text-success">
                                                <i class="fa fa-calculator fs-5"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-dark fw-medium" style="font-size: 13px;">Jurnal
                                                    Akuntansi</h6>
                                                <small class="text-muted" style="font-size: 11px;">Terbentuk
                                                    Otomatis</small>
                                            </div>
                                        </div>
                                        <button class="btn btn-sm btn-light border" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#journal-entries"><i
                                                class="fa fa-chevron-down"></i></button>
                                    </div>

                                    <div class="collapse bg-light" id="journal-entries">
                                        <div class="p-3">
                                            <div class="alert alert-info small mb-0 p-2">
                                                <i class="fa fa-info-circle me-1"></i> Jurnal tercatat di sistem
                                                Accounting.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Activity/Status Sidebar -->
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-header bg-white border-bottom py-3 px-4">
                                <h6 class="fw-bold text-dark mb-0"><i class="fa fa-history text-primary me-2"></i> Riwayat
                                    Status</h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="timeline-simple">
                                    <div class="d-flex mb-3">
                                        <div class="me-3 d-flex flex-column align-items-center">
                                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center"
                                                style="width: 24px; height: 24px; font-size: 10px; flex-shrink: 0;">
                                                <i class="fa fa-check"></i>
                                            </div>
                                            <div class="h-100 border-start border-2 mt-1" style="border-color: #e2e8f0;">
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0 small">Pembayaran Dibuat</h6>
                                            <p class="text-muted small mb-0" id="created-at">-</p>
                                            <p class="text-muted small fst-italic">Dibuat oleh System</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="loading-state" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted mt-2">Memuat data kuitansi...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Receipt Modal (Comprehensive version matching Index) -->
    <div class="modal fade" id="modalEditReceipt" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header">
                    <h5 class="fw-bold mb-0 text-white">Edit Kuitansi Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="editReceiptForm" enctype="multipart/form-data">
                        <input type="hidden" name="payment_id" id="edit-payment-id">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">Pembayaran Dari</label>
                                <select id="edit-mitra_id" name="mitra_id" class="tom-select-init"
                                    placeholder="Pilih Pelanggan...">
                                    <option value="">Loading...</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-muted small">Metode Pembayaran</label>
                                <select id="edit-metode_pembayaran" name="metode_pembayaran" class="form-select">
                                    <option value="Transfer">Transfer</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Cek/Giro">Cek/Giro</option>
                                    <option value="Tunai">Tunai</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-muted small">Ref. No.</label>
                                <input type="text" name="ref_no" id="edit-ref_no" class="form-control"
                                    placeholder="Contoh: REF-123">
                            </div>
                        </div>

                        <!-- Row 2: Setoran Ke & Catatan -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-muted small">Nomor Pembayaran</label>
                                <input type="text" name="nomor_pembayaran" id="edit-nomor_pembayaran"
                                    class="form-control bg-light" readonly required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-muted small">Tanggal Pembayaran</label>
                                <input type="date" name="tgl_pembayaran" id="edit-tgl_pembayaran" class="form-control bg-light"
                                    readonly required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">Setoran Ke (Akun Keuangan)</label>
                                <select id="edit-akun_keuangan_id" name="akun_keuangan_id" class="form-select">
                                    <option value="">Loading...</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label fw-bold text-muted small">Catatan</label>
                                <textarea name="catatan" id="edit-catatan" class="form-control" rows="1"
                                    placeholder="Masukkan catatan tambahan..."></textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold m-0"><i class="fa fa-file-invoice me-2 text-primary"></i>Rincian Tagihan
                            </h6>
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
                    <button type="button" onclick="updateReceipt(event)" class="btn btn-primary px-4 fw-bold shadow-sm"
                        id="btn-edit-submit">SIMPAN PERUBAHAN</button>
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

    <!-- Template for Invoice Selection Item -->
    <template id="tpl-invoice-selection-item">
        <label class="list-group-item d-flex gap-3">
            <input class="form-check-input flex-shrink-0 tpl-checkbox" type="checkbox">
            <span class="pt-1 form-checked-content w-100">
                <div class="d-flex justify-content-between w-100">
                    <strong class="tpl-inv-no"></strong>
                    <small class="text-muted tpl-inv-date"></small>
                </div>
                <div class="d-flex justify-content-between w-100 small">
                    <span class="tpl-inv-total"></span>
                    <span class="text-danger fw-bold tpl-inv-sisa"></span>
                </div>
            </span>
        </label>
    </template>

    <!-- Template for Selected Invoice Row -->
    <template id="tpl-selected-invoice-row">
        <tr>
            <td class="tpl-inv-no"></td>
            <td class="tpl-pelanggan"></td>
            <td class="text-center tpl-tgl"></td>
            <td class="text-end tpl-total"></td>
            <td class="text-end tpl-tertagih"></td>
            <td class="text-end pe-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text small">Rp</span>
                    <input type="text" class="form-control text-end tpl-input-bayar">
                    <button class="btn btn-outline-danger tpl-btn-remove" type="button">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </td>
        </tr>
    </template>

    <div class="modal fade" id="modalPrintPreview" tabindex="-1" aria-labelledby="modalPrintPreviewLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-4">
                <div class="modal-header border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" id="modalPrintPreviewLabel">
                        <i class="fa fa-print text-primary me-2"></i> Preview Cetak Kuitansi
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
@push('css')
    <style>
        .upload-area:hover {
            border-color: #0d6efd !important;
            background-color: #f0f7ff !important;
        }
    </style>
@endpush
@endsection

@push('js')
    <script>
        const paymentId = '{{ $id }}';
        const API_URL = '{{ url('api/payment-api') }}';
        const API_INVOICE = '{{ url('api/invoice-api') }}';
        const API_MITRA = '{{ url('api/mitra-api') }}';
        const API_FIN_ACC = '{{ url('api/financial-account-api') }}';
        let currentPaymentData = null;
        let tomMitra, tomFin;
        let selectedInvoices = [];

        async function loadPaymentDetail() {
            try {
                const response = await fetch(`${API_URL}/${paymentId}`);
                const result = await response.json();

                if (result.success) {
                    renderPayment(result.data);
                    document.getElementById('loading-state').style.display = 'none';
                    document.getElementById('main-content').style.display = 'flex';
                } else {
                    alert('Kuitansi tidak ditemukan');
                }
            } catch (e) {
                console.error(e);
                alert('Gagal memuat data');
            }
        }

        function renderPayment(data) {
            // Store current payment data for editing
            currentPaymentData = data;

            // Header
            document.getElementById('header-subtitle').textContent = `No. ${data.nomor_pembayaran}`;

            const btnPrint = document.getElementById('btn-print');
            btnPrint.href = 'javascript:void(0)';
            btnPrint.onclick = () => openPrintPreview(data.id);
            btnPrint.classList.remove('d-none');

            // Main Visual
            document.getElementById('pay-no').textContent = `#${data.nomor_pembayaran}`;
            document.getElementById('pay-date').textContent = formatDate(data.tgl_pembayaran);

            // Mitra
            if (data.invoice && data.invoice.mitra) {
                document.getElementById('mitra-name').textContent = data.invoice.mitra.nama;
                document.getElementById('mitra-address').textContent = data.invoice.mitra.alamat || '-';
                document.getElementById('mitra-phone').textContent = data.invoice.mitra.no_telp || '-';
            } else {
                document.getElementById('mitra-name').textContent = 'Umum';
            }

            document.getElementById('pay-method').textContent = data.metode_pembayaran || '-';
            document.getElementById('pay-account').textContent = data.akun_keuangan?.nama_akun || '-';
            document.getElementById('pay-ref').textContent = data.ref_no || '-';

            // Invoice Info
            if (data.invoice) {
                document.getElementById('inv-no').textContent = data.invoice.nomor_invoice;
                document.getElementById('inv-date').textContent = `Tgl Invoice: ${formatDate(data.invoice.tgl_invoice)}`;
                document.getElementById('inv-total').textContent = formatIDR(data.invoice.total_akhir);

                document.getElementById('sidebar-inv-ref').textContent = data.invoice.nomor_invoice;
                document.getElementById('sidebar-btn-inv').href = `{{ url('sales') }}/${data.invoice.id}`;
            }

            // Totals
            document.getElementById('val-amount-paid').textContent = formatIDR(data.jumlah_bayar);
            document.getElementById('val-total-akhir').textContent = formatIDR(data.jumlah_bayar);

            document.getElementById('pay-notes').textContent = data.catatan || 'Tidak ada catatan.';

            // Sidebar
            document.getElementById('sidebar-pay-no').textContent = data.nomor_pembayaran;

            const sidebarBtnPrint = document.getElementById('sidebar-btn-print');
            sidebarBtnPrint.href = 'javascript:void(0)';
            sidebarBtnPrint.onclick = () => openPrintPreview(data.id);

            document.getElementById('created-at').textContent = new Date(data.created_at).toLocaleString('id-ID');
        }

        function formatIDR(val) {
            if (!val) return 'Rp 0';
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(val);
        }

        function formatDate(dateStr) {
            if (!dateStr) return '-';
            const d = new Date(dateStr);
            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = d.getFullYear();
            return `${day}/${month}/${year}`;
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

        function openEditModal() {
            if (!currentPaymentData) {
                alert('Data kuitansi belum dimuat.');
                return;
            }

            // Reset selected invoices
            selectedInvoices = [];
            
            // Fill basic info
            document.getElementById('edit-payment-id').value = currentPaymentData.id;
            document.getElementById('edit-nomor_pembayaran').value = currentPaymentData.nomor_pembayaran;
            
            // Set date from created_at (format YYYY-MM-DD)
            const createdAtDate = currentPaymentData.created_at.split('T')[0];
            document.getElementById('edit-tgl_pembayaran').value = createdAtDate;
            
            document.getElementById('edit-metode_pembayaran').value = currentPaymentData.metode_pembayaran || 'Transfer';
            document.getElementById('edit-ref_no').value = currentPaymentData.ref_no || '';
            document.getElementById('edit-catatan').value = currentPaymentData.catatan || '';

            // Set TomSelect Mitra
            if (tomMitra && currentPaymentData.invoice && currentPaymentData.invoice.mitra_id) {
                tomMitra.skipClear = true;
                tomMitra.setValue(currentPaymentData.invoice.mitra_id);
                tomMitra.skipClear = false;
            }

            // Set TomSelect Account
            if (tomFin && currentPaymentData.akun_keuangan_id) {
                tomFin.setValue(currentPaymentData.akun_keuangan_id);
            }

            // Add the currently linked invoice to selectedInvoices
            if (currentPaymentData.invoice) {
                const inv = currentPaymentData.invoice;
                // Calculate tertagih: sisa tagihan before THIS payment
                // Actually, for editing, tertagih should be (total - (sum_all - current_payment))
                const totalPaidOthers = (inv.payment_sum_jumlah_bayar || 0) - currentPaymentData.jumlah_bayar;
                const tertagih = inv.total_akhir - totalPaidOthers;

                selectedInvoices.push({
                    id: inv.id,
                    nomor_invoice: inv.nomor_invoice,
                    pelanggan: inv.mitra?.nama || '-',
                    tgl: inv.tgl_invoice,
                    jatuh_tempo: inv.tgl_jatuh_tempo || '-',
                    total: inv.total_akhir,
                    tertagih: tertagih,
                    bayar: currentPaymentData.jumlah_bayar
                });
            }

            renderSelectedTable();

            // Signature handle
            clearSignature();

            const bModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEditReceipt'), {
                focus: false
            });
            bModal.show();
        }

        async function updateReceipt(event) {
            if (event) event.preventDefault();

            if (selectedInvoices.length === 0) {
                alert('Pilih minimal satu invoice!');
                return;
            }

            const btn = document.getElementById('btn-edit-submit');
            const originalText = btn.innerText;
            btn.disabled = true;
            btn.innerText = 'Menyimpan...';

            try {
                // For editing, we assume only one invoice is being updated for now, 
                // matching how the detail view works. 
                const inv = selectedInvoices[0];
                const form = document.getElementById('editReceiptForm');
                const formData = new FormData(form);

                // Use PUT for update
                // Note: Standard HTML forms don't support PUT, and FormData might not work directly with PUT in some frameworks 
                // if not handled by method spoofing. But our API PaymentController::update handles PUT.
                // However, PUT with FormData (multipart) is tricky in PHP/Laravel. 
                // Usually we use POST with _method=PUT or send as JSON.
                // Since we might have a file (signature), we'll use POST with _method=PUT.

                formData.append('_method', 'PUT');
                formData.append('invoice_id', inv.id);
                formData.append('jumlah_bayar', inv.bayar);
                formData.append('ref_no', document.getElementById('edit-ref_no').value);
                formData.append('akun_keuangan_id', document.getElementById('edit-akun_keuangan_id').value);

                const response = await fetch(`${API_URL}/${paymentId}`, {
                    method: 'POST', // Use POST with spoofing for multipart compatibility
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    alert('Kuitansi berhasil diperbarui!');
                    bootstrap.Modal.getInstance(document.getElementById('modalEditReceipt')).hide();
                    loadPaymentDetail();
                } else {
                    let msg = result.message || 'Gagal memperbarui kuitansi';
                    if (result.errors) {
                        const errorDetails = Object.values(result.errors).flat().join('\n');
                        msg += ':\n' + errorDetails;
                    }
                    alert(msg);
                }
            } catch (e) {
                console.error(e);
                alert('Terjadi kesalahan saat menghubungi server.');
            } finally {
                btn.disabled = false;
                btn.innerText = originalText;
            }
        }

        async function loadDropdowns() {
            try {
                // Load Mitras
                const resMitra = await fetch(`${API_MITRA}?per_page=1000`);
                const jsonMitra = await resMitra.json();
                let mitraData = jsonMitra.success ? (jsonMitra.data?.data || jsonMitra.data || []) : [];

                const mitraSelect = document.getElementById('edit-mitra_id');
                mitraSelect.innerHTML = '<option value="">Pilih Pelanggan...</option>';
                mitraData.forEach(m => {
                    mitraSelect.innerHTML += `<option value="${m.id}">${m.nama}</option>`;
                });

                tomMitra = new TomSelect("#edit-mitra_id", {
                    create: false,
                    onChange: () => {
                        if (!tomMitra.skipClear) {
                            selectedInvoices = [];
                            renderSelectedTable();
                        }
                    }
                });

                // Load Accounts
                const resFin = await fetch(API_FIN_ACC);
                const jsonFin = await resFin.json();
                let finData = jsonFin.success ? (jsonFin.data?.data || jsonFin.data || []) : [];

                const coaSelect = document.getElementById('edit-akun_keuangan_id');
                coaSelect.innerHTML = '<option value="">Pilih Akun...</option>';
                finData.forEach(acc => {
                    const accName = acc.nama_akun || acc.name || 'Unnamed';
                    coaSelect.innerHTML += `<option value="${acc.id}">${accName}</option>`;
                });

                tomFin = new TomSelect("#edit-akun_keuangan_id", { create: false });

            } catch (e) {
                console.error("Failed to load dropdowns:", e);
            }
        }

        async function openInvoiceSelection() {
            // Force close TomSelect to prevent it from popping up when modal focus shifts
            if (typeof tomMitra !== 'undefined' && tomMitra) {
                tomMitra.close();
                tomMitra.blur();
            }

            const mitraId = document.getElementById('edit-mitra_id').value;
            if (!mitraId) {
                alert('Pilih Pelanggan terlebih dahulu!');
                return;
            }

            const container = document.getElementById('selection-list-container');
            container.innerHTML = '<div class="text-center"><div class="spinner-border"></div></div>';

            const modal = new bootstrap.Modal(document.getElementById('modalInvoiceSelection'), {
                focus: false
            });
            modal.show();

            try {
                const res = await fetch(`${API_INVOICE}?mitra_id=${mitraId}&tipe_invoice=Sales&per_page=100`);
                const result = await res.json();

                if (result.success) {
                    const listContainer = document.createElement('div');
                    listContainer.className = 'list-group';
                    const template = document.getElementById('tpl-invoice-selection-item');
                    
                    const invoices = result.data.data.filter(inv => 
                        inv.status_pembayaran !== 'Paid' && inv.status_dok === 'Approved'
                    );

                    if (invoices.length === 0) {
                        container.innerHTML = '<div class="alert alert-info">Tidak ada invoice tertagih.</div>';
                        return;
                    }

                    invoices.forEach(inv => {
                        const clone = template.content.cloneNode(true);
                        const isSelected = selectedInvoices.find(s => s.id == inv.id);
                        const sisa = inv.total_akhir - (inv.payment_sum_jumlah_bayar || 0);

                        const checkbox = clone.querySelector('.tpl-checkbox');
                        checkbox.value = inv.id;
                        checkbox.checked = !!isSelected;
                        checkbox.setAttribute('data-json', JSON.stringify(inv));

                        clone.querySelector('.tpl-inv-no').textContent = inv.nomor_invoice;
                        clone.querySelector('.tpl-inv-date').textContent = formatDateToDMY(inv.tgl_invoice);
                        clone.querySelector('.tpl-inv-total').textContent = `Total: ${formatIDR(inv.total_akhir)}`;
                        clone.querySelector('.tpl-inv-sisa').textContent = `Sisa: ${formatIDR(sisa)}`;

                        listContainer.appendChild(clone);
                    });
                    container.innerHTML = '';
                    container.appendChild(listContainer);
                }
            } catch (e) {
                container.innerHTML = '<div class="text-danger">Gagal memuat invoice.</div>';
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
                tbody.innerHTML = '<tr><td colspan="6" class="text-center py-5 text-muted">Belum ada invoice dipilih.</td></tr>';
                document.getElementById('total-payment-display').innerText = 'IDR 0.00';
                return;
            }

            const template = document.getElementById('tpl-selected-invoice-row');
            selectedInvoices.forEach((inv, index) => {
                const clone = template.content.cloneNode(true);
                totalBayar += parseFloat(inv.bayar) || 0;

                clone.querySelector('.tpl-inv-no').textContent = inv.nomor_invoice;
                clone.querySelector('.tpl-pelanggan').textContent = inv.pelanggan;
                clone.querySelector('.tpl-tgl').textContent = formatDateToDMY(inv.tgl);
                clone.querySelector('.tpl-total').textContent = formatNumber(inv.total);
                clone.querySelector('.tpl-tertagih').textContent = formatNumber(inv.tertagih);

                const inputBayar = clone.querySelector('.tpl-input-bayar');
                inputBayar.value = formatRupiahSimple(inv.bayar);
                inputBayar.oninput = (e) => {
                    formatRupiahInput(e.target);
                    // Strip all non-digits for the raw value
                    let rawValue = e.target.value.replace(/\D/g, '');
                    
                    if (parseFloat(rawValue) > inv.tertagih) {
                        alert(`Jumlah bayar tidak boleh melebihi sisa tagihan (${formatIDR(inv.tertagih)})`);
                        rawValue = inv.tertagih;
                        e.target.value = formatRupiahSimple(rawValue);
                    }
                    
                    selectedInvoices[index].bayar = rawValue || 0;
                    renderSelectedTableCountOnly();
                };

                clone.querySelector('.tpl-btn-remove').onclick = () => {
                    selectedInvoices.splice(index, 1);
                    renderSelectedTable();
                };
                tbody.appendChild(clone);
            });
            document.getElementById('total-payment-display').innerText = formatIDR(totalBayar);
        }

        function renderSelectedTableCountOnly() {
            let total = 0;
            selectedInvoices.forEach(inv => total += parseFloat(inv.bayar) || 0);
            document.getElementById('total-payment-display').innerText = formatIDR(total);
        }

        function clearSignature() {
            document.getElementById('signature_file').value = '';
            document.getElementById('upload-signature-area').classList.remove('d-none');
            document.getElementById('signature-preview').classList.add('d-none');
        }

        function formatRupiahSimple(angka) {
            if (angka === undefined || angka === null || angka === '') return '';
            
            // Convert to string to handle various input types
            let val = angka.toString();
            
            // If it contains a dot, it's likely a decimal from the API (e.g., "30000.00")
            // We strip the decimals for IDR display to avoid inflation (30000.00 -> 3000000)
            if (val.includes('.')) {
                val = val.split('.')[0];
            }
            
            // Remove all non-digits (like thousands separators if already present)
            val = val.replace(/\D/g, '');
            
            if (!val) return '0';
            
            // Format as IDR integer
            return new Intl.NumberFormat('id-ID').format(parseInt(val, 10));
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

        function formatNumber(val) {
            return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2 }).format(val);
        }

        function formatDateToDMY(dateStr) {
            if (!dateStr) return '-';
            const d = new Date(dateStr);
            return `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`;
        }

        function triggerPrint() {
            const iframe = document.getElementById('print-iframe');
            if (iframe && iframe.contentWindow) {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadPaymentDetail();
            loadDropdowns();

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
        });
    </script>
@endpush
