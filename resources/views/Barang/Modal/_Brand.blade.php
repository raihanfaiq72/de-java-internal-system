<div class="modal fade" id="modalBrand" tabindex="-1" aria-labelledby="brandModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
<<<<<<< Updated upstream
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="brandModalTitle">
                    <i class="fa fa-tags me-2 text-primary"></i>Tambah Brand
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
=======
        <div class="modal-content border-0 bg-white">

            <div class="modal-header bg-white px-4 py-4 d-flex align-items-center justify-content-between border-0">
                <div class="d-flex align-items-center gap-3">
                    <i class="iconoir-tag text-dark" style="font-size: 20px;"></i>
                    <div>
                        <h5 class="dr-title mb-1" id="brandModalTitle" style="font-size: 16px;">Kelola Brand & Merk</h5>
                        <p class="dr-subtitle mb-0" style="font-size: 12px;">Tambahkan brand baru atau perbarui tautan supplier.</p>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-4 pb-4 pt-0 bg-white">
>>>>>>> Stashed changes
                <form id="formBrand">
                    <input type="hidden" id="brandId">

                    <div class="mb-4">
<<<<<<< Updated upstream
                        <label class="form-label fw-bold">Nama Brand <span class="text-danger">*</span></label>
                        <input type="text" id="nama_brand" class="form-control"
                            placeholder="Contoh: Castrol">
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Supplier</label>
                        <select multiple id="suppliers" class="form-select" placeholder="Pilih supplier ..."></select>
                        <div class="invalid-feedback"></div>
=======
                        <label class="dr-label mb-2">Nama Brand / Merk *</label>
                        <input type="text" id="nama_brand" class="dr-input fw-bold" placeholder="Misal: Indofood, Samsung, dll" required>
                    </div>

                    <div class="mb-0">
                        <label class="dr-label mb-2">Pilih Mitra / Supplier Penyuplai</label>
                        <select id="select-supplier-brand" name="supplier_ids[]" class="dr-input" multiple placeholder="Pilih Supplier...">
                            <!-- Options akan diisi via JS (masterSuppliers) -->
                        </select>
                        <div class="mt-2 text-muted" style="font-size: 11px;">
                            <i class="iconoir-info-circle"></i> Memudahkan pencarian produk berdasarkan supplier saat restock.
                        </div>
>>>>>>> Stashed changes
                    </div>
                </form>
            </div>

<<<<<<< Updated upstream
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    <i class="fa fa-times me-2"></i>Batal
                </button>
                <button type="button" class="btn btn-primary px-4 fw-bold shadow-sm" id="btnSaveBrand" onclick="saveBrand()">
                    <i class="fa fa-save me-2"></i>Simpan Brand
=======
            <div class="modal-footer bg-white border-0 px-4 py-4 d-flex justify-content-end gap-2">
                <button type="button" class="dr-btn-modal" data-bs-dismiss="modal">
                    <i class="iconoir-xmark text-danger"></i> Batal
                </button>
                <button type="button" class="dr-btn-modal" id="btnSaveBrand">
                    <i class="iconoir-check text-success"></i> Simpan Brand
>>>>>>> Stashed changes
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Gunakan let dan nama unik agar tidak bentrok dengan index.blade.php
    let tsBrandSupplier = null;
    
    document.addEventListener('DOMContentLoaded', function() {
        const el = document.getElementById('select-supplier-brand');
        if (el && typeof TomSelect !== 'undefined') {
            tsBrandSupplier = new TomSelect(el, {
                plugins: ['remove_button'],
                create: false,
                valueField: 'id',
                labelField: 'nama',
                searchField: ['nama'],
                placeholder: 'Pilih satu atau lebih supplier...',
                dropdownParent: 'body'
            });
            
            // Tunggu sebentar agar masterSuppliers di index.blade.php terisi
            setTimeout(() => {
                if (window.masterSuppliers && window.masterSuppliers.length > 0) {
                    tsBrandSupplier.addOptions(window.masterSuppliers);
                } else if (typeof masterSuppliers !== 'undefined' && masterSuppliers.length > 0) {
                    tsBrandSupplier.addOptions(masterSuppliers);
                }
            }, 500);
        }
    });

    // Override fungsi syncSupplierOptions milik Tab _Brand
    function syncSupplierOptions() {
        if (!tsBrandSupplier) return;
        
        const suppliers = window.masterSuppliers || (typeof masterSuppliers !== 'undefined' ? masterSuppliers : []);
        if (tsBrandSupplier.options.length === 0 && suppliers.length > 0) {
            tsBrandSupplier.addOptions(suppliers);
        }
        tsBrandSupplier.refreshOptions(false);
    }
    
    // Pastikan fungsi editBrand bisa mengakses TomSelect yang benar
    const originalEditBrand = window.editBrand;
    window.editBrand = async function(id) {
        // Panggil fungsi asli jika ada, atau jalankan logika edit
        const form = document.getElementById('formBrand');
        if (!form) return;
        
        form.reset();
        document.getElementById('brandModalTitle').innerText = 'Edit Brand';
        document.getElementById('brandId').value = id;

        try {
            const res = await fetch(`${BRAND_URL}/${id}`);
            const result = await res.json();

            if (result.success) {
                const brand = result.data;
                document.getElementById('nama_brand').value = brand.nama_brand;

                if (tsBrandSupplier) {
                    tsBrandSupplier.clear();
                    syncSupplierOptions();
                    if (brand.suppliers && Array.isArray(brand.suppliers)) {
                        const ids = brand.suppliers.map(s => String(s.id));
                        tsBrandSupplier.setValue(ids);
                    }
                }

                new bootstrap.Modal(document.getElementById('modalBrand')).show();
            }
        } catch (error) {
            console.error('Edit error:', error);
        }
    };
    
    // Pastikan fungsi openBrandModal bisa mengakses TomSelect yang benar
    window.openBrandModal = function() {
        const form = document.getElementById('formBrand');
        if (form) form.reset();
        
        document.getElementById('brandId').value = '';
        document.getElementById('brandModalTitle').innerText = 'Tambah Brand Baru';

        if (tsBrandSupplier) {
            tsBrandSupplier.clear();
            syncSupplierOptions();
        }

        new bootstrap.Modal(document.getElementById('modalBrand')).show();
    };

    // Sinkronkan fungsi saveBrand agar menggunakan tsBrandSupplier
    const originalSaveBrand = window.saveBrand;
    window.saveBrand = async function() {
        const btn = document.getElementById('btnSaveBrand');
        const brandId = document.getElementById('brandId').value;
        const brandName = document.getElementById('nama_brand').value;
        
        if (!tsBrandSupplier) {
            console.error('TomSelect Brand Supplier belum siap');
            return;
        }
        
        const selectedSuppliers = tsBrandSupplier.getValue();

        if (!brandName) {
            alert('Nama Brand wajib diisi!');
            return;
        }

        const url = brandId ? `${BRAND_URL}/${brandId}` : BRAND_URL;
        const method = brandId ? 'PUT' : 'POST';
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const payload = {
            nama_brand: brandName,
            supplier_ids: selectedSuppliers
        };

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>...';

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
                loadBrandData();
            } else {
                alert('Gagal: ' + (result.message || 'Terjadi kesalahan'));
            }
        } catch (error) {
            console.error('Save error:', error);
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="iconoir-check text-success"></i> Simpan Brand';
        }
    };
</script>
