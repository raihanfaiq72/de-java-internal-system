@extends('Layout.main')

@section('main')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box d-md-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="page-title">Detail Laporan Massal</h4>
                                <p class="text-muted small mb-0">Periode: {{ $bulkReport->period_name }}</p>
                            </div>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('bulk-reports.index') }}">Laporan Massal</a></li>
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
                                <a href="{{ route('bulk-reports.generate-pdf', $bulkReport->id) }}" target="_blank" class="btn btn-success fw-bold px-4 shadow-sm">
                                    <i class="fa fa-file-pdf me-1"></i> Generate PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Tabs -->
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-white border-bottom">
                        <ul class="nav nav-tabs card-header-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#sales-invoice" role="tab">
                                    <i class="iconoir-file-invoice me-1"></i> Sales Invoice
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#payments" role="tab">
                                    <i class="iconoir-money-send me-1"></i> Payments Summary
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
                                                    <td>{{ $invoice->mitra->name }}</td>
                                                    <td class="fw-bold">Rp {{ number_format($invoice->total_akhir, 0, ',', '.') }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $invoice->status_pembayaran == 'Lunas' ? 'success' : 'warning' }} rounded-pill px-3">
                                                            {{ $invoice->status_pembayaran }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center py-4 text-muted">
                                                        <i class="fa fa-invoice fs-1 text-secondary opacity-50 d-block mb-2"></i>
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
                                                    <td class="fw-bold text-success">Rp {{ number_format($payment->jumlah_bayar, 0, ',', '.') }}</td>
                                                    <td>{{ ucfirst($payment->metode_pembayaran) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center py-4 text-muted">
                                                        <i class="fa fa-money-bill fs-1 text-secondary opacity-50 d-block mb-2"></i>
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
                                                    <td class="text-end">Rp {{ number_format($aging['aging_buckets']['current'], 0, ',', '.') }}</td>
                                                    <td class="text-end">Rp {{ number_format($aging['aging_buckets']['1_15'], 0, ',', '.') }}</td>
                                                    <td class="text-end">Rp {{ number_format($aging['aging_buckets']['16_30'], 0, ',', '.') }}</td>
                                                    <td class="text-end">Rp {{ number_format($aging['aging_buckets']['31_45'], 0, ',', '.') }}</td>
                                                    <td class="text-end">Rp {{ number_format($aging['aging_buckets']['46_60'], 0, ',', '.') }}</td>
                                                    <td class="text-end">Rp {{ number_format($aging['aging_buckets']['61_plus'], 0, ',', '.') }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="9" class="text-center py-4 text-muted">
                                                        <i class="fa fa-calendar fs-1 text-secondary opacity-50 d-block mb-2"></i>
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
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-body">
                                                <h6 class="card-title text-primary">Pendapatan</h6>
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between">
                                                        <span>Penjualan</span>
                                                        <strong>Rp {{ number_format($profitLossData['sales_revenue'], 0, ',', '.') }}</strong>
                                                    </div>
                                                    <div class="d-flex justify-content-between">
                                                        <span>Pendapatan Lainnya</span>
                                                        <strong>Rp {{ number_format($profitLossData['other_revenues'], 0, ',', '.') }}</strong>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="d-flex justify-content-between">
                                                    <span class="fw-bold">Total Pendapatan</span>
                                                    <strong class="text-primary">Rp {{ number_format($profitLossData['sales_revenue'] + $profitLossData['other_revenues'], 0, ',', '.') }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-body">
                                                <h6 class="card-title text-danger">Beban</h6>
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between">
                                                        <span>Harga Pokok Penjualan</span>
                                                        <strong>Rp {{ number_format($profitLossData['cost_of_goods_sold'], 0, ',', '.') }}</strong>
                                                    </div>
                                                    <div class="d-flex justify-content-between">
                                                        <span>Beban Operasional</span>
                                                        <strong>Rp {{ number_format($profitLossData['operating_expenses'], 0, ',', '.') }}</strong>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="d-flex justify-content-between">
                                                    <span class="fw-bold">Total Beban</span>
                                                    <strong class="text-danger">Rp {{ number_format($profitLossData['cost_of_goods_sold'] + $profitLossData['operating_expenses'], 0, ',', '.') }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card border-0 shadow-sm mt-3">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="d-flex justify-content-between">
                                                    <h6>Laba Kotor</h6>
                                                    <h6 class="text-primary">Rp {{ number_format($profitLossData['gross_profit'], 0, ',', '.') }}</h6>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex justify-content-between">
                                                    <h6>Laba Bersih</h6>
                                                    <h6 class="text-success">Rp {{ number_format($profitLossData['net_profit'], 0, ',', '.') }}</h6>
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
@endsection

@push('css')
<link href="{{ url('') }}/assets/libs/simple-datatables/style.css" rel="stylesheet" type="text/css" />
@endpush

@push('js')
<script src="{{ url('') }}/assets/libs/simple-datatables/umd/simple-datatables.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Sales Invoice Table
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

    // Payments Table
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

    // A/R Aging Table
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
</script>
@endpush
