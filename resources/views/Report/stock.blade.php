@extends('Layout.main')

@section('main')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">

<div class="page-wrapper" style="background-color: #f4f7fa; min-height: 100vh; font-family: 'Inter', sans-serif;">
    <div class="page-content py-4">
        <div class="container-fluid">
            <!-- Header -->
            <div class="row align-items-center mb-4">
                <div class="col-md-7">
                    <h4 class="fw-bold text-dark mb-1">Laporan Stok & Inventori</h4>
                    <p class="text-muted small mb-0">Pantau ketersediaan barang dan nilai aset persediaan secara real-time.</p>
                </div>
                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                    <button class="btn btn-white border fw-bold px-3 shadow-sm text-dark me-2">
                        <i class="fa fa-download me-1"></i> Export Excel
                    </button>
                    <button class="btn btn-primary fw-bold px-4 shadow-sm">
                        <i class="fa fa-sync me-1"></i> Refresh Data
                    </button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4 g-3">
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-body p-4 border-start border-4 border-primary">
                            <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px; letter-spacing: 1px;">Total Varian Produk</label>
                            <h4 class="fw-bold mb-0 text-dark">{{ $stats->total_items }} SKU</h4>
                            <small class="text-muted mt-2 d-block">Tersebar di berbagai kategori</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-body p-4 border-start border-4 border-danger">
                            <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px; letter-spacing: 1px;">Stok Menipis (Alert)</label>
                            <h4 class="fw-bold mb-0 text-danger">{{ $stats->low_stock }} Produk</h4>
                            <small class="text-danger fw-semibold mt-2 d-block"><i class="fa fa-exclamation-circle me-1"></i> Segera lakukan Re-order</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-body p-4 border-start border-4 border-success">
                            <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px; letter-spacing: 1px;">Estimasi Nilai Stok</label>
                            <h4 class="fw-bold mb-0 text-success">Rp {{ number_format($stats->total_valuation) }}</h4>
                            <small class="text-muted mt-2 d-block">Berdasarkan Harga Beli Terakhir</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock Table -->
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="card-header bg-white border-bottom p-4">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="fw-bold mb-0">Status Inventori Real-time</h6>
                        </div>
                        <div class="col-auto">
                            <div class="d-flex gap-2">
                                <select class="form-select form-select-sm" style="width: 150px;">
                                    <option value="">Semua Kategori</option>
                                </select>
                                <div class="input-group input-group-sm" style="width: 200px;">
                                    <input type="text" class="form-control border-end-0" placeholder="Cari Produk...">
                                    <span class="input-group-text bg-white border-start-0 text-muted"><i class="fa fa-search"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-uppercase text-muted" style="font-size: 11px; letter-spacing: 0.5px;">
                                <tr>
                                    <th class="ps-4 py-3">Kode Produk</th>
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th class="text-center">Satuan</th>
                                    <th class="text-end">Stok Tersedia</th>
                                    <th class="text-center">Status Stok</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                <tr>
                                    <td class="ps-4"><span class="f-mono text-dark fw-bold">{{ $product->sku_kode }}</span></td>
                                    <td><div class="fw-semibold text-dark">{{ $product->nama_produk }}</div></td>
                                    <td><span class="badge bg-soft-secondary text-secondary">{{ $product->nama_kategori ?? 'N/A' }}</span></td>
                                    <td class="text-center text-muted">{{ $product->nama_unit ?? '-' }}</td>
                                    <td class="text-end pe-3">
                                        <h6 class="mb-0 fw-bold {{ $product->current_stock <= 10 ? 'text-danger' : 'text-dark' }}">
                                            {{ number_format($product->current_stock ?? 0) }}
                                        </h6>
                                    </td>
                                    <td class="text-center">
                                        @if($product->current_stock <= 0)
                                            <span class="badge bg-danger">OUT OF STOCK</span>
                                        @elseif($product->current_stock <= 10)
                                            <span class="badge bg-warning text-dark">LOW STOCK</span>
                                        @else
                                            <span class="badge bg-success">HEALTHY</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                                @if($products->isEmpty())
                                    <tr><td colspan="6" class="text-center p-4 text-muted fst-italic">Belum ada data produk terdaftar.</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-top p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">Menampilkan {{ $products->count() }} produk</small>
                        {{-- Pagination could be here --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
