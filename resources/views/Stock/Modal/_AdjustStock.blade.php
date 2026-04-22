<div class="modal fade" id="modalAdjustStock" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="dr-card-title fs-5">
                    <i class="iconoir-edit-pencil me-2 text-primary"></i> Sesuaikan Stok
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="adjust-stock-id">
                
                <div class="mb-4">
                    <label class="dr-label mb-2">Produk</label>
                    <div class="p-3 bg-light border-0 rounded-3 d-flex align-items-center gap-3" style="background-color: #f8f9fa !important;">
                        <div class="bg-white rounded-2 border d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="iconoir-box-iso text-muted"></i>
                        </div>
                        <div>
                            <div id="adjust-stock-sku" class="fw-bold text-dark small"></div>
                            <div id="adjust-stock-name" class="text-muted small"></div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="dr-label mb-2">Lokasi Stok</label>
                    <select id="adjust-stock-location" class="dr-input">
                        <option value="">-- Pilih Lokasi (Opsional) --</option>
                    </select>
                    <small class="text-muted d-block mt-2" style="font-size: 12px; line-height: 1.4;">
                        <i class="iconoir-info-empty me-1"></i> Jika kosong, penyesuaian berlaku pada stok global.
                    </small>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="dr-label mb-2">Stok Saat Ini</label>
                        <div class="position-relative">
                            <input type="text" id="adjust-stock-current" class="dr-input bg-light" style="background-color: #f1f1f4 !important;" readonly>
                            <i class="iconoir-lock position-absolute end-0 top-50 translate-middle-y me-3 text-muted small"></i>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="dr-label mb-2">Stok Baru</label>
                        <input type="number" id="adjust-stock-new" class="dr-input" min="0" placeholder="0">
                    </div>
                </div>
                
                <div class="mb-0">
                    <label class="dr-label mb-2">Catatan (Optional)</label>
                    <textarea id="adjust-stock-note" class="dr-input" rows="3" style="height: auto;" placeholder="Alasan penyesuaian..."></textarea>
                </div>
            </div>
            <div class="modal-footer gap-2">
                <button class="dr-btn dr-btn-outline grow justify-content-center" data-bs-dismiss="modal">Batal</button>
                <button class="dr-btn dr-btn-primary grow justify-content-center" onclick="submitAdjustStock()">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>
