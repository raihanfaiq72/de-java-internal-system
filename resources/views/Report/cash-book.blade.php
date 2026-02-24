@extends('Layout.main')

@section('main')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box d-md-flex justify-content-between align-items-center">
                            <h4 class="page-title">Laporan Kas Besar</h4>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                                <li class="breadcrumb-item active">Report: Kas Besar</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4 d-flex justify-content-between align-items-end">
                        <form action="{{ route('report.cash-book') }}" method="GET" class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-muted small text-uppercase">Akun Keuangan</label>
                                <select name="account_id" class="form-select">
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}" {{ $accountId == $acc->id ? 'selected' : '' }}>
                                            {{ $acc->code }} - {{ $acc->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-muted small text-uppercase">Dari Tanggal</label>
                                <input type="date" name="start_date" class="form-control" value="{{ request('start_date', $start->toDateString()) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-muted small text-uppercase">Sampai Tanggal</label>
                                <input type="date" name="end_date" class="form-control" value="{{ request('end_date', $end->toDateString()) }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary fw-bold w-100">Preview</button>
                            </div>
                        </form>
                        <div class="text-end">
                            <button onclick="window.print()" class="btn btn-white border fw-bold px-3 shadow-sm text-dark">
                                <i class="iconoir-print me-1"></i> Cetak
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4">
                        <div class="text-center mb-3">
                            <h5 class="mb-1 fw-bold">LAPORAN KAS BESAR {{ $officeName }}</h5>
                            <div class="text-muted">PERIODE {{ \Carbon\Carbon::parse($start)->translatedFormat('j F Y') }} - {{ \Carbon\Carbon::parse($end)->translatedFormat('j F Y') }}</div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead>
                                    <tr style="background:#d94f19;color:#fff;">
                                        <th class="text-center" style="width:60px;">No</th>
                                        <th class="text-center" style="width:140px;">Tanggal</th>
                                        <th class="text-center" style="width:140px;">Nama Akun</th>
                                        <th class="text-center">Keterangan</th>
                                        <th class="text-center" style="width:160px;">Debit</th>
                                        <th class="text-center" style="width:160px;">Kredit</th>
                                        <th class="text-center" style="width:180px;">Total Saldo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rows as $i => $r)
                                        <tr>
                                            <td class="text-center">{{ $i + 1 }}</td>
                                            <td class="text-center">{{ \Carbon\Carbon::parse($r['date'])->translatedFormat('j-M-y') }}</td>
                                            <td class="text-center">{{ $r['type'] }}</td>
                                            <td>{{ $r['description'] }}</td>
                                            <td class="text-end">Rp {{ number_format($r['debit'], 0, ',', '.') }}</td>
                                            <td class="text-end">Rp {{ number_format($r['credit'], 0, ',', '.') }}</td>
                                            <td class="text-end {{ $r['balance'] < 0 ? 'text-danger' : '' }}">Rp {{ number_format($r['balance'], 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="4" class="text-end fw-bold">TOTAL</td>
                                        <td class="text-end fw-bold">Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold">Rp {{ number_format($totalCredit, 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold text-danger">Rp {{ number_format($finalBalance, 0, ',', '.') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('css')
    <style>
        @media print {
            .page-title-box, .breadcrumb, form, .btn, .card.border-0.shadow-sm.rounded-3.mb-4 { display: none !important; }
            .table th, .table td { font-size: 12px; }
        }
    </style>
@endpush
