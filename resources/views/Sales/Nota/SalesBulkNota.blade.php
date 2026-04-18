<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Massal Nota Penjualan</title>
    <style>
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
            color: #000;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }
        .page-break {
            page-break-before: always;
        }
        .nota-container {
            width: 210mm;
            margin: 10px auto;
            padding: 20px;
            border: 2px solid #000;
            background: #fff;
        }
        .kop-header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
        }
        .kop-header img {
            max-width: 100%;
            max-height: 80px;
        }
        .header-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        .customer-details td {
            padding: 2px 0;
            vertical-align: top;
            font-weight: bold;
        }
        .invoice-meta {
            text-align: right;
            line-height: 1.4;
        }
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }
        .main-table th, .main-table td {
            border: 2px solid #000;
            padding: 10px 5px;
            font-weight: bold;
        }
        .main-table th {
            background-color: #f2f2f2 !important;
            text-transform: uppercase;
        }
        .text-left { text-align: left !important; }
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        
        .footer-wrapper {
            display: flex;
            width: 100%;
            margin-top: -2px; 
        }
        .footer-content {
            width: 60%;
            border: 2px solid #000;
            padding: 10px;
        }
        .footer-right {
            width: 40%;
            margin-left: -2px;
        }
        .total-table {
            width: 100%;
            border-collapse: collapse;
        }
        .total-table td {
            padding: 10px;
            border: 2px solid #000;
            font-weight: bold;
            font-size: 15px;
        }
        .total-table tr:last-child td {
            background-color: #f2f2f2 !important;
        }
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            text-align: center;
        }
        .sig-box { width: 33%; font-size: 12px; }
        .sig-space { height: 60px; }
        .bank-info {
            margin-top: 15px;
            font-size: 12px;
            font-weight: bold;
            border-top: 2px dashed #000;
            padding-top: 10px;
        }
        #loading { text-align: center; padding: 100px; font-weight: bold; }

        @media print {
            body { padding: 0; background: none; }
            .nota-container { 
                margin: 0 auto; 
                width: 100%; 
                border: 2px solid #000; 
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
            <div class="kop-section kop-header" style="display: none;"></div>
            <div class="header-info">
                <div class="customer-details">
                    <table>
                        <tr><td>Kepada Yth</td><td class="mitra_nama"></td></tr>
                        <tr><td>Alamat</td><td class="mitra_alamat"></td></tr>
                        <tr><td>Salesman</td><td class="ref_no"></td></tr>
                    </table>
                </div>
                <div class="invoice-meta">
                    <span class="tgl_invoice" style="font-size: 18px; font-weight: bold;"></span><br>
                    <strong>NO. INV:</strong> <strong class="nomor_invoice"></strong><br>
                    <strong>PEMBAYARAN: <span class="status_pembayaran">CASH</span></strong>
                </div>
            </div>
            <table class="main-table">
                <thead>
                    <tr>
                        <th width="5%">NO</th>
                        <th width="15%">KODE SUPPLIER</th>
                        <th width="30%">NAMA BARANG</th>
                        <th width="10%">SATUAN</th>
                        <th width="8%">QTY</th>
                        <th width="15%">HARGA SATUAN</th>
                        <th width="7%">DISC</th>
                        <th width="10%">TOTAL</th>
                    </tr>
                </thead>
                <tbody class="items-body"></tbody>
            </table>
            <div class="footer-wrapper">
                <div class="footer-content">
                    <div style="font-style: italic; font-size: 12px;"><br>
                        <strong>Catatan: Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.</strong>
                    </div>
                    <div class="signature-section">
                        <div class="sig-box"><strong>Penerima</strong><div class="sig-space"></div><strong>( ............ )</strong></div>
                        <div class="sig-box"><strong>Pengirim</strong><div class="sig-space"></div><strong>( ............ )</strong></div>
                        <div class="sig-box"><strong>Hormat Kami</strong><div class="sig-space"></div><strong>( ............ )</strong></div>
                    </div>
                </div>
                <div class="footer-right">
                    <table class="total-table">
                        <tr>
                            <td width="40%">TOTAL Rp</td>
                            <td class="text-right total_akhir"></td>
                        </tr>
                        <tr>
                            <td style="font-size: 13px;">Bayar</td>
                            <td class="text-right total_bayar" style="font-size: 13px;"></td>
                        </tr>
                        <tr>
                            <td>SISA</td>
                            <td class="text-right sisa_tagihan" style="background-color: #eee;"></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="bank-info">
                PEMBAYARAN VIA TRANSFER: BCA 1234567890 - A/N PT. DE JAVA
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
                        const container = clone.querySelector('.nota-container');

                        if (data.is_kop && data.logo_img) {
                            const kop = clone.querySelector('.kop-section');
                            kop.style.display = 'block';
                            kop.innerHTML = `<img src="{{ asset('') }}${data.logo_img}" alt="Logo">`;
                        }

                        clone.querySelector('.tgl_invoice').innerText = formatDate(data.tgl_invoice);
                        clone.querySelector('.nomor_invoice').innerText = data.nomor_invoice;
                        clone.querySelector('.ref_no').innerText = ': ' + (data.ref_no || '-');
                        clone.querySelector('.mitra_nama').innerText = ': ' + (data.mitra?.nama || '-');
                        clone.querySelector('.mitra_alamat').innerText = ': ' + (data.mitra?.alamat || '-');
                        clone.querySelector('.status_pembayaran').innerText = data.status_pembayaran || 'CASH';

                        const tbody = clone.querySelector('.items-body');
                        data.items.forEach((item, index) => {
                            const p = item.product || {};
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td class="text-center">${index + 1}</td>
                                <td class="text-center">${p.supplier?.nomor_mitra || '-'}</td>
                                <td class="text-left">${p.nama_produk || item.nama_produk_manual || '-'}</td>
                                <td class="text-center">${p.satuan || '-'}</td>
                                <td class="text-center">${formatNumber(item.qty)}</td>
                                <td class="text-right">${formatNumber(item.harga_satuan || item.harga_jual || 0)}</td>
                                <td class="text-center">${item.diskon_nilai > 0 ? formatNumber(item.diskon_nilai) : '-'}</td>
                                <td class="text-right">${formatNumber(item.total_harga_item)}</td>
                            `;
                            tbody.appendChild(tr);
                        });

                        for (let j = data.items.length; j < 5; j++) {
                            tbody.insertAdjacentHTML('beforeend', '<tr><td style="height:35px;"></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>');
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
