@extends('Layout.main')

@section('main')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="container-fluid">
                <!-- Premium Header Section -->
                <div class="d-md-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="dr-card-title fs-3 mb-1">Manajemen Barang</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0" style="background: transparent; padding: 0;">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-muted text-decoration-none">Dashboard</a></li>
                                <li class="breadcrumb-item active fw-bold text-primary" aria-current="page">Inventaris Barang</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex gap-2">
                        <!-- Space for global buttons if needed -->
                    </div>
                </div>

                <div class="dr-card p-0 overflow-hidden">
                    <div class="dr-card-header border-bottom px-4 pt-4 pb-0 bg-light-subtle">
                        <ul class="nav nav-tabs border-0" id="barangTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active fw-bold px-4 py-3 border-0 border-bottom border-3" 
                                    id="produk-tab" data-bs-toggle="tab" data-bs-target="#tab-produk" type="button" role="tab">
                                    <i class="iconoir-box-iso me-2"></i> Daftar Produk
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-bold px-4 py-3 border-0 border-bottom border-3" 
                                    id="brand-tab" data-bs-toggle="tab" data-bs-target="#tab-brand" type="button" role="tab">
                                    <i class="iconoir-tag me-2"></i> Brand & Merk
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div class="p-4">
                        <div class="tab-content">
                            {{-- TAB 1: PRODUK --}}
                            @include('Barang.Tab._Produk')

                            {{-- TAB 2: BRAND --}}
                            @include('Barang.Tab._Brand')
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
        /* Specific tweaks for this page that aren't global yet */
        .nav-tabs .nav-link {
            color: var(--dr-text-muted);
            background: transparent;
            font-size: 14px;
            transition: all 0.2s ease;
        }
        .nav-tabs .nav-link:hover {
            color: var(--dr-primary);
            background: rgba(0, 122, 255, 0.05);
        }
        .nav-tabs .nav-link.active {
            color: var(--dr-primary) !important;
            border-color: var(--dr-primary) !important;
            background: transparent !important;
        }

        /* Resizer Handle Styling */
        .resizer {
            position: absolute;
            top: 0;
            right: 0;
            width: 5px;
            cursor: col-resize;
            user-select: none;
            height: 100%;
            z-index: 10;
        }
        .resizer:hover, .resizing {
            border-right: 3px solid var(--dr-primary);
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
                
                // Initialize Excel-like Resizable Columns
                if (typeof initResizableTable === 'function') {
                    initResizableTable();
                }
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
