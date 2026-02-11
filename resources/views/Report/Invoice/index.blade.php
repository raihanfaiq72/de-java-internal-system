@extends('Layout.main')

@section('main')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">

<div class="page-wrapper" style="background-color: #f4f7fa; min-height: 100vh; font-family: 'Inter', sans-serif;">
    <div class="page-content py-4">
        <div class="container-fluid">
            
            <!-- Header & Filter -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body p-4">
                    <form action="{{ route('report.invoice') }}" method="GET" id="filterForm">
                        <div class="row align-items-end g-3">
                            <div class="col-md-3">
                                <h4 class="fw-bold text-dark mb-1">Laporan Invoice</h4>
                                <p class="text-muted small mb-0">Rekapitulasi penjualan, pembayaran, dan produk.</p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small text-muted">Periode Pembayaran</label>
                                <div class="input-group">
                                    <input type="date" class="form-control" name="start_date" value="{{ $startDate }}">
                                    <span class="input-group-text bg-white border-start-0 border-end-0">s/d</span>
                                    <input type="date" class="form-control" name="end_date" value="{{ $endDate }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold small text-muted">Mitra</label>
                                <select class="form-select" name="mitra_id">
                                    <option value="">Semua Mitra</option>
                                    @foreach($mitras as $m)
                                        <option value="{{ $m->id }}" {{ request('mitra_id') == $m->id ? 'selected' : '' }}>{{ $m->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold small text-muted">Pencarian</label>
                                <input type="text" class="form-control" name="search" placeholder="No Invoice/Mitra..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary fw-bold shadow-sm">
                                        <i class="fa fa-filter me-1"></i> Terapkan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabs -->
            <ul class="nav nav-tabs nav-fill mb-4 border-bottom-0" id="reportTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold py-3 rounded-top-3" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                        <i class="iconoir-stats-report me-2"></i>Rekap Umum
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold py-3 rounded-top-3" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button" role="tab">
                        <i class="iconoir-wallet me-2"></i>Rekap Pembayaran
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold py-3 rounded-top-3" id="products-tab" data-bs-toggle="tab" data-bs-target="#products" type="button" role="tab">
                        <i class="iconoir-box-iso me-2"></i>Laporan Produk Terjual
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="reportTabsContent">
                
                <!-- Tab 1: Rekap Umum -->
                <div class="tab-pane fade show active" id="general" role="tabpanel">
                    <div class="row g-4">
                        <!-- Line Chart: Income vs Expenditure -->
                        <div class="col-lg-8">
                            <div class="card border-0 shadow-sm rounded-3 h-100">
                                <div class="card-header bg-white border-0 pt-4 px-4">
                                    <h6 class="fw-bold mb-0">Income vs Expenditure (Tahun {{ $year }})</h6>
                                    <small class="text-muted">Perbandingan pemasukan dan pengeluaran per bulan</small>
                                </div>
                                <div class="card-body p-4">
                                    <div id="incomeExpenseChart"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Pie Chart: Receivables Composition -->
                        <div class="col-lg-4">
                            <div class="card border-0 shadow-sm rounded-3 h-100">
                                <div class="card-header bg-white border-0 pt-4 px-4">
                                    <h6 class="fw-bold mb-0">Komposisi Piutang (Sales)</h6>
                                    <small class="text-muted">Status pembayaran tagihan penjualan</small>
                                </div>
                                <div class="card-body p-4 d-flex align-items-center justify-content-center">
                                    <div id="receivablesPieChart" style="width: 100%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 2: Rekap Pembayaran -->
                <div class="tab-pane fade" id="payments" role="tabpanel">
                    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                        <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">Daftar Pembayaran</h6>
                                <small class="text-muted">
                                    Total: <span class="fw-bold text-primary">Rp {{ number_format($totalPaymentAmount, 0, ',', '.') }}</span> 
                                    ({{ $totalUniqueInvoices }} Invoice)
                                </small>
                            </div>
                            <button class="btn btn-white border btn-sm shadow-sm fw-bold text-dark" onclick="window.print()">
                                <i class="fa fa-print me-1"></i> Print
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="px-4 py-3 text-muted small fw-bold text-uppercase">Tanggal</th>
                                        <th class="py-3 text-muted small fw-bold text-uppercase">No. Invoice</th>
                                        <th class="py-3 text-muted small fw-bold text-uppercase">Metode</th>
                                        <th class="py-3 text-muted small fw-bold text-uppercase">No. Mitra</th>
                                        <th class="py-3 text-muted small fw-bold text-uppercase">Nama Mitra</th>
                                        <th class="px-4 py-3 text-muted small fw-bold text-uppercase text-end">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payments as $payment)
                                        <tr>
                                            <td class="px-4">{{ \Carbon\Carbon::parse($payment->tgl_pembayaran)->format('d M Y') }}</td>
                                            <td class="fw-bold text-primary">{{ $payment->nomor_invoice }}</td>
                                            <td>
                                                <span class="badge bg-soft-info text-info border border-info border-opacity-25 rounded-pill px-2">
                                                    {{ $payment->metode_pembayaran }}
                                                </span>
                                            </td>
                                            <td>{{ $payment->nomor_mitra }}</td>
                                            <td>{{ $payment->nama_mitra }}</td>
                                            <td class="px-4 text-end fw-bold">Rp {{ number_format($payment->jumlah_bayar, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <div class="mb-3"><i class="iconoir-file-not-found fs-1 text-muted opacity-50"></i></div>
                                                <h6 class="fw-bold text-dark">Tidak ada data pembayaran</h6>
                                                <p class="text-muted small mb-0">Sesuaikan filter periode atau pencarian.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="bg-light fw-bold">
                                    <tr>
                                        <td colspan="5" class="px-4 text-end text-uppercase text-muted small">Total Pembayaran</td>
                                        <td class="px-4 text-end text-dark">Rp {{ number_format($totalPaymentAmount, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Tab 3: Laporan Produk Terjual -->
                <div class="tab-pane fade" id="products" role="tabpanel">
                    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                        <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">Daftar Produk Terjual</h6>
                                <small class="text-muted">
                                    Total Qty: <span class="fw-bold text-success">{{ number_format($totalSoldQty, 0, ',', '.') }}</span>
                                    (Berdasarkan pembayaran periode ini)
                                </small>
                            </div>
                            <button class="btn btn-white border btn-sm shadow-sm fw-bold text-dark" onclick="window.print()">
                                <i class="fa fa-print me-1"></i> Print
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="px-4 py-3 text-muted small fw-bold text-uppercase">Nama Produk</th>
                                        <th class="py-3 text-muted small fw-bold text-uppercase">Kode (SKU)</th>
                                        <th class="py-3 text-muted small fw-bold text-uppercase">Kategori</th>
                                        <th class="py-3 text-muted small fw-bold text-uppercase">Satuan</th>
                                        <th class="px-4 py-3 text-muted small fw-bold text-uppercase text-end">Kuantitas Terjual</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($soldProducts as $product)
                                        <tr>
                                            <td class="px-4 fw-bold text-dark">{{ $product->nama_produk }}</td>
                                            <td class="text-muted font-monospace small">{{ $product->sku_kode }}</td>
                                            <td>{{ $product->nama_kategori ?? '-' }}</td>
                                            <td>{{ $product->satuan }}</td>
                                            <td class="px-4 text-end fw-bold text-success">{{ number_format($product->total_qty, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5">
                                                <div class="mb-3"><i class="iconoir-box-iso fs-1 text-muted opacity-50"></i></div>
                                                <h6 class="fw-bold text-dark">Tidak ada produk terjual</h6>
                                                <p class="text-muted small mb-0">Belum ada invoice lunas/terbayar pada periode ini.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="bg-light fw-bold">
                                    <tr>
                                        <td colspan="4" class="px-4 text-end text-uppercase text-muted small">Total Kuantitas</td>
                                        <td class="px-4 text-end text-success">{{ number_format($totalSoldQty, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Income vs Expenditure Chart
    var incomeExpenseOptions = {
        series: [{
            name: 'Income',
            data: @json($chartIncome)
        }, {
            name: 'Expenditure',
            data: @json($chartExpense)
        }],
        chart: {
            type: 'line',
            height: 350,
            toolbar: { show: false },
            fontFamily: 'Inter, sans-serif'
        },
        colors: ['#0d6efd', '#dc3545'], // Blue for Income, Red for Expense
        stroke: {
            curve: 'smooth',
            width: 3
        },
        dataLabels: { enabled: false },
        xaxis: {
            categories: @json($chartLabels),
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                formatter: function (value) {
                    return "Rp " + new Intl.NumberFormat('id-ID').format(value);
                }
            }
        },
        grid: {
            borderColor: '#f1f1f1',
        },
        tooltip: {
            y: {
                formatter: function (value) {
                    return "Rp " + new Intl.NumberFormat('id-ID').format(value);
                }
            }
        }
    };
    var incomeExpenseChart = new ApexCharts(document.querySelector("#incomeExpenseChart"), incomeExpenseOptions);
    incomeExpenseChart.render();

    // 2. Receivables Composition Pie Chart
    var pieOptions = {
        series: @json($pieSeries),
        labels: @json($pieLabels),
        chart: {
            type: 'pie',
            height: 350,
            fontFamily: 'Inter, sans-serif'
        },
        colors: ['#ffc107', '#198754'], // Warning (Outstanding), Success (Received)
        dataLabels: {
            enabled: true,
            formatter: function (val, opts) {
                return opts.w.globals.series[opts.seriesIndex] > 0 ? val.toFixed(1) + "%" : "";
            }
        },
        legend: {
            position: 'bottom'
        },
        tooltip: {
            y: {
                formatter: function (value) {
                    return "Rp " + new Intl.NumberFormat('id-ID').format(value);
                }
            }
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '65%' // Make it a donut if preferred, but type is pie
                }
            }
        }
    };
    var pieChart = new ApexCharts(document.querySelector("#receivablesPieChart"), pieOptions);
    pieChart.render();
    
    // Maintain active tab on refresh (optional, simple implementation)
    // For now, let it default to first tab or use hash if user clicks
});
</script>
@endpush
@endsection
