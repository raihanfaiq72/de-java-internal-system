<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Penjualan</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #333;
            margin: 20px;
        }

        .nota-container {
            width: 800px;
            border: 1px solid #000;
            padding: 15px;
            margin: auto;
        }

        .header {
            display: flex;
            justify-content: flex-end;
            text-align: right;
            margin-bottom: 10px;
        }

        .invoice-info {
            line-height: 1.2;
        }

        .customer-section {
            display: flex;
            justify-content: flex-start;
            margin-bottom: 15px;
        }

        .customer-details {
            width: 50%;
        }

        .customer-details table {
            width: 100%;
            border-collapse: collapse;
        }

        .customer-details td {
            padding: 2px;
            vertical-align: top;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .main-table th, .main-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }

        .main-table .text-left {
            text-align: left;
        }

        .main-table .text-right {
            text-align: right;
        }

        .footer-section {
            display: flex;
            justify-content: space-between;
        }

        .footer-left {
            width: 60%;
        }

        .footer-right {
            width: 35%;
        }

        .signature-grid {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            text-align: center;
        }

        .signature-box {
            width: 30%;
        }

        .total-table {
            width: 100%;
            border-collapse: collapse;
        }

        .total-table td {
            padding: 5px;
            border: 1px solid #000;
        }

        .note {
            font-style: italic;
            margin-top: 10px;
            font-size: 10px;
        }

        .bank-info {
            margin-top: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="nota-container">
    <div class="header">
        <div class="invoice-info">
            <strong>06 January 2026</strong><br>
            No. Invoice<br>
            <strong>INV - 88590</strong>
        </div>
    </div>

    <div class="customer-section">
        <div class="customer-details">
            <table>
                <tr>
                    <td width="30%">Kepada Yth</td>
                    <td>: BP HARTONO</td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>: UNGARAN</td>
                </tr>
                <tr>
                    <td>Salesman</td>
                    <td>: HN</td>
                </tr>
            </table>
        </div>
    </div>

    <table class="main-table">
        <thead>
            <tr>
                <th width="5%">No.</th>
                <th width="10%">Kode</th>
                <th width="35%">Nama Barang</th>
                <th width="10%">Size</th>
                <th width="5%">QTY</th>
                <th width="15%">Harga Satuan</th>
                <th width="5%">Disc %</th>
                <th width="15%">Harga Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>CN</td>
                <td class="text-left">PRIMA XP 20W50 6X4</td>
                <td>PCS</td>
                <td>6</td>
                <td class="text-right">159,142</td>
                <td></td>
                <td class="text-right">954,852</td>
            </tr>
            <tr>
                <td>2</td>
                <td></td>
                <td class="text-left">UPSOL COOLANT 12X1</td>
                <td>PCS</td>
                <td>12</td>
                <td class="text-right">8,100</td>
                <td></td>
                <td class="text-right">97,200</td>
            </tr>
            <tr><td>3</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
            <tr><td>4</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
        </tbody>
    </table>

    <div class="footer-section">
        <div class="footer-left">
            <div class="note">
                Note : Barang yang sudah dibeli tidak dapat dikembalikan
            </div>
            <div class="signature-grid">
                <div class="signature-box">
                    Penerima<br><br><br><br>
                    (................)
                </div>
                <div class="signature-box">
                    Pengirim<br><br><br><br>
                    (................)
                </div>
                <div class="signature-box">
                    Pengawas<br><br><br><br>
                    (................)
                </div>
            </div>
            <div class="bank-info">
                No. Rekening BCA : 2210330641<br>
                A/N : Wildan Rahmasyah Putra
            </div>
        </div>
        <div class="footer-right">
            <table class="total-table">
                <tr>
                    <td width="40%">CASH</td>
                    <td class="text-right">Rp</td>
                </tr>
                <tr>
                    <td>Titip Jual</td>
                    <td class="text-right">Rp</td>
                </tr>
                <tr>
                    <td><strong>TOTAL</strong></td>
                    <td class="text-right"><strong>1,051,052</strong></td>
                </tr>
                <tr>
                    <td>Administrasi</td>
                    <td class="text-right">Rp</td>
                </tr>
            </table>
        </div>
    </div>
</div>

</body>
</html>