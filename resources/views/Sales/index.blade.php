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

        /* Table Styling */
        #invoiceTable thead th {
            border-bottom: 1px solid #e2e8f0;
            border-top: 1px solid #e2e8f0;
            background: #f8fafc;
            padding-top: 12px;
            padding-bottom: 12px;
        }

        #invoiceTable tbody td {
            font-size: 13px;
            color: #334155;
            padding-top: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        .clickable-row {
            cursor: pointer;
            transition: background-color 0.1s;
        }

        .clickable-row:hover {
            background-color: #f1f5f9 !important;
        }

        /* Nested Detail Row */
        .detail-row td {
            padding: 0 !important;
            border-bottom: 0 !important;
        }

        .detail-wrapper {
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        /* Badges */
        .f-badge {
            font-size: 10px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
        }

        .f-paid {
            background: #dcfce7;
            color: #166534;
        }

        .f-unpaid {
            background: #fee2e2;
            color: #991b1b;
        }

        .f-partial {
            background: #fef3c7;
            color: #92400e;
        }

        /* Chevron */
        .chevron-icon {
            display: inline-block;
            transition: transform 0.2s ease-in-out;
            color: #cbd5e1;
            font-size: 10px;
            margin-right: 2px;
        }

        .clickable-row[aria-expanded="true"] .chevron-icon {
            transform: rotate(90deg);
            color: #3b82f6;
        }

        /* Dropdown */
        .dropdown-menu-finance {
            border: none;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            min-width: 140px;
            border-top: 3px solid #3b82f6;
        }

        .dropdown-menu-finance .dropdown-item {
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            padding: 6px 12px;
        }

        .dropdown-menu-finance .dropdown-item:hover {
            background-color: #f1f5f9;
            color: #3b82f6;
        }
    </style>

    <style>
        .ts-dropdown {
            z-index: 9999 !important;
        }

        .ts-wrapper.form-control {
            overflow: visible !important;
        }

        .f-input:focus,
        .f-text:focus {
            outline: none !important;
            border-color: #3b82f6 !important;
        }

        .ts-dropdown-content {
            z-index: 2001 !important;
        }
    </style>
    <style>
        @media (max-width: 576px) {
            .page-title-box .breadcrumb { display: none; }
            .card .card-body { padding: 1rem !important; }
            .nav-tabs-finance .nav-link { font-size: 12px; padding: 8px 10px; }
            #invoiceTable { display: block; width: 100%; overflow-x: auto; }
            .detail-wrapper { padding: 8px; }

            /* Header: make tabs and actions stack */
            .card-header.d-flex { flex-wrap: wrap; gap: .5rem; }
            .card-header .nav-tabs-finance { width: 100%; margin-top: .5rem; }
            .card-header .text-end { width: 100%; text-align: left !important; }
            .card-header .text-end .btn { width: 100%; }
            .card-header .text-end .btn + .btn { margin-top: .5rem; }
        }
    </style>
@endpush

@section('main')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box d-md-flex justify-content-between align-items-center">
                            <h4 class="page-title">Manajemen Penjualan</h4>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                                <li class="breadcrumb-item active">Sales</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                    <div class="card-header bg-white border-bottom py-0 px-4 d-flex align-items-center justify-content-between">
                        <ul class="nav nav-tabs nav-tabs-finance" role="tablist">
                            <li class="nav-item"><a class="nav-link active fw-bold py-3" data-bs-toggle="tab"
                                    href="#invoice-active">Invoice Aktif</a></li>
                            <li class="nav-item"><a class="nav-link fw-bold py-3" data-bs-toggle="tab"
                                    href="#invoice-archive">Arsip</a></li>
                            <li class="nav-item"><a class="nav-link fw-bold py-3" data-bs-toggle="tab"
                                    href="#invoice-in-trash">Invoice Terhapus</a></li>
                        </ul>
                        <div class="text-end">
                            <button class="btn btn-white border fw-bold px-3 me-2 shadow-sm text-dark">
                                <i class="fa fa-file-excel me-1 text-success"></i> Export .xls
                            </button>
                            <button class="btn btn-primary fw-bold px-4 shadow-sm"
                                onclick="openInvoiceModal(null, null, 'create')">
                                <i class="fa fa-plus-circle me-1"></i> Buat Invoice Baru
                            </button>
                        </div>
                    </div>

                    <div class="card-body p-4 bg-white">
                        <div class="mb-4 p-3 rounded-3 bg-light border shadow-sm">
                            <div class="row g-2 align-items-end">
                                <div class="col-12 col-md-4">
                                    <label class="f-label mb-1 fw-bold text-muted" style="font-size: 11px;">Pencarian</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white border-end-0"><i class="fa fa-search text-muted"></i></span>
                                        <input type="text" id="filter-search" class="form-control border-start-0 ps-0 shadow-none" placeholder="No. Invoice / Mitra...">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-2">
                                    <label class="f-label mb-1 fw-bold text-muted" style="font-size: 11px;">Status Dok</label>
                                    <select id="filter-status-dok" class="tom-select-init">
                                        @php $documentStatus = ['Draft', 'Sent', 'Failed', 'Approved', 'Rejected']; @endphp
                                        <option value="">Semua</option>
                                        @foreach ($documentStatus as $doc)
                                            <option value="{{ $doc }}">{{ $doc }} </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-sm-6 col-md-2">
                                    <label class="f-label mb-1 fw-bold text-muted" style="font-size: 11px;">Pembayaran</label>
                                    <select id="filter-status-bayar" class="tom-select-init">
                                        @php
                                            $paymentStatus = [
                                                'Draft' => 'Draft',
                                                'Unpaid' => 'Belum',
                                                'Overdue' => 'Telat',
                                                'Paid' => 'Lunas',
                                                'Partially Paid' => 'Sebagian',
                                            ];
                                        @endphp
                                        <option value="">Semua</option>
                                        @foreach ($paymentStatus as $value => $display)
                                            <option value="{{ $value }}">{{ $display }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-sm-6 col-md-2">
                                    <label class="f-label mb-1 fw-bold text-muted" style="font-size: 11px;">Mitra</label>
                                    <select id="filter-mitra-id" class="tom-select-init">
                                        <option value="">Semua Pemasok...</option>
                                    </select>
                                </div>
                                <div class="col-6 col-md-2">
                                    <label class="f-label mb-1 fw-bold text-muted" style="font-size: 11px;">Tgl. Invoice</label>
                                    <input type="date" id="filter-tgl-invoice" class="form-control form-control-sm shadow-none">
                                </div>
                                <div class="col-6 col-md-2">
                                    <label class="f-label mb-1 fw-bold text-muted" style="font-size: 11px;">Jatuh Tempo</label>
                                    <input type="date" id="filter-tgl-jatuh-tempo" class="form-control form-control-sm shadow-none">
                                </div>
                                <div class="col-12 col-sm-6 col-md-2 d-flex gap-2">
                                    <button onclick="loadInvoiceData()" class="btn btn-dark fw-bold btn-sm flex-fill shadow-sm" title="Filter">
                                        <i class="fa fa-filter"></i>
                                    </button>
                                    <button onclick="resetFilter()" class="btn btn-light border border-danger fw-bold text-danger btn-sm flex-fill" title="Reset">
                                        <i class="fa fa-sync"></i>
                                    </button>
                                </div>
                                <div class="col-12 col-sm-6 col-md-2">
                                    <button onclick="exportSales()" class="btn btn-success fw-bold btn-sm w-100 shadow-sm" title="Export Excel">
                                        <i class="fa fa-file-excel me-1"></i> Export
                                    </button>
                                </div>
                            </div>
                            <div id="bulk-action-area" class="d-none border-top pt-2 mt-2">
                                <button class="btn btn-outline-primary fw-bold btn-sm px-3 me-1" onclick="initBulkEdit()">
                                    <i class="fa fa-pencil"></i>
                                </button>
                                <button class="btn btn-outline-danger fw-bold btn-sm px-3" onclick="bulkDelete()">
                                    <i class="fa fa-trash-can"></i>
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="invoiceTable">
                                <thead class="bg-light text-uppercase text-secondary fw-bold"
                                    style="font-size: 11px; letter-spacing: 0.5px;">
                                    <tr>
                                        <th class="ps-3" width="40">
                                            <input type="checkbox" class="form-check-input" id="master-checkbox"
                                                onclick="toggleSelectAll(this)">
                                        </th>
                                        <th width="40" class="text-center">#</th>
                                        <th width="150">Invoice No</th>
                                        <th width="200">Pemasok</th>
                                        <th width="120" class="text-center">Status</th>
                                        <th width="150" class="text-end">Jumlah</th>
                                        <th width="150" class="text-end">Terhutang</th>
                                        <th width="120" class="text-center">Tgl. Invoice</th>
                                        <th width="80" class="text-end pe-3">Tindakan</th>
                                    </tr>
                                </thead>
                                <tbody id="invoiceTableBody" class="border-top-0">
                                    <!-- Rows injected by JS -->
                                </tbody>
                            </table>
                        </div>

                        <div
                            class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 pt-3 border-top">
                            <span id="pagination-info" class="text-muted small fw-medium"></span>
                            <nav>
                                <ul class="pagination pagination-sm mb-0 shadow-sm" id="pagination-container">
                                </ul>
                            </nav>
                        </div>

                        <div class="tab-content">
                            <div class="tab-pane active" id="invoice-active"></div>
                            <div class="tab-pane" id="invoice-archive"></div>
                            <div class="tab-pane" id="invoice-in-trash"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('Sales.Modal.modal-fullscreen')
    @include('Sales.Modal.detail-modal')
    @include('Sales.Partials.invoice-templates')
    @include('Mitra.Modal.modal-fullscreen')

    <div class="modal fade" id="modalPrintPreview" tabindex="-1" aria-labelledby="modalPrintPreviewLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-4">
                <div class="modal-header border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" id="modalPrintPreviewLabel">
                        <i class="fa fa-print text-primary me-2"></i> Preview Cetak Nota
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

    @include('Sales.Modal.modal-bulk-edit')
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        function exportSales() {
            let params = new URLSearchParams();

            // Collect filters similar to loadInvoiceData
            const search = document.getElementById('filter-search').value;
            const statusDok = document.getElementById('filter-status-dok').value;
            const statusBayar = document.getElementById('filter-status-bayar').value;
            const mitraId = document.getElementById('filter-mitra-id').value;
            const tglInvoice = document.getElementById('filter-tgl-invoice').value;
            const tglJatuhTempo = document.getElementById('filter-tgl-jatuh-tempo').value;

            if (search) params.append('search', search);
            if (statusDok) params.append('status_dok', statusDok);
            if (statusBayar) params.append('status_pembayaran', statusBayar);
            // activeTab logic is handled in controller via tab_status or similar if needed
            // For now assuming default active tab or passing it
            
            // Check active tab
            const activeTabPane = document.querySelector('.tab-pane.active');
            let tabStatus = 'active';
            if (activeTabPane.id === 'invoice-archive') tabStatus = 'archive';
            if (activeTabPane.id === 'invoice-in-trash') tabStatus = 'trash';
            
            params.append('tab_status', tabStatus);

            window.location.href = '/sales/export?' + params.toString();
        }

        if (!window.financeApp) {
            window.financeApp = {
                API_URL: '/api/invoice-api',
                selectedIds: [],
                formatIDR: (val) => new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(val),
                formatDate: (dateStr) => {
                    if (!dateStr) return '-';
                    const date = new Date(dateStr);
                    return date.toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric'
                    });
                }
            };
        }

        let currentTabStatus = 'active';

        // Safe TomSelect wrapper
        const safeTomSelect = (selector, options) => {
            if (typeof TomSelect !== 'undefined') {
                try {
                    return new TomSelect(selector, options);
                } catch (e) {
                    console.warn('TomSelect init failed for', selector, e);
                    return null;
                }
            }
            return null;
        };

        document.querySelectorAll('.nav-tabs-finance .nav-link').forEach(tab => {
            tab.addEventListener('shown.bs.tab', function (event) {
                const targetId = event.target.getAttribute('href');

                if (targetId === '#invoice-archive') {
                    currentTabStatus = 'archive';
                } else if (targetId === '#invoice-in-trash') {
                    currentTabStatus = 'trash';
                } else {
                    currentTabStatus = 'active';
                }

                loadInvoiceData();
            });

        });

        let masterMitra, products, salesPersons = []; // Will be populated from API

        async function initializeMasterData() {
            try {
                const [mitraRes, productRes] = await Promise.all([
                    fetch('/api/mitra-api').then(r => r.json()),
                    fetch('/api/product-api').then(r => r.json()),
                ]);

                if (mitraRes.success) {
                    masterMitra = mitraRes.data.data || mitraRes.data;
                    const selectFilter = document.getElementById('filter-mitra-id');

                    masterMitra.forEach(m => {
                        const opt = document.createElement('option');
                        opt.value = m.id;
                        opt.textContent = m.nama;
                        selectFilter.appendChild(opt);
                    });

                    if (tomSelectMitraIndex) tomSelectMitraIndex.sync();
                }

                if (productRes.success) {
                    products = productRes.data.data || productRes.data;

                    products.forEach(p => {
                        const opt = document.createElement('option');
                        opt.value = p.id;
                        opt.textContent = p.nama_produk;
                    });
                }
            } catch (error) {
                alert('Gagal memuat data master:', error);
            }
        }

        let paidInvoiceIds = [];

        async function loadInvoiceData(url = window.financeApp.API_URL) {
            const tbody = document.getElementById('invoiceTableBody');
            tbody.innerHTML =
                '<tr><td colspan="10" class="text-center p-5"><div class="spinner-border spinner-border-sm text-primary"></div><p class="mt-2 text-muted small mb-0">Memuat data...</p></td></tr>';
            resetSelection();
            paidInvoiceIds = []; // Reset

            try {
                let finalUrl = (typeof url === 'string' && url.includes('/')) ? url : window.financeApp.API_URL;
                const params = new URLSearchParams({
                    tipe_invoice: 'Sales',
                    tab_status: currentTabStatus,
                    search: document.getElementById('filter-search').value,
                    status_dok: document.getElementById('filter-status-dok').value,
                    status_pembayaran: document.getElementById('filter-status-bayar').value,
                    mitra_id: document.getElementById('filter-mitra-id').value,
                    tgl_invoice: document.getElementById('filter-tgl-invoice').value,
                    tgl_jatuh_tempo: document.getElementById('filter-tgl-jatuh-tempo').value
                });

                // Fetch invoices and payments in parallel
                const [invoiceRes, paymentRes] = await Promise.all([
                    fetch(`${finalUrl}${finalUrl.includes('?') ? '&' : '?'}${params.toString()}`),
                    fetch(
                        `/api/payment-api?tipe_receipt=Sales&limit=1000`) // Fetch enough payments to cover visible invoices
                ]);

                const result = await invoiceRes.json();
                const paymentResult = await paymentRes.json();

                if (paymentResult.success) {
                    // Extract invoice_ids from payments
                    // Note: API structure might be result.data.data or result.data depending on pagination
                    const payments = paymentResult.data.data || paymentResult.data || [];
                    paidInvoiceIds = payments.map(p => String(p.invoice_id)); // Store as strings
                }

                if (result.success) {
                    renderInvoiceList(result.data.data);
                    renderPagination(result.data);
                }
            } catch (error) {
                console.error(error);
                tbody.innerHTML =
                    '<tr><td colspan="10" class="text-center text-danger p-4">Gagal memuat data.</td></tr>';
            }
        }

        function openDetailModal(item) {
            const modalEl = document.getElementById('detailInvoiceModal');
            if (!modalEl) return;

            // Populate Modal Data
            document.getElementById('detailInvoiceNumber').textContent = item.nomor_invoice;
            document.getElementById('detailInvoiceTitle').textContent = `Detail Invoice ${item.nomor_invoice}`;
            document.getElementById('detailDate').textContent = window.financeApp.formatDate(item.tgl_invoice);
            document.getElementById('detailDueDate').textContent = window.financeApp.formatDate(item.tgl_jatuh_tempo);

            // Mitra Info
            document.getElementById('detailMitraName').textContent = item.mitra?.nama || '-';
            document.getElementById('detailMitraAddress').textContent = item.mitra?.alamat || '-';

            // Payment Status
            const payBadge = document.getElementById('detailPaymentStatus');
            const payClass = item.status_pembayaran === 'Paid' ? 'bg-success-subtle text-success' : (item
                .status_pembayaran === 'Unpaid' ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning');
            payBadge.className = `badge ${payClass} border`;
            payBadge.textContent = item.status_pembayaran;

            // Items
            const tbody = document.getElementById('detailItemsBody');
            tbody.innerHTML = '';
            item.items?.forEach(it => {
                const tr = document.createElement('tr');
                tr.classList.add('border-bottom', 'border-light');
                tr.innerHTML = `
                                                                                                                                                                <td class="ps-3 py-3">
                                                                                                                                                                    <div class="fw-bold text-dark">${it.nama_produk_manual || it.product?.nama_produk || '-'}</div>
                                                                                                                                                                    <div class="small text-muted">${it.product?.kode_produk || '-'}</div>
                                                                                                                                                                </td>
                                                                                                                                                                <td class="text-center py-3">${parseFloat(it.qty)} ${it.product?.unit?.nama_unit || ''}</td>
                                                                                                                                                                <td class="text-end py-3">${window.financeApp.formatIDR(it.harga_satuan)}</td>
                                                                                                                                                                <td class="text-end pe-3 py-3">${window.financeApp.formatIDR(it.total_harga_item)}</td>
                                                                                                                                                            `;
                tbody.appendChild(tr);
            });

            // Totals
            document.getElementById('detailSubtotal').textContent = window.financeApp.formatIDR(item.total_akhir);
            document.getElementById('detailTotal').textContent = window.financeApp.formatIDR(item.total_akhir);

            // Action Buttons
            document.getElementById('btnDetailEdit').onclick = () => {
                bootstrap.Modal.getInstance(document.getElementById('detailInvoiceModal')).hide();
                openInvoiceModal(item.id, null, 'edit');
            };
            document.getElementById('btnDetailDelete').onclick = async () => {
                if (await macConfirm('Hapus invoice', 'Yakin ingin menghapus invoice ini?')) {
                    bootstrap.Modal.getInstance(document.getElementById('detailInvoiceModal')).hide();
                    deleteInvoice(item.id);
                }
            };

            const btnDetailPrint = document.getElementById('btnDetailPrint');
            btnDetailPrint.href = 'javascript:void(0)';
            btnDetailPrint.onclick = () => openPrintPreview(item.id);

            // Show Modal
            new bootstrap.Modal(document.getElementById('detailInvoiceModal')).show();
        }

        function openPrintPreview(id) {
            const printUrl = `{{ url('sales/print') }}/${id}`;

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

        function renderInvoiceList(data) {
            const tbody = document.getElementById('invoiceTableBody');
            const rowTpl = document.getElementById('row-template');

            tbody.innerHTML = '';

            if (!data || data.length === 0) {
                tbody.innerHTML =
                    '<tr><td colspan="10" class="text-center p-5 text-muted fst-italic">Data tidak ditemukan.</td></tr>';
                return;
            }

            data.forEach((item, index) => {
                const row = rowTpl.content.cloneNode(true);
                const trMain = row.querySelector('.clickable-row');

                // Click to open detail
                trMain.removeAttribute('data-bs-target');
                trMain.removeAttribute('data-bs-toggle');
                trMain.onclick = (e) => {
                    // Prevent if clicked on checkbox or actions
                    if (e.target.closest('.invoice-checkbox') || e.target.closest('.dropdown') || e.target
                        .closest('a') || e.target.closest('button')) return;
                    window.location.href = `{{ url('sales') }}/${item.id}`;
                };

                row.querySelector('.invoice-checkbox').dataset.id = item.id;
                row.querySelector('.row-index').textContent = index + 1;
                row.querySelector('.row-nomor').textContent = item.nomor_invoice;
                row.querySelector('.row-ref').textContent = item.ref_no || '-';
                row.querySelector('.row-mitra').textContent = item.mitra ? item.mitra.nama : 'Umum';
                row.querySelector('.row-mitra-no').textContent = item.mitra?.nomor_mitra || '-';

                // Badge Status Dokumen
                const statusBadge = row.querySelector('.row-status-dok-badge');
                statusBadge.innerHTML =
                    `<span class="badge ${item.status_dok === 'Approved' ? 'bg-soft-primary text-primary' : 'bg-soft-secondary text-secondary'}" style="font-size:10px;">${item.status_dok.toUpperCase()}</span>`;

                // Badge Status Bayar
                const payBadge = row.querySelector('.row-status-bayar-badge');
                const payClass = item.status_pembayaran === 'Paid' ? 'f-paid' : (item.status_pembayaran ===
                    'Unpaid' ? 'f-unpaid' : 'f-partial');
                payBadge.textContent = item.status_pembayaran;
                payBadge.classList.add(payClass);

                // Nilai Uang
                const amountDue = item.total_akhir - (item.payment_sum_jumlah_bayar || 0);
                row.querySelector('.row-total').textContent = window.financeApp.formatIDR(item.total_akhir);
                row.querySelector('.row-terhutang').textContent = window.financeApp.formatIDR(amountDue);
                row.querySelector('.row-terhutang').classList.add(amountDue > 0 ? 'text-danger' : 'text-success');

                // Tanggal
                row.querySelector('.row-tgl').textContent = window.financeApp.formatDate(item.tgl_invoice);
                row.querySelector('.row-jatuh-tempo').textContent =
                    `Due: ${window.financeApp.formatDate(item.tgl_jatuh_tempo)}`;

                // Aksi
                const btnShow = row.querySelector('.btn-show');
                if (btnShow) {
                    btnShow.onclick = (e) => {
                        e.preventDefault();
                        openDetailModal(item);
                    };
                    btnShow.removeAttribute('href');
                }

                const btnReceipt = row.querySelector('.btn-create-receipt');
                if (btnReceipt) {
                    // Hide if payment exists for this invoice
                    if (paidInvoiceIds.includes(String(item.id))) {
                        btnReceipt.closest('li').remove();
                    } else {
                        btnReceipt.onclick = (e) => {
                            e.preventDefault();
                            window.location.href =
                                `{{ route('sales.receipt') }}?open_create=true&invoice_id=${item.id}&mitra_id=${item.mitra_id}`;
                        };
                    }
                }

                row.querySelector('.btn-edit').onclick = () => openInvoiceModal(item.id, null, 'edit');
                row.querySelector('.btn-delete').onclick = () => deleteInvoice(item.id);
                const btnPrint = row.querySelector('.btn-print');
                btnPrint.href = 'javascript:void(0)';
                btnPrint.onclick = () => openPrintPreview(item.id);

                // Remove chevron
                const chevron = row.querySelector('.chevron-icon');
                if (chevron) chevron.remove();

                tbody.appendChild(row);
            });
        }

        // Checkbox & Utils
        function toggleSelectAll(master) {
            document.querySelectorAll('.invoice-checkbox').forEach(cb => cb.checked = master.checked);
            updateSelection();
        }

        function updateSelection() {
            const chk = document.querySelectorAll('.invoice-checkbox:checked');
            window.financeApp.selectedIds = Array.from(chk).map(c => c.dataset.id);
            const area = document.getElementById('bulk-action-area');
            if (window.financeApp.selectedIds.length > 0) {
                area.classList.remove('d-none');
                area.querySelector('.selected-count').innerText = `${window.financeApp.selectedIds.length} terpilih`;
            } else {
                area.classList.add('d-none');
            }
        }

        function resetSelection() {
            window.financeApp.selectedIds = [];
            if (document.getElementById('master-checkbox')) document.getElementById('master-checkbox').checked = false;
            updateSelection();
        }

        async function deleteInvoice(id) {
            if (!await macConfirm('Hapus invoice', 'Yakin ingin menghapus invoice ini?')) return;
            try {
                const res = await fetch(`${window.financeApp.API_URL}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const dat = await res.json();
                if (dat.success) {
                    alert('Terhapus');
                    loadInvoiceData();
                } else alert(dat.message);
            } catch (e) {
                alert('Gagal menghapus');
            }
        }

        async function bulkDelete() {
            const count = window.financeApp.selectedIds.length;
            if (count === 0) return;

            if (!await macConfirm('Hapus invoice', `Yakin ingin menghapus ${count} data?`)) return;

            const btn = document.querySelector('#bulk-action-area button.btn-outline-danger');
            const originalContent = btn.innerHTML;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
            btn.disabled = true;

            let successCount = 0;
            let failCount = 0;

            try {
                const promises = window.financeApp.selectedIds.map(id =>
                    fetch(`${window.financeApp.API_URL}/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    }).then(res => res.json())
                        .then(data => {
                            if (data.success) successCount++;
                            else failCount++;
                        })
                        .catch(err => {
                            console.error(`Failed to delete ${id}:`, err);
                            failCount++;
                        })
                );

                await Promise.all(promises);

                alert(`Proses Selesai.\nBerhasil: ${successCount}\nGagal: ${failCount}`);
                loadInvoiceData(); // This will also reset selection
            } catch (e) {
                console.error(e);
                alert('Terjadi kesalahan sistem saat menghapus massal.');
            } finally {
                if (btn) {
                    btn.innerHTML = originalContent;
                    btn.disabled = false;
                }
            }
        }

        function renderPagination(meta) {
            const c = document.getElementById('pagination-container');
            c.innerHTML = '';
            document.getElementById('pagination-info').innerText = `${meta.from || 0}-${meta.to || 0} dari ${meta.total}`;
            meta.links.forEach(l => {
                const cls = l.active ? 'bg-primary text-white' : 'bg-white text-dark';
                c.insertAdjacentHTML('beforeend',
                    `<li class="page-item ${!l.url ? 'disabled' : ''}"><a class="page-link border-0 mx-1 rounded shadow-sm fw-bold ${cls}" href="#" onclick="loadInvoiceData('${l.url}')">${l.label}</a></li>`
                );
            });
        }

        function resetFilter() {
            document.getElementById('filter-search').value = '';
            document.getElementById('filter-tgl-invoice').value = '';
            document.getElementById('filter-tgl-jatuh-tempo').value = '';

            document.querySelectorAll('.tom-select-init').forEach(s => {
                s.tomselect?.clear();
                s.tomselect?.setValue('');
            });

            loadInvoiceData();
        }

        let tomSelectPaymentStatus, tomSelectDocumentStatus, tomSelectMitraIndex;

        function initFilters() {
            if (typeof TomSelect === 'undefined') {
                console.warn('TomSelect is not loaded. Filters will use default select behavior.');
            }

            tomSelectDocumentStatus = safeTomSelect('#filter-status-dok', {
                create: false,
                allowEmptyOption: true,
                dropdownParent: 'body'
            });

            tomSelectPaymentStatus = safeTomSelect('#filter-status-bayar', {
                create: false,
                allowEmptyOption: true,
                dropdownParent: 'body'
            });

            tomSelectMitraIndex = safeTomSelect('#filter-mitra-id', {
                create: false,
                allowEmptyOption: true,
                placeholder: 'Cari Pemasok / Mitra ...',
                dropdownParent: 'body'
            })

            const searchInput = document.getElementById('filter-search');
            if (searchInput) {
                searchInput.addEventListener('keypress', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        loadInvoiceData();
                    }
                });
            }
        }

        document.addEventListener('DOMContentLoaded', async () => {
            try {
                initFilters();
            } catch (e) {
                console.error('Filter init error:', e);
            }

            try {
                await initializeMasterData();
            } catch (e) {
                console.error('Master data error:', e);
            }

            loadInvoiceData();
        });
    </script>
@endpush
