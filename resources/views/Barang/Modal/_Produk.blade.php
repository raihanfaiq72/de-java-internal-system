<div class="modal fade" id="modalProduk" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalProdukTitle">
                    <i class="fa fa-edit me-2 text-warning"></i>Edit Produk
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="edit-produk-id">

                <div class="modal-grid-container">
                    <!-- Column Left: Identity -->
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
                        </div>
                    </div>

                    <!-- Column Right: Specification & Pricing -->
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
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Stok Saat Ini (Disabled)</label>
                            <input type="number" id="edit-produk-qty" class="form-control bg-light" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Harga Beli</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" id="edit-produk-beli" class="form-control fw-bold text-primary" value="0">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Harga Jual</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" id="edit-produk-jual" class="form-control fw-bold text-success" value="0">
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-bold">Harga Tempo</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" id="edit-produk-tempo" class="form-control fw-bold text-warning" value="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    <i class="fa fa-times me-2"></i>Tutup
                </button>
                <button id="btn-simpan-edit-produk" class="btn btn-primary px-4 fw-bold" onclick="submitEditProduk()">
                    <i class="fa fa-save me-2"></i>Simpan Perubahan
                </button>
            </div>

        </div>
    </div>
</div>
