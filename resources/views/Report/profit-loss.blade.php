@extends('Layout.main')

@section('main')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">

<div class="page-wrapper" style="background-color: #f4f7fa; min-height: 100vh; font-family: 'Inter', sans-serif;">
    <div class="page-content py-4">
        <div class="container-fluid">
            <!-- Header -->
            <div class="text-center mb-5">
                <h3 class="fw-bold text-dark">Laporan Laba Rugi</h3>
                <h5 class="text-muted">Dejava Internal System</h5>
                <p class="text-secondary small">Periode: Januari {{ $year }} - Desember {{ $year }}</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                        <div class="card-body p-0">
                            <table class="table mb-0">
                                <thead>
                                    <tr class="bg-light">
                                        <th class="ps-4 py-3">DESKRIPSI</th>
                                        <th class="text-end pe-4 py-3">TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Pendapatan -->
                                    <tr class="table-info">
                                        <td class="ps-4 fw-bold py-3" colspan="2">PENDAPATAN</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-5 py-3 text-dark">Pendapatan Penjualan</td>
                                        <td class="text-end pe-4 py-3 fw-bold">Rp {{ number_format($revenue) }}</td>
                                    </tr>
                                    <tr class="bg-light">
                                        <td class="ps-4 fw-bold py-3">Total Pendapatan</td>
                                        <td class="text-end pe-4 py-3 fw-bold text-primary">Rp {{ number_format($revenue) }}</td>
                                    </tr>

                                    <!-- HPP -->
                                    <tr class="table-secondary">
                                        <td class="ps-4 fw-bold py-3" colspan="2">HARGA POKOK PENJUALAN (HPP)</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-5 py-3 text-dark">HPP / Pembelian Stok</td>
                                        <td class="text-end pe-4 py-3 fw-bold">(Rp {{ number_format($cogs) }})</td>
                                    </tr>
                                    <tr class="bg-light">
                                        <td class="ps-4 fw-bold py-3">Total HPP</td>
                                        <td class="text-end pe-4 py-3 fw-bold text-danger">Rp {{ number_format($cogs) }}</td>
                                    </tr>

                                    <!-- Laba Kotor -->
                                    <tr class="bg-dark text-white">
                                        <td class="ps-4 fw-bold py-3 text-uppercase">Laba Kotor</td>
                                        <td class="text-end pe-4 py-3 fw-bold border-top border-light">Rp {{ number_format($grossProfit) }}</td>
                                    </tr>

                                    <!-- Biaya Operasional -->
                                    <tr class="table-warning">
                                        <td class="ps-4 fw-bold py-3" colspan="2">BIAYA OPERASIONAL</td>
                                    </tr>
                                    @foreach($expenses as $exp)
                                    <tr>
                                        <td class="ps-5 py-3 text-dark">{{ $exp->nama_biaya }}</td>
                                        <td class="text-end pe-4 py-3 fw-bold">Rp {{ number_format($exp->total) }}</td>
                                    </tr>
                                    @endforeach
                                    @if($expenses->isEmpty())
                                        <tr><td colspan="2" class="text-center p-3 text-muted">Tidak ada biaya operasional tercatat.</td></tr>
                                    @endif
                                    <tr class="bg-light">
                                        <td class="ps-4 fw-bold py-3">Total Biaya Operasional</td>
                                        <td class="text-end pe-4 py-3 fw-bold text-danger">Rp {{ number_format($totalExpenses) }}</td>
                                    </tr>

                                    <!-- Laba Bersih -->
                                    <tr class="table-success border-top border-2 border-success">
                                        <td class="ps-4 fw-bold py-3 fs-5">LABA (RUGI) BERSIH</td>
                                        <td class="text-end pe-4 py-3 fw-bold fs-5">Rp {{ number_format($netIncome) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="mt-4 text-center">
                        <small class="text-muted">Laporan ini dibuat secara otomatis oleh sistem.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
