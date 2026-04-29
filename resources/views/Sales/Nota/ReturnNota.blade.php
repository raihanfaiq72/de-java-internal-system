<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Nota Return</title>
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

        .nota-container {
            width: 190mm;
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

        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
            font-size: 14px;
        }

        .main-table th,
        .main-table td {
            border: 2px solid #000;
            padding: 0px 3px !important;
            font-weight: bold;
            line-height: 1.0 !important;
            /* Extremely tight */
        }

        .main-table tbody td {
            border-left: 2px solid #000;
            border-right: 2px solid #000;
            border-top: none !important;
            border-bottom: none !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }

        .main-table tbody tr:last-child td {
            border-bottom: 2px solid #000 !important;
        }

        .main-table th {
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

        .total-footer {
            font-size: 18px !important;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            text-align: center;
        }

        .sig-box {
            width: 22%;
            font-size: 12px;
        }

        .sig-space {
            height: 50px;
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
                margin: 12mm 15mm 10mm 5mm;
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
                <strong style="font-size: 12px;">NOTA RETURN</strong><br>
                <span style="font-size: 12px;">NO NOTA RETURN: <strong id="nomor_invoice"></strong></span>
            </div>
        </div>

        <table class="main-table">
            <thead>
                <tr>
                    <th width="5%">NO</th>
                    <th width="18%">KODE</th>
                    <th width="27%">NAMA BARANG</th>
                    <th width="8%">SATUAN</th>
                    <th width="8%">QTY</th>
                    <th width="12%">HARGA</th>
                    <th width="7%">DISC</th>
                    <th width="15%">TOTAL</th>
                </tr>
            </thead>
            <tbody id="items-body"></tbody>
            <tfoot>
                <tr class="total-footer">
                    <td colspan="5" rowspan="3"
                        style="border: none !important; border-top: 2px solid #000 !important; vertical-align: top; padding: 5px !important;">
                        <div style="font-style: italic; font-size: 10px;">
                            <strong>Nota return ini merupakan koreksi atas kesalahan cetak nota penjualan.</strong>
                        </div>
                    </td>
                    <td colspan="2" class="text-left"
                        style="border-top: 2px solid #000 !important; padding: 0 5px !important; line-height: 1.0 !important; height: 20px;">
                        TOTAL Rp</td>
                    <td class="text-right"
                        style="border-top: 2px solid #000 !important; padding: 0 5px !important; line-height: 1.0 !important; height: 20px;">
                        <span id="total_akhir"></span>
                    </td>
                </tr>
            </tfoot>
        </table>

        <div class="signature-section">
            <div class="sig-box">
                <strong>Penerima</strong>
                <div class="sig-space"></div>
                ( . . . . . . . . )
            </div>
            <div class="sig-box">
                <strong>Checker</strong>
                <div class="sig-space"></div>
                ( . . . . . . . . )
            </div>
            <div class="sig-box">
                <strong>Admin</strong>
                <div class="sig-space"></div>
                ( . . . . . . . . )
            </div>
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
                    document.getElementById('ref_no').innerText = ': ' + (data.sales?.name || '-');
                    document.getElementById('mitra_nama').innerText = ': ' + (data.mitra?.nama || '-');
                    document.getElementById('mitra_alamat').innerText = ': ' + (data.mitra?.alamat || '-');

                    data.items.forEach((item, index) => {
                        const clone = template.content.cloneNode(true);
                        const p = item.product || {};
                        clone.querySelector('.col-no').textContent = index + 1;
                        clone.querySelector('.col-supplier-code').textContent = p.supplier
                            ?.nomor_mitra || '-';
                        clone.querySelector('.col-nama').innerHTML =
                            `<strong>${p.nama_produk || item.nama_produk_manual || '-'}</strong>` +
                            (item.deskripsi_produk ?
                                `<br><span style="font-size:11px; font-weight:normal;">${item.deskripsi_produk}</span>` :
                                '');

                        clone.querySelector('.col-satuan').textContent = p.unit?.nama_unit || item
                            .satuan || 'Pcs';
                        clone.querySelector('.col-qty').textContent = formatNumber(item.qty);
                        clone.querySelector('.col-harga').textContent = formatNumber(item
                            .harga_satuan || 0);
                        let discText = '-';
                        if (item.diskon_nilai > 0) {
                            discText = item.diskon_tipe === 'Percentage' ?
                                formatNumber(item.diskon_nilai) + '%' :
                                formatNumber(item.diskon_nilai);
                        }
                        clone.querySelector('.col-disc').textContent = discText;
                        clone.querySelector('.col-total').textContent = formatNumber(item
                            .total_harga_item);
                        tbody.appendChild(clone);
                    });

                    // Fill empty rows (min 15)
                    for (let i = data.items.length; i < 15; i++) {
                        tbody.insertAdjacentHTML('beforeend',
                            `<tr><td class="text-center" style="height:14px; line-height: 1;">${i + 1}</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>`
                        );
                    }

                    document.getElementById('total_akhir').innerText = formatNumber(data.total_akhir);

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
    </script>
</body>

</html>
