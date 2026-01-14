<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuitansi Pembayaran</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
            margin: 20px;
        }
        .container {
            width: 700px;
            margin: auto;
            border: 1px solid #ccc;
            padding: 30px;
            background: #fff;
        }
        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .label {
            font-weight: bold;
            width: 150px;
        }
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .main-table th {
            background: #f4f4f4;
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }
        .main-table td {
            border: 1px solid #333;
            padding: 8px;
        }
        .summary-row {
            font-weight: bold;
            background: #f9f9f9;
        }
        .footer {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 200px;
            text-align: center;
        }
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }
        .amount-box {
            font-size: 14px;
            font-weight: bold;
            background: #eee;
            padding: 10px;
            display: inline-block;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <div>
            <div class="title">Kuitansi Pembayaran</div>
            <div style="margin-top: 5px;">No. Kuitansi: <strong>{{ $payment->nomor_pembayaran }}</strong></div>
        </div>
        <div style="text-align: right">
            <div>Tanggal: <strong>{{ \Carbon\Carbon::parse($payment->tgl_pembayaran)->format('d/m/Y') }}</strong></div>
            <div style="margin-top: 5px;">Status: <strong>SETTLED</strong></div>
        </div>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Diterima @if($payment->invoice->tipe_invoice == 'Purchase') Dari @else Untuk @endif</td>
            <td>: {{ $payment->invoice->mitra->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Metode Pembayaran</td>
            <td>: {{ $payment->metode_pembayaran }} ({{ $payment->akun_keuangan->nama_akun ?? '-' }})</td>
        </tr>
        <tr>
            <td class="label">Catatan</td>
            <td>: {{ $payment->catatan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">No. Referensi</td>
            <td>: {{ $payment->ref_no ?? '-' }}</td>
        </tr>
    </table>

    <div style="margin-bottom: 10px; font-weight: bold;">Rincian Pembayaran:</div>
    <table class="main-table">
        <thead>
            <tr>
                <th>No. Invoice</th>
                <th>Tanggal</th>
                <th style="text-align: right">Total Invoice</th>
                <th style="text-align: right">Jumlah Terbayar</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $payment->invoice->nomor_invoice }}</td>
                <td>{{ \Carbon\Carbon::parse($payment->invoice->tgl_invoice)->format('d/m/Y') }}</td>
                <td style="text-align: right">{{ number_format($payment->invoice->total_akhir, 2) }}</td>
                <td style="text-align: right; font-weight: bold;">{{ number_format($payment->jumlah_bayar, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        <div class="amount-box">
            TOTAL: IDR {{ number_format($payment->jumlah_bayar, 2) }}
        </div>
        @php $sgd_rate = 13128.53; $sgd = $payment->jumlah_bayar / $sgd_rate; @endphp
        <div style="margin-left: 20px; display: inline-block; color: #666;">
            (Equivalent to SGD {{ number_format($sgd, 2) }})
        </div>
    </div>

    <div class="footer">
        <div class="signature-box">
            Penerima / Pembayar<br><br><br>
            <div class="signature-line">( {{ $payment->invoice->mitra->nama ?? '................' }} )</div>
        </div>
        <div class="signature-box">
            Admin Keuangan<br><br><br>
            <div class="signature-line">( ................................ )</div>
        </div>
    </div>
</div>

<script>
    window.onload = function() {
        // window.print();
    }
</script>

</body>
</html>
