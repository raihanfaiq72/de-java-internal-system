@extends('Layout.main')

@include('Layout._dejava_promo')
@include('Layout._tour_guide')

<style>
    .tour-btn-dashboard {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 8px 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 14px;
        color: #6c757d;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .tour-btn-dashboard:hover {
        background: #e9ecef;
        border-color: #adb5bd;
        color: #495057;
        transform: translateY(-1px);
    }

    .tour-btn-dashboard i {
        font-size: 16px;
    }
</style>

@section('main')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="container-fluid">

                <!-- Page Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box d-md-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h4 class="page-title fw-bold">Dashboard <span id="dashboardRoleName">-</span></h4>
                                <p class="text-muted mb-0 small">Memantau operasional di
                                    <strong id="dashboardOfficeName">Kantor Belum Dipilih</strong>
                                </p>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-soft-success text-success p-2">
                                    <i class="fa fa-building me-1"></i> <span id="dashboardOfficeCode">-</span>
                                </span>
                                <button onclick="startDeJavaTour()"
                                    style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 8px 12px; cursor: pointer; font-size: 14px; color: #6c757d; display: inline-flex; align-items: center; gap: 6px;">
                                    <i class="fa fa-question-circle"></i>
                                    <span class="d-none d-md-inline">Tour</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Driver Dashboard -->
                <div id="dashboardDriverSection" class="d-none">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card bg-primary text-white mb-4">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h3 class="text-white fw-bold">Halo, <span
                                                    id="dashboardUserName">{{ auth()->user()->name }}</span>!</h3>
                                            <p class="text-white-50 mb-3">Anda memiliki <span
                                                    id="dashboardActiveJobsHero">0</span>
                                                pengiriman aktif saat ini.</p>
                                            <a href="{{ route('driver.delivery.index') }}"
                                                class="btn btn-light fw-bold text-primary">
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
                                    <h3 class="fw-bold text-dark" id="dashboardActiveJobs">0</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <p class="text-muted small text-uppercase fw-bold">Selesai Hari Ini</p>
                                    <h3 class="fw-bold text-success" id="dashboardCompletedToday">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mt-3 d-none" id="dashboardDriverJobsCard">
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
                                    <tbody id="dashboardDriverJobsTbody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="dashboardAdminSection" class="d-none">
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
                                                    <h3 class="text-white fw-bold">Selamat Datang,
                                                        {{ auth()->user()->name }}!
                                                    </h3>
                                                    <p class="text-white-50">
                                                        Anda login sebagai <strong id="dashboardRoleNameAdmin">-</strong>.
                                                        Hari ini ada <span id="dashboardNewOrdersHero">0</span> pesanan
                                                        baru.
                                                    </p>
                                                    <a href="{{ route('sales') }}"
                                                        class="btn btn-light btn-sm fw-bold px-3">Kelola Penjualan</a>
                                                </div>
                                                <div class="col-sm-4 text-end d-none d-sm-block">
                                                    <img src="{{ url('assets/images/extra/fund.png') }}" alt="img"
                                                        height="120" class="mb-n4">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Stats Grid -->
                                <div class="col-md-6 col-lg-4">
                                    <div class="card border-0 shadow-sm mb-3 bg-soft-primary">
                                        <div class="card-body p-3">
                                            <p class="text-primary font-11 fw-bold text-uppercase mb-1">Total Pendapatan</p>
                                            <h4 class="fw-bold mb-0 text-dark">Rp <span id="dashboardRevenue">0</span></h4>
                                            <div class="mt-1 small text-muted font-10">Sales Terbayar Lunas</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-lg-4">
                                    <div class="card border-0 shadow-sm mb-3">
                                        <div class="card-body p-3">
                                            <p class="text-muted font-11 fw-bold text-uppercase mb-1">Piutang Usaha</p>
                                            <h4 class="fw-bold mb-0 text-danger">Rp <span id="dashboardPiutang">0</span>
                                            </h4>
                                            <div class="mt-1 small text-muted font-10">Tagihan ke Customer</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-lg-4">
                                    <div class="card border-0 shadow-sm mb-3">
                                        <div class="card-body p-3">
                                            <p class="text-muted font-11 fw-bold text-uppercase mb-1">Utang Usaha</p>
                                            <h4 class="fw-bold mb-0 text-warning">Rp <span id="dashboardHutang">0</span>
                                            </h4>
                                            <div class="mt-1 small text-muted font-10">Utang ke Supplier</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-lg-6">
                                    <div class="card border-0 shadow-sm mb-3 bg-soft-success">
                                        <div class="card-body p-3">
                                            <p class="text-success font-11 fw-bold text-uppercase mb-1">Saldo Aktif</p>
                                            <h4 class="fw-bold mb-0 text-dark">Rp <span id="dashboardSaldo">0</span></h4>
                                            <div class="mt-1 small text-muted font-10">Kas & Bank Tersedia</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-lg-6">
                                    <div class="card border-0 shadow-sm mb-3">
                                        <div class="card-body p-3">
                                            <p class="text-muted font-11 fw-bold text-uppercase mb-1">Pendapatan Bersih</p>
                                            <h4 class="fw-bold mb-0 text-success">Rp <span
                                                    id="dashboardLabaBersih">0</span></h4>
                                            <div class="mt-1 small text-muted font-10">Total Pendapatan - Total Beban</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Sidebar (Office Info & Other Stats) -->
                        <div class="col-lg-4">
                            <div class="card border-0 shadow-sm mb-3">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="text-muted font-11 fw-bold text-uppercase mb-1">Pesanan Baru</p>
                                            <h5 class="fw-bold mb-0 text-dark" id="dashboardNewOrders">0</h5>
                                        </div>
                                        <div class="text-end">
                                            <p class="text-muted font-11 fw-bold text-uppercase mb-1">Stok Menipis</p>
                                            <h5 class="fw-bold mb-0 text-danger" id="dashboardLowStock">0</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white py-3 border-0">
                                    <h5 class="card-title mb-0 fw-bold">Informasi Kantor</h5>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="d-flex align-items-center mb-3">
                                        <div
                                            class="avatar-sm bg-soft-primary text-primary rounded-circle me-3 d-flex align-items-center justify-content-center">
                                            <i class="fa fa-map-marker-alt"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold">Lokasi Utama</h6>
                                            <p class="text-muted small mb-0" id="dashboardOfficeCodeInfo">-</p>
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
                                            <tbody id="dashboardRecentTransactionsTbody"></tbody>
                                        </table>
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

@push('js')
    <script src="{{ url('') }}/assets/libs/apexcharts/apexcharts.min.js"></script>
    <script src="{{ url('') }}/assets/js/pages/index.init.js"></script>
    <script>
        function formatRupiah(n) {
            const x = Number(n || 0);
            return x.toLocaleString('id-ID');
        }

        function formatDateTime(v) {
            if (!v) return '-';
            const d = new Date(v);
            if (Number.isNaN(d.getTime())) return '-';
            return d.toLocaleDateString('id-ID') + ' ' + d.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function formatDate(v) {
            if (!v) return '-';
            const d = new Date(v);
            if (Number.isNaN(d.getTime())) return '-';
            return d.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        }
        document.addEventListener('DOMContentLoaded', async function() {
            try {
                const res = await fetch("{{ route('dashboard-api.summary') }}");
                const json = await res.json();
                if (!json.success) throw new Error(json.message || 'Gagal memuat dashboard');
                const d = json.data || {};
                document.getElementById('dashboardRoleName').textContent = d.roleName || '-';
                document.getElementById('dashboardOfficeName').textContent = d.office?.name ||
                    'Kantor Belum Dipilih';
                document.getElementById('dashboardOfficeCode').textContent = d.office?.code || '-';
                document.getElementById('dashboardOfficeCodeInfo').textContent = d.office?.code || '-';
                document.getElementById('dashboardUserName').textContent = d.user?.name || document
                    .getElementById('dashboardUserName').textContent;
                if (d.isDriver) {
                    document.getElementById('dashboardDriverSection').classList.remove('d-none');
                    const active = d.stats?.active_jobs ?? 0;
                    const completed = d.stats?.completed_today ?? 0;
                    document.getElementById('dashboardActiveJobs').textContent = active;
                    document.getElementById('dashboardActiveJobsHero').textContent = active;
                    document.getElementById('dashboardCompletedToday').textContent = completed;
                    const jobs = Array.isArray(d.driverJobs) ? d.driverJobs : [];
                    const card = document.getElementById('dashboardDriverJobsCard');
                    const tbody = document.getElementById('dashboardDriverJobsTbody');
                    tbody.innerHTML = '';
                    if (jobs.length) {
                        card.classList.remove('d-none');
                        jobs.forEach((job) => {
                            const tr = document.createElement('tr');
                            const badge = (job.status === 'in_transit') ? 'bg-warning' : 'bg-info';
                            const statusText = (job.status || '').replace(/_/g, ' ');
                            const href = "{{ route('driver.delivery.show', '__ID__') }}".replace(
                                '__ID__', job.delivery_order_id);
                            tr.innerHTML = `
                                <td class="ps-4 fw-bold">${job.delivery_order_number || '-'}</td>
                                <td><span class="badge ${badge}">${statusText.charAt(0).toUpperCase() + statusText.slice(1)}</span></td>
                                <td>${formatDateTime(job.created_at)}</td>
                                <td class="text-end pe-4"><a href="${href}" class="btn btn-sm btn-primary">Lihat</a></td>
                            `;
                            tbody.appendChild(tr);
                        });
                    } else {
                        card.classList.add('d-none');
                    }
                } else {
                    document.getElementById('dashboardAdminSection').classList.remove('d-none');
                    document.getElementById('dashboardRoleNameAdmin').textContent = d.roleName || '-';
                    document.getElementById('dashboardNewOrdersHero').textContent = d.stats?.new_orders ?? 0;

                    document.getElementById('dashboardRevenue').textContent = formatRupiah(d.stats?.revenue ??
                        0);
                    document.getElementById('dashboardPiutang').textContent = formatRupiah(d.stats
                        ?.piutang_usaha ?? 0);
                    document.getElementById('dashboardHutang').textContent = formatRupiah(d.stats
                        ?.hutang_usaha ?? 0);
                    document.getElementById('dashboardSaldo').textContent = formatRupiah(d.stats?.saldo_aktif ??
                        0);
                    document.getElementById('dashboardLabaBersih').textContent = formatRupiah(d.stats
                        ?.laba_bersih ?? 0);

                    document.getElementById('dashboardNewOrders').textContent = d.stats?.new_orders ?? 0;
                    document.getElementById('dashboardLowStock').textContent = d.stats?.low_stock ?? 0;

                    const tbody = document.getElementById('dashboardRecentTransactionsTbody');
                    tbody.innerHTML = '';
                    const txs = Array.isArray(d.recentTransactions) ? d.recentTransactions : [];
                    if (!txs.length) {
                        tbody.innerHTML =
                            '<tr><td colspan="5" class="text-center py-4 text-muted small">Belum ada transaksi di kantor ini.</td></tr>';
                    } else {
                        txs.forEach((tx) => {
                            const paid = tx.status_pembayaran === 'Paid';
                            const badge = paid ? 'bg-success' : 'bg-warning';
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td class="ps-4 fw-bold text-primary">${tx.nomor_invoice || '-'}</td>
                                <td class="fw-medium">${tx.mitra?.nama || '-'}</td>
                                <td class="fw-bold">Rp ${formatRupiah(tx.total_akhir)}</td>
                                <td><span class="badge ${badge} rounded-pill px-3">${tx.status_pembayaran || 'Unpaid'}</span></td>
                                <td class="text-center">${formatDate(tx.created_at)}</td>
                            `;
                            tbody.appendChild(tr);
                        });
                    }
                }
            } catch (err) {
                console.error('Dashboard Error:', err);
            }
        });
    </script>
@endpush
