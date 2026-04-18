<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Massal Nota Pembelian</title>
    <style>
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }
        .page-break {
            page-break-before: always;
        }
        .nota-container {
            width: 800px;
            border: 1px solid #000;
            padding: 15px;
            margin: 10px auto;
            background: #fff;
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
        .main-table .text-left { text-align: left; }
        .main-table .text-right { text-align: right; }
        .footer-section {
            display: flex;
            justify-content: space-between;
        }
        .footer-left { width: 60%; }
        .footer-right { width: 35%; }
        .signature-grid {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            text-align: center;
        }
        .signature-box { width: 30%; }
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
        #loading { text-align: center; padding: 100px; font-weight: bold; }

        @media print {
            body { padding: 0; background: none; }
            .nota-container { 
                margin: 0 auto; 
                width: 100%; 
                border: 1px solid #000; 
            }
            @page { size: landscape; margin: 5mm; }
            .page-break { page-break-after: always; }
        }
    </style>
</head>
<body>
    <div id="loading">MENYIAPKAN DOKUMEN...</div>
    <div id="bulk-content"></div>

    <template id="nota-template">
        <div class="nota-container">
            <div class="header">
                <div class="invoice-info">
                    <strong class="tgl_invoice"></strong><br>
                    No. Invoice<br>
                    <strong class="nomor_invoice"></strong>
                </div>
            </div>
            <div class="customer-section">
                <div class="customer-details">
                    <table>
                        <tr><td width="30%">Pembelian Dari</td><td class="mitra_nama">: </td></tr>
                        <tr><td>Alamat</td><td class="mitra_alamat">: </td></tr>
                        <tr><td>Purchasing Staff</td><td class="ref_no">: </td></tr>
                    </table>
                </div>
            </div>
            <table class="main-table">
                <thead>
                    <tr>
                        <th width="5%">No.</th>
                        <th width="10%">Kode</th>
                        <th>Supplier</th>
                        <th>Brand</th>
                        <th width="35%">Nama Barang</th>
                        <th width="10%">Kategori</th>
                        <th width="5%">Kemasan</th>
                        <th>Satuan</th>
                        <th width="15%">Qty</th>
                        <th width="5%">Disc %</th>
                        <th width="15%">Harga Total</th>
                    </tr>
                </thead>
                <tbody class="items-body"></tbody>
            </table>
            <div class="footer-section">
                <div class="footer-left">
                    <div class="note">Note : Barang yang sudah diterima sesuai dengan invoice.</div>
                    <div class="signature-grid">
                        <div class="signature-box">Penerima<br><br><br><br>(................)</div>
                        <div class="signature-box">Gudang<br><br><br><br>(................)</div>
                        <div class="signature-box">Supplier<br><br><br><br>(................)</div>
                    </div>
                </div>
                <div class="footer-right">
                    <table class="total-table">
                        <tr><td>Total</td><td class="text-right total_akhir"></td></tr>
                        <tr><td>DP / Bayar</td><td class="text-right total_bayar"></td></tr>
                        <tr><td>Sisa</td><td class="text-right sisa_tagihan"></td></tr>
                    </table>
                    <div class="bank-info">BCA 1234567890<br>A/N PT. DE JAVA</div>
                </div>
            </div>
        </div>
    </template>

    <script>
        const INVOICE_IDS = @json($ids);
        const API_BASE_URL = '{{ url('api/invoice-api') }}/';

        function formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit', month: 'long', year: 'numeric'
            });
        }

        async function loadInvoices() {
            const bulkContent = document.getElementById('bulk-content');
            const template = document.getElementById('nota-template');

            for (let i = 0; i < INVOICE_IDS.length; i++) {
                try {
                    const response = await fetch(API_BASE_URL + INVOICE_IDS[i]);
                    const result = await response.json();

                    if (result.success) {
                        const data = result.data;
                        const clone = template.content.cloneNode(true);

                        clone.querySelector('.tgl_invoice').innerText = formatDate(data.tgl_invoice);
                        clone.querySelector('.nomor_invoice').innerText = data.nomor_invoice;
                        clone.querySelector('.ref_no').innerText = ': ' + (data.ref_no || '-');
                        clone.querySelector('.mitra_nama').innerText = ': ' + (data.mitra?.nama || 'N/A');
                        clone.querySelector('.mitra_alamat').innerText = ': ' + (data.mitra?.alamat || '-');

                        const tbody = clone.querySelector('.items-body');
                        data.items.forEach((item, index) => {
                            const p = item.product || {};
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>${index + 1}</td>
                                <td>${p.sku_kode || '-'}</td>
                                <td class="text-left">${p.supplier?.nama || '-'}</td>
                                <td class="text-left">${p.brand?.nama_brand || '-'}</td>
                                <td class="text-left">${p.nama_produk || item.nama_produk_manual || '-'}</td>
                                <td>${p.category?.nama_kategori || '-'}</td>
                                <td>${p.kemasan || '-'}</td>
                                <td>${p.satuan || '-'}</td>
                                <td>${formatNumber(item.qty)}</td>
                                <td>${item.diskon_nilai > 0 ? formatNumber(item.diskon_nilai) : '0'}</td>
                                <td class="text-right">${formatNumber(item.total_harga_item)}</td>
                            `;
                            tbody.appendChild(tr);
                        });

                        for (let j = data.items.length; j < 5; j++) {
                            tbody.insertAdjacentHTML('beforeend', `<tr>${'<td>&nbsp;</td>'.repeat(11)}</tr>`);
                        }

                        const totalBayar = data.payment ? data.payment.reduce((sum, p) => sum + parseFloat(p.jumlah_bayar), 0) : 0;
                        clone.querySelector('.total_akhir').innerText = formatNumber(data.total_akhir);
                        clone.querySelector('.total_bayar').innerText = formatNumber(totalBayar);
                        clone.querySelector('.sisa_tagihan').innerText = formatNumber(data.total_akhir - totalBayar);

                        if (i > 0) {
                            const pb = document.createElement('div');
                            pb.className = 'page-break';
                            bulkContent.appendChild(pb);
                        }
                        bulkContent.appendChild(clone);
                    }
                } catch (error) {
                    console.error('Error loading invoice:', INVOICE_IDS[i], error);
                }
            }
            document.getElementById('loading').style.display = 'none';
        }

        document.addEventListener('DOMContentLoaded', loadInvoices);
    </script>
</body>
</html>
