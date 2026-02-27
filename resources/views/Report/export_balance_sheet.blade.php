<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Neraca_" . date('Y-m-d') . ".xls");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Neraca</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .indent-1 { padding-left: 20px; }
        .indent-2 { padding-left: 40px; }
        .bg-gray { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h3>Laporan Neraca</h3>
    <p>Per Tanggal: {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</p>

    <!-- AKTIVA -->
    <h4>AKTIVA</h4>
    <table>
        <thead>
            <tr>
                <th>Kode Akun</th>
                <th>Nama Akun</th>
                <th>Saldo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($aktivaGroups as $group)
                <tr class="bg-gray">
                    <td colspan="3" class="text-bold">{{ $group->kode_kelompok }} - {{ $group->nama_kelompok }}</td>
                </tr>
                @foreach($group->type as $type)
                    <tr>
                        <td colspan="3" class="text-bold indent-1">{{ $type->nama_tipe }}</td>
                    </tr>
                    @foreach($type->coas as $coa)
                    <tr>
                        <td class="indent-2">{{ $coa->kode_akun }}</td>
                        <td>{{ $coa->nama_akun }}</td>
                        <td class="text-right">{{ number_format($coa->balance, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="2" class="text-bold indent-1">Total {{ $type->nama_tipe }}</td>
                        <td class="text-right text-bold">{{ number_format($type->total_balance, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="2" class="text-bold">Total {{ $group->nama_kelompok }}</td>
                    <td class="text-right text-bold">{{ number_format($group->total_balance, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="bg-gray">
                <td colspan="2" class="text-bold">TOTAL AKTIVA</td>
                <td class="text-right text-bold">{{ number_format($totalAktiva, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <br>

    <!-- KEWAJIBAN & MODAL -->
    <h4>KEWAJIBAN & MODAL</h4>
    <table>
        <thead>
            <tr>
                <th>Kode Akun</th>
                <th>Nama Akun</th>
                <th>Saldo</th>
            </tr>
        </thead>
        <tbody>
            <!-- KEWAJIBAN -->
            @foreach($kewajibanGroups as $group)
                <tr class="bg-gray">
                    <td colspan="3" class="text-bold">{{ $group->kode_kelompok }} - {{ $group->nama_kelompok }}</td>
                </tr>
                @foreach($group->type as $type)
                    <tr>
                        <td colspan="3" class="text-bold indent-1">{{ $type->nama_tipe }}</td>
                    </tr>
                    @foreach($type->coas as $coa)
                    <tr>
                        <td class="indent-2">{{ $coa->kode_akun }}</td>
                        <td>{{ $coa->nama_akun }}</td>
                        <td class="text-right">{{ number_format($coa->balance, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="2" class="text-bold indent-1">Total {{ $type->nama_tipe }}</td>
                        <td class="text-right text-bold">{{ number_format($type->total_balance, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="2" class="text-bold">Total {{ $group->nama_kelompok }}</td>
                    <td class="text-right text-bold">{{ number_format($group->total_balance, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="bg-gray">
                <td colspan="2" class="text-bold">TOTAL KEWAJIBAN</td>
                <td class="text-right text-bold">{{ number_format($totalKewajiban, 0, ',', '.') }}</td>
            </tr>

            <!-- MODAL -->
            @foreach($modalGroups as $group)
                <tr class="bg-gray">
                    <td colspan="3" class="text-bold">{{ $group->kode_kelompok }} - {{ $group->nama_kelompok }}</td>
                </tr>
                @foreach($group->type as $type)
                    <tr>
                        <td colspan="3" class="text-bold indent-1">{{ $type->nama_tipe }}</td>
                    </tr>
                    @foreach($type->coas as $coa)
                    <tr>
                        <td class="indent-2">{{ $coa->kode_akun }}</td>
                        <td>{{ $coa->nama_akun }}</td>
                        <td class="text-right">{{ number_format($coa->balance, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="2" class="text-bold indent-1">Total {{ $type->nama_tipe }}</td>
                        <td class="text-right text-bold">{{ number_format($type->total_balance, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="2" class="text-bold">Total {{ $group->nama_kelompok }}</td>
                    <td class="text-right text-bold">{{ number_format($group->total_balance, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            
            <tr>
                <td colspan="2" class="text-bold">Laba Tahun Berjalan</td>
                <td class="text-right text-bold">{{ number_format($currentYearProfit, 0, ',', '.') }}</td>
            </tr>

            <tr class="bg-gray">
                <td colspan="2" class="text-bold">TOTAL MODAL</td>
                <td class="text-right text-bold">{{ number_format($totalModal, 0, ',', '.') }}</td>
            </tr>

            <tr class="bg-gray">
                <td colspan="2" class="text-bold">TOTAL KEWAJIBAN & MODAL</td>
                <td class="text-right text-bold">{{ number_format($totalPasiva, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
