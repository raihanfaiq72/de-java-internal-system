<!-- Modal Location CRUD -->
<div class="modal fade" id="modalLocation" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="location-modal-title">Buat Lokasi Stok</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="location-id">
                <div class="mb-3">
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
            </div>
        </div>
    </div>
</div>

<!-- Modal Opening Stock -->
<div class="modal fade" id="modalOpeningStock" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Persediaan Awal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="opening-stock-location-id">
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
            </div>
        </div>
    </div>
</div>

<!-- Modal Stock Opname -->
<div class="modal fade" id="modalStockOpname" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Stok Opname</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="opname-location-id">
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
            </div>
        </div>
    </div>
</div>
