<table class="table">
    <thead>
        <tr>
            <th>SKU</th>
            <th>Nama Produk</th>
            <th>Kategori</th>
            <th>Merk</th>
            <th>Stok Fisik</th>
            <th>Satuan</th>
            <th>Harga Beli</th>
            <th>Nilai Aset</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $product)
                @php
                    $qty = $product->qty;
                    if(request('location_id')) {
                        $qty = $product->location_qty ?? 0;
                    }
                @endphp
                <tr>
            <td>{{ $product->sku_kode }}</td>
            <td>{{ $product->nama_produk }}</td>
            <td>{{ $product->category->nama_kategori ?? '-' }}</td>
            <td>{{ $product->brand->name ?? '-' }}</td>
            <td>{{ $qty }}</td>
            <td>{{ $product->satuan }}</td>
            <td>{{ $product->harga_beli }}</td>
            <td>{{ $qty * $product->harga_beli }}</td>
        </tr>
        @endforeach
    </tbody>
</table>