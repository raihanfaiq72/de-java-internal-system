<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detail Tagihan Supplier - {{ $supplier->nama }}</title>
</head>
<body>
    <table border="1" cellpadding="5" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th colspan="8" style="background-color: #f0f0f0; text-align: center; font-size: 16px; font-weight: bold;">
                    DETAIL TAGIHAN SUPPLIER
                </th>
            </tr>
            <tr>
                <th colspan="8" style="background-color: #f8f8f8; text-align: center; font-size: 14px;">
                    {{ $supplier->nama }} 
                    @if($supplier->kota) - {{ $supplier->kota }} @endif
                    @if($supplier->no_hp) - {{ $supplier->no_hp }} @endif
                </th>
            </tr>
            <tr style="background-color: #e0e0e0; font-weight: bold;">
                <th>NO. INVOICE</th>
                <th>TANGGAL</th>
                <th>JATUH TEMPO</th>
                <th>TOTAL TAGIHAN</th>
                <th>SUDAH DIBAYAR</th>
                <th>SISA TAGIHAN</th>
                <th>STATUS</th>
                <th>KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoiceData as $invoice)
                <tr>
                    <td>{{ $invoice['nomor_invoice'] }}</td>
                    <td>{{ \Carbon\Carbon::parse($invoice['tgl_invoice'])->format('d/m/Y') }}</td>
                    <td>{{ $invoice['tgl_jatuh_tempo'] ? \Carbon\Carbon::parse($invoice['tgl_jatuh_tempo'])->format('d/m/Y') : '-' }}</td>
                    <td style="text-align: right;">{{ number_format($invoice['total_akhir'], 0, ',', '.') }}</td>
                    <td style="text-align: right;">{{ number_format($invoice['total_paid'], 0, ',', '.') }}</td>
                    <td style="text-align: right; font-weight: bold;">{{ number_format($invoice['remaining'], 0, ',', '.') }}</td>
                    <td style="text-align: center;">
                        @if($invoice['days_overdue'] > 0)
                            TERLAMBAT {{ $invoice['days_overdue'] }} HARI
                        @elseif($invoice['days_overdue'] <= 7 && $invoice['days_overdue'] >= 0)
                            MINGGU INI
                        @else
                            AMAN
                        @endif
                    </td>
                    <td>{{ $invoice['keterangan'] ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px;">
                        Tidak ada data tagihan untuk supplier ini dalam periode yang dipilih
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($invoiceData->count() > 0)
            <tfoot>
                <tr style="background-color: #f0f0f0; font-weight: bold;">
                    <td colspan="3" style="text-align: right;">TOTAL:</td>
                    <td style="text-align: right;">{{ number_format($totalInvoice, 0, ',', '.') }}</td>
                    <td style="text-align: right;">{{ number_format($totalPayment, 0, ',', '.') }}</td>
                    <td style="text-align: right;">{{ number_format($totalRemaining, 0, ',', '.') }}</td>
                    <td colspan="2"></td>
                </tr>
                <tr style="background-color: #e8e8e8; font-weight: bold;">
                    <td colspan="2">REKAPITULASI:</td>
                    <td>Jumlah Invoice: {{ $invoiceData->count() }}</td>
                    <td style="text-align: right;">Total Tagihan: {{ number_format($totalInvoice, 0, ',', '.') }}</td>
                    <td style="text-align: right;">Sudah Dibayar: {{ number_format($totalPayment, 0, ',', '.') }}</td>
                    <td style="text-align: right;">Sisa Tagihan: {{ number_format($totalRemaining, 0, ',', '.') }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        @endif
    </table>
    
    <table border="0" cellpadding="5" cellspacing="0" width="100%" style="margin-top: 20px;">
        <tr>
            <td style="font-size: 12px; color: #666;">
                <strong>Dicetak pada:</strong> {{ date('d/m/Y H:i:s') }}<br>
                <strong>Periode:</strong> 
                @if(request('start_date') && request('end_date'))
                    {{ \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') }} - {{ \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') }}
                @else
                Semua Data
                @endif
            </td>
        </tr>
    </table>
</body>
</html>
