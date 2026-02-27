<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Invoice_Per_Produk_" . date('Y-m-d') . ".xls");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Invoice Per Produk</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h3>Laporan Invoice Per Produk</h3>
    <p>Periode: {{ $startDate }} s/d {{ $endDate }}</p>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Status Pembayaran</th>
                <th>Tanggal Invoice</th>
                <th>Nomor Invoice</th>
                <th>Nama Mitra</th>
                <th>Nama Produk</th>
                <th>Qty</th>
                <th>Harga Satuan</th>
                <th>Diskon Item</th>
                <th>Total Diskon</th>
                <th>Total Akhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoiceItems as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->status_pembayaran }}</td>
                <td>{{ \Carbon\Carbon::parse($item->tgl_invoice)->format('d M Y') }}</td>
                <td>{{ $item->nomor_invoice }}</td>
                <td>{{ $item->nama_mitra }}</td>
                <td>{{ $item->nama_produk }}</td>
                <td class="text-right">{{ number_format($item->qty, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item->diskon_nilai, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item->total_diskon, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item->total_akhir, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="10" class="text-right">Total Transaksi</th>
                <th class="text-right">{{ number_format($summaryTotalTransaction, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
