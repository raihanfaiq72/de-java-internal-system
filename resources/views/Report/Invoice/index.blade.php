@extends('Layout.main')

@section('main')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">

<div class="page-wrapper" style="background-color: #f4f7fa; min-height: 100vh; font-family: 'Inter', sans-serif;">
    <div class="page-content py-4">
        <div class="container-fluid">
            
            <!-- Header -->
            <div class="row align-items-center mb-4">
                <div class="col-md-7">
                    <h4 class="fw-bold text-dark mb-1">Laporan Invoice</h4>
                    <p class="text-muted small mb-0">Rekapitulasi invoice, pembayaran, dan piutang usaha.</p>
                </div>
                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                    <button class="btn btn-white border fw-bold px-3 shadow-sm text-dark me-2">
                        <i class="fa fa-print me-1"></i> Cetak Laporan
                    </button>
                </div>
            </div>

            <!-- Tabs -->
            <ul class="nav nav-tabs nav-tabs-custom border-bottom-0 mb-4" id="reportTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold px-4 py-2" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-selected="true">
                        <i class="fa fa-chart-line me-2"></i> Rekap Umum
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold px-4 py-2" id="payment-tab" data-bs-toggle="tab" data-bs-target="#payment" type="button" role="tab" aria-selected="false">
                        <i class="fa fa-table me-2"></i> Rekap Pembayaran
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="reportTabsContent">
                
                <!-- Tab: Rekap Umum -->
                <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                    <div class="row g-4">
                        <!-- Line Chart -->
                        <div class="col-lg-8">
                            <div class="card border-0 shadow-sm rounded-3 h-100">
                                <div class="card-header bg-white border-0 pt-4 px-4">
                                    <h6 class="fw-bold mb-0">Arus Kas (Pemasukan vs Pengeluaran)</h6>
                                    <small class="text-muted">Berdasarkan pembayaran invoice Sales & Purchase 12 bulan terakhir</small>
                                </div>
                                <div class="card-body p-4">
                                    <div id="cashFlowChart"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Pie Chart -->
                        <div class="col-lg-4">
                            <div class="card border-0 shadow-sm rounded-3 h-100">
                                <div class="card-header bg-white border-0 pt-4 px-4">
                                    <h6 class="fw-bold mb-0">Komposisi Piutang</h6>
                                    <small class="text-muted">Top 5 Mitra dengan sisa tagihan Sales terbesar</small>
                                </div>
                                <div class="card-body p-4 d-flex align-items-center justify-content-center">
                                    <div id="receivableChart" class="w-100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab: Rekap Pembayaran -->
                <div class="tab-pane fade" id="payment" role="tabpanel" aria-labelledby="payment-tab">
                    
                    <!-- Filters -->
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-body p-4">
                            <form action="{{ route('report.invoice') }}" method="GET" id="filterForm">
                                <input type="hidden" name="tab" value="payment">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold small text-muted">Mitra</label>
                                        <select class="form-select" name="mitra_id">
                                            <option value="">Semua Mitra</option>
                                            @foreach($mitras as $mitra)
                                                <option value="{{ $mitra->id }}" {{ request('mitra_id') == $mitra->id ? 'selected' : '' }}>
                                                    {{ $mitra->nama }} - {{ $mitra->nomor_mitra }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold small text-muted">Periode</label>
                                        <div class="input-group">
                                            <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
                                            <span class="input-group-text bg-white border-start-0 border-end-0">s/d</span>
                                            <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small text-muted">Pencarian</label>
                                        <input type="text" class="form-control" name="search" placeholder="Cari No Invoice, No Pembayaran, Nama..." value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary fw-bold w-100 shadow-sm">
                                            <i class="fa fa-filter me-1"></i> Terapkan
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                        <div class="card-header bg-white border-bottom py-3 px-4">
                            <h6 class="mb-0 fw-bold text-dark">Riwayat Pembayaran</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="px-4 py-3 text-muted small fw-bold text-uppercase" width="50">No</th>
                                        <th class="py-3 text-muted small fw-bold text-uppercase">No. Pembayaran</th>
                                        <th class="py-3 text-muted small fw-bold text-uppercase">Tanggal</th>
                                        <th class="py-3 text-muted small fw-bold text-uppercase">Invoice</th>
                                        <th class="py-3 text-muted small fw-bold text-uppercase">Mitra</th>
                                        <th class="py-3 text-muted small fw-bold text-uppercase text-end">Jumlah Bayar</th>
                                        <th class="px-4 py-3 text-muted small fw-bold text-uppercase text-center">Metode</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payments as $payment)
                                        <tr>
                                            <td class="px-4 text-muted">{{ $loop->iteration + ($payments->currentPage() - 1) * $payments->perPage() }}</td>
                                            <td class="fw-bold text-primary">{{ $payment->nomor_pembayaran }}</td>
                                            <td>{{ \Carbon\Carbon::parse($payment->tgl_pembayaran)->format('d M Y') }}</td>
                                            <td>
                                                @if($payment->invoice)
                                                    <span class="badge bg-soft-info text-info border border-info border-opacity-25 rounded-pill px-2">
                                                        {{ $payment->invoice->nomor_invoice }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($payment->invoice && $payment->invoice->mitra)
                                                    <div class="fw-bold text-dark">{{ $payment->invoice->mitra->nama }}</div>
                                                    <div class="small text-muted">{{ $payment->invoice->mitra->nomor_mitra }}</div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-end fw-bold text-dark">Rp {{ number_format($payment->jumlah_bayar, 0, ',', '.') }}</td>
                                            <td class="px-4 text-center">
                                                <span class="badge bg-light text-dark border">{{ $payment->metode_pembayaran }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">
                                                <div class="mb-2"><i class="fa fa-search fa-2x opacity-25"></i></div>
                                                Tidak ada data pembayaran ditemukan.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer bg-white border-top py-3">
                            {{ $payments->links() }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Activate correct tab if present in URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('tab') === 'payment' || urlParams.get('mitra_id') || urlParams.get('search')) {
            const triggerEl = document.querySelector('#payment-tab');
            const tab = new bootstrap.Tab(triggerEl);
            tab.show();
        }

        // Line Chart: Income vs Expense
        var optionsCashFlow = {
            series: [{
                name: 'Pemasukan (Sales)',
                data: @json($incomes)
            }, {
                name: 'Pengeluaran (Purchase)',
                data: @json($expenses)
            }],
            chart: {
                type: 'area',
                height: 350,
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif'
            },
            colors: ['#00E396', '#FF4560'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            xaxis: {
                categories: @json($months),
            },
            yaxis: {
                labels: {
                    formatter: function (value) {
                        return "Rp " + new Intl.NumberFormat('id-ID').format(value);
                    }
                }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.2,
                    stops: [0, 90, 100]
                }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return "Rp " + new Intl.NumberFormat('id-ID').format(val)
                    }
                }
            }
        };

        var chartCashFlow = new ApexCharts(document.querySelector("#cashFlowChart"), optionsCashFlow);
        chartCashFlow.render();

        // Pie Chart: Receivables
        var optionsPie = {
            series: @json($pieSeries),
            labels: @json($pieLabels),
            chart: {
                type: 'donut',
                height: 350,
                fontFamily: 'Inter, sans-serif'
            },
            colors: ['#008FFB', '#00E396', '#FEB019', '#FF4560', '#775DD0'],
            plotOptions: {
                pie: {
                    donut: {
                        size: '65%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total Piutang',
                                formatter: function (w) {
                                    const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    return "Rp " + new Intl.NumberFormat('id-ID', { notation: "compact", compactDisplay: "short" }).format(total);
                                }
                            }
                        }
                    }
                }
            },
            dataLabels: { enabled: false },
            legend: { position: 'bottom' },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return "Rp " + new Intl.NumberFormat('id-ID').format(val)
                    }
                }
            }
        };

        var chartPie = new ApexCharts(document.querySelector("#receivableChart"), optionsPie);
        chartPie.render();
    });
</script>

<style>
    .nav-tabs-custom .nav-link {
        color: #6c757d;
        border: none;
        border-bottom: 2px solid transparent;
        transition: all 0.2s;
    }
    .nav-tabs-custom .nav-link:hover {
        color: #0d6efd;
    }
    .nav-tabs-custom .nav-link.active {
        color: #0d6efd;
        background: transparent;
        border-bottom: 2px solid #0d6efd;
    }
    .badge.bg-soft-info {
        background-color: rgba(13, 202, 240, 0.1) !important;
    }
</style>
@endsection
