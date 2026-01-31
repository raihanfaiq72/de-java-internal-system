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
@endpush

@section('main')
    <div class="page-wrapper" style="background-color: #f8fafc; min-height: 100vh; font-family: 'Inter', sans-serif;">
        <div class="page-content">
            <div class="container-fluid">

                <div class="row align-items-center mb-4">
                    <div class="col-md-7">
                        <h4 class="fw-bold text-dark mb-1">Manajemen Delivery Order</h4>
                        <p class="text-muted small mb-0">Pantau delivery order saat ini.</p>
                    </div>
                    <div class="col-md-5 text-md-end mt-3 mt-md-0">
                        <button class="btn btn-primary fw-bold px-4 shadow-sm" onclick="openDeliveryOrderModal()">
                            <i class="fa fa-plus-circle me-1"></i> TAMBAH DO
                        </button>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                    {{-- <div class="card-header bg-white border-bottom py-0 px-4">
                        <ul class="nav nav-tabs nav-tabs-finance" role="tablist">
                            <li class="nav-item"><a class="nav-link active fw-bold py-3" data-bs-toggle="tab"
                                    href="#invoice-active">Invoice Aktif</a></li>
                            <li class="nav-item"><a class="nav-link fw-bold py-3" data-bs-toggle="tab"
                                    href="#invoice-archive">Arsip</a></li>
                            <li class="nav-item"><a class="nav-link fw-bold py-3" data-bs-toggle="tab"
                                    href="#invoice-in-trash">Invoice Terhapus</a></li>
                        </ul>
                    </div> --}}

                    <div class="card-body p-4 bg-white">
                        <div class="mb-4 p-3 rounded-3 bg-light border shadow-sm">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-4">
                                    <label class="f-label mb-1 fw-bold text-muted"
                                        style="font-size: 11px;">Pencarian</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white border-end-0 py-1">
                                            <i class="fa fa-search text-muted small"></i>
                                        </span>
                                        <input type="text" id="filter-search"
                                            class="form-control border-start-0 shadow-none py-1"
                                            placeholder="Ketik nomor DO atau Invoice...">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <button onclick="loadInvoiceData()" class="btn btn-dark fw-bold shadow-sm px-3 w-100"
                                        style="padding-top: 7px; padding-bottom: 7px;">
                                        <i class="fa fa-filter me-1 small"></i> FILTER
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="invoiceTable">
                                <thead class="bg-light text-uppercase text-secondary fw-bold"
                                    style="font-size: 11px; letter-spacing: 0.5px;">
                                    <tr>
                                        <th class="text-start">
                                            <input type="checkbox" class="form-check-input" id="master-checkbox"
                                                onclick="toggleSelectAll(this)">
                                        </th>
                                        <th class="text-start">#</th>
                                        <th>Delivery Order Note</th>
                                        <th>Invoice</th>
                                        <th class="text-end pe-3">Tindakan</th>
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

                        {{-- <div class="tab-content">
                            <div class="tab-pane active" id="invoice-active"></div>
                            <div class="tab-pane" id="invoice-archive"></div>
                            <div class="tab-pane" id="invoice-in-trash"></div>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('DeliveryOrder.Modal.modal-fullscreen')
    {{-- @include('Sales.Modal.detail-modal')
    @include('Sales.Partials.invoice-templates') --}}
@endsection

@push('js')
    <script>
        let INVOICES_URL = "{{ route('invoice-apiindex') }}"
        let FLEETS_URL = "{{ route('fleet-api.index') }}";

        let masterInvoices = [],
            masterFleets = [];

        async function initializeMasterData() {
            try {
                const [invoicesRes, fleetsRes] = await Promise.all([
                    fetch(INVOICES_URL).then(r => r.json()),
                    fetch(FLEETS_URL).then(r => r.json())
                ]);

                if (invoicesRes.success) {
                    masterInvoices = invoicesRes.data.data || invoicesRes.data;
                }

                if (fleetsRes.success) {
                    masterFleets = fleetsRes.data.data || fleetsRes.data;
                }

                console.log(masterInvoices);
                console.log(masterFleets);
            } catch (error) {
                alert('Gagal memuat data master:');
            }
        }

        document.addEventListener('DOMContentLoaded', async () => {
            await initializeMasterData();
        })
    </script>
@endpush
