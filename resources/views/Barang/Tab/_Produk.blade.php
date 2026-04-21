<div class="tab-pane fade show active" id="tab-produk" role="tabpanel">

    <div class="row g-2 mb-3">
        <!-- Filter Controls -->
        <div class="col-md-3">
            <label class="small fw-bold text-muted">Cari Barang</label>
            <div class="input-group input-group-sm">
                <span class="input-group-text"><i class="fa fa-search"></i></span>
                <input type="text" id="filter-produk-search" class="form-control"
                    placeholder="Ketik pencarian...">
            </div>
        </div>
        <div class="col-md-3">
            <label class="small fw-bold text-muted">Supplier</label>
            <select id="filter-produk-supplier" class="form-select form-select-sm">
                <option value="">Semua Supplier</option>
            </select>
        </div>
        
        <!-- Action Buttons -->
        <div class="col-md-6">
            <div class="d-flex gap-2 justify-content-end align-items-end">
                <button onclick="loadProductData()" class="btn btn-outline-primary btn-sm">
                    <i class="fa fa-filter me-1"></i> Filter
                </button>
                <button onclick="exportProduk()" class="btn btn-outline-success btn-sm">
                    <i class="fa fa-download me-1"></i> Export
                </button>
                <div class="vr"></div>
                <button class="btn btn-success btn-sm" onclick="tambahMassalProduk()">
                    <i class="fa fa-layer-group me-1"></i> Massal
                </button>
                <button class="btn btn-primary btn-sm" onclick="tambahProduk()">
                    <i class="fa fa-plus me-1"></i> Tambah
                </button>
            </div>
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
                    <th>Qty</th>
                    <th>Harga Beli</th>
                    <th>Harga Jual</th>
                    <th>Harga Tempo</th>
                    <th>COA</th>
                    <th width="80" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="produk-table-body">
                <tr>
                    <td colspan="12" class="text-center text-muted">Memuat data...</td>
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
            <input type="number" class="form-control form-control-sm in-qty" value="0" min="0">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm in-beli" value="0">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm in-jual" value="0">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm in-tempo" value="0">
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
        <td class="col-qty text-center"></td>
        <td class="col-beli text-end"></td>
        <td class="col-jual text-end"></td>
        <td class="col-tempo text-end"></td>
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

        async function syncBrandLink(brandId, supplierId, tsInstance) {
            const brand = masterBrands.find(b => String(b.id) === String(brandId));
            if (!brand || !supplierId) return;

            const isLinked = brand.suppliers && brand.suppliers.some(s => String(s.id) === String(
                supplierId));
            if (isLinked) return;

            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                    'content') || '';
                const res = await fetch(BRAND_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        nama_brand: brand.nama_brand,
                        supplier_ids: [supplierId],
                    })
                });
                const result = await res.json();
                if (result.success || result.status === 'success') {
                    const idx = masterBrands.findIndex(b => b.id === brand.id);
                    if (idx !== -1) masterBrands[idx] = result.data;

                    if (tsInstance) {
                        tsInstance.updateOption(brand.id, {
                            value: brand.id,
                            text: result.data.nama_brand
                        });
                    }
                }
            } catch (e) {
                console.error("Link sync failed:", e);
            }
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
            const inTempo = tr.querySelector('.in-tempo');
            
            inBeli.addEventListener('input', function() {
                formatRupiahInput(this);
            });
            inJual.addEventListener('input', function() {
                formatRupiahInput(this);
            });
            inTempo.addEventListener('input', function() {
                formatRupiahInput(this);
            });

            let tsProductBrand, tsProductSupplier, tsProductKategori, tsCOA;

            tsProductBrand = new TomSelect(tr.querySelector('.in-brand'), {

                options: masterBrands.map(b => {
                    const supplierId = tsProductSupplier ? tsProductSupplier.getValue() : null;
                    const isLinked = b.suppliers && b.suppliers.some(s => String(s.id) === String(
                        supplierId));
                    return {
                        value: b.id,
                        text: b.nama_brand
                    };
                }),
                placeholder: 'Brand...',
                dropdownParent: 'body',
                onChange: function(brandId) {
                    if (!brandId) return;
                    const supplierId = tsProductSupplier.getValue();
                    syncBrandLink(brandId, supplierId, tsProductBrand);
                },
                create: async function(input, callback) {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                        'content') || '';
                    const supplierId = tsProductSupplier.getValue();

                    try {
                        const res = await fetch(BRAND_URL, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token
                            },
                            body: JSON.stringify({
                                nama_brand: input,
                                supplier_ids: [supplierId],
                            })
                        });
                        const data = await res.json();
                        if (data.status === 'success' || data.success) {
                            const idx = masterBrands.findIndex(b => b.id === data.data.id);
                            if (idx !== -1) masterBrands[idx] = data.data;
                            else masterBrands.push(data.data);

                            callback({
                                value: data.data.id,
                                text: data.data.nama_brand
                            });
                        } else {
                            alert(data.message || 'Gagal memproses brand');
                            callback(false);
                        }
                    } catch (err) {
                        console.error(err);
                        alert('Gagal memproses brand');
                        callback(false);
                    }
                }
            });


            tsProductSupplier = new TomSelect(tr.querySelector('.in-supplier'), {
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

                    const options = masterBrands.map(b => {
                        const isLinked = b.suppliers && b.suppliers.some(s => String(s.id) ===
                            String(supplierId));
                        return {
                            value: b.id,
                            text: b.nama_brand
                        };
                    });

                    tsProductBrand.addOptions(options);
                    tsProductBrand.refreshOptions(false);
                }
            });


            tsProductKategori = new TomSelect(tr.querySelector('.in-kategori'), {
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

            tsCOA = new TomSelect(tr.querySelector('.in-coa'), {
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

                const qtyValue = tr.querySelector('.in-qty').value;
                const payload = {
                    sku_kode: skuInput.value,
                    nama_produk: tr.querySelector('.in-nama').value,
                    supplier_id: supplierId,
                    brand_id: brandId,
                    product_category_id: categoryId,
                    kemasan: Number.isFinite(kemasanVal) ? kemasanVal : null,
                    satuan: tr.querySelector('.in-satuan').value,
                    qty: qtyValue || 0,
                    harga_beli: inBeli.value.replace(/\./g, ''),
                    harga_jual: inJual.value.replace(/\./g, ''),
                    harga_tempo: inTempo.value.replace(/\./g, ''),
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
                    alert(
                        'Brand wajib dipilih. Jika brand belum ada untuk supplier ini, silakan buat brand terlebih dahulu.'
                    );
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

            let tsBrand, tsSupplier, tsKategori, tsCOA;

            tsBrand = new TomSelect(editRow.querySelector('.in-brand'), {
                options: masterBrands.map(b => {
                    const supplierId = typeof tsSupplier !== 'undefined' ? tsSupplier.getValue() :
                        null;
                    const isLinked = b.suppliers && b.suppliers.some(s => String(s.id) === String(
                        supplierId));
                    return {
                        value: b.id,
                        text: b.nama_brand
                    };
                }),
                placeholder: 'Brand...',
                dropdownParent: 'body',
                onChange: function(brandId) {
                    if (!brandId) return;
                    const supplierId = tsSupplier.getValue();
                    syncBrandLink(brandId, supplierId, tsBrand);
                },
                create: async function(input, callback) {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                        'content') || '';
                    const supplierId = tsSupplier.getValue();

                    try {
                        const res = await fetch(BRAND_URL, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token
                            },
                            body: JSON.stringify({
                                nama_brand: input,
                                supplier_ids: [supplierId],
                            })
                        });
                        const data = await res.json();
                        if (data.status === 'success' || data.success) {
                            const idx = masterBrands.findIndex(b => b.id === data.data.id);
                            if (idx !== -1) masterBrands[idx] = data.data;
                            else masterBrands.push(data.data);

                            callback({
                                value: data.data.id,
                                text: data.data.nama_brand
                            });
                        } else {
                            alert(data.message || 'Gagal memproses brand');
                            callback(false);
                        }
                    } catch (err) {
                        console.error(err);
                        alert('Gagal memproses brand');
                        callback(false);
                    }
                }
            });


            tsSupplier = new TomSelect(editRow.querySelector('.in-supplier'), {
                options: masterSuppliers.map(s => ({
                    value: s.id,
                    text: s.nama
                })),
                placeholder: 'Supplier...',
                dropdownParent: 'body',
                onChange: function(supplierId) {
                    if (!tsBrand) return;

                    tsBrand.clear();
                    tsBrand.clearOptions();

                    const options = masterBrands.map(b => {
                        const isLinked = b.suppliers && b.suppliers.some(s => String(s.id) ===
                            String(supplierId));
                        return {
                            value: b.id,
                            text: b.nama_brand
                        };
                    });

                    tsBrand.addOptions(options);
                    tsBrand.refreshOptions(false);
                }
            });

            tsKategori = new TomSelect(editRow.querySelector('.in-kategori'), {
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

            tsCOA = new TomSelect(editRow.querySelector('.in-coa'), {
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
            editRow.querySelector('.in-qty').value = item.qty || 0;
            editRow.querySelector('.in-beli').value = item.harga_beli || 0;
            editRow.querySelector('.in-jual').value = item.harga_jual || 0;
            editRow.querySelector('.in-tempo').value = item.harga_tempo || 0;

            tsSupplier.setValue(item.supplier_id);
            tsBrand.setValue(item.brand_id);
            tsKategori.setValue(item.product_category_id);
            tsCOA.setValue(item.coa_id);

            const inBeliEdit = editRow.querySelector('.in-beli');
            const inJualEdit = editRow.querySelector('.in-jual');
            const inTempoEdit = editRow.querySelector('.in-tempo');

            inBeliEdit.addEventListener('input', function() {
                formatRupiahInput(this);
            });
            inJualEdit.addEventListener('input', function() {
                formatRupiahInput(this);
            });
            inTempoEdit.addEventListener('input', function() {
                formatRupiahInput(this);
            });

            inBeliEdit.value = new Intl.NumberFormat('id-ID').format(item.harga_beli || 0);
            inJualEdit.value = new Intl.NumberFormat('id-ID').format(item.harga_jual || 0);
            inTempoEdit.value = new Intl.NumberFormat('id-ID').format(item.harga_tempo || 0);

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
                    qty: editRow.querySelector('.in-qty').value || 0,
                    harga_beli: inBeliEdit.value.replace(/\./g, ''),
                    harga_jual: inJualEdit.value.replace(/\./g, ''),
                    harga_tempo: inTempoEdit.value.replace(/\./g, ''),
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
                    alert(
                        'Brand wajib dipilih. Jika brand belum ada untuk supplier ini, silakan buat brand terlebih dahulu.'
                    );
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
                clone.querySelector('.col-qty').textContent = item.qty || 0;
                clone.querySelector('.col-beli').textContent = formatIDR(item.harga_beli);
                clone.querySelector('.col-jual').textContent = formatIDR(item.harga_jual);
                clone.querySelector('.col-tempo').textContent = formatIDR(item.harga_tempo);
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
                const supplierId = document.getElementById('filter-produk-supplier').value;

                const fetchUrl = new URL(url);
                if (search) fetchUrl.searchParams.append('search', search);
                if (supplierId) fetchUrl.searchParams.append('supplier_id', supplierId);

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
            const supplierId = document.getElementById('filter-produk-supplier').value;
            let url = '/barang/export?';
            const params = [];
            if (search) params.push(`search=${encodeURIComponent(search)}`);
            if (supplierId) params.push(`supplier_id=${supplierId}`);
            url += params.join('&');
            window.location.href = url;
        }

        const formatIDR = (val) => new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(val);
    </script>
@endpush
