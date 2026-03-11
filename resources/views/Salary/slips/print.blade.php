<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji - {{ $salarySlip->employee->name }}</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
        }

        .slip-title {
            font-size: 18px;
            margin-top: 10px;
            text-transform: uppercase;
        }

        .details-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .details-table td {
            padding: 5px;
        }

        .salary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .salary-table th,
        .salary-table td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .salary-table th {
            background-color: #f2f2f2;
            text-align: left;
        }

        .total-row {
            font-weight: bold;
            background-color: #eee;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 180px;
            text-align: center;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            margin-top: 60px;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="company-name">DE JAVA INTERNAL SYSTEM</div>
        <div class="slip-title">SLIP GAJI KARYAWAN</div>
        <div>Periode: {{ $salarySlip->salaryPeriod->name }}</div>
    </div>

    <table class="details-table">
        <tr>
            <td width="150"><strong>Nama Karyawan</strong></td>
            <td>: {{ $salarySlip->employee->name }}</td>
            <td width="150"><strong>NIK</strong></td>
            <td>: {{ $salarySlip->employee->nik }}</td>
        </tr>
        <tr>
            <td><strong>Jabatan</strong></td>
            <td>: {{ $salarySlip->employee->position }}</td>
            <td><strong>Status</strong></td>
            <td>: {{ ucfirst($salarySlip->employee->status) }}</td>
        </tr>
    </table>

    <table class="salary-table">
        <thead>
            <tr>
                <th>Deskripsi</th>
                <th style="text-align: right;">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Gaji Pokok ({{ $salarySlip->details['work_days'] ?? 0 }} hari kerja)</td>
                <td style="text-align: right;">{{ number_format($salarySlip->basic_salary, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Premi / Bonus</td>
                <td style="text-align: right;">{{ number_format($salarySlip->premi, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Uang Makan</td>
                <td style="text-align: right;">{{ number_format($salarySlip->meal_allowance, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="text-danger">Potongan Keterlambatan</td>
                <td style="text-align: right; color: red;">-
                    {{ number_format($salarySlip->late_deduction, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="text-danger">Potongan Lainnya</td>
                <td style="text-align: right; color: red;">-
                    {{ number_format($salarySlip->other_deduction, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td>TOTAL DITERIMA</td>
                <td style="text-align: right;">{{ number_format($salarySlip->total_salary, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <div class="signature-box">
            <p>Penerima,</p>
            <div class="signature-line"></div>
            <p>{{ $salarySlip->employee->name }}</p>
        </div>
        <div class="signature-box">
            <p>Mengetahui,</p>
            <div class="signature-line"></div>
            <p>a.n. Wildan</p>
        </div>
        <div class="signature-box">
            <p>Accounting,</p>
            <div class="signature-line"></div>
            <p>Accounting</p>
        </div>
    </div>

</body>

</html>
