<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Note</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
        }

        .header p {
            margin: 5px 0 0;
            font-size: 14px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .info-group strong {
            display: block;
            font-size: 12px;
            color: #666;
            margin-bottom: 2px;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin: 20px 0 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px 12px;
            text-align: left;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-row td {
            font-weight: bold;
            background-color: #f8f9fa;
        }

        .signature-section {
            margin-top: 50px;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            text-align: center;
        }

        .signature-box {
            margin-top: 60px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Delivery Order</h1>
            <p id="printOfficeName">De Java Internal System</p>
        </div>

        <div class="info-grid">
            <div>
                <div class="info-group">
                    <strong>Nomor DO:</strong>
                    <span id="printDoNumber">-</span>
                </div>
                <div class="info-group" style="margin-top: 10px;">
                    <strong>Tanggal Pengiriman:</strong>
                    <span id="printDeliveryDate">-</span>
                </div>
            </div>
            <div class="text-end">
                <div class="info-group">
                    <strong>Status:</strong>
                    <span id="printStatus">-</span>
                </div>
            </div>
        </div>

        <div class="section-title">Informasi Armada & Pengemudi</div>
        <div class="info-grid" id="printFleetInfo">
            <div>
                <div class="info-group">
                    <strong>Armada:</strong>
                    <span id="printFleetName">-</span>
                </div>
                <div class="info-group" style="margin-top: 10px;">
                    <strong>Pengemudi:</strong>
                    <span id="printDriverName">-</span>
                </div>
            </div>
            <div>
                <div class="info-group">
                    <strong>Estimasi Jarak:</strong>
                    <span id="printDistance">-</span>
                </div>
                <div class="info-group" style="margin-top: 10px;">
                    <strong>Estimasi BBM:</strong>
                    Rp <span id="printFuelCost">0</span>
                </div>
            </div>
        </div>
        <div id="printNoFleet" style="display:none;">Belum ada data armada.</div>

        <div class="section-title">Daftar Invoice (Tujuan)</div>
        <table>
            <thead>
                <tr>
                    <th width="50">#</th>
                    <th>No. Invoice</th>
                    <th>Mitra / Tujuan</th>
                    <th>Alamat</th>
                </tr>
            </thead>
            <tbody id="printInvoicesTbody"></tbody>
        </table>

        <div id="printCostsWrap" style="display:none;">
            <div class="section-title">Rincian Biaya Operasional</div>
            <table style="width: 60%;">
                <thead>
                    <tr>
                        <th>Keterangan Biaya</th>
                        <th class="text-end" width="150">Jumlah</th>
                    </tr>
                </thead>
                <tbody id="printCostsTbody"></tbody>
            </table>
        </div>

        <div id="printNotesWrap" style="display:none;">
            <div class="section-title">Catatan</div>
            <p id="printNotes"></p>
        </div>

        <div class="signature-section">
            <div>
                <p>Dibuat Oleh,</p>
                <div class="signature-box">(Admin Gudang)</div>
            </div>
            <div>
                <p>Dikirim Oleh,</p>
                <div class="signature-box">(Pengemudi)</div>
            </div>
            <div>
                <p>Diterima Oleh,</p>
                <div class="signature-box">(Penerima)</div>
            </div>
        </div>
    </div>
    <script>
        const DO_ID = {{ $doId }};
        const DO_API_SHOW = "{{ route('delivery-order-api.show', ['id' => '__ID__']) }}";
        const DO_FLEET_API_BY_DO = "{{ route('delivery-order-fleet-api.by-do', ['doId' => '__ID__']) }}";

        function rupiah(n) {
            return Number(n || 0).toLocaleString('id-ID');
        }
        function rupiah2(n) {
            return Number(n || 0).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
        function escapeHtml(s) {
            return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
        }

        document.addEventListener('DOMContentLoaded', async function () {
            try {
                const doRes = await fetch(DO_API_SHOW.replace('__ID__', DO_ID));
                const doJson = await doRes.json();
                if (!doJson.success) throw new Error(doJson.message || 'Gagal memuat DO');
                const d = doJson.data || {};
                document.title = 'Delivery Note - ' + (d.delivery_order_number || 'DO');
                document.getElementById('printOfficeName').textContent = d.office?.name || 'De Java Internal System';
                document.getElementById('printDoNumber').textContent = d.delivery_order_number || '-';
                document.getElementById('printDeliveryDate').textContent = d.delivery_date ? new Date(d.delivery_date).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) : '-';
                const statusMap = {
                    draft: 'Draft',
                    scheduled: 'Scheduled',
                    in_transit: 'In Transit',
                    delivering: 'Delivering',
                    partially_delivered: 'Partially Delivered',
                    completed: 'Completed',
                    returned: 'Returned',
                    cancelled: 'Cancelled',
                    Scheduled: 'Scheduled'
                };
                const st = d.status || 'draft';
                document.getElementById('printStatus').textContent = statusMap[st] || String(st);

                const fRes = await fetch(DO_FLEET_API_BY_DO.replace('__ID__', DO_ID));
                const fJson = await fRes.json();
                const fleet = fJson.success ? fJson.data : null;
                if (!fleet) {
                    document.getElementById('printFleetInfo').style.display = 'none';
                    document.getElementById('printNoFleet').style.display = 'block';
                } else {
                    const fn = fleet.fleet ? `${fleet.fleet.fleet_name || '-'} (${fleet.fleet.license_plate || fleet.fleet.plate_number || '-'})` : '-';
                    document.getElementById('printFleetName').textContent = fn;
                    document.getElementById('printDriverName').textContent = fleet.driver?.name || '-';
                    document.getElementById('printDistance').textContent = `${rupiah2(fleet.estimated_distance_km || 0)} KM`;
                    document.getElementById('printFuelCost').textContent = rupiah(fleet.estimated_fuel_cost || 0);

                    const costs = Array.isArray(fleet.additional_costs) ? fleet.additional_costs : [];
                    if (costs.length) {
                        const wrap = document.getElementById('printCostsWrap');
                        const tbody = document.getElementById('printCostsTbody');
                        wrap.style.display = 'block';
                        tbody.innerHTML = '';
                        let total = Number(fleet.estimated_fuel_cost || 0);
                        tbody.innerHTML += `<tr><td>Estimasi BBM</td><td class="text-end">Rp ${rupiah(fleet.estimated_fuel_cost || 0)}</td></tr>`;
                        costs.forEach(c => {
                            total += Number(c.amount || 0);
                            tbody.innerHTML += `<tr><td>${escapeHtml(c.name || '-')}</td><td class="text-end">Rp ${rupiah(c.amount || 0)}</td></tr>`;
                        });
                        tbody.innerHTML += `<tr class="total-row"><td>Total Estimasi Biaya</td><td class="text-end">Rp ${rupiah(total)}</td></tr>`;
                    }
                }

                const notes = d.notes || '';
                if (notes) {
                    document.getElementById('printNotesWrap').style.display = 'block';
                    document.getElementById('printNotes').textContent = notes;
                }

                const inv = Array.isArray(d.invoices) ? d.invoices : [];
                const tbody = document.getElementById('printInvoicesTbody');
                tbody.innerHTML = '';
                inv.forEach((x, idx) => {
                    const invNo = x.invoice?.nomor_invoice || '-';
                    const m = x.invoice?.mitra || {};
                    tbody.innerHTML += `
                        <tr>
                            <td class="text-center">${idx + 1}</td>
                            <td>${escapeHtml(invNo)}</td>
                            <td>${escapeHtml(m.nama || 'Umum')}</td>
                            <td>${escapeHtml(m.alamat || '-')}</td>
                        </tr>
                    `;
                });
            } catch (e) {
                console.error(e);
            }
        });
    </script>
</body>

</html>
