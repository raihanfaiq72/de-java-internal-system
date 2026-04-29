<div class="card border-0 shadow-sm rounded-3">
    <div
        class="card-header bg-white border-bottom py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h6 class="mb-0 fw-bold text-dark">Daftar Nota</h6>
            <small class="text-muted">
                Total: <span class="fw-bold text-primary">Rp
                    {{ number_format($totalNotaAmount, 0, ',', '.') }}</span>
                ({{ $totalUniqueInvoices }} Nota)
            </small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="dropdown">
                <button class="btn btn-light border btn-sm shadow-sm fw-bold dropdown-toggle text-dark" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                    <i class="iconoir-view-columns-3 me-1"></i> Kolom
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow p-2 colToggleMenu" data-target="#table-payments"
                    style="min-width: 200px; max-height: 450px; overflow-y: auto; z-index: 100000;">
                    <!-- Column toggles via JS -->
                </ul>
            </div>
            <div class="btn-group shadow-sm">
                <button class="btn btn-white border btn-sm fw-bold text-dark" onclick="printTable('payments')">
                    <i class="fa fa-print me-1"></i> Print
                </button>
                <button class="btn btn-white border btn-sm fw-bold text-dark" onclick="exportReport('payments')">
                    <i class="fa fa-file-excel me-1 text-success"></i> Export Excel
                </button>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="table-payments">
            <thead class="bg-light">
                @php
                    $sortBy = request('sort_by', 'invoices.tgl_invoice');
                    $sortDir = request('sort_dir', 'desc');
                @endphp
                <tr>
                    <th class="px-4 py-3 text-muted small fw-bold text-uppercase cursor-pointer sortable" data-sort="status_pembayaran">
                        Status
                        @if($sortBy === 'status_pembayaran')
                            <i class="fa fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} ms-1"></i>
                        @else
                            <i class="fa fa-sort text-muted opacity-50 ms-1"></i>
                        @endif
                    </th>
                    <th class="py-3 text-muted small fw-bold text-uppercase cursor-pointer sortable" data-sort="invoices.tgl_invoice">
                        Tanggal
                        @if($sortBy === 'invoices.tgl_invoice')
                            <i class="fa fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} ms-1"></i>
                        @else
                            <i class="fa fa-sort text-muted opacity-50 ms-1"></i>
                        @endif
                    </th>
                    <th class="py-3 text-muted small fw-bold text-uppercase cursor-pointer sortable" data-sort="invoices.nomor_invoice">
                        No. Nota
                        @if($sortBy === 'invoices.nomor_invoice')
                            <i class="fa fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} ms-1"></i>
                        @else
                            <i class="fa fa-sort text-muted opacity-50 ms-1"></i>
                        @endif
                    </th>
                    <th class="py-3 text-muted small fw-bold text-uppercase">
                        Metode
                    </th>
                    <th class="py-3 text-muted small fw-bold text-uppercase cursor-pointer sortable" data-sort="users.name">
                        Sales
                        @if($sortBy === 'users.name' || $sortBy === 'nama_sales')
                            <i class="fa fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} ms-1"></i>
                        @else
                            <i class="fa fa-sort text-muted opacity-50 ms-1"></i>
                        @endif
                    </th>
                    <th class="py-3 text-muted small fw-bold text-uppercase cursor-pointer sortable" data-sort="mitras.nomor_mitra">
                        No. Mitra
                        @if($sortBy === 'mitras.nomor_mitra')
                            <i class="fa fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} ms-1"></i>
                        @else
                            <i class="fa fa-sort text-muted opacity-50 ms-1"></i>
                        @endif
                    </th>
                    <th class="py-3 text-muted small fw-bold text-uppercase cursor-pointer sortable" data-sort="mitras.nama">
                        Nama Mitra
                        @if($sortBy === 'mitras.nama')
                            <i class="fa fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} ms-1"></i>
                        @else
                            <i class="fa fa-sort text-muted opacity-50 ms-1"></i>
                        @endif
                    </th>
                    <th class="py-3 text-muted small fw-bold text-uppercase">
                        Qty
                    </th>
                    <th class="py-3 text-muted small fw-bold text-uppercase cursor-pointer sortable" data-sort="invoices.total_akhir">
                        Total Nota
                        @if($sortBy === 'invoices.total_akhir')
                            <i class="fa fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} ms-1"></i>
                        @else
                            <i class="fa fa-sort text-muted opacity-50 ms-1"></i>
                        @endif
                    </th>
                    <th class="px-4 py-3 text-muted small fw-bold text-uppercase text-end cursor-pointer sortable" data-sort="payments.jumlah_bayar">
                        Terbayar
                        @if($sortBy === 'payments.jumlah_bayar')
                            <i class="fa fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} ms-1"></i>
                        @else
                            <i class="fa fa-sort text-muted opacity-50 ms-1"></i>
                        @endif
                    </th>
                    <th class="px-4 py-3 text-muted small fw-bold text-uppercase cursor-pointer sortable" data-sort="sisa_piutang">
                        Sisa Piutang
                        @if($sortBy === 'sisa_piutang')
                            <i class="fa fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} ms-1"></i>
                        @else
                            <i class="fa fa-sort text-muted opacity-50 ms-1"></i>
                        @endif
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td class="px-4">
                            @php
                                $statusClass = match ($payment->status_pembayaran) {
                                    'Paid' => 'success',
                                    'Unpaid' => 'danger',
                                    'Partially Paid' => 'warning',
                                    'Overdue' => 'dark',
                                    default => 'secondary',
                                };
                            @endphp
                            <span
                                class="badge bg-soft-{{ $statusClass }} text-{{ $statusClass }} border border-{{ $statusClass }} border-opacity-25 rounded-pill px-2 py-1">
                                {{ $payment->status_pembayaran }}
                            </span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($payment->tgl_pembayaran)->format('d M Y') }}</td>
                        <td class="fw-bold">
                            <a href="{{ $payment->tipe_invoice === 'Sales' ? route('sales.show', $payment->id) : route('purchase.show', $payment->id) }}" class="text-primary text-decoration-none">
                                {{ $payment->nomor_invoice }}
                            </a>
                        </td>
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
                            <span
                                class="badge {{ $badgeClass }} border border-opacity-25 rounded-pill px-2 py-1 fw-semibold">
                                <i class="fa {{ $icon }} me-1"></i>
                                {{ $method }}
                            </span>
                        </td>
                        <td class="small">{{ $payment->nama_sales ?: '-' }}</td>
                        <td>{{ $payment->nomor_mitra ?: '-' }}</td>
                        <td>{{ $payment->nama_mitra }}</td>
                        <td>{{ (int) $payment->total_qty ?? '-' }}</td>
                        <td class="fw-bold">Rp {{ number_format($payment->total_akhir, 0, ',', '.') }}</td>
                        <td class="px-4 text-end fw-bold">Rp {{ number_format($payment->jumlah_bayar, 0, ',', '.') }}
                        </td>
                        <td class="px-4 fw-bold">
                            @php
                                $outstanding = $payment->total_akhir - $payment->jumlah_bayar;
                            @endphp
                            @if ($outstanding > 0)
                                <span class="text-danger">
                                    Rp {{ number_format($outstanding, 0, ',', '.') }}
                                </span>
                            @else
                                <span class="text-success">
                                    Lunas
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center py-5">
                            <div class="mb-3"><i class="iconoir-file-not-found fs-1 text-muted opacity-50"></i></div>
                            <h6 class="fw-bold text-dark">Tidak ada data pembayaran</h6>
                            <p class="text-muted small mb-0">Sesuaikan filter periode atau pencarian.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-light fw-bold">
                <tr>
                    <td colspan="8" class="fw-bold text-end text-uppercase">Total</td>
                    <td class="px-4text-dark fw-bold">Rp {{ number_format($totalNotaAmount, 0, ',', '.') }}</td>
                    <td class="px-4 text-end text-dark fw-bold">Rp
                        {{ number_format($totalPaymentAmount, 0, ',', '.') }}</td>
                    <td class="px-4 text-danger fw-bold">Rp
                        {{ number_format($totalNotaAmount - $totalPaymentAmount, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="card-footer bg-white border-top py-3 px-4">
        {{ $payments->links('pagination::bootstrap-5') }}
    </div>
</div>
