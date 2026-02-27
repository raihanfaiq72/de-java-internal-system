<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Produk_Terjual_" . date('Y-m-d') . ".xls");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Produk Terjual</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h3>Laporan Produk Terjual</h3>
    <p>Periode: {{ $startDate }} s/d {{ $endDate }}</p>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Produk</th>
                <th>Kode (SKU)</th>
                <th>Kategori</th>
                <th>Satuan</th>
                <th>Kuantitas Terjual</th>
            </tr>
        </thead>
        <tbody>
            @foreach($soldProducts as $index => $product)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $product->nama_produk }}</td>
                <td>{{ $product->sku_kode }}</td>
                <td>{{ $product->nama_kategori ?? '-' }}</td>
                <td>{{ $product->satuan }}</td>
                <td class="text-right">{{ number_format($product->total_qty, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" class="text-right">Total Kuantitas</th>
                <th class="text-right">{{ number_format($totalSoldQty, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
