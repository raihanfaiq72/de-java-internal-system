<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>LAPORAN TAGIHAN SUPPLIER</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h2 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .bold {
            font-weight: bold;
        }
        .total-row {
            background-color: #f0f0f0;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN TAGIHAN SUPPLIER</h2>
        <p>Tanggal Cetak: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>TGL-TR</th>
                <th>TGL-JT</th>
                <th>NO. INVOICE</th>
                <th>NAMA SUPPLIER</th>
                <th>KOTA</th>
                <th>TOTAL</th>
                <th>TITIPAN</th>
                <th>SISA TAGIHAN</th>
                <th>KET</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($invoiceData as $data)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($data['tgl_invoice'])->format('d/m/Y') }}</td>
                    <td>{{ $data['tgl_jatuh_tempo'] ? \Carbon\Carbon::parse($data['tgl_jatuh_tempo'])->format('d/m/Y') : '-' }}</td>
                    <td>{{ $data['nomor_invoice'] }}</td>
                    <td class="bold">{{ $data['mitra_name'] }}</td>
                    <td>{{ $data['mitra_city'] }}</td>
                    <td class="text-right">{{ number_format($data['total_akhir'], 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($data['total_paid'], 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($data['remaining'], 0, ',', '.') }}</td>
                    <td>{{ $data['keterangan'] ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">Tidak ada data tagihan supplier</td>
                </tr>
            @endforelse
        </tbody>
        @if ($invoiceData->count() > 0)
            <tfoot>
                <tr class="total-row">
                    <td colspan="5" class="text-right bold">TOTAL:</td>
                    <td class="text-right">{{ number_format($totalInvoice, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($totalPayment, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($totalRemaining, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        @endif
    </table>
</body>
</html>
