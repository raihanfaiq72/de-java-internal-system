<!-- Modal Adjust Stock -->
<div class="modal fade" id="modalAdjustStock" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
<<<<<<< Updated upstream
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sesuaikan Stok</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
=======
        <div class="modal-content border-0 bg-white">
            
            <div class="modal-header bg-white px-4 py-4 d-flex align-items-center justify-content-between border-0">
                <div class="d-flex align-items-center gap-3">
                    <i class="iconoir-edit-pencil text-dark" style="font-size: 20px;"></i>
                    <div>
                        <h5 class="dr-title mb-1" style="font-size: 16px;">Penyesuaian Stok</h5>
                        <p class="dr-subtitle mb-0" style="font-size: 12px;">Koreksi jumlah stok fisik produk di sistem.</p>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
>>>>>>> Stashed changes
            </div>

            <div class="modal-body px-4 pb-4 pt-0 bg-white">
                <input type="hidden" id="adjust-stock-id">
                
<<<<<<< Updated upstream
                <div class="mb-3">
                    <label class="small fw-bold text-muted">Produk</label>
                    <div class="p-2 bg-light border rounded">
                        <span id="adjust-stock-sku" class="fw-bold"></span> - 
                        <span id="adjust-stock-name"></span>
=======
                <div class="p-3 border rounded-3 mb-4 bg-white d-flex align-items-center gap-3">
                    <div class="bg-light rounded p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="iconoir-box-iso text-muted"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark small" id="adjust-stock-name">Produk Nama</div>
                        <div class="text-muted" style="font-size: 11px;" id="adjust-stock-sku">SKU-000</div>
>>>>>>> Stashed changes
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
<<<<<<< Updated upstream
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
=======
                        <label class="dr-label mb-2">Stok Saat Ini</label>
                        <input type="text" id="adjust-stock-current" class="dr-input bg-light" readonly value="0">
                    </div>
                    <div class="col-md-6">
                        <label class="dr-label mb-2">Stok Baru *</label>
                        <input type="number" id="adjust-stock-new" class="dr-input fw-bold" value="0" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="dr-label mb-2">Pilih Lokasi Gudang</label>
                    <select id="adjust-stock-location" class="dr-input">
                        <option value="">-- Semua Lokasi --</option>
                    </select>
                </div>

                <div class="mb-0">
                    <label class="dr-label mb-2">Catatan Penyesuaian *</label>
                    <textarea id="adjust-stock-note" class="dr-input" rows="2" style="height: auto;" placeholder="Alasan penyesuaian stok (misal: Barang rusak, opname rutin, dll)" required></textarea>
                </div>
            </div>

            <div class="modal-footer bg-white border-0 px-4 py-4 d-flex justify-content-end gap-2">
                <button type="button" class="dr-btn-modal" data-bs-dismiss="modal">
                    <i class="iconoir-xmark text-danger"></i> Batal
                </button>
                <button type="button" class="dr-btn-modal" onclick="submitAdjustStock()">
                    <i class="iconoir-check text-success"></i> Simpan Perubahan
                </button>
>>>>>>> Stashed changes
            </div>

        </div>
    </div>
</div>
