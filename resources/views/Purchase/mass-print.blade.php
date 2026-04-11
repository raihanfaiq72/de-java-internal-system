<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>LAPORAN PEMBELIAN {{ strtoupper($salesPerson->name ?? 'Tanpa Staff') }}</title>
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
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
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
            background-color: #f0f0f0 !important;
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
            background-color: #f9f9f9 !important;
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
            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .page-break {
                page-break-before: always;
            }
            
            .no-print {
                display: none;
            }

            .table th {
                background-color: #f0f0f0 !important;
            }

            .total-row {
                background-color: #f9f9f9 !important;
            }
        }
    </style>
</head>
<body>

<!-- STEP 1: PENGELUARAN UANG -->
<div class="header">
    <h2>LAPORAN PENGELUARAN PERIODE {{ strtoupper(date('F Y', strtotime($dateFrom))) }}</h2>
    <h3>Staff / Purchaser: {{ $salesPerson->name ?? 'Tanpa Staff' }}</h3>
    <p>Periode: {{ date('d M Y', strtotime($dateFrom)) }} - {{ date('d M Y', strtotime($dateTo)) }}</p>
</div>

<table class="table">
    <thead>
        <tr>
            <th width="15%">TGL PEMBAYARAN</th>
            <th width="40%">PEMASOK</th>
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
    <h2>DETAIL PEMBAYARAN PER INVOICE (PEMBELIAN)</h2>
    <h3>Staff / Purchaser: {{ $salesPerson->name ?? 'Tanpa Staff' }}</h3>
    <p>Periode: {{ date('d M Y', strtotime($dateFrom)) }} - {{ date('d M Y', strtotime($dateTo)) }}</p>
</div>

<table class="table">
    <thead>
        <tr>
            <th width="10%">TGL INVOICE</th>
            <th width="15%">NO. INVOICE</th>
            <th width="30%">PEMASOK</th>
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
                    <tr style="background-color: #f9f9f9 !important;">
                        <td colspan="3" style="padding-left: 30px;">
                            {{ date('d-M-y', strtotime($payment->payment_date)) }} 
                            @if($payment->payment_note) - {{ $payment->payment_note }} @endif
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
    <h2>REKAPITULASI PEMBELIAN</h2>
    <h3>Staff / Purchaser: {{ $salesPerson->name ?? 'Tanpa Staff' }}</h3>
    <p>Periode: {{ date('d M Y', strtotime($dateFrom)) }} - {{ date('d M Y', strtotime($dateTo)) }}</p>
</div>

<!-- Summary Cards -->
<table class="table" style="margin-bottom: 30px; border: none;">
    <tr>
        <td width="25%" style="background-color: #e8f5e8 !important; border: 2px solid #4CAF50 !important; padding: 0;">
            <div style="text-align: center; padding: 15px;">
                <h4 style="color: #2E7D32; margin-bottom: 5px; font-size: 11px;">TOTAL INVOICE</h4>
                <h3 style="color: #2E7D32; font-size: 14px;">Rp {{ number_format($totalInvoices, 0, ',', '.') }}</h3>
            </div>
        </td>
        <td width="25%" style="background-color: #e3f2fd !important; border: 2px solid #2196F3 !important; padding: 0;">
            <div style="text-align: center; padding: 15px;">
                <h4 style="color: #1565C0; margin-bottom: 5px; font-size: 11px;">TERBAYAR</h4>
                <h3 style="color: #1565C0; font-size: 14px;">Rp {{ number_format($totalPayments, 0, ',', '.') }}</h3>
            </div>
        </td>
        <td width="25%" style="background-color: #fff3e0 !important; border: 2px solid #FF9800 !important; padding: 0;">
            <div style="text-align: center; padding: 15px;">
                <h4 style="color: #EF6C00; margin-bottom: 5px; font-size: 11px;">SISA PIUTANG</h4>
                <h3 style="color: #EF6C00; font-size: 14px;">Rp {{ number_format($totalRemaining, 0, ',', '.') }}</h3>
            </div>
        </td>
        <td width="25%" style="background-color: #fce4ec !important; border: 2px solid #E91E63 !important; padding: 0;">
            <div style="text-align: center; padding: 15px;">
                <h4 style="color: #C2185B; margin-bottom: 5px; font-size: 11px;">PAYMENT RATE</h4>
                <h3 style="color: #C2185B; font-size: 14px;">{{ $totalInvoices > 0 ? number_format(($totalPayments / $totalInvoices) * 100, 1) : 0 }}%</h3>
            </div>
        </td>
    </tr>
</table>

<!-- Monthly Chart -->
<div class="chart-container">
    <h3 style="margin-bottom: 20px;">GRAFIK PEMBELIAN BULANAN</h3>
    <table class="table" style="table-layout: fixed;">
        <thead>
            <tr>
                <th width="100px">Bulan</th>
                @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'] as $month)
                    <th>{{ $month }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="font-weight: bold;">Pembelian</td>
                @foreach($monthlyData as $amount)
                    <td class="text-right" style="font-size: 9px; padding: 4px;">{{ $amount > 0 ? number_format($amount/1000000, 1) . 'M' : '0' }}</td>
                @endforeach
            </tr>
            <tr>
                <td style="font-weight: bold; vertical-align: bottom;">Grafik</td>
                @foreach($monthlyData as $amount)
                    <td style="height: 120px; vertical-align: bottom; padding: 0 5px; border-top: none;">
                        @php
                            $maxAmount = max($monthlyData) ?: 1;
                            $height = ($amount / $maxAmount) * 100;
                        @endphp
                        <div style="width: 100%; height: {{ $height }}px; background-color: #2196F3 !important; border-top-left-radius: 2px; border-top-right-radius: 2px;"></div>
                    </td>
                @endforeach
            </tr>
        </tbody>
    </table>
    <p style="text-align: left; font-size: 9px; margin-top: 5px; color: #666;">* Nilai grafik dalam jutaan (M)</p>
</div>

<!-- Footer -->
<div class="footer" style="position: relative; margin-top: 50px;">
    <p>Laporan ini dicetak pada: {{ date('d F Y H:i:s') }} | User: {{ auth()->user()->name }}</p>
</div>

</body>
</html>
