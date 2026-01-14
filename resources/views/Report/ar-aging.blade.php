@extends('Layout.main')

@section('main')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">

<div class="page-wrapper" style="background-color: #f4f7fa; min-height: 100vh; font-family: 'Inter', sans-serif;">
    <div class="page-content py-4">
        <div class="container-fluid">
            <!-- Header -->
            <div class="row align-items-center mb-4">
                <div class="col-md-7">
                    <h4 class="fw-bold text-dark mb-1">Laporan Umur Piutang (AR Aging)</h4>
                    <p class="text-muted small mb-0">Analisis keterlambatan pembayaran invoice berdasarkan rentang waktu.</p>
                </div>
                <div class="col-md-5 text-md-end">
                    <button class="btn btn-white border fw-bold shadow-sm me-2"><i class="fa fa-download me-1"></i> PDF</button>
                    <button class="btn btn-primary fw-bold shadow-sm">Cetak Laporan</button>
                </div>
            </div>

            <!-- Aging Buckets -->
            <div class="row g-3 mb-4">
                @php
                    $buckets = [
                        ['label' => 'Belum Jatuh Tempo', 'value' => $agingBuckets['current'], 'color' => 'success'],
                        ['label' => '1 - 30 Hari', 'value' => $agingBuckets['1_30'], 'color' => 'info'],
                        ['label' => '31 - 60 Hari', 'value' => $agingBuckets['31_60'], 'color' => 'warning'],
                        ['label' => '61 - 90 Hari', 'value' => $agingBuckets['61_90'], 'color' => 'orange'],
                        ['label' => '> 90 Hari', 'value' => $agingBuckets['90_plus'], 'color' => 'danger'],
                    ];
                @endphp
                @foreach($buckets as $bucket)
                <div class="col">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-body p-3 text-center border-bottom border-4 border-{{ $bucket['color'] }}">
                            <small class="text-muted fw-bold text-uppercase" style="font-size: 10px;">{{ $bucket['label'] }}</small>
                            <h5 class="fw-bold mb-0 mt-1">Rp {{ number_format($bucket['value']) }}</h5>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="card-header bg-white border-bottom p-4">
                    <h6 class="fw-bold mb-0">Rincian Piutang Per Pelanggan</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-uppercase text-muted" style="font-size: 11px; letter-spacing: 0.5px;">
                                <tr>
                                    <th class="ps-4">No. Invoice</th>
                                    <th>Pelanggan</th>
                                    <th>Tgl. Invoice</th>
                                    <th>Jatuh Tempo</th>
                                    <th class="text-center">Keterlambatan</th>
                                    <th class="text-end pe-4">Sisa Piutang</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoices as $inv)
                                <tr>
                                    <td class="ps-4"><span class="fw-bold text-primary">{{ $inv->nomor_invoice }}</span></td>
                                    <td><div class="fw-semibold">{{ $inv->client_name }}</div></td>
                                    <td>{{ date('d M Y', strtotime($inv->tgl_invoice)) }}</td>
                                    <td>{{ date('d M Y', strtotime($inv->tgl_jatuh_tempo)) }}</td>
                                    <td class="text-center">
                                        @if($inv->days_overdue > 0)
                                            <span class="badge bg-soft-danger text-danger fw-bold">{{ $inv->days_overdue }} Hari</span>
                                        @else
                                            <span class="badge bg-soft-success text-success">Lancar</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <h6 class="mb-0 fw-bold">Rp {{ number_format($inv->balance) }}</h6>
                                    </td>
                                </tr>
                                @endforeach
                                @if($invoices->isEmpty())
                                    <tr><td colspan="6" class="text-center p-5 text-muted fst-italic">Tidak ada piutang outstanding saat ini.</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .bg-soft-danger { background-color: rgba(220, 53, 69, 0.1); }
    .bg-soft-success { background-color: rgba(25, 135, 84, 0.1); }
    .bg-soft-info { background-color: rgba(13, 202, 240, 0.1); }
    .border-orange { border-color: #fd7e14 !important; }
</style>
@endsection
