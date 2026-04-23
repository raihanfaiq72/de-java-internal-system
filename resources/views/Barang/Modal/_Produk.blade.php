<div class="modal fade" id="modalProduk" tabindex="-1" aria-labelledby="modalProdukTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
<<<<<<< Updated upstream
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalProdukTitle">
                    <i class="fa fa-edit me-2 text-warning"></i>Edit Produk
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
=======
        <div class="modal-content border-0 bg-white">

            <div class="modal-header bg-white px-4 py-4 d-flex align-items-center justify-content-between border-0">
                <div class="d-flex align-items-center gap-3">
                    <i class="iconoir-edit-pencil text-dark" style="font-size: 20px;"></i>
                    <div>
                        <h5 class="dr-title mb-1" id="modalProdukTitle" style="font-size: 16px;">Edit Informasi Produk</h5>
                        <p class="dr-subtitle mb-0" style="font-size: 12px;">Perbarui detail spesifikasi, kategori, dan penentuan harga barang.</p>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-4 pb-4 pt-0 bg-white">
>>>>>>> Stashed changes
                <input type="hidden" id="edit-produk-id">

                <div class="row g-4">
                    <!-- Column Left: Identity -->
<<<<<<< Updated upstream
                    <div class="modal-column">
                        <div class="modal-section-title">Identitas Produk</div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">SKU</label>
                            <input type="text" id="edit-produk-sku" class="form-control" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" id="edit-produk-nama" class="form-control" placeholder="Contoh: Oli Mesin 1L">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Supplier <span class="text-danger">*</span></label>
                            <select id="edit-produk-supplier" class="form-select"></select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Brand <span class="text-danger">*</span></label>
                            <select id="edit-produk-brand" class="form-select"></select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Kategori <span class="text-danger">*</span></label>
                            <select id="edit-produk-kategori" class="form-select"></select>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-bold">COA <span class="text-danger">*</span></label>
                            <select id="edit-produk-coa" class="form-select"></select>
=======
                    <div class="col-md-6 border-end">
                        <div class="text-primary fw-bold mb-3 small text-uppercase ls-1" style="font-size: 10px; letter-spacing: 1px;">Identitas Produk</div>
                        
                        <div class="mb-3">
                            <label class="dr-label mb-2">SKU / Kode Barang</label>
                            <input type="text" id="edit-produk-sku" class="dr-input bg-light" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="dr-label mb-2">Nama Produk *</label>
                            <input type="text" id="edit-produk-nama" class="dr-input fw-bold" placeholder="Cth: Oli Mesin 1L">
                        </div>

                        <div class="mb-3">
                            <label class="dr-label mb-2">Supplier *</label>
                            <select id="edit-produk-supplier" class="dr-input"></select>
                        </div>

                        <div class="mb-3">
                            <label class="dr-label mb-2">Brand / Merk *</label>
                            <select id="edit-produk-brand" class="dr-input"></select>
                        </div>

                        <div class="mb-3">
                            <label class="dr-label mb-2">Kategori *</label>
                            <select id="edit-produk-kategori" class="dr-input"></select>
                        </div>

                        <div class="mb-0">
                            <label class="dr-label mb-2">COA (Akun Akuntansi) *</label>
                            <select id="edit-produk-coa" class="dr-input"></select>
>>>>>>> Stashed changes
                        </div>
                    </div>

                    <!-- Column Right: Specification & Pricing -->
<<<<<<< Updated upstream
                    <div class="modal-column">
                        <div class="modal-section-title">Harga & Spesifikasi</div>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold">Kemasan</label>
                                <input type="number" id="edit-produk-kemasan" class="form-control" placeholder="Cth: 12">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold">Satuan</label>
                                <input type="text" id="edit-produk-satuan" class="form-control" placeholder="Cth: DUS">
=======
                    <div class="col-md-6">
                        <div class="text-primary fw-bold mb-3 small text-uppercase ls-1" style="font-size: 10px; letter-spacing: 1px;">Harga & Spesifikasi</div>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="dr-label mb-2">Isi Kemasan</label>
                                <input type="number" id="edit-produk-kemasan" class="dr-input" placeholder="Cth: 12">
                            </div>
                            <div class="col-6">
                                <label class="dr-label mb-2">Unit Satuan</label>
                                <input type="text" id="edit-produk-satuan" class="dr-input" placeholder="Cth: DUS">
>>>>>>> Stashed changes
                            </div>
                        </div>

                        <div class="mb-3">
<<<<<<< Updated upstream
                            <label class="form-label fw-bold text-muted">Stok Saat Ini (Disabled)</label>
                            <input type="number" id="edit-produk-qty" class="form-control bg-light" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Harga Beli</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" id="edit-produk-beli" class="form-control fw-bold text-primary" value="0">
=======
                            <label class="dr-label mb-2">Stok Saat Ini (Read-only)</label>
                            <input type="number" id="edit-produk-qty" class="dr-input bg-light" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="dr-label mb-2">Harga Beli Dasar</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted px-2">Rp</span>
                                <input type="text" id="edit-produk-beli" class="dr-input border-start-0 fw-bold text-dark" value="0">
>>>>>>> Stashed changes
                            </div>
                        </div>

                        <div class="mb-3">
<<<<<<< Updated upstream
                            <label class="form-label fw-bold">Harga Jual</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" id="edit-produk-jual" class="form-control fw-bold text-success" value="0">
=======
                            <label class="dr-label mb-2">Harga Jual (CASH)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted px-2">Rp</span>
                                <input type="text" id="edit-produk-jual" class="dr-input border-start-0 fw-bold text-dark" value="0">
>>>>>>> Stashed changes
                            </div>
                        </div>

                        <div class="mb-0">
<<<<<<< Updated upstream
                            <label class="form-label fw-bold">Harga Tempo</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" id="edit-produk-tempo" class="form-control fw-bold text-warning" value="0">
=======
                            <label class="dr-label mb-2">Harga Jual (TEMPO)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted px-2">Rp</span>
                                <input type="text" id="edit-produk-tempo" class="dr-input border-start-0 fw-bold text-dark" value="0">
>>>>>>> Stashed changes
                            </div>
                        </div>
                    </div>
                </div>
            </div>

<<<<<<< Updated upstream
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    <i class="fa fa-times me-2"></i>Tutup
                </button>
                <button id="btn-simpan-edit-produk" class="btn btn-primary px-4 fw-bold" onclick="submitEditProduk()">
                    <i class="fa fa-save me-2"></i>Simpan Perubahan
=======
            <div class="modal-footer bg-white border-0 px-4 py-4 d-flex justify-content-end gap-2">
                <button type="button" class="dr-btn-modal" data-bs-dismiss="modal">
                    <i class="iconoir-xmark text-danger"></i> Batal
                </button>
                <button type="button" id="btn-simpan-edit-produk" class="dr-btn-modal" onclick="submitEditProduk()">
                    <i class="iconoir-check text-success"></i> Simpan Perubahan
>>>>>>> Stashed changes
                </button>
            </div>

        </div>
    </div>
</div>
