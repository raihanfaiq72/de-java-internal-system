<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Pembayaran_" . date('Y-m-d') . ".xls");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pembayaran</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h3>Laporan Pembayaran</h3>
    <p>Periode: {{ $startDate }} s/d {{ $endDate }}</p>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>No. Invoice</th>
                <th>Metode</th>
                <th>No. Mitra</th>
                <th>Nama Mitra</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $index => $payment)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($payment->tgl_pembayaran)->format('d M Y') }}</td>
                <td>{{ $payment->nomor_invoice }}</td>
                <td>{{ $payment->metode_pembayaran }}</td>
                <td>{{ $payment->nomor_mitra }}</td>
                <td>{{ $payment->nama_mitra }}</td>
                <td class="text-right">{{ number_format($payment->jumlah_bayar, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6" class="text-right">Total Pembayaran</th>
                <th class="text-right">{{ number_format($totalPaymentAmount, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
