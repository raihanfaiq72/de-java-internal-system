<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white border-bottom py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h6 class="mb-0 fw-bold text-dark">Laporan Invoice Per Produk</h6>
            <small class="text-muted me-3">
                Total Nilai: <span class="fw-bold text-primary">Rp {{ number_format($summaryTotalTransaction, 0, ',', '.') }}</span>
            </small>
            <small class="text-muted">
                Invoice Terlibat: <span class="fw-bold text-dark">{{ $summaryTotalInvoices }}</span>
            </small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="dropdown">
                <button class="btn btn-light border btn-sm shadow-sm fw-bold dropdown-toggle text-dark" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                    <i class="iconoir-view-columns-3 me-1"></i> Kolom
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow p-2 colToggleMenu" data-target="#table-invoice-products" style="min-width: 200px; max-height: 450px; overflow-y: auto;">
                    <!-- Column toggles via JS -->
                </ul>
            </div>
            <div class="btn-group shadow-sm">
                <button class="btn btn-white border btn-sm fw-bold text-dark" onclick="printTable('invoice-products')">
                    <i class="fa fa-print me-1"></i> Print
                </button>
                <button class="btn btn-white border btn-sm fw-bold text-dark" onclick="exportReport('invoice_items')">
                    <i class="fa fa-file-excel me-1 text-success"></i> Export
                </button>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="table-invoice-products">
            <thead class="bg-light">
                <tr>
                    <th class="px-4 py-3 text-muted small fw-bold text-uppercase">Status</th>
                    <th class="py-3 text-muted small fw-bold text-uppercase">Tanggal</th>
                    <th class="py-3 text-muted small fw-bold text-uppercase">Produk</th>
                    <th class="py-3 text-muted small fw-bold text-uppercase">Mitra</th>
                    <th class="py-3 text-muted small fw-bold text-uppercase">No. Invoice</th>
                    <th class="text-end py-3 text-muted small fw-bold text-uppercase">Qty</th>
                    <th class="text-end py-3 text-muted small fw-bold text-uppercase">Harga</th>
                    <th class="text-end py-3 text-muted small fw-bold text-uppercase">Disc. Item</th>
                    <th class="text-end py-3 text-muted small fw-bold text-uppercase">Tot. Disc</th>
                    <th class="px-4 py-3 text-muted small fw-bold text-uppercase text-end">Total Akhir</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoiceItems as $item)
                    <tr>
                        <td class="px-4">
                            @php
                                $statusClass = match($item->status_pembayaran) {
                                    'Paid' => 'success',
                                    'Unpaid' => 'danger',
                                    'Partially Paid' => 'warning',
                                    'Overdue' => 'dark',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-soft-{{ $statusClass }} text-{{ $statusClass }} border border-{{ $statusClass }} border-opacity-25 rounded-pill px-2">
                                {{ $item->status_pembayaran }}
                            </span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($item->tgl_invoice)->format('d M Y') }}</td>
                        <td class="fw-bold text-dark">{{ $item->nama_produk }}</td>
                        <td>{{ $item->nama_mitra }}</td>
                        <td class="text-primary fw-bold">{{ $item->nomor_invoice }}</td>
                        <td class="text-end">{{ number_format($item->qty, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                        <td class="text-end text-danger">{{ number_format($item->diskon_nilai, 0, ',', '.') }}</td>
                        <td class="text-end text-danger">Rp {{ number_format($item->total_diskon, 0, ',', '.') }}</td>
                        <td class="px-4 text-end fw-bold">Rp {{ number_format($item->total_akhir, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-5">
                            <div class="mb-3"><i class="iconoir-list fs-1 text-muted opacity-50"></i></div>
                            <h6 class="fw-bold text-dark">Tidak ada data item invoice</h6>
                            <p class="text-muted small mb-0">Sesuaikan filter untuk melihat data.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-light fw-bold">
                <tr>
                    <td colspan="9" class="px-4 text-end text-uppercase text-muted small">Total Transaksi</td>
                    <td class="px-4 text-end text-primary">Rp {{ number_format($summaryTotalTransaction, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
