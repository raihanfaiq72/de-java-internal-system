<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Massal Slip Gaji</title>
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .page-break {
            page-break-after: always;
        }
        .slip-container {
            border: 1px solid #000;
            padding: 20px;
            margin-bottom: 20px;
            min-height: 380px;
            box-sizing: border-box;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        .table-info td {
            border: 1px solid #000;
            padding: 4px 10px;
        }
        .table-title {
            width: 120px;
            float: right;
        }
        .table-title td {
            border: 1px solid #000;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            line-height: 1.4;
        }
        .section-title {
            font-weight: bold;
            margin-top: 25px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .table-earnings td {
            padding: 3px 0;
            vertical-align: top;
        }
        .col-desc { width: 30%; }
        .col-calc { width: 40%; }
        .col-eq { width: 5%; text-align: center; }
        .col-amount { width: 25%; }
        .box-total {
            border: 2px solid #000;
            margin-top: 5px;
        }
        .box-total td {
            padding: 4px 5px;
            font-weight: bold;
        }
        .row-total {
            border-bottom: 1px solid #000;
        }
        .row-ket {
            text-align: center;
            font-style: italic;
        }
        .table-signature {
            margin-top: 15px;
        }
        .table-signature td {
            vertical-align: top;
        }
        .spacer {
            height: 50px;
        }
        .two-slips {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 20px;
        }
        .slip-box {
            border: 1px solid #000;
            padding: 20px;
            min-height: 350px;
            box-sizing: border-box;
        }
        @media print {
            body { 
                margin: 0;
                padding: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .two-slips { 
                page-break-inside: avoid;
                page-break-after: auto;
            }
            .slip-box {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }
    </style>
</head>
<body>

@php
    $slips = $salaryPeriod->salarySlips;
    $slipChunks = $slips->chunk(2);
@endphp

@foreach($slipChunks as $chunk)
    @if($chunk->count() == 2)
        <!-- Two slips on one page -->
        <div class="two-slips">
            @foreach($chunk as $index => $salarySlip)
                <div class="slip-box">
                    <table>
                        <tr>
                            <td style="width: 50%; vertical-align: top;">
                                <table class="table-info" style="width: 80%;">
                                    <tr>
                                        <td style="width: 30%;">NAMA</td>
                                        <td>{{ $salarySlip->employee->name }}</td>
                                    </tr>
                                    <tr>
                                        <td>NIK</td>
                                        <td>{{ $salarySlip->employee->nik }}</td>
                                    </tr>
                                </table>
                            </td>
                            <td style="width: 50%; vertical-align: top;">
                                <table class="table-title">
                                    <tr>
                                        <td>SLIP<br>GAJI<br>KARYAWAN</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <div class="section-title">
                        PENGHASILAN (PERIODE {{ strtoupper($salarySlip->salaryPeriod->name) }})
                    </div>

                    <table class="table-earnings">
                        <tr>
                            <td class="col-desc">PREMI</td>
                            <td class="col-calc"></td>
                            <td class="col-eq">=</td>
                            <td class="col-amount">Rp. {{ number_format($salarySlip->premi, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="col-desc">GAJI HARIAN</td>
                            <td class="col-calc">RP. {{ number_format($salarySlip->details['daily_rate'] ?? 50000, 0, ',', '.') }} x {{ $salarySlip->details['work_days'] ?? 0 }}(HARI)</td>
                            <td class="col-eq">=</td>
                            <td class="col-amount">Rp. {{ number_format($salarySlip->basic_salary, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="col-desc">UANG MAKAN</td>
                            <td class="col-calc">RP. {{ number_format(($salarySlip->meal_allowance / ($salarySlip->details['work_days'] ?? 1)), 0, ',', '.') }} x {{ $salarySlip->details['work_days'] ?? 0 }}(HARI)</td>
                            <td class="col-eq">=</td>
                            <td class="col-amount">Rp. {{ number_format($salarySlip->meal_allowance, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="col-desc">KAS BON</td>
                            <td class="col-calc"></td>
                            <td class="col-eq">=</td>
                            <td class="col-amount">Rp. {{ number_format($salarySlip->other_deduction, 0, ',', '.') }}</td>
                        </tr>
                    </table>

                    <table class="box-total">
                        <tr class="row-total">
                            <td style="width: 70%;">TOTAL</td>
                            <td style="width: 5%; text-align: center;">=</td>
                            <td style="width: 25%;">Rp. {{ number_format($salarySlip->total_salary, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="row-ket">
                                KET {{ $salarySlip->details['notes'] ?? ('IZIN ' . (($salarySlip->details['work_days'] ?? 0) - ($salarySlip->details['present'] ?? 0)) . ' HARI, TERLAMBAT ABSEN ' . ($salarySlip->details['late'] ?? 0) . 'X') }}
                            </td>
                        </tr>
                    </table>

                    <table class="table-signature">
                        <tr>
                            <td style="width: 35%;">Penerima</td>
                            <td style="width: 30%; text-align: left;">Mengetahui,</td>
                            <td style="width: 35%;">
                                Senin, 2 Maret 2026<br>
                                Adm. Finance
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="spacer"></td>
                        </tr>
                        <tr>
                            <td>{{ $salarySlip->employee->name }}</td>
                            <td style="text-align: left;">Wildan R</td>
                            <td>Noveka</td>
                        </tr>
                    </table>
                </div>
            @endforeach
        </div>
    @else
        <!-- Single slip on one page -->
        <div class="slip-container">
            @foreach($chunk as $salarySlip)
                <table>
                    <tr>
                        <td style="width: 50%; vertical-align: top;">
                            <table class="table-info" style="width: 80%;">
                                <tr>
                                    <td style="width: 30%;">NAMA</td>
                                    <td>{{ $salarySlip->employee->name }}</td>
                                </tr>
                                <tr>
                                    <td>NIK</td>
                                    <td>{{ $salarySlip->employee->nik }}</td>
                                </tr>
                            </table>
                        </td>
                        <td style="width: 50%; vertical-align: top;">
                            <table class="table-title">
                                <tr>
                                    <td>SLIP<br>GAJI<br>KARYAWAN</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <div class="section-title">
                    PENGHASILAN (PERIODE {{ strtoupper($salarySlip->salaryPeriod->name) }})
                </div>

                <table class="table-earnings">
                    <tr>
                        <td class="col-desc">PREMI</td>
                        <td class="col-calc"></td>
                        <td class="col-eq">=</td>
                        <td class="col-amount">Rp. {{ number_format($salarySlip->premi, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="col-desc">GAJI HARIAN</td>
                        <td class="col-calc">RP. {{ number_format($salarySlip->details['daily_rate'] ?? 50000, 0, ',', '.') }} x {{ $salarySlip->details['work_days'] ?? 0 }}(HARI)</td>
                        <td class="col-eq">=</td>
                        <td class="col-amount">Rp. {{ number_format($salarySlip->basic_salary, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="col-desc">UANG MAKAN</td>
                        <td class="col-calc">RP. {{ number_format(($salarySlip->meal_allowance / ($salarySlip->details['work_days'] ?? 1)), 0, ',', '.') }} x {{ $salarySlip->details['work_days'] ?? 0 }}(HARI)</td>
                        <td class="col-eq">=</td>
                        <td class="col-amount">Rp. {{ number_format($salarySlip->meal_allowance, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="col-desc">KAS BON</td>
                        <td class="col-calc"></td>
                        <td class="col-eq">=</td>
                        <td class="col-amount">Rp. {{ number_format($salarySlip->other_deduction, 0, ',', '.') }}</td>
                    </tr>
                </table>

                <table class="box-total">
                    <tr class="row-total">
                        <td style="width: 70%;">TOTAL</td>
                        <td style="width: 5%; text-align: center;">=</td>
                        <td style="width: 25%;">Rp. {{ number_format($salarySlip->total_salary, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="row-ket">
                            KET {{ $salarySlip->details['notes'] ?? ('IZIN ' . (($salarySlip->details['work_days'] ?? 0) - ($salarySlip->details['present'] ?? 0)) . ' HARI, TERLAMBAT ABSEN ' . ($salarySlip->details['late'] ?? 0) . 'X') }}
                        </td>
                    </tr>
                </table>

                <table class="table-signature">
                    <tr>
                        <td style="width: 35%;">Penerima</td>
                        <td style="width: 30%; text-align: left;">Mengetahui,</td>
                        <td style="width: 35%;">
                            Senin, 2 Maret 2026<br>
                            Adm. Finance
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="spacer"></td>
                    </tr>
                    <tr>
                        <td>{{ $salarySlip->employee->name }}</td>
                        <td style="text-align: left;">Wildan R</td>
                        <td>Noveka</td>
                    </tr>
                </table>
            @endforeach
        </div>
    @endif
    
    @if(!$loop->last)
        <div class="page-break"></div>
    @endif
@endforeach

</body>
</html>
