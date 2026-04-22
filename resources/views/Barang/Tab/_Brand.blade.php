<div class="tab-pane fade" id="tab-brand" role="tabpanel">

    <div class="d-flex flex-wrap align-items-end gap-3 mb-4 bg-light-subtle p-3 border-radius-sm border">
        <!-- Filter Controls -->
        <div style="flex: 1; min-width: 250px;">
            <label class="dr-label mb-2">Cari Brand</label>
            <div class="input-group">
                <span class="input-group-text border-end-0 bg-white"><i class="iconoir-search"></i></span>
                <input type="text" id="filter-brand-search" class="dr-input border-start-0" style="padding-left: 0;"
                    placeholder="Ketik nama brand...">
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="ms-auto d-flex gap-2">
            <button onclick="resetFilterBrand()" class="dr-btn dr-btn-outline" title="Reset Filter" style="padding: 10px 14px;">
                <i class="iconoir-undo"></i>
            </button>
            <button class="dr-btn dr-btn-primary px-4" onclick="openBrandModal()">
                <i class="iconoir-plus me-1"></i> TAMBAH BRAND
            </button>
        </div>
    </div>

    <div class="dr-table-container mb-4">
        <table class="dr-table align-middle">
            <thead>
                <tr>
                    <th class="ps-4">Nama Brand</th>
                    <th width="80" class="text-center pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody id="brand-table-body">
                <tr>
                    <td colspan="2" class="text-center text-muted py-5">
                        <div class="spinner-border spinner-border-sm text-primary me-2"></div> Memuat data brand...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center px-2">
        <span id="brand-pagination-info" class="text-muted small fw-medium"></span>
        <nav>
            <ul class="pagination pagination-sm mb-0 gap-1" id="brand-pagination-container"></ul>
        </nav>
    </div>

</div>

<template id="brand-row-template">
    <tr>
        <td class="brand-name ps-4 fw-semibold"></td>
        <td class="text-center pe-4">
            <div class="dropdown">
                <button class="dr-btn dr-btn-outline p-2 shadow-none border-0 bg-light-subtle dropdown-toggle no-caret" data-bs-toggle="dropdown">
                    <i class="iconoir-more-vert fs-5"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2" style="border-radius: 12px; min-width: 160px;">
                    <li>
                        <a class="dropdown-item btn-edit d-flex align-items-center gap-2 py-2 px-3 rounded-2" href="javascript:void(0)">
                            <i class="iconoir-edit-pencil text-warning"></i> 
                            <span class="fw-semibold" style="font-size: 13px;">Edit Brand</span>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider opacity-50"></li>
                    <li>
                        <a class="dropdown-item text-danger btn-delete d-flex align-items-center gap-2 py-2 px-3 rounded-2" href="javascript:void(0)">
                            <i class="iconoir-trash"></i>
                            <span class="fw-semibold" style="font-size: 13px;">Hapus Brand</span>
                        </a>
                    </li>
                </ul>
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
            if (!tsSupplier) return;
            if (tsSupplier.options.length === 0 && masterSuppliers.length > 0) {
                tsSupplier.addOptions(masterSuppliers);
            }
            tsSupplier.refreshOptions(false);
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
                initResizableTable();
            }
        }

        function renderBrandPagination(meta) {
            const info = document.getElementById('brand-pagination-info');
            if (info) {
                info.innerText = `Menampilkan ${meta.from || 0} ke ${meta.to || 0} dari ${meta.total} data`;
            }

            const container = document.getElementById('brand-pagination-container');
            container.innerHTML = '';

            if (meta.links) {
                meta.links.forEach(link => {
                    const active = link.active ? 'active' : '';
                    const disabled = !link.url ? 'disabled' : '';
                    const label = link.label.replace('&laquo;', '').replace('&raquo;', '');

                    const li = document.createElement('li');
                    li.className = `page-item ${active} ${disabled}`;
                    li.innerHTML = `<a class="page-link shadow-none" href="javascript:void(0)">${label}</a>`;

                    if (link.url && !link.active) {
                        li.onclick = () => loadBrandData(link.url);
                    }

                    container.appendChild(li);
                });
            }
        }

        async function editBrand(id) {
            const form = document.getElementById('formBrand');
            form.reset();
            document.getElementById('brandModalTitle').innerText = 'Edit Brand';
            document.getElementById('brandId').value = id;

            try {
                const res = await fetch(`${BRAND_URL}/${id}`);
                const result = await res.json();

                if (result.success) {
                    const brand = result.data;
                    document.getElementById('nama_brand').value = brand.nama_brand;

                    if (tsSupplier) {
                        tsSupplier.clear();
                        syncSupplierOptions();
                        if (brand.suppliers && Array.isArray(brand.suppliers)) {
                            const ids = brand.suppliers.map(s => String(s.id));
                            tsSupplier.setValue(ids);
                        }
                    }

                    new bootstrap.Modal(document.getElementById('modalBrand')).show();
                }
            } catch (error) {
                console.error('Edit error:', error);
                alert('Terjadi kesalahan saat memuat data brand.');
            }
        }

        async function deleteBrand(id) {
            if (!await macConfirm('Hapus brand', 'Yakin ingin menghapus brand ini?')) return;

            try {
                const res = await fetch(`${BRAND_URL}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                            'content') || ''
                    }
                });
                const result = await res.json();

                if (result.success) {
                    loadBrandData();
                } else {
                    alert('Gagal: ' + result.message);
                }
            } catch (error) {
                console.error('Delete error:', error);
            }
        }

        async function loadSupplier() {
            if (!tsSupplier) return;

            try {
                const res = await fetch(SUPPLIER_URL + '?per_page=1000');
                const result = await res.json();

                if (!result.success) return;

                const data = result.data.data || result.data;

                tsSupplier.clearOptions();
                tsSupplier.addOptions(data);

                tsSupplier.refreshOptions(false);
            } catch (error) {
                console.error('Gagal load supplier: ', error);
            }
        }

        async function openBrandModal() {
            document.getElementById('formBrand').reset();
            document.getElementById('brandId').value = '';
            document.getElementById('brandModalTitle').innerText = 'Tambah Brand Baru';

            if (tsSupplier) {
                tsSupplier.clear();
                syncSupplierOptions();
            }

            new bootstrap.Modal(document.getElementById('modalBrand')).show();
        }

        async function saveBrand() {
            const btn = document.getElementById('btnSaveBrand');
            const brandId = document.getElementById('brandId').value;
            const brandName = document.getElementById('nama_brand').value;
            const selectedSuppliers = tsSupplier.getValue();

            if (!brandName) {
                alert('Nama Brand wajib diisi!');
                return;
            }

            const url = brandId ? `${BRAND_URL}/${brandId}` : BRAND_URL;
            const method = brandId ? 'PUT' : 'POST';
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                'content') || '';

            const payload = {
                nama_brand: brandName,
                supplier_ids: selectedSuppliers
            };

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

            try {
                const res = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify(payload)
                });

                const result = await res.json();

                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modalBrand')).hide();
                    alert('Data berhasil disimpan!');
                    loadBrandData();
                } else {
                    alert('Gagal: ' + (result.message || 'Terjadi kesalahan'));
                }
            } catch (error) {
                console.error('Save error:', error);
                alert('Terjadi kesalahan sistem saat menyimpan.');
            } finally {
                btn.disabled = false;
                btn.innerText = 'Simpan';
            }
        }
    </script>
@endpush