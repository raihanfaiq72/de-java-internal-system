<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Pembelian</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            color: #333;
            margin: 5px;
            font-weight: bold;
        }

        .nota-container {
            width: 190mm;
            /* Adjusted to standard width */
            border: 1px solid #000;
            padding: 5px;
            margin: auto;
        }

        .header {
            display: flex;
            justify-content: flex-end;
            text-align: right;
            margin-bottom: 5px;
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

        .main-table th,
        .main-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
            font-weight: bold;
        }

        .main-table tbody td {
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            border-top: none !important;
            border-bottom: none !important;
            padding: 2px 5px !important;
        }

        .main-table tbody tr:last-child td {
            border-bottom: 1px solid #000 !important;
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
            width: 65%;
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
            padding: 0px 5px !important;
            border: 1px solid #000;
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

        /* Keep outer top and bottom only */
        .total-table tr:first-child td {
            border-top: 1px solid #000 !important;
        }

        .total-table tr:last-child td {
            border-bottom: 1px solid #000 !important;
        }

        .total-table td:first-child {
            width: 45%;
        }

        .total-table td:last-child {
            width: 55%;
        }

        .note {
            font-style: italic;
            margin-top: 5px;
            font-size: 10px;
        }

        .bank-info {
            margin-top: 5px;
            font-size: 10px;
            font-weight: bold;
        }

        @media print {
            @page {
                size: auto;
                margin: 15mm 15mm 10mm 5mm;
                /* Moved Down and Left */
            }
        }
    </style>
</head>

<body>

    <div id="loading">Memuat data invoice...</div>

    <div class="nota-container">
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
                        <td width="30%">Pembelian Dari</td>
                        <td id="mitra_nama">: </td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td id="mitra_alamat">: </td>
                    </tr>
                    <tr>
                        <td>Purchasing Staff</td>
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
                    <th>Barang</th>
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
            <tbody id="items-body">
                <!-- Items injected here -->
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="8" rowspan="6"
                        style="border: none !important; vertical-align: top; padding-top: 5px !important; text-align: left !important;">
                        <div class="note">
                            Note : Barang yang sudah diterima sesuai dengan invoice.
                        </div>
                        <div
                            style="display: flex; justify-content: space-between; margin-top: 10px; text-align: center;">
                            <div class="signature-box"><strong>Penerima</strong><br><br><br>(................)</div>
                            <div class="signature-box"><strong>Gudang</strong><br><br><br>(................)</div>
                            <div class="signature-box"><strong>Supplier</strong><br><br><br>(................)</div>
                        </div>
                        <div
                            style="margin-top: 5px; border: 1px solid #000; padding: 2px; min-height: 20px; text-align: left; font-weight: bold;">
                            TERBILANG: <span id="terbilang_display"
                                style="font-style: italic; text-transform: uppercase;"></span>
                        </div>
                    </td>
                    <td class="text-left">Subtotal</td>
                    <td colspan="2" class="text-right" id="subtotal_display">0</td>
                </tr>
                <tr>
                    <td class="text-left">Diskon</td>
                    <td colspan="2" class="text-right" id="diskon_display">0</td>
                </tr>
                <tr>
                    <td class="text-left">Pajak (PPN)</td>
                    <td colspan="2" class="text-right" id="pajak_display">0</td>
                </tr>
                <tr>
                    <td class="text-left">Biaya Lain</td>
                    <td colspan="2" class="text-right" id="biaya_lain_display">0</td>
                </tr>
                <tr>
                    <td class="text-left">Cashback</td>
                    <td colspan="2" class="text-right" id="cashback_display">0</td>
                </tr>
                <tr style="background-color: #f2f2f2 !important;">
                    <td class="text-left">TOTAL Rp</td>
                    <td colspan="2" class="text-right" id="total_akhir"></td>
                </tr>
            </tfoot>
        </table>
        <div class="bank-info">
            BCA 2220330641
            A/N WILDAN RAHMASYAH PUTRA
        </div>
    </div>

    <template id="item-row-template">
        <tr>
            <td class="col-no"></td>
            <td class="col-kode"></td>
            <td class="col-supplier text-left"></td>
            <td class="col-brand text-left"></td>
            <td class="col-nama text-left"></td>
            <td class="col-kategori"></td>
            <td class="col-kemasan"></td>
            <td class="col-satuan"></td>
            <td class="col-qty"></td>
            <td class="col-disc"></td>
            <td class="col-total text-right"></td>
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
                    const template = document.getElementById('item-row-template');
                    const tbody = document.getElementById('items-body');
                    tbody.innerHTML = '';

                    // Header
                    document.getElementById('tgl_invoice').innerText = formatDate(data.tgl_invoice);
                    document.getElementById('nomor_invoice').innerText = data.nomor_invoice;
                    document.getElementById('ref_no').innerText = ': ' + (data.ref_no || '-');
                    document.getElementById('mitra_nama').innerText = ': ' + (data.mitra?.nama || 'N/A');
                    document.getElementById('mitra_alamat').innerText = ': ' + (data.mitra?.alamat || '-');

                    data.items.forEach((item, index) => {
                        const clone = template.content.cloneNode(true);
                        const p = item.product || {};

                        clone.querySelector('.col-no').textContent = index + 1;
                        clone.querySelector('.col-kode').textContent = p.sku_kode || '-';
                        clone.querySelector('.col-supplier').textContent = p.supplier?.nama || '-';
                        clone.querySelector('.col-brand').textContent = p.brand?.nama_brand || '-';
                        clone.querySelector('.col-nama').textContent = p.nama_produk || item
                            .nama_produk_manual || '-';
                        clone.querySelector('.col-kategori').textContent = p.category?.nama_kategori ||
                            '-';
                        clone.querySelector('.col-kemasan').textContent = p.kemasan || '-';
                        clone.querySelector('.col-satuan').textContent = p.satuan || '-';
                        clone.querySelector('.col-qty').textContent = formatNumber(item.qty);
                        let discText = '0';
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

                    // Fill empty rows to 5
                    const minRows = 5;
                    for (let i = data.items.length; i < minRows; i++) {
                        const emptyRow = `<tr>${'<td>&nbsp;</td>'.repeat(11)}</tr>`;
                        tbody.insertAdjacentHTML('beforeend', emptyRow);
                    }

                    // Footer Totals
                    const totalBayar = data.payment ? data.payment.reduce((sum, p) => sum + parseFloat(p
                        .jumlah_bayar), 0) : 0;

                    const subtotalItems = data.items.reduce((sum, item) => sum + parseFloat(item
                        .total_harga_item), 0);
                    document.getElementById('subtotal_display').innerText = formatNumber(subtotalItems);

                    let discVal = parseFloat(data.diskon_tambahan_nilai || 0);
                    let discText = formatNumber(discVal);
                    if (data.diskon_tambahan_tipe === 'Percentage') {
                        let nominal = (subtotalItems * discVal / 100);
                        discText = `${discVal}% (${formatNumber(nominal)})`;
                    }
                    document.getElementById('diskon_display').innerText = discText;

                    document.getElementById('pajak_display').innerText = formatNumber(data.pajak_ppn || 0);
                    document.getElementById('biaya_lain_display').innerText = formatNumber(data.other_fee ||
                        data.biaya_kirim || 0);
                    document.getElementById('cashback_display').innerText = formatNumber(data.cashback || 0);

                    document.getElementById('total_akhir').innerText = formatNumber(data.total_akhir);
                    document.getElementById('total_bayar').innerText = formatNumber(totalBayar);
                    document.getElementById('sisa_tagihan').innerText = formatNumber(data.total_akhir -
                        totalBayar);

                    document.getElementById('terbilang_display').innerText = terbilang(data.total_akhir) +
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
