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
                            <div class="col-md-4">
                                <h4 class="fw-bold text-dark mb-1">Laporan Invoice Penjualan</h4>
                                <p class="text-muted small mb-0">Monitor performa penjualan, piutang, dan penagihan.</p>
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
                                <input type="text" class="form-control" name="search" placeholder="Cari Nama Mitra / No. Invoice..." value="{{ request('search') }}">
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
                <!-- Total Penjualan -->
                <div class="col-lg-3">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-body p-4 border-start border-4 border-primary">
                            <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px; letter-spacing: 1px;">Total Penjualan</label>
                            <h4 class="fw-bold mb-0 text-dark">Rp {{ number_format($totalSales, 0, ',', '.') }}</h4>
                            <small class="text-muted mt-2 d-block">Berdasarkan filter periode</small>
                        </div>
                    </div>
                </div>
                <!-- Total Piutang (AR) -->
                <div class="col-lg-3">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-body p-4 border-start border-4 border-warning">
                            <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px; letter-spacing: 1px;">Total Piutang (AR)</label>
                            <h4 class="fw-bold mb-0 text-dark">Rp {{ number_format($totalAR, 0, ',', '.') }}</h4>
                            <small class="text-muted mt-2 d-block">Sisa tagihan belum terbayar</small>
                        </div>
                    </div>
                </div>
                <!-- Piutang Overdue -->
                <div class="col-lg-3">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-body p-4 border-start border-4 border-danger">
                            <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px; letter-spacing: 1px;">Piutang Overdue</label>
                            <h4 class="fw-bold mb-0 text-danger">Rp {{ number_format($totalOverdue, 0, ',', '.') }}</h4>
                            <small class="text-danger fw-semibold mt-2 d-block"><i class="fa fa-exclamation-circle me-1"></i> Lewat Jatuh Tempo</small>
                        </div>
                    </div>
                </div>
                <!-- Rasio Penagihan -->
                <div class="col-lg-3">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-body p-4 border-start border-4 border-success">
                            <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px; letter-spacing: 1px;">Rasio Penagihan</label>
                            <h4 class="fw-bold mb-0 text-success">{{ number_format($collectionRatio, 1) }}%</h4>
                            <div class="progress mt-2" style="height: 6px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $collectionRatio }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4 g-4">
                <!-- 2. Grafik Tren Penjualan Bulanan -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h6 class="fw-bold mb-0">Tren Penjualan Bulanan</h6>
                        </div>
                        <div class="card-body p-4">
                            <div id="salesTrendChart"></div>
                        </div>
                    </div>
                </div>

                <!-- 3. Top 5 Pelanggan -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h6 class="fw-bold mb-0">Top 5 Pelanggan</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="list-group list-group-flush">
                                @foreach($topClientLabels as $index => $label)
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
                                                    $max = $topClientSeries->max() ?: 1;
                                                    $val = $topClientSeries[$index];
                                                    $percent = ($val / $max) * 100;
                                                @endphp
                                                <div class="progress-bar bg-primary" style="width: {{ $percent }}%"></div>
                                            </div>
                                        </div>
                                        <div class="ms-3 text-end">
                                            <span class="fw-bold text-dark small">Rp {{ number_format($topClientSeries[$index], 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                @endforeach
                                @if(count($topClientLabels) == 0)
                                    <div class="text-center text-muted py-4 small">Belum ada data penjualan</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Daftar Invoice Terhutang -->
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark">Daftar Invoice Terhutang (Piutang)</h6>
                    <button class="btn btn-white border btn-sm shadow-sm fw-bold text-dark" onclick="window.print()">
                        <i class="fa fa-file-pdf me-1 text-danger"></i> Export PDF
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3 text-muted small fw-bold text-uppercase">Nomor Invoice</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase">Pelanggan</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase">Tgl Invoice</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase">Jatuh Tempo</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase text-end">Nilai Total</th>
                                <th class="py-3 text-muted small fw-bold text-uppercase text-end">Sisa Piutang</th>
                                <th class="px-4 py-3 text-muted small fw-bold text-uppercase text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $inv)
                                @php
                                    $paid = $inv->payment->sum('jumlah_bayar');
                                    $balance = $inv->total_akhir - $paid;
                                    $isOverdue = \Carbon\Carbon::parse($inv->tgl_jatuh_tempo)->isPast() && $balance > 0;
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
                                        <h6 class="fw-bold text-dark">Semua piutang telah lunas!</h6>
                                        <p class="text-muted small mb-0">Tidak ada invoice penjualan dengan sisa tagihan saat ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white border-top py-3">
                    {{ $invoices->links() }}
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sales Trend Chart
        var optionsTrend = {
            series: [{
                name: 'Total Penjualan',
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

        var chartTrend = new ApexCharts(document.querySelector("#salesTrendChart"), optionsTrend);
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
