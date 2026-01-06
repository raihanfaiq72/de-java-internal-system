@extends('Layout.main')

@section('main')
<div class="page-wrapper">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box d-md-flex justify-content-between align-items-center">
                        <h4 class="page-title">Manajemen Barang</h4>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item active">Barang</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom-0 pb-0">
                            <ul class="nav nav-pills" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active fw-bold" data-bs-toggle="tab" href="#tab-produk" role="tab">
                                        <i class="fa fa-box me-1"></i> Produk
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link fw-bold" data-bs-toggle="tab" href="#tab-kategori-produk" role="tab">
                                        <i class="fa fa-tags me-1"></i> Kategori Produk
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link fw-bold" data-bs-toggle="tab" href="#tab-unit" role="tab">
                                        <i class="fa fa-ruler-combined me-1"></i> Unit Ukuran
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link fw-bold" data-bs-toggle="tab" href="#tab-kategori-unit" role="tab">
                                        <i class="fa fa-layer-group me-1"></i> Kategori Unit
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="card-body">
                            <div class="tab-content">

                                {{-- TAB 1: PRODUK --}}
                                @include('Barang.Tab._Produk')

                                {{-- TAB 2: KATEGORI PRODUK --}}
                                @include('Barang.Tab._KategoriProduk')

                                {{-- TAB 3: UNIT UKURAN --}}
                                @include('Barang.Tab._UnitUkuran')

                                {{-- TAB 4: KATEGORI UNIT --}}
                                @include('Barang.Tab._KategoriUnit')

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')

<style>
    .nav-pills .nav-link.active {
        background-color: #0d6efd;
        color: white;
    }
    .nav-pills .nav-link {
        color: #6c757d;
        border-radius: 8px;
        margin-right: 5px;
    }
    .table thead th {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>

@endpush