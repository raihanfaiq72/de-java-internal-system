@extends('Layout.main')

@section('main')
    <div class="page-wrapper" style="background-color: #f8fafc; min-height: 100vh; font-family: 'Inter', sans-serif;">
        <div class="page-content">
            <div class="container-fluid">

                <!-- Header -->
                <div class="row align-items-center mb-3 mb-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('purchase.receipt') }}"
                                class="btn btn-light border me-3 shadow-sm rounded-circle"
                                style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <i class="fa fa-arrow-left"></i>
                            </a>
                            <div>
                                <h4 class="fw-bold text-dark mb-0">Detail Kuitansi Pembelian</h4>
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
                        <button class="btn btn-primary fw-bold px-4 shadow-sm"
                            onclick="alert('Fitur Edit akan segera hadir')">
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
                                        <label class="text-uppercase text-muted fw-bold small mb-2">Dibayarkan
                                            Kepada:</label>
                                        <h6 class="fw-bold text-dark mb-1" id="mitra-name">-</h6>
                                        <p class="text-muted small mb-0" id="mitra-address">-</p>
                                        <p class="text-muted small" id="mitra-phone">-</p>
                                    </div>
                                    <div class="col-md-6 text-md-end">
                                        <label class="text-uppercase text-muted fw-bold small mb-2">Metode
                                            Pembayaran:</label>
                                        <h6 class="fw-bold text-dark mb-1" id="pay-method">-</h6>
                                        <p class="mb-1"><span class="text-muted">Diambil Dari:</span> <span
                                                class="fw-bold" id="pay-account">-</span></p>
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
                                                <h6 class="mb-0 text-dark fw-medium" style="font-size: 13px;">Kuitansi
                                                    Pembayaran
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
                                                    Pembelian
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
@endsection

@push('js')
    <script>
        const paymentId = '{{ $id }}';
        const API_URL = '{{ url('api/payment-api') }}';

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
                document.getElementById('sidebar-btn-inv').href = `{{ url('purchase') }}/${data.invoice.id}`;
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
            const printUrl = `{{ url('purchase-receipt/print') }}/${id}`;

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

        document.addEventListener('DOMContentLoaded', () => {
            loadPaymentDetail();
        });
    </script>
@endpush
