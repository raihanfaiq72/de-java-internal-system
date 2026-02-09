<div class="tab-pane fade show active" id="tab-produk" role="tabpanel">

    <div class="row g-2 mb-3 align-items-end">
        <div class="col-md-3">
            <label class="small fw-bold text-muted">Cari Barang</label>
            <input type="text" id="filter-produk-search" class="form-control form-control-sm"
                placeholder="Ketik pencarian...">
        </div>

        <div class="col-md-auto">
            <button onclick="loadProductData()" class="btn btn-sm btn-dark px-3">
                <i class="fa fa-filter me-1"></i> Filter
            </button>
        </div>

        <div class="col text-end">
            <button class="btn btn-sm btn-primary px-3 shadow-sm" onclick="tambahProduk()">
                <i class="fa fa-plus me-1"></i> TAMBAH PRODUK
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th width="120">SKU</th>
                    <th>Supplier</th>
                    <th>Brand</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Kemasan</th>
                    <th>Satuan</th>
                    <th>Harga Beli</th>
                    <th>Harga Jual</th>
                    <th>COA</th>
                    <th width="80" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="produk-table-body">
                <tr>
                    <td colspan="11" class="text-center text-muted">Memuat data...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center px-2">
        <span id="produk-pagination-info" class="text-muted small"></span>
        <nav>
            <ul class="pagination pagination-sm mb-0" id="produk-pagination-container"></ul>
        </nav>
    </div>

</div>

<template id="produk-row-inline-template">
    <tr class="bg-primary bg-opacity-10 border-primary">
        <td>
            <input type="text" class="form-control form-control-sm in-sku" disabled>
        </td>
        <td>
            <select class="in-supplier"></select>
        </td>
        <td>
            <select class="in-brand"></select>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm in-nama">
        </td>
        <td>
            <select class="in-kategori"></select>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm in-kemasan">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm in-satuan">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm in-beli" value="0">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm in-jual" value="0">
        </td>
        <td>
            <select class="in-coa"></select>
        </td>
        <td class="text-center">
            <div class="d-flex gap-1 justify-content-center">
                <button class="btn btn-xs btn-success btn-save-inline" title="Simpan">
                    <i class="fa fa-check"></i>
                </button>
                <button class="btn btn-xs btn-danger btn-cancel-inline" title="Batal">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </td>
    </tr>
</template>

<template id="produk-row-display-template">
    <tr>
        <td class="col-sku fw-bold text-primary"></td>
        <td class="col-supplier"></td>
        <td class="col-brand"></td>
        <td class="col-nama"></td>
        <td class="col-kategori"></td>
        <td class="col-kemasan text-center"></td>
        <td class="col-satuan text-center"></td>
        <td class="col-beli text-end"></td>
        <td class="col-jual text-end"></td>
        <td class="col-coa text-start"></td>
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
                        <a class="dropdown-item btn-delete text-danger" href="javascript:void(0)">
                            <i class="fa fa-trash me-2"></i> Hapus
                        </a>
                    </li>
                </ul>
            </div>
        </td>
    </tr>
</template>

@push('css')
    <style>
        .btn-xs {
            padding: 0.25rem 0.4rem;
            font-size: 0.75rem;
            line-height: 1;
            border-radius: 0.2rem;
        }

        .ts-control {
            min-height: 31px !important;
            padding: 2px 8px !important;
            font-size: 12px !important;
        }
    </style>
@endpush

@include('Barang.Modal._Produk')

@push('js')
    <script>
        function formatRupiahInput(el) {
            let val = el.value.replace(/[^0-9]/g, '');

            if (val === "") {
                el.value = "";
                return;
            }

            el.value = new Intl.NumberFormat('id-ID').format(val);
        }

        async function tambahProduk() {
            await Promise.all([
                fetchMasterSuppliers(),
                fetchMasterBrands(),
                fetchMasterCategories(),
                fetchMasterCOA()
            ]);

            const tbody = document.getElementById('produk-table-body');
            const template = document.getElementById('produk-row-inline-template');

            if (document.querySelector('.in-sku')) return;

            const clone = template.content.cloneNode(true);
            const tr = clone.querySelector('tr');

            tbody.prepend(tr);

            const inBeli = tr.querySelector('.in-beli');
            const inJual = tr.querySelector('.in-jual');

            inBeli.addEventListener('input', function() {
                formatRupiahInput(this);
            });
            inJual.addEventListener('input', function() {
                formatRupiahInput(this);
            });

            const tsProductBrand = new TomSelect(tr.querySelector('.in-brand'), {
                options: masterBrands.map(b => ({
                    value: b.id,
                    text: b.nama_brand
                })),
                placeholder: 'Brand...',
                dropdownParent: 'body'
            });

            const tsProductSupplier = new TomSelect(tr.querySelector('.in-supplier'), {
                options: masterSuppliers.map(s => ({
                    value: s.id,
                    text: s.nama
                })),
                placeholder: 'Supplier...',
                dropdownParent: 'body',
                onChange: function(supplierId) {
                    if (!tsProductBrand) return;

                    tsProductBrand.clear();
                    tsProductBrand.clearOptions();

                    if (!supplierId) {
                        const allOptions = masterBrands.map(b => ({
                            value: b.id,
                            text: b.nama_brand
                        }));
                        tsProductBrand.addOptions(allOptions);
                    } else {
                        const filteredBrands = masterBrands.filter(brand => {
                            return brand.suppliers && brand.suppliers.some(s => String(s.id) ===
                                String(supplierId))
                        })
                        const newOptions = filteredBrands.map(b => ({
                            value: b.id,
                            text: b.nama_brand
                        }));
                        tsProductBrand.addOptions(newOptions);
                    }

                    tsProductBrand.refreshOptions(false);
                }
            });

            const tsProductKategori = new TomSelect(tr.querySelector('.in-kategori'), {
                options: masterCategories.map(c => ({
                    value: c.id,
                    text: c.nama_kategori
                })),
                placeholder: 'Kategori...',
                dropdownParent: 'body',
                create: function(input, callback) {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                        'content') || '';

                    fetch(CATEGORY_URL, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token
                            },
                            body: JSON.stringify({
                                nama_kategori: input
                            })
                        }).then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                masterCategories.push(result.data);

                                callback({
                                    value: result.data.id,
                                    text: result.data.nama_kategori
                                });

                                console.log('Kategori baru berhasil disimpan:', result.data
                                    .nama_kategori);
                            } else {
                                alert('Gagal menyimpan kategori baru: ' + result.message);
                                callback(false);
                            }
                        })
                        .catch(err => {
                            console.error('Error creating category:', err);
                            callback(false);
                        });
                }
            });

            const tsCOA = new TomSelect(tr.querySelector('.in-coa'), {
                options: masterCOA.map(c => ({
                    value: c.id,
                    text: c.nama_akun
                })),
                placeholder: 'COA...',
                create: false,
                dropdownParent: 'body'
            });

            const skuInput = tr.querySelector('.in-sku');
            skuInput.value = '...';
            const res = await fetch(NEXT_SKU_URL);
            const result = await res.json();
            if (result.success) skuInput.value = result.data;

            tr.querySelector('.btn-cancel-inline').onclick = () => tr.remove();

            tr.querySelector('.btn-save-inline').onclick = async () => {
                const payload = {
                    sku_kode: skuInput.value,
                    nama_produk: tr.querySelector('.in-nama').value,
                    supplier_id: tsProductSupplier.getValue(),
                    brand_id: tsProductBrand.getValue(),
                    product_category_id: tsProductKategori.getValue(),
                    kemasan: tr.querySelector('.in-kemasan').value,
                    satuan: tr.querySelector('.in-satuan').value,
                    harga_beli: inBeli.value.replace(/\./g, ''),
                    harga_jual: inJual.value.replace(/\./g, ''),
                    track_stock: 1,
                    akun_penjualan_id: 1,
                    akun_pembelian_id: 1,
                    coa_id: tsCOA.getValue()
                };

                if (!payload.nama_produk || !payload.sku_kode) {
                    alert('Nama dan SKU wajib diisi');
                    return;
                }

                try {
                    const res = await fetch(PRODUCT_URL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                ?.getAttribute('content')
                        },
                        body: JSON.stringify(payload)
                    });

                    const result = await res.json();
                    if (result.success) {
                        alert('Produk berhasil ditambahkan');
                        loadProductData();
                    } else {
                        alert('Gagal: ' + result.message);
                    }
                } catch (e) {
                    console.error(e);
                }
            };
        }

        async function editProduk(id) {
            const res = await fetch(`${PRODUCT_URL}/${id}`);
            const result = await res.json();
            if (!result.success) return alert('Gagal mengambil data produk');

            const item = result.data;

            const oldRow = document.getElementById(`row-product-${id}`);
            if (!oldRow) return;

            const template = document.getElementById('produk-row-inline-template');
            const clone = template.content.cloneNode(true);
            const editRow = clone.querySelector('tr');

            const tsBrand = new TomSelect(editRow.querySelector('.in-brand'), {
                options: masterBrands.map(b => ({
                    value: b.id,
                    text: b.nama_brand
                })),
                placeholder: 'Brand...',
                dropdownParent: 'body'
            });

            const tsSupplier = new TomSelect(editRow.querySelector('.in-supplier'), {
                options: masterSuppliers.map(s => ({
                    value: s.id,
                    text: s.nama
                })),
                placeholder: 'Supplier...',
                dropdownParent: 'body',
                onChange: function(supplierId) {
                    tsBrand.clear();
                    tsBrand.clearOptions();
                    const filtered = masterBrands.filter(brand =>
                        !supplierId || (brand.suppliers && brand.suppliers.some(s => String(s.id) ===
                            String(supplierId)))
                    );
                    tsBrand.addOptions(filtered.map(b => ({
                        value: b.id,
                        text: b.nama_brand
                    })));
                }
            });

            const tsKategori = new TomSelect(editRow.querySelector('.in-kategori'), {
                options: masterCategories.map(c => ({
                    value: c.id,
                    text: c.nama_kategori
                })),
                create: function(input, callback) {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                        'content') || '';

                    fetch(CATEGORY_URL, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token
                            },
                            body: JSON.stringify({
                                nama_kategori: input
                            })
                        }).then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                masterCategories.push(result.data);

                                callback({
                                    value: result.data.id,
                                    text: result.data.nama_kategori
                                });

                                console.log('Kategori baru berhasil disimpan:', result.data
                                    .nama_kategori);
                            } else {
                                alert('Gagal menyimpan kategori baru: ' + result.message);
                                callback(false);
                            }
                        })
                        .catch(err => {
                            console.error('Error creating category:', err);
                            callback(false);
                        });
                },
                dropdownParent: 'body'
            });

            const tsCOA = new TomSelect(editRow.querySelector('.in-coa'), {
                options: masterCOA.map(c => ({
                    value: c.id,
                    text: c.nama_akun
                })),
                dropdownParent: 'body'
            });

            editRow.querySelector('.in-sku').value = item.sku_kode;
            editRow.querySelector('.in-nama').value = item.nama_produk;
            editRow.querySelector('.in-kemasan').value = item.kemasan || '';
            editRow.querySelector('.in-satuan').value = item.satuan || '';
            editRow.querySelector('.in-beli').value = item.harga_beli || 0;
            editRow.querySelector('.in-jual').value = item.harga_jual || 0;

            tsSupplier.setValue(item.supplier_id);
            tsBrand.setValue(item.brand_id);
            tsKategori.setValue(item.product_category_id);
            tsCOA.setValue(item.coa_id);

            const inBeliEdit = editRow.querySelector('.in-beli');
            const inJualEdit = editRow.querySelector('.in-jual');

            inBeliEdit.addEventListener('input', function() {
                formatRupiahInput(this);
            });
            inJualEdit.addEventListener('input', function() {
                formatRupiahInput(this);
            });

            inBeliEdit.value = new Intl.NumberFormat('id-ID').format(item.harga_beli || 0);
            inJualEdit.value = new Intl.NumberFormat('id-ID').format(item.harga_jual || 0);

            oldRow.replaceWith(editRow);

            editRow.querySelector('.btn-cancel-inline').onclick = () => loadProductData();

            editRow.querySelector('.btn-save-inline').onclick = async () => {
                const payload = {
                    sku_kode: item.sku_kode,
                    nama_produk: editRow.querySelector('.in-nama').value,
                    supplier_id: tsSupplier.getValue(),
                    brand_id: tsBrand.getValue(),
                    product_category_id: tsKategori.getValue(),
                    kemasan: editRow.querySelector('.in-kemasan').value,
                    satuan: editRow.querySelector('.in-satuan').value,
                    harga_beli: inBeliEdit.value.replace(/\./g, ''),
                    harga_jual: inJualEdit.value.replace(/\./g, ''),
                    coa_id: tsCOA.getValue(),
                    track_stock: 1
                };

                try {
                    const saveRes = await fetch(`${PRODUCT_URL}/${id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                ?.getAttribute('content')
                        },
                        body: JSON.stringify(payload)
                    });

                    const saveResult = await saveRes.json();
                    if (saveResult.success) {
                        alert('Produk berhasil diperbarui');
                        loadProductData();
                    } else {
                        alert('Gagal memperbarui: ' + saveResult.message);
                    }
                } catch (e) {
                    console.error(e);
                }
            };
        }

        async function hapusProduk(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus produk ini? Tindakan ini tidak dapat dibatalkan.')) return;

            try {
                const res = await fetch(`${PRODUCT_URL}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                            'content') || ''
                    }
                });

                const result = await res.json();

                if (result.success) {
                    const row = document.getElementById(`row-product-${id}`);
                    if (row) {
                        row.style.transition = '0.3s';
                        row.style.opacity = '0';
                        row.style.backgroundColor = '#fee2e2';

                        setTimeout(() => {
                            row.remove();
                            loadProductData();
                        }, 300);
                    }

                    alert('Produk berhasil dihapus.');
                } else {
                    alert('Gagal menghapus: ' + (result.message || 'Terjadi kesalahan server'));
                }
            } catch (error) {
                console.error('Delete error:', error);
                alert('Terjadi kesalahan sistem saat mencoba menghapus data.');
            }
        }

        function renderProductTable(data) {
            const tbody = document.getElementById('produk-table-body');
            const template = document.getElementById('produk-row-display-template');
            tbody.innerHTML = '';

            if (!data || data.length === 0) {
                tbody.innerHTML =
                    `<tr><td colspan="12" class="text-center text-muted py-4">Data produk tidak ditemukan</td></tr>`;
                return;
            }

            data.forEach(item => {
                const clone = template.content.cloneNode(true);
                const tr = clone.querySelector('tr');
                tr.id = `row-product-${item.id}`;

                const coaLookup = masterCOA.find(c => String(c.id) === String(item.coa_id));
                const coaName = coaLookup ? coaLookup.nama_akun : '-';

                // Mengisi Konten Baris
                clone.querySelector('.col-sku').textContent = item.sku_kode || '-';
                clone.querySelector('.col-supplier').textContent = item.supplier?.nama || '-';
                clone.querySelector('.col-brand').textContent = item.brand?.nama_brand || '-';
                clone.querySelector('.col-nama').textContent = item.nama_produk || '-';
                clone.querySelector('.col-kategori').textContent = item.category?.nama_kategori || '-';
                clone.querySelector('.col-kemasan').textContent = item.kemasan || '-';
                clone.querySelector('.col-satuan').textContent = item.satuan || '-';
                clone.querySelector('.col-beli').textContent = formatIDR(item.harga_beli);
                clone.querySelector('.col-jual').textContent = formatIDR(item.harga_jual);
                clone.querySelector('.col-coa').textContent = coaName;

                // Memasang Event Klik
                clone.querySelector('.btn-edit').onclick = (e) => {
                    e.preventDefault();
                    editProduk(item.id);
                };
                clone.querySelector('.btn-delete').onclick = (e) => {
                    e.preventDefault();
                    hapusProduk(item.id);
                };

                tbody.appendChild(clone);
            });
        }

        function renderProductPagination(meta) {
            document.getElementById('produk-pagination-info').innerText =
                `Menampilkan ${meta.from || 0} ke ${meta.to || 0} dari ${meta.total} data`;

            const container = document.getElementById('produk-pagination-container');
            container.innerHTML = '';

            meta.links.forEach(link => {
                const active = link.active ? 'active' : '';
                const disabled = !link.url ? 'disabled' : '';
                const label = link.label.replace('&laquo;', '').replace('&raquo;', '');

                container.insertAdjacentHTML('beforeend', `
                <li class="page-item ${active} ${disabled}">
                    <a class="page-link shadow-none"
                       href="javascript:void(0)"
                       onclick="loadProductData('${link.url}')">
                        ${label}
                    </a>
                </li>
            `);
            });
        }

        async function loadProductData(url = PRODUCT_URL) {
            if (typeof url !== 'string') url = PRODUCT_URL;

            const tbody = document.getElementById('produk-table-body');
            tbody.innerHTML = `
            <tr>
                <td colspan="12" class="text-center py-4">
                    <div class="spinner-border text-primary"></div>
                </td>
            </tr>`;

            try {
                const search = document.getElementById('filter-produk-search').value;

                const fetchUrl = new URL(url);
                if (search) fetchUrl.searchParams.append('search', search);

                const res = await fetch(fetchUrl);
                const result = await res.json();

                if (result.success) {
                    renderProductTable(result.data.data);
                    renderProductPagination(result.data);
                }
            } catch (error) {
                console.error('Fetch error:', error);
                tbody.innerHTML = `
                <tr>
                    <td colspan="12" class="text-center text-danger">
                        Gagal memuat data
                    </td>
                </tr>`;
            }
        }

        const formatIDR = (val) => new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(val);
    </script>
    {{-- <script>
        const API_URL = 'http://localhost:8000/api/product-api';
        const CATEGORY_API_URL = 'http://localhost:8000/api/product-categories-api';
        const UNIT_CATEGORY_API = 'http://localhost:8000/api/unit-categories-api';
        const UNIT_API = 'http://localhost:8000/api/unit-api';
        const NEXT_SKU_API = 'http://localhost:8000/api/product-next-sku-api';

        document.addEventListener('DOMContentLoaded', () => {
            loadKategoriProduk();
            loadProductData();
        });

        const formatIDR = (val) => new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(val);

        async function loadKategoriProduk() {
            const select = document.getElementById('filter-produk-kategori');

            try {
                const res = await fetch(CATEGORY_API_URL);
                const result = await res.json();

                if (!result.success) return;


                select.innerHTML = `<option value="">Semua Kategori</option>`;
                result.data.data.forEach(item => {
                    select.insertAdjacentHTML(
                        'beforeend',
                        `<option value="${item.id}">${item.nama_kategori}</option>`
                    );
                });

                if (tomSelectFilterKategoriProduk) {
                    tomSelectFilterKategoriProduk.sync();
                }
            } catch (error) {
                console.error('Gagal load kategori:', error);
            }
        }

        async function loadKategoriProdukModal() {
            const select = document.getElementById('produk-kategori');

            try {
                const res = await fetch(CATEGORY_API_URL);
                const result = await res.json();

                if (!result.success) return;

                select.innerHTML = `<option value="">-- Pilih --</option>`;
                result.data.data.forEach(item => {
                    select.insertAdjacentHTML(
                        'beforeend',
                        `<option value="${item.id}">${item.nama_kategori}</option>`
                    );
                });

                if (tomSelectProdukKategori) {
                    tomSelectProdukKategori.sync();
                }
            } catch (error) {
                console.error("Gagal memuat opsi kategori", e);
            }
        }

        async function loadUnitCategoryModal() {
            const select = document.getElementById('produk-unit-category');

            try {
                const res = await fetch(UNIT_CATEGORY_API);
                const result = await res.json();

                if (!result.success) return;
                select.innerHTML = `<option value="">-- Pilih --</option>`;

                const data = result.data.data ?? result.data;
                data.forEach(item => {
                    select.insertAdjacentHTML(
                        'beforeend',
                        `<option value="${item.id}">${item.nama_kategori}</option>`
                    );
                });

                if (tomSelectProdukUnitKategori) {
                    tomSelectProdukUnitKategori.sync();
                }
            } catch (error) {
                console.error("Gagal memuat opsi", e);
            }
        }

        document.getElementById('produk-unit-category')
            ?.addEventListener('change', loadUnitByCategory);

        async function loadUnitByCategory() {
            const categoryId = this.value;

            if (!tomSelectProdukUnit) return;

            if (!categoryId || categoryId === '') {
                tomSelectProdukUnit.clear(true);
                tomSelectProdukUnit.clearOptions();
                tomSelectProdukUnit.enable();
                return;
            }

            tomSelectProdukUnit.disable();
            tomSelectProdukUnit.clearOptions();
            tomSelectProdukUnit.addOption({
                value: 'loading_state',
                text: 'Loading...'
            });
            tomSelectProdukUnit.setValue('loading_state');

            try {
                const res = await fetch(`${UNIT_API}?unit_category_id=${categoryId}`);
                const result = await res.json();

                tomSelectProdukUnit.clear(true);
                tomSelectProdukUnit.clearOptions();

                if (result.success) {
                    const data = result.data.data ?? result.data;
                    data.forEach(item => {
                        tomSelectProdukUnit.addOption({
                            value: item.id,
                            text: item.nama_unit
                        });
                    });
                }

                tomSelectProdukUnit.enable();
            } catch (error) {
                console.error("Gagal memuat opsi", e);
            }
        }

        async function loadProductData(url = API_URL) {
            if (typeof url !== 'string') url = API_URL;

            const tbody = document.getElementById('produk-table-body');
            tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4">
                    <div class="spinner-border text-primary"></div>
                </td>
            </tr>`;

            try {
                const search = document.getElementById('filter-produk-search').value;
                const kategori = document.getElementById('filter-produk-kategori').value;

                const fetchUrl = new URL(url);
                if (search) fetchUrl.searchParams.append('search', search);
                if (kategori) fetchUrl.searchParams.append('category', kategori);

                const res = await fetch(fetchUrl);
                const result = await res.json();

                if (result.success) {
                    renderProductTable(result.data.data);
                    renderProductPagination(result.data);
                }
            } catch (error) {
                tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center text-danger">
                        Gagal memuat data
                    </td>
                </tr>`;
            }
        }

        function renderProductTable(data) {
            const tbody = document.getElementById('produk-table-body');
            tbody.innerHTML = '';

            if (!data || data.length === 0) {
                tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center text-muted">
                        Data tidak ditemukan
                    </td>
                </tr>`;
                return;
            }

            data.forEach(item => {
                tbody.insertAdjacentHTML('beforeend', `
                <tr>
                    <td class="fw-bold">${item.sku_kode}</td>
                    <td>${item.nama_produk}</td>
                    <td>${item.category?.nama_kategori ?? '-'}</td>
                    <td>${item.unit?.nama_unit ?? '-'}</td>
                    <td>${formatIDR(item.harga_beli) ?? '-'}</td>
                    <td>${formatIDR(item.harga_jual) ?? '-'}</td>
                    <td class="text-center">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light border dropdown-toggle"
                                data-bs-toggle="dropdown">Aksi</button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)"
                                       onclick="detailProduk(${item.id})">
                                        <i class="fa fa-eye me-2 text-primary"></i> Detail
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)"
                                       onclick="editProduk(${item.id})">
                                        <i class="fa fa-edit me-2 text-warning"></i> Edit
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)"
                                       onclick="recalculateStock(${item.id})">
                                        <i class="fa fa-sync me-2 text-info"></i> Rekalkulasi Stok
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="javascript:void(0)"
                                       onclick="hapusProduk(${item.id})">
                                        <i class="fa fa-trash me-2"></i> Hapus
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            `);
            });
        }

        function renderProductPagination(meta) {
            document.getElementById('produk-pagination-info').innerText =
                `Menampilkan ${meta.from || 0} ke ${meta.to || 0} dari ${meta.total} data`;

            const container = document.getElementById('produk-pagination-container');
            container.innerHTML = '';

            meta.links.forEach(link => {
                const active = link.active ? 'active' : '';
                const disabled = !link.url ? 'disabled' : '';
                const label = link.label.replace('&laquo;', '').replace('&raquo;', '');

                container.insertAdjacentHTML('beforeend', `
                <li class="page-item ${active} ${disabled}">
                    <a class="page-link shadow-none"
                       href="javascript:void(0)"
                       onclick="loadProductData('${link.url}')">
                        ${label}
                    </a>
                </li>
            `);
            });
        }
        async function recalculateStock(id) {
            if (!confirm('Apakah Anda yakin ingin menghitung ulang stok produk ini?')) return;

            try {
                const res = await fetch(`${API_URL}/${id}/recalculate-stock`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                        // CSRF excluded for API
                    }
                });
                const result = await res.json();

                if (result.success) {
                    alert('Stok berhasil direkalkulasi. Stok baru: ' + result.data.qty);
                    loadProductData(); // Reload table
                } else {
                    alert('Gagal: ' + result.message);
                }
            } catch (error) {
                console.error(error);
                alert('Terjadi kesalahan saat menghubungi server');
            }
        }
    </script>

    <script>
        let deleteProdukId = null;

        function toggleTrackStock() {
            const isChecked = document.getElementById('produk-track-stock').checked;
            const stockFields = document.querySelectorAll('.stock-field');

            stockFields.forEach(el => {
                el.style.display = isChecked ? 'block' : 'none';
            });

            if (!isChecked) {
                document.getElementById('produk-qty').value = '';
            }
        }

        async function generateSku() {
            const skuInput = document.getElementById('produk-sku');
            skuInput.value = 'Loading...';
            skuInput.disabled = true;

            try {
                const res = await fetch(NEXT_SKU_API);
                const result = await res.json();

                if (result.success) {
                    skuInput.value = result.data;
                } else {
                    skuInput.value = '';
                    alert('Gagal generate SKU');
                }
            } catch (e) {
                console.error(e);
                skuInput.value = '';
            } finally {
                skuInput.disabled = false;
            }
        }

        async function tambahProduk() {
            document.getElementById('modalProdukTitle').innerText = 'Tambah Produk';
            document.getElementById('btn-simpan-produk').style.display = 'block';
            setModalFieldsDisabled(false);

            document.getElementById('produk-id').value = '';
            document.getElementById('produk-sku').value = '';
            document.getElementById('produk-nama').value = '';
            document.getElementById('produk-harga-beli').value = '';
            document.getElementById('produk-harga-jual').value = '';
            document.getElementById('produk-qty').value = '';
            document.getElementById('produk-deskripsi').value = '';

            // Reset track stock
            document.getElementById('produk-track-stock').checked = false;
            toggleTrackStock();

            // Auto generate SKU
            await generateSku();

            await loadKategoriProdukModal();
            await loadUnitCategoryModal();

            document.getElementById('produk-unit').innerHTML =
                `<option value="">-- Pilih --</option>`;
            document.getElementById('produk-unit').disabled = true;

            new bootstrap.Modal('#modalProduk').show();
        }

        async function detailProduk(id) {
            const res = await fetch(`${API_URL}/${id}`);
            const result = await res.json();

            if (!result.success) return alert(result.message);

            const p = result.data;

            document.getElementById('modalProdukTitle').innerText = 'Detail Produk';
            document.getElementById('btn-simpan-produk').style.display = 'none';
            setModalFieldsDisabled(true);

            document.getElementById('produk-id').value = p.id;
            document.getElementById('produk-sku').value = p.sku_kode;
            document.getElementById('produk-nama').value = p.nama_produk;

            await loadKategoriProdukModal();
            document.getElementById('produk-kategori').value = p.product_category_id;

            await loadUnitCategoryModal();
            document.getElementById('produk-unit-category').value = p.unit_category_id;

            // Load units for this category manually to set value
            await loadUnitByCategoryManually(p.unit_category_id);
            document.getElementById('produk-unit').value = p.unit_id;

            document.getElementById('produk-harga-beli').value = p.harga_beli;
            document.getElementById('produk-harga-jual').value = p.harga_jual;
            document.getElementById('produk-qty').value = p.qty;
            document.getElementById('produk-deskripsi').value = p.deskripsi_produk;

            new bootstrap.Modal('#modalProduk').show();
        }

        async function editProduk(id) {
            const res = await fetch(`${API_URL}/${id}`);
            const result = await res.json();

            if (!result.success) return alert(result.message);

            const p = result.data;

            document.getElementById('modalProdukTitle').innerText = 'Edit Produk';
            document.getElementById('btn-simpan-produk').style.display = 'block';
            setModalFieldsDisabled(false);

            document.getElementById('produk-id').value = p.id;
            document.getElementById('produk-sku').value = p.sku_kode;
            document.getElementById('produk-nama').value = p.nama_produk;

            await loadKategoriProdukModal();
            document.getElementById('produk-kategori').value = p.product_category_id;

            await loadUnitCategoryModal();
            document.getElementById('produk-unit-category').value = p.unit_category_id;

            // Load units for this category manually to set value
            await loadUnitByCategoryManually(p.unit_category_id);
            document.getElementById('produk-unit').value = p.unit_id;

            document.getElementById('produk-harga-beli').value = p.harga_beli;
            document.getElementById('produk-harga-jual').value = p.harga_jual;
            document.getElementById('produk-qty').value = p.qty;
            document.getElementById('produk-deskripsi').value = p.deskripsi_produk;

            new bootstrap.Modal('#modalProduk').show();
        }

        function setModalFieldsDisabled(disabled) {
            const fields = [
                'produk-sku', 'produk-nama', 'produk-kategori',
                'produk-unit-category', 'produk-unit', 'produk-harga-beli',
                'produk-harga-jual', 'produk-qty', 'produk-deskripsi',
                'produk-track-stock'
            ];
            fields.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.disabled = disabled;
            });
        }

        async function loadUnitByCategoryManually(categoryId) {
            const unitSelect = document.getElementById('produk-unit');
            if (!categoryId || !tomSelectProdukUnit) return;

            const res = await fetch(`${UNIT_API}?unit_category_id=${categoryId}`);
            const result = await res.json();

            if (!result.success) return;

            tomSelectProdukUnit.clearOptions();

            unitSelect.innerHTML = `<option value="">-- Pilih --</option>`;

            const data = result.data.data ?? result.data;
            data.forEach(item => {
                tomSelectProdukUnit.addOption({
                    value: item.id,
                    text: item.nama_unit
                });
            });

            tomSelectProdukUnit.enable();
            tomSelectProdukUnit.sync();
        }
        async function submitProduk() {
            const id = document.getElementById('produk-id').value;

            const payload = {
                sku_kode: document.getElementById('produk-sku').value,
                nama_produk: document.getElementById('produk-nama').value,
                product_category_id: document.getElementById('produk-kategori').value,
                unit_category_id: document.getElementById('produk-unit-category').value,
                unit_id: document.getElementById('produk-unit').value,
                harga_beli: document.getElementById('produk-harga-beli').value,
                harga_jual: document.getElementById('produk-harga-jual').value,
                qty: document.getElementById('produk-qty').value,
                deskripsi_produk: document.getElementById('produk-deskripsi').value,
                track_stock: document.getElementById('produk-track-stock').checked ? 1 : 0,
                akun_penjualan_id: 1,
                akun_pembelian_id: 1,
                akun_diskon_penjualan_id: 1,
                akun_diskon_pembelian_id: 1,
            };

            const url = id ? `${API_URL}/${id}` : API_URL;
            const method = id ? 'PUT' : 'POST';

            const res = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const result = await res.json();

            if (!result.success) {
                alert(result.message);
                return;
            }

            bootstrap.Modal.getInstance(
                document.getElementById('modalProduk')
            ).hide();

            loadProductData();
        }

        function hapusProduk(id) {
            if (!confirm('Yakin ingin menghapus produk ini?')) return;

            fetch(`${API_URL}/${id}`, {
                    method: 'DELETE'
                })
                .then(res => res.json())
                .then(result => {
                    if (!result.success) {
                        alert(result.message);
                        return;
                    }
                    loadProductData();
                    alert('Produk berhasil dihapus');
                });
        }
    </script> --}}
@endpush
