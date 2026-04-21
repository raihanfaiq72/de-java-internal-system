<div class="card border-0 shadow-sm rounded-3">
    <div
        class="card-header bg-white border-bottom py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h6 class="mb-0 fw-bold text-dark">Daftar Pembayaran</h6>
            <small class="text-muted">
                Total: <span class="fw-bold text-primary">Rp
                    {{ number_format($totalPaymentAmount, 0, ',', '.') }}</span>
                ({{ $totalUniqueInvoices }} Invoice)
            </small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="dropdown">
                <button class="btn btn-light border btn-sm shadow-sm fw-bold dropdown-toggle text-dark" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                    <i class="iconoir-view-columns-3 me-1"></i> Kolom
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow p-2 colToggleMenu" data-target="#table-payments"
                    style="min-width: 200px; max-height: 450px; overflow-y: auto;">
                    <!-- Column toggles via JS -->
                </ul>
            </div>
            <div class="btn-group shadow-sm">
                <button class="btn btn-white border btn-sm fw-bold text-dark" onclick="printTable('payments')">
                    <i class="fa fa-print me-1"></i> Print
                </button>
                <button class="btn btn-white border btn-sm fw-bold text-dark" onclick="exportReport('payments')">
                    <i class="fa fa-file-excel me-1 text-success"></i> Export
                </button>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="table-payments">
            <thead class="bg-light">
                <tr>
                    <th class="px-4 py-3 text-muted small fw-bold text-uppercase">Tanggal</th>
                    <th class="py-3 text-muted small fw-bold text-uppercase">No. Invoice</th>
                    <th class="py-3 text-muted small fw-bold text-uppercase">Metode</th>
                    <th class="py-3 text-muted small fw-bold text-uppercase">No. Mitra</th>
                    <th class="py-3 text-muted small fw-bold text-uppercase">Nama Mitra</th>
                    <th class="py-3 text-muted small fw-bold text-uppercase">Qty</th>
                    <th class="py-3 text-muted small fw-bold text-uppercase">Total Invoice</th>
                    <th class="px-4 py-3 text-muted small fw-bold text-uppercase text-end">Terbayar</th>
                    <th class="px-4 py-3 text-muted small fw-bold text-uppercase text-end">Sisa Piutang</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td class="px-4">{{ \Carbon\Carbon::parse($payment->tgl_pembayaran)->format('d M Y') }}</td>
                        <td class="fw-bold text-primary">{{ $payment->nomor_invoice }}</td>
                        <td>
                            @php
                                $method = $payment->metode_pembayaran;
                                $badgeClass = 'bg-soft-secondary text-secondary';
                                $icon = 'fa-clock';
                                
                                if ($method === 'Tunai') {
                                    $badgeClass = 'bg-soft-success text-success';
                                    $icon = 'fa-money-bill-wave';
                                } elseif ($method === 'Transfer') {
                                    $badgeClass = 'bg-soft-primary text-primary';
                                    $icon = 'fa-exchange-alt';
                                } elseif ($method === 'Kartu Kredit') {
                                    $badgeClass = 'bg-soft-info text-info';
                                    $icon = 'fa-credit-card';
                                } elseif ($method === 'Belum Bayar') {
                                    $badgeClass = 'bg-danger-subtle text-danger'; // Match invoice page styling
                                    $icon = 'fa-exclamation-triangle';
                                }
                            @endphp
                            <span class="badge {{ $badgeClass }} border border-opacity-25 rounded-pill px-3 py-2 fw-semibold">
                                <i class="fa {{ $icon }} me-1"></i>
                                {{ $method }}
                            </span>
                        </td>
                        <td>{{ $payment->nomor_mitra }}</td>
                        <td>{{ $payment->nama_mitra }}</td>
                        <td>{{ (int) $payment->total_qty ?? '-' }}</td>
                        <td class="fw-bold">Rp {{ number_format($payment->total_akhir, 0, ',', '.') }}</td>
                        <td class="px-4 text-end fw-bold">Rp {{ number_format($payment->jumlah_bayar, 0, ',', '.') }}</td>
                        <td class="px-4 text-end fw-bold">
                            @php
                                $outstanding = $payment->total_akhir - $payment->jumlah_bayar;
                            @endphp
                            @if($outstanding > 0)
                                <span class="badge bg-soft-danger text-danger rounded-pill px-3 py-2 fw-semibold">
                                    <i class="fa fa-exclamation-circle me-1"></i>
                                    Rp {{ number_format($outstanding, 0, ',', '.') }}
                                </span>
                            @else
                                <span class="badge bg-soft-success text-success rounded-pill px-3 py-2 fw-semibold">
                                    <i class="fa fa-check-circle me-1"></i>
                                    Lunas
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div class="mb-3"><i class="iconoir-file-not-found fs-1 text-muted opacity-50"></i></div>
                            <h6 class="fw-bold text-dark">Tidak ada data pembayaran</h6>
                            <p class="text-muted small mb-0">Sesuaikan filter periode atau pencarian.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-light fw-bold">
                <tr>
                    <td colspan="8" class="px-4 text-end text-uppercase text-muted small">Total Pembayaran</td>
                    <td class="px-4 text-end text-dark">Rp {{ number_format($totalPaymentAmount, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>