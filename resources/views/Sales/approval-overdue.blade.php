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
@endpush

@section('main')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box d-md-flex justify-content-between align-items-center">
                            <h4 class="page-title">Persetujuan Invoice Overdue</h4>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#">Penjualan</a></li>
                                <li class="breadcrumb-item active">Persetujuan Overdue</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                    <div class="card-body p-4 bg-white">
                        <div class="mb-4 p-3 rounded-3 bg-light border shadow-sm">
                            <div class="d-flex flex-nowrap align-items-end gap-2 overflow-auto pb-1"
                                style="scrollbar-width: thin;">

                                <div style="min-width: 0; flex: 1.5;">
                                    <label class="f-label mb-1 fw-bold text-muted" style="font-size: 11px;">Nomor
                                        Invoice</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white border-end-0"><i
                                                class="fa fa-search text-muted"></i></span>
                                        <input type="text" id="filter-search"
                                            class="form-control border-start-0 ps-0 shadow-none"
                                            placeholder="Cari No. Invoice...">
                                    </div>
                                </div>

                                <div style="min-width: 0; flex: 1.5;">
                                    <label class="f-label mb-1 fw-bold text-muted" style="font-size: 11px;">Mitra</label>
                                    <select id="filter-mitra-id" class="tom-select-init">
                                        <option value="">Semua Mitra...</option>
                                    </select>
                                </div>

                                <div style="min-width: 0; flex: 1;">
                                    <label class="f-label mb-1 fw-bold text-muted" style="font-size: 11px;">Tgl.
                                        Invoice</label>
                                    <input type="date" id="filter-tgl-invoice"
                                        class="form-control form-control-sm shadow-none">
                                </div>

                                <div style="min-width: 0; flex: 0.5;" class="d-flex gap-1">
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
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="invoiceTable">
                                <thead class="bg-light text-uppercase text-secondary fw-bold"
                                    style="font-size: 11px; letter-spacing: 0.5px;">
                                    <tr>
                                        <th width="40" class="text-center">#</th>
                                        <th width="150">Invoice No</th>
                                        <th width="200">Mitra</th>
                                        <th width="150" class="text-end">Total Akhir</th>
                                        <th width="120" class="text-center">Tgl. Invoice</th>
                                        <th width="100" class="text-center pe-3">Tindakan</th>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        if (!window.financeApp) {
            window.financeApp = {
                API_URL: '/api/invoice-api/overdue-admin',
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
        } else {
            window.financeApp.API_URL = '/api/invoice-api/overdue-admin';
        }

        let tomSelectMitra;

        document.addEventListener('DOMContentLoaded', function() {
            initializeTomSelect();
            initializeMasterData();
            loadInvoiceData();
        });

        function initializeTomSelect() {
            tomSelectMitra = new TomSelect('#filter-mitra-id', {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                dropdownParent: 'body'
            });
        }

        async function initializeMasterData() {
            try {
                const response = await fetch('/api/mitra-api');
                const result = await response.json();
                if (result.success) {
                    const mitras = result.data.data || result.data || [];
                    mitras.forEach(m => {
                        tomSelectMitra.addOption({
                            value: m.id,
                            text: m.nama
                        });
                    });
                }
            } catch (error) {
                console.error('Master data error:', error);
            }
        }

        async function loadInvoiceData(url = window.financeApp.API_URL) {
            const tbody = document.getElementById('invoiceTableBody');
            tbody.innerHTML =
                '<tr><td colspan="6" class="text-center p-5"><div class="spinner-border spinner-border-sm text-primary"></div></td></tr>';

            try {
                const params = new URLSearchParams({
                    search: document.getElementById('filter-search').value,
                    mitra_id: document.getElementById('filter-mitra-id').value,
                    date_from: document.getElementById('filter-tgl-invoice').value,
                    date_to: document.getElementById('filter-tgl-invoice').value,
                });

                const response = await fetch(`${url}${url.includes('?') ? '&' : '?'}${params.toString()}`);
                const result = await response.json();

                if (result.success) {
                    renderInvoiceList(result.data.data);
                    renderPagination(result.data);
                }
            } catch (error) {
                tbody.innerHTML =
                    '<tr><td colspan="6" class="text-center text-danger p-4">Gagal memuat data.</td></tr>';
            }
        }

        function renderInvoiceList(items) {
            const tbody = document.getElementById('invoiceTableBody');
            tbody.innerHTML = '';

            if (items.length === 0) {
                tbody.innerHTML =
                    '<tr><td colspan="6" class="text-center p-4">Tidak ada invoice overdue yang perlu disetujui.</td></tr>';
                return;
            }

            items.forEach((item, index) => {
                const row = document.createElement('tr');
                row.className = 'clickable-row';
                row.onclick = (e) => {
                    if (!e.target.closest('.action-area') && !e.target.closest('button')) {
                        window.location.href = `/sales/approval-overdue/${item.id}`;
                    }
                };

                const statusBadge = '<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2">Overdue</span>';

                row.innerHTML = `
                    <td class="text-center text-muted small">${index + 1}</td>
                    <td class="fw-bold text-primary">${item.nomor_invoice}</td>
                    <td>
                        <div class="fw-bold">${item.mitra?.nama || '-'}</div>
                        <div class="text-muted small">${item.ref_no || '-'}</div>
                    </td>
                    <td class="text-end fw-bold">${window.financeApp.formatIDR(item.total_akhir)}</td>
                    <td class="text-center text-muted f-mono">${window.financeApp.formatDate(item.tgl_invoice)}</td>
                    <td class="text-center action-area">
                        <button class="btn btn-sm btn-success shadow-sm me-1" onclick="handleApproveToggle(${item.id})" title="Setujui (Jadi Unpaid)"><i class="fa fa-check"></i></button>
                        <button class="btn btn-sm btn-danger shadow-sm" onclick="handleRejectToggle(${item.id})" title="Tolak"><i class="fa fa-times"></i></button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        async function handleApproveToggle(id) {
            if (!await macConfirm('Konfirmasi Persetujuan', 'Setujui perpanjangan untuk invoice ini? Status akan menjadi Unpaid dan tempo ditambah 1 bulan.', { confirmText: 'Setujui', confirmType: 'success' })) return;
            performAction(id, 'approve-overdue');
        }

        async function handleRejectToggle(id) {
            if (!await macConfirm('Konfirmasi Penolakan', 'Tolak invoice ini?', { confirmText: 'Tolak', confirmType: 'danger' })) return;
            performAction(id, 'reject-overdue');
        }

        async function performAction(id, action) {
            try {
                const response = await fetch(`/api/invoice-api/${id}/${action}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const result = await response.json();
                if (result.success) {
                    loadInvoiceData();
                } else {
                    alert(result.message);
                }
            } catch (error) {
                alert('Terjadi kesalahan sistem.');
            }
        }

        function renderPagination(data) {
            const container = document.getElementById('pagination-container');
            const info = document.getElementById('pagination-info');
            container.innerHTML = '';

            if (!data || !data.links) return;

            info.textContent = `Menampilkan ${data.from || 0} sampai ${data.to || 0} dari ${data.total} data`;

            data.links.forEach((link, idx) => {
                const li = document.createElement('li');
                li.className = `page-item ${link.active ? 'active' : ''} ${!link.url ? 'disabled' : ''}`;
                li.innerHTML = `
                    <button class="page-link" onclick="loadInvoiceData('${link.url}')">
                        ${link.label.replace('&laquo;', '').replace('&raquo;', '')}
                    </button>
                `;
                container.appendChild(li);
            });
        }

        function resetFilter() {
            document.getElementById('filter-search').value = '';
            tomSelectMitra.clear();
            document.getElementById('filter-tgl-invoice').value = '';
            loadInvoiceData();
        }
    </script>
@endpush
