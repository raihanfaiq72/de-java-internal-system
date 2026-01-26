@extends('Layout.main')

@section('main')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">

<div class="page-wrapper" style="background-color: #f4f7fa; min-height: 100vh; font-family: 'Inter', sans-serif;">
    <div class="page-content py-4">
        <div class="container-fluid">
            <!-- Header & Filter -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Neraca Keuangan</h3>
                    <p class="text-muted mb-0">Laporan posisi keuangan per tanggal tertentu</p>
                </div>
                <div>
                    <!-- Export Buttons -->
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-download me-1"></i> Unduh
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#" onclick="window.print()"><i class="fa fa-file-pdf me-2"></i>PDF / Cetak</a></li>
                            <li><a class="dropdown-item" href="{{ route('report.balance-sheet.export.csv') }}?date={{ $date }}"><i class="fa fa-file-csv me-2"></i>CSV</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body p-4">
                    <form method="GET" action="{{ route('report.balance-sheet') }}" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tanggal Laporan</label>
                            <input type="date" name="date" class="form-control" value="{{ $date }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fa fa-filter me-1"></i> Tampilkan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mb-4">
                <h5 class="fw-bold text-dark">Dejava Internal System</h5>
                <p class="text-secondary">Per Tanggal: {{ date('d F Y', strtotime($date)) }}</p>
            </div>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-white border-bottom">
                                <tr>
                                    <th class="ps-4 py-3 fw-bold text-dark" style="width: 70%;">Tipe Akun</th>
                                    <th class="text-end pe-4 py-3 fw-bold text-dark" style="width: 30%;">Saldo</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- AKTIVA SECTION --}}
                                <tr style="background-color: #e3f2fd;">
                                    <td colspan="2" class="ps-4 py-2 fw-bold text-dark">Aktiva - 1000</td>
                                </tr>
                                
                                @php $totalAktiva = 0; @endphp
                                @foreach($aktivaGroups as $groupName => $accounts)
                                    @if($accounts->isNotEmpty())
                                        <!-- Group Header -->
                                        <tr>
                                            <td colspan="2" class="ps-4 py-2 fw-semibold text-secondary">
                                                <i class="fa fa-chevron-down me-2" style="font-size: 0.8rem;"></i> {{ $groupName }}
                                            </td>
                                        </tr>
                                        <!-- Accounts -->
                                        @foreach($accounts as $item)
                                        <tr>
                                            <td class="ps-5 py-2" style="padding-left: 3rem !important;">
                                                <span class="text-dark">{{ $item->nama_akun }} - {{ $item->kode_akun }}</span>
                                            </td>
                                            <td class="text-end pe-4 py-2 text-dark">
                                                Rp {{ number_format($item->balance) }}
                                            </td>
                                        </tr>
                                        @endforeach
                                        @php $totalAktiva += $accounts->sum('balance'); @endphp
                                    @endif
                                @endforeach

                                <!-- Total Aktiva -->
                                <tr class="bg-white">
                                    <td class="ps-4 py-3 fw-bold text-dark">Total Aktiva</td>
                                    <td class="text-end pe-4 py-3 fw-bold text-dark">Rp {{ number_format($totalAktiva) }}</td>
                                </tr>

                                {{-- KEWAJIBAN SECTION --}}
                                <tr style="background-color: #e3f2fd;">
                                    <td colspan="2" class="ps-4 py-2 fw-bold text-dark">Kewajiban - 2000</td>
                                </tr>

                                @php $totalKewajiban = 0; @endphp
                                @foreach($kewajibanGroups as $groupName => $accounts)
                                    @if($accounts->isNotEmpty())
                                        <tr>
                                            <td colspan="2" class="ps-4 py-2 fw-semibold text-secondary">
                                                <i class="fa fa-chevron-down me-2" style="font-size: 0.8rem;"></i> {{ $groupName }}
                                            </td>
                                        </tr>
                                        @foreach($accounts as $item)
                                        <tr>
                                            <td class="ps-5 py-2" style="padding-left: 3rem !important;">
                                                <span class="text-dark">{{ $item->nama_akun }} - {{ $item->kode_akun }}</span>
                                            </td>
                                            <td class="text-end pe-4 py-2 text-dark">
                                                Rp {{ number_format($item->balance) }}
                                            </td>
                                        </tr>
                                        @endforeach
                                        @php $totalKewajiban += $accounts->sum('balance'); @endphp
                                    @endif
                                @endforeach

                                <tr class="bg-white">
                                    <td class="ps-4 py-3 fw-bold text-dark">Total Kewajiban</td>
                                    <td class="text-end pe-4 py-3 fw-bold text-dark">Rp {{ number_format($totalKewajiban) }}</td>
                                </tr>

                                {{-- MODAL SECTION --}}
                                <tr style="background-color: #e3f2fd;">
                                    <td colspan="2" class="ps-4 py-2 fw-bold text-dark">Modal - 3000</td>
                                </tr>

                                @php $totalModal = 0; @endphp
                                @foreach($modalGroups as $groupName => $accounts)
                                    @if($accounts->isNotEmpty())
                                        <tr>
                                            <td colspan="2" class="ps-4 py-2 fw-semibold text-secondary">
                                                <i class="fa fa-chevron-down me-2" style="font-size: 0.8rem;"></i> {{ $groupName }}
                                            </td>
                                        </tr>
                                        @foreach($accounts as $item)
                                        <tr>
                                            <td class="ps-5 py-2" style="padding-left: 3rem !important;">
                                                <span class="text-dark">{{ $item->nama_akun }} - {{ $item->kode_akun }}</span>
                                            </td>
                                            <td class="text-end pe-4 py-2 text-dark">
                                                Rp {{ number_format($item->balance) }}
                                            </td>
                                        </tr>
                                        @endforeach
                                        @php $totalModal += $accounts->sum('balance'); @endphp
                                    @endif
                                @endforeach

                                <tr class="bg-white">
                                    <td class="ps-4 py-3 fw-bold text-dark">Total Modal</td>
                                    <td class="text-end pe-4 py-3 fw-bold text-dark">Rp {{ number_format($totalModal) }}</td>
                                </tr>

                                {{-- SUMMARY --}}
                                <tr class="bg-light">
                                    <td class="ps-4 py-3 fw-bold text-dark">Total Kewajiban dan Modal</td>
                                    <td class="text-end pe-4 py-3 fw-bold text-dark">Rp {{ number_format($totalKewajiban + $totalModal) }}</td>
                                </tr>

                                @php $checkBalance = $totalAktiva - ($totalKewajiban + $totalModal); @endphp
                                <tr class="bg-white">
                                    <td class="ps-4 py-3 fw-bold text-dark">Rugi yang Dilaporkan</td>
                                    <td class="text-end pe-4 py-3 fw-bold text-dark">Rp {{ number_format($checkBalance) }}</td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .d-print-none { display: none !important; }
        .page-wrapper { background-color: white !important; }
        .card { box-shadow: none !important; border: none !important; }
        .btn, form { display: none !important; }
        thead th { color: #000 !important; }
    }
</style>
@endsection
