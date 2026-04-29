<div class="card border-0 shadow-sm rounded-3">
    <div
        class="card-header bg-white border-bottom py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h6 class="mb-0 fw-bold text-dark">Laporan Nota Per Produk</h6>
            <small class="text-muted me-3">
                Total Nilai: <span class="fw-bold text-primary">Rp
                    {{ number_format($summaryTotalTransaction, 0, ',', '.') }}</span>
            </small>
            <small class="text-muted">
                Nota Terlibat: <span class="fw-bold text-dark">{{ $summaryTotalInvoices }}</span>
            </small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="dropdown">
                <button class="btn btn-light border btn-sm shadow-sm fw-bold dropdown-toggle text-dark" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                    <i class="iconoir-view-columns-3 me-1"></i> Kolom
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow p-2 colToggleMenu"
                    data-target="#table-invoice-products"
                    style="min-width: 200px; max-height: 450px; overflow-y: auto; z-index: 100000;">
                    <!-- Column toggles via JS -->
                </ul>
            </div>
            <div class="btn-group shadow-sm">
                <button class="btn btn-white border btn-sm fw-bold text-dark" onclick="printTable('invoice-products')">
                    <i class="fa fa-print me-1"></i> Print
                </button>
                <button class="btn btn-white border btn-sm fw-bold text-dark" onclick="exportReport('invoice_items')">
                    <i class="fa fa-file-excel me-1 text-success"></i> Export Excel
                </button>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="table-invoice-products">
            <thead class="bg-light">
                @php
                    $sortBy = request('sort_by', 'invoices.tgl_invoice');
                    $sortDir = request('sort_dir', 'desc');
                @endphp
                <tr>
                    <th class="px-4 py-3 text-muted small fw-bold text-uppercase cursor-pointer sortable"
                        data-sort="status_pembayaran">
                        Status
                        @if ($sortBy === 'status_pembayaran')
                            <i class="fa fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} ms-1"></i>
                        @else
                            <i class="fa fa-sort text-muted opacity-50 ms-1"></i>
                        @endif
                    </th>
                    <th class="py-3 text-muted small fw-bold text-uppercase cursor-pointer sortable"
                        data-sort="invoices.tgl_invoice">
                        Tanggal
                        @if ($sortBy === 'invoices.tgl_invoice')
                            <i class="fa fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} ms-1"></i>
                        @else
                            <i class="fa fa-sort text-muted opacity-50 ms-1"></i>
                        @endif
                    </th>
                    <th class="py-3 text-muted small fw-bold text-uppercase cursor-pointer sortable"
                        data-sort="invoices.nomor_invoice">
                        No. Nota
                        @if ($sortBy === 'invoices.nomor_invoice')
                            <i class="fa fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} ms-1"></i>
                        @else
                            <i class="fa fa-sort text-muted opacity-50 ms-1"></i>
                        @endif
                    </th>
                    <th class="py-3 text-muted small fw-bold text-uppercase cursor-pointer sortable"
                        data-sort="suppliers.nomor_mitra">
                        Kode
                        @if ($sortBy === 'suppliers.nomor_mitra' || $sortBy === 'kode_supplier')
                            <i class="fa fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} ms-1"></i>
                        @else
                            <i class="fa fa-sort text-muted opacity-50 ms-1"></i>
                        @endif
                    </th>
                    <th class="py-3 text-muted small fw-bold text-uppercase cursor-pointer sortable"
                        data-sort="products.nama_produk">
                        Produk
                        @if ($sortBy === 'products.nama_produk')
                            <i class="fa fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} ms-1"></i>
                        @else
                            <i class="fa fa-sort text-muted opacity-50 ms-1"></i>
                        @endif
                    </th>
                    <th class="py-3 text-muted small fw-bold text-uppercase cursor-pointer sortable"
                        data-sort="mitras.nama">
                        Mitra
                        @if ($sortBy === 'mitras.nama')
                            <i class="fa fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} ms-1"></i>
                        @else
                            <i class="fa fa-sort text-muted opacity-50 ms-1"></i>
                        @endif
                    </th>

                    <th class="text-end py-3 text-muted small fw-bold text-uppercase">
                        Qty
                    </th>
                    <th class="text-end py-3 text-muted small fw-bold text-uppercase cursor-pointer sortable"
                        data-sort="invoice_items.harga_satuan">
                        Harga
                        @if ($sortBy === 'invoice_items.harga_satuan')
                            <i class="fa fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} ms-1"></i>
                        @else
                            <i class="fa fa-sort text-muted opacity-50 ms-1"></i>
                        @endif
                    </th>
                    <th class="text-end py-3 text-muted small fw-bold text-uppercase cursor-pointer sortable"
                        data-sort="invoice_items.diskon_nilai">
                        Disc. Item
                        @if ($sortBy === 'invoice_items.diskon_nilai')
                            <i class="fa fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} ms-1"></i>
                        @else
                            <i class="fa fa-sort text-muted opacity-50 ms-1"></i>
                        @endif
                    </th>
                    <th class="text-end py-3 text-muted small fw-bold text-uppercase cursor-pointer sortable"
                        data-sort="total_diskon">
                        Tot. Disc
                        @if ($sortBy === 'total_diskon')
                            <i class="fa fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} ms-1"></i>
                        @else
                            <i class="fa fa-sort text-muted opacity-50 ms-1"></i>
                        @endif
                    </th>
                    <th class="px-4 py-3 text-muted small fw-bold text-uppercase text-end cursor-pointer sortable"
                        data-sort="total_akhir">
                        Total Akhir
                        @if ($sortBy === 'total_akhir')
                            <i class="fa fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} ms-1"></i>
                        @else
                            <i class="fa fa-sort text-muted opacity-50 ms-1"></i>
                        @endif
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoiceItems as $item)
                    <tr>
                        <td class="px-4">
                            @php
                                $statusClass = match ($item->status_pembayaran) {
                                    'Paid' => 'success',
                                    'Unpaid' => 'danger',
                                    'Partially Paid' => 'warning',
                                    'Overdue' => 'dark',
                                    default => 'secondary',
                                };
                            @endphp
                            <span
                                class="badge bg-soft-{{ $statusClass }} text-{{ $statusClass }} border border-{{ $statusClass }} border-opacity-25 rounded-pill px-2 py-1">
                                {{ $item->status_pembayaran }}
                            </span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($item->tgl_invoice)->format('d M Y') }}</td>
                        <td class="fw-bold">
                            <a href="{{ $item->tipe_invoice === 'Sales' ? route('sales.show', $item->invoice_id) : route('purchase.show', $item->invoice_id) }}"
                                class="text-primary text-decoration-none">
                                {{ $item->nomor_invoice }}
                            </a>
                        </td>
                        <td class="text-muted font-monospace small">{{ $item->kode_supplier ?: '-' }}</td>
                        <td class="fw-bold text-dark">{{ $item->nama_produk }}</td>
                        <td>{{ $item->nama_mitra }}</td>
                        <td class="text-end fw-bold">{{ number_format($item->qty, 0, ',', '.') }}</td>
                        <td class="text-start fw-bold text-dark">Rp
                            {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                        <td class="text-start fw-bold text-danger">Rp
                            {{ number_format($item->diskon_nilai, 0, ',', '.') }}</td>
                        <td class="text-start fw-bold text-danger">Rp
                            {{ number_format($item->total_diskon, 0, ',', '.') }}</td>
                        <td class="px-4 text-start fw-bold text-primary">Rp
                            {{ number_format($item->total_akhir, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center py-5">
                            <div class="mb-3"><i class="iconoir-list fs-1 text-muted opacity-50"></i></div>
                            <h6 class="fw-bold text-dark">Tidak ada data item nota</h6>
                            <p class="text-muted small mb-0">Sesuaikan filter untuk melihat data.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-light fw-bold">
                <tr>
                    <td colspan="10" class="px-4 text-end text-uppercase text-muted small fw-bold">Total Transaksi
                    </td>
                    <td class="px-4 text-primary fw-bold">Rp
                        {{ number_format($summaryTotalTransaction, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="card-footer bg-white border-top py-3 px-4">
        {{ $invoiceItems->links('pagination::bootstrap-5') }}
    </div>
</div>
