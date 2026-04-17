<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota Penjualan - Konsisten & Tebal</title>
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
        /* Container Utama dengan Border Tebal Konsisten */
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
        /* Tabel Utama - Border 2px Konsisten */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }
        .main-table th, .main-table td {
            border: 2px solid #000; /* Semua border disamakan 2px */
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
        
        /* Bagian Bawah (Catatan & Total) */
        .footer-wrapper {
            display: flex;
            width: 100%;
            /* Menghilangkan border-top agar tidak double dengan bottom tabel */
            margin-top: -2px; 
        }
        .footer-left {
            width: 60%;
            border: 2px solid #000;
            padding: 10px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .footer-right {
            width: 40%;
            margin-left: -2px; /* Agar border kiri menempel dengan border kanan footer-left */
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
        /* Tanda Tangan */
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            text-align: center;
        }
        .sig-box { width: 30%; font-size: 12px; }
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
                margin: 0; 
                width: 100%; 
                border: 2px solid #000; 
            }
            .no-print { display: none; }
            @page { size: auto; margin: 10mm; }
        }
    </style>
</head>
<body>

    <div id="loading">MENYIAPKAN DOKUMEN...</div>

    <div class="nota-container" id="nota-content" style="display: none;">
        <div id="kop-section" class="kop-header" style="display: none;"></div>

        <div class="header-info">
            <div class="customer-details">
                <table>
                    <tr><td>Kepada Yth</td><td id="mitra_nama"></td></tr>
                    <tr><td>Alamat</td><td id="mitra_alamat"></td></tr>
                    <tr><td>Salesman</td><td id="ref_no"></td></tr>
                </table>
            </div>
            <div class="invoice-meta">
                <span id="tgl_invoice" style="font-size: 18px; font-weight: bold;"></span><br>
                NO. INV: <strong id="nomor_invoice"></strong><br>
                PEMBAYARAN: <strong>CASH</strong>
            </div>
        </div>

        <table class="main-table">
            <thead>
                <tr>
                    <th width="5%">NO</th>
                    <th width="15%">KODE</th>
                    <th width="35%">NAMA BARANG</th>
                    <th width="8%">QTY</th>
                    <th width="15%">HARGA</th>
                    <th width="7%">DISC</th>
                    <th width="15%">TOTAL</th>
                </tr>
            </thead>
            <tbody id="items-body"></tbody>
        </table>

        <div class="footer-wrapper">
            <div class="footer-left">
                <div style="font-style: italic; font-size: 12px;">
                    Catatan: Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.
                </div>
                <div class="signature-section">
                    <div class="sig-box">Penerima<div class="sig-space"></div>( ............ )</div>
                    <div class="sig-box">Pengirim<div class="sig-space"></div>( ............ )</div>
                    <div class="sig-box">Hormat Kami<div class="sig-space"></div>( ............ )</div>
                </div>
            </div>
            <div class="footer-right">
                <table class="total-table">
                    <tr>
                        <td width="40%">TOTAL Rp</td>
                        <td class="text-right" id="total_akhir"></td>
                    </tr>
                    <tr>
                        <td style="font-size: 13px;">Bayar</td>
                        <td class="text-right" id="total_bayar" style="font-size: 13px;"></td>
                    </tr>
                    <tr>
                        <td>SISA</td>
                        <td class="text-right" id="sisa_tagihan" style="background-color: #eee;"></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="bank-info">
            PEMBAYARAN VIA TRANSFER: BCA 1234567890 - A/N PT. DE JAVA
        </div>
    </div>

    <div class="no-print" style="margin: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 30px; font-weight: bold; cursor: pointer;">CETAK NOTA</button>
    </div>

    <template id="item-row-template">
        <tr>
            <td class="text-center col-no"></td>
            <td class="text-center col-kode"></td>
            <td class="text-left col-nama"></td>
            <td class="text-center col-qty"></td>
            <td class="text-right col-harga"></td>
            <td class="text-center col-disc"></td>
            <td class="text-right col-total"></td>
        </tr>
    </template>

    <script>
        const INVOICE_ID = '{{ $id }}';
        const API_URL = '{{ url('api/invoice-api') }}/' + INVOICE_ID;

        function formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit', month: 'long', year: 'numeric'
            });
        }

        document.addEventListener('DOMContentLoaded', async () => {
            try {
                const response = await fetch(API_URL);
                const result = await response.json();

                if (result.success) {
                    const data = result.data;
                    const tbody = document.getElementById('items-body');
                    const template = document.getElementById('item-row-template');
                    tbody.innerHTML = '';

                    if (data.is_kop && data.logo_img) {
                        const kop = document.getElementById('kop-section');
                        kop.style.display = 'block';
                        kop.innerHTML = `<img src="{{ asset('') }}${data.logo_img}" alt="Logo">`;
                    }

                    document.getElementById('tgl_invoice').innerText = formatDate(data.tgl_invoice);
                    document.getElementById('nomor_invoice').innerText = data.nomor_invoice;
                    document.getElementById('ref_no').innerText = ': ' + (data.ref_no || '-');
                    document.getElementById('mitra_nama').innerText = ': ' + (data.mitra?.nama || '-');
                    document.getElementById('mitra_alamat').innerText = ': ' + (data.mitra?.alamat || '-');

                    data.items.forEach((item, index) => {
                        const clone = template.content.cloneNode(true);
                        const p = item.product || {};
                        clone.querySelector('.col-no').textContent = index + 1;
                        clone.querySelector('.col-kode').textContent = p.sku_kode || '-';
                        clone.querySelector('.col-nama').textContent = p.nama_produk || item.nama_produk_manual || '-';
                        clone.querySelector('.col-qty').textContent = formatNumber(item.qty);
                        clone.querySelector('.col-harga').textContent = formatNumber(item.harga_satuan || 0);
                        clone.querySelector('.col-disc').textContent = item.diskon_nilai > 0 ? formatNumber(item.diskon_nilai) : '-';
                        clone.querySelector('.col-total').textContent = formatNumber(item.total_harga_item);
                        tbody.appendChild(clone);
                    });

                    // Baris kosong (minimal 5 baris)
                    for (let i = data.items.length; i < 5; i++) {
                        tbody.insertAdjacentHTML('beforeend', '<tr><td style="height:35px;"></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>');
                    }

                    const totalBayar = data.payment ? data.payment.reduce((sum, p) => sum + parseFloat(p.jumlah_bayar), 0) : 0;
                    document.getElementById('total_akhir').innerText = formatNumber(data.total_akhir);
                    document.getElementById('total_bayar').innerText = formatNumber(totalBayar);
                    document.getElementById('sisa_tagihan').innerText = formatNumber(data.total_akhir - totalBayar);

                    document.getElementById('loading').style.display = 'none';
                    document.getElementById('nota-content').style.display = 'block';
                }
            } catch (error) {
                document.getElementById('loading').innerText = 'GAGAL MEMUAT DATA.';
            }
        });
    </script>
</body>
</html>