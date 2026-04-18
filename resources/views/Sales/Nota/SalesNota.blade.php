<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Nota Penjualan</title>
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
            font-weight: bold;
        }

        /* Container Utama dengan Border Tebal Konsisten */
        .nota-container {
            width: 190mm;
            /* Reduced from 210mm to avoid cutting */
            margin: 10px auto;
            padding: 10px;
            border: 2px solid #000;
            background: #fff;
        }

        .kop-header {
            text-align: center;
            margin-bottom: 5px;
            border-bottom: 3px double #000;
            padding-bottom: 5px;
        }

        .kop-header img {
            max-width: 100%;
            max-height: 80px;
        }

        .header-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
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

        .main-table th,
        .main-table td {
            border: 2px solid #000;
            /* Semua border disamakan 2px */
            padding: 5px;
            font-weight: bold;
        }

        .main-table tbody td {
            border-left: 2px solid #000;
            border-right: 2px solid #000;
            border-top: none !important;
            border-bottom: none !important;
            padding: 2px 5px !important;
        }

        .main-table tbody tr:last-child td {
            border-bottom: 2px solid #000 !important;
        }

        .main-table th {
            background-color: #f2f2f2 !important;
            text-transform: uppercase;
        }

        .text-left {
            text-align: left !important;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        /* Bagian Bawah (Catatan & Total) */
        .footer-wrapper {
            display: flex;
            width: 100%;
            /* Menghilangkan border-top agar tidak double dengan bottom tabel */
            margin-top: -2px;
        }

        .footer-left {
            width: 68%;
            padding: 5px 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .footer-right {
            width: 32%;
            margin-left: -2px;
        }

        .total-table {
            width: 100%;
            border-collapse: collapse;
        }

        .total-table td {
            padding: 5px;
            border: 2px solid #000;
            font-weight: bold;
            font-size: 14px;
        }

        .total-table td:first-child {
            width: 46.875%;
            /* Aligns with Harga Satuan (15/32) */
        }

        .total-table td:last-child {
            width: 53.125%;
            /* Aligns with Disc + Total (17/32) */
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

        .sig-box {
            width: 30%;
            font-size: 12px;
        }

        .sig-space {
            height: 60px;
        }

        .bank-info {
            margin-top: 5px;
            font-size: 10px;
            font-weight: bold;
            border-top: 2px dashed #000;
            padding-top: 5px;
        }

        #loading {
            text-align: center;
            padding: 100px;
            font-weight: bold;
        }

        @media print {
            body {
                padding: 0;
                background: none;
            }

            .nota-container {
                margin: 0;
                width: 100%;
                border: 2px solid #000;
            }

            .no-print {
                display: none;
            }

            @page {
                size: auto;
                margin: 15mm 15mm 10mm 5mm;
                /* Top Right Bottom Left - Moved Down and Left */
            }
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
                    <tr>
                        <td>Kepada Yth</td>
                        <td id="mitra_nama"></td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td id="mitra_alamat"></td>
                    </tr>
                    <tr>
                        <td>Salesman</td>
                        <td id="ref_no"></td>
                    </tr>
                </table>
            </div>
            <div class="invoice-meta">
                <span id="tgl_invoice" style="font-size: 18px; font-weight: bold;"></span><br>
                <strong>NO. INV:</strong> <strong id="nomor_invoice"></strong><br>
                <strong>PEMBAYARAN: <span id="status_pembayaran">CASH</span></strong>
            </div>
        </div>

        <table class="main-table">
            <thead>
                <tr>
                    <th width="5%">NO</th>
                    <th width="15%">KODE BARANG</th>
                    <th width="30%">NAMA BARANG</th>
                    <th width="10%">SATUAN</th>
                    <th width="8%">QTY</th>
                    <th width="15%">HARGA SATUAN</th>
                    <th width="7%">DISC</th>
                    <th width="10%">TOTAL</th>
                </tr>
            </thead>
            <tbody id="items-body"></tbody>
            <tfoot>
                <tr>
                    <td colspan="5" rowspan="4"
                        style="border: none !important; vertical-align: top; padding-top: 5px !important;">
                        <div style="font-style: italic; font-size: 11px;">
                            <strong>Catatan: Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.</strong>
                        </div>
                        <div class="signature-section">
                            <div class="sig-box"><strong>Penerima</strong>
                                <div class="sig-space"></div><strong>( ............ )</strong>
                            </div>
                            <div class="sig-box"><strong>Pengirim</strong>
                                <div class="sig-space"></div><strong>( ............ )</strong>
                            </div>
                            <div class="sig-box"><strong>Hormat Kami</strong>
                                <div class="sig-space"></div><strong>( ............ )</strong>
                            </div>
                        </div>
                    </td>
                    <td class="text-left" style="border: 2px solid #000 !important; padding: 2px !important;">Biaya
                        Lain
                    </td>
                    <td colspan="2" class="text-right" id="biaya_lain_display"
                        style="border: 2px solid #000 !important; padding: 2px !important;">0</td>
                </tr>
                <tr>
                    <td class="text-left" style="border: 2px solid #000 !important; padding: 2px !important;">TOTAL Rp
                    </td>
                    <td colspan="2" class="text-right" id="total_akhir"
                        style="border: 2px solid #000 !important; padding: 2px !important;"></td>
                </tr>
                <tr>
                    <td class="text-left" style="border: 2px solid #000 !important; padding: 2px !important;">Bayar</td>
                    <td colspan="2" class="text-right" id="total_bayar"
                        style="border: 2px solid #000 !important; padding: 2px !important;"></td>
                </tr>
                <tr>
                    <td class="text-left" style="border: 2px solid #000 !important; padding: 2px !important;">SISA</td>
                    <td colspan="2" class="text-right" id="sisa_tagihan"
                        style="border: 2px solid #000 !important; padding: 2px !important; background-color: #f2f2f2 !important;">
                    </td>
                </tr>
            </tfoot>
        </table>

        <div class="bank-info">
            PEMBAYARAN VIA TRANSFER: BCA 1234567890 - A/N PT. DE JAVA
        </div>
    </div>


    <template id="item-row-template">
        <tr>
            <td class="text-center col-no"></td>
            <td class="text-center col-supplier-code"></td>
            <td class="text-left col-nama"></td>
            <td class="text-center col-satuan"></td>
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
                day: '2-digit',
                month: 'long',
                year: 'numeric'
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
                    document.getElementById('status_pembayaran').innerText = data.status_pembayaran || 'CASH';

                    data.items.forEach((item, index) => {
                        const clone = template.content.cloneNode(true);
                        const p = item.product || {};
                        clone.querySelector('.col-no').textContent = index + 1;
                        clone.querySelector('.col-supplier-code').textContent = p.supplier
                            ?.nomor_mitra || '-';
                        clone.querySelector('.col-nama').textContent = p.nama_produk || item
                            .nama_produk_manual || '-';
                        clone.querySelector('.col-satuan').textContent = p.satuan || '-';
                        clone.querySelector('.col-qty').textContent = formatNumber(item.qty);
                        clone.querySelector('.col-harga').textContent = formatNumber(item
                            .harga_satuan || item.harga_jual || 0);
                        clone.querySelector('.col-disc').textContent = item.diskon_nilai > 0 ?
                            formatNumber(item.diskon_nilai) : '-';
                        clone.querySelector('.col-total').textContent = formatNumber(item
                            .total_harga_item);
                        tbody.appendChild(clone);
                    });

                    // Baris kosong (minimal 5 baris)
                    for (let i = data.items.length; i < 5; i++) {
                        tbody.insertAdjacentHTML('beforeend',
                            '<tr><td style="height:35px;"></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>'
                        );
                    }

                    const totalBayar = data.payment ? data.payment.reduce((sum, p) => sum + parseFloat(p
                        .jumlah_bayar), 0) : 0;
                    document.getElementById('total_akhir').innerText = formatNumber(data.total_akhir);
                    document.getElementById('biaya_lain_display').innerText = formatNumber(data.biaya_kirim ||
                        0);
                    document.getElementById('total_bayar').innerText = formatNumber(totalBayar);
                    document.getElementById('sisa_tagihan').innerText = formatNumber(data.total_akhir -
                        totalBayar);

                    document.getElementById('loading').style.display = 'none';
                    document.getElementById('nota-content').style.display = 'block';

                    // Auto Print
                    const urlParams = new URLSearchParams(window.location.search);
                    if (!urlParams.get('no_print')) {
                        setTimeout(() => {
                            window.print();
                        }, 500);
                    }
                }
            } catch (error) {
                document.getElementById('loading').innerText = 'GAGAL MEMUAT DATA.';
            }
        });
    </script>
</body>

</html>
