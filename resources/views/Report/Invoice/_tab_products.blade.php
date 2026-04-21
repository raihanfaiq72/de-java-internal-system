<div class="card border-0 shadow-sm rounded-3">
    @php
        $metric = request('product_metric', 'both');
    @endphp
    <div
        class="card-header bg-white border-bottom py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h6 class="mb-0 fw-bold text-dark">Daftar Produk Terjual</h6>
            <small class="text-muted">
                @if($metric === 'qty')
                    Total Qty: <span class="fw-bold text-success">{{ number_format($totalSoldQty, 0, ',', '.') }}</span>
                @elseif($metric === 'value')
                    Total Value: <span class="fw-bold text-primary">Rp
                        {{ number_format($totalSoldValue ?? 0, 0, ',', '.') }}</span>
                @else
                    Total Qty: <span class="fw-bold text-success">{{ number_format($totalSoldQty, 0, ',', '.') }}</span>
                    <span class="mx-2">•</span>
                    Total Value: <span class="fw-bold text-primary">Rp
                        {{ number_format($totalSoldValue ?? 0, 0, ',', '.') }}</span>
                @endif
                <span class="ms-1">(Berdasarkan pembayaran periode ini)</span>
            </small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="dropdown">
                <button class="btn btn-light border btn-sm shadow-sm fw-bold dropdown-toggle text-dark" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                    <i class="iconoir-view-columns-3 me-1"></i> Kolom
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow p-2 colToggleMenu" data-target="#table-products"
                    style="min-width: 200px; max-height: 450px; overflow-y: auto; z-index: 100000;">
                    <!-- Column toggles via JS -->
                </ul>
            </div>
            <div class="btn-group shadow-sm">
                <button class="btn btn-white border btn-sm fw-bold text-dark" onclick="printTable('products')">
                    <i class="fa fa-print me-1"></i> Print
                </button>
                <button class="btn btn-white border btn-sm fw-bold text-dark" onclick="exportReport('sold_products')">
                    <i class="fa fa-file-excel me-1 text-success"></i> Export Excel
                </button>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="table-products">
            <thead class="bg-light">
                @php
                    $sortBy = request('sort_by', 'products.nama_produk');
                    $sortDir = request('sort_dir', 'asc');
                @endphp
                <tr>
                    <th class="px-4 py-3 text-muted small fw-bold text-uppercase cursor-pointer sortable" data-sort="products.nama_produk">
                        Nama Produk
                        @if($sortBy === 'products.nama_produk')
                            <i class="fa fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} ms-1"></i>
                        @else
                            <i class="fa fa-sort text-muted opacity-50 ms-1"></i>
                        @endif
                    </th>
                    <th class="py-3 text-muted small fw-bold text-uppercase cursor-pointer sortable" data-sort="products.sku_kode">
                        Kode (SKU)
                        @if($sortBy === 'products.sku_kode')
                            <i class="fa fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} ms-1"></i>
                        @else
                            <i class="fa fa-sort text-muted opacity-50 ms-1"></i>
                        @endif
                    </th>
                    <th class="py-3 text-muted small fw-bold text-uppercase cursor-pointer sortable" data-sort="product_categories.nama_kategori">
                        Kategori
                        @if($sortBy === 'product_categories.nama_kategori')
                            <i class="fa fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} ms-1"></i>
                        @else
                            <i class="fa fa-sort text-muted opacity-50 ms-1"></i>
                        @endif
                    </th>
                    <th class="py-3 text-muted small fw-bold text-uppercase cursor-pointer sortable" data-sort="products.satuan">
                        Satuan
                        @if($sortBy === 'products.satuan')
                            <i class="fa fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} ms-1"></i>
                        @else
                            <i class="fa fa-sort text-muted opacity-50 ms-1"></i>
                        @endif
                    </th>
                    @if($metric === 'qty')
                        <th class="px-4 py-3 text-muted small fw-bold text-uppercase text-end">
                            Kuantitas
                        </th>
                    @elseif($metric === 'value')
                        <th class="px-4 py-3 text-muted small fw-bold text-uppercase text-end cursor-pointer sortable" data-sort="total_value">
                            Value (Rp)
                            @if($sortBy === 'total_value')
                                <i class="fa fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} ms-1"></i>
                            @else
                                <i class="fa fa-sort text-muted opacity-50 ms-1"></i>
                            @endif
                        </th>
                    @else
                        <th class="py-3 text-muted small fw-bold text-uppercase text-end">
                            Kuantitas
                        </th>
                        <th class="px-4 py-3 text-muted small fw-bold text-uppercase text-end cursor-pointer sortable" data-sort="total_value">
                            Value (Rp)
                            @if($sortBy === 'total_value')
                                <i class="fa fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} ms-1"></i>
                            @else
                                <i class="fa fa-sort text-muted opacity-50 ms-1"></i>
                            @endif
                        </th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($soldProducts as $product)
                    <tr>
                        <td class="px-4 fw-bold text-dark">{{ $product->nama_produk }}</td>
                        <td class="text-muted font-monospace small">{{ $product->sku_kode }}</td>
                        <td>{{ $product->nama_kategori ?? '-' }}</td>
                        <td>{{ $product->satuan }}</td>
                        @if($metric === 'qty')
                            <td class="px-4 text-end fw-bold text-success">{{ number_format($product->total_qty, 0, ',', '.') }}
                            </td>
                        @elseif($metric === 'value')
                            <td class="px-4 text-end fw-bold text-primary">Rp
                                {{ number_format($product->total_value ?? 0, 0, ',', '.') }}</td>
                        @else
                            <td class="text-end fw-bold text-success">{{ number_format($product->total_qty, 0, ',', '.') }}</td>
                            <td class="px-4 text-end fw-bold text-primary">Rp
                                {{ number_format($product->total_value ?? 0, 0, ',', '.') }}</td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $metric === 'both' ? 6 : 5 }}" class="text-center py-5">
                            <div class="mb-3"><i class="iconoir-box-iso fs-1 text-muted opacity-50"></i></div>
                            <h6 class="fw-bold text-dark">Tidak ada produk terjual</h6>
                            <p class="text-muted small mb-0">Belum ada invoice lunas/terbayar pada periode ini.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-light fw-bold">
                <tr>
                    @if($metric === 'qty')
                        <td colspan="4" class="px-4 text-end text-uppercase text-muted small">Total Kuantitas</td>
                        <td class="px-4 text-end text-success">{{ number_format($totalSoldQty, 0, ',', '.') }}</td>
                    @elseif($metric === 'value')
                        <td colspan="4" class="px-4 text-end text-uppercase text-muted small">Total Value</td>
                        <td class="px-4 text-end text-primary">Rp {{ number_format($totalSoldValue ?? 0, 0, ',', '.') }}
                        </td>
                    @else
                        <td colspan="4" class="px-4 text-end text-uppercase text-muted small">Total Kuantitas</td>
                        <td class="text-end text-success">{{ number_format($totalSoldQty, 0, ',', '.') }}</td>
                        <td class="px-4 text-end text-primary">Rp {{ number_format($totalSoldValue ?? 0, 0, ',', '.') }}
                        </td>
                    @endif
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="card-footer bg-white border-top py-3 px-4">
        {{ $soldProducts->links('pagination::bootstrap-5') }}
    </div>
</div>