<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Data_Mitra_" . date('Y-m-d') . ".xls");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Mitra</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h3>Data Mitra</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No. Mitra</th>
                <th>Nama Perusahaan</th>
                <th>Tipe Mitra</th>
                <th>No. KTP/NPWP</th>
                <th>Kontak Utama</th>
                <th>Jabatan</th>
                <th>Email</th>
                <th>Telepon</th>
                <th>Alamat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mitras as $index => $mitra)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $mitra->nomor_mitra }}</td>
                <td>{{ $mitra->nama }}</td>
                <td>{{ $mitra->tipe_mitra }}</td>
                <td>{{ $mitra->identity_card }}</td>
                <td>{{ $mitra->kontak_nama }}</td>
                <td>{{ $mitra->kontak_jabatan }}</td>
                <td>{{ $mitra->email }}</td>
                <td>{{ $mitra->no_hp }}</td>
                <td>{{ $mitra->alamat }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
