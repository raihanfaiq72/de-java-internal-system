<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Invoice_General_" . date('Y-m-d') . ".xls");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Invoice - Rekap Umum</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 5px; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        h4 { margin-bottom: 5px; }
    </style>
</head>
<body>
    <h3>Laporan Invoice - Rekap Umum</h3>
    <p>Periode: {{ $year }}</p>

    <h4>Income vs Expenditure (Per Bulan)</h4>
    <table>
        <thead>
            <tr>
                <th>Bulan</th>
                <th>Income (Pemasukan)</th>
                <th>Expenditure (Pengeluaran)</th>
                <th>Profit/Loss</th>
            </tr>
        </thead>
        <tbody>
            @foreach($chartLabels as $index => $label)
            <tr>
                <td>{{ $label }}</td>
                <td class="text-right">{{ number_format($chartIncome[$index], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($chartExpense[$index], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($chartIncome[$index] - $chartExpense[$index], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th>Total</th>
                <th class="text-right">{{ number_format(array_sum($chartIncome), 0, ',', '.') }}</th>
                <th class="text-right">{{ number_format(array_sum($chartExpense), 0, ',', '.') }}</th>
                <th class="text-right">{{ number_format(array_sum($chartIncome) - array_sum($chartExpense), 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    <h4>Komposisi Piutang (Sales)</h4>
    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th>Nilai</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total Penjualan (Sales)</td>
                <td class="text-right">{{ number_format($totalSales, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Diterima (Received)</td>
                <td class="text-right">{{ number_format($totalReceived, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Belum Dibayar (Outstanding)</td>
                <td class="text-right">{{ number_format($outstanding, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
