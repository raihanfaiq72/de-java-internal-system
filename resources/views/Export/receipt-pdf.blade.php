<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Export PDF Kuitansi</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        .header h2 {
            color: #007bff;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .summary-item {
            display: inline-block;
            margin-right: 30px;
            margin-bottom: 10px;
        }
        .summary-label {
            font-weight: bold;
            color: #495057;
        }
        .summary-value {
            color: #007bff;
            font-size: 16px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background: #007bff;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #007bff;
        }
        td {
            padding: 8px 10px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .status-paid {
            background: #28a745;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
        }
        .status-partial {
            background: #ffc107;
            color: #212529;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
        }
        .status-unpaid {
            background: #dc3545;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 10px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .amount {
            font-weight: bold;
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN KUITANSI PENJUALAN</h2>
        <p>
            @if($exportAll)
                Export Semua Data
            @else
                Periode: {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : 'Awal' }} 
                s/d {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : 'Sekarang' }}
            @endif
        </p>
        <p>Status: {{ $statusFilter ? ucfirst($statusFilter) : 'Semua Status' }}</p>
        <p>Tanggal Export: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="summary">
        <div class="summary-item">
            <span class="summary-label">Total Kuitansi:</span>
            <span class="summary-value">{{ count($payments) }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Total Nominal:</span>
            <span class="summary-value">Rp {{ number_format($payments->sum('jumlah_bayar'), 0, ',', '.') }}</span>
        </div>
        @if($statusFilter)
        <div class="summary-item">
            <span class="summary-label">Filter Status:</span>
            <span class="summary-value">{{ ucfirst($statusFilter) }}</span>
        </div>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nomor Pembayaran</th>
                <th>Tanggal Pembayaran</th>
                <th>Nomor Invoice</th>
                <th>Pelanggan</th>
                <th>Metode Pembayaran</th>
                <th class="text-right">Jumlah Bayar</th>
                <th>Status Invoice</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $index => $payment)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $payment->nomor_pembayaran }}</td>
                <td>{{ \Carbon\Carbon::parse($payment->tgl_pembayaran)->format('d/m/Y') }}</td>
                <td>{{ $payment->invoice->nomor_invoice ?? '-' }}</td>
                <td>{{ $payment->invoice->mitra->nama ?? 'Umum' }}</td>
                <td>{{ $payment->metode_pembayaran }}</td>
                <td class="text-right amount">Rp {{ number_format($payment->jumlah_bayar, 0, ',', '.') }}</td>
                <td class="text-center">
                    @if($payment->invoice->status_pembayaran == 'Paid')
                        <span class="status-paid">Lunas</span>
                    @elseif($payment->invoice->status_pembayaran == 'Partially Paid')
                        <span class="status-partial">Sebagian</span>
                    @else
                        <span class="status-unpaid">Belum Lunas</span>
                    @endif
                </td>
                <td>{{ $payment->catatan ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">Tidak ada data kuitansi</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6" class="text-right">TOTAL:</th>
                <th class="text-right amount">Rp {{ number_format($payments->sum('jumlah_bayar'), 0, ',', '.') }}</th>
                <th colspan="2"></th>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Laporan ini di-generate secara otomatis oleh sistem pada {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>© {{ date('Y') }} Sistem Penjualan - All Rights Reserved</p>
    </div>
</body>
</html>
