<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Penjualan</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #333;
            margin: 20px;
        }

        .nota-container {
            width: 800px;
            border: 1px solid #000;
            padding: 15px;
            margin: auto;
        }

        .header {
            display: flex;
            justify-content: flex-end;
            text-align: right;
            margin-bottom: 10px;
        }

        .invoice-info {
            line-height: 1.2;
        }

        .customer-section {
            display: flex;
            justify-content: flex-start;
            margin-bottom: 15px;
        }

        .customer-details {
            width: 50%;
        }

        .customer-details table {
            width: 100%;
            border-collapse: collapse;
        }

        .customer-details td {
            padding: 2px;
            vertical-align: top;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .main-table th, .main-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }

        .main-table .text-left {
            text-align: left;
        }

        .main-table .text-right {
            text-align: right;
        }

        .footer-section {
            display: flex;
            justify-content: space-between;
        }

        .footer-left {
            width: 60%;
        }

        .footer-right {
            width: 35%;
        }

        .signature-grid {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            text-align: center;
        }

        .signature-box {
            width: 30%;
        }

        .total-table {
            width: 100%;
            border-collapse: collapse;
        }

        .total-table td {
            padding: 5px;
            border: 1px solid #000;
        }

        .note {
            font-style: italic;
            margin-top: 10px;
            font-size: 10px;
        }

        .bank-info {
            margin-top: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="nota-container">
    <div class="header">
        <div class="invoice-info">
            <strong>{{ \Carbon\Carbon::parse($invoice->tgl_invoice)->format('d F Y') }}</strong><br>
            No. Invoice<br>
            <strong>{{ $invoice->nomor_invoice }}</strong>
        </div>
    </div>

    <div class="customer-section">
        <div class="customer-details">
            <table>
                <tr>
                    <td width="30%">Supplier</td>
                    <td>: {{ $invoice->mitra->nama ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>: {{ $invoice->mitra->alamat ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Ref. No.</td>
                    <td>: {{ $invoice->ref_no ?? '-' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <table class="main-table">
        <thead>
            <tr>
                <th width="5%">No.</th>
                <th width="10%">Kode</th>
                <th width="35%">Nama Barang</th>
                <th width="10%">Size</th>
                <th width="5%">QTY</th>
                <th width="15%">Harga Satuan</th>
                <th width="5%">Disc %</th>
                <th width="15%">Harga Total</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($invoice->items as $index => $item)
            @php $lineTotal = $item->qty * $item->harga_satuan; $total += $lineTotal; @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->product->kode_produk ?? '-' }}</td>
                <td class="text-left">{{ $item->product->nama_produk ?? '-' }}</td>
                <td>{{ $item->product->unit->nama_unit ?? '-' }}</td>
                <td>{{ number_format($item->qty) }}</td>
                <td class="text-right">{{ number_format($item->harga_satuan) }}</td>
                <td></td>
                <td class="text-right">{{ number_format($lineTotal) }}</td>
            </tr>
            @endforeach
            @for($i = count($invoice->items); $i < 5; $i++)
            <tr>
                <td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
            @endfor
        </tbody>
    </table>

    <div class="footer-section">
        <div class="footer-left">
            <div class="note">
                Note : Barang yang sudah diterima sesuai dengan invoice.
            </div>
            <div class="signature-grid">
                <div class="signature-box">
                    Penerima<br><br><br><br>
                    (................)
                </div>
                <div class="signature-box">
                    Gudang<br><br><br><br>
                    (................)
                </div>
                <div class="signature-box">
                    Supplier<br><br><br><br>
                    (................)
                </div>
            </div>
        </div>
        <div class="footer-right">
            <table class="total-table">
                <tr>
                    <td width="40%">DP</td>
                    <td class="text-right">Rp {{ number_format($invoice->payment->sum('jumlah_bayar') ?? 0) }}</td>
                </tr>
                <tr>
                    <td>Sisa</td>
                    <td class="text-right">Rp {{ number_format($invoice->total_akhir - ($invoice->payment->sum('jumlah_bayar') ?? 0)) }}</td>
                </tr>
                <tr>
                    <td><strong>TOTAL</strong></td>
                    <td class="text-right"><strong>{{ number_format($invoice->total_akhir) }}</strong></td>
                </tr>
                <tr>
                    <td>Administrasi</td>
                    <td class="text-right">Rp</td>
                </tr>
            </table>
        </div>
    </div>
</div>

</body>
</html>