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

        table {
            border-collapse: collapse !important;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
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
            padding: 1px;
            font-weight: bold;
        }

        .main-table tbody td {
            border-left: 2px solid #000;
            border-right: 2px solid #000;
            border-top: none !important;
            border-bottom: none !important;
            padding: 0px !important;
        }

        .main-table tbody tr:last-child td {
            border-bottom: 2px solid #000 !important;
        }

        .main-table th {
            /* background-color: #f2f2f2 !important; */
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
            padding: 0px 5px !important;
            /* Minimized padding */
            border: 2px solid #000;
            font-weight: bold;
            font-size: 11px;
        }

        /* Remove all internal horizontal borders */
        .total-table tr td {
            border-top: none !important;
            border-bottom: none !important;
        }

        /* Remove middle vertical border */
        .total-table td:first-child {
            border-right: none !important;
        }

        .total-table td:last-child {
            border-left: none !important;
        }

        /* Restore outer top and bottom only */
        .total-table tr:first-child td {
            border-top: 2px solid #000 !important;
        }

        .total-table tr:last-child td {
            border-bottom: 2px solid #000 !important;
        }

        .total-table td:first-child {
            width: 45%;
        }

        .total-table td:last-child {
            width: 55%;
        }

        .total-table tr:last-child td {
            /* background-color: #f2f2f2 !important; */
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
            font-size: 14px;
        }

        #loading {
            text-align: center;
            padding: 100px;
            font-weight: bold;
        }

        .total-footer {
            font-size: 16px !important;
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
                margin: 12mm 15mm 10mm 5mm;
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
                    <th width="12%">KODE</th>
                    <th width="30%">NAMA BARANG</th>
                    <th width="8%">SATUAN</th>
                    <th width="8%">QTY</th>
                    <th width="15%">HARGA SATUAN</th>
                    <th width="7%">DISC</th>
                    <th width="15%">TOTAL</th>
                </tr>
            </thead>
            <tbody id="items-body"></tbody>
            <tfoot>
                <tr>
                    <td colspan="5" rowspan="6"
                        style="border: none !important; vertical-align: top; padding-top: 5px !important;">
                        <div style="font-style: italic; font-size: 10px;">
                            <strong>Catatan: Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-top: 5px;">
                            <div class="sig-box text-center"><strong>Admin</strong><br><br><br>( . . . . .)</div>
                            <div class="sig-box text-center"><strong>Checker</strong><br><br><br>( . . . . . .)</div>
                            <div class="sig-box text-center"><strong>Penerima</strong><br><br><br>( . . . . .)</div>
                        </div>
                    </td>
                </tr>
                <!--
                <tr>
                    <td class="text-left">Diskon</td>
                    <td colspan="2" class="text-right" id="diskon_display">0</td>
                </tr>
                <tr>
                    <td class="text-left">Biaya Lain</td>
                    <td colspan="2" class="text-right" id="biaya_lain_display">0</td>
                </tr>
                -->
                <tr>
                    <td class="text-left">Cashback</td>
                    <td colspan="2" class="text-right" id="cashback_display">0</td>
                </tr>
                <tr class="total-footer">
                    <td class="text-left">TOTAL Rp</td>
                    <td colspan="2" class="text-right" id="total_akhir"></td>
                </tr>
            </tfoot>
        </table>

        <div class="bank-info">
            PEMBAYARAN VIA TRANSFER: BCA 2220330641 - A/N WILDAN RAHMASYAH PUTRA
        </div>
    </div>


    <template id="item-row-template">
        <tr>
            <td class="text-center col-no"></td>
            <td class="text-center col-supplier-code" style="font-size: 10px;"></td>
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
                    document.getElementById('ref_no').innerText = ': ' + (data.sales?.name || '-');
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
                        let discText = '-';
                        if (item.diskon_nilai > 0) {
                            if (item.diskon_tipe === 'Percentage') {
                                discText = formatNumber(item.diskon_nilai) + '%';
                            } else {
                                discText = formatNumber(item.diskon_nilai);
                            }
                        }
                        clone.querySelector('.col-disc').textContent = discText;
                        clone.querySelector('.col-total').textContent = formatNumber(item
                            .total_harga_item);
                        tbody.appendChild(clone);
                    });

                    // Baris kosong (minimal 13 baris)
                    for (let i = data.items.length; i < 15; i++) {
                        tbody.insertAdjacentHTML('beforeend',
                            `<tr><td class="text-center" style="height:15px;">${i + 1}</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>`
                        );
                    }

                    const totalBayar = data.payment ? data.payment.reduce((sum, p) => sum + parseFloat(p
                        .jumlah_bayar), 0) : 0;

                    const subtotalItems = data.items.reduce((sum, item) => sum + parseFloat(item
                        .total_harga_item), 0);
                    // document.getElementById('subtotal_display').innerText = formatNumber(subtotalItems);

                    let discVal = parseFloat(data.diskon_tambahan_nilai || 0);
                    let discText = formatNumber(discVal);
                    if (data.diskon_tambahan_tipe === 'Percentage') {
                        let nominal = (subtotalItems * discVal / 100);
                        discText = `${discVal}% (${formatNumber(nominal)})`;
                    }
                    // document.getElementById('diskon_display').innerText = discText;

                    // document.getElementById('biaya_lain_display').innerText = formatNumber(data.other_fee ||
                    //     data.biaya_kirim || 0);
                    document.getElementById('cashback_display').innerText = formatNumber(data.cashback || 0);

                    document.getElementById('total_akhir').innerText = formatNumber(data.total_akhir);
                    // document.getElementById('total_bayar').innerText = formatNumber(totalBayar);
                    // document.getElementById('sisa_tagihan').innerText = formatNumber(data.total_akhir -
                    //     totalBayar);

                    " RUPIAH";

                    document.getElementById('loading').style.display = 'none';
                    document.getElementById('nota-content').style.display = 'block';

                    const urlParams = new URLSearchParams(window.location.search);
                    if (!urlParams.get('no_print')) {
                        setTimeout(() => {
                            window.print();
                        }, 500);
                    }
                }
            } catch (error) {
                console.error(error);
                document.getElementById('loading').innerText = 'TERJADI KESALAHAN.';
            }
        });

        function terbilang(n) {
            if (n < 0) return "MINUS " + terbilang(-n);
            if (n === 0) return "";
            const satuan = ["", "SATU", "DUA", "TIGA", "EMPAT", "LIMA", "ENAM", "TUJUH", "DELAPAN", "SEMBILAN", "SEPULUH",
                "SEBELAS"
            ];
            let res = "";
            if (n < 12) res = satuan[n];
            else if (n < 20) res = terbilang(n - 10) + " BELAS";
            else if (n < 100) res = terbilang(Math.floor(n / 10)) + " PULUH " + terbilang(n % 10);
            else if (n < 200) res = "SERATUS " + terbilang(n - 100);
            else if (n < 1000) res = terbilang(Math.floor(n / 100)) + " RATUS " + terbilang(n % 100);
            else if (n < 2000) res = "SERIBU " + terbilang(n - 1000);
            else if (n < 1000000) res = terbilang(Math.floor(n / 1000)) + " RIBU " + terbilang(n % 1000);
            else if (n < 1000000000) res = terbilang(Math.floor(n / 1000000)) + " JUTA " + terbilang(n % 1000000);
            else if (n < 1000000000000) res = terbilang(Math.floor(n / 1000000000)) + " MILIAR " + terbilang(n %
                1000000000);
            return res.trim();
        }
    </script>
</body>

</html>
