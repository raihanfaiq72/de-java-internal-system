<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Stok_" . date('Y-m-d') . ".xls");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Stok</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h3>Laporan Stok</h3>
    <p>Tanggal Cetak: {{ date('d M Y H:i') }}</p>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Produk</th>
                <th>Kategori</th>
                <th>Unit</th>
                <th>Qty Awal</th>
                <th>Qty Masuk</th>
                <th>Qty Keluar</th>
                <th>Qty Akhir</th>
                <th>Nilai Awal</th>
                <th>Nilai Masuk</th>
                <th>Nilai Keluar</th>
                <th>Nilai Akhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $index => $product)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $product->nama_produk }} {{ $product->sku_kode ? '('.$product->sku_kode.')' : '' }}</td>
                <td>{{ $product->nama_kategori }}</td>
                <td>{{ $product->satuan }}</td>
                <td class="text-right">{{ number_format($product->opening_qty, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($product->qty_in, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($product->qty_out, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($product->closing_qty, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($product->opening_value, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($product->value_in, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($product->value_out, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($product->closing_value, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-right">Total</th>
                <th class="text-right">{{ number_format($products->sum('opening_qty'), 0, ',', '.') }}</th>
                <th class="text-right">{{ number_format($products->sum('qty_in'), 0, ',', '.') }}</th>
                <th class="text-right">{{ number_format($products->sum('qty_out'), 0, ',', '.') }}</th>
                <th class="text-right">{{ number_format($products->sum('closing_qty'), 0, ',', '.') }}</th>
                <th class="text-right">{{ number_format($products->sum('opening_value'), 0, ',', '.') }}</th>
                <th class="text-right">{{ number_format($products->sum('value_in'), 0, ',', '.') }}</th>
                <th class="text-right">{{ number_format($products->sum('value_out'), 0, ',', '.') }}</th>
                <th class="text-right">{{ number_format($products->sum('closing_value'), 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
