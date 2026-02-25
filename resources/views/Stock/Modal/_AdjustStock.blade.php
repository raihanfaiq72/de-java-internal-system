<div class="modal fade" id="modalAdjustStock" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sesuaikan Stok</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="adjust-stock-id">
                
                <div class="mb-3">
                    <label class="small fw-bold text-muted">Produk</label>
                    <div class="p-2 bg-light border rounded">
                        <span id="adjust-stock-sku" class="fw-bold"></span> - 
                        <span id="adjust-stock-name"></span>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="small fw-bold">Lokasi Stok</label>
                    <select id="adjust-stock-location" class="form-select form-select-sm">
                        <option value="">-- Pilih Lokasi (Opsional) --</option>
                    </select>
                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Jika kosong, akan menyesuaikan stok global/tak teralokasi.</small>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="small fw-bold">Stok Saat Ini</label>
                        <input type="text" id="adjust-stock-current" class="form-control form-control-sm" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold">Stok Baru</label>
                        <input type="number" id="adjust-stock-new" class="form-control form-control-sm" min="0">
                    </div>
                </div>
                
                <div class="mt-3">
                    <label class="small fw-bold">Catatan (Optional)</label>
                    <textarea id="adjust-stock-note" class="form-control form-control-sm" rows="2" placeholder="Alasan penyesuaian..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary btn-sm" onclick="submitAdjustStock()">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>
