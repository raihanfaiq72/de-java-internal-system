<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Massal - {{ $bulkReport->period_name }}</title>
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .page-break {
            page-break-before: always;
        }

        .report-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }

        .report-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .report-period {
            font-size: 14px;
            color: #666;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .nowrap {
            white-space: nowrap;
        }

        @page portrait {
            size: A4 portrait;
            margin: 15mm;
        }

        @page landscape {
            size: A4 landscape;
            margin: 15mm;
        }

        .portrait-page {
            page: portrait;
        }

        .landscape-page {
            page: landscape;
        }

        .profit-loss-section {
            margin-bottom: 30px;
        }

        .profit-loss-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }

        .aging-table {
            border-collapse: collapse;
            border: 1px solid #000;
            margin-bottom: 30px;
        }

        .aging-table th,
        .aging-table td {
            border: 1px solid #000;
            padding: 8px;
        }

        .profit-loss-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .profit-loss-box {
            border: 1px solid #000;
            padding: 15px;
        }

        .profit-loss-box h6 {
            margin-bottom: 10px;
            font-weight: bold;
        }

        .profit-loss-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .profit-loss-total {
            border-top: 1px solid #000;
            padding-top: 10px;
            margin-top: 10px;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        /* Cover Page */
        .cover-page {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 90vh;
            text-align: center;
            page-break-after: always;
        }

        .cover-title {
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
            line-height: 1.4;
            margin-bottom: 30px;
        }

        .cover-divider {
            width: 80px;
            border-top: 3px solid #000;
            margin: 20px auto;
        }

        .cover-meta {
            font-size: 13px;
            color: #444;
            margin-top: 10px;
        }

        .cover-footer {
            position: absolute;
            bottom: 30mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 11px;
            color: #888;
        }
    </style>
</head>

<body>

    <!-- Cover Page -->
    <div class="cover-page">
        <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <div class="cover-divider"></div>
            <div class="cover-title">
                CV DEJAVANESE AUTOPARTS<br>
                LAPORAN KEUANGAN BULANAN<br>
                PERIODE {{ strtoupper($bulkReport->period_name) }}
            </div>
            <div class="cover-divider"></div>
            <div class="cover-meta" style="margin-top: 40px;">
                Tanggal Cetak: {{ date('d F Y') }}<br>
                Dicetak oleh: {{ auth()->user()->name }}
            </div>
        </div>
    </div>

    <!-- Page 1: Sales Invoice Report -->
    <div class="report-header">
        <div class="report-title">CV DEJAVANESE AUTOPARTS</div>
        <div class="report-title">LAPORAN SALES INVOICE</div>
        <div class="report-period">Periode: {{ $bulkReport->period_name }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="3%">No.</th>
                <th width="10%">No. Invoice</th>
                <th width="8%">Tanggal</th>
                <th width="7%" class="text-center">Status</th>
                <th width="15%">Pelanggan</th>
                <th width="14.25%" class="text-right nowrap">Total Amount</th>
                <th width="14.25%" class="text-right nowrap">Paid</th>
                <th width="14.25%" class="text-right nowrap">Down Payment</th>
                <th width="14.25%" class="text-right nowrap">Amount Due</th>
            </tr>
        </thead>
        <tbody>
            @php
                $salesTotal = 0;
                $totalPaid = 0;
                $totalDownPayment = 0;
                $totalAmountDue = 0;
            @endphp
            @forelse($salesData as $i => $invoice)
                @php
                    $salesTotal += $invoice->total_akhir;
                    $invoicePaid = $invoice->payment()->sum('jumlah_bayar');
                    $invoiceTotal = $invoice->total_akhir;
                    $paidAmount = 0;
                    $downPaymentAmount = 0;

                    foreach ($invoice->payment as $payment) {
                        if ($payment->jumlah_bayar >= $invoiceTotal) {
                            $paidAmount += $payment->jumlah_bayar;
                        } else {
                            $downPaymentAmount += $payment->jumlah_bayar;
                        }
                    }

                    $amountDue = $invoiceTotal - $paidAmount - $downPaymentAmount;
                    $totalPaid += $paidAmount;
                    $totalDownPayment += $downPaymentAmount;
                    $totalAmountDue += $amountDue;
                @endphp
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $invoice->nomor_invoice }}</td>
                    <td>{{ $invoice->tgl_invoice->format('d/m/Y') }}</td>
                    <td class="text-center">
                        {{ $invoice->status_pembayaran == 'Paid' ? 'Lunas' : $invoice->status_pembayaran }}
                    </td>
                    <td>{{ $invoice->mitra->nama ?? '-' }}</td>
                    <td class="text-right bold nowrap">Rp. {{ number_format($invoice->total_akhir, 2, ',', '.') }}</td>
                    <td class="text-right bold nowrap">Rp. {{ number_format($paidAmount, 2, ',', '.') }}</td>
                    <td class="text-right bold nowrap">Rp. {{ number_format($downPaymentAmount, 2, ',', '.') }}</td>
                    <td class="text-right bold nowrap">Rp. {{ number_format($amountDue, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center py-4">
                        <em>Tidak ada data penjualan pada periode ini</em>
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if ($salesData->count() > 0)
            <tfoot>
                <tr style="background-color: #f2f2f2;">
                    <td colspan="5" class="text-right bold">TOTAL</td>
                    <td class="text-right bold nowrap">Rp. {{ number_format($salesTotal, 2, ',', '.') }}</td>
                    <td class="text-right bold nowrap">Rp. {{ number_format($totalPaid, 2, ',', '.') }}</td>
                    <td class="text-right bold nowrap">Rp. {{ number_format($totalDownPayment, 2, ',', '.') }}</td>
                    <td class="text-right bold nowrap">Rp. {{ number_format($totalAmountDue, 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    <!-- Page 2: Payments Summary -->
    <div class="page-break"></div>
    <div class="report-header">
        <div class="report-title">CV DEJAVANESE AUTOPARTS</div>
        <div class="report-title">LAPORAN PAYMENTS SUMMARY</div>
        <div class="report-period">Periode: {{ $bulkReport->period_name }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="3%">No.</th>
                <th width="12%">No. Invoice</th>
                <th width="10%">Tanggal</th>
                <th width="8%">Status</th>
                <th width="10%">Metode</th>
                <th width="10%">No. Mitra</th>
                <th>Nama Mitra</th>
                <th width="14%" class="text-right nowrap">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @php $paymentsTotal = 0; @endphp
            @forelse($paymentsData as $i => $payment)
                @php $paymentsTotal += $payment->jumlah_bayar; @endphp
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $payment->invoice->nomor_invoice ?? '-' }}</td>
                    <td>{{ $payment->tgl_pembayaran->format('d/m/Y') }}</td>
                    <td>{{ $payment->invoice->status_pembayaran ?? '-' }}</td>
                    <td>{{ ucfirst($payment->metode_pembayaran) }}</td>
                    <td>{{ $payment->invoice->mitra->nomor_mitra ?? '-' }}</td>
                    <td>{{ $payment->invoice->mitra->nama ?? '-' }}</td>
                    <td class="text-right bold nowrap">Rp. {{ number_format($payment->jumlah_bayar, 2, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <em>Tidak ada data pembayaran pada periode ini</em>
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if ($paymentsData->count() > 0)
            <tfoot>
                <tr style="background-color: #f2f2f2;">
                    <td colspan="7" class="text-right bold">TOTAL</td>
                    <td class="text-right bold nowrap">Rp. {{ number_format($paymentsTotal, 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    <!-- Page 3: A/R Aging Summary (Landscape) -->
    <div class="landscape-page">
        <div class="report-header">
            <div class="report-title">CV DEJAVANESE AUTOPARTS</div>
            <div class="report-title">LAPORAN A/R AGING SUMMARY</div>
            <div class="report-period">Periode: {{ $bulkReport->period_name }}</div>
        </div>

        @php
            $groupedAging = collect($arAgingData)->groupBy('customer');
            $grandTotalPiutang = collect($arAgingData)->sum(function ($item) {
                return array_sum($item['aging_buckets']);
            });
        @endphp

        @forelse($groupedAging as $customer => $items)
            @if (!$loop->first)
                <div class="page-break"></div>
            @endif
            <table class="aging-table">
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th colspan="8" class="bold" style="font-size: 12px; padding: 10px;">Mitra:
                            {{ $customer }}
                        </th>
                    </tr>
                    <tr>
                        <th width="15%">Invoice No.</th>
                        <th width="10%">Due Date</th>
                        <th width="12.5%" class="text-right">Current</th>
                        <th width="12.5%" class="text-right">1 - 15</th>
                        <th width="12.5%" class="text-right">16 - 30</th>
                        <th width="12.5%" class="text-right">31 - 45</th>
                        <th width="12.5%" class="text-right">46 - 60</th>
                        <th width="12.5%" class="text-right">61+</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $subTotal = [
                            'current' => 0,
                            '1_15' => 0,
                            '16_30' => 0,
                            '31_45' => 0,
                            '46_60' => 0,
                            '61_plus' => 0,
                        ];
                    @endphp
                    @foreach ($items as $item)
                        @php
                            foreach ($subTotal as $key => $val) {
                                $bucketKey = $key == '61_plus' ? '61_plus' : $key;
                                $subTotal[$key] += $item['aging_buckets'][$bucketKey] ?? 0;
                            }
                        @endphp
                        <tr>
                            <td>{{ $item['invoice_no'] }}</td>
                            <td>{{ $item['due_date'] }}</td>
                            <td class="text-right nowrap">Rp.
                                {{ number_format($item['aging_buckets']['current'], 2, ',', '.') }}
                            </td>
                            <td class="text-right nowrap">Rp.
                                {{ number_format($item['aging_buckets']['1_15'], 2, ',', '.') }}</td>
                            <td class="text-right nowrap">Rp.
                                {{ number_format($item['aging_buckets']['16_30'], 2, ',', '.') }}</td>
                            <td class="text-right nowrap">Rp.
                                {{ number_format($item['aging_buckets']['31_45'], 2, ',', '.') }}</td>
                            <td class="text-right nowrap">Rp.
                                {{ number_format($item['aging_buckets']['46_60'], 2, ',', '.') }}</td>
                            <td class="text-right nowrap">Rp.
                                {{ number_format($item['aging_buckets']['61_plus'], 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background-color: #f2f2f2;">
                        <td colspan="2" class="bold">Total</td>
                        <td class="text-right bold nowrap">Rp. {{ number_format($subTotal['current'], 2, ',', '.') }}
                        </td>
                        <td class="text-right bold nowrap">Rp. {{ number_format($subTotal['1_15'], 2, ',', '.') }}</td>
                        <td class="text-right bold nowrap">Rp. {{ number_format($subTotal['16_30'], 2, ',', '.') }}
                        </td>
                        <td class="text-right bold nowrap">Rp. {{ number_format($subTotal['31_45'], 2, ',', '.') }}
                        </td>
                        <td class="text-right bold nowrap">Rp. {{ number_format($subTotal['46_60'], 2, ',', '.') }}
                        </td>
                        <td class="text-right bold nowrap">Rp. {{ number_format($subTotal['61_plus'], 2, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        @empty
            <div style="text-align: center; padding: 20px; border: 1px solid #000; background: #f9f9f9;">
                <em>Tidak ada data piutang jatuh tempo</em>
            </div>
        @endforelse

        <div class="page-break"></div>
        <div style="text-align: center; margin-top: 150px;">
            <h1 style="font-size: 32px; margin-bottom: 10px; border: none;">Client Statement Report</h1>
            <h2 style="font-size: 24px; margin-bottom: 80px;">CV Dejavanese Autoparts</h2>

            <p style="font-size: 20px; margin-bottom: 20px;">Periode {{ $bulkReport->period_name }}</p>
            <h2 class="bold" style="font-size: 28px;">Grand Total Piutang Rp.
                {{ number_format($grandTotalPiutang, 0, ',', '.') }}</h2>
        </div>
    </div>

    <!-- Page 4: Profit Loss Report -->
    <div class="portrait-page">
        <div style="text-align: center; margin-bottom: 30px; margin-top: 20px;">
            <div style="font-size: 18px; font-weight: bold;">Laporan Laba Rugi</div>
            <div style="font-size: 16px; font-weight: bold;">CV Dejavanese Autoparts</div>
            <div style="font-size: 14px; margin-top: 5px; font-weight: bold;">Periode Laporan :
                {{ $bulkReport->start_date->format('Y-m-d') }} Sampai {{ $bulkReport->end_date->format('Y-m-d') }}
            </div>
        </div>

        <div style="margin-bottom: 25px; padding-bottom: 5px;">
            <table width="100%" style="margin-bottom: 0; border: none;">
                <tr>
                    <td class="bold" style="font-size: 14px; border: none;">Tipe Akun</td>
                    <td width="30%" style="border: none;"></td>
                </tr>
            </table>
        </div>

        @foreach ($profitLossData['report'] as $group)
            @if ($group['total_balance'] != 0 || collect($group['types'])->flatMap(fn($t) => $t['accounts'])->count() > 0)
                <div style="margin-bottom: 30px;">
                    <div class="bold" style="font-size: 15px; margin-bottom: 15px;">{{ $group['name'] }}</div>

                    @foreach ($group['types'] as $type)
                        <div style="margin-bottom: 15px;">
                            <table width="100%" style="margin-bottom: 10px; border: none;">
                                <tr>
                                    <td class="bold" style="padding-left: 20px; border: none;">{{ $type['name'] }}
                                    </td>
                                    <td width="30%" class="text-right bold" style="border: none;">Rp.
                                        {{ number_format($type['total_balance'], 2, ',', '.') }}</td>
                                </tr>
                            </table>

                            @foreach ($type['accounts'] as $account)
                                <table width="100%" style="margin-bottom: 8px; border: none;">
                                    <tr>
                                        <td style="padding-left: 40px; border: none;">{{ $account['name'] }} -
                                            {{ $account['code'] }}</td>
                                        <td width="30%" class="text-right" style="border: none;">Rp.
                                            {{ number_format($account['balance'], 2, ',', '.') }}</td>
                                    </tr>
                                </table>
                            @endforeach
                        </div>
                    @endforeach

                    <div style="margin-top: 10px; padding-top: 10px;">
                        <table width="100%" style="margin-bottom: 0; border: none;">
                            <tr>
                                <td class="bold" style="border: none;">Total {{ $group['name'] }}</td>
                                <td width="30%" class="text-right bold" style="border: none;">Rp.
                                    {{ number_format($group['total_balance'], 2, ',', '.') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            @endif
        @endforeach

        <div style="margin-top: 40px; padding-top: 10px;">
            <table width="100%" style="border: none;">
                <tr>
                    <td class="bold" style="font-size: 16px; border: none;">Laba Bersih</td>
                    <td width="30%" class="text-right bold" style="font-size: 16px; border: none;">Rp.
                        {{ number_format($profitLossData['net_profit'], 2, ',', '.') }}</td>
                </tr>
            </table>
        <div style="margin-top: 50px; text-align: center; font-size: 10px; color: #666;">
            <p>Laporan ini dihasilkan pada {{ date('d/m/Y H:i:s') }}</p>
            <p>Oleh: {{ auth()->user()->name }}</p>
        </div>
    </div>

</body>

</html>
