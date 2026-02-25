<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
    <table border="1" style="border-collapse: collapse; width: 100%;">
        <thead>
            <tr>
                <th colspan="7" style="text-align: center; font-weight: bold; font-size: 16px; border: 1px solid black;">LAPORAN KAS KECIL CV DE JAVANESE AUTO PARTS</th>
            </tr>
            <tr>
                <th colspan="7" style="text-align: center; font-weight: bold; font-size: 14px; border: 1px solid black;">PERIODE {{ $start->translatedFormat('d F Y') }} - {{ $end->translatedFormat('d F Y') }}</th>
            </tr>
            <tr>
                <th style="border: 1px solid black; font-weight: bold; text-align: center; width: 50px;">No</th>
                <th style="border: 1px solid black; font-weight: bold; text-align: center; width: 100px;">Tanggal</th>
                <th style="border: 1px solid black; font-weight: bold; text-align: center; width: 150px;">Nama Akun</th>
                <th style="border: 1px solid black; font-weight: bold; text-align: center; width: 300px;">Keterangan</th>
                <th style="border: 1px solid black; font-weight: bold; text-align: center; width: 120px;">Debit</th>
                <th style="border: 1px solid black; font-weight: bold; text-align: center; width: 120px;">Kredit</th>
                <th style="border: 1px solid black; font-weight: bold; text-align: center; width: 120px;">Total Saldo</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalDebit = 0;
                $totalCredit = 0;
            @endphp
            @foreach($rows as $index => $row)
                @php
                    $totalDebit += $row['debit'];
                    $totalCredit += $row['credit'];
                @endphp
                <tr>
                    <td style="border: 1px solid black; text-align: center;">{{ $loop->iteration }}</td>
                    <td style="border: 1px solid black; text-align: center;">{{ \Carbon\Carbon::parse($row['date'])->format('M d, y') }}</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $row['account_name'] }}</td>
                    <td style="border: 1px solid black;">{{ $row['description'] }}</td>
                    <td style="border: 1px solid black; text-align: right;">
                        @if($row['debit'] > 0)
                            Rp {{ number_format($row['debit'], 0, ',', '.') }}
                        @endif
                    </td>
                    <td style="border: 1px solid black; text-align: right;">
                        @if($row['credit'] > 0)
                            Rp {{ number_format($row['credit'], 0, ',', '.') }}
                        @endif
                    </td>
                    <td style="border: 1px solid black; text-align: right;">
                        Rp {{ number_format($row['balance'], 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="border: 1px solid black; font-weight: bold; text-align: center; background-color: #FFFF00;">TOTAL</td>
                <td style="border: 1px solid black; font-weight: bold; text-align: right; background-color: #FFFF00;">Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
                <td style="border: 1px solid black; font-weight: bold; text-align: right; background-color: #FFFF00;">Rp {{ number_format($totalCredit, 0, ',', '.') }}</td>
                <td style="border: 1px solid black; font-weight: bold; text-align: right; background-color: #FFFF00;">Rp {{ number_format($rows[count($rows)-1]['balance'] ?? 0, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
