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
                @if(!in_array(0, $hiddenColumns)) <th>Nama Produk</th> @endif
                @if(!in_array(1, $hiddenColumns)) <th>Kode (SKU)</th> @endif
                @if(!in_array(2, $hiddenColumns)) <th>Kategori</th> @endif
                @if(!in_array(3, $hiddenColumns)) <th>Satuan</th> @endif
                @if(!in_array(4, $hiddenColumns)) <th>Kuantitas Terjual</th> @endif
            </tr>
        </thead>
        <tbody>
            @foreach($soldProducts as $index => $product)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                @if(!in_array(0, $hiddenColumns)) <td>{{ $product->nama_produk }}</td> @endif
                @if(!in_array(1, $hiddenColumns)) <td>{{ $product->sku_kode }}</td> @endif
                @if(!in_array(2, $hiddenColumns)) <td>{{ $product->nama_kategori ?? '-' }}</td> @endif
                @if(!in_array(3, $hiddenColumns)) <td>{{ $product->satuan }}</td> @endif
                @if(!in_array(4, $hiddenColumns)) <td class="text-right">{{ number_format($product->total_qty, 0, ',', '.') }}</td> @endif
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                @php 
                    $footSpan = empty($hiddenColumns) ? 5 : (5 - count($hiddenColumns));
                @endphp
                <th colspan="{{ $footSpan }}" class="text-right">Total Kuantitas</th>
                @if(!in_array(4, $hiddenColumns))
                    <th class="text-right">{{ number_format($totalSoldQty, 0, ',', '.') }}</th>
                @endif
            </tr>
        </tfoot>
    </table>
</body>
</html>
