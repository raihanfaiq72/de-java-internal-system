@extends('Layout.main')

@section('main')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">

    <div class="page-wrapper" style="background-color: #f4f7fa; min-height: 100vh; font-family: 'Inter', sans-serif;">
        <div class="page-content py-4">
            <div class="container-fluid">
                <!-- Header -->
                <div class="row align-items-center mb-4">
                    <div class="col-md-7">
                        <h4 class="fw-bold text-dark mb-1">Laporan Laba Rugi</h4>
                        <p class="text-muted small mb-0">Ringkasan pendapatan dan beban periode {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
                    </div>
                    <div class="col-md-5 text-md-end mt-3 mt-md-0">
                        <button class="btn btn-white border fw-bold px-3 shadow-sm text-dark me-2" onclick="window.print()">
                            <i class="iconoir-printer me-1"></i> Print
                        </button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <form action="{{ route('report.profit-loss') }}" method="GET" class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-muted small text-uppercase">Dari Tanggal</label>
                                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-muted small text-uppercase">Sampai Tanggal</label>
                                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary fw-bold w-100">
                                    <i class="iconoir-filter me-1"></i> Terapkan Filter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row">
                    <!-- Detail Groups -->
                    <div class="col-lg-8">
                        @forelse($report as $group)
                            <div class="card border-0 shadow-sm rounded-3 mb-4">
                                <div class="card-header bg-white border-bottom p-3">
                                    <h6 class="fw-bold mb-0 text-dark">{{ $group['code'] }} - {{ $group['name'] }}</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0 align-middle">
                                            <thead class="bg-light text-muted small text-uppercase">
                                                <tr>
                                                    <th class="px-4 py-2 w-75">Nama Akun</th>
                                                    <th class="py-2 text-end px-4">Saldo</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($group['accounts'] as $account)
                                                    <tr>
                                                        <td class="px-4 py-2 text-muted">{{ $account['nama_akun'] }}</td>
                                                        <td class="text-end px-4 fw-medium text-dark">
                                                            {{ number_format($account['balance'], 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="bg-light fw-bold">
                                                <tr>
                                                    <td class="px-4 py-2 text-uppercase text-muted small">Total {{ $group['name'] }}</td>
                                                    <td class="text-end px-4 text-dark">{{ number_format($group['total_balance'], 0, ',', '.') }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="card border-0 shadow-sm rounded-3 mb-4">
                                <div class="card-body p-5 text-center">
                                    <div class="mb-3"><i class="iconoir-file-not-found fs-1 text-muted opacity-50"></i></div>
                                    <h6 class="fw-bold text-dark">Tidak ada data</h6>
                                    <p class="text-muted small mb-0">Tidak ada transaksi pada periode ini.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <!-- Summary Sidebar -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-3 mb-4 sticky-top" style="top: 20px; z-index: 1;">
                            <div class="card-header bg-primary text-white p-4 rounded-top-3">
                                <h5 class="fw-bold mb-0">Ringkasan Laba Rugi</h5>
                                <p class="mb-0 small opacity-75">Periode {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
                            </div>
                            <div class="card-body p-4">
                                <div class="mb-4">
                                    <label class="text-muted small text-uppercase fw-bold mb-1">Total Pendapatan</label>
                                    <div class="fs-4 fw-bold text-success">
                                        Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}
                                    </div>
                                    <small class="text-muted">(Kelompok 4000 + 7001)</small>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="text-muted small text-uppercase fw-bold mb-1">Total Beban</label>
                                    <div class="fs-4 fw-bold text-danger">
                                        (Rp {{ number_format($summary['total_expense'], 0, ',', '.') }})
                                    </div>
                                    <small class="text-muted">(Kelompok 5000, 6000, 6800, 8001, 9000)</small>
                                </div>

                                <hr class="my-4">

                                <div>
                                    <label class="text-muted small text-uppercase fw-bold mb-1">Laba / Rugi Bersih</label>
                                    <div class="fs-3 fw-bold {{ $summary['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        Rp {{ number_format($summary['net_profit'], 0, ',', '.') }}
                                    </div>
                                    <span class="badge bg-soft-{{ $summary['net_profit'] >= 0 ? 'success' : 'danger' }} text-{{ $summary['net_profit'] >= 0 ? 'success' : 'danger' }} mt-2">
                                        {{ $summary['net_profit'] >= 0 ? 'LABA' : 'RUGI' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
