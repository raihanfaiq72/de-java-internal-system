<div class="modal fade" id="modalProduk" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalProdukTitle">Tambah Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="produk-id">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="small fw-bold">SKU</label>
                        <input type="text" id="produk-sku" class="form-control form-control-sm">
                    </div>

                    <div class="col-md-6">
                        <label class="small fw-bold">Nama Produk</label>
                        <input type="text" id="produk-nama" class="form-control form-control-sm">
                    </div>

                    <div class="col-md-4">
                        <label class="small fw-bold">Kategori Produk</label>
                        <select id="produk-kategori" class="form-select form-select-sm">
                            <option value="">-- Pilih --</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="small fw-bold">Kategori Unit</label>
                        <select id="produk-unit-category" class="form-select form-select-sm">
                            <option value="">-- Pilih --</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="small fw-bold">Unit</label>
                        <select id="produk-unit" class="form-select form-select-sm" disabled>
                            <option value="">-- Pilih --</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="small fw-bold">Harga Beli</label>
                        <input type="number" id="produk-harga-beli" class="form-control form-control-sm">
                    </div>

                    <div class="col-md-4">
                        <label class="small fw-bold">Harga Jual</label>
                        <input type="number" id="produk-harga-jual" class="form-control form-control-sm">
                    </div>

                    <div class="col-md-4">
                        <label class="small fw-bold">Harga Tempo</label>
                        <input type="number" id="produk-harga-tempo" class="form-control form-control-sm" value="0">
                    </div>

                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="produk-track-stock"
                                onchange="toggleTrackStock()">
                            <label class="form-check-label small fw-bold" for="produk-track-stock">Lacak Stok Barang
                                (Track Stock)</label>
                        </div>
                    </div>

                    <div class="col-md-4 stock-field" style="display:none;">
                        <label class="small fw-bold">Stok Awal (Qty)</label>
                        <input type="number" id="produk-qty" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-8 stock-field" style="display:none;">
                        <span class="text-muted small d-block mt-4">
                            * Stok awal akan dicatat sebagai "Initial Balance" dengan HPP sesuai Harga Beli.
                        </span>
                    </div>

                    <div class="col-12">
                        <label class="small fw-bold">Deskripsi</label>
                        <textarea id="produk-deskripsi" class="form-control form-control-sm"></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button id="btn-simpan-produk" class="btn btn-primary btn-sm" onclick="submitProduk()">Simpan</button>
            </div>

        </div>
    </div>
</div>
