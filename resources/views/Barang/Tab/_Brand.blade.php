<div class="tab-pane fade show" id="tab-brand" role="tabpanel">

    <div class="row g-2 mb-3 align-items-end">
        <div class="col-md-3">
            <label class="small fw-bold text-muted">Cari</label>
            <input type="text" id="filter-brand-search" class="form-control form-control-sm"
                placeholder="Ketik pencarian...">
        </div>

        <div class="col-md-auto">
            <button onclick="loadBrandData()" class="btn btn-sm btn-dark px-3">
                <i class="fa fa-filter me-1"></i> Filter
            </button>
        </div>

        <div class="col text-end">
            <button class="btn btn-sm btn-primary px-3 shadow-sm" onclick="openBrandModal()">
                <i class="fa fa-plus me-1"></i> TAMBAH BRAND
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Nama Brand</th>
                    <th width="80" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="brand-table-body">
                <tr>
                    <td colspan="2" class="text-center text-muted">Memuat data...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center px-2">
        <span id="brand-pagination-info" class="text-muted small"></span>
        <nav>
            <ul class="pagination pagination-sm mb-0" id="brand-pagination-container"></ul>
        </nav>
    </div>

</div>

<template id="brand-row-template">
    <tr>
        <td class="brand-name"></td>
        <td class="text-center">
            <div class="dropdown">
                <button class="btn btn-sm btn-light border dropdown-toggle" data-bs-toggle="dropdown">Aksi</button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item btn-edit" href="javascript:void(0)">
                            <i class="fa fa-edit me-2 text-warning"></i> Edit
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item text-danger btn-delete" href="javascript:void(0)">
                            <i class="fa fa-trash me-2"></i> Hapus
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
        });

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

                clone.querySelector('.btn-edit').onclick = () => editBrand(item.id);
                clone.querySelector('.btn-delete').onclick = () => deleteBrand(item.id);

                tbody.appendChild(clone);
            });
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