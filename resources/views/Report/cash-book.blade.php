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

                <!-- Filter Form (Hidden on Print) -->
                <div class="d-print-none card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <form action="{{ route('report.cash-book') }}" method="GET" class="row align-items-end g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-muted small text-uppercase mb-2">AKUN KEUANGAN</label>
                                <select name="account_id" class="form-select py-2">
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}" {{ $accountId == $acc->id ? 'selected' : '' }}>
                                            {{ $acc->code }} - {{ $acc->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-muted small text-uppercase mb-2">DARI TANGGAL</label>
                                <div class="input-group">
                                    <input type="date" name="start_date" class="form-control py-2"
                                        value="{{ request('start_date', $start->toDateString()) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-muted small text-uppercase mb-2">SAMPAI
                                    TANGGAL</label>
                                <div class="input-group">
                                    <input type="date" name="end_date" class="form-control py-2"
                                        value="{{ request('end_date', $end->toDateString()) }}">
                                </div>
                            </div>
                            <div class="col-md-3 text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    <button type="submit" class="btn btn-primary fw-bold px-4 py-2">
                                        Preview
                                    </button>
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#printPreviewModal"
                                        class="btn btn-white border fw-bold px-4 py-2 shadow-sm text-dark d-flex align-items-center justify-content-center">
                                        <i class="fa fa-print me-2"></i> Cetak
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Report Content -->
                <div class="card border-0 shadow-none bg-white">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm w-100"
                                style="border: 2px solid #000; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px; color: #000;">
                                <thead>
                                    <!-- Title Row -->
                                    <tr style="background-color: #fff; border: 1px solid #000;">
                                        <th colspan="7" class="text-center py-3"
                                            style="border: 1px solid #000; font-size: 16px; font-weight: bold; text-transform: uppercase; color: #000;">
                                            LAPORAN KAS BESAR {{ $officeName }}<br>
                                            <span style="font-size: 12px; font-weight: bold;">PERIODE
                                                {{ \Carbon\Carbon::parse($start)->translatedFormat('d F Y') }} -
                                                {{ \Carbon\Carbon::parse($end)->translatedFormat('d F Y') }}</span>
                                        </th>
                                    </tr>
                                    <!-- Column Headers -->
                                    <tr style="background-color: #C0504D; color: #000; border: 1px solid #000;">
                                        <th class="text-center align-middle text-white py-2"
                                            style="border: 1px solid #000; width: 40px; font-weight: bold;">No</th>
                                        <th class="text-center align-middle text-white py-2"
                                            style="border: 1px solid #000; width: 90px; font-weight: bold;">Tanggal</th>
                                        <th class="text-center align-middle text-white py-2"
                                            style="border: 1px solid #000; width: 120px; font-weight: bold;">Nama Akun</th>
                                        <th class="text-center align-middle text-white py-2"
                                            style="border: 1px solid #000; font-weight: bold;">Keterangan</th>
                                        <th class="text-center align-middle text-white py-2"
                                            style="border: 1px solid #000; width: 110px; font-weight: bold;">Debit</th>
                                        <th class="text-center align-middle text-white py-2"
                                            style="border: 1px solid #000; width: 110px; font-weight: bold;">Kredit</th>
                                        <th class="text-center align-middle text-white py-2"
                                            style="border: 1px solid #000; width: 130px; font-weight: bold;">Total Saldo
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rows as $i => $r)
                                        <tr
                                            style="border: 1px solid #000; background-color: {{ $i % 2 == 0 ? '#fff' : '#f9f9f9' }};">
                                            <td class="text-center align-middle" style="border: 1px solid #000; padding: 4px;">
                                                {{ $i + 1 }}</td>
                                            <td class="text-center align-middle" style="border: 1px solid #000; padding: 4px;">
                                                {{ \Carbon\Carbon::parse($r['date'])->translatedFormat('d-M-y') }}</td>
                                            <td class="text-center align-middle" style="border: 1px solid #000; padding: 4px;">
                                                {{ $r['type'] }}</td>
                                            <td class="align-middle" style="border: 1px solid #000; padding: 4px;">
                                                {{ $r['description'] }}</td>
                                            <td class="text-end align-middle" style="border: 1px solid #000; padding: 4px;">Rp
                                                {{ number_format($r['debit'], 0, ',', '.') }}</td>
                                            <td class="text-end align-middle" style="border: 1px solid #000; padding: 4px;">Rp
                                                {{ number_format($r['credit'], 0, ',', '.') }}</td>
                                            <td class="text-end align-middle fw-bold"
                                                style="border: 1px solid #000; padding: 4px; color: #000;">Rp
                                                {{ number_format($r['balance'], 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach

                                    <!-- Print Spacer Row (Push footer to bottom only on print) -->
                                    <tr class="d-none d-print-table-row" style="height: 100%;">
                                        <td colspan="7" style="border: none;">&nbsp;</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr style="background-color: #ddd; border: 1px solid #000;">
                                        <td colspan="4" class="text-center align-middle fw-bold"
                                            style="border: 1px solid #000; padding: 6px; text-transform: uppercase;">TOTAL
                                        </td>
                                        <td class="text-end align-middle fw-bold"
                                            style="border: 1px solid #000; padding: 6px;">Rp
                                            {{ number_format($totalDebit, 0, ',', '.') }}</td>
                                        <td class="text-end align-middle fw-bold"
                                            style="border: 1px solid #000; padding: 6px;">Rp
                                            {{ number_format($totalCredit, 0, ',', '.') }}</td>
                                        <td class="text-end align-middle fw-bold text-danger"
                                            style="border: 1px solid #000; padding: 6px;">Rp
                                            {{ number_format($finalBalance, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Print Preview Modal -->
    <div class="modal fade" id="printPreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white d-print-none">
                    <h5 class="modal-title">
                        <i class="fa fa-print me-2"></i> Preview Cetak Laporan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-light p-4 d-print-none">
                    <div class="card border shadow-sm mx-auto" style="max-width: 210mm; min-height: 297mm; overflow: hidden;">
                        <div class="card-body p-5 bg-white">
                            <!-- Preview Report Content -->
                            <div class="table-responsive">
                                <table class="table table-sm w-100"
                                    style="border: 2px solid #000; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px; color: #000;">
                                    <thead>
                                        <tr style="background-color: #fff; border: 1px solid #000;">
                                            <th colspan="7" class="text-center py-3"
                                                style="border: 1px solid #000; font-size: 16px; font-weight: bold; text-transform: uppercase; color: #000;">
                                                LAPORAN KAS BESAR {{ $officeName }}<br>
                                                <span style="font-size: 12px; font-weight: bold;">PERIODE
                                                    {{ \Carbon\Carbon::parse($start)->translatedFormat('d F Y') }} -
                                                    {{ \Carbon\Carbon::parse($end)->translatedFormat('d F Y') }}</span>
                                            </th>
                                        </tr>
                                        <tr style="background-color: #C0504D; color: #000; border: 1px solid #000;">
                                            <th class="text-center align-middle text-white py-2"
                                                style="border: 1px solid #000; width: 40px; font-weight: bold;">No</th>
                                            <th class="text-center align-middle text-white py-2"
                                                style="border: 1px solid #000; width: 90px; font-weight: bold;">Tanggal</th>
                                            <th class="text-center align-middle text-white py-2"
                                                style="border: 1px solid #000; width: 120px; font-weight: bold;">Nama Akun</th>
                                            <th class="text-center align-middle text-white py-2"
                                                style="border: 1px solid #000; font-weight: bold;">Keterangan</th>
                                            <th class="text-center align-middle text-white py-2"
                                                style="border: 1px solid #000; width: 110px; font-weight: bold;">Debit</th>
                                            <th class="text-center align-middle text-white py-2"
                                                style="border: 1px solid #000; width: 110px; font-weight: bold;">Kredit</th>
                                            <th class="text-center align-middle text-white py-2"
                                                style="border: 1px solid #000; width: 130px; font-weight: bold;">Total Saldo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rows as $i => $r)
                                            <tr style="border: 1px solid #000; background-color: {{ $i % 2 == 0 ? '#fff' : '#f9f9f9' }};">
                                                <td class="text-center align-middle" style="border: 1px solid #000; padding: 4px;">{{ $i + 1 }}</td>
                                                <td class="text-center align-middle" style="border: 1px solid #000; padding: 4px;">{{ \Carbon\Carbon::parse($r['date'])->translatedFormat('d-M-y') }}</td>
                                                <td class="text-center align-middle" style="border: 1px solid #000; padding: 4px;">{{ $r['type'] }}</td>
                                                <td class="align-middle" style="border: 1px solid #000; padding: 4px;">{{ $r['description'] }}</td>
                                                <td class="text-end align-middle" style="border: 1px solid #000; padding: 4px;">Rp {{ number_format($r['debit'], 0, ',', '.') }}</td>
                                                <td class="text-end align-middle" style="border: 1px solid #000; padding: 4px;">Rp {{ number_format($r['credit'], 0, ',', '.') }}</td>
                                                <td class="text-end align-middle fw-bold" style="border: 1px solid #000; padding: 4px; color: #000;">Rp {{ number_format($r['balance'], 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr style="background-color: #ddd; border: 1px solid #000;">
                                            <td colspan="4" class="text-center align-middle fw-bold" style="border: 1px solid #000; padding: 6px; text-transform: uppercase;">TOTAL</td>
                                            <td class="text-end align-middle fw-bold" style="border: 1px solid #000; padding: 6px;">Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
                                            <td class="text-end align-middle fw-bold" style="border: 1px solid #000; padding: 6px;">Rp {{ number_format($totalCredit, 0, ',', '.') }}</td>
                                            <td class="text-end align-middle fw-bold text-danger" style="border: 1px solid #000; padding: 6px;">Rp {{ number_format($finalBalance, 0, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light d-print-none">
                    <button type="button" class="btn btn-light fw-bold border" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" onclick="printReport()" class="btn btn-primary fw-bold">
                        <i class="fa fa-print me-2"></i> Cetak Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <style>
        @media print {

            .page-title-box,
            .breadcrumb,
            form,
            .btn,
            .card.border-0.shadow-sm.rounded-3.mb-4 {
                display: none !important;
            }

            .page-content {
                padding: 0 !important;
                margin: 0 !important;
            }

            .container-fluid {
                padding: 0 !important;
            }

            body {
                background-color: white !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .table th,
            .table td {
                border: 1px solid #000 !important;
            }

            .modal, .modal-backdrop {
                display: none !important;
            }

            /* Ensure footer stays at bottom if content is short, but standard tfoot behavior applies */
            /* Using a spacer row approach */
            tr.d-print-table-row {
                height: auto !important;
            }

            /* Hide URL/Header/Footer from browser print settings if possible via CSS */
            @page {
                margin: 10mm;
                size: auto;
            }
        }
    </style>
@endpush

@push('js')
    <script>
        function printReport() {
            var modalEl = document.getElementById('printPreviewModal');
            var modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) {
                modal.hide();
            }
            
            // Wait slightly for modal hide animation to finish
            setTimeout(() => {
                window.print();
            }, 300);
        }
    </script>
@endpush