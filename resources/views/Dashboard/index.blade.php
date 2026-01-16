@extends('Layout.main')

@section('main')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box d-md-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h4 class="page-title fw-bold">Dashboard Analitik</h4>
                                <p class="text-muted mb-0 small">Memantau operasional di
                                    <strong>{{ $office->name ?? 'Kantor Belum Dipilih' }}</strong></p>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-soft-success text-success p-2">
                                    <i class="iconoir-Pin-outline me-1"></i> {{ $office->code ?? '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card bg-welcome-img overflow-hidden border-0 shadow-sm mb-4"
                                    style="background: linear-gradient(45deg, #007AFF, #00C6FF);">
                                    <div class="card-body p-4">
                                        <div class="row align-items-center">
                                            <div class="col-sm-8">
                                                <h3 class="text-white fw-bold">Selamat Datang, {{ auth()->user()->name }}!
                                                </h3>
                                                <p class="text-white-50">Hari ini ada {{ $newOrders }} pesanan baru di
                                                    kantor {{ $office->name }}. Yuk, selesaikan tugasmu sekarang.</p>
                                                <a href="{{ route('sales') }}"
                                                    class="btn btn-white btn-sm fw-bold px-3">Kelola Penjualan</a>
                                            </div>
                                            <div class="col-sm-4 text-end d-none d-sm-block">
                                                <img src="{{ url('assets/images/extra/fund.png') }}" alt="img"
                                                    height="120" class="mb-n4">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <p class="text-muted font-12 fw-bold text-uppercase mb-1">Total Pendapatan</p>
                                        <h4 class="fw-bold mb-0 text-dark">Rp
                                            {{ number_format($totalRevenue, 0, ',', '.') }}</h4>
                                        <div class="mt-2 small text-success fw-bold">
                                            <i class="fa fa-caret-up me-1"></i> 100% dari Paid Invoice
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <p class="text-muted font-12 fw-bold text-uppercase mb-1">Pesanan Hari Ini</p>
                                        <h4 class="fw-bold mb-0 text-dark">{{ $newOrders }} <small
                                                class="text-muted font-13">Invoice</small></h4>
                                        <div class="mt-2 small text-muted">Berdasarkan tanggal hari ini</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <p class="text-muted font-12 fw-bold text-uppercase mb-1">Status Sistem</p>
                                        <h4 class="fw-bold mb-0 text-success">Online</h4>
                                        <div class="mt-2 small text-muted">Koneksi Database Stabil</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm mt-2">
                            <div class="card-header bg-white py-3 border-0">
                                <h5 class="card-title mb-0 fw-bold">Tren Penjualan Bulanan</h5>
                            </div>
                            <div class="card-body pt-0">
                                <div id="reports" class="apex-charts pill-bar"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white py-3 border-0">
                                <h5 class="card-title mb-0 fw-bold">Status Pembayaran</h5>
                            </div>
                            <div class="card-body pt-0 text-center">
                                <div id="cashflow" class="apex-charts"></div>
                                <div class="row mt-4">
                                    <div class="col-6">
                                        <p class="text-muted font-11 fw-bold text-uppercase mb-1">Lunas</p>
                                        <h5 class="text-success fw-bold mb-0">High</h5>
                                    </div>
                                    <div class="col-6">
                                        <p class="text-muted font-11 fw-bold text-uppercase mb-1">Tertunda</p>
                                        <h5 class="text-danger fw-bold mb-0">Low</h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white py-3 border-0">
                                <h5 class="card-title mb-0 fw-bold">Informasi Kantor</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div
                                        class="avatar-sm bg-soft-primary text-primary rounded-circle me-3 d-flex align-items-center justify-content-center">
                                        <i class="fa fa-map-marker-alt"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">Lokasi Utama</h6>
                                        <p class="text-muted small mb-0">{{ $office->code }}</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div
                                        class="avatar-sm bg-soft-info text-info rounded-circle me-3 d-flex align-items-center justify-content-center">
                                        <i class="fa fa-clock"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">Zona Waktu</h6>
                                        <p class="text-muted small mb-0">Asia/Jakarta (GMT+7)</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm overflow-hidden">
                            <div class="card-header bg-white py-3 border-0">
                                <h5 class="card-title mb-0 fw-bold">Transaksi Terbaru di {{ $office->name }}</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr class="font-11 text-uppercase fw-bold text-muted">
                                                <th class="ps-4">No. Invoice</th>
                                                <th>Pelanggan</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                                <th class="text-center">Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentTransactions as $tx)
                                                <tr>
                                                    <td class="ps-4 fw-bold text-primary">{{ $tx->nomor_invoice }}</td>
                                                    <td class="fw-medium">{{ $tx->mitra->nama }}</td>
                                                    <td class="fw-bold text-dark">Rp
                                                        {{ number_format($tx->total_akhir, 0, ',', '.') }}</td>
                                                    <td>
                                                        <span
                                                            class="badge {{ $tx->status_pembayaran == 'Paid' ? 'bg-success' : 'bg-warning' }} rounded-pill">
                                                            {{ $tx->status_pembayaran }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center text-muted small">
                                                        {{ date('d/m/Y', strtotime($tx->created_at)) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center py-4 text-muted small">Belum ada
                                                        transaksi di kantor ini.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ url('') }}/assets/libs/apexcharts/apexcharts.min.js"></script>
    <script src="{{ url('') }}/assets/js/pages/index.init.js"></script>
@endpush
