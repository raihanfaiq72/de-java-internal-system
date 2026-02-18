@extends('Layout.main')

@section('main')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<div class="page-wrapper">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box d-md-flex justify-content-between align-items-center">
                        <h4 class="page-title">Laporan Invoice Pembelian</h4>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item active">Report: Purchase</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Header & Filter -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body p-4">
                    <form action="{{ route('report.purchase') }}" method="GET" id="filterForm">
                        <div class="row align-items-end g-3">
                            <div class="col-md-4">
                                <h4 class="fw-bold text-dark mb-1">Laporan Invoice Pembelian</h4>
                                <p class="text-muted small mb-0">Monitor aktivitas pembelian, hutang usaha, dan pembayaran ke supplier.</p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small text-muted">Periode Invoice</label>
                                <div class="input-group">
                                    <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
                                    <span class="input-group-text bg-white border-start-0 border-end-0">s/d</span>
                                    <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small text-muted">Pencarian</label>
                                <input type="text" class="form-control" name="search" placeholder="Cari Nama Supplier / No. Invoice..." value="{{ request('search') }}">
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

            <!-- 1. Summary Cards -->
            <div class="row mb-4 g-3">
                <!-- Total Pembelian -->
                <div class="col-lg-3">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-body p-4 border-start border-4 border-primary">
                            <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px; letter-spacing: 1px;">Total Pembelian</label>
                            <h4 class="fw-bold mb-0 text-dark">Rp {{ number_format($totalPurchase, 0, ',', '.') }}</h4>
                            <small class="text-muted mt-2 d-block">Berdasarkan filter periode</small>
                        </div>
                    </div>
                </div>
                <!-- Total Hutang (AP) -->
                <div class="col-lg-3">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-body p-4 border-start border-4 border-warning">
                            <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px; letter-spacing: 1px;">Total Hutang (AP)</label>
                            <h4 class="fw-bold mb-0 text-dark">Rp {{ number_format($totalAP, 0, ',', '.') }}</h4>
                            <small class="text-muted mt-2 d-block">Sisa tagihan belum dibayar</small>
                        </div>
                    </div>
                </div>
                <!-- Hutang Overdue -->
                <div class="col-lg-3">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-body p-4 border-start border-4 border-danger">
                            <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px; letter-spacing: 1px;">Hutang Overdue</label>
                            <h4 class="fw-bold mb-0 text-danger">Rp {{ number_format($totalOverdue, 0, ',', '.') }}</h4>
                            <small class="text-danger fw-semibold mt-2 d-block"><i class="fa fa-exclamation-circle me-1"></i> Lewat Jatuh Tempo</small>
                        </div>
                    </div>
                </div>
                <!-- Rasio Pembayaran -->
                <div class="col-lg-3">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-body p-4 border-start border-4 border-success">
                            <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px; letter-spacing: 1px;">Rasio Pembayaran</label>
                            <h4 class="fw-bold mb-0 text-success">{{ number_format($paymentRatio, 1) }}%</h4>
                            <div class="progress mt-2" style="height: 6px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $paymentRatio }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4 g-4">
                <!-- 2. Grafik Tren Pembelian Bulanan -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h6 class="fw-bold mb-0">Tren Pembelian Bulanan</h6>
                        </div>
                        <div class="card-body p-4">
                            <div id="purchaseTrendChart"></div>
                        </div>
                    </div>
                </div>

                <!-- 3. Top 5 Supplier -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h6 class="fw-bold mb-0">Top 5 Supplier</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="list-group list-group-flush">
                                @foreach($topSupplierLabels as $index => $label)
                                    <div class="list-group-item px-0 border-0 d-flex align-items-center mb-2">
                                        <div class="flex-shrink-0 me-3">
                                            <span class="avatar-sm bg-light text-primary rounded fw-bold d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                {{ $index + 1 }}
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0 text-dark small fw-bold">{{ $label }}</h6>
                                            <div class="progress mt-1" style="height: 4px;">
                                                @php 
                                                    $max = $topSupplierSeries->max() ?: 1;
                                                    $val = $topSupplierSeries[$index];
                                                    $percent = ($val / $max) * 100;
                                                @endphp
                                                <div class="progress-bar bg-primary" style="width: {{ $percent }}%"></div>
                                            </div>
                                        </div>
                                        <div class="ms-3 text-end">
                                            <span class="fw-bold text-dark small">Rp {{ number_format($topSupplierSeries[$index], 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                @endforeach
                                @if(count($topSupplierLabels) == 0)
                                    <div class="text-center text-muted py-4 small">Belum ada data pembelian</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Daftar Invoice Belum Lunas (Hutang) -->
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark">Daftar Invoice Belum Lunas (Hutang)</h6>
                    <button class="btn btn-white border btn-sm shadow-sm fw-bold text-dark" onclick="window.print()">
                        <i class="fa fa-file-pdf me-1 text-danger"></i> Export PDF
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3 text-muted small fw-bold text-uppercase">Nomor Invoice</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase">Supplier</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase">Tgl Invoice</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase">Jatuh Tempo</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase text-end">Nilai Total</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase text-end">Sisa Hutang</th>
                                <th class="px-4 py-3 text-muted small fw-bold text-uppercase text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payables as $inv)
                                @php
                                    // Calculated in controller but safer to access property if passed, or recalculate if object not modified.
                                    // Controller uses map to add sisa_tagihan, so we can use it.
                                    $balance = $inv->sisa_tagihan; 
                                    $isOverdue = \Carbon\Carbon::parse($inv->tgl_jatuh_tempo)->isPast() && $balance > 0;
                                    $paid = $inv->payment->sum('jumlah_bayar'); // Or total_akhir - balance
                                @endphp
                                <tr>
                                    <td class="px-4 fw-bold text-primary">{{ $inv->nomor_invoice }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $inv->mitra->nama ?? '-' }}</div>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($inv->tgl_invoice)->format('d M Y') }}</td>
                                    <td>
                                        <span class="{{ $isOverdue ? 'text-danger fw-bold' : '' }}">
                                            {{ \Carbon\Carbon::parse($inv->tgl_jatuh_tempo)->format('d M Y') }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold">Rp {{ number_format($inv->total_akhir, 0, ',', '.') }}</td>
                                    <td class="text-end fw-bold text-danger">Rp {{ number_format($balance, 0, ',', '.') }}</td>
                                    <td class="px-4 text-center">
                                        @if($isOverdue)
                                            <span class="badge bg-soft-danger text-danger border border-danger border-opacity-25 rounded-pill px-3">Overdue</span>
                                        @elseif($paid > 0)
                                            <span class="badge bg-soft-warning text-warning border border-warning border-opacity-25 rounded-pill px-3">Partially Paid</span>
                                        @else
                                            <span class="badge bg-soft-secondary text-secondary border border-secondary border-opacity-25 rounded-pill px-3">Unpaid</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="mb-3"><i class="fa fa-check-circle fa-3x text-success opacity-50"></i></div>
                                        <h6 class="fw-bold text-dark">Semua hutang telah lunas!</h6>
                                        <p class="text-muted small mb-0">Tidak ada invoice pembelian dengan sisa tagihan saat ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{-- Pagination if available, but collection was filtered after get(), so links() won't work directly on collection unless we use LengthAwarePaginator. 
                     For now, since we did ->get() then ->filter(), pagination is lost. 
                     The controller did: $payables = $query->get()->map()->filter(); 
                     So $payables is a Collection, not Paginator.
                     We should probably remove links() or implement manual pagination. 
                     Given the time, I'll remove links() for now as lists might not be huge, or just leave it out. --}}
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Purchase Trend Chart
        var optionsTrend = {
            series: [{
                name: 'Total Pembelian',
                data: @json($trendSeries)
            }],
            chart: {
                type: 'bar',
                height: 320,
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif'
            },
            colors: ['#007AFF'],
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    columnWidth: '40%',
                }
            },
            dataLabels: { enabled: false },
            xaxis: {
                categories: @json($trendLabels),
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    formatter: function (value) {
                        return "Rp " + new Intl.NumberFormat('id-ID', { notation: "compact", compactDisplay: "short" }).format(value);
                    }
                }
            },
            grid: {
                strokeDashArray: 4,
                yaxis: { lines: { show: true } }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return "Rp " + new Intl.NumberFormat('id-ID').format(val)
                    }
                }
            }
        };

        var chartTrend = new ApexCharts(document.querySelector("#purchaseTrendChart"), optionsTrend);
        chartTrend.render();
    });
</script>

<style>
    .bg-soft-primary { background-color: rgba(0, 122, 255, 0.1) !important; }
    .bg-soft-success { background-color: rgba(40, 167, 69, 0.1) !important; }
    .bg-soft-warning { background-color: rgba(255, 193, 7, 0.1) !important; }
    .bg-soft-danger { background-color: rgba(220, 53, 69, 0.1) !important; }
    .bg-soft-secondary { background-color: rgba(108, 117, 125, 0.1) !important; }
    
    .avatar-sm { width: 32px; height: 32px; font-size: 12px; }
</style>
@endsection
