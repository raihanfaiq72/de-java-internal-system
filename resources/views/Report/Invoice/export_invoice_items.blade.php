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
                @if(!in_array(0, $hiddenColumns)) <th>Status Pembayaran</th> @endif
                @if(!in_array(1, $hiddenColumns)) <th>Tanggal Invoice</th> @endif
                @if(!in_array(4, $hiddenColumns)) <th>Nomor Invoice</th> @endif
                @if(!in_array(3, $hiddenColumns)) <th>Nama Mitra</th> @endif
                @if(!in_array(2, $hiddenColumns)) <th>Nama Produk</th> @endif
                @if(!in_array(5, $hiddenColumns)) <th>Qty</th> @endif
                @if(!in_array(6, $hiddenColumns)) <th>Harga Satuan</th> @endif
                @if(!in_array(7, $hiddenColumns)) <th>Diskon Item</th> @endif
                @if(!in_array(8, $hiddenColumns)) <th>Total Diskon</th> @endif
                @if(!in_array(9, $hiddenColumns)) <th>Total Akhir</th> @endif
            </tr>
        </thead>
        <tbody>
            @foreach($invoiceItems as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                @if(!in_array(0, $hiddenColumns)) <td>{{ $item->status_pembayaran }}</td> @endif
                @if(!in_array(1, $hiddenColumns)) <td>{{ \Carbon\Carbon::parse($item->tgl_invoice)->format('d M Y') }}</td> @endif
                @if(!in_array(4, $hiddenColumns)) <td>{{ $item->nomor_invoice }}</td> @endif
                @if(!in_array(3, $hiddenColumns)) <td>{{ $item->nama_mitra }}</td> @endif
                @if(!in_array(2, $hiddenColumns)) <td>{{ $item->nama_produk }}</td> @endif
                @if(!in_array(5, $hiddenColumns)) <td class="text-right">{{ number_format($item->qty, 0, ',', '.') }}</td> @endif
                @if(!in_array(6, $hiddenColumns)) <td class="text-right">{{ number_format($item->harga_satuan, 0, ',', '.') }}</td> @endif
                @if(!in_array(7, $hiddenColumns)) <td class="text-right">{{ number_format($item->diskon_nilai, 0, ',', '.') }}</td> @endif
                @if(!in_array(8, $hiddenColumns)) <td class="text-right">{{ number_format($item->total_diskon, 0, ',', '.') }}</td> @endif
                @if(!in_array(9, $hiddenColumns)) <td class="text-right">{{ number_format($item->total_akhir, 0, ',', '.') }}</td> @endif
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                @php 
                    $footSpan = empty($hiddenColumns) ? 10 : (10 - count($hiddenColumns));
                @endphp
                <th colspan="{{ $footSpan }}" class="text-right">Total Transaksi</th>
                @if(!in_array(9, $hiddenColumns))
                    <th class="text-right">{{ number_format($summaryTotalTransaction, 0, ',', '.') }}</th>
                @endif
            </tr>
        </tfoot>
    </table>
</body>
</html>
