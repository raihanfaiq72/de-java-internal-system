<div class="modal fade" id="modalAdjustStock" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="dr-card-title fs-5">
                    <i class="iconoir-edit-pencil me-2 text-primary"></i> Adjust Stok
                </h5>
            </div>
            <div class="modal-body">
                <input type="hidden" id="adjust-stock-id">
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="dr-label mb-2">Nama Produk</label>
                        <div class="position-relative">
                            <input type="text" id="adjust-stock-name" class="dr-input" style="background-color: var(--dr-panel-soft) !important;" readonly>
                            <i class="iconoir-lock position-absolute end-0 top-50 translate-middle-y me-3 text-muted small"></i>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="dr-label mb-2">Kode SKU</label>
                        <div class="position-relative">
                            <input type="text" id="adjust-stock-sku" class="dr-input" style="background-color: var(--dr-panel-soft) !important;" readonly>
                            <i class="iconoir-lock position-absolute end-0 top-50 translate-middle-y me-3 text-muted small"></i>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="dr-label mb-2">Stok Saat Ini</label>
                        <div class="position-relative">
                            <input type="text" id="adjust-stock-current" class="dr-input" style="background-color: var(--dr-panel-soft) !important;" readonly>
                            <i class="iconoir-lock position-absolute end-0 top-50 translate-middle-y me-3 text-muted small"></i>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="dr-label mb-2">Stok Baru</label>
                        <input type="number" id="adjust-stock-new" class="dr-input" min="0" placeholder="0">
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="dr-label mb-2">Lokasi Stok (Optional)</label>
                    <select id="adjust-stock-location" class="dr-input">
                        <option value="">-- Pilih Lokasi (Opsional) --</option>
                    </select>
                </div>

                <div class="mb-0">
                    <label class="dr-label mb-2">Catatan (Optional)</label>
                    <textarea id="adjust-stock-note" class="dr-input" rows="3" style="height: auto;" placeholder="Alasan penyesuaian..."></textarea>
                </div>
            </div>
            <div class="modal-footer d-flex gap-2">
                <button class="dr-btn-modal dr-btn-modal-cancel grow justify-content-center" data-bs-dismiss="modal">
                    <i class="iconoir-xmark"></i> Batal
                </button>
                <button class="dr-btn-modal dr-btn-modal-save grow justify-content-center" onclick="submitAdjustStock()">
                    <i class="iconoir-check"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>
