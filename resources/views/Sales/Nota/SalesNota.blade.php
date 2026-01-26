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
        
        #loading {
            text-align: center;
            padding: 50px;
            font-size: 16px;
        }
    </style>
</head>
<body>

<div id="loading">Memuat data invoice...</div>

<div class="nota-container" id="nota-content" style="display: none;">
    <div class="header">
        <div class="invoice-info">
            <strong id="tgl_invoice"></strong><br>
            No. Invoice<br>
            <strong id="nomor_invoice"></strong>
        </div>
    </div>

    <div class="customer-section">
        <div class="customer-details">
            <table>
                <tr>
                    <td width="30%">Kepada Yth</td>
                    <td id="mitra_nama">: </td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td id="mitra_alamat">: </td>
                </tr>
                <tr>
                    <td>Salesman</td>
                    <td id="ref_no">: </td>
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
        <tbody id="items-body">
            <!-- Items injected here -->
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
                    Hormat Kami<br><br><br><br>
                    (................)
                </div>
            </div>
        </div>
        <div class="footer-right">
             <table class="total-table">
                <tr>
                    <td>Total</td>
                    <td class="text-right" id="total_akhir"></td>
                </tr>
                <tr>
                    <td>DP / Bayar</td>
                    <td class="text-right" id="total_bayar"></td>
                </tr>
                <tr>
                    <td>Sisa</td>
                    <td class="text-right" id="sisa_tagihan"></td>
                </tr>
            </table>
            <div class="bank-info">
                BCA 1234567890<br>
                A/N PT. DE JAVA
            </div>
        </div>
    </div>
</div>

<script>
    const INVOICE_ID = '{{ $id }}';
    const API_URL = '{{ url('api/invoice-api') }}/' + INVOICE_ID;

    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(num);
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
    }

    document.addEventListener('DOMContentLoaded', async () => {
        try {
            const response = await fetch(API_URL);
            const result = await response.json();
            
            if (result.success) {
                const data = result.data;
                
                // Header
                document.getElementById('tgl_invoice').innerText = formatDate(data.tgl_invoice);
                document.getElementById('nomor_invoice').innerText = data.nomor_invoice;
                document.getElementById('ref_no').innerText = ': ' + (data.ref_no || '-');
                
                // Mitra
                document.getElementById('mitra_nama').innerText = ': ' + (data.mitra?.nama || 'N/A');
                document.getElementById('mitra_alamat').innerText = ': ' + (data.mitra?.alamat || '-');
                
                // Items
                const tbody = document.getElementById('items-body');
                let total = 0;
                
                data.items.forEach((item, index) => {
                    const lineTotal = item.qty * item.harga_satuan; // Simple calc, real logic might include tax/disc
                    // Note: API response might ideally return line_total, but we calc here if needed or use what's available
                    // Assuming basic line total = qty * price for now, matching blade logic
                    
                    const row = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item.product?.kode_produk || '-'}</td>
                            <td class="text-left">${item.product?.nama_produk || '-'}</td>
                            <td>${item.product?.unit?.nama_unit || '-'}</td>
                            <td>${formatNumber(item.qty)}</td>
                            <td class="text-right">${formatNumber(item.harga_satuan)}</td>
                            <td></td>
                            <td class="text-right">${formatNumber(lineTotal)}</td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });
                
                // Fill empty rows to 5
                for(let i = data.items.length; i < 5; i++) {
                     tbody.innerHTML += `<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>`;
                }

                // Footer Totals
                // The API show() usually includes payment sum or we might need to rely on what's returned
                // InvoiceController::show returns 'payment' relation.
                const totalBayar = data.payment ? data.payment.reduce((sum, p) => sum + parseFloat(p.jumlah_bayar), 0) : 0;
                
                document.getElementById('total_akhir').innerText = formatNumber(data.total_akhir);
                document.getElementById('total_bayar').innerText = formatNumber(totalBayar);
                document.getElementById('sisa_tagihan').innerText = formatNumber(data.total_akhir - totalBayar);

                // Show Content
                document.getElementById('loading').style.display = 'none';
                document.getElementById('nota-content').style.display = 'block';

                // Auto Print
                // setTimeout(() => window.print(), 1000);
            } else {
                document.getElementById('loading').innerText = 'Gagal memuat data invoice.';
            }
        } catch (error) {
            console.error(error);
            document.getElementById('loading').innerText = 'Terjadi kesalahan koneksi.';
        }
    });
</script>

</body>
</html>
