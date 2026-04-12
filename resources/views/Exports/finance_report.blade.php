<table>
    <thead>
        <tr>
            <th colspan="7" style="font-weight: bold; font-size: 14px; text-align: center;">LAPORAN KEUANGAN: {{ strtoupper($account->name ?? 'SEMUA AKUN') }}</th>
        </tr>
        <tr>
            <th colspan="7" style="text-align: center;">Periode: {{ $start->format('d/m/Y') }} - {{ $end->format('d/m/Y') }}</th>
        </tr>
        <tr></tr>
        <tr style="background-color: #f1f1f1; font-weight: bold;">
            <th style="border: 1px solid black; text-align: center; width: 5px;">No</th>
            <th style="border: 1px solid black; text-align: center; width: 15px;">Tanggal</th>
            <th style="border: 1px solid black; text-align: center; width: 40px;">Keterangan</th>
            <th style="border: 1px solid black; text-align: center; width: 20px;">Debit</th>
            <th style="border: 1px solid black; text-align: center; width: 20px;">Kredit</th>
            <th style="border: 1px solid black; text-align: center; width: 20px;">Saldo</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="border: 1px solid black; text-align: center;">1</td>
            <td style="border: 1px solid black; text-align: center;">{{ $start->format('d/m/Y') }}</td>
            <td style="border: 1px solid black; font-weight: bold;">SALDO AWAL</td>
            <td style="border: 1px solid black; text-align: right;">-</td>
            <td style="border: 1px solid black; text-align: right;">-</td>
            <td style="border: 1px solid black; text-align: right; font-weight: bold;">Rp {{ number_format($data['opening_balance'], 0, ',', '.') }}</td>
        </tr>
        @php
            $balance = $data['opening_balance'];
            $totalDebit = 0;
            $totalCredit = 0;
            $iteration = 2;
        @endphp
        @foreach($data['rows'] as $row)
            @php
                $balance += $row['debit'];
                $balance -= $row['credit'];
                $totalDebit += $row['debit'];
                $totalCredit += $row['credit'];
            @endphp
            <tr>
                <td style="border: 1px solid black; text-align: center;">{{ $iteration }}</td>
                <td style="border: 1px solid black; text-align: center;">{{ \Carbon\Carbon::parse($row['date'])->format('d/m/Y') }}</td>
                <td style="border: 1px solid black;">
                    {{ strtoupper($row['account_name']) }}<br>
                    <span style="color: #666666; font-size: 10px;">{{ $row['description'] }}</span>
                </td>
                <td style="border: 1px solid black; text-align: right;">{{ $row['debit'] > 0 ? 'Rp ' . number_format($row['debit'], 0, ',', '.') : '-' }}</td>
                <td style="border: 1px solid black; text-align: right;">{{ $row['credit'] > 0 ? 'Rp ' . number_format($row['credit'], 0, ',', '.') : '-' }}</td>
                <td style="border: 1px solid black; text-align: right;">Rp {{ number_format($balance, 0, ',', '.') }}</td>
            </tr>
            @php $iteration++; @endphp
        @endforeach
        <tr style="background-color: #f1f1f1; font-weight: bold;">
            <td colspan="3" style="border: 1px solid black; text-align: center;">TOTAL</td>
            <td style="border: 1px solid black; text-align: right;">Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
            <td style="border: 1px solid black; text-align: right;">Rp {{ number_format($totalCredit, 0, ',', '.') }}</td>
            <td style="border: 1px solid black; text-align: right;">Rp {{ number_format($balance, 0, ',', '.') }}</td>
        </tr>
    </tbody>
</table>
