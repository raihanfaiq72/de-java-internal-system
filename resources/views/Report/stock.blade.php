@extends('Layout.main')

@section('main')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box d-md-flex justify-content-between align-items-center">
                            <h4 class="page-title">Laporan Stok</h4>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                                <li class="breadcrumb-item active">Report: Stock</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <form action="{{ route('report.stock') }}" method="GET" class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-muted small text-uppercase">Tanggal Dari</label>
                                <input type="date" name="start_date" class="form-control"
                                    value="{{ request('start_date', date('Y-m-01')) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-muted small text-uppercase">Tanggal Sampai</label>
                                <input type="date" name="end_date" class="form-control"
                                    value="{{ request('end_date', date('Y-m-t')) }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold text-muted small text-uppercase">Kategori</label>
                                <select name="category_id" id="select-category" class="form-select">
                                    <option value="">Semua</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}"
                                            {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold text-muted small text-uppercase">Produk</label>
                                <select name="product_id" id="select-product" class="form-select">
                                    <option value="">Semua</option>
                                    @foreach ($allProducts as $prod)
                                        <option value="{{ $prod->id }}"
                                            {{ request('product_id') == $prod->id ? 'selected' : '' }}>
                                            {{ $prod->nama_produk }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold text-muted small text-uppercase">Lokasi</label>
                                <select name="location_id" id="select-location" class="form-select">
                                    <option value="">Semua</option>
                                    @foreach ($locations as $loc)
                                        <option value="{{ $loc->id }}"
                                            {{ request('location_id') == $loc->id ? 'selected' : '' }}>
                                            {{ $loc->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 mt-3 text-end">
                                <button type="submit" class="btn btn-primary fw-bold px-4">Hasilkan Laporan</button>
                                <button type="button" onclick="exportStock()" class="btn btn-success fw-bold px-4 ms-2">
                                    <i class="fa fa-file-excel me-1"></i> Export Excel
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
                                <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px;">Opening
                                    Quantity</label>
                                <h5 class="fw-bold mb-0 text-dark">{{ number_format($stats['opening_qty'], 2) }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4 border-start border-4 border-info">
                                <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px;">Quantity
                                    In</label>
                                <h5 class="fw-bold mb-0 text-info">{{ number_format($stats['qty_in'], 2) }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4 border-start border-4 border-warning">
                                <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px;">Quantity
                                    Out</label>
                                <h5 class="fw-bold mb-0 text-warning">{{ number_format($stats['qty_out'], 2) }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4 border-start border-4 border-primary">
                                <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px;">Closing
                                    Quantity</label>
                                <h5 class="fw-bold mb-0 text-primary">{{ number_format($stats['closing_qty'], 2) }}</h5>
                            </div>
                        </div>
                    </div>

                    <!-- Value Row -->
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4 border-start border-4 border-secondary">
                                <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px;">Opening
                                    Value</label>
                                <h5 class="fw-bold mb-0 text-dark">Rp {{ number_format($stats['opening_value']) }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4 border-start border-4 border-info">
                                <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px;">Value
                                    In</label>
                                <h5 class="fw-bold mb-0 text-info">Rp {{ number_format($stats['value_in']) }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4 border-start border-4 border-warning">
                                <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px;">Value
                                    Out</label>
                                <h5 class="fw-bold mb-0 text-warning">Rp {{ number_format($stats['value_out']) }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4 border-start border-4 border-primary">
                                <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px;">Closing
                                    Value</label>
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
                                    @foreach ($products as $product)
                                        <tr>
                                            <td class="ps-4 fw-bold text-dark">{{ $product->sku_kode }}</td>
                                            <td class="fw-bold">{{ $product->nama_produk }}</td>
                                            <td>{{ $product->nama_kategori }}</td>
                                            <td>{{ $product->satuan }}</td>

                                            <td class="text-end">{{ number_format($product->opening_qty, 2) }}</td>
                                            <td class="text-end text-success">{{ number_format($product->qty_in, 2) }}
                                            </td>
                                            <td class="text-end text-danger">{{ number_format($product->qty_out, 2) }}
                                            </td>
                                            <td class="text-end fw-bold">{{ number_format($product->closing_qty, 2) }}
                                            </td>

                                            <td class="text-end ps-4">{{ number_format($product->opening_value) }}</td>
                                            <td class="text-end text-success">{{ number_format($product->value_in) }}</td>
                                            <td class="text-end text-danger">{{ number_format($product->value_out) }}</td>
                                            <td class="text-end fw-bold pe-4">{{ number_format($product->closing_value) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    function exportStock() {
        // Collect current filters
        const startDate = document.querySelector('input[name="start_date"]').value;
        const endDate = document.querySelector('input[name="end_date"]').value;
        const categoryId = document.querySelector('select[name="category_id"]').value;
        const productId = document.querySelector('select[name="product_id"]').value;
        const locationId = document.querySelector('select[name="location_id"]').value;

        // Construct URL
        const params = new URLSearchParams({
            start_date: startDate,
            end_date: endDate,
            category_id: categoryId,
            product_id: productId,
            location_id: locationId
        });

        window.location.href = "{{ route('report.stock.export') }}?" + params.toString();
    }
</script>
@endpush

@push('css')
    <link href="{{ url('') }}/assets/libs/simple-datatables/style.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <style>
        .ts-wrapper {
            z-index: 10;
        }
    </style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script src="{{ url('') }}/assets/libs/simple-datatables/umd/simple-datatables.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const tableEl = document.querySelector("#stockTable");
            if (tableEl) {
                new simpleDatatables.DataTable(tableEl, {
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
            }

            const tsConfig = {
                create: false,
                allowEmptyOption: true,
                dropdownParent: 'body'
            };

            const ids = ['#select-category', '#select-product', '#select-location'];
            ids.forEach(id => {
                const el = document.querySelector(id);
                if (el) new TomSelect(el, tsConfig);
            });
        });
    </script>
@endpush
