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
                                        <a class="nav-link active fw-bold" data-bs-toggle="tab" href="#tab-produk"
                                            role="tab">
                                            <i class="fa fa-box me-1"></i> Produk
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#tab-brand" class="nav-link fw-bold" data-bs-toggle="tab" role="tab">
                                            <i class="fa fa-tags me-1"></i> Brand
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <div class="card-body">
                                <div class="tab-content">

                                    {{-- TAB 1: PRODUK --}}
                                    @include('Barang.Tab._Produk')

                                    {{-- TAB 2: BRAND --}}
                                    @include('Barang.Tab._Brand')

                                    {{-- TAB 2: KATEGORI PRODUK --}}
                                    {{-- @include('Barang.Tab._KategoriProduk') --}}

                                    {{-- TAB 3: UNIT UKURAN --}}
                                    {{-- @include('Barang.Tab._UnitUkuran') --}}

                                    {{-- TAB 4: KATEGORI UNIT --}}
                                    {{-- @include('Barang.Tab._KategoriUnit') --}}

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('Barang.Modal._ProdukMassal')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

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
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        /* Unified Modal Consistency */
        .modal-header {
            background-color: #ffffff;
            border-bottom: 3px solid #0d6efd;
            padding: 1.25rem;
        }
        .modal-title {
            color: #212529;
            font-weight: 800;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
        }
        .modal-body {
            padding: 1.5rem;
        }
        .modal-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            padding: 1rem 1.5rem;
        }

        /* Better Labels & Inputs for Elderly */
        .form-label, label.small.fw-bold {
            font-size: 0.9rem !important;
            color: #343a40 !important;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .form-control, .form-select {
            border: 1px solid #ced4da;
            padding: 0.6rem 0.75rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }

        /* Section Dividers in Modals */
        .modal-section-title {
            font-size: 0.8rem;
            font-weight: 800;
            color: #6c757d;
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 5px;
            margin-top: 10px;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
    </style>

    <style>
        .ts-dropdown {
            z-index: 2000 !important;
        }

        .ts-wrapper.form-control {
            overflow: visible !important;
        }

        .ts-dropdown-content {
            z-index: 2001 !important;
        }
    </style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <script>
        const BRAND_URL = "{{ route('brand-api.index') }}";
        const SUPPLIER_URL = "{{ route('mitra-api.index') }}";
        const SUPPLIER_BRAND_URL = "{{ route('supplier-brand-api.index') }}";
        const PRODUCT_URL = "{{ route('product-api.index') }}";
        const CATEGORY_URL = "{{ route('product-category-api.index') }}";
        const NEXT_SKU_URL = "{{ route('product.next-sku-api') }}";
        const COA_URL = "{{ route('coa-api.index') }}";

        let masterSuppliers = [],
            masterBrands = [],
            masterCategories = [],
            masterCOA = [];

        let tsSupplier = null;

        const safeTomSelect = (selector, options) => {
            if (typeof TomSelect !== 'undefined') {
                try {
                    return new TomSelect(selector, options);
                } catch (e) {
                    console.warn('TomSelect init failed for', selector, e);
                    return null;
                }
            }
            return null;
        };

        function initTomSelect() {
            if (typeof TomSelect === 'undefined') {
                console.warn('TomSelect is not loaded. Filters will use default select behavior.');
            }

            tsSupplier = safeTomSelect('#suppliers', {
                plugins: ['remove_button'],
                valueField: 'id',
                labelField: 'nama',
                searchField: 'nama',
                create: false,
                allowEmptyOption: true,
                placeholder: 'Pilih Supplier ...',
                dropdownParent: 'body'
            });
        }

        async function fetchMasterSuppliers() {
            try {
                const res = await fetch(SUPPLIER_URL + '?per_page=1000');
                const result = await res.json();
                if (result.success) {
                    masterSuppliers = result.data.data || result.data;

                    if (tsSupplier) {
                        tsSupplier.clearOptions();
                        tsSupplier.addOptions(masterSuppliers);
                    }
                }
            } catch (error) {
                console.error("Gagal memuat master supplier:", error);
            }
        }

        async function fetchMasterBrands() {
            try {
                const res = await fetch(BRAND_URL);
                const result = await res.json();
                if (result.success) {
                    masterBrands = result.data.data || result.data;
                }
            } catch (error) {
                console.error("Gagal memuat master brand:", error);
            }
        }

        async function fetchMasterCategories() {
            try {
                const res = await fetch(CATEGORY_URL);
                const result = await res.json();
                if (result.success) {
                    masterCategories = result.data.data || result.data;
                }
            } catch (error) {
                console.error("Gagal memuat master kategori:", error);
            }
        }

        async function fetchMasterCOA() {
            try {
                const res = await fetch(COA_URL);
                const result = await res.json();

                if (result.success) {
                    masterCOA = result.data.data || result.data;
                }
            } catch (error) {
                console.error('Gagal memuat master COA:', error);
            }
        }

        document.addEventListener('DOMContentLoaded', async () => {
            initTomSelect();

            await Promise.all([
                fetchMasterSuppliers(),
                fetchMasterBrands(),
                fetchMasterCategories(),
                fetchMasterCOA()
            ]);

            if (typeof loadProductData === 'function') {
                await loadProductData();
            }

            document.getElementById('btnSaveBrand')
                .addEventListener('click', saveBrand);

            // Populate supplier filter dropdown with TomSelect
            setTimeout(() => {
                const supplierFilter = document.getElementById('filter-produk-supplier');
                if (supplierFilter && masterSuppliers.length > 0) {
                    supplierFilter.innerHTML = '<option value="">Semua Supplier</option>';
                    masterSuppliers.forEach(supplier => {
                        supplierFilter.innerHTML += `<option value="${supplier.id}">${supplier.nama}</option>`;
                    });
                    
                    window.tsProductSupplierFilter = new TomSelect(supplierFilter, {
                        create: false,
                        allowEmptyOption: true,
                        placeholder: 'Semua Supplier',
                        dropdownParent: 'body',
                        onChange: function() {
                            if(typeof loadProductData === 'function') loadProductData();
                        }
                    });
                }

                const brandFilter = document.getElementById('filter-produk-brand');
                if (brandFilter && masterBrands.length > 0) {
                    brandFilter.innerHTML = '<option value="">Semua Brand</option>';
                    masterBrands.forEach(brand => {
                        brandFilter.innerHTML += `<option value="${brand.id}">${brand.nama_brand}</option>`;
                    });
                    
                    window.tsProductBrandFilter = new TomSelect(brandFilter, {
                        create: false,
                        allowEmptyOption: true,
                        placeholder: 'Semua Brand',
                        dropdownParent: 'body',
                        onChange: function() {
                            if(typeof loadProductData === 'function') loadProductData();
                        }
                    });
                }

                const kategoriFilter = document.getElementById('filter-produk-kategori');
                if (kategoriFilter && masterCategories.length > 0) {
                    kategoriFilter.innerHTML = '<option value="">Semua Kategori</option>';
                    masterCategories.forEach(kategori => {
                        kategoriFilter.innerHTML += `<option value="${kategori.id}">${kategori.nama_kategori}</option>`;
                    });
                    
                    window.tsProductKategoriFilter = new TomSelect(kategoriFilter, {
                        create: false,
                        allowEmptyOption: true,
                        placeholder: 'Semua Kategori',
                        dropdownParent: 'body',
                        onChange: function() {
                            if(typeof loadProductData === 'function') loadProductData();
                        }
                    });
                }
            }, 1000);
        });
    </script>
@endpush
