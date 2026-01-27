<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-0">Manajemen COA</h5>
            <small class="text-muted">Struktur akun per {{ date('d F Y', strtotime($date)) }}</small>
        </div>
        <div>
            <a href="{{ route('report.balance-sheet') }}?date={{ $date }}" target="_blank" class="btn btn-sm btn-outline-primary">
                Lihat Neraca
            </a>
        </div>
    </div>

    <div class="accordion" id="accordionCOA">
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
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{$digit}}" aria-expanded="false" aria-controls="collapse{{$digit}}">
                        {{ $labels[$digit] ?? "Kelompok $digit" }}
                    </button>
                </h2>
                <div id="collapse{{$digit}}" class="accordion-collapse collapse" aria-labelledby="heading{{$digit}}" data-bs-parent="#accordionCOA">
                    <div class="accordion-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-3">Nama Akun</th>
                                        <th class="text-end pe-3">Kode</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($list as $acc)
                                        <tr>
                                            <td class="ps-3">{{ $acc->nama_akun }}</td>
                                            <td class="text-end pe-3">{{ $acc->kode_akun }}</td>
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
