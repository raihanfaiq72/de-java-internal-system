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
                                                <th class="text-nowrap">Tanggal</th>
                                                <th class="text-nowrap">No. Faktur</th>
                                                <th>Pelanggan</th>
                                                <th class="text-nowrap">Total</th>
                                                <th class="text-nowrap">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($salesData as $invoice)
                                                <tr>
                                                    <td>{{ $invoice->tgl_invoice->format('d/m/Y') }}</td>
                                                    <td>{{ $invoice->nomor_invoice }}</td>
                                                    <td>{{ $invoice->mitra->nama }}</td>
                                                    <td class="fw-bold text-nowrap">Rp
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
                                                <th class="text-nowrap">No.</th>
                                                <th class="text-nowrap">No. Invoice</th>
                                                <th class="text-nowrap">Tanggal</th>
                                                <th class="text-nowrap">Status</th>
                                                <th class="text-nowrap">Metode</th>
                                                <th class="text-nowrap">No. Mitra</th>
                                                <th>Nama Mitra</th>
                                                <th class="text-nowrap">Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $paymentsTotal = 0; @endphp
                                            @forelse($paymentsData as $i => $payment)
                                                @php $paymentsTotal += $payment->jumlah_bayar; @endphp
                                                <tr>
                                                    <td>{{ $i + 1 }}</td>
                                                    <td>{{ $payment->invoice->nomor_invoice ?? '-' }}</td>
                                                    <td>{{ $payment->tgl_pembayaran->format('d/m/Y') }}</td>
                                                    <td>
                                                        <span
                                                            class="badge bg-{{ $payment->invoice && $payment->invoice->status_pembayaran == 'Paid' ? 'success' : 'warning' }} rounded-pill px-2 small">
                                                            {{ $payment->invoice->status_pembayaran ?? '-' }}
                                                        </span>
                                                    </td>
                                                    <td>{{ ucfirst($payment->metode_pembayaran) }}</td>
                                                    <td>{{ $payment->invoice->mitra->kode ?? '-' }}</td>
                                                    <td>{{ $payment->invoice->mitra->nama ?? '-' }}</td>
                                                    <td class="fw-bold text-success text-nowrap">Rp
                                                        {{ number_format($payment->jumlah_bayar, 0, ',', '.') }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center py-4 text-muted">
                                                        <i
                                                            class="fa fa-money-bill fs-1 text-secondary opacity-50 d-block mb-2"></i>
                                                        Tidak ada data pembayaran pada periode ini
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        @if ($paymentsData->count() > 0)
                                            <tfoot>
                                                <tr class="bg-light fw-bold">
                                                    <td colspan="7" class="text-end">TOTAL</td>
                                                    <td class="text-success text-nowrap">Rp
                                                        {{ number_format($paymentsTotal, 0, ',', '.') }}</td>
                                                </tr>
                                            </tfoot>
                                        @endif
                                    </table>
                                </div>
                            </div>

                            <!-- A/R Aging Tab -->
                            <div class="tab-pane fade" id="ar-aging" role="tabpanel">
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle me-2"></i>
                                    Laporan ini akan dicetak dalam format landscape
                                </div>
                                @php
                                    $groupedAging = collect($arAgingData)->groupBy('customer');
                                @endphp

                                @forelse($groupedAging as $customer => $items)
                                    <div class="mb-4">
                                        <h6 class="fw-bold text-primary mb-3">
                                            <i class="iconoir-user me-1"></i> Mitra: {{ $customer }}
                                        </h6>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover align-middle">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th class="text-nowrap">No. Invoice</th>
                                                        <th class="text-nowrap">Jatuh Tempo</th>
                                                        <th class="text-nowrap text-end">Current</th>
                                                        <th class="text-nowrap text-end">1 - 15</th>
                                                        <th class="text-nowrap text-end">16 - 30</th>
                                                        <th class="text-nowrap text-end">31 - 45</th>
                                                        <th class="text-nowrap text-end">46 - 60</th>
                                                        <th class="text-nowrap text-end">61+</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $subTotal = [
                                                            'current' => 0,
                                                            '1_15' => 0,
                                                            '16_30' => 0,
                                                            '31_45' => 0,
                                                            '46_60' => 0,
                                                            '61_plus' => 0,
                                                        ];
                                                    @endphp
                                                    @foreach ($items as $item)
                                                        @php
                                                            foreach ($subTotal as $key => $val) {
                                                                $subTotal[$key] += $item['aging_buckets'][$key] ?? 0;
                                                            }
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $item['invoice_no'] }}</td>
                                                            <td>{{ $item['due_date'] }}</td>
                                                            <td class="text-end text-nowrap">Rp.
                                                                {{ number_format($item['aging_buckets']['current'], 2, '.', ',') }}
                                                            </td>
                                                            <td class="text-end text-nowrap">Rp.
                                                                {{ number_format($item['aging_buckets']['1_15'], 2, '.', ',') }}
                                                            </td>
                                                            <td class="text-end text-nowrap">Rp.
                                                                {{ number_format($item['aging_buckets']['16_30'], 2, '.', ',') }}
                                                            </td>
                                                            <td class="text-end text-nowrap">Rp.
                                                                {{ number_format($item['aging_buckets']['31_45'], 2, '.', ',') }}
                                                            </td>
                                                            <td class="text-end text-nowrap">Rp.
                                                                {{ number_format($item['aging_buckets']['46_60'], 2, '.', ',') }}
                                                            </td>
                                                            <td class="text-end text-nowrap">Rp.
                                                                {{ number_format($item['aging_buckets']['61_plus'], 2, '.', ',') }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot class="bg-light fw-bold">
                                                    <tr>
                                                        <td class="text-end">TOTAL</td>
                                                        <td></td>
                                                        <td class="text-end text-nowrap text-primary">Rp.
                                                            {{ number_format($subTotal['current'], 2, '.', ',') }}</td>
                                                        <td class="text-end text-nowrap text-primary">Rp.
                                                            {{ number_format($subTotal['1_15'], 2, '.', ',') }}</td>
                                                        <td class="text-end text-nowrap text-primary">Rp.
                                                            {{ number_format($subTotal['16_30'], 2, '.', ',') }}</td>
                                                        <td class="text-end text-nowrap text-primary">Rp.
                                                            {{ number_format($subTotal['31_45'], 2, '.', ',') }}</td>
                                                        <td class="text-end text-nowrap text-primary">Rp.
                                                            {{ number_format($subTotal['46_60'], 2, '.', ',') }}</td>
                                                        <td class="text-end text-nowrap text-primary">Rp.
                                                            {{ number_format($subTotal['61_plus'], 2, '.', ',') }}</td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted">
                                        <i class="fa fa-calendar fs-1 text-secondary opacity-50 d-block mb-2"></i>
                                        Tidak ada data piutang jatuh tempo
                                    </div>
                                @endforelse
                            </div>

                            <!-- Profit Loss Tab -->
                            <div class="tab-pane fade" id="profit-loss" role="tabpanel">
                                <div class="card border-0 shadow-sm overflow-hidden mb-4">
                                    <div class="card-header bg-light py-3 border-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 fw-bold text-dark">Ringkasan Laporan Laba Rugi</h6>
                                            <span class="badge bg-primary px-3 py-2">Periode:
                                                {{ $bulkReport->period_name }}</span>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th class="ps-4 py-3">Tipe Akun</th>
                                                        <th class="text-end pe-4 py-3">Saldo</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($profitLossData['report'] as $group)
                                                        @if ($group['total_balance'] != 0 || collect($group['types'])->flatMap(fn($t) => $t['accounts'])->count() > 0)
                                                            <tr class="table-light">
                                                                <td colspan="2" class="ps-4 fw-bold text-uppercase"
                                                                    style="letter-spacing: 1px; font-size: 0.85rem;">
                                                                    {{ $group['name'] }}</td>
                                                            </tr>
                                                            @foreach ($group['types'] as $type)
                                                                <tr>
                                                                    <td class="ps-5 fw-bold text-dark">
                                                                        {{ $type['name'] }}</td>
                                                                    <td class="text-end pe-4 fw-bold text-dark">
                                                                        Rp.
                                                                        {{ number_format($type['total_balance'], 2, ',', '.') }}
                                                                    </td>
                                                                </tr>
                                                                @foreach ($type['accounts'] as $account)
                                                                    <tr>
                                                                        <td class="ps-5 text-muted"
                                                                            style="padding-left: 4rem !important;">
                                                                            {{ $account['name'] }} -
                                                                            {{ $account['code'] }}</td>
                                                                        <td class="text-end pe-4 text-muted">
                                                                            Rp.
                                                                            {{ number_format($account['balance'], 2, ',', '.') }}
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            @endforeach
                                                            <tr class="bg-light-subtle">
                                                                <td class="ps-4 fw-bold text-primary">Total
                                                                    {{ $group['name'] }}</td>
                                                                <td class="text-end pe-4 fw-bold text-primary">
                                                                    Rp.
                                                                    {{ number_format($group['total_balance'], 2, ',', '.') }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-dark py-4 border-0">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <h5 class="text-white mb-0 fw-bold ps-3">Laba Bersih</h5>
                                            </div>
                                            <div class="col-6 text-end">
                                                <h4 class="text-primary mb-0 fw-bold pe-3">
                                                    Rp. {{ number_format($profitLossData['net_profit'], 2, ',', '.') }}
                                                </h4>
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
                loading.classList.remove('d-none');
                loading.style.removeProperty('display');
                loading.style.display = 'flex';
            }
            iframe.style.display = 'none';

            // Set iframe source
            iframe.src = url;

            // Show modal
            const bModal = bootstrap.Modal.getOrCreateInstance(modalContainer);
            bModal.show();

            // Safety timeout in case onload doesn't fire
            setTimeout(() => {
                const loadingElement = document.getElementById('preview-loading');
                if (loadingElement && loadingElement.style.display !== 'none' && !loadingElement.classList.contains('d-none')) {
                    console.log('Safety timeout triggered, forcing hideLoading...');
                    hideLoading();
                }
            }, 5000);
        }

        function hideLoading() {
            const iframe = document.getElementById('bulk-preview-iframe');
            const loading = document.getElementById('preview-loading');

            console.log('Iframe loaded, hiding loading overlay...');

            if (loading) {
                loading.classList.add('d-none');
                loading.style.setProperty('display', 'none', 'important');
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
                        loading.classList.remove('d-none');
                        loading.style.removeProperty('display');
                        loading.style.display = 'flex';
                    }
                });
            }
        });
    </script>
@endpush
