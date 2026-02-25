<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Stok</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        h2 { margin-bottom: 5px; }
        .header { margin-bottom: 20px; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>Laporan Stok</h2>
        <p>Tanggal: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th class="text-center">Stok</th>
                <th class="text-center">Satuan</th>
                <th class="text-end">Harga Beli</th>
                <th class="text-end">Nilai Aset</th>
            </tr>
        </thead>
        <tbody>
            @php $totalAsset = 0; @endphp
            @foreach($products as $product)
            @php
                $qty = $product->qty;
                if(request('location_id')) {
                    $qty = $product->location_qty ?? 0;
                }
                $asset = $qty * $product->harga_beli;
                $totalAsset += $asset;
            @endphp
            <tr>
                <td>{{ $product->sku_kode }}</td>
                <td>{{ $product->nama_produk }}</td>
                <td>{{ $product->category->nama_kategori ?? '-' }}</td>
                <td class="text-center">{{ number_format($qty, 0, ',', '.') }}</td>
                <td class="text-center">{{ $product->satuan }}</td>
                <td class="text-end">Rp {{ number_format($product->harga_beli, 0, ',', '.') }}</td>
                <td class="text-end">Rp {{ number_format($asset, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="text-end"><strong>Total Nilai Aset</strong></td>
                <td class="text-end"><strong>Rp {{ number_format($totalAsset, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>