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
                            <h4 class="my-2 fs-20 fw-bold">Rp <span id="piutangTotalSaldo">0,00</span></h4>
                            <p class="text-muted mb-3 small">Dana Sedang Diproses: <strong>Rp 0.00</strong></p>
                            <button class="btn btn-primary w-100">Terima Pembayaran</button>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-corner-img">
                        <div class="card-body">
                            <p class="text-muted text-uppercase mb-0 fs-12">Piutang Usaha</p>
                            <h4 class="mt-1 mb-3 fw-bold text-primary">Rp <span id="piutangTotal">0,00</span></h4>
                            <div class="row text-center bg-light rounded g-0 p-2">
                                <div class="col-4 border-end">
                                    <h6 class="m-0" id="piutangUnpaid">0</h6>
                                    <small class="font-11">Unpaid</small>
                                </div>
                                <div class="col-4 border-end">
                                    <h6 class="m-0" id="piutangPartial">0</h6>
                                    <small class="font-11">Partial</small>
                                </div>
                                <div class="col-4">
                                    <h6 class="m-0 text-danger" id="piutangOverdue">0</h6>
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
                            <h4 class="mt-1 mb-3 fw-bold text-danger">Rp <span id="utangTotal">0,00</span></h4>
                            <div class="row text-center bg-light rounded g-0 p-2">
                                <div class="col-4 border-end">
                                    <h6 class="m-0" id="utangUnpaid">0</h6>
                                    <small class="font-11">Unpaid</small>
                                </div>
                                <div class="col-4 border-end">
                                    <h6 class="m-0" id="utangPartial">0</h6>
                                    <small class="font-11">Partial</small>
                                </div>
                                <div class="col-4">
                                    <h6 class="m-0 text-danger" id="utangOverdue">0</h6>
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
                                    <h3 class="fw-bold" id="piutangTotalLabaRugi">Rp 0,00</h3>
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
        function formatRp(v) {
            return Number(v || 0).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
        fetch("{{ route('dashboard-piutang-api.summary') }}")
            .then(r => r.json())
            .then(json => {
                if (!json.success) throw new Error(json.message || 'Gagal memuat data');
                const d = json.data || {};
                document.getElementById('piutangTotalSaldo').textContent = formatRp(d.totalSaldo);
                document.getElementById('piutangTotal').textContent = formatRp(d.piutang?.total_nilai || 0);
                document.getElementById('piutangUnpaid').textContent = d.piutang?.count_unpaid || 0;
                document.getElementById('piutangPartial').textContent = d.piutang?.count_partial || 0;
                document.getElementById('piutangOverdue').textContent = d.piutang?.count_overdue || 0;
                document.getElementById('utangTotal').textContent = formatRp(d.utang?.total_nilai || 0);
                document.getElementById('utangUnpaid').textContent = d.utang?.count_unpaid || 0;
                document.getElementById('utangPartial').textContent = d.utang?.count_partial || 0;
                document.getElementById('utangOverdue').textContent = d.utang?.count_overdue || 0;
                const laba = Number(d.totalLabaRugi || 0);
                const labaEl = document.getElementById('piutangTotalLabaRugi');
                labaEl.textContent = 'Rp ' + formatRp(laba);
                labaEl.classList.add(laba >= 0 ? 'text-success' : 'text-danger');
                var options = {
                    chart: { height: 320, type: 'area', toolbar: { show: false } },
                    colors: ['#22c55e', '#ef4444'],
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth', width: 2 },
                    series: [{
                        name: 'Pendapatan',
                        data: d.monthlyIncome || []
                    }, {
                        name: 'Pengeluaran',
                        data: d.monthlyExpense || []
                    }],
                    xaxis: {
                        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                    },
                    tooltip: {
                        y: { formatter: function (val) { return "Rp " + Number(val || 0).toLocaleString('id-ID') } }
                    }
                };
                var chart = new ApexCharts(document.querySelector("#chart_laba_rugi"), options);
                chart.render();
            })
            .catch(e => console.error(e));
    });
</script>
@endpush
