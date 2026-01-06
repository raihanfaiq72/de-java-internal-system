@extends('Layout.main')
@section('main')
<div class="page-wrapper">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                        <h4 class="page-title">Dashboard Keuangan</h4>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#">Approx</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="card bg-globe-img">
                        <div class="card-body">
                            <span class="fs-16 fw-semibold">Total Saldo Aktif</span>
                            <h4 class="my-2 fs-20 fw-bold">Rp {{ number_format($totalSaldo, 2) }}</h4>
                            <p class="text-muted mb-3 small">Dana Sedang Diproses: <strong>Rp 0.00</strong></p>
                            <button class="btn btn-primary w-100">Terima Pembayaran</button>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-corner-img">
                        <div class="card-body">
                            <p class="text-muted text-uppercase mb-0 fs-12">Piutang Usaha</p>
                            <h4 class="mt-1 mb-3 fw-bold text-primary">Rp {{ number_format($piutangData->total_nilai, 2) }}</h4>
                            <div class="row text-center bg-light rounded g-0 p-2">
                                <div class="col-4 border-end">
                                    <h6 class="m-0">{{ $piutangData->count_unpaid }}</h6>
                                    <small class="font-11">Unpaid</small>
                                </div>
                                <div class="col-4 border-end">
                                    <h6 class="m-0">{{ $piutangData->count_partial }}</h6>
                                    <small class="font-11">Partial</small>
                                </div>
                                <div class="col-4">
                                    <h6 class="m-0 text-danger">{{ $piutangData->count_overdue }}</h6>
                                    <small class="font-11">Overdue</small>
                                </div>
                            </div>
                            <button class="btn btn-soft-primary w-100 mt-3">Kirim Invoice</button>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-corner-img">
                        <div class="card-body">
                            <p class="text-muted text-uppercase mb-0 fs-12">Utang Usaha</p>
                            <h4 class="mt-1 mb-3 fw-bold text-danger">Rp {{ number_format($utangData->total_nilai, 2) }}</h4>
                            <div class="row text-center bg-light rounded g-0 p-2">
                                <div class="col-4 border-end">
                                    <h6 class="m-0">{{ $utangData->count_unpaid }}</h6>
                                    <small class="font-11">Unpaid</small>
                                </div>
                                <div class="col-4 border-end">
                                    <h6 class="m-0">{{ $utangData->count_partial }}</h6>
                                    <small class="font-11">Partial</small>
                                </div>
                                <div class="col-4">
                                    <h6 class="m-0 text-danger">{{ $utangData->count_overdue }}</h6>
                                    <small class="font-11">Overdue</small>
                                </div>
                            </div>
                            <button class="btn btn-soft-danger w-100 mt-3">Bayar Tagihan</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h4 class="card-title">Laba dan Rugi</h4>
                                    <p class="mb-0 text-muted">Total laba (rugi) bersih periode tahun 2026</p>
                                </div>
                                <div class="col-auto">
                                    <h3 class="{{ $totalLabaRugi >= 0 ? 'text-success' : 'text-danger' }} fw-bold">
                                        Rp {{ number_format($totalLabaRugi, 2) }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="chart_laba_rugi" class="apex-charts"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('js')
<script src="{{url('')}}/assets/libs/apexcharts/apexcharts.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var options = {
            chart: { height: 320, type: 'area', toolbar: { show: false } },
            colors: ['#22c55e', '#ef4444'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            series: [{
                name: 'Pendapatan',
                data: @json($monthlyIncome)
            }, {
                name: 'Pengeluaran',
                data: @json($monthlyExpense)
            }],
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            },
            tooltip: {
                y: { formatter: function (val) { return "Rp " + val.toLocaleString() } }
            }
        };
        var chart = new ApexCharts(document.querySelector("#chart_laba_rugi"), options);
        chart.render();
    });
</script>
@endpush