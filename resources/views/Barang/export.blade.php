<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Data_Barang_" . date('Y-m-d') . ".xls");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Barang</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h3>Data Barang</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>SKU</th>
                <th>Supplier</th>
                <th>Brand</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th>Kemasan</th>
                <th>Satuan</th>
                <th>Harga Beli</th>
                <th>Harga Jual</th>
                <th>Harga Tempo</th>
                <th>Stok</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $index => $product)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $product->sku_kode }}</td>
                <td>{{ $product->supplier->nama ?? '-' }}</td>
                <td>{{ $product->brand->nama_brand ?? '-' }}</td>
                <td>{{ $product->nama_produk }}</td>
                <td>{{ $product->category->nama_kategori ?? '-' }}</td>
                <td>{{ $product->kemasan }}</td>
                <td>{{ $product->satuan }}</td>
                <td class="text-right">{{ number_format($product->harga_beli, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($product->harga_jual, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($product->harga_tempo, 0, ',', '.') }}</td>
                <td class="text-right">{{ $product->stok ?? 0 }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
