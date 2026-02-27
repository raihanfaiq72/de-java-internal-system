<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Sales_Invoices_" . date('Y-m-d') . ".xls");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Invoices</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h3>Sales Invoices</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>No. Invoice</th>
                <th>Ref No</th>
                <th>Mitra</th>
                <th>Jatuh Tempo</th>
                <th>Status Dokumen</th>
                <th>Status Pembayaran</th>
                <th>Total Akhir</th>
                <th>Sisa Tagihan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $index => $invoice)
            @php
                $paid = $invoice->payment->sum('jumlah_bayar');
                $balance = $invoice->total_akhir - $paid;
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($invoice->tgl_invoice)->format('d M Y') }}</td>
                <td>{{ $invoice->nomor_invoice }}</td>
                <td>{{ $invoice->ref_no }}</td>
                <td>{{ $invoice->mitra->nama ?? '-' }}</td>
                <td>{{ $invoice->tgl_jatuh_tempo ? \Carbon\Carbon::parse($invoice->tgl_jatuh_tempo)->format('d M Y') : '-' }}</td>
                <td>{{ $invoice->status_dok }}</td>
                <td>{{ $invoice->status_pembayaran }}</td>
                <td class="text-right">{{ number_format($invoice->total_akhir, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($balance, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="8" class="text-right">Total</th>
                <th class="text-right">{{ number_format($invoices->sum('total_akhir'), 0, ',', '.') }}</th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
