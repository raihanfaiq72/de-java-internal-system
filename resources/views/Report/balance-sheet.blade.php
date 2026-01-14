@extends('Layout.main')

@section('main')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">

<div class="page-wrapper" style="background-color: #f4f7fa; min-height: 100vh; font-family: 'Inter', sans-serif;">
    <div class="page-content py-4">
        <div class="container-fluid">
            <!-- Header -->
            <div class="text-center mb-5">
                <h3 class="fw-bold text-dark">Neraca Keuangan</h3>
                <h5 class="text-muted">Dejava Internal System</h5>
                <p class="text-secondary small">Per Tanggal: {{ date('d F Y', strtotime($date)) }}</p>
            </div>

            <div class="row g-4">
                <!-- Aktiva Side -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-primary py-3">
                            <h6 class="mb-0 text-white fw-bold">AKTIVA (ASET)</h6>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover mb-0">
                                <tbody>
                                    @php $totalAktiva = 0; @endphp
                                    @foreach($aktiva as $item)
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="fw-semibold text-dark">{{ $item->nama_akun }}</div>
                                            <small class="text-muted f-mono">{{ $item->kode_akun }}</small>
                                        </td>
                                        <td class="text-end pe-4 py-3 fw-bold">
                                            Rp {{ number_format($item->balance) }}
                                        </td>
                                    </tr>
                                    @php $totalAktiva += $item->balance; @endphp
                                    @endforeach
                                    
                                    @if($aktiva->isEmpty())
                                        <tr><td colspan="2" class="text-center p-4 text-muted">Belum ada data Aktiva.</td></tr>
                                    @endif
                                </tbody>
                                <tfoot class="bg-light border-top">
                                    <tr class="fs-5">
                                        <td class="ps-4 py-3 fw-bold text-primary">TOTAL AKTIVA</td>
                                        <td class="text-end pe-4 py-3 fw-bold text-primary">Rp {{ number_format($totalAktiva) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Kewajiban & Modal Side -->
                <div class="col-lg-6">
                    <!-- Kewajiban -->
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-header bg-dark py-3">
                            <h6 class="mb-0 text-white fw-bold">KEWAJIBAN (HUTANG)</h6>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover mb-0">
                                <tbody>
                                    @php $totalKewajiban = 0; @endphp
                                    @foreach($kewajiban as $item)
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="fw-semibold text-dark">{{ $item->nama_akun }}</div>
                                            <small class="text-muted f-mono">{{ $item->kode_akun }}</small>
                                        </td>
                                        <td class="text-end pe-4 py-3 fw-bold">
                                            Rp {{ number_format($item->balance) }}
                                        </td>
                                    </tr>
                                    @php $totalKewajiban += $item->balance; @endphp
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light border-top">
                                    <tr>
                                        <td class="ps-4 py-3 fw-bold">Total Kewajiban</td>
                                        <td class="text-end pe-4 py-3 fw-bold">Rp {{ number_format($totalKewajiban) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Modal -->
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-secondary py-3">
                            <h6 class="mb-0 text-white fw-bold">MODAL (EKUITAS)</h6>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover mb-0">
                                <tbody>
                                    @php $totalModal = 0; @endphp
                                    @foreach($modal as $item)
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="fw-semibold text-dark">{{ $item->nama_akun }}</div>
                                            <small class="text-muted f-mono">{{ $item->kode_akun }}</small>
                                        </td>
                                        <td class="text-end pe-4 py-3 fw-bold">
                                            Rp {{ number_format($item->balance) }}
                                        </td>
                                    </tr>
                                    @php $totalModal += $item->balance; @endphp
                                    @endforeach
                                    {{-- Laba Berjalan --}}
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="fw-semibold text-success">Laba Tahun Berjalan</div>
                                            <small class="text-muted f-mono">3-LABA</small>
                                        </td>
                                        <td class="text-end pe-4 py-3 fw-bold text-success">
                                            Rp {{ number_format($netProfit) }}
                                        </td>
                                    </tr>
                                    @php $totalModal += $netProfit; @endphp
                                </tbody>
                                <tfoot class="bg-light border-top">
                                    <tr class="fs-5">
                                        <td class="ps-4 py-3 fw-bold text-dark">TOTAL KEWAJIBAN & MODAL</td>
                                        <td class="text-end pe-4 py-3 fw-bold text-dark">Rp {{ number_format($totalKewajiban + $totalModal) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Validation Check -->
            @php $diff = abs($totalAktiva - ($totalKewajiban + $totalModal)); @endphp
            @if($diff > 1)
            <div class="alert alert-warning mt-4 shadow-sm border-0">
                <i class="fa fa-exclamation-triangle me-2"></i> PERINGATAN: Neraca belum seimbang! Selisih: <strong>Rp {{ number_format($diff) }}</strong>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
