<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>LAPORAN PENJUALAN {{ strtoupper($salesPerson->name ?? 'Tanpa Sales Person') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .header h2 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .header h3 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .header p {
            font-size: 11px;
            margin: 2px 0;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .table th,
        .table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        
        .table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        
        .table .text-right {
            text-align: right;
        }
        
        .table .text-center {
            text-align: center;
        }
        
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        
        .chart-container {
            margin: 20px 0;
            text-align: center;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        
        @media print {
            .page-break {
                page-break-before: always;
            }
            
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

<!-- STEP 1: OMSET UANG MASUK -->
<div class="header">
    <h2>OMSET UANG MASUK PERIODE {{ strtoupper(date('F Y', strtotime($dateFrom))) }}</h2>
    <h3>Sales Person: {{ $salesPerson->name ?? 'Tanpa Sales Person' }}</h3>
    <p>Periode: {{ date('d M Y', strtotime($dateFrom)) }} - {{ date('d M Y', strtotime($dateTo)) }}</p>
</div>

<table class="table">
    <thead>
        <tr>
            <th width="15%">TGL PEMBAYARAN</th>
            <th width="40%">CUSTOMER</th>
            <th width="20%">TOTAL</th>
            <th width="25%">NO. NOTA</th>
        </tr>
    </thead>
    <tbody>
        @forelse($paymentsByDate as $payment)
            <tr>
                <td class="text-center">{{ date('d-M-y', strtotime($payment['date'])) }}</td>
                <td>{{ implode(', ', $payment['customers']->take(3)->toArray()) }}{{ $payment['customers']->count() > 3 ? '...' : '' }}</td>
                <td class="text-right">{{ number_format($payment['total'], 0, ',', '.') }}</td>
                <td>{{ implode(', ', $payment['receipts']->take(2)->toArray()) }}{{ $payment['receipts']->count() > 2 ? '...' : '' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center">Tidak ada data pembayaran</td>
            </tr>
        @endforelse
        <tr class="total-row">
            <td colspan="2" class="text-right"><strong>TOTAL</strong></td>
            <td class="text-right"><strong>{{ number_format($totalPayments, 0, ',', '.') }}</strong></td>
            <td></td>
        </tr>
    </tbody>
</table>

<!-- PAGE BREAK -->
<div class="page-break"></div>

<!-- STEP 2: DETAIL PEMBAYARAN PER INVOICE -->
<div class="header">
    <h2>DETAIL PEMBAYARAN PER INVOICE</h2>
    <h3>Sales Person: {{ $salesPerson->name ?? 'Tanpa Sales Person' }}</h3>
    <p>Periode: {{ date('d M Y', strtotime($dateFrom)) }} - {{ date('d M Y', strtotime($dateTo)) }}</p>
</div>

<table class="table">
    <thead>
        <tr>
            <th width="10%">TGL INVOICE</th>
            <th width="15%">NO. INVOICE</th>
            <th width="30%">CUSTOMER</th>
            <th width="15%">TOTAL INVOICE</th>
            <th width="15%">PEMBAYARAN</th>
            <th width="15%">SISA</th>
        </tr>
    </thead>
    <tbody>
        @forelse($invoices->groupBy('id') as $invoiceGroup)
            @php
                $invoice = $invoiceGroup->first();
                $totalPaid = $invoiceGroup->sum('payment_amount');
                $remaining = $invoice->total_akhir - $totalPaid;
            @endphp
            <tr>
                <td class="text-center">{{ date('d-M-y', strtotime($invoice->tgl_invoice)) }}</td>
                <td>{{ $invoice->nomor_invoice }}</td>
                <td>{{ $invoice->mitra_nama ?? '-' }}</td>
                <td class="text-right">{{ number_format($invoice->total_akhir, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($totalPaid, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($remaining, 0, ',', '.') }}</td>
            </tr>
            
            @if($invoiceGroup->where('payment_amount', '>', 0)->count() > 0)
                @foreach($invoiceGroup->where('payment_amount', '>', 0) as $payment)
                    @if($payment->payment_amount > 0)
                    <tr style="background-color: #f9f9f9;">
                        <td colspan="3" style="padding-left: 30px;">
                            {{ date('d-M-y', strtotime($payment->payment_date)) }} 
                            @if($payment->payment_note) - {{ $payment->payment_note }} @endif
                            @if(str_contains(strtolower($payment->payment_note ?? ''), 'titip')) 
                                <span style="color: #ff6600; font-weight: bold;">(Titip)</span>
                            @endif
                        </td>
                        <td class="text-right"></td>
                        <td class="text-right">{{ number_format($payment->payment_amount, 0, ',', '.') }}</td>
                        <td class="text-right"></td>
                    </tr>
                    @endif
                @endforeach
            @endif
        @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada data invoice</td>
            </tr>
        @endforelse
        
        <tr class="total-row">
            <td colspan="3" class="text-right"><strong>TOTAL</strong></td>
            <td class="text-right"><strong>{{ number_format($totalInvoices, 0, ',', '.') }}</strong></td>
            <td class="text-right"><strong>{{ number_format($totalPayments, 0, ',', '.') }}</strong></td>
            <td class="text-right"><strong>{{ number_format($totalRemaining, 0, ',', '.') }}</strong></td>
        </tr>
    </tbody>
</table>

<!-- PAGE BREAK -->
<div class="page-break"></div>

<!-- STEP 3: REKAPITULASI & GRAFIK -->
<div class="header">
    <h2>REKAPITULASI PENJUALAN</h2>
    <h3>Sales Person: {{ $salesPerson->name ?? 'Tanpa Sales Person' }}</h3>
    <p>Periode: {{ date('d M Y', strtotime($dateFrom)) }} - {{ date('d M Y', strtotime($dateTo)) }}</p>
</div>

<!-- Summary Cards -->
<table class="table" style="margin-bottom: 30px;">
    <tr>
        <td width="25%" style="background-color: #e8f5e8; border: 2px solid #4CAF50;">
            <div style="text-align: center; padding: 15px;">
                <h4 style="color: #2E7D32; margin-bottom: 5px;">TOTAL INVOICE</h4>
                <h3 style="color: #2E7D32;">Rp {{ number_format($totalInvoices, 0, ',', '.') }}</h3>
            </div>
        </td>
        <td width="25%" style="background-color: #e3f2fd; border: 2px solid #2196F3;">
            <div style="text-align: center; padding: 15px;">
                <h4 style="color: #1565C0; margin-bottom: 5px;">TERBAYAR</h4>
                <h3 style="color: #1565C0;">Rp {{ number_format($totalPayments, 0, ',', '.') }}</h3>
            </div>
        </td>
        <td width="25%" style="background-color: #fff3e0; border: 2px solid #FF9800;">
            <div style="text-align: center; padding: 15px;">
                <h4 style="color: #EF6C00; margin-bottom: 5px;">SISA PIUTANG</h4>
                <h3 style="color: #EF6C00;">Rp {{ number_format($totalRemaining, 0, ',', '.') }}</h3>
            </div>
        </td>
        <td width="25%" style="background-color: #fce4ec; border: 2px solid #E91E63;">
            <div style="text-align: center; padding: 15px;">
                <h4 style="color: #C2185B; margin-bottom: 5px;">COLLECTION RATE</h4>
                <h3 style="color: #C2185B;">{{ $totalInvoices > 0 ? number_format(($totalPayments / $totalInvoices) * 100, 1) : 0 }}%</h3>
            </div>
        </td>
    </tr>
</table>

<!-- Monthly Chart -->
<div class="chart-container">
    <h3 style="margin-bottom: 20px;">GRAFIK PENJUALAN BULANAN</h3>
    <table class="table">
        <thead>
            <tr>
                <th width="8%">Bulan</th>
                @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'] as $month)
                    <th width="7%">{{ $month }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="font-weight: bold;">Penjualan</td>
                @foreach($monthlyData as $amount)
                    <td class="text-right">{{ number_format($amount, 0, ',', '.') }}</td>
                @endforeach
            </tr>
            <tr>
                <td colspan="13" style="height: 100px; vertical-align: bottom; padding: 0;">
                    @php
                        $maxAmount = max($monthlyData);
                        if($maxAmount > 0) {
                            foreach($monthlyData as $i => $amount) {
                                $height = ($amount / $maxAmount) * 80;
                                echo "<div style='display: inline-block; width: 7%; height: {$height}px; background-color: #4CAF50; margin: 0 1%; vertical-align: bottom;'></div>";
                            }
                        }
                    @endphp
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Payment Status Summary -->
<table class="table">
    <thead>
        <tr>
            <th width="30%">JENIS PEMBAYARAN</th>
            <th width="25%">JUMLAH</th>
            <th width="25%">NILAI</th>
            <th width="20%">PERSENTASE</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Lunas</td>
            <td class="text-center">{{ $invoices->groupBy('id')->filter(function($inv) { return $inv->sum('payment_amount') >= $inv->first()->total_akhir; })->count() }}</td>
            <td class="text-right">{{ number_format($invoices->groupBy('id')->filter(function($inv) { return $inv->sum('payment_amount') >= $inv->first()->total_akhir; })->sum(function($inv) { return $inv->first()->total_akhir; }), 0, ',', '.') }}</td>
            <td class="text-center">{{ $totalInvoices > 0 ? number_format(($invoices->groupBy('id')->filter(function($inv) { return $inv->sum('payment_amount') >= $inv->first()->total_akhir; })->sum(function($inv) { return $inv->first()->total_akhir; }) / $totalInvoices) * 100, 1) : 0 }}%</td>
        </tr>
        <tr>
            <td>Sebagian (Termasuk Titip)</td>
            <td class="text-center">{{ $invoices->groupBy('id')->filter(function($inv) { $paid = $inv->sum('payment_amount'); return $paid > 0 && $paid < $inv->first()->total_akhir; })->count() }}</td>
            <td class="text-right">{{ number_format($invoices->groupBy('id')->filter(function($inv) { $paid = $inv->sum('payment_amount'); return $paid > 0 && $paid < $inv->first()->total_akhir; })->sum('payment_amount'), 0, ',', '.') }}</td>
            <td class="text-center">{{ $totalInvoices > 0 ? number_format(($invoices->groupBy('id')->filter(function($inv) { $paid = $inv->sum('payment_amount'); return $paid > 0 && $paid < $inv->first()->total_akhir; })->sum('payment_amount') / $totalInvoices) * 100, 1) : 0 }}%</td>
        </tr>
        <tr>
            <td>Belum Dibayar</td>
            <td class="text-center">{{ $invoices->groupBy('id')->filter(function($inv) { return $inv->sum('payment_amount') == 0; })->count() }}</td>
            <td class="text-right">{{ number_format($invoices->groupBy('id')->filter(function($inv) { return $inv->sum('payment_amount') == 0; })->sum(function($inv) { return $inv->first()->total_akhir; }), 0, ',', '.') }}</td>
            <td class="text-center">{{ $totalInvoices > 0 ? number_format(($invoices->groupBy('id')->filter(function($inv) { return $inv->sum('payment_amount') == 0; })->sum(function($inv) { return $inv->first()->total_akhir; }) / $totalInvoices) * 100, 1) : 0 }}%</td>
        </tr>
    </tbody>
</table>

<!-- Footer -->
<div class="footer" style="position: relative; margin-top: 50px;">
    <p>Laporan ini dicetak pada: {{ date('d F Y H:i:s') }} | User: {{ auth()->user()->name }}</p>
</div>

</body>
</html>
