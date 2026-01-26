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
        #loading {
            text-align: center;
            padding: 50px;
            font-size: 16px;
        }
    </style>
</head>
<body>

<div id="loading">Memuat data kuitansi...</div>

<div class="container" id="receipt-content" style="display: none;">
    <div class="header">
        <div>
            <div class="title">Kuitansi Pembayaran</div>
            <div style="margin-top: 5px;">No. Kuitansi: <strong id="nomor_pembayaran"></strong></div>
        </div>
        <div style="text-align: right">
            <div>Tanggal: <strong id="tgl_pembayaran"></strong></div>
            <div style="margin-top: 5px;">Status: <strong>SETTLED</strong></div>
        </div>
    </div>

    <table class="info-table">
        <tr>
            <td class="label" id="label-from-to">Diterima Dari</td>
            <td id="mitra_nama">: </td>
        </tr>
        <tr>
            <td class="label">Metode Pembayaran</td>
            <td id="metode_pembayaran">: </td>
        </tr>
        <tr>
            <td class="label">Catatan</td>
            <td id="catatan">: </td>
        </tr>
        <tr>
            <td class="label">No. Referensi</td>
            <td id="ref_no">: </td>
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
        <tbody id="details-body">
            <!-- Data injected here -->
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        <div class="amount-box" id="amount-box">
            TOTAL: IDR 0.00
        </div>
        <div style="margin-left: 20px; display: inline-block; color: #666;" id="sgd-box">
            (Equivalent to SGD 0.00)
        </div>
    </div>

    <div class="footer">
        <div class="signature-box">
            Penerima / Pembayar<br><br><br>
            <div class="signature-line" id="signature-mitra">( ................................ )</div>
        </div>
        <div class="signature-box">
            Admin Keuangan<br><br><br>
            <div class="signature-line">( ................................ )</div>
        </div>
    </div>
</div>

<script>
    const PAYMENT_ID = '{{ $id }}';
    const API_URL = '{{ url('api/payment-api') }}/' + PAYMENT_ID;
    const SGD_RATE = 13128.53;

    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(num);
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
    }

    document.addEventListener('DOMContentLoaded', async () => {
        try {
            const response = await fetch(API_URL);
            const result = await response.json();
            
            if (result.success) {
                const data = result.data;
                const invoice = data.invoice || {};
                const mitra = invoice.mitra || {};
                const account = data.akun_keuangan || {};

                // Header
                document.getElementById('nomor_pembayaran').innerText = data.nomor_pembayaran;
                document.getElementById('tgl_pembayaran').innerText = formatDate(data.tgl_pembayaran);

                // Info
                const labelFromTo = invoice.tipe_invoice === 'Purchase' ? 'Diterima Dari' : 'Diterima Untuk';
                document.getElementById('label-from-to').innerText = labelFromTo;
                document.getElementById('mitra_nama').innerText = ': ' + (mitra.nama || '-');
                document.getElementById('metode_pembayaran').innerText = `: ${data.metode_pembayaran} (${account.nama_akun || '-'})`;
                document.getElementById('catatan').innerText = ': ' + (data.catatan || '-');
                document.getElementById('ref_no').innerText = ': ' + (data.ref_no || '-');

                // Details
                const tbody = document.getElementById('details-body');
                tbody.innerHTML = `
                    <tr>
                        <td>${invoice.nomor_invoice || '-'}</td>
                        <td>${invoice.tgl_invoice ? formatDate(invoice.tgl_invoice) : '-'}</td>
                        <td style="text-align: right">${formatNumber(invoice.total_akhir || 0)}</td>
                        <td style="text-align: right; font-weight: bold;">${formatNumber(data.jumlah_bayar || 0)}</td>
                    </tr>
                `;

                // Totals
                document.getElementById('amount-box').innerText = `TOTAL: IDR ${formatNumber(data.jumlah_bayar)}`;
                const sgd = (parseFloat(data.jumlah_bayar) / SGD_RATE);
                document.getElementById('sgd-box').innerText = `(Equivalent to SGD ${formatNumber(sgd)})`;

                // Signature
                document.getElementById('signature-mitra').innerText = `( ${mitra.nama || '................'} )`;

                // Show Content
                document.getElementById('loading').style.display = 'none';
                document.getElementById('receipt-content').style.display = 'block';

                // Auto Print
                // setTimeout(() => window.print(), 1000);
            } else {
                document.getElementById('loading').innerText = 'Gagal memuat data kuitansi.';
            }
        } catch (error) {
            console.error(error);
            document.getElementById('loading').innerText = 'Terjadi kesalahan koneksi.';
        }
    });
</script>

</body>
</html>
