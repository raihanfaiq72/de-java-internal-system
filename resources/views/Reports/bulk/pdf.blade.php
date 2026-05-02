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
            page-break-after: always;
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
        th, td {
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
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .bold {
            font-weight: bold;
        }
        .landscape-page {
            page: landscape;
            size: A4 landscape;
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
    <div class="report-title">LAPORAN SALES INVOICE</div>
    <div class="report-period">Periode: {{ $bulkReport->period_name }}</div>
</div>

<table>
    <thead>
        <tr>
            <th width="4%">No.</th>
            <th>Tanggal</th>
            <th>No. Faktur</th>
            <th>Pelanggan</th>
            <th class="text-right">Total</th>
            <th class="text-center">Status</th>
        </tr>
    </thead>
    <tbody>
        @php $salesTotal = 0; @endphp
        @forelse($salesData as $i => $invoice)
            @php $salesTotal += $invoice->total_akhir; @endphp
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $invoice->tgl_invoice->format('d/m/Y') }}</td>
                <td>{{ $invoice->nomor_invoice }}</td>
                <td>{{ $invoice->mitra->nama ?? '-' }}</td>
                <td class="text-right bold">Rp {{ number_format($invoice->total_akhir, 0, ',', '.') }}</td>
                <td class="text-center">{{ $invoice->status_pembayaran == 'Paid' ? 'Lunas' : $invoice->status_pembayaran }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center py-4">
                    <em>Tidak ada data penjualan pada periode ini</em>
                </td>
            </tr>
        @endforelse
    </tbody>
    @if($salesData->count() > 0)
    <tfoot>
        <tr style="background-color: #f2f2f2;">
            <td colspan="4" class="text-right bold">TOTAL</td>
            <td class="text-right bold">Rp {{ number_format($salesTotal, 0, ',', '.') }}</td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>

<!-- Page 2: Payments Summary -->
<div class="page-break"></div>
<div class="report-header">
    <div class="report-title">LAPORAN PAYMENTS SUMMARY</div>
    <div class="report-period">Periode: {{ $bulkReport->period_name }}</div>
</div>

<table>
    <thead>
        <tr>
            <th width="4%">No.</th>
            <th>Tanggal</th>
            <th>Pelanggan</th>
            <th>No. Referensi</th>
            <th class="text-right">Jumlah</th>
            <th>Metode</th>
        </tr>
    </thead>
    <tbody>
        @php $paymentsTotal = 0; @endphp
        @forelse($paymentsData as $i => $payment)
            @php $paymentsTotal += $payment->jumlah_bayar; @endphp
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $payment->tgl_pembayaran->format('d/m/Y') }}</td>
                <td>{{ $payment->invoice->mitra->nama ?? '-' }}</td>
                <td>{{ $payment->nomor_pembayaran }}</td>
                <td class="text-right bold">Rp {{ number_format($payment->jumlah_bayar, 0, ',', '.') }}</td>
                <td>{{ ucfirst($payment->metode_pembayaran) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center py-4">
                    <em>Tidak ada data pembayaran pada periode ini</em>
                </td>
            </tr>
        @endforelse
    </tbody>
    @if($paymentsData->count() > 0)
    <tfoot>
        <tr style="background-color: #f2f2f2;">
            <td colspan="4" class="text-right bold">TOTAL</td>
            <td class="text-right bold">Rp {{ number_format($paymentsTotal, 0, ',', '.') }}</td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>

<!-- Page 3: A/R Aging Summary (Landscape) -->
<div class="page-break landscape-page"></div>
<div class="report-header">
    <div class="report-title">LAPORAN A/R AGING SUMMARY</div>
    <div class="report-period">Periode: {{ $bulkReport->period_name }}</div>
</div>

<table>
    <thead>
        <tr>
            <th>Pelanggan</th>
            <th>No. Invoice</th>
            <th>Jatuh Tempo</th>
            <th class="text-right">Current</th>
            <th class="text-right">1-15</th>
            <th class="text-right">16-30</th>
            <th class="text-right">31-45</th>
            <th class="text-right">46-60</th>
            <th class="text-right">61+</th>
        </tr>
    </thead>
    <tbody>
        @forelse($arAgingData as $aging)
            <tr>
                <td>{{ $aging['customer'] }}</td>
                <td>{{ $aging['invoice_no'] }}</td>
                <td>{{ $aging['due_date'] }}</td>
                <td class="text-right">Rp {{ number_format($aging['aging_buckets']['current'], 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($aging['aging_buckets']['1_15'], 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($aging['aging_buckets']['16_30'], 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($aging['aging_buckets']['31_45'], 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($aging['aging_buckets']['46_60'], 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($aging['aging_buckets']['61_plus'], 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center py-4">
                    <em>Tidak ada data piutang jatuh tempo</em>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<!-- Page 4: Profit Loss Report -->
<div class="page-break"></div>
<div class="report-header">
    <div class="report-title">LAPORAN LABA RUGI</div>
    <div class="report-period">Periode: {{ $bulkReport->period_name }}</div>
</div>

<div class="profit-loss-section">
    <div class="profit-loss-grid">
        <div class="profit-loss-box">
            <h6 class="text-primary">PENDAPATAN</h6>
            <div class="profit-loss-item">
                <span>Penjualan</span>
                <span class="bold">Rp {{ number_format($profitLossData['sales_revenue'], 0, ',', '.') }}</span>
            </div>
            <div class="profit-loss-item">
                <span>Pendapatan Lainnya</span>
                <span class="bold">Rp {{ number_format($profitLossData['other_revenues'], 0, ',', '.') }}</span>
            </div>
            <div class="profit-loss-total">
                <div class="profit-loss-item">
                    <span class="bold">Total Pendapatan</span>
                    <span class="bold text-primary">Rp {{ number_format($profitLossData['sales_revenue'] + $profitLossData['other_revenues'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
        
        <div class="profit-loss-box">
            <h6 class="text-danger">BEBAN</h6>
            <div class="profit-loss-item">
                <span>Harga Pokok Penjualan</span>
                <span class="bold">Rp {{ number_format($profitLossData['cost_of_goods_sold'], 0, ',', '.') }}</span>
            </div>
            <div class="profit-loss-item">
                <span>Beban Operasional</span>
                <span class="bold">Rp {{ number_format($profitLossData['operating_expenses'], 0, ',', '.') }}</span>
            </div>
            <div class="profit-loss-total">
                <div class="profit-loss-item">
                    <span class="bold">Total Beban</span>
                    <span class="bold text-danger">Rp {{ number_format($profitLossData['cost_of_goods_sold'] + $profitLossData['operating_expenses'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="profit-loss-box">
        <div class="profit-loss-grid">
            <div>
                <div class="profit-loss-item">
                    <span class="bold">Laba Kotor</span>
                    <span class="bold text-primary">Rp {{ number_format($profitLossData['gross_profit'], 0, ',', '.') }}</span>
                </div>
            </div>
            <div>
                <div class="profit-loss-item">
                    <span class="bold">Laba Bersih</span>
                    <span class="bold text-success">Rp {{ number_format($profitLossData['net_profit'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div style="margin-top: 50px; text-align: center; font-size: 10px; color: #666;">
    <p>Laporan ini dihasilkan pada {{ date('d/m/Y H:i:s') }}</p>
    <p>Oleh: {{ auth()->user()->name }}</p>
</div>

</body>
</html>
