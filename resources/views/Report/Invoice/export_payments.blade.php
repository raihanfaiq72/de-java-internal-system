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
                @if(!in_array(0, $hiddenColumns)) <th>Tanggal</th> @endif
                @if(!in_array(1, $hiddenColumns)) <th>No. Invoice</th> @endif
                @if(!in_array(2, $hiddenColumns)) <th>Metode</th> @endif
                @if(!in_array(3, $hiddenColumns)) <th>No. Mitra</th> @endif
                @if(!in_array(4, $hiddenColumns)) <th>Nama Mitra</th> @endif
                @if(!in_array(5, $hiddenColumns)) <th>Jumlah</th> @endif
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $index => $payment)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                @if(!in_array(0, $hiddenColumns)) <td>{{ \Carbon\Carbon::parse($payment->tgl_pembayaran)->format('d M Y') }}</td> @endif
                @if(!in_array(1, $hiddenColumns)) <td>{{ $payment->nomor_invoice }}</td> @endif
                @if(!in_array(2, $hiddenColumns)) <td>{{ $payment->metode_pembayaran }}</td> @endif
                @if(!in_array(3, $hiddenColumns)) <td>{{ $payment->nomor_mitra }}</td> @endif
                @if(!in_array(4, $hiddenColumns)) <td>{{ $payment->nama_mitra }}</td> @endif
                @if(!in_array(5, $hiddenColumns)) <td class="text-right">{{ number_format($payment->jumlah_bayar, 0, ',', '.') }}</td> @endif
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                @php 
                    $footSpan = empty($hiddenColumns) ? 6 : (6 - count($hiddenColumns));
                @endphp
                <th colspan="{{ $footSpan }}" class="text-right">Total Pembayaran</th>
                @if(!in_array(5, $hiddenColumns))
                    <th class="text-right">{{ number_format($totalPaymentAmount, 0, ',', '.') }}</th>
                @endif
            </tr>
        </tfoot>
    </table>
</body>
</html>
