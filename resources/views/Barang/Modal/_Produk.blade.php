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

                <div class="row g-3">
                    <div class="col-12">
                        <div class="modal-section-title">Identitas Produk</div>
                    </div>
                    <div class="col-md-4">
                        <label class="small fw-bold">SKU</label>
                        <input type="text" id="edit-produk-sku" class="form-control" disabled>
                    </div>

                    <div class="col-md-8">
                        <label class="small fw-bold">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" id="edit-produk-nama" class="form-control">
                    </div>

                    <div class="col-12">
                        <div class="modal-section-title">Supplier & Kategori</div>
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold">Supplier <span class="text-danger">*</span></label>
                        <select id="edit-produk-supplier" class="form-select"></select>
                    </div>

                    <div class="col-md-6">
                        <label class="small fw-bold">Brand <span class="text-danger">*</span></label>
                        <select id="edit-produk-brand" class="form-select"></select>
                    </div>

                    <div class="col-md-6">
                        <label class="small fw-bold">Kategori Secara Keseluruhan <span class="text-danger">*</span></label>
                        <select id="edit-produk-kategori" class="form-select"></select>
                    </div>

                    <div class="col-md-6">
                        <label class="small fw-bold">COA (Chart of Account) <span class="text-danger">*</span></label>
                        <select id="edit-produk-coa" class="form-select"></select>
                    </div>

                    <div class="col-12">
                        <div class="modal-section-title">Harga & Kemasan</div>
                    </div>
                    <div class="col-md-4">
                        <label class="small fw-bold">Kemasan</label>
                        <input type="number" id="edit-produk-kemasan" class="form-control" placeholder="Cth: 12">
                    </div>

                    <div class="col-md-4">
                        <label class="small fw-bold">Satuan</label>
                        <input type="text" id="edit-produk-satuan" class="form-control" placeholder="Cth: DUS">
                    </div>

                    <div class="col-md-4">
                        <label class="small fw-bold">Qty (Disabled)</label>
                        <input type="number" id="edit-produk-qty" class="form-control" disabled title="Ubah Qty bisa lewat menu Stok Masuk/Keluar">
                    </div>

                    <div class="col-md-4">
                        <label class="small fw-bold">Harga Beli</label>
                        <input type="text" id="edit-produk-beli" class="form-control" value="0">
                    </div>

                    <div class="col-md-4">
                        <label class="small fw-bold">Harga Jual</label>
                        <input type="text" id="edit-produk-jual" class="form-control" value="0">
                    </div>

                    <div class="col-md-4">
                        <label class="small fw-bold">Harga Tempo</label>
                        <input type="text" id="edit-produk-tempo" class="form-control" value="0">
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button id="btn-simpan-edit-produk" class="btn btn-primary px-4 fw-bold" onclick="submitEditProduk()">Simpan Perubahan</button>
            </div>

        </div>
    </div>
</div>
