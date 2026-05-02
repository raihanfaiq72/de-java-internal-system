@extends('Layout.main')

@section('main')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box d-md-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="page-title text-primary fw-bold">Detail Laporan Massal</h4>
                                <p class="text-muted small mb-0">
                                    <i class="iconoir-calendar me-1"></i> Periode: {{ $bulkReport->period_name }}
                                </p>
                            </div>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('bulk-reports.index') }}">Laporan Massal</a>
                                </li>
                                <li class="breadcrumb-item active">Detail</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold">Laporan Siap Dicetak</h6>
                                <p class="text-muted small mb-0">4 laporan akan digabung dalam 1 PDF</p>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('bulk-reports.index') }}" class="btn btn-light fw-bold px-4">
                                    <i class="fa fa-arrow-left me-1"></i> Kembali
                                </a>
                                <button type="button" onclick="openPreview('{{ $bulkReport->slug }}')"
                                    class="btn btn-success fw-bold px-4 shadow-sm">
                                    <i class="fa fa-file-pdf me-1"></i> Generate PDF
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Tabs -->
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-white border-bottom-0 pb-0">
                        <ul class="nav nav-tabs card-header-tabs border-bottom-0" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#sales-invoice" role="tab">
                                    <i class="iconoir-empty-page me-1"></i> Sales Invoice
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#payments" role="tab">
                                    <i class="iconoir-dollar-circle me-1"></i> Payments Summary
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#ar-aging" role="tab">
                                    <i class="iconoir-calendar me-1"></i> A/R Aging
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#profit-loss" role="tab">
                                    <i class="iconoir-graph-up me-1"></i> Laba Rugi
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- Sales Invoice Tab -->
                            <div class="tab-pane fade show active" id="sales-invoice" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover align-middle" id="salesTable">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>No. Faktur</th>
                                                <th>Pelanggan</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($salesData as $invoice)
                                                <tr>
                                                    <td>{{ $invoice->tgl_invoice->format('d/m/Y') }}</td>
                                                    <td>{{ $invoice->nomor_invoice }}</td>
                                                    <td>{{ $invoice->mitra->nama }}</td>
                                                    <td class="fw-bold">Rp
                                                        {{ number_format($invoice->total_akhir, 0, ',', '.') }}</td>
                                                    <td>
                                                        <span
                                                            class="badge bg-{{ $invoice->status_pembayaran == 'Paid' ? 'success' : 'warning' }} rounded-pill px-3">
                                                            {{ $invoice->status_pembayaran }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center py-4 text-muted">
                                                        <i
                                                            class="fa fa-invoice fs-1 text-secondary opacity-50 d-block mb-2"></i>
                                                        Tidak ada data penjualan pada periode ini
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Payments Tab -->
                            <div class="tab-pane fade" id="payments" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover align-middle" id="paymentsTable">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Pelanggan</th>
                                                <th>No. Referensi</th>
                                                <th>Jumlah</th>
                                                <th>Metode</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($paymentsData as $payment)
                                                <tr>
                                                    <td>{{ $payment->tgl_pembayaran->format('d/m/Y') }}</td>
                                                    <td>{{ $payment->invoice->mitra->name }}</td>
                                                    <td>{{ $payment->nomor_pembayaran }}</td>
                                                    <td class="fw-bold text-success">Rp
                                                        {{ number_format($payment->jumlah_bayar, 0, ',', '.') }}</td>
                                                    <td>{{ ucfirst($payment->metode_pembayaran) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center py-4 text-muted">
                                                        <i
                                                            class="fa fa-money-bill fs-1 text-secondary opacity-50 d-block mb-2"></i>
                                                        Tidak ada data pembayaran pada periode ini
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- A/R Aging Tab -->
                            <div class="tab-pane fade" id="ar-aging" role="tabpanel">
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle me-2"></i>
                                    Laporan ini akan dicetak dalam format landscape
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover align-middle" id="arAgingTable">
                                        <thead>
                                            <tr>
                                                <th>Pelanggan</th>
                                                <th>No. Invoice</th>
                                                <th>Jatuh Tempo</th>
                                                <th>Current</th>
                                                <th>1-15</th>
                                                <th>16-30</th>
                                                <th>31-45</th>
                                                <th>46-60</th>
                                                <th>61+</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($arAgingData as $aging)
                                                <tr>
                                                    <td>{{ $aging['customer'] }}</td>
                                                    <td>{{ $aging['invoice_no'] }}</td>
                                                    <td>{{ $aging['due_date'] }}</td>
                                                    <td class="text-end">Rp
                                                        {{ number_format($aging['aging_buckets']['current'], 0, ',', '.') }}
                                                    </td>
                                                    <td class="text-end">Rp
                                                        {{ number_format($aging['aging_buckets']['1_15'], 0, ',', '.') }}
                                                    </td>
                                                    <td class="text-end">Rp
                                                        {{ number_format($aging['aging_buckets']['16_30'], 0, ',', '.') }}
                                                    </td>
                                                    <td class="text-end">Rp
                                                        {{ number_format($aging['aging_buckets']['31_45'], 0, ',', '.') }}
                                                    </td>
                                                    <td class="text-end">Rp
                                                        {{ number_format($aging['aging_buckets']['46_60'], 0, ',', '.') }}
                                                    </td>
                                                    <td class="text-end">Rp
                                                        {{ number_format($aging['aging_buckets']['61_plus'], 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="9" class="text-center py-4 text-muted">
                                                        <i
                                                            class="fa fa-calendar fs-1 text-secondary opacity-50 d-block mb-2"></i>
                                                        Tidak ada data piutang jatuh tempo
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Profit Loss Tab -->
                            <div class="tab-pane fade" id="profit-loss" role="tabpanel">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="card border-0 shadow-sm h-100">
                                            <div class="card-body p-4">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="bg-primary-subtle p-2 rounded-3 me-3">
                                                        <i class="iconoir-nav-arrow-right text-primary fs-4"></i>
                                                    </div>
                                                    <h6 class="card-title mb-0 fw-bold">Pendapatan</h6>
                                                </div>
                                                <div class="space-y-3">
                                                    <div
                                                        class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                                        <span class="text-muted">Total Penjualan</span>
                                                        <span class="fw-bold">Rp
                                                            {{ number_format($profitLossData['sales_revenue'], 0, ',', '.') }}</span>
                                                    </div>
                                                    <div
                                                        class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                                        <span class="text-muted">Pendapatan Lainnya</span>
                                                        <span class="fw-bold">Rp
                                                            {{ number_format($profitLossData['other_revenues'], 0, ',', '.') }}</span>
                                                    </div>
                                                </div>
                                                <div
                                                    class="d-flex justify-content-between align-items-center mt-4 p-3 bg-primary-subtle rounded-3">
                                                    <span class="fw-bold text-primary">Total Pendapatan</span>
                                                    <h5 class="fw-bold text-primary mb-0">Rp
                                                        {{ number_format($profitLossData['sales_revenue'] + $profitLossData['other_revenues'], 0, ',', '.') }}
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-0 shadow-sm h-100">
                                            <div class="card-body p-4">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="bg-danger-subtle p-2 rounded-3 me-3">
                                                        <i class="iconoir-nav-arrow-left text-danger fs-4"></i>
                                                    </div>
                                                    <h6 class="card-title mb-0 fw-bold">Beban & HPP</h6>
                                                </div>
                                                <div class="space-y-3">
                                                    <div
                                                        class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                                        <span class="text-muted">Harga Pokok Penjualan (HPP)</span>
                                                        <span class="fw-bold">Rp
                                                            {{ number_format($profitLossData['cost_of_goods_sold'], 0, ',', '.') }}</span>
                                                    </div>
                                                    <div
                                                        class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                                        <span class="text-muted">Beban Operasional</span>
                                                        <span class="fw-bold">Rp
                                                            {{ number_format($profitLossData['operating_expenses'], 0, ',', '.') }}</span>
                                                    </div>
                                                </div>
                                                <div
                                                    class="d-flex justify-content-between align-items-center mt-4 p-3 bg-danger-subtle rounded-3">
                                                    <span class="fw-bold text-danger">Total Beban</span>
                                                    <h5 class="fw-bold text-danger mb-0">Rp
                                                        {{ number_format($profitLossData['cost_of_goods_sold'] + $profitLossData['operating_expenses'], 0, ',', '.') }}
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card border-0 shadow-sm mt-4 bg-dark text-white overflow-hidden">
                                    <div class="card-body p-4 position-relative">
                                        <div class="row align-items-center">
                                            <div class="col-md-6 border-end border-secondary">
                                                <div class="d-flex justify-content-between align-items-center pe-md-4">
                                                    <div>
                                                        <p class="text-light-subtle small mb-0">Laba Kotor (Gross Profit)
                                                        </p>
                                                        <h3 class="fw-bold mb-0 text-info">Rp
                                                            {{ number_format($profitLossData['gross_profit'], 0, ',', '.') }}
                                                        </h3>
                                                    </div>
                                                    <i class="iconoir-graph-up text-white fs-1 opacity-25"></i>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex justify-content-between align-items-center ps-md-4">
                                                    <div>
                                                        <p class="text-light-subtle small mb-0">Laba Bersih (Net Profit)
                                                        </p>
                                                        <h3 class="fw-bold mb-0 text-success">Rp
                                                            {{ number_format($profitLossData['net_profit'], 0, ',', '.') }}
                                                        </h3>
                                                    </div>
                                                    <i class="iconoir-coins text-white fs-1 opacity-25"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="modal fade" id="modalBulkPreview" tabindex="-1" aria-labelledby="modalBulkPreviewLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-4">
                <div class="modal-header border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" id="modalBulkPreviewLabel">
                        <i class="iconoir-print text-primary me-2"></i> Preview Laporan Massal
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="ratio ratio-16x9 border rounded bg-light" style="min-height: 70vh;">
                        <!-- Loading State -->
                        <div id="preview-loading"
                            class="d-flex flex-column justify-content-center align-items-center h-100">
                            <div class="spinner-border text-primary mb-3" role="status"
                                style="width: 3rem; height: 3rem;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <h6 class="text-muted mb-2">Sedang Memuat Preview</h6>
                            <p class="text-muted small mb-0">Mohon tunggu sebentar...</p>
                        </div>

                        <!-- Iframe (Hidden initially) -->
                        <iframe id="bulk-preview-iframe" src="" allowfullscreen style="display: none;"
                            onload="hideLoading()"></iframe>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-success fw-bold px-4 shadow-sm" onclick="triggerBulkPrint()">
                        <i class="fa fa-print me-1"></i> Cetak Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link href="{{ url('') }}/assets/libs/simple-datatables/style.css" rel="stylesheet" type="text/css" />
    <style>
        .page-wrapper {
            background-color: #f8f9fa;
        }

        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
        }

        /* Fix Tabs Styling */
        .nav-tabs {
            border-bottom: 2px solid #f1f1f1 !important;
        }

        .nav-tabs .nav-link {
            border: none !important;
            color: #6c757d;
            font-weight: 600;
            padding: 1rem 1.5rem;
            position: relative;
            transition: color 0.3s;
        }

        .nav-tabs .nav-link:hover {
            color: #0d6efd;
        }

        .nav-tabs .nav-link.active {
            color: #0d6efd !important;
            background: transparent !important;
        }

        .nav-tabs .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: #0d6efd;
            border-radius: 3px 3px 0 0;
        }

        .tab-content {
            padding-top: 1rem;
        }

        .card-header-tabs {
            margin-bottom: 0 !important;
        }
    </style>
@endpush

@push('js')
    <script src="{{ url('') }}/assets/libs/simple-datatables/umd/simple-datatables.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Initialize DataTables
            const salesTableEl = document.querySelector("#salesTable");
            if (salesTableEl) {
                new simpleDatatables.DataTable(salesTableEl, {
                    searchable: true,
                    fixedHeight: false,
                    perPage: 25,
                    labels: {
                        placeholder: "Cari...",
                        perPage: "item per halaman",
                        noRows: "Tidak ada data ditemukan",
                        info: "Menampilkan {start} sampai {end} dari {rows} entri",
                    }
                });
            }

            const paymentsTableEl = document.querySelector("#paymentsTable");
            if (paymentsTableEl) {
                new simpleDatatables.DataTable(paymentsTableEl, {
                    searchable: true,
                    fixedHeight: false,
                    perPage: 25,
                    labels: {
                        placeholder: "Cari...",
                        perPage: "item per halaman",
                        noRows: "Tidak ada data ditemukan",
                        info: "Menampilkan {start} sampai {end} dari {rows} entri",
                    }
                });
            }

            const arAgingTableEl = document.querySelector("#arAgingTable");
            if (arAgingTableEl) {
                new simpleDatatables.DataTable(arAgingTableEl, {
                    searchable: true,
                    fixedHeight: false,
                    perPage: 25,
                    labels: {
                        placeholder: "Cari...",
                        perPage: "item per halaman",
                        noRows: "Tidak ada data ditemukan",
                        info: "Menampilkan {start} sampai {end} dari {rows} entri",
                    }
                });
            }
        });

        function openPreview(id) {
            const url = `/bulk-reports/${id}/preview`;
            const modalContainer = document.getElementById('modalBulkPreview');

            if (!modalContainer) {
                console.error('Modal container tidak ditemukan di halaman ini.');
                return;
            }

            const iframe = modalContainer.querySelector('iframe');
            const loading = document.getElementById('preview-loading');

            if (!iframe) {
                console.error('Elemen iframe tidak ditemukan di dalam modal.');
                alert('Gagal memuat preview laporan.');
                return;
            }

            // Show loading state
            if (loading) {
                loading.style.display = 'flex';
            }
            iframe.style.display = 'none';

            // Set iframe source
            iframe.src = url;

            // Show modal
            const bModal = bootstrap.Modal.getOrCreateInstance(modalContainer);
            bModal.show();
        }

        function hideLoading() {
            const iframe = document.getElementById('bulk-preview-iframe');
            const loading = document.getElementById('preview-loading');

            if (loading) {
                loading.style.display = 'none';
            }
            if (iframe) {
                iframe.style.display = 'block';
            }
        }

        function triggerBulkPrint() {
            const iframe = document.getElementById('bulk-preview-iframe');
            if (iframe && iframe.contentWindow) {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            }
        }

        // Reset loading state when modal is closed
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('modalBulkPreview');
            if (modal) {
                modal.addEventListener('hidden.bs.modal', function() {
                    const iframe = document.getElementById('bulk-preview-iframe');
                    const loading = document.getElementById('preview-loading');

                    if (iframe) {
                        iframe.src = '';
                        iframe.style.display = 'none';
                    }
                    if (loading) {
                        loading.style.display = 'flex';
                    }
                });
            }
        });
    </script>
@endpush
