<?php
header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment; filename=Laporan_Pembayaran_Detail_" . date('Y-m-d') . ".xlsx");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pembayaran Detail</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 5px; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .invoice-header { background-color: #e6f3ff; font-weight: bold; }
        .item-row { background-color: #ffffff; }
        .invoice-footer { background-color: #f9f9f9; font-weight: bold; }
        .grand-total { background-color: #d4edda; font-weight: bold; }
    </style>
</head>
<body>
    <h3>Laporan Pembayaran Detail dengan Item Invoice</h3>
    <p>Periode: {{ $startDate }} s/d {{ $endDate }}</p>
    
    @foreach($payments as $payment)
        @if(!empty($payment->items))
        <table>
            <thead>
                <tr class="invoice-header">
                    <th colspan="8">Invoice: {{ $payment->nomor_invoice }}</th>
                </tr>
                <tr>
                    <th colspan="2">Tanggal: {{ \Carbon\Carbon::parse($payment->tgl_pembayaran)->format('d M Y') }}</th>
                    <th colspan="3">Mitra: {{ $payment->nama_mitra }} ({{ $payment->nomor_mitra }})</th>
                    <th colspan="3">Metode: {{ $payment->metode_pembayaran }}</th>
                </tr>
                <tr>
                    <th class="text-center">No</th>
                    <th>Nama Produk</th>
                    <th>Kode</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Harga</th>
                    <th class="text-right">Diskon</th>
                    <th class="text-right">Total</th>
                    <th class="text-right">Total Akhir</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payment->items as $index => $item)
                <tr class="item-row">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->nama_produk }}</td>
                    <td>{{ $item->sku_kode }}</td>
                    <td class="text-center">{{ number_format($item->qty, 0, ',', '.') }} {{ $item->satuan }}</td>
                    <td class="text-right">{{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->total_diskon, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->total_harga_item, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->total_akhir, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="invoice-footer">
                    <td colspan="6" class="text-right">Subtotal Invoice</td>
                    <td class="text-right">{{ number_format($payment->items->sum('total_harga_item'), 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($payment->items->sum('total_akhir'), 0, ',', '.') }}</td>
                </tr>
                <tr class="invoice-footer">
                    <td colspan="7" class="text-right">Jumlah Terbayar</td>
                    <td class="text-right">{{ number_format($payment->jumlah_bayar, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
        @endif
    @endforeach
    
    @if($payments->isNotEmpty())
    <table>
        <thead>
            <tr>
                <th colspan="8">Ringkasan Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            <tr class="grand-total">
                <td colspan="7" class="text-right">Total Invoice</td>
                <td class="text-center">{{ $totalUniqueInvoices }}</td>
            </tr>
            <tr class="grand-total">
                <td colspan="7" class="text-right">Total Pembayaran</td>
                <td class="text-right">{{ number_format($totalPaymentAmount, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
    @endif
</body>
</html>
