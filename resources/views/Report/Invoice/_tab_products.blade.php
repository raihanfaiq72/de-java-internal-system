<div class="card border-0 shadow-sm rounded-3 overflow-hidden">
    <div
        class="card-header bg-white border-bottom py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h6 class="mb-0 fw-bold text-dark">Daftar Produk Terjual</h6>
            <small class="text-muted">
                Total Qty: <span class="fw-bold text-success">{{ number_format($totalSoldQty, 0, ',', '.') }}</span>
                (Berdasarkan pembayaran periode ini)
            </small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="dropdown">
                <button class="btn btn-light border btn-sm shadow-sm fw-bold dropdown-toggle text-dark" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                    <i class="iconoir-view-columns-3 me-1"></i> Kolom
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow p-2 colToggleMenu" data-target="#table-products"
                    style="min-width: 200px; max-height: 300px; overflow-y: auto;">
                    <!-- Column toggles via JS -->
                </ul>
            </div>
            <div class="btn-group shadow-sm">
                <button class="btn btn-white border btn-sm fw-bold text-dark" onclick="printTable('products')">
                    <i class="fa fa-print me-1"></i> Print
                </button>
                <button class="btn btn-white border btn-sm fw-bold text-dark" onclick="exportReport('sold_products')">
                    <i class="fa fa-file-excel me-1 text-success"></i> Export
                </button>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="table-products">
            <thead class="bg-light">
                <tr>
                    <th class="px-4 py-3 text-muted small fw-bold text-uppercase">Nama Produk</th>
                    <th class="py-3 text-muted small fw-bold text-uppercase">Kode (SKU)</th>
                    <th class="py-3 text-muted small fw-bold text-uppercase">Kategori</th>
                    <th class="py-3 text-muted small fw-bold text-uppercase">Satuan</th>
                    <th class="px-4 py-3 text-muted small fw-bold text-uppercase text-end">Kuantitas Terjual</th>
                </tr>
            </thead>
            <tbody>
                @forelse($soldProducts as $product)
                    <tr>
                        <td class="px-4 fw-bold text-dark">{{ $product->nama_produk }}</td>
                        <td class="text-muted font-monospace small">{{ $product->sku_kode }}</td>
                        <td>{{ $product->nama_kategori ?? '-' }}</td>
                        <td>{{ $product->satuan }}</td>
                        <td class="px-4 text-end fw-bold text-success">{{ number_format($product->total_qty, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="mb-3"><i class="iconoir-box-iso fs-1 text-muted opacity-50"></i></div>
                            <h6 class="fw-bold text-dark">Tidak ada produk terjual</h6>
                            <p class="text-muted small mb-0">Belum ada invoice lunas/terbayar pada periode ini.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-light fw-bold">
                <tr>
                    <td colspan="4" class="px-4 text-end text-uppercase text-muted small">Total Kuantitas</td>
                    <td class="px-4 text-end text-success">{{ number_format($totalSoldQty, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>