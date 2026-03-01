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
                                <a class="nav-link fw-bold" data-bs-toggle="tab" href="#tab-mitra" role="tab">
                                    <i class="iconoir-group me-2"></i>Import Mitra
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link fw-bold" data-bs-toggle="tab" href="#tab-sales" role="tab">
                                    <i class="iconoir-page me-2"></i>Import Sales Invoice
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link fw-bold" data-bs-toggle="tab" href="#tab-receipt" role="tab">
                                    <i class="iconoir-scan-qr-code me-2"></i>Import Sales Receipt (PDF)
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link fw-bold" data-bs-toggle="tab" href="#tab-purchase" role="tab">
                                    <i class="iconoir-cart me-2"></i>Import Purchase Invoice
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link fw-bold" data-bs-toggle="tab" href="#tab-purchase-receipt" role="tab">
                                    <i class="iconoir-doc-text me-2"></i>Import Purchase Receipt (PDF)
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
                            
                            <!-- Tab Mitra -->
                            <div class="tab-pane fade" id="tab-mitra" role="tabpanel">
                                <div class="row justify-content-center">
                                    <div class="col-lg-6">
                                        <div class="text-center mb-4">
                                            <div class="mb-3">
                                                <i class="iconoir-group fs-1 text-info"></i>
                                            </div>
                                            <h5 class="fw-bold">Upload File Excel Mitra</h5>
                                            <p class="text-muted">
                                                Format yang didukung: .xlsx, .xls, .csv<br>
                                                Pastikan file memiliki header kolom yang sesuai.
                                            </p>
                                            <a href="{{ route('import.template', ['type' => 'mitra']) }}" class="btn btn-outline-success btn-sm fw-bold">
                                                <i class="iconoir-download me-1"></i> Download Template Import Mitra
                                            </a>
                                        </div>

                                        <form action="{{ route('import.mitra') }}" method="POST" enctype="multipart/form-data" class="border rounded-3 p-4 bg-light">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="file_mitra" class="form-label fw-bold">Pilih File</label>
                                                <input class="form-control" type="file" id="file_mitra" name="file" required>
                                            </div>
                                            <div class="d-grid">
                                                <button type="submit" class="btn btn-info fw-bold text-white">
                                                    <i class="iconoir-upload me-1"></i> Mulai Import Mitra
                                                </button>
                                            </div>
                                        </form>

                                        <div class="mt-4 alert alert-info small">
                                            <strong>Catatan:</strong>
                                            <ul class="mb-0 ps-3">
                                                <li>Data mitra akan diupdate berdasarkan Nama.</li>
                                                <li>Nama mitra akan dipisahkan otomatis dari Badan Usaha (PT, CV, dll).</li>
                                                <li>Jika tidak ada Badan Usaha, akan diset default.</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tab Sales -->
                            <div class="tab-pane fade" id="tab-sales" role="tabpanel">
                                <div class="row justify-content-center">
                                    <div class="col-lg-6">
                                        <div class="text-center mb-4">
                                            <div class="mb-3">
                                                <i class="iconoir-page fs-1 text-success"></i>
                                            </div>
                                            <h5 class="fw-bold">Upload File Excel Sales Invoice</h5>
                                            <p class="text-muted">
                                                Format yang didukung: .xlsx, .xls, .csv<br>
                                                Pastikan file memiliki header kolom yang sesuai.
                                            </p>
                                            <a href="{{ route('import.template', ['type' => 'sales']) }}" class="btn btn-outline-success btn-sm fw-bold">
                                                <i class="iconoir-download me-1"></i> Download Template Sales Invoice
                                            </a>
                                        </div>

                                        <form action="{{ route('import.sales') }}" method="POST" enctype="multipart/form-data" class="border rounded-3 p-4 bg-light">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="file_sales" class="form-label fw-bold">Pilih File</label>
                                                <input class="form-control" type="file" id="file_sales" name="file" required>
                                            </div>
                                            <div class="d-grid">
                                                <button type="submit" class="btn btn-success fw-bold text-white">
                                                    <i class="iconoir-upload me-1"></i> Mulai Import Sales
                                                </button>
                                            </div>
                                        </form>

                                        <div class="mt-4 alert alert-info small">
                                            <strong>Catatan:</strong>
                                            <ul class="mb-0 ps-3">
                                                <li>Satu Invoice bisa memiliki banyak baris (item).</li>
                                                <li>Pastikan kolom "number" sama untuk item dalam invoice yang sama.</li>
                                                <li>Nama Partner akan dicari otomatis atau dibuat baru jika tidak ditemukan.</li>
                                                <li>Produk akan dicari berdasarkan nama atau dibuat baru (dengan SKU otomatis) jika tidak ada.</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tab Receipt -->
                            <div class="tab-pane fade" id="tab-receipt" role="tabpanel">
                                <div class="row justify-content-center">
                                    <div class="col-lg-6">
                                        <div class="text-center mb-4">
                                            <div class="mb-3">
                                                <i class="iconoir-scan-qr-code fs-1 text-danger"></i>
                                            </div>
                                            <h5 class="fw-bold">Upload Sales Receipt (PDF)</h5>
                                            <p class="text-muted">
                                                Format yang didukung: .pdf<br>
                                                Sistem akan membaca Payment No, Invoice No, dan Amount secara otomatis.
                                            </p>
                                        </div>

                                        <form action="{{ route('import.receipt') }}" method="POST" enctype="multipart/form-data" class="border rounded-3 p-4 bg-light">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="file_receipt" class="form-label fw-bold">Pilih File PDF</label>
                                                <input class="form-control" type="file" id="file_receipt" name="file[]" multiple required accept=".pdf">
                                                <div class="form-text">Bisa pilih banyak file sekaligus (Max 20 file per upload).</div>
                                            </div>
                                            <div class="d-grid">
                                                <button type="submit" class="btn btn-danger fw-bold text-white">
                                                    <i class="iconoir-upload me-1"></i> Mulai Import Receipt
                                                </button>
                                            </div>
                                        </form>

                                        <div class="mt-4 alert alert-info small">
                                            <strong>Proses Import:</strong>
                                            <ul class="mb-0 ps-3">
                                                <li>Upload file PDF Kuitansi.</li>
                                                <li>Sistem akan membaca text dari PDF.</li>
                                                <li>Mencocokkan Invoice No dengan database.</li>
                                                <li>Membuat data Pembayaran otomatis jika cocok.</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tab Purchase -->
                            <div class="tab-pane fade" id="tab-purchase" role="tabpanel">
                                <div class="row justify-content-center">
                                    <div class="col-lg-6">
                                        <div class="text-center mb-4">
                                            <div class="mb-3">
                                                <i class="iconoir-cart fs-1 text-warning"></i>
                                            </div>
                                            <h5 class="fw-bold">Upload File Excel Purchase Invoice</h5>
                                            <p class="text-muted">
                                                Format yang didukung: .xlsx, .xls, .csv<br>
                                                Pastikan file memiliki header kolom yang sesuai.
                                            </p>
                                            <a href="{{ route('import.template', ['type' => 'purchase']) }}" class="btn btn-outline-warning btn-sm fw-bold">
                                                <i class="iconoir-download me-1"></i> Download Template Purchase Invoice
                                            </a>
                                        </div>

                                        <form action="{{ route('import.purchase') }}" method="POST" enctype="multipart/form-data" class="border rounded-3 p-4 bg-light">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="file_purchase" class="form-label fw-bold">Pilih File</label>
                                                <input class="form-control" type="file" id="file_purchase" name="file" required>
                                            </div>
                                            <div class="d-grid">
                                                <button type="submit" class="btn btn-warning fw-bold text-dark">
                                                    <i class="iconoir-upload me-1"></i> Mulai Import Purchase
                                                </button>
                                            </div>
                                        </form>

                                        <div class="mt-4 alert alert-info small">
                                            <strong>Catatan:</strong>
                                            <ul class="mb-0 ps-3">
                                                <li>Satu Invoice bisa memiliki banyak baris (item).</li>
                                                <li>Pastikan kolom "number" sama untuk item dalam invoice yang sama.</li>
                                                <li>Nama Supplier akan dicari otomatis atau dibuat baru jika tidak ditemukan.</li>
                                                <li>Produk akan dicari berdasarkan nama atau dibuat baru (dengan SKU otomatis) jika tidak ada.</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab Purchase Receipt -->
                            <div class="tab-pane fade" id="tab-purchase-receipt" role="tabpanel">
                                <div class="row justify-content-center">
                                    <div class="col-lg-6">
                                        <div class="text-center mb-4">
                                            <div class="mb-3">
                                                <i class="iconoir-doc-text fs-1 text-secondary"></i>
                                            </div>
                                            <h5 class="fw-bold">Upload Purchase Receipt (PDF)</h5>
                                            <p class="text-muted">
                                                Format yang didukung: .pdf<br>
                                                Sistem akan membaca Payment No, Invoice No, dan Amount secara otomatis.
                                            </p>
                                        </div>

                                        <form action="{{ route('import.purchase.receipt') }}" method="POST" enctype="multipart/form-data" class="border rounded-3 p-4 bg-light">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="file_purchase_receipt" class="form-label fw-bold">Pilih File PDF</label>
                                                <input class="form-control" type="file" id="file_purchase_receipt" name="file[]" multiple required accept=".pdf">
                                                <div class="form-text">Bisa pilih banyak file sekaligus (Max 20 file per upload).</div>
                                            </div>
                                            <div class="d-grid">
                                                <button type="submit" class="btn btn-secondary fw-bold text-white">
                                                    <i class="iconoir-upload me-1"></i> Mulai Import Purchase Receipt
                                                </button>
                                            </div>
                                        </form>

                                        <div class="mt-4 alert alert-info small">
                                            <strong>Proses Import:</strong>
                                            <ul class="mb-0 ps-3">
                                                <li>Upload file PDF Kuitansi Pembelian.</li>
                                                <li>Sistem akan membaca text dari PDF.</li>
                                                <li>Mencocokkan Invoice No dengan database.</li>
                                                <li>Membuat data Pembayaran otomatis jika cocok.</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
