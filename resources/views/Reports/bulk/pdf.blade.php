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
    </style>
</head>
<body>

<!-- Page 1: Sales Invoice Report -->
<div class="report-header">
    <div class="report-title">LAPORAN SALES INVOICE</div>
    <div class="report-period">Periode: {{ $bulkReport->period_name }}</div>
</div>

<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>No. Faktur</th>
            <th>Pelanggan</th>
            <th>Total</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($salesData as $invoice)
            <tr>
                <td>{{ $invoice->tgl_invoice->format('d/m/Y') }}</td>
                <td>{{ $invoice->nomor_invoice }}</td>
                <td>{{ $invoice->mitra->nama }}</td>
                <td class="text-right bold">Rp {{ number_format($invoice->total_akhir, 0, ',', '.') }}</td>
                <td class="text-center">{{ $invoice->status_pembayaran == 'Paid' ? 'Lunas' : $invoice->status_pembayaran }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center py-4">
                    <em>Tidak ada data penjualan pada periode ini</em>
                </td>
            </tr>
        @endforelse
    </tbody>
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
            <th>Tanggal</th>
            <th>Pelanggan</th>
            <th>No. Referensi</th>
            <th>Jumlah</th>
            <th>Metode</th>
        </tr>
    </thead>
    <tbody>
        @forelse($paymentsData as $payment)
            <tr>
                <td>{{ $payment->tgl_pembayaran->format('d/m/Y') }}</td>
                <td>{{ $payment->invoice->mitra->name }}</td>
                <td>{{ $payment->nomor_pembayaran }}</td>
                <td class="text-right bold">Rp {{ number_format($payment->jumlah_bayar, 0, ',', '.') }}</td>
                <td>{{ ucfirst($payment->metode_pembayaran) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center py-4">
                    <em>Tidak ada data pembayaran pada periode ini</em>
                </td>
            </tr>
        @endforelse
    </tbody>
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
