<div class="tab-pane fade show active" id="tab-produk" role="tabpanel">

    <!-- Modern Filter Controls -->
    <div class="dr-filter-bar mb-4">
        <div class="dr-filter-group" style="flex: 2; min-width: 200px;">
            <label class="dr-label">Cari Barang</label>
            <div class="dr-search-wrap">
                <i class="iconoir-search dr-search-icon"></i>
                <input type="text" id="filter-produk-search" class="dr-input dr-search-input"
                    placeholder="Ketik nama atau SKU...">
            </div>
        </div>
        <div class="dr-filter-group" style="flex: 1; min-width: 150px;">
            <label class="dr-label">Supplier</label>
            <select id="filter-produk-supplier" class="dr-input">
                <option value="">Semua Supplier</option>
            </select>
        </div>
        <div class="dr-filter-group" style="flex: 1; min-width: 150px;">
            <label class="dr-label">Brand</label>
            <select id="filter-produk-brand" class="dr-input">
                <option value="">Semua Brand</option>
            </select>
        </div>
        <div class="dr-filter-group" style="flex: 1; min-width: 150px;">
            <label class="dr-label">Kategori</label>
            <select id="filter-produk-kategori" class="dr-input">
                <option value="">Semua Kategori</option>
            </select>
        </div>
        <div class="dr-filter-actions">
            <label class="dr-label" style="visibility: hidden;">-</label>
            <div class="d-flex gap-2">
                <button onclick="resetFilterProduk()" class="dr-btn dr-btn-outline" title="Reset Filter">
                    <i class="iconoir-undo"></i>
                </button>
                <button onclick="exportProduk()" class="dr-btn dr-btn-outline" title="Export Excel">
                    <i class="iconoir-download"></i>
                </button>
                <button class="dr-btn dr-btn-primary" onclick="tambahMassalProduk()">
                    <i class="iconoir-plus"></i> TAMBAH
                </button>
            </div>
        </div>
    </div>

    <!-- Enhanced Data Table -->
    <div class="dr-table-container mb-4">
        <table class="dr-table align-middle">
            <thead>
                <tr>
                    <th width="120" class="ps-4" style="cursor: pointer;" onclick="toggleSort('sku_kode')">SKU <span id="sort-icon-sku_kode"></span></th>
                    <th>Supplier</th>
                    <th>Brand</th>
                    <th style="cursor: pointer;" onclick="toggleSort('nama_produk')">Nama Produk <span id="sort-icon-nama_produk"></span></th>
                    <th>Kategori</th>
                    <th class="text-center" style="cursor: pointer;" onclick="toggleSort('kemasan')">Kemasan <span id="sort-icon-kemasan"></span></th>
                    <th class="text-center" style="cursor: pointer;" onclick="toggleSort('satuan')">Satuan <span id="sort-icon-satuan"></span></th>
                    <th class="text-center" style="cursor: pointer;" onclick="toggleSort('qty')">Qty <span id="sort-icon-qty"></span></th>
                    <th class="text-end" style="cursor: pointer;" onclick="toggleSort('harga_beli')">Harga Beli <span id="sort-icon-harga_beli"></span></th>
                    <th class="text-end" style="cursor: pointer;" onclick="toggleSort('harga_jual')">Harga Jual <span id="sort-icon-harga_jual"></span></th>
                    <th class="text-end" style="cursor: pointer;" onclick="toggleSort('harga_tempo')">Harga Tempo <span id="sort-icon-harga_tempo"></span></th>
                    <th>COA</th>
                    <th width="60" class="text-center pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody id="produk-table-body">
                <tr>
                    <td colspan="13" class="text-center text-muted py-5">
                        <div class="spinner-border spinner-border-sm text-primary me-2"></div> Memuat data...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center px-2">
        <span id="produk-pagination-info" class="text-muted small fw-medium"></span>
        <nav>
            <ul class="pagination pagination-sm mb-0 gap-1" id="produk-pagination-container"></ul>
        </nav>
    </div>

</div>



<template id="produk-row-display-template">
    <tr>
        <td class="col-sku fw-bold text-primary ps-4"></td>
        <td class="col-supplier"></td>
        <td class="col-brand"></td>
        <td class="col-nama fw-semibold"></td>
        <td class="col-kategori"></td>
        <td class="col-kemasan text-center"></td>
        <td class="col-satuan text-center"></td>
        <td class="col-qty text-center"></td>
        <td class="col-beli text-end"></td>
        <td class="col-jual text-end"></td>
        <td class="col-tempo text-end"></td>
        <td class="col-coa text-start"></td>
        <td class="text-center pe-4">
            <div class="dropdown">
                <button class="dr-btn dr-btn-outline p-2 shadow-none border-0 bg-light-subtle dropdown-toggle no-caret" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="iconoir-more-vert fs-5"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2" style="border-radius: 12px; min-width: 160px;">
                    <li>
                        <a class="dropdown-item btn-edit d-flex align-items-center gap-2 py-2 px-3 rounded-2" href="javascript:void(0)">
                            <i class="iconoir-edit-pencil text-warning"></i> 
                            <span class="fw-semibold" style="font-size: 13px;">Edit Produk</span>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider opacity-50"></li>
                    <li>
                        <a class="dropdown-item text-danger btn-delete d-flex align-items-center gap-2 py-2 px-3 rounded-2" href="javascript:void(0)">
                            <i class="iconoir-trash"></i>
                            <span class="fw-semibold" style="font-size: 13px;">Hapus Produk</span>
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

        // Resizable Column Logic
        function initResizableTable() {
            const table = document.querySelector('.table');
            const cols = table.querySelectorAll('th');
            
            cols.forEach((col) => {
                // Add resizer element if not exists
                if (!col.querySelector('.resizer')) {
                    const resizer = document.createElement('div');
                    resizer.classList.add('resizer');
                    col.appendChild(resizer);
                    
                    createResizableColumn(col, resizer);
                }
            });
        }

        function createResizableColumn(col, resizer) {
            let x = 0;
            let w = 0;

            const onMouseMove = (e) => {
                const dx = e.clientX - x;
                col.style.width = `${w + dx}px`;
                resizer.classList.add('resizing');
            };

            const onMouseUp = () => {
                document.removeEventListener('mousemove', onMouseMove);
                document.removeEventListener('mouseup', onMouseUp);
                resizer.classList.remove('resizing');
            };

            resizer.addEventListener('mousedown', (e) => {
                x = e.clientX;
                const styles = window.getComputedStyle(col);
                w = parseInt(styles.width, 10);

                document.addEventListener('mousemove', onMouseMove);
                document.addEventListener('mouseup', onMouseUp);
            });
        }

        let pCurrentSort = 'created_at';
        let pCurrentDir = 'desc';

        function toggleSort(field) {
            if (pCurrentSort === field) {
                pCurrentDir = pCurrentDir === 'desc' ? 'asc' : 'desc';
            } else {
                pCurrentSort = field;
                pCurrentDir = 'asc';
            }
            updateSortIcons();
            loadProductData();
        }

        function updateSortIcons() {
            // Reset all icons to a faint neutral state
            document.querySelectorAll('th span[id^="sort-icon-"]').forEach(el => {
                el.innerHTML = '<i class="fa fa-sort text-muted ms-1" style="opacity: 0.3;"></i>';
            });

            // Highlight the active sort column
            const iconEl = document.getElementById('sort-icon-' + pCurrentSort);
            if (iconEl) {
                if (pCurrentDir === 'asc') {
                    iconEl.innerHTML = '<i class="fa fa-sort-up text-primary ms-1" style="font-size: 1.2rem;"></i>';
                } else {
                    iconEl.innerHTML = '<i class="fa fa-sort-down text-primary ms-1" style="font-size: 1.2rem;"></i>';
                }
            }
        }

        function formatBiggerPriceHTML(val) {
            val = val || 0;
            if (val == 0) {
                return '<span class="text-muted">Rp 0</span>';
            }
            return '<span class="fw-bold text-dark">' + formatIDR(val) + '</span>';
        }

        let pSearchTimeout = null;
        document.getElementById('filter-produk-search').addEventListener('input', function() {
            clearTimeout(pSearchTimeout);
            pSearchTimeout = setTimeout(() => {
                loadProductData();
            }, 500);
        });

        function resetFilterProduk() {
            document.getElementById('filter-produk-search').value = '';
            if(window.tsProductSupplierFilter) window.tsProductSupplierFilter.clear();
            if(window.tsProductBrandFilter) window.tsProductBrandFilter.clear();
            if(window.tsProductKategoriFilter) window.tsProductKategoriFilter.clear();
            pCurrentSort = 'created_at';
            pCurrentDir = 'desc';
            updateSortIcons();
            loadProductData();
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



        let tsEditSupplier, tsEditBrand, tsEditKategori, tsEditCOA;

        async function editProduk(id) {
            document.getElementById('edit-produk-id').value = id;
            
            const inBeli = document.getElementById('edit-produk-beli');
            const inJual = document.getElementById('edit-produk-jual');
            const inTempo = document.getElementById('edit-produk-tempo');

            if(!inBeli.hasAttribute('data-listener')) {
                inBeli.addEventListener('input', function() { formatRupiahInput(this); });
                inJual.addEventListener('input', function() { formatRupiahInput(this); });
                inTempo.addEventListener('input', function() { formatRupiahInput(this); });
                inBeli.setAttribute('data-listener', 'true');
            }

            try {
                const res = await fetch(`${PRODUCT_URL}/${id}`);
                const result = await res.json();
                if (!result.success) return alert('Gagal mengambil data produk');

                const item = result.data;

                document.getElementById('edit-produk-sku').value = item.sku_kode || '';
                document.getElementById('edit-produk-nama').value = item.nama_produk || '';
                document.getElementById('edit-produk-kemasan').value = item.kemasan || '';
                document.getElementById('edit-produk-satuan').value = item.satuan || '';
                document.getElementById('edit-produk-qty').value = item.qty || 0;
                
                inBeli.value = item.harga_beli || 0;
                inJual.value = item.harga_jual || 0;
                inTempo.value = item.harga_tempo || 0;
                
                formatRupiahInput(inBeli);
                formatRupiahInput(inJual);
                formatRupiahInput(inTempo);

                if(tsEditSupplier) tsEditSupplier.destroy();
                if(tsEditBrand) tsEditBrand.destroy();
                if(tsEditKategori) tsEditKategori.destroy();
                if(tsEditCOA) tsEditCOA.destroy();

                tsEditBrand = new TomSelect('#edit-produk-brand', {
                    valueField: 'value',
                    labelField: 'text',
                    searchField: 'text',
                    options: masterBrands.map(b => ({ value: b.id, text: b.nama_brand })),
                    placeholder: 'Pilih Brand...',
                    dropdownParent: 'body',
                    create: async function(input, callback) {
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                        const supplierId = tsEditSupplier ? tsEditSupplier.getValue() : null;

                        try {
                            const res = await fetch(BRAND_URL, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': token
                                },
                                body: JSON.stringify({ nama_brand: input, supplier_ids: [supplierId] })
                            });
                            const data = await res.json();
                            if (data.status === 'success' || data.success) {
                                masterBrands.push(data.data);
                                callback({ value: data.data.id, text: data.data.nama_brand });
                            } else {
                                alert(data.message || 'Gagal memproses brand');
                                callback(false);
                            }
                        } catch (err) {
                            alert('Gagal memproses brand');
                            callback(false);
                        }
                    }
                });

                tsEditSupplier = new TomSelect('#edit-produk-supplier', {
                    valueField: 'value',
                    labelField: 'text',
                    searchField: 'text',
                    options: masterSuppliers.map(s => ({ value: s.id, text: s.nama })),
                    placeholder: 'Pilih Supplier...',
                    dropdownParent: 'body',
                    onChange: function(supplierId) {
                        if (!tsEditBrand) return;
                        tsEditBrand.clear();
                        tsEditBrand.clearOptions();
                        const options = masterBrands.map(b => ({ value: b.id, text: b.nama_brand }));
                        tsEditBrand.addOptions(options);
                        tsEditBrand.refreshOptions(false);
                    }
                });

                tsEditKategori = new TomSelect('#edit-produk-kategori', {
                    valueField: 'value',
                    labelField: 'text',
                    searchField: 'text',
                    options: masterCategories.map(c => ({ value: c.id, text: c.nama_kategori })),
                    placeholder: 'Pilih Kategori...',
                    dropdownParent: 'body'
                });

                tsEditCOA = new TomSelect('#edit-produk-coa', {
                    valueField: 'value',
                    labelField: 'text',
                    searchField: 'text',
                    options: masterCOA.map(c => ({ value: c.id, text: c.nama_akun })),
                    placeholder: 'Pilih COA...',
                    dropdownParent: 'body'
                });

                tsEditSupplier.setValue(item.supplier_id);
                tsEditBrand.setValue(item.brand_id);
                tsEditKategori.setValue(item.product_category_id);
                tsEditCOA.setValue(item.coa_id);

                new bootstrap.Modal(document.getElementById('modalProduk')).show();

            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan memuat produk.');
            }
        }

        async function submitEditProduk() {
            const id = document.getElementById('edit-produk-id').value;
            if (!id) return;

            const btn = document.getElementById('btn-simpan-edit-produk');
            btn.disabled = true;
            btn.innerHTML = 'Menyimpan...';

            const supplierId = tsEditSupplier.getValue();
            const brandId = tsEditBrand.getValue();
            const categoryId = tsEditKategori.getValue();
            const coaId = tsEditCOA.getValue();
            const kemasanRaw = document.getElementById('edit-produk-kemasan').value;
            const kemasanVal = kemasanRaw === '' ? null : parseInt(kemasanRaw, 10);

            const payload = {
                nama_produk: document.getElementById('edit-produk-nama').value,
                supplier_id: supplierId,
                brand_id: brandId,
                product_category_id: categoryId,
                kemasan: Number.isFinite(kemasanVal) ? kemasanVal : null,
                satuan: document.getElementById('edit-produk-satuan').value,
                harga_beli: document.getElementById('edit-produk-beli').value.replace(/\./g, ''),
                harga_jual: document.getElementById('edit-produk-jual').value.replace(/\./g, ''),
                harga_tempo: document.getElementById('edit-produk-tempo').value.replace(/\./g, ''),
                coa_id: coaId,
            };

            if (!payload.nama_produk) { alert('Nama produk wajib diisi'); btn.disabled = false; btn.innerHTML = 'Simpan Perubahan'; return; }
            if (!payload.supplier_id) { alert('Supplier wajib dipilih'); btn.disabled = false; btn.innerHTML = 'Simpan Perubahan'; return; }
            if (!payload.brand_id) { alert('Brand wajib dipilih.'); btn.disabled = false; btn.innerHTML = 'Simpan Perubahan'; return; }
            if (!payload.product_category_id) { alert('Kategori wajib dipilih'); btn.disabled = false; btn.innerHTML = 'Simpan Perubahan'; return; }
            if (!payload.coa_id) { alert('COA wajib dipilih'); btn.disabled = false; btn.innerHTML = 'Simpan Perubahan'; return; }

            try {
                const saveRes = await fetch(`${PRODUCT_URL}/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    body: JSON.stringify(payload)
                });

                const saveResult = await saveRes.json();
                if (saveResult.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modalProduk')).hide();
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
                alert('Terjadi kesalahan pada sistem.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = 'Simpan Perubahan';
            }
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
                
                const supplierLabel = item.supplier?.nama || 'TANPA SUPLIER';
                const brandLabel = item.brand?.nama_brand || 'TANPA BRAND';
                
                const supplierEl = clone.querySelector('.col-supplier');
                supplierEl.textContent = supplierLabel;
                if (!item.supplier) supplierEl.classList.add('text-faint');
                
                const brandEl = clone.querySelector('.col-brand');
                brandEl.textContent = brandLabel;
                if (!item.brand) brandEl.classList.add('text-faint');

                clone.querySelector('.col-nama').textContent = item.nama_produk || '-';
                clone.querySelector('.col-kategori').textContent = item.category?.nama_kategori || '-';
                clone.querySelector('.col-kemasan').textContent = item.kemasan || '-';
                clone.querySelector('.col-satuan').textContent = item.satuan || '-';

                const qty = item.qty || 0;
                if(qty < 10 && qty >= 0) {
                    clone.querySelector('.col-qty').innerHTML = `<span class="badge bg-danger p-2" style="font-size: 13px;">${qty}</span>`;
                } else {
                    clone.querySelector('.col-qty').textContent = qty;
                }

                clone.querySelector('.col-beli').innerHTML = formatBiggerPriceHTML(item.harga_beli);
                clone.querySelector('.col-jual').innerHTML = formatBiggerPriceHTML(item.harga_jual);
                clone.querySelector('.col-tempo').innerHTML = formatBiggerPriceHTML(item.harga_tempo);
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
                const brandId = document.getElementById('filter-produk-brand').value;
                const categoryId = document.getElementById('filter-produk-kategori').value;

                const fetchUrl = new URL(url);
                if (search) fetchUrl.searchParams.append('search', search);
                if (supplierId) fetchUrl.searchParams.append('supplier_id', supplierId);
                if (brandId) fetchUrl.searchParams.append('brand_id', brandId);
                if (categoryId) fetchUrl.searchParams.append('category', categoryId);

                fetchUrl.searchParams.append('sort', pCurrentSort);
                fetchUrl.searchParams.append('dir', pCurrentDir);

                const res = await fetch(fetchUrl);
                const result = await res.json();

                if (result.success) {
                    renderProductTable(result.data.data);
                    renderProductPagination(result.data);
                    updateSortIcons();
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
            const brandId = document.getElementById('filter-produk-brand').value;
            const categoryId = document.getElementById('filter-produk-kategori').value;
            let url = '/barang/export?';
            const params = [];
            if (search) params.push(`search=${encodeURIComponent(search)}`);
            if (supplierId) params.push(`supplier_id=${supplierId}`);
            if (brandId) params.push(`brand_id=${brandId}`);
            if (categoryId) params.push(`category=${categoryId}`);
            if (pCurrentSort) params.push(`sort=${pCurrentSort}`);
            if (pCurrentDir) params.push(`dir=${pCurrentDir}`);
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
