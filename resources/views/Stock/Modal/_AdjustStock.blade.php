<div class="modal fade" id="modalAdjustStock" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="dr-card-title fs-5">
                    <i class="iconoir-edit-pencil me-2 text-primary"></i> Adjust Stok Per Lokasi
                </h5>
            </div>
            <div class="modal-body">
                <input type="hidden" id="adjust-stock-id">

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="dr-label mb-2">Nama Produk</label>
                        <div class="position-relative">
                            <input type="text" id="adjust-stock-name" class="dr-input"
                                style="background-color: var(--dr-panel-soft) !important;" readonly>
                            <i
                                class="iconoir-lock position-absolute end-0 top-50 translate-middle-y me-3 text-muted small"></i>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="dr-label mb-2">Kode SKU</label>
                        <div class="position-relative">
                            <input type="text" id="adjust-stock-sku" class="dr-input"
                                style="background-color: var(--dr-panel-soft) !important;" readonly>
                            <i
                                class="iconoir-lock position-absolute end-0 top-50 translate-middle-y me-3 text-muted small"></i>
                        </div>
                    </div>
                </div>

                <div class="dr-table-container mb-4" style="max-height: 400px; overflow-y: auto;">
                    <table class="dr-table align-middle">
                        <thead>
                            <tr>
                                <th class="ps-3">Lokasi Gudang</th>
                                <th class="text-center" style="width: 150px;">Stok Saat Ini</th>
                                <th class="text-center" style="width: 200px;">Stok Baru</th>
                            </tr>
                        </thead>
                        <tbody id="adjust-stock-location-list">
                            <!-- Populated via JS -->
                        </tbody>
                    </table>
                </div>

                <div class="mb-0">
                    <label class="dr-label mb-2">Catatan Perubahan</label>
                    <textarea id="adjust-stock-note" class="dr-input" rows="2"
                        style="height: 80px; display: flex; align-items: center; justify-content: center; padding-top: 25px;"
                        placeholder="Alasan penyesuaian (opsional)..."></textarea>
                </div>
            </div>
            <div class="modal-footer d-flex gap-2">
                <div class="me-auto">
                    <span class="text-muted small fw-medium">Total Terhitung: <strong id="adjust-stock-total-calc"
                            class="text-dark">0</strong></span>
                </div>
                <button class="dr-btn-modal dr-btn-modal-cancel grow-0 justify-content-center" data-bs-dismiss="modal"
                    style="width: 100px;">
                    Batal
                </button>
                <button class="dr-btn-modal dr-btn-modal-save grow justify-content-center"
                    onclick="submitAdjustStock()">
                    <i class="iconoir-check me-1"></i> Simpan Semua
                </button>
            </div>
        </div>
    </div>
</div>
