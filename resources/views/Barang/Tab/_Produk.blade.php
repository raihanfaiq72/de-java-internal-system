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
            <button onclick="exportProduk()" class="btn btn-sm btn-white border px-3 text-dark shadow-sm">
                <i class="fa fa-file-excel me-1 text-success"></i> Export
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

            inBeli.addEventListener('input', function () {
                formatRupiahInput(this);
            });
            inJual.addEventListener('input', function () {
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
                onChange: function (supplierId) {
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
                create: function (input, callback) {
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
                const supplierId = tsProductSupplier.getValue();
                const brandId = tsProductBrand.getValue();
                const categoryId = tsProductKategori.getValue();
                const coaId = tsCOA.getValue();
                const kemasanRaw = tr.querySelector('.in-kemasan').value;
                const kemasanVal = kemasanRaw === '' ? null : parseInt(kemasanRaw, 10);

                const payload = {
                    sku_kode: skuInput.value,
                    nama_produk: tr.querySelector('.in-nama').value,
                    supplier_id: supplierId,
                    brand_id: brandId,
                    product_category_id: categoryId,
                    kemasan: Number.isFinite(kemasanVal) ? kemasanVal : null,
                    satuan: tr.querySelector('.in-satuan').value,
                    harga_beli: inBeli.value.replace(/\./g, ''),
                    harga_jual: inJual.value.replace(/\./g, ''),
                    track_stock: 1,
                    akun_penjualan_id: 1,
                    akun_pembelian_id: 1,
                    coa_id: coaId
                };

                if (!payload.nama_produk || !payload.sku_kode) {
                    alert('Nama dan SKU wajib diisi');
                    return;
                }
                if (!payload.supplier_id) {
                    alert('Supplier wajib dipilih');
                    return;
                }
                if (!payload.brand_id) {
                    alert('Brand wajib dipilih. Jika brand belum ada untuk supplier ini, silakan buat brand terlebih dahulu.');
                    return;
                }
                if (!payload.product_category_id) {
                    alert('Kategori wajib dipilih');
                    return;
                }
                if (!payload.coa_id) {
                    alert('COA wajib dipilih');
                    return;
                }
                if (payload.kemasan !== null && !Number.isInteger(payload.kemasan)) {
                    alert('Kemasan harus berupa angka');
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
                        if (result.errors && typeof result.errors === 'object') {
                            const messages = Object.values(result.errors).flat().join('\n');
                            alert('Validasi gagal:\n' + messages);
                        } else {
                            alert('Gagal: ' + (result.message || 'Terjadi kesalahan.'));
                        }
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
                onChange: function (supplierId) {
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
                create: function (input, callback) {
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

            inBeliEdit.addEventListener('input', function () {
                formatRupiahInput(this);
            });
            inJualEdit.addEventListener('input', function () {
                formatRupiahInput(this);
            });

            inBeliEdit.value = new Intl.NumberFormat('id-ID').format(item.harga_beli || 0);
            inJualEdit.value = new Intl.NumberFormat('id-ID').format(item.harga_jual || 0);

            oldRow.replaceWith(editRow);

            editRow.querySelector('.btn-cancel-inline').onclick = () => loadProductData();

            editRow.querySelector('.btn-save-inline').onclick = async () => {
                const supplierId = tsSupplier.getValue();
                const brandId = tsBrand.getValue();
                const categoryId = tsKategori.getValue();
                const coaId = tsCOA.getValue();
                const kemasanRaw = editRow.querySelector('.in-kemasan').value;
                const kemasanVal = kemasanRaw === '' ? null : parseInt(kemasanRaw, 10);

                const payload = {
                    sku_kode: item.sku_kode,
                    nama_produk: editRow.querySelector('.in-nama').value,
                    supplier_id: supplierId,
                    brand_id: brandId,
                    product_category_id: categoryId,
                    kemasan: Number.isFinite(kemasanVal) ? kemasanVal : null,
                    satuan: editRow.querySelector('.in-satuan').value,
                    harga_beli: inBeliEdit.value.replace(/\./g, ''),
                    harga_jual: inJualEdit.value.replace(/\./g, ''),
                    coa_id: coaId,
                    track_stock: 1
                };
                if (!payload.nama_produk) {
                    alert('Nama produk wajib diisi');
                    return;
                }
                if (!payload.supplier_id) {
                    alert('Supplier wajib dipilih');
                    return;
                }
                if (!payload.brand_id) {
                    alert('Brand wajib dipilih. Jika brand belum ada untuk supplier ini, silakan buat brand terlebih dahulu.');
                    return;
                }
                if (!payload.product_category_id) {
                    alert('Kategori wajib dipilih');
                    return;
                }
                if (!payload.coa_id) {
                    alert('COA wajib dipilih');
                    return;
                }
                if (payload.kemasan !== null && !Number.isInteger(payload.kemasan)) {
                    alert('Kemasan harus berupa angka');
                    return;
                }

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
                        if (saveResult.errors && typeof saveResult.errors === 'object') {
                            const messages = Object.values(saveResult.errors).flat().join('\n');
                            alert('Validasi gagal:\n' + messages);
                        } else {
                            alert('Gagal memperbarui: ' + (saveResult.message || 'Terjadi kesalahan.'));
                        }
                    }
                } catch (e) {
                    console.error(e);
                }
            };
        }

        async function hapusProduk(id) {
            if (!await macConfirm('Hapus produk', 'Yakin ingin menghapus produk ini?')) return;

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

        function exportProduk() {
            const search = document.getElementById('filter-produk-search').value;
            let url = '/barang/export?';
            if (search) url += `search=${encodeURIComponent(search)}`;
            window.location.href = url;
        }

        const formatIDR = (val) => new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(val);
    </script>
@endpush
