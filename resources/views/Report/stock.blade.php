@extends('Layout.main')

@section('main')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">

<div class="page-wrapper" style="background-color: #f4f7fa; min-height: 100vh; font-family: 'Inter', sans-serif;">
    <div class="page-content py-4">
        <div class="container-fluid">
            <!-- Header -->
            <div class="row align-items-center mb-4">
                <div class="col-md-7">
                    <h4 class="fw-bold text-dark mb-1">Laporan Stok</h4>
                    <p class="text-muted small mb-0">Pergerakan stok dan inventori periode tertentu.</p>
                </div>
                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                    <a href="{{ route('report.stock.export', request()->query()) }}" class="btn btn-white border fw-bold px-3 shadow-sm text-dark me-2">
                        <i class="iconoir-download me-1"></i> Unduh
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body p-4">
                    <form action="{{ route('report.stock') }}" method="GET" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-muted small text-uppercase">Tanggal Dari</label>
                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date', date('Y-m-01')) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-muted small text-uppercase">Tanggal Sampai</label>
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date', date('Y-m-t')) }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold text-muted small text-uppercase">Kategori</label>
                            <select name="category_id" class="form-select">
                                <option value="">Semua</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold text-muted small text-uppercase">Produk</label>
                            <select name="product_id" class="form-select">
                                <option value="">Semua</option>
                                @foreach($allProducts as $prod)
                                    <option value="{{ $prod->id }}" {{ request('product_id') == $prod->id ? 'selected' : '' }}>
                                        {{ $prod->nama_produk }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Location Filter (Optional) -->
                        <div class="col-md-2">
                             <label class="form-label fw-bold text-muted small text-uppercase">Lokasi</label>
                             <select name="location_id" class="form-select">
                                 <option value="">Semua</option>
                                 @foreach($locations as $loc)
                                     <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>
                                         {{ $loc->name }}
                                     </option>
                                 @endforeach
                             </select>
                        </div>

                        <div class="col-12 mt-3 text-end">
                            <button type="submit" class="btn btn-primary fw-bold px-4">
                                Hasilkan Laporan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row g-3 mb-4">
                <!-- Quantity Row -->
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4 border-start border-4 border-secondary">
                            <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px;">Opening Quantity</label>
                            <h5 class="fw-bold mb-0 text-dark">{{ number_format($stats['opening_qty'], 2) }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4 border-start border-4 border-info">
                            <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px;">Quantity In</label>
                            <h5 class="fw-bold mb-0 text-info">{{ number_format($stats['qty_in'], 2) }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4 border-start border-4 border-warning">
                            <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px;">Quantity Out</label>
                            <h5 class="fw-bold mb-0 text-warning">{{ number_format($stats['qty_out'], 2) }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4 border-start border-4 border-primary">
                            <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px;">Closing Quantity</label>
                            <h5 class="fw-bold mb-0 text-primary">{{ number_format($stats['closing_qty'], 2) }}</h5>
                        </div>
                    </div>
                </div>

                <!-- Value Row -->
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4 border-start border-4 border-secondary">
                            <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px;">Opening Value</label>
                            <h5 class="fw-bold mb-0 text-dark">Rp {{ number_format($stats['opening_value']) }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4 border-start border-4 border-info">
                            <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px;">Value In</label>
                            <h5 class="fw-bold mb-0 text-info">Rp {{ number_format($stats['value_in']) }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4 border-start border-4 border-warning">
                            <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px;">Value Out</label>
                            <h5 class="fw-bold mb-0 text-warning">Rp {{ number_format($stats['value_out']) }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4 border-start border-4 border-primary">
                            <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px;">Closing Value</label>
                            <h5 class="fw-bold mb-0 text-primary">Rp {{ number_format($stats['closing_value']) }}</h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Table -->
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 13px;" id="stockTable">
                            <thead class="bg-light text-uppercase text-muted fw-bold" style="font-size: 11px;">
                                <tr>
                                    <th class="ps-4 py-3">Kode Produk</th>
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th>Satuan</th>
                                    <th class="text-end">Opening Qty</th>
                                    <th class="text-end">Qty In</th>
                                    <th class="text-end">Qty Out</th>
                                    <th class="text-end fw-bold">Closing Qty</th>
                                    <th class="text-end ps-4">Opening Value</th>
                                    <th class="text-end">Value In</th>
                                    <th class="text-end">Value Out</th>
                                    <th class="text-end fw-bold pe-4">Closing Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                <tr>
                                    <td class="ps-4 fw-bold text-dark">{{ $product->sku_kode }}</td>
                                    <td class="fw-bold">{{ $product->nama_produk }}</td>
                                    <td>{{ $product->nama_kategori }}</td>
                                    <td>{{ $product->nama_unit }}</td>
                                    
                                    <td class="text-end">{{ number_format($product->opening_qty, 2) }}</td>
                                    <td class="text-end text-success">{{ number_format($product->qty_in, 2) }}</td>
                                    <td class="text-end text-danger">{{ number_format($product->qty_out, 2) }}</td>
                                    <td class="text-end fw-bold">{{ number_format($product->closing_qty, 2) }}</td>
                                    
                                    <td class="text-end ps-4">{{ number_format($product->opening_value) }}</td>
                                    <td class="text-end text-success">{{ number_format($product->value_in) }}</td>
                                    <td class="text-end text-danger">{{ number_format($product->value_out) }}</td>
                                    <td class="text-end fw-bold pe-4">{{ number_format($product->closing_value) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="12" class="text-center py-5 text-muted">
                                        <i class="iconoir-file-not-found fs-1 d-block mb-2"></i>
                                        Tidak ada data stok untuk periode ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('css')
<link href="{{url('')}}/assets/libs/simple-datatables/style.css" rel="stylesheet" type="text/css" />
@endpush

@push('js')
<script src="{{url('')}}/assets/libs/simple-datatables/umd/simple-datatables.js"></script>
<script>
    const stockTable = new simpleDatatables.DataTable("#stockTable", {
        searchable: true,
        fixedHeight: false,
        perPage: 10,
        labels: {
            placeholder: "Cari...",
            perPage: "item per halaman",
            noRows: "Tidak ada data ditemukan",
            info: "Menampilkan {start} sampai {end} dari {rows} entri",
        }
    });
</script>
@endpush
