<!-- Modal Location CRUD -->
<div class="modal fade" id="modalLocation" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="dr-card-title fs-5" id="location-modal-title">
                    <i class="iconoir-map-pin me-2 text-primary"></i> Buat Lokasi Stok
                </h5>
            </div>
            <div class="modal-body">
                <input type="hidden" id="location-id">
                <div class="mb-4">
                    <label class="dr-label mb-2">Nama Lokasi</label>
                    <input type="text" id="location-name" class="dr-input" placeholder="Contoh: Gudang Utama">
                </div>
                <div class="mb-0">
                    <label class="dr-label mb-2">Tipe Lokasi</label>
                    <select id="location-type" class="dr-input">
                        <option value="stock">Gudang / Stok Fisik</option>
                        <option value="virtual">Virtual / Dropship</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer d-flex gap-2">
                <button class="dr-btn-modal dr-btn-modal-cancel grow justify-content-center" data-bs-dismiss="modal">
                    <i class="iconoir-xmark"></i> Batal
                </button>
                <button class="dr-btn-modal dr-btn-modal-save grow justify-content-center" onclick="submitLocation()">
                    <i class="iconoir-check"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Opening Stock -->
<div class="modal fade" id="modalOpeningStock" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="dr-card-title fs-5">
                    <i class="iconoir-add-database me-2 text-success"></i> Persediaan Awal
                </h5>
            </div>
            <div class="modal-body">
                <input type="hidden" id="opening-stock-location-id">
                <div class="mb-4">
                    <label class="dr-label mb-2">Lokasi Terpilih</label>
                    <div class="position-relative">
                        <input type="text" id="opening-stock-location-name" class="dr-input" style="background-color: var(--dr-panel-soft) !important;" readonly>
                        <i class="iconoir-lock position-absolute end-0 top-50 translate-middle-y me-3 text-muted small"></i>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="dr-label mb-2">Pilih Produk</label>
                    <select id="opening-stock-product-id" class="dr-input select2-opening">
                        <option value="">-- Pilih Produk --</option>
                    </select>
                </div>
                <div class="row g-3 mb-0">
                    <div class="col-md-6">
                        <label class="dr-label mb-2">Kuantitas</label>
                        <input type="number" id="opening-stock-qty" class="dr-input" min="1" value="1">
                    </div>
                    <div class="col-md-6">
                        <label class="dr-label mb-2">Harga Beli (Satuan)</label>
                        <div class="position-relative">
                            <span class="position-absolute start-0 top-50 translate-middle-y ms-3 text-muted small fw-bold">Rp</span>
                            <input type="number" id="opening-stock-cost" class="dr-input ps-5" min="0" placeholder="0">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex gap-2">
                <button class="dr-btn-modal dr-btn-modal-cancel grow justify-content-center" data-bs-dismiss="modal">
                    <i class="iconoir-xmark"></i> Batal
                </button>
                <button class="dr-btn-modal dr-btn-modal-save grow justify-content-center" onclick="submitOpeningStock()">
                    <i class="iconoir-check"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Stock Opname -->
<div class="modal fade" id="modalStockOpname" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="dr-card-title fs-5">
                    <i class="iconoir-search-window me-2 text-warning"></i> Stok Opname
                </h5>
            </div>
            <div class="modal-body">
                <input type="hidden" id="opname-location-id">
                <div class="mb-4">
                    <label class="dr-label mb-2">Lokasi Terpilih</label>
                    <div class="position-relative">
                        <input type="text" id="opname-location-name" class="dr-input" style="background-color: var(--dr-panel-soft) !important;" readonly>
                        <i class="iconoir-lock position-absolute end-0 top-50 translate-middle-y me-3 text-muted small"></i>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="dr-label mb-2">Pilih Produk</label>
                    <select id="opname-product-id" class="dr-input select2-opname">
                        <option value="">-- Pilih Produk --</option>
                    </select>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="dr-label mb-2">Stok Tercatat</label>
                        <input type="text" id="opname-qty-current" class="dr-input" style="background-color: var(--dr-panel-soft) !important;" readonly value="0">
                    </div>
                    <div class="col-md-6">
                        <label class="dr-label mb-2">Stok Fisik</label>
                        <input type="number" id="opname-qty-physical" class="dr-input" min="0" placeholder="0">
                    </div>
                </div>
                <div class="mb-0">
                    <label class="dr-label mb-2">Catatan</label>
                    <textarea id="opname-note" class="dr-input" rows="2" style="height: auto;" placeholder="Alasan perbedaan stok..."></textarea>
                </div>
            </div>
            <div class="modal-footer d-flex gap-2">
                <button class="dr-btn-modal dr-btn-modal-cancel grow justify-content-center" data-bs-dismiss="modal">
                    <i class="iconoir-xmark"></i> Batal
                </button>
                <button class="dr-btn-modal dr-btn-modal-save grow justify-content-center" onclick="submitStockOpname()">
                    <i class="iconoir-check-circle"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>
