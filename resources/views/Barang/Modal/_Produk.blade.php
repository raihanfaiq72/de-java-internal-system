<div class="modal fade" id="modalProduk" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius: var(--dr-radius);">

            <div class="modal-header border-bottom-0 p-4 pb-2">
                <h5 class="dr-card-title fs-4" id="modalProdukTitle">
                    <i class="iconoir-edit-pencil me-2 text-warning"></i> Edit Produk
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4 pt-2">
                <input type="hidden" id="edit-produk-id">

                <div class="modal-grid-container">
                    <!-- Column Left: Identity -->
                    <div class="modal-column">
                        <div class="dr-label text-primary border-bottom pb-2 mb-3 mt-2" style="font-size: 11px; letter-spacing: 1px; text-transform: uppercase;">Identitas Produk</div>
                        
                        <div class="mb-3">
                            <label class="dr-label">SKU / Kode Barang</label>
                            <input type="text" id="edit-produk-sku" class="dr-input bg-light-subtle" style="opacity: 0.8;" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="dr-label">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" id="edit-produk-nama" class="dr-input" placeholder="Cth: Oli Mesin 1L">
                        </div>

                        <div class="mb-3">
                            <label class="dr-label">Supplier <span class="text-danger">*</span></label>
                            <select id="edit-produk-supplier" class="dr-input"></select>
                        </div>

                        <div class="mb-3">
                            <label class="dr-label">Brand / Merk <span class="text-danger">*</span></label>
                            <select id="edit-produk-brand" class="dr-input"></select>
                        </div>

                        <div class="mb-3">
                            <label class="dr-label">Kategori <span class="text-danger">*</span></label>
                            <select id="edit-produk-kategori" class="dr-input"></select>
                        </div>

                        <div class="mb-0">
                            <label class="dr-label">COA (Akun Akuntansi) <span class="text-danger">*</span></label>
                            <select id="edit-produk-coa" class="dr-input"></select>
                        </div>
                    </div>

                    <!-- Column Right: Specification & Pricing -->
                    <div class="modal-column">
                        <div class="dr-label text-primary border-bottom pb-2 mb-3 mt-2" style="font-size: 11px; letter-spacing: 1px; text-transform: uppercase;">Harga & Spesifikasi</div>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="dr-label">Isi Kemasan</label>
                                <input type="number" id="edit-produk-kemasan" class="dr-input" placeholder="Cth: 12">
                            </div>
                            <div class="col-6">
                                <label class="dr-label">Unit Satuan</label>
                                <input type="text" id="edit-produk-satuan" class="dr-input" placeholder="Cth: DUS">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="dr-label text-muted">Stok Saat Ini (Hanya Baca)</label>
                            <input type="number" id="edit-produk-qty" class="dr-input bg-light-subtle" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="dr-label">Harga Beli Dasar</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted">Rp</span>
                                <input type="text" id="edit-produk-beli" class="dr-input border-start-0 fw-bold text-primary" value="0">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="dr-label">Harga Jual (CASH)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted">Rp</span>
                                <input type="text" id="edit-produk-jual" class="dr-input border-start-0 fw-bold text-success" value="0">
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="dr-label">Harga Jual (TEMPO)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted">Rp</span>
                                <input type="text" id="edit-produk-tempo" class="dr-input border-start-0 fw-bold text-warning" value="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-top-0 p-4 pt-0">
                <button type="button" class="dr-btn dr-btn-outline px-4" data-bs-dismiss="modal">
                    <i class="iconoir-cancel me-2"></i> Batal
                </button>
                <button id="btn-simpan-edit-produk" class="dr-btn dr-btn-primary px-4 shadow-sm" onclick="submitEditProduk()">
                    <i class="iconoir-check me-2"></i> Simpan Perubahan
                </button>
            </div>

        </div>
    </div>
</div>
