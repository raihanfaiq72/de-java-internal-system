<!-- Modal Location -->
<div class="modal fade" id="modalLocation" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
<<<<<<< Updated upstream
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="location-modal-title">Buat Lokasi Stok</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
=======
        <div class="modal-content border-0 bg-white">
            <div class="modal-header bg-white px-4 py-4 d-flex align-items-center justify-content-between border-0">
                <div class="d-flex align-items-center gap-3">
                    <i class="iconoir-map-pin text-dark" style="font-size: 20px;"></i>
                    <div>
                        <h5 class="dr-title mb-1" id="location-modal-title" style="font-size: 16px;">Buat Lokasi Stok</h5>
                        <p class="dr-subtitle mb-0" style="font-size: 12px;">Kelola gudang atau titik distribusi barang.</p>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
>>>>>>> Stashed changes
            </div>
            <div class="modal-body px-4 pb-4 pt-0 bg-white">
                <input type="hidden" id="location-id">
                <div class="mb-3">
<<<<<<< Updated upstream
                    <label class="small fw-bold">Nama Lokasi</label>
                    <input type="text" id="location-name" class="form-control" placeholder="Contoh: Gudang Utama">
                </div>
                <div class="mb-3">
                    <label class="small fw-bold">Tipe Lokasi</label>
                    <select id="location-type" class="form-select">
                        <option value="stock">Stock</option>
                        <option value="virtual">Virtual</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary btn-sm" onclick="submitLocation()">Simpan</button>
=======
                    <label class="dr-label mb-2">Nama Lokasi/Gudang *</label>
                    <input type="text" id="location-name" class="dr-input fw-bold" placeholder="Misal: Gudang Utama">
                </div>
                <div class="mb-0">
                    <label class="dr-label mb-2">Tipe Lokasi *</label>
                    <select id="location-type" class="dr-input">
                        <option value="Warehouse">Gudang (Warehouse)</option>
                        <option value="Outlet">Outlet / Toko</option>
                        <option value="Other">Lainnya</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer bg-white border-0 px-4 py-4 d-flex justify-content-end gap-2">
                <button type="button" class="dr-btn-modal" data-bs-dismiss="modal">
                    <i class="iconoir-xmark text-danger"></i> Batal
                </button>
                <button type="button" class="dr-btn-modal" onclick="submitLocation()">
                    <i class="iconoir-check text-success"></i> Simpan Lokasi
                </button>
>>>>>>> Stashed changes
            </div>
        </div>
    </div>
</div>

<!-- Modal Opening Stock -->
<div class="modal fade" id="modalOpeningStock" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
<<<<<<< Updated upstream
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Persediaan Awal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
=======
        <div class="modal-content border-0 bg-white">
            <div class="modal-header bg-white px-4 py-4 d-flex align-items-center justify-content-between border-0">
                <div class="d-flex align-items-center gap-3">
                    <i class="iconoir-add-database text-dark" style="font-size: 20px;"></i>
                    <div>
                        <h5 class="dr-title mb-1" style="font-size: 16px;">Persediaan Awal</h5>
                        <p class="dr-subtitle mb-0" style="font-size: 12px;">Inisialisasi saldo stok awal produk.</p>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
>>>>>>> Stashed changes
            </div>
            <div class="modal-body px-4 pb-4 pt-0 bg-white">
                <input type="hidden" id="opening-stock-location-id">
<<<<<<< Updated upstream
                <div class="mb-3">
                    <label class="small fw-bold">Lokasi</label>
                    <input type="text" id="opening-stock-location-name" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="small fw-bold">Pilih Produk</label>
                    <select id="opening-stock-product-id" class="form-select select2-opening">
                        <option value="">-- Pilih Produk --</option>
                    </select>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="small fw-bold">Kuantitas</label>
                        <input type="number" id="opening-stock-qty" class="form-control" min="1" value="1">
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold">Harga Beli (Satuan)</label>
                        <input type="number" id="opening-stock-cost" class="form-control" min="0">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary btn-sm" onclick="submitOpeningStock()">Simpan</button>
=======
                <div class="p-3 border rounded-3 mb-4 d-flex align-items-start gap-3 bg-white">
                    <i class="iconoir-info-circle text-dark mt-1"></i>
                    <span class="text-dark small" style="line-height: 1.4; font-size: 12px;">Pilih file Excel yang berisi daftar produk dan jumlah stok awal untuk lokasi <strong id="opening-stock-location-name"></strong>.</span>
                </div>
                <div class="mb-0">
                    <label class="dr-label mb-2">Pilih File Excel (.xlsx) *</label>
                    <input type="file" id="opening-stock-file" class="dr-input" accept=".xlsx">
                    <div class="mt-2 text-muted" style="font-size: 11px;">
                        <a href="#" class="text-primary text-decoration-none"><i class="iconoir-download"></i> Download Template Excel</a>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white border-0 px-4 py-4 d-flex justify-content-end gap-2">
                <button type="button" class="dr-btn-modal" data-bs-dismiss="modal">
                    <i class="iconoir-xmark text-danger"></i> Batal
                </button>
                <button type="button" class="dr-btn-modal" onclick="submitOpeningStock()">
                    <i class="iconoir-check text-success"></i> Simpan Stok
                </button>
>>>>>>> Stashed changes
            </div>
        </div>
    </div>
</div>

<!-- Modal Stock Opname -->
<div class="modal fade" id="modalStockOpname" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
<<<<<<< Updated upstream
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Stok Opname</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
=======
        <div class="modal-content border-0 bg-white">
            <div class="modal-header bg-white px-4 py-4 d-flex align-items-center justify-content-between border-0">
                <div class="d-flex align-items-center gap-3">
                    <i class="iconoir-search-window text-dark" style="font-size: 20px;"></i>
                    <div>
                        <h5 class="dr-title mb-1" style="font-size: 16px;">Stok Opname</h5>
                        <p class="dr-subtitle mb-0" style="font-size: 12px;">Penyesuaian stok fisik dengan sistem.</p>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
>>>>>>> Stashed changes
            </div>
            <div class="modal-body px-4 pb-4 pt-0 bg-white">
                <input type="hidden" id="opname-location-id">
<<<<<<< Updated upstream
                <div class="mb-3">
                    <label class="small fw-bold">Lokasi</label>
                    <input type="text" id="opname-location-name" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="small fw-bold">Pilih Produk</label>
                    <select id="opname-product-id" class="form-select select2-opname">
                        <option value="">-- Pilih Produk --</option>
                    </select>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="small fw-bold">Stok Tercatat</label>
                        <input type="text" id="opname-qty-current" class="form-control" readonly value="0">
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold">Stok Fisik</label>
                        <input type="number" id="opname-qty-physical" class="form-control" min="0">
                    </div>
                </div>
                <div class="mt-3">
                    <label class="small fw-bold">Catatan</label>
                    <textarea id="opname-note" class="form-control" rows="2" placeholder="Alasan perbedaan stok..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary btn-sm" onclick="submitStockOpname()">Simpan</button>
=======
                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <label class="dr-label mb-2">Pilih Barang *</label>
                        <select id="opname-barang-id" class="dr-input" placeholder="Cari barang..."></select>
                    </div>
                    <div class="col-md-6">
                        <label class="dr-label mb-2">Stok Sistem</label>
                        <input type="text" id="opname-system-qty" class="dr-input bg-light" readonly value="0">
                    </div>
                    <div class="col-md-6">
                        <label class="dr-label mb-2">Stok Fisik *</label>
                        <input type="number" id="opname-physical-qty" class="dr-input fw-bold" value="0">
                    </div>
                    <div class="col-md-12">
                        <label class="dr-label mb-2">Catatan Penyesuaian</label>
                        <textarea id="opname-note" class="dr-input" rows="2" style="height: auto;" placeholder="Alasan perbedaan stok..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white border-0 px-4 py-4 d-flex justify-content-end gap-2">
                <button type="button" class="dr-btn-modal" data-bs-dismiss="modal">
                    <i class="iconoir-xmark text-danger"></i> Batal
                </button>
                <button type="button" class="dr-btn-modal" onclick="submitStockOpname()">
                    <i class="iconoir-check text-success"></i> Simpan Opname
                </button>
>>>>>>> Stashed changes
            </div>
        </div>
    </div>
</div>
