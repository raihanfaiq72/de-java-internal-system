@extends('Layout.main')

@section('main')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="container-fluid">

                <!-- Page Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box d-md-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h4 class="page-title fw-bold">Dashboard {{ $roleName }}</h4>
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

                <!-- Driver Dashboard -->
                @if($isDriver)
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card bg-primary text-white mb-4">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h3 class="text-white fw-bold">Halo, {{ auth()->user()->name }}!</h3>
                                            <p class="text-white-50 mb-3">Anda memiliki {{ $stats['active_jobs'] ?? 0 }} pengiriman aktif saat ini.</p>
                                            <a href="{{ route('driver.delivery.index') }}" class="btn btn-light fw-bold text-primary">
                                                <i class="iconoir-steering-wheel me-1"></i> Buka Aplikasi Driver
                                            </a>
                                        </div>
                                        <div class="d-none d-md-block">
                                            <i class="iconoir-delivery-truck display-1 text-white-50"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <p class="text-muted small text-uppercase fw-bold">Pengiriman Aktif</p>
                                    <h3 class="fw-bold text-dark">{{ $stats['active_jobs'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <p class="text-muted small text-uppercase fw-bold">Selesai Hari Ini</p>
                                    <h3 class="fw-bold text-success">{{ $stats['completed_today'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(count($driverJobs) > 0)
                        <div class="card border-0 shadow-sm mt-3">
                            <div class="card-header bg-white py-3 border-0">
                                <h5 class="card-title mb-0 fw-bold">Daftar Tugas Pengiriman</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="ps-4">No. DO</th>
                                                <th>Status</th>
                                                <th>Tanggal</th>
                                                <th class="text-end pe-4">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($driverJobs as $job)
                                                <tr>
                                                    <td class="ps-4 fw-bold">{{ $job->deliveryOrder->delivery_order_number ?? '-' }}</td>
                                                    <td>
                                                        <span class="badge {{ $job->status == 'in_transit' ? 'bg-warning' : 'bg-info' }}">
                                                            {{ ucfirst(str_replace('_', ' ', $job->status)) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $job->created_at->format('d/m/Y H:i') }}</td>
                                                    <td class="text-end pe-4">
                                                        <a href="{{ route('driver.delivery.show', $job->delivery_order_id) }}" class="btn btn-sm btn-primary">
                                                            Lihat
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                @else
                <!-- General / Admin Dashboard -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="row">
                            <!-- Welcome Card -->
                            <div class="col-md-12">
                                <div class="card bg-welcome-img overflow-hidden border-0 shadow-sm mb-4"
                                    style="background: linear-gradient(45deg, #007AFF, #00C6FF);">
                                    <div class="card-body p-4">
                                        <div class="row align-items-center">
                                            <div class="col-sm-8">
                                                <h3 class="text-white fw-bold">Selamat Datang, {{ auth()->user()->name }}!</h3>
                                                <p class="text-white-50">
                                                    Anda login sebagai <strong>{{ $roleName }}</strong>.
                                                    Hari ini ada {{ $stats['new_orders'] ?? 0 }} pesanan baru.
                                                </p>
                                                <a href="{{ route('sales') }}" class="btn btn-white btn-sm fw-bold px-3">Kelola Penjualan</a>
                                            </div>
                                            <div class="col-sm-4 text-end d-none d-sm-block">
                                                <img src="{{ url('assets/images/extra/fund.png') }}" alt="img" height="120" class="mb-n4">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Stats Grid -->
                            <div class="col-md-6 col-lg-3">
                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body p-3">
                                        <p class="text-muted font-10 fw-bold text-uppercase mb-1">Total Pendapatan</p>
                                        <h5 class="fw-bold mb-0 text-dark">Rp {{ number_format($stats['revenue'] ?? 0, 0, ',', '.') }}</h5>
                                        <div class="mt-1 small text-success fw-bold font-10">Paid Invoice</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-3">
                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body p-3">
                                        <p class="text-muted font-10 fw-bold text-uppercase mb-1">Pesanan Baru</p>
                                        <h5 class="fw-bold mb-0 text-dark">{{ $stats['new_orders'] ?? 0 }}</h5>
                                        <div class="mt-1 small text-muted font-10">Hari Ini</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-3">
                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body p-3">
                                        <p class="text-muted font-10 fw-bold text-uppercase mb-1">Hutang Pembelian</p>
                                        <h5 class="fw-bold mb-0 text-warning">{{ $stats['pending_purchases'] ?? 0 }}</h5>
                                        <div class="mt-1 small text-muted font-10">Belum Lunas</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-3">
                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body p-3">
                                        <p class="text-muted font-10 fw-bold text-uppercase mb-1">Stok Menipis</p>
                                        <h5 class="fw-bold mb-0 text-danger">{{ $stats['low_stock'] ?? 0 }}</h5>
                                        <div class="mt-1 small text-muted font-10">Perlu Restock</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Charts -->
                        <div class="card border-0 shadow-sm mt-2">
                            <div class="card-header bg-white py-3 border-0">
                                <h5 class="card-title mb-0 fw-bold">Tren Penjualan Bulanan</h5>
                            </div>
                            <div class="card-body pt-0">
                                <div id="reports" class="apex-charts pill-bar"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Sidebar -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white py-3 border-0">
                                <h5 class="card-title mb-0 fw-bold">Status Pembayaran</h5>
                            </div>
                            <div class="card-body pt-0 text-center">
                                <div id="cashflow" class="apex-charts"></div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white py-3 border-0">
                                <h5 class="card-title mb-0 fw-bold">Informasi Kantor</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar-sm bg-soft-primary text-primary rounded-circle me-3 d-flex align-items-center justify-content-center">
                                        <i class="fa fa-map-marker-alt"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">Lokasi Utama</h6>
                                        <p class="text-muted small mb-0">{{ $office->code }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="row mt-2">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm overflow-hidden">
                            <div class="card-header bg-white py-3 border-0">
                                <h5 class="card-title mb-0 fw-bold">Transaksi Penjualan Terbaru</h5>
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
                                                    <td class="fw-medium">{{ $tx->mitra->nama ?? '-' }}</td>
                                                    <td class="fw-bold text-dark">Rp {{ number_format($tx->total_akhir, 0, ',', '.') }}</td>
                                                    <td>
                                                        <span class="badge {{ $tx->status_pembayaran == 'Paid' ? 'bg-success' : 'bg-warning' }} rounded-pill">
                                                            {{ $tx->status_pembayaran }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center text-muted small">{{ date('d/m/Y', strtotime($tx->created_at)) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center py-4 text-muted small">Belum ada transaksi di kantor ini.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ url('') }}/assets/libs/apexcharts/apexcharts.min.js"></script>
    <script src="{{ url('') }}/assets/js/pages/index.init.js"></script>
@endpush
