<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Note - {{ $do->delivery_order_number }}</title>
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
            <p>{{ $do->office->name ?? 'De Java Internal System' }}</p>
        </div>

        <div class="info-grid">
            <div>
                <div class="info-group">
                    <strong>Nomor DO:</strong>
                    {{ $do->delivery_order_number }}
                </div>
                <div class="info-group" style="margin-top: 10px;">
                    <strong>Tanggal Pengiriman:</strong>
                    {{ \Carbon\Carbon::parse($do->delivery_date)->format('d F Y') }}
                </div>
            </div>
            <div class="text-end">
                @php
                    $statusMap = [
                        'draft' => 'Draft',
                        'scheduled' => 'Scheduled',
                        'in_transit' => 'In Transit',
                        'delivering' => 'Delivering',
                        'partially_delivered' => 'Partially Delivered',
                        'completed' => 'Completed',
                        'returned' => 'Returned',
                        'cancelled' => 'Cancelled',
                    ];
                @endphp
                <div class="info-group">
                    <strong>Status:</strong>
                    {{ $statusMap[$do->status] ?? ucfirst($do->status) }}
                </div>
            </div>
        </div>

        <div class="section-title">Informasi Armada & Pengemudi</div>
        @php
            $fleetData = $do->fleets->first();
        @endphp
        @if($fleetData)
            <div class="info-grid">
                <div>
                    <div class="info-group">
                        <strong>Armada:</strong>
                        {{ $fleetData->fleet->fleet_name ?? '-' }} ({{ $fleetData->fleet->license_plate ?? '-' }})
                    </div>
                    <div class="info-group" style="margin-top: 10px;">
                        <strong>Pengemudi:</strong>
                        {{ $fleetData->driver->name ?? '-' }}
                    </div>
                </div>
                <div>
                    <div class="info-group">
                        <strong>Estimasi Jarak:</strong>
                        {{ $fleetData->estimated_distance_km }} KM
                    </div>
                    <div class="info-group" style="margin-top: 10px;">
                        <strong>Estimasi BBM:</strong>
                        Rp {{ number_format($fleetData->estimated_fuel_cost, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        @else
            <p>Belum ada data armada.</p>
        @endif

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
            <tbody>
                @foreach($do->invoices as $idx => $inv)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td>{{ $inv->invoice->nomor_invoice ?? '-' }}</td>
                        <td>{{ $inv->invoice->mitra->nama ?? 'Umum' }}</td>
                        <td>{{ $inv->invoice->mitra->alamat ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($fleetData && !empty($fleetData->additional_costs))
            <div class="section-title">Rincian Biaya Operasional</div>
            <table style="width: 60%;">
                <thead>
                    <tr>
                        <th>Keterangan Biaya</th>
                        <th class="text-end" width="150">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Estimasi BBM</td>
                        <td class="text-end">Rp {{ number_format($fleetData->estimated_fuel_cost, 0, ',', '.') }}</td>
                    </tr>
                    @php $totalCost = $fleetData->estimated_fuel_cost; @endphp
                    @foreach($fleetData->additional_costs as $cost)
                        <tr>
                            <td>{{ $cost['name'] }}</td>
                            <td class="text-end">Rp {{ number_format($cost['amount'], 0, ',', '.') }}</td>
                        </tr>
                        @php $totalCost += $cost['amount']; @endphp
                    @endforeach
                    <tr class="total-row">
                        <td>Total Estimasi Biaya</td>
                        <td class="text-end">Rp {{ number_format($totalCost, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        @endif

        @if($do->notes)
            <div class="section-title">Catatan</div>
            <p>{{ $do->notes }}</p>
        @endif

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
</body>

</html>