<div class="tab-pane fade show" id="tab-brand" role="tabpanel">

<<<<<<< Updated upstream
    <div class="row g-2 mb-3">
        <!-- Filter Controls -->
        <div class="col-md-4">
            <label class="small fw-bold text-muted">Cari Brand</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa fa-search"></i></span>
                <input type="text" id="filter-brand-search" class="form-control"
                    placeholder="Nama Brand...">
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="col text-end align-self-end">
            <div class="d-flex gap-2 justify-content-end">
                <button onclick="resetFilterBrand()" class="btn btn-outline-secondary" title="Reset Filter">
                    <i class="fa fa-undo"></i>
                </button>
                <div class="vr"></div>
                <button class="btn btn-primary fw-bold px-4 shadow-sm" onclick="openBrandModal()">
                    <i class="fa fa-plus me-1"></i> TAMBAH BRAND
=======
    <div class="dr-filter-bar px-4 py-3 bg-light-subtle border-bottom gap-3">
        <div class="dr-filter-group" style="flex: 1; min-width: 250px;">
            <label class="dr-label">Cari Brand</label>
            <div class="dr-search-wrap">
                <i class="iconoir-search dr-search-icon"></i>
                <input type="text" id="filter-brand-search" class="dr-input dr-search-input"
                    placeholder="Ketik nama brand...">
            </div>
        </div>
        
        <div class="dr-filter-actions">
            <label class="dr-label" style="visibility: hidden;">-</label>
            <div class="d-flex gap-1">
                <button onclick="resetFilterBrand()" class="dr-btn-icon" title="Reset Filter">
                    <i class="iconoir-undo"></i>
                </button>
                <button class="dr-btn dr-btn-primary ms-2" onclick="openBrandModal()">
                    <i class="iconoir-plus-circle me-1"></i> Tambah Brand
>>>>>>> Stashed changes
                </button>
            </div>
        </div>
    </div>

<<<<<<< Updated upstream
    <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Nama Brand</th>
                    <th width="80" class="text-center">Aksi</th>
=======
    <div class="dr-table-container">
        <table class="dr-table align-middle mb-0" id="table-brand">
            <thead>
                <tr>
                    <th class="ps-4">Nama Brand</th>
                    <th width="80" class="text-end pe-4">Aksi</th>
>>>>>>> Stashed changes
                </tr>
            </thead>
            <tbody id="brand-table-body">
                <tr>
                    <td colspan="2" class="text-center text-muted">Memuat data...</td>
                </tr>
            </tbody>
        </table>
    </div>

<<<<<<< Updated upstream
    <div class="d-flex justify-content-between align-items-center px-2">
        <span id="brand-pagination-info" class="text-muted small"></span>
=======
    <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top bg-white">
        <span id="brand-pagination-info" class="text-muted small fw-bold"></span>
>>>>>>> Stashed changes
        <nav>
            <ul class="pagination pagination-sm mb-0" id="brand-pagination-container"></ul>
        </nav>
    </div>

</div>

<template id="brand-row-template">
    <tr>
<<<<<<< Updated upstream
        <td class="brand-name"></td>
        <td class="text-center">
            <div class="dropdown">
                <button class="btn btn-sm btn-light border shadow-sm dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fa fa-cog text-muted"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li>
                        <a class="dropdown-item btn-edit" href="javascript:void(0)">
                            <i class="fa fa-edit me-2 text-warning"></i> Edit Brand
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger btn-delete" href="javascript:void(0)">
                            <i class="fa fa-trash me-2"></i> Hapus Brand
                        </a>
                    </li>
                </ul>
=======
        <td class="brand-name ps-4 fw-bold text-dark small"></td>
        <td class="text-end pe-4">
            <div class="d-flex justify-content-end gap-1">
                <button class="dr-btn-icon dr-btn-icon-edit btn-edit" title="Edit Brand">
                    <i class="iconoir-edit-pencil"></i>
                </button>
                <button class="dr-btn-icon dr-btn-icon-delete btn-delete" title="Hapus Brand">
                    <i class="iconoir-trash"></i>
                </button>
>>>>>>> Stashed changes
            </div>
        </td>
    </tr>
</template>

@include('Barang.Modal._Brand')

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            loadBrandData(BRAND_URL);
            
            // Auto-filter with debounce
            let brandSearchTimeout = null;
            document.getElementById('filter-brand-search').addEventListener('input', function() {
                clearTimeout(brandSearchTimeout);
                brandSearchTimeout = setTimeout(() => {
                    loadBrandData();
                }, 500);
            });
        });

        function resetFilterBrand() {
            document.getElementById('filter-brand-search').value = '';
            loadBrandData();
        }

        function syncSupplierOptions() {
            if (typeof tsBrandSupplier === 'undefined' || !tsBrandSupplier) return;
            const suppliers = window.masterSuppliers || (typeof masterSuppliers !== 'undefined' ? masterSuppliers : []);
            if (tsBrandSupplier.options.length === 0 && suppliers.length > 0) {
                tsBrandSupplier.addOptions(suppliers);
            }
            tsBrandSupplier.refreshOptions(false);
        }

        async function loadBrandData(url = BRAND_URL) {
            if (typeof url !== 'string') url = BRAND_URL;

            const tbody = document.getElementById('brand-table-body');
            tbody.innerHTML =
                `<tr><td colspan="2" class="text-center py-4"><div class="spinner-border text-primary"></div></td></tr>`;

            try {
                const search = document.getElementById('filter-brand-search').value;
                const fetchUrl = new URL(url);
                if (search) fetchUrl.searchParams.append('search', search);

                const res = await fetch(fetchUrl);
                const result = await res.json();

                if (result.success) {
                    renderBrandTable(result.data.data);
                    renderBrandPagination(result.data);
                }
            } catch (error) {
                console.error('Gagal memuat data brand:', error);
                tbody.innerHTML =
                    `<tr><td colspan="2" class="text-center text-danger">Gagal memuat data brand</td></tr>`;
            }
        }

        function renderBrandTable(data) {
            const tbody = document.getElementById('brand-table-body');
            const template = document.getElementById('brand-row-template');
            tbody.innerHTML = '';

            if (!data || data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="2" class="text-center text-muted">Data brand tidak ditemukan</td></tr>`;
                return;
            }

            data.forEach(item => {
                const clone = template.content.cloneNode(true);
                clone.querySelector('.brand-name').textContent = item.nama_brand;
 
                const btnEdit = clone.querySelector('.btn-edit');
                const btnDelete = clone.querySelector('.btn-delete');
                
                if(btnEdit) btnEdit.onclick = () => editBrand(item.id);
                if(btnDelete) btnDelete.onclick = () => deleteBrand(item.id);
 
                tbody.appendChild(clone);
            });

            // Initialize Draggable Columns for Brand Table
            if (typeof initResizableTable === 'function') {
                initResizableTable('#table-brand');
            }
        }

        function renderBrandPagination(meta) {
            const info = document.getElementById('brand-pagination-info');
            if (info) {
                info.innerText = `Menampilkan ${meta.from || 0} - ${meta.to || 0} dari ${meta.total} data`;
            }

            const container = document.getElementById('brand-pagination-container');
            container.innerHTML = '';

            if (meta.links) {
                meta.links.forEach(link => {
                    const active = link.active ? 'active' : '';
                    const disabled = !link.url ? 'disabled' : '';
                    const label = link.label.includes('Previous') ? '<i class="iconoir-nav-arrow-left"></i>' : (link.label.includes('Next') ? '<i class="iconoir-nav-arrow-right"></i>' : link.label);

                    const li = document.createElement('li');
                    li.className = `page-item ${active} ${disabled}`;
                    li.innerHTML = `<button class="page-link border-0 mx-0 rounded shadow-none fw-bold">${label}</button>`;

                    if (link.url && !link.active) {
                        li.onclick = () => loadBrandData(link.url);
                    }

                    container.appendChild(li);
                });
            }
        }

        // Fungsi editBrand, openBrandModal, dan saveBrand sudah ada di Modal/_Brand.blade.php
    </script>
@endpush