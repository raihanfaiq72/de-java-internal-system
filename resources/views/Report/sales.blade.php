@extends('Layout.main')

@section('main')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">

<div class="page-wrapper" style="background-color: #f4f7fa; min-height: 100vh; font-family: 'Inter', sans-serif;">
    <div class="page-content py-4">
        <div class="container-fluid">
            <!-- Header -->
            <div class="row align-items-center mb-4">
                <div class="col-md-7">
                    <h4 class="fw-bold text-dark mb-1">Laporan Penjualan & Piutang</h4>
                    <p class="text-muted small mb-0">Monitor performa penjualan dan kesehatan piutang usaha.</p>
                </div>
                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                    <button class="btn btn-white border fw-bold px-3 shadow-sm text-dark me-2">
                        <i class="fa fa-download me-1"></i> Export PDF
                    </button>
                    <button class="btn btn-primary fw-bold px-4 shadow-sm">
                        <i class="fa fa-filter me-1"></i> Filter Periode
                    </button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4 g-3">
                <div class="col-lg-3">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-body p-4 border-start border-4 border-primary">
                            <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px; letter-spacing: 1px;">Total Penjualan</label>
                            <h4 class="fw-bold mb-0 text-dark">Rp {{ number_format($stats->total_revenue ?? 0) }}</h4>
                            <small class="text-success fw-semibold mt-2 d-block"><i class="fa fa-arrow-up me-1"></i> 12% dari bulan lalu</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-body p-4 border-start border-4 border-warning">
                            <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px; letter-spacing: 1px;">Total Piutang (AR)</label>
                            <h4 class="fw-bold mb-0 text-dark">Rp {{ number_format($stats->total_ar ?? 0) }}</h4>
                            <small class="text-muted mt-2 d-block">{{ $stats->total_count }} Dokumen Invoice</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-body p-4 border-start border-4 border-danger">
                            <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px; letter-spacing: 1px;">Piutang Overdue</label>
                            <h4 class="fw-bold mb-0 text-danger">Rp {{ number_format($stats->total_overdue ?? 0) }}</h4>
                            <small class="text-danger fw-semibold mt-2 d-block"><i class="fa fa-exclamation-triangle me-1"></i> Perlu Penagihan Segera</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-body p-4 border-start border-4 border-success">
                            <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px; letter-spacing: 1px;">Rasio Penagihan</label>
                            @php 
                                $collected = ($stats->total_revenue - $stats->total_ar);
                                $ratio = $stats->total_revenue > 0 ? ($collected / $stats->total_revenue) * 100 : 0;
                            @endphp
                            <h4 class="fw-bold mb-0 text-success">{{ number_format($ratio, 1) }}%</h4>
                            <div class="progress mt-2" style="height: 6px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $ratio }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4 g-4">
                <!-- Main Chart -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h6 class="fw-bold mb-0">Tren Penjualan Bulanan ({{ date('Y') }})</h6>
                        </div>
                        <div class="card-body p-4">
                            <canvas id="salesChart" height="280"></canvas>
                        </div>
                    </div>
                </div>
                <!-- Top Clients -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h6 class="fw-bold mb-0">Top 5 Pelanggan</h6>
                        </div>
                        <div class="card-body p-4">
                            @foreach($topClients as $client)
                            <div class="d-flex align-items-center mb-4">
                                <div class="flex-shrink-0 avatar-sm bg-light rounded text-primary d-flex align-items-center justify-content-center fw-bold">
                                    {{ substr($client->nama, 0, 2) }}
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0 fw-bold">{{ $client->nama }}</h6>
                                    <small class="text-muted">Total Transaksi</small>
                                </div>
                                <div class="text-end">
                                    <h6 class="mb-0 fw-bold">Rp {{ number_format($client->value) }}</h6>
                                </div>
                            </div>
                            @endforeach
                            @if($topClients->isEmpty())
                                <p class="text-center text-muted">Belum ada data pelanggan.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Piutang Section -->
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="card-header bg-white border-bottom p-4">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="fw-bold mb-0">Daftar Invoice Terhutang (Piutang)</h6>
                        </div>
                        <div class="col-auto">
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control border-end-0" placeholder="Cari Pelanggan...">
                                <span class="input-group-text bg-white border-start-0 text-muted"><i class="fa fa-search"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-uppercase text-muted" style="font-size: 11px; letter-spacing: 0.5px;">
                                <tr>
                                    <th class="ps-4 py-3">No. Invoice</th>
                                    <th>Pelanggan</th>
                                    <th>Tgl. Invoice</th>
                                    <th>Tempo</th>
                                    <th class="text-end">Nilai Total</th>
                                    <th class="text-end">Sisa Piutang</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- We would fetch these from DB where balance > 0 --}}
                                @php
                                    $invoices = DB::table('invoices')
                                        ->join('mitras', 'invoices.mitra_id', '=', 'mitras.id')
                                        ->where('tipe_invoice', 'Sales')
                                        ->where('status_pembayaran', '!=', 'Paid')
                                        ->select('invoices.*', 'mitras.nama as mitra_nama')
                                        ->get();
                                @endphp
                                @foreach($invoices as $inv)
                                    @php
                                        $paid = DB::table('payments')->where('invoice_id', $inv->id)->sum('jumlah_bayar');
                                        $balance = $inv->total_akhir - $paid;
                                    @endphp
                                    <tr>
                                        <td class="ps-4"><span class="fw-bold text-primary">{{ $inv->nomor_invoice }}</span></td>
                                        <td><div class="fw-semibold">{{ $inv->mitra_nama }}</div></td>
                                        <td>{{ date('d M Y', strtotime($inv->tgl_invoice)) }}</td>
                                        <td>
                                            @php $isOverdue = strtotime($inv->tgl_jatuh_tempo) < time(); @endphp
                                            <span class="{{ $isOverdue ? 'text-danger fw-bold' : '' }}">
                                                {{ date('d M Y', strtotime($inv->tgl_jatuh_tempo)) }}
                                            </span>
                                        </td>
                                        <td class="text-end fw-medium">Rp {{ number_format($inv->total_akhir) }}</td>
                                        <td class="text-end"><span class="badge bg-soft-warning text-warning fw-bold fs-6">Rp {{ number_format($balance) }}</span></td>
                                        <td class="text-center">
                                            @if($isOverdue)
                                                <span class="badge bg-danger">OVERDUE</span>
                                            @else
                                                <span class="badge bg-warning text-dark">{{ $inv->status_pembayaran }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                @if($invoices->isEmpty())
                                    <tr><td colspan="7" class="text-center p-4 text-muted fst-italic">Semua piutang telah lunas. Luar biasa!</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('salesChart').getContext('2d');
    const monthlyData = @json($monthlyData);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Penjualan Bulanan',
                data: monthlyData,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#fff',
                pointBorderWidth: 2,
                pointBorderColor: '#3b82f6'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9' },
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + (value/1000000).toFixed(1) + 'M';
                        }
                    }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
@endpush

@endsection
