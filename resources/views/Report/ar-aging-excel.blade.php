<table>
    <thead>
        <tr>
            <th colspan="10" style="font-weight: bold; font-size: 14px; text-align: center;">RINGKASAN UMUR PIUTANG</th>
        </tr>
        <tr>
            <th style="font-weight: bold; border: 1px solid #000; text-align: center;">Total Customers</th>
            <th style="font-weight: bold; border: 1px solid #000; text-align: center;">Avg. Day Past Due</th>
            <th style="font-weight: bold; border: 1px solid #000; text-align: center;">Total Invoices</th>
            <th style="font-weight: bold; border: 1px solid #000; text-align: center;">Total Amount</th>
            <th style="font-weight: bold; border: 1px solid #000; text-align: center;">&lt;= 0 Hari</th>
            <th style="font-weight: bold; border: 1px solid #000; text-align: center;">1-15 Hari</th>
            <th style="font-weight: bold; border: 1px solid #000; text-align: center;">16-30 Hari</th>
            <th style="font-weight: bold; border: 1px solid #000; text-align: center;">31-45 Hari</th>
            <th style="font-weight: bold; border: 1px solid #000; text-align: center;">46-60 Hari</th>
            <th style="font-weight: bold; border: 1px solid #000; text-align: center;">61+ Hari</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="border: 1px solid #000; text-align: center;">{{ $summary['total_customers'] }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ number_format($summary['avg_days_past_due'], 2) }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $summary['total_invoices'] }}</td>
            <td style="border: 1px solid #000; text-align: right;">{{ number_format($summary['total_amount'], 0, ',', '.') }}</td>
            <td style="border: 1px solid #000; text-align: right;">{{ number_format($summary['buckets']['current'], 0, ',', '.') }}</td>
            <td style="border: 1px solid #000; text-align: right;">{{ number_format($summary['buckets']['1-15'], 0, ',', '.') }}</td>
            <td style="border: 1px solid #000; text-align: right;">{{ number_format($summary['buckets']['16-30'], 0, ',', '.') }}</td>
            <td style="border: 1px solid #000; text-align: right;">{{ number_format($summary['buckets']['31-45'], 0, ',', '.') }}</td>
            <td style="border: 1px solid #000; text-align: right;">{{ number_format($summary['buckets']['46-60'], 0, ',', '.') }}</td>
            <td style="border: 1px solid #000; text-align: right;">{{ number_format($summary['buckets']['61+'], 0, ',', '.') }}</td>
        </tr>
    </tbody>
</table>

<br>
<br>

<table>
    <thead>
        <tr>
            <th colspan="9" style="font-weight: bold; font-size: 14px; text-align: center;">DETAIL PELANGGAN</th>
        </tr>
        <tr>
            <th style="font-weight: bold; border: 1px solid #000; text-align: center;">No</th>
            <th style="font-weight: bold; border: 1px solid #000; text-align: center;">Kode Pelanggan</th>
            <th style="font-weight: bold; border: 1px solid #000; text-align: center;">Nama Pelanggan</th>
            <th style="font-weight: bold; border: 1px solid #000; text-align: center;">Tagihan Invoice</th>
            <th style="font-weight: bold; border: 1px solid #000; text-align: center;">Sudah Bayar</th>
            <th style="font-weight: bold; border: 1px solid #000; text-align: center;">Outstanding</th>
            <th style="font-weight: bold; border: 1px solid #000; text-align: center;">Sales Code</th>
            <th style="font-weight: bold; border: 1px solid #000; text-align: center;">Sales Name</th>
            <th style="font-weight: bold; border: 1px solid #000; text-align: center;">Umur Nota</th>
        </tr>
    </thead>
    <tbody>
        @foreach($customerDetails as $i => $row)
            <tr>
                <td style="border: 1px solid #000; text-align: center;">{{ $i + 1 }}</td>
                <td style="border: 1px solid #000; text-align: left;">{{ $row['kode'] }}</td>
                <td style="border: 1px solid #000; text-align: left;">{{ $row['nama'] }}</td>
                <td style="border: 1px solid #000; text-align: right;">{{ number_format($row['tagihan'], 0, ',', '.') }}</td>
                <td style="border: 1px solid #000; text-align: right;">{{ number_format($row['dibayar'], 0, ',', '.') }}</td>
                <td style="border: 1px solid #000; text-align: right;">{{ number_format($row['outstanding'], 0, ',', '.') }}</td>
                <td style="border: 1px solid #000; text-align: left;">{{ $row['sales_code'] }}</td>
                <td style="border: 1px solid #000; text-align: left;">{{ $row['sales_name'] }}</td>
                <td style="border: 1px solid #000; text-align: center;">{{ $row['umur_nota'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
