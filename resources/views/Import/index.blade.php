@extends('Layout.main')

@section('main')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box d-md-flex justify-content-between align-items-center">
                            <h4 class="page-title">Import Data</h4>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                                <li class="breadcrumb-item active">Import</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white border-bottom p-0">
                        <ul class="nav nav-tabs px-4 pt-3" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active fw-bold" data-bs-toggle="tab" href="#tab-stock" role="tab">
                                    <i class="iconoir-box-iso me-2"></i>Import Stock
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link fw-bold disabled" data-bs-toggle="tab" href="#tab-mitra" role="tab" aria-disabled="true">
                                    <i class="iconoir-group me-2"></i>Import Mitra (Soon)
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body p-4">
                        <div class="tab-content">
                            <!-- Tab Stock -->
                            <div class="tab-pane fade show active" id="tab-stock" role="tabpanel">
                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif
                                @if(session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        {{ session('error') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                <div class="row justify-content-center">
                                    <div class="col-lg-6">
                                        <div class="text-center mb-4">
                                            <div class="mb-3">
                                                <i class="iconoir-upload-square fs-1 text-primary"></i>
                                            </div>
                                            <h5 class="fw-bold">Upload File Excel</h5>
                                            <p class="text-muted">
                                                Format yang didukung: .xlsx, .xls, .csv<br>
                                                Pastikan file memiliki header kolom yang sesuai.
                                            </p>
                                            <a href="{{ route('import.template', ['type' => 'stock']) }}" class="btn btn-outline-success btn-sm fw-bold">
                                                <i class="iconoir-download me-1"></i> Download Template Import Stok
                                            </a>
                                        </div>

                                        <form action="{{ route('import.stock') }}" method="POST" enctype="multipart/form-data" class="border rounded-3 p-4 bg-light">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="file" class="form-label fw-bold">Pilih File</label>
                                                <input class="form-control" type="file" id="file" name="file" required>
                                            </div>
                                            <div class="d-grid">
                                                <button type="submit" class="btn btn-primary fw-bold">
                                                    <i class="iconoir-upload me-1"></i> Mulai Import
                                                </button>
                                            </div>
                                        </form>

                                        <div class="mt-4 alert alert-info small">
                                            <strong>Catatan:</strong>
                                            <ul class="mb-0 ps-3">
                                                <li>Data stok akan diupdate berdasarkan SKU.</li>
                                                <li>Jika produk belum ada, akan dibuat otomatis.</li>
                                                <li>Lokasi stok akan diset ke "Gudang Utama".</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tab Mitra (Placeholder) -->
                            <div class="tab-pane fade" id="tab-mitra" role="tabpanel">
                                <p class="text-center text-muted py-5">Fitur import mitra akan segera tersedia.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
