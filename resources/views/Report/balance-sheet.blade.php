@extends('Layout.main')

@section('main')
<div class="page-wrapper">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box d-md-flex justify-content-between align-items-center">
                        <h4 class="page-title">Neraca Keuangan</h4>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item active">Report: Balance Sheet</li>
                        </ol>
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
                        <div class="col-md-6 text-end">
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
                                @foreach($aktivaGroups as $group)
                                    @php 
                                        $groupTotal = $group->total_balance;
                                        $totalAktiva += $groupTotal;
                                    @endphp
                                    <tr class="group-header" data-group="aktiva-{{ $group->id }}">
                                        <td colspan="2" class="ps-4 py-2 fw-semibold text-secondary" style="cursor: pointer;">
                                            <div class="d-flex justify-content-between">
                                                <span>
                                                    <i class="fa fa-chevron-right me-2 chevron" style="font-size: 0.8rem; transition: transform 0.2s;"></i> 
                                                    {{ $group->nama_kelompok }}
                                                </span>
                                                <span class="me-4 text-muted" style="font-size: 0.9em;">Rp {{ number_format($groupTotal) }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    @foreach($group->type as $type)
                                        @if($type->coas->isNotEmpty())
                                            <tr class="group-row" data-group="aktiva-{{ $group->id }}" style="display: none; background-color: #fcfcfc;">
                                                <td colspan="2" class="ps-5 py-2 fw-bold text-secondary" style="padding-left: 3rem !important; font-size: 0.9em;">
                                                    {{ $type->nama_tipe }}
                                                </td>
                                            </tr>
                                            @foreach($type->coas as $item)
                                            <tr class="group-row" data-group="aktiva-{{ $group->id }}" style="display: none;">
                                                <td class="ps-5 py-2" style="padding-left: 4rem !important;">
                                                    <span class="text-dark">{{ $item->nama_akun }} - {{ $item->kode_akun }}</span>
                                                </td>
                                                <td class="text-end pe-4 py-2 text-dark">
                                                    Rp {{ number_format($item->balance) }}
                                                </td>
                                            </tr>
                                            @endforeach
                                        @endif
                                    @endforeach
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
                                @foreach($kewajibanGroups as $group)
                                    @php 
                                        $groupTotal = $group->total_balance;
                                        $totalKewajiban += $groupTotal;
                                    @endphp
                                    <tr class="group-header" data-group="kewajiban-{{ $group->id }}">
                                        <td colspan="2" class="ps-4 py-2 fw-semibold text-secondary" style="cursor: pointer;">
                                            <div class="d-flex justify-content-between">
                                                <span>
                                                    <i class="fa fa-chevron-right me-2 chevron" style="font-size: 0.8rem; transition: transform 0.2s;"></i> 
                                                    {{ $group->nama_kelompok }}
                                                </span>
                                                <span class="me-4 text-muted" style="font-size: 0.9em;">Rp {{ number_format($groupTotal) }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    @foreach($group->type as $type)
                                        @if($type->coas->isNotEmpty())
                                            <tr class="group-row" data-group="kewajiban-{{ $group->id }}" style="display: none; background-color: #fcfcfc;">
                                                <td colspan="2" class="ps-5 py-2 fw-bold text-secondary" style="padding-left: 3rem !important; font-size: 0.9em;">
                                                    {{ $type->nama_tipe }}
                                                </td>
                                            </tr>
                                            @foreach($type->coas as $item)
                                            <tr class="group-row" data-group="kewajiban-{{ $group->id }}" style="display: none;">
                                                <td class="ps-5 py-2" style="padding-left: 4rem !important;">
                                                    <span class="text-dark">{{ $item->nama_akun }} - {{ $item->kode_akun }}</span>
                                                </td>
                                                <td class="text-end pe-4 py-2 text-dark">
                                                    Rp {{ number_format($item->balance) }}
                                                </td>
                                            </tr>
                                            @endforeach
                                        @endif
                                    @endforeach
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
                                @foreach($modalGroups as $group)
                                    @php 
                                        $groupTotal = $group->total_balance;
                                        $totalModal += $groupTotal;
                                    @endphp
                                    <tr class="group-header" data-group="modal-{{ $group->id }}">
                                        <td colspan="2" class="ps-4 py-2 fw-semibold text-secondary" style="cursor: pointer;">
                                            <div class="d-flex justify-content-between">
                                                <span>
                                                    <i class="fa fa-chevron-right me-2 chevron" style="font-size: 0.8rem; transition: transform 0.2s;"></i> 
                                                    {{ $group->nama_kelompok }}
                                                </span>
                                                <span class="me-4 text-muted" style="font-size: 0.9em;">Rp {{ number_format($groupTotal) }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    @foreach($group->type as $type)
                                        @if($type->coas->isNotEmpty())
                                            <tr class="group-row" data-group="modal-{{ $group->id }}" style="display: none; background-color: #fcfcfc;">
                                                <td colspan="2" class="ps-5 py-2 fw-bold text-secondary" style="padding-left: 3rem !important; font-size: 0.9em;">
                                                    {{ $type->nama_tipe }}
                                                </td>
                                            </tr>
                                            @foreach($type->coas as $item)
                                            <tr class="group-row" data-group="modal-{{ $group->id }}" style="display: none;">
                                                <td class="ps-5 py-2" style="padding-left: 4rem !important;">
                                                    <span class="text-dark">{{ $item->nama_akun }} - {{ $item->kode_akun }}</span>
                                                </td>
                                                <td class="text-end pe-4 py-2 text-dark">
                                                    Rp {{ number_format($item->balance) }}
                                                </td>
                                            </tr>
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endforeach

                                {{-- Laba Tahun Berjalan (Separated) --}}
                                <tr class="bg-white">
                                    <td class="ps-4 py-3 fw-semibold text-dark">Laba Tahun Berjalan</td>
                                    <td class="text-end pe-4 py-3 text-dark">Rp {{ number_format($currentYearEarnings) }}</td>
                                </tr>
                                @php $totalModal += $currentYearEarnings; @endphp

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
@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('tr.group-header').forEach(function(header) {
        header.addEventListener('click', function() {
            const group = header.getAttribute('data-group');
            const rows = document.querySelectorAll('tr.group-row[data-group="' + group + '"]');
            const chevron = header.querySelector('.chevron');
            const isHidden = rows.length ? rows[0].style.display === 'none' : true;
            rows.forEach(function(r) { r.style.display = isHidden ? '' : 'none'; });
            if (chevron) {
                chevron.style.transform = isHidden ? 'rotate(90deg)' : 'rotate(0deg)';
            }
        });
    });
});
</script>
@endpush
@endsection
