<div class="modal fade" id="modalKategoriProduk" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalKategoriTitle">
                    <i class="fa fa-folder me-2 text-primary"></i>Tambah Kategori
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="kategori-id">

                <div class="mb-4">
                    <label class="form-label fw-bold">Nama Kategori <span class="text-danger">*</span></label>
                    <input type="text" id="kategori-nama"
                           class="form-control">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Parent Kategori</label>
                    <select id="kategori-parent"
                            class="form-select">
                        <option value="">— Tanpa Parent —</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Deskripsi</label>
                    <textarea id="kategori-deskripsi"
                              class="form-control"
                              rows="3"></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary px-4"
                        data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary px-4 fw-bold"
                        onclick="submitKategoriProduk()">
                    <i class="fa fa-save me-1"></i> Simpan Kategori
                </button>
            </div>

        </div>
    </div>
</div>
