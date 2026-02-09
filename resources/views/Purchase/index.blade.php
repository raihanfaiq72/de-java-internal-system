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
            z-index: 2000 !important;
        }

        .ts-wrapper.form-control {
            overflow: visible !important;
        }

        .ts-dropdown-content {
            z-index: 2001 !important;
        }
    </style>
@endpush

@section('main')
    <div class="page-wrapper" style="background-color: #f8fafc; min-height: 100vh; font-family: 'Inter', sans-serif;">
        <div class="page-content">
            <div class="container-fluid">

                <div class="row align-items-center mb-4">
                    <div class="col-md-7">
                        <h4 class="fw-bold text-dark mb-1">Manajemen Invoice Pembelian</h4>
                        <p class="text-muted small mb-0">Kelola tagihan supplier dan pantau hutang usaha secara real-time.
                        </p>
                    </div>
                    <div class="col-md-5 text-md-end mt-3 mt-md-0">
                        <button class="btn btn-white border fw-bold px-3 me-2 shadow-sm text-dark">
                            <i class="fa fa-file-excel me-1 text-success"></i> Export .xls
                        </button>
                        <button class="btn btn-primary fw-bold px-4 shadow-sm" onclick="openInvoiceModal()">
                            <i class="fa fa-plus-circle me-1"></i> Buat Invoice Baru
                        </button>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                    <div class="card-header bg-white border-bottom py-0 px-4">
                        <ul class="nav nav-tabs nav-tabs-finance" role="tablist">
                            <li class="nav-item"><a class="nav-link active fw-bold py-3" data-bs-toggle="tab"
                                    href="#invoice-active">Invoice Aktif</a></li>
                            <li class="nav-item"><a class="nav-link fw-bold py-3" data-bs-toggle="tab"
                                    href="#invoice-archive">Arsip</a></li>
                            <li class="nav-item"><a class="nav-link fw-bold py-3" data-bs-toggle="tab"
                                    href="#invoice-in-trash">Invoice Terhapus</a></li>
                        </ul>
                    </div>

                    <div class="card-body p-4 bg-white">
                        <div class="mb-4 p-3 rounded-3 bg-light border shadow-sm">
                            <div class="d-flex flex-nowrap align-items-end gap-2 overflow-auto pb-1"
                                style="scrollbar-width: thin;">

                                <div style="min-width: 0; flex: 1.2;">
                                    <label class="f-label mb-1 fw-bold text-muted"
                                        style="font-size: 11px;">Pencarian</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white border-end-0"><i
                                                class="fa fa-search text-muted"></i></span>
                                        <input type="text" id="filter-search"
                                            class="form-control border-start-0 ps-0 shadow-none"
                                            placeholder="No. Invoice / Mitra...">
                                    </div>
                                </div>

                                <div style="min-width: 0; flex: 1;">
                                    <label class="f-label mb-1 fw-bold text-muted" style="font-size: 11px;">Status
                                        Dok</label>
                                    <select id="filter-status-dok" class="tom-select-init">
                                        @php $documentStatus = ['Draft', 'Sent', 'Failed', 'Approved', 'Rejected']; @endphp
                                        <option value="">Semua</option>
                                        @foreach ($documentStatus as $doc)
                                            <option value="{{ $doc }}">{{ $doc }} </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div style="min-width: 0; flex: 1;">
                                    <label class="f-label mb-1 fw-bold text-muted"
                                        style="font-size: 11px;">Pembayaran</label>
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

                                <div style="min-width: 0; flex: 1;">
                                    <label class="f-label mb-1 fw-bold text-muted" style="font-size: 11px;">Mitra</label>
                                    <select id="filter-mitra-id" class="tom-select-init">
                                        <option value="">Semua Pemasok...</option>
                                    </select>
                                </div>

                                <div style="min-width: 0; flex: 1;">
                                    <label class="f-label mb-1 fw-bold text-muted" style="font-size: 11px;">Tgl.
                                        Invoice</label>
                                    <input type="date" id="filter-tgl-invoice"
                                        class="form-control form-control-sm shadow-none">
                                </div>

                                <div style="min-width: 0; flex: 1;">
                                    <label class="f-label mb-1 fw-bold text-muted" style="font-size: 11px;">Jatuh
                                        Tempo</label>
                                    <input type="date" id="filter-tgl-jatuh-tempo"
                                        class="form-control form-control-sm shadow-none">
                                </div>

                                <div class="d-flex gap-1" style="flex: 1; min-width: 0;">
                                    <button onclick="loadInvoiceData()"
                                        class="btn btn-dark fw-bold btn-sm flex-fill shadow-sm" title="Filter">
                                        <i class="fa fa-filter"></i>
                                    </button>
                                    <button onclick="resetFilter()"
                                        class="btn btn-light border border-danger fw-bold text-danger btn-sm flex-fill"
                                        title="Reset">
                                        <i class="fa fa-sync"></i>
                                    </button>
                                </div>

                                <div id="bulk-action-area" class="d-none flex-shrink-0 ms-2 border-start ps-2">
                                    <button class="btn btn-outline-danger fw-bold btn-sm px-3" onclick="bulkDelete()">
                                        <i class="fa fa-trash-can"></i>
                                    </button>
                                </div>
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
                                        <th width="80" class="text-center">Att</th>
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

    @include('Purchase.Modal.modal-fullscreen')
    @include('Purchase.Modal.detail-modal')
    @include('Purchase.Partials.invoice-templates')

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

{{-- @push('css')
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
        transition: transform 0.2s;
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
@endpush --}}

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
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

        let masterMitra = [];

        async function initializeMasterData() {
            try {
                const [mitraRes] = await Promise.all([
                    fetch('/api/mitra-api').then(r => r.json())
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
            } catch (error) {
                alert('Gagal memuat data master:', error);
            }
        }

        async function loadInvoiceData(url = window.financeApp.API_URL) {
            const tbody = document.getElementById('invoiceTableBody');
            tbody.innerHTML =
                '<tr><td colspan="10" class="text-center p-5"><div class="spinner-border spinner-border-sm text-primary"></div><p class="mt-2 text-muted small mb-0">Memuat data...</p></td></tr>';
            resetSelection();

            try {
                let finalUrl = (typeof url === 'string' && url.includes('/')) ? url : window.financeApp.API_URL;
                const params = new URLSearchParams({
                    tipe_invoice: 'Purchase',
                    search: document.getElementById('filter-search').value,
                    status_dok: document.getElementById('filter-status-dok').value,
                    status_pembayaran: document.getElementById('filter-status-bayar').value
                });

                const response = await fetch(`${finalUrl}${finalUrl.includes('?') ? '&' : '?'}${params.toString()}`);
                const result = await response.json();

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
            document.getElementById('btnDetailDelete').onclick = () => {
                if (confirm('Hapus invoice ini?')) {
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
            const printUrl = `{{ url('purchase/print') }}/${id}`;

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
                    window.location.href = `{{ url('purchase') }}/${item.id}`;
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
                    btnReceipt.onclick = (e) => {
                        e.preventDefault();
                        // Redirect to receipt page with params
                        window.location.href =
                            `{{ route('purchase.receipt') }}?open_create=true&invoice_id=${item.id}&mitra_id=${item.mitra_id}`;
                    };
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
            if (!confirm('Hapus invoice pembelian ini?')) return;
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

        function bulkDelete() {
            alert('Fitur hapus massal segera hadir.');
        }

        function renderPagination(meta) {
            /* reusing prev logic or simplified */
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