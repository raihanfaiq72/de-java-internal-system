@extends('Layout.main')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap"
        rel="stylesheet">
    <style>
        .f-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 5px;
            display: block;
        }

        .f-mono {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
        }

        .timeline-simple {
            position: relative;
            padding-left: 10px;
        }

        /* Invoice Standard Styling */
        .card.rounded-4 {
            border-radius: 1rem !important;
            border: 1px solid #cbd5e0 !important;
        }

        .shadow-sm {
            box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .075) !important;
        }

        .bg-light {
            background-color: #f1f5f9 !important;
        }

        .border-dashed {
            border-style: dashed !important;
            border-width: 2px !important;
            border-color: #cbd5e0 !important;
        }

        /* Ensure card header respect border radius */
        .card-header:first-child {
            border-radius: 1rem 1rem 0 0 !important;
        }

        .table-borderless thead th {
            border: 0;
        }
    </style>
@endpush

@section('main')
    <div class="page-wrapper" style="background-color: #f8fafc; min-height: 100vh; font-family: 'Inter', sans-serif;">
        <script>
            window.suratJalanId = '{{ $id ?? $surat_jalan->id }}';
            window.API_URL = '{{ url('api/surat-jalan-api') }}';
            window.masterProduk = [];
            window.financeApp = window.financeApp || {
                API_URL: window.API_URL,
                formatIDR: (val) => new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(val || 0),
                formatDate: (dateStr) => {
                    if (!dateStr) return '-';
                    const d = new Date(dateStr);
                    return d.toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
                }
            };
        </script>

        <div class="page-content">
            <div class="container-fluid">

                <!-- Header Section -->
                <div class="row align-items-center mt-3 mb-4">
                    <div class="col-md-5">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('surat-jalan') }}" class="btn btn-white border me-3 shadow-sm rounded-circle"
                                style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <i class="fa fa-arrow-left text-dark"></i>
                            </a>
                            <div>
                                <h4 class="fw-bold text-dark mb-0">Detail Surat Jalan</h4>
                                <p class="text-muted small mb-0 f-mono fw-bold" id="header-subtitle">Loading...</p>
                            </div>
                        </div>
                    </div>
                    <div
                        class="col-md-7 text-md-end mt-3 mt-md-0 d-flex justify-content-md-end align-items-center gap-2 flex-wrap">
                        <span id="header-status-badge"
                            class="badge bg-secondary fs-6 px-3 py-2 rounded-pill">Loading...</span>

                        <button id="btn-convert" class="btn btn-success fw-bold px-3 shadow-sm d-none"
                            onclick="openConvertModal()">
                            <i class="fa fa-file-invoice me-1"></i> Buat Invoice
                        </button>

                        <a id="btn-print" href="#"
                            class="btn btn-white border fw-bold px-3 shadow-sm text-dark d-none">
                            <i class="fa fa-print me-1"></i> Cetak
                        </a>
                        <button id="btn-edit-header" class="btn btn-warning text-dark fw-bold px-3 shadow-sm"
                            onclick="openSuratJalanModal(suratJalanId, 'edit')">
                            <i class="fa fa-pencil me-1"></i> Edit Data
                        </button>
                    </div>
                </div>

                <div class="row" id="main-content" style="display:none;">
                    <!-- Left Column: Document Visual -->
                    <div class="col-lg-8">
                        <div class="card shadow-sm rounded-4 overflow-hidden mb-4">
                            <div class="card-body p-5 bg-white">
                                <!-- Document Header -->
                                <div class="d-flex justify-content-between mb-5">
                                    <div>
                                        <h3 class="fw-bold text-primary mb-1">SURAT JALAN</h3>
                                        <p class="text-muted mb-0" id="inv-no">-</p>
                                    </div>
                                    <div class="text-end">
                                        <h5 class="fw-bold text-dark mb-0" id="inv-date">-</h5>
                                        <p class="text-muted small">Tanggal Surat Jalan</p>
                                    </div>
                                </div>

                                <!-- Parties Section -->
                                <div class="row mb-5">
                                    <div class="col-md-6">
                                        <label class="text-uppercase text-muted fw-bold small mb-2">Tujuan
                                            Pengiriman:</label>
                                        <h6 class="fw-bold text-dark mb-1" id="mitra-name">-</h6>
                                        <p class="text-muted small mb-0" id="mitra-address">-</p>
                                        <p class="text-muted small" id="mitra-phone">-</p>
                                    </div>
                                    <div class="col-md-6 text-md-end">
                                        <label class="text-uppercase text-muted fw-bold small mb-2">Detail
                                            Pengiriman:</label>
                                        <p class="mb-1"><span class="text-muted">Salesman/Ref:</span> <span
                                                class="fw-bold text-dark" id="inv-ref">-</span></p>
                                        <p class="mb-1"><span class="text-muted">No. Referensi:</span> <span
                                                class="fw-bold text-dark" id="val-ref-no">-</span></p>
                                    </div>
                                </div>

                                <!-- Items Table -->
                                <div class="table-responsive mb-4">
                                    <table class="table table-borderless align-middle">
                                        <thead class="bg-light text-secondary text-uppercase small fw-bold">
                                            <tr>
                                                <th class="py-3 ps-4 rounded-start">Item Produk</th>
                                                <th class="py-3 text-center">Qty</th>
                                                <th class="py-3 text-center pe-4 rounded-end">Satuan</th>
                                            </tr>
                                        </thead>
                                        <tbody id="items-body">
                                            <!-- JS populated -->
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Notes Section -->
                                <div class="row mt-5">
                                    <div class="col-12">
                                        <div class="p-3 bg-light rounded border border-light">
                                            <p class="mb-1 fw-bold text-dark small">Keterangan / Catatan:</p>
                                            <p class="mb-0 text-muted small fst-italic" id="inv-notes">Tidak ada catatan.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Sidebar -->
                    <div class="col-lg-4">
                        <!-- Status Card -->
                        <div class="card shadow-sm rounded-4 mb-4 overflow-hidden">
                            <div class="card-header bg-white border-bottom py-3 px-4">
                                <h6 class="fw-bold text-dark mb-0">Status Dokumen</h6>
                            </div>
                            <div class="card-body p-4 text-center">
                                <div id="status-card-content" class="p-3 rounded-4 bg-light border">
                                    <h5 class="fw-bold text-dark mb-0" id="sidebar-status-text">-</h5>
                                </div>
                            </div>
                        </div>

                        <!-- Activity/History Sidebar -->
                        <div class="card shadow-sm rounded-4 overflow-hidden">
                            <div
                                class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                                <h6 class="fw-bold text-dark mb-0"><i class="fa fa-history text-primary me-2"></i> Riwayat
                                    Aktivitas</h6>
                                <span class="badge bg-light text-dark rounded-pill border fw-normal" id="log-count">0</span>
                            </div>
                            <div class="card-body p-4">
                                <div class="timeline-simple" id="timeline-body">
                                    <!-- Dynamic content -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="loading-state" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted mt-3 fw-medium">Sedang menyiapkan data surat jalan...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Convert to Invoice Modal -->
    <div class="modal fade" id="convertModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-success"><i class="fa fa-file-invoice me-2"></i>Konversi ke
                        Invoice Penjualan</h5>
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
                            <input type="text" id="conv_nomor" class="form-control fw-bold"
                                placeholder="INV/2026/...">
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
                            <input type="text" id="conv_ref" class="form-control"
                                placeholder="Otomatis dari surat jalan">
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark mb-2 mt-3">Harga Barang</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="conv-items-table">
                            <thead class="bg-light text-uppercase text-secondary fw-bold" style="font-size:11px;">
                                <tr>
                                    <th class="ps-3" width="35%">Produk</th>
                                    <th class="text-center" width="15%">Qty</th>
                                    <th class="text-end" width="25%">Harga Satuan</th>
                                    <th class="text-end pe-3" width="25%">Total</th>
                                </tr>
                            </thead>
                            <tbody id="conv-items-body"></tbody>
                            <tfoot>
                                <tr class="fw-bold">
                                    <td colspan="3" class="text-end pe-3 fw-bold">GRAND TOTAL</td>
                                    <td class="text-end pe-3 fw-bold" id="conv-grand-total">Rp 0</td>
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

    <div class="modal fade" id="modalPrintPreview" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="modal-header bg-dark text-white border-bottom-0 py-5 px-4">
                    <h5 class="modal-title fw-bold text-warning">
                        <i class="fa fa-print me-2"></i> Preview Cetak Surat Jalan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 bg-light">
                    <div style="height: 80vh;">
                        <iframe id="print-iframe" src="" class="w-100 h-100 border-0" allowfullscreen></iframe>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top mt-2 py-3 px-4">
                    <button type="button" class="btn btn-light fw-bold px-4 rounded-pill"
                        data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary fw-bold px-5 rounded-pill shadow-sm"
                        onclick="triggerPrint()">
                        <i class="fa fa-print me-2"></i> Cetak Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>

    @include('SuratJalan.Modal.modal-fullscreen')
@endsection

@push('js')
    <script>
        let sjDetailData = null;

        async function loadSuratJalanDetail() {
            try {
                const res = await fetch(`${API_URL}/${suratJalanId}`);
                const result = await res.json();
                if (result.success) {
                    sjDetailData = result.data;
                    renderSuratJalan(sjDetailData);
                    document.getElementById('loading-state').style.display = 'none';
                    document.getElementById('main-content').style.display = 'flex';
                } else {
                    alert('Data tidak ditemukan');
                }
            } catch (e) {
                console.error(e);
                alert('Gagal memuat data');
            }
        }

        function renderSuratJalan(data) {
            // Header
            document.getElementById('header-subtitle').textContent = `No: ${data.nomor_invoice}`;

            const badge = document.getElementById('header-status-badge');
            const isSent = data.status_dok === 'Sent';
            badge.textContent = isSent ? 'Sudah Dikonversi' : 'Draft';
            badge.className = `badge fs-6 px-3 py-2 rounded-pill ${isSent ? 'bg-success' : 'bg-warning text-dark'}`;

            document.getElementById('sidebar-status-text').textContent = isSent ? 'Terkonversi ke Invoice' :
                'Draft Pengiriman';

            const btnConvert = document.getElementById('btn-convert');
            if (btnConvert) {
                if (isSent) btnConvert.classList.add('d-none');
                else btnConvert.classList.remove('d-none');
            }

            const btnPrint = document.getElementById('btn-print');
            if (btnPrint) {
                btnPrint.onclick = () => openPrintPreview(data.id);
                btnPrint.classList.remove('d-none');
            }

            const btnEdit = document.getElementById('btn-edit-header');
            if (btnEdit) {
                if (data.status_dok === 'Draft') btnEdit.classList.remove('d-none');
                else btnEdit.classList.add('d-none');
            }

            // Main Document Info
            document.getElementById('inv-no').textContent = data.nomor_invoice;
            document.getElementById('inv-date').textContent = financeApp.formatDate(data.tgl_invoice);
            document.getElementById('val-ref-no').textContent = data.ref_no || '-';

            // Mitra Info
            if (data.mitra) {
                document.getElementById('mitra-name').textContent = data.mitra.nama;
                document.getElementById('mitra-address').textContent = data.mitra.alamat || '-';
                document.getElementById('mitra-phone').textContent = data.mitra.nomor_hp || data.mitra.no_hp || data.mitra
                    .no_telp || '-';
            }
            document.getElementById('inv-ref').textContent = data.sales?.name || 'Tanpa Salesperson';

            // Items Rendering
            const tbody = document.getElementById('items-body');
            tbody.innerHTML = '';
            if (data.items) {
                data.items.forEach((item, index) => {
                    tbody.innerHTML += `
                    <tr>
                        <td class="ps-4 py-3">
                            <div class="fw-bold text-dark mb-0">${item.product?.nama_produk || item.nama_produk_manual || '-'}</div>
                            ${item.deskripsi_produk ? `<div class="small text-muted fst-italic">${item.deskripsi_produk}</div>` : ''}
                        </td>
                        <td class="text-center py-3 fw-bold text-dark">${parseFloat(item.qty).toLocaleString('id-ID')}</td>
                        <td class="text-center py-3 pe-4 text-muted small text-uppercase">${item.product?.unit?.nama_unit || item.satuan || 'Pcs'}</td>
                    </tr>
                `;
                });
            }

            document.getElementById('inv-notes').textContent = data.keterangan ||
                'Tidak ada catatan tambahan untuk pengiriman ini.';

            // Activity Log
            document.getElementById('log-count').textContent = data.activities ? data.activities.length : 0;
            renderTimeline(data.activities);
        }

        function renderTimeline(activities) {
            const body = document.getElementById('timeline-body');
            body.innerHTML = '';

            if (!activities || activities.length === 0) {
                body.innerHTML = '<p class="text-muted small text-center py-4">Belum ada riwayat aktivitas.</p>';
                return;
            }

            // Newest to oldest
            const sorted = [...activities].sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

            sorted.forEach((log, index) => {
                const isLast = index === sorted.length - 1;
                const dateObj = new Date(log.created_at);
                const dateStr = dateObj.toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
                const timeStr = dateObj.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit'
                });

                let title = log.tindakan;
                let icon = 'fa-info-circle';
                let colorClass = 'bg-primary';

                if (log.tindakan === 'Create') {
                    title = 'Dokumen Dibuat';
                    icon = 'fa-plus';
                    colorClass = 'bg-info';
                } else if (log.tindakan === 'Update') {
                    title = 'Dokumen Diperbarui';
                    icon = 'fa-pencil-alt';
                    colorClass = 'bg-primary';

                    if (log.data_sesudah && log.data_sesudah.status_dok) {
                        title = `Status: ${log.data_sesudah.status_dok}`;
                        colorClass = log.data_sesudah.status_dok === 'Sent' ? 'bg-success' : 'bg-warning text-dark';
                    }
                } else if (log.tindakan === 'Soft Delete') {
                    title = 'Masuk Kotak Sampah';
                    icon = 'fa-trash';
                    colorClass = 'bg-danger';
                }

                body.innerHTML += `
                <div class="d-flex ${isLast ? '' : 'mb-4'}">
                    <div class="me-3 d-flex flex-column align-items-center" style="width: 24px; flex-shrink: 0;">
                        <div class="rounded-circle ${colorClass} text-white d-flex align-items-center justify-content-center shadow-sm"
                            style="width: 24px; height: 24px; font-size: 10px; z-index: 2; flex-shrink: 0;">
                            <i class="fa ${icon}"></i>
                        </div>
                        ${!isLast ? '<div class="flex-grow-1 border-start border-2 mt-1" style="border-color: #e2e8f0; margin-left: 0px; min-height: 20px;"></div>' : ''}
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 13px;">${title}</h6>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="text-muted" style="font-size: 11px;"><i class="fa fa-calendar-alt me-1"></i>${dateStr}</span>
                            <span class="text-muted" style="font-size: 11px;"><i class="fa fa-clock me-1"></i>${timeStr}</span>
                        </div>
                        <p class="text-muted small mb-0" style="font-size: 11px;">Oleh: <span class="fw-bold text-dark">${log.user ? log.user.name : 'System'}</span></p>
                    </div>
                </div>
            `;
            });
        }

        // ── Convert Modal ─────────────────────────────────────────────────────────────
        async function fetchMasterProduk() {
            if (window.masterProduk && Array.isArray(window.masterProduk) && window.masterProduk.length > 0)
                return window.masterProduk;
            try {
                const res = await fetch('{{ url('api/product-api') }}' + '?per_page=1000');
                const result = await res.json();

                // Robust check for array data in various common response structures
                let data = [];
                if (Array.isArray(result)) {
                    data = result;
                } else if (result && typeof result === 'object') {
                    data = result.data || result.products || result.items || [];
                    if (!Array.isArray(data)) data = [];
                }

                window.masterProduk = data;
                return window.masterProduk;
            } catch (e) {
                console.error('Gagal memuat master produk', e);
                window.masterProduk = [];
                return [];
            }
        }

        async function openConvertModal() {
            if (!sjDetailData) return;

            const btn = document.getElementById('btn-convert');
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Memuat...';

            const products = await fetchMasterProduk();
            btn.disabled = false;
            btn.innerHTML = originalHtml;

            const today = new Date().toISOString().split('T')[0];
            const due = new Date();
            due.setDate(due.getDate() + 30);

            document.getElementById('conv_nomor').value = 'INV/' + new Date().getFullYear() + '/' + Date.now()
                .toString()
                .slice(-5);
            document.getElementById('conv_tgl').value = today;
            document.getElementById('conv_jatuh_tempo').value = due.toISOString().split('T')[0];
            document.getElementById('conv_ref').value = sjDetailData.nomor_invoice;

            const tbody = document.getElementById('conv-items-body');
            tbody.innerHTML = (sjDetailData.items || []).map(item => {
                const productId = item.id_produk || item.product_id || item.product?.id;
                const master = products.find(p => p.id == productId) || item.product;
                const defaultPrice = master ? (master.harga_jual || master.harga_satuan || master.harga || 0) :
                    0;

                return `
                <tr data-item-id="${item.id}">
                    <td class="ps-3">
                        <div class="fw-bold text-dark small">${item.nama_produk_manual || item.product?.nama_produk || '-'}</div>
                        <input type="hidden" class="conv-item-id" value="${item.id}">
                    </td>
                    <td class="text-center">
                        <input type="number" class="form-control form-control-sm text-center conv-qty" value="${item.qty}" min="0.01" step="0.01" oninput="recalcConvTotal()">
                    </td>
                    <td class="text-end">
                        <input type="text" class="form-control form-control-sm text-end conv-harga" value="${parseInt(defaultPrice).toLocaleString('id-ID')}" oninput="formatConvRupiah(this); recalcConvTotal()">
                    </td>
                    <td class="text-end pe-3 fw-bold conv-row-total">Rp 0</td>
                </tr>
            `;
            }).join('');

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
                const qty = parseFloat(row.querySelector('.conv-qty')?.value || 0);
                const harga = cleanConvNum(row.querySelector('.conv-harga')?.value || 0);
                const total = qty * harga;
                grand += total;
                const totalEl = row.querySelector('.conv-row-total');
                if (totalEl) totalEl.textContent = financeApp.formatIDR(total);
            });
            document.getElementById('conv-grand-total').textContent = financeApp.formatIDR(grand);
        }

        async function doConvertToInvoice() {
            const nomor = document.getElementById('conv_nomor').value;
            const tgl = document.getElementById('conv_tgl').value;
            if (!nomor || !tgl) {
                alert('Nomor invoice dan tanggal wajib diisi.');
                return;
            }

            const items = [];
            document.querySelectorAll('#conv-items-body tr').forEach(row => {
                const id = row.querySelector('.conv-item-id')?.value;
                const qty = parseFloat(row.querySelector('.conv-qty')?.value || 0);
                const harga = cleanConvNum(row.querySelector('.conv-harga')?.value || 0);
                if (id && qty > 0) {
                    items.push({
                        id: parseInt(id),
                        qty,
                        harga_satuan: harga,
                        diskon_nilai: 0,
                        diskon_tipe: 'Fixed'
                    });
                }
            });

            if (items.length === 0) {
                alert('Tidak ada item valid.');
                return;
            }

            const payload = {
                nomor_invoice: nomor,
                tgl_invoice: tgl,
                tgl_jatuh_tempo: document.getElementById('conv_jatuh_tempo').value,
                ref_no: document.getElementById('conv_ref').value,
                items
            };

            const btn = document.querySelector('[onclick="doConvertToInvoice()"]');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Memproses...';

            try {
                const res = await fetch(`${API_URL}/${suratJalanId}/convert-to-invoice`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });
                const result = await res.json();

                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('convertModal')).hide();
                    const invId = result.data?.invoice?.id;
                    if (invId) {
                        const open = await macConfirm('Berhasil', 'Invoice berhasil dibuat! Buka halaman invoice sekarang?', {
                            confirmText: 'Buka Invoice',
                            confirmType: 'primary',
                            cancelText: 'Tutup'
                        });
                        if (open) {
                            window.location.href = `/sales/${invId}`;
                        } else {
                            loadSuratJalanDetail();
                        }
                    } else {
                        loadSuratJalanDetail();
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

        function openPrintPreview(id) {
            const iframe = document.getElementById('print-iframe');
            iframe.src = `{{ url('surat-jalan/print') }}/${id}?no_print=1`;
            const modal = new bootstrap.Modal(document.getElementById('modalPrintPreview'));
            modal.show();
        }

        function triggerPrint() {
            const iframe = document.getElementById('print-iframe');
            if (iframe && iframe.contentWindow) {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadSuratJalanDetail();
        });
    </script>
@endpush
