<!-- Modal Tambah Massal Produk -->
<div class="modal fade" id="modalProdukMassal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-layer-group me-2 text-primary"></i>Tambah Massal Produk
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="alert alert-info d-flex align-items-center">
                    <i class="fa fa-info-circle me-2"></i>
                    <div>
                        <strong>Tip:</strong> Isi data produk secara massal. Anda bisa menambahkan hingga 20 produk sekaligus.
                    </div>
                </div>

                <!-- Controls -->
                <div class="row align-items-center mb-4">
                    <div class="col-md-8">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-success" onclick="addProductRow()">
                                <i class="fa fa-plus me-2"></i> Tambah Baris
                            </button>
                            <button type="button" class="btn btn-outline-warning" onclick="clearAllRows()">
                                <i class="fa fa-trash-alt me-2"></i> Hapus Semua
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="d-flex align-items-center justify-content-end">
                            <span class="text-muted me-2">Total Produk:</span>
                            <span class="badge bg-primary fs-6 px-3 py-2" id="rowCount">0</span>
                        </div>
                    </div>
                </div>

                <!-- Product Table -->
                <div class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
                    <table class="table table-bordered table-sm" id="massalProductTable">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th width="50" class="text-center">#</th>
                                <th width="120">SKU</th>
                                <th>Supplier</th>
                                <th>Brand</th>
                                <th>Nama Produk <span class="text-danger">*</span></th>
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
                        <tbody id="massalProductBody">
                            <!-- Rows will be added dynamically -->
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div id="emptyState" class="text-center py-5">
                    <i class="fa fa-layer-group fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum ada produk ditambahkan</h5>
                    <p class="text-muted">Klik "Tambah Baris" untuk mulai menambah produk</p>
                </div>
            </div>

            <div class="modal-footer bg-light">
                <div class="d-flex justify-content-between w-100">
                    <div>
                        <small class="text-muted">
                            <i class="fa fa-info-circle me-1"></i>
                            Pastikan semua field wajib diisi sebelum menyimpan
                        </small>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fa fa-times me-2"></i> Batal
                        </button>
                        <button type="button" class="btn btn-primary" onclick="saveMassalProducts()">
                            <i class="fa fa-save me-2"></i> Simpan Semua Produk
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .massal-input {
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 8px 12px;
        font-size: 14px;
        width: 100%;
        min-height: 38px;
    }
    
    #massalProductTable td {
        padding: 8px 4px !important;
        vertical-align: middle;
    }
    
    .required-field {
        border-left: 4px solid #dc3545;
    }
    
    .row-number {
        font-weight: bold;
        color: #6c757d;
    }
</style>

<script>
    let productRowCount = 0;
    let suppliers = [];
    let brands = [];
    let categories = [];
    let units = [];
    let coas = [];

    // Initialize data
    async function initializeMassalModal() {
        try {
            // Load dropdown data using the same pattern as existing system
            const [suppliersRes, brandsRes, categoriesRes, coasRes] = await Promise.all([
                fetch('{{ route("mitra-api.index") }}' + '?per_page=1000'),
                fetch('{{ route("brand-api.index") }}'),
                fetch('{{ route("product-category-api.index") }}'),
                fetch('{{ route("coa-api.index") }}')
            ]);
            
            // Debug API responses
            console.log('API Responses:');
            console.log('Suppliers response status:', suppliersRes.status);
            console.log('Brands response status:', brandsRes.status);
            console.log('Categories response status:', categoriesRes.status);
            console.log('COAs response status:', coasRes.status);

            // Check if responses are OK before parsing JSON - using same pattern as existing system
            if (suppliersRes.ok) {
                const supplierData = await suppliersRes.json();
                console.log('Suppliers raw data:', supplierData);
                if (supplierData.success) {
                    suppliers = supplierData.data.data || supplierData.data;
                } else {
                    console.log('Suppliers API returned success=false');
                    suppliers = [];
                }
            } else {
                console.log('Suppliers response not OK');
                suppliers = [];
            }
            
            if (brandsRes.ok) {
                const brandData = await brandsRes.json();
                console.log('Brands raw data:', brandData);
                if (brandData.success) {
                    brands = brandData.data.data || brandData.data;
                } else {
                    console.log('Brands API returned success=false');
                    brands = [];
                }
            } else {
                console.log('Brands response not OK');
                brands = [];
            }
            
            if (categoriesRes.ok) {
                const categoryData = await categoriesRes.json();
                console.log('Categories raw data:', categoryData);
                if (categoryData.success) {
                    categories = categoryData.data.data || categoryData.data;
                } else {
                    console.log('Categories API returned success=false');
                    categories = [];
                }
            } else {
                console.log('Categories response not OK');
                categories = [];
            }
            
            if (coasRes.ok) {
                const coaData = await coasRes.json();
                console.log('COAs raw data:', coaData);
                if (coaData.success) {
                    coas = coaData.data.data || coaData.data;
                } else {
                    console.log('COAs API returned success=false');
                    coas = [];
                }
            } else {
                console.log('COAs response not OK');
                coas = [];
            }
            
            // For units, we'll need to create a static array since there's no unit API
            units = [
                { id: 'PCS', nama: 'PCS' },
                { id: 'BOX', nama: 'BOX' },
                { id: 'PACK', nama: 'PACK' },
                { id: 'LUSIN', nama: 'LUSIN' },
                { id: 'KODI', nama: 'KODI' },
                { id: 'GROSS', nama: 'GROSS' },
                { id: 'KG', nama: 'KG' },
                { id: 'GR', nama: 'GR' },
                { id: 'LITER', nama: 'LITER' },
                { id: 'ML', nama: 'ML' },
                { id: 'METER', nama: 'METER' },
                { id: 'CM', nama: 'CM' }
            ];
            
        } catch (error) {
            console.error('Error loading data:', error);
            // Set empty arrays as fallback
            suppliers = [];
            brands = [];
            categories = [];
            units = [
                { id: 'PCS', nama: 'PCS' },
                { id: 'BOX', nama: 'BOX' },
                { id: 'PACK', nama: 'PACK' },
                { id: 'LUSIN', nama: 'LUSIN' },
                { id: 'KODI', nama: 'KODI' },
                { id: 'GROSS', nama: 'GROSS' },
                { id: 'KG', nama: 'KG' },
                { id: 'GR', nama: 'GR' },
                { id: 'LITER', nama: 'LITER' },
                { id: 'ML', nama: 'ML' },
                { id: 'METER', nama: 'METER' },
                { id: 'CM', nama: 'CM' }
            ];
            coas = [];
        }
    }

    // Add new product row
    function addProductRow() {
        productRowCount++;
        updateRowCount();
        updateEmptyState();
        
        console.log('Adding row with data:', {
            suppliers: suppliers.length,
            brands: brands.length,
            categories: categories.length,
            units: units.length,
            coas: coas.length
        });
        
        const tbody = document.getElementById('massalProductBody');
        const row = document.createElement('tr');
        row.id = `product-row-${productRowCount}`;
        
        // Helper function to safely map arrays
        function safeMap(array, getValue) {
            if (!Array.isArray(array)) {
                console.log('Array is not valid:', array);
                return '';
            }
            return array.map(getValue).join('');
        }

        row.innerHTML = `
            <td class="text-center align-middle">
                <span class="row-number">${productRowCount}</span>
            </td>
            <td>
                <input type="text" class="form-control form-control-sm in-sku-${productRowCount}" placeholder="Auto generate" disabled>
            </td>
            <td>
                <select class="form-select form-select-sm in-supplier-${productRowCount}">
                    <option value="">-- Pilih --</option>
                    ${safeMap(suppliers, s => `<option value="${s.id}">${s.nama}</option>`)}
                </select>
            </td>
            <td>
                <select class="form-select form-select-sm in-brand-${productRowCount}">
                    <option value="">-- Pilih --</option>
                    ${safeMap(brands, b => `<option value="${b.id}">${b.nama_brand || b.nama}</option>`)}
                </select>
            </td>
            <td>
                <input type="text" class="form-control form-control-sm required-field in-nama-${productRowCount}" placeholder="Nama produk wajib diisi">
            </td>
            <td>
                <select class="form-select form-select-sm in-kategori-${productRowCount}">
                    <option value="">-- Pilih --</option>
                    ${safeMap(categories, c => `<option value="${c.id}">${c.nama_kategori || c.nama}</option>`)}
                </select>
            </td>
            <td>
                <input type="text" class="form-control form-control-sm in-kemasan-${productRowCount}" placeholder="Contoh: Box, Botol">
            </td>
            <td>
                <select class="form-select form-select-sm in-unit-${productRowCount}">
                    <option value="">-- Pilih --</option>
                    ${safeMap(units, u => `<option value="${u.id}">${u.nama}</option>`)}
                </select>
            </td>
            <td>
                <input type="number" class="form-control form-control-sm in-qty-${productRowCount}" placeholder="0" value="0" min="0">
            </td>
            <td>
                <input type="text" class="form-control form-control-sm in-beli-${productRowCount}" placeholder="0" value="0">
            </td>
            <td>
                <input type="text" class="form-control form-control-sm in-jual-${productRowCount}" placeholder="0" value="0">
            </td>
            <td>
                <input type="text" class="form-control form-control-sm in-tempo-${productRowCount}" placeholder="0" value="0">
            </td>
            <td>
                <select class="form-select form-select-sm in-coa-${productRowCount}">
                    <option value="">-- Pilih --</option>
                    ${safeMap(coas, c => `<option value="${c.id}">${c.nama_akun || c.nama}</option>`)}
                </select>
            </td>
            <td class="text-center align-middle">
                <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" onclick="removeProductRow(${productRowCount})" title="Hapus Baris">
                    <i class="fa fa-times"></i>
                </button>
            </td>
        `;

        // Initialize TomSelect for dropdowns like the existing system
        setTimeout(() => {
            try {
                // Use the row variable that's still in scope
                const currentRow = row;
                
                // Initialize supplier dropdown
                const supplierSelect = currentRow.querySelector(`.in-supplier-${productRowCount}`);
                if (supplierSelect && typeof TomSelect !== 'undefined') {
                    new TomSelect(supplierSelect, {
                        valueField: 'id',
                        labelField: 'nama',
                        searchField: 'nama',
                        create: false,
                        placeholder: 'Supplier...',
                        dropdownParent: 'body'
                    });
                }

                // Initialize brand dropdown
                const brandSelect = currentRow.querySelector(`.in-brand-${productRowCount}`);
                if (brandSelect && typeof TomSelect !== 'undefined') {
                    new TomSelect(brandSelect, {
                        valueField: 'id',
                        labelField: 'nama_brand',
                        searchField: 'nama_brand',
                        create: false,
                        placeholder: 'Brand...',
                        dropdownParent: 'body'
                    });
                }

                // Initialize category dropdown
                const categorySelect = currentRow.querySelector(`.in-kategori-${productRowCount}`);
                if (categorySelect && typeof TomSelect !== 'undefined') {
                    new TomSelect(categorySelect, {
                        valueField: 'id',
                        labelField: 'nama_kategori',
                        searchField: 'nama_kategori',
                        create: false,
                        placeholder: 'Kategori...',
                        dropdownParent: 'body'
                    });
                }

                // Initialize COA dropdown
                const coaSelect = currentRow.querySelector(`.in-coa-${productRowCount}`);
                if (coaSelect && typeof TomSelect !== 'undefined') {
                    new TomSelect(coaSelect, {
                        valueField: 'id',
                        labelField: 'nama_akun',
                        searchField: 'nama_akun',
                        create: false,
                        placeholder: 'COA...',
                        dropdownParent: 'body'
                    });
                }

                // Initialize unit dropdown
                const unitSelect = currentRow.querySelector(`.in-unit-${productRowCount}`);
                if (unitSelect && typeof TomSelect !== 'undefined') {
                    new TomSelect(unitSelect, {
                        valueField: 'id',
                        labelField: 'nama',
                        searchField: 'nama',
                        create: false,
                        placeholder: 'Satuan...',
                        dropdownParent: 'body'
                    });
                }

                // Add price formatting like existing system
                const beliInput = currentRow.querySelector(`.in-beli-${productRowCount}`);
                const jualInput = currentRow.querySelector(`.in-jual-${productRowCount}`);
                const tempoInput = currentRow.querySelector(`.in-tempo-${productRowCount}`);

                if (beliInput) {
                    beliInput.addEventListener('input', function() {
                        formatRupiahInput(this);
                    });
                }
                if (jualInput) {
                    jualInput.addEventListener('input', function() {
                        formatRupiahInput(this);
                    });
                }
                if (tempoInput) {
                    tempoInput.addEventListener('input', function() {
                        formatRupiahInput(this);
                    });
                }

            } catch (error) {
                console.log('TomSelect initialization error:', error);
            }
        }, 100);
        
        tbody.appendChild(row);
    }

    // Remove product row
    function removeProductRow(id) {
        const row = document.getElementById(`product-row-${id}`);
        if (row) {
            row.remove();
            updateRowCount();
            updateEmptyState();
        }
    }

    // Clear all rows
    function clearAllRows() {
        if (confirm('Apakah Anda yakin ingin menghapus semua baris?')) {
            document.getElementById('massalProductBody').innerHTML = '';
            productRowCount = 0;
            updateRowCount();
            updateEmptyState();
        }
    }

    // Update row count
    function updateRowCount() {
        const rows = document.querySelectorAll('#massalProductBody tr').length;
        document.getElementById('rowCount').textContent = `${rows} Produk`;
    }

    // Update empty state
    function updateEmptyState() {
        const rows = document.querySelectorAll('#massalProductBody tr').length;
        const emptyState = document.getElementById('emptyState');
        const table = document.querySelector('.table-responsive');
        
        if (rows === 0) {
            emptyState.style.display = 'block';
            table.style.display = 'none';
        } else {
            emptyState.style.display = 'none';
            table.style.display = 'block';
        }
    }

    // Save all products
    async function saveMassalProducts() {
        const rows = document.querySelectorAll('#massalProductBody tr');
        const products = [];
        let hasError = false;

        // Collect data from all rows using same logic as existing system
        rows.forEach((row, index) => {
            const rowId = row.id.replace('product-row-', '');
            
            // Get values using the same selectors as existing system
            const skuInput = row.querySelector(`.in-sku-${rowId}`);
            const namaInput = row.querySelector(`.in-nama-${rowId}`);
            const supplierSelect = row.querySelector(`.in-supplier-${rowId}`);
            const brandSelect = row.querySelector(`.in-brand-${rowId}`);
            const categorySelect = row.querySelector(`.in-kategori-${rowId}`);
            const kemasanInput = row.querySelector(`.in-kemasan-${rowId}`);
            const unitSelect = row.querySelector(`.in-unit-${rowId}`);
            const beliInput = row.querySelector(`.in-beli-${rowId}`);
            const jualInput = row.querySelector(`.in-jual-${rowId}`);
            const tempoInput = row.querySelector(`.in-tempo-${rowId}`);
            const coaSelect = row.querySelector(`.in-coa-${rowId}`);

            // Get TomSelect instances for dropdowns
            const getTomSelectValue = (element) => {
                if (element && element.tomselect) {
                    return element.tomselect.getValue();
                }
                return element ? element.value : '';
            };

            const nama = namaInput ? namaInput.value.trim() : '';
            
            if (!nama) {
                hasError = true;
                if (namaInput) namaInput.classList.add('is-invalid');
                return;
            }

            // Build payload using same structure as existing system
            const supplierId = getTomSelectValue(supplierSelect);
            const brandId = getTomSelectValue(brandSelect);
            const categoryId = getTomSelectValue(categorySelect);
            const coaId = getTomSelectValue(coaSelect);
            const unitId = getTomSelectValue(unitSelect);
            const kemasanRaw = kemasanInput ? kemasanInput.value : '';
            const kemasanVal = kemasanRaw === '' ? null : parseInt(kemasanRaw, 10);

            // Get qty value
            const qtyInput = row.querySelector(`.in-qty-${rowId}`);
            const qty = qtyInput ? qtyInput.value : 0;

            const product = {
                sku_kode: skuInput ? (skuInput.value.trim() || generateSKU()) : generateSKU(),
                nama_produk: nama,
                supplier_id: supplierId || null,
                brand_id: brandId || null,
                product_category_id: categoryId || null,
                kemasan: Number.isFinite(kemasanVal) ? kemasanVal : null,
                satuan: unitId || null,
                qty: qty || 0,
                harga_beli: beliInput ? beliInput.value.replace(/\./g, '') : 0,
                harga_jual: jualInput ? jualInput.value.replace(/\./g, '') : 0,
                harga_tempo: tempoInput ? tempoInput.value.replace(/\./g, '') : 0,
                coa_id: coaId || null
            };

            // Validation using same logic as existing system
            if (!product.nama_produk || !product.sku_kode) {
                hasError = true;
                alert('Baris ke-' + (index + 1) + ': Nama dan SKU wajib diisi');
                return;
            }
            if (!product.supplier_id) {
                hasError = true;
                alert('Baris ke-' + (index + 1) + ': Supplier wajib dipilih');
                return;
            }
            if (!product.brand_id) {
                hasError = true;
                alert('Baris ke-' + (index + 1) + ': Brand wajib dipilih');
                return;
            }
            if (!product.product_category_id) {
                hasError = true;
                alert('Baris ke-' + (index + 1) + ': Kategori wajib dipilih');
                return;
            }
            if (!product.coa_id) {
                hasError = true;
                alert('Baris ke-' + (index + 1) + ': COA wajib dipilih');
                return;
            }
            if (product.kemasan !== null && !Number.isInteger(product.kemasan)) {
                hasError = true;
                alert('Baris ke-' + (index + 1) + ': Kemasan harus berupa angka');
                return;
            }

            products.push(product);
        });

        if (hasError) {
            alert('Harap lengkapi field nama produk yang wajib diisi!');
            return;
        }

        if (products.length === 0) {
            alert('Tidak ada produk untuk disimpan!');
            return;
        }

        try {
            // Show loading
            const saveBtn = document.querySelector('[onclick="saveMassalProducts()"]');
            const originalText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Menyimpan...';
            saveBtn.disabled = true;

            const response = await fetch('{{ route("product-api.bulk-store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ products })
            });

            const result = await response.json();

            if (response.ok) {
                alert(`Berhasil menyimpan ${products.length} produk!`);
                bootstrap.Modal.getInstance(document.getElementById('modalProdukMassal')).hide();
                clearAllRows();
                // Refresh product table
                if (typeof loadProductData === 'function') {
                    loadProductData();
                }
            } else {
                alert('Error: ' + (result.message || 'Terjadi kesalahan saat menyimpan produk'));
            }
        } catch (error) {
            console.error('Error saving products:', error);
            alert('Terjadi kesalahan saat menyimpan produk. Silakan coba lagi.');
        } finally {
            // Restore button
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;
        }
    }

    // Generate SKU
    function generateSKU() {
        return 'PRD-' + Date.now().toString(36).toUpperCase();
    }

    // Open modal
    function tambahMassalProduk() {
        // Show modal immediately
        const modal = new bootstrap.Modal(document.getElementById('modalProdukMassal'));
        modal.show();
        
        // Initialize data in background
        initializeMassalModal().then(() => {
            console.log('Data loaded successfully');
            console.log('Suppliers:', suppliers.length);
            console.log('Brands:', brands.length);
            console.log('Categories:', categories.length);
            console.log('COAs:', coas.length);
        }).catch(error => {
            console.error('Error loading data:', error);
        });
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateEmptyState();
    });
</script>
