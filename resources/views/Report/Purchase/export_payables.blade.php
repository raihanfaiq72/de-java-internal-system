<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Hutang_" . date('Y-m-d') . ".xls");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Hutang (Unpaid Purchase Invoices)</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h3>Laporan Hutang (Unpaid Purchase Invoices)</h3>
    @if($startDate && $endDate)
    <p>Periode: {{ $startDate }} s/d {{ $endDate }}</p>
    @endif
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Invoice</th>
                <th>No. Invoice</th>
                <th>Jatuh Tempo</th>
                <th>Supplier</th>
                <th>Status</th>
                <th>Total Tagihan</th>
                <th>Sudah Dibayar</th>
                <th>Sisa Tagihan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payables as $index => $invoice)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($invoice->tgl_invoice)->format('d M Y') }}</td>
                <td>{{ $invoice->nomor_invoice }}</td>
                <td>{{ $invoice->tgl_jatuh_tempo ? \Carbon\Carbon::parse($invoice->tgl_jatuh_tempo)->format('d M Y') : '-' }}</td>
                <td>{{ $invoice->mitra->nama ?? '-' }}</td>
                <td>{{ $invoice->status_pembayaran }}</td>
                <td class="text-right">{{ number_format($invoice->total_akhir, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($invoice->total_akhir - $invoice->sisa_tagihan, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($invoice->sisa_tagihan, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="8" class="text-right">Total Sisa Tagihan</th>
                <th class="text-right">{{ number_format($payables->sum('sisa_tagihan'), 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
