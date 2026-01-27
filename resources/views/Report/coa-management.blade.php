@extends('Layout.main')

@section('main')
<div class="page-wrapper">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row align-items-center mb-4">
                <div class="col-md-7">
                    <h4 class="fw-bold text-dark mb-1">Manajemen Chart of Accounts</h4>
                    <p class="text-muted small mb-0">Struktur akun per {{ date('d F Y', strtotime($date)) }}</p>
                </div>
                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                    <a href="{{ route('report.balance-sheet') }}?date={{ $date }}" class="btn btn-outline-primary fw-bold px-3 shadow-sm">
                        <i class="fa fa-table me-1"></i> Lihat Neraca
                    </a>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="card-body p-0">
                    <div class="accordion accordion-flush" id="accordionCOA">
                        @php
                            $labels = [
                                '1' => 'Aktiva (Assets)',
                                '2' => 'Kewajiban (Liabilities)',
                                '3' => 'Modal (Equity)',
                                '4' => 'Pendapatan',
                                '5' => 'Beban',
                                '6' => 'Kelompok 6',
                                '7' => 'Kelompok 7',
                                '8' => 'Kelompok 8',
                                '9' => 'Kelompok 9',
                            ];
                        @endphp
                        @foreach($topGroups as $digit => $list)
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading{{$digit}}">
                                    <button class="accordion-button collapsed fw-bold text-dark bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{$digit}}" aria-expanded="false" aria-controls="collapse{{$digit}}">
                                        <span class="badge bg-light text-primary border me-3">{{ $digit }}</span>
                                        {{ $labels[$digit] ?? "Kelompok $digit" }}
                                    </button>
                                </h2>
                                <div id="collapse{{$digit}}" class="accordion-collapse collapse" aria-labelledby="heading{{$digit}}" data-bs-parent="#accordionCOA">
                                    <div class="accordion-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">Nama Akun</th>
                                                        <th class="text-end pe-4 py-3 text-uppercase small fw-bold text-muted">Kode Akun</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($list as $acc)
                                                        <tr>
                                                            <td class="ps-4 fw-medium text-dark">{{ $acc->nama_akun }}</td>
                                                            <td class="text-end pe-4 font-monospace text-muted">{{ $acc->kode_akun }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
