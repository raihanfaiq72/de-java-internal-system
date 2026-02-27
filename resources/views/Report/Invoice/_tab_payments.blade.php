<div class="card border-0 shadow-sm rounded-3 overflow-hidden">
    <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
        <div>
            <h6 class="mb-0 fw-bold text-dark">Daftar Pembayaran</h6>
            <small class="text-muted">
                Total: <span class="fw-bold text-primary">Rp {{ number_format($totalPaymentAmount, 0, ',', '.') }}</span> 
                ({{ $totalUniqueInvoices }} Invoice)
            </small>
        </div>
        <div>
            <button class="btn btn-white border btn-sm shadow-sm fw-bold text-dark" onclick="window.print()">
                <i class="fa fa-print me-1"></i> Print
            </button>
            <button class="btn btn-white border btn-sm shadow-sm fw-bold text-dark ms-2" onclick="exportReport('payments')">
                <i class="fa fa-file-excel me-1 text-success"></i> Export
            </button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="px-4 py-3 text-muted small fw-bold text-uppercase">Tanggal</th>
                    <th class="py-3 text-muted small fw-bold text-uppercase">No. Invoice</th>
                    <th class="py-3 text-muted small fw-bold text-uppercase">Metode</th>
                    <th class="py-3 text-muted small fw-bold text-uppercase">No. Mitra</th>
                    <th class="py-3 text-muted small fw-bold text-uppercase">Nama Mitra</th>
                    <th class="px-4 py-3 text-muted small fw-bold text-uppercase text-end">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td class="px-4">{{ \Carbon\Carbon::parse($payment->tgl_pembayaran)->format('d M Y') }}</td>
                        <td class="fw-bold text-primary">{{ $payment->nomor_invoice }}</td>
                        <td>
                            <span class="badge bg-soft-info text-info border border-info border-opacity-25 rounded-pill px-2">
                                {{ $payment->metode_pembayaran }}
                            </span>
                        </td>
                        <td>{{ $payment->nomor_mitra }}</td>
                        <td>{{ $payment->nama_mitra }}</td>
                        <td class="px-4 text-end fw-bold">Rp {{ number_format($payment->jumlah_bayar, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="mb-3"><i class="iconoir-file-not-found fs-1 text-muted opacity-50"></i></div>
                            <h6 class="fw-bold text-dark">Tidak ada data pembayaran</h6>
                            <p class="text-muted small mb-0">Sesuaikan filter periode atau pencarian.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-light fw-bold">
                <tr>
                    <td colspan="5" class="px-4 text-end text-uppercase text-muted small">Total Pembayaran</td>
                    <td class="px-4 text-end text-dark">Rp {{ number_format($totalPaymentAmount, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
